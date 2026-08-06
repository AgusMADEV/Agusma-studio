const panels = Array.from(document.querySelectorAll("[data-admin-panel]"));
const navLinks = Array.from(document.querySelectorAll("[data-admin-nav]"));
const collapseToggle = document.querySelector("[data-admin-collapse-toggle]");
const collapseBody = document.querySelector("[data-admin-collapse-body]");
const panelControls = Array.from(document.querySelectorAll(".admin-panel-toolbar"));

function normalizeViewId(value) {
  return (value || "").replace(/^#/, "").trim();
}

function getPanelItems(panel) {
  return Array.from(panel.querySelectorAll("[data-admin-item]"));
}

function getDetailPanels(panel) {
  return Array.from(panel.querySelectorAll("[data-admin-detail-panel]"));
}

function getFirstVisibleItem(panel) {
  return getPanelItems(panel).find((item) => !item.hidden) || null;
}

function getPublicAssetPrefix() {
  const { pathname } = window.location;
  const adminSegment = "/admin/";
  const adminIndex = pathname.indexOf(adminSegment);

  if (adminIndex === -1) {
    return "/public/";
  }

  const basePath = pathname.slice(0, adminIndex);
  return `${basePath}/public/`;
}

function normalizeImageSource(value) {
  const source = (value || "").trim();

  if (source === "") {
    return "";
  }

  if (
    source.startsWith("http://") ||
    source.startsWith("https://") ||
    source.startsWith("data:") ||
    source.startsWith("/")
  ) {
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

function setDetailMode(detailPanel, mode) {
  if (!detailPanel) {
    return;
  }

  const editableBlock = detailPanel.querySelector("[data-admin-editable]");
  detailPanel.dataset.mode = mode;

  if (editableBlock) {
    editableBlock.hidden = mode !== "edit";
  }

  syncMasterDetailMode(detailPanel.closest("[data-admin-master-detail]"));
}

function syncMasterDetailMode(masterDetail) {
  if (!masterDetail) {
    return;
  }

  const activeDetailPanel = masterDetail.querySelector(
    ".admin-detail-panel:not([hidden])",
  );
  const nextMode = activeDetailPanel?.dataset.mode === "edit" ? "edit" : "view";

  masterDetail.dataset.detailMode = nextMode;
}

function resetDetailPanel(detailPanel, resetForm = false) {
  if (!detailPanel) {
    return;
  }

  if (resetForm) {
    if (typeof detailPanel.reset === "function") {
      detailPanel.reset();
    } else {
      Array.from(detailPanel.querySelectorAll("form")).forEach((form) => form.reset());
    }
  }

  setDetailMode(detailPanel, "view");
  syncImagePreviews(detailPanel);
  syncSectionVisualSettings(detailPanel);
  Array.from(detailPanel.querySelectorAll("form")).forEach((form) => {
    syncMediaRelationForm(form);
  });
}

function setImageFieldState(field, source) {
  const preview = field.querySelector("[data-admin-image-preview]");
  const image = field.querySelector("[data-admin-image-tag]");
  const placeholder = field.querySelector(".admin-asset-placeholder");
  const normalizedSource = normalizeImageSource(source);
  const hasSource = normalizedSource !== "";

  if (!preview || !image || !placeholder) {
    return;
  }

  preview.classList.toggle("is-empty", !hasSource);
  image.hidden = !hasSource;
  placeholder.hidden = hasSource;

  if (hasSource) {
    image.src = normalizedSource;
  } else {
    image.removeAttribute("src");
  }
}

function bindImageField(field) {
  const input = field.querySelector("[data-admin-image-input]");
  const image = field.querySelector("[data-admin-image-tag]");
  const browseButton = field.querySelector("[data-admin-image-browse]");
  const clearButton = field.querySelector("[data-admin-image-clear]");

  if (!input || !image) {
    return;
  }

  const syncPreview = () => {
    setImageFieldState(field, input.value.trim());
  };

  input.addEventListener("input", syncPreview);
  image.addEventListener("error", () => setImageFieldState(field, ""));
  browseButton?.addEventListener("click", () => {
    input.focus();
    input.select();
  });
  clearButton?.addEventListener("click", () => {
    input.value = "";
    syncPreview();
  });

  syncPreview();
}

function syncImagePreviews(root = document) {
  Array.from(root.querySelectorAll("[data-admin-image-field]")).forEach((field) => {
    const input = field.querySelector("[data-admin-image-input]");

    if (!input) {
      return;
    }

    setImageFieldState(field, input.value.trim());
  });
}

function bindImageFields() {
  Array.from(document.querySelectorAll("[data-admin-image-field]")).forEach((field) => {
    bindImageField(field);
  });
}

function syncMediaRelationForm(form) {
  const collectionSelect = form.querySelector("[data-admin-media-collection]");

  if (!collectionSelect) {
    return;
  }

  const collectionId = collectionSelect.value;
  const relatedSelects = [
    form.querySelector("[data-admin-piece-select]"),
    form.querySelector("[data-admin-section-select]"),
  ].filter(Boolean);

  relatedSelects.forEach((select) => {
    let selectedOptionIsVisible = select.value === "";

    Array.from(select.options).forEach((option) => {
      const optionCollectionId = option.dataset.collectionId || "";
      const isPlaceholder = option.value === "";
      const shouldShow = isPlaceholder || optionCollectionId === collectionId;

      option.hidden = !shouldShow;
      option.disabled = !shouldShow;

      if (option.selected && shouldShow) {
        selectedOptionIsVisible = true;
      }
    });

    if (!selectedOptionIsVisible) {
      select.value = "";
    }
  });
}

function bindMediaRelationForms() {
  const forms = Array.from(document.querySelectorAll("form"));

  forms.forEach((form) => {
    const collectionSelect = form.querySelector("[data-admin-media-collection]");

    if (!collectionSelect) {
      return;
    }

    collectionSelect.addEventListener("change", () => syncMediaRelationForm(form));
    syncMediaRelationForm(form);
  });
}

function syncSectionVisualSettings(root = document) {
  const forms = [];

  if (root instanceof Element && root.matches("[data-section-settings-form]")) {
    forms.push(root);
  }

  forms.push(...Array.from(root.querySelectorAll?.("[data-section-settings-form]") || []));

  forms.forEach((form) => {
    const typeSelect = form.querySelector("[data-section-type-select]");
    const panels = Array.from(form.querySelectorAll("[data-section-settings-panel]"));
    const activeType = typeSelect?.value || "";

    panels.forEach((panel) => {
      panel.hidden = panel.dataset.sectionSettingsPanel !== activeType;
    });
  });
}

function bindSectionVisualSettings() {
  const forms = Array.from(document.querySelectorAll("[data-section-settings-form]"));

  forms.forEach((form) => {
    const typeSelect = form.querySelector("[data-section-type-select]");

    typeSelect?.addEventListener("change", () => syncSectionVisualSettings(form));
    form.addEventListener("reset", () => {
      window.setTimeout(() => syncSectionVisualSettings(form), 0);
    });
  });

  syncSectionVisualSettings();
}

function clearPanelControls(panel) {
  const searchInput = panel.querySelector("[data-admin-search]");
  const statusFilter = panel.querySelector("[data-admin-status-filter]");
  const collectionFilter = panel.querySelector("[data-admin-collection-filter]");

  if (searchInput) searchInput.value = "";
  if (statusFilter) statusFilter.value = "all";
  if (collectionFilter) collectionFilter.value = "all";

  applyPanelFilters(panel);
}

function bindOpenMediaButtons() {
  Array.from(document.querySelectorAll("[data-admin-open-media]")).forEach((button) => {
    button.addEventListener("click", () => {
      const mediaPanel = panels.find((panel) => panel.id === "multimedia");
      const detailId = button.dataset.mediaDetailId || "";

      if (!mediaPanel || detailId === "") {
        return;
      }

      clearPanelControls(mediaPanel);
      window.location.hash = "#multimedia";
      setActiveView("multimedia");
      setDetailSelection(mediaPanel, detailId);
    });
  });
}

function sortPanelItems(panel) {
  const sortSelect = panel.querySelector("[data-admin-sort]");
  const list = panel.querySelector(".admin-record-list");

  if (!sortSelect || !list) {
    return;
  }

  const items = getPanelItems(panel);
  const sortValue = sortSelect.value;

  items.sort((left, right) => {
    const leftOrder = Number(left.dataset.order || 0);
    const rightOrder = Number(right.dataset.order || 0);
    const leftName = (left.dataset.name || "").toLowerCase();
    const rightName = (right.dataset.name || "").toLowerCase();
    const leftCollection = (left.dataset.collectionName || "").toLowerCase();
    const rightCollection = (right.dataset.collectionName || "").toLowerCase();
    const collectionOrder = panel.id === "secciones"
      ? leftCollection.localeCompare(rightCollection)
      : 0;

    switch (sortValue) {
      case "order-desc":
        return collectionOrder || rightOrder - leftOrder || leftName.localeCompare(rightName);
      case "name-asc":
        return leftName.localeCompare(rightName) || leftOrder - rightOrder;
      case "name-desc":
        return rightName.localeCompare(leftName) || leftOrder - rightOrder;
      case "order-asc":
      default:
        return collectionOrder || leftOrder - rightOrder || leftName.localeCompare(rightName);
    }
  });

  items.forEach((item) => list.appendChild(item));
}

function setDetailSelection(panel, requestedDetailId, allowEmptySelection = false) {
  const items = getPanelItems(panel);
  const detailPanels = getDetailPanels(panel);
  const masterDetail = panel.querySelector("[data-admin-master-detail]");
  const detailColumn = panel.querySelector(".admin-detail-column");
  const emptyDetail = panel.querySelector("[data-admin-empty-detail]");
  const firstVisibleItem = getFirstVisibleItem(panel);
  let nextDetailId = "";

  if (
    requestedDetailId &&
    items.some(
      (item) => item.dataset.detailId === requestedDetailId && !item.hidden,
    )
  ) {
    nextDetailId = requestedDetailId;
  } else if (!allowEmptySelection) {
    nextDetailId = firstVisibleItem?.dataset.detailId || "";
  }

  items.forEach((item) => {
    const isSelected = nextDetailId !== "" && item.dataset.detailId === nextDetailId;
    item.classList.toggle("is-selected", isSelected);
    item.setAttribute("aria-pressed", isSelected ? "true" : "false");
  });

  detailPanels.forEach((detailPanel) => {
    if (detailPanel.dataset.detailId !== nextDetailId) {
      resetDetailPanel(detailPanel, true);
    }

    detailPanel.hidden = detailPanel.dataset.detailId !== nextDetailId;
  });

  if (emptyDetail) {
    emptyDetail.hidden = nextDetailId !== "";
  }

  if (masterDetail) {
    masterDetail.dataset.detailOpen = nextDetailId !== "" ? "true" : "false";
    syncMasterDetailMode(masterDetail);
  }

  if (detailColumn) {
    detailColumn.hidden = nextDetailId === "";
  }

  panel.dataset.selectedDetailId = nextDetailId;
}

function applyPanelFilters(panel) {
  const searchInput = panel.querySelector("[data-admin-search]");
  const statusFilter = panel.querySelector("[data-admin-status-filter]");
  const collectionFilter = panel.querySelector("[data-admin-collection-filter]");
  const items = getPanelItems(panel);
  const noResults = panel.querySelector("[data-admin-no-results]");

  if (items.length === 0) {
    return;
  }

  const query = searchInput ? searchInput.value.trim().toLowerCase() : "";
  const statusValue = statusFilter ? statusFilter.value : "all";
  const collectionValue = collectionFilter ? collectionFilter.value : "all";
  let visibleCount = 0;

  items.forEach((item) => {
    const itemText = item.textContent.toLowerCase();
    const isActive = item.dataset.active === "1";
    const isFeatured = item.dataset.featured === "1";
    const matchesSearch = query === "" || itemText.includes(query);
    const matchesCollection =
      collectionValue === "all" || item.dataset.collectionId === collectionValue;
    const matchesStatus =
      statusValue === "all" ||
      (statusValue === "active" && isActive) ||
      (statusValue === "inactive" && !isActive) ||
      (statusValue === "featured" && isFeatured);

    const shouldShow = matchesSearch && matchesCollection && matchesStatus;
    item.hidden = !shouldShow;

    if (shouldShow) {
      visibleCount += 1;
    }
  });

  if (noResults) {
    noResults.hidden = visibleCount !== 0;
  }

  setDetailSelection(panel, panel.dataset.selectedDetailId || "", true);
}

function bindPanelFilters() {
  panelControls.forEach((toolbar) => {
    const panel = toolbar.closest("[data-admin-panel]");

    if (!panel) {
      return;
    }

    const searchInput = toolbar.querySelector("[data-admin-search]");
    const statusFilter = toolbar.querySelector("[data-admin-status-filter]");
    const collectionFilter = toolbar.querySelector("[data-admin-collection-filter]");
    const sortSelect = toolbar.querySelector("[data-admin-sort]");

    searchInput?.addEventListener("input", () => applyPanelFilters(panel));
    statusFilter?.addEventListener("change", () => applyPanelFilters(panel));
    collectionFilter?.addEventListener("change", () => applyPanelFilters(panel));
    sortSelect?.addEventListener("change", () => {
      sortPanelItems(panel);
      applyPanelFilters(panel);
    });
    sortPanelItems(panel);
    applyPanelFilters(panel);
  });
}

function bindCollapseToggle() {
  if (!collapseToggle || !collapseBody) {
    return;
  }

  collapseToggle.addEventListener("click", () => {
    const isCollapsed = collapseBody.classList.toggle("is-collapsed");
    collapseToggle.setAttribute("aria-expanded", String(!isCollapsed));
    collapseToggle.textContent = isCollapsed ? "Mostrar formularios" : "Ocultar formularios";
  });
}

function bindDetailSelectors() {
  panels.forEach((panel) => {
    const items = getPanelItems(panel);
    const closeButtons = Array.from(panel.querySelectorAll("[data-admin-close-detail]"));
    const editToggles = Array.from(panel.querySelectorAll("[data-admin-edit-toggle]"));
    const cancelButtons = Array.from(panel.querySelectorAll("[data-admin-cancel-edit]"));

    items.forEach((item) => {
      item.addEventListener("click", () => {
        setDetailSelection(panel, item.dataset.detailId || "");
      });
    });

    closeButtons.forEach((button) => {
      button.addEventListener("click", () => {
        const detailPanel = button.closest("[data-admin-detail-panel]");
        resetDetailPanel(detailPanel, true);
        setDetailSelection(panel, "", true);
      });
    });

    editToggles.forEach((button) => {
      button.addEventListener("click", () => {
        const detailPanel = button.closest("[data-admin-detail-panel]");

        if (!detailPanel) {
          return;
        }

        setDetailMode(detailPanel, "edit");
      });
    });

    cancelButtons.forEach((button) => {
      button.addEventListener("click", () => {
        const detailPanel = button.closest("[data-admin-detail-panel]");

        resetDetailPanel(detailPanel, true);
      });
    });

    getDetailPanels(panel).forEach((detailPanel) => {
      setDetailMode(detailPanel, "view");
    });

    setDetailSelection(panel, panel.dataset.selectedDetailId || "", true);
  });
}

function setActiveView(viewId) {
  if (panels.length === 0) {
    return;
  }

  const normalizedViewId = normalizeViewId(viewId);
  const activePanel = panels.find((panel) => panel.id === normalizedViewId) || panels[0];

  panels.forEach((panel) => {
    panel.hidden = panel !== activePanel;
  });

  navLinks.forEach((link) => {
    const targetId = normalizeViewId(new URL(link.href).hash);
    const isActive = targetId === activePanel.id;
    link.setAttribute("aria-current", isActive ? "page" : "false");
  });

  document.body.dataset.adminView = activePanel.id;
  setDetailSelection(activePanel, activePanel.dataset.selectedDetailId || "", true);
}

function syncViewFromHash() {
  setActiveView(window.location.hash);
}

function initAdmin() {
  syncViewFromHash();
  bindCollapseToggle();
  bindPanelFilters();
  bindDetailSelectors();
  bindImageFields();
  bindMediaRelationForms();
  bindSectionVisualSettings();
  bindOpenMediaButtons();
}

window.addEventListener("hashchange", syncViewFromHash);

if (document.readyState === "loading") {
  window.addEventListener("DOMContentLoaded", initAdmin, { once: true });
} else {
  initAdmin();
}
