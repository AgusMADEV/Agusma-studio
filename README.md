# Agusma-studio

Base para servir la web desde XAMPP con datos dinamicos en MySQL, APIs PHP propias y administracion modular.

## Documentacion principal

- [GUIA_IMPLEMENTACION_CONTENIDO.md](GUIA_IMPLEMENTACION_CONTENIDO.md): explicacion completa de la arquitectura actual, migraciones, APIs, frontend, admin y flujo editorial.

## Estructura anadida

- [config/database.php](config/database.php): configuracion central de PDO para MySQL.
- [config/database.example.php](config/database.example.php): plantilla de configuracion local segura.
- [public/api/featured-collections.php](public/api/featured-collections.php): endpoint JSON que devuelve las colecciones destacadas.
- [public/api/categories.php](public/api/categories.php): endpoint JSON que devuelve las categorias activas.
- [public/api/entities.php](public/api/entities.php): endpoint JSON que devuelve entidades por categoria.
- [public/api/collections.php](public/api/collections.php): endpoint JSON que devuelve colecciones por entidad.
- [public/api/collection-detail.php](public/api/collection-detail.php): endpoint JSON que devuelve el detalle completo de una coleccion.
- [admin/index.php](admin/index.php): panel modular para categorias, entidades, colecciones, piezas y multimedia.
- [database/schema.sql](database/schema.sql): script para instalaciones nuevas.
- [database/migrations/20260724_categories_admin.sql](database/migrations/20260724_categories_admin.sql): migracion anterior de categorias y admin.
- [database/migrations/20260725_content_architecture.sql](database/migrations/20260725_content_architecture.sql): migracion principal de la nueva arquitectura de contenido.

## Como conectarlo con phpMyAdmin

1. Abre http://localhost/phpmyadmin.
2. Si es una instalacion nueva, crea la base de datos importando [database/schema.sql](database/schema.sql) desde la pestana Importar.
3. Si ya existe una base anterior del proyecto, importa [database/migrations/20260725_content_architecture.sql](database/migrations/20260725_content_architecture.sql).
4. Configura la conexion local en [config/database.local.php](config/database.local.php) usando la plantilla de [config/database.example.php](config/database.example.php), o define las variables de entorno AGUSMA_DB_*.
5. Sirve la web desde Apache con una URL tipo http://localhost/Agusma-studio/public/.
6. Abre el panel en http://localhost/Agusma-studio/admin/.

## Flujo actual de datos

La portada carga categorias y colecciones destacadas desde [public/api/categories.php](public/api/categories.php) y [public/api/featured-collections.php](public/api/featured-collections.php). Las paginas de categoria cargan entidades y luego colecciones, y las vistas dinamicas [public/entity.php](public/entity.php) y [public/collection.php](public/collection.php) completan la navegacion del contenido.

## Siguiente paso natural

La guia completa del sistema esta en [GUIA_IMPLEMENTACION_CONTENIDO.md](GUIA_IMPLEMENTACION_CONTENIDO.md).