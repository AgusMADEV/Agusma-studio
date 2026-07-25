<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/request.php';

$categorySlug = publicPageSlugParam('category');
$entitySlug = publicPageSlugParam('entity');
$entityTitle = publicPageTitleFromSlug($entitySlug, 'Entity');

$pageTitle = $entityTitle . ' | AgusMA Studio';
$metaDescription = 'Entity detail view for AgusMA Studio content architecture.';
$canonicalPath = $categorySlug !== null && $entitySlug !== null
    ? sprintf('./entity.php?category=%s&entity=%s', urlencode($categorySlug), urlencode($entitySlug))
    : './entity.php';
$styles = [
    './css/variables.css',
    './css/base.css',
    './css/category-showcase.css',
];
$headerNavLinks = [
    ['href' => './index.php#collections', 'label' => 'Collections'],
    ['href' => './football.php', 'label' => 'Football'],
    ['href' => './fashion.php', 'label' => 'Fashion'],
    ['href' => './special-editions.php', 'label' => 'Special Editions'],
    ['href' => './index.php#journal', 'label' => 'Journal'],
];
$headerActionLink = ['href' => './index.php', 'label' => 'Contact'];
$railLabel = 'Entity View';

require __DIR__ . '/includes/head.php';
require __DIR__ . '/includes/category-rail.php';
require __DIR__ . '/includes/site-header.php';
?>
  <main class="category-page" data-entity-page data-category-slug="<?= htmlspecialchars((string) ($categorySlug ?? ''), ENT_QUOTES, 'UTF-8') ?>" data-entity-slug="<?= htmlspecialchars((string) ($entitySlug ?? ''), ENT_QUOTES, 'UTF-8') ?>">
    <section class="category-hero">
      <div class="category-hero__content">
        <p class="category-hero__eyebrow" data-entity-eyebrow>Entity detail</p>
        <h1 data-entity-name><?= htmlspecialchars($entityTitle, ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="category-hero__description" data-entity-description>Cargando entidad...</p>

        <div class="category-hero__actions">
          <a href="#entity-collections">Explore collections</a>
          <a href="./index.php#collections">Back to categories</a>
        </div>
      </div>

      <div class="category-hero__panel">
        <div class="category-hero__panel-copy">
          <p>Entity focus</p>
          <strong data-entity-type>Loading entity type</strong>
        </div>
      </div>
    </section>

    <section class="category-metrics" aria-label="Entity overview">
      <article>
        <span>Category</span>
        <strong data-entity-category><?= htmlspecialchars(publicPageTitleFromSlug($categorySlug, 'Category'), ENT_QUOTES, 'UTF-8') ?></strong>
        <p>The parent category that structures this entity inside AgusMA Studio.</p>
      </article>
      <article>
        <span>Output</span>
        <strong data-entity-collection-count>0 collections</strong>
        <p>Collections available inside this entity hierarchy.</p>
      </article>
      <article>
        <span>Navigation</span>
        <strong>Entity → Collection</strong>
        <p>This page sits between category discovery and full collection detail.</p>
      </article>
    </section>

    <section id="entity-collections" class="category-section">
      <div class="category-section__header">
        <div>
          <p class="category-section__eyebrow">Collections</p>
          <h2 data-entity-collections-title>Collections</h2>
        </div>
        <a href="./index.php#collections">Return to categories</a>
      </div>

      <p class="category-status" data-entity-status>Cargando colecciones...</p>
      <div class="category-grid" data-entity-collection-grid></div>
    </section>
  </main>

<?php require __DIR__ . '/includes/site-footer.php'; ?>
  <script type="module" src="./js/entity-page.js"></script>
</body>
</html>