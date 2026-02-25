<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración de Idiomas para Eunomia CMS
    |--------------------------------------------------------------------------
    |
    | Esta configuración define los idiomas disponibles en el sistema CMS
    | y sus propiedades principales para el manejo multiidioma.
    |
    */

    'idiomas_disponibles' => [
        'es' => [
            'nombre' => 'Español',
            'label' => 'CAS',
            'imagen' => 'es.png',
            'principal' => true,
        ],
        'as' => [
            'nombre' => 'Asturiano', 
            'label' => 'AST',
            'imagen' => 'as.png',
            'principal' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Idioma por defecto
    |--------------------------------------------------------------------------
    |
    | Define el código del idioma que se usará por defecto cuando no se
    | especifique ninguno explícitamente.
    |
    */
    'idioma_defecto' => 'es',

    /*
    |--------------------------------------------------------------------------
    | Detectar idioma automáticamente
    |--------------------------------------------------------------------------
    |
    | Si está habilitado, el sistema intentará detectar el idioma preferido
    | del usuario basándose en las cabeceras HTTP Accept-Language.
    |
    */
    'deteccion_automatica' => true,

    /*
    |--------------------------------------------------------------------------
    | Prefijo URL para idiomas
    |--------------------------------------------------------------------------
    |
    | Define si se debe agregar el código de idioma como prefijo en las URLs
    | ejemplo: /es/inicio, /as/entamu
    |
    */
    'prefijo_url' => true,

    /*
    |--------------------------------------------------------------------------
    | Ocultar idioma por defecto en URL
    |--------------------------------------------------------------------------
    |
    | Si está habilitado, el idioma por defecto no aparecerá en la URL
    | ejemplo: /inicio en lugar de /es/inicio
    |
    */
    'ocultar_defecto_url' => true,
];