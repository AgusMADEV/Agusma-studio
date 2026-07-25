<?php

declare(strict_types=1);

function adminSaveRecord(PDO $connection, string $table, array $data, ?int $id = null): void
{
    $allowedTables = ['categories', 'entities', 'collections', 'pieces', 'media'];

    if (!in_array($table, $allowedTables, true)) {
        throw new InvalidArgumentException('Tabla no permitida.');
    }

    if ($id === null) {
        $columns = array_keys($data);
        $placeholders = array_map(static fn (string $column): string => ':' . $column, $columns);
        $statement = $connection->prepare(
            sprintf(
                'INSERT INTO %s (%s) VALUES (%s)',
                $table,
                implode(', ', $columns),
                implode(', ', $placeholders)
            )
        );
        $statement->execute(array_combine($placeholders, array_values($data)) ?: []);
        return;
    }

    $assignments = array_map(static fn (string $column): string => sprintf('%s = :%s', $column, $column), array_keys($data));
    $parameters = array_combine(
        array_map(static fn (string $column): string => ':' . $column, array_keys($data)),
        array_values($data)
    ) ?: [];
    $parameters[':id'] = $id;

    $statement = $connection->prepare(
        sprintf('UPDATE %s SET %s WHERE id = :id', $table, implode(', ', $assignments))
    );
    $statement->execute($parameters);
}

function adminDeleteRecord(PDO $connection, string $table, int $id): void
{
    $allowedTables = ['categories', 'entities', 'collections', 'pieces', 'media'];

    if (!in_array($table, $allowedTables, true)) {
        throw new InvalidArgumentException('Tabla no permitida.');
    }

    $statement = $connection->prepare(sprintf('DELETE FROM %s WHERE id = :id', $table));
    $statement->execute([':id' => $id]);
}

function adminAssertCollectionExists(PDO $connection, int $collectionId): void
{
    $statement = $connection->prepare('SELECT id FROM collections WHERE id = :id');
    $statement->execute([':id' => $collectionId]);

    if ($statement->fetchColumn() === false) {
        throw new InvalidArgumentException('La coleccion seleccionada no existe.');
    }
}

function adminAssertPieceBelongsToCollection(PDO $connection, int $pieceId, int $collectionId): void
{
    $statement = $connection->prepare('SELECT id FROM pieces WHERE id = :id AND collection_id = :collection_id');
    $statement->execute([
        ':id' => $pieceId,
        ':collection_id' => $collectionId,
    ]);

    if ($statement->fetchColumn() === false) {
        throw new InvalidArgumentException('La pieza seleccionada no pertenece a la coleccion elegida.');
    }
}

function adminBuildCategoryData(): array
{
    $slug = adminRequireSlug(adminPostString('slug'));
    adminDisallowNationalTeamsCategory($slug);

    return [
        'name' => adminPostString('name'),
        'slug' => $slug,
        'short_description' => adminPostNullableString('short_description'),
        'description' => adminPostNullableString('description'),
        'visual_key' => adminPostString('visual_key'),
        'cover_image' => adminPostNullableString('cover_image'),
        'link_url' => adminNormalizeLink(adminPostNullableString('link_url')),
        'display_order' => adminPostInt('display_order'),
        'is_active' => adminPostBool('is_active'),
    ];
}

function adminBuildEntityData(): array
{
    return [
        'category_id' => adminPostInt('category_id'),
        'name' => adminPostString('name'),
        'slug' => adminRequireSlug(adminPostString('slug')),
        'entity_type' => adminPostString('entity_type') ?: 'other',
        'subtitle' => adminPostNullableString('subtitle'),
        'short_description' => adminPostNullableString('short_description'),
        'description' => adminPostNullableString('description'),
        'logo_url' => adminPostNullableString('logo_url'),
        'cover_image' => adminPostNullableString('cover_image'),
        'primary_color' => adminPostNullableString('primary_color'),
        'secondary_color' => adminPostNullableString('secondary_color'),
        'background_color' => adminPostNullableString('background_color'),
        'text_color' => adminPostNullableString('text_color'),
        'display_order' => adminPostInt('display_order'),
        'is_featured' => adminPostBool('is_featured'),
        'is_active' => adminPostBool('is_active'),
    ];
}

function adminBuildCollectionData(): array
{
    return [
        'entity_id' => adminPostInt('entity_id'),
        'name' => adminPostString('name'),
        'slug' => adminRequireSlug(adminPostString('slug')),
        'subtitle' => adminPostNullableString('subtitle'),
        'collection_year' => adminPostNullableInt('collection_year'),
        'season' => adminPostNullableString('season'),
        'short_description' => adminPostNullableString('short_description'),
        'description' => adminPostNullableString('description'),
        'concept' => adminPostNullableString('concept'),
        'cover_image' => adminPostNullableString('cover_image'),
        'thumbnail_image' => adminPostNullableString('thumbnail_image'),
        'primary_color' => adminPostNullableString('primary_color'),
        'secondary_color' => adminPostNullableString('secondary_color'),
        'background_color' => adminPostNullableString('background_color'),
        'text_color' => adminPostNullableString('text_color'),
        'image_variant' => adminPostNullableString('image_variant'),
        'layout_style' => adminPostNullableString('layout_style'),
        'display_order' => adminPostInt('display_order'),
        'is_featured' => adminPostBool('is_featured'),
        'is_active' => adminPostBool('is_active'),
        'published_at' => adminPostDateTime('published_at'),
    ];
}

function adminBuildPieceData(): array
{
    return [
        'collection_id' => adminPostInt('collection_id'),
        'name' => adminPostString('name'),
        'slug' => adminRequireSlug(adminPostString('slug')),
        'piece_type' => adminPostString('piece_type') ?: 'other',
        'subtitle' => adminPostNullableString('subtitle'),
        'short_description' => adminPostNullableString('short_description'),
        'description' => adminPostNullableString('description'),
        'cover_image' => adminPostNullableString('cover_image'),
        'display_order' => adminPostInt('display_order'),
        'is_featured' => adminPostBool('is_featured'),
        'is_active' => adminPostBool('is_active'),
    ];
}

function adminBuildMediaData(PDO $connection): array
{
    $collectionId = adminPostInt('collection_id');
    $pieceId = adminPostNullableInt('piece_id');

    adminAssertCollectionExists($connection, $collectionId);

    if ($pieceId !== null) {
        adminAssertPieceBelongsToCollection($connection, $pieceId, $collectionId);
    }

    return [
        'collection_id' => $collectionId,
        'piece_id' => $pieceId,
        'media_type' => adminPostString('media_type') ?: 'image',
        'file_url' => adminPostString('file_url'),
        'thumbnail_url' => adminPostNullableString('thumbnail_url'),
        'title' => adminPostNullableString('title'),
        'alt_text' => adminPostNullableString('alt_text'),
        'caption' => adminPostNullableString('caption'),
        'section_key' => adminPostNullableString('section_key'),
        'display_order' => adminPostInt('display_order'),
        'is_cover' => adminPostBool('is_cover'),
        'is_active' => adminPostBool('is_active'),
    ];
}

function adminHandlePost(PDO $connection): never
{
    $action = adminPostString('submit_action');

    if ($action === '') {
        $action = adminPostString('action');
    }

    if ($action === '') {
        adminRedirect('Accion no reconocida.');
    }

    try {
        $connection->beginTransaction();

        switch ($action) {
            case 'create_category':
                adminSaveRecord($connection, 'categories', adminBuildCategoryData(), null);
                $message = 'Categoria creada.';
                break;
            case 'update_category':
                adminSaveRecord($connection, 'categories', adminBuildCategoryData(), adminPostInt('id'));
                $message = 'Categoria actualizada.';
                break;
            case 'delete_category':
                adminDeleteRecord($connection, 'categories', adminPostInt('id'));
                $message = 'Categoria eliminada.';
                break;
            case 'create_entity':
                adminSaveRecord($connection, 'entities', adminBuildEntityData(), null);
                $message = 'Entidad creada.';
                break;
            case 'update_entity':
                adminSaveRecord($connection, 'entities', adminBuildEntityData(), adminPostInt('id'));
                $message = 'Entidad actualizada.';
                break;
            case 'delete_entity':
                adminDeleteRecord($connection, 'entities', adminPostInt('id'));
                $message = 'Entidad eliminada.';
                break;
            case 'create_collection':
                adminSaveRecord($connection, 'collections', adminBuildCollectionData(), null);
                $message = 'Coleccion creada.';
                break;
            case 'update_collection':
                adminSaveRecord($connection, 'collections', adminBuildCollectionData(), adminPostInt('id'));
                $message = 'Coleccion actualizada.';
                break;
            case 'delete_collection':
                adminDeleteRecord($connection, 'collections', adminPostInt('id'));
                $message = 'Coleccion eliminada.';
                break;
            case 'create_piece':
                adminSaveRecord($connection, 'pieces', adminBuildPieceData(), null);
                $message = 'Pieza creada.';
                break;
            case 'update_piece':
                adminSaveRecord($connection, 'pieces', adminBuildPieceData(), adminPostInt('id'));
                $message = 'Pieza actualizada.';
                break;
            case 'delete_piece':
                adminDeleteRecord($connection, 'pieces', adminPostInt('id'));
                $message = 'Pieza eliminada.';
                break;
            case 'create_media':
                adminSaveRecord($connection, 'media', adminBuildMediaData($connection), null);
                $message = 'Multimedia creada.';
                break;
            case 'update_media':
                adminSaveRecord($connection, 'media', adminBuildMediaData($connection), adminPostInt('id'));
                $message = 'Multimedia actualizada.';
                break;
            case 'delete_media':
                adminDeleteRecord($connection, 'media', adminPostInt('id'));
                $message = 'Multimedia eliminada.';
                break;
            default:
                throw new InvalidArgumentException('Accion no reconocida.');
        }

        $connection->commit();
        adminRedirect($message);
    } catch (Throwable $exception) {
        if ($connection->inTransaction()) {
            $connection->rollBack();
        }

        error_log($exception->getMessage());
        adminRedirect($exception instanceof InvalidArgumentException ? $exception->getMessage() : 'No se pudo guardar el cambio.');
    }
}