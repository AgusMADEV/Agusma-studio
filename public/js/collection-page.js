import { fetchCollectionDetail } from "./modules/api.js";

const root = document.querySelector("[data-collection-page]");

if (root) {
  const categorySlug = root.dataset.categorySlug || "";
  const entitySlug = root.dataset.entitySlug || "";
  const collectionSlug = root.dataset.collectionSlug || "";

  const nameNode = root.querySelector("[data-collection-name]");
  const descriptionNode = root.querySelector("[data-collection-description]");
  const eyebrowNode = root.querySelector("[data-collection-eyebrow]");
  const entityNode = root.querySelector("[data-collection-entity]");
  const parentLink = root.querySelector("[data-collection-parent-link]");
  const primaryAction = root.querySelector("[data-collection-primary-action]");
  const heroPanel = root.querySelector("[data-collection-hero-panel]");
  const tagsNode = root.querySelector("[data-collection-tags]");
  const sectionsNode = root.querySelector("[data-collection-sections]");

  async function loadCollectionPage() {
    if (categorySlug === "" || entitySlug === "" || collectionSlug === "") {
      renderPageError("Faltan los slugs necesarios para cargar la colección.");
      return;
    }

    try {
      const payload = await fetchCollectionDetail({
        category: categorySlug,
        entity: entitySlug,
        collection: collectionSlug,
      });

      const category = payload.category || {};
      const entity = payload.entity || {};
      const collection = payload.collection || {};
      const pieces = Array.isArray(payload.pieces) ? payload.pieces : [];
      const media = Array.isArray(payload.media) ? payload.media : [];
      const tags = Array.isArray(payload.tags) ? payload.tags : [];
      const sections = Array.isArray(payload.sections) ? payload.sections : [];

      renderCollectionHeader({ category, entity, collection, tags, sections });
      renderCollectionSections({ sections, pieces, media });
    } catch (error) {
      console.error(error);
      renderPageError("No se pudo cargar la colección solicitada.");
    }
  }

  function renderCollectionHeader({ category, entity, collection, tags, sections }) {
    if (nameNode) {
      nameNode.textContent = collection.name || "Collection";
    }

    if (descriptionNode) {
      descriptionNode.textContent = collection.description
        || collection.short_description
        || "No hay descripción publicada para esta colección.";
    }

    if (eyebrowNode) {
      eyebrowNode.textContent = `${category.name || "Category"} · ${entity.name || "Entity"}`;
    }

    if (entityNode) {
      entityNode.textContent = entity.name || "Entity";
    }

    if (parentLink) {
      parentLink.href = `./entity.php?category=${encodeURIComponent(category.slug || categorySlug)}&entity=${encodeURIComponent(entity.slug || entitySlug)}`;
    }

    if (tagsNode) {
      tagsNode.replaceChildren(
        ...(tags.length > 0
          ? tags.map(createTag)
          : [createPlaceholderTag("No tags")]),
      );
    }

    const firstContentSection = sections.find((section) => section.section_type !== "intro") || sections[0];
    if (primaryAction && firstContentSection) {
      primaryAction.href = `#${sectionDomId(firstContentSection)}`;
    }

    applyCollectionAppearance(collection);

    const heroImage = collection.cover_image || collection.thumbnail_image || null;
    if (heroPanel && heroImage) {
      heroPanel.style.setProperty("--category-hero-visual", `url("${escapeCssUrl(heroImage)}")`);
      heroPanel.dataset.hasImage = "true";
    }
  }

  function applyCollectionAppearance(collection) {
    const layoutStyle = sanitizeClassName(collection.layout_style || "standard");

    root.dataset.layoutStyle = layoutStyle;
    root.classList.add(`collection-page--${layoutStyle}`);

    setOptionalCssVariable("--collection-primary", collection.primary_color);
    setOptionalCssVariable("--collection-secondary", collection.secondary_color);
    setOptionalCssVariable("--collection-background", collection.background_color);
    setOptionalCssVariable("--collection-text", collection.text_color);
  }

  function setOptionalCssVariable(name, value) {
    if (typeof value === "string" && value.trim() !== "") {
      root.style.setProperty(name, value.trim());
    }
  }

  function renderCollectionSections({ sections, pieces, media }) {
    if (!sectionsNode) return;

    sectionsNode.replaceChildren();

    if (sections.length === 0) {
      sectionsNode.append(createStatus("Esta colección todavía no tiene secciones publicadas."));
      return;
    }

    const fragment = document.createDocumentFragment();

    sections.forEach((section) => {
      const component = createSectionComponent(section, { pieces, media });
      if (component) fragment.append(component);
    });

    if (!fragment.hasChildNodes()) {
      sectionsNode.append(createStatus("No hay componentes compatibles para mostrar."));
      return;
    }

    sectionsNode.append(fragment);
  }

  function createSectionComponent(section, context) {
    const components = {
      intro: createIntroSection,
      pieces: createPiecesSection,
      gallery: createGallerySection,
      technical_details: createTechnicalDetailsSection,
      full_image: createFullImageSection,
    };

    const renderer = components[section.section_type] || createGenericSection;
    return renderer(section, context);
  }

  function createIntroSection(section) {
    const settings = getSectionSettings(section);
    const alignment = settingChoice(settings, "alignment", ["left", "center", "right"], "left");
    const contentWidth = settingChoice(settings, "content_width", ["narrow", "normal", "wide"], "normal");
    const spacing = settingChoice(settings, "spacing", ["compact", "normal", "large"], "normal");
    const variant = surfaceVariant(settings, "default");
    const element = createSectionElement(section, "intro");

    applySurfaceVariant(element, variant);
    element.classList.add(
      `collection-section--align-${alignment}`,
      `collection-section--content-${contentWidth}`,
      `collection-section--spacing-${spacing}`,
    );

    const content = document.createElement("div");
    content.className = "collection-intro__content";
    content.append(createSectionHeading(section));
    appendBody(content, section.body);
    element.append(content);

    return element;
  }

  function createPiecesSection(section, { pieces }) {
    const settings = getSectionSettings(section);
    const layout = settingChoice(settings, "layout", ["grid", "editorial", "list"], "grid");
    const columns = settingChoice(settings, "columns", ["2", "3", "4"], "2");
    const imageRatio = settingChoice(settings, "image_ratio", ["auto", "portrait", "square", "landscape"], "portrait");
    const gap = settingChoice(settings, "gap", ["compact", "normal", "wide"], "normal");
    const showDescription = settingBoolean(settings, "show_description", true);
    const showPieceType = settingBoolean(settings, "show_piece_type", true);
    const variant = surfaceVariant(settings, "default");
    const element = createSectionElement(section, "pieces");

    applySurfaceVariant(element, variant);
    element.append(createSectionHeading(section));

    if (pieces.length === 0) {
      element.append(createStatus("No hay piezas activas en esta colección."));
      return element;
    }

    const grid = document.createElement("div");
    grid.className = [
      "collection-piece-grid",
      `collection-piece-grid--${layout}`,
      `collection-grid--ratio-${imageRatio}`,
      `collection-grid--gap-${gap}`,
    ].join(" ");
    grid.style.setProperty("--section-columns", columns);
    grid.append(...pieces.map((piece) => createPieceCard(piece, { showDescription, showPieceType })));
    element.append(grid);

    return element;
  }

  function createGallerySection(section, { media }) {
    const settings = getSectionSettings(section);
    const layout = settingChoice(settings, "layout", ["grid", "editorial", "masonry", "carousel"], "editorial");
    const columns = settingChoice(settings, "columns", ["2", "3", "4"], "3");
    const imageRatio = settingChoice(settings, "image_ratio", ["auto", "portrait", "square", "landscape"], "auto");
    const gap = settingChoice(settings, "gap", ["compact", "normal", "wide"], "normal");
    const showCaptions = settingBoolean(settings, "show_captions", true);
    const variant = surfaceVariant(settings, "default");
    const element = createSectionElement(section, "gallery");

    applySurfaceVariant(element, variant);
    element.append(createSectionHeading(section));

    const sectionMedia = media.filter((item) => item.section_key === section.section_key);

    if (sectionMedia.length === 0) {
      element.append(createEmptyComponentMessage("Añade imágenes en media con section_key = ", section.section_key));
      return element;
    }

    const grid = document.createElement("div");
    grid.className = [
      "collection-gallery-grid",
      `collection-gallery-grid--${layout}`,
      `collection-grid--ratio-${imageRatio}`,
      `collection-grid--gap-${gap}`,
    ].join(" ");
    grid.style.setProperty("--section-columns", columns);
    grid.append(...sectionMedia.map((item) => createMediaCard(item, { showCaptions })));
    element.append(grid);

    return element;
  }

  function createTechnicalDetailsSection(section) {
    const settings = getSectionSettings(section);
    const layout = settingChoice(settings, "layout", ["split", "stacked", "cards"], "split");
    const columns = settingChoice(settings, "columns", ["1", "2", "3"], "2");
    const alignment = settingChoice(settings, "alignment", ["left", "center"], "left");
    const variant = surfaceVariant(settings, "dark", ["light", "dark", "primary", "secondary"]);
    const element = createSectionElement(section, "technical-details");

    applySurfaceVariant(element, variant);

    const card = document.createElement("div");
    card.className = [
      "collection-technical-card",
      `collection-technical-card--${layout}`,
      `collection-technical-card--align-${alignment}`,
    ].join(" ");
    card.style.setProperty("--section-columns", columns);
    card.append(createSectionHeading(section));

    const content = document.createElement("div");
    content.className = "collection-technical-content";
    content.append(...createBodyParagraphs(section.body, layout === "cards" ? "collection-technical-item" : "collection-section__body"));
    card.append(content);
    element.append(card);

    return element;
  }

  function createFullImageSection(section, { media }) {
    const settings = getSectionSettings(section);
    const height = settingChoice(settings, "height", ["auto", "half", "full"], "half");
    const fit = settingChoice(settings, "fit", ["cover", "contain"], "cover");
    const position = settingChoice(settings, "position", ["top", "center", "bottom"], "center");
    const overlay = settingChoice(settings, "overlay", ["none", "light", "dark"], "dark");
    const copyPosition = settingChoice(settings, "copy_position", ["bottom-left", "bottom-center", "center"], "bottom-left");
    const element = createSectionElement(section, "full-image");
    const sectionMedia = media.filter((item) => item.section_key === section.section_key);
    const featuredMedia = sectionMedia[0];

    if (!featuredMedia) {
      element.append(createSectionHeading(section));
      element.append(createEmptyComponentMessage("Añade una imagen en media con section_key = ", section.section_key));
      return element;
    }

    const figure = createMediaFigure(featuredMedia, [
      "collection-full-image",
      `collection-full-image--height-${height}`,
      `collection-full-image--fit-${fit}`,
      `collection-full-image--position-${position}`,
      `collection-full-image--overlay-${overlay}`,
      `collection-full-image--copy-${copyPosition}`,
    ].join(" "));

    if (section.eyebrow || section.title || (typeof section.body === "string" && section.body.trim() !== "")) {
      const copy = document.createElement("div");
      copy.className = "collection-full-image__copy";
      copy.append(createSectionHeading(section));
      appendBody(copy, section.body);
      figure.append(copy);
    }

    element.append(figure);
    return element;
  }

  function createGenericSection(section) {
    const element = createSectionElement(section, "generic");
    element.append(createSectionHeading(section));
    appendBody(element, section.body);
    return element;
  }

  function createSectionElement(section, modifier) {
    const element = document.createElement("section");
    element.id = sectionDomId(section);
    element.className = `collection-section collection-section--${modifier}`;
    element.dataset.sectionType = section.section_type || "generic";
    element.dataset.sectionKey = section.section_key || "";

    Object.entries(getSectionSettings(section)).forEach(([key, value]) => {
      if (["string", "number", "boolean"].includes(typeof value)) {
        element.dataset[toDatasetKey(key)] = String(value);
      }
    });

    return element;
  }

  function createSectionHeading(section) {
    const header = document.createElement("header");
    header.className = "collection-section__header";

    if (section.eyebrow) {
      const eyebrow = document.createElement("p");
      eyebrow.className = "category-section__eyebrow";
      eyebrow.textContent = section.eyebrow;
      header.append(eyebrow);
    }

    if (section.title) {
      const title = document.createElement("h2");
      title.textContent = section.title;
      header.append(title);
    }

    return header;
  }

  function appendBody(parent, body) {
    parent.append(...createBodyParagraphs(body));
  }

  function createBodyParagraphs(body, className = "collection-section__body") {
    if (typeof body !== "string" || body.trim() === "") return [];

    return body
      .split(/\n\s*\n/)
      .map((paragraph) => paragraph.trim())
      .filter(Boolean)
      .map((paragraph) => {
        const node = document.createElement("p");
        node.className = className;
        node.textContent = paragraph;
        return node;
      });
  }

  function createTag(tag) {
    const span = document.createElement("span");
    span.className = "collection-tag";
    span.textContent = tag.name;
    return span;
  }

  function createPlaceholderTag(label) {
    const span = document.createElement("span");
    span.className = "collection-tag";
    span.textContent = label;
    return span;
  }

  function createPieceCard(piece, { showDescription, showPieceType }) {
    const article = document.createElement("article");
    article.className = "collection-piece-card";

    const visualUrl = piece.cover_image
      || (Array.isArray(piece.media) ? piece.media.find((item) => item.is_cover)?.file_url : null)
      || (Array.isArray(piece.media) ? piece.media[0]?.file_url : null);

    if (visualUrl) {
      const visual = document.createElement("div");
      visual.className = "collection-piece-card__visual";

      const image = document.createElement("img");
      image.src = visualUrl;
      image.alt = piece.name || "Collection piece";
      image.loading = "lazy";
      visual.append(image);
      article.append(visual);
    }

    const body = document.createElement("div");
    body.className = "collection-piece-card__body";

    if (showPieceType) {
      const eyebrow = document.createElement("p");
      eyebrow.className = "category-section__eyebrow";
      eyebrow.textContent = formatPieceType(piece.piece_type || "piece");
      body.append(eyebrow);
    }

    const title = document.createElement("h3");
    title.className = "collection-piece-card__title";
    title.textContent = piece.name || "Piece";
    body.append(title);

    if (showDescription) {
      const description = document.createElement("p");
      description.className = "collection-piece-card__description";
      description.textContent = piece.description
        || piece.short_description
        || "No description published for this piece.";
      body.append(description);
    }

    article.append(body);
    return article;
  }

  function createMediaCard(media, { showCaptions }) {
    const article = document.createElement("article");
    article.className = "collection-media-card";

    const figure = createMediaFigure(media, "collection-media-card__visual");
    article.append(figure);

    if (showCaptions && (media.title || media.caption)) {
      const body = document.createElement("div");
      body.className = "collection-media-card__body";

      if (media.title) {
        const title = document.createElement("h3");
        title.className = "collection-media-card__title";
        title.textContent = media.title;
        body.append(title);
      }

      if (media.caption) {
        const caption = document.createElement("p");
        caption.className = "collection-media-card__description";
        caption.textContent = media.caption;
        body.append(caption);
      }

      article.append(body);
    }

    return article;
  }

  function createMediaFigure(media, className) {
    const figure = document.createElement("figure");
    figure.className = className;

    if (media.media_type === "video") {
      const video = document.createElement("video");
      video.src = media.file_url;
      video.controls = true;
      video.preload = "metadata";
      video.setAttribute("aria-label", media.alt_text || media.title || "Collection video");
      figure.append(video);
      return figure;
    }

    const image = document.createElement("img");
    image.src = media.thumbnail_url || media.file_url;
    image.alt = media.alt_text || media.title || "Collection image";
    image.loading = "lazy";
    figure.append(image);

    return figure;
  }

  function getSectionSettings(section) {
    return section.settings && typeof section.settings === "object" && !Array.isArray(section.settings)
      ? section.settings
      : {};
  }

  function settingChoice(settings, key, allowedValues, fallback) {
    const value = String(settings[key] ?? "");
    return allowedValues.includes(value) ? value : fallback;
  }

  function settingBoolean(settings, key, fallback) {
    if (!(key in settings)) return fallback;
    return settings[key] === true || settings[key] === 1 || settings[key] === "1" || settings[key] === "true";
  }

  function surfaceVariant(settings, fallback = "default", allowed = ["default", "light", "dark", "primary", "secondary"]) {
    return settingChoice(settings, "variant", allowed, fallback);
  }

  function applySurfaceVariant(element, variant) {
    element.classList.add(`collection-section--surface-${variant}`);
  }

  function createStatus(message) {
    const status = document.createElement("p");
    status.className = "category-status";
    status.textContent = message;
    return status;
  }

  function createEmptyComponentMessage(prefix, value) {
    const message = document.createElement("p");
    message.className = "collection-section__empty";
    message.append(document.createTextNode(prefix));

    const code = document.createElement("code");
    code.textContent = value;
    message.append(code);

    return message;
  }

  function renderPageError(message) {
    if (!sectionsNode) return;
    sectionsNode.replaceChildren(createStatus(message));
  }

  function sectionDomId(section) {
    const key = section.section_key || `section-${section.id || "content"}`;
    return `collection-section-${sanitizeClassName(key)}`;
  }

  function sanitizeClassName(value) {
    return String(value)
      .trim()
      .toLowerCase()
      .replace(/[^a-z0-9_-]+/g, "-")
      .replace(/^-+|-+$/g, "") || "standard";
  }

  function toDatasetKey(value) {
    return String(value).replace(/-([a-z])/g, (_, letter) => letter.toUpperCase());
  }

  function formatPieceType(value) {
    return String(value).replaceAll("_", " ");
  }

  function escapeCssUrl(value) {
    return String(value).replace(/["\\\n\r\f]/g, "\\$&");
  }

  loadCollectionPage();
}
