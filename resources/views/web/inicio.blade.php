
@extends('layouts.app')

@section('title', 'Inicio - ' . ($configuracion->nombre_empresa ?? 'Nuntris Teatro'))

@php
    $slidesCollection = isset($slides) ? collect($slides) : collect();
    $idiomaActual = idioma_actual();
    $idiomaRuta = \App\Helpers\IdiomaHelper::etiquetaParaRuta($idiomaActual);

    $textoInicio = null;
    if ($contenidoInicio) {
        $textoInicio = $contenidoInicio->textos->first(function ($texto) use ($idiomaActual) {
            return optional($texto->idioma)->etiqueta === $idiomaActual;
        }) ?? $contenidoInicio->textos->first();
    }
    // Definir galería e imágenes de bienvenida solo si el contenido tiene galería asociada
    $galeria = null;
    $imagenesGaleria = collect();
    if (isset($contenidoInicio) && $contenidoInicio->galeria) {
        $galeria = $contenidoInicio->galeria;
        $imagenesGaleria = $galeria->images ?? collect();
    }
@endphp

@section('content')
    <section class="hero-section">
        @if($slidesCollection->isNotEmpty())
            <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="7000">
                <div class="carousel-inner">
                    @foreach($slidesCollection as $slide)
                        @php
                            $imagenUrl = $slide->imagen_url;
                            $tituloSlide = $slide->titulo ?: ($configuracion->nombre_empresa ?? 'Nuntris Teatro');
                            $descripcionSlide = $slide->descripcion ?: $configuracion->metadescripcion;
                            $ctaUrl = $slide->url;
                            $ctaTarget = $slide->nueva_ventana ? '_blank' : '_self';
                        @endphp
                        <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                            <div class="hero-slide" style="{{ $imagenUrl ? "background-image: url('{$imagenUrl}')" : 'background-color: var(--brand-primary);' }}">
                                <div class="hero-overlay">
                                    <div class="container">
                                        <div class="row justify-content-center justify-content-lg-start">
                                            <div class="col-lg-8 col-xl-6 hero-caption">
                                                <h1 class="display-5 mb-3">{{ $tituloSlide }}</h1>
                                                @if($descripcionSlide)
                                                    <p class="lead mb-4">{{ $descripcionSlide }}</p>
                                                @endif
                                                @if($ctaUrl)
                                                    @php app()->setLocale($idiomaActual); @endphp
                                                    <a href="{{ $ctaUrl }}" class="btn btn-warning btn-lg shadow-sm" target="{{ $ctaTarget }}">
                                                        {{ __('web.descubrir') }}
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($slidesCollection->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                @endif
            </div>
        @else
            <div class="hero-slide" style="background-color: var(--brand-primary);">
                <div class="hero-overlay">
                    <div class="container">
                        <div class="row justify-content-center justify-content-lg-start">
                            <div class="col-lg-8 col-xl-6 hero-caption">
                                <h1 class="display-5 mb-3">{{ $configuracion->nombre_empresa ?? 'Nuntris Teatro' }}</h1>
                                <p class="lead mb-4">{{ $configuracion->metadescripcion ?? 'Compañía de teatro asturiana especializada en obras clásicas y contemporáneas.' }}</p>
                                <a href="{{ route('noticias', ['idioma' => $idiomaRuta]) }}" class="btn btn-warning btn-lg shadow-sm">
                                    <i class="fa-solid fa-newspaper me-2"></i>{{ __('Ver noticias') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>

    @if($textoInicio)
        <section id="contenido-inicio" >
            <div class="container">
                <div class="row">
                    <div class="col-lg-10 col-xl-8">
                        <article>
                            <header>
                                <h1>{{ $textoInicio->titulo ?? ($configuracion->nombre_empresa ?? 'Nuntris Teatro') }}</h1>
                            @if(!empty($textoInicio->subtitulo))
                                <h2>{{ $textoInicio->subtitulo }}</h2>
                            @endif
                             </header>
                            @if(!empty($textoInicio->resumen))
                                {!! $textoInicio->resumen !!}
                            @endif
                            @if(!empty($textoInicio->contenido))
                                
                                    {!! $textoInicio->contenido !!}
                            @endif
                        </article>
                    </div>
                </div>

                {{-- Galería de bienvenida asociada al contenido de portada --}}
                @if($galeria && $imagenesGaleria && $imagenesGaleria->count() > 0)
                <div class="container mt-4">
                <div class="mt-4" id="galeria-bienvenida">
                   
                    <div id="galeriaCarrusel" class="carousel slide" data-bs-ride="carousel">
                        @php $numSlides = ceil($imagenesGaleria->count() / 3); @endphp
                        @if($numSlides > 1)
                        <div class="carousel-indicators">
                            @for($i = 0; $i < $numSlides; $i++)
                                <button type="button" data-bs-target="#galeriaCarrusel" data-bs-slide-to="{{ $i }}" class="{{ $i === 0 ? 'active' : '' }}" aria-current="{{ $i === 0 ? 'true' : 'false' }}" aria-label="Slide {{ $i+1 }}"></button>
                            @endfor
                        </div>
                        @endif
                        <div class="carousel-inner">
                            @foreach($imagenesGaleria->chunk(3) as $chunkIdx => $imagenesChunk)
                                <div class="carousel-item {{ $chunkIdx === 0 ? 'active' : '' }}">
                                    <div class="row justify-content-center">
                                        @foreach($imagenesChunk as $imgIdx => $imagen)
                                            @php
                                                $idx = $chunkIdx * 3 + $imgIdx;
                                                $urlImagen = $imagen->imagen ? Storage::url($imagen->imagen) : '';
                                            @endphp
                                            <div class="col-12 col-md-4 d-flex justify-content-center mb-3 mb-md-0">
                                                <a href="#" class="galeria-img-link" data-idx="{{ $idx }}" data-bs-toggle="modal" data-bs-target="#modalGaleria">
                                                    <img src="{{ $urlImagen }}" alt="{{ $imagen->alt_text ?? 'Imagen galería' }}" class="img-fluid rounded shadow-lg" style="max-height:340px;object-fit:contain;">
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($imagenesGaleria->count() > 3)
                            <button class="carousel-control-prev" type="button" data-bs-target="#galeriaCarrusel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Anterior</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#galeriaCarrusel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Siguiente</span>
                            </button>
                        @endif
                    </div>
                    <!-- Modal Galería (igual que antes) -->
                    <div class="modal fade" id="modalGaleria" tabindex="-1" aria-labelledby="modalGaleriaLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content bg-dark text-white position-relative">
                                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                <div class="modal-body text-center position-relative">
                                    <button type="button" class="btn btn-dark position-absolute top-50 start-0 translate-middle-y ms-2" id="galeriaPrev" style="z-index:2;">
                                        <i class="fa-solid fa-chevron-left fa-2x"></i>
                                    </button>
                                    <img id="modalGaleriaImg" src="" alt="" class="img-fluid rounded shadow-lg" style="max-height:70vh;">
                                    <button type="button" class="btn btn-dark position-absolute top-50 end-0 translate-middle-y me-2" id="galeriaNext" style="z-index:2;">
                                        <i class="fa-solid fa-chevron-right fa-2x"></i>
                                    </button>
                                    <div class="mt-3" id="modalGaleriaCaption"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const imagenes = [
                                @foreach($imagenesGaleria as $img)
                                    {
                                        url: "{{ $img->imagen ? Storage::url($img->imagen) : '' }}",
                                        alt: "{{ addslashes($img->alt_text ?? 'Imagen galería') }}",
                                        titulo: "{{ addslashes($img->titulo ?? '') }}",
                                        descripcion: "{!! addslashes($img->descripcion ?? '') !!}"
                                    },
                                @endforeach
                            ];
                            let idxActual = 0;
                            const modalImg = document.getElementById('modalGaleriaImg');
                            const modalCaption = document.getElementById('modalGaleriaCaption');
                            const galeriaLinks = document.querySelectorAll('.galeria-img-link');
                            const prevBtn = document.getElementById('galeriaPrev');
                            const nextBtn = document.getElementById('galeriaNext');
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
                            document.getElementById('modalGaleria').addEventListener('show.bs.modal', function() {
                                mostrarImagen(idxActual);
                            });
                        });
                    </script>
                </div>
                </div><!-- .container -->
                @endif
            </div>
        </section>
    @endif

    <section id="noticias-portada">
        <div class="container">
            @if($noticiasPortada && $noticiasPortada->count() > 0)
                <div class="col-lg-10">
                    <div>
                        
                        <h3>{{ __('web.ultimas_noticias') }}</h3>
                    </div>
                </div>

                <div class="row g-4">
                    @foreach($noticiasPortada as $noticia)
                        @php
                            $textoNoticia = $noticia->textos->first();
                        @endphp
                        @if($textoNoticia)
                            <div class="col-md-6 col-lg-3">
                                <div class="card h-100 border-0 shadow-sm">
                                    @if($noticia->imagen_portada)
                                        {!! responsive_image_html(
                                            $noticia->imagen_portada,
                                            $textoNoticia->imagen_portada_alt ?? $noticia->imagen_portada_alt ?? $textoNoticia->titulo ?? 'Noticia',
                                            'card-img-top noticias-img'
                                        ) !!}
                                    @endif
                                    <div class="card-body d-flex flex-column">
                                        <h4>{{ $textoNoticia->titulo }}</h4>
                                        @if($noticia->fecha_publicacion)
                                            <p class="text-muted small mb-2">
                                                <i class="fa-solid fa-calendar-days me-1"></i>{{ optional($noticia->fecha_publicacion)->format('d/m/Y') }}
                                             
                                              <spam class="text-muted ms-2">
                                                <i class="fas fa-map-marker-alt"></i> {{ $noticia->lugar }}
                                            </spam> 
                                        </p> 
                                        @endif
                                                                                
                                    
                                       
                                        
                                        @if($textoNoticia->resumen)
                                           {!! $textoNoticia->resumen !!}
                                        @endif
                                        <div class="mt-3">
                                            <a href="{{ route('contenido', [$idiomaRuta, $textoNoticia->slug]) }}" class="btn btn-link px-0 fw-semibold">
                                               {{ __('web.leer_mas') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <h4 class="fw-bold mb-3">{{ __('web.Próximamente') }}</h4>
                 </div>
                </div>
            @endif
        </div>
    </section>


   


    <section class="py-5">
        <div class="container">
            <div class="card border-0 shadow-lg overflow-hidden">
                <div class="row g-0 align-items-center">
                    <div class="col-lg-7 p-5 p-lg-5">
                        <h3 class="fw-bold mb-3">{{ $idiomaActual === 'ast' ? '¿Quies saber más?' : '¿Quieres saber más?' }}</h3>
                        <p class="text-muted mb-4">{{ $idiomaActual === 'ast' ? 'Contáctanos y cuéntanos qué necesites. Tamos pa ayudar onde faga falta.' : 'Contáctanos y cuéntanos qué necesitas. Estamos para ayudarte en todo lo que haga falta.' }}</p>
                        @if(!empty($configuracion) && !empty($configuracion->email))
                            <a href="mailto:{{ $configuracion->email }}" class="btn btn-dark btn-lg">
                                <i class="fa-solid fa-envelope me-2"></i>{{ $idiomaActual === 'ast' ? 'Contautar' : 'Contactar' }}
                            </a>
                        @endif
                    </div>
                    <div class="col-lg-5 bg-dark position-relative text-white py-5 px-4">
                        <div class="position-absolute top-0 start-0 w-100 h-100" style="background-image: url('{{ asset('maqueta/assets/img/backgrounds/Recurso1.png') }}'); background-size: cover; background-position: center; opacity: 0.25;"></div>
                        <div class="position-relative">
                            <h4 class="fw-semibold mb-3">{{ __('Información de contacto') }}</h4>
                            <ul class="list-unstyled mb-0">
                                @if($configuracion->telefono_empresa)
                                    <li class="mb-2"><i class="fa-solid fa-phone me-2"></i>{{ $configuracion->telefono_empresa }}</li>
                                @endif
                                @if($configuracion->movil_empresa)
                                    <li class="mb-2"><i class="fa-solid fa-mobile-screen-button me-2"></i>{{ $configuracion->movil_empresa }}</li>
                                @endif
                                @if($configuracion->direccion_empresa)
                                    <li class="mb-2"><i class="fa-solid fa-location-dot me-2"></i>{{ $configuracion->direccion_empresa }}</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
@push('styles')
     @vite(['resources/css/inicio.css'])
@endpush