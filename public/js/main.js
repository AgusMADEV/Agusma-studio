import { fetchCategories, fetchFeaturedCollections } from "./modules/api.js";
import { createCollectionCard } from "./modules/content-ui.js";

const header = document.querySelector(".header");
const categoryGrid = document.querySelector("[data-category-grid]");
const categoryStatus = document.querySelector("[data-category-status]");
const featuredGrid = document.querySelector("[data-featured-grid]");
const featuredStatus = document.querySelector("[data-featured-status]");

const defaultCategoryRoutes = {
  football: "./football.php",
  fashion: "./fashion.php",
  "special-editions": "./special-editions.php",
};

window.addEventListener("scroll", () => {
  if (window.scrollY > 40) {
    header.classList.add("is-scrolled");
  } else {
    header.classList.remove("is-scrolled");
  }
});

async function loadFeaturedCollections() {
  if (!featuredGrid || !featuredStatus) {
    return;
  }

  try {
    const collections = await fetchFeaturedCollections();

    if (!Array.isArray(collections) || collections.length === 0) {
      featuredStatus.textContent = "No hay colecciones destacadas publicadas todavia.";
      featuredGrid.innerHTML = "";
      return;
    }

    featuredGrid.replaceChildren(
      ...collections.map((collection) => createCollectionCard(collection))
    );

    featuredStatus.hidden = true;
  } catch (error) {
    console.error(error);
    featuredStatus.textContent = "No se pudieron cargar las colecciones desde la base de datos.";
  }
}

async function loadCategories() {
  if (!categoryGrid || !categoryStatus) {
    return;
  }

  try {
    const categories = await fetchCategories();

    if (!Array.isArray(categories) || categories.length === 0) {
      categoryStatus.textContent = "No hay categorias publicadas todavia.";
      return;
    }

    categoryGrid.replaceChildren(
      ...categories.map((category, index) => createCategoryCard(category, index))
    );
  } catch (error) {
    console.error(error);
    categoryStatus.textContent = "No se pudieron cargar las categorias desde la base de datos.";
  }
}

function createCategoryCard(category, index) {
  const article = document.createElement("article");
  article.className = `category-card category-card--${category.visual_key}`;

  const number = document.createElement("span");
  number.textContent = String(index + 1).padStart(2, "0");

  const body = document.createElement("div");
  body.className = "category-card__body";

  const title = document.createElement("h2");
  title.textContent = category.name;

  const link = document.createElement("a");
  link.href = resolveCategoryHref(category);
  link.setAttribute("aria-label", `Open ${category.name} category`);
  link.textContent = "→";

  const visual = document.createElement("div");
  visual.className = "category-card__visual";
  visual.setAttribute("aria-hidden", "true");

  body.append(title, link);
  article.append(number, body, visual);

  return article;
}

function resolveCategoryHref(category) {
  const configuredHref = typeof category.link_url === "string"
    ? category.link_url.trim()
    : "";

  if (configuredHref !== "" && configuredHref !== "#") {
    return configuredHref;
  }

  return defaultCategoryRoutes[category.slug] || "#";
}

loadCategories();
loadFeaturedCollections();