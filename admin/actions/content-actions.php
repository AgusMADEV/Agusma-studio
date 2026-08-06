<?php

declare(strict_types=1);

function adminSaveRecord(PDO $connection, string $table, array $data, ?int $id = null): int
{
    $allowedTables = ['categories', 'entities', 'collections', 'collection_sections', 'pieces', 'media'];

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
        return (int) $connection->lastInsertId();
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

    return $id;
}

function adminDeleteRecord(PDO $connection, string $table, int $id): void
{
    $allowedTables = ['categories', 'entities', 'collections', 'collection_sections', 'pieces', 'media'];

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


function adminFetchSection(PDO $connection, int $sectionId): array
{
    $statement = $connection->prepare(
        'SELECT id, collection_id, section_key, section_type, title FROM collection_sections WHERE id = :id LIMIT 1'
    );
    $statement->execute([':id' => $sectionId]);
    $section = $statement->fetch();

    if (!is_array($section)) {
        throw new InvalidArgumentException('La seccion seleccionada no existe.');
    }

    return $section;
}

function adminFetchMedia(PDO $connection, int $mediaId): array
{
    $statement = $connection->prepare(
        'SELECT id, collection_id, piece_id, section_key, file_url, display_order, is_active FROM media WHERE id = :id LIMIT 1'
    );
    $statement->execute([':id' => $mediaId]);
    $media = $statement->fetch();

    if (!is_array($media)) {
        throw new InvalidArgumentException('El recurso multimedia seleccionado no existe.');
    }

    return $media;
}

function adminAssertSectionBelongsToCollection(PDO $connection, int $collectionId, string $sectionKey): void
{
    $statement = $connection->prepare(
        'SELECT id FROM collection_sections WHERE collection_id = :collection_id AND section_key = :section_key LIMIT 1'
    );
    $statement->execute([
        ':collection_id' => $collectionId,
        ':section_key' => $sectionKey,
    ]);

    if ($statement->fetchColumn() === false) {
        throw new InvalidArgumentException('La seccion indicada no pertenece a la coleccion seleccionada.');
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
        'hero_image' => adminPostNullableString('hero_image'),
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


function adminRequireSectionKey(string $sectionKey): string
{
    if (!preg_match('/^[a-z0-9]+(?:[-_][a-z0-9]+)*$/', $sectionKey)) {
        throw new InvalidArgumentException('La section key solo puede contener minusculas, numeros, guiones y guiones bajos.');
    }

    return $sectionKey;
}

function adminRequireSectionType(string $sectionType): string
{
    $allowedTypes = array_keys(adminSectionTypeOptions());

    if (!in_array($sectionType, $allowedTypes, true)) {
        throw new InvalidArgumentException('El tipo de seccion no es compatible con la vista publica.');
    }

    return $sectionType;
}

function adminNormalizeSettingsJson(?string $value): ?string
{
    if ($value === null || trim($value) === '') {
        return null;
    }

    try {
        $decoded = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException) {
        throw new InvalidArgumentException('Settings JSON no contiene un JSON valido.');
    }

    if (!is_array($decoded) || (array_is_list($decoded) && $decoded !== [])) {
        throw new InvalidArgumentException('Settings JSON debe ser un objeto JSON, por ejemplo {"alignment":"left"}.');
    }

    return (string) json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function adminAssertSectionKeyAvailable(
    PDO $connection,
    int $collectionId,
    string $sectionKey,
    ?int $excludeId = null
): void {
    $sql = 'SELECT id FROM collection_sections WHERE collection_id = :collection_id AND section_key = :section_key';
    $parameters = [
        ':collection_id' => $collectionId,
        ':section_key' => $sectionKey,
    ];

    if ($excludeId !== null) {
        $sql .= ' AND id <> :exclude_id';
        $parameters[':exclude_id'] = $excludeId;
    }

    $statement = $connection->prepare($sql . ' LIMIT 1');
    $statement->execute($parameters);

    if ($statement->fetchColumn() !== false) {
        throw new InvalidArgumentException('Ya existe una seccion con esa key dentro de la coleccion.');
    }
}

function adminNextSectionOrder(PDO $connection, int $collectionId): int
{
    $statement = $connection->prepare(
        'SELECT COALESCE(MAX(display_order), 0) + 1 FROM collection_sections WHERE collection_id = :collection_id'
    );
    $statement->execute([':collection_id' => $collectionId]);

    return max(1, (int) $statement->fetchColumn());
}

function adminBuildSectionData(PDO $connection, ?int $existingId = null): array
{
    $collectionId = adminPostInt('collection_id');
    $sectionKey = adminRequireSectionKey(adminPostString('section_key'));
    $sectionType = adminRequireSectionType(adminPostString('section_type'));

    adminAssertCollectionExists($connection, $collectionId);
    adminAssertSectionKeyAvailable($connection, $collectionId, $sectionKey, $existingId);

    $orderRaw = trim((string) ($_POST['display_order'] ?? ''));
    $displayOrder = $orderRaw === ''
        ? adminNextSectionOrder($connection, $collectionId)
        : max(0, (int) $orderRaw);

    return [
        'collection_id' => $collectionId,
        'section_key' => $sectionKey,
        'section_type' => $sectionType,
        'eyebrow' => adminPostNullableString('eyebrow'),
        'title' => adminPostNullableString('title'),
        'body' => adminPostNullableString('body'),
        'settings_json' => adminEncodeSectionVisualSettings(
            $sectionType,
            $_POST['visual_settings'][$sectionType] ?? null
        ),
        'display_order' => $displayOrder,
        'is_active' => adminPostBool('is_active'),
    ];
}

function adminMoveSection(PDO $connection, int $sectionId, int $direction): bool
{
    $statement = $connection->prepare('SELECT collection_id FROM collection_sections WHERE id = :id');
    $statement->execute([':id' => $sectionId]);
    $collectionId = $statement->fetchColumn();

    if ($collectionId === false) {
        throw new InvalidArgumentException('La seccion seleccionada no existe.');
    }

    $listStatement = $connection->prepare(
        'SELECT id FROM collection_sections WHERE collection_id = :collection_id ORDER BY display_order ASC, id ASC'
    );
    $listStatement->execute([':collection_id' => (int) $collectionId]);
    $ids = array_map('intval', $listStatement->fetchAll(PDO::FETCH_COLUMN));
    $currentIndex = array_search($sectionId, $ids, true);

    if ($currentIndex === false) {
        throw new InvalidArgumentException('No se pudo localizar la seccion dentro de su coleccion.');
    }

    $targetIndex = $currentIndex + $direction;

    if ($targetIndex < 0 || $targetIndex >= count($ids)) {
        return false;
    }

    [$ids[$currentIndex], $ids[$targetIndex]] = [$ids[$targetIndex], $ids[$currentIndex]];
    $update = $connection->prepare('UPDATE collection_sections SET display_order = :display_order WHERE id = :id');

    foreach ($ids as $index => $id) {
        $update->execute([
            ':display_order' => $index + 1,
            ':id' => $id,
        ]);
    }

    return true;
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

function adminNormalizeMediaType(string $mediaType): string
{
    $normalized = strtolower(trim($mediaType));

    if ($normalized === '') {
        return 'image';
    }

    if (preg_match('/^[a-z0-9]+(?:[-_][a-z0-9]+)*$/', $normalized) !== 1) {
        throw new InvalidArgumentException('El tipo multimedia solo puede contener minusculas, numeros, guiones y guiones bajos.');
    }

    return $normalized;
}

function adminStoreUploadedImage(string $fieldName, int $collectionId): ?string
{
    $file = $_FILES[$fieldName] ?? null;

    if (!is_array($file) || (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);

    if ($error !== UPLOAD_ERR_OK) {
        $messages = [
            UPLOAD_ERR_INI_SIZE => 'La imagen supera el limite configurado por el servidor.',
            UPLOAD_ERR_FORM_SIZE => 'La imagen supera el limite permitido por el formulario.',
            UPLOAD_ERR_PARTIAL => 'La imagen solo se ha subido parcialmente.',
            UPLOAD_ERR_NO_TMP_DIR => 'El servidor no dispone de carpeta temporal para la subida.',
            UPLOAD_ERR_CANT_WRITE => 'El servidor no ha podido guardar la imagen.',
            UPLOAD_ERR_EXTENSION => 'Una extension del servidor ha bloqueado la subida.',
        ];

        throw new InvalidArgumentException($messages[$error] ?? 'No se pudo subir la imagen.');
    }

    $temporaryPath = (string) ($file['tmp_name'] ?? '');
    $size = (int) ($file['size'] ?? 0);

    if ($temporaryPath === '' || !is_uploaded_file($temporaryPath)) {
        throw new InvalidArgumentException('El archivo recibido no es una subida valida.');
    }

    if ($size <= 0 || $size > 15 * 1024 * 1024) {
        throw new InvalidArgumentException('La imagen debe pesar como maximo 15 MB.');
    }

    if (!class_exists('finfo')) {
        throw new RuntimeException('La extension Fileinfo de PHP es necesaria para validar las imagenes.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = (string) $finfo->file($temporaryPath);
    $extensions = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/avif' => 'avif',
        'image/gif' => 'gif',
    ];

    if (!isset($extensions[$mimeType])) {
        throw new InvalidArgumentException('Formato no compatible. Usa JPG, PNG, WEBP, AVIF o GIF.');
    }

    $originalName = pathinfo((string) ($file['name'] ?? 'imagen'), PATHINFO_FILENAME);
    $safeName = strtolower((string) preg_replace('/[^a-zA-Z0-9]+/', '-', $originalName));
    $safeName = trim($safeName, '-');

    if ($safeName === '') {
        $safeName = 'imagen';
    }

    $directory = dirname(__DIR__, 2) . '/public/uploads/collections/' . $collectionId;

    if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
        throw new RuntimeException('No se pudo crear la carpeta de subidas.');
    }

    $fileName = sprintf(
        '%s-%s-%s.%s',
        date('Ymd-His'),
        bin2hex(random_bytes(4)),
        substr($safeName, 0, 80),
        $extensions[$mimeType]
    );
    $destination = $directory . '/' . $fileName;

    if (!move_uploaded_file($temporaryPath, $destination)) {
        throw new RuntimeException('No se pudo guardar la imagen subida.');
    }

    return './uploads/collections/' . $collectionId . '/' . $fileName;
}

function adminNextMediaOrder(
    PDO $connection,
    int $collectionId,
    ?string $sectionKey,
    ?int $pieceId = null
): int {
    $conditions = ['collection_id = :collection_id'];
    $parameters = [':collection_id' => $collectionId];

    if ($sectionKey === null) {
        $conditions[] = '(section_key IS NULL OR section_key = \'\')';
    } else {
        $conditions[] = 'section_key = :section_key';
        $parameters[':section_key'] = $sectionKey;
    }

    if ($pieceId === null) {
        $conditions[] = 'piece_id IS NULL';
    } else {
        $conditions[] = 'piece_id = :piece_id';
        $parameters[':piece_id'] = $pieceId;
    }

    $statement = $connection->prepare(
        'SELECT COALESCE(MAX(display_order), 0) + 1 FROM media WHERE ' . implode(' AND ', $conditions)
    );
    $statement->execute($parameters);

    return max(1, (int) $statement->fetchColumn());
}

function adminBuildMediaData(PDO $connection, array $overrides = []): array
{
    $collectionId = isset($overrides['collection_id'])
        ? (int) $overrides['collection_id']
        : adminPostInt('collection_id');
    $pieceId = array_key_exists('piece_id', $overrides)
        ? ($overrides['piece_id'] === null ? null : (int) $overrides['piece_id'])
        : adminPostNullableInt('piece_id');
    $sectionKey = array_key_exists('section_key', $overrides)
        ? ($overrides['section_key'] === null ? null : (string) $overrides['section_key'])
        : adminPostNullableString('section_key');

    adminAssertCollectionExists($connection, $collectionId);

    if ($pieceId !== null) {
        adminAssertPieceBelongsToCollection($connection, $pieceId, $collectionId);
    }

    if ($sectionKey !== null) {
        $sectionKey = adminRequireSectionKey($sectionKey);
        adminAssertSectionBelongsToCollection($connection, $collectionId, $sectionKey);
    }

    $uploadedPath = adminStoreUploadedImage('media_file', $collectionId);
    $fileUrl = $uploadedPath ?? adminPostString('file_url');

    if ($fileUrl === '') {
        throw new InvalidArgumentException('Selecciona una imagen o indica una URL/ruta de archivo.');
    }

    $orderRaw = trim((string) ($_POST['display_order'] ?? ''));
    $displayOrder = array_key_exists('display_order', $overrides)
        ? (int) $overrides['display_order']
        : ($orderRaw === ''
            ? adminNextMediaOrder($connection, $collectionId, $sectionKey, $pieceId)
            : max(0, (int) $orderRaw));

    return [
        'collection_id' => $collectionId,
        'piece_id' => $pieceId,
        'media_type' => adminNormalizeMediaType(adminPostString('media_type') ?: 'image'),
        'file_url' => $fileUrl,
        'thumbnail_url' => adminPostNullableString('thumbnail_url'),
        'title' => adminPostNullableString('title'),
        'alt_text' => adminPostNullableString('alt_text'),
        'caption' => adminPostNullableString('caption'),
        'section_key' => $sectionKey,
        'display_order' => $displayOrder,
        'is_cover' => adminPostBool('is_cover'),
        'is_active' => adminPostBool('is_active'),
    ];
}

function adminUpdateSectionAndLinkedMedia(PDO $connection, int $sectionId, array $data): void
{
    $current = adminFetchSection($connection, $sectionId);
    $oldCollectionId = (int) $current['collection_id'];
    $oldSectionKey = (string) $current['section_key'];
    $newCollectionId = (int) $data['collection_id'];
    $newSectionKey = (string) $data['section_key'];

    if ($oldCollectionId !== $newCollectionId) {
        $pieceMediaStatement = $connection->prepare(
            'SELECT COUNT(*) FROM media WHERE collection_id = :collection_id AND section_key = :section_key AND piece_id IS NOT NULL'
        );
        $pieceMediaStatement->execute([
            ':collection_id' => $oldCollectionId,
            ':section_key' => $oldSectionKey,
        ]);

        if ((int) $pieceMediaStatement->fetchColumn() > 0) {
            throw new InvalidArgumentException('No puedes mover la seccion a otra coleccion mientras tenga multimedia vinculada a piezas.');
        }
    }

    adminSaveRecord($connection, 'collection_sections', $data, $sectionId);

    if ($oldCollectionId !== $newCollectionId || $oldSectionKey !== $newSectionKey) {
        $statement = $connection->prepare(
            'UPDATE media
             SET collection_id = :new_collection_id, section_key = :new_section_key
             WHERE collection_id = :old_collection_id AND section_key = :old_section_key'
        );
        $statement->execute([
            ':new_collection_id' => $newCollectionId,
            ':new_section_key' => $newSectionKey,
            ':old_collection_id' => $oldCollectionId,
            ':old_section_key' => $oldSectionKey,
        ]);
    }
}

function adminDeleteSectionAndDetachMedia(PDO $connection, int $sectionId): void
{
    $section = adminFetchSection($connection, $sectionId);
    $statement = $connection->prepare(
        'UPDATE media SET section_key = NULL, display_order = 0
         WHERE collection_id = :collection_id AND section_key = :section_key'
    );
    $statement->execute([
        ':collection_id' => (int) $section['collection_id'],
        ':section_key' => (string) $section['section_key'],
    ]);
    adminDeleteRecord($connection, 'collection_sections', $sectionId);
}

function adminAssertMediaAttachedToSection(PDO $connection, int $sectionId, int $mediaId): array
{
    $section = adminFetchSection($connection, $sectionId);
    $media = adminFetchMedia($connection, $mediaId);

    if (
        (int) $media['collection_id'] !== (int) $section['collection_id']
        || (string) ($media['section_key'] ?? '') !== (string) $section['section_key']
        || $media['piece_id'] !== null
    ) {
        throw new InvalidArgumentException('El recurso no pertenece a esta seccion.');
    }

    return [$section, $media];
}

function adminAttachMediaToSection(PDO $connection, int $sectionId, int $mediaId): void
{
    $section = adminFetchSection($connection, $sectionId);
    $media = adminFetchMedia($connection, $mediaId);

    if ((int) $media['collection_id'] !== (int) $section['collection_id']) {
        throw new InvalidArgumentException('Solo puedes vincular recursos de la misma coleccion.');
    }

    if ($media['piece_id'] !== null) {
        throw new InvalidArgumentException('La multimedia de una pieza no se puede vincular como recurso general de seccion.');
    }

    $existingKey = trim((string) ($media['section_key'] ?? ''));

    if ($existingKey !== '') {
        throw new InvalidArgumentException('Ese recurso ya esta vinculado a otra seccion. Desvinculalo primero.');
    }

    $statement = $connection->prepare(
        'UPDATE media SET section_key = :section_key, display_order = :display_order WHERE id = :id'
    );
    $statement->execute([
        ':section_key' => (string) $section['section_key'],
        ':display_order' => adminNextMediaOrder(
            $connection,
            (int) $section['collection_id'],
            (string) $section['section_key']
        ),
        ':id' => $mediaId,
    ]);
}

function adminDetachMediaFromSection(PDO $connection, int $sectionId, int $mediaId): void
{
    adminAssertMediaAttachedToSection($connection, $sectionId, $mediaId);
    $statement = $connection->prepare('UPDATE media SET section_key = NULL, display_order = 0 WHERE id = :id');
    $statement->execute([':id' => $mediaId]);
}

function adminToggleSectionMedia(PDO $connection, int $sectionId, int $mediaId): bool
{
    [, $media] = adminAssertMediaAttachedToSection($connection, $sectionId, $mediaId);
    $nextState = (int) $media['is_active'] === 1 ? 0 : 1;
    $statement = $connection->prepare('UPDATE media SET is_active = :is_active WHERE id = :id');
    $statement->execute([
        ':is_active' => $nextState,
        ':id' => $mediaId,
    ]);

    return $nextState === 1;
}

function adminMoveSectionMedia(PDO $connection, int $sectionId, int $mediaId, int $direction): bool
{
    [$section] = adminAssertMediaAttachedToSection($connection, $sectionId, $mediaId);
    $statement = $connection->prepare(
        'SELECT id FROM media
         WHERE collection_id = :collection_id AND section_key = :section_key AND piece_id IS NULL
         ORDER BY display_order ASC, id ASC'
    );
    $statement->execute([
        ':collection_id' => (int) $section['collection_id'],
        ':section_key' => (string) $section['section_key'],
    ]);
    $ids = array_map('intval', $statement->fetchAll(PDO::FETCH_COLUMN));
    $currentIndex = array_search($mediaId, $ids, true);

    if ($currentIndex === false) {
        throw new InvalidArgumentException('No se pudo localizar el recurso dentro de la seccion.');
    }

    $targetIndex = $currentIndex + $direction;

    if ($targetIndex < 0 || $targetIndex >= count($ids)) {
        return false;
    }

    [$ids[$currentIndex], $ids[$targetIndex]] = [$ids[$targetIndex], $ids[$currentIndex]];
    $update = $connection->prepare('UPDATE media SET display_order = :display_order WHERE id = :id');

    foreach ($ids as $index => $id) {
        $update->execute([
            ':display_order' => $index + 1,
            ':id' => $id,
        ]);
    }

    return true;
}

function adminDeleteSectionMedia(PDO $connection, int $sectionId, int $mediaId): void
{
    adminAssertMediaAttachedToSection($connection, $sectionId, $mediaId);
    adminDeleteRecord($connection, 'media', $mediaId);
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

    $redirectView = 'crear-contenido';
    $redirectDetail = null;

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
            case 'create_section':
                $sectionId = adminSaveRecord($connection, 'collection_sections', adminBuildSectionData($connection), null);
                $message = 'Seccion creada.';
                $redirectView = 'secciones';
                $redirectDetail = 'section-' . $sectionId;
                break;
            case 'update_section':
                $sectionId = adminPostInt('id');
                adminUpdateSectionAndLinkedMedia($connection, $sectionId, adminBuildSectionData($connection, $sectionId));
                $message = 'Seccion actualizada y multimedia sincronizada.';
                $redirectView = 'secciones';
                $redirectDetail = 'section-' . $sectionId;
                break;
            case 'move_section_up':
                $sectionId = adminPostInt('id');
                $moved = adminMoveSection($connection, $sectionId, -1);
                $message = $moved ? 'Seccion movida hacia arriba.' : 'La seccion ya es la primera de su coleccion.';
                $redirectView = 'secciones';
                $redirectDetail = 'section-' . $sectionId;
                break;
            case 'move_section_down':
                $sectionId = adminPostInt('id');
                $moved = adminMoveSection($connection, $sectionId, 1);
                $message = $moved ? 'Seccion movida hacia abajo.' : 'La seccion ya es la ultima de su coleccion.';
                $redirectView = 'secciones';
                $redirectDetail = 'section-' . $sectionId;
                break;
            case 'delete_section':
                adminDeleteSectionAndDetachMedia($connection, adminPostInt('id'));
                $message = 'Seccion eliminada. Su multimedia ha quedado sin seccion.';
                $redirectView = 'secciones';
                break;
            case 'create_section_media':
                $sectionId = adminPostInt('section_id');
                $section = adminFetchSection($connection, $sectionId);
                $data = adminBuildMediaData($connection, [
                    'collection_id' => (int) $section['collection_id'],
                    'piece_id' => null,
                    'section_key' => (string) $section['section_key'],
                ]);
                $mediaId = adminSaveRecord($connection, 'media', $data, null);
                $message = 'Imagen vinculada a la seccion.';
                $redirectView = 'secciones';
                $redirectDetail = 'section-' . $sectionId;
                break;
            case 'attach_section_media':
                $sectionId = adminPostInt('section_id');
                adminAttachMediaToSection($connection, $sectionId, adminPostInt('media_id'));
                $message = 'Recurso existente vinculado a la seccion.';
                $redirectView = 'secciones';
                $redirectDetail = 'section-' . $sectionId;
                break;
            case 'detach_section_media':
                $sectionId = adminPostInt('section_id');
                adminDetachMediaFromSection($connection, $sectionId, adminPostInt('media_id'));
                $message = 'Recurso desvinculado de la seccion.';
                $redirectView = 'secciones';
                $redirectDetail = 'section-' . $sectionId;
                break;
            case 'toggle_section_media':
                $sectionId = adminPostInt('section_id');
                $active = adminToggleSectionMedia($connection, $sectionId, adminPostInt('media_id'));
                $message = $active ? 'Recurso activado.' : 'Recurso ocultado.';
                $redirectView = 'secciones';
                $redirectDetail = 'section-' . $sectionId;
                break;
            case 'move_section_media_up':
                $sectionId = adminPostInt('section_id');
                $moved = adminMoveSectionMedia($connection, $sectionId, adminPostInt('media_id'), -1);
                $message = $moved ? 'Imagen movida hacia arriba.' : 'La imagen ya es la primera.';
                $redirectView = 'secciones';
                $redirectDetail = 'section-' . $sectionId;
                break;
            case 'move_section_media_down':
                $sectionId = adminPostInt('section_id');
                $moved = adminMoveSectionMedia($connection, $sectionId, adminPostInt('media_id'), 1);
                $message = $moved ? 'Imagen movida hacia abajo.' : 'La imagen ya es la ultima.';
                $redirectView = 'secciones';
                $redirectDetail = 'section-' . $sectionId;
                break;
            case 'delete_section_media':
                $sectionId = adminPostInt('section_id');
                adminDeleteSectionMedia($connection, $sectionId, adminPostInt('media_id'));
                $message = 'Recurso eliminado.';
                $redirectView = 'secciones';
                $redirectDetail = 'section-' . $sectionId;
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
                $mediaId = adminSaveRecord($connection, 'media', adminBuildMediaData($connection), null);
                $message = 'Multimedia creada.';
                $redirectView = 'multimedia';
                $redirectDetail = 'media-' . $mediaId;
                break;
            case 'update_media':
                $mediaId = adminPostInt('id');
                adminSaveRecord($connection, 'media', adminBuildMediaData($connection), $mediaId);
                $message = 'Multimedia actualizada.';
                $redirectView = 'multimedia';
                $redirectDetail = 'media-' . $mediaId;
                break;
            case 'delete_media':
                adminDeleteRecord($connection, 'media', adminPostInt('id'));
                $message = 'Multimedia eliminada.';
                $redirectView = 'multimedia';
                break;
            default:
                throw new InvalidArgumentException('Accion no reconocida.');
        }

        $connection->commit();
        adminRedirect($message, $redirectView, $redirectDetail);
    } catch (Throwable $exception) {
        if ($connection->inTransaction()) {
            $connection->rollBack();
        }

        error_log($exception->getMessage());

        if (str_contains($action, 'section')) {
            $redirectView = 'secciones';
            $sectionId = adminPostInt('section_id', adminPostInt('id'));
            $redirectDetail = $sectionId > 0 ? 'section-' . $sectionId : null;
        } elseif (str_contains($action, 'media')) {
            $redirectView = 'multimedia';
        }

        adminRedirect(
            $exception instanceof InvalidArgumentException ? $exception->getMessage() : 'No se pudo guardar el cambio.',
            $redirectView,
            $redirectDetail
        );
    }
}

