<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/database.php';

function adminRedirect(string $message): never
{
    header('Location: /Agusma-studio/admin/?message=' . urlencode($message));
    exit;
}

function adminPostString(string $key): string
{
    return trim((string) ($_POST[$key] ?? ''));
}

function adminPostInt(string $key, int $default = 0): int
{
    return max(0, (int) ($_POST[$key] ?? $default));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $connection = databaseConnection();
    if (isset($_POST['delete_category'])) {
      $statement = $connection->prepare('DELETE FROM categories WHERE id = :id');
      $statement->execute([
        ':id' => adminPostInt('id'),
      ]);

      adminRedirect('Categoria eliminada.');
    }

    if (isset($_POST['delete_collection'])) {
      $statement = $connection->prepare('DELETE FROM featured_collections WHERE id = :id');
      $statement->execute([
        ':id' => adminPostInt('id'),
      ]);

      adminRedirect('Coleccion eliminada.');
    }

        $action = adminPostString('action');

        if ($action === 'create_category') {
            $statement = $connection->prepare(
                'INSERT INTO categories (name, slug, visual_key, link_url, display_order, is_active)
                VALUES (:name, :slug, :visual_key, :link_url, :display_order, :is_active)'
            );
            $statement->execute([
                ':name' => adminPostString('name'),
                ':slug' => adminPostString('slug'),
                ':visual_key' => adminPostString('visual_key'),
                ':link_url' => adminPostString('link_url') ?: '#',
                ':display_order' => adminPostInt('display_order'),
                ':is_active' => isset($_POST['is_active']) ? 1 : 0,
            ]);

            adminRedirect('Categoria creada.');
        }

        if ($action === 'update_category') {
            $statement = $connection->prepare(
                'UPDATE categories
                SET name = :name,
                    slug = :slug,
                    visual_key = :visual_key,
                    link_url = :link_url,
                    display_order = :display_order,
                    is_active = :is_active
                WHERE id = :id'
            );
            $statement->execute([
                ':id' => adminPostInt('id'),
                ':name' => adminPostString('name'),
                ':slug' => adminPostString('slug'),
                ':visual_key' => adminPostString('visual_key'),
                ':link_url' => adminPostString('link_url') ?: '#',
                ':display_order' => adminPostInt('display_order'),
                ':is_active' => isset($_POST['is_active']) ? 1 : 0,
            ]);

            adminRedirect('Categoria actualizada.');
        }

        if ($action === 'create_collection') {
            $statement = $connection->prepare(
                'INSERT INTO featured_collections (category_id, title, collection_year, image_variant, display_order, is_active)
                VALUES (:category_id, :title, :collection_year, :image_variant, :display_order, :is_active)'
            );
            $statement->execute([
                ':category_id' => adminPostInt('category_id') ?: null,
                ':title' => adminPostString('title'),
                ':collection_year' => adminPostInt('collection_year'),
                ':image_variant' => adminPostString('image_variant') === 'dark' ? 'dark' : 'light',
                ':display_order' => adminPostInt('display_order'),
                ':is_active' => isset($_POST['is_active']) ? 1 : 0,
            ]);

            adminRedirect('Coleccion creada.');
        }

        if ($action === 'update_collection') {
            $statement = $connection->prepare(
                'UPDATE featured_collections
                SET category_id = :category_id,
                    title = :title,
                    collection_year = :collection_year,
                    image_variant = :image_variant,
                    display_order = :display_order,
                    is_active = :is_active
                WHERE id = :id'
            );
            $statement->execute([
                ':id' => adminPostInt('id'),
                ':category_id' => adminPostInt('category_id') ?: null,
                ':title' => adminPostString('title'),
                ':collection_year' => adminPostInt('collection_year'),
                ':image_variant' => adminPostString('image_variant') === 'dark' ? 'dark' : 'light',
                ':display_order' => adminPostInt('display_order'),
                ':is_active' => isset($_POST['is_active']) ? 1 : 0,
            ]);

            adminRedirect('Coleccion actualizada.');
        }

        adminRedirect('Accion no reconocida.');
    } catch (Throwable $exception) {
        error_log($exception->getMessage());
        adminRedirect('No se pudo guardar el cambio.');
    }
}

$connection = databaseConnection();

$categories = $connection->query(
    'SELECT id, name, slug, visual_key, link_url, display_order, is_active
    FROM categories
    ORDER BY display_order ASC, id ASC'
)->fetchAll();

$collections = $connection->query(
    'SELECT fc.id, fc.category_id, fc.title, fc.collection_year, fc.image_variant, fc.display_order, fc.is_active,
            c.name AS category_name
    FROM featured_collections fc
    LEFT JOIN categories c ON c.id = fc.category_id
    ORDER BY fc.display_order ASC, fc.id ASC'
)->fetchAll();

$flashMessage = trim((string) ($_GET['message'] ?? ''));
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin AgusMA Studio</title>
  <link rel="stylesheet" href="../public/css/variables.css" />
  <link rel="stylesheet" href="../public/css/admin.css" />
</head>
<body>
  <main class="admin-shell">
    <header class="admin-header">
      <div>
        <p class="admin-eyebrow">Panel basico</p>
        <h1>Admin AgusMA Studio</h1>
      </div>
      <a class="admin-link" href="../public/index.html">Ver web</a>
    </header>

    <?php if ($flashMessage !== ''): ?>
      <p class="admin-flash"><?= htmlspecialchars($flashMessage, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <section class="admin-grid">
      <section class="admin-card admin-card--form">
        <h2>Nueva categoria</h2>
        <form method="post" class="admin-form">
          <input type="hidden" name="action" value="create_category" />
          <label>Nombre<input type="text" name="name" required /></label>
          <label>Slug<input type="text" name="slug" required /></label>
          <label>Visual key<input type="text" name="visual_key" required /></label>
          <label>Enlace<input type="text" name="link_url" value="#" /></label>
          <label>Orden<input type="number" name="display_order" value="0" min="0" /></label>
          <label class="admin-checkbox"><input type="checkbox" name="is_active" checked /> Activa</label>
          <button type="submit">Crear categoria</button>
        </form>
      </section>

      <section class="admin-card admin-card--form">
        <h2>Nueva coleccion</h2>
        <form method="post" class="admin-form">
          <input type="hidden" name="action" value="create_collection" />
          <label>Titulo<input type="text" name="title" required /></label>
          <label>Categoria
            <select name="category_id">
              <option value="0">Sin categoria</option>
              <?php foreach ($categories as $category): ?>
                <option value="<?= (int) $category['id'] ?>"><?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?></option>
              <?php endforeach; ?>
            </select>
          </label>
          <label>Ano<input type="number" name="collection_year" value="2026" min="1900" max="2100" required /></label>
          <label>Visual
            <select name="image_variant">
              <option value="light">Light</option>
              <option value="dark">Dark</option>
            </select>
          </label>
          <label>Orden<input type="number" name="display_order" value="0" min="0" /></label>
          <label class="admin-checkbox"><input type="checkbox" name="is_active" checked /> Activa</label>
          <button type="submit">Crear coleccion</button>
        </form>
      </section>
    </section>

    <section class="admin-card">
      <div class="admin-section-title">
        <h2>Categorias</h2>
        <p><?= count($categories) ?> registradas</p>
      </div>

      <div class="admin-list">
        <?php foreach ($categories as $category): ?>
          <form method="post" class="admin-item">
            <input type="hidden" name="action" value="update_category" />
            <input type="hidden" name="id" value="<?= (int) $category['id'] ?>" />
            <label>Nombre<input type="text" name="name" value="<?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?>" required /></label>
            <label>Slug<input type="text" name="slug" value="<?= htmlspecialchars($category['slug'], ENT_QUOTES, 'UTF-8') ?>" required /></label>
            <label>Visual key<input type="text" name="visual_key" value="<?= htmlspecialchars($category['visual_key'], ENT_QUOTES, 'UTF-8') ?>" required /></label>
            <label>Enlace<input type="text" name="link_url" value="<?= htmlspecialchars($category['link_url'], ENT_QUOTES, 'UTF-8') ?>" /></label>
            <label>Orden<input type="number" name="display_order" value="<?= (int) $category['display_order'] ?>" min="0" /></label>
            <label class="admin-checkbox"><input type="checkbox" name="is_active" <?= (int) $category['is_active'] === 1 ? 'checked' : '' ?> /> Activa</label>
            <div class="admin-actions">
              <button type="submit">Guardar</button>
              <button type="submit" name="delete_category" value="1" class="admin-button admin-button--danger" formnovalidate onclick="return confirm('Se eliminara esta categoria. Continuar?');">Eliminar</button>
            </div>
          </form>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="admin-card">
      <div class="admin-section-title">
        <h2>Colecciones destacadas</h2>
        <p><?= count($collections) ?> registradas</p>
      </div>

      <div class="admin-list">
        <?php foreach ($collections as $collection): ?>
          <form method="post" class="admin-item">
            <input type="hidden" name="action" value="update_collection" />
            <input type="hidden" name="id" value="<?= (int) $collection['id'] ?>" />
            <label>Titulo<input type="text" name="title" value="<?= htmlspecialchars($collection['title'], ENT_QUOTES, 'UTF-8') ?>" required /></label>
            <label>Categoria
              <select name="category_id">
                <option value="0">Sin categoria</option>
                <?php foreach ($categories as $category): ?>
                  <option value="<?= (int) $category['id'] ?>" <?= (int) $collection['category_id'] === (int) $category['id'] ? 'selected' : '' ?>><?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
              </select>
            </label>
            <label>Ano<input type="number" name="collection_year" value="<?= (int) $collection['collection_year'] ?>" min="1900" max="2100" required /></label>
            <label>Visual
              <select name="image_variant">
                <option value="light" <?= $collection['image_variant'] === 'light' ? 'selected' : '' ?>>Light</option>
                <option value="dark" <?= $collection['image_variant'] === 'dark' ? 'selected' : '' ?>>Dark</option>
              </select>
            </label>
            <label>Orden<input type="number" name="display_order" value="<?= (int) $collection['display_order'] ?>" min="0" /></label>
            <label class="admin-checkbox"><input type="checkbox" name="is_active" <?= (int) $collection['is_active'] === 1 ? 'checked' : '' ?> /> Activa</label>
            <div class="admin-actions">
              <button type="submit">Guardar</button>
              <button type="submit" name="delete_collection" value="1" class="admin-button admin-button--danger" formnovalidate onclick="return confirm('Se eliminara esta coleccion. Continuar?');">Eliminar</button>
            </div>
          </form>
        <?php endforeach; ?>
      </div>
    </section>
  </main>
</body>
</html>