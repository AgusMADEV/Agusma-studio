<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/database.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $connection = databaseConnection();
    $statement = $connection->query(
        'SELECT id, name, slug, visual_key, link_url, display_order
        FROM categories
        WHERE is_active = 1
        ORDER BY display_order ASC, id ASC'
    );

    echo json_encode([
        'data' => $statement->fetchAll(),
    ], JSON_THROW_ON_ERROR);
} catch (Throwable $exception) {
    error_log($exception->getMessage());
    http_response_code(500);

    echo json_encode([
        'error' => 'No se pudieron cargar las categorias.',
    ], JSON_THROW_ON_ERROR);
}