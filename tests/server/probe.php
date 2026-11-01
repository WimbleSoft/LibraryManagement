<?php
// Copied into the document root by run.sh only; never deployed.
// Reports the PHP options that .htaccess / .user.ini are meant to set.
$flag = static fn (string $name): bool => filter_var(ini_get($name), FILTER_VALIDATE_BOOLEAN);

header('Content-Type: application/json');
echo json_encode([
    'sapi'                            => PHP_SAPI,
    'display_errors'                  => $flag('display_errors'),
    'session.use_strict_mode'         => $flag('session.use_strict_mode'),
    'session.cookie_httponly'         => $flag('session.cookie_httponly'),
    'session.upload_progress.enabled' => $flag('session.upload_progress.enabled'),
    'post_max_size'                   => ini_get('post_max_size'),
    'upload_max_filesize'             => ini_get('upload_max_filesize'),
]);
