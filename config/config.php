<?php

return [
    'database' => [
        'host' => $_ENV['DB_HOST'] ?? 'localhost',
        'name' => $_ENV['DB_NAME'] ?? 'servex_db',
        'username' => $_ENV['DB_USER'] ?? 'root',
        'password' => $_ENV['DB_PASS'] ?? '',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    ],
    'cache' => [
        'host' => $_ENV['REDIS_HOST'] ?? 'localhost',
        'port' => (int)($_ENV['REDIS_PORT'] ?? 6379),
        'password' => $_ENV['REDIS_PASSWORD'] ?? null,
    ],
    'auth' => [
        'secret_key' => $_ENV['JWT_SECRET'] ?? 'your-secret-key-please-change-it',
        'token_ttl' => (int)($_ENV['JWT_TTL'] ?? 3600),
    ]
]; 