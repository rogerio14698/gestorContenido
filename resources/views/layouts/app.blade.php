<!DOCTYPE html>
<html lang="{{ idioma_etiqueta_html() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $idiomaEtiqueta = app()->getLocale() ?? (function_exists('idioma_actual') ? idioma_actual() : 'es');
        $textoMeta = $configEmpresa?->textos?->where('idioma.etiqueta', $idiomaEtiqueta)->first();
        if (!$textoMeta) {
            // Fallback al idioma principal
            $textoMeta = $configEmpresa?->textos?->where('idioma.es_principal', true)->first();
        }
        $metaTitulo = $textoMeta?->metatitulo ?? 'Nuntris Teatro';
        $metaDescripcion = $textoMeta?->metadescripcion ?? 'Compañía de teatro asturiana';
    @endphp
    <title>@yield('metaTitulo', $metaTitulo)</title>
    <meta name="description" content="@yield('meta_description', $metaDescripcion)">

    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />


    @vite(['resources/css/web.css'])
    @stack('styles')
</head>
<body>
    @php
        $idiomaActual = function_exists('idioma_actual') ? idioma_actual() : app()->getLocale();
        $idiomaRuta = \App\Helpers\IdiomaHelper::etiquetaParaRuta($idiomaActual ?? 'es');
        $idiomasDisponibles = function_exists('idiomas_disponibles') ? idiomas_disponibles() : [];
        $menusCollection = isset($menus) ? collect($menus) : collect();
    @endphp


    <header>
  @include('web.partials.menu')
    </header>

    <main>
        @yield('content')
    </main>

    @include('web.partials.footer', [
        'configuracion' => $configuracion,
        'configEmpresa' => $configEmpresa ?? null,
        'menuPrincipal' => $menuPrincipal ?? [],
        'idiomaRuta' => $idiomaRuta
    ])

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>