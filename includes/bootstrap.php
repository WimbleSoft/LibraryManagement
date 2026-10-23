<?php
/**
 * Common start-up code. Every page includes this file first.
 *
 * It hardens PHP and session settings, sends security headers,
 * opens the database connection and loads the selected language.
 */

declare(strict_types=1);

if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

require_once __DIR__ . '/functions.php';

$config = require __DIR__ . '/config.php';

// Never leak stack traces, paths or SQL to the browser.
ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

send_security_headers();
start_secure_session();

$connection = connect_database($config['db']);
$lang = load_language($config['languages'], $config['default_language']);
