<?php

declare(strict_types=1);

$pageTitle = 'AgusMA Studio';
$metaDescription = 'AgusMA Studio explores football, fashion and special-edition concepts through curated collectible releases.';
$canonicalPath = './index.php';
$styles = [
    './css/variables.css',
    './css/base.css',
    './css/style.css',
    './css/responsive.css',
];
$headerNavLinks = [
    ['href' => '#collections', 'label' => 'Collections'],
    ['href' => '#archive', 'label' => 'Archive'],
    ['href' => '#concepts', 'label' => 'Concepts'],
    ['href' => '#studio', 'label' => 'Studio'],
    ['href' => '#journal', 'label' => 'Journal'],
];
$headerActionLink = ['href' => '#contact', 'label' => 'Contact'];

require __DIR__ . '/includes/head.php';
require __DIR__ . '/includes/home-sidebar.php';
require __DIR__ . '/includes/site-header.php';
?>
  <main class="page">
    <section class="hero">
      <div class="hero__content">
        <p class="hero__eyebrow">Welcome to AgusMA Studio</p>

        <h1 class="hero__title">
          Concepts.<br />
          Collections.<br />
          Culture.
        </h1>

        <p class="hero__description">
          A curated design studio and digital archive exploring the intersections
          of football, fashion and culture through timeless concepts and collectible editions.
        </p>

        <a href="#collections" class="hero__link">
          Explore collections <span>→</span>
        </a>
      </div>

    </section>

    <section id="collections" class="category-grid" data-category-grid>
      <p class="category-status" data-category-status>Cargando categorias...</p>

      <article class="category-card category-card--football">
        <span>01</span>
        <div class="category-card__body">
          <h2>Football</h2>
          <a href="#" aria-label="Open Football category">→</a>
        </div>
        <div class="category-card__visual" aria-hidden="true"></div>
      </article>

      <article class="category-card category-card--fashion">
        <span>02</span>
        <div class="category-card__body">
          <h2>Fashion</h2>
          <a href="./fashion.php" aria-label="Open Fashion category">→</a>
        </div>
        <div class="category-card__visual" aria-hidden="true"></div>
      </article>

      <article class="category-card category-card--special-editions">
        <span>03</span>
        <div class="category-card__body">
          <h2>Special Editions</h2>
          <a href="./special-editions.php" aria-label="Open Special Editions category">→</a>
        </div>
        <div class="category-card__visual" aria-hidden="true"></div>
      </article>
    </section>

    <section class="featured">
      <div class="section-header">
        <p>Featured Collections</p>
        <a href="./football.php">Browse architecture →</a>
      </div>

      <p class="featured-status" data-featured-status>Cargando colecciones...</p>

      <div class="featured-grid" data-featured-grid>
        <article class="collection-card">
          <div class="collection-card__image"></div>
          <div class="collection-card__info">
            <h3>Lumen Collection</h3>
            <span>2026</span>
          </div>
        </article>

        <article class="collection-card">
          <div class="collection-card__image collection-card__image--dark"></div>
          <div class="collection-card__info">
            <h3>Nocturne Kit</h3>
            <span>2026</span>
          </div>
        </article>

        <article class="collection-card">
          <div class="collection-card__image"></div>
          <div class="collection-card__info">
            <h3>Terrain Edition</h3>
            <span>2026</span>
          </div>
        </article>

        <article class="collection-card">
          <div class="collection-card__image"></div>
          <div class="collection-card__info">
            <h3>Atelier Archive Vol. I</h3>
            <span>2026</span>
          </div>
        </article>
      </div>
    </section>
  </main>

<?php require __DIR__ . '/includes/site-footer.php'; ?>
  <script type="module" src="./js/main.js"></script>
</body>
</html>