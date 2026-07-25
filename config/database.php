<?php

declare(strict_types=1);

function databaseDefaultConfig(): array
{
    return [
        'host' => getenv('AGUSMA_DB_HOST') ?: '127.0.0.1',
        'port' => (int) (getenv('AGUSMA_DB_PORT') ?: 3306),
        'dbname' => getenv('AGUSMA_DB_NAME') ?: 'agusma_studio',
        'charset' => getenv('AGUSMA_DB_CHARSET') ?: 'utf8mb4',
        'username' => getenv('AGUSMA_DB_USER') ?: '',
        'password' => getenv('AGUSMA_DB_PASS') ?: '',
    ];
}

function databaseConfig(): array
{
    $config = databaseDefaultConfig();
    $localConfigPath = __DIR__ . '/database.local.php';

    if (is_file($localConfigPath)) {
        $localConfig = require $localConfigPath;

        if (is_array($localConfig)) {
            $config = array_merge($config, $localConfig);
        }
    }

    return $config;
}

function databaseConnection(): PDO
{
    static $connection = null;

    if ($connection instanceof PDO) {
        return $connection;
    }

    $config = databaseConfig();

    if ($config['username'] === '') {
        throw new RuntimeException('Database username is not configured. Copy config/database.example.php to config/database.local.php or set AGUSMA_DB_* environment variables.');
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        $config['host'],
        $config['port'],
        $config['dbname'],
        $config['charset']
    );

    $connection = new PDO(
        $dsn,
        $config['username'],
        $config['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    return $connection;
}