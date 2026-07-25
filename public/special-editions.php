<?php

declare(strict_types=1);

$pageTitle = 'Special Editions | AgusMA Studio';
$metaDescription = 'Special Editions by AgusMA Studio: limited concepts, collectible drops and experimental archive releases.';
$canonicalPath = './special-editions.php';
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
$railLabel = 'Special Editions';

require __DIR__ . '/includes/head.php';
require __DIR__ . '/includes/category-rail.php';
require __DIR__ . '/includes/site-header.php';
?>
  <main class="category-page" data-category-browser data-category-slug="special-editions">
    <section class="category-hero" style="--category-hero-visual: url('./assets/images/cat-special.png');">
      <div class="category-hero__content">
        <p class="category-hero__eyebrow">Category 03 · Special Editions</p>
        <h1>Limited releases designed to feel singular from the first glance.</h1>
        <p class="category-hero__description">
          Special Editions collects the studio's rarer experiments: sharper concepts, tighter runs and stronger
          narrative framing for pieces that need a more exceptional treatment.
        </p>

        <div class="category-hero__actions">
          <a href="#featured-collections">Explore drops</a>
          <a href="./index.php#collections">See all categories</a>
        </div>
      </div>

      <div class="category-hero__panel">
        <div class="category-hero__panel-copy">
          <p>Editorial focus</p>
          <strong>Limited concepts, sharper contrast and archive-worthy execution.</strong>
        </div>
      </div>
    </section>

    <section class="category-metrics" aria-label="Special editions overview">
      <article>
        <span>Direction</span>
        <strong>Limited capsules</strong>
        <p>Experimental drops with stronger contrast, rarer references and tighter scope.</p>
      </article>
      <article>
        <span>Output</span>
        <strong data-category-count>0 collections</strong>
        <p>Collections loaded from the selected special-edition entity.</p>
      </article>
      <article>
        <span>Approach</span>
        <strong>Edition-first</strong>
        <p>Each release aims to feel authored, finite and distinct from the ongoing archive.</p>
      </article>
    </section>

    <section class="category-section">
      <div class="category-section__header">
        <div>
          <p class="category-section__eyebrow">Entities</p>
          <h2>Special edition entities</h2>
        </div>
      </div>

      <p class="category-status" data-entity-status>Cargando entidades de special editions...</p>
      <div class="entity-grid" data-entity-grid></div>
    </section>

    <section id="featured-collections" class="category-section">
      <div class="category-section__header">
        <div>
          <p class="category-section__eyebrow">Collections</p>
          <h2 data-selected-entity-name>Special edition collections</h2>
        </div>
        <a href="./index.php#collections">Return to categories</a>
      </div>

      <p class="category-status" data-collection-status>Selecciona una entidad para ver sus colecciones.</p>
      <div class="category-grid" data-collection-grid></div>
    </section>

    <section id="editorial-notes" class="category-notes">
      <div>
        <p class="category-section__eyebrow">Editorial notes</p>
        <h2>These releases should feel exceptional, not just different.</h2>
      </div>
      <p>
        The special-editions layer exists for concepts that need more ceremony: slower pacing, stronger narrative
        contrast and a visual system that immediately signals rarity inside the wider AgusMA archive.
      </p>
    </section>
  </main>

<?php require __DIR__ . '/includes/site-footer.php'; ?>
  <script type="module" src="./js/category-page.js"></script>
</body>
</html>