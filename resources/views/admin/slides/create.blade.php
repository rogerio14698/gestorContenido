@extends('admin.layouts.app')

@section('title', 'Crear Slide')
@section('page-title', 'Crear Slide')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.slides.index') }}">Slides</a></li>
    <li class="breadcrumb-item active">Crear</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-plus"></i> Crear Nuevo Slide
                        </h3>
                    </div>
                    <form action="{{ route('admin.slides.store') }}" method="POST" enctype="multipart/form-data" id="slideForm">
                        @csrf
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
                                                                   value="{{ old('translations.'.$idioma->id.'.titulo') }}" 
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
                                                                      placeholder="Descripción opcional del slide en {{ $idioma->nombre }}">{{ old('translations.'.$idioma->id.'.descripcion') }}</textarea>
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
                                                                   value="{{ old('translations.'.$idioma->id.'.alt_text') }}" 
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
                                                                   value="{{ old('translations.'.$idioma->id.'.url') }}" 
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
                                    <!-- Imagen -->
                                    <div class="card mb-3">
                                        <div class="card-header">
                                            <h5 class="card-title">
                                                <i class="fas fa-image"></i> Imagen del Slide
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="imagen" class="form-label">Seleccionar Imagen</label>
                                                <input type="file" 
                                                       class="form-control @error('imagen') is-invalid @enderror" 
                                                       id="imagen" 
                                                       name="imagen" 
                                                       accept="image/*">
                                                @error('imagen')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">
                                                    Formatos permitidos: JPG, PNG, GIF, WebP. Tamaño máximo: 10MB.
                                                </small>
                                            </div>

                                            <!-- Preview de imagen -->
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
                                                           {{ old('visible', true) ? 'checked' : '' }}>
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
                                                           {{ old('activo', true) ? 'checked' : '' }}>
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
                                                           {{ old('nueva_ventana') ? 'checked' : '' }}>
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
                                                <strong>Orden:</strong> Se asignará automáticamente al final de la lista.
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
                                    <i class="fas fa-save"></i> Crear Slide
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-slides.css') }}">
<style>
.nav-tabs .nav-link {
    border-radius: 0.375rem 0.375rem 0 0;
}
.nav-tabs .nav-link.active {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
}
.custom-control-input:checked ~ .custom-control-label::before {
    background-color: #007bff;
    border-color: #007bff;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Preview de imagen
    $('#imagen').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#previewImage').attr('src', e.target.result);
                $('#imagePreview').show();
            };
            reader.readAsDataURL(file);
        } else {
            $('#imagePreview').hide();
        }
    });

    // Validación del formulario
    $('#slideForm').submit(function(e) {
        let isValid = true;
        
        // Verificar que al menos un título esté lleno
        $('.tab-pane').each(function() {
            const titulo = $(this).find('input[name*="[titulo]"]');
            if (titulo.val().trim() === '') {
                isValid = false;
                titulo.addClass('is-invalid');
            } else {
                titulo.removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Por favor, completa al menos el título en todos los idiomas.');
        }
    });
    
    console.log('✅ Formulario de creación de slides inicializado');
});
</script>
@endpush