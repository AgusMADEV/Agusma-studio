import { fetchCollections, fetchEntities } from "./modules/api.js";
import { buildCollectionHref, buildEntityHref } from "./modules/content-ui.js";

const TYPE_LABELS = {
  club: "Clubs",
  national_team: "National Teams",
};

const TYPE_SINGULAR_LABELS = {
  club: "Club",
  national_team: "National Team",
};

const SORT_LABELS = {
  latest: "Latest",
  oldest: "Oldest",
  alphabetical: "A-Z",
};

function normalizeType(value) {
  return value in TYPE_LABELS ? value : "club";
}

function resolveAssetUrl(value) {
  if (typeof value !== "string" || value.trim() === "") {
    return "";
  }

  try {
    return new URL(value.trim(), window.location.href).href;
  } catch {
    return value.trim();
  }
}

function initFootballPage() {
  const root = document.querySelector("[data-football-page]");

  if (!root) {
    return;
  }

  const header = document.querySelector(".header");
  const categorySlug = root.dataset.categorySlug || "football";
  const entityGrid = root.querySelector("[data-entity-grid]");
  const entityStatus = root.querySelector("[data-entity-status]");
  const selectedEntityName = root.querySelector("[data-selected-entity-name]");
  const selectedEntityLink = root.querySelector("[data-selected-entity-link]");
  const featuredLabel = root.querySelector("[data-featured-label]");
  const heroSection = root.querySelector(".football-hero");
  const archiveGrid = root.querySelector("[data-archive-grid]");
  const archiveStatus = root.querySelector("[data-archive-status]");
  const archiveSummary = root.querySelector("[data-archive-summary]");
  const archiveFilterList = root.querySelector("[data-archive-filter-list]");
  const archiveViewControls = Array.from(root.querySelectorAll("[data-archive-view-control]"));
  const archiveToolbarControlNodes = {
    category: root.querySelector("[data-archive-toolbar-control='category']"),
    year: root.querySelector("[data-archive-toolbar-control='year']"),
    color: root.querySelector("[data-archive-toolbar-control='color']"),
    type: root.querySelector("[data-archive-toolbar-control='type']"),
  };
  const archiveToolbarMenuNodes = {
    category: root.querySelector("[data-archive-toolbar-menu='category']"),
    year: root.querySelector("[data-archive-toolbar-menu='year']"),
    color: root.querySelector("[data-archive-toolbar-menu='color']"),
    type: root.querySelector("[data-archive-toolbar-menu='type']"),
    sort: root.querySelector("[data-archive-toolbar-menu='sort']"),
  };
  const archiveSortControl = root.querySelector("[data-archive-sort-control]");
  const archiveToolbarMenus = Array.from(root.querySelectorAll(".football-archive__toolbar-menu"));
  const tabNodes = Array.from(root.querySelectorAll("[data-entity-type-tab]"));

  let entities = [];
  let collectionsByEntity = new Map();
  let activeType = normalizeType(root.dataset.defaultEntityType || "club");
  let archiveFilter = "all";
  let archiveView = "grid";
  let archiveToolbarState = {
    category: "all",
    year: "all",
    color: "all",
    type: "all",
    sort: "latest",
  };
  let openArchiveMenu = null;

  function updateHeaderState() {
    if (!header) {
      return;
    }

    header.classList.toggle("is-scrolled", window.scrollY > 40);
  }

  function getEntitiesByType(type = activeType) {
    return entities.filter((entity) => entity.entity_type === type);
  }

  function getAllArchiveCollections() {
    return entities.flatMap((entity) => collectionsByEntity.get(entity.slug) || []);
  }

  function normalizeArchiveColor(value) {
    return value === "dark" ? "dark" : "light";
  }

  function normalizeArchiveType(value) {
    return typeof value === "string" && value.trim() !== "" ? value.trim().toLowerCase() : "standard";
  }

  function formatArchiveTypeLabel(value) {
    return String(value)
      .split(/[-_\s]+/)
      .filter(Boolean)
      .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
      .join(" ");
  }

  function getCollectionYearValue(collection) {
    return Number(collection.collection_year || 0);
  }

  function getArchiveToolbarOptions() {
    const collections = getAllArchiveCollections();
    const entityOptions = entities.map((entity) => ({
      value: entity.slug,
      label: entity.name,
    }));
    const yearOptions = [...new Set(collections
      .map((collection) => getCollectionYearValue(collection))
      .filter((year) => year > 0))]
      .sort((left, right) => right - left)
      .map((year) => ({ value: String(year), label: String(year) }));
    const colorOptions = [...new Set(collections.map((collection) => normalizeArchiveColor(collection.image_variant)))].map((value) => ({
      value,
      label: value === "dark" ? "Dark" : "Light",
    }));
    const typeOptions = [...new Set(collections.map((collection) => normalizeArchiveType(collection.layout_style)))].map((value) => ({
      value,
      label: formatArchiveTypeLabel(value),
    }));

    return {
      category: [{ value: "all", label: "Category" }, ...entityOptions],
      year: [{ value: "all", label: "Year" }, ...yearOptions],
      color: [{ value: "all", label: "Color" }, ...colorOptions],
      type: [{ value: "all", label: "Type" }, ...typeOptions],
    };
  }

  function sortCollections(collections) {
    return [...collections].sort((left, right) => {
      if (archiveToolbarState.sort === "alphabetical") {
        return String(left.name || "").localeCompare(String(right.name || ""));
      }

      const direction = archiveToolbarState.sort === "oldest" ? 1 : -1;
      const yearDelta = getCollectionYearValue(left) - getCollectionYearValue(right);

      if (yearDelta !== 0) {
        return yearDelta * direction;
      }

      const featuredDelta = Number(left.is_featured) - Number(right.is_featured);

      if (featuredDelta !== 0) {
        return featuredDelta * direction;
      }

      return String(left.name || "").localeCompare(String(right.name || ""));
    });
  }

  function getArchiveCollections(type = archiveFilter) {
    const visibleEntities = type === "all" ? entities : getEntitiesByType(type);
    const collections = visibleEntities
      .flatMap((entity) => collectionsByEntity.get(entity.slug) || [])
      .filter((collection) => {
        if (archiveToolbarState.category !== "all" && collection.entity_slug !== archiveToolbarState.category) {
          return false;
        }

        if (archiveToolbarState.year !== "all" && String(getCollectionYearValue(collection)) !== archiveToolbarState.year) {
          return false;
        }

        if (archiveToolbarState.color !== "all" && normalizeArchiveColor(collection.image_variant) !== archiveToolbarState.color) {
          return false;
        }

        if (archiveToolbarState.type !== "all" && normalizeArchiveType(collection.layout_style) !== archiveToolbarState.type) {
          return false;
        }

        return true;
      });

    return sortCollections(collections);
  }

  function updateArchiveViewControls() {
    archiveViewControls.forEach((button) => {
      const isActive = button.dataset.archiveViewControl === archiveView;

      button.classList.toggle("is-active", isActive);
      button.setAttribute("aria-pressed", isActive ? "true" : "false");
    });
  }

  function updateArchiveToolbarControls() {
    const optionSets = getArchiveToolbarOptions();

    Object.entries(archiveToolbarControlNodes).forEach(([key, button]) => {
      if (!button) {
        return;
      }

      const selectedOption = optionSets[key]?.find((option) => option.value === archiveToolbarState[key]) ?? optionSets[key]?.[0];
      const isActive = archiveToolbarState[key] !== "all";

      button.textContent = selectedOption?.label || key;
      button.classList.toggle("is-active", isActive);
      button.setAttribute("aria-label", `${key} filter: ${selectedOption?.label || key}`);
    });

    if (archiveSortControl) {
      archiveSortControl.textContent = SORT_LABELS[archiveToolbarState.sort] || SORT_LABELS.latest;
      archiveSortControl.classList.toggle("is-active", archiveToolbarState.sort !== "latest");
      archiveSortControl.setAttribute("aria-label", `Sort by ${SORT_LABELS[archiveToolbarState.sort] || SORT_LABELS.latest}`);
    }
  }

  function getArchiveSortOptions() {
    return Object.entries(SORT_LABELS).map(([value, label]) => ({ value, label }));
  }

  function updateArchiveMenuState() {
    Object.entries(archiveToolbarMenuNodes).forEach(([key, menu]) => {
      if (!menu) {
        return;
      }

      const isOpen = openArchiveMenu === key;
      const trigger = key === "sort" ? archiveSortControl : archiveToolbarControlNodes[key];

      menu.hidden = !isOpen;
      menu.parentElement?.classList.toggle("is-open", isOpen);

      if (trigger) {
        trigger.setAttribute("aria-expanded", isOpen ? "true" : "false");
      }
    });
  }

  function renderArchiveDropdownMenus() {
    const optionSets = getArchiveToolbarOptions();

    Object.entries(archiveToolbarMenuNodes).forEach(([key, menu]) => {
      if (!menu) {
        return;
      }

      const options = key === "sort" ? getArchiveSortOptions() : (optionSets[key] || []);
      const selectedValue = key === "sort" ? archiveToolbarState.sort : archiveToolbarState[key];
      const nodes = options.map((option) => {
        const button = document.createElement("button");
        const isSelected = option.value === selectedValue;

        button.type = "button";
        button.className = "football-archive__dropdown-option";
        button.dataset.selected = isSelected ? "true" : "false";
        button.setAttribute("role", "menuitemradio");
        button.setAttribute("aria-checked", isSelected ? "true" : "false");
        button.textContent = option.label;
        button.addEventListener("click", () => {
          if (key === "sort") {
            archiveToolbarState = {
              ...archiveToolbarState,
              sort: option.value,
            };
          } else {
            archiveToolbarState = {
              ...archiveToolbarState,
              [key]: option.value,
            };
          }

          openArchiveMenu = null;
          renderAll();
        });

        return button;
      });

      menu.replaceChildren(...nodes);
      menu.setAttribute("role", "menu");
    });

    updateArchiveMenuState();
  }

  function toggleArchiveMenu(key) {
    openArchiveMenu = openArchiveMenu === key ? null : key;
    updateArchiveMenuState();
  }

  function updateTabs() {
    tabNodes.forEach((tab) => {
      const isActive = (tab.dataset.entityTypeTab || "club") === activeType;
      tab.classList.toggle("is-active", isActive);
      tab.setAttribute("aria-pressed", isActive ? "true" : "false");
    });
  }

  function setHeroBackground(category) {
    if (!heroSection) {
      return;
    }

    const heroImage = typeof category?.hero_image === "string" && category.hero_image.trim() !== ""
      ? category.hero_image.trim()
      : (typeof category?.cover_image === "string" ? category.cover_image.trim() : "");
    const heroImageUrl = resolveAssetUrl(heroImage);

    if (heroImageUrl === "") {
      heroSection.style.removeProperty("--football-hero-image");
      return;
    }

    heroSection.style.setProperty("--football-hero-image", `url("${heroImageUrl.replace(/"/g, '\\"')}")`);
  }

  function createFeaturedEntityCard(entity, index) {
    const article = document.createElement("article");
    article.className = "football-featured-card";

    const link = document.createElement("a");
    link.className = "football-featured-card__link";
    link.href = buildEntityHref(categorySlug, entity.slug);

    const kicker = document.createElement("span");
    kicker.className = "football-featured-card__index";
    kicker.textContent = String(index + 1).padStart(2, "0");

    const title = document.createElement("h3");
    title.className = "football-featured-card__title";
    title.textContent = entity.name;

    const logoWrap = document.createElement("div");
    logoWrap.className = "football-featured-card__logo-wrap";

    if (typeof entity.logo_url === "string" && entity.logo_url.trim() !== "") {
      const logo = document.createElement("img");
      logo.className = "football-featured-card__logo";
      logo.src = entity.logo_url;
      logo.alt = entity.name;
      logoWrap.append(logo);
    } else {
      const fallback = document.createElement("span");
      fallback.className = "football-featured-card__logo-fallback";
      fallback.textContent = entity.name.slice(0, 2).toUpperCase();
      logoWrap.append(fallback);
    }

    const footer = document.createElement("div");
    footer.className = "football-featured-card__footer";

    const cta = document.createElement("span");
    cta.textContent = "View archive";

    const label = document.createElement("span");
    label.textContent = TYPE_SINGULAR_LABELS[normalizeType(entity.entity_type)] || "Entity";

    footer.append(cta, label);
    link.append(kicker, title, logoWrap, footer);
    article.append(link);

    return article;
  }

  function createArchiveCard(collection) {
    const article = document.createElement("article");
    article.className = "football-archive-card";

    const link = document.createElement("a");
    link.className = "football-archive-card__link";
    link.href = buildCollectionHref(collection);

    const visual = document.createElement("div");
    visual.className = collection.image_variant === "dark"
      ? "football-archive-card__visual football-archive-card__visual--dark"
      : "football-archive-card__visual";

    if (typeof collection.cover_image === "string" && collection.cover_image.trim() !== "") {
      visual.style.backgroundImage = `linear-gradient(180deg, rgba(12, 12, 12, 0.12), rgba(12, 12, 12, 0.28)), url('${collection.cover_image}')`;
      visual.style.backgroundSize = "cover";
      visual.style.backgroundPosition = "center";
    }

    const body = document.createElement("div");
    body.className = "football-archive-card__body";

    const content = document.createElement("div");
    content.className = "football-archive-card__content";

    const eyebrow = document.createElement("p");
    eyebrow.className = "football-archive-card__eyebrow";
    eyebrow.textContent = `${TYPE_LABELS[normalizeType(collection.entity_type)]} · ${collection.entity_name}`;

    const title = document.createElement("h3");
    title.className = "football-archive-card__title";
    title.textContent = collection.name;

    const description = document.createElement("p");
    description.className = "football-archive-card__description";
    description.textContent = collection.short_description
      || collection.subtitle
      || collection.description
      || "";

    const meta = document.createElement("div");
    meta.className = "football-archive-card__meta";

    const year = document.createElement("span");
    year.textContent = collection.collection_year
      ? String(collection.collection_year)
      : (collection.season || "Archive");

    const cta = document.createElement("span");
    cta.textContent = "Open";

    const aside = document.createElement("div");
    aside.className = "football-archive-card__aside";

    const itemCount = document.createElement("span");
    itemCount.className = "football-archive-card__item-count";
    itemCount.textContent = `${Number(collection.piece_count || 0)} ${Number(collection.piece_count || 0) === 1 ? "item" : "items"}`;

    const arrow = document.createElement("span");
    arrow.className = "football-archive-card__arrow";
    arrow.textContent = "→";

    meta.append(year, cta);
    aside.append(itemCount, arrow);
    content.append(eyebrow, title, description, meta);
    body.append(content, aside);
    link.append(visual, body);
    article.append(link);

    return article;
  }

  function renderFeaturedEntities() {
    const visibleEntities = getEntitiesByType();

    if (!entityGrid || !entityStatus) {
      return;
    }

    if (visibleEntities.length === 0) {
      entityGrid.innerHTML = "";
      entityStatus.hidden = false;
      entityStatus.textContent = "No hay entidades publicadas para esta seccion.";
      return;
    }

    entityGrid.replaceChildren(...visibleEntities.slice(0, 6).map((entity, index) => createFeaturedEntityCard(entity, index)));
    entityStatus.hidden = true;
  }

  function renderFeaturedHeader() {
    if (!selectedEntityName || !featuredLabel || !selectedEntityLink) {
      return;
    }

    const label = TYPE_LABELS[activeType] || "Football";

    featuredLabel.textContent = `Featured ${label.toLowerCase()}`;
    selectedEntityName.textContent = `${label} selection`;
    selectedEntityLink.href = "#football-archive-title";
    selectedEntityLink.textContent = `View all ${label.toLowerCase()}`;
  }

  function renderArchiveFilters() {
    if (!archiveFilterList) {
      return;
    }

    const filterDefinitions = ["all", "club", "national_team"].filter((type) => type === "all" || getEntitiesByType(type).length > 0);
    const nodes = filterDefinitions.map((type) => {
      const button = document.createElement("button");
      const collections = getArchiveCollections(type);
      const isActive = archiveFilter === type;

      button.type = "button";
      button.className = "football-archive-filter";
      button.dataset.active = isActive ? "true" : "false";
      button.innerHTML = `<span>${type === "all" ? "All" : TYPE_LABELS[type]}</span><strong>${collections.length}</strong>`;
      button.addEventListener("click", () => {
        archiveFilter = type;
        renderArchiveFilters();
        renderArchiveGrid();
      });

      return button;
    });

    archiveFilterList.replaceChildren(...nodes);
  }

  function renderArchiveGrid() {
    if (!archiveGrid || !archiveStatus || !archiveSummary) {
      return;
    }

    const collections = getArchiveCollections();
    const label = archiveFilter === "all" ? "Football" : (TYPE_LABELS[archiveFilter] || "Football");

    archiveGrid.classList.toggle("is-list", archiveView === "list");

    archiveSummary.textContent = `${collections.length} ${collections.length === 1 ? "collection" : "collections"} in ${label.toLowerCase()}.`;

    if (collections.length === 0) {
      archiveGrid.innerHTML = "";
      archiveStatus.hidden = false;
      archiveStatus.textContent = "No hay colecciones disponibles en este tramo del archivo.";
      return;
    }

    archiveGrid.replaceChildren(...collections.map((collection) => createArchiveCard(collection)));
    archiveStatus.hidden = true;
  }

  async function primeCollections() {
    await Promise.all(entities.map(async (entity) => {
      try {
        const payload = await fetchCollections({ category: categorySlug, entity: entity.slug });
        const collections = Array.isArray(payload.collections) ? payload.collections : [];

        collectionsByEntity.set(entity.slug, collections.map((collection) => ({
          ...collection,
          entity_name: payload.entity?.name || entity.name,
          entity_slug: payload.entity?.slug || entity.slug,
          entity_type: payload.entity?.entity_type || entity.entity_type,
          category_slug: payload.category?.slug || categorySlug,
        })));
      } catch (error) {
        console.error(`No se pudieron cargar las colecciones de ${entity.slug}.`, error);
        collectionsByEntity.set(entity.slug, []);
      }
    }));
  }

  function renderAll() {
    updateTabs();
    renderFeaturedHeader();
    renderFeaturedEntities();
    updateArchiveViewControls();
    updateArchiveToolbarControls();
    renderArchiveDropdownMenus();
    renderArchiveFilters();
    renderArchiveGrid();
  }

  tabNodes.forEach((tab) => {
    tab.addEventListener("click", () => {
      activeType = normalizeType(tab.dataset.entityTypeTab || "club");
      renderAll();
    });
  });

  archiveViewControls.forEach((button) => {
    button.addEventListener("click", () => {
      archiveView = button.dataset.archiveViewControl === "list" ? "list" : "grid";
      renderAll();
    });
  });

  Object.entries(archiveToolbarControlNodes).forEach(([key, button]) => {
    if (!button) {
      return;
    }

    button.addEventListener("click", () => {
      toggleArchiveMenu(key);
    });
  });

  if (archiveSortControl) {
    archiveSortControl.addEventListener("click", () => {
      toggleArchiveMenu("sort");
    });
  }

  document.addEventListener("click", (event) => {
    if (!(event.target instanceof Node)) {
      return;
    }

    const clickedInsideMenu = archiveToolbarMenus.some((menu) => menu.contains(event.target));

    if (!clickedInsideMenu && openArchiveMenu !== null) {
      openArchiveMenu = null;
      updateArchiveMenuState();
    }
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape" && openArchiveMenu !== null) {
      openArchiveMenu = null;
      updateArchiveMenuState();
    }
  });

  window.addEventListener("scroll", updateHeaderState, { passive: true });
  updateHeaderState();

  async function loadPage() {
    try {
      if (entityStatus) {
        entityStatus.hidden = false;
        entityStatus.textContent = "Cargando entidades de football...";
      }

      if (archiveStatus) {
        archiveStatus.hidden = false;
        archiveStatus.textContent = "Cargando archivo de football...";
      }

      const payload = await fetchEntities({ category: categorySlug });
      setHeroBackground(payload.category || null);
      entities = Array.isArray(payload.entities) ? payload.entities : [];

      await primeCollections();
      archiveView = "grid";
      archiveToolbarState = {
        category: "all",
        year: "all",
        color: "all",
        type: "all",
        sort: "latest",
      };
      archiveFilter = "all";
      openArchiveMenu = null;

      if (entities.length === 0) {
        if (entityStatus) {
          entityStatus.textContent = "No hay entidades publicadas para football.";
        }

        if (archiveStatus) {
          archiveStatus.textContent = "No hay archivo publicado para football.";
        }

        return;
      }

      renderAll();
    } catch (error) {
      console.error(error);

      if (entityStatus) {
        entityStatus.hidden = false;
        entityStatus.textContent = "No se pudieron cargar las entidades.";
      }

      if (archiveStatus) {
        archiveStatus.hidden = false;
        archiveStatus.textContent = "No se pudo cargar el archivo de football.";
      }
    }
  }

  loadPage();
}

initFootballPage();