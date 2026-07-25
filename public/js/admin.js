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

function applyPanelFilters(panel) {
  const searchInput = panel.querySelector("[data-admin-search]");
  const statusFilter = panel.querySelector("[data-admin-status-filter]");
  const items = getPanelItems(panel);
  const noResults = panel.querySelector("[data-admin-no-results]");

  if (items.length === 0) {
    return;
  }

  const query = searchInput ? searchInput.value.trim().toLowerCase() : "";
  const statusValue = statusFilter ? statusFilter.value : "all";
  let visibleCount = 0;

  items.forEach((item) => {
    const itemText = item.textContent.toLowerCase();
    const isActive = item.querySelector('input[name="is_active"]')?.checked ?? true;
    const isFeatured = item.querySelector('input[name="is_featured"]')?.checked ?? false;
    const matchesSearch = query === "" || itemText.includes(query);
    const matchesStatus =
      statusValue === "all" ||
      (statusValue === "active" && isActive) ||
      (statusValue === "inactive" && !isActive) ||
      (statusValue === "featured" && isFeatured);

    const shouldShow = matchesSearch && matchesStatus;
    item.hidden = !shouldShow;

    if (shouldShow) {
      visibleCount += 1;
    }
  });

  if (noResults) {
    noResults.hidden = visibleCount !== 0;
  } else if (visibleCount === 0) {
    const emptyState = document.createElement("p");
    emptyState.className = "admin-no-results";
    emptyState.textContent = "No hay resultados con ese filtro.";
    emptyState.dataset.adminNoResults = "true";
    panel.querySelector(".admin-list")?.append(emptyState);
  }
}

function bindPanelFilters() {
  panelControls.forEach((toolbar) => {
    const panel = toolbar.closest("[data-admin-panel]");

    if (!panel) {
      return;
    }

    const searchInput = toolbar.querySelector("[data-admin-search]");
    const statusFilter = toolbar.querySelector("[data-admin-status-filter]");

    searchInput?.addEventListener("input", () => applyPanelFilters(panel));
    statusFilter?.addEventListener("change", () => applyPanelFilters(panel));
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
}

function syncViewFromHash() {
  setActiveView(window.location.hash);
}

window.addEventListener("hashchange", syncViewFromHash);
window.addEventListener("DOMContentLoaded", () => {
  syncViewFromHash();
  bindCollapseToggle();
  bindPanelFilters();
});
