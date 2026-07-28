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
    ['href' => './index.php#studio', 'label' => 'Studio'],
    ['href' => './index.php#journal', 'label' => 'Journal'],
];
$headerActionLink = ['href' => './index.php', 'label' => 'Contact'];
$headerClass = 'header header--transparent';
$railLabel = 'Football Archive';

require __DIR__ . '/includes/head.php';
require __DIR__ . '/includes/category-rail.php';
require __DIR__ . '/includes/site-header.php';
?>
  <main class="category-page football-page" data-football-page data-category-slug="football" data-default-entity-type="club">
    <section class="football-hero">
      <div class="football-hero__content">
        <p class="football-hero__eyebrow">Collections</p>
        <h1>Football.</h1>
        <p class="football-hero__description">
          A curated selection of football clubs and national teams reimagined through timeless design
          and collectible visual storytelling.
        </p>

        <div class="football-hero__actions">
          <a href="#featured-collections">Explore football</a>
          <a href="./index.php#collections">See all categories</a>
        </div>
      </div>

      <div class="football-hero__panel">
        <div class="football-hero__panel-copy">
          <p>Editorial focus</p>
          <strong>Classic clubs, national identities and collectible execution.</strong>
        </div>
      </div>

      <div class="football-nav" aria-label="Football section selector">
        <div class="football-tabs" role="tablist" aria-label="Football entities by type">
        <button type="button" class="football-tab" data-entity-type-tab="club" aria-pressed="true">Clubs</button>
        <button type="button" class="football-tab" data-entity-type-tab="national_team" aria-pressed="false">National Teams</button>
        </div>
        <p class="football-status" data-entity-status>Cargando entidades de football...</p>
      </div>
    </section>

    <section id="featured-collections" class="football-featured">
      <div class="football-section__header">
        <div>
          <p class="football-section__eyebrow" data-featured-label>Featured clubs</p>
          <h2 data-selected-entity-name>Football entities</h2>
        </div>
        <a data-selected-entity-link href="#football-archive-title">View all clubs</a>
      </div>
      <div class="football-featured-row" data-entity-grid></div>
    </section>

    <section class="football-archive" aria-labelledby="football-archive-title">
      <h2 id="football-archive-title" class="football-archive__title">Football archive</h2>

      <div class="football-archive__layout">
        <aside class="football-archive__sidebar" aria-label="Archive filters">
          <p class="football-archive__sidebar-eyebrow">Browse the archive</p>
          <div class="football-archive__filters" data-archive-filter-list></div>
        </aside>

        <div class="football-archive__main">
          <div class="football-archive__toolbar">
            <div class="football-archive__toolbar-group football-archive__toolbar-group--view" aria-label="Archive view selector">
              <span class="football-archive__toolbar-label">View</span>
              <button type="button" class="football-archive__toolbar-action is-active" data-archive-view-control="grid" aria-pressed="true">Grid</button>
              <button type="button" class="football-archive__toolbar-action" data-archive-view-control="list" aria-pressed="false">List</button>
            </div>

            <div class="football-archive__toolbar-group football-archive__toolbar-group--filters" aria-label="Archive display filters">
              <span class="football-archive__toolbar-label">Filter</span>
              <div class="football-archive__toolbar-menu">
                <button type="button" class="football-archive__toolbar-action" data-archive-toolbar-control="category" aria-haspopup="menu" aria-expanded="false">Category</button>
                <div class="football-archive__toolbar-dropdown" data-archive-toolbar-menu="category" hidden></div>
              </div>
              <div class="football-archive__toolbar-menu">
                <button type="button" class="football-archive__toolbar-action" data-archive-toolbar-control="year" aria-haspopup="menu" aria-expanded="false">Year</button>
                <div class="football-archive__toolbar-dropdown" data-archive-toolbar-menu="year" hidden></div>
              </div>
              <div class="football-archive__toolbar-menu">
                <button type="button" class="football-archive__toolbar-action" data-archive-toolbar-control="color" aria-haspopup="menu" aria-expanded="false">Color</button>
                <div class="football-archive__toolbar-dropdown" data-archive-toolbar-menu="color" hidden></div>
              </div>
              <div class="football-archive__toolbar-menu">
                <button type="button" class="football-archive__toolbar-action" data-archive-toolbar-control="type" aria-haspopup="menu" aria-expanded="false">Type</button>
                <div class="football-archive__toolbar-dropdown" data-archive-toolbar-menu="type" hidden></div>
              </div>
            </div>

            <div class="football-archive__toolbar-group football-archive__toolbar-group--sort" aria-label="Archive sorting">
              <span class="football-archive__toolbar-label">Sort</span>
              <div class="football-archive__toolbar-menu">
                <button type="button" class="football-archive__toolbar-action" data-archive-sort-control aria-haspopup="menu" aria-expanded="false">Latest</button>
                <div class="football-archive__toolbar-dropdown football-archive__toolbar-dropdown--sort" data-archive-toolbar-menu="sort" hidden></div>
              </div>
            </div>
          </div>

          <p class="football-archive__summary" data-archive-summary>Loading archive...</p>
          <p class="football-status" data-archive-status>Cargando archivo de football...</p>
          <div class="football-archive-grid" data-archive-grid></div>
        </div>
      </div>
    </section>
  </main>

<?php require __DIR__ . '/includes/site-footer.php'; ?>
  <script type="module" src="./js/football.js?v=20260728c"></script>
</body>
</html>