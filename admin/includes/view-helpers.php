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

    return sprintf('%s (%s)', (string) $piece['name'], $collectionName);
}