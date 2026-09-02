<?php

declare(strict_types=1);

/**
 * Central definition of the visual controls available for every public section component.
 * The same definition is used to render the admin controls and to validate saved values.
 */
function adminSectionVisualSchema(): array
{
    return [
        'hero' => [
            'label' => 'Hero',
            'description' => 'Configura la cabecera principal, la imagen, la altura y los elementos informativos de la coleccion.',
            'fields' => [
                'layout' => [
                    'label' => 'Composicion',
                    'type' => 'select',
                    'default' => 'split',
                    'options' => [
                        'split' => 'Texto e imagen',
                        'full_bleed' => 'Imagen a sangre',
                        'centered' => 'Contenido centrado',
                        'minimal' => 'Minimal',
                    ],
                ],
                'height' => [
                    'label' => 'Altura',
                    'type' => 'select',
                    'default' => 'viewport',
                    'options' => [
                        'compact' => 'Compacta',
                        'viewport' => 'Portada',
                        'full' => 'Pantalla completa',
                    ],
                ],
                'image_position' => [
                    'label' => 'Posicion de imagen',
                    'type' => 'select',
                    'default' => 'right',
                    'options' => [
                        'right' => 'Derecha',
                        'left' => 'Izquierda',
                        'background' => 'Como fondo',
                    ],
                ],
                'fit' => [
                    'label' => 'Ajuste de imagen',
                    'type' => 'select',
                    'default' => 'cover',
                    'options' => ['cover' => 'Cubrir', 'contain' => 'Contener'],
                ],
                'position' => [
                    'label' => 'Encuadre',
                    'type' => 'select',
                    'default' => 'center',
                    'options' => ['top' => 'Superior', 'center' => 'Centro', 'bottom' => 'Inferior'],
                ],
                'overlay' => [
                    'label' => 'Superposicion',
                    'type' => 'select',
                    'default' => 'dark',
                    'options' => ['none' => 'Sin overlay', 'light' => 'Claro', 'dark' => 'Oscuro'],
                ],
                'alignment' => [
                    'label' => 'Alineacion del texto',
                    'type' => 'select',
                    'default' => 'left',
                    'options' => ['left' => 'Izquierda', 'center' => 'Centrada', 'right' => 'Derecha'],
                ],
                'show_entity' => [
                    'label' => 'Mostrar categoria y entidad',
                    'type' => 'checkbox',
                    'default' => true,
                ],
                'show_tags' => [
                    'label' => 'Mostrar etiquetas',
                    'type' => 'checkbox',
                    'default' => true,
                ],
                'show_actions' => [
                    'label' => 'Mostrar botones',
                    'type' => 'checkbox',
                    'default' => true,
                ],
            ],
        ],
        'intro' => [
            'label' => 'Introduccion',
            'description' => 'Controla la alineacion, anchura y superficie del bloque conceptual.',
            'fields' => [
                'alignment' => [
                    'label' => 'Alineacion',
                    'type' => 'select',
                    'default' => 'left',
                    'options' => ['left' => 'Izquierda', 'center' => 'Centrada', 'right' => 'Derecha'],
                ],
                'content_width' => [
                    'label' => 'Anchura del texto',
                    'type' => 'select',
                    'default' => 'normal',
                    'options' => ['narrow' => 'Estrecha', 'normal' => 'Normal', 'wide' => 'Amplia'],
                ],
                'variant' => [
                    'label' => 'Fondo',
                    'type' => 'select',
                    'default' => 'default',
                    'options' => [
                        'default' => 'Sin fondo',
                        'light' => 'Claro',
                        'dark' => 'Oscuro',
                        'primary' => 'Color principal',
                        'secondary' => 'Color secundario',
                    ],
                ],
                'spacing' => [
                    'label' => 'Espaciado',
                    'type' => 'select',
                    'default' => 'normal',
                    'options' => ['compact' => 'Compacto', 'normal' => 'Normal', 'large' => 'Amplio'],
                ],
            ],
        ],
        'pieces' => [
            'label' => 'Piezas',
            'description' => 'Configura la composicion de las tarjetas que forman la coleccion.',
            'fields' => [
                'layout' => [
                    'label' => 'Diseno',
                    'type' => 'select',
                    'default' => 'grid',
                    'options' => ['grid' => 'Cuadricula', 'editorial' => 'Editorial', 'list' => 'Lista'],
                ],
                'columns' => [
                    'label' => 'Columnas',
                    'type' => 'select',
                    'default' => '2',
                    'options' => ['2' => '2 columnas', '3' => '3 columnas', '4' => '4 columnas'],
                ],
                'image_ratio' => [
                    'label' => 'Formato de imagen',
                    'type' => 'select',
                    'default' => 'portrait',
                    'options' => [
                        'auto' => 'Automatico',
                        'portrait' => 'Vertical',
                        'square' => 'Cuadrado',
                        'landscape' => 'Panoramico',
                    ],
                ],
                'gap' => [
                    'label' => 'Separacion',
                    'type' => 'select',
                    'default' => 'normal',
                    'options' => ['compact' => 'Compacta', 'normal' => 'Normal', 'wide' => 'Amplia'],
                ],
                'variant' => [
                    'label' => 'Fondo de seccion',
                    'type' => 'select',
                    'default' => 'default',
                    'options' => [
                        'default' => 'Sin fondo',
                        'light' => 'Claro',
                        'dark' => 'Oscuro',
                        'primary' => 'Color principal',
                        'secondary' => 'Color secundario',
                    ],
                ],
                'show_piece_type' => [
                    'label' => 'Mostrar tipo de pieza',
                    'type' => 'checkbox',
                    'default' => true,
                ],
                'show_description' => [
                    'label' => 'Mostrar descripcion',
                    'type' => 'checkbox',
                    'default' => true,
                ],
            ],
        ],
        'piece_showcase' => [
            'label' => 'Pieza protagonista',
            'description' => 'Presenta una pieza de la coleccion como momento editorial de gran formato.',
            'fields' => [
                'piece_slug' => [
                    'label' => 'Slug de la pieza',
                    'type' => 'text',
                    'default' => '',
                    'placeholder' => 'home-kit',
                ],
                'layout' => [
                    'label' => 'Composicion',
                    'type' => 'select',
                    'default' => 'split',
                    'options' => [
                        'split' => 'Texto + imagen',
                        'immersive' => 'Imagen inmersiva',
                    ],
                ],
                'image_position' => [
                    'label' => 'Posicion de imagen',
                    'type' => 'select',
                    'default' => 'right',
                    'options' => ['right' => 'Derecha', 'left' => 'Izquierda'],
                ],
                'fit' => [
                    'label' => 'Ajuste de imagen',
                    'type' => 'select',
                    'default' => 'cover',
                    'options' => ['cover' => 'Cubrir', 'contain' => 'Contener'],
                ],
                'position_x' => [
                    'label' => 'Encuadre horizontal',
                    'type' => 'select',
                    'default' => '50',
                    'options' => [
                        '0' => 'Extremo izquierdo',
                        '25' => 'Izquierda',
                        '50' => 'Centro',
                        '75' => 'Derecha',
                        '100' => 'Extremo derecho',
                    ],
                ],
                'position_y' => [
                    'label' => 'Encuadre vertical',
                    'type' => 'select',
                    'default' => '50',
                    'options' => [
                        '0' => 'Arriba',
                        '25' => 'Superior',
                        '50' => 'Centro',
                        '75' => 'Inferior',
                        '100' => 'Abajo',
                    ],
                ],
                'height' => [
                    'label' => 'Altura',
                    'type' => 'select',
                    'default' => 'editorial',
                    'options' => [
                        'compact' => 'Compacta',
                        'editorial' => 'Editorial',
                        'viewport' => 'Portada',
                        'full' => 'Pantalla completa',
                    ],
                ],
                'variant' => [
                    'label' => 'Fondo',
                    'type' => 'select',
                    'default' => 'default',
                    'options' => [
                        'default' => 'Sin fondo',
                        'light' => 'Claro',
                        'dark' => 'Oscuro',
                        'primary' => 'Color principal',
                        'secondary' => 'Color secundario',
                    ],
                ],
                'piece_number' => [
                    'label' => 'Numero editorial',
                    'type' => 'text',
                    'default' => '',
                    'placeholder' => '01',
                ],
                'show_piece_number' => [
                    'label' => 'Mostrar numero editorial',
                    'type' => 'checkbox',
                    'default' => true,
                ],
                'show_piece_type' => [
                    'label' => 'Mostrar tipo de pieza',
                    'type' => 'checkbox',
                    'default' => true,
                ],
                'show_secondary_image' => [
                    'label' => 'Mostrar segunda imagen',
                    'type' => 'checkbox',
                    'default' => true,
                ],
            ],
        ],
        'gallery' => [
            'label' => 'Galeria',
            'description' => 'Elige entre cuadricula, mosaico editorial, columnas masonry o carrusel horizontal.',
            'fields' => [
                'layout' => [
                    'label' => 'Diseno',
                    'type' => 'select',
                    'default' => 'editorial',
                    'options' => [
                        'grid' => 'Cuadricula',
                        'editorial' => 'Mosaico editorial',
                        'masonry' => 'Masonry',
                        'carousel' => 'Carrusel horizontal',
                    ],
                ],
                'columns' => [
                    'label' => 'Columnas',
                    'type' => 'select',
                    'default' => '3',
                    'options' => ['2' => '2 columnas', '3' => '3 columnas', '4' => '4 columnas'],
                ],
                'image_ratio' => [
                    'label' => 'Formato de imagen',
                    'type' => 'select',
                    'default' => 'auto',
                    'options' => [
                        'auto' => 'Automatico',
                        'portrait' => 'Vertical',
                        'square' => 'Cuadrado',
                        'landscape' => 'Panoramico',
                    ],
                ],
                'gap' => [
                    'label' => 'Separacion',
                    'type' => 'select',
                    'default' => 'normal',
                    'options' => ['compact' => 'Compacta', 'normal' => 'Normal', 'wide' => 'Amplia'],
                ],
                'variant' => [
                    'label' => 'Fondo de seccion',
                    'type' => 'select',
                    'default' => 'default',
                    'options' => [
                        'default' => 'Sin fondo',
                        'light' => 'Claro',
                        'dark' => 'Oscuro',
                        'primary' => 'Color principal',
                        'secondary' => 'Color secundario',
                    ],
                ],
                'show_captions' => [
                    'label' => 'Mostrar titulos y pies',
                    'type' => 'checkbox',
                    'default' => true,
                ],
            ],
        ],
        'technical_details' => [
            'label' => 'Detalles tecnicos',
            'description' => 'Presenta materiales y acabados como bloque, columnas o fichas independientes.',
            'fields' => [
                'layout' => [
                    'label' => 'Diseno',
                    'type' => 'select',
                    'default' => 'split',
                    'options' => ['split' => 'Dos bloques', 'stacked' => 'Apilado', 'cards' => 'Fichas'],
                ],
                'columns' => [
                    'label' => 'Columnas de contenido',
                    'type' => 'select',
                    'default' => '2',
                    'options' => ['1' => '1 columna', '2' => '2 columnas', '3' => '3 columnas'],
                ],
                'alignment' => [
                    'label' => 'Alineacion',
                    'type' => 'select',
                    'default' => 'left',
                    'options' => ['left' => 'Izquierda', 'center' => 'Centrada'],
                ],
                'variant' => [
                    'label' => 'Fondo',
                    'type' => 'select',
                    'default' => 'dark',
                    'options' => [
                        'light' => 'Claro',
                        'dark' => 'Oscuro',
                        'primary' => 'Color principal',
                        'secondary' => 'Color secundario',
                    ],
                ],
            ],
        ],
        'full_image' => [
            'label' => 'Imagen completa',
            'description' => 'Controla la altura, el encuadre y la posicion del texto sobre la imagen.',
            'fields' => [
                'height' => [
                    'label' => 'Altura',
                    'type' => 'select',
                    'default' => 'half',
                    'options' => ['auto' => 'Automatica', 'half' => 'Media pantalla', 'full' => 'Pantalla completa'],
                ],
                'fit' => [
                    'label' => 'Ajuste de imagen',
                    'type' => 'select',
                    'default' => 'cover',
                    'options' => ['cover' => 'Cubrir', 'contain' => 'Contener'],
                ],
                'position' => [
                    'label' => 'Encuadre',
                    'type' => 'select',
                    'default' => 'center',
                    'options' => ['top' => 'Superior', 'center' => 'Centro', 'bottom' => 'Inferior'],
                ],
                'overlay' => [
                    'label' => 'Superposicion',
                    'type' => 'select',
                    'default' => 'dark',
                    'options' => ['none' => 'Sin overlay', 'light' => 'Claro', 'dark' => 'Oscuro'],
                ],
                'copy_position' => [
                    'label' => 'Posicion del texto',
                    'type' => 'select',
                    'default' => 'bottom-left',
                    'options' => [
                        'bottom-left' => 'Abajo izquierda',
                        'bottom-center' => 'Abajo centrado',
                        'center' => 'Centro',
                    ],
                ],
            ],
        ],
    ];
}

function adminSectionVisualDefaults(string $sectionType): array
{
    $schema = adminSectionVisualSchema()[$sectionType] ?? null;

    if (!is_array($schema)) {
        return [];
    }

    $defaults = [];

    foreach ($schema['fields'] as $key => $field) {
        $defaults[$key] = $field['default'];
    }

    return $defaults;
}

function adminDecodeSectionVisualSettings(?string $value, string $sectionType): array
{
    $defaults = adminSectionVisualDefaults($sectionType);

    if ($value === null || trim($value) === '') {
        return $defaults;
    }

    try {
        $decoded = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException) {
        return $defaults;
    }

    return adminSanitizeSectionVisualSettings($sectionType, is_array($decoded) ? $decoded : []);
}

function adminSanitizeSectionVisualSettings(string $sectionType, mixed $input): array
{
    $schema = adminSectionVisualSchema()[$sectionType] ?? null;

    if (!is_array($schema)) {
        throw new InvalidArgumentException('El tipo de seccion no admite configuracion visual.');
    }

    if (!is_array($input)) {
        return adminSectionVisualDefaults($sectionType);
    }

    $settings = [];

    foreach ($schema['fields'] as $key => $field) {
        if ($field['type'] === 'checkbox') {
            $settings[$key] = isset($input[$key]) && in_array((string) $input[$key], ['1', 'true', 'on', 'yes'], true);
            continue;
        }

        if ($field['type'] === 'text') {
            $value = trim((string) ($input[$key] ?? $field['default']));
            $settings[$key] = substr($value, 0, 160);
            continue;
        }

        $value = trim((string) ($input[$key] ?? $field['default']));
        $allowedValues = array_map('strval', array_keys($field['options']));
        $settings[$key] = in_array($value, $allowedValues, true) ? $value : (string) $field['default'];
    }

    return $settings;
}

function adminEncodeSectionVisualSettings(string $sectionType, mixed $input): string
{
    return (string) json_encode(
        adminSanitizeSectionVisualSettings($sectionType, $input),
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
}
