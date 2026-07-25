<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? 'AgusMA Studio';
$styles = $styles ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
<?php require __DIR__ . '/seo-meta.php'; ?>
<?php foreach ($styles as $style): ?>
  <link rel="stylesheet" href="<?= htmlspecialchars($style, ENT_QUOTES, 'UTF-8') ?>" />
<?php endforeach; ?>
</head>
<body>