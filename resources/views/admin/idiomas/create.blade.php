@extends('admin.layouts.app')

@section('title', 'Crear Idioma - Eunomia CMS')

@section('page-title', 'Crear Nuevo Idioma')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.idiomas.index') }}">Idiomas</a></li>
    <li class="breadcrumb-item active">Crear</li>
@endsection

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8 col-md-10 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-language me-2"></i>
                            Información del Idioma
                        </h3>
                    </div>
                    <form action="{{ route('admin.idiomas.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            
                            <!-- Nombre del idioma -->
                            <div class="form-group mb-3">
                                <label for="nombre" class="form-label">
                                    <i class="fas fa-globe me-1"></i>
                                    Nombre del Idioma <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('nombre') is-invalid @enderror" 
                                       id="nombre" 
                                       name="nombre" 
                                       value="{{ old('nombre') }}" 
                                       placeholder="Ej: Español, English, Asturiano"
                                       required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Nombre completo del idioma que se mostrará a los usuarios
                                </small>
                            </div>

                            <!-- Etiqueta del idioma -->
                            <div class="form-group mb-3">
                                <label for="etiqueta" class="form-label">
                                    <i class="fas fa-code me-1"></i>
                                    Etiqueta del Idioma <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('etiqueta') is-invalid @enderror" 
                                       id="etiqueta" 
                                       name="etiqueta" 
                                       value="{{ old('etiqueta') }}" 
                                       placeholder="Ej: es, en, ast"
                                       pattern="[a-zA-Z][a-zA-Z\-_]*"
                                       maxlength="10"
                                       required>
                                @error('etiqueta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Código usado en el atributo <code>lang</code> del HTML. Debe comenzar con letra y puede contener letras, guiones y guiones bajos.
                                </small>
                            </div>

                            <!-- Imagen/Bandera -->
                            <div class="form-group mb-3">
                                <label for="imagen" class="form-label">
                                    <i class="fas fa-flag me-1"></i>
                                    Imagen/Bandera del Idioma
                                </label>
                                <div class="input-group">
                                    <input type="file" 
                                           class="form-control @error('imagen') is-invalid @enderror" 
                                           id="imagen" 
                                           name="imagen" 
                                           accept="image/*">
                                    <button type="button" 
                                            class="btn btn-outline-secondary" 
                                            onclick="previewImage()"
                                            title="Vista previa">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('imagen')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Formatos soportados: JPEG, JPG, PNG, GIF, SVG, WebP. Tamaño máximo: 2MB
                                </small>
                                
                                <!-- Vista previa de imagen -->
                                <div id="image-preview" class="mt-3" style="display: none;">
                                    <img id="preview-img" 
                                         src="" 
                                         alt="Vista previa" 
                                         class="img-thumbnail" 
                                         style="max-width: 200px; max-height: 150px;">
                                    <button type="button" 
                                            class="btn btn-sm btn-danger ms-2" 
                                            onclick="clearImagePreview()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Orden -->
                                <div class="col-md-4 mb-3">
                                    <label for="orden" class="form-label">
                                        <i class="fas fa-sort-numeric-up me-1"></i>
                                        Orden de Visualización
                                    </label>
                                    <input type="number" 
                                           class="form-control @error('orden') is-invalid @enderror" 
                                           id="orden" 
                                           name="orden" 
                                           value="{{ old('orden', 0) }}" 
                                           min="0"
                                           step="1">
                                    @error('orden')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Menor número = mayor prioridad
                                    </small>
                                </div>

                                <!-- Estado activo -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-toggle-on me-1"></i>
                                        Estado del Idioma
                                    </label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="activo" 
                                               name="activo" 
                                               value="1"
                                               {{ old('activo', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="activo">
                                            Idioma activo y disponible
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Solo los idiomas activos aparecerán en el sitio web
                                    </small>
                                </div>

                                <!-- Idioma principal -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-star me-1"></i>
                                        Idioma Principal
                                    </label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="es_principal" 
                                               name="es_principal" 
                                               value="1"
                                               {{ old('es_principal', false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="es_principal">
                                            Establecer como idioma por defecto
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Solo puede haber un idioma principal
                                    </small>
                                </div>
                            </div>

                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-6">
                                    <a href="{{ route('admin.idiomas.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-1"></i>
                                        Volver a la Lista
                                    </a>
                                </div>
                                <div class="col-6 text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>
                                        Crear Idioma
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
.form-check-input:checked {
    background-color: #28a745;
    border-color: #28a745;
}

.form-check-input:focus {
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

#image-preview {
    border: 2px dashed #dee2e6;
    border-radius: 0.375rem;
    padding: 15px;
    text-align: center;
    background-color: #f8f9fa;
}
</style>
@endpush

@push('scripts')
<script>
console.log('📝 Script de creación de idioma cargado');

// Vista previa de imagen
function previewImage() {
    const input = document.getElementById('imagen');
    const preview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Limpiar vista previa
function clearImagePreview() {
    const input = document.getElementById('imagen');
    const preview = document.getElementById('image-preview');
    
    input.value = '';
    preview.style.display = 'none';
}

// Auto-preview cuando se selecciona archivo
document.getElementById('imagen').addEventListener('change', function() {
    if (this.files && this.files[0]) {
        previewImage();
    } else {
        clearImagePreview();
    }
});

// Generar etiqueta automáticamente desde nombre
document.getElementById('nombre').addEventListener('input', function() {
    const etiquetaField = document.getElementById('etiqueta');
    
    // Solo si el campo etiqueta está vacío
    if (!etiquetaField.value.trim()) {
        let etiqueta = this.value.toLowerCase();
        
        // Mapeo básico de idiomas comunes
        const mapeo = {
            'español': 'es',
            'castellano': 'es',
            'english': 'en',
            'inglés': 'en',
            'ingles': 'en',
            'asturiano': 'ast',
            'bable': 'ast',
            'catalán': 'ca',
            'catalan': 'ca',
            'euskera': 'eu',
            'vasco': 'eu',
            'gallego': 'gl',
            'galego': 'gl',
            'português': 'pt',
            'portugues': 'pt',
            'français': 'fr',
            'frances': 'fr',
            'deutsch': 'de',
            'alemán': 'de',
            'italiano': 'it',
            'русский': 'ru',
            'ruso': 'ru'
        };
        
        if (mapeo[etiqueta]) {
            etiquetaField.value = mapeo[etiqueta];
        } else {
            // Generar etiqueta básica (primeras 2-3 letras)
            etiqueta = etiqueta
                .replace(/[^a-zA-Z]/g, '')
                .substring(0, 3);
            etiquetaField.value = etiqueta;
        }
    }
});

// Validación en tiempo real de la etiqueta
document.getElementById('etiqueta').addEventListener('input', function() {
    const valor = this.value;
    const regex = /^[a-zA-Z][a-zA-Z\-_]*$/;
    
    if (valor && !regex.test(valor)) {
        this.setCustomValidity('La etiqueta debe comenzar con una letra y solo puede contener letras, guiones y guiones bajos');
        this.classList.add('is-invalid');
    } else {
        this.setCustomValidity('');
        this.classList.remove('is-invalid');
    }
});

document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Formulario de creación listo');
});
</script>
@endpush