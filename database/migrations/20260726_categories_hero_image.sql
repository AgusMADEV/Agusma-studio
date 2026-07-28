USE agusma_studio;

ALTER TABLE categories
  ADD COLUMN IF NOT EXISTS hero_image VARCHAR(255) NULL AFTER cover_image;

UPDATE categories
SET hero_image = CASE slug
    WHEN 'football' THEN COALESCE(hero_image, './assets/images/hero-bg3b.svg', cover_image)
    WHEN 'fashion' THEN COALESCE(hero_image, cover_image, './assets/images/cat-fashion.png')
    WHEN 'special-editions' THEN COALESCE(hero_image, cover_image, './assets/images/cat-special.png')
    ELSE COALESCE(hero_image, cover_image)
  END
WHERE hero_image IS NULL OR hero_image = '';