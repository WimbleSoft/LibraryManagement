# Security

## Reporting a vulnerability

Please report security problems privately to the maintainer rather than in a
public issue.

## Protections in the application

- All database access uses prepared statements (no SQL injection).
- All output is HTML-escaped (no cross-site scripting).
- Every form, including delete and sign-out, is a POST request with a CSRF token.
- Passwords are stored with `password_hash()`. Old MD5 hashes are upgraded
  automatically on the next successful sign-in.
- Failed sign-ins are throttled per username and IP address.
- Sessions use strict mode, `HttpOnly` and `SameSite=Strict` cookies (`Secure` over HTTPS),
  and the session id changes on sign-in.
- The language switcher only accepts known language codes, so it cannot be
  used to include arbitrary files.
- Security headers are sent with every page (Content-Security-Policy,
  X-Frame-Options, X-Content-Type-Options, Referrer-Policy).
- Database errors are logged, never shown to visitors.

## PHP2Shell (CVE-2026-109618, CVE-2026-109619)

In October 2026 PHP released emergency fixes for two critical,
unauthenticated remote-code-execution flaws known as **PHP2Shell**. They are
in PHP itself, not in this application, and they affect almost every PHP
version from 5.4 to 8.5. Any server running this application on an unpatched
PHP is exposed.

- **CVE-2026-109618**: memory corruption while handling out-of-memory conditions.
- **CVE-2026-109619**: a use-after-free caused by state left over between
  requests on the same worker; session upload progress is one way to reach it.

**What to do:**

1. **Upgrade PHP** to 8.2.35, 8.3.36, 8.4.27 or 8.5.12 (or newer), or install
   your distribution's patched package. PHP 7.x and older will not be fixed;
   move to a supported version.
2. **Restart** every service that loads PHP (Apache, PHP-FPM) so the old code
   is no longer in memory.
3. Until you can upgrade, use PHP's workarounds:
   - in `php.ini`, set `memory_limit = -1` so scripts cannot change it, keep
     `post_max_size` small, and limit request size in the web server;
   - set `session.upload_progress.enabled = 0`. This only partly mitigates
     CVE-2026-109619, so upgrading is still required.

This repository already applies the per-directory workarounds: the
`.htaccess` file (Apache with mod_php) and the `.user.ini` file (PHP-FPM/CGI)
turn off session upload progress, cap `post_max_size` at 1 MB, and limit
request bodies to 1 MB. The application has no file uploads and its forms do
not use multipart encoding. **These settings reduce the risk but do not
replace upgrading PHP.**
