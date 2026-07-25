<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/database.php';
require_once dirname(__DIR__, 2) . '/includes/content-repository.php';

function apiJsonSuccess(mixed $data, ?string $message = null, int $statusCode = 200): never
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');

    echo json_encode([
        'success' => true,
        'data' => $data,
        'message' => $message,
    ], JSON_THROW_ON_ERROR);
    exit;
}

function apiJsonError(string $message, int $statusCode = 400): never
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');

    echo json_encode([
        'success' => false,
        'data' => null,
        'message' => $message,
    ], JSON_THROW_ON_ERROR);
    exit;
}

function apiGetConnection(): PDO
{
    return databaseConnection();
}

function apiRequestSlug(string $key): string
{
    $value = trim((string) ($_GET[$key] ?? ''));

    if ($value === '') {
        apiJsonError(sprintf('Falta el parametro requerido: %s.', $key), 400);
    }

    if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $value)) {
        apiJsonError(sprintf('El parametro %s no es valido.', $key), 400);
    }

    return $value;
}

function apiOptionalFilter(string $key): ?string
{
    $value = trim((string) ($_GET[$key] ?? ''));

    if ($value === '') {
        return null;
    }

    if (!preg_match('/^[a-z0-9_-]+$/', $value)) {
        apiJsonError(sprintf('El parametro %s no es valido.', $key), 400);
    }

    return $value;
}

function apiHandleException(Throwable $exception, string $message = 'No se pudo completar la solicitud.'): never
{
    error_log($exception->getMessage());
    apiJsonError($message, 500);
}