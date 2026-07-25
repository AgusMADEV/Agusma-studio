<?php

declare(strict_types=1);

require_once __DIR__ . '/_helpers.php';

try {
    $categorySlug = apiRequestSlug('category');
    $entitySlug = apiRequestSlug('entity');
    $collectionSlug = apiRequestSlug('collection');
    $payload = contentFetchCollectionDetail(apiGetConnection(), $categorySlug, $entitySlug, $collectionSlug, true);

    if ($payload === null) {
        apiJsonError('No se encontro la coleccion solicitada.', 404);
    }

    apiJsonSuccess($payload);
} catch (Throwable $exception) {
    apiHandleException($exception, 'No se pudo cargar el detalle de la coleccion.');
}