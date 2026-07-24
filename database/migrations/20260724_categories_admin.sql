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

ALTER TABLE featured_collections
  ADD COLUMN category_id INT UNSIGNED NULL AFTER id;

ALTER TABLE featured_collections
  ADD KEY idx_featured_collections_category_id (category_id);

ALTER TABLE featured_collections
  ADD CONSTRAINT fk_featured_collections_category_id
  FOREIGN KEY (category_id) REFERENCES categories (id)
  ON DELETE SET NULL
  ON UPDATE CASCADE;

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

UPDATE featured_collections fc
LEFT JOIN categories c ON c.slug = 'football'
SET fc.category_id = c.id
WHERE fc.title = 'Lumen Collection' AND fc.category_id IS NULL;

UPDATE featured_collections fc
LEFT JOIN categories c ON c.slug = 'fashion'
SET fc.category_id = c.id
WHERE fc.title = 'Nocturne Kit' AND fc.category_id IS NULL;

UPDATE featured_collections fc
LEFT JOIN categories c ON c.slug = 'national-teams'
SET fc.category_id = c.id
WHERE fc.title = 'Terrain Edition' AND fc.category_id IS NULL;

UPDATE featured_collections fc
LEFT JOIN categories c ON c.slug = 'special-editions'
SET fc.category_id = c.id
WHERE fc.title = 'Atelier Archive Vol. I' AND fc.category_id IS NULL;