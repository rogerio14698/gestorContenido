@extends('layout.app')

@section('title', $gallery->nombre)
@section('meta_description', $gallery->descripcion ?: 'Galería de imágenes: ' . $gallery->nombre)

@section('content')
<div class="hero-section mb-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('galleries.index', ['idioma' => app()->getLocale()]) }}" class="text-white-50">
                                <i class="fas fa-images me-1"></i>
                                Galerías
                            </a>
                        </li>
                        <li class="breadcrumb-item active text-white" aria-current="page">
                            {{ $gallery->nombre }}
                        </li>
                    </ol>
                </nav>
                
                <h1 class="display-4 fw-bold text-white mb-3">
                    {{ $gallery->nombre }}
                </h1>
                
                @if($gallery->descripcion)
                    <p class="lead text-white-75 mb-3">
                        {{ $gallery->descripcion }}
                    </p>
                @endif
                
                <div class="gallery-stats">
                    <span class="badge bg-white bg-opacity-20 text-white me-3">
                        <i class="fas fa-images me-1"></i>
                        {{ $gallery->images->count() }} imágenes
                    </span>
                    <span class="badge bg-white bg-opacity-20 text-white">
                        <i class="fas fa-calendar me-1"></i>
                        {{ $gallery->created_at->format('d/m/Y') }}
                    </span>
                </div>
            </div>
            
            @if($gallery->imagen_portada)
                <div class="col-lg-4 text-center">
                    <div class="hero-image-container">
                        <img src="{{ get_responsive_image_url('storage/' . $gallery->imagen_portada, 'desktop') }}" 
                             alt="{{ $gallery->nombre }}"
                             class="img-fluid rounded shadow-lg"
                             style="max-height: 300px; object-fit: cover;">
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="container">
    @if($gallery->images->count() > 0)
        <!-- Filtros y controles -->
        <div class="row mb-4">
            <div class="col-lg-8">
                <div class="gallery-controls">
                    <button type="button" class="btn btn-outline-primary btn-sm me-2" id="grid-view-btn">
                        <i class="fas fa-th"></i> Cuadrícula
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-sm me-2" id="masonry-view-btn">
                        <i class="fas fa-th-large"></i> Mosaico
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="slideshow-btn">
                        <i class="fas fa-play"></i> Presentación
                    </button>
                </div>
            </div>
            <div class="col-lg-4 text-end">
                <span class="text-muted">
                    Mostrando {{ $gallery->images->count() }} imágenes
                </span>
            </div>
        </div>

        <!-- Galería de imágenes -->
        <div id="gallery-container" class="gallery-grid">
            @foreach($gallery->images as $index => $image)
                <div class="gallery-item" data-index="{{ $index }}">
                    <div class="image-wrapper">
                        <img src="{{ get_responsive_image_url('storage/' . $image->imagen, 'mobile') }}" 
                             data-full="{{ asset('storage/' . $image->imagen) }}"
                             alt="{{ get_gallery_image_alt($image) }}"
                             class="gallery-image"
                             loading="lazy"
                             onclick="openLightbox({{ $index }})">
                        
                        <div class="image-overlay">
                            <div class="overlay-content">
                                <h6 class="text-white mb-1">
                                    {{ $image->titulo ?: 'Imagen ' . ($index + 1) }}
                                </h6>
                                @if($image->descripcion)
                                    <p class="text-white-75 small mb-2">
                                        {{ Str::limit($image->descripcion, 60) }}
                                    </p>
                                @endif
                                <button type="button" class="btn btn-sm btn-light" onclick="openLightbox({{ $index }})">
                                    <i class="fas fa-search-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-image fa-4x text-muted mb-4"></i>
            <h3 class="text-muted">Esta galería está vacía</h3>
            <p class="text-muted">No hay imágenes para mostrar en esta galería.</p>
            <a href="{{ route('galleries.index', ['idioma' => app()->getLocale()]) }}" class="btn btn-primary">
                <i class="fas fa-arrow-left me-2"></i>
                Volver a Galerías
            </a>
        </div>
    @endif
</div>

<!-- Lightbox Modal -->
<div id="lightbox" class="lightbox" style="display: none;">
    <div class="lightbox-content">
        <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
        
        <div class="lightbox-image-container">
            <img id="lightbox-image" src="" alt="">
            
            <!-- Controles de navegación -->
            <button type="button" class="lightbox-nav lightbox-prev" onclick="changeLightboxImage(-1)">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button type="button" class="lightbox-nav lightbox-next" onclick="changeLightboxImage(1)">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
        
        <div class="lightbox-info">
            <h5 id="lightbox-title"></h5>
            <p id="lightbox-description"></p>
            <div class="lightbox-counter">
                <span id="lightbox-current">1</span> / <span id="lightbox-total">{{ $gallery->images->count() }}</span>
            </div>
        </div>
    </div>
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
    
    .breadcrumb {
        background: none;
        padding: 0;
        margin: 0;
    }
    
    .breadcrumb a {
        text-decoration: none;
    }
    
    .gallery-controls {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }
    
    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }
    
    .gallery-masonry {
        column-count: 3;
        column-gap: 1.5rem;
    }
    
    .gallery-item {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        break-inside: avoid;
        margin-bottom: 1.5rem;
    }
    
    .gallery-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
    
    .image-wrapper {
        position: relative;
        width: 100%;
        height: 250px;
        overflow: hidden;
    }
    
    .gallery-masonry .image-wrapper {
        height: auto;
    }
    
    .gallery-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
        cursor: pointer;
    }
    
    .gallery-masonry .gallery-image {
        height: auto;
    }
    
    .gallery-item:hover .gallery-image {
        transform: scale(1.05);
    }
    
    .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.8) 100%);
        display: flex;
        align-items: flex-end;
        padding: 1.5rem;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .gallery-item:hover .image-overlay {
        opacity: 1;
    }
    
    .overlay-content h6 {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    /* Lightbox Styles */
    .lightbox {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.95);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .lightbox-content {
        position: relative;
        max-width: 90vw;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    
    .lightbox-close {
        position: absolute;
        top: -50px;
        right: 0;
        color: white;
        font-size: 2rem;
        cursor: pointer;
        z-index: 10001;
    }
    
    .lightbox-image-container {
        position: relative;
        max-width: 100%;
        max-height: 70vh;
    }
    
    #lightbox-image {
        max-width: 100%;
        max-height: 70vh;
        object-fit: contain;
        border-radius: 8px;
    }
    
    .lightbox-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        font-size: 1.5rem;
        padding: 1rem;
        cursor: pointer;
        border-radius: 50%;
        transition: background 0.3s ease;
    }
    
    .lightbox-nav:hover {
        background: rgba(255, 255, 255, 0.3);
    }
    
    .lightbox-prev {
        left: -60px;
    }
    
    .lightbox-next {
        right: -60px;
    }
    
    .lightbox-info {
        color: white;
        text-align: center;
        margin-top: 1rem;
        max-width: 600px;
    }
    
    .lightbox-counter {
        margin-top: 1rem;
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.9rem;
    }
    
    @media (max-width: 992px) {
        .gallery-masonry {
            column-count: 2;
        }
        
        .gallery-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1rem;
        }
    }
    
    @media (max-width: 768px) {
        .hero-section {
            padding: 2rem 0;
        }
        
        .display-4 {
            font-size: 2rem;
        }
        
        .gallery-masonry {
            column-count: 1;
        }
        
        .gallery-grid {
            grid-template-columns: 1fr;
        }
        
        .lightbox-nav {
            position: fixed;
            top: 50%;
            font-size: 1.2rem;
            padding: 0.8rem;
        }
        
        .lightbox-prev {
            left: 20px;
        }
        
        .lightbox-next {
            right: 20px;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    const galleryImages = @json($gallery->images->map(function($image, $index) {
        return [
            'url' => asset('storage/' . $image->imagen),
            'title' => $image->titulo ?: $gallery->nombre . ' - Imagen ' . ($index + 1),
            'description' => $image->descripcion
        ];
    }));
    
    let currentImageIndex = 0;
    
    // Cambiar vista de galería
    document.getElementById('grid-view-btn')?.addEventListener('click', function() {
        const container = document.getElementById('gallery-container');
        container.className = 'gallery-grid';
        updateActiveButton(this);
    });
    
    document.getElementById('masonry-view-btn')?.addEventListener('click', function() {
        const container = document.getElementById('gallery-container');
        container.className = 'gallery-masonry';
        updateActiveButton(this);
    });
    
    function updateActiveButton(activeBtn) {
        document.querySelectorAll('.gallery-controls .btn').forEach(btn => {
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-outline-primary');
        });
        activeBtn.classList.remove('btn-outline-primary');
        activeBtn.classList.add('btn-primary');
    }
    
    // Funciones del lightbox
    function openLightbox(index) {
        currentImageIndex = index;
        updateLightboxImage();
        document.getElementById('lightbox').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeLightbox() {
        document.getElementById('lightbox').style.display = 'none';
        document.body.style.overflow = 'auto';
    }
    
    function changeLightboxImage(direction) {
        currentImageIndex += direction;
        
        if (currentImageIndex >= galleryImages.length) {
            currentImageIndex = 0;
        } else if (currentImageIndex < 0) {
            currentImageIndex = galleryImages.length - 1;
        }
        
        updateLightboxImage();
    }
    
    function updateLightboxImage() {
        const image = galleryImages[currentImageIndex];
        document.getElementById('lightbox-image').src = image.url;
        document.getElementById('lightbox-title').textContent = image.title;
        document.getElementById('lightbox-description').textContent = image.description || '';
        document.getElementById('lightbox-current').textContent = currentImageIndex + 1;
    }
    
    // Controles de teclado
    document.addEventListener('keydown', function(e) {
        const lightbox = document.getElementById('lightbox');
        if (lightbox.style.display === 'flex') {
            switch(e.key) {
                case 'Escape':
                    closeLightbox();
                    break;
                case 'ArrowLeft':
                    changeLightboxImage(-1);
                    break;
                case 'ArrowRight':
                    changeLightboxImage(1);
                    break;
            }
        }
    });
    
    // Cerrar lightbox al hacer clic fuera de la imagen
    document.getElementById('lightbox').addEventListener('click', function(e) {
        if (e.target === this) {
            closeLightbox();
        }
    });
    
    // Inicializar vista por defecto
    document.addEventListener('DOMContentLoaded', function() {
        updateActiveButton(document.getElementById('grid-view-btn'));
    });
</script>
@endsection