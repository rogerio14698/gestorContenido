@extends('admin.layouts.app')

@section('title', 'Editar Slide')
@section('page-title', 'Editar Slide')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.slides.index') }}">Slides</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-edit"></i> Editar Slide
                        </h3>
                    </div>
                    <form action="{{ route('admin.slides.update', $slide) }}" method="POST" enctype="multipart/form-data" id="slideForm">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="row">
                                <!-- Panel de traducciones -->
                                <div class="col-lg-8">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="card-title">
                                                <i class="fas fa-language"></i> Contenido por Idioma
                                            </h4>
                                        </div>
                                        <div class="card-body">
                                            <!-- Tabs para idiomas -->
                                            <ul class="nav nav-tabs" id="languageTabs" role="tablist">
                                                @foreach($idiomas as $index => $idioma)
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                                                id="tab-{{ $idioma->etiqueta }}" 
                                                                data-bs-toggle="tab" 
                                                                data-bs-target="#content-{{ $idioma->etiqueta }}" 
                                                                type="button" role="tab">
                                                            <i class="fas fa-flag"></i> {{ $idioma->nombre }}
                                                            @if($idioma->es_principal)
                                                                <span class="badge badge-primary ms-1">Principal</span>
                                                            @endif
                                                        </button>
                                                    </li>
                                                @endforeach
                                            </ul>

                                            <!-- Contenido de tabs -->
                                            <div class="tab-content mt-3" id="languageTabsContent">
                                                @foreach($idiomas as $index => $idioma)
                                                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                                         id="content-{{ $idioma->etiqueta }}" 
                                                         role="tabpanel">
                                                        
                                                        <!-- Título -->
                                                        <div class="form-group mb-3">
                                                            <label for="translations_{{ $idioma->id }}_titulo" class="form-label">
                                                                Título en {{ $idioma->nombre }} <span class="text-danger">*</span>
                                                            </label>
                                                            <input type="text" 
                                                                   class="form-control @error('translations.'.$idioma->id.'.titulo') is-invalid @enderror" 
                                                                   id="translations_{{ $idioma->id }}_titulo" 
                                                                   name="translations[{{ $idioma->id }}][titulo]" 
                                                                   value="{{ old('translations.'.$idioma->id.'.titulo', $translations[$idioma->id]['titulo'] ?? '') }}" 
                                                                   placeholder="Título del slide en {{ $idioma->nombre }}" 
                                                                   required>
                                                            @error('translations.'.$idioma->id.'.titulo')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <!-- Descripción -->
                                                        <div class="form-group mb-3">
                                                            <label for="translations_{{ $idioma->id }}_descripcion" class="form-label">
                                                                Descripción en {{ $idioma->nombre }}
                                                            </label>
                                                            <textarea class="form-control @error('translations.'.$idioma->id.'.descripcion') is-invalid @enderror" 
                                                                      id="translations_{{ $idioma->id }}_descripcion" 
                                                                      name="translations[{{ $idioma->id }}][descripcion]" 
                                                                      rows="4" 
                                                                      placeholder="Descripción opcional del slide en {{ $idioma->nombre }}">{{ old('translations.'.$idioma->id.'.descripcion', $translations[$idioma->id]['descripcion'] ?? '') }}</textarea>
                                                            @error('translations.'.$idioma->id.'.descripcion')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <!-- Texto Alternativo -->
                                                        <div class="form-group mb-3">
                                                            <label for="translations_{{ $idioma->id }}_alt_text" class="form-label">
                                                                Texto Alternativo en {{ $idioma->nombre }}
                                                            </label>
                                                            <input type="text" 
                                                                   class="form-control @error('translations.'.$idioma->id.'.alt_text') is-invalid @enderror" 
                                                                   id="translations_{{ $idioma->id }}_alt_text" 
                                                                   name="translations[{{ $idioma->id }}][alt_text]" 
                                                                   value="{{ old('translations.'.$idioma->id.'.alt_text', $translations[$idioma->id]['alt_text'] ?? '') }}" 
                                                                   placeholder="Texto alternativo para accesibilidad">
                                                            @error('translations.'.$idioma->id.'.alt_text')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                            <small class="form-text text-muted">
                                                                Importante para accesibilidad web (recomendado).
                                                            </small>
                                                        </div>

                                                        <!-- URL -->
                                                        <div class="form-group mb-3">
                                                            <label for="translations_{{ $idioma->id }}_url" class="form-label">
                                                                URL de Enlace en {{ $idioma->nombre }}
                                                            </label>
                                                            <input type="url" 
                                                                   class="form-control @error('translations.'.$idioma->id.'.url') is-invalid @enderror" 
                                                                   id="translations_{{ $idioma->id }}_url" 
                                                                   name="translations[{{ $idioma->id }}][url]" 
                                                                   value="{{ old('translations.'.$idioma->id.'.url', $translations[$idioma->id]['url'] ?? '') }}" 
                                                                   placeholder="https://ejemplo.com">
                                                            @error('translations.'.$idioma->id.'.url')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                            <small class="form-text text-muted">
                                                                URL a la que dirigir cuando se haga clic (opcional).
                                                            </small>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Panel de configuración -->
                                <div class="col-lg-4">
                                    <!-- Imagen actual -->
                                    @if($slide->hasImage())
                                        <div class="card mb-3">
                                            <div class="card-header">
                                                <h5 class="card-title">
                                                    <i class="fas fa-image"></i> Imagen Actual
                                                </h5>
                                            </div>
                                            <div class="card-body text-center">
                                                <img src="{{ $slide->miniatura_url }}" class="img-fluid rounded mb-2" 
                                                     alt="{{ $slide->currentTranslation()->alt_text ?? 'Slide actual' }}"
                                                     style="max-height: 150px;">
                                                <p class="text-muted small">Imagen actual del slide</p>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Nueva imagen -->
                                    <div class="card mb-3">
                                        <div class="card-header">
                                            <h5 class="card-title">
                                                <i class="fas fa-image"></i> {{ $slide->hasImage() ? 'Cambiar' : 'Agregar' }} Imagen
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="imagen" class="form-label">Seleccionar Nueva Imagen</label>
                                                <input type="file" 
                                                       class="form-control @error('imagen') is-invalid @enderror" 
                                                       id="imagen" 
                                                       name="imagen" 
                                                       accept="image/*">
                                                @error('imagen')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">
                                                    {{ $slide->hasImage() ? 'Deja vacío para mantener la imagen actual.' : '' }}
                                                    Formatos permitidos: JPG, PNG, GIF, WebP. Tamaño máximo: 10MB.
                                                </small>
                                            </div>

                                            <!-- Preview de nueva imagen -->
                                            <div id="imagePreview" class="mt-3" style="display: none;">
                                                <img id="previewImage" class="img-fluid rounded" alt="Preview">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Configuración -->
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title">
                                                <i class="fas fa-cog"></i> Configuración
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <!-- Visible -->
                                            <div class="form-group mb-3">
                                                <!-- Hidden field para asegurar que se envíe un valor -->
                                                <input type="hidden" name="visible" value="0">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" 
                                                           class="custom-control-input" 
                                                           id="visible" 
                                                           name="visible" 
                                                           value="1" 
                                                           {{ old('visible', $slide->visible) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="visible">
                                                        <strong>Visible</strong>
                                                    </label>
                                                </div>
                                                <small class="form-text text-muted">
                                                    Determina si el slide se muestra en el sitio web.
                                                </small>
                                            </div>

                                            <!-- Activo -->
                                            <div class="form-group mb-3">
                                                <!-- Hidden field para asegurar que se envíe un valor -->
                                                <input type="hidden" name="activo" value="0">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" 
                                                           class="custom-control-input" 
                                                           id="activo" 
                                                           name="activo" 
                                                           value="1" 
                                                           {{ old('activo', $slide->activo) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="activo">
                                                        <strong>Activo</strong>
                                                    </label>
                                                </div>
                                                <small class="form-text text-muted">
                                                    Habilita o deshabilita completamente el slide.
                                                </small>
                                            </div>

                                            <!-- Nueva Ventana -->
                                            <div class="form-group mb-3">
                                                <!-- Hidden field para asegurar que se envíe un valor -->
                                                <input type="hidden" name="nueva_ventana" value="0">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" 
                                                           class="custom-control-input" 
                                                           id="nueva_ventana" 
                                                           name="nueva_ventana" 
                                                           value="1" 
                                                           {{ old('nueva_ventana', $slide->nueva_ventana) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="nueva_ventana">
                                                        <strong>Abrir en Nueva Ventana</strong>
                                                    </label>
                                                </div>
                                                <small class="form-text text-muted">
                                                    Si tiene URL, abrirá en una nueva pestaña/ventana.
                                                </small>
                                            </div>

                                            <!-- Información de orden -->
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle"></i>
                                                <strong>Orden:</strong> {{ $slide->orden }}
                                                <br><small>Puedes cambiar el orden desde la lista principal.</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.slides.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Actualizar Slide
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Preview de imagen
    const imagenInput = document.getElementById('imagen');
    const imagePreview = document.getElementById('imagePreview');
    const previewImage = document.getElementById('previewImage');

    if (imagenInput) {
        imagenInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    imagePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                imagePreview.style.display = 'none';
            }
        });
    }

    // Validación del formulario
    const form = document.getElementById('slideForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Verificar que al menos un título esté rellenado
            let hayTitulo = false;
            document.querySelectorAll('input[name*="[titulo]"]').forEach(input => {
                if (input.value.trim() !== '') {
                    hayTitulo = true;
                }
            });

            if (!hayTitulo) {
                e.preventDefault();
                alert('Debe completar al menos un título.');
                return false;
            }
        });
    }
});
</script>
@endsection