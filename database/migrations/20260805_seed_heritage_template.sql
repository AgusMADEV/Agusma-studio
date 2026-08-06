USE agusma_studio;

START TRANSACTION;

INSERT INTO collection_templates (name, slug, description, preview_image, is_active)
SELECT
  'Heritage Template',
  'heritage-template',
  'Editorial template built for commemorative collection pages with a heritage narrative, timeline, gallery and feature-story sections.',
  './assets/images/hero-125.png',
  1
WHERE NOT EXISTS (
  SELECT 1
  FROM collection_templates
  WHERE slug = 'heritage-template'
);

INSERT INTO collection_template_variants (
  template_id,
  name,
  slug,
  description,
  preview_image,
  default_config,
  is_active
)
SELECT
  t.id,
  'Narrative',
  'narrative',
  'Story-first variant with a hero manifesto, legacy timeline, editorial gallery, feature icons and a closing banner.',
  './assets/images/hero-125.png',
  '{
    "theme": {
      "tone": "heritage",
      "surface": "ivory",
      "accent": "sky-blue",
      "ornament": "gold",
      "imageStyle": "duotone-archive"
    },
    "header": {
      "style": "minimal-editorial",
      "showSearch": true,
      "showAccount": true
    },
    "sections": [
      {
        "key": "hero",
        "type": "split-hero",
        "label": "01",
        "headlineField": "name",
        "bodyField": "concept",
        "cta": {
          "label": "Explora colección",
          "target": "gallery"
        },
        "media": {
          "sectionKey": "hero",
          "fallback": "./assets/images/hero-125.png"
        }
      },
      {
        "key": "timeline",
        "type": "milestone-rail",
        "label": "02",
        "eyebrow": "Nuestra historia",
        "items": [
          {
            "year": "1902",
            "title": "Nace el club"
          },
          {
            "year": "1956",
            "title": "La realeza toma forma"
          },
          {
            "year": "2000",
            "title": "Una era dorada"
          },
          {
            "year": "2027",
            "title": "125 años de legado"
          }
        ]
      },
      {
        "key": "gallery",
        "type": "editorial-grid",
        "label": "03",
        "eyebrow": "La camiseta del 125 aniversario",
        "columns": 4,
        "source": "collection-media",
        "fallbackMedia": [
          "./assets/images/125an.png",
          "./assets/images/125anback.png"
        ]
      },
      {
        "key": "features",
        "type": "icon-features",
        "label": "04",
        "eyebrow": "Detalles que cuentan quiénes somos",
        "items": [
          {
            "title": "Emblema exclusivo",
            "description": "125 años"
          },
          {
            "title": "AERO READY",
            "description": "Tejido técnico"
          },
          {
            "title": "Entrega personalizada",
            "description": "Edición limitada"
          },
          {
            "title": "Tecnología heat.rdy",
            "description": "Ligereza y rendimiento"
          }
        ]
      },
      {
        "key": "closing-banner",
        "type": "split-banner",
        "label": "05",
        "headline": "El futuro comienza aquí",
        "media": {
          "fallback": "./assets/images/hero-125.png"
        }
      }
    ]
  }',
  1
FROM collection_templates t
WHERE t.slug = 'heritage-template'
  AND NOT EXISTS (
    SELECT 1
    FROM collection_template_variants v
    WHERE v.template_id = t.id
      AND v.slug = 'narrative'
  );

COMMIT;