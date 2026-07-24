# Agusma-studio

Base inicial para servir la web desde XAMPP con datos dinamicos en MySQL y administracion desde phpMyAdmin.

## Estructura anadida

- [config/database.php](config/database.php): configuracion central de PDO para MySQL.
- [public/api/featured-collections.php](public/api/featured-collections.php): endpoint JSON que devuelve las colecciones destacadas.
- [public/api/categories.php](public/api/categories.php): endpoint JSON que devuelve las categorias activas.
- [admin/index.php](admin/index.php): panel basico para crear y editar categorias y colecciones.
- [database/schema.sql](database/schema.sql): script para instalaciones nuevas.
- [database/migrations/20260724_categories_admin.sql](database/migrations/20260724_categories_admin.sql): migracion para bases existentes.

## Como conectarlo con phpMyAdmin

1. Abre http://localhost/phpmyadmin.
2. Si es una instalacion nueva, crea la base de datos importando [database/schema.sql](database/schema.sql) desde la pestana Importar.
3. Si ya tenias la tabla featured_collections creada, importa primero [database/migrations/20260724_categories_admin.sql](database/migrations/20260724_categories_admin.sql).
4. Revisa las credenciales en [config/database.php](config/database.php). Ahora mismo el proyecto esta configurado con:
	- host: 127.0.0.1
	- puerto: 3306
	- base de datos: agusma_studio
	- usuario: agusma-studio
	- contrasena: agusma-studio
5. Sirve la web desde Apache con una URL tipo http://localhost/Agusma-studio/public/.
6. Abre el panel en http://localhost/Agusma-studio/admin/.

## Flujo actual de datos

La portada carga categorias y colecciones destacadas desde [public/api/categories.php](public/api/categories.php) y [public/api/featured-collections.php](public/api/featured-collections.php). El frontend hace fetch desde [public/js/main.js](public/js/main.js) y reemplaza las tarjetas estaticas por las filas almacenadas en MySQL.

## Siguiente paso natural

Si vas a manejar mas contenido dinamico, conviene repetir este patron por secciones: tabla MySQL, endpoint PHP y consumo desde el frontend.