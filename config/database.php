<?php

declare(strict_types=1);

function databaseConfig(): array
{
    return [
        'host' => '127.0.0.1',
        'port' => 3306,
        'dbname' => 'agusma_studio',
        'charset' => 'utf8mb4',
        'username' => 'agusma-studio',
        'password' => 'agusma-studio',
    ];
}

function databaseConnection(): PDO
{
    static $connection = null;

    if ($connection instanceof PDO) {
        return $connection;
    }

    $config = databaseConfig();
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