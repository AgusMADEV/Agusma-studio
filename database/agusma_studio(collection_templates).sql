-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-08-2026 a las 09:11:28
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `agusma_studio`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `short_description` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `visual_key` varchar(60) NOT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `hero_image` varchar(255) DEFAULT NULL,
  `link_url` varchar(255) NOT NULL DEFAULT '#',
  `display_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `short_description`, `description`, `visual_key`, `cover_image`, `hero_image`, `link_url`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Football', 'football', 'Club football, national teams and collectible kit concepts.', 'Football gathers club projects, national team narratives and archive-driven kit systems inside a single category.', 'football', './assets/images/cat-football2.png', './assets/images/cat-football2.png', './football.php', 1, 1, '2026-07-24 16:46:34', '2026-08-04 06:42:32'),
(2, 'National Teams', 'national-teams', 'Legacy row kept only for historical migration.', 'National Teams has been absorbed into Football entities using entity_type = national_team.', 'national-teams', NULL, NULL, '#', 2, 0, '2026-07-24 16:46:34', '2026-07-25 14:29:07'),
(3, 'Fashion', 'fashion', 'Editorial garments, styling systems and wearable concepts.', 'Fashion collects AgusMA Studio garment concepts and styling-led editorial explorations.', 'fashion', './assets/images/cat-fashion.png', './assets/images/cat-fashion.png', './fashion.php', 3, 1, '2026-07-24 16:46:34', '2026-08-04 06:40:35'),
(4, 'Special Editions', 'special-editions', 'Limited releases and experimental collectible concepts.', 'Special Editions focuses on rarer releases, collectible drops and exceptional concept capsules.', 'special-editions', './assets/images/cat-special.png', './assets/images/cat-special.png', './special-editions.php', 4, 1, '2026-07-24 16:46:34', '2026-08-04 06:40:35');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `collections`
--

CREATE TABLE `collections` (
  `id` int(10) UNSIGNED NOT NULL,
  `entity_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(180) NOT NULL,
  `slug` varchar(180) NOT NULL,
  `subtitle` varchar(191) DEFAULT NULL,
  `collection_year` smallint(5) UNSIGNED DEFAULT NULL,
  `season` varchar(60) DEFAULT NULL,
  `short_description` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `concept` text DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `thumbnail_image` varchar(255) DEFAULT NULL,
  `primary_color` varchar(30) DEFAULT NULL,
  `secondary_color` varchar(30) DEFAULT NULL,
  `background_color` varchar(30) DEFAULT NULL,
  `text_color` varchar(30) DEFAULT NULL,
  `image_variant` varchar(30) DEFAULT NULL,
  `layout_style` varchar(60) DEFAULT NULL,
  `template_id` smallint(5) UNSIGNED DEFAULT NULL,
  `template_variant_id` smallint(5) UNSIGNED DEFAULT NULL,
  `template_config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`template_config`)),
  `display_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `published_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `collections`
--

INSERT INTO `collections` (`id`, `entity_id`, `name`, `slug`, `subtitle`, `collection_year`, `season`, `short_description`, `description`, `concept`, `cover_image`, `thumbnail_image`, `primary_color`, `secondary_color`, `background_color`, `text_color`, `image_variant`, `layout_style`, `template_id`, `template_variant_id`, `template_config`, `display_order`, `is_featured`, `is_active`, `published_at`, `created_at`, `updated_at`) VALUES
(1, 3, 'Lumen Collection', 'lumen-collection', 'Migrated legacy collection', 2026, NULL, 'Imported from legacy featured_collections.', 'This collection was migrated automatically from the legacy featured_collections table to preserve historical data.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'light', 'standard', NULL, NULL, NULL, 1, 1, 1, '2026-07-25 16:29:07', '2026-07-25 14:29:07', '2026-07-25 14:29:07'),
(2, 5, 'Nocturne Kit', 'nocturne-kit', 'Migrated legacy collection', 2026, NULL, 'Imported from legacy featured_collections.', 'This collection was migrated automatically from the legacy featured_collections table to preserve historical data.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'dark', 'standard', NULL, NULL, NULL, 2, 1, 1, '2026-07-25 16:29:07', '2026-07-25 14:29:07', '2026-07-25 14:29:07'),
(3, 4, 'Terrain Edition', 'terrain-edition', 'Migrated legacy collection', 2026, NULL, 'Imported from legacy featured_collections.', 'This collection was migrated automatically from the legacy featured_collections table to preserve historical data.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'light', 'standard', NULL, NULL, NULL, 3, 1, 1, '2026-07-25 16:29:07', '2026-07-25 14:29:07', '2026-07-25 14:29:07'),
(4, 1, 'Real Madrid 2026/27', 'real-madrid-2026-27', 'Season system', 2026, '2026/27', 'A full seasonal kit program for Real Madrid.', 'Real Madrid 2026/27 structures the club into a complete collection architecture with separate pieces for each kit expression.', 'A collectible seasonal system balancing heritage, clarity and editorial restraint.', NULL, NULL, '#d9c7a4', '#ffffff', '#f7f1e8', '#111111', 'light', 'standard', NULL, NULL, NULL, 1, 1, 1, '2026-07-25 16:29:07', '2026-07-25 14:29:07', '2026-07-25 14:29:07'),
(5, 2, 'Cristiano Ronaldo Legacy Collection', 'cristiano-ronaldo-legacy-collection', 'Legacy capsule', 2026, NULL, 'A national-team capsule built around Portugal and the legacy narrative.', 'Cristiano Ronaldo Legacy Collection expands Football beyond clubs, framing Portugal as a national-team entity with its own collectible collection logic.', 'Legacy storytelling inside the Football category through the national_team entity type.', NULL, NULL, '#8f1f32', '#d4af37', '#f7eceb', '#111111', 'light', 'standard', NULL, NULL, NULL, 2, 1, 1, '2026-07-25 16:29:08', '2026-07-25 14:29:08', '2026-07-25 14:29:08'),
(6, 5, 'Essentials Collection', 'essentials-collection', 'Core apparel line', 2026, NULL, 'Studio-led garments designed as wearable archive staples.', 'Essentials Collection gives Fashion a reusable architecture for garments, separating the collection itself from each product piece.', 'Minimal wardrobe system with editorial treatment.', NULL, NULL, '#111111', '#d9d2c9', '#faf8f4', '#111111', 'dark', 'standard', NULL, NULL, NULL, 1, 1, 1, '2026-07-25 16:29:08', '2026-07-25 14:29:08', '2026-07-25 14:29:08'),
(7, 6, 'Atelier Archive Vol. I', 'atelier-archive-vol-i', 'Special edition capsule', 2026, NULL, 'A limited AgusMA Studio Lab release for collectible special editions.', 'Atelier Archive Vol. I anchors the Special Editions category with a reusable entity and piece hierarchy.', 'Edition-first storytelling, limited cadence and archive-oriented presentation.', NULL, NULL, '#312822', '#d4c4aa', '#f5efe7', '#111111', 'light', 'editorial', NULL, NULL, NULL, 1, 1, 1, '2026-07-25 16:29:08', '2026-07-25 14:29:08', '2026-07-25 14:29:08'),
(8, 1, 'Century and quarter of legacy', 'real-madrid-125', 'Legacy', 2026, '2026/27', 'Camiseta conmemorativa del 125 aniversario del Real Madrid, con una estética clásica, elegante y premium.', 'Diseño en blanco marfil con detalles en azul celeste y un escudo conmemorativo dorado en relieve como elemento protagonista. El cuello de inspiración clásica, el patrón tonal del tejido y los logotipos termosellados aportan profundidad y sofisticación, manteniendo una imagen limpia, ligera y deportiva.', 'Una unión entre tradición y modernidad que transforma los 125 años de historia del club en una pieza exclusiva.', './assets/images/125an.png', './assets/images/125anback.png', '#F5F2ED', '#7A9FC6', '#f7f1e8', '#111111', 'light', 'standard', NULL, NULL, NULL, 0, 1, 1, '2026-08-04 17:10:00', '2026-08-04 15:13:17', '2026-08-05 05:49:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `collection_tags`
--

CREATE TABLE `collection_tags` (
  `collection_id` int(10) UNSIGNED NOT NULL,
  `tag_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `collection_tags`
--

INSERT INTO `collection_tags` (`collection_id`, `tag_id`) VALUES
(4, 1),
(4, 2),
(5, 1),
(5, 3),
(6, 4),
(7, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `collection_templates`
--

CREATE TABLE `collection_templates` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(60) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `preview_image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `collection_template_variants`
--

CREATE TABLE `collection_template_variants` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `template_id` smallint(5) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(80) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `preview_image` varchar(255) DEFAULT NULL,
  `default_config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`default_config`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entities`
--

CREATE TABLE `entities` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `entity_type` varchar(60) NOT NULL DEFAULT 'other',
  `subtitle` varchar(191) DEFAULT NULL,
  `short_description` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `logo_url` varchar(255) DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `primary_color` varchar(30) DEFAULT NULL,
  `secondary_color` varchar(30) DEFAULT NULL,
  `background_color` varchar(30) DEFAULT NULL,
  `text_color` varchar(30) DEFAULT NULL,
  `display_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `entities`
--

INSERT INTO `entities` (`id`, `category_id`, `name`, `slug`, `entity_type`, `subtitle`, `short_description`, `description`, `logo_url`, `cover_image`, `primary_color`, `secondary_color`, `background_color`, `text_color`, `display_order`, `is_featured`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Real Madrid CF', 'real-madrid', 'club', 'Club entity', 'Historic club concepts and seasonal kit systems.', 'Real Madrid acts as the flagship club entity inside Football, ready to host full seasonal collections and individual pieces.', './assets/images/escudoRM.png', NULL, '#d9c7a4', '#ffffff', '#f6f1e6', '#111111', 1, 1, 1, '2026-07-25 14:29:07', '2026-08-04 15:16:31'),
(2, 1, 'Portugal', 'portugal', 'national_team', 'National team entity', 'National team capsules and player-led editorial collections.', 'Portugal represents the national-team branch inside Football and replaces National Teams as a standalone category.', NULL, NULL, '#9f2033', '#d4af37', '#f5ece8', '#111111', 2, 1, 1, '2026-07-25 14:29:07', '2026-07-25 14:29:07'),
(3, 1, 'Football Archive', 'football-archive', 'concept', 'Legacy migration entity', 'Preserves legacy featured collections formerly attached directly to Football.', 'Football Archive receives migrated legacy featured collections so no historical records are lost during the architecture transition.', NULL, NULL, NULL, NULL, NULL, NULL, 90, 0, 1, '2026-07-25 14:29:07', '2026-07-25 14:29:07'),
(4, 1, 'National Team Archive', 'national-team-archive', 'national_team', 'Legacy migration entity', 'Preserves legacy national-team collections inside Football.', 'National Team Archive holds historical items migrated from the legacy National Teams category while the new public model uses Football entities.', NULL, NULL, NULL, NULL, NULL, NULL, 91, 0, 1, '2026-07-25 14:29:07', '2026-07-25 14:29:07'),
(5, 3, 'AgusMA Studio', 'agusma-studio', 'studio', 'Studio entity', 'Studio-led fashion concepts and wearable essentials.', 'AgusMA Studio hosts fashion collections and studio-native apparel concepts under the new architecture.', NULL, NULL, '#111111', '#ded8cf', '#faf9f6', '#111111', 1, 1, 1, '2026-07-25 14:29:07', '2026-07-25 14:29:07'),
(6, 4, 'AgusMA Studio Lab', 'agusma-studio-lab', 'concept', 'Studio lab entity', 'Limited capsules, experiments and collectible special releases.', 'AgusMA Studio Lab groups the most experimental and special-edition releases under a single reusable entity.', NULL, NULL, '#2a2520', '#d4c4aa', '#f3eee7', '#111111', 1, 1, 1, '2026-07-25 14:29:07', '2026-07-25 14:29:07'),
(7, 1, 'FC Barcelona', 'barcelona', 'club', 'Club entity', 'Historic corrupt club concepts and seasonal kit systems.', 'FC Barcelona acts as the flagship club entity inside Football, ready to host full seasonal collections and individual pieces.', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, '2026-07-25 16:17:51', '2026-07-25 16:49:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `featured_collections`
--

CREATE TABLE `featured_collections` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `title` varchar(150) NOT NULL,
  `collection_year` smallint(5) UNSIGNED NOT NULL,
  `image_variant` enum('light','dark') NOT NULL DEFAULT 'light',
  `display_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `featured_collections`
--

INSERT INTO `featured_collections` (`id`, `category_id`, `title`, `collection_year`, `image_variant`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Lumen Collection', 2026, 'light', 1, 1, '2026-07-24 16:46:34', '2026-07-24 16:46:34'),
(2, 3, 'Nocturne Kit', 2026, 'dark', 2, 1, '2026-07-24 16:46:35', '2026-07-24 16:46:35'),
(3, 2, 'Terrain Edition', 2026, 'light', 3, 1, '2026-07-24 16:46:35', '2026-07-24 16:46:35');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `media`
--

CREATE TABLE `media` (
  `id` int(10) UNSIGNED NOT NULL,
  `collection_id` int(10) UNSIGNED NOT NULL,
  `piece_id` int(10) UNSIGNED DEFAULT NULL,
  `media_type` varchar(60) NOT NULL DEFAULT 'image',
  `file_url` varchar(255) NOT NULL,
  `thumbnail_url` varchar(255) DEFAULT NULL,
  `title` varchar(180) DEFAULT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `caption` text DEFAULT NULL,
  `section_key` varchar(80) DEFAULT NULL,
  `display_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_cover` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `media`
--

INSERT INTO `media` (`id`, `collection_id`, `piece_id`, `media_type`, `file_url`, `thumbnail_url`, `title`, `alt_text`, `caption`, `section_key`, `display_order`, `is_cover`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 8, NULL, 'image', './assets/images/hero-125.png', NULL, NULL, NULL, NULL, 'hero', 0, 1, 1, '2026-08-05 06:45:12', '2026-08-05 06:45:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pieces`
--

CREATE TABLE `pieces` (
  `id` int(10) UNSIGNED NOT NULL,
  `collection_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(180) NOT NULL,
  `slug` varchar(180) NOT NULL,
  `piece_type` varchar(60) NOT NULL DEFAULT 'other',
  `subtitle` varchar(191) DEFAULT NULL,
  `short_description` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `display_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pieces`
--

INSERT INTO `pieces` (`id`, `collection_id`, `name`, `slug`, `piece_type`, `subtitle`, `short_description`, `description`, `cover_image`, `display_order`, `is_featured`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 4, 'Home Kit', 'home-kit', 'home_kit', NULL, 'Primary match kit.', 'Primary match kit for the Real Madrid 2026/27 collection.', NULL, 1, 1, 1, '2026-07-25 14:29:08', '2026-07-25 14:29:08'),
(2, 4, 'Away Kit', 'away-kit', 'away_kit', NULL, 'Secondary match kit.', 'Secondary match kit for the Real Madrid 2026/27 collection.', NULL, 2, 0, 1, '2026-07-25 14:29:08', '2026-07-25 14:29:08'),
(3, 4, 'Third Kit', 'third-kit', 'third_kit', NULL, 'Alternate match kit.', 'Third kit concept for the Real Madrid 2026/27 collection.', NULL, 3, 0, 1, '2026-07-25 14:29:08', '2026-07-25 14:29:08'),
(4, 4, 'Goalkeeper Kit', 'goalkeeper-kit', 'goalkeeper_kit', NULL, 'Goalkeeper-specific kit.', 'Goalkeeper kit concept for the Real Madrid 2026/27 collection.', NULL, 4, 0, 1, '2026-07-25 14:29:08', '2026-07-25 14:29:08'),
(5, 5, 'Home Kit', 'home-kit', 'home_kit', NULL, 'Primary national-team kit.', 'Home kit concept inside the Cristiano Ronaldo Legacy Collection.', NULL, 1, 1, 1, '2026-07-25 14:29:08', '2026-07-25 14:29:08'),
(6, 8, 'Special Edition', 'special-edition', 'concept', NULL, 'Collectible legacy variation.', 'Special-edition piece expanding the Portugal narrative inside Football.', './assets/images/125an.png', 2, 0, 1, '2026-07-25 14:29:08', '2026-08-05 06:58:10'),
(7, 6, 'Oversized T-shirt', 'oversized-t-shirt', 'shirt', NULL, 'Core oversized top.', 'Oversized T-shirt inside the AgusMA Studio Essentials Collection.', NULL, 1, 1, 1, '2026-07-25 14:29:08', '2026-07-25 14:29:08'),
(8, 6, 'Hoodie', 'hoodie', 'hoodie', NULL, 'Layering staple.', 'Hoodie inside the AgusMA Studio Essentials Collection.', NULL, 2, 0, 1, '2026-07-25 14:29:08', '2026-07-25 14:29:08'),
(9, 7, 'Concept Poster', 'concept-poster', 'poster', NULL, 'Limited-edition print companion.', 'Poster-style collectible for Atelier Archive Vol. I.', NULL, 1, 1, 1, '2026-07-25 14:29:08', '2026-07-25 14:29:08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tags`
--

CREATE TABLE `tags` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tags`
--

INSERT INTO `tags` (`id`, `name`, `slug`, `created_at`) VALUES
(1, 'Football', 'football', '2026-07-25 14:29:08'),
(2, 'Club', 'club', '2026-07-25 14:29:08'),
(3, 'National Team', 'national-team', '2026-07-25 14:29:08'),
(4, 'Fashion', 'fashion', '2026-07-25 14:29:08'),
(5, 'Special Edition', 'special-edition', '2026-07-25 14:29:08');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_categories_slug` (`slug`);

--
-- Indices de la tabla `collections`
--
ALTER TABLE `collections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_collections_entity_slug` (`entity_id`,`slug`),
  ADD KEY `idx_collections_entity_id` (`entity_id`),
  ADD KEY `idx_collections_featured` (`is_featured`,`is_active`,`display_order`),
  ADD KEY `idx_collections_template_id` (`template_id`),
  ADD KEY `fk_collection_template_variant` (`template_variant_id`);

--
-- Indices de la tabla `collection_tags`
--
ALTER TABLE `collection_tags`
  ADD PRIMARY KEY (`collection_id`,`tag_id`),
  ADD KEY `idx_collection_tags_tag_id` (`tag_id`);

--
-- Indices de la tabla `collection_templates`
--
ALTER TABLE `collection_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_collection_template_slug` (`slug`);

--
-- Indices de la tabla `collection_template_variants`
--
ALTER TABLE `collection_template_variants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_template_variant` (`template_id`,`slug`);

--
-- Indices de la tabla `entities`
--
ALTER TABLE `entities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_entities_category_slug` (`category_id`,`slug`),
  ADD KEY `idx_entities_category_id` (`category_id`);

--
-- Indices de la tabla `featured_collections`
--
ALTER TABLE `featured_collections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_featured_collections_category_id` (`category_id`);

--
-- Indices de la tabla `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_media_collection_id` (`collection_id`),
  ADD KEY `idx_media_piece_id` (`piece_id`);

--
-- Indices de la tabla `pieces`
--
ALTER TABLE `pieces`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_pieces_collection_slug` (`collection_id`,`slug`),
  ADD KEY `idx_pieces_collection_id` (`collection_id`);

--
-- Indices de la tabla `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_tags_slug` (`slug`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `collections`
--
ALTER TABLE `collections`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `collection_templates`
--
ALTER TABLE `collection_templates`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `collection_template_variants`
--
ALTER TABLE `collection_template_variants`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `entities`
--
ALTER TABLE `entities`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `featured_collections`
--
ALTER TABLE `featured_collections`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `media`
--
ALTER TABLE `media`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `pieces`
--
ALTER TABLE `pieces`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `collections`
--
ALTER TABLE `collections`
  ADD CONSTRAINT `fk_collection_template_variant` FOREIGN KEY (`template_variant_id`) REFERENCES `collection_template_variants` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_collections_entity_id` FOREIGN KEY (`entity_id`) REFERENCES `entities` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_collections_template_id` FOREIGN KEY (`template_id`) REFERENCES `collection_templates` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `collection_tags`
--
ALTER TABLE `collection_tags`
  ADD CONSTRAINT `fk_collection_tags_collection_id` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_collection_tags_tag_id` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `collection_template_variants`
--
ALTER TABLE `collection_template_variants`
  ADD CONSTRAINT `fk_variant_template` FOREIGN KEY (`template_id`) REFERENCES `collection_templates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `entities`
--
ALTER TABLE `entities`
  ADD CONSTRAINT `fk_entities_category_id` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `featured_collections`
--
ALTER TABLE `featured_collections`
  ADD CONSTRAINT `fk_featured_collections_category_id` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `media`
--
ALTER TABLE `media`
  ADD CONSTRAINT `fk_media_collection_id` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_media_piece_id` FOREIGN KEY (`piece_id`) REFERENCES `pieces` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pieces`
--
ALTER TABLE `pieces`
  ADD CONSTRAINT `fk_pieces_collection_id` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
