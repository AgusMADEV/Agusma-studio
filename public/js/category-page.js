import { initCategoryBrowser } from "./modules/category-browser.js";

initCategoryBrowser({
  rootSelector: "[data-category-browser]",
  entityGridSelector: "[data-entity-grid]",
  entityStatusSelector: "[data-entity-status]",
  collectionGridSelector: "[data-collection-grid]",
  collectionStatusSelector: "[data-collection-status]",
  selectedEntityNameSelector: "[data-selected-entity-name]",
  countSelector: "[data-category-count]",
});