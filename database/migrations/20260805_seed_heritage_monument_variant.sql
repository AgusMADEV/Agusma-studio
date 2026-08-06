USE agusma_studio;

START TRANSACTION;

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
  'Monument',
  'monument',
  'Monument-focused variant with a manifesto quote, key stats, gallery and reduced editorial rhythm.',
  './assets/images/hero-125.png',
  '{
    "theme": {
      "tone": "heritage",
      "surface": "ivory",
      "accent": "sky-blue",
      "ornament": "gold",
      "imageStyle": "archive-clean"
    },
    "sections": [
      {
        "key": "hero",
        "type": "split-hero",
        "label": "01",
        "headlineField": "name",
        "bodyField": "short_description",
        "cta": {
          "label": "Ver detalles",
          "target": "section-gallery"
        },
        "media": {
          "sectionKey": "hero",
          "fallback": "./assets/images/hero-125.png"
        }
      },
      {
        "key": "manifesto",
        "type": "quote-block",
        "label": "02",
        "eyebrow": "Manifiesto",
        "quote": "125 años convertidos en una pieza silenciosa, limpia y ceremonial.",
        "caption": "Legacy edition"
      },
      {
        "key": "stats",
        "type": "stat-grid",
        "label": "03",
        "eyebrow": "Datos clave",
        "items": [
          {
            "value": "1902",
            "label": "Origen",
            "detail": "Nacimiento del club"
          },
          {
            "value": "125",
            "label": "Aniversario",
            "detail": "Años de historia"
          },
          {
            "value": "01",
            "label": "Edición",
            "detail": "Capsule conmemorativa"
          },
          {
            "value": "RM",
            "label": "Sello",
            "detail": "Identidad central"
          }
        ]
      },
      {
        "key": "gallery",
        "type": "editorial-grid",
        "label": "04",
        "eyebrow": "Vista de producto",
        "columns": 3,
        "fallbackMedia": [
          "./assets/images/125an.png",
          "./assets/images/125anback.png",
          "./assets/images/hero-125.png"
        ]
      },
      {
        "key": "pieces",
        "type": "piece-list",
        "label": "05",
        "eyebrow": "Construcción de la edición",
        "headline": "Detalles de la colección"
      },
      {
        "key": "closing-banner",
        "type": "split-banner",
        "label": "06",
        "headline": "Un cierre construido para perdurar",
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
      AND v.slug = 'monument'
  );

COMMIT;