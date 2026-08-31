<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/view-helpers.php';
require_once __DIR__ . '/includes/page-data.php';
require_once __DIR__ . '/actions/handle-request.php';

$flashState = adminFlash();
$connection = databaseConnection();
$dashboard = adminLoadDashboardData($connection);
$categories = $dashboard['categories'];
$entities = $dashboard['entities'];
$templates = $dashboard['templates'];
$templateSections = $dashboard['templateSections'];
$collections = $dashboard['collections'];
$sections = $dashboard['sections'];
$pieces = $dashboard['pieces'];
$mediaItems = $dashboard['mediaItems'];
$legacyFeaturedCount = $dashboard['legacyFeaturedCount'];
$flashMessage = trim((string) ($flashState['message'] ?? ''));
$flashType = ($flashState['type'] ?? '') === 'error' ? 'error' : 'success';
$requestedViewId = trim((string) ($flashState['view'] ?? ''));
$requestedDetailId = trim((string) ($flashState['detail'] ?? ''));
$requestedViewId = preg_match('/^[a-z0-9-]+$/', $requestedViewId) === 1 ? $requestedViewId : '';
$requestedDetailId = preg_match('/^[a-z0-9-]+$/', $requestedDetailId) === 1 ? $requestedDetailId : '';
$mediaBySection = [];
$unassignedMediaByCollection = [];
$templateSectionsByTemplate = [];

foreach ($templateSections as $templateSection) {
    $templateSectionsByTemplate[(int) $templateSection['template_id']][] = $templateSection;
}

foreach ($mediaItems as $mediaItem) {
    if ($mediaItem['piece_id'] !== null) {
        continue;
    }

    $collectionId = (int) $mediaItem['collection_id'];
    $sectionKey = trim((string) ($mediaItem['section_key'] ?? ''));

    if ($sectionKey === '') {
        $unassignedMediaByCollection[$collectionId][] = $mediaItem;
        continue;
    }

    $mediaBySection[$collectionId . ':' . $sectionKey][] = $mediaItem;
}

$totalCategories = count($categories);
$totalEntities = count($entities);
$totalTemplates = count($templates);
$totalCollections = count($collections);
$totalSections = count($sections);
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
<body data-admin-initial-view="<?= adminEscape($requestedViewId) ?>">
  <div class="admin-app">
    <aside class="admin-sidebar" aria-label="Vistas del panel">
      <div class="admin-brand">
        <a href="#categorias" data-admin-nav>
          <strong>AGUSMA</strong>
          <span>Studio</span>
        </a>
      </div>

      <nav class="admin-sidebar-nav">
        <a class="admin-sidebar-link" href="#resumen" data-admin-nav><span class="admin-sidebar-icon"></span><span>Resumen</span></a>
        <a class="admin-sidebar-link" href="#categorias" data-admin-nav><span class="admin-sidebar-icon"></span><span>Categorias</span></a>
        <a class="admin-sidebar-link" href="#entidades" data-admin-nav><span class="admin-sidebar-icon"></span><span>Entidades</span></a>
        <a class="admin-sidebar-link" href="#colecciones" data-admin-nav><span class="admin-sidebar-icon"></span><span>Colecciones</span></a>
        <a class="admin-sidebar-link" href="#plantillas" data-admin-nav><span class="admin-sidebar-icon"></span><span>Plantillas</span></a>
        <a class="admin-sidebar-link" href="#secciones" data-admin-nav><span class="admin-sidebar-icon"></span><span>Secciones</span></a>
        <a class="admin-sidebar-link" href="#piezas" data-admin-nav><span class="admin-sidebar-icon"></span><span>Piezas</span></a>
        <a class="admin-sidebar-link" href="#multimedia" data-admin-nav><span class="admin-sidebar-icon"></span><span>Multimedia</span></a>
      </nav>

      <div class="admin-sidebar-footer">
        <div class="admin-user-chip">
          <span class="admin-user-avatar">AS</span>
          <div>
            <strong>AgusMA Team</strong>
            <span>Administrador</span>
          </div>
        </div>
      </div>
    </aside>

    <div class="admin-main">
      <header class="admin-topbar">
        <div class="admin-topbar-title">
          <button type="button" class="admin-icon-button" aria-label="Menu">
            <span></span>
            <span></span>
            <span></span>
          </button>
          <p>Panel administrativo</p>
        </div>
        <div class="admin-topbar-actions">
          <a class="admin-icon-link" href="#crear-contenido" aria-label="Crear contenido"></a>
          <a class="admin-icon-link" href="../public/index.php" aria-label="Ver sitio"></a>
          <span class="admin-user-badge">AS</span>
        </div>
      </header>

      <main class="admin-shell">
        <?php if ($flashMessage !== ''): ?>
          <p
            class="admin-flash admin-flash--<?= adminEscape($flashType) ?>"
            data-admin-flash
            data-admin-flash-type="<?= adminEscape($flashType) ?>"
            role="<?= $flashType === 'error' ? 'alert' : 'status' ?>"
            aria-live="<?= $flashType === 'error' ? 'assertive' : 'polite' ?>"
          ><?= adminEscape($flashMessage) ?></p>
        <?php endif; ?>

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
              <span>plantillas</span>
              <strong><?= $totalTemplates ?></strong>
              <p>Estructuras reutilizables para nuevas colecciones.</p>
            </article>
            <article class="admin-summary-card">
              <span>secciones</span>
              <strong><?= $totalSections ?></strong>
              <p>Bloques dinamicos que componen cada coleccion.</p>
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
                <label>Plantilla inicial
                  <select name="template_id">
                    <option value="">Coleccion vacia</option>
                    <?php foreach ($templates as $template): ?>
                      <?php if ((int) $template['is_active'] === 1): ?>
                        <option value="<?= (int) $template['id'] ?>"><?= adminEscape((string) $template['name']) ?> · <?= (int) $template['section_count'] ?> secciones</option>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  </select>
                </label>
                <p class="admin-upload-hint">La plantilla solo se copia al crear la coleccion. Los cambios posteriores son independientes.</p>
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
                <label>Orden<input type="number" name="display_order" value="0" min="0" /></label>
                <label class="admin-checkbox"><input type="checkbox" name="is_featured" /> Destacada</label>
                <label class="admin-checkbox"><input type="checkbox" name="is_active" checked /> Activa</label>
                <button type="submit">Crear coleccion</button>
              </form>
            </section>


            <section class="admin-card admin-card--form">
              <h3>Nueva plantilla</h3>
              <form method="post" class="admin-form">
                <input type="hidden" name="action" value="create_template" />
                <label>Nombre<input type="text" name="name" required /></label>
                <label>Slug<input type="text" name="slug" required /></label>
                <label>Descripcion<textarea name="description"></textarea></label>
                <label>Imagen de vista previa<input type="text" name="preview_image" /></label>
                <label>Orden<input type="number" name="display_order" value="0" min="0" /></label>
                <label class="admin-checkbox"><input type="checkbox" name="is_active" checked /> Activa</label>
                <button type="submit">Crear plantilla</button>
              </form>
            </section>

            <section class="admin-card admin-card--form" id="crear-seccion">
              <h3>Nueva seccion</h3>
              <form method="post" class="admin-form" data-section-settings-form>
                <input type="hidden" name="action" value="create_section" />
                <label>Coleccion
                  <select name="collection_id" required>
                    <?php foreach ($collections as $collection): ?>
                      <option value="<?= (int) $collection['id'] ?>"><?= adminEscape(adminCollectionLabel($collection)) ?></option>
                    <?php endforeach; ?>
                  </select>
                </label>
                <label>Tipo de seccion
                  <select name="section_type" required data-section-type-select>
                    <?php foreach (adminSectionTypeOptions() as $sectionType => $sectionTypeLabel): ?>
                      <option value="<?= adminEscape($sectionType) ?>"><?= adminEscape($sectionTypeLabel) ?></option>
                    <?php endforeach; ?>
                  </select>
                </label>
                <label>Section key<input type="text" name="section_key" placeholder="campaign" required /></label>
                <label>Eyebrow<input type="text" name="eyebrow" placeholder="Campaign" /></label>
                <label>Titulo<input type="text" name="title" /></label>
                <label>Contenido<textarea name="body"></textarea></label>
                <?= adminRenderSectionVisualSettings('hero') ?>
                <label>Orden<input type="number" name="display_order" min="0" placeholder="Automatico" /></label>
                <label class="admin-checkbox"><input type="checkbox" name="is_active" checked /> Activa</label>
                <button type="submit">Crear seccion</button>
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
              <form method="post" class="admin-form" enctype="multipart/form-data">
                <input type="hidden" name="action" value="create_media" />
                <label>Coleccion
                  <select name="collection_id" required data-admin-media-collection>
                    <?php foreach ($collections as $collection): ?>
                      <option value="<?= (int) $collection['id'] ?>"><?= adminEscape(adminCollectionLabel($collection)) ?></option>
                    <?php endforeach; ?>
                  </select>
                </label>
                <label>Pieza opcional
                  <select name="piece_id" data-admin-piece-select>
                    <option value="">Multimedia general de la coleccion</option>
                    <?php foreach ($pieces as $piece): ?>
                      <option value="<?= (int) $piece['id'] ?>" data-collection-id="<?= (int) $piece['collection_id'] ?>"><?= adminEscape(adminPieceLabel($piece)) ?></option>
                    <?php endforeach; ?>
                  </select>
                </label>
                <label>Media type<input type="text" name="media_type" value="image" required /></label>
                <label>Subir imagen<input type="file" name="media_file" accept="image/jpeg,image/png,image/webp,image/avif,image/gif" /></label>
                <label>File URL / ruta<input type="text" name="file_url" placeholder="Opcional si subes un archivo" /></label>
                <label>Thumbnail URL<input type="text" name="thumbnail_url" /></label>
                <label>Titulo<input type="text" name="title" /></label>
                <label>Alt text<input type="text" name="alt_text" /></label>
                <label>Caption<textarea name="caption"></textarea></label>
                <label>Seccion opcional
                  <select name="section_key" data-admin-section-select>
                    <option value="">Sin seccion</option>
                    <?php foreach ($sections as $section): ?>
                      <option value="<?= adminEscape((string) $section['section_key']) ?>" data-collection-id="<?= (int) $section['collection_id'] ?>"><?= adminEscape((string) $section['collection_name'] . ' · ' . ($section['title'] ?: $section['section_key'])) ?></option>
                    <?php endforeach; ?>
                  </select>
                </label>
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
          <div class="admin-master-detail" data-admin-master-detail>
            <div class="admin-master-detail-main">
              <div class="admin-page-heading">
                <div>
                  <h2>Categorias</h2>
                  <p>Gestiona las categorias que organizan tu contenido por orden, estado y estructura.</p>
                </div>
                <span class="admin-page-count"><?= count($categories) ?> registradas</span>
              </div>

              <div class="admin-panel-toolbar">
                <label class="admin-panel-search">
                  <span>Buscar</span>
                  <input type="search" placeholder="Buscar por nombre, slug o descripcion..." data-admin-search />
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
                <label class="admin-panel-filter admin-panel-filter--sort">
                  <span>Orden</span>
                  <select data-admin-sort>
                    <option value="order-asc">Ascendente</option>
                    <option value="order-desc">Descendente</option>
                    <option value="name-asc">A-Z</option>
                    <option value="name-desc">Z-A</option>
                  </select>
                </label>
                <a class="admin-toolbar-cta" href="#crear-contenido">Nueva categoria</a>
              </div>

              <div class="admin-record-list admin-list">
                <div class="admin-list-head" aria-hidden="true">
                  <span>Categoria</span>
                  <span>Slug</span>
                  <span>Estado</span>
                  <span>Orden</span>
                  <span>Acciones</span>
                </div>
                <?php foreach ($categories as $category): ?>
                  <?php $categorySummary = adminFirstNonEmpty([(string) $category['short_description'], (string) $category['description'], (string) $category['visual_key']], 'Sin resumen disponible.'); ?>
                  <?php $categoryPreview = adminRecordPreviewImage($category, ['cover_image', 'hero_image']); ?>
                  <button type="button" class="admin-item admin-item--category" data-admin-item data-admin-detail-trigger data-detail-id="category-<?= (int) $category['id'] ?>" data-active="<?= (int) $category['is_active'] ?>" data-featured="0" data-order="<?= (int) $category['display_order'] ?>" data-name="<?= adminEscape(strtolower((string) $category['name'])) ?>" aria-controls="detail-category-<?= (int) $category['id'] ?>">
                    <span class="admin-item-main">
                      <span class="admin-item-media<?= $categoryPreview === '' ? ' is-empty' : '' ?>">
                        <?php if ($categoryPreview !== ''): ?>
                          <img src="<?= adminEscape($categoryPreview) ?>" alt="<?= adminEscape($category['name']) ?>" loading="lazy" />
                        <?php else: ?>
                          <span><?= adminEscape(strtoupper(substr((string) $category['name'], 0, 1))) ?></span>
                        <?php endif; ?>
                      </span>
                      <span class="admin-item-copy">
                        <span class="admin-item-kicker">Categoria</span>
                        <span class="admin-item-title"><?= adminEscape($category['name']) ?></span>
                        <span class="admin-item-summary"><?= adminEscape($categorySummary) ?></span>
                      </span>
                    </span>
                    <span class="admin-item-cell admin-item-cell--slug"><?= adminEscape($category['slug']) ?></span>
                    <span class="admin-item-cell admin-item-cell--status"><span class="admin-status-badge"><?= (int) $category['is_active'] === 1 ? 'Activa' : 'Inactiva' ?></span></span>
                    <span class="admin-item-cell admin-item-cell--order"><?= (int) $category['display_order'] ?></span>
                    <span class="admin-item-cell admin-item-cell--action"><span class="admin-item-link">Ver detalle</span><span class="admin-item-chevron" aria-hidden="true"></span></span>
                  </button>
                <?php endforeach; ?>
                <p class="admin-no-results" data-admin-no-results hidden>No hay resultados con ese filtro.</p>
                <p class="admin-list-foot">Mostrando 1 a <?= count($categories) ?> de <?= count($categories) ?> categorias</p>
              </div>
            </div>

            <div class="admin-detail-column">
              <p class="admin-detail-empty" data-admin-empty-detail hidden>Selecciona una categoria visible para ver sus detalles.</p>
              <?php foreach ($categories as $category): ?>
                <?php $categorySummary = adminFirstNonEmpty([(string) $category['short_description'], (string) $category['description'], (string) $category['visual_key']], 'Sin resumen disponible.'); ?>
                <?php $categoryPreview = adminRecordPreviewImage($category, ['hero_image', 'cover_image']); ?>
                <form method="post" class="admin-detail-panel" data-admin-detail-panel data-detail-id="category-<?= (int) $category['id'] ?>" id="detail-category-<?= (int) $category['id'] ?>" hidden>
                  <input type="hidden" name="action" value="update_category" />
                  <input type="hidden" name="id" value="<?= (int) $category['id'] ?>" />
                  <div class="admin-detail-card">
                    <div class="admin-detail-topbar">
                      <p>Detalle de categoria</p>
                      <button type="button" class="admin-detail-close" data-admin-close-detail aria-label="Cerrar detalle">x</button>
                    </div>
                    <div class="admin-detail-hero<?= $categoryPreview === '' ? ' is-empty' : '' ?>">
                      <?php if ($categoryPreview !== ''): ?>
                        <img src="<?= adminEscape($categoryPreview) ?>" alt="<?= adminEscape($category['name']) ?>" loading="lazy" />
                      <?php else: ?>
                        <span><?= adminEscape($category['visual_key']) ?></span>
                      <?php endif; ?>
                    </div>
                    <div class="admin-edit-intro">
                      <h3>Editar categoria</h3>
                      <p>Actualiza la informacion y los assets de esta categoria.</p>
                    </div>
                    <div class="admin-edit-meta">
                      <span class="admin-status-badge"><?= (int) $category['is_active'] === 1 ? 'Activa' : 'Inactiva' ?></span>
                      <span class="admin-edit-chip">Orden <?= (int) $category['display_order'] ?></span>
                    </div>
                    <div class="admin-detail-copy">
                      <div class="admin-detail-readonly">
                        <div><span>Nombre</span><strong><?= adminEscape($category['name']) ?></strong></div>
                        <div><span>Slug</span><strong><?= adminEscape($category['slug']) ?></strong></div>
                        <div><span>Estado</span><strong class="admin-status-badge"><?= (int) $category['is_active'] === 1 ? 'Activa' : 'Inactiva' ?></strong></div>
                        <div><span>Orden</span><strong><?= (int) $category['display_order'] ?></strong></div>
                        <div><span>Resumen / descripcion</span><strong><?= adminEscape($categorySummary) ?></strong></div>
                        <div><span>Fecha de actualizacion</span><strong><?= adminEscape(adminFormatDateLabel((string) $category['updated_at'])) ?></strong></div>
                      </div>
                    </div>
                    <div class="admin-detail-actions-label">Acciones rapidas</div>
                    <div class="admin-detail-actions-quick">
                      <button type="button" class="admin-link admin-link--button" data-admin-edit-toggle>Editar</button>
                      <button type="submit" name="submit_action" value="duplicate_category" class="admin-link admin-link--ghost" formnovalidate>Duplicar</button>
                      <button type="button" class="admin-link admin-link--ghost" disabled>Archivar</button>
                    </div>
                    <div class="admin-detail-editor" data-admin-editable hidden>
                      <div class="admin-detail-editor-row admin-detail-editor-row--split">
                        <label class="admin-detail-field">Nombre<input type="text" name="name" value="<?= adminEscape($category['name']) ?>" required /></label>
                        <label class="admin-detail-field">Slug<input type="text" name="slug" value="<?= adminEscape($category['slug']) ?>" required /></label>
                      </div>
                      <div class="admin-detail-editor-row">
                        <label class="admin-detail-field">Visual key<input type="text" name="visual_key" value="<?= adminEscape($category['visual_key']) ?>" required /></label>
                      </div>
                      <div class="admin-detail-editor-row">
                        <label class="admin-detail-field">Resumen<input type="text" name="short_description" value="<?= adminEscape($category['short_description']) ?>" /></label>
                      </div>
                      <div class="admin-detail-editor-row">
                        <label class="admin-detail-field">Descripcion<textarea name="description"><?= adminEscape($category['description']) ?></textarea></label>
                      </div>
                      <div class="admin-detail-editor-row admin-detail-editor-row--split admin-detail-editor-row--media">
                        <label class="admin-asset-field admin-detail-field" data-admin-image-field>
                          <span>Cover image</span>
                          <span class="admin-asset-preview<?= $category['cover_image'] === '' ? ' is-empty' : '' ?>" data-admin-image-preview>
                            <img src="<?= adminEscape(adminRecordPreviewImage($category, ['cover_image'])) ?>" alt="Preview cover image" data-admin-image-tag<?= $category['cover_image'] === '' ? ' hidden' : '' ?> loading="lazy" />
                            <span class="admin-asset-placeholder"<?= $category['cover_image'] !== '' ? ' hidden' : '' ?>>Sin imagen</span>
                          </span>
                          <span class="admin-asset-actions">
                            <button type="button" class="admin-asset-action" data-admin-image-browse>Cambiar</button>
                            <button type="button" class="admin-asset-action admin-asset-action--icon" data-admin-image-clear aria-label="Limpiar cover image">x</button>
                          </span>
                          <input type="text" name="cover_image" value="<?= adminEscape($category['cover_image']) ?>" data-admin-image-input />
                        </label>
                        <label class="admin-asset-field admin-detail-field" data-admin-image-field>
                          <span>Hero image</span>
                          <span class="admin-asset-preview<?= $category['hero_image'] === '' ? ' is-empty' : '' ?>" data-admin-image-preview>
                            <img src="<?= adminEscape(adminRecordPreviewImage($category, ['hero_image'])) ?>" alt="Preview hero image" data-admin-image-tag<?= $category['hero_image'] === '' ? ' hidden' : '' ?> loading="lazy" />
                            <span class="admin-asset-placeholder"<?= $category['hero_image'] !== '' ? ' hidden' : '' ?>>Sin imagen</span>
                          </span>
                          <span class="admin-asset-actions">
                            <button type="button" class="admin-asset-action" data-admin-image-browse>Cambiar</button>
                            <button type="button" class="admin-asset-action admin-asset-action--icon" data-admin-image-clear aria-label="Limpiar hero image">x</button>
                          </span>
                          <input type="text" name="hero_image" value="<?= adminEscape($category['hero_image']) ?>" data-admin-image-input />
                        </label>
                      </div>
                      <div class="admin-detail-editor-row admin-detail-editor-row--split">
                        <label class="admin-detail-field">Enlace<input type="text" name="link_url" value="<?= adminEscape($category['link_url']) ?>" /></label>
                        <label class="admin-detail-field admin-detail-field--compact">Orden<input type="number" name="display_order" value="<?= (int) $category['display_order'] ?>" min="0" /></label>
                      </div>
                      <div class="admin-detail-editor-row">
                        <label class="admin-checkbox admin-checkbox--hint"><input type="checkbox" name="is_active" <?= adminChecked($category['is_active']) ?> /><span class="admin-checkbox-copy"><span>Activa</span><small class="admin-checkbox-hint">La categoria estara visible en el sitio.</small></span></label>
                      </div>
                      <div class="admin-detail-editor-row admin-actions admin-actions--editor">
                        <button type="submit" class="admin-button admin-button--primary">Guardar cambios</button>
                        <button type="button" class="admin-button admin-button--secondary" data-admin-cancel-edit>Cancelar</button>
                      </div>
                      <div class="admin-detail-editor-row">
                        <button type="submit" name="submit_action" value="delete_category" class="admin-button admin-button--danger admin-button--text" formnovalidate onclick="return confirm('Se eliminara esta categoria. Continuar?');">Eliminar categoria</button>
                      </div>
                    </div>
                  </div>
                </form>
              <?php endforeach; ?>
            </div>
          </div>
        </section>

        <section class="admin-panel" id="entidades" data-admin-panel>
          <div class="admin-master-detail" data-admin-master-detail>
            <div class="admin-master-detail-main">
              <div class="admin-page-heading">
                <div>
                  <h2>Entidades</h2>
                  <p>Administra marcas, selecciones y universos editoriales.</p>
                </div>
                <span class="admin-page-count"><?= count($entities) ?> registradas</span>
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
                <label class="admin-panel-filter admin-panel-filter--sort">
                  <span>Orden</span>
                  <select data-admin-sort>
                    <option value="order-asc">Ascendente</option>
                    <option value="order-desc">Descendente</option>
                    <option value="name-asc">A-Z</option>
                    <option value="name-desc">Z-A</option>
                  </select>
                </label>
                <a class="admin-toolbar-cta" href="#crear-contenido">Nueva entidad</a>
              </div>

              <div class="admin-record-list admin-list">
                <div class="admin-list-head admin-list-head--wide" aria-hidden="true">
                  <span>Entidad</span>
                  <span>Slug</span>
                  <span>Tipo</span>
                  <span>Estado</span>
                  <span>Acciones</span>
                </div>
                <?php foreach ($entities as $entity): ?>
                  <?php $entitySummary = adminFirstNonEmpty([(string) $entity['short_description'], (string) $entity['subtitle'], (string) $entity['description']], 'Sin resumen disponible.'); ?>
                  <?php $entityPreview = adminRecordPreviewImage($entity, ['cover_image', 'logo_url']); ?>
                  <button type="button" class="admin-item admin-item--table" data-admin-item data-admin-detail-trigger data-detail-id="entity-<?= (int) $entity['id'] ?>" data-active="<?= (int) $entity['is_active'] ?>" data-featured="<?= (int) $entity['is_featured'] ?>" data-order="<?= (int) $entity['display_order'] ?>" data-name="<?= adminEscape(strtolower((string) $entity['name'])) ?>" aria-controls="detail-entity-<?= (int) $entity['id'] ?>">
                    <span class="admin-item-main">
                      <span class="admin-item-media<?= $entityPreview === '' ? ' is-empty' : '' ?>">
                        <?php if ($entityPreview !== ''): ?>
                          <img src="<?= adminEscape($entityPreview) ?>" alt="<?= adminEscape($entity['name']) ?>" loading="lazy" />
                        <?php else: ?>
                          <span><?= adminEscape(strtoupper(substr((string) $entity['name'], 0, 1))) ?></span>
                        <?php endif; ?>
                      </span>
                      <span class="admin-item-copy">
                        <span class="admin-item-kicker">Entidad · <?= adminEscape((string) ($entity['category_name'] ?? 'Sin categoria')) ?></span>
                        <span class="admin-item-title"><?= adminEscape($entity['name']) ?></span>
                        <span class="admin-item-summary"><?= adminEscape($entitySummary) ?></span>
                      </span>
                    </span>
                    <span class="admin-item-cell"><?= adminEscape($entity['slug']) ?></span>
                    <span class="admin-item-cell"><?= adminEscape($entity['entity_type']) ?></span>
                    <span class="admin-item-cell"><span class="admin-status-badge"><?= (int) $entity['is_active'] === 1 ? 'Activa' : 'Inactiva' ?></span></span>
                    <span class="admin-item-cell admin-item-cell--action"><span class="admin-item-link">Ver detalle</span><span class="admin-item-chevron" aria-hidden="true"></span></span>
                  </button>
                <?php endforeach; ?>
                <p class="admin-no-results" data-admin-no-results hidden>No hay resultados con ese filtro.</p>
                <p class="admin-list-foot">Mostrando 1 a <?= count($entities) ?> de <?= count($entities) ?> entidades</p>
              </div>
            </div>

            <div class="admin-detail-column">
              <p class="admin-detail-empty" data-admin-empty-detail hidden>Selecciona una entidad visible para ver sus detalles.</p>
              <?php foreach ($entities as $entity): ?>
                <?php $entitySummary = adminFirstNonEmpty([(string) $entity['short_description'], (string) $entity['subtitle'], (string) $entity['description']], 'Sin resumen disponible.'); ?>
                <?php $entityPreview = adminRecordPreviewImage($entity, ['cover_image', 'logo_url']); ?>
                <form method="post" class="admin-detail-panel" data-admin-detail-panel data-detail-id="entity-<?= (int) $entity['id'] ?>" id="detail-entity-<?= (int) $entity['id'] ?>" hidden>
                  <input type="hidden" name="action" value="update_entity" />
                  <input type="hidden" name="id" value="<?= (int) $entity['id'] ?>" />
                  <div class="admin-detail-card">
                    <div class="admin-detail-topbar">
                      <p>Detalle de entidad</p>
                      <button type="button" class="admin-detail-close" data-admin-close-detail aria-label="Cerrar detalle">x</button>
                    </div>
                    <div class="admin-detail-hero<?= $entityPreview === '' ? ' is-empty' : '' ?>">
                      <?php if ($entityPreview !== ''): ?>
                        <img src="<?= adminEscape($entityPreview) ?>" alt="<?= adminEscape($entity['name']) ?>" loading="lazy" />
                      <?php else: ?>
                        <span><?= adminEscape($entity['entity_type']) ?></span>
                      <?php endif; ?>
                    </div>
                    <div class="admin-edit-intro">
                      <h3>Editar entidad</h3>
                      <p>Actualiza la informacion editorial y visual de esta entidad.</p>
                    </div>
                    <div class="admin-edit-meta">
                      <span class="admin-status-badge"><?= (int) $entity['is_active'] === 1 ? 'Activa' : 'Inactiva' ?></span>
                      <span class="admin-edit-chip">Orden <?= (int) $entity['display_order'] ?></span>
                    </div>
                    <div class="admin-detail-copy">
                      <div class="admin-detail-readonly">
                        <div><span>Nombre</span><strong><?= adminEscape($entity['name']) ?></strong></div>
                        <div><span>Slug</span><strong><?= adminEscape($entity['slug']) ?></strong></div>
                        <div><span>Tipo</span><strong><?= adminEscape($entity['entity_type']) ?></strong></div>
                        <div><span>Categoria</span><strong><?= adminEscape((string) ($entity['category_name'] ?? 'Sin categoria')) ?></strong></div>
                        <div><span>Resumen / descripcion</span><strong><?= adminEscape($entitySummary) ?></strong></div>
                        <div><span>Fecha de actualizacion</span><strong><?= adminEscape(adminFormatDateLabel((string) $entity['updated_at'])) ?></strong></div>
                      </div>
                    </div>
                    <div class="admin-detail-actions-label">Acciones rapidas</div>
                    <div class="admin-detail-actions-quick">
                      <button type="button" class="admin-link admin-link--button" data-admin-edit-toggle>Editar</button>
                      <button type="submit" name="submit_action" value="duplicate_entity" class="admin-link admin-link--ghost" formnovalidate>Duplicar</button>
                      <button type="button" class="admin-link admin-link--ghost" disabled>Archivar</button>
                    </div>
                    <div class="admin-detail-editor" data-admin-editable hidden>
                      <div class="admin-detail-editor-row admin-detail-editor-row--split">
                        <label class="admin-detail-field">Categoria
                          <select name="category_id" required>
                            <?php foreach ($categories as $category): ?>
                              <option value="<?= (int) $category['id'] ?>" <?= adminSelected($entity['category_id'], $category['id']) ?>><?= adminEscape(adminCategoryLabel($category)) ?></option>
                            <?php endforeach; ?>
                          </select>
                        </label>
                        <label class="admin-detail-field">Nombre<input type="text" name="name" value="<?= adminEscape($entity['name']) ?>" required /></label>
                      </div>
                      <div class="admin-detail-editor-row admin-detail-editor-row--split">
                        <label class="admin-detail-field">Slug<input type="text" name="slug" value="<?= adminEscape($entity['slug']) ?>" required /></label>
                        <label class="admin-detail-field">Entity type<input type="text" name="entity_type" value="<?= adminEscape($entity['entity_type']) ?>" required /></label>
                      </div>
                      <div class="admin-detail-editor-row admin-detail-editor-row--split">
                        <label class="admin-detail-field">Subtitle<input type="text" name="subtitle" value="<?= adminEscape($entity['subtitle']) ?>" /></label>
                        <label class="admin-detail-field admin-detail-field--compact">Orden<input type="number" name="display_order" value="<?= (int) $entity['display_order'] ?>" min="0" /></label>
                      </div>
                      <div class="admin-detail-editor-row">
                        <label class="admin-detail-field">Resumen<input type="text" name="short_description" value="<?= adminEscape($entity['short_description']) ?>" /></label>
                      </div>
                      <div class="admin-detail-editor-row">
                        <label class="admin-detail-field">Descripcion<textarea name="description"><?= adminEscape($entity['description']) ?></textarea></label>
                      </div>
                      <div class="admin-detail-editor-row admin-detail-editor-row--split admin-detail-editor-row--media">
                        <label class="admin-asset-field admin-detail-field" data-admin-image-field>
                          <span>Logo URL</span>
                          <span class="admin-asset-preview<?= $entity['logo_url'] === '' ? ' is-empty' : '' ?>" data-admin-image-preview>
                            <img src="<?= adminEscape(adminRecordPreviewImage($entity, ['logo_url'])) ?>" alt="Preview logo" data-admin-image-tag<?= $entity['logo_url'] === '' ? ' hidden' : '' ?> loading="lazy" />
                            <span class="admin-asset-placeholder"<?= $entity['logo_url'] !== '' ? ' hidden' : '' ?>>Sin imagen</span>
                          </span>
                          <span class="admin-asset-actions">
                            <button type="button" class="admin-asset-action" data-admin-image-browse>Cambiar</button>
                            <button type="button" class="admin-asset-action admin-asset-action--icon" data-admin-image-clear aria-label="Limpiar logo">x</button>
                          </span>
                          <input type="text" name="logo_url" value="<?= adminEscape($entity['logo_url']) ?>" data-admin-image-input />
                        </label>
                        <label class="admin-asset-field admin-detail-field" data-admin-image-field>
                          <span>Cover image</span>
                          <span class="admin-asset-preview<?= $entity['cover_image'] === '' ? ' is-empty' : '' ?>" data-admin-image-preview>
                            <img src="<?= adminEscape(adminRecordPreviewImage($entity, ['cover_image'])) ?>" alt="Preview cover image" data-admin-image-tag<?= $entity['cover_image'] === '' ? ' hidden' : '' ?> loading="lazy" />
                            <span class="admin-asset-placeholder"<?= $entity['cover_image'] !== '' ? ' hidden' : '' ?>>Sin imagen</span>
                          </span>
                          <span class="admin-asset-actions">
                            <button type="button" class="admin-asset-action" data-admin-image-browse>Cambiar</button>
                            <button type="button" class="admin-asset-action admin-asset-action--icon" data-admin-image-clear aria-label="Limpiar cover image">x</button>
                          </span>
                          <input type="text" name="cover_image" value="<?= adminEscape($entity['cover_image']) ?>" data-admin-image-input />
                        </label>
                      </div>
                      <div class="admin-detail-editor-row admin-detail-editor-row--split">
                        <label class="admin-detail-field">Primary color<input type="text" name="primary_color" value="<?= adminEscape($entity['primary_color']) ?>" /></label>
                        <label class="admin-detail-field">Secondary color<input type="text" name="secondary_color" value="<?= adminEscape($entity['secondary_color']) ?>" /></label>
                        <label class="admin-detail-field">Background color<input type="text" name="background_color" value="<?= adminEscape($entity['background_color']) ?>" /></label>
                        <label class="admin-detail-field">Text color<input type="text" name="text_color" value="<?= adminEscape($entity['text_color']) ?>" /></label>
                      </div>
                      <div class="admin-detail-editor-row admin-detail-editor-row--split">
                        <label class="admin-checkbox"><input type="checkbox" name="is_featured" <?= adminChecked($entity['is_featured']) ?> /> Destacada</label>
                        <label class="admin-checkbox"><input type="checkbox" name="is_active" <?= adminChecked($entity['is_active']) ?> /> Activa</label>
                      </div>
                      <div class="admin-detail-editor-row admin-actions admin-actions--editor">
                        <button type="submit" class="admin-button admin-button--primary">Guardar cambios</button>
                        <button type="button" class="admin-button admin-button--secondary" data-admin-cancel-edit>Cancelar</button>
                      </div>
                      <div class="admin-detail-editor-row">
                        <button type="submit" name="submit_action" value="delete_entity" class="admin-button admin-button--danger admin-button--text" formnovalidate onclick="return confirm('Se eliminara esta entidad. Continuar?');">Eliminar entidad</button>
                      </div>
                    </div>
                  </div>
                </form>
              <?php endforeach; ?>
            </div>
          </div>
        </section>

        <section class="admin-panel" id="colecciones" data-admin-panel>
          <div class="admin-master-detail" data-admin-master-detail>
            <div class="admin-master-detail-main">
              <div class="admin-page-heading">
                <div>
                  <h2>Colecciones</h2>
                  <p>Supervisa las colecciones publicadas o en desarrollo.</p>
                </div>
                <span class="admin-page-count"><?= count($collections) ?> registradas</span>
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
                    <option value="published">Published</option>
                    <option value="draft">Draft</option>
                    <option value="inactive">Desactivadas</option>
                    <option value="featured">Destacadas</option>
                  </select>
                </label>
                <label class="admin-panel-filter admin-panel-filter--sort">
                  <span>Orden</span>
                  <select data-admin-sort>
                    <option value="order-asc">Ascendente</option>
                    <option value="order-desc">Descendente</option>
                    <option value="name-asc">A-Z</option>
                    <option value="name-desc">Z-A</option>
                  </select>
                </label>
                <a class="admin-toolbar-cta" href="#crear-contenido">Nueva coleccion</a>
              </div>

              <div class="admin-record-list admin-list">
                <div class="admin-list-head admin-list-head--wide" aria-hidden="true">
                  <span>Coleccion</span>
                  <span>Slug</span>
                  <span>Ano</span>
                  <span>Estado</span>
                  <span>Acciones</span>
                </div>
                <?php foreach ($collections as $collection): ?>
                  <?php $collectionSummary = adminFirstNonEmpty([(string) $collection['short_description'], (string) $collection['subtitle'], (string) $collection['description']], 'Sin resumen disponible.'); ?>
                  <?php $collectionPreview = adminRecordPreviewImage($collection, ['thumbnail_image', 'cover_image']); ?>
                  <?php $collectionEditorialStatus = adminCollectionEditorialStatus($collection); ?>
                  <?php $collectionIsPublished = $collectionEditorialStatus === 'Published'; ?>
                  <button type="button" class="admin-item admin-item--table" data-admin-item data-admin-detail-trigger data-detail-id="collection-<?= (int) $collection['id'] ?>" data-active="<?= (int) $collection['is_active'] ?>" data-published="<?= $collectionIsPublished ? '1' : '0' ?>" data-featured="<?= (int) $collection['is_featured'] ?>" data-order="<?= (int) $collection['display_order'] ?>" data-name="<?= adminEscape(strtolower((string) $collection['name'])) ?>" aria-controls="detail-collection-<?= (int) $collection['id'] ?>">
                    <span class="admin-item-main">
                      <span class="admin-item-media<?= $collectionPreview === '' ? ' is-empty' : '' ?>">
                        <?php if ($collectionPreview !== ''): ?>
                          <img src="<?= adminEscape($collectionPreview) ?>" alt="<?= adminEscape($collection['name']) ?>" loading="lazy" />
                        <?php else: ?>
                          <span><?= adminEscape(strtoupper(substr((string) $collection['name'], 0, 1))) ?></span>
                        <?php endif; ?>
                      </span>
                      <span class="admin-item-copy">
                        <span class="admin-item-kicker">Coleccion · <?= adminEscape((string) ($collection['entity_name'] ?? 'Sin entidad')) ?></span>
                        <span class="admin-item-title"><?= adminEscape($collection['name']) ?></span>
                        <span class="admin-item-summary"><?= adminEscape($collectionSummary) ?></span>
                      </span>
                    </span>
                    <span class="admin-item-cell"><?= adminEscape($collection['slug']) ?></span>
                    <span class="admin-item-cell"><?= !empty($collection['collection_year']) ? (int) $collection['collection_year'] : 'n/a' ?></span>
                    <span class="admin-item-cell"><span class="admin-status-badge admin-status-badge--<?= strtolower($collectionEditorialStatus) ?>"><?= adminEscape($collectionEditorialStatus) ?></span></span>
                    <span class="admin-item-cell admin-item-cell--action"><span class="admin-item-link">Ver detalle</span><span class="admin-item-chevron" aria-hidden="true"></span></span>
                  </button>
                <?php endforeach; ?>
                <p class="admin-no-results" data-admin-no-results hidden>No hay resultados con ese filtro.</p>
                <p class="admin-list-foot">Mostrando 1 a <?= count($collections) ?> de <?= count($collections) ?> colecciones</p>
              </div>
            </div>

            <div class="admin-detail-column">
              <p class="admin-detail-empty" data-admin-empty-detail hidden>Selecciona una coleccion visible para ver sus detalles.</p>
              <?php foreach ($collections as $collection): ?>
                <?php $collectionSummary = adminFirstNonEmpty([(string) $collection['short_description'], (string) $collection['subtitle'], (string) $collection['description']], 'Sin resumen disponible.'); ?>
                <?php $collectionPreview = adminRecordPreviewImage($collection, ['cover_image', 'thumbnail_image']); ?>
                <?php $collectionEditorialStatus = adminCollectionEditorialStatus($collection); ?>
                <?php $collectionIsPublished = $collectionEditorialStatus === 'Published'; ?>
                <?php $collectionPreviewUrl = adminCollectionPreviewUrl($collection); ?>
                <form method="post" class="admin-detail-panel" data-admin-detail-panel data-detail-id="collection-<?= (int) $collection['id'] ?>" id="detail-collection-<?= (int) $collection['id'] ?>" hidden>
                  <input type="hidden" name="action" value="update_collection" />
                  <input type="hidden" name="id" value="<?= (int) $collection['id'] ?>" />
                  <div class="admin-detail-card">
                    <div class="admin-detail-topbar">
                      <p>Detalle de coleccion</p>
                      <button type="button" class="admin-detail-close" data-admin-close-detail aria-label="Cerrar detalle">x</button>
                    </div>
                    <div class="admin-detail-hero<?= $collectionPreview === '' ? ' is-empty' : '' ?>">
                      <?php if ($collectionPreview !== ''): ?>
                        <img src="<?= adminEscape($collectionPreview) ?>" alt="<?= adminEscape($collection['name']) ?>" loading="lazy" />
                      <?php else: ?>
                        <span><?= adminEscape((string) ($collection['entity_name'] ?? 'Coleccion')) ?></span>
                      <?php endif; ?>
                    </div>
                    <div class="admin-edit-intro">
                      <h3>Editar coleccion</h3>
                      <p>Actualiza el contenido, la narrativa y los assets de esta coleccion.</p>
                    </div>
                    <div class="admin-edit-meta">
                      <span class="admin-status-badge admin-status-badge--<?= strtolower($collectionEditorialStatus) ?>"><?= adminEscape($collectionEditorialStatus) ?></span>
                      <span class="admin-edit-chip">Orden <?= (int) $collection['display_order'] ?></span>
                    </div>
                    <div class="admin-detail-copy">
                      <div class="admin-detail-readonly">
                        <div><span>Nombre</span><strong><?= adminEscape($collection['name']) ?></strong></div>
                        <div><span>Slug</span><strong><?= adminEscape($collection['slug']) ?></strong></div>
                        <div><span>Entidad</span><strong><?= adminEscape((string) ($collection['entity_name'] ?? 'Sin entidad')) ?></strong></div>
                        <div><span>Plantilla de origen</span><strong><?= adminEscape((string) ($collection['template_name'] ?? 'Coleccion vacia')) ?></strong></div>
                        <div><span>Ano</span><strong><?= !empty($collection['collection_year']) ? (int) $collection['collection_year'] : 'n/a' ?></strong></div>
                        <div><span>Resumen / descripcion</span><strong><?= adminEscape($collectionSummary) ?></strong></div>
                        <div><span>Fecha de actualizacion</span><strong><?= adminEscape(adminFormatDateLabel((string) $collection['updated_at'])) ?></strong></div>
                        <div><span>Publicacion</span><strong><?= $collectionIsPublished ? adminEscape(adminFormatDateLabel((string) $collection['published_at'])) : 'Sin publicar' ?></strong></div>
                      </div>
                    </div>
                    <div class="admin-detail-actions-label">Acciones rapidas</div>
                    <div class="admin-detail-actions-quick">
                      <button type="button" class="admin-link admin-link--button" data-admin-edit-toggle>Editar</button>
                      <a href="<?= adminEscape($collectionPreviewUrl) ?>" class="admin-link admin-link--ghost" target="_blank" rel="noopener">Preview</a>
                      <?php if ($collectionIsPublished): ?>
                        <button type="submit" name="submit_action" value="unpublish_collection" class="admin-link admin-link--ghost" formnovalidate>Volver a Draft</button>
                      <?php else: ?>
                        <button type="submit" name="submit_action" value="publish_collection" class="admin-link admin-link--button" formnovalidate>Publish</button>
                      <?php endif; ?>
                      <button type="submit" name="submit_action" value="duplicate_collection" class="admin-link admin-link--ghost" formnovalidate>Duplicar</button>
                      <button type="submit" name="submit_action" value="save_collection_as_template" class="admin-link admin-link--ghost" formnovalidate>Guardar como plantilla</button>
                      <button type="submit" name="submit_action" value="regenerate_collection_preview" class="admin-link admin-link--ghost" formnovalidate onclick="return confirm('El enlace de preview actual dejara de funcionar. Continuar?');">Regenerar preview</button>
                    </div>
                    <div class="admin-detail-editor" data-admin-editable hidden>
                      <div class="admin-detail-editor-row admin-detail-editor-row--split">
                        <label class="admin-detail-field">Entidad
                          <select name="entity_id" required>
                            <?php foreach ($entities as $entity): ?>
                              <option value="<?= (int) $entity['id'] ?>" <?= adminSelected($collection['entity_id'], $entity['id']) ?>><?= adminEscape(adminEntityLabel($entity)) ?></option>
                            <?php endforeach; ?>
                          </select>
                        </label>
                        <label class="admin-detail-field">Nombre<input type="text" name="name" value="<?= adminEscape($collection['name']) ?>" required /></label>
                      </div>
                      <div class="admin-detail-editor-row admin-detail-editor-row--split">
                        <label class="admin-detail-field">Slug<input type="text" name="slug" value="<?= adminEscape($collection['slug']) ?>" required /></label>
                        <label class="admin-detail-field">Subtitle<input type="text" name="subtitle" value="<?= adminEscape($collection['subtitle']) ?>" /></label>
                      </div>
                      <div class="admin-detail-editor-row admin-detail-editor-row--split">
                        <label class="admin-detail-field admin-detail-field--compact">Ano<input type="number" name="collection_year" value="<?= (int) $collection['collection_year'] ?>" min="1900" max="2100" /></label>
                        <label class="admin-detail-field">Season<input type="text" name="season" value="<?= adminEscape($collection['season']) ?>" /></label>
                        <label class="admin-detail-field admin-detail-field--compact">Orden<input type="number" name="display_order" value="<?= (int) $collection['display_order'] ?>" min="0" /></label>
                      </div>
                      <div class="admin-detail-editor-row">
                        <label class="admin-detail-field">Resumen<input type="text" name="short_description" value="<?= adminEscape($collection['short_description']) ?>" /></label>
                      </div>
                      <div class="admin-detail-editor-row admin-detail-editor-row--split">
                        <label class="admin-detail-field">Descripcion<textarea name="description"><?= adminEscape($collection['description']) ?></textarea></label>
                        <label class="admin-detail-field">Concept<textarea name="concept"><?= adminEscape($collection['concept']) ?></textarea></label>
                      </div>
                      <div class="admin-detail-editor-row admin-detail-editor-row--split admin-detail-editor-row--media">
                        <label class="admin-asset-field admin-detail-field" data-admin-image-field>
                          <span>Cover image</span>
                          <span class="admin-asset-preview<?= $collection['cover_image'] === '' ? ' is-empty' : '' ?>" data-admin-image-preview>
                            <img src="<?= adminEscape(adminRecordPreviewImage($collection, ['cover_image'])) ?>" alt="Preview cover image" data-admin-image-tag<?= $collection['cover_image'] === '' ? ' hidden' : '' ?> loading="lazy" />
                            <span class="admin-asset-placeholder"<?= $collection['cover_image'] !== '' ? ' hidden' : '' ?>>Sin imagen</span>
                          </span>
                          <span class="admin-asset-actions">
                            <button type="button" class="admin-asset-action" data-admin-image-browse>Cambiar</button>
                            <button type="button" class="admin-asset-action admin-asset-action--icon" data-admin-image-clear aria-label="Limpiar cover image">x</button>
                          </span>
                          <input type="text" name="cover_image" value="<?= adminEscape($collection['cover_image']) ?>" data-admin-image-input />
                        </label>
                        <label class="admin-asset-field admin-detail-field" data-admin-image-field>
                          <span>Thumbnail image</span>
                          <span class="admin-asset-preview<?= $collection['thumbnail_image'] === '' ? ' is-empty' : '' ?>" data-admin-image-preview>
                            <img src="<?= adminEscape(adminRecordPreviewImage($collection, ['thumbnail_image'])) ?>" alt="Preview thumbnail image" data-admin-image-tag<?= $collection['thumbnail_image'] === '' ? ' hidden' : '' ?> loading="lazy" />
                            <span class="admin-asset-placeholder"<?= $collection['thumbnail_image'] !== '' ? ' hidden' : '' ?>>Sin imagen</span>
                          </span>
                          <span class="admin-asset-actions">
                            <button type="button" class="admin-asset-action" data-admin-image-browse>Cambiar</button>
                            <button type="button" class="admin-asset-action admin-asset-action--icon" data-admin-image-clear aria-label="Limpiar thumbnail image">x</button>
                          </span>
                          <input type="text" name="thumbnail_image" value="<?= adminEscape($collection['thumbnail_image']) ?>" data-admin-image-input />
                        </label>
                      </div>
                      <div class="admin-detail-editor-row admin-detail-editor-row--split">
                        <label class="admin-detail-field">Estado editorial<input type="text" value="<?= adminEscape($collectionEditorialStatus) ?>" readonly /></label>
                        <label class="admin-detail-field">Image variant<input type="text" name="image_variant" value="<?= adminEscape($collection['image_variant']) ?>" /></label>
                        <label class="admin-detail-field">Layout style<input type="text" name="layout_style" value="<?= adminEscape($collection['layout_style']) ?>" /></label>
                      </div>
                      <div class="admin-detail-editor-row admin-detail-editor-row--split">
                        <label class="admin-detail-field">Primary color<input type="text" name="primary_color" value="<?= adminEscape($collection['primary_color']) ?>" /></label>
                        <label class="admin-detail-field">Secondary color<input type="text" name="secondary_color" value="<?= adminEscape($collection['secondary_color']) ?>" /></label>
                      </div>
                      <div class="admin-detail-editor-row admin-detail-editor-row--split">
                        <label class="admin-detail-field">Background color<input type="text" name="background_color" value="<?= adminEscape($collection['background_color']) ?>" /></label>
                        <label class="admin-detail-field">Text color<input type="text" name="text_color" value="<?= adminEscape($collection['text_color']) ?>" /></label>
                      </div>
                      <div class="admin-detail-editor-row admin-detail-editor-row--split">
                        <label class="admin-checkbox"><input type="checkbox" name="is_featured" <?= adminChecked($collection['is_featured']) ?> /> Destacada</label>
                        <label class="admin-checkbox"><input type="checkbox" name="is_active" <?= adminChecked($collection['is_active']) ?> /> Activa</label>
                      </div>
                      <div class="admin-detail-editor-row admin-actions admin-actions--editor">
                        <button type="submit" class="admin-button admin-button--primary">Guardar cambios</button>
                        <button type="button" class="admin-button admin-button--secondary" data-admin-cancel-edit>Cancelar</button>
                      </div>
                      <div class="admin-detail-editor-row">
                        <button type="submit" name="submit_action" value="delete_collection" class="admin-button admin-button--danger admin-button--text" formnovalidate onclick="return confirm('Se eliminara esta coleccion. Continuar?');">Eliminar coleccion</button>
                      </div>
                    </div>
                  </div>
                </form>
              <?php endforeach; ?>
            </div>
          </div>
        </section>


        <section class="admin-panel" id="plantillas" data-admin-panel data-selected-detail-id="<?= adminEscape(str_starts_with($requestedDetailId, 'template-') ? $requestedDetailId : '') ?>">
          <div class="admin-section-title">
            <div>
              <p class="admin-eyebrow">Plantillas</p>
              <h2>Estructuras reutilizables</h2>
            </div>
            <p>Crea presets de secciones y aplicalos como punto de partida al crear nuevas colecciones.</p>
          </div>

          <div class="admin-panel-toolbar">
            <label class="admin-search-field">Buscar<input type="search" placeholder="Nombre, slug o descripcion" data-admin-search /></label>
            <label>Estado
              <select data-admin-status-filter>
                <option value="all">Todas</option>
                <option value="active">Activas</option>
                <option value="inactive">Inactivas</option>
              </select>
            </label>
            <label>Orden
              <select data-admin-sort>
                <option value="order-asc">Orden ascendente</option>
                <option value="order-desc">Orden descendente</option>
                <option value="name-asc">Nombre A-Z</option>
                <option value="name-desc">Nombre Z-A</option>
              </select>
            </label>
            <a class="admin-link admin-link--button" href="#crear-contenido">Nueva plantilla</a>
          </div>

          <div class="admin-master-detail" data-admin-master-detail>
            <div class="admin-list-column admin-master-detail-main">
              <div class="admin-list-head admin-list-head--collections">
                <span>Plantilla</span><span>Slug</span><span>Secciones</span><span>Estado</span>
              </div>
              <div class="admin-record-list">
                <?php foreach ($templates as $template): ?>
                  <?php $templatePreview = adminRecordPreviewImage($template, ['preview_image']); ?>
                  <button type="button" class="admin-item admin-item--table" data-admin-item data-admin-detail-trigger data-detail-id="template-<?= (int) $template['id'] ?>" data-active="<?= (int) $template['is_active'] ?>" data-featured="0" data-order="<?= (int) $template['display_order'] ?>" data-name="<?= adminEscape(strtolower((string) $template['name'])) ?>" aria-controls="detail-template-<?= (int) $template['id'] ?>">
                    <span class="admin-item-main">
                      <span class="admin-item-media<?= $templatePreview === '' ? ' is-empty' : '' ?>">
                        <?php if ($templatePreview !== ''): ?>
                          <img src="<?= adminEscape($templatePreview) ?>" alt="<?= adminEscape($template['name']) ?>" loading="lazy" />
                        <?php else: ?>
                          <span><?= adminEscape(strtoupper(substr((string) $template['name'], 0, 1))) ?></span>
                        <?php endif; ?>
                      </span>
                      <span class="admin-item-copy">
                        <span class="admin-item-kicker">Preset de coleccion</span>
                        <span class="admin-item-title"><?= adminEscape($template['name']) ?></span>
                        <span class="admin-item-summary"><?= adminEscape(adminFirstNonEmpty([(string) $template['description']], 'Sin descripcion.')) ?></span>
                      </span>
                    </span>
                    <span class="admin-item-cell"><?= adminEscape($template['slug']) ?></span>
                    <span class="admin-item-cell"><?= (int) $template['section_count'] ?></span>
                    <span class="admin-item-cell"><span class="admin-status-badge"><?= (int) $template['is_active'] === 1 ? 'Activa' : 'Inactiva' ?></span></span>
                  </button>
                <?php endforeach; ?>
              </div>
              <p class="admin-no-results" data-admin-no-results hidden>No hay plantillas que coincidan con los filtros.</p>
              <p class="admin-list-foot">Mostrando <?= count($templates) ?> plantillas</p>
            </div>

            <div class="admin-detail-column">
              <p class="admin-detail-empty" data-admin-empty-detail hidden>Selecciona una plantilla para editar su estructura.</p>
              <?php foreach ($templates as $template): ?>
                <?php
                  $templateId = (int) $template['id'];
                  $currentTemplateSections = $templateSectionsByTemplate[$templateId] ?? [];
                  $templatePreview = adminRecordPreviewImage($template, ['preview_image']);
                ?>
                <div class="admin-detail-panel" data-admin-detail-panel data-detail-id="template-<?= $templateId ?>" id="detail-template-<?= $templateId ?>" hidden>
                  <div class="admin-detail-card">
                    <div class="admin-detail-topbar">
                      <p>Detalle de plantilla</p>
                      <button type="button" class="admin-detail-close" data-admin-close-detail aria-label="Cerrar detalle">x</button>
                    </div>
                    <div class="admin-detail-hero<?= $templatePreview === '' ? ' is-empty' : '' ?>">
                      <?php if ($templatePreview !== ''): ?>
                        <img src="<?= adminEscape($templatePreview) ?>" alt="<?= adminEscape($template['name']) ?>" loading="lazy" />
                      <?php else: ?>
                        <span><?= adminEscape((string) $template['name']) ?></span>
                      <?php endif; ?>
                    </div>
                    <div class="admin-edit-intro">
                      <h3>Editar plantilla</h3>
                      <p>Los cambios afectan a futuras colecciones, no a las que ya fueron creadas.</p>
                    </div>
                    <div class="admin-edit-meta">
                      <span class="admin-status-badge"><?= (int) $template['is_active'] === 1 ? 'Activa' : 'Inactiva' ?></span>
                      <span class="admin-edit-chip"><?= count($currentTemplateSections) ?> secciones</span>
                    </div>
                    <div class="admin-detail-copy">
                      <div class="admin-detail-readonly">
                        <div><span>Nombre</span><strong><?= adminEscape($template['name']) ?></strong></div>
                        <div><span>Slug</span><strong><?= adminEscape($template['slug']) ?></strong></div>
                        <div><span>Secciones</span><strong><?= count($currentTemplateSections) ?></strong></div>
                        <div><span>Descripcion</span><strong><?= adminEscape(adminFirstNonEmpty([(string) $template['description']], 'Sin descripcion.')) ?></strong></div>
                      </div>
                    </div>
                    <div class="admin-detail-actions-label">Acciones rapidas</div>
                    <div class="admin-detail-actions-quick">
                      <button type="button" class="admin-link admin-link--button" data-admin-edit-toggle>Editar</button>
                      <form method="post" class="admin-inline-form">
                        <input type="hidden" name="action" value="duplicate_template" />
                        <input type="hidden" name="id" value="<?= $templateId ?>" />
                        <button type="submit" class="admin-link admin-link--ghost">Duplicar</button>
                      </form>
                      <form method="post" class="admin-inline-form" onsubmit="return confirm('¿Eliminar esta plantilla y todas sus secciones?');">
                        <input type="hidden" name="action" value="delete_template" />
                        <input type="hidden" name="id" value="<?= $templateId ?>" />
                        <button type="submit" class="admin-link admin-link--ghost">Eliminar</button>
                      </form>
                    </div>

                    <form method="post" class="admin-detail-editor" data-admin-editable hidden>
                      <input type="hidden" name="action" value="update_template" />
                      <input type="hidden" name="id" value="<?= $templateId ?>" />
                      <div class="admin-detail-editor-row admin-detail-editor-row--split">
                        <label class="admin-detail-field">Nombre<input type="text" name="name" value="<?= adminEscape($template['name']) ?>" required /></label>
                        <label class="admin-detail-field">Slug<input type="text" name="slug" value="<?= adminEscape($template['slug']) ?>" required /></label>
                      </div>
                      <div class="admin-detail-editor-row">
                        <label class="admin-detail-field">Descripcion<textarea name="description"><?= adminEscape($template['description']) ?></textarea></label>
                      </div>
                      <div class="admin-detail-editor-row admin-detail-editor-row--split">
                        <label class="admin-detail-field">Imagen de vista previa<input type="text" name="preview_image" value="<?= adminEscape($template['preview_image']) ?>" /></label>
                        <label class="admin-detail-field admin-detail-field--compact">Orden<input type="number" name="display_order" value="<?= (int) $template['display_order'] ?>" min="0" /></label>
                      </div>
                      <label class="admin-checkbox"><input type="checkbox" name="is_active" <?= adminChecked((int) $template['is_active'] === 1) ?> /> Activa</label>
                      <div class="admin-detail-editor-actions">
                        <button type="button" class="admin-link admin-link--ghost" data-admin-cancel-edit>Cancelar</button>
                        <button type="submit" class="admin-link admin-link--button">Guardar plantilla</button>
                      </div>
                    </form>

                    <section class="admin-template-sections-manager">
                      <div class="admin-section-media-heading">
                        <div>
                          <p class="admin-eyebrow">Estructura</p>
                          <h3>Secciones de la plantilla</h3>
                        </div>
                        <span class="admin-section-media-count"><?= count($currentTemplateSections) ?> bloques</span>
                      </div>

                      <div class="admin-template-section-list">
                        <?php foreach ($currentTemplateSections as $templateSectionIndex => $templateSection): ?>
                          <details class="admin-template-section-item">
                            <summary>
                              <span class="admin-template-section-order"><?= (int) $templateSection['display_order'] ?></span>
                              <span>
                                <strong><?= adminEscape((string) ($templateSection['title'] ?: $templateSection['section_key'])) ?></strong>
                                <small><?= adminEscape((string) $templateSection['section_type']) ?> · <?= adminEscape((string) $templateSection['section_key']) ?></small>
                              </span>
                              <span class="admin-status-badge"><?= (int) $templateSection['is_active'] === 1 ? 'Activa' : 'Inactiva' ?></span>
                            </summary>
                            <form method="post" class="admin-template-section-form" data-section-settings-form>
                              <input type="hidden" name="action" value="update_template_section" />
                              <input type="hidden" name="id" value="<?= (int) $templateSection['id'] ?>" />
                              <input type="hidden" name="template_id" value="<?= $templateId ?>" />
                              <div class="admin-section-media-form-grid">
                                <label>Tipo
                                  <select name="section_type" required data-section-type-select>
                                    <?php foreach (adminSectionTypeOptions() as $sectionType => $sectionTypeLabel): ?>
                                      <option value="<?= adminEscape($sectionType) ?>" <?= adminSelected($templateSection['section_type'], $sectionType) ?>><?= adminEscape($sectionTypeLabel) ?></option>
                                    <?php endforeach; ?>
                                  </select>
                                </label>
                                <label>Section key<input type="text" name="section_key" value="<?= adminEscape($templateSection['section_key']) ?>" required /></label>
                              </div>
                              <div class="admin-section-media-form-grid">
                                <label>Eyebrow<input type="text" name="eyebrow" value="<?= adminEscape($templateSection['eyebrow']) ?>" /></label>
                                <label>Titulo<input type="text" name="title" value="<?= adminEscape($templateSection['title']) ?>" /></label>
                              </div>
                              <label>Contenido<textarea name="body"><?= adminEscape($templateSection['body']) ?></textarea></label>
                              <?= adminRenderSectionVisualSettings((string) $templateSection['section_type'], $templateSection['settings_json']) ?>
                              <div class="admin-section-media-form-grid">
                                <label>Orden<input type="number" name="display_order" value="<?= (int) $templateSection['display_order'] ?>" min="0" /></label>
                                <label class="admin-checkbox"><input type="checkbox" name="is_active" <?= adminChecked((int) $templateSection['is_active'] === 1) ?> /> Activa</label>
                              </div>
                              <div class="admin-template-section-actions">
                                <button type="submit" name="submit_action" value="update_template_section" class="admin-media-action">Guardar</button>
                                <button type="submit" name="submit_action" value="move_template_section_up" class="admin-media-action" formnovalidate <?= $templateSectionIndex === 0 ? 'disabled' : '' ?>>Subir</button>
                                <button type="submit" name="submit_action" value="move_template_section_down" class="admin-media-action" formnovalidate <?= $templateSectionIndex === count($currentTemplateSections) - 1 ? 'disabled' : '' ?>>Bajar</button>
                                <button type="submit" name="submit_action" value="delete_template_section" class="admin-media-action admin-media-action--danger" formnovalidate onclick="return confirm('¿Eliminar esta seccion de la plantilla?');">Eliminar</button>
                              </div>
                            </form>
                          </details>
                        <?php endforeach; ?>
                      </div>

                      <details class="admin-template-section-item admin-template-section-item--new">
                        <summary><span class="admin-template-section-order">+</span><span><strong>Añadir seccion</strong><small>Nuevo bloque reutilizable</small></span></summary>
                        <form method="post" class="admin-template-section-form" data-section-settings-form>
                          <input type="hidden" name="action" value="create_template_section" />
                          <input type="hidden" name="template_id" value="<?= $templateId ?>" />
                          <div class="admin-section-media-form-grid">
                            <label>Tipo
                              <select name="section_type" required data-section-type-select>
                                <?php foreach (adminSectionTypeOptions() as $sectionType => $sectionTypeLabel): ?>
                                  <option value="<?= adminEscape($sectionType) ?>"><?= adminEscape($sectionTypeLabel) ?></option>
                                <?php endforeach; ?>
                              </select>
                            </label>
                            <label>Section key<input type="text" name="section_key" placeholder="campaign" required /></label>
                          </div>
                          <div class="admin-section-media-form-grid">
                            <label>Eyebrow<input type="text" name="eyebrow" /></label>
                            <label>Titulo<input type="text" name="title" /></label>
                          </div>
                          <label>Contenido<textarea name="body"></textarea></label>
                          <?= adminRenderSectionVisualSettings('hero') ?>
                          <div class="admin-section-media-form-grid">
                            <label>Orden<input type="number" name="display_order" min="0" placeholder="Automatico" /></label>
                            <label class="admin-checkbox"><input type="checkbox" name="is_active" checked /> Activa</label>
                          </div>
                          <button type="submit" class="admin-link admin-link--button">Añadir seccion</button>
                        </form>
                      </details>
                    </section>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </section>

        <section class="admin-panel" id="secciones" data-admin-panel data-selected-detail-id="<?= adminEscape(str_starts_with($requestedDetailId, 'section-') ? $requestedDetailId : '') ?>">
          <div class="admin-master-detail" data-admin-master-detail>
            <div class="admin-master-detail-main">
              <div class="admin-page-heading">
                <div>
                  <h2>Secciones</h2>
                  <p>Compone cada vista de coleccion con bloques reutilizables y ordenables.</p>
                </div>
                <span class="admin-page-count"><?= count($sections) ?> registradas</span>
              </div>

              <div class="admin-panel-toolbar admin-panel-toolbar--sections">
                <label class="admin-panel-search">
                  <span>Buscar</span>
                  <input type="search" placeholder="Titulo, key, tipo o coleccion" data-admin-search />
                </label>
                <label class="admin-panel-filter">
                  <span>Coleccion</span>
                  <select data-admin-collection-filter>
                    <option value="all">Todas</option>
                    <?php foreach ($collections as $collection): ?>
                      <option value="<?= (int) $collection['id'] ?>"><?= adminEscape(adminCollectionLabel($collection)) ?></option>
                    <?php endforeach; ?>
                  </select>
                </label>
                <label class="admin-panel-filter">
                  <span>Estado</span>
                  <select data-admin-status-filter>
                    <option value="all">Todos</option>
                    <option value="active">Activas</option>
                    <option value="inactive">Inactivas</option>
                  </select>
                </label>
                <label class="admin-panel-filter admin-panel-filter--sort">
                  <span>Orden</span>
                  <select data-admin-sort>
                    <option value="order-asc">Ascendente</option>
                    <option value="order-desc">Descendente</option>
                    <option value="name-asc">A-Z</option>
                    <option value="name-desc">Z-A</option>
                  </select>
                </label>
                <a class="admin-toolbar-cta" href="#crear-contenido">Nueva seccion</a>
              </div>

              <div class="admin-record-list admin-list">
                <div class="admin-list-head admin-list-head--sections" aria-hidden="true">
                  <span>Seccion</span>
                  <span>Coleccion</span>
                  <span>Tipo</span>
                  <span>Orden</span>
                  <span>Estado</span>
                  <span>Acciones</span>
                </div>
                <?php foreach ($sections as $section): ?>
                  <?php $sectionTitle = adminFirstNonEmpty([(string) $section['title'], (string) $section['eyebrow'], (string) $section['section_key']], 'Seccion sin titulo'); ?>
                  <?php $sectionSummary = adminFirstNonEmpty([(string) $section['body'], (string) $section['section_key']], 'Sin contenido textual.'); ?>
                  <?php $sectionTypeLabel = adminSectionTypeLabel((string) $section['section_type']); ?>
                  <button type="button" class="admin-item admin-item--section" data-admin-item data-admin-detail-trigger data-detail-id="section-<?= (int) $section['id'] ?>" data-active="<?= (int) $section['is_active'] ?>" data-featured="0" data-order="<?= (int) $section['display_order'] ?>" data-name="<?= adminEscape(strtolower($sectionTitle . ' ' . $section['section_key'])) ?>" data-collection-id="<?= (int) $section['collection_id'] ?>" data-collection-name="<?= adminEscape(strtolower((string) $section['collection_name'])) ?>" aria-controls="detail-section-<?= (int) $section['id'] ?>">
                    <span class="admin-item-main">
                      <span class="admin-item-media is-empty"><span class="admin-section-type-mark"><?= adminEscape(strtoupper(substr((string) $section['section_type'], 0, 2))) ?></span></span>
                      <span class="admin-item-copy">
                        <span class="admin-item-kicker"><?= adminEscape((string) $section['section_key']) ?></span>
                        <span class="admin-item-title"><?= adminEscape($sectionTitle) ?></span>
                        <span class="admin-item-summary"><?= adminEscape($sectionSummary) ?></span>
                      </span>
                    </span>
                    <span class="admin-item-cell"><?= adminEscape((string) $section['collection_name']) ?></span>
                    <span class="admin-item-cell"><?= adminEscape($sectionTypeLabel) ?></span>
                    <span class="admin-item-cell admin-item-cell--order"><?= (int) $section['display_order'] ?></span>
                    <span class="admin-item-cell"><span class="admin-status-badge"><?= (int) $section['is_active'] === 1 ? 'Activa' : 'Inactiva' ?></span></span>
                    <span class="admin-item-cell admin-item-cell--action"><span class="admin-item-link">Ver detalle</span><span class="admin-item-chevron" aria-hidden="true"></span></span>
                  </button>
                <?php endforeach; ?>
                <p class="admin-no-results" data-admin-no-results hidden>No hay secciones con ese filtro.</p>
                <p class="admin-list-foot">Mostrando <?= count($sections) ?> secciones</p>
              </div>
            </div>

            <div class="admin-detail-column">
              <p class="admin-detail-empty" data-admin-empty-detail hidden>Selecciona una seccion visible para ver sus detalles.</p>
              <?php foreach ($sections as $section): ?>
                <?php $sectionTitle = adminFirstNonEmpty([(string) $section['title'], (string) $section['eyebrow'], (string) $section['section_key']], 'Seccion sin titulo'); ?>
                <?php $sectionSummary = adminFirstNonEmpty([(string) $section['body'], (string) $section['section_key']], 'Sin contenido textual.'); ?>
                <?php $sectionTypeLabel = adminSectionTypeLabel((string) $section['section_type']); ?>
                <?php $sectionMediaKey = (int) $section['collection_id'] . ':' . (string) $section['section_key']; ?>
                <?php $sectionMediaItems = $mediaBySection[$sectionMediaKey] ?? []; ?>
                <?php $availableSectionMedia = $unassignedMediaByCollection[(int) $section['collection_id']] ?? []; ?>
                <div class="admin-detail-panel" data-admin-detail-panel data-detail-id="section-<?= (int) $section['id'] ?>" id="detail-section-<?= (int) $section['id'] ?>" hidden>
                  <div class="admin-detail-card">
                    <div class="admin-detail-topbar">
                      <p>Detalle de seccion</p>
                      <button type="button" class="admin-detail-close" data-admin-close-detail aria-label="Cerrar detalle">x</button>
                    </div>
                    <div class="admin-detail-hero admin-detail-hero--section">
                      <span><?= adminEscape($sectionTypeLabel) ?></span>
                    </div>
                    <div class="admin-edit-intro">
                      <h3>Editar seccion</h3>
                      <p>Cambia el componente, el contenido y su posicion dentro de la coleccion.</p>
                    </div>
                    <div class="admin-edit-meta">
                      <span class="admin-status-badge"><?= (int) $section['is_active'] === 1 ? 'Activa' : 'Inactiva' ?></span>
                      <span class="admin-edit-chip">Orden <?= (int) $section['display_order'] ?></span>
                      <span class="admin-edit-chip"><?= count($sectionMediaItems) ?> recursos</span>
                    </div>
                    <div class="admin-detail-copy">
                      <div class="admin-detail-readonly">
                        <div><span>Titulo</span><strong><?= adminEscape($sectionTitle) ?></strong></div>
                        <div><span>Section key</span><strong><?= adminEscape((string) $section['section_key']) ?></strong></div>
                        <div><span>Tipo</span><strong><?= adminEscape($sectionTypeLabel) ?></strong></div>
                        <div><span>Coleccion</span><strong><?= adminEscape((string) $section['collection_name']) ?></strong></div>
                        <div><span>Orden</span><strong><?= (int) $section['display_order'] ?></strong></div>
                        <div><span>Multimedia</span><strong><?= count($sectionMediaItems) ?> vinculada<?= count($sectionMediaItems) === 1 ? '' : 's' ?></strong></div>
                        <div><span>Contenido</span><strong><?= adminEscape($sectionSummary) ?></strong></div>
                        <div><span>Settings</span><strong><?= adminEscape(adminFormatJsonForTextarea($section['settings_json']) ?: 'Sin ajustes') ?></strong></div>
                        <div><span>Fecha de actualizacion</span><strong><?= adminEscape(adminFormatDateLabel((string) $section['updated_at'])) ?></strong></div>
                      </div>
                    </div>
                    <div class="admin-detail-actions-label">Acciones rapidas</div>
                    <div class="admin-detail-actions-quick">
                      <button type="button" class="admin-link admin-link--button" data-admin-edit-toggle>Editar</button>
                      <form method="post" class="admin-inline-form">
                        <input type="hidden" name="action" value="move_section_up" />
                        <input type="hidden" name="id" value="<?= (int) $section['id'] ?>" />
                        <button type="submit" class="admin-link admin-link--move">Subir</button>
                      </form>
                      <form method="post" class="admin-inline-form">
                        <input type="hidden" name="action" value="move_section_down" />
                        <input type="hidden" name="id" value="<?= (int) $section['id'] ?>" />
                        <button type="submit" class="admin-link admin-link--move">Bajar</button>
                      </form>
                    </div>

                    <form method="post" class="admin-detail-editor" data-admin-editable data-section-settings-form hidden>
                      <input type="hidden" name="action" value="update_section" />
                      <input type="hidden" name="id" value="<?= (int) $section['id'] ?>" />
                      <div class="admin-detail-editor-row admin-detail-editor-row--split">
                        <label class="admin-detail-field">Coleccion
                          <select name="collection_id" required>
                            <?php foreach ($collections as $collection): ?>
                              <option value="<?= (int) $collection['id'] ?>" <?= adminSelected($section['collection_id'], $collection['id']) ?>><?= adminEscape(adminCollectionLabel($collection)) ?></option>
                            <?php endforeach; ?>
                          </select>
                        </label>
                        <label class="admin-detail-field">Tipo de seccion
                          <select name="section_type" required data-section-type-select>
                            <?php foreach (adminSectionTypeOptions() as $sectionType => $sectionTypeOptionLabel): ?>
                              <option value="<?= adminEscape($sectionType) ?>" <?= adminSelected($section['section_type'], $sectionType) ?>><?= adminEscape($sectionTypeOptionLabel) ?></option>
                            <?php endforeach; ?>
                          </select>
                        </label>
                      </div>
                      <div class="admin-detail-editor-row admin-detail-editor-row--split">
                        <label class="admin-detail-field">Section key<input type="text" name="section_key" value="<?= adminEscape($section['section_key']) ?>" required /></label>
                        <label class="admin-detail-field admin-detail-field--compact">Orden<input type="number" name="display_order" value="<?= (int) $section['display_order'] ?>" min="0" /></label>
                      </div>
                      <div class="admin-detail-editor-row admin-detail-editor-row--split">
                        <label class="admin-detail-field">Eyebrow<input type="text" name="eyebrow" value="<?= adminEscape($section['eyebrow']) ?>" /></label>
                        <label class="admin-detail-field">Titulo<input type="text" name="title" value="<?= adminEscape($section['title']) ?>" /></label>
                      </div>
                      <div class="admin-detail-editor-row">
                        <label class="admin-detail-field">Contenido<textarea name="body"><?= adminEscape($section['body']) ?></textarea></label>
                      </div>
                      <div class="admin-detail-editor-row">
                        <?= adminRenderSectionVisualSettings((string) $section['section_type'], $section['settings_json']) ?>
                      </div>
                      <div class="admin-detail-editor-row admin-detail-editor-row--split">
                        <label class="admin-checkbox"><input type="checkbox" name="is_active" <?= adminChecked($section['is_active']) ?> /> Activa</label>
                      </div>
                      <div class="admin-detail-editor-row admin-actions admin-actions--editor">
                        <button type="submit" class="admin-button admin-button--primary">Guardar cambios</button>
                        <button type="button" class="admin-button admin-button--secondary" data-admin-cancel-edit>Cancelar</button>
                      </div>
                      <div class="admin-detail-editor-row">
                        <button type="submit" name="submit_action" value="delete_section" class="admin-button admin-button--danger admin-button--text" formnovalidate onclick="return confirm('La seccion se eliminara y sus recursos quedaran sin seccion. Continuar?');">Eliminar seccion</button>
                      </div>
                    </form>

                    <section class="admin-section-media-manager" data-section-media-manager>
                      <div class="admin-section-media-heading">
                        <div>
                          <p class="admin-eyebrow">Multimedia vinculada</p>
                          <h3><?= adminEscape($sectionTitle) ?></h3>
                        </div>
                        <span class="admin-section-media-count"><?= count($sectionMediaItems) ?> recursos</span>
                      </div>

                      <?php if ($sectionMediaItems !== []): ?>
                        <div class="admin-section-media-list">
                          <?php foreach ($sectionMediaItems as $sectionMediaIndex => $sectionMedia): ?>
                            <?php $sectionMediaPreview = adminRecordPreviewImage($sectionMedia, ['thumbnail_url', 'file_url']); ?>
                            <article class="admin-section-media-item<?= (int) $sectionMedia['is_active'] === 1 ? '' : ' is-inactive' ?>">
                              <div class="admin-section-media-preview<?= $sectionMediaPreview === '' ? ' is-empty' : '' ?>">
                                <?php if ($sectionMediaPreview !== ''): ?>
                                  <img src="<?= adminEscape($sectionMediaPreview) ?>" alt="<?= adminEscape((string) ($sectionMedia['alt_text'] ?: $sectionMedia['title'] ?: 'Imagen de la seccion')) ?>" loading="lazy" />
                                <?php else: ?>
                                  <span><?= adminEscape(strtoupper(substr((string) $sectionMedia['media_type'], 0, 1))) ?></span>
                                <?php endif; ?>
                              </div>
                              <div class="admin-section-media-copy">
                                <span><?= (int) $sectionMedia['display_order'] ?> · <?= (int) $sectionMedia['is_active'] === 1 ? 'Visible' : 'Oculta' ?></span>
                                <strong><?= adminEscape((string) ($sectionMedia['title'] ?: basename((string) $sectionMedia['file_url']))) ?></strong>
                                <small><?= adminEscape((string) ($sectionMedia['alt_text'] ?: $sectionMedia['caption'] ?: $sectionMedia['file_url'])) ?></small>
                              </div>
                              <div class="admin-section-media-actions">
                                <button type="button" class="admin-media-action" data-admin-open-media data-media-detail-id="media-<?= (int) $sectionMedia['id'] ?>">Editar</button>
                                <form method="post" class="admin-inline-form">
                                  <input type="hidden" name="action" value="move_section_media_up" />
                                  <input type="hidden" name="section_id" value="<?= (int) $section['id'] ?>" />
                                  <input type="hidden" name="media_id" value="<?= (int) $sectionMedia['id'] ?>" />
                                  <button type="submit" class="admin-media-action" <?= $sectionMediaIndex === 0 ? 'disabled' : '' ?>>Subir</button>
                                </form>
                                <form method="post" class="admin-inline-form">
                                  <input type="hidden" name="action" value="move_section_media_down" />
                                  <input type="hidden" name="section_id" value="<?= (int) $section['id'] ?>" />
                                  <input type="hidden" name="media_id" value="<?= (int) $sectionMedia['id'] ?>" />
                                  <button type="submit" class="admin-media-action" <?= $sectionMediaIndex === count($sectionMediaItems) - 1 ? 'disabled' : '' ?>>Bajar</button>
                                </form>
                                <form method="post" class="admin-inline-form">
                                  <input type="hidden" name="action" value="toggle_section_media" />
                                  <input type="hidden" name="section_id" value="<?= (int) $section['id'] ?>" />
                                  <input type="hidden" name="media_id" value="<?= (int) $sectionMedia['id'] ?>" />
                                  <button type="submit" class="admin-media-action"><?= (int) $sectionMedia['is_active'] === 1 ? 'Ocultar' : 'Mostrar' ?></button>
                                </form>
                                <form method="post" class="admin-inline-form">
                                  <input type="hidden" name="action" value="detach_section_media" />
                                  <input type="hidden" name="section_id" value="<?= (int) $section['id'] ?>" />
                                  <input type="hidden" name="media_id" value="<?= (int) $sectionMedia['id'] ?>" />
                                  <button type="submit" class="admin-media-action admin-media-action--muted">Desvincular</button>
                                </form>
                                <form method="post" class="admin-inline-form">
                                  <input type="hidden" name="action" value="delete_section_media" />
                                  <input type="hidden" name="section_id" value="<?= (int) $section['id'] ?>" />
                                  <input type="hidden" name="media_id" value="<?= (int) $sectionMedia['id'] ?>" />
                                  <button type="submit" class="admin-media-action admin-media-action--danger" onclick="return confirm('Se eliminara este recurso de la base de datos. Continuar?');">Eliminar</button>
                                </form>
                              </div>
                            </article>
                          <?php endforeach; ?>
                        </div>
                      <?php else: ?>
                        <p class="admin-section-media-empty">Esta seccion aun no tiene recursos. Sube una imagen o vincula una ya existente.</p>
                      <?php endif; ?>

                      <div class="admin-section-media-forms">
                        <form method="post" enctype="multipart/form-data" class="admin-section-media-form">
                          <input type="hidden" name="action" value="create_section_media" />
                          <input type="hidden" name="section_id" value="<?= (int) $section['id'] ?>" />
                          <input type="hidden" name="media_type" value="image" />
                          <div class="admin-section-media-form-heading">
                            <strong>Anadir imagen</strong>
                            <span>Se guardara directamente en <?= adminEscape((string) $section['section_key']) ?>.</span>
                          </div>
                          <label>Archivo
                            <input type="file" name="media_file" accept="image/jpeg,image/png,image/webp,image/avif,image/gif" />
                          </label>
                          <label>O URL / ruta
                            <input type="text" name="file_url" placeholder="./assets/images/campaign-01.jpg" />
                          </label>
                          <div class="admin-section-media-form-grid">
                            <label>Titulo<input type="text" name="title" /></label>
                            <label>Texto alternativo<input type="text" name="alt_text" /></label>
                          </div>
                          <label>Pie de foto<textarea name="caption"></textarea></label>
                          <label class="admin-checkbox"><input type="checkbox" name="is_active" checked /> Visible en la web</label>
                          <button type="submit" class="admin-button admin-button--primary">Subir y vincular</button>
                          <p class="admin-upload-hint">JPG, PNG, WEBP, AVIF o GIF. Maximo 15 MB.</p>
                        </form>

                        <form method="post" class="admin-section-media-form admin-section-media-form--existing">
                          <input type="hidden" name="action" value="attach_section_media" />
                          <input type="hidden" name="section_id" value="<?= (int) $section['id'] ?>" />
                          <div class="admin-section-media-form-heading">
                            <strong>Vincular existente</strong>
                            <span>Solo aparecen recursos generales sin seccion de esta coleccion.</span>
                          </div>
                          <label>Recurso disponible
                            <select name="media_id" <?= $availableSectionMedia === [] ? 'disabled' : 'required' ?>>
                              <?php if ($availableSectionMedia === []): ?>
                                <option value="">No hay recursos disponibles</option>
                              <?php else: ?>
                                <?php foreach ($availableSectionMedia as $availableMedia): ?>
                                  <option value="<?= (int) $availableMedia['id'] ?>"><?= adminEscape((string) ($availableMedia['title'] ?: basename((string) $availableMedia['file_url']))) ?></option>
                                <?php endforeach; ?>
                              <?php endif; ?>
                            </select>
                          </label>
                          <button type="submit" class="admin-button admin-button--secondary" <?= $availableSectionMedia === [] ? 'disabled' : '' ?>>Vincular recurso</button>
                        </form>
                      </div>
                    </section>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </section>

        <section class="admin-panel" id="piezas" data-admin-panel>
          <div class="admin-master-detail" data-admin-master-detail>
            <div class="admin-master-detail-main">
              <div class="admin-page-heading">
                <div>
                  <h2>Piezas</h2>
                  <p>Controla las piezas individuales de cada coleccion.</p>
                </div>
                <span class="admin-page-count"><?= count($pieces) ?> registradas</span>
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
                <label class="admin-panel-filter admin-panel-filter--sort">
                  <span>Orden</span>
                  <select data-admin-sort>
                    <option value="order-asc">Ascendente</option>
                    <option value="order-desc">Descendente</option>
                    <option value="name-asc">A-Z</option>
                    <option value="name-desc">Z-A</option>
                  </select>
                </label>
                <a class="admin-toolbar-cta" href="#crear-contenido">Nueva pieza</a>
              </div>

              <div class="admin-record-list admin-list">
                <div class="admin-list-head admin-list-head--wide" aria-hidden="true">
                  <span>Pieza</span>
                  <span>Slug</span>
                  <span>Tipo</span>
                  <span>Estado</span>
                  <span>Acciones</span>
                </div>
                <?php foreach ($pieces as $piece): ?>
                  <?php $pieceSummary = adminFirstNonEmpty([(string) $piece['short_description'], (string) $piece['subtitle'], (string) $piece['description']], 'Sin resumen disponible.'); ?>
                  <?php $piecePreview = adminRecordPreviewImage($piece, ['cover_image']); ?>
                  <button type="button" class="admin-item admin-item--table" data-admin-item data-admin-detail-trigger data-detail-id="piece-<?= (int) $piece['id'] ?>" data-active="<?= (int) $piece['is_active'] ?>" data-featured="<?= (int) $piece['is_featured'] ?>" data-order="<?= (int) $piece['display_order'] ?>" data-name="<?= adminEscape(strtolower((string) $piece['name'])) ?>" aria-controls="detail-piece-<?= (int) $piece['id'] ?>">
                    <span class="admin-item-main">
                      <span class="admin-item-media<?= $piecePreview === '' ? ' is-empty' : '' ?>">
                        <?php if ($piecePreview !== ''): ?>
                          <img src="<?= adminEscape($piecePreview) ?>" alt="<?= adminEscape($piece['name']) ?>" loading="lazy" />
                        <?php else: ?>
                          <span><?= adminEscape(strtoupper(substr((string) $piece['name'], 0, 1))) ?></span>
                        <?php endif; ?>
                      </span>
                      <span class="admin-item-copy">
                        <span class="admin-item-kicker">Pieza · <?= adminEscape((string) ($piece['collection_name'] ?? 'Sin coleccion')) ?></span>
                        <span class="admin-item-title"><?= adminEscape($piece['name']) ?></span>
                        <span class="admin-item-summary"><?= adminEscape($pieceSummary) ?></span>
                      </span>
                    </span>
                    <span class="admin-item-cell"><?= adminEscape($piece['slug']) ?></span>
                    <span class="admin-item-cell"><?= adminEscape($piece['piece_type']) ?></span>
                    <span class="admin-item-cell"><span class="admin-status-badge"><?= (int) $piece['is_active'] === 1 ? 'Activa' : 'Inactiva' ?></span></span>
                    <span class="admin-item-cell admin-item-cell--action"><span class="admin-item-link">Ver detalle</span><span class="admin-item-chevron" aria-hidden="true"></span></span>
                  </button>
                <?php endforeach; ?>
                <p class="admin-no-results" data-admin-no-results hidden>No hay resultados con ese filtro.</p>
                <p class="admin-list-foot">Mostrando 1 a <?= count($pieces) ?> de <?= count($pieces) ?> piezas</p>
              </div>
            </div>

            <div class="admin-detail-column">
              <p class="admin-detail-empty" data-admin-empty-detail hidden>Selecciona una pieza visible para ver sus detalles.</p>
              <?php foreach ($pieces as $piece): ?>
                <?php $pieceSummary = adminFirstNonEmpty([(string) $piece['short_description'], (string) $piece['subtitle'], (string) $piece['description']], 'Sin resumen disponible.'); ?>
                <?php $piecePreview = adminRecordPreviewImage($piece, ['cover_image']); ?>
                <form method="post" class="admin-detail-panel" data-admin-detail-panel data-detail-id="piece-<?= (int) $piece['id'] ?>" id="detail-piece-<?= (int) $piece['id'] ?>" hidden>
                  <input type="hidden" name="action" value="update_piece" />
                  <input type="hidden" name="id" value="<?= (int) $piece['id'] ?>" />
                  <div class="admin-detail-card">
                    <div class="admin-detail-topbar">
                      <p>Detalle de pieza</p>
                      <button type="button" class="admin-detail-close" data-admin-close-detail aria-label="Cerrar detalle">x</button>
                    </div>
                    <div class="admin-detail-hero<?= $piecePreview === '' ? ' is-empty' : '' ?>">
                      <?php if ($piecePreview !== ''): ?>
                        <img src="<?= adminEscape($piecePreview) ?>" alt="<?= adminEscape($piece['name']) ?>" loading="lazy" />
                      <?php else: ?>
                        <span><?= adminEscape($piece['piece_type']) ?></span>
                      <?php endif; ?>
                    </div>
                    <div class="admin-edit-intro">
                      <h3>Editar pieza</h3>
                      <p>Actualiza la informacion principal y los recursos de esta pieza.</p>
                    </div>
                    <div class="admin-edit-meta">
                      <span class="admin-status-badge"><?= (int) $piece['is_active'] === 1 ? 'Activa' : 'Inactiva' ?></span>
                      <span class="admin-edit-chip">Orden <?= (int) $piece['display_order'] ?></span>
                    </div>
                    <div class="admin-detail-copy">
                      <div class="admin-detail-readonly">
                        <div><span>Nombre</span><strong><?= adminEscape($piece['name']) ?></strong></div>
                        <div><span>Slug</span><strong><?= adminEscape($piece['slug']) ?></strong></div>
                        <div><span>Tipo</span><strong><?= adminEscape($piece['piece_type']) ?></strong></div>
                        <div><span>Coleccion</span><strong><?= adminEscape((string) ($piece['collection_name'] ?? 'Sin coleccion')) ?></strong></div>
                        <div><span>Resumen / descripcion</span><strong><?= adminEscape($pieceSummary) ?></strong></div>
                        <div><span>Fecha de actualizacion</span><strong><?= adminEscape(adminFormatDateLabel((string) $piece['updated_at'])) ?></strong></div>
                      </div>
                    </div>
                    <div class="admin-detail-actions-label">Acciones rapidas</div>
                    <div class="admin-detail-actions-quick">
                      <button type="button" class="admin-link admin-link--button" data-admin-edit-toggle>Editar</button>
                      <button type="submit" name="submit_action" value="duplicate_piece" class="admin-link admin-link--ghost" formnovalidate>Duplicar</button>
                      <button type="button" class="admin-link admin-link--ghost" disabled>Archivar</button>
                    </div>
                    <div class="admin-detail-editor" data-admin-editable hidden>
                      <div class="admin-detail-editor-row admin-detail-editor-row--split">
                        <label class="admin-detail-field">Coleccion
                          <select name="collection_id" required>
                            <?php foreach ($collections as $collection): ?>
                              <option value="<?= (int) $collection['id'] ?>" <?= adminSelected($piece['collection_id'], $collection['id']) ?>><?= adminEscape(adminCollectionLabel($collection)) ?></option>
                            <?php endforeach; ?>
                          </select>
                        </label>
                        <label class="admin-detail-field">Nombre<input type="text" name="name" value="<?= adminEscape($piece['name']) ?>" required /></label>
                      </div>
                      <div class="admin-detail-editor-row admin-detail-editor-row--split">
                        <label class="admin-detail-field">Slug<input type="text" name="slug" value="<?= adminEscape($piece['slug']) ?>" required /></label>
                        <label class="admin-detail-field">Piece type<input type="text" name="piece_type" value="<?= adminEscape($piece['piece_type']) ?>" required /></label>
                      </div>
                      <div class="admin-detail-editor-row admin-detail-editor-row--split">
                        <label class="admin-detail-field">Subtitle<input type="text" name="subtitle" value="<?= adminEscape($piece['subtitle']) ?>" /></label>
                        <label class="admin-detail-field admin-detail-field--compact">Orden<input type="number" name="display_order" value="<?= (int) $piece['display_order'] ?>" min="0" /></label>
                      </div>
                      <div class="admin-detail-editor-row">
                        <label class="admin-detail-field">Resumen<input type="text" name="short_description" value="<?= adminEscape($piece['short_description']) ?>" /></label>
                      </div>
                      <div class="admin-detail-editor-row">
                        <label class="admin-detail-field">Descripcion<textarea name="description"><?= adminEscape($piece['description']) ?></textarea></label>
                      </div>
                      <div class="admin-detail-editor-row admin-detail-editor-row--media">
                        <label class="admin-asset-field admin-detail-field" data-admin-image-field>
                          <span>Cover image</span>
                          <span class="admin-asset-preview admin-asset-preview--wide<?= $piece['cover_image'] === '' ? ' is-empty' : '' ?>" data-admin-image-preview>
                            <img src="<?= adminEscape(adminRecordPreviewImage($piece, ['cover_image'])) ?>" alt="Preview cover image" data-admin-image-tag<?= $piece['cover_image'] === '' ? ' hidden' : '' ?> loading="lazy" />
                            <span class="admin-asset-placeholder"<?= $piece['cover_image'] !== '' ? ' hidden' : '' ?>>Sin imagen</span>
                          </span>
                          <span class="admin-asset-actions">
                            <button type="button" class="admin-asset-action" data-admin-image-browse>Cambiar</button>
                            <button type="button" class="admin-asset-action admin-asset-action--icon" data-admin-image-clear aria-label="Limpiar cover image">x</button>
                          </span>
                          <input type="text" name="cover_image" value="<?= adminEscape($piece['cover_image']) ?>" data-admin-image-input />
                        </label>
                      </div>
                      <div class="admin-detail-editor-row admin-detail-editor-row--split">
                        <label class="admin-checkbox"><input type="checkbox" name="is_featured" <?= adminChecked($piece['is_featured']) ?> /> Destacada</label>
                        <label class="admin-checkbox"><input type="checkbox" name="is_active" <?= adminChecked($piece['is_active']) ?> /> Activa</label>
                      </div>
                      <div class="admin-detail-editor-row admin-actions admin-actions--editor">
                        <button type="submit" class="admin-button admin-button--primary">Guardar cambios</button>
                        <button type="button" class="admin-button admin-button--secondary" data-admin-cancel-edit>Cancelar</button>
                      </div>
                      <div class="admin-detail-editor-row">
                        <button type="submit" name="submit_action" value="delete_piece" class="admin-button admin-button--danger admin-button--text" formnovalidate onclick="return confirm('Se eliminara esta pieza. Continuar?');">Eliminar pieza</button>
                      </div>
                    </div>
                  </div>
                </form>
              <?php endforeach; ?>
            </div>
          </div>
        </section>

        <section class="admin-panel" id="multimedia" data-admin-panel data-selected-detail-id="<?= adminEscape(str_starts_with($requestedDetailId, 'media-') ? $requestedDetailId : '') ?>">
          <div class="admin-master-detail" data-admin-master-detail>
            <div class="admin-master-detail-main">
              <div class="admin-page-heading">
                <div>
                  <h2>Multimedia</h2>
                  <p>Relaciona imagenes y recursos con colecciones y piezas.</p>
                </div>
                <span class="admin-page-count"><?= count($mediaItems) ?> registradas</span>
              </div>

              <div class="admin-panel-toolbar">
                <label class="admin-panel-search">
                  <span>Buscar</span>
                  <input type="search" placeholder="Titulo, ruta o coleccion" data-admin-search />
                </label>
                <label class="admin-panel-filter">
                  <span>Coleccion</span>
                  <select data-admin-collection-filter>
                    <option value="all">Todas</option>
                    <?php foreach ($collections as $collection): ?>
                      <option value="<?= (int) $collection['id'] ?>"><?= adminEscape(adminCollectionLabel($collection)) ?></option>
                    <?php endforeach; ?>
                  </select>
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
                <label class="admin-panel-filter admin-panel-filter--sort">
                  <span>Orden</span>
                  <select data-admin-sort>
                    <option value="order-asc">Ascendente</option>
                    <option value="order-desc">Descendente</option>
                    <option value="name-asc">A-Z</option>
                    <option value="name-desc">Z-A</option>
                  </select>
                </label>
                <a class="admin-toolbar-cta" href="#crear-contenido">Nuevo recurso</a>
              </div>

              <div class="admin-record-list admin-list">
                <div class="admin-list-head admin-list-head--wide" aria-hidden="true">
                  <span>Recurso</span>
                  <span>Tipo</span>
                  <span>Scope</span>
                  <span>Estado</span>
                  <span>Acciones</span>
                </div>
                <?php foreach ($mediaItems as $media): ?>
                  <?php $mediaSummary = adminFirstNonEmpty([(string) $media['caption'], (string) $media['alt_text'], (string) $media['file_url']], 'Sin resumen disponible.'); ?>
                  <?php $mediaPreview = adminRecordPreviewImage($media, ['thumbnail_url', 'file_url']); ?>
                  <button type="button" class="admin-item admin-item--table" data-admin-item data-admin-detail-trigger data-detail-id="media-<?= (int) $media['id'] ?>" data-active="<?= (int) $media['is_active'] ?>" data-featured="<?= (int) $media['is_cover'] ?>" data-order="<?= (int) $media['display_order'] ?>" data-name="<?= adminEscape(strtolower((string) ($media['title'] ?: $media['file_url']))) ?>" data-collection-id="<?= (int) $media['collection_id'] ?>" data-collection-name="<?= adminEscape(strtolower((string) $media['collection_name'])) ?>" aria-controls="detail-media-<?= (int) $media['id'] ?>">
                    <span class="admin-item-main">
                      <span class="admin-item-media<?= $mediaPreview === '' ? ' is-empty' : '' ?>">
                        <?php if ($mediaPreview !== ''): ?>
                          <img src="<?= adminEscape($mediaPreview) ?>" alt="<?= adminEscape((string) ($media['title'] ?: $media['alt_text'] ?: 'Multimedia')) ?>" loading="lazy" />
                        <?php else: ?>
                          <span><?= adminEscape(strtoupper(substr((string) ($media['media_type'] ?: 'M'), 0, 1))) ?></span>
                        <?php endif; ?>
                      </span>
                      <span class="admin-item-copy">
                        <span class="admin-item-kicker">Multimedia · <?= adminEscape((string) ($media['collection_name'] ?? 'Sin coleccion')) ?></span>
                        <span class="admin-item-title"><?= adminEscape((string) ($media['title'] ?: $media['file_url'])) ?></span>
                        <span class="admin-item-summary"><?= adminEscape($mediaSummary) ?></span>
                      </span>
                    </span>
                    <span class="admin-item-cell"><?= adminEscape($media['media_type']) ?></span>
                    <span class="admin-item-cell"><?= adminEscape((string) ($media['piece_name'] ?? ($media['section_title'] ? 'Seccion · ' . $media['section_title'] : 'Coleccion'))) ?></span>
                    <span class="admin-item-cell"><span class="admin-status-badge"><?= (int) $media['is_active'] === 1 ? 'Activa' : 'Inactiva' ?></span></span>
                    <span class="admin-item-cell admin-item-cell--action"><span class="admin-item-link">Ver detalle</span><span class="admin-item-chevron" aria-hidden="true"></span></span>
                  </button>
                <?php endforeach; ?>
                <p class="admin-no-results" data-admin-no-results hidden>No hay resultados con ese filtro.</p>
                <p class="admin-list-foot">Mostrando 1 a <?= count($mediaItems) ?> de <?= count($mediaItems) ?> recursos</p>
              </div>
            </div>

            <div class="admin-detail-column">
              <p class="admin-detail-empty" data-admin-empty-detail hidden>Selecciona un recurso visible para ver sus detalles.</p>
              <?php foreach ($mediaItems as $media): ?>
                <?php $mediaSummary = adminFirstNonEmpty([(string) $media['caption'], (string) $media['alt_text'], (string) $media['file_url']], 'Sin resumen disponible.'); ?>
                <?php $mediaPreview = adminRecordPreviewImage($media, ['file_url', 'thumbnail_url']); ?>
                <form method="post" enctype="multipart/form-data" class="admin-detail-panel" data-admin-detail-panel data-detail-id="media-<?= (int) $media['id'] ?>" id="detail-media-<?= (int) $media['id'] ?>" hidden>
                  <input type="hidden" name="action" value="update_media" />
                  <input type="hidden" name="id" value="<?= (int) $media['id'] ?>" />
                  <div class="admin-detail-card">
                    <div class="admin-detail-topbar">
                      <p>Detalle de multimedia</p>
                      <button type="button" class="admin-detail-close" data-admin-close-detail aria-label="Cerrar detalle">x</button>
                    </div>
                    <div class="admin-detail-hero<?= $mediaPreview === '' ? ' is-empty' : '' ?>">
                      <?php if ($mediaPreview !== ''): ?>
                        <img src="<?= adminEscape($mediaPreview) ?>" alt="<?= adminEscape((string) ($media['title'] ?: $media['alt_text'] ?: 'Multimedia')) ?>" loading="lazy" />
                      <?php else: ?>
                        <span><?= adminEscape($media['media_type']) ?></span>
                      <?php endif; ?>
                    </div>
                    <div class="admin-edit-intro">
                      <h3>Editar recurso</h3>
                      <p>Actualiza el archivo, el contexto y los metadatos de este recurso.</p>
                    </div>
                    <div class="admin-edit-meta">
                      <span class="admin-status-badge"><?= (int) $media['is_active'] === 1 ? 'Activa' : 'Inactiva' ?></span>
                      <span class="admin-edit-chip">Orden <?= (int) $media['display_order'] ?></span>
                    </div>
                    <div class="admin-detail-copy">
                      <div class="admin-detail-readonly">
                        <div><span>Titulo</span><strong><?= adminEscape((string) ($media['title'] ?: $media['file_url'])) ?></strong></div>
                        <div><span>Tipo</span><strong><?= adminEscape($media['media_type']) ?></strong></div>
                        <div><span>Coleccion</span><strong><?= adminEscape((string) ($media['collection_name'] ?? 'Sin coleccion')) ?></strong></div>
                        <div><span>Scope</span><strong><?= adminEscape((string) ($media['piece_name'] ?? 'Coleccion')) ?></strong></div>
                        <div><span>Seccion</span><strong><?= adminEscape((string) ($media['section_title'] ?: $media['section_key'] ?: 'Sin seccion')) ?></strong></div>
                        <div><span>Resumen / descripcion</span><strong><?= adminEscape($mediaSummary) ?></strong></div>
                        <div><span>Fecha de actualizacion</span><strong><?= adminEscape(adminFormatDateLabel((string) $media['updated_at'])) ?></strong></div>
                      </div>
                    </div>
                    <div class="admin-detail-actions-label">Acciones rapidas</div>
                    <div class="admin-detail-actions-quick">
                      <button type="button" class="admin-link admin-link--button" data-admin-edit-toggle>Editar</button>
                      <button type="submit" name="submit_action" value="duplicate_media" class="admin-link admin-link--ghost" formnovalidate>Duplicar</button>
                      <button type="button" class="admin-link admin-link--ghost" disabled>Archivar</button>
                    </div>
                    <div class="admin-detail-editor" data-admin-editable hidden>
                      <div class="admin-detail-editor-row admin-detail-editor-row--split">
                        <label class="admin-detail-field">Coleccion
                          <select name="collection_id" required data-admin-media-collection>
                            <?php foreach ($collections as $collection): ?>
                              <option value="<?= (int) $collection['id'] ?>" <?= adminSelected($media['collection_id'], $collection['id']) ?>><?= adminEscape(adminCollectionLabel($collection)) ?></option>
                            <?php endforeach; ?>
                          </select>
                        </label>
                        <label class="admin-detail-field">Pieza opcional
                          <select name="piece_id" data-admin-piece-select>
                            <option value="">Multimedia general de la coleccion</option>
                            <?php foreach ($pieces as $piece): ?>
                              <option value="<?= (int) $piece['id'] ?>" data-collection-id="<?= (int) $piece['collection_id'] ?>" <?= adminSelected($media['piece_id'], $piece['id']) ?>><?= adminEscape(adminPieceLabel($piece)) ?></option>
                            <?php endforeach; ?>
                          </select>
                        </label>
                      </div>
                      <div class="admin-detail-editor-row admin-detail-editor-row--split">
                        <label class="admin-detail-field">Media type<input type="text" name="media_type" value="<?= adminEscape($media['media_type']) ?>" required /></label>
                        <label class="admin-detail-field">Titulo<input type="text" name="title" value="<?= adminEscape($media['title']) ?>" /></label>
                      </div>
                      <div class="admin-detail-editor-row admin-detail-editor-row--split">
                        <label class="admin-detail-field">Alt text<input type="text" name="alt_text" value="<?= adminEscape($media['alt_text']) ?>" /></label>
                        <label class="admin-detail-field">Seccion
                          <select name="section_key" data-admin-section-select>
                            <option value="">Sin seccion</option>
                            <?php foreach ($sections as $section): ?>
                              <option value="<?= adminEscape((string) $section['section_key']) ?>" data-collection-id="<?= (int) $section['collection_id'] ?>" <?= ((int) $media['collection_id'] === (int) $section['collection_id'] && (string) $media['section_key'] === (string) $section['section_key']) ? 'selected' : '' ?>><?= adminEscape((string) ($section['title'] ?: $section['section_key'])) ?></option>
                            <?php endforeach; ?>
                          </select>
                        </label>
                        <label class="admin-detail-field admin-detail-field--compact">Orden<input type="number" name="display_order" value="<?= (int) $media['display_order'] ?>" min="0" /></label>
                      </div>
                      <div class="admin-detail-editor-row">
                        <label class="admin-detail-field">Reemplazar archivo
                          <input type="file" name="media_file" accept="image/jpeg,image/png,image/webp,image/avif,image/gif" />
                        </label>
                        <p class="admin-upload-hint">Opcional. Si eliges un archivo nuevo, sustituira la ruta principal al guardar.</p>
                      </div>
                      <div class="admin-detail-editor-row admin-detail-editor-row--split admin-detail-editor-row--media">
                        <label class="admin-asset-field admin-detail-field" data-admin-image-field>
                          <span>File URL / ruta</span>
                          <span class="admin-asset-preview admin-asset-preview--wide<?= $media['file_url'] === '' ? ' is-empty' : '' ?>" data-admin-image-preview>
                            <img src="<?= adminEscape(adminRecordPreviewImage($media, ['file_url'])) ?>" alt="Preview file URL" data-admin-image-tag<?= $media['file_url'] === '' ? ' hidden' : '' ?> loading="lazy" />
                            <span class="admin-asset-placeholder"<?= $media['file_url'] !== '' ? ' hidden' : '' ?>>Sin preview</span>
                          </span>
                          <span class="admin-asset-actions">
                            <button type="button" class="admin-asset-action" data-admin-image-browse>Cambiar</button>
                            <button type="button" class="admin-asset-action admin-asset-action--icon" data-admin-image-clear aria-label="Limpiar file URL">x</button>
                          </span>
                          <input type="text" name="file_url" value="<?= adminEscape($media['file_url']) ?>" data-admin-image-input required />
                        </label>
                        <label class="admin-asset-field admin-detail-field" data-admin-image-field>
                          <span>Thumbnail URL</span>
                          <span class="admin-asset-preview<?= $media['thumbnail_url'] === '' ? ' is-empty' : '' ?>" data-admin-image-preview>
                            <img src="<?= adminEscape(adminRecordPreviewImage($media, ['thumbnail_url'])) ?>" alt="Preview thumbnail URL" data-admin-image-tag<?= $media['thumbnail_url'] === '' ? ' hidden' : '' ?> loading="lazy" />
                            <span class="admin-asset-placeholder"<?= $media['thumbnail_url'] !== '' ? ' hidden' : '' ?>>Sin imagen</span>
                          </span>
                          <span class="admin-asset-actions">
                            <button type="button" class="admin-asset-action" data-admin-image-browse>Cambiar</button>
                            <button type="button" class="admin-asset-action admin-asset-action--icon" data-admin-image-clear aria-label="Limpiar thumbnail URL">x</button>
                          </span>
                          <input type="text" name="thumbnail_url" value="<?= adminEscape($media['thumbnail_url']) ?>" data-admin-image-input />
                        </label>
                      </div>
                      <div class="admin-detail-editor-row">
                        <label class="admin-detail-field">Caption<textarea name="caption"><?= adminEscape($media['caption']) ?></textarea></label>
                      </div>
                      <div class="admin-detail-editor-row admin-detail-editor-row--split">
                        <label class="admin-checkbox"><input type="checkbox" name="is_cover" <?= adminChecked($media['is_cover']) ?> /> Portada</label>
                        <label class="admin-checkbox"><input type="checkbox" name="is_active" <?= adminChecked($media['is_active']) ?> /> Activa</label>
                      </div>
                      <div class="admin-detail-editor-row admin-actions admin-actions--editor">
                        <button type="submit" class="admin-button admin-button--primary">Guardar cambios</button>
                        <button type="button" class="admin-button admin-button--secondary" data-admin-cancel-edit>Cancelar</button>
                      </div>
                      <div class="admin-detail-editor-row">
                        <button type="submit" name="submit_action" value="delete_media" class="admin-button admin-button--danger admin-button--text" formnovalidate onclick="return confirm('Se eliminara este elemento multimedia. Continuar?');">Eliminar recurso</button>
                      </div>
                    </div>
                  </div>
                </form>
              <?php endforeach; ?>
            </div>
          </div>
        </section>
        </div>
      </main>
    </div>
  </div>
  <script type="module" src="../public/js/admin.js"></script>
</body>
</html>