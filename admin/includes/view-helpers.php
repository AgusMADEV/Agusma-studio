<?php

declare(strict_types=1);

function adminEscape(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function adminChecked(mixed $value): string
{
    return (int) $value === 1 ? 'checked' : '';
}

function adminSelected(mixed $left, mixed $right): string
{
    return (string) $left === (string) $right ? 'selected' : '';
}

function adminCategoryLabel(array $category): string
{
    return (string) ($category['name'] ?? '');
}

function adminEntityLabel(array $entity): string
{
    $type = trim((string) ($entity['entity_type'] ?? ''));
    $categoryName = trim((string) ($entity['category_name'] ?? ''));
    $suffix = $type !== '' ? sprintf(' · %s', $type) : '';

    if ($categoryName !== '') {
        return sprintf('%s (%s)%s', (string) $entity['name'], $categoryName, $suffix);
    }

    return sprintf('%s%s', (string) $entity['name'], $suffix);
}

function adminCollectionLabel(array $collection): string
{
    $entityName = trim((string) ($collection['entity_name'] ?? ''));

    if ($entityName === '') {
        return (string) ($collection['name'] ?? '');
    }

    return sprintf('%s (%s)', (string) $collection['name'], $entityName);
}

function adminPieceLabel(array $piece): string
{
    $collectionName = trim((string) ($piece['collection_name'] ?? ''));

    if ($collectionName === '') {
        return (string) ($piece['name'] ?? '');
    }

    return sprintf('%s (%s)', (string) ($piece['name'] ?? ''), $collectionName);
}

function adminFirstNonEmpty(array $values, string $fallback = ''): string
{
    foreach ($values as $value) {
        $normalized = trim((string) $value);

        if ($normalized !== '') {
            return $normalized;
        }
    }

    return $fallback;
}

function adminRecordPreviewImage(array $record, array $keys): string
{
    $scriptName = (string) ($_SERVER['SCRIPT_NAME'] ?? '');
    $basePath = trim(str_replace('\\', '/', dirname(dirname($scriptName))), '/');
    $publicPrefix = $basePath === '' ? '/public/' : '/' . $basePath . '/public/';

    foreach ($keys as $key) {
        $value = trim((string) ($record[$key] ?? ''));

        if ($value !== '') {
            if (
                str_starts_with($value, 'http://')
                || str_starts_with($value, 'https://')
                || str_starts_with($value, 'data:')
                || str_starts_with($value, '/')
                || str_starts_with($value, '../')
            ) {
                return $value;
            }

            if (str_starts_with($value, './')) {
                return $publicPrefix . substr($value, 2);
            }

            return $publicPrefix . ltrim($value, '/');
        }
    }

    return '';
}

function adminFormatDateLabel(?string $value): string
{
    if ($value === null || trim($value) === '') {
        return 'Sin fecha';
    }

    $timestamp = strtotime($value);

    if ($timestamp === false) {
        return $value;
    }

    $months = ['ene.', 'feb.', 'mar.', 'abr.', 'may.', 'jun.', 'jul.', 'ago.', 'sep.', 'oct.', 'nov.', 'dic.'];

    return sprintf(
        '%d %s %d, %02d:%02d',
        (int) date('j', $timestamp),
        $months[(int) date('n', $timestamp) - 1],
        (int) date('Y', $timestamp),
        (int) date('H', $timestamp),
        (int) date('i', $timestamp)
    );
}