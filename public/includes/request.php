<?php

declare(strict_types=1);

function publicPageSlugParam(string $key): ?string
{
    $value = trim((string) ($_GET[$key] ?? ''));

    if ($value === '') {
        return null;
    }

    if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $value)) {
        return null;
    }

    return $value;
}

function publicPageTitleFromSlug(?string $slug, string $fallback): string
{
    if ($slug === null) {
        return $fallback;
    }

    $label = str_replace('-', ' ', $slug);

    return ucwords($label);
}

function publicEscape(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}