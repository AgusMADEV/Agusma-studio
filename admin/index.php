<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/view-helpers.php';
require_once __DIR__ . '/includes/page-data.php';
require_once __DIR__ . '/actions/handle-request.php';

$connection = databaseConnection();
$dashboard = adminLoadDashboardData($connection);
$categories = $dashboard['categories'];
$entities = $dashboard['entities'];
$collections = $dashboard['collections'];
$pieces = $dashboard['pieces'];
$mediaItems = $dashboard['mediaItems'];
$legacyFeaturedCount = $dashboard['legacyFeaturedCount'];
$flashMessage = trim((string) ($_GET['message'] ?? ''));
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin AgusMA Studio</title>
  <link rel="stylesheet" href="../public/css/variables.css" />
  <link rel="stylesheet" href="../public/css/admin.css" />
</head>
<body>
  <main class="admin-shell">
    <header class="admin-header">
      <div>
        <p class="admin-eyebrow">Panel basico</p>
        <h1>Admin AgusMA Studio</h1>
      </div>
      <a class="admin-link" href="../public/index.php">Ver web</a>
    </header>

    <?php if ($flashMessage !== ''): ?>
      <p class="admin-flash"><?= adminEscape($flashMessage) ?></p>
    <?php endif; ?>

    <p class="admin-flash">Legacy: la tabla featured_collections sigue presente con <?= $legacyFeaturedCount ?> registros mientras termina la migracion del contenido.</p>

    <section class="admin-grid">
      <section class="admin-card admin-card--form">
        <h2>Nueva categoria</h2>
        <form method="post" class="admin-form">
          <input type="hidden" name="action" value="create_category" />
          <label>Nombre<input type="text" name="name" required /></label>
          <label>Slug<input type="text" name="slug" required /></label>
          <label>Resumen<input type="text" name="short_description" /></label>
          <label>Descripcion<textarea name="description"></textarea></label>
          <label>Visual key<input type="text" name="visual_key" required /></label>
          <label>Cover image<input type="text" name="cover_image" /></label>
          <label>Enlace<input type="text" name="link_url" value="#" /></label>
          <label>Orden<input type="number" name="display_order" value="0" min="0" /></label>
          <label class="admin-checkbox"><input type="checkbox" name="is_active" checked /> Activa</label>
          <button type="submit">Crear categoria</button>
        </form>
      </section>

      <section class="admin-card admin-card--form">
        <h2>Nueva entidad</h2>
        <form method="post" class="admin-form">
          <label>Categoria
            <select name="category_id" required>
              <?php foreach ($categories as $category): ?>
                <option value="<?= (int) $category['id'] ?>"><?= adminEscape(adminCategoryLabel($category)) ?></option>
              <?php endforeach; ?>
            </select>
          </label>
          <input type="hidden" name="action" value="create_entity" />
          <label>Nombre<input type="text" name="name" required /></label>
          <label>Slug<input type="text" name="slug" required /></label>
          <label>Entity type<input type="text" name="entity_type" value="other" required /></label>
          <label>Subtitle<input type="text" name="subtitle" /></label>
          <label>Resumen<input type="text" name="short_description" /></label>
          <label>Descripcion<textarea name="description"></textarea></label>
          <label>Logo URL<input type="text" name="logo_url" /></label>
          <label>Cover image<input type="text" name="cover_image" /></label>
          <label>Primary color<input type="text" name="primary_color" /></label>
          <label>Secondary color<input type="text" name="secondary_color" /></label>
          <label>Background color<input type="text" name="background_color" /></label>
          <label>Text color<input type="text" name="text_color" /></label>
          <label>Orden<input type="number" name="display_order" value="0" min="0" /></label>
          <label class="admin-checkbox"><input type="checkbox" name="is_featured" /> Destacada</label>
          <label class="admin-checkbox"><input type="checkbox" name="is_active" checked /> Activa</label>
          <button type="submit">Crear entidad</button>
        </form>
      </section>

      <section class="admin-card admin-card--form">
        <h2>Nueva coleccion</h2>
        <form method="post" class="admin-form">
          <input type="hidden" name="action" value="create_collection" />
          <label>Entidad
            <select name="entity_id" required>
              <?php foreach ($entities as $entity): ?>
                <option value="<?= (int) $entity['id'] ?>"><?= adminEscape(adminEntityLabel($entity)) ?></option>
              <?php endforeach; ?>
            </select>
          </label>
          <label>Nombre<input type="text" name="name" required /></label>
          <label>Slug<input type="text" name="slug" required /></label>
          <label>Subtitle<input type="text" name="subtitle" /></label>
          <label>Ano<input type="number" name="collection_year" min="1900" max="2100" /></label>
          <label>Season<input type="text" name="season" /></label>
          <label>Resumen<input type="text" name="short_description" /></label>
          <label>Descripcion<textarea name="description"></textarea></label>
          <label>Concept<textarea name="concept"></textarea></label>
          <label>Cover image<input type="text" name="cover_image" /></label>
          <label>Thumbnail image<input type="text" name="thumbnail_image" /></label>
          <label>Primary color<input type="text" name="primary_color" /></label>
          <label>Secondary color<input type="text" name="secondary_color" /></label>
          <label>Background color<input type="text" name="background_color" /></label>
          <label>Text color<input type="text" name="text_color" /></label>
          <label>Image variant<input type="text" name="image_variant" value="light" /></label>
          <label>Layout style<input type="text" name="layout_style" value="standard" /></label>
          <label>Published at<input type="datetime-local" name="published_at" /></label>
          <label>Orden<input type="number" name="display_order" value="0" min="0" /></label>
          <label class="admin-checkbox"><input type="checkbox" name="is_featured" /> Destacada</label>
          <label class="admin-checkbox"><input type="checkbox" name="is_active" checked /> Activa</label>
          <button type="submit">Crear coleccion</button>
        </form>
      </section>

      <section class="admin-card admin-card--form">
        <h2>Nueva pieza</h2>
        <form method="post" class="admin-form">
          <input type="hidden" name="action" value="create_piece" />
          <label>Coleccion
            <select name="collection_id" required>
              <?php foreach ($collections as $collection): ?>
                <option value="<?= (int) $collection['id'] ?>"><?= adminEscape(adminCollectionLabel($collection)) ?></option>
              <?php endforeach; ?>
            </select>
          </label>
          <label>Nombre<input type="text" name="name" required /></label>
          <label>Slug<input type="text" name="slug" required /></label>
          <label>Piece type<input type="text" name="piece_type" value="other" required /></label>
          <label>Subtitle<input type="text" name="subtitle" /></label>
          <label>Resumen<input type="text" name="short_description" /></label>
          <label>Descripcion<textarea name="description"></textarea></label>
          <label>Cover image<input type="text" name="cover_image" /></label>
          <label>Orden<input type="number" name="display_order" value="0" min="0" /></label>
          <label class="admin-checkbox"><input type="checkbox" name="is_featured" /> Destacada</label>
          <label class="admin-checkbox"><input type="checkbox" name="is_active" checked /> Activa</label>
          <button type="submit">Crear pieza</button>
        </form>
      </section>

      <section class="admin-card admin-card--form">
        <h2>Nueva multimedia</h2>
        <form method="post" class="admin-form">
          <input type="hidden" name="action" value="create_media" />
          <label>Coleccion
            <select name="collection_id" required>
              <?php foreach ($collections as $collection): ?>
                <option value="<?= (int) $collection['id'] ?>"><?= adminEscape(adminCollectionLabel($collection)) ?></option>
              <?php endforeach; ?>
            </select>
          </label>
          <label>Pieza opcional
            <select name="piece_id">
              <option value="">Multimedia general de la coleccion</option>
              <?php foreach ($pieces as $piece): ?>
                <option value="<?= (int) $piece['id'] ?>"><?= adminEscape(adminPieceLabel($piece)) ?></option>
              <?php endforeach; ?>
            </select>
          </label>
          <label>Media type<input type="text" name="media_type" value="image" required /></label>
          <label>File URL / ruta<input type="text" name="file_url" required /></label>
          <label>Thumbnail URL<input type="text" name="thumbnail_url" /></label>
          <label>Titulo<input type="text" name="title" /></label>
          <label>Alt text<input type="text" name="alt_text" /></label>
          <label>Caption<textarea name="caption"></textarea></label>
          <label>Section key<input type="text" name="section_key" /></label>
          <label>Orden<input type="number" name="display_order" value="0" min="0" /></label>
          <label class="admin-checkbox"><input type="checkbox" name="is_cover" /> Portada</label>
          <label class="admin-checkbox"><input type="checkbox" name="is_active" checked /> Activa</label>
          <button type="submit">Crear multimedia</button>
        </form>
      </section>
    </section>

    <section class="admin-card">
      <div class="admin-section-title">
        <h2>Categorias</h2>
        <p><?= count($categories) ?> registradas</p>
      </div>

      <div class="admin-list">
        <?php foreach ($categories as $category): ?>
          <form method="post" class="admin-item">
            <input type="hidden" name="action" value="update_category" />
            <input type="hidden" name="id" value="<?= (int) $category['id'] ?>" />
            <label>Nombre<input type="text" name="name" value="<?= adminEscape($category['name']) ?>" required /></label>
            <label>Slug<input type="text" name="slug" value="<?= adminEscape($category['slug']) ?>" required /></label>
            <label>Resumen<input type="text" name="short_description" value="<?= adminEscape($category['short_description']) ?>" /></label>
            <label>Descripcion<textarea name="description"><?= adminEscape($category['description']) ?></textarea></label>
            <label>Visual key<input type="text" name="visual_key" value="<?= adminEscape($category['visual_key']) ?>" required /></label>
            <label>Cover image<input type="text" name="cover_image" value="<?= adminEscape($category['cover_image']) ?>" /></label>
            <label>Enlace<input type="text" name="link_url" value="<?= adminEscape($category['link_url']) ?>" /></label>
            <label>Orden<input type="number" name="display_order" value="<?= (int) $category['display_order'] ?>" min="0" /></label>
            <label class="admin-checkbox"><input type="checkbox" name="is_active" <?= adminChecked($category['is_active']) ?> /> Activa</label>
            <div class="admin-actions">
              <button type="submit">Guardar</button>
              <button type="submit" name="submit_action" value="delete_category" class="admin-button admin-button--danger" formnovalidate onclick="return confirm('Se eliminara esta categoria. Continuar?');">Eliminar</button>
            </div>
          </form>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="admin-card">
      <div class="admin-section-title">
        <h2>Entidades</h2>
        <p><?= count($entities) ?> registradas</p>
      </div>

      <div class="admin-list">
        <?php foreach ($entities as $entity): ?>
          <form method="post" class="admin-item">
            <input type="hidden" name="action" value="update_entity" />
            <input type="hidden" name="id" value="<?= (int) $entity['id'] ?>" />
            <label>Categoria
              <select name="category_id" required>
                <?php foreach ($categories as $category): ?>
                  <option value="<?= (int) $category['id'] ?>" <?= adminSelected($entity['category_id'], $category['id']) ?>><?= adminEscape(adminCategoryLabel($category)) ?></option>
                <?php endforeach; ?>
              </select>
            </label>
            <label>Nombre<input type="text" name="name" value="<?= adminEscape($entity['name']) ?>" required /></label>
            <label>Slug<input type="text" name="slug" value="<?= adminEscape($entity['slug']) ?>" required /></label>
            <label>Entity type<input type="text" name="entity_type" value="<?= adminEscape($entity['entity_type']) ?>" required /></label>
            <label>Subtitle<input type="text" name="subtitle" value="<?= adminEscape($entity['subtitle']) ?>" /></label>
            <label>Resumen<input type="text" name="short_description" value="<?= adminEscape($entity['short_description']) ?>" /></label>
            <label>Descripcion<textarea name="description"><?= adminEscape($entity['description']) ?></textarea></label>
            <label>Logo URL<input type="text" name="logo_url" value="<?= adminEscape($entity['logo_url']) ?>" /></label>
            <label>Cover image<input type="text" name="cover_image" value="<?= adminEscape($entity['cover_image']) ?>" /></label>
            <label>Primary color<input type="text" name="primary_color" value="<?= adminEscape($entity['primary_color']) ?>" /></label>
            <label>Secondary color<input type="text" name="secondary_color" value="<?= adminEscape($entity['secondary_color']) ?>" /></label>
            <label>Background color<input type="text" name="background_color" value="<?= adminEscape($entity['background_color']) ?>" /></label>
            <label>Text color<input type="text" name="text_color" value="<?= adminEscape($entity['text_color']) ?>" /></label>
            <label>Orden<input type="number" name="display_order" value="<?= (int) $entity['display_order'] ?>" min="0" /></label>
            <label class="admin-checkbox"><input type="checkbox" name="is_featured" <?= adminChecked($entity['is_featured']) ?> /> Destacada</label>
            <label class="admin-checkbox"><input type="checkbox" name="is_active" <?= adminChecked($entity['is_active']) ?> /> Activa</label>
            <div class="admin-actions">
              <button type="submit">Guardar</button>
              <button type="submit" name="submit_action" value="delete_entity" class="admin-button admin-button--danger" formnovalidate onclick="return confirm('Se eliminara esta entidad. Continuar?');">Eliminar</button>
            </div>
          </form>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="admin-card">
      <div class="admin-section-title">
        <h2>Colecciones</h2>
        <p><?= count($collections) ?> registradas</p>
      </div>

      <div class="admin-list">
        <?php foreach ($collections as $collection): ?>
          <form method="post" class="admin-item">
            <input type="hidden" name="action" value="update_collection" />
            <input type="hidden" name="id" value="<?= (int) $collection['id'] ?>" />
            <label>Entidad
              <select name="entity_id" required>
                <?php foreach ($entities as $entity): ?>
                  <option value="<?= (int) $entity['id'] ?>" <?= adminSelected($collection['entity_id'], $entity['id']) ?>><?= adminEscape(adminEntityLabel($entity)) ?></option>
                <?php endforeach; ?>
              </select>
            </label>
            <label>Nombre<input type="text" name="name" value="<?= adminEscape($collection['name']) ?>" required /></label>
            <label>Slug<input type="text" name="slug" value="<?= adminEscape($collection['slug']) ?>" required /></label>
            <label>Subtitle<input type="text" name="subtitle" value="<?= adminEscape($collection['subtitle']) ?>" /></label>
            <label>Ano<input type="number" name="collection_year" value="<?= adminEscape($collection['collection_year']) ?>" min="1900" max="2100" /></label>
            <label>Season<input type="text" name="season" value="<?= adminEscape($collection['season']) ?>" /></label>
            <label>Resumen<input type="text" name="short_description" value="<?= adminEscape($collection['short_description']) ?>" /></label>
            <label>Descripcion<textarea name="description"><?= adminEscape($collection['description']) ?></textarea></label>
            <label>Concept<textarea name="concept"><?= adminEscape($collection['concept']) ?></textarea></label>
            <label>Cover image<input type="text" name="cover_image" value="<?= adminEscape($collection['cover_image']) ?>" /></label>
            <label>Thumbnail image<input type="text" name="thumbnail_image" value="<?= adminEscape($collection['thumbnail_image']) ?>" /></label>
            <label>Primary color<input type="text" name="primary_color" value="<?= adminEscape($collection['primary_color']) ?>" /></label>
            <label>Secondary color<input type="text" name="secondary_color" value="<?= adminEscape($collection['secondary_color']) ?>" /></label>
            <label>Background color<input type="text" name="background_color" value="<?= adminEscape($collection['background_color']) ?>" /></label>
            <label>Text color<input type="text" name="text_color" value="<?= adminEscape($collection['text_color']) ?>" /></label>
            <label>Image variant<input type="text" name="image_variant" value="<?= adminEscape($collection['image_variant']) ?>" /></label>
            <label>Layout style<input type="text" name="layout_style" value="<?= adminEscape($collection['layout_style']) ?>" /></label>
            <label>Published at<input type="datetime-local" name="published_at" value="<?= adminEscape(adminFormatDateTimeInput($collection['published_at'])) ?>" /></label>
            <label>Orden<input type="number" name="display_order" value="<?= (int) $collection['display_order'] ?>" min="0" /></label>
            <label class="admin-checkbox"><input type="checkbox" name="is_featured" <?= adminChecked($collection['is_featured']) ?> /> Destacada</label>
            <label class="admin-checkbox"><input type="checkbox" name="is_active" <?= adminChecked($collection['is_active']) ?> /> Activa</label>
            <div class="admin-actions">
              <button type="submit">Guardar</button>
              <button type="submit" name="submit_action" value="delete_collection" class="admin-button admin-button--danger" formnovalidate onclick="return confirm('Se eliminara esta coleccion. Continuar?');">Eliminar</button>
            </div>
          </form>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="admin-card">
      <div class="admin-section-title">
        <h2>Piezas</h2>
        <p><?= count($pieces) ?> registradas</p>
      </div>

      <div class="admin-list">
        <?php foreach ($pieces as $piece): ?>
          <form method="post" class="admin-item">
            <input type="hidden" name="action" value="update_piece" />
            <input type="hidden" name="id" value="<?= (int) $piece['id'] ?>" />
            <label>Coleccion
              <select name="collection_id" required>
                <?php foreach ($collections as $collection): ?>
                  <option value="<?= (int) $collection['id'] ?>" <?= adminSelected($piece['collection_id'], $collection['id']) ?>><?= adminEscape(adminCollectionLabel($collection)) ?></option>
                <?php endforeach; ?>
              </select>
            </label>
            <label>Nombre<input type="text" name="name" value="<?= adminEscape($piece['name']) ?>" required /></label>
            <label>Slug<input type="text" name="slug" value="<?= adminEscape($piece['slug']) ?>" required /></label>
            <label>Piece type<input type="text" name="piece_type" value="<?= adminEscape($piece['piece_type']) ?>" required /></label>
            <label>Subtitle<input type="text" name="subtitle" value="<?= adminEscape($piece['subtitle']) ?>" /></label>
            <label>Resumen<input type="text" name="short_description" value="<?= adminEscape($piece['short_description']) ?>" /></label>
            <label>Descripcion<textarea name="description"><?= adminEscape($piece['description']) ?></textarea></label>
            <label>Cover image<input type="text" name="cover_image" value="<?= adminEscape($piece['cover_image']) ?>" /></label>
            <label>Orden<input type="number" name="display_order" value="<?= (int) $piece['display_order'] ?>" min="0" /></label>
            <label class="admin-checkbox"><input type="checkbox" name="is_featured" <?= adminChecked($piece['is_featured']) ?> /> Destacada</label>
            <label class="admin-checkbox"><input type="checkbox" name="is_active" <?= adminChecked($piece['is_active']) ?> /> Activa</label>
            <div class="admin-actions">
              <button type="submit">Guardar</button>
              <button type="submit" name="submit_action" value="delete_piece" class="admin-button admin-button--danger" formnovalidate onclick="return confirm('Se eliminara esta pieza. Continuar?');">Eliminar</button>
            </div>
          </form>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="admin-card">
      <div class="admin-section-title">
        <h2>Multimedia</h2>
        <p><?= count($mediaItems) ?> registradas</p>
      </div>

      <div class="admin-list">
        <?php foreach ($mediaItems as $media): ?>
          <form method="post" class="admin-item">
            <input type="hidden" name="action" value="update_media" />
            <input type="hidden" name="id" value="<?= (int) $media['id'] ?>" />
            <label>Coleccion
              <select name="collection_id" required>
                <?php foreach ($collections as $collection): ?>
                  <option value="<?= (int) $collection['id'] ?>" <?= adminSelected($media['collection_id'], $collection['id']) ?>><?= adminEscape(adminCollectionLabel($collection)) ?></option>
                <?php endforeach; ?>
              </select>
            </label>
            <label>Pieza opcional
              <select name="piece_id">
                <option value="">Multimedia general de la coleccion</option>
                <?php foreach ($pieces as $piece): ?>
                  <option value="<?= (int) $piece['id'] ?>" <?= adminSelected($media['piece_id'], $piece['id']) ?>><?= adminEscape(adminPieceLabel($piece)) ?></option>
                <?php endforeach; ?>
              </select>
            </label>
            <label>Media type<input type="text" name="media_type" value="<?= adminEscape($media['media_type']) ?>" required /></label>
            <label>File URL / ruta<input type="text" name="file_url" value="<?= adminEscape($media['file_url']) ?>" required /></label>
            <label>Thumbnail URL<input type="text" name="thumbnail_url" value="<?= adminEscape($media['thumbnail_url']) ?>" /></label>
            <label>Titulo<input type="text" name="title" value="<?= adminEscape($media['title']) ?>" /></label>
            <label>Alt text<input type="text" name="alt_text" value="<?= adminEscape($media['alt_text']) ?>" /></label>
            <label>Caption<textarea name="caption"><?= adminEscape($media['caption']) ?></textarea></label>
            <label>Section key<input type="text" name="section_key" value="<?= adminEscape($media['section_key']) ?>" /></label>
            <label>Orden<input type="number" name="display_order" value="<?= (int) $media['display_order'] ?>" min="0" /></label>
            <label class="admin-checkbox"><input type="checkbox" name="is_cover" <?= adminChecked($media['is_cover']) ?> /> Portada</label>
            <label class="admin-checkbox"><input type="checkbox" name="is_active" <?= adminChecked($media['is_active']) ?> /> Activa</label>
            <div class="admin-actions">
              <button type="submit">Guardar</button>
              <button type="submit" name="submit_action" value="delete_media" class="admin-button admin-button--danger" formnovalidate onclick="return confirm('Se eliminara este elemento multimedia. Continuar?');">Eliminar</button>
            </div>
          </form>
        <?php endforeach; ?>
      </div>
    </section>
  </main>
</body>
</html>