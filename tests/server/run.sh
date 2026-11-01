#!/usr/bin/env bash
# Checks the web server hardening (.htaccess, .user.ini, deploy/nginx.conf)
# and sign-in throttling on real Apache, PHP-FPM and Nginx servers.
#
# Needs Docker with the compose plugin, curl and python3.
# Usage: tests/server/run.sh   (KEEP=1 leaves the containers running)

set -euo pipefail

HERE=$(cd "$(dirname "$0")" && pwd)
REPO=$(cd "$HERE/../.." && pwd)
WORK=$(mktemp -d)
export APP_DIR="$WORK/app"
export COMPOSE_PROJECT_NAME=library-server-test
COMPOSE=(docker compose -f "$HERE/docker-compose.yml")

cleanup() {
    if [ "${KEEP:-0}" != 1 ]; then
        "${COMPOSE[@]}" down -v --remove-orphans >/dev/null 2>&1 || true
        rm -rf "$WORK"
    fi
}
trap cleanup EXIT

# Serve a copy of the repository (including .git, which must stay hidden)
# with the probe script added to the document root.
cp -a "$REPO" "$APP_DIR"
cp "$HERE/probe.php" "$APP_DIR/_probe.php"

"${COMPOSE[@]}" up -d --build --wait --quiet-pull

SERVERS=("apache-modphp=http://localhost:8081" "apache-fpm=http://localhost:8082" "nginx-fpm=http://localhost:8083")
FAILURES=0
pass() { printf '  ok    %s\n' "$1"; }
fail() { printf '  FAIL  %s\n' "$1"; FAILURES=$((FAILURES + 1)); }

sql() {
    "${COMPOSE[@]}" exec -T db mariadb -uroot -proot-test libraryrecords -N -e "$1"
}

status() { curl -s -o /dev/null -w '%{http_code}' "$@"; }

# Signs in and prints the HTTP status and Location header of the answer.
sign_in() {
    local base=$1 password=$2 jar token
    shift 2
    jar=$(mktemp -p "$WORK")
    token=$(curl -s -c "$jar" "$base/login.php" | sed -n 's/.*name="csrf_token" value="\([0-9a-f]*\)".*/\1/p')
    curl -s -o "$jar.body" -b "$jar" -w '%{http_code} %{redirect_url}' \
        --data-urlencode "csrf_token=$token" --data-urlencode "username=admin" \
        --data-urlencode "password=$password" "$@" "$base/login.php"
}

for entry in "${SERVERS[@]}"; do
    name=${entry%%=*}
    base=${entry#*=}
    echo "== $name ($base)"

    # The application itself works.
    code=$(status "$base/login.php")
    [ "$code" = 200 ] && pass "login page loads" || fail "login page returned $code"

    # Internal files are never served.
    for path in /.git/config /.git/HEAD /.htaccess /.user.ini /.gitattributes \
                /includes/config.php /includes/ /lang/en.php /database/ \
                /database/libraryrecords.sql /README.md /deploy/nginx.conf \
                /tests/server/run.sh; do
        code=$(status "$base$path")
        case $code in
            403|404) pass "$path hidden ($code)" ;;
            *)       fail "$path returned $code" ;;
        esac
    done

    # PHP options from .htaccess (mod_php) or .user.ini (PHP-FPM).
    probe=$(curl -s "$base/_probe.php")
    if python3 - "$probe" <<'PY'
import json, sys
p = json.loads(sys.argv[1])
want = {
    "display_errors": False,
    "session.use_strict_mode": True,
    "session.cookie_httponly": True,
    "session.upload_progress.enabled": False,
    "post_max_size": "1M",
    "upload_max_filesize": "1M",
}
bad = {k: p.get(k) for k, v in want.items() if p.get(k) != v}
print("        sapi=%s" % p.get("sapi"))
if bad:
    print("        wrong: %s" % bad)
    sys.exit(1)
PY
    then pass "PHP options applied"; else fail "PHP options not applied"; fi

    # An oversized sign-in (valid token and password plus 2 MB of padding)
    # must never be processed. Apache and Nginx should refuse it with 413
    # before PHP runs; failing that, PHP's post_max_size must drop the body.
    head -c 2097152 /dev/zero | tr '\0' a > "$WORK/padding"
    result=$(sign_in "$base" 'ChangeMe123!' --data-urlencode "padding@$WORK/padding")
    case $result in
        413*) pass "2 MB request refused by the web server (413)" ;;
        400*) pass "2 MB request dropped by PHP post_max_size (web server let it through)" ;;
        *)    fail "2 MB request was processed ('$result')" ;;
    esac
    # Chunked bodies carry no Content-Length, so they must be capped too.
    code=$(status -H 'Transfer-Encoding: chunked' --data-binary @"$WORK/padding" "$base/login.php")
    case $code in
        403|411|413) pass "2 MB chunked request refused ($code)" ;;
        *)           fail "2 MB chunked request returned $code" ;;
    esac

    # Correct password signs in.
    result=$(sign_in "$base" 'ChangeMe123!')
    [[ $result == 302*index.php ]] && pass "admin can sign in" || fail "sign-in returned '$result'"
done

echo "== sign-in throttling"
base=http://localhost:8083

# Failures from another IP address must not lock the real admin out.
sql "DELETE FROM login_attempts;
     INSERT INTO login_attempts (username, ipAddress, attemptedAt)
     SELECT 'admin', '203.0.113.9', NOW() FROM seq_1_to_30;"
result=$(sign_in "$base" 'ChangeMe123!')
[[ $result == 302*index.php ]] && pass "failures from another IP do not lock admin out" \
    || fail "admin locked out by another IP's failures ('$result')"

# Five wrong passwords from this address lock this username here.
sql "DELETE FROM login_attempts;"
for _ in 1 2 3 4 5; do sign_in "$base" 'wrong-password' >/dev/null; done
result=$(sign_in "$base" 'ChangeMe123!')
[[ $result == 200* ]] && pass "username locked after 5 failures from the same IP" \
    || fail "no lockout after 5 failures ('$result')"

# Twenty failures from this address for any usernames lock the address.
# One real failure tells us which address PHP sees for this script.
sql "DELETE FROM login_attempts;"
sign_in "$base" 'wrong-password' >/dev/null
ip=$(sql "SELECT ipAddress FROM login_attempts LIMIT 1;")
sql "DELETE FROM login_attempts;
     INSERT INTO login_attempts (username, ipAddress, attemptedAt)
     SELECT CONCAT('user', seq), '$ip', NOW() FROM seq_1_to_20;"
result=$(sign_in "$base" 'ChangeMe123!')
[[ $result == 200* ]] && pass "IP locked after 20 failures for any username" \
    || fail "no IP lockout after 20 failures ('$result')"
sql "DELETE FROM login_attempts;"

echo
if [ "$FAILURES" -eq 0 ]; then
    echo "All checks passed."
else
    echo "$FAILURES check(s) failed."
    exit 1
fi
