@extends('admin.layouts.app')

@section('title', 'Nueva Configuración de Imagen')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Nueva Configuración de Imagen</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.image-configs.index') }}">Configuraciones</a></li>
                        <li class="breadcrumb-item active">Nueva</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-image"></i> Crear Nueva Configuración
                    </h3>
                </div>

                <form action="{{ route('admin.image-configs.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tipo_contenido">Tipo de Contenido</label>
                                    <select name="tipo_contenido" id="tipo_contenido" 
                                            class="form-control @error('tipo_contenido') is-invalid @enderror" required>
                                        <option value="">Seleccionar tipo...</option>
                                        <option value="noticia" {{ old('tipo_contenido') == 'noticia' ? 'selected' : '' }}>Noticia</option>
                                        <option value="pagina" {{ old('tipo_contenido') == 'pagina' ? 'selected' : '' }}>Página</option>
                                        <option value="entrevista" {{ old('tipo_contenido') == 'entrevista' ? 'selected' : '' }}>Entrevista</option>
                                    </select>
                                    @error('tipo_contenido')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="tipo_imagen">Tipo de Imagen</label>
                                    <select name="tipo_imagen" id="tipo_imagen" 
                                            class="form-control @error('tipo_imagen') is-invalid @enderror" required>
                                        <option value="">Seleccionar tipo...</option>
                                        <option value="imagen" {{ old('tipo_imagen') == 'imagen' ? 'selected' : '' }}>Imagen de Contenido</option>
                                        <option value="imagen_portada" {{ old('tipo_imagen') == 'imagen_portada' ? 'selected' : '' }}>Imagen de Portada</option>
                                    </select>
                                    @error('tipo_imagen')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="formato">Formato de Imagen</label>
                                    <select name="formato" id="formato" 
                                            class="form-control @error('formato') is-invalid @enderror" required>
                                        <option value="jpg" {{ old('formato') == 'jpg' ? 'selected' : '' }}>JPG</option>
                                        <option value="png" {{ old('formato') == 'png' ? 'selected' : '' }}>PNG</option>
                                        <option value="webp" {{ old('formato') == 'webp' ? 'selected' : '' }}>WebP</option>
                                    </select>
                                    @error('formato')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="ancho">Ancho (px)</label>
                                            <input type="number" name="ancho" id="ancho" 
                                                   class="form-control @error('ancho') is-invalid @enderror"
                                                   value="{{ old('ancho', 800) }}" min="50" max="5000" required>
                                            @error('ancho')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="alto">Alto (px)</label>
                                            <input type="number" name="alto" id="alto" 
                                                   class="form-control @error('alto') is-invalid @enderror"
                                                   value="{{ old('alto', 600) }}" min="50" max="5000" required>
                                            @error('alto')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="calidad">Calidad (%)</label>
                                    <input type="range" name="calidad" id="calidad" 
                                           class="form-control-range" min="1" max="100" 
                                           value="{{ old('calidad', 85) }}" 
                                           oninput="document.getElementById('calidadValue').textContent = this.value + '%'">
                                    <small class="text-muted">Calidad actual: <span id="calidadValue">{{ old('calidad', 85) }}%</span></small>
                                    @error('calidad')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <h5><i class="fas fa-cogs"></i> Opciones Avanzadas</h5>
                                <hr>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" name="mantener_aspecto" id="mantener_aspecto" 
                                               class="custom-control-input" 
                                               {{ old('mantener_aspecto', true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="mantener_aspecto">
                                            Mantener proporción de aspecto
                                        </label>
                                    </div>
                                    <small class="text-muted">
                                        Conserva las proporciones originales de la imagen
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" name="redimensionar" id="redimensionar" 
                                               class="custom-control-input" 
                                               {{ old('redimensionar', true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="redimensionar">
                                            Habilitar redimensionado
                                        </label>
                                    </div>
                                    <small class="text-muted">
                                        Permite redimensionar automáticamente las imágenes
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" name="activo" id="activo" 
                                               class="custom-control-input" 
                                               {{ old('activo', true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="activo">
                                            Configuración activa
                                        </label>
                                    </div>
                                    <small class="text-muted">
                                        Solo configuraciones activas se aplican
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Información:</strong> Esta configuración se aplicará automáticamente cuando se suban imágenes del tipo especificado para el tipo de contenido seleccionado.
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Configuración
                        </button>
                        <a href="{{ route('admin.image-configs.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Presets para diferentes tipos
    const presets = {
        'noticia': {
            'imagen': { ancho: 800, alto: 600 },
            'imagen_portada': { ancho: 400, alto: 300 }
        },
        'pagina': {
            'imagen': { ancho: 1200, alto: 800 },
            'imagen_portada': { ancho: 600, alto: 400 }
        },
        'entrevista': {
            'imagen': { ancho: 800, alto: 600 },
            'imagen_portada': { ancho: 400, alto: 300 }
        }
    };

    // Auto-ajustar dimensiones según tipo
    $('#tipo_contenido, #tipo_imagen').on('change', function() {
        const tipoContenido = $('#tipo_contenido').val();
        const tipoImagen = $('#tipo_imagen').val();
        
        if (tipoContenido && tipoImagen && presets[tipoContenido] && presets[tipoContenido][tipoImagen]) {
            const preset = presets[tipoContenido][tipoImagen];
            $('#ancho').val(preset.ancho);
            $('#alto').val(preset.alto);
        }
    });
});
</script>
@endsection