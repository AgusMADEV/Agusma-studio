<?php

declare(strict_types=1);

$pageTitle = 'Fashion | AgusMA Studio';
$metaDescription = 'Fashion category by AgusMA Studio: editorial garments, typography-led styling and collectible visual direction.';
$canonicalPath = './fashion.php';
$styles = [
    './css/variables.css',
    './css/base.css',
    './css/category-showcase.css',
];
$headerNavLinks = [
    ['href' => './index.php#collections', 'label' => 'Collections'],
    ['href' => '#featured-collections', 'label' => 'Archive'],
    ['href' => '#editorial-notes', 'label' => 'Concepts'],
    ['href' => './index.php#studio', 'label' => 'Studio'],
    ['href' => './index.php#journal', 'label' => 'Journal'],
];
$headerActionLink = ['href' => './index.php', 'label' => 'Contact'];
$railLabel = 'Fashion Archive';

require __DIR__ . '/includes/head.php';
require __DIR__ . '/includes/category-rail.php';
require __DIR__ . '/includes/site-header.php';
?>
  <main class="category-page" data-category-browser data-category-slug="fashion">
    <section class="category-hero" style="--category-hero-visual: url('./assets/images/cat-fashion.png');">
      <div class="category-hero__content">
        <p class="category-hero__eyebrow">Category 02 · Fashion</p>
        <h1>Styling systems translated into collectible fashion concepts.</h1>
        <p class="category-hero__description">
          Fashion at AgusMA Studio is treated as a visual system: silhouette, typography, materials and editorial
          framing working together to create restrained but memorable pieces.
        </p>

        <div class="category-hero__actions">
          <a href="#featured-collections">Explore drops</a>
          <a href="./index.php#collections">See all categories</a>
        </div>
      </div>

      <div class="category-hero__panel">
        <div class="category-hero__panel-copy">
          <p>Editorial focus</p>
          <strong>Garment culture, elevated layouts and collectible presentation.</strong>
        </div>
      </div>
    </section>

    <section class="category-metrics" aria-label="Fashion category overview">
      <article>
        <span>Direction</span>
        <strong>Editorial fashion</strong>
        <p>Refined styling, visual clarity and strong typographic hierarchy.</p>
      </article>
      <article>
        <span>Output</span>
        <strong data-category-count>0 collections</strong>
        <p>Collections loaded from the selected fashion entity.</p>
      </article>
      <article>
        <span>Approach</span>
        <strong>Image-led</strong>
        <p>Every concept is built to feel like a magazine object rather than a plain product card.</p>
      </article>
    </section>

    <section class="category-section">
      <div class="category-section__header">
        <div>
          <p class="category-section__eyebrow">Entities</p>
          <h2>Fashion entities</h2>
        </div>
      </div>

      <p class="category-status" data-entity-status>Cargando entidades de fashion...</p>
      <div class="entity-grid" data-entity-grid></div>
    </section>

    <section id="featured-collections" class="category-section">
      <div class="category-section__header">
        <div>
          <p class="category-section__eyebrow">Collections</p>
          <h2 data-selected-entity-name>Fashion collections</h2>
        </div>
        <a href="./index.php#collections">Return to categories</a>
      </div>

      <p class="category-status" data-collection-status>Selecciona una entidad para ver sus colecciones.</p>
      <div class="category-grid" data-collection-grid></div>
    </section>

    <section id="editorial-notes" class="category-notes">
      <div>
        <p class="category-section__eyebrow">Editorial notes</p>
        <h2>Fashion here is sequencing, not just styling.</h2>
      </div>
      <p>
        The category highlights pieces where clothing language and art direction are inseparable. The result should
        read like an edition: paced, deliberate and immediately recognizable as part of the studio archive.
      </p>
    </section>
  </main>

<?php require __DIR__ . '/includes/site-footer.php'; ?>
  <script type="module" src="./js/category-page.js"></script>
</body>
</html>