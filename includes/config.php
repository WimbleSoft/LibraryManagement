<?php
/**
 * Application configuration.
 *
 * Every value can be overridden with an environment variable so that
 * credentials do not have to be committed to the repository.
 */

return [
    'db' => [
        'host'     => getenv('LIBRARY_DB_HOST') ?: 'localhost',
        'port'     => getenv('LIBRARY_DB_PORT') ?: '3306',
        'name'     => getenv('LIBRARY_DB_NAME') ?: 'libraryrecords',
        'user'     => getenv('LIBRARY_DB_USER') ?: 'library',
        'password' => getenv('LIBRARY_DB_PASSWORD') ?: '',
    ],

    // Languages that may be selected. Only these files are ever loaded.
    'languages'        => ['en', 'tr'],
    'default_language' => 'en',

    // Sign-in throttling: lock the username/IP pair after this many
    // failures for the given number of seconds.
    'login_max_attempts'   => 5,
    'login_lockout_seconds' => 900,
];
