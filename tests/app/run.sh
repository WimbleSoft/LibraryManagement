#!/usr/bin/env bash
# Checks the lending rules end to end: due dates, overdue blocks,
# borrowing limits, returns with condition, lost items, and archiving
# items and staff instead of deleting them.
#
# Uses the database and Apache + mod_php services from tests/server.
# Needs Docker with the compose plugin and curl.
# Usage: tests/app/run.sh   (KEEP=1 leaves the containers running)

set -euo pipefail

HERE=$(cd "$(dirname "$0")" && pwd)
REPO=$(cd "$HERE/../.." && pwd)
WORK=$(mktemp -d)
export APP_DIR="$WORK/app"
export COMPOSE_PROJECT_NAME=library-app-test
COMPOSE=(docker compose -f "$REPO/tests/server/docker-compose.yml")
BASE=http://localhost:8081
JAR="$WORK/cookies"

cleanup() {
    if [ "${KEEP:-0}" != 1 ]; then
        "${COMPOSE[@]}" down -v --remove-orphans >/dev/null 2>&1 || true
        rm -rf "$WORK"
    fi
}
trap cleanup EXIT

cp -a "$REPO" "$APP_DIR"
"${COMPOSE[@]}" up -d --build --wait --quiet-pull db apache-modphp

FAILURES=0
pass() { printf '  ok    %s\n' "$1"; }
fail() { printf '  FAIL  %s\n' "$1"; FAILURES=$((FAILURES + 1)); }
check() { # check "description" actual expected
    [ "$2" = "$3" ] && pass "$1" || fail "$1 (got '$2', expected '$3')"
}

sql() {
    "${COMPOSE[@]}" exec -T db mariadb -uroot -proot-test libraryrecords -N -e "$1"
}

TOKEN=
get() { curl -s -b "$JAR" -c "$JAR" "$BASE/$1"; }
# post page field=value... : submits a form with the session's CSRF token.
post() {
    local page=$1 args=()
    shift
    for field in "$@"; do args+=(--data-urlencode "$field"); done
    curl -s -o /dev/null -b "$JAR" -c "$JAR" --data-urlencode "csrf_token=$TOKEN" "${args[@]}" "$BASE/$page"
}

TODAY=$(date -u +%F)
IN_21=$(date -u -d '+21 days' +%F)
TOMORROW=$(date -u -d '+1 day' +%F)
YESTERDAY=$(date -u -d '-1 day' +%F)
LAST_WEEK=$(date -u -d '-7 days' +%F)

echo "== sign in"
TOKEN=$(get login.php | sed -n 's/.*name="csrf_token" value="\([0-9a-f]*\)".*/\1/p')
curl -s -o /dev/null -b "$JAR" -c "$JAR" --data-urlencode "csrf_token=$TOKEN" \
    --data-urlencode username=admin --data-urlencode 'password=ChangeMe123!' "$BASE/login.php"
TOKEN=$(get index.php | sed -n 's/.*name="csrf_token" value="\([0-9a-f]*\)".*/\1/p' | head -1)
[ -n "$TOKEN" ] && pass "signed in" || { fail "could not sign in"; exit 1; }

echo "== pages"
for lang in en tr; do
    get "index.php?lang=$lang" >/dev/null
    for page in index.php loans.php 'loans.php?show=open' 'loans.php?show=overdue' books.php computers.php keys.php personnel.php settings.php; do
        body=$(curl -s -b "$JAR" -c "$JAR" -w '\n%{http_code}' "$BASE/$page")
        code=${body##*$'\n'}
        if [ "$code" = 200 ] && grep -q '</html>' <<<"$body"; then pass "$lang $page loads"; else fail "$lang $page returned $code or was cut off"; fi
    done
done
get "index.php?lang=en" >/dev/null

echo "== setup"
post personnel.php action=add fullName=Alice username=alice password=alice-password-1
post personnel.php action=add fullName=Bob username=bob password=bob-password-1
ALICE=$(sql "SELECT personnelId FROM personnel WHERE username = 'alice'")
BOB=$(sql "SELECT personnelId FROM personnel WHERE username = 'bob'")
post books.php action=add bookName=Dune bookAuthor=Herbert
post computers.php action=add manufacturer=Dell model=Latitude serialNumber=SN1
post keys.php action=add keyNumber=K1 keyDescription=Lab
BOOK=$(sql "SELECT bookId FROM books WHERE bookName = 'Dune'")
PC=$(sql "SELECT computerId FROM computers WHERE serialNumber = 'SN1'")
KEY=$(sql "SELECT keyId FROM roomkeys WHERE keyNumber = 'K1'")
[ -n "$ALICE$BOB$BOOK$PC$KEY" ] && pass "people and items created"

echo "== due dates"
post loans.php action=add itemType=0 itemId="$BOOK" borrowerId="$ALICE" loanDate="$TOMORROW" dueDate=
check "loan date in the future is refused" "$(sql "SELECT COUNT(*) FROM loans")" 0
post loans.php action=add itemType=0 itemId="$BOOK" borrowerId="$ALICE" loanDate="$TODAY" dueDate="$YESTERDAY"
check "due date before loan date is refused" "$(sql "SELECT COUNT(*) FROM loans")" 0
post loans.php action=add itemType=0 itemId="$BOOK" borrowerId="$ALICE" loanDate="$TODAY" dueDate=
check "empty due date uses the 21-day book period" "$(sql "SELECT dueDate FROM loans WHERE itemType = 0")" "$IN_21"
BOOK_LOAN=$(sql "SELECT loanId FROM loans WHERE itemType = 0")

echo "== overdue"
sql "UPDATE loans SET loanDate = '$LAST_WEEK', dueDate = '$YESTERDAY' WHERE loanId = $BOOK_LOAN"
# Read pages into a variable first: with pipefail, grep -q closing the pipe early would fail the check.
body=$(get index.php); grep -q 'Overdue loans: 1' <<<"$body" && pass "dashboard shows 1 overdue loan" || fail "dashboard overdue count"
body=$(get 'loans.php?show=overdue'); grep -q '1 days overdue' <<<"$body" && pass "overdue filter lists the loan" || fail "overdue filter"
post loans.php action=add itemType=1 itemId="$PC" borrowerId="$ALICE" loanDate="$TODAY" dueDate=
check "no new loan while the borrower has an overdue item" "$(sql "SELECT COUNT(*) FROM loans WHERE itemType = 1")" 0
post loans.php action=add itemType=1 itemId="$PC" borrowerId="$ALICE" loanDate="$TODAY" dueDate= override=1
check "override lends anyway" "$(sql "SELECT COUNT(*) FROM loans WHERE itemType = 1")" 1
PC_LOAN=$(sql "SELECT loanId FROM loans WHERE itemType = 1")
post loans.php action=extend loanId="$BOOK_LOAN" dueDate="$TOMORROW"
check "due date can be extended" "$(sql "SELECT dueDate FROM loans WHERE loanId = $BOOK_LOAN")" "$TOMORROW"

echo "== returns"
post loans.php action=return loanId="$BOOK_LOAN" returnDate="$TOMORROW" condition=0
check "return date in the future is refused" "$(sql "SELECT returnDate IS NULL FROM loans WHERE loanId = $BOOK_LOAN")" 1
post loans.php action=return loanId="$BOOK_LOAN" returnDate="$YESTERDAY" condition=1 note=Torn
check "backdated damaged return is recorded" \
    "$(sql "SELECT CONCAT(returnDate, ' ', returnCondition, ' ', returnNote) FROM loans WHERE loanId = $BOOK_LOAN")" "$YESTERDAY 1 Torn"
check "damaged item goes to repair" "$(sql "SELECT status FROM books WHERE bookId = $BOOK")" 1
post loans.php action=add itemType=0 itemId="$BOOK" borrowerId="$BOB" loanDate="$TODAY" dueDate=
check "item in repair cannot be lent" "$(sql "SELECT COUNT(*) FROM loans WHERE itemType = 0")" 1
post loans.php action=lost loanId="$PC_LOAN" replacementCost=250,50 note=Stolen
check "lost loan is closed with its cost" \
    "$(sql "SELECT CONCAT(returnCondition, ' ', replacementCost) FROM loans WHERE loanId = $PC_LOAN")" "3 250.50"
check "lost item is marked lost" "$(sql "SELECT status FROM computers WHERE computerId = $PC")" 3
post loans.php action=delete loanId="$PC_LOAN"
check "loans cannot be deleted" "$(sql "SELECT COUNT(*) FROM loans")" 2

echo "== archive instead of delete"
post books.php action=delete bookId="$BOOK"
check "item with history is kept" "$(sql "SELECT COUNT(*) FROM books WHERE bookId = $BOOK")" 1
post books.php action=update bookId="$BOOK" bookName=Dune bookAuthor=Herbert status=2
check "item can be retired" "$(sql "SELECT status FROM books WHERE bookId = $BOOK")" 2
post books.php action=add bookName=Typo bookAuthor=Nobody
TYPO=$(sql "SELECT bookId FROM books WHERE bookName = 'Typo'")
post books.php action=delete bookId="$TYPO"
check "item without history can be deleted" "$(sql "SELECT COUNT(*) FROM books WHERE bookId = $TYPO")" 0
post personnel.php action=delete personnelId="$ALICE"
check "person with history is kept" "$(sql "SELECT COUNT(*) FROM personnel WHERE personnelId = $ALICE")" 1
post personnel.php action=update personnelId="$ALICE" fullName=Alice username=alice
check "person can be deactivated" "$(sql "SELECT isActive FROM personnel WHERE personnelId = $ALICE")" 0
post loans.php action=add itemType=2 itemId="$KEY" borrowerId="$ALICE" loanDate="$TODAY" dueDate=
check "inactive person cannot borrow" "$(sql "SELECT COUNT(*) FROM loans WHERE itemType = 2")" 0

echo "== limits"
post settings.php action=lending loanDaysBook=21 loanDaysComputer=14 loanDaysKey=1 maxOpenLoans=1 blockWhenOverdue=1
check "lending rules are saved" "$(sql "SELECT value FROM settings WHERE name = 'maxOpenLoans'")" 1
post loans.php action=add itemType=2 itemId="$KEY" borrowerId="$BOB" loanDate="$TODAY" dueDate=
check "key uses the 1-day period" "$(sql "SELECT dueDate FROM loans WHERE itemType = 2")" "$TOMORROW"
post computers.php action=add manufacturer=HP model=Probook serialNumber=SN2
PC2=$(sql "SELECT computerId FROM computers WHERE serialNumber = 'SN2'")
post loans.php action=add itemType=1 itemId="$PC2" borrowerId="$BOB" loanDate="$TODAY" dueDate=
check "second loan over the limit is refused" "$(sql "SELECT COUNT(*) FROM loans WHERE borrowerId = $BOB")" 1
post personnel.php action=update personnelId="$BOB" fullName=Bob username=bob
check "person with open loans cannot be deactivated" "$(sql "SELECT isActive FROM personnel WHERE personnelId = $BOB")" 1
post keys.php action=update keyId="$KEY" keyNumber=K1 keyDescription=Lab status=2
check "item on loan cannot be retired" "$(sql "SELECT status FROM roomkeys WHERE keyId = $KEY")" 0

echo "== deactivated administrator"
post personnel.php action=add fullName=Carol username=carol password=carol-password-1 isAdmin=1
CAROL=$(sql "SELECT personnelId FROM personnel WHERE username = 'carol'")
post personnel.php action=update personnelId="$CAROL" fullName=Carol username=carol isAdmin=1
CJAR="$WORK/carol"
CTOKEN=$(curl -s -c "$CJAR" "$BASE/login.php" | sed -n 's/.*name="csrf_token" value="\([0-9a-f]*\)".*/\1/p')
location=$(curl -s -o /dev/null -w '%{redirect_url}' -b "$CJAR" -c "$CJAR" --data-urlencode "csrf_token=$CTOKEN" \
    --data-urlencode username=carol --data-urlencode password=carol-password-1 "$BASE/login.php")
check "inactive administrator cannot sign in" "$location" ""

echo
if [ "$FAILURES" -eq 0 ]; then echo "All lending checks passed."; else echo "$FAILURES lending check(s) failed."; exit 1; fi
