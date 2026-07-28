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
$totalCategories = count($categories);
$totalEntities = count($entities);
$totalCollections = count($collections);
$totalPieces = count($pieces);
$totalMedia = count($mediaItems);
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
        <p class="admin-eyebrow">Panel administrativo</p>
        <h1>Admin AgusMA Studio</h1>
        <p class="admin-lead">Gestiona el contenido por niveles: categorias, entidades, colecciones, piezas y multimedia, con una vista mas clara para crear y mantener todo desde un mismo lugar.</p>
      </div>
      <div class="admin-header-actions">
        <a class="admin-link" href="#crear-contenido">Crear contenido</a>
        <a class="admin-link" href="#resumen">Resumen</a>
        <a class="admin-link" href="../public/index.php">Ver web</a>
      </div>
    </header>

    <?php if ($flashMessage !== ''): ?>
      <p class="admin-flash"><?= adminEscape($flashMessage) ?></p>
    <?php endif; ?>

    <div class="admin-layout">
      <aside class="admin-sidebar" aria-label="Vistas del panel">
        <div class="admin-sidebar__intro">
          <p class="admin-eyebrow">Vistas</p>
          <h2>Panel lateral</h2>
          <p>Abre una vista por tabla para reducir ruido y trabajar por bloques.</p>
        </div>

        <nav class="admin-sidebar-nav">
          <a class="admin-sidebar-link" href="#resumen" data-admin-nav>Resumen</a>
          <a class="admin-sidebar-link" href="#crear-contenido" data-admin-nav>Crear contenido</a>
          <a class="admin-sidebar-link" href="#categorias" data-admin-nav>Categorias</a>
          <a class="admin-sidebar-link" href="#entidades" data-admin-nav>Entidades</a>
          <a class="admin-sidebar-link" href="#colecciones" data-admin-nav>Colecciones</a>
          <a class="admin-sidebar-link" href="#piezas" data-admin-nav>Piezas</a>
          <a class="admin-sidebar-link" href="#multimedia" data-admin-nav>Multimedia</a>
          <a class="admin-sidebar-link" href="../public/index.php">Ver web</a>
        </nav>
      </aside>

      <div class="admin-views">
        <section class="admin-panel" id="resumen" data-admin-panel>
          <div class="admin-section-title">
            <div>
              <p class="admin-eyebrow">Resumen</p>
              <h2>Estado general</h2>
            </div>
            <p>Acceso rapido al volumen y al estado de cada capa de contenido.</p>
          </div>

          <section class="admin-summary" aria-label="Resumen general">
            <article class="admin-summary-card">
              <span>categorias</span>
              <strong><?= $totalCategories ?></strong>
              <p>Base de organizacion del catalogo.</p>
            </article>
            <article class="admin-summary-card">
              <span>entidades</span>
              <strong><?= $totalEntities ?></strong>
              <p>Marcas, selecciones o bloques editoriales.</p>
            </article>
            <article class="admin-summary-card">
              <span>colecciones</span>
              <strong><?= $totalCollections ?></strong>
              <p>Conjuntos publicados o en proceso.</p>
            </article>
            <article class="admin-summary-card">
              <span>piezas</span>
              <strong><?= $totalPieces ?></strong>
              <p>Elementos individuales dentro de cada coleccion.</p>
            </article>
            <article class="admin-summary-card">
              <span>multimedia</span>
              <strong><?= $totalMedia ?></strong>
              <p>Imagenes, recursos y material asociado.</p>
            </article>
          </section>

          <?php if ($flashMessage !== ''): ?>
            <p class="admin-flash"><?= adminEscape($flashMessage) ?></p>
          <?php endif; ?>

          <aside class="admin-note">
            <strong>Estado tecnico</strong>
            <p>Legacy: la tabla featured_collections sigue presente con <?= $legacyFeaturedCount ?> registros mientras termina la migracion del contenido.</p>
          </aside>
        </section>

        <section class="admin-panel" id="crear-contenido" data-admin-panel>
          <div class="admin-section-title">
            <div>
              <p class="admin-eyebrow">Alta rapida</p>
              <h2>Crear contenido</h2>
            </div>
            <button type="button" class="admin-collapse-toggle" data-admin-collapse-toggle aria-expanded="true" aria-controls="crear-contenido-body">Ocultar formularios</button>
          </div>

          <div id="crear-contenido-body" class="admin-collapse-body" data-admin-collapse-body>
            <p class="admin-panel-help">Empieza por la base y sigue el orden natural del catalogo.</p>

            <section class="admin-grid admin-grid--forms">
            <section class="admin-card admin-card--form">
              <h3>Nueva categoria</h3>
              <form method="post" class="admin-form">
                <input type="hidden" name="action" value="create_category" />
                <label>Nombre<input type="text" name="name" required /></label>
                <label>Slug<input type="text" name="slug" required /></label>
                <label>Resumen<input type="text" name="short_description" /></label>
                <label>Descripcion<textarea name="description"></textarea></label>
                <label>Visual key<input type="text" name="visual_key" required /></label>
                <label>Cover image<input type="text" name="cover_image" /></label>
                <label>Hero image<input type="text" name="hero_image" /></label>
                <label>Enlace<input type="text" name="link_url" value="#" /></label>
                <label>Orden<input type="number" name="display_order" value="0" min="0" /></label>
                <label class="admin-checkbox"><input type="checkbox" name="is_active" checked /> Activa</label>
                <button type="submit">Crear categoria</button>
              </form>
            </section>

            <section class="admin-card admin-card--form">
              <h3>Nueva entidad</h3>
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
              <h3>Nueva coleccion</h3>
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
              <h3>Nueva pieza</h3>
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
              <h3>Nueva multimedia</h3>
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
          </div>
        </section>

        <section class="admin-panel" id="categorias" data-admin-panel>
          <div class="admin-section-title">
            <h2>Categorias</h2>
            <p><?= count($categories) ?> registradas</p>
          </div>

          <div class="admin-panel-toolbar">
            <label class="admin-panel-search">
              <span>Buscar</span>
              <input type="search" placeholder="Nombre, slug o resumen" data-admin-search />
            </label>
            <label class="admin-panel-filter">
              <span>Estado</span>
              <select data-admin-status-filter>
                <option value="all">Todos</option>
                <option value="active">Activas</option>
                <option value="inactive">Inactivas</option>
                <option value="featured">Destacadas</option>
              </select>
            </label>
          </div>

          <div class="admin-list">
            <?php foreach ($categories as $category): ?>
              <form method="post" class="admin-item" data-admin-item>
                <input type="hidden" name="action" value="update_category" />
                <input type="hidden" name="id" value="<?= (int) $category['id'] ?>" />
                <div class="admin-item-header">
                  <div>
                    <p class="admin-item-kicker">Categoria</p>
                    <h3 class="admin-item-title"><?= adminEscape($category['name']) ?></h3>
                  </div>
                  <div class="admin-item-meta">
                    <span><?= adminEscape($category['slug']) ?></span>
                    <span><?= (int) $category['is_active'] === 1 ? 'Activa' : 'Inactiva' ?></span>
                  </div>
                </div>
                <div class="admin-item-grid">
                  <label>Nombre<input type="text" name="name" value="<?= adminEscape($category['name']) ?>" required /></label>
                  <label>Slug<input type="text" name="slug" value="<?= adminEscape($category['slug']) ?>" required /></label>
                  <label>Visual key<input type="text" name="visual_key" value="<?= adminEscape($category['visual_key']) ?>" required /></label>
                  <label class="admin-field--wide">Resumen<input type="text" name="short_description" value="<?= adminEscape($category['short_description']) ?>" /></label>
                  <label class="admin-field--wide">Descripcion<textarea name="description"><?= adminEscape($category['description']) ?></textarea></label>
                  <label>Cover image<input type="text" name="cover_image" value="<?= adminEscape($category['cover_image']) ?>" /></label>
                  <label>Hero image<input type="text" name="hero_image" value="<?= adminEscape($category['hero_image']) ?>" /></label>
                  <label>Enlace<input type="text" name="link_url" value="<?= adminEscape($category['link_url']) ?>" /></label>
                  <label>Orden<input type="number" name="display_order" value="<?= (int) $category['display_order'] ?>" min="0" /></label>
                  <label class="admin-checkbox"><input type="checkbox" name="is_active" <?= adminChecked($category['is_active']) ?> /> Activa</label>
                  <div class="admin-actions">
                    <button type="submit">Guardar</button>
                    <button type="submit" name="submit_action" value="delete_category" class="admin-button admin-button--danger" formnovalidate onclick="return confirm('Se eliminara esta categoria. Continuar?');">Eliminar</button>
                  </div>
                </div>
              </form>
            <?php endforeach; ?>
          </div>
        </section>

        <section class="admin-panel" id="entidades" data-admin-panel>
          <div class="admin-section-title">
            <h2>Entidades</h2>
            <p><?= count($entities) ?> registradas</p>
          </div>

          <div class="admin-panel-toolbar">
            <label class="admin-panel-search">
              <span>Buscar</span>
              <input type="search" placeholder="Nombre, slug o tipo" data-admin-search />
            </label>
            <label class="admin-panel-filter">
              <span>Estado</span>
              <select data-admin-status-filter>
                <option value="all">Todos</option>
                <option value="active">Activas</option>
                <option value="inactive">Inactivas</option>
                <option value="featured">Destacadas</option>
              </select>
            </label>
          </div>

          <div class="admin-list">
            <?php foreach ($entities as $entity): ?>
              <form method="post" class="admin-item" data-admin-item>
                <input type="hidden" name="action" value="update_entity" />
                <input type="hidden" name="id" value="<?= (int) $entity['id'] ?>" />
                <div class="admin-item-header">
                  <div>
                    <p class="admin-item-kicker">Entidad · <?= adminEscape((string) ($entity['category_name'] ?? 'Sin categoria')) ?></p>
                    <h3 class="admin-item-title"><?= adminEscape($entity['name']) ?></h3>
                  </div>
                  <div class="admin-item-meta">
                    <span><?= adminEscape($entity['slug']) ?></span>
                    <span><?= adminEscape($entity['entity_type']) ?></span>
                    <span><?= (int) $entity['is_active'] === 1 ? 'Activa' : 'Inactiva' ?></span>
                  </div>
                </div>
                <div class="admin-item-grid">
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
                  <label class="admin-field--wide">Resumen<input type="text" name="short_description" value="<?= adminEscape($entity['short_description']) ?>" /></label>
                  <label class="admin-field--wide">Descripcion<textarea name="description"><?= adminEscape($entity['description']) ?></textarea></label>
                  <label>Logo URL<input type="text" name="logo_url" value="<?= adminEscape($entity['logo_url']) ?>" /></label>
                  <label>Cover image<input type="text" name="cover_image" value="<?= adminEscape($entity['cover_image']) ?>" /></label>
                  <label>Orden<input type="number" name="display_order" value="<?= (int) $entity['display_order'] ?>" min="0" /></label>
                  <label>Primary color<input type="text" name="primary_color" value="<?= adminEscape($entity['primary_color']) ?>" /></label>
                  <label>Secondary color<input type="text" name="secondary_color" value="<?= adminEscape($entity['secondary_color']) ?>" /></label>
                  <label>Background color<input type="text" name="background_color" value="<?= adminEscape($entity['background_color']) ?>" /></label>
                  <label>Text color<input type="text" name="text_color" value="<?= adminEscape($entity['text_color']) ?>" /></label>
                  <label class="admin-checkbox"><input type="checkbox" name="is_featured" <?= adminChecked($entity['is_featured']) ?> /> Destacada</label>
                  <label class="admin-checkbox"><input type="checkbox" name="is_active" <?= adminChecked($entity['is_active']) ?> /> Activa</label>
                  <div class="admin-actions">
                    <button type="submit">Guardar</button>
                    <button type="submit" name="submit_action" value="delete_entity" class="admin-button admin-button--danger" formnovalidate onclick="return confirm('Se eliminara esta entidad. Continuar?');">Eliminar</button>
                  </div>
                </div>
              </form>
            <?php endforeach; ?>
          </div>
        </section>

        <section class="admin-panel" id="colecciones" data-admin-panel>
          <div class="admin-section-title">
            <h2>Colecciones</h2>
            <p><?= count($collections) ?> registradas</p>
          </div>

          <div class="admin-panel-toolbar">
            <label class="admin-panel-search">
              <span>Buscar</span>
              <input type="search" placeholder="Nombre, slug o entidad" data-admin-search />
            </label>
            <label class="admin-panel-filter">
              <span>Estado</span>
              <select data-admin-status-filter>
                <option value="all">Todos</option>
                <option value="active">Activas</option>
                <option value="inactive">Inactivas</option>
                <option value="featured">Destacadas</option>
              </select>
            </label>
          </div>

          <div class="admin-list">
            <?php foreach ($collections as $collection): ?>
              <form method="post" class="admin-item" data-admin-item>
                <input type="hidden" name="action" value="update_collection" />
                <input type="hidden" name="id" value="<?= (int) $collection['id'] ?>" />
                <div class="admin-item-header">
                  <div>
                    <p class="admin-item-kicker">Coleccion · <?= adminEscape((string) ($collection['entity_name'] ?? 'Sin entidad')) ?></p>
                    <h3 class="admin-item-title"><?= adminEscape($collection['name']) ?></h3>
                  </div>
                  <div class="admin-item-meta">
                    <span><?= adminEscape($collection['slug']) ?></span>
                    <?php if (!empty($collection['collection_year'])): ?>
                      <span><?= (int) $collection['collection_year'] ?></span>
                    <?php endif; ?>
                    <span><?= (int) $collection['is_active'] === 1 ? 'Activa' : 'Inactiva' ?></span>
                  </div>
                </div>
                <div class="admin-item-grid">
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
                  <label>Ano<input type="number" name="collection_year" value="<?= (int) $collection['collection_year'] ?>" min="1900" max="2100" /></label>
                  <label>Season<input type="text" name="season" value="<?= adminEscape($collection['season']) ?>" /></label>
                  <label class="admin-field--wide">Resumen<input type="text" name="short_description" value="<?= adminEscape($collection['short_description']) ?>" /></label>
                  <label class="admin-field--wide">Descripcion<textarea name="description"><?= adminEscape($collection['description']) ?></textarea></label>
                  <label class="admin-field--wide">Concept<textarea name="concept"><?= adminEscape($collection['concept']) ?></textarea></label>
                  <label>Cover image<input type="text" name="cover_image" value="<?= adminEscape($collection['cover_image']) ?>" /></label>
                  <label>Thumbnail image<input type="text" name="thumbnail_image" value="<?= adminEscape($collection['thumbnail_image']) ?>" /></label>
                  <label>Published at<input type="datetime-local" name="published_at" value="<?= adminEscape(adminFormatDateTimeInput($collection['published_at'])) ?>" /></label>
                  <label>Image variant<input type="text" name="image_variant" value="<?= adminEscape($collection['image_variant']) ?>" /></label>
                  <label>Layout style<input type="text" name="layout_style" value="<?= adminEscape($collection['layout_style']) ?>" /></label>
                  <label>Orden<input type="number" name="display_order" value="<?= (int) $collection['display_order'] ?>" min="0" /></label>
                  <label>Primary color<input type="text" name="primary_color" value="<?= adminEscape($collection['primary_color']) ?>" /></label>
                  <label>Secondary color<input type="text" name="secondary_color" value="<?= adminEscape($collection['secondary_color']) ?>" /></label>
                  <label>Background color<input type="text" name="background_color" value="<?= adminEscape($collection['background_color']) ?>" /></label>
                  <label>Text color<input type="text" name="text_color" value="<?= adminEscape($collection['text_color']) ?>" /></label>
                  <label class="admin-checkbox"><input type="checkbox" name="is_featured" <?= adminChecked($collection['is_featured']) ?> /> Destacada</label>
                  <label class="admin-checkbox"><input type="checkbox" name="is_active" <?= adminChecked($collection['is_active']) ?> /> Activa</label>
                  <div class="admin-actions">
                    <button type="submit">Guardar</button>
                    <button type="submit" name="submit_action" value="delete_collection" class="admin-button admin-button--danger" formnovalidate onclick="return confirm('Se eliminara esta coleccion. Continuar?');">Eliminar</button>
                  </div>
                </div>
              </form>
            <?php endforeach; ?>
          </div>
        </section>

        <section class="admin-panel" id="piezas" data-admin-panel>
          <div class="admin-section-title">
            <h2>Piezas</h2>
            <p><?= count($pieces) ?> registradas</p>
          </div>

          <div class="admin-panel-toolbar">
            <label class="admin-panel-search">
              <span>Buscar</span>
              <input type="search" placeholder="Nombre, slug o coleccion" data-admin-search />
            </label>
            <label class="admin-panel-filter">
              <span>Estado</span>
              <select data-admin-status-filter>
                <option value="all">Todos</option>
                <option value="active">Activas</option>
                <option value="inactive">Inactivas</option>
                <option value="featured">Destacadas</option>
              </select>
            </label>
          </div>

          <div class="admin-list">
            <?php foreach ($pieces as $piece): ?>
              <form method="post" class="admin-item" data-admin-item>
                <input type="hidden" name="action" value="update_piece" />
                <input type="hidden" name="id" value="<?= (int) $piece['id'] ?>" />
                <div class="admin-item-header">
                  <div>
                    <p class="admin-item-kicker">Pieza · <?= adminEscape((string) ($piece['collection_name'] ?? 'Sin coleccion')) ?></p>
                    <h3 class="admin-item-title"><?= adminEscape($piece['name']) ?></h3>
                  </div>
                  <div class="admin-item-meta">
                    <span><?= adminEscape($piece['slug']) ?></span>
                    <span><?= adminEscape($piece['piece_type']) ?></span>
                    <span><?= (int) $piece['is_active'] === 1 ? 'Activa' : 'Inactiva' ?></span>
                  </div>
                </div>
                <div class="admin-item-grid">
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
                  <label>Orden<input type="number" name="display_order" value="<?= (int) $piece['display_order'] ?>" min="0" /></label>
                  <label class="admin-field--wide">Resumen<input type="text" name="short_description" value="<?= adminEscape($piece['short_description']) ?>" /></label>
                  <label class="admin-field--wide">Descripcion<textarea name="description"><?= adminEscape($piece['description']) ?></textarea></label>
                  <label class="admin-field--wide">Cover image<input type="text" name="cover_image" value="<?= adminEscape($piece['cover_image']) ?>" /></label>
                  <label class="admin-checkbox"><input type="checkbox" name="is_featured" <?= adminChecked($piece['is_featured']) ?> /> Destacada</label>
                  <label class="admin-checkbox"><input type="checkbox" name="is_active" <?= adminChecked($piece['is_active']) ?> /> Activa</label>
                  <div class="admin-actions">
                    <button type="submit">Guardar</button>
                    <button type="submit" name="submit_action" value="delete_piece" class="admin-button admin-button--danger" formnovalidate onclick="return confirm('Se eliminara esta pieza. Continuar?');">Eliminar</button>
                  </div>
                </div>
              </form>
            <?php endforeach; ?>
          </div>
        </section>

        <section class="admin-panel" id="multimedia" data-admin-panel>
          <div class="admin-section-title">
            <h2>Multimedia</h2>
            <p><?= count($mediaItems) ?> registradas</p>
          </div>

          <div class="admin-panel-toolbar">
            <label class="admin-panel-search">
              <span>Buscar</span>
              <input type="search" placeholder="Titulo, ruta o coleccion" data-admin-search />
            </label>
            <label class="admin-panel-filter">
              <span>Estado</span>
              <select data-admin-status-filter>
                <option value="all">Todos</option>
                <option value="active">Activas</option>
                <option value="inactive">Inactivas</option>
                <option value="featured">Destacadas</option>
              </select>
            </label>
          </div>

          <div class="admin-list">
            <?php foreach ($mediaItems as $media): ?>
              <form method="post" class="admin-item" data-admin-item>
                <input type="hidden" name="action" value="update_media" />
                <input type="hidden" name="id" value="<?= (int) $media['id'] ?>" />
                <div class="admin-item-header">
                  <div>
                    <p class="admin-item-kicker">Multimedia · <?= adminEscape((string) ($media['collection_name'] ?? 'Sin coleccion')) ?></p>
                    <h3 class="admin-item-title"><?= adminEscape((string) ($media['title'] ?: $media['file_url'])) ?></h3>
                  </div>
                  <div class="admin-item-meta">
                    <span><?= adminEscape($media['media_type']) ?></span>
                    <span><?= (int) $media['is_cover'] === 1 ? 'Portada' : 'Recurso' ?></span>
                    <span><?= (int) $media['is_active'] === 1 ? 'Activa' : 'Inactiva' ?></span>
                  </div>
                </div>
                <div class="admin-item-grid">
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
                  <label class="admin-field--wide">File URL / ruta<input type="text" name="file_url" value="<?= adminEscape($media['file_url']) ?>" required /></label>
                  <label>Thumbnail URL<input type="text" name="thumbnail_url" value="<?= adminEscape($media['thumbnail_url']) ?>" /></label>
                  <label>Titulo<input type="text" name="title" value="<?= adminEscape($media['title']) ?>" /></label>
                  <label>Alt text<input type="text" name="alt_text" value="<?= adminEscape($media['alt_text']) ?>" /></label>
                  <label>Section key<input type="text" name="section_key" value="<?= adminEscape($media['section_key']) ?>" /></label>
                  <label>Orden<input type="number" name="display_order" value="<?= (int) $media['display_order'] ?>" min="0" /></label>
                  <label class="admin-field--wide">Caption<textarea name="caption"><?= adminEscape($media['caption']) ?></textarea></label>
                  <label class="admin-checkbox"><input type="checkbox" name="is_cover" <?= adminChecked($media['is_cover']) ?> /> Portada</label>
                  <label class="admin-checkbox"><input type="checkbox" name="is_active" <?= adminChecked($media['is_active']) ?> /> Activa</label>
                  <div class="admin-actions">
                    <button type="submit">Guardar</button>
                    <button type="submit" name="submit_action" value="delete_media" class="admin-button admin-button--danger" formnovalidate onclick="return confirm('Se eliminara este elemento multimedia. Continuar?');">Eliminar</button>
                  </div>
                </div>
              </form>
            <?php endforeach; ?>
          </div>
        </section>
      </div>
    </div>
  </main>
  <script type="module" src="../public/js/admin.js"></script>
</body>
</html>