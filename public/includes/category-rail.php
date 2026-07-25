<?php

declare(strict_types=1);

$railLabel = $railLabel ?? 'Category Archive';
?>
<aside class="category-rail" aria-hidden="true">
  <img src="./assets/images/monogram.svg" alt="" />
  <span><?= htmlspecialchars($railLabel, ENT_QUOTES, 'UTF-8') ?></span>
</aside>