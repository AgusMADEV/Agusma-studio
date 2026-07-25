<?php

declare(strict_types=1);

require_once __DIR__ . '/_helpers.php';

try {
    apiJsonSuccess(contentFetchFeaturedCollections(apiGetConnection()));
} catch (Throwable $exception) {
    apiHandleException($exception, 'No se pudieron cargar las colecciones destacadas.');
}