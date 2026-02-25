@extends('layouts.app')



@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>               
                 {{ __('web.noticias') }}
                </h1>
              
            </div>
        </div>
    </div>

    @if($noticias && $noticias->count() > 0)
        <div class="row">
            @foreach($noticias as $noticia)
                               
                
                @php
                    $texto = $noticia->textos->where('idioma.etiqueta', app()->getLocale())->first() ?? $noticia->textos->first();
                @endphp
                @if($texto)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card news-card h-100">
                                @if($noticia->imagen_portada)
                                    {!! responsive_image_html(
                                        $noticia->imagen_portada, 
                                        $texto->imagen_portada_alt ?? $noticia->imagen_portada_alt ?? $texto->titulo ?? 'Noticia de ' . ($noticia->lugar ?? 'teatro'),
                                        'card-img-top',
                                        'height: 250px; object-fit: cover;'
                                    ) !!}
                                @endif
                                
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">{{ $texto->titulo }}</h5>
                                    
                                    <div class="mb-2">
                                        @if($noticia->fecha_publicacion)
                                            <small class="text-muted">
                                                <i class="fas fa-calendar"></i> 
                                                {{ $noticia->fecha_publicacion->format('d/m/Y') }}
                                            </small>
                                        @endif
                                        
                                        @if($noticia->lugar)
                                            <small class="text-muted ms-2">
                                                <i class="fas fa-map-marker-alt"></i> {{ $noticia->lugar }}
                                            </small>
                                        @endif
                                    </div>
                                    
                                    @if($texto->resumen)
                                        {!! $texto->resumen !!}
                                    @elseif($texto->contenido)
                                        <p class="card-text flex-grow-1">
                                            {{ Str::limit(strip_tags($texto->contenido), 150) }}
                                        </p>
                                    @endif
                                    
                                    <div class="mt-auto">
                                        <a href="{{ route('contenido', [app()->getLocale(), $texto->slug]) }}">
                                            {{ __('web.leer_mas') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

            @endforeach
        </div>
        
        <!-- Paginación -->
        <div class="row">
            <div class="col-12 d-flex justify-content-center">
                {{ $noticias->links() }}
            </div>
        </div>
    @else
        <!-- Mensaje cuando no hay noticias -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                        <h4 class="fw-bold mb-3">{{ __('web.Próximamente') }}</h4>
                        
                        
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')

@endpush