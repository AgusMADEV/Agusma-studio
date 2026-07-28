<?php

declare(strict_types=1);

$headerNavLinks = $headerNavLinks ?? [];
$homeHref = $homeHref ?? './index.php';
$headerActionLink = $headerActionLink ?? ['href' => '#contact', 'label' => 'Contact'];
$headerClass = trim((string) ($headerClass ?? 'header'));
?>
<header class="<?= htmlspecialchars($headerClass, ENT_QUOTES, 'UTF-8') ?>">
  <a href="<?= htmlspecialchars($homeHref, ENT_QUOTES, 'UTF-8') ?>" class="header__logo"><img src="./assets/images/logo.svg" alt="AgusMA Studio Logo" /></a>

  <nav class="header__nav" aria-label="Primary">
<?php foreach ($headerNavLinks as $link): ?>
    <a href="<?= htmlspecialchars((string) $link['href'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $link['label'], ENT_QUOTES, 'UTF-8') ?></a>
<?php endforeach; ?>
  </nav>

  <div class="header__actions">
    <button type="button">Search</button>
    <a href="<?= htmlspecialchars((string) $headerActionLink['href'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $headerActionLink['label'], ENT_QUOTES, 'UTF-8') ?></a>
  </div>
</header>