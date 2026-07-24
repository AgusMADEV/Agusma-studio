CREATE DATABASE IF NOT EXISTS agusma_studio
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE agusma_studio;

CREATE TABLE IF NOT EXISTS categories (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  slug VARCHAR(120) NOT NULL,
  visual_key VARCHAR(60) NOT NULL,
  link_url VARCHAR(255) NOT NULL DEFAULT '#',
  display_order INT UNSIGNED NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY unique_categories_slug (slug)
);

CREATE TABLE IF NOT EXISTS featured_collections (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  category_id INT UNSIGNED NULL,
  title VARCHAR(150) NOT NULL,
  collection_year SMALLINT UNSIGNED NOT NULL,
  image_variant ENUM('light', 'dark') NOT NULL DEFAULT 'light',
  display_order INT UNSIGNED NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_featured_collections_category_id (category_id),
  CONSTRAINT fk_featured_collections_category_id
    FOREIGN KEY (category_id) REFERENCES categories (id)
    ON DELETE SET NULL
    ON UPDATE CASCADE
);

INSERT INTO categories (name, slug, visual_key, link_url, display_order)
SELECT 'Football', 'football', 'football', '#', 1
WHERE NOT EXISTS (
  SELECT 1 FROM categories WHERE slug = 'football'
);

INSERT INTO categories (name, slug, visual_key, link_url, display_order)
SELECT 'National Teams', 'national-teams', 'national-teams', '#', 2
WHERE NOT EXISTS (
  SELECT 1 FROM categories WHERE slug = 'national-teams'
);

INSERT INTO categories (name, slug, visual_key, link_url, display_order)
SELECT 'Fashion', 'fashion', 'fashion', '#', 3
WHERE NOT EXISTS (
  SELECT 1 FROM categories WHERE slug = 'fashion'
);

INSERT INTO categories (name, slug, visual_key, link_url, display_order)
SELECT 'Special Editions', 'special-editions', 'special-editions', '#', 4
WHERE NOT EXISTS (
  SELECT 1 FROM categories WHERE slug = 'special-editions'
);

INSERT INTO featured_collections (category_id, title, collection_year, image_variant, display_order)
SELECT c.id, 'Lumen Collection', 2026, 'light', 1
FROM categories c
WHERE c.slug = 'football'
AND NOT EXISTS (
  SELECT 1 FROM featured_collections WHERE title = 'Lumen Collection'
);

INSERT INTO featured_collections (category_id, title, collection_year, image_variant, display_order)
SELECT c.id, 'Nocturne Kit', 2026, 'dark', 2
FROM categories c
WHERE c.slug = 'fashion'
AND NOT EXISTS (
  SELECT 1 FROM featured_collections WHERE title = 'Nocturne Kit'
);

INSERT INTO featured_collections (category_id, title, collection_year, image_variant, display_order)
SELECT c.id, 'Terrain Edition', 2026, 'light', 3
FROM categories c
WHERE c.slug = 'national-teams'
AND NOT EXISTS (
  SELECT 1 FROM featured_collections WHERE title = 'Terrain Edition'
);

INSERT INTO featured_collections (category_id, title, collection_year, image_variant, display_order)
SELECT c.id, 'Atelier Archive Vol. I', 2026, 'light', 4
FROM categories c
WHERE c.slug = 'special-editions'
WHERE NOT EXISTS (
  SELECT 1 FROM featured_collections WHERE title = 'Atelier Archive Vol. I'
);