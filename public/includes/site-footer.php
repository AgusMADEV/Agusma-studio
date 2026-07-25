<?php

declare(strict_types=1);

$footerLabel = $footerLabel ?? 'AgusMA Studio';
?>
  <footer class="site-footer">
    <div class="site-footer__brand">
      <p class="site-footer__eyebrow">AgusMA Studio</p>
      <p class="site-footer__copy">
        Curated football, fashion and special-edition concepts built as collectible digital objects.
      </p>
    </div>

    <div class="site-footer__links" aria-label="Footer">
      <a href="./index.php#collections">Collections</a>
      <a href="./football.php">Football</a>
      <a href="./fashion.php">Fashion</a>
      <a href="./special-editions.php">Special Editions</a>
    </div>

    <p class="site-footer__meta">© 2026 <?= htmlspecialchars($footerLabel, ENT_QUOTES, 'UTF-8') ?></p>
  </footer>