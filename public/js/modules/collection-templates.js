import { buildEntityHref } from "./content-ui.js";

const templateRenderers = {
  "heritage-template:narrative": renderHeritageNarrativeTemplate,
  "heritage-template:monument": renderHeritageNarrativeTemplate,
  "heritage-template": renderHeritageNarrativeTemplate,
};

const heritageSectionRenderers = {
  "split-hero": renderHeritageSplitHero,
  "milestone-rail": renderHeritageMilestoneRail,
  "quote-block": renderHeritageQuoteBlock,
  "stat-grid": renderHeritageStatGrid,
  "editorial-grid": renderHeritageEditorialGrid,
  "icon-features": renderHeritageIconFeatures,
  "piece-list": renderHeritagePieceList,
  "split-banner": renderHeritageSplitBanner,
};

export function renderCollectionTemplate(root, payload, routeContext) {
  const renderer = templateRenderers[buildTemplateKey(payload.template, payload.template_variant)];

  if (!renderer) {
    return false;
  }

  renderer(root, payload, routeContext);
  return true;
}

function buildTemplateKey(template, templateVariant) {
  return `${template?.slug || ""}:${templateVariant?.slug || ""}`;
}

function renderHeritageNarrativeTemplate(root, payload, routeContext) {
  const { category = {}, entity = {}, collection = {}, template_variant: templateVariant = {}, pieces = [], media = [], tags = [] } = payload;
  const config = mergeTemplateConfig(collection, templateVariant);
  const configuredSections = Array.isArray(config.sections) ? config.sections : [];
  const sections = configuredSections.some((section) => section?.type === "piece-list")
    ? configuredSections
    : [...configuredSections, { key: "pieces", type: "piece-list", label: String(configuredSections.length + 1).padStart(2, "0") }];

  root.classList.add("collection-page--heritage");
  root.style.setProperty("--heritage-accent", collection.secondary_color || "#7A9FC6");
  root.style.setProperty("--heritage-surface", collection.background_color || "#f6f1e8");
  root.style.setProperty("--heritage-text", collection.text_color || "#111111");

  const renderContext = {
    category,
    entity,
    collection,
    media,
    pieces,
    tags,
    routeContext,
  };

  const markup = sections
    .map((section) => renderHeritageSection(section, renderContext))
    .filter((sectionMarkup) => sectionMarkup !== "")
    .join("");

  root.innerHTML = markup;
}

function renderHeritageSection(section, renderContext) {
  const renderer = heritageSectionRenderers[section?.type || ""];

  if (!renderer) {
    return "";
  }

  return renderer(section, renderContext);
}

function renderHeritageSplitHero(section, renderContext) {
  const { category, entity, collection, media, routeContext } = renderContext;
  const heading = readFieldValue(collection, section.headlineField) || collection.name || "Collection";
  const manifesto = readFieldValue(collection, section.bodyField)
    || collection.concept
    || collection.description
    || collection.short_description
    || "Una narrativa editorial construida desde legado, memoria y futuro.";
  const asideText = collection.description || collection.short_description || manifesto;
  const heroImage = findSectionMedia(
    media,
    section.media?.sectionKey || section.key || "hero",
    section.media?.fallback || collection.cover_image || collection.thumbnail_image,
  );
  const entityHref = buildEntityHref(routeContext.categorySlug || category.slug || "", routeContext.entitySlug || entity.slug || "");

  return `
    <section class="heritage-hero">
      ${createSectionIndex(section.label || "01")}
      <div class="heritage-hero__copy">
        <p class="heritage-hero__eyebrow">${escapeHtml(category.name || "Collection")}</p>
        <h1 class="heritage-hero__title">${escapeHtml(heading)}</h1>
        <p class="heritage-hero__manifesto">${escapeHtml(manifesto)}</p>
        <div class="heritage-hero__actions">
          <a href="#${escapeHtml(section.cta?.target || "heritage-gallery")}">${escapeHtml(section.cta?.label || "Explora colección")}</a>
          <a href="${escapeHtml(entityHref)}">Volver a la entidad</a>
        </div>
      </div>
      <div class="heritage-hero__aside">
        <p>${escapeHtml(entity.name || "Entity")}</p>
        <strong>${escapeHtml(asideText)}</strong>
      </div>
      <div class="heritage-hero__media${heroImage === "" ? " is-empty" : ""}">
        ${heroImage !== "" ? `<img src="${escapeHtml(heroImage)}" alt="${escapeHtml(heading)}" />` : `<div class="heritage-hero__placeholder">${escapeHtml(heading)}</div>`}
      </div>
    </section>
  `;
}

function renderHeritageMilestoneRail(section) {
  const items = Array.isArray(section.items) ? section.items : [];

  if (items.length === 0) {
    return "";
  }

  return `
    <section class="heritage-section heritage-section--timeline">
      ${createSectionIndex(section.label || "02")}
      <div class="heritage-section__header">
        <p class="heritage-section__eyebrow">${escapeHtml(section.eyebrow || "Nuestra historia")}</p>
      </div>
      <div class="heritage-timeline">
        ${items.map((item) => `<article class="heritage-timeline__item"><strong>${escapeHtml(item.year || "")}</strong><span>${escapeHtml(item.title || "")}</span></article>`).join("")}
      </div>
    </section>
  `;
}

function renderHeritageQuoteBlock(section, renderContext) {
  const { collection, entity } = renderContext;
  const quote = section.quote || collection.concept || collection.description || collection.short_description;

  if (String(quote || "").trim() === "") {
    return "";
  }

  return `
    <section class="heritage-section heritage-section--quote">
      ${createSectionIndex(section.label || "02")}
      <div class="heritage-quote-block">
        <p class="heritage-section__eyebrow">${escapeHtml(section.eyebrow || entity.name || "Manifiesto")}</p>
        <blockquote>${escapeHtml(quote)}</blockquote>
        ${section.caption ? `<span class="heritage-quote-block__caption">${escapeHtml(section.caption)}</span>` : ""}
      </div>
    </section>
  `;
}

function renderHeritageStatGrid(section) {
  const items = Array.isArray(section.items) ? section.items : [];

  if (items.length === 0) {
    return "";
  }

  return `
    <section class="heritage-section heritage-section--stats">
      ${createSectionIndex(section.label || "03")}
      <div class="heritage-section__header">
        <p class="heritage-section__eyebrow">${escapeHtml(section.eyebrow || "Datos clave")}</p>
      </div>
      <div class="heritage-stat-grid">
        ${items.map((item) => `<article class="heritage-stat-card"><strong>${escapeHtml(item.value || "")}</strong><span>${escapeHtml(item.label || "")}</span><p>${escapeHtml(item.detail || "")}</p></article>`).join("")}
      </div>
    </section>
  `;
}

function renderHeritageEditorialGrid(section, renderContext) {
  const { collection, pieces, media } = renderContext;
  const galleryItems = collectGalleryItems(collection, pieces, media, section);

  if (galleryItems.length === 0) {
    return "";
  }

  const columns = Math.max(2, Math.min(4, Number(section.columns || 4)));
  const sectionId = section.key === "gallery" ? "heritage-gallery" : `section-${escapeHtml(section.key || "gallery")}`;

  return `
    <section id="${sectionId}" class="heritage-section heritage-section--gallery">
      ${createSectionIndex(section.label || "03")}
      <div class="heritage-section__header">
        <p class="heritage-section__eyebrow">${escapeHtml(section.eyebrow || "La colección")}</p>
      </div>
      <div class="heritage-gallery heritage-gallery--${columns}">
        ${galleryItems.map((item) => `<figure class="heritage-gallery__item"><img src="${escapeHtml(item.src)}" alt="${escapeHtml(item.title || collection.name || "Collection")}" loading="lazy" /></figure>`).join("")}
      </div>
    </section>
  `;
}

function renderHeritageIconFeatures(section) {
  const items = Array.isArray(section.items) ? section.items : [];

  if (items.length === 0) {
    return "";
  }

  return `
    <section class="heritage-section heritage-section--features">
      ${createSectionIndex(section.label || "04")}
      <div class="heritage-section__header">
        <p class="heritage-section__eyebrow">${escapeHtml(section.eyebrow || "Detalles")}</p>
      </div>
      <div class="heritage-features">
        ${items.map((item) => `<article class="heritage-feature"><span class="heritage-feature__icon">${escapeHtml((item.title || "").slice(0, 2).toUpperCase() || "HT")}</span><h3>${escapeHtml(item.title || "")}</h3><p>${escapeHtml(item.description || "")}</p></article>`).join("")}
      </div>
    </section>
  `;
}

function renderHeritagePieceList(section, renderContext) {
  const { collection, pieces, tags } = renderContext;

  if (pieces.length === 0 && tags.length === 0) {
    return "";
  }

  return `
    <section class="heritage-section heritage-section--pieces">
      ${createSectionIndex(section.label || "05")}
      <div class="heritage-section__header heritage-section__header--split">
        <div>
          <p class="heritage-section__eyebrow">${escapeHtml(section.eyebrow || "Piezas")}</p>
          <h2>${escapeHtml(section.headline || collection.name || "Collection")}</h2>
        </div>
        <div class="heritage-tag-list">
          ${tags.map((tag) => `<span class="heritage-tag">${escapeHtml(tag.name || "")}</span>`).join("")}
        </div>
      </div>
      <div class="heritage-piece-list">
        ${pieces.map((piece) => `<article class="heritage-piece-card"><div><p class="heritage-piece-card__eyebrow">${escapeHtml(piece.piece_type || "piece")}</p><h3>${escapeHtml(piece.name || "")}</h3></div><p>${escapeHtml(piece.description || piece.short_description || "No hay descripción publicada para esta pieza.")}</p></article>`).join("")}
      </div>
    </section>
  `;
}

function renderHeritageSplitBanner(section, renderContext) {
  const { collection, media } = renderContext;
  const fallback = section.media?.fallback || collection.thumbnail_image || collection.cover_image;
  const image = findSectionMedia(media, section.media?.sectionKey || section.key || "closing-banner", fallback);

  return `
    <section class="heritage-closing">
      ${createSectionIndex(section.label || "06")}
      <div class="heritage-closing__copy">
        <h2>${escapeHtml(section.headline || collection.name || "Collection")}</h2>
      </div>
      <div class="heritage-closing__media${image === "" ? " is-empty" : ""}">
        ${image !== "" ? `<img src="${escapeHtml(image)}" alt="${escapeHtml(collection.name || "Collection")} closing image" loading="lazy" />` : ""}
      </div>
    </section>
  `;
}

function mergeTemplateConfig(collection, templateVariant) {
  const variantConfig = isObject(templateVariant?.default_config) ? templateVariant.default_config : {};
  const collectionConfig = isObject(collection?.template_config) ? collection.template_config : {};
  return deepMerge(variantConfig, collectionConfig);
}

function deepMerge(base, overrides) {
  if (Array.isArray(base) && Array.isArray(overrides)) {
    return overrides;
  }

  if (!isObject(base) || !isObject(overrides)) {
    return overrides;
  }

  const merged = { ...base };

  Object.entries(overrides).forEach(([key, value]) => {
    merged[key] = key in merged ? deepMerge(merged[key], value) : value;
  });

  return merged;
}

function collectGalleryItems(collection, pieces, media, section) {
  const items = [];

  const pushItem = (source, title = "") => {
    const normalized = normalizeAssetUrl(source);

    if (normalized === "" || items.some((item) => item.src === normalized)) {
      return;
    }

    items.push({ src: normalized, title });
  };

  if (Array.isArray(section.fallbackMedia)) {
    section.fallbackMedia.forEach((source) => pushItem(source, collection.name || "Collection"));
  }

  pushItem(collection.cover_image, collection.name || "Cover");
  pushItem(collection.thumbnail_image, `${collection.name || "Collection"} detail`);
  media.forEach((item) => pushItem(item.file_url, item.title || item.alt_text || collection.name || "Collection image"));
  pieces.forEach((piece) => {
    pushItem(piece.cover_image, piece.name || "Piece");
    if (Array.isArray(piece.media)) {
      piece.media.forEach((item) => pushItem(item.file_url, item.title || item.alt_text || piece.name || "Piece image"));
    }
  });

  return items.slice(0, Math.max(1, Number(section.columns || 4)));
}

function findSectionMedia(media, sectionKey, fallback = "") {
  const match = media.find((item) => item.section_key === sectionKey && normalizeAssetUrl(item.file_url) !== "");
  return normalizeAssetUrl(match?.file_url || fallback);
}

function readFieldValue(record, fieldName) {
  if (typeof fieldName !== "string" || fieldName.trim() === "") {
    return "";
  }

  return String(record?.[fieldName] || "").trim();
}

function createSectionIndex(label) {
  return `<div class="heritage-section-index"><span>${escapeHtml(label)}</span></div>`;
}

function getPublicAssetPrefix() {
  const { pathname } = window.location;
  const publicSegment = "/public/";
  const publicIndex = pathname.indexOf(publicSegment);

  if (publicIndex === -1) {
    return "/public/";
  }

  return pathname.slice(0, publicIndex + publicSegment.length);
}

function normalizeAssetUrl(value) {
  const source = String(value || "").trim();

  if (source === "") {
    return "";
  }

  if (source.startsWith("http://") || source.startsWith("https://") || source.startsWith("data:") || source.startsWith("/")) {
    return source;
  }

  if (source.startsWith("../")) {
    return new URL(source, window.location.href).pathname;
  }

  if (source.startsWith("./")) {
    return `${getPublicAssetPrefix()}${source.slice(2)}`;
  }

  return `${getPublicAssetPrefix()}${source.replace(/^\/+/, "")}`;
}

function escapeHtml(value) {
  return String(value ?? "")
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#39;");
}

function isObject(value) {
  return value !== null && typeof value === "object" && !Array.isArray(value);
}