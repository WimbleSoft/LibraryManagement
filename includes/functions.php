<?php
/**
 * Shared helper functions.
 */

declare(strict_types=1);

// Loan item types, stored in loans.itemType.
const ITEM_BOOK     = 0;
const ITEM_COMPUTER = 1;
const ITEM_KEY      = 2;

/* ------------------------------------------------------------------
 * Start-up
 * ------------------------------------------------------------------ */

function send_security_headers(): void
{
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: same-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    header(
        "Content-Security-Policy: default-src 'self'; "
        . "script-src 'self' https://cdnjs.cloudflare.com https://cdn.datatables.net; "
        . "style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://cdn.datatables.net; "
        . "font-src 'self' data: https://cdnjs.cloudflare.com; "
        . "img-src 'self' data:; "
        . "form-action 'self'; frame-ancestors 'none'; base-uri 'self'; object-src 'none'"
    );

    if (is_https()) {
        header('Strict-Transport-Security: max-age=31536000');
    }
}

function is_https(): bool
{
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (int) ($_SERVER['SERVER_PORT'] ?? 0) === 443;
}

function start_secure_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_name('LIBRARYSESSID');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => is_https(),
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
    session_start();
}

function connect_database(array $db): PDO
{
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        $db['host'],
        $db['port'],
        $db['name']
    );

    try {
        return new PDO($dsn, $db['user'], $db['password'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $exception) {
        error_log('Database connection failed: ' . $exception->getMessage());
        http_response_code(500);
        exit('Database connection failed. Please contact the administrator.');
    }
}

/**
 * Picks the interface language. Only whitelisted language codes are used
 * when building the file path, so the request can never choose which
 * file gets included.
 */
function load_language(array $available, string $default): array
{
    $requested = $_GET['lang'] ?? null;
    if (is_string($requested) && in_array($requested, $available, true)) {
        $_SESSION['lang'] = $requested;
    }

    $code = $_SESSION['lang'] ?? null;
    if (!is_string($code) || !in_array($code, $available, true)) {
        $browser = strtolower(substr((string) ($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? ''), 0, 2));
        $code = in_array($browser, $available, true) ? $browser : $default;
    }

    $GLOBALS['languageCode'] = $code;

    return require APP_ROOT . '/lang/' . $code . '.php';
}

/* ------------------------------------------------------------------
 * Output
 * ------------------------------------------------------------------ */

/** Escapes a value for safe use in HTML text and attributes. */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Returns a translated string, or the key itself when it is missing. */
function t(string $key): string
{
    return $GLOBALS['lang'][$key] ?? $key;
}

function item_type_name(int $type): string
{
    switch ($type) {
        case ITEM_BOOK:
            return t('Book');
        case ITEM_COMPUTER:
            return t('Computer');
        case ITEM_KEY:
            return t('Key');
        default:
            return t('Unknown');
    }
}

function format_date(?string $date): string
{
    if ($date === null || $date === '') {
        return '';
    }

    $parsed = DateTime::createFromFormat('Y-m-d', $date);

    return $parsed ? $parsed->format('d.m.Y') : '';
}

/* ------------------------------------------------------------------
 * Requests, redirects and flash messages
 * ------------------------------------------------------------------ */

/** Redirects to a page of this application and stops the script. */
function redirect(string $page): void
{
    header('Location: ' . $page);
    exit;
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function post_string(string $name, int $maxLength = 255): string
{
    $value = $_POST[$name] ?? '';
    if (!is_string($value)) {
        return '';
    }

    return mb_substr(trim($value), 0, $maxLength);
}

function post_int(string $name): ?int
{
    $value = filter_var($_POST[$name] ?? null, FILTER_VALIDATE_INT);

    return $value === false || $value === null ? null : $value;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function take_flashes(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);

    return $messages;
}

/* ------------------------------------------------------------------
 * CSRF protection
 * ------------------------------------------------------------------ */

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/** Rejects the request unless it carries the session's CSRF token. */
function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        http_response_code(400);
        exit(e(t('Invalid_Request')));
    }
}

/* ------------------------------------------------------------------
 * Authentication
 * ------------------------------------------------------------------ */

/**
 * Makes sure an administrator is signed in and returns their record.
 * The record is re-read on every request so that deleted or demoted
 * accounts lose access immediately.
 */
function require_admin(PDO $connection): array
{
    $personnelId = $_SESSION['personnelId'] ?? null;
    if (!is_int($personnelId)) {
        redirect('login.php');
    }

    $statement = $connection->prepare(
        'SELECT personnelId, username, fullName, email, phone, isAdmin
           FROM personnel WHERE personnelId = ?'
    );
    $statement->execute([$personnelId]);
    $user = $statement->fetch();

    if (!$user || (int) $user['isAdmin'] !== 1) {
        sign_out();
        redirect('login.php');
    }

    return $user;
}

function sign_in(array $user): void
{
    // A fresh session id prevents session fixation.
    session_regenerate_id(true);
    $_SESSION['personnelId'] = (int) $user['personnelId'];
    unset($_SESSION['csrf_token']);
}

function sign_out(): void
{
    $language = $_SESSION['lang'] ?? null;
    $_SESSION = [];
    session_regenerate_id(true);
    if ($language !== null) {
        $_SESSION['lang'] = $language;
    }
}

function hash_password(string $password): string
{
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Checks a password against a stored hash. Hashes created by older
 * versions of the application are unsalted MD5; those are still accepted
 * once so they can be upgraded, and $needsRehash is set to true.
 */
function verify_password(string $password, string $hash, ?bool &$needsRehash = null): bool
{
    $needsRehash = false;

    if (preg_match('/^[a-f0-9]{32}$/i', $hash)) {
        $needsRehash = true;

        return hash_equals(strtolower($hash), md5($password));
    }

    if (!password_verify($password, $hash)) {
        return false;
    }

    $needsRehash = password_needs_rehash($hash, PASSWORD_DEFAULT);

    return true;
}

function is_valid_password(string $password): bool
{
    return mb_strlen($password) >= 8;
}

/* ------------------------------------------------------------------
 * Sign-in throttling
 * ------------------------------------------------------------------ */

function client_ip(): string
{
    return (string) ($_SERVER['REMOTE_ADDR'] ?? '');
}

function is_login_locked(PDO $connection, string $username, array $config): bool
{
    $statement = $connection->prepare(
        'SELECT COUNT(*) FROM login_attempts
          WHERE (username = ? OR ipAddress = ?)
            AND attemptedAt > (NOW() - INTERVAL ? SECOND)'
    );
    $statement->execute([$username, client_ip(), $config['login_lockout_seconds']]);

    return (int) $statement->fetchColumn() >= $config['login_max_attempts'];
}

function record_failed_login(PDO $connection, string $username): void
{
    $connection
        ->prepare('INSERT INTO login_attempts (username, ipAddress, attemptedAt) VALUES (?, ?, NOW())')
        ->execute([$username, client_ip()]);
}

function clear_failed_logins(PDO $connection, string $username): void
{
    $connection
        ->prepare('DELETE FROM login_attempts WHERE username = ? OR attemptedAt < (NOW() - INTERVAL 1 DAY)')
        ->execute([$username]);
}

/* ------------------------------------------------------------------
 * Loans
 * ------------------------------------------------------------------ */

/** Returns true when the item currently has a loan that was not returned. */
function is_item_on_loan(PDO $connection, int $itemType, int $itemId): bool
{
    $statement = $connection->prepare(
        'SELECT 1 FROM loans WHERE itemType = ? AND itemId = ? AND returnDate IS NULL LIMIT 1'
    );
    $statement->execute([$itemType, $itemId]);

    return (bool) $statement->fetchColumn();
}

/**
 * Returns the open loans of one item type, keyed by item id, so list pages
 * can show who has an item without running a query per row.
 */
function open_loans_by_item(PDO $connection, int $itemType): array
{
    $statement = $connection->prepare(
        'SELECT l.itemId, l.loanDate, borrower.fullName AS borrowerName, lender.fullName AS lenderName
           FROM loans l
           LEFT JOIN personnel borrower ON borrower.personnelId = l.borrowerId
           LEFT JOIN personnel lender ON lender.personnelId = l.lenderId
          WHERE l.itemType = ? AND l.returnDate IS NULL'
    );
    $statement->execute([$itemType]);

    $loans = [];
    foreach ($statement->fetchAll() as $loan) {
        $loans[(int) $loan['itemId']] = $loan;
    }

    return $loans;
}

/** Deletes an item and its loan history, unless it is currently on loan. */
function delete_item(PDO $connection, string $table, string $idColumn, int $itemType, int $itemId): void
{
    if (is_item_on_loan($connection, $itemType, $itemId)) {
        flash('danger', t('Item_On_Loan_Cannot_Delete'));

        return;
    }

    $connection->beginTransaction();
    $connection->prepare("DELETE FROM {$table} WHERE {$idColumn} = ?")->execute([$itemId]);
    $connection->prepare('DELETE FROM loans WHERE itemType = ? AND itemId = ?')->execute([$itemType, $itemId]);
    $connection->commit();

    flash('success', t('Record_Deleted'));
}
