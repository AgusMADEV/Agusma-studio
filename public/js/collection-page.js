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
  const tagsNode = root.querySelector("[data-collection-tags]");
  const piecesTitle = root.querySelector("[data-collection-pieces-title]");
  const statusNode = root.querySelector("[data-collection-status]");
  const mediaStatusNode = root.querySelector("[data-collection-media-status]");
  const pieceGridNode = root.querySelector("[data-collection-piece-grid]");
  const mediaGridNode = root.querySelector("[data-collection-media-grid]");

  async function loadCollectionPage() {
    if (categorySlug === "" || entitySlug === "" || collectionSlug === "") {
      if (statusNode) {
        statusNode.textContent = "Faltan los slugs necesarios para cargar la coleccion.";
      }
      return;
    }

    try {
      const payload = await fetchCollectionDetail({ category: categorySlug, entity: entitySlug, collection: collectionSlug });
      const category = payload.category || {};
      const entity = payload.entity || {};
      const collection = payload.collection || {};
      const pieces = Array.isArray(payload.pieces) ? payload.pieces : [];
      const media = Array.isArray(payload.media) ? payload.media : [];
      const tags = Array.isArray(payload.tags) ? payload.tags : [];

      if (nameNode) nameNode.textContent = collection.name || "Collection";
      if (descriptionNode) descriptionNode.textContent = collection.description || collection.short_description || "No hay descripcion publicada para esta coleccion.";
      if (eyebrowNode) eyebrowNode.textContent = `${category.name || "Category"} · ${entity.name || "Entity"}`;
      if (entityNode) entityNode.textContent = entity.name || "Entity";
      if (parentLink) parentLink.href = `./entity.php?category=${encodeURIComponent(category.slug || categorySlug)}&entity=${encodeURIComponent(entity.slug || entitySlug)}`;
      if (piecesTitle) piecesTitle.textContent = `${collection.name || "Collection"} pieces`;

      if (tagsNode) {
        tagsNode.replaceChildren(...(tags.length > 0 ? tags.map(createTag) : [createPlaceholderTag("No tags") ]));
      }

      if (pieceGridNode && statusNode) {
        if (pieces.length === 0) {
          statusNode.textContent = "No hay piezas activas en esta coleccion.";
          pieceGridNode.innerHTML = "";
        } else {
          pieceGridNode.replaceChildren(...pieces.map(createPieceCard));
          statusNode.hidden = true;
        }
      }

      if (mediaGridNode && mediaStatusNode) {
        if (media.length === 0) {
          mediaStatusNode.textContent = "No hay multimedia general publicada para esta coleccion.";
          mediaGridNode.innerHTML = "";
        } else {
          mediaGridNode.replaceChildren(...media.map(createMediaCard));
          mediaStatusNode.hidden = true;
        }
      }
    } catch (error) {
      console.error(error);
      if (statusNode) {
        statusNode.textContent = "No se pudo cargar la coleccion solicitada.";
      }
      if (mediaStatusNode) {
        mediaStatusNode.textContent = "No se pudo cargar la multimedia de la coleccion.";
      }
    }
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

  function createPieceCard(piece) {
    const article = document.createElement("article");
    article.className = "collection-piece-card";

    const body = document.createElement("div");
    body.className = "collection-piece-card__body";

    const eyebrow = document.createElement("p");
    eyebrow.className = "category-section__eyebrow";
    eyebrow.textContent = piece.piece_type || "piece";

    const title = document.createElement("h3");
    title.className = "collection-piece-card__title";
    title.textContent = piece.name;

    const description = document.createElement("p");
    description.className = "collection-piece-card__description";
    description.textContent = piece.description || piece.short_description || "No description published for this piece.";

    body.append(eyebrow, title, description);

    if (Array.isArray(piece.media) && piece.media.length > 0) {
      const mediaWrap = document.createElement("div");
      mediaWrap.className = "collection-piece-card__media";
      mediaWrap.append(...piece.media.map(createMediaCard));
      body.append(mediaWrap);
    }

    article.append(body);
    return article;
  }

  function createMediaCard(media) {
    const article = document.createElement("article");
    article.className = "collection-media-card";

    const body = document.createElement("div");
    body.className = "collection-media-card__body";

    const eyebrow = document.createElement("p");
    eyebrow.className = "category-section__eyebrow";
    eyebrow.textContent = media.media_type || "media";

    const title = document.createElement("h3");
    title.className = "collection-media-card__title";
    title.textContent = media.title || media.alt_text || "Media asset";

    const description = document.createElement("p");
    description.className = "collection-media-card__description";
    description.textContent = media.caption || media.alt_text || media.file_url;

    const link = document.createElement("a");
    link.href = media.file_url;
    link.className = "collection-media-card__file";
    link.target = "_blank";
    link.rel = "noreferrer";
    link.textContent = "Open asset";

    body.append(eyebrow, title, description, link);
    article.append(body);

    return article;
  }

  loadCollectionPage();
}