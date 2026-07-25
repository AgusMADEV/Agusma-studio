<?php

declare(strict_types=1);

require_once __DIR__ . '/_helpers.php';

try {
    $categorySlug = apiRequestSlug('category');
    $entitySlug = apiRequestSlug('entity');
    $payload = contentFetchCollectionsByEntity(apiGetConnection(), $categorySlug, $entitySlug, true);

    if ($payload === []) {
        apiJsonError('No se encontro la entidad solicitada.', 404);
    }

    apiJsonSuccess($payload);
} catch (Throwable $exception) {
    apiHandleException($exception, 'No se pudieron cargar las colecciones.');
}