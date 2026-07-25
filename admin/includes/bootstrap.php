<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/database.php';

function adminRedirect(string $message): never
{
    header('Location: /Agusma-studio/admin/?message=' . urlencode($message));
    exit;
}

function adminPostString(string $key): string
{
    return trim((string) ($_POST[$key] ?? ''));
}

function adminPostNullableString(string $key): ?string
{
    $value = adminPostString($key);

    return $value === '' ? null : $value;
}

function adminPostInt(string $key, int $default = 0): int
{
    return max(0, (int) ($_POST[$key] ?? $default));
}

function adminPostNullableInt(string $key): ?int
{
    $rawValue = trim((string) ($_POST[$key] ?? ''));

    if ($rawValue === '') {
        return null;
    }

    return max(0, (int) $rawValue);
}

function adminPostBool(string $key): int
{
    return isset($_POST[$key]) ? 1 : 0;
}

function adminPostDateTime(string $key): ?string
{
    $value = trim((string) ($_POST[$key] ?? ''));

    if ($value === '') {
        return null;
    }

    if (!preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $value)) {
        return null;
    }

    return str_replace('T', ' ', $value) . ':00';
}

function adminRequireSlug(string $slug): string
{
    if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
        throw new InvalidArgumentException('El slug no es valido.');
    }

    return $slug;
}

function adminDisallowNationalTeamsCategory(string $slug): void
{
    if ($slug === 'national-teams') {
        throw new InvalidArgumentException('National Teams ya no se administra como categoria independiente.');
    }
}

function adminNormalizeLink(?string $value): string
{
    return $value === null || $value === '' ? '#' : $value;
}

function adminFormatDateTimeInput(?string $value): string
{
    if ($value === null || $value === '') {
        return '';
    }

    return str_replace(' ', 'T', substr($value, 0, 16));
}