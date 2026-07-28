# Guia de Implementacion de la Nueva Arquitectura de Contenido

## 1. Objetivo de esta fase

En esta iteracion el proyecto dejo atras el modelo inicial, que solo distinguia categorias y colecciones destacadas, y paso a una arquitectura de contenido mucho mas escalable.

El objetivo fue mantener el stack actual:

- PHP sin frameworks.
- MySQL con SQL directo.
- PDO como capa de acceso a datos.
- XAMPP como entorno local.

Y al mismo tiempo resolver estas limitaciones del modelo anterior:

- No habia una jerarquia real entre categoria, entidad, coleccion y pieza.
- National Teams estaba modelado como categoria publica independiente, cuando en realidad debia vivir dentro de Football.
- El frontend dependia demasiado de featured_collections como fuente principal.
- El admin estaba concentrado en un unico archivo y no escalaba bien.
- Las APIs no tenian un contrato uniforme.

El resultado final de esta fase es una arquitectura basada en contenido real y reusable:

- Categoria -> Entidad -> Coleccion -> Pieza -> Multimedia

---

## 2. Arquitectura final de datos

### 2.1 Jerarquia de contenido

La estructura nueva queda asi:

1. Categoria
2. Entidad
3. Coleccion
4. Pieza
5. Multimedia

Ademas se anadieron tags para clasificar colecciones.

### 2.2 Tablas principales

#### categories

Representa los grandes bloques publicos de navegacion.

Ejemplos:

- Football
- Fashion
- Special Editions

Campos relevantes:

- name
- slug
- short_description
- description
- visual_key
- cover_image
- hero_image
- link_url
- display_order
- is_active

#### entities

Representa el sujeto o agrupador real dentro de una categoria.

Ejemplos:

- Real Madrid
- Portugal
- AgusMA Studio
- AgusMA Studio Lab

Campos relevantes:

- category_id
- name
- slug
- entity_type
- subtitle
- short_description
- description
- logo_url
- cover_image
- primary_color
- secondary_color
- background_color
- text_color
- display_order
- is_featured
- is_active

El campo entity_type es importante porque permite clasificar entidades sin convertir cada tipo en categoria publica. Por eso National Teams deja de ser categoria y pasa a convivir dentro de Football como entity_type = national_team.

#### collections

Representa una coleccion concreta asociada a una entidad.

Ejemplos:

- Real Madrid 2026/27
- Cristiano Ronaldo Legacy Collection
- Essentials Collection

Campos relevantes:

- entity_id
- name
- slug
- subtitle
- collection_year
- season
- short_description
- description
- concept
- cover_image
- thumbnail_image
- primary_color
- secondary_color
- background_color
- text_color
- image_variant
- layout_style
- display_order
- is_featured
- is_active
- published_at

#### pieces

Representa una pieza interna de una coleccion.

Ejemplos:

- Home Kit
- Away Kit
- Hoodie
- Concept Poster

Campos relevantes:

- collection_id
- name
- slug
- piece_type
- subtitle
- short_description
- description
- cover_image
- display_order
- is_featured
- is_active

#### media

Representa activos multimedia asociados a una coleccion completa o a una pieza concreta.

Campos relevantes:

- collection_id
- piece_id
- media_type
- file_url
- thumbnail_url
- title
- alt_text
- caption
- section_key
- display_order
- is_cover
- is_active

#### tags y collection_tags

Permiten clasificar colecciones con una taxonomia reusable.

Ejemplos:

- archive
- football
- fashion
- special-edition

---

## 3. Estrategia de migracion

Se prepararon dos caminos distintos:

### 3.1 Instalacion limpia desde cero

Archivo principal:

- [database/schema.sql](database/schema.sql)

Este archivo crea la estructura completa nueva y deja una base funcional desde cero con datos iniciales de ejemplo.

### 3.2 Migracion sobre una base existente

Archivo principal:

- [database/migrations/20260725_content_architecture.sql](database/migrations/20260725_content_architecture.sql)

Esta migracion hace varias cosas importantes:

1. Amplia la tabla categories con descripciones y portada.
2. Inserta o normaliza las categorias canonicas publicas.
3. Marca national-teams como fila legacy inactiva.
4. Crea las tablas entities, collections, pieces, media, tags y collection_tags.
5. Migra el contenido legacy de featured_collections hacia collections.
6. Inserta entidades y colecciones canonicas de ejemplo.
7. Conserva la tabla featured_collections como legacy temporal en lugar de destruirla.

### 3.3 Decision clave sobre National Teams

National Teams ya no se trata como categoria publica. La web publica ahora expone Football y dentro de Football filtra entidades por tipo:

- club
- national_team
- all

Eso permite una navegacion mas coherente sin perder contenido historico.

---

## 4. Configuracion de base de datos

### 4.1 Archivo principal de conexion

- [config/database.php](config/database.php)

Esta capa centraliza la conexion PDO y permite dos fuentes de configuracion:

1. Variables de entorno AGUSMA_DB_*
2. Archivo local [config/database.local.php](config/database.local.php)

Tambien existe una plantilla segura:

- [config/database.example.php](config/database.example.php)

Y el archivo local queda fuera del repositorio mediante:

- [.gitignore](.gitignore)

### 4.2 Comportamiento actual

Si no hay usuario configurado, [config/database.php](config/database.php) lanza una excepcion explicita para evitar fallos silenciosos.

En el entorno local actual se ajusto [config/database.local.php](config/database.local.php) para usar las credenciales reales 
del MySQL de XAMPP.

---

## 5. Backend compartido de contenido

### 5.1 Repositorio central

- [includes/content-repository.php](includes/content-repository.php)

Este archivo concentra la logica de lectura de contenido. La idea es evitar SQL repetido repartido por APIs, vistas publicas y panel admin.

Funciones principales:

- contentFetchCategories
- contentFetchCategoryBySlug
- contentFetchEntityBySlug
- contentFetchEntitiesByCategory
- contentFetchCollectionsByEntity
- contentFetchCollectionRecord
- contentFetchPiecesForCollection
- contentFetchCollectionMedia
- contentFetchCollectionTags
- contentFetchCollectionDetail
- contentFetchFeaturedCollections

La funcion mas importante para el detalle publico es contentFetchCollectionDetail, porque devuelve una estructura agregada con:

- category
- entity
- collection
- pieces
- media
- media_by_piece
- tags

---

## 6. Contrato uniforme de APIs

### 6.1 Helpers comunes

- [public/api/_helpers.php](public/api/_helpers.php)

Aqui se unifico el comportamiento comun de los endpoints:

- respuesta JSON correcta
- acceso a la conexion PDO
- validacion de slugs
- manejo seguro de errores

### 6.2 Formato de respuesta

Todas las APIs nuevas siguen este contrato:

Respuesta de exito:

```json
{
  "success": true,
  "data": {},
  "message": null
}
```

Respuesta de error:

```json
{
  "success": false,
  "data": null,
  "message": "Mensaje seguro para frontend"
}
```

### 6.3 Endpoints principales

- [public/api/categories.php](public/api/categories.php)
  Devuelve categorias activas publicas.

- [public/api/entities.php](public/api/entities.php)
  Devuelve entidades por categoria, con filtro opcional type.

- [public/api/collections.php](public/api/collections.php)
  Devuelve colecciones para una entidad concreta.

- [public/api/collection-detail.php](public/api/collection-detail.php)
  Devuelve el detalle completo de una coleccion.

- [public/api/pieces.php](public/api/pieces.php)
  Devuelve las piezas de una coleccion.

- [public/api/featured-collections.php](public/api/featured-collections.php)
  Devuelve las colecciones destacadas usando collections.is_featured.

### 6.4 Cambio importante respecto al modelo anterior

Antes la home dependia directamente de featured_collections como tabla principal. Ahora la home obtiene las destacadas desde collections, y featured_collections solo se conserva para migracion y trazabilidad legacy.

---

## 7. Frontend publico nuevo

### 7.1 Conversion a PHP modular

Las paginas publicas principales ya no estan montadas como HTML suelto. Se apoyan en includes compartidos:

- [public/includes/head.php](public/includes/head.php)
- [public/includes/seo-meta.php](public/includes/seo-meta.php)
- [public/includes/site-header.php](public/includes/site-header.php)
- [public/includes/site-footer.php](public/includes/site-footer.php)
- [public/includes/category-rail.php](public/includes/category-rail.php)
- [public/includes/request.php](public/includes/request.php)

Esto permite:

- mantener un header consistente
- compartir SEO y footer
- reutilizar parseo de slugs
- reducir duplicacion

### 7.2 Paginas publicas actuales

- [public/index.php](public/index.php)
  Home con categorias y colecciones destacadas.

- [public/football.php](public/football.php)
  Navegacion Football -> entidades -> colecciones.

- [public/fashion.php](public/fashion.php)
  Navegacion Fashion -> entidades -> colecciones.

- [public/special-editions.php](public/special-editions.php)
  Navegacion Special Editions -> entidades -> colecciones.

- [public/entity.php](public/entity.php)
  Vista dinamica para una entidad concreta.

- [public/collection.php](public/collection.php)
  Vista dinamica para una coleccion concreta.

### 7.3 Flujo de navegacion publico

El flujo final ya no es categoria -> featured collection sin contexto.

Ahora el flujo es:

1. Home
2. Categoria
3. Entidad
4. Coleccion
5. Pieza y multimedia

### 7.4 Modulos JavaScript

- [public/js/modules/api.js](public/js/modules/api.js)
  Capa comun de fetch para todas las APIs.

- [public/js/modules/content-ui.js](public/js/modules/content-ui.js)
  Construccion de cards y enlaces para entidades y colecciones.

- [public/js/modules/category-browser.js](public/js/modules/category-browser.js)
  Controlador generico para Category -> Entity -> Collection.

- [public/js/main.js](public/js/main.js)
  Home.

- [public/js/football.js](public/js/football.js)
  Inicializa el browser de Football.

- [public/js/category-page.js](public/js/category-page.js)
  Inicializa el browser para Fashion y Special Editions.

- [public/js/entity-page.js](public/js/entity-page.js)
  Carga una entidad concreta y sus colecciones.

- [public/js/collection-page.js](public/js/collection-page.js)
  Carga el detalle completo de una coleccion.

### 7.5 Estilos asociados

- [public/css/base.css](public/css/base.css)
- [public/css/style.css](public/css/style.css)
- [public/css/category-showcase.css](public/css/category-showcase.css)
- [public/css/football.css](public/css/football.css)
- [public/css/collection-detail.css](public/css/collection-detail.css)
- [public/css/admin.css](public/css/admin.css)

---

## 8. Admin modularizado

### 8.1 Objetivo

El panel admin original era demasiado compacto para crecer con el nuevo modelo. Se separo la logica en varios archivos con responsabilidades claras.

### 8.2 Bootstrap y utilidades

- [admin/includes/bootstrap.php](admin/includes/bootstrap.php)
  Normalizacion de POST, redirecciones y validaciones comunes.

- [admin/includes/view-helpers.php](admin/includes/view-helpers.php)
  Helpers de render y escape para la vista del admin.

- [admin/includes/page-data.php](admin/includes/page-data.php)
  Carga datasets completos del dashboard.

### 8.3 Acciones CRUD

- [admin/actions/content-actions.php](admin/actions/content-actions.php)
- [admin/actions/handle-request.php](admin/actions/handle-request.php)

Estas capas permiten crear, editar y borrar registros de:

- categories
- entities
- collections
- pieces
- media

Tambien se anadieron validaciones relevantes:

- evitar que National Teams vuelva a crearse como categoria publica
- comprobar que una coleccion exista antes de crear media
- comprobar que una pieza pertenezca a la coleccion indicada
- trabajar con transacciones por cada accion POST

### 8.4 Vista principal del panel

- [admin/index.php](admin/index.php)

El panel ya renderiza formularios y listados editables para todo el modelo principal:

- categorias
- entidades
- colecciones
- piezas
- multimedia

Ademas muestra informacion sobre el estado legacy de featured_collections.

---

## 9. Casos de contenido ya sembrados

La migracion y el schema dejan varios casos funcionales para probar el sistema:

### Football

- Real Madrid
- Portugal
- Football Archive
- National Team Archive

Colecciones destacadas:

- Real Madrid 2026/27
- Cristiano Ronaldo Legacy Collection
- varias legacy migradas

### Fashion

- AgusMA Studio

Colecciones destacadas:

- Essentials Collection

### Special Editions

- AgusMA Studio Lab

Colecciones destacadas:

- Atelier Archive Vol. I

Esto deja ejemplos reales para validar todo el recorrido del frontend.

---

## 10. Decisiones de implementacion importantes

### 10.1 No se uso framework

Todo se mantuvo con PHP procedural/modular y PDO, tal como pediste. No hay Laravel, no hay ORM, no hay capa Node, no hay build server obligatorio.

### 10.2 Migracion sin destruccion de datos

La tabla featured_collections no se elimino. Se dejo como legado temporal para no romper historico ni trazabilidad.

### 10.3 National Teams deja de ser categoria publica

Se resolvio a nivel de datos, de admin y de frontend. La categoria legacy se desactiva y el contenido se reubica en Football mediante entidades de tipo national_team.

### 10.4 La home ya no depende del modelo viejo

La home sigue mostrando destacadas, pero la fuente de verdad pasa a ser collections.is_featured.

### 10.5 La navegacion publica ahora tiene contexto real

Antes una coleccion podia aparecer suelta. Ahora siempre existe el camino:

- categoria
- entidad
- coleccion

Eso mejora coherencia, escalabilidad y administracion editorial.

---

## 11. Validacion realizada durante la implementacion

Se validaron de forma iterativa:

- sintaxis PHP con php -l en los archivos principales
- errores de editor sobre PHP, JS y CSS tocados
- respuesta real de APIs clave
- conexion MySQL real en entorno XAMPP

Casos confirmados al cierre de esta fase:

- [public/api/categories.php](public/api/categories.php) responde correctamente.
- [public/api/featured-collections.php](public/api/featured-collections.php) responde correctamente.
- [config/database.local.php](config/database.local.php) ya estaba alineado con el acceso real de MySQL en este entorno.
- Los modulos JS nuevos no tienen errores de parseo pendientes.

---

## 12. Como probar el sistema de punta a punta

### 12.1 Preparacion

1. Arranca Apache y MySQL en XAMPP.
2. Asegurate de que la base agusma_studio exista.
3. Si partes de cero, importa [database/schema.sql](database/schema.sql).
4. Si ya tenias base anterior, importa [database/migrations/20260725_content_architecture.sql](database/migrations/20260725_content_architecture.sql).
5. Revisa [config/database.local.php](config/database.local.php).

### 12.2 Probar APIs

Prueba estas rutas en navegador:

- /Agusma-studio/public/api/categories.php
- /Agusma-studio/public/api/entities.php?category=football
- /Agusma-studio/public/api/entities.php?category=football&type=club
- /Agusma-studio/public/api/entities.php?category=football&type=national_team
- /Agusma-studio/public/api/collections.php?category=football&entity=real-madrid
- /Agusma-studio/public/api/collection-detail.php?category=football&entity=real-madrid&collection=real-madrid-2026-27

### 12.3 Probar frontend

1. Abre /Agusma-studio/public/index.php
2. Comprueba que carga categorias.
3. Comprueba que carga featured collections.
4. Entra en Football.
5. Cambia entre All Football, Club Football y National Teams.
6. Selecciona una entidad.
7. Comprueba que aparecen sus colecciones.
8. Entra a una coleccion.
9. Comprueba que se renderizan tags, piezas y multimedia.

### 12.4 Probar admin

1. Abre /Agusma-studio/admin/
2. Crea o edita una entidad.
3. Crea o edita una coleccion.
4. Crea o edita una pieza.
5. Crea o edita multimedia.
6. Recarga el frontend y verifica que los cambios aparecen.

---

## 13. Flujo editorial recomendado

Para crear contenido nuevo de forma ordenada, el flujo correcto es:

1. Crear o elegir una categoria.
2. Crear o elegir una entidad dentro de esa categoria.
3. Crear una coleccion dentro de la entidad.
4. Crear piezas dentro de la coleccion.
5. Asociar multimedia a la coleccion o a piezas concretas.
6. Marcar la coleccion como featured si debe aparecer en la home.

Ejemplo real:

1. Categoria: Football
2. Entidad: Real Madrid
3. Coleccion: Real Madrid 2026/27
4. Piezas: Home Kit, Away Kit, Third Kit, Goalkeeper Kit
5. Multimedia: assets de portada, mockups, imagenes por pieza

---

## 14. Que queda preparado para fases siguientes

La base ya esta lista para continuar con mejoras sin reabrir la arquitectura:

- administracion de tags desde el panel
- filtros visuales por tags o season
- reemplazo de textos estaticos restantes por contenido editable
- mas entidades por categoria
- mas plantillas visuales de coleccion
- enriquecimiento de multimedia por secciones

La parte dificil de la reestructuracion ya esta resuelta: datos, APIs, panel y navegacion publica trabajan sobre el mismo modelo.

---

## 15. Archivos clave de referencia

### Base de datos

- [database/schema.sql](database/schema.sql)
- [database/migrations/20260724_categories_admin.sql](database/migrations/20260724_categories_admin.sql)
- [database/migrations/20260725_content_architecture.sql](database/migrations/20260725_content_architecture.sql)

### Configuracion

- [config/database.php](config/database.php)
- [config/database.example.php](config/database.example.php)
- [config/database.local.php](config/database.local.php)

### Backend compartido

- [includes/content-repository.php](includes/content-repository.php)
- [public/api/_helpers.php](public/api/_helpers.php)

### APIs publicas

- [public/api/categories.php](public/api/categories.php)
- [public/api/entities.php](public/api/entities.php)
- [public/api/collections.php](public/api/collections.php)
- [public/api/collection-detail.php](public/api/collection-detail.php)
- [public/api/pieces.php](public/api/pieces.php)
- [public/api/featured-collections.php](public/api/featured-collections.php)

### Frontend publico

- [public/index.php](public/index.php)
- [public/football.php](public/football.php)
- [public/fashion.php](public/fashion.php)
- [public/special-editions.php](public/special-editions.php)
- [public/entity.php](public/entity.php)
- [public/collection.php](public/collection.php)

### JS publico

- [public/js/modules/api.js](public/js/modules/api.js)
- [public/js/modules/content-ui.js](public/js/modules/content-ui.js)
- [public/js/modules/category-browser.js](public/js/modules/category-browser.js)
- [public/js/main.js](public/js/main.js)
- [public/js/football.js](public/js/football.js)
- [public/js/category-page.js](public/js/category-page.js)
- [public/js/entity-page.js](public/js/entity-page.js)
- [public/js/collection-page.js](public/js/collection-page.js)

### Admin

- [admin/index.php](admin/index.php)
- [admin/actions/content-actions.php](admin/actions/content-actions.php)
- [admin/actions/handle-request.php](admin/actions/handle-request.php)
- [admin/includes/bootstrap.php](admin/includes/bootstrap.php)
- [admin/includes/view-helpers.php](admin/includes/view-helpers.php)
- [admin/includes/page-data.php](admin/includes/page-data.php)

---

## 16. Resumen ejecutivo

Lo que se ha implementado en esta fase no es un simple cambio visual. Es una reconstruccion del sistema de contenido para que Agusma Studio pueda crecer sin volver a quedarse atrapado en una estructura plana.

Ahora el proyecto tiene:

- una arquitectura de datos seria
- APIs coherentes
- un admin preparado para escalar
- frontend dinamico con contexto real
- migracion controlada sin tirar datos legacy

En otras palabras: la web ya no esta atada a tarjetas sueltas, sino a un modelo editorial completo.