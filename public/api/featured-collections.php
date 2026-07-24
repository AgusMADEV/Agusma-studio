<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/database.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $connection = databaseConnection();
    $statement = $connection->query(
        'SELECT fc.id, fc.title, fc.collection_year, fc.image_variant,
                c.name AS category_name, c.slug AS category_slug
        FROM featured_collections fc
        LEFT JOIN categories c ON c.id = fc.category_id
        WHERE fc.is_active = 1
        ORDER BY fc.display_order ASC, fc.id ASC'
    );

    echo json_encode([
        'data' => $statement->fetchAll(),
    ], JSON_THROW_ON_ERROR);
} catch (Throwable $exception) {
    error_log($exception->getMessage());
    http_response_code(500);

    echo json_encode([
        'error' => 'No se pudieron cargar las colecciones destacadas.',
    ], JSON_THROW_ON_ERROR);
}