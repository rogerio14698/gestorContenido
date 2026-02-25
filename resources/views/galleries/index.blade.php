@extends('layout.app')

@section('title', 'Galerías de Imágenes')
@section('meta_description', 'Explora nuestras galerías de imágenes del teatro.')

@section('content')
<div class="hero-section mb-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-12 text-center">
                <h1 class="display-4 fw-bold text-primary mb-3">
                    <i class="fas fa-images me-3"></i>
                    Galerías de Imágenes
                </h1>
                <p class="lead text-muted">
                    Descubre momentos únicos a través de nuestras colecciones fotográficas
                </p>
            </div>
        </div>
    </div>
</div>

<div class="container">
    @if($galleries->count() > 0)
        <div class="row">
            @foreach($galleries as $gallery)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="gallery-card card h-100 shadow-sm">
                        <div class="gallery-image-container">
                            @if($gallery->imagen_portada)
                                <img src="{{ get_responsive_image_url('storage/' . $gallery->imagen_portada, 'desktop') }}" 
                                     alt="{{ $gallery->nombre }}"
                                     class="card-img-top gallery-cover-image"
                                     loading="lazy">
                            @else
                                <div class="no-image-placeholder d-flex align-items-center justify-content-center">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            @endif
                            
                            <!-- Overlay con información -->
                            <div class="gallery-overlay">
                                <div class="gallery-info">
                                    <h5 class="text-white mb-2">{{ $gallery->nombre }}</h5>
                                    <p class="text-white-50 mb-3">
                                        <i class="fas fa-images me-2"></i>
                                        {{ $gallery->images->count() }} imágenes
                                    </p>
                                    <a href="{{ route('galleries.show', ['idioma' => app()->getLocale(), 'slug' => $gallery->slug]) }}" 
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye me-2"></i>
                                        Ver Galería
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <h5 class="card-title">{{ $gallery->nombre }}</h5>
                            @if($gallery->descripcion)
                                <p class="card-text text-muted">
                                    {{ Str::limit($gallery->descripcion, 120) }}
                                </p>
                            @endif
                            
                            <div class="gallery-meta d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    {{ $gallery->created_at->format('d/m/Y') }}
                                </small>
                                <span class="badge bg-primary">
                                    {{ $gallery->images->count() }} fotos
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-images fa-4x text-muted mb-4"></i>
                    <h3 class="text-muted">No hay galerías disponibles</h3>
                    <p class="text-muted">
                        Actualmente no tenemos galerías publicadas. 
                        Vuelve pronto para ver nuestras nuevas colecciones.
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('styles')
<style>
    .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 4rem 0;
        margin-bottom: 3rem;
    }
    
    .gallery-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
        border: none;
    }
    
    .gallery-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important;
    }
    
    .gallery-image-container {
        position: relative;
        height: 250px;
        overflow: hidden;
    }
    
    .gallery-cover-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .gallery-card:hover .gallery-cover-image {
        transform: scale(1.1);
    }
    
    .no-image-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(45deg, #f8f9fa, #e9ecef);
    }
    
    .gallery-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.7) 100%);
        display: flex;
        align-items: flex-end;
        padding: 1.5rem;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .gallery-card:hover .gallery-overlay {
        opacity: 1;
    }
    
    .gallery-info h5 {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .gallery-meta {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #f0f0f0;
    }
    
    .card-title {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 1rem;
    }
    
    .badge {
        font-size: 0.75rem;
        padding: 0.4rem 0.8rem;
    }
    
    @media (max-width: 768px) {
        .hero-section {
            padding: 2rem 0;
        }
        
        .display-4 {
            font-size: 2rem;
        }
        
        .gallery-image-container {
            height: 200px;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    // Lazy loading mejorado para imágenes
    document.addEventListener('DOMContentLoaded', function() {
        const images = document.querySelectorAll('img[loading="lazy"]');
        
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.src; // Trigger load
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });
            
            images.forEach(img => imageObserver.observe(img));
        }
    });
</script>
@endsection