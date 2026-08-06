USE agusma_studio;

START TRANSACTION;

CREATE TABLE IF NOT EXISTS collection_templates (
  id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(60) NOT NULL,
  description VARCHAR(255) NULL,
  preview_image VARCHAR(255) NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY unique_collection_template_slug (slug)
);

CREATE TABLE IF NOT EXISTS collection_template_variants (
  id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  template_id SMALLINT UNSIGNED NOT NULL,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(80) NOT NULL,
  description VARCHAR(255) NULL,
  preview_image VARCHAR(255) NULL,
  default_config LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL CHECK (json_valid(default_config)),
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY unique_template_variant (template_id, slug)
);

ALTER TABLE collections
  ADD COLUMN IF NOT EXISTS template_id SMALLINT UNSIGNED NULL AFTER layout_style,
  ADD COLUMN IF NOT EXISTS template_variant_id SMALLINT UNSIGNED NULL AFTER template_id,
  ADD COLUMN IF NOT EXISTS template_config LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL CHECK (json_valid(template_config)) AFTER template_variant_id;

SET @variant_template_fk_exists := (
  SELECT COUNT(*)
  FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND TABLE_NAME = 'collection_template_variants'
    AND CONSTRAINT_NAME = 'fk_variant_template'
    AND CONSTRAINT_TYPE = 'FOREIGN KEY'
);

SET @sql := IF(
  @variant_template_fk_exists = 0,
  'ALTER TABLE collection_template_variants ADD CONSTRAINT fk_variant_template FOREIGN KEY (template_id) REFERENCES collection_templates (id) ON DELETE CASCADE ON UPDATE CASCADE',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @collections_template_index_exists := (
  SELECT COUNT(*)
  FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'collections'
    AND INDEX_NAME = 'idx_collections_template_id'
);

SET @sql := IF(
  @collections_template_index_exists = 0,
  'ALTER TABLE collections ADD KEY idx_collections_template_id (template_id)',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @collections_variant_index_exists := (
  SELECT COUNT(*)
  FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'collections'
    AND INDEX_NAME = 'fk_collection_template_variant'
);

SET @sql := IF(
  @collections_variant_index_exists = 0,
  'ALTER TABLE collections ADD KEY fk_collection_template_variant (template_variant_id)',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @collections_template_fk_exists := (
  SELECT COUNT(*)
  FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND TABLE_NAME = 'collections'
    AND CONSTRAINT_NAME = 'fk_collections_template_id'
    AND CONSTRAINT_TYPE = 'FOREIGN KEY'
);

SET @sql := IF(
  @collections_template_fk_exists = 0,
  'ALTER TABLE collections ADD CONSTRAINT fk_collections_template_id FOREIGN KEY (template_id) REFERENCES collection_templates (id) ON DELETE SET NULL ON UPDATE CASCADE',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @collections_variant_fk_exists := (
  SELECT COUNT(*)
  FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND TABLE_NAME = 'collections'
    AND CONSTRAINT_NAME = 'fk_collection_template_variant'
    AND CONSTRAINT_TYPE = 'FOREIGN KEY'
);

SET @sql := IF(
  @collections_variant_fk_exists = 0,
  'ALTER TABLE collections ADD CONSTRAINT fk_collection_template_variant FOREIGN KEY (template_variant_id) REFERENCES collection_template_variants (id) ON DELETE SET NULL ON UPDATE CASCADE',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

COMMIT;