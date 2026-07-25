<?php

declare(strict_types=1);

$metaDescription = $metaDescription ?? 'AgusMA Studio explores football, fashion and collectible concept design through curated editorial releases.';
$metaRobots = $metaRobots ?? 'index,follow';
$metaKeywords = $metaKeywords ?? 'AgusMA Studio, football design, fashion archive, collectible concepts';
$canonicalPath = $canonicalPath ?? null;
$socialImage = $socialImage ?? './assets/images/logo.svg';
?>
  <meta name="description" content="<?= htmlspecialchars($metaDescription, ENT_QUOTES, 'UTF-8') ?>" />
  <meta name="robots" content="<?= htmlspecialchars($metaRobots, ENT_QUOTES, 'UTF-8') ?>" />
  <meta name="keywords" content="<?= htmlspecialchars($metaKeywords, ENT_QUOTES, 'UTF-8') ?>" />
  <meta property="og:title" content="<?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?>" />
  <meta property="og:description" content="<?= htmlspecialchars($metaDescription, ENT_QUOTES, 'UTF-8') ?>" />
  <meta property="og:type" content="website" />
  <meta property="og:image" content="<?= htmlspecialchars($socialImage, ENT_QUOTES, 'UTF-8') ?>" />
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="<?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?>" />
  <meta name="twitter:description" content="<?= htmlspecialchars($metaDescription, ENT_QUOTES, 'UTF-8') ?>" />
<?php if (is_string($canonicalPath) && $canonicalPath !== ''): ?>
  <link rel="canonical" href="<?= htmlspecialchars($canonicalPath, ENT_QUOTES, 'UTF-8') ?>" />
<?php endif; ?>