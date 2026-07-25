export function buildCollectionHref(collection) {
  const collectionSlug = collection.collection_slug || collection.slug || "";

  return `./collection.php?category=${encodeURIComponent(collection.category_slug)}&entity=${encodeURIComponent(collection.entity_slug)}&collection=${encodeURIComponent(collectionSlug)}`;
}

export function buildEntityHref(categorySlug, entitySlug) {
  return `./entity.php?category=${encodeURIComponent(categorySlug)}&entity=${encodeURIComponent(entitySlug)}`;
}

export function createCollectionCard(collection, cardClass = "collection-card") {
  const article = document.createElement("article");
  article.className = cardClass;

  const link = document.createElement("a");
  link.href = buildCollectionHref(collection);
  link.className = `${cardClass}__link`;

  const image = document.createElement("div");
  image.className = collection.image_variant === "dark"
    ? `${cardClass}__image ${cardClass}__image--dark`
    : `${cardClass}__image`;

  if (typeof collection.cover_image === "string" && collection.cover_image.trim() !== "") {
    image.style.backgroundImage = `linear-gradient(180deg, rgba(12, 12, 12, 0.08), rgba(12, 12, 12, 0.08)), url('${collection.cover_image}')`;
    image.style.backgroundSize = "cover";
    image.style.backgroundPosition = "center";
  }

  const info = document.createElement("div");
  info.className = `${cardClass}__info`;

  const textGroup = document.createElement("div");

  const title = document.createElement("h3");
  title.textContent = collection.name;

  const entity = document.createElement("p");
  entity.className = `${cardClass}__entity`;
  entity.textContent = collection.entity_name;

  textGroup.append(title, entity);

  const meta = document.createElement("span");
  meta.textContent = collection.collection_year ? String(collection.collection_year) : (collection.season || collection.entity_type || "Archive");

  info.append(textGroup, meta);
  link.append(image, info);
  article.append(link);

  return article;
}

export function createEntityCard(entity, categorySlug, isActive = false) {
  const article = document.createElement("article");
  article.className = "entity-card";
  article.dataset.active = isActive ? "true" : "false";

  const button = document.createElement("button");
  button.type = "button";
  button.className = "entity-card__button";
  button.dataset.entitySlug = entity.slug;

  const eyebrow = document.createElement("p");
  eyebrow.className = "entity-card__eyebrow";
  eyebrow.textContent = entity.entity_type;

  const title = document.createElement("h3");
  title.className = "entity-card__title";
  title.textContent = entity.name;

  const description = document.createElement("p");
  description.className = "entity-card__description";
  description.textContent = entity.short_description || entity.subtitle || "Entity in AgusMA Studio archive.";

  const footer = document.createElement("div");
  footer.className = "entity-card__footer";

  const openLink = document.createElement("a");
  openLink.href = buildEntityHref(categorySlug, entity.slug);
  openLink.className = "entity-card__link";
  openLink.textContent = "Open entity";

  const status = document.createElement("span");
  status.className = "entity-card__status";
  status.textContent = isActive ? "Selected" : "Browse";

  footer.append(openLink, status);
  button.append(eyebrow, title, description, footer);
  article.append(button);

  return article;
}

export function setEntityCardState(grid, activeSlug) {
  Array.from(grid.querySelectorAll(".entity-card")).forEach((card) => {
    const button = card.querySelector("[data-entity-slug]");
    const isActive = button?.dataset.entitySlug === activeSlug;
    card.dataset.active = isActive ? "true" : "false";

    const status = card.querySelector(".entity-card__status");
    if (status) {
      status.textContent = isActive ? "Selected" : "Browse";
    }
  });
}