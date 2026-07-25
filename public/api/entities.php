<?php

declare(strict_types=1);

require_once __DIR__ . '/_helpers.php';

try {
    $categorySlug = apiRequestSlug('category');
    $entityType = apiOptionalFilter('type');
    $payload = contentFetchEntitiesByCategory(apiGetConnection(), $categorySlug, $entityType, true);

    if ($payload === []) {
        apiJsonError('No se encontro la categoria solicitada.', 404);
    }

    apiJsonSuccess($payload);
} catch (Throwable $exception) {
    apiHandleException($exception, 'No se pudieron cargar las entidades.');
}