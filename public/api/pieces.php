<?php

declare(strict_types=1);

require_once __DIR__ . '/_helpers.php';

try {
    $categorySlug = apiRequestSlug('category');
    $entitySlug = apiRequestSlug('entity');
    $collectionSlug = apiRequestSlug('collection');
    $collection = contentFetchCollectionRecord(apiGetConnection(), $categorySlug, $entitySlug, $collectionSlug, true);

    if ($collection === null) {
        apiJsonError('No se encontro la coleccion solicitada.', 404);
    }

    $pieces = contentFetchPiecesForCollection(apiGetConnection(), (int) $collection['id'], true);

    apiJsonSuccess([
        'category' => [
            'id' => $collection['category_id'],
            'name' => $collection['category_name'],
            'slug' => $collection['category_slug'],
        ],
        'entity' => [
            'id' => $collection['entity_id'],
            'name' => $collection['entity_name'],
            'slug' => $collection['entity_slug'],
            'entity_type' => $collection['entity_type'],
        ],
        'collection' => [
            'id' => $collection['id'],
            'name' => $collection['name'],
            'slug' => $collection['slug'],
        ],
        'pieces' => $pieces,
    ]);
} catch (Throwable $exception) {
    apiHandleException($exception, 'No se pudieron cargar las piezas.');
}