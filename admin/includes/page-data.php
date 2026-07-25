<?php

declare(strict_types=1);

function adminLegacyFeaturedCount(PDO $connection): int
{
    $statement = $connection->query("SHOW TABLES LIKE 'featured_collections'");

    if ($statement->fetchColumn() === false) {
        return 0;
    }

    return (int) $connection->query('SELECT COUNT(*) FROM featured_collections')->fetchColumn();
}

function adminLoadDashboardData(PDO $connection): array
{
    $categories = $connection->query(
        "SELECT id, name, slug, short_description, description, visual_key, cover_image, link_url, display_order, is_active
        FROM categories
        WHERE slug <> 'national-teams'
        ORDER BY display_order ASC, id ASC"
    )->fetchAll();

    $entities = $connection->query(
        "SELECT e.id, e.category_id, e.name, e.slug, e.entity_type, e.subtitle, e.short_description, e.description,
                e.logo_url, e.cover_image, e.primary_color, e.secondary_color, e.background_color, e.text_color,
                e.display_order, e.is_featured, e.is_active, c.name AS category_name, c.slug AS category_slug
        FROM entities e
        INNER JOIN categories c ON c.id = e.category_id
        ORDER BY c.display_order ASC, e.display_order ASC, e.id ASC"
    )->fetchAll();

    $collections = $connection->query(
        "SELECT col.id, col.entity_id, col.name, col.slug, col.subtitle, col.collection_year, col.season,
                col.short_description, col.description, col.concept, col.cover_image, col.thumbnail_image,
                col.primary_color, col.secondary_color, col.background_color, col.text_color, col.image_variant,
                col.layout_style, col.display_order, col.is_featured, col.is_active, col.published_at,
                e.name AS entity_name, e.slug AS entity_slug, e.entity_type, c.name AS category_name, c.slug AS category_slug
        FROM collections col
        INNER JOIN entities e ON e.id = col.entity_id
        INNER JOIN categories c ON c.id = e.category_id
        ORDER BY c.display_order ASC, e.display_order ASC, col.display_order ASC, col.id ASC"
    )->fetchAll();

    $pieces = $connection->query(
        "SELECT p.id, p.collection_id, p.name, p.slug, p.piece_type, p.subtitle, p.short_description, p.description,
                p.cover_image, p.display_order, p.is_featured, p.is_active,
                col.name AS collection_name, e.name AS entity_name, c.name AS category_name
        FROM pieces p
        INNER JOIN collections col ON col.id = p.collection_id
        INNER JOIN entities e ON e.id = col.entity_id
        INNER JOIN categories c ON c.id = e.category_id
        ORDER BY c.display_order ASC, e.display_order ASC, col.display_order ASC, p.display_order ASC, p.id ASC"
    )->fetchAll();

    $mediaItems = $connection->query(
        "SELECT m.id, m.collection_id, m.piece_id, m.media_type, m.file_url, m.thumbnail_url, m.title, m.alt_text,
                m.caption, m.section_key, m.display_order, m.is_cover, m.is_active,
                col.name AS collection_name, p.name AS piece_name
        FROM media m
        INNER JOIN collections col ON col.id = m.collection_id
        LEFT JOIN pieces p ON p.id = m.piece_id
        ORDER BY col.display_order ASC, m.display_order ASC, m.id ASC"
    )->fetchAll();

    $legacyFeaturedCount = adminLegacyFeaturedCount($connection);

    return [
        'categories' => $categories,
        'entities' => $entities,
        'collections' => $collections,
        'pieces' => $pieces,
        'mediaItems' => $mediaItems,
        'legacyFeaturedCount' => $legacyFeaturedCount,
    ];
}