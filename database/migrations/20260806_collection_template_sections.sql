USE agusma_studio;

START TRANSACTION;

ALTER TABLE collection_templates
  ADD COLUMN IF NOT EXISTS display_order INT UNSIGNED NOT NULL DEFAULT 0 AFTER preview_image;

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

-- The old variant layer is no longer used. Collections retain template_id only
-- as provenance; their copied collection_sections remain fully independent.
SET @variant_fk_exists := (
  SELECT COUNT(*)
  FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND TABLE_NAME = 'collections'
    AND CONSTRAINT_NAME = 'fk_collection_template_variant'
    AND CONSTRAINT_TYPE = 'FOREIGN KEY'
);
SET @sql := IF(
  @variant_fk_exists > 0,
  'ALTER TABLE collections DROP FOREIGN KEY fk_collection_template_variant',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @variant_index_exists := (
  SELECT COUNT(*)
  FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'collections'
    AND INDEX_NAME = 'fk_collection_template_variant'
);
SET @sql := IF(
  @variant_index_exists > 0,
  'ALTER TABLE collections DROP INDEX fk_collection_template_variant',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @variant_column_exists := (
  SELECT COUNT(*)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'collections'
    AND COLUMN_NAME = 'template_variant_id'
);
SET @sql := IF(
  @variant_column_exists > 0,
  'ALTER TABLE collections DROP COLUMN template_variant_id',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @template_config_exists := (
  SELECT COUNT(*)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'collections'
    AND COLUMN_NAME = 'template_config'
);
SET @sql := IF(
  @template_config_exists > 0,
  'ALTER TABLE collections DROP COLUMN template_config',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

DROP TABLE IF EXISTS collection_template_variants;

-- Old templates based only on variants have no reusable section rows yet.
-- Keep them as drafts so they cannot generate an unexpectedly empty collection.
UPDATE collection_templates t
SET t.is_active = 0
WHERE NOT EXISTS (
  SELECT 1
  FROM collection_template_sections ts
  WHERE ts.template_id = t.id
);

COMMIT;
