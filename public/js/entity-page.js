import { fetchCollections } from "./modules/api.js";
import { createCollectionCard } from "./modules/content-ui.js";

const root = document.querySelector("[data-entity-page]");

if (root) {
  const categorySlug = root.dataset.categorySlug || "";
  const entitySlug = root.dataset.entitySlug || "";
  const nameNode = root.querySelector("[data-entity-name]");
  const descriptionNode = root.querySelector("[data-entity-description]");
  const eyebrowNode = root.querySelector("[data-entity-eyebrow]");
  const typeNode = root.querySelector("[data-entity-type]");
  const categoryNode = root.querySelector("[data-entity-category]");
  const countNode = root.querySelector("[data-entity-collection-count]");
  const titleNode = root.querySelector("[data-entity-collections-title]");
  const statusNode = root.querySelector("[data-entity-status]");
  const gridNode = root.querySelector("[data-entity-collection-grid]");

  async function loadEntityPage() {
    if (categorySlug === "" || entitySlug === "") {
      if (statusNode) {
        statusNode.textContent = "Faltan los slugs necesarios para cargar la entidad.";
      }
      return;
    }

    try {
      const payload = await fetchCollections({ category: categorySlug, entity: entitySlug });
      const entity = payload.entity || {};
      const category = payload.category || {};
      const collections = Array.isArray(payload.collections) ? payload.collections : [];

      if (nameNode) nameNode.textContent = entity.name || "Entity";
      if (descriptionNode) descriptionNode.textContent = entity.description || entity.short_description || "No hay descripcion publicada para esta entidad.";
      if (eyebrowNode) eyebrowNode.textContent = `${category.name || "Category"} · ${entity.entity_type || "entity"}`;
      if (typeNode) typeNode.textContent = entity.entity_type || "entity";
      if (categoryNode) categoryNode.textContent = category.name || "Category";
      if (countNode) countNode.textContent = `${collections.length} ${collections.length === 1 ? "collection" : "collections"}`;
      if (titleNode) titleNode.textContent = `${entity.name || "Entity"} collections`;

      if (!statusNode || !gridNode) {
        return;
      }

      if (collections.length === 0) {
        statusNode.textContent = "No hay colecciones activas para esta entidad.";
        gridNode.innerHTML = "";
        return;
      }

      gridNode.replaceChildren(...collections.map((collection) => createCollectionCard({
        ...collection,
        entity_name: entity.name || "",
        entity_slug: entity.slug || entitySlug,
        entity_type: entity.entity_type || "",
        category_slug: category.slug || categorySlug,
      }, "category-collection-card")));
      statusNode.hidden = true;
    } catch (error) {
      console.error(error);
      if (statusNode) {
        statusNode.textContent = "No se pudo cargar la entidad solicitada.";
      }
    }
  }

  loadEntityPage();
}