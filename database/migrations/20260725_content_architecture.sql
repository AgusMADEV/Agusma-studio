USE agusma_studio;

START TRANSACTION;

ALTER TABLE categories
  ADD COLUMN IF NOT EXISTS short_description VARCHAR(255) NULL AFTER slug,
  ADD COLUMN IF NOT EXISTS description TEXT NULL AFTER short_description,
  ADD COLUMN IF NOT EXISTS cover_image VARCHAR(255) NULL AFTER visual_key,
  ADD COLUMN IF NOT EXISTS hero_image VARCHAR(255) NULL AFTER cover_image;

UPDATE categories
SET short_description = CASE slug
      WHEN 'football' THEN 'Club football, national teams and collectible kit concepts.'
      WHEN 'fashion' THEN 'Editorial garments, styling systems and wearable concepts.'
      WHEN 'special-editions' THEN 'Limited releases and experimental collectible concepts.'
      ELSE short_description
    END,
    description = CASE slug
      WHEN 'football' THEN 'Football gathers club projects, national team narratives and archive-driven kit systems inside a single category.'
      WHEN 'fashion' THEN 'Fashion collects AgusMA Studio garment concepts and styling-led editorial explorations.'
      WHEN 'special-editions' THEN 'Special Editions focuses on rarer releases, collectible drops and exceptional concept capsules.'
      ELSE description
    END,
    cover_image = CASE slug
      WHEN 'football' THEN COALESCE(cover_image, './assets/images/cat-football2.png')
      WHEN 'fashion' THEN COALESCE(cover_image, './assets/images/cat-fashion.png')
      WHEN 'special-editions' THEN COALESCE(cover_image, './assets/images/cat-special.png')
      ELSE cover_image
    END,
    hero_image = CASE slug
      WHEN 'football' THEN COALESCE(hero_image, './assets/images/hero-bg3b.svg')
      WHEN 'fashion' THEN COALESCE(hero_image, cover_image, './assets/images/cat-fashion.png')
      WHEN 'special-editions' THEN COALESCE(hero_image, cover_image, './assets/images/cat-special.png')
      ELSE COALESCE(hero_image, cover_image)
    END,
    link_url = CASE slug
      WHEN 'football' THEN './football.php'
      WHEN 'fashion' THEN './fashion.php'
      WHEN 'special-editions' THEN './special-editions.php'
      ELSE link_url
    END
WHERE slug IN ('football', 'fashion', 'special-editions');

UPDATE categories
SET is_active = 0,
    link_url = '#',
    short_description = COALESCE(short_description, 'Legacy row kept only for historical migration.'),
    description = COALESCE(description, 'National Teams has been absorbed into Football entities using entity_type = national_team.')
WHERE slug = 'national-teams';

INSERT INTO categories (name, slug, short_description, description, visual_key, cover_image, hero_image, link_url, display_order, is_active)
SELECT 'Football', 'football', 'Club football, national teams and collectible kit concepts.', 'Football gathers club projects, national team narratives and archive-driven kit systems inside a single category.', 'football', './assets/images/cat-football2.png', './assets/images/hero-bg3b.svg', './football.php', 1, 1
WHERE NOT EXISTS (
  SELECT 1 FROM categories WHERE slug = 'football'
);

INSERT INTO categories (name, slug, short_description, description, visual_key, cover_image, hero_image, link_url, display_order, is_active)
SELECT 'Fashion', 'fashion', 'Editorial garments, styling systems and wearable concepts.', 'Fashion collects AgusMA Studio garment concepts and styling-led editorial explorations.', 'fashion', './assets/images/cat-fashion.png', './assets/images/cat-fashion.png', './fashion.php', 2, 1
WHERE NOT EXISTS (
  SELECT 1 FROM categories WHERE slug = 'fashion'
);

INSERT INTO categories (name, slug, short_description, description, visual_key, cover_image, hero_image, link_url, display_order, is_active)
SELECT 'Special Editions', 'special-editions', 'Limited releases and experimental collectible concepts.', 'Special Editions focuses on rarer releases, collectible drops and exceptional concept capsules.', 'special-editions', './assets/images/cat-special.png', './assets/images/cat-special.png', './special-editions.php', 3, 1
WHERE NOT EXISTS (
  SELECT 1 FROM categories WHERE slug = 'special-editions'
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
  CONSTRAINT fk_collections_entity_id
    FOREIGN KEY (entity_id) REFERENCES entities (id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
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

INSERT INTO entities (category_id, name, slug, entity_type, subtitle, short_description, description, cover_image, primary_color, secondary_color, background_color, text_color, display_order, is_featured, is_active)
SELECT c.id, 'Real Madrid', 'real-madrid', 'club', 'Club entity', 'Historic club concepts and seasonal kit systems.', 'Real Madrid acts as the flagship club entity inside Football, ready to host full seasonal collections and individual pieces.', NULL, '#d9c7a4', '#ffffff', '#f6f1e6', '#111111', 1, 1, 1
FROM categories c
WHERE c.slug = 'football'
  AND NOT EXISTS (
    SELECT 1 FROM entities e WHERE e.category_id = c.id AND e.slug = 'real-madrid'
  );

INSERT INTO entities (category_id, name, slug, entity_type, subtitle, short_description, description, cover_image, primary_color, secondary_color, background_color, text_color, display_order, is_featured, is_active)
SELECT c.id, 'Portugal', 'portugal', 'national_team', 'National team entity', 'National team capsules and player-led editorial collections.', 'Portugal represents the national-team branch inside Football and replaces National Teams as a standalone category.', NULL, '#9f2033', '#d4af37', '#f5ece8', '#111111', 2, 1, 1
FROM categories c
WHERE c.slug = 'football'
  AND NOT EXISTS (
    SELECT 1 FROM entities e WHERE e.category_id = c.id AND e.slug = 'portugal'
  );

INSERT INTO entities (category_id, name, slug, entity_type, subtitle, short_description, description, cover_image, display_order, is_featured, is_active)
SELECT c.id, 'Football Archive', 'football-archive', 'concept', 'Legacy migration entity', 'Preserves legacy featured collections formerly attached directly to Football.', 'Football Archive receives migrated legacy featured collections so no historical records are lost during the architecture transition.', NULL, 90, 0, 1
FROM categories c
WHERE c.slug = 'football'
  AND NOT EXISTS (
    SELECT 1 FROM entities e WHERE e.category_id = c.id AND e.slug = 'football-archive'
  );

INSERT INTO entities (category_id, name, slug, entity_type, subtitle, short_description, description, cover_image, display_order, is_featured, is_active)
SELECT c.id, 'National Team Archive', 'national-team-archive', 'national_team', 'Legacy migration entity', 'Preserves legacy national-team collections inside Football.', 'National Team Archive holds historical items migrated from the legacy National Teams category while the new public model uses Football entities.', NULL, 91, 0, 1
FROM categories c
WHERE c.slug = 'football'
  AND NOT EXISTS (
    SELECT 1 FROM entities e WHERE e.category_id = c.id AND e.slug = 'national-team-archive'
  );

INSERT INTO entities (category_id, name, slug, entity_type, subtitle, short_description, description, cover_image, primary_color, secondary_color, background_color, text_color, display_order, is_featured, is_active)
SELECT c.id, 'AgusMA Studio', 'agusma-studio', 'studio', 'Studio entity', 'Studio-led fashion concepts and wearable essentials.', 'AgusMA Studio hosts fashion collections and studio-native apparel concepts under the new architecture.', NULL, '#111111', '#ded8cf', '#faf9f6', '#111111', 1, 1, 1
FROM categories c
WHERE c.slug = 'fashion'
  AND NOT EXISTS (
    SELECT 1 FROM entities e WHERE e.category_id = c.id AND e.slug = 'agusma-studio'
  );

INSERT INTO entities (category_id, name, slug, entity_type, subtitle, short_description, description, cover_image, primary_color, secondary_color, background_color, text_color, display_order, is_featured, is_active)
SELECT c.id, 'AgusMA Studio Lab', 'agusma-studio-lab', 'concept', 'Studio lab entity', 'Limited capsules, experiments and collectible special releases.', 'AgusMA Studio Lab groups the most experimental and special-edition releases under a single reusable entity.', NULL, '#2a2520', '#d4c4aa', '#f3eee7', '#111111', 1, 1, 1
FROM categories c
WHERE c.slug = 'special-editions'
  AND NOT EXISTS (
    SELECT 1 FROM entities e WHERE e.category_id = c.id AND e.slug = 'agusma-studio-lab'
  );

INSERT INTO collections (
  entity_id, name, slug, subtitle, collection_year, season, short_description, description, concept,
  cover_image, thumbnail_image, primary_color, secondary_color, background_color, text_color,
  image_variant, layout_style, display_order, is_featured, is_active, published_at
)
SELECT e.id, fc.title,
       LOWER(
         REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(fc.title, '.', ''), '/', '-'), ' ', '-'), '''', ''), '&', 'and')
       ) AS legacy_slug,
       'Migrated legacy collection',
       fc.collection_year,
       NULL,
       'Imported from legacy featured_collections.',
       'This collection was migrated automatically from the legacy featured_collections table to preserve historical data.',
       NULL,
       NULL,
       NULL,
       NULL,
       NULL,
       NULL,
       NULL,
       fc.image_variant,
       'standard',
       fc.display_order,
       fc.is_active,
       fc.is_active,
       NOW()
FROM featured_collections fc
LEFT JOIN categories legacy_category ON legacy_category.id = fc.category_id
INNER JOIN categories target_category ON target_category.slug = CASE
  WHEN legacy_category.slug = 'national-teams' THEN 'football'
  ELSE legacy_category.slug
END
INNER JOIN entities e ON e.category_id = target_category.id AND e.slug = CASE
  WHEN legacy_category.slug = 'football' THEN 'football-archive'
  WHEN legacy_category.slug = 'national-teams' THEN 'national-team-archive'
  WHEN legacy_category.slug = 'fashion' THEN 'agusma-studio'
  WHEN legacy_category.slug = 'special-editions' THEN 'agusma-studio-lab'
  ELSE 'football-archive'
END
WHERE NOT EXISTS (
  SELECT 1 FROM collections c
  WHERE c.entity_id = e.id
    AND c.slug = LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(fc.title, '.', ''), '/', '-'), ' ', '-'), '''', ''), '&', 'and'))
);

INSERT INTO collections (
  entity_id, name, slug, subtitle, collection_year, season, short_description, description, concept,
  cover_image, thumbnail_image, primary_color, secondary_color, background_color, text_color,
  image_variant, layout_style, display_order, is_featured, is_active, published_at
)
SELECT e.id, 'Real Madrid 2026/27', 'real-madrid-2026-27', 'Season system', 2026, '2026/27',
       'A full seasonal kit program for Real Madrid.',
       'Real Madrid 2026/27 structures the club into a complete collection architecture with separate pieces for each kit expression.',
       'A collectible seasonal system balancing heritage, clarity and editorial restraint.',
       NULL, NULL, '#d9c7a4', '#ffffff', '#f7f1e8', '#111111', 'light', 'standard', 1, 1, 1, NOW()
FROM entities e
WHERE e.slug = 'real-madrid'
  AND NOT EXISTS (
    SELECT 1 FROM collections c WHERE c.entity_id = e.id AND c.slug = 'real-madrid-2026-27'
  );

INSERT INTO collections (
  entity_id, name, slug, subtitle, collection_year, season, short_description, description, concept,
  cover_image, thumbnail_image, primary_color, secondary_color, background_color, text_color,
  image_variant, layout_style, display_order, is_featured, is_active, published_at
)
SELECT e.id, 'Cristiano Ronaldo Legacy Collection', 'cristiano-ronaldo-legacy-collection', 'Legacy capsule', 2026, NULL,
       'A national-team capsule built around Portugal and the legacy narrative.',
       'Cristiano Ronaldo Legacy Collection expands Football beyond clubs, framing Portugal as a national-team entity with its own collectible collection logic.',
       'Legacy storytelling inside the Football category through the national_team entity type.',
       NULL, NULL, '#8f1f32', '#d4af37', '#f7eceb', '#111111', 'light', 'standard', 2, 1, 1, NOW()
FROM entities e
WHERE e.slug = 'portugal'
  AND NOT EXISTS (
    SELECT 1 FROM collections c WHERE c.entity_id = e.id AND c.slug = 'cristiano-ronaldo-legacy-collection'
  );

INSERT INTO collections (
  entity_id, name, slug, subtitle, collection_year, season, short_description, description, concept,
  cover_image, thumbnail_image, primary_color, secondary_color, background_color, text_color,
  image_variant, layout_style, display_order, is_featured, is_active, published_at
)
SELECT e.id, 'Essentials Collection', 'essentials-collection', 'Core apparel line', 2026, NULL,
       'Studio-led garments designed as wearable archive staples.',
       'Essentials Collection gives Fashion a reusable architecture for garments, separating the collection itself from each product piece.',
       'Minimal wardrobe system with editorial treatment.',
       NULL, NULL, '#111111', '#d9d2c9', '#faf8f4', '#111111', 'dark', 'standard', 1, 1, 1, NOW()
FROM entities e
WHERE e.slug = 'agusma-studio'
  AND NOT EXISTS (
    SELECT 1 FROM collections c WHERE c.entity_id = e.id AND c.slug = 'essentials-collection'
  );

INSERT INTO collections (
  entity_id, name, slug, subtitle, collection_year, season, short_description, description, concept,
  cover_image, thumbnail_image, primary_color, secondary_color, background_color, text_color,
  image_variant, layout_style, display_order, is_featured, is_active, published_at
)
SELECT e.id, 'Atelier Archive Vol. I', 'atelier-archive-vol-i', 'Special edition capsule', 2026, NULL,
       'A limited AgusMA Studio Lab release for collectible special editions.',
       'Atelier Archive Vol. I anchors the Special Editions category with a reusable entity and piece hierarchy.',
       'Edition-first storytelling, limited cadence and archive-oriented presentation.',
       NULL, NULL, '#312822', '#d4c4aa', '#f5efe7', '#111111', 'light', 'editorial', 1, 1, 1, NOW()
FROM entities e
WHERE e.slug = 'agusma-studio-lab'
  AND NOT EXISTS (
    SELECT 1 FROM collections c WHERE c.entity_id = e.id AND c.slug = 'atelier-archive-vol-i'
  );

INSERT INTO pieces (collection_id, name, slug, piece_type, subtitle, short_description, description, cover_image, display_order, is_featured, is_active)
SELECT c.id, 'Home Kit', 'home-kit', 'home_kit', NULL, 'Primary match kit.', 'Primary match kit for the Real Madrid 2026/27 collection.', NULL, 1, 1, 1
FROM collections c
WHERE c.slug = 'real-madrid-2026-27'
  AND NOT EXISTS (SELECT 1 FROM pieces p WHERE p.collection_id = c.id AND p.slug = 'home-kit');

INSERT INTO pieces (collection_id, name, slug, piece_type, subtitle, short_description, description, cover_image, display_order, is_featured, is_active)
SELECT c.id, 'Away Kit', 'away-kit', 'away_kit', NULL, 'Secondary match kit.', 'Secondary match kit for the Real Madrid 2026/27 collection.', NULL, 2, 0, 1
FROM collections c
WHERE c.slug = 'real-madrid-2026-27'
  AND NOT EXISTS (SELECT 1 FROM pieces p WHERE p.collection_id = c.id AND p.slug = 'away-kit');

INSERT INTO pieces (collection_id, name, slug, piece_type, subtitle, short_description, description, cover_image, display_order, is_featured, is_active)
SELECT c.id, 'Third Kit', 'third-kit', 'third_kit', NULL, 'Alternate match kit.', 'Third kit concept for the Real Madrid 2026/27 collection.', NULL, 3, 0, 1
FROM collections c
WHERE c.slug = 'real-madrid-2026-27'
  AND NOT EXISTS (SELECT 1 FROM pieces p WHERE p.collection_id = c.id AND p.slug = 'third-kit');

INSERT INTO pieces (collection_id, name, slug, piece_type, subtitle, short_description, description, cover_image, display_order, is_featured, is_active)
SELECT c.id, 'Goalkeeper Kit', 'goalkeeper-kit', 'goalkeeper_kit', NULL, 'Goalkeeper-specific kit.', 'Goalkeeper kit concept for the Real Madrid 2026/27 collection.', NULL, 4, 0, 1
FROM collections c
WHERE c.slug = 'real-madrid-2026-27'
  AND NOT EXISTS (SELECT 1 FROM pieces p WHERE p.collection_id = c.id AND p.slug = 'goalkeeper-kit');

INSERT INTO pieces (collection_id, name, slug, piece_type, subtitle, short_description, description, cover_image, display_order, is_featured, is_active)
SELECT c.id, 'Home Kit', 'home-kit', 'home_kit', NULL, 'Primary national-team kit.', 'Home kit concept inside the Cristiano Ronaldo Legacy Collection.', NULL, 1, 1, 1
FROM collections c
WHERE c.slug = 'cristiano-ronaldo-legacy-collection'
  AND NOT EXISTS (SELECT 1 FROM pieces p WHERE p.collection_id = c.id AND p.slug = 'home-kit');

INSERT INTO pieces (collection_id, name, slug, piece_type, subtitle, short_description, description, cover_image, display_order, is_featured, is_active)
SELECT c.id, 'Special Edition', 'special-edition', 'concept', NULL, 'Collectible legacy variation.', 'Special-edition piece expanding the Portugal narrative inside Football.', NULL, 2, 0, 1
FROM collections c
WHERE c.slug = 'cristiano-ronaldo-legacy-collection'
  AND NOT EXISTS (SELECT 1 FROM pieces p WHERE p.collection_id = c.id AND p.slug = 'special-edition');

INSERT INTO pieces (collection_id, name, slug, piece_type, subtitle, short_description, description, cover_image, display_order, is_featured, is_active)
SELECT c.id, 'Oversized T-shirt', 'oversized-t-shirt', 'shirt', NULL, 'Core oversized top.', 'Oversized T-shirt inside the AgusMA Studio Essentials Collection.', NULL, 1, 1, 1
FROM collections c
WHERE c.slug = 'essentials-collection'
  AND NOT EXISTS (SELECT 1 FROM pieces p WHERE p.collection_id = c.id AND p.slug = 'oversized-t-shirt');

INSERT INTO pieces (collection_id, name, slug, piece_type, subtitle, short_description, description, cover_image, display_order, is_featured, is_active)
SELECT c.id, 'Hoodie', 'hoodie', 'hoodie', NULL, 'Layering staple.', 'Hoodie inside the AgusMA Studio Essentials Collection.', NULL, 2, 0, 1
FROM collections c
WHERE c.slug = 'essentials-collection'
  AND NOT EXISTS (SELECT 1 FROM pieces p WHERE p.collection_id = c.id AND p.slug = 'hoodie');

INSERT INTO pieces (collection_id, name, slug, piece_type, subtitle, short_description, description, cover_image, display_order, is_featured, is_active)
SELECT c.id, 'Concept Poster', 'concept-poster', 'poster', NULL, 'Limited-edition print companion.', 'Poster-style collectible for Atelier Archive Vol. I.', NULL, 1, 1, 1
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
  AND NOT EXISTS (
    SELECT 1 FROM collection_tags ct WHERE ct.collection_id = c.id AND ct.tag_id = t.id
  );

INSERT INTO collection_tags (collection_id, tag_id)
SELECT c.id, t.id
FROM collections c
INNER JOIN tags t ON t.slug IN ('football', 'national-team')
WHERE c.slug = 'cristiano-ronaldo-legacy-collection'
  AND NOT EXISTS (
    SELECT 1 FROM collection_tags ct WHERE ct.collection_id = c.id AND ct.tag_id = t.id
  );

INSERT INTO collection_tags (collection_id, tag_id)
SELECT c.id, t.id
FROM collections c
INNER JOIN tags t ON t.slug = 'fashion'
WHERE c.slug = 'essentials-collection'
  AND NOT EXISTS (
    SELECT 1 FROM collection_tags ct WHERE ct.collection_id = c.id AND ct.tag_id = t.id
  );

INSERT INTO collection_tags (collection_id, tag_id)
SELECT c.id, t.id
FROM collections c
INNER JOIN tags t ON t.slug = 'special-edition'
WHERE c.slug = 'atelier-archive-vol-i'
  AND NOT EXISTS (
    SELECT 1 FROM collection_tags ct WHERE ct.collection_id = c.id AND ct.tag_id = t.id
  );

COMMIT;