import { fetchCollections, fetchEntities } from "./api.js";
import { createCollectionCard, createEntityCard, setEntityCardState } from "./content-ui.js";

export function initCategoryBrowser(config) {
  const root = document.querySelector(config.rootSelector);

  if (!root) {
    return;
  }

  const categorySlug = root.dataset.categorySlug || config.categorySlug || "";
  const entityGrid = root.querySelector(config.entityGridSelector);
  const entityStatus = root.querySelector(config.entityStatusSelector);
  const collectionGrid = root.querySelector(config.collectionGridSelector);
  const collectionStatus = root.querySelector(config.collectionStatusSelector);
  const selectedEntityName = root.querySelector(config.selectedEntityNameSelector);
  const countNode = root.querySelector(config.countSelector);
  const tabNodes = Array.from(root.querySelectorAll(config.tabSelector || "[data-entity-type-tab]"));

  let entities = [];
  let activeEntitySlug = null;
  let activeType = root.dataset.defaultEntityType || "all";

  async function loadEntities() {
    if (!entityGrid || !entityStatus || !collectionGrid || !collectionStatus || !selectedEntityName || !countNode || categorySlug === "") {
      return;
    }

    entityStatus.hidden = false;
    entityStatus.textContent = "Cargando entidades...";
    collectionStatus.hidden = false;
    collectionStatus.textContent = "Selecciona una entidad para ver sus colecciones.";

    try {
      const payload = await fetchEntities({
        category: categorySlug,
        type: activeType === "all" ? null : activeType,
      });

      entities = Array.isArray(payload.entities) ? payload.entities : [];

      if (entities.length === 0) {
        entityGrid.innerHTML = "";
        collectionGrid.innerHTML = "";
        selectedEntityName.textContent = "No entities";
        countNode.textContent = "0 collections";
        entityStatus.textContent = "No hay entidades publicadas para esta categoria.";
        return;
      }

      if (!entities.some((entity) => entity.slug === activeEntitySlug)) {
        activeEntitySlug = entities[0].slug;
      }

      entityGrid.replaceChildren(...entities.map((entity) => createEntityCard(entity, categorySlug, entity.slug === activeEntitySlug)));
      entityStatus.hidden = true;
      bindEntityButtons();
      await loadCollections();
    } catch (error) {
      console.error(error);
      entityStatus.textContent = "No se pudieron cargar las entidades.";
      selectedEntityName.textContent = "Unavailable";
      countNode.textContent = "0 collections";
    }
  }

  async function loadCollections() {
    if (!collectionGrid || !collectionStatus || !selectedEntityName || !countNode || activeEntitySlug === null) {
      return;
    }

    collectionStatus.hidden = false;
    collectionStatus.textContent = "Cargando colecciones...";

    try {
      const payload = await fetchCollections({ category: categorySlug, entity: activeEntitySlug });
      const collections = Array.isArray(payload.collections) ? payload.collections : [];
      selectedEntityName.textContent = payload.entity?.name || "Collections";
      countNode.textContent = `${collections.length} ${collections.length === 1 ? "collection" : "collections"}`;
      setEntityCardState(entityGrid, activeEntitySlug);

      if (collections.length === 0) {
        collectionGrid.innerHTML = "";
        collectionStatus.textContent = "No hay colecciones publicadas para esta entidad.";
        return;
      }

      collectionGrid.replaceChildren(...collections.map((collection) => createCollectionCard({
        ...collection,
        entity_name: payload.entity?.name || "",
        entity_slug: payload.entity?.slug || activeEntitySlug,
        entity_type: payload.entity?.entity_type || "",
        category_slug: payload.category?.slug || categorySlug,
      }, "category-collection-card")));
      collectionStatus.hidden = true;
    } catch (error) {
      console.error(error);
      collectionStatus.textContent = "No se pudieron cargar las colecciones de la entidad.";
      countNode.textContent = "0 collections";
    }
  }

  function bindEntityButtons() {
    Array.from(entityGrid.querySelectorAll("[data-entity-slug]"))
      .forEach((button) => {
        button.addEventListener("click", async () => {
          activeEntitySlug = button.dataset.entitySlug || null;
          await loadCollections();
        });
      });
  }

  function bindTabs() {
    tabNodes.forEach((tab) => {
      tab.addEventListener("click", async () => {
        activeType = tab.dataset.entityTypeTab || "all";

        tabNodes.forEach((node) => {
          const isActive = node === tab;
          node.classList.toggle("is-active", isActive);
          node.setAttribute("aria-pressed", isActive ? "true" : "false");
        });

        await loadEntities();
      });
    });
  }

  bindTabs();
  loadEntities();
}