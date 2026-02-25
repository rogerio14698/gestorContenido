@extends('admin.layouts.app')

@section('title', 'Galería: ' . $gallery->nombre)

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ $gallery->nombre }}</h1>
                    <p class="text-muted">{{ $gallery->descripcion }}</p>
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-right">
                        <a href="{{ route('admin.galleries.edit', $gallery) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar Galería
                        </a>
                        <a href="{{ route('admin.galleries.index') }}" class="btn btn-secondary ml-2">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Panel de upload de imágenes -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cloud-upload-alt"></i>
                        Subir Nuevas Imágenes
                    </h3>
                </div>
                <div class="card-body">
                    <form id="upload-form" action="{{ route('admin.galleries.images.upload', $gallery) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="images" class="form-label">Seleccionar Imágenes</label>
                            <input type="file" 
                                   class="form-control" 
                                   id="images" 
                                   name="images[]" 
                                   multiple 
                                   accept="image/*"
                                   required>
                            <small class="form-text text-muted">
                                Puedes seleccionar múltiples imágenes. Formatos: JPG, PNG, GIF, WebP. Máximo 10MB por imagen.
                            </small>
                        </div>
                        <button type="submit" class="btn btn-primary" id="upload-btn">
                            <i class="fas fa-upload me-2"></i>
                            Subir Imágenes
                        </button>
                    </form>
                    
                    <div id="upload-progress" style="display: none;" class="mt-3">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%"></div>
                        </div>
                        <p class="mt-2">Subiendo imágenes...</p>
                    </div>
                </div>
            </div>

            <!-- Galería de imágenes -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-images"></i>
                        Imágenes de la Galería ({{ $gallery->images->count() }})
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-info" id="toggle-sort-btn">
                            <i class="fas fa-sort" id="sort-icon"></i>
                            <span id="sort-text">Modo Ordenar</span>
                        </button>
                        <button type="button" class="btn btn-sm btn-warning ms-2" onclick="testModal()">
                            <i class="fas fa-test"></i>
                            Probar Modal
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($gallery->images->count() > 0)
                        <div id="sort-instructions" class="alert alert-info" style="display: none;">
                            <i class="fas fa-info-circle"></i>
                            <strong>Modo Ordenar Activo:</strong> 
                            <br>• <strong>Arrastra</strong> las imágenes para cambiar su orden
                            <br>• <strong>Edita</strong> los números directamente en los campos
                            <br>• Los cambios se guardan automáticamente
                            <br>• <strong>Haz clic en "Guardar Orden"</strong> para finalizar
                        </div>
                        
                        <div id="images-grid" class="sortable-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
                            @foreach($gallery->images->sortBy('orden') as $image)
                                <div class="image-item" 
                                     data-id="{{ $image->id }}" 
                                     data-orden="{{ $image->orden }}"
                                     draggable="false">
                                    <div class="card image-card">

                                        <div class="orden-input">
                                            <input type="number" 
                                                   class="form-control form-control-sm orden-field" 
                                                   value="{{ $image->orden }}" 
                                                   min="1" 
                                                   data-image-id="{{ $image->id }}">
                                        </div>
                                        
                                        <div class="image-container position-relative">
                                            <img src="{{ asset('storage/' . $image->imagen) }}" 
                                                 alt="{{ $image->alt_text }}"
                                                 class="card-img-top"
                                                 style="height: 150px; object-fit: cover; width: 100%; border-radius: 4px 4px 0 0;">
                                            
                                            <!-- Overlay con controles -->
                                            <div class="image-overlay">
                                                <div class="overlay-controls">
                                                    <button type="button" 
                                                            class="btn btn-sm btn-primary me-1" 
                                                            onclick="editImageTexts({{ $image->id }})"
                                                            title="Editar textos">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger" 
                                                            onclick="deleteImage({{ $image->id }})"
                                                            title="Eliminar imagen">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        @php
                                            $principalText = isset($idiomaPrincipal)
                                                ? $image->texts->firstWhere('idioma_id', $idiomaPrincipal->id)
                                                : null;
                                            $originalName = $image->metadatos['original_name'] ?? null;
                                            $displayTitle = $image->titulo
                                                ?: ($principalText->titulo ?? $originalName);
                                            $principalAlt = $principalText->alt_text ?? null;
                                            $baseAlt = $image->getOriginal('alt_text');
                                            $displayAlt = $principalAlt ?: $baseAlt;
                                        @endphp
                                        <div class="card-body">
                                            <div class="orden-display" style="font-size: 12px; margin-bottom: 6px;">
                                                Orden: {{ $image->orden }}
                                                @if($loop->first)
                                                    <span class="badge bg-success ms-1" style="font-size: 9px;">Portada</span>
                                                @endif
                                            </div>
                                            @if($displayTitle)
                                                <h6 class="image-title mb-1" title="{{ $displayTitle }}">
                                                    {{ \Illuminate\Support\Str::limit($displayTitle, 60) }}
                                                </h6>
                                            @endif
                                            <p class="image-alt text-muted small mb-0" title="{{ $displayAlt ?? 'Sin texto ALT configurado' }}">
                                                @if($displayAlt)
                                                    ALT: {{ \Illuminate\Support\Str::limit($displayAlt, 80) }}
                                                @else
                                                    <span class="text-warning">Sin texto ALT configurado</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-image fa-3x text-muted mb-3"></i>
                            <h4>No hay imágenes en esta galería</h4>
                            <p class="text-muted">Sube algunas imágenes usando el formulario de arriba.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal para editar textos multiidioma -->
<div class="modal fade" id="editImageTextsModal" tabindex="-1" aria-labelledby="editImageTextsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editImageTextsModalLabel">
                    <i class="fas fa-language"></i> Textos Multiidioma
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="imageTextsForm">
                    @csrf
                    <input type="hidden" id="imageId" name="image_id">
                    
                    <!-- Imagen preview -->
                    <div class="text-center mb-4">
                        <img id="modalImagePreview" src="" alt="" class="img-thumbnail" style="max-height: 150px;">
                    </div>

                    <!-- Tabs para idiomas -->
                    <!-- DEBUG: Número de idiomas activos: {{ $idiomasActivos->count() }} -->
                    <ul class="nav nav-tabs" id="languageTabsTexts" role="tablist">
                        @if($idiomasActivos->count() === 0)
                            <li class="nav-item">
                                <span class="nav-link text-warning">⚠️ No hay idiomas activos configurados</span>
                            </li>
                        @else
                            <!-- DEBUG: Renderizando {{ $idiomasActivos->count() }} idiomas -->
                        @endif
                        @foreach($idiomasActivos as $index => $idioma)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                        id="lang-{{ $idioma->id }}-tab" 
                                        data-bs-toggle="tab" 
                                        data-bs-target="#lang-{{ $idioma->id }}-text-pane" 
                                        type="button" 
                                        role="tab"
                                        title="{{ $idioma->nombre }}">
                                    @if($idioma->imagen)
                                        <img src="{{ asset('storage/' . $idioma->imagen) }}" 
                                             alt="{{ $idioma->nombre }}" 
                                             style="width: 16px; height: 16px; object-fit: cover; margin-right: 5px;">
                                    @endif
                                    {{ strtoupper($idioma->etiqueta) }}
                                    @if($idioma->es_principal)
                                        <i class="fas fa-star text-warning ms-1" title="Idioma principal"></i>
                                    @endif
                                </button>
                            </li>
                        @endforeach
                    </ul>

                    <!-- Contenido de tabs -->
                    <div class="tab-content mt-3" id="languageTabContent">
                        @foreach($idiomasActivos as $index => $idioma)
                            <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                 id="lang-{{ $idioma->id }}-text-pane" 
                                 role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label for="titulo_{{ $idioma->id }}" class="form-label">
                                                <i class="fas fa-heading"></i> Título
                                            </label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="titulo_{{ $idioma->id }}" 
                                                   name="titulo[{{ $idioma->id }}]"
                                                   placeholder="Título de la imagen en {{ $idioma->nombre }}">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="descripcion_{{ $idioma->id }}" class="form-label">
                                                <i class="fas fa-align-left"></i> Descripción
                                            </label>
                                            <textarea class="form-control" 
                                                      id="descripcion_{{ $idioma->id }}" 
                                                      name="descripcion[{{ $idioma->id }}]" 
                                                      rows="3"
                                                      placeholder="Descripción de la imagen en {{ $idioma->nombre }}"></textarea>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="alt_text_{{ $idioma->id }}" class="form-label">
                                                <i class="fas fa-universal-access"></i> Texto Alternativo (ALT)
                                                <small class="text-muted"> - Para accesibilidad</small>
                                            </label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="alt_text_{{ $idioma->id }}" 
                                                   name="alt_text[{{ $idioma->id }}]"
                                                   placeholder="Texto alternativo en {{ $idioma->nombre }}"
                                                   maxlength="255">
                                            <small class="form-text text-info">
                                                <i class="fas fa-info-circle"></i>
                                                Este texto es crucial para usuarios con discapacidad visual y SEO.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-primary" onclick="saveImageTexts()">
                    <i class="fas fa-save"></i> Guardar Textos
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('styles')
<style>
    /* === GRID MEJORADO - VERSIÓN 2.0 === */
    
    /* Contenedor principal de imágenes - Grid más compacto */
    .sortable-container {
        display: grid !important;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)) !important;
        gap: 20px !important;
        padding: 20px 0 !important;
        max-width: 100% !important;
    }

    /* Forzar 4 columnas en pantallas grandes */
    @media (min-width: 1200px) {
        .sortable-container {
            grid-template-columns: repeat(4, 1fr) !important;
            gap: 20px !important;
        }
    }

    /* 3 columnas en pantallas medianas */
    @media (min-width: 768px) and (max-width: 1199px) {
        .sortable-container {
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 15px !important;
        }
    }

    .image-item {
        position: relative;
        transition: transform 0.2s ease, opacity 0.2s ease;
        width: 100% !important;
        max-width: none !important;
    }

    .image-card {
        position: relative;
        overflow: hidden;
        border: 2px solid transparent;
        transition: all 0.2s ease;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        height: fit-content;
        width: 100% !important;
        display: block !important;
    }
    
    .image-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    }

    .image-card .card-body {
        padding: 10px 12px;
        font-size: 13px;
    }

    .image-title {
        font-size: 13px;
        font-weight: 600;
        color: #2c3e50;
    }

    .image-alt {
        font-size: 12px;
        color: #6c757d;
    }
    
    .image-container {
        position: relative;
        overflow: hidden;
    }
    
    .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .image-card:hover .image-overlay {
        opacity: 1;
    }
    
    .overlay-controls {
        display: flex;
        gap: 10px;
    }
    
    /* Indicador de orden siempre visible - más pequeño */
    .orden-badge {
        position: absolute;
        top: 8px;
        left: 8px;
        background: #007bff;
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 11px;
        z-index: 10;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    
    /* Campo de orden editable - más pequeño */
    .orden-input {
        position: absolute;
        top: 8px;
        right: 8px;
        opacity: 0;
        transition: opacity 0.3s ease;
        z-index: 15;
        width: 45px;
    }
    
    .orden-field {
        text-align: center;
        font-size: 11px;
        height: 24px;
        padding: 2px 4px;
    }
    
    /* Estados del drag & drop nativo */
    .image-item[draggable="true"] {
        cursor: grab;
        transition: all 0.2s ease;
        user-select: none;
    }
    
    .image-item[draggable="true"]:hover {
        transform: scale(1.02);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    .image-item[draggable="true"]:active {
        cursor: grabbing;
    }
    
    .image-item[style*="opacity: 0.5"] {
        opacity: 0.5 !important;
        transform: rotate(5deg) scale(0.9);
        z-index: 1000;
        box-shadow: 0 10px 25px rgba(0,0,0,0.4);
    }
    
    .image-item.drag-over {
        border: 3px solid #28a745 !important;
        background-color: rgba(40, 167, 69, 0.1);
        transform: scale(1.05);
    }
    
    /* Modo ordenar activo */
    .sort-mode .image-item {
        border: 2px dashed #007bff;
        margin: 5px;
    }
    
    .sort-mode .image-card {
        border-color: #007bff;
        background: #f8f9fa;
        cursor: grab;
    }
    
    .sort-mode .image-item[draggable="true"] {
        cursor: grab;
    }
    
    .sort-mode .orden-badge {
        background: #28a745;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(40, 167, 69, 0); }
        100% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
    }
    
    .sort-mode .orden-input {
        opacity: 1;
    }
    
    .sort-mode .orden-field {
        border-color: #007bff;
        background: white;
    }
    
    .sort-mode .image-overlay {
        display: none !important;
    }
    
    /* Campo de orden */
    .orden-input {
        position: absolute;
        top: 8px;
        right: 8px;
        z-index: 20;
        width: 50px;
        opacity: 0.3;
        transition: opacity 0.2s ease;
    }
    
    .orden-input:hover {
        opacity: 1;
    }
    
    .orden-field {
        text-align: center;
        font-weight: bold;
        background: rgba(255,255,255,0.95);
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 12px;
    }
    
    /* Estados del drag & drop nativo */
    .image-item[draggable="true"] {
        cursor: move;
        transition: all 0.2s ease;
    }
    
    .image-item[style*="opacity: 0.5"] {
        opacity: 0.5 !important;
        transform: rotate(2deg) scale(0.95);
        z-index: 1000;
        box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    }
    
    .image-item.drag-over {
        border: 3px solid #28a745 !important;
        background-color: rgba(40, 167, 69, 0.1);
        transform: scale(1.02);
    }
    
    /* Modo ordenar activo */
    .sort-mode .image-card {
        border-color: #007bff;
        background: #f8f9fa;
        cursor: move;
    }
    
    .sort-mode .image-item {
        border: 2px dashed #007bff;
    }
    
    .sort-mode .image-item:hover {
        transform: scale(1.02);
        box-shadow: 0 5px 15px rgba(0,0,0,0.15);
    }
    
    .sort-mode .drag-handle {
        opacity: 1;
        background: #007bff;
        color: white;
    }
    
    .sort-mode .orden-input {
        opacity: 1;
    }
    
    .sort-mode .orden-field {
        border-color: #007bff;
        background: white;
    }
    
    .sort-mode .image-overlay {
        display: none !important;
    }
    
    /* Animación de actualización */
    .updating-order {
        border-color: #28a745 !important;
        animation: pulse 1s infinite;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.02); }
        100% { transform: scale(1); }
    }

    /* Responsive - móviles */
    @media (max-width: 767px) {
        .sortable-container {
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            padding: 10px 0;
        }
        
        .image-card .card-body {
            padding: 8px 10px;
            font-size: 12px;
        }
        
        .orden-badge, .orden-input {
            width: 20px;
            height: 20px;
            font-size: 10px;
        }
        
        .orden-field {
            height: 20px;
            font-size: 10px;
        }
    }

    /* Extra pequeños - 1 columna */
    @media (max-width: 480px) {
        .sortable-container {
            grid-template-columns: 1fr;
        }
    }

    /* ====== ESTILOS PARA DRAG & DROP CON SORTABLEJS ====== */
    
    /* Elemento siendo arrastrado (ghost) */
    .sortable-ghost {
        opacity: 0.4;
        background: #f8f9fa !important;
        border: 2px dashed #007bff !important;
        transform: rotate(5deg) scale(0.95) !important;
        box-shadow: 0 15px 30px rgba(0,0,0,0.3) !important;
    }
    
    /* Elemento seleccionado para arrastrar */
    .sortable-chosen {
        transform: scale(1.05) !important;
        box-shadow: 0 8px 25px rgba(0,0,0,0.2) !important;
        z-index: 999 !important;
        border: 2px solid #28a745 !important;
    }
    
    /* Elemento mientras se arrastra */
    .sortable-drag {
        opacity: 0.8 !important;
        transform: rotate(8deg) scale(0.9) !important;
        box-shadow: 0 20px 40px rgba(0,0,0,0.4) !important;
        z-index: 1000 !important;
        background: rgba(255,255,255,0.95) !important;
    }
    
    /* Fallback para navegadores sin soporte nativo */
    .sortable-fallback {
        opacity: 0.6 !important;
        background: #e3f2fd !important;
        border: 3px solid #2196f3 !important;
        transform: rotate(10deg) scale(0.85) !important;
        box-shadow: 0 25px 50px rgba(0,0,0,0.5) !important;
    }
    
    /* Modo drag activo */
    .sort-mode .image-item {
        cursor: grab;
        transition: all 0.3s ease;
    }
    
    .sort-mode .image-item:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
    
    .sort-mode .image-item:active {
        cursor: grabbing;
    }
    
    /* Feedback visual mejorado */
    .sort-mode .orden-badge {
        background: linear-gradient(45deg, #28a745, #20c997) !important;
        animation: pulse-green 2s infinite;
        box-shadow: 0 0 15px rgba(40, 167, 69, 0.5);
    }
    
    @keyframes pulse-green {
        0%, 100% { 
            transform: scale(1);
            box-shadow: 0 0 10px rgba(40, 167, 69, 0.5);
        }
        50% { 
            transform: scale(1.1);
            box-shadow: 0 0 20px rgba(40, 167, 69, 0.8);
        }
    }
    
    /* Indicador de que se puede arrastrar */
    .sort-mode .image-card::before {
        content: "✋ Arrastra";
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(0, 123, 255, 0.9);
        color: white;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
        opacity: 0;
        transition: opacity 0.3s ease;
        z-index: 100;
        pointer-events: none;
    }
    
    .sort-mode .image-card:hover::before {
        opacity: 1;
    }
</style>
@endsection

@push('scripts')
<!-- SortableJS Library -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>

// Variables globales
let sortMode = false;
let sortableInstance = null;

// Endpoints for gallery AJAX operations
const uploadUrl = '{{ route('admin.galleries.images.upload', $gallery) }}';
const updateOrderUrl = '{{ route('admin.galleries.images.update-order', $gallery) }}';
const deleteImageBaseUrl = '{{ url('admin/galleries/' . $gallery->id . '/images') }}';
const imageTextsBaseUrl = '{{ url('admin/gallery-images') }}';

// Función principal para probar la conexión
function testConnection() {
    console.log('🧪 Probando conexión al servidor...');
    
    fetch(updateOrderUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ 
            updates: [
                { id: 1, orden: 1 }
            ]
        })
    })
    .then(response => {
        console.log('📡 Status:', response.status);
        return response.text();
    })
    .then(text => {
        console.log('📦 Respuesta:', text);
        alert('Conexión OK: ' + text);
    })
    .catch(error => {
        console.error('❌ Error:', error);
        alert('Error: ' + error);
    });
}

// Función simple para cambiar orden
function cambiarOrden(imageId, nuevoOrden) {
    console.log(`� Cambiando orden: Imagen ${imageId} → ${nuevoOrden}`);
    
    if (!nuevoOrden || nuevoOrden < 1) {
        alert('❌ El orden debe ser mayor a 0');
        return;
    }
    
    // Mostrar que está procesando
    const input = document.querySelector(`input[data-image-id="${imageId}"]`);
    if (input) {
        input.style.backgroundColor = '#fff3cd';
        input.disabled = true;
    }
    
    // Enviar al servidor
    fetch(updateOrderUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ 
            updates: [
                { 
                    id: parseInt(imageId), 
                    orden: parseInt(nuevoOrden) 
                }
            ]
        })
    })
    .then(response => {
        console.log('📡 Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('📦 Datos recibidos:', data);
        
        if (data.success) {
            alert('✅ ¡Orden actualizado exitosamente!');
            // Recargar página para ver cambios
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            alert('❌ Error del servidor: ' + (data.message || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('❌ Error completo:', error);
        alert('❌ Error de conexión: ' + error.message);
    })
    .finally(() => {
        // Restaurar input
        if (input) {
            input.style.backgroundColor = '';
            input.disabled = false;
        }
    });
}

// ====== NUEVAS FUNCIONES PARA DRAG & DROP ======

// Función para inicializar el drag & drop
function initializeDragAndDrop() {
    const container = document.querySelector('.sortable-container');
    
    if (!container) {
        console.error('❌ No se encontró el contenedor de imágenes');
        return;
    }
    
    console.log('🎯 Inicializando Drag & Drop...');
    
    sortableInstance = Sortable.create(container, {
        animation: 200,
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        dragClass: 'sortable-drag',
        forceFallback: false,
        fallbackClass: 'sortable-fallback',
        
        onStart: function(evt) {
            console.log('🚀 Drag iniciado:', evt.oldIndex);
            // Se añaden estilos automáticamente con las clases CSS
        },
        
        onEnd: function(evt) {
            console.log('🎯 Drag terminado:', evt.oldIndex, '→', evt.newIndex);
            
            // Si cambió de posición, actualizar orden
            if (evt.oldIndex !== evt.newIndex) {
                updateOrderAfterDrag();
            }
        },
        
        onMove: function(evt) {
            // Añadir efecto visual al elemento sobre el que se arrastra
            const related = evt.related;
            if (related) {
                related.style.transform = 'scale(1.05)';
                related.style.background = 'rgba(40, 167, 69, 0.1)';
                
                setTimeout(() => {
                    related.style.transform = '';
                    related.style.background = '';
                }, 300);
            }
        }
    });
    
    console.log('✅ Drag & Drop inicializado correctamente');
}

// Función para actualizar el orden después del drag & drop
function updateOrderAfterDrag() {
    console.log('🔄 Actualizando orden después del drag...');
    
    const items = document.querySelectorAll('.image-item');
    const updates = [];
    
    items.forEach((item, index) => {
        const imageId = item.getAttribute('data-id');
        const newOrder = index + 1;
        
        // Actualizar el campo de orden en el DOM
        const orderField = item.querySelector('.orden-field');
        const orderBadge = item.querySelector('.orden-number');
        
        if (orderField) orderField.value = newOrder;
        if (orderBadge) orderBadge.textContent = newOrder;
        
        updates.push({
            id: parseInt(imageId),
            orden: newOrder
        });
        
        console.log(`📝 Imagen ${imageId} → Orden ${newOrder}`);
    });
    
    // Enviar actualización al servidor
    sendBulkOrderUpdate(updates);
}

// Función para enviar actualizaciones masivas de orden
function sendBulkOrderUpdate(updates) {
    console.log('📡 Enviando actualización masiva:', updates);
    
    // Mostrar indicador visual
    const container = document.querySelector('.sortable-container');
    container.style.opacity = '0.7';
    
    fetch(updateOrderUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ updates: updates })
    })
    .then(response => {
        console.log('📡 Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('📦 Datos recibidos:', data);
        
        if (data.success) {
            console.log('✅ ¡Orden actualizado exitosamente!');
            
            // Feedback visual de éxito
            container.style.background = 'rgba(40, 167, 69, 0.1)';
            setTimeout(() => {
                container.style.background = '';
            }, 1000);
            
            // Actualizar contadores de orden
            updateOrderDisplays();
        } else {
            alert('❌ Error del servidor: ' + (data.message || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('❌ Error completo:', error);
        alert('❌ Error de conexión: ' + error.message);
    })
    .finally(() => {
        container.style.opacity = '';
    });
}

// Función para actualizar los displays de orden
function updateOrderDisplays() {
    const items = document.querySelectorAll('.image-item');
    items.forEach((item, index) => {
        const orderDisplay = item.querySelector('.orden-display');
        if (orderDisplay) {
            const newOrder = index + 1;
            orderDisplay.innerHTML = `Orden: ${newOrder}` + 
                (index === 0 ? ' <span class="badge bg-success ms-2">Portada</span>' : '');
        }
    });
}

// Función para guardar orden actual al salir del modo
function saveCurrentOrder() {
    console.log('💾 Guardando orden actual...');
    
    const items = document.querySelectorAll('.image-item');
    const updates = [];
    
    items.forEach((item, index) => {
        const imageId = item.getAttribute('data-id');
        const orderField = item.querySelector('.orden-field');
        const currentOrder = orderField ? parseInt(orderField.value) : index + 1;
        
        updates.push({
            id: parseInt(imageId),
            orden: currentOrder
        });
    });
    
    // Solo enviar si hay cambios
    if (updates.length > 0) {
        sendBulkOrderUpdate(updates);
    }
}

// Configurar eventos cuando carga la página
document.addEventListener('DOMContentLoaded', function() {
    console.log('📋 Configurando eventos...');
    
    // Configurar botón de modo ordenar
    const sortButton = document.getElementById('toggle-sort-btn');
    if (sortButton) {
        sortButton.addEventListener('click', toggleSortMode);
        console.log('🔘 Botón de ordenar configurado');
    } else {
        console.error('❌ No se encontró el botón de ordenar');
    }
    
    // Encontrar todos los campos de orden
    const orderFields = document.querySelectorAll('.orden-field');
    console.log(`🔍 Encontrados ${orderFields.length} campos de orden`);
    
    orderFields.forEach((field, index) => {
        console.log(`🎯 Configurando campo ${index + 1}:`, field);
        
        // Evento cuando cambia el valor
        field.addEventListener('change', function() {
            const imageId = this.getAttribute('data-image-id');
            const newOrder = this.value;
            console.log(`🔄 Campo cambió: Imagen ${imageId}, nuevo orden: ${newOrder}`);
            cambiarOrden(imageId, newOrder);
        });
        
        // Evento cuando presiona Enter
        field.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                console.log('⏎ Enter presionado');
                e.preventDefault();
                this.blur(); // Esto dispara el evento change
            }
        });
        
        // Evento de clic en las flechas del input number
        field.addEventListener('input', function() {
            console.log('🔢 Input cambió:', this.value);
            // Opcional: cambiar automáticamente sin esperar a blur
        });
    });
    
    console.log('✅ Eventos configurados correctamente');
});

// Función para toggle del modo ordenar (simplificado)
function toggleSortMode() {
    sortMode = !sortMode;
    console.log('� Modo ordenar:', sortMode ? 'ACTIVADO' : 'DESACTIVADO');
    
    const container = document.querySelector('.sortable-container');
    const instructions = document.getElementById('sort-instructions');
    const sortIcon = document.getElementById('sort-icon');
    const sortText = document.getElementById('sort-text');
    
    if (sortMode) {
        // ACTIVAR MODO ORDENAR
        container.classList.add('sort-mode');
        instructions.style.display = 'block';
        sortIcon.className = 'fas fa-save';
        sortText.textContent = 'Guardar Orden';
        
        // Inicializar Drag & Drop con SortableJS
        initializeDragAndDrop();
        
        console.log('🎯 MODO ORDENAR ACTIVADO - Drag & Drop habilitado');
    } else {
        // DESACTIVAR MODO ORDENAR
        container.classList.remove('sort-mode');
        instructions.style.display = 'none';
        sortIcon.className = 'fas fa-sort';
        sortText.textContent = 'Modo Ordenar';
        
        // Destruir instancia de drag & drop
        if (sortableInstance) {
            sortableInstance.destroy();
            sortableInstance = null;
            console.log('🗑️ Drag & Drop desactivado');
        }
        
        // Guardar orden final
        saveCurrentOrder();
        
        console.log('💾 Orden guardado y modo desactivado');
    }
}

// Upload de imágenes (simplificado)
document.getElementById('upload-form').addEventListener('submit', function(e) {
    e.preventDefault();
    console.log('📤 Subiendo imágenes...');
    
    const formData = new FormData(this);
    const uploadBtn = document.getElementById('upload-btn');
    
    uploadBtn.disabled = true;
    uploadBtn.textContent = 'Subiendo...';
    
    fetch(uploadUrl, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            window.location.reload();
        } else {
            alert('❌ ' + (data.message || 'Error al subir'));
        }
    })
    .catch(error => {
        alert('❌ Error: ' + error.message);
    })
    .finally(() => {
        uploadBtn.disabled = false;
        uploadBtn.textContent = 'Subir Imágenes';
    });
});

function deleteImage(imageId) {
    if (confirm('¿Estás seguro de que quieres eliminar esta imagen?\n\nEsta acción no se puede deshacer.')) {
        console.log('🗑️ Eliminando imagen ID:', imageId);
        
        // Mostrar indicador de carga
        const imageElement = document.querySelector(`[data-id="${imageId}"]`);
        if (imageElement) {
            imageElement.style.opacity = '0.5';
            imageElement.style.pointerEvents = 'none';
        }
        
    fetch(`${deleteImageBaseUrl}/${imageId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('📡 Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('📦 Datos recibidos:', data);
            
            if (data.success) {
                console.log('✅ Imagen eliminada exitosamente');
                
                // Animar y eliminar el elemento
                if (imageElement) {
                    imageElement.style.transform = 'scale(0)';
                    imageElement.style.transition = 'all 0.3s ease';
                    
                    setTimeout(() => {
                        imageElement.remove();
                        // Actualizar contadores y orden
                        updateOrderDisplays();
                    }, 300);
                }
                
                // Mostrar mensaje de éxito
                console.log('✅ ' + data.message);
            } else {
                console.error('❌ Error del servidor:', data.message);
                
                // Restaurar elemento si hay error
                if (imageElement) {
                    imageElement.style.opacity = '';
                    imageElement.style.pointerEvents = '';
                }
            }
        })
        .catch(error => {
            console.error('❌ Error completo:', error);
            
            // Restaurar elemento si hay error
            if (imageElement) {
                imageElement.style.opacity = '';
                imageElement.style.pointerEvents = '';
            }
        });
    }
}

// Función de prueba que puedes llamar desde la consola del navegador
window.testGallery = function() {
    console.log('🧪 Ejecutando pruebas...');
    testConnection();
};

// === FUNCIONES PARA EDICIÓN MULTIIDIOMA ===

/**
 * Función de prueba para abrir el modal directamente
 */
function testModal() {
    console.log('🧪 Probando modal...');
    
    // Verificar que el modal existe
    const modalElement = document.getElementById('editImageTextsModal');
    if (!modalElement) {
        alert('❌ Error: No se encontró el modal');
        return;
    }
    
    // Mostrar información de debug
    const tabsContainer = document.getElementById('languageTabsTexts');
    const tabs = tabsContainer ? tabsContainer.querySelectorAll('.nav-link') : [];
    console.log('📋 Pestañas encontradas:', tabs.length);
    
    if (tabs.length === 0) {
        alert('⚠️ No hay idiomas configurados');
        return;
    }
    
    // Llenar algunos datos de prueba
    document.getElementById('imageId').value = 1;
    document.getElementById('modalImagePreview').src = '/storage/images/galeria/imagen/content_1_16nov08_1762514696_4WBGr5.jpg';
    
    // Mostrar modal
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
    
    console.log('✅ Modal mostrado');
}

/**
 * Abrir modal para editar textos multiidioma de una imagen
 */
function editImageTexts(imageId) {
    console.log('🌐 Editando textos multiidioma para imagen:', imageId);
    
    // Verificar que el modal existe
    const modalElement = document.getElementById('editImageTextsModal');
    if (!modalElement) {
        alert('❌ Error: No se encontró el modal editImageTextsModal');
        return;
    }
    
    // Verificar que existen pestañas de idiomas
    const tabsContainer = document.getElementById('languageTabsTexts');
    const tabs = tabsContainer ? tabsContainer.querySelectorAll('.nav-link') : [];
    console.log('📋 Pestañas encontradas:', tabs.length);
    
    if (tabs.length === 0) {
        alert('⚠️ No hay idiomas configurados. Ve a Admin > Idiomas para configurar idiomas primero.');
        return;
    }
    
    // Obtener datos de la imagen
    fetch(`${imageTextsBaseUrl}/${imageId}/texts`)
        .then(response => {
            console.log('📡 Respuesta recibida:', response.status);
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Establecer ID de imagen
                document.getElementById('imageId').value = imageId;
                
                // Mostrar preview de la imagen
                const preview = document.getElementById('modalImagePreview');
                preview.src = data.image.imagen_url;
                preview.alt = data.image.alt_text || 'Imagen de galería';
                
                // Llenar campos por idioma
                data.texts.forEach(text => {
                    const tituloField = document.getElementById(`titulo_${text.idioma_id}`);
                    const descripcionField = document.getElementById(`descripcion_${text.idioma_id}`);
                    const altField = document.getElementById(`alt_text_${text.idioma_id}`);
                    
                    if (tituloField) tituloField.value = text.titulo || '';
                    if (descripcionField) descripcionField.value = text.descripcion || '';
                    if (altField) altField.value = text.alt_text || '';
                });
                
                // Mostrar modal
                const modal = new bootstrap.Modal(document.getElementById('editImageTextsModal'));
                modal.show();
            } else {
                alert('❌ Error al cargar datos: ' + data.message);
            }
        })
        .catch(error => {
            console.error('❌ Error:', error);
            alert('❌ Error al cargar textos de la imagen');
        });
}

/**
 * Guardar textos multiidioma de una imagen
 */
function saveImageTexts() {
    const imageId = document.getElementById('imageId').value;
    const formData = new FormData(document.getElementById('imageTextsForm'));
    
    console.log('💾 Guardando textos multiidioma para imagen:', imageId);
    
    fetch(`${imageTextsBaseUrl}/${imageId}/texts`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('✅ Textos guardados exitosamente');
            
            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('editImageTextsModal'));
            modal.hide();
            
            // Mostrar mensaje de éxito
            alert('✅ Textos guardados exitosamente');
            
            // Opcional: Recargar la página para ver los cambios
            // window.location.reload();
        } else {
            alert('❌ Error al guardar: ' + data.message);
        }
    })
    .catch(error => {
        console.error('❌ Error:', error);
        alert('❌ Error al guardar textos');
    });
}

console.log('💡 TIP: Puedes ejecutar testGallery() en la consola del navegador para probar la conexión');
</script>
@endpush