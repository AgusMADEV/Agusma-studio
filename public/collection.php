<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/request.php';

$categorySlug = publicPageSlugParam('category');
$entitySlug = publicPageSlugParam('entity');
$collectionSlug = publicPageSlugParam('collection');
$collectionTitle = publicPageTitleFromSlug($collectionSlug, 'Collection');

$pageTitle = $collectionTitle . ' | AgusMA Studio';
$metaDescription = 'Collection detail view for AgusMA Studio content architecture.';
$canonicalPath = $categorySlug !== null && $entitySlug !== null && $collectionSlug !== null
    ? sprintf('./collection.php?category=%s&entity=%s&collection=%s', urlencode($categorySlug), urlencode($entitySlug), urlencode($collectionSlug))
    : './collection.php';
$styles = [
    './css/variables.css',
    './css/base.css',
    './css/category-showcase.css',
    './css/collection-detail.css',
];
$headerNavLinks = [
    ['href' => './index.php#collections', 'label' => 'Collections'],
    ['href' => './football.php', 'label' => 'Football'],
    ['href' => './fashion.php', 'label' => 'Fashion'],
    ['href' => './special-editions.php', 'label' => 'Special Editions'],
    ['href' => './index.php#journal', 'label' => 'Journal'],
];
$headerActionLink = ['href' => './index.php', 'label' => 'Contact'];
$railLabel = 'Collection View';

require __DIR__ . '/includes/head.php';
require __DIR__ . '/includes/category-rail.php';
require __DIR__ . '/includes/site-header.php';
?>
  <main
    class="category-page collection-page"
    data-collection-page
    data-category-slug="<?= htmlspecialchars((string) ($categorySlug ?? ''), ENT_QUOTES, 'UTF-8') ?>"
    data-entity-slug="<?= htmlspecialchars((string) ($entitySlug ?? ''), ENT_QUOTES, 'UTF-8') ?>"
    data-collection-slug="<?= htmlspecialchars((string) ($collectionSlug ?? ''), ENT_QUOTES, 'UTF-8') ?>"
  >
    <section class="category-hero collection-hero">
      <div class="category-hero__content">
        <p class="category-hero__eyebrow" data-collection-eyebrow>Collection detail</p>
        <h1 data-collection-name><?= htmlspecialchars($collectionTitle, ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="category-hero__description" data-collection-description>Cargando colección...</p>

        <div class="category-hero__actions">
          <a href="#collection-content" data-collection-primary-action>Explore collection</a>
          <a href="./index.php#collections" data-collection-parent-link>Return to entity</a>
        </div>
      </div>

      <div class="category-hero__panel" data-collection-hero-panel>
        <div class="category-hero__panel-copy">
          <p>Collection focus</p>
          <strong data-collection-entity>Loading entity</strong>
        </div>
      </div>
    </section>

    <section class="collection-tags" aria-label="Collection tags">
      <p class="category-section__eyebrow">Tags</p>
      <div class="collection-tags__list" data-collection-tags>
        <span class="collection-tag">Cargando</span>
      </div>
    </section>

    <div id="collection-content" class="collection-sections" data-collection-sections aria-live="polite">
      <p class="category-status" data-collection-status>Cargando contenido de la colección...</p>
    </div>
  </main>

<?php require __DIR__ . '/includes/site-footer.php'; ?>
  <script type="module" src="./js/collection-page.js"></script>
</body>
</html>