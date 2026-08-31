-- Draft / Preview / Publish for collections
ALTER TABLE collections
  ADD COLUMN preview_token CHAR(64) NULL AFTER published_at;

UPDATE collections
SET preview_token = SHA2(CONCAT(UUID(), '-', id, '-', RAND()), 256)
WHERE preview_token IS NULL OR preview_token = '';

ALTER TABLE collections
  MODIFY preview_token CHAR(64) NOT NULL,
  ADD UNIQUE KEY unique_collections_preview_token (preview_token);
