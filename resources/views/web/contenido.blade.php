@extends('layouts.app')


@php
    $idiomaVista = isset($idioma) ? $idioma : (request()->route('idioma') ?? app()->getLocale());
    $texto = null;
    if ($contenido) {
        $texto = $contenido->textos->where('idioma.etiqueta', $idiomaVista)->first();
    }
@endphp
@section('title', $texto?->metatitulo ?: ($texto?->titulo ? $texto->titulo . ' - ' : '') . ($configuracion->nombre_empresa ?? 'Nuntris Teatro'))
@section('meta_description', $texto?->metadescripcion ?: $texto?->resumen)

@section('content')

<div class="container">
    @if($texto)
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('inicio', app()->getLocale()) }}">
                        {{ app()->getLocale() == 'as' ? 'Entamu' : 'Inicio' }}
           
                        </a>
                    </li>
                @endif
                <li class="breadcrumb-item active" aria-current="page">
                    @if($texto)
                </li>
            </ol>
        </nav>
<section id="detalle-contenido">
        <div class="row">
            <!-- Contenido principal -->
            <div class="col-lg-8">
                <article>
                    @if($contenido->imagen)
                        {!! responsive_image_html(
                            $contenido->imagen, 
                            $texto->imagen_alt ?? $contenido->imagen_alt ?? $texto->titulo ?? 'Imagen de ' . ucfirst($contenido->tipo_contenido),
                            'card-img-top','max-height: 400px; object-fit: cover; width: 100%;') !!}
                    @endif
                    
                    <header>
                        <!-- Título -->
                        <h1>{{ $texto->titulo }}</h1>
                        
                        <!-- Subtítulo -->
                        @if($texto->subtitulo)
                            <h2>{{ $texto->subtitulo }}</h2>
                        @endif
                    </header>
                      
                        <div
                            @if($contenido->fecha_publicacion && $contenido->tipo_contenido == 'noticia')
                            
                
                                    {{ $contenido->fecha_publicacion->format('d/m/Y') }}
                           
                            @endif
                            
                            @if($contenido->lugar)
                     
                              
                                    {{ $contenido->lugar }}
                     
                            @endif
                            
        
                     
                                {{ ucfirst($contenido->tipo_contenido) }}

                        </div>
                        
                        <!-- Resumen -->
                        @if($texto->resumen)
                           
                                {!! $texto->resumen !!}

                        @endif
                        
                        <!-- Contenido -->
                        @if($texto->contenido)
                            <div class="content-body">
                                {!! $texto->contenido !!}
                            </div>
                        @endif
                    </div>
                </article>

                {{-- Galería asociada (debajo del contenido principal) --}}
                @if(isset($galeria) && $galeria->images->count() > 0)
                    <section class="py-5">
                        <div class="container">
                            <h3 class="fw-bold mb-4">{{ $galeria->nombre ?? __('Galería') }}</h3>
                            <div class="row g-3 mb-4">
                                @foreach($galeria->images as $idx => $img)
                                    <div class="col-md-4">
                                        <a href="#" class="galeria-img-link" data-idx="{{ $idx }}" data-bs-toggle="modal" data-bs-target="#modalGaleriaDetalle">
                                            <img src="{{ Storage::url($img->imagen) }}" alt="{{ $img->alt_text ?? $img->titulo ?? '' }}" class="img-fluid rounded shadow-sm w-100" style="cursor:pointer;object-fit:cover;max-height:220px;">
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                            <!-- Modal Galería Detalle -->
                            <div class="modal fade" id="modalGaleriaDetalle" tabindex="-1" aria-labelledby="modalGaleriaDetalleLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content bg-dark text-white position-relative">
                                        <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                        <div class="modal-body text-center position-relative">
                                            <button type="button" class="btn btn-dark position-absolute top-50 start-0 translate-middle-y ms-2" id="galeriaDetallePrev" style="z-index:2;">
                                                <i class="fa-solid fa-chevron-left fa-2x"></i>
                                            </button>
                                            <img id="modalGaleriaDetalleImg" src="" alt="" class="img-fluid rounded shadow-lg" style="max-height:70vh;">
                                            <button type="button" class="btn btn-dark position-absolute top-50 end-0 translate-middle-y me-2" id="galeriaDetalleNext" style="z-index:2;">
                                                <i class="fa-solid fa-chevron-right fa-2x"></i>
                                            </button>
                                            <div class="mt-3" id="modalGaleriaDetalleCaption"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @push('scripts')
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const imagenes = [
                                        @foreach($galeria->images as $img)
                                            {
                                                url: "{{ $img->imagen ? Storage::url($img->imagen) : '' }}",
                                                alt: "{{ addslashes($img->alt_text ?? $img->titulo ?? 'Imagen galería') }}",
                                                titulo: "{{ addslashes($img->titulo ?? '') }}",
                                                descripcion: "{!! addslashes($img->descripcion ?? '') !!}"
                                            },
                                        @endforeach
                                    ];
                                    let idxActual = 0;
                                    const modalImg = document.getElementById('modalGaleriaDetalleImg');
                                    const modalCaption = document.getElementById('modalGaleriaDetalleCaption');
                                    const galeriaLinks = document.querySelectorAll('.galeria-img-link');
                                    const prevBtn = document.getElementById('galeriaDetallePrev');
                                    const nextBtn = document.getElementById('galeriaDetalleNext');
                                    function mostrarImagen(idx) {
                                        if (!imagenes[idx]) return;
                                        idxActual = idx;
                                        modalImg.src = imagenes[idx].url;
                                        modalImg.alt = imagenes[idx].alt;
                                        let caption = '';
                                        if(imagenes[idx].titulo) caption += '<h5>' + imagenes[idx].titulo + '</h5>';
                                        if(imagenes[idx].descripcion) caption += '<div>' + imagenes[idx].descripcion + '</div>';
                                        modalCaption.innerHTML = caption;
                                    }
                                    galeriaLinks.forEach(link => {
                                        link.addEventListener('click', function(e) {
                                            e.preventDefault();
                                            const idx = parseInt(this.getAttribute('data-idx'));
                                            mostrarImagen(idx);
                                        });
                                    });
                                    prevBtn.addEventListener('click', function() {
                                        let prev = idxActual - 1;
                                        if (prev < 0) prev = imagenes.length - 1;
                                        mostrarImagen(prev);
                                    });
                                    nextBtn.addEventListener('click', function() {
                                        let next = idxActual + 1;
                                        if (next >= imagenes.length) next = 0;
                                        mostrarImagen(next);
                                    });
                                    document.getElementById('modalGaleriaDetalle').addEventListener('show.bs.modal', function() {
                                        mostrarImagen(idxActual);
                                    });
                                });
                            </script>
                            @endpush
                        </div>
                    </section>
                @endif
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Widget de información -->
                <div class="card mb-4">
                   <div class="card-header">
                       <h5 class="mb-0">
                           <i class="fas fa-info-circle"></i> {{ __('Información') }}
                       </h5>
                   </div>
                     
              
                    <div class="card-body">
                        @if($contenido->fecha)
                            <p>
                                <strong>{{ __('Fecha:') }}</strong>
                                {{ $contenido->fecha->format('d/m/Y') }}
                            </p>
                        @endif
                        
                        @if($contenido->lugar)
                            <p class="mb-2">
                                <strong>{{ __('Lugar:') }}</strong><br>
                                {{ $contenido->lugar }}
                            </p>
                        @endif
                        
                        @if($contenido->galeria)
                            {{-- Eliminado el botón de galería, ya que la galería se muestra debajo del contenido principal --}}
                        @endif
                    </div>
                </div>
                
                <!-- Widget de contacto -->
                @if($configuracion->email || $configuracion->telefono_empresa)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-envelope"></i> 
                                {{ __('Contacto') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($configuracion->email)
                                <p class="mb-2">
                                    <a href="mailto:{{ $configuracion->email }}" class="btn btn-outline-primary btn-sm w-100">
                                        <i class="fas fa-envelope"></i> 
                                        {{ __('Enviar email') }}
                                    </a>
                                </p>
                            @endif
                            
                            @if($configuracion->telefono_empresa)
                                <p class="mb-0">
                                    <a href="tel:{{ $configuracion->telefono_empresa }}" class="btn btn-outline-secondary btn-sm w-100">
                                        <i class="fas fa-phone"></i> 
                                        {{ $configuracion->telefono_empresa }}
                                    </a>
                                </p>
                            @endif
                        </div>
                    </div>
                @endif
                
                <!-- Widget de redes sociales -->
                @if($configuracion->facebook || $configuracion->twitter || $configuracion->instagram || $configuracion->youtube)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-share-alt"></i> 
                                {{ __('Síguenos') }}
                            </h5>
                        </div>
                        <div class="card-body text-center">
                            @if($configuracion->facebook)
                                <a href="{{ $configuracion->facebook }}" target="_blank" class="btn btn-primary btn-sm me-1 mb-2">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                            @endif
                            @if($configuracion->twitter)
                                <a href="{{ $configuracion->twitter }}" target="_blank" class="btn btn-info btn-sm me-1 mb-2">
                                    <i class="fab fa-twitter"></i>
                                </a>
                            @endif
                            @if($configuracion->instagram)
                                <a href="{{ $configuracion->instagram }}" target="_blank" class="btn btn-danger btn-sm me-1 mb-2">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            @endif
                            @if($configuracion->youtube)
                                <a href="{{ $configuracion->youtube }}" target="_blank" class="btn btn-dark btn-sm me-1 mb-2">
                                    <i class="fab fa-youtube"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
</section>
    @endif
</div>
                @endsection

@push('styles')
    @vite(['resources/css/contenido.css'])
@endpush