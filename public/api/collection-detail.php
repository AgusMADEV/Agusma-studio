<?php

declare(strict_types=1);

require_once __DIR__ . '/_helpers.php';

try {
    $categorySlug = apiRequestSlug('category');
    $entitySlug = apiRequestSlug('entity');
    $collectionSlug = apiRequestSlug('collection');
    $previewToken = trim((string) ($_GET['preview'] ?? ''));
    $connection = apiGetConnection();

    if ($previewToken !== '') {
        if (!preg_match('/^[a-f0-9]{64}$/', $previewToken)) {
            apiJsonError('El enlace de preview no es valido.', 403);
        }

        $previewRecord = contentFetchCollectionRecord(
            $connection,
            $categorySlug,
            $entitySlug,
            $collectionSlug,
            true,
            false
        );

        if ($previewRecord === null
            || !is_string($previewRecord['preview_token'] ?? null)
            || !hash_equals((string) $previewRecord['preview_token'], $previewToken)) {
            apiJsonError('El enlace de preview no es valido o ha caducado.', 403);
        }

        $payload = contentFetchCollectionDetail(
            $connection,
            $categorySlug,
            $entitySlug,
            $collectionSlug,
            true,
            false
        );
    } else {
        $payload = contentFetchCollectionDetail(
            $connection,
            $categorySlug,
            $entitySlug,
            $collectionSlug,
            true,
            true
        );
    }

    if ($payload === null) {
        apiJsonError('No se encontro la coleccion solicitada.', 404);
    }

    $payload['preview_mode'] = $previewToken !== '';
    apiJsonSuccess($payload);
} catch (Throwable $exception) {
    apiHandleException($exception, 'No se pudo cargar el detalle de la coleccion.');
}
