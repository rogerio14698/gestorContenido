<?php
if (!function_exists('normalizar_etiqueta_idioma')) {
    /**
     * Normaliza la etiqueta de idioma a su código base (ej: 'es-ES' => 'es', 'ast' => 'as').
     * Si el idioma es compuesto (es-ES, en-US), devuelve la parte antes del guion.
     * Si es un alias conocido, lo traduce; si no, lo deja tal cual.
     */
    function normalizar_etiqueta_idioma($idioma)
    {
        if (!$idioma) return 'es';
        $idioma = strtolower($idioma);
        // Quitar región si existe (ej: es-ES -> es)
        $idioma = explode('-', $idioma)[0];
        // Alias conocidos
        $map = [
            'castellano' => 'es',
            'cas' => 'es',
            'spanish' => 'es',
            'asturiano' => 'as',
            'ast' => 'as',
        ];
        return $map[$idioma] ?? $idioma;
    }
}


use App\Services\ImageService;

if (!function_exists('responsive_image')) {
    /**
     * Generar URL de imagen responsive
     */
    function responsive_image(string $desktopPath, bool $isMobile = false): string
    {
        $imageService = app(ImageService::class);
        return $imageService->getResponsiveImageUrl($desktopPath, $isMobile);
    }
}

if (!function_exists('responsive_image_html')) {
    /**
     * Generar HTML de imagen responsive con srcset
     */
    function responsive_image_html(string $desktopPath, string $alt = '', string $class = '', string $style = ''): string
    {
        $imageService = app(ImageService::class);
        return $imageService->generateResponsiveImageHtml($desktopPath, $alt, $class, $style);
    }
}

if (!function_exists('get_image_alt')) {
    /**
     * Obtener descripción ALT apropiada para el idioma actual
     */
    function get_image_alt($content, $texto = null, string $tipo = 'imagen'): string
    {
        // Si hay texto específico del idioma, usar su ALT
        if ($texto && $tipo === 'imagen' && $texto->imagen_alt) {
            return $texto->imagen_alt;
        }
        if ($texto && $tipo === 'imagen_portada' && $texto->imagen_portada_alt) {
            return $texto->imagen_portada_alt;
        }
        
        // Fallback al ALT global del contenido
        switch ($menu->tipo_enlace) {
            case 'url_externa':
                return $menu->url_externa ?: $menu->url ?: '#';

            case 'ancla':
            case 'anchor':
                return $menu->url ? ('#' . ltrim($menu->url, '#')) : '#';

            case 'contenido':
                if ($menu->content) {
                    // Priorizar slug del contenido asociado
                    $textoContenido = $menu->content->textos->first(function ($texto) use ($idiomaEtiqueta) {
                        $etiquetaTexto = \App\Helpers\IdiomaHelper::normalizarEtiqueta(optional($texto->idioma)->etiqueta);
                        return $texto->activo && $etiquetaTexto === $idiomaEtiqueta;
                    }) ?? $menu->content->textos->first();

                    $slug = $textoContenido?->slug;

                    // DEBUG: Mostrar slug y URL generada en el HTML (solo para depuración)
                    // DEBUG eliminado

                    if ($slug) {
                        try {
                            $url = route('contenido', [
                                'idioma' => $idiomaRuta,
                                'slug' => $slug,
                            ]);
                            // DEBUG eliminado
                            return $url;
                        } catch (\Throwable $e) {
                            // Ignorar excepción y continuar con fallback.
                        }
                    }
                }
                break;
        }

        // Si no hay slug ni url externa, devolver '#'
        return '#';
    }
}

if (!function_exists('idiomas_activos')) {
    /**
     * Obtener todos los idiomas activos del sistema
     */
    function idiomas_activos(): \Illuminate\Database\Eloquent\Collection
    {
        return \App\Models\Idioma::activosParaFrontend();
    }
}

if (!function_exists('idioma_principal')) {
    /**
     * Obtener el idioma principal del sistema
     */
    function idioma_principal(): ?\App\Models\Idioma
    {
        return \App\Models\Idioma::principal();
    }
}

if (!function_exists('idioma_actual')) {
    /**
     * Obtener el idioma actual basado en el contexto de la ruta
     */
    function idioma_actual(): string
    {
        // Intentar obtener idioma de la ruta actual
        $idioma = request()->route('idioma');
        
        if ($idioma) {
            return \App\Helpers\IdiomaHelper::normalizarEtiqueta($idioma);
        }
        
        // Fallback al idioma principal
        $principal = idioma_principal();
        return $principal ? \App\Helpers\IdiomaHelper::normalizarEtiqueta($principal->etiqueta) : 'es';
    }
}

if (!function_exists('url_idioma')) {
    /**
     * Generar URL para un idioma específico manteniendo la ruta actual
     */
    function url_idioma(string $etiquetaIdioma, ?string $ruta = null): string
    {
        $rutaActual = $ruta ?? request()->route()->getName();
        $parametros = request()->route()->parameters();
        
        // Actualizar el parámetro de idioma
        $parametros['idioma'] = \App\Helpers\IdiomaHelper::etiquetaParaRuta($etiquetaIdioma);
        
        try {
            return route($rutaActual, $parametros);
        } catch (\Exception $e) {
            // Fallback a la página principal del idioma
            return route('inicio', ['idioma' => \App\Helpers\IdiomaHelper::etiquetaParaRuta($etiquetaIdioma)]);
        }
    }
}

if (!function_exists('idioma_etiqueta_html')) {
    /**
     * Obtener la etiqueta HTML lang para el idioma actual
     */
    function idioma_etiqueta_html(): string
    {
        $etiqueta = idioma_actual();
        
        // Obtener información completa del idioma si existe en BD
        $idioma = \App\Models\Idioma::where('etiqueta', $etiqueta)->where('activo', true)->first();
        
        return $idioma ? $idioma->codigo_html : strtolower($etiqueta);
    }
}

if (!function_exists('idiomas_disponibles')) {
    /**
     * Obtener array de idiomas para usar en selectores/menús
     */
    function idiomas_disponibles(): array
    {
        return idiomas_activos()->map(function($idioma) {
            return [
                'etiqueta' => $idioma->etiqueta,
                'nombre' => $idioma->nombre,
                'imagen_url' => $idioma->imagen_url,
                'es_principal' => $idioma->es_principal,
                'url' => url_idioma($idioma->etiqueta)
            ];
        })->toArray();
    }
}

if (!function_exists('es_idioma_actual')) {
    /**
     * Verificar si una etiqueta de idioma es el idioma actual
     */
    function es_idioma_actual(string $etiqueta): bool
    {
        return idioma_actual() === \App\Helpers\IdiomaHelper::normalizarEtiqueta($etiqueta);
    }
}

if (!function_exists('menu_url')) {
    /**
     * Obtener la URL adecuada para un ítem de menú considerando el idioma actual.
     */
    function menu_url(\App\Models\Menu $menu, ?string $idiomaEtiqueta = null): string
    {
        $menu->loadMissing(['textos.idioma', 'content.textos.idioma']);

        $idiomaEtiqueta = \App\Helpers\IdiomaHelper::normalizarEtiqueta($idiomaEtiqueta ?? idioma_actual());
        $idiomaRuta = \App\Helpers\IdiomaHelper::etiquetaParaRuta($idiomaEtiqueta ?? 'es');

        switch ($menu->tipo_enlace) {
            case 'url_externa':
                return $menu->url_externa ?: $menu->url ?: '#';

            case 'ancla':
            case 'anchor':
                return $menu->url ? ('#' . ltrim($menu->url, '#')) : '#';

            case 'contenido':
                if ($menu->content) {
                    // Buscar el slug correcto del contenido según idioma
                    $textoMenu = $menu->textos->first(function ($texto) use ($idiomaEtiqueta) {
                        $etiquetaTexto = \App\Helpers\IdiomaHelper::normalizarEtiqueta(optional($texto->idioma)->etiqueta);
                        return $texto->activo && $etiquetaTexto === $idiomaEtiqueta;
                    });
                    $slug = $textoMenu?->slug;
                    if (!$slug) {
                        $textoContenido = $menu->content->textos->first(function ($texto) use ($idiomaEtiqueta) {
                            $etiquetaTexto = \App\Helpers\IdiomaHelper::normalizarEtiqueta(optional($texto->idioma)->etiqueta);
                            return $texto->activo && $etiquetaTexto === $idiomaEtiqueta;
                        }) ?? $menu->content->textos->first();
                        $slug = $textoContenido?->slug;
                    }
                    // Si no hay slug, fallback
                    if (!$slug) {
                        return '#';
                    }
                    // Determinar tipo de contenido
                    $tipo = optional($menu->content->tipoContenido)->nombre;
                    $routeParams = ['idioma' => $idiomaRuta, 'slug' => $slug];
                    // Seleccionar la ruta según el tipo
                    switch ($tipo) {
                        case 'noticia':
                        case 'noticias':
                            $routeName = 'noticias.show';
                            break;
                        case 'entrevista':
                        case 'entrevistas':
                            $routeName = 'entrevistas.show';
                            break;
                        case 'pagina':
                        case 'página':
                            $routeName = 'pagina.show';
                            break;
                        default:
                            $routeName = 'contenido';
                    }
                    try {
                        $url = route($routeName, $routeParams);
                        // DEBUG eliminado
                        return $url;
                    } catch (\Throwable $e) {
                        // Fallback a la ruta por defecto
                        return route('contenido', $routeParams);
                    }
                }
                break;
        }

        if (!empty($menu->url)) {
            return $menu->url;
        }

        return '#';
    }
}

if (!function_exists('get_gallery_image_alt')) {
    /**
     * Obtener texto alternativo multiidioma para imágenes de galería
     */
    function get_gallery_image_alt($galleryImage, $idiomaEtiqueta = null): string
    {
        if (!$galleryImage) {
            return 'Imagen de galería';
        }

        // Determinar idioma a usar
        $etiqueta = $idiomaEtiqueta ?: idioma_actual();
        
        // Buscar idioma por etiqueta
        $idioma = \App\Models\Idioma::where('etiqueta', $etiqueta)->first();
        if (!$idioma) {
            $idioma = \App\Models\Idioma::where('es_principal', true)->first();
        }

        if ($idioma && method_exists($galleryImage, 'getMultilingualAltText')) {
            return $galleryImage->getMultilingualAltText($idioma->id);
        }

        // Fallback al método estándar del modelo
        if (method_exists($galleryImage, 'getMultilingualAltText')) {
            return $galleryImage->getMultilingualAltText();
        }

        // Último fallback
        return $galleryImage->alt_text ?: $galleryImage->titulo ?: 'Imagen de galería';
    }
}