# Draft / Preview / Publish

## 1. Ejecutar la migración

Antes de abrir el panel con esta versión, ejecutar una sola vez:

`database/migrations/20260831_collection_publishing.sql`

La migración añade `collections.preview_token`, genera un token para las colecciones existentes y crea un índice único.

## 2. Estados editoriales

- Draft: `published_at` es NULL.
- Published: `published_at` tiene una fecha igual o anterior al momento actual y la colección está activa.
- Desactivada: `is_active = 0`.

`published_at` ya no se edita manualmente en el formulario. Se controla con Publish / Volver a Draft.

## 3. Preview

El panel genera una URL de este tipo:

`collection.php?category=...&entity=...&collection=...&preview=<token>`

La API valida el token con `hash_equals`. Los previews usan `noindex,nofollow,noarchive` y muestran una banda de PREVIEW.

El botón "Regenerar preview" invalida el enlace anterior.

## 4. Visibilidad pública

Los endpoints públicos de colecciones, detalle, piezas y destacados solo exponen colecciones activas con:

`published_at IS NOT NULL AND published_at <= NOW()`

Por tanto un Draft no aparece en listados ni puede abrirse mediante su URL pública normal.
