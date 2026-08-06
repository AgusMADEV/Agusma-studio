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
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY unique_collection_section_key (collection_id, section_key),
  KEY idx_collection_sections_order (collection_id, is_active, display_order),
  CONSTRAINT fk_collection_sections_collection
    FOREIGN KEY (collection_id) REFERENCES collections(id)
    ON UPDATE CASCADE ON DELETE CASCADE
);
