CREATE DATABASE IF NOT EXISTS agusma_studio
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE agusma_studio;

CREATE TABLE IF NOT EXISTS categories (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  slug VARCHAR(120) NOT NULL,
  short_description VARCHAR(255) NULL,
  description TEXT NULL,
  visual_key VARCHAR(60) NOT NULL,
  cover_image VARCHAR(255) NULL,
  hero_image VARCHAR(255) NULL,
  link_url VARCHAR(255) NOT NULL DEFAULT '#',
  display_order INT UNSIGNED NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY unique_categories_slug (slug)
);

CREATE TABLE IF NOT EXISTS entities (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  category_id INT UNSIGNED NOT NULL,
  name VARCHAR(150) NOT NULL,
  slug VARCHAR(150) NOT NULL,
  entity_type VARCHAR(60) NOT NULL DEFAULT 'other',
  subtitle VARCHAR(191) NULL,
  short_description VARCHAR(255) NULL,
  description TEXT NULL,
  logo_url VARCHAR(255) NULL,
  cover_image VARCHAR(255) NULL,
  primary_color VARCHAR(30) NULL,
  secondary_color VARCHAR(30) NULL,
  background_color VARCHAR(30) NULL,
  text_color VARCHAR(30) NULL,
  display_order INT UNSIGNED NOT NULL DEFAULT 0,
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY unique_entities_category_slug (category_id, slug),
  KEY idx_entities_category_id (category_id),
  CONSTRAINT fk_entities_category_id
    FOREIGN KEY (category_id) REFERENCES categories (id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS collection_templates (
  id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(60) NOT NULL,
  description VARCHAR(255) NULL,
  preview_image VARCHAR(255) NULL,
  display_order INT UNSIGNED NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY unique_collection_template_slug (slug)
);

CREATE TABLE IF NOT EXISTS collection_template_sections (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  template_id SMALLINT UNSIGNED NOT NULL,
  section_key VARCHAR(80) NOT NULL,
  section_type VARCHAR(60) NOT NULL,
  eyebrow VARCHAR(150) NULL,
  title VARCHAR(200) NULL,
  body TEXT NULL,
  settings_json JSON NULL,
  display_order INT UNSIGNED NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY unique_template_section_key (template_id, section_key),
  KEY idx_template_sections_order (template_id, is_active, display_order),
  CONSTRAINT fk_template_sections_template
    FOREIGN KEY (template_id) REFERENCES collection_templates (id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS collections (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  entity_id INT UNSIGNED NOT NULL,
  name VARCHAR(180) NOT NULL,
  slug VARCHAR(180) NOT NULL,
  subtitle VARCHAR(191) NULL,
  collection_year SMALLINT UNSIGNED NULL,
  season VARCHAR(60) NULL,
  short_description VARCHAR(255) NULL,
  description TEXT NULL,
  concept TEXT NULL,
  cover_image VARCHAR(255) NULL,
  thumbnail_image VARCHAR(255) NULL,
  primary_color VARCHAR(30) NULL,
  secondary_color VARCHAR(30) NULL,
  background_color VARCHAR(30) NULL,
  text_color VARCHAR(30) NULL,
  image_variant VARCHAR(30) NULL,
  layout_style VARCHAR(60) NULL,
  template_id SMALLINT UNSIGNED NULL,
  display_order INT UNSIGNED NOT NULL DEFAULT 0,
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  published_at DATETIME NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY unique_collections_entity_slug (entity_id, slug),
  KEY idx_collections_entity_id (entity_id),
  KEY idx_collections_featured (is_featured, is_active, display_order),
  KEY idx_collections_template_id (template_id),
  CONSTRAINT fk_collections_entity_id
    FOREIGN KEY (entity_id) REFERENCES entities (id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT fk_collections_template_id
    FOREIGN KEY (template_id) REFERENCES collection_templates (id)
    ON UPDATE CASCADE
    ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS collection_sections (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  collection_id INT UNSIGNED NOT NULL,

  section_key VARCHAR(80) NOT NULL,
  section_type VARCHAR(60) NOT NULL,

  eyebrow VARCHAR(150) NULL,
  title VARCHAR(200) NULL,
  body TEXT NULL,

  settings_json JSON NULL,

  display_order INT UNSIGNED NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,

  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL
    DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),

  UNIQUE KEY unique_collection_section_key (
    collection_id,
    section_key
  ),

  KEY idx_collection_sections_order (
    collection_id,
    is_active,
    display_order
  ),

  CONSTRAINT fk_collection_sections_collection
    FOREIGN KEY (collection_id)
    REFERENCES collections(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS pieces (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  collection_id INT UNSIGNED NOT NULL,
  name VARCHAR(180) NOT NULL,
  slug VARCHAR(180) NOT NULL,
  piece_type VARCHAR(60) NOT NULL DEFAULT 'other',
  subtitle VARCHAR(191) NULL,
  short_description VARCHAR(255) NULL,
  description TEXT NULL,
  cover_image VARCHAR(255) NULL,
  display_order INT UNSIGNED NOT NULL DEFAULT 0,
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY unique_pieces_collection_slug (collection_id, slug),
  KEY idx_pieces_collection_id (collection_id),
  CONSTRAINT fk_pieces_collection_id
    FOREIGN KEY (collection_id) REFERENCES collections (id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS media (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  collection_id INT UNSIGNED NOT NULL,
  piece_id INT UNSIGNED NULL,
  media_type VARCHAR(60) NOT NULL DEFAULT 'image',
  file_url VARCHAR(255) NOT NULL,
  thumbnail_url VARCHAR(255) NULL,
  title VARCHAR(180) NULL,
  alt_text VARCHAR(255) NULL,
  caption TEXT NULL,
  section_key VARCHAR(80) NULL,
  display_order INT UNSIGNED NOT NULL DEFAULT 0,
  is_cover TINYINT(1) NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_media_collection_id (collection_id),
  KEY idx_media_piece_id (piece_id),
  CONSTRAINT fk_media_collection_id
    FOREIGN KEY (collection_id) REFERENCES collections (id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT fk_media_piece_id
    FOREIGN KEY (piece_id) REFERENCES pieces (id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS tags (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  slug VARCHAR(120) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY unique_tags_slug (slug)
);

CREATE TABLE IF NOT EXISTS collection_tags (
  collection_id INT UNSIGNED NOT NULL,
  tag_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (collection_id, tag_id),
  KEY idx_collection_tags_tag_id (tag_id),
  CONSTRAINT fk_collection_tags_collection_id
    FOREIGN KEY (collection_id) REFERENCES collections (id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT fk_collection_tags_tag_id
    FOREIGN KEY (tag_id) REFERENCES tags (id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
);

INSERT INTO categories (name, slug, short_description, description, visual_key, cover_image, hero_image, link_url, display_order, is_active)
SELECT 'Football', 'football', 'Club football, national teams and collectible kit concepts.', 'Football gathers club projects, national team narratives and archive-driven kit systems inside a single category.', 'football', './assets/images/cat-football2.png', './assets/images/hero-bg3b.svg', './football.php', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE slug = 'football');

INSERT INTO categories (name, slug, short_description, description, visual_key, cover_image, hero_image, link_url, display_order, is_active)
SELECT 'Fashion', 'fashion', 'Editorial garments, styling systems and wearable concepts.', 'Fashion collects AgusMA Studio garment concepts and styling-led editorial explorations.', 'fashion', './assets/images/cat-fashion.png', './assets/images/cat-fashion.png', './fashion.php', 2, 1
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE slug = 'fashion');

INSERT INTO categories (name, slug, short_description, description, visual_key, cover_image, hero_image, link_url, display_order, is_active)
SELECT 'Special Editions', 'special-editions', 'Limited releases and experimental collectible concepts.', 'Special Editions focuses on rarer releases, collectible drops and exceptional concept capsules.', 'special-editions', './assets/images/cat-special.png', './assets/images/cat-special.png', './special-editions.php', 3, 1
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE slug = 'special-editions');

INSERT INTO entities (category_id, name, slug, entity_type, subtitle, short_description, description, display_order, is_featured, is_active, primary_color, secondary_color, background_color, text_color)
SELECT c.id, 'Real Madrid', 'real-madrid', 'club', 'Club entity', 'Historic club concepts and seasonal kit systems.', 'Real Madrid acts as the flagship club entity inside Football, ready to host full seasonal collections and individual pieces.', 1, 1, 1, '#d9c7a4', '#ffffff', '#f6f1e6', '#111111'
FROM categories c
WHERE c.slug = 'football'
  AND NOT EXISTS (SELECT 1 FROM entities e WHERE e.category_id = c.id AND e.slug = 'real-madrid');

INSERT INTO entities (category_id, name, slug, entity_type, subtitle, short_description, description, display_order, is_featured, is_active, primary_color, secondary_color, background_color, text_color)
SELECT c.id, 'Portugal', 'portugal', 'national_team', 'National team entity', 'National team capsules and player-led editorial collections.', 'Portugal represents the national-team branch inside Football and replaces National Teams as a standalone category.', 2, 1, 1, '#9f2033', '#d4af37', '#f5ece8', '#111111'
FROM categories c
WHERE c.slug = 'football'
  AND NOT EXISTS (SELECT 1 FROM entities e WHERE e.category_id = c.id AND e.slug = 'portugal');

INSERT INTO entities (category_id, name, slug, entity_type, subtitle, short_description, description, display_order, is_featured, is_active, primary_color, secondary_color, background_color, text_color)
SELECT c.id, 'AgusMA Studio', 'agusma-studio', 'studio', 'Studio entity', 'Studio-led fashion concepts and wearable essentials.', 'AgusMA Studio hosts fashion collections and studio-native apparel concepts under the new architecture.', 1, 1, 1, '#111111', '#ded8cf', '#faf9f6', '#111111'
FROM categories c
WHERE c.slug = 'fashion'
  AND NOT EXISTS (SELECT 1 FROM entities e WHERE e.category_id = c.id AND e.slug = 'agusma-studio');

INSERT INTO entities (category_id, name, slug, entity_type, subtitle, short_description, description, display_order, is_featured, is_active, primary_color, secondary_color, background_color, text_color)
SELECT c.id, 'AgusMA Studio Lab', 'agusma-studio-lab', 'concept', 'Studio lab entity', 'Limited capsules, experiments and collectible special releases.', 'AgusMA Studio Lab groups the most experimental and special-edition releases under a single reusable entity.', 1, 1, 1, '#2a2520', '#d4c4aa', '#f3eee7', '#111111'
FROM categories c
WHERE c.slug = 'special-editions'
  AND NOT EXISTS (SELECT 1 FROM entities e WHERE e.category_id = c.id AND e.slug = 'agusma-studio-lab');

INSERT INTO collections (entity_id, name, slug, subtitle, collection_year, season, short_description, description, concept, image_variant, layout_style, display_order, is_featured, is_active, published_at, primary_color, secondary_color, background_color, text_color)
SELECT e.id, 'Real Madrid 2026/27', 'real-madrid-2026-27', 'Season system', 2026, '2026/27', 'A full seasonal kit program for Real Madrid.', 'Real Madrid 2026/27 structures the club into a complete collection architecture with separate pieces for each kit expression.', 'A collectible seasonal system balancing heritage, clarity and editorial restraint.', 'light', 'standard', 1, 1, 1, NOW(), '#d9c7a4', '#ffffff', '#f7f1e8', '#111111'
FROM entities e
WHERE e.slug = 'real-madrid'
  AND NOT EXISTS (SELECT 1 FROM collections c WHERE c.entity_id = e.id AND c.slug = 'real-madrid-2026-27');

INSERT INTO collections (entity_id, name, slug, subtitle, collection_year, season, short_description, description, concept, image_variant, layout_style, display_order, is_featured, is_active, published_at, primary_color, secondary_color, background_color, text_color)
SELECT e.id, 'Cristiano Ronaldo Legacy Collection', 'cristiano-ronaldo-legacy-collection', 'Legacy capsule', 2026, NULL, 'A national-team capsule built around Portugal and the legacy narrative.', 'Cristiano Ronaldo Legacy Collection expands Football beyond clubs, framing Portugal as a national-team entity with its own collectible collection logic.', 'Legacy storytelling inside the Football category through the national_team entity type.', 'light', 'standard', 2, 1, 1, NOW(), '#8f1f32', '#d4af37', '#f7eceb', '#111111'
FROM entities e
WHERE e.slug = 'portugal'
  AND NOT EXISTS (SELECT 1 FROM collections c WHERE c.entity_id = e.id AND c.slug = 'cristiano-ronaldo-legacy-collection');

INSERT INTO collections (entity_id, name, slug, subtitle, collection_year, season, short_description, description, concept, image_variant, layout_style, display_order, is_featured, is_active, published_at, primary_color, secondary_color, background_color, text_color)
SELECT e.id, 'Essentials Collection', 'essentials-collection', 'Core apparel line', 2026, NULL, 'Studio-led garments designed as wearable archive staples.', 'Essentials Collection gives Fashion a reusable architecture for garments, separating the collection itself from each product piece.', 'Minimal wardrobe system with editorial treatment.', 'dark', 'standard', 1, 1, 1, NOW(), '#111111', '#d9d2c9', '#faf8f4', '#111111'
FROM entities e
WHERE e.slug = 'agusma-studio'
  AND NOT EXISTS (SELECT 1 FROM collections c WHERE c.entity_id = e.id AND c.slug = 'essentials-collection');

INSERT INTO collections (entity_id, name, slug, subtitle, collection_year, season, short_description, description, concept, image_variant, layout_style, display_order, is_featured, is_active, published_at, primary_color, secondary_color, background_color, text_color)
SELECT e.id, 'Atelier Archive Vol. I', 'atelier-archive-vol-i', 'Special edition capsule', 2026, NULL, 'A limited AgusMA Studio Lab release for collectible special editions.', 'Atelier Archive Vol. I anchors the Special Editions category with a reusable entity and piece hierarchy.', 'Edition-first storytelling, limited cadence and archive-oriented presentation.', 'light', 'editorial', 1, 1, 1, NOW(), '#312822', '#d4c4aa', '#f5efe7', '#111111'
FROM entities e
WHERE e.slug = 'agusma-studio-lab'
  AND NOT EXISTS (SELECT 1 FROM collections c WHERE c.entity_id = e.id AND c.slug = 'atelier-archive-vol-i');

INSERT INTO pieces (collection_id, name, slug, piece_type, short_description, description, display_order, is_featured, is_active)
SELECT c.id, 'Home Kit', 'home-kit', 'home_kit', 'Primary match kit.', 'Primary match kit for the Real Madrid 2026/27 collection.', 1, 1, 1
FROM collections c
WHERE c.slug = 'real-madrid-2026-27'
  AND NOT EXISTS (SELECT 1 FROM pieces p WHERE p.collection_id = c.id AND p.slug = 'home-kit');

INSERT INTO pieces (collection_id, name, slug, piece_type, short_description, description, display_order, is_featured, is_active)
SELECT c.id, 'Away Kit', 'away-kit', 'away_kit', 'Secondary match kit.', 'Secondary match kit for the Real Madrid 2026/27 collection.', 2, 0, 1
FROM collections c
WHERE c.slug = 'real-madrid-2026-27'
  AND NOT EXISTS (SELECT 1 FROM pieces p WHERE p.collection_id = c.id AND p.slug = 'away-kit');

INSERT INTO pieces (collection_id, name, slug, piece_type, short_description, description, display_order, is_featured, is_active)
SELECT c.id, 'Third Kit', 'third-kit', 'third_kit', 'Alternate match kit.', 'Third kit concept for the Real Madrid 2026/27 collection.', 3, 0, 1
FROM collections c
WHERE c.slug = 'real-madrid-2026-27'
  AND NOT EXISTS (SELECT 1 FROM pieces p WHERE p.collection_id = c.id AND p.slug = 'third-kit');

INSERT INTO pieces (collection_id, name, slug, piece_type, short_description, description, display_order, is_featured, is_active)
SELECT c.id, 'Goalkeeper Kit', 'goalkeeper-kit', 'goalkeeper_kit', 'Goalkeeper-specific kit.', 'Goalkeeper kit concept for the Real Madrid 2026/27 collection.', 4, 0, 1
FROM collections c
WHERE c.slug = 'real-madrid-2026-27'
  AND NOT EXISTS (SELECT 1 FROM pieces p WHERE p.collection_id = c.id AND p.slug = 'goalkeeper-kit');

INSERT INTO pieces (collection_id, name, slug, piece_type, short_description, description, display_order, is_featured, is_active)
SELECT c.id, 'Home Kit', 'home-kit', 'home_kit', 'Primary national-team kit.', 'Home kit concept inside the Cristiano Ronaldo Legacy Collection.', 1, 1, 1
FROM collections c
WHERE c.slug = 'cristiano-ronaldo-legacy-collection'
  AND NOT EXISTS (SELECT 1 FROM pieces p WHERE p.collection_id = c.id AND p.slug = 'home-kit');

INSERT INTO pieces (collection_id, name, slug, piece_type, short_description, description, display_order, is_featured, is_active)
SELECT c.id, 'Special Edition', 'special-edition', 'concept', 'Collectible legacy variation.', 'Special-edition piece expanding the Portugal narrative inside Football.', 2, 0, 1
FROM collections c
WHERE c.slug = 'cristiano-ronaldo-legacy-collection'
  AND NOT EXISTS (SELECT 1 FROM pieces p WHERE p.collection_id = c.id AND p.slug = 'special-edition');

INSERT INTO pieces (collection_id, name, slug, piece_type, short_description, description, display_order, is_featured, is_active)
SELECT c.id, 'Oversized T-shirt', 'oversized-t-shirt', 'shirt', 'Core oversized top.', 'Oversized T-shirt inside the AgusMA Studio Essentials Collection.', 1, 1, 1
FROM collections c
WHERE c.slug = 'essentials-collection'
  AND NOT EXISTS (SELECT 1 FROM pieces p WHERE p.collection_id = c.id AND p.slug = 'oversized-t-shirt');

INSERT INTO pieces (collection_id, name, slug, piece_type, short_description, description, display_order, is_featured, is_active)
SELECT c.id, 'Hoodie', 'hoodie', 'hoodie', 'Layering staple.', 'Hoodie inside the AgusMA Studio Essentials Collection.', 2, 0, 1
FROM collections c
WHERE c.slug = 'essentials-collection'
  AND NOT EXISTS (SELECT 1 FROM pieces p WHERE p.collection_id = c.id AND p.slug = 'hoodie');

INSERT INTO pieces (collection_id, name, slug, piece_type, short_description, description, display_order, is_featured, is_active)
SELECT c.id, 'Concept Poster', 'concept-poster', 'poster', 'Limited-edition print companion.', 'Poster-style collectible for Atelier Archive Vol. I.', 1, 1, 1
FROM collections c
WHERE c.slug = 'atelier-archive-vol-i'
  AND NOT EXISTS (SELECT 1 FROM pieces p WHERE p.collection_id = c.id AND p.slug = 'concept-poster');

INSERT INTO tags (name, slug)
SELECT 'Football', 'football'
WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'football');

INSERT INTO tags (name, slug)
SELECT 'Club', 'club'
WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'club');

INSERT INTO tags (name, slug)
SELECT 'National Team', 'national-team'
WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'national-team');

INSERT INTO tags (name, slug)
SELECT 'Fashion', 'fashion'
WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'fashion');

INSERT INTO tags (name, slug)
SELECT 'Special Edition', 'special-edition'
WHERE NOT EXISTS (SELECT 1 FROM tags WHERE slug = 'special-edition');

INSERT INTO collection_tags (collection_id, tag_id)
SELECT c.id, t.id
FROM collections c
INNER JOIN tags t ON t.slug IN ('football', 'club')
WHERE c.slug = 'real-madrid-2026-27'
  AND NOT EXISTS (SELECT 1 FROM collection_tags ct WHERE ct.collection_id = c.id AND ct.tag_id = t.id);

INSERT INTO collection_tags (collection_id, tag_id)
SELECT c.id, t.id
FROM collections c
INNER JOIN tags t ON t.slug IN ('football', 'national-team')
WHERE c.slug = 'cristiano-ronaldo-legacy-collection'
  AND NOT EXISTS (SELECT 1 FROM collection_tags ct WHERE ct.collection_id = c.id AND ct.tag_id = t.id);

INSERT INTO collection_tags (collection_id, tag_id)
SELECT c.id, t.id
FROM collections c
INNER JOIN tags t ON t.slug = 'fashion'
WHERE c.slug = 'essentials-collection'
  AND NOT EXISTS (SELECT 1 FROM collection_tags ct WHERE ct.collection_id = c.id AND ct.tag_id = t.id);

INSERT INTO collection_tags (collection_id, tag_id)
SELECT c.id, t.id
FROM collections c
INNER JOIN tags t ON t.slug = 'special-edition'
WHERE c.slug = 'atelier-archive-vol-i'
  AND NOT EXISTS (SELECT 1 FROM collection_tags ct WHERE ct.collection_id = c.id AND ct.tag_id = t.id);