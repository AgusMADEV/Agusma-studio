<?php

declare(strict_types=1);

$pageTitle = 'Football | AgusMA Studio';
$metaDescription = 'Football by AgusMA Studio: club concepts, national team capsules and collectible editorial football drops.';
$canonicalPath = './football.php';
$styles = [
    './css/variables.css',
    './css/base.css',
    './css/football.css',
];
$headerNavLinks = [
    ['href' => './index.php#collections', 'label' => 'Collections'],
    ['href' => '#featured-collections', 'label' => 'Archive'],
    ['href' => '#editorial-notes', 'label' => 'Concepts'],
    ['href' => './index.php#studio', 'label' => 'Studio'],
    ['href' => './index.php#journal', 'label' => 'Journal'],
];
$headerActionLink = ['href' => './index.php', 'label' => 'Contact'];
$railLabel = 'Football Archive';

require __DIR__ . '/includes/head.php';
require __DIR__ . '/includes/category-rail.php';
require __DIR__ . '/includes/site-header.php';
?>
  <main class="category-page" data-category-browser data-category-slug="football" data-default-entity-type="all">
    <section class="football-hero">
      <div class="football-hero__content">
        <p class="football-hero__eyebrow">Category 01 · Football</p>
        <h1>Concept-driven football pieces built as collectible archive objects.</h1>
        <p class="football-hero__description">
          A focused selection of club stories, retro futures and editorial kit explorations.
          This category groups the most refined football concepts from AgusMA Studio into one view.
        </p>

        <div class="football-hero__actions">
          <a href="#featured-collections">Explore drops</a>
          <a href="./index.php#collections">See all categories</a>
        </div>
      </div>

      <div class="football-hero__panel">
        <div class="football-hero__panel-copy">
          <p>Editorial focus</p>
          <strong>Classic clubs, modern typography, collectible execution.</strong>
        </div>
      </div>
    </section>

    <section class="football-metrics" aria-label="Football category overview">
      <article>
        <span>Direction</span>
        <strong>Football culture</strong>
        <p>Club identity, nostalgia and contemporary presentation.</p>
      </article>
      <article>
        <span>Output</span>
        <strong data-category-count>0 collections</strong>
        <p>Collections loaded from the selected entity inside Football.</p>
      </article>
      <article>
        <span>Approach</span>
        <strong>Archive-first</strong>
        <p>Timeless layouts, restrained palettes and concept-led storytelling.</p>
      </article>
    </section>

    <section class="football-section">
      <div class="football-section__header">
        <div>
          <p class="football-section__eyebrow">Entities</p>
          <h2>Football entities</h2>
        </div>
      </div>

      <div class="football-tabs" role="tablist" aria-label="Football entities by type">
        <button type="button" class="football-tab is-active" data-entity-type-tab="all" aria-pressed="true">All Football</button>
        <button type="button" class="football-tab" data-entity-type-tab="club" aria-pressed="false">Club Football</button>
        <button type="button" class="football-tab" data-entity-type-tab="national_team" aria-pressed="false">National Teams</button>
      </div>

      <p class="football-status" data-entity-status>Cargando entidades de football...</p>
      <div class="football-entity-grid" data-entity-grid></div>
    </section>

    <section id="featured-collections" class="football-section">
      <div class="football-section__header">
        <div>
          <p class="football-section__eyebrow">Collections</p>
          <h2 data-selected-entity-name>Football collections</h2>
        </div>
        <a href="./index.php#collections">Return to categories</a>
      </div>

      <p class="football-status" data-collection-status>Selecciona una entidad para ver sus colecciones.</p>
      <div class="football-grid" data-collection-grid></div>
    </section>

    <section id="editorial-notes" class="football-notes">
      <div>
        <p class="football-section__eyebrow">Editorial notes</p>
        <h2>Built to feel like a capsule, not a catalog.</h2>
      </div>
      <p>
        The football category is designed as a tighter viewing layer inside the studio. Instead of showing
        every project equally, it prioritizes pieces that carry the clearest attitude, strongest silhouette
        and most collectible visual system.
      </p>
    </section>
  </main>

<?php require __DIR__ . '/includes/site-footer.php'; ?>
  <script type="module" src="./js/football.js"></script>
</body>
</html>