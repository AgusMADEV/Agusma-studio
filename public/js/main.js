const header = document.querySelector(".header");
const categoryGrid = document.querySelector("[data-category-grid]");
const categoryStatus = document.querySelector("[data-category-status]");
const featuredGrid = document.querySelector("[data-featured-grid]");
const featuredStatus = document.querySelector("[data-featured-status]");

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
    const response = await fetch("./api/featured-collections.php");

    if (!response.ok) {
      throw new Error("La respuesta del servidor no fue valida.");
    }

    const payload = await response.json();

    if (!Array.isArray(payload.data) || payload.data.length === 0) {
      featuredStatus.textContent = "No hay colecciones destacadas publicadas todavia.";
      featuredGrid.innerHTML = "";
      return;
    }

    featuredGrid.replaceChildren(
      ...payload.data.map((collection) => createCollectionCard(collection))
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
    const response = await fetch("./api/categories.php");

    if (!response.ok) {
      throw new Error("La respuesta del servidor no fue valida.");
    }

    const payload = await response.json();

    if (!Array.isArray(payload.data) || payload.data.length === 0) {
      categoryStatus.textContent = "No hay categorias publicadas todavia.";
      return;
    }

    categoryGrid.replaceChildren(
      ...payload.data.map((category, index) => createCategoryCard(category, index))
    );
  } catch (error) {
    console.error(error);
    categoryStatus.textContent = "No se pudieron cargar las categorias desde la base de datos.";
  }
}

function createCollectionCard(collection) {
  const article = document.createElement("article");
  article.className = "collection-card";

  const image = document.createElement("div");
  image.className = collection.image_variant === "dark"
    ? "collection-card__image collection-card__image--dark"
    : "collection-card__image";

  const info = document.createElement("div");
  info.className = "collection-card__info";

  const title = document.createElement("h3");
  title.textContent = collection.title;

  const year = document.createElement("span");
  year.textContent = String(collection.collection_year);

  info.append(title, year);
  article.append(image, info);

  return article;
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
  link.href = category.link_url || "#";
  link.setAttribute("aria-label", `Open ${category.name} category`);
  link.textContent = "→";

  const visual = document.createElement("div");
  visual.className = "category-card__visual";
  visual.setAttribute("aria-hidden", "true");

  body.append(title, link);
  article.append(number, body, visual);

  return article;
}

loadCategories();
loadFeaturedCollections();