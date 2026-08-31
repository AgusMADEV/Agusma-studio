async function fetchJson(url) {
  const response = await fetch(url);
  const payload = await response.json().catch(() => null);

  if (!response.ok || !payload || payload.success !== true) {
    throw new Error(payload?.message || "La respuesta del servidor no fue valida.");
  }

  return payload.data;
}

function buildQuery(params) {
  const query = new URLSearchParams();

  Object.entries(params).forEach(([key, value]) => {
    if (value !== null && value !== undefined && value !== "") {
      query.set(key, String(value));
    }
  });

  const serialized = query.toString();

  return serialized === "" ? "" : `?${serialized}`;
}

export async function fetchCategories() {
  return fetchJson("./api/categories.php");
}

export async function fetchEntities({ category, type = null }) {
  return fetchJson(`./api/entities.php${buildQuery({ category, type })}`);
}

export async function fetchCollections({ category, entity }) {
  return fetchJson(`./api/collections.php${buildQuery({ category, entity })}`);
}

export async function fetchCollectionDetail({ category, entity, collection, preview = null }) {
  return fetchJson(`./api/collection-detail.php${buildQuery({ category, entity, collection, preview })}`);
}

export async function fetchPieces({ category, entity, collection }) {
  return fetchJson(`./api/pieces.php${buildQuery({ category, entity, collection })}`);
}

export async function fetchFeaturedCollections() {
  return fetchJson("./api/featured-collections.php");
}