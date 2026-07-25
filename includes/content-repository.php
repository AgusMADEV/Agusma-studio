<?php

declare(strict_types=1);

function contentFetchCategories(PDO $connection, bool $activeOnly = true): array
{
    $sql = 'SELECT id, name, slug, short_description, description, visual_key, cover_image, link_url, display_order, is_active
        FROM categories';

    if ($activeOnly) {
        $sql .= ' WHERE is_active = 1';
    }

    $sql .= ' ORDER BY display_order ASC, id ASC';

    return $connection->query($sql)->fetchAll();
}

function contentFetchCategoryBySlug(PDO $connection, string $slug, bool $activeOnly = true): ?array
{
    $sql = 'SELECT id, name, slug, short_description, description, visual_key, cover_image, link_url, display_order, is_active
        FROM categories
        WHERE slug = :slug';

    if ($activeOnly) {
        $sql .= ' AND is_active = 1';
    }

    $statement = $connection->prepare($sql);
    $statement->execute([':slug' => $slug]);

    $category = $statement->fetch();

    return is_array($category) ? $category : null;
}

function contentFetchEntityBySlug(PDO $connection, string $categorySlug, string $entitySlug, bool $activeOnly = true): ?array
{
    $sql = 'SELECT
            c.id AS category_id,
            c.name AS category_name,
            c.slug AS category_slug,
            c.short_description AS category_short_description,
            c.description AS category_description,
            c.visual_key AS category_visual_key,
            c.cover_image AS category_cover_image,
            c.link_url AS category_link_url,
            e.id,
            e.name,
            e.slug,
            e.entity_type,
            e.subtitle,
            e.short_description,
            e.description,
            e.logo_url,
            e.cover_image,
            e.primary_color,
            e.secondary_color,
            e.background_color,
            e.text_color,
            e.display_order,
            e.is_featured,
            e.is_active
        FROM entities e
        INNER JOIN categories c ON c.id = e.category_id
        WHERE c.slug = :category_slug
          AND e.slug = :entity_slug';

    if ($activeOnly) {
        $sql .= ' AND c.is_active = 1 AND e.is_active = 1';
    }

    $statement = $connection->prepare($sql);
    $statement->execute([
        ':category_slug' => $categorySlug,
        ':entity_slug' => $entitySlug,
    ]);

    $entity = $statement->fetch();

    return is_array($entity) ? $entity : null;
}

function contentFetchEntitiesByCategory(PDO $connection, string $categorySlug, ?string $entityType = null, bool $activeOnly = true): array
{
    $category = contentFetchCategoryBySlug($connection, $categorySlug, $activeOnly);

    if ($category === null) {
        return [];
    }

    $sql = 'SELECT id, category_id, name, slug, entity_type, subtitle, short_description, description,
            logo_url, cover_image, primary_color, secondary_color, background_color, text_color,
            display_order, is_featured, is_active
        FROM entities
        WHERE category_id = :category_id';

    $parameters = [':category_id' => (int) $category['id']];

    if ($activeOnly) {
        $sql .= ' AND is_active = 1';
    }

    if ($entityType !== null) {
        $sql .= ' AND entity_type = :entity_type';
        $parameters[':entity_type'] = $entityType;
    }

    $sql .= ' ORDER BY is_featured DESC, display_order ASC, id ASC';

    $statement = $connection->prepare($sql);
    $statement->execute($parameters);

    return [
        'category' => $category,
        'entities' => $statement->fetchAll(),
    ];
}

function contentFetchCollectionsByEntity(PDO $connection, string $categorySlug, string $entitySlug, bool $activeOnly = true): array
{
    $entity = contentFetchEntityBySlug($connection, $categorySlug, $entitySlug, $activeOnly);

    if ($entity === null) {
        return [];
    }

    $sql = 'SELECT
            col.id,
            col.entity_id,
            col.name,
            col.slug,
            col.subtitle,
            col.collection_year,
            col.season,
            col.short_description,
            col.description,
            col.concept,
            col.cover_image,
            col.thumbnail_image,
            col.primary_color,
            col.secondary_color,
            col.background_color,
            col.text_color,
            col.image_variant,
            col.layout_style,
            col.display_order,
            col.is_featured,
            col.is_active,
            col.published_at,
            COUNT(p.id) AS piece_count
        FROM collections col
        LEFT JOIN pieces p ON p.collection_id = col.id AND p.is_active = 1
        WHERE col.entity_id = :entity_id';

    if ($activeOnly) {
        $sql .= ' AND col.is_active = 1';
    }

    $sql .= ' GROUP BY col.id
        ORDER BY col.is_featured DESC, col.display_order ASC, col.id ASC';

    $statement = $connection->prepare($sql);
    $statement->execute([':entity_id' => (int) $entity['id']]);

    return [
        'category' => [
            'id' => $entity['category_id'],
            'name' => $entity['category_name'],
            'slug' => $entity['category_slug'],
            'short_description' => $entity['category_short_description'],
            'description' => $entity['category_description'],
            'visual_key' => $entity['category_visual_key'],
            'cover_image' => $entity['category_cover_image'],
            'link_url' => $entity['category_link_url'],
        ],
        'entity' => [
            'id' => $entity['id'],
            'name' => $entity['name'],
            'slug' => $entity['slug'],
            'entity_type' => $entity['entity_type'],
            'subtitle' => $entity['subtitle'],
            'short_description' => $entity['short_description'],
            'description' => $entity['description'],
            'logo_url' => $entity['logo_url'],
            'cover_image' => $entity['cover_image'],
            'primary_color' => $entity['primary_color'],
            'secondary_color' => $entity['secondary_color'],
            'background_color' => $entity['background_color'],
            'text_color' => $entity['text_color'],
            'display_order' => $entity['display_order'],
            'is_featured' => $entity['is_featured'],
            'is_active' => $entity['is_active'],
        ],
        'collections' => $statement->fetchAll(),
    ];
}

function contentFetchCollectionRecord(PDO $connection, string $categorySlug, string $entitySlug, string $collectionSlug, bool $activeOnly = true): ?array
{
    $sql = 'SELECT
            c.id AS category_id,
            c.name AS category_name,
            c.slug AS category_slug,
            c.short_description AS category_short_description,
            c.description AS category_description,
            c.visual_key AS category_visual_key,
            c.cover_image AS category_cover_image,
            c.link_url AS category_link_url,
            e.id AS entity_id,
            e.name AS entity_name,
            e.slug AS entity_slug,
            e.entity_type,
            e.subtitle AS entity_subtitle,
            e.short_description AS entity_short_description,
            e.description AS entity_description,
            e.logo_url,
            e.cover_image AS entity_cover_image,
            e.primary_color AS entity_primary_color,
            e.secondary_color AS entity_secondary_color,
            e.background_color AS entity_background_color,
            e.text_color AS entity_text_color,
            col.id,
            col.name,
            col.slug,
            col.subtitle,
            col.collection_year,
            col.season,
            col.short_description,
            col.description,
            col.concept,
            col.cover_image,
            col.thumbnail_image,
            col.primary_color,
            col.secondary_color,
            col.background_color,
            col.text_color,
            col.image_variant,
            col.layout_style,
            col.display_order,
            col.is_featured,
            col.is_active,
            col.published_at
        FROM collections col
        INNER JOIN entities e ON e.id = col.entity_id
        INNER JOIN categories c ON c.id = e.category_id
        WHERE c.slug = :category_slug
          AND e.slug = :entity_slug
          AND col.slug = :collection_slug';

    if ($activeOnly) {
        $sql .= ' AND c.is_active = 1 AND e.is_active = 1 AND col.is_active = 1';
    }

    $statement = $connection->prepare($sql);
    $statement->execute([
        ':category_slug' => $categorySlug,
        ':entity_slug' => $entitySlug,
        ':collection_slug' => $collectionSlug,
    ]);

    $collection = $statement->fetch();

    return is_array($collection) ? $collection : null;
}

function contentFetchPiecesForCollection(PDO $connection, int $collectionId, bool $activeOnly = true): array
{
    $sql = 'SELECT id, collection_id, name, slug, piece_type, subtitle, short_description, description,
            cover_image, display_order, is_featured, is_active
        FROM pieces
        WHERE collection_id = :collection_id';

    if ($activeOnly) {
        $sql .= ' AND is_active = 1';
    }

    $sql .= ' ORDER BY display_order ASC, id ASC';

    $statement = $connection->prepare($sql);
    $statement->execute([':collection_id' => $collectionId]);

    return $statement->fetchAll();
}

function contentFetchCollectionMedia(PDO $connection, int $collectionId, bool $activeOnly = true): array
{
    $sql = 'SELECT id, collection_id, piece_id, media_type, file_url, thumbnail_url, title, alt_text,
            caption, section_key, display_order, is_cover, is_active
        FROM media
        WHERE collection_id = :collection_id';

    if ($activeOnly) {
        $sql .= ' AND is_active = 1';
    }

    $sql .= ' ORDER BY is_cover DESC, display_order ASC, id ASC';

    $statement = $connection->prepare($sql);
    $statement->execute([':collection_id' => $collectionId]);

    return $statement->fetchAll();
}

function contentFetchCollectionTags(PDO $connection, int $collectionId): array
{
    $statement = $connection->prepare(
        'SELECT t.id, t.name, t.slug
        FROM collection_tags ct
        INNER JOIN tags t ON t.id = ct.tag_id
        WHERE ct.collection_id = :collection_id
        ORDER BY t.name ASC, t.id ASC'
    );
    $statement->execute([':collection_id' => $collectionId]);

    return $statement->fetchAll();
}

function contentFetchCollectionDetail(PDO $connection, string $categorySlug, string $entitySlug, string $collectionSlug, bool $activeOnly = true): ?array
{
    $collection = contentFetchCollectionRecord($connection, $categorySlug, $entitySlug, $collectionSlug, $activeOnly);

    if ($collection === null) {
        return null;
    }

    $pieces = contentFetchPiecesForCollection($connection, (int) $collection['id'], $activeOnly);
    $media = contentFetchCollectionMedia($connection, (int) $collection['id'], $activeOnly);
    $tags = contentFetchCollectionTags($connection, (int) $collection['id']);
    $mediaByPiece = [];
    $generalMedia = [];

    foreach ($media as $mediaItem) {
        $pieceId = $mediaItem['piece_id'];

        if ($pieceId === null) {
            $generalMedia[] = $mediaItem;
            continue;
        }

        $mediaByPiece[(string) $pieceId][] = $mediaItem;
    }

    foreach ($pieces as &$piece) {
        $piece['media'] = $mediaByPiece[(string) $piece['id']] ?? [];
    }
    unset($piece);

    return [
        'category' => [
            'id' => $collection['category_id'],
            'name' => $collection['category_name'],
            'slug' => $collection['category_slug'],
            'short_description' => $collection['category_short_description'],
            'description' => $collection['category_description'],
            'visual_key' => $collection['category_visual_key'],
            'cover_image' => $collection['category_cover_image'],
            'link_url' => $collection['category_link_url'],
        ],
        'entity' => [
            'id' => $collection['entity_id'],
            'name' => $collection['entity_name'],
            'slug' => $collection['entity_slug'],
            'entity_type' => $collection['entity_type'],
            'subtitle' => $collection['entity_subtitle'],
            'short_description' => $collection['entity_short_description'],
            'description' => $collection['entity_description'],
            'logo_url' => $collection['logo_url'],
            'cover_image' => $collection['entity_cover_image'],
            'primary_color' => $collection['entity_primary_color'],
            'secondary_color' => $collection['entity_secondary_color'],
            'background_color' => $collection['entity_background_color'],
            'text_color' => $collection['entity_text_color'],
        ],
        'collection' => [
            'id' => $collection['id'],
            'name' => $collection['name'],
            'slug' => $collection['slug'],
            'subtitle' => $collection['subtitle'],
            'collection_year' => $collection['collection_year'],
            'season' => $collection['season'],
            'short_description' => $collection['short_description'],
            'description' => $collection['description'],
            'concept' => $collection['concept'],
            'cover_image' => $collection['cover_image'],
            'thumbnail_image' => $collection['thumbnail_image'],
            'primary_color' => $collection['primary_color'],
            'secondary_color' => $collection['secondary_color'],
            'background_color' => $collection['background_color'],
            'text_color' => $collection['text_color'],
            'image_variant' => $collection['image_variant'],
            'layout_style' => $collection['layout_style'],
            'display_order' => $collection['display_order'],
            'is_featured' => $collection['is_featured'],
            'is_active' => $collection['is_active'],
            'published_at' => $collection['published_at'],
        ],
        'pieces' => $pieces,
        'media' => $generalMedia,
        'media_by_piece' => $mediaByPiece,
        'tags' => $tags,
    ];
}

function contentFetchFeaturedCollections(PDO $connection): array
{
    $statement = $connection->query(
        'SELECT
            col.id,
            col.name,
            col.slug AS collection_slug,
            col.subtitle,
            col.collection_year,
            col.season,
            col.short_description,
            col.cover_image,
            col.thumbnail_image,
            col.image_variant,
            col.display_order,
            e.name AS entity_name,
            e.slug AS entity_slug,
            e.entity_type,
            c.name AS category_name,
            c.slug AS category_slug
        FROM collections col
        INNER JOIN entities e ON e.id = col.entity_id
        INNER JOIN categories c ON c.id = e.category_id
        WHERE col.is_featured = 1
          AND col.is_active = 1
          AND e.is_active = 1
          AND c.is_active = 1
        ORDER BY col.display_order ASC, col.id ASC'
    );

    return $statement->fetchAll();
}