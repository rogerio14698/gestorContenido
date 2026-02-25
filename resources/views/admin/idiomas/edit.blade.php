@extends('admin.layouts.app')

@section('title', 'Editar Idioma - Eunomia CMS')

@section('page-title', 'Editar Idioma: ' . $idioma->nombre)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.idiomas.index') }}">Idiomas</a></li>
    <li class="breadcrumb-item active">Editar</li>
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
                            Editar Información del Idioma
                        </h3>
                        <div class="card-tools">
                            @if($idioma->es_principal)
                                <span class="badge badge-primary">
                                    <i class="fas fa-star me-1"></i>Principal
                                </span>
                            @endif
                            @if($idioma->activo)
                                <span class="badge badge-success">Activo</span>
                            @else
                                <span class="badge badge-secondary">Inactivo</span>
                            @endif
                        </div>
                    </div>
                    <form action="{{ route('admin.idiomas.update', $idioma) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
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
                                       value="{{ old('nombre', $idioma->nombre) }}" 
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
                                       value="{{ old('etiqueta', $idioma->etiqueta) }}" 
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

                            <!-- Imagen/Bandera actual y nueva -->
                            <div class="form-group mb-3">
                                <label class="form-label">
                                    <i class="fas fa-flag me-1"></i>
                                    Imagen/Bandera del Idioma
                                </label>

                                <!-- Mostrar imagen actual -->
                                @if($idioma->imagen_url)
                                    <div class="mb-3 p-3 bg-light rounded">
                                        <h6>Imagen actual:</h6>
                                        <img src="{{ $idioma->imagen_url }}" 
                                             alt="{{ $idioma->nombre }}" 
                                             class="img-thumbnail mb-2" 
                                             style="max-width: 200px; max-height: 150px;">
                                        <br>
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="eliminar_imagen" 
                                                   name="eliminar_imagen" 
                                                   value="1">
                                            <label class="form-check-label text-danger" for="eliminar_imagen">
                                                <i class="fas fa-trash me-1"></i>
                                                Eliminar imagen actual
                                            </label>
                                        </div>
                                    </div>
                                @endif

                                <!-- Subir nueva imagen -->
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
                                    {{ $idioma->imagen_url ? 'Subir nueva imagen (reemplazará la actual)' : 'Subir imagen del idioma' }}. 
                                    Formatos: JPEG, JPG, PNG, GIF, SVG, WebP. Máximo: 2MB
                                </small>
                                
                                <!-- Vista previa de nueva imagen -->
                                <div id="image-preview" class="mt-3" style="display: none;">
                                    <h6>Vista previa de nueva imagen:</h6>
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
                                           value="{{ old('orden', $idioma->orden) }}" 
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
                                               {{ old('activo', $idioma->activo) ? 'checked' : '' }}>
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
                                               {{ old('es_principal', $idioma->es_principal) ? 'checked' : '' }}>
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
                                        Actualizar Idioma
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Información adicional -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="fas fa-info-circle me-2"></i>
                            Información Adicional
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Código HTML:</strong>
                                <code>lang="{{ $idioma->codigo_html }}"</code>
                            </div>
                            <div class="col-md-6">
                                <strong>Creado:</strong>
                                {{ $idioma->created_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                        @if($idioma->updated_at != $idioma->created_at)
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <strong>Última modificación:</strong>
                                    {{ $idioma->updated_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        @endif
                    </div>
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
console.log('✏️ Script de edición de idioma cargado');

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
            
            // Desmarcar eliminar imagen actual si se selecciona nueva
            const eliminarCheck = document.getElementById('eliminar_imagen');
            if (eliminarCheck) {
                eliminarCheck.checked = false;
            }
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

// Manejar checkbox de eliminar imagen
const eliminarImagenCheck = document.getElementById('eliminar_imagen');
if (eliminarImagenCheck) {
    eliminarImagenCheck.addEventListener('change', function() {
        if (this.checked) {
            // Si se marca eliminar, limpiar vista previa de nueva imagen
            clearImagePreview();
        }
    });
}

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
    console.log('✅ Formulario de edición listo');
});
</script>
@endpush