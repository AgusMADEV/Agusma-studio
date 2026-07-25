<?php

declare(strict_types=1);

require_once __DIR__ . '/_helpers.php';

try {
    apiJsonSuccess(contentFetchCategories(apiGetConnection(), true));
} catch (Throwable $exception) {
    apiHandleException($exception, 'No se pudieron cargar las categorias.');
}