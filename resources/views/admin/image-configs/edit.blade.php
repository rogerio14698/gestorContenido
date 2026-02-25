@extends('admin.layouts.app')

@section('title', 'Editar Configuración de Imagen')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Editar Configuración de Imagen</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.image-configs.index') }}">Configuraciones</a></li>
                        <li class="breadcrumb-item active">Editar</li>
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
                        <i class="fas fa-edit"></i> Editar: {{ ucfirst($imageConfig->tipo_contenido) }} - {{ ucfirst(str_replace('_', ' ', $imageConfig->tipo_imagen)) }}
                    </h3>
                </div>

                <form action="{{ route('admin.image-configs.update', $imageConfig) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tipo de Contenido</label>
                                    <input type="text" class="form-control" 
                                           value="{{ ucfirst($imageConfig->tipo_contenido) }}" readonly>
                                    <small class="text-muted">El tipo de contenido no se puede modificar</small>
                                </div>

                                <div class="form-group">
                                    <label>Tipo de Imagen</label>
                                    <input type="text" class="form-control" 
                                           value="{{ ucfirst(str_replace('_', ' ', $imageConfig->tipo_imagen)) }}" readonly>
                                    <small class="text-muted">El tipo de imagen no se puede modificar</small>
                                </div>

                                <div class="form-group">
                                    <label for="formato">Formato de Imagen</label>
                                    <select name="formato" id="formato" 
                                            class="form-control @error('formato') is-invalid @enderror" required>
                                        <option value="jpg" {{ old('formato', $imageConfig->formato) == 'jpg' ? 'selected' : '' }}>JPG</option>
                                        <option value="png" {{ old('formato', $imageConfig->formato) == 'png' ? 'selected' : '' }}>PNG</option>
                                        <option value="webp" {{ old('formato', $imageConfig->formato) == 'webp' ? 'selected' : '' }}>WebP</option>
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
                                                   value="{{ old('ancho', $imageConfig->ancho) }}" min="50" max="5000" required>
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
                                                   value="{{ old('alto', $imageConfig->alto) }}" min="0" max="5000" step="1" placeholder="Dejar vacío o 0 para solo ancho">
                                            <small class="text-muted">Si dejas vacío o pones 0, solo se fijará el ancho y se mantendrá la proporción.</small>
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
                                           value="{{ old('calidad', $imageConfig->calidad) }}" 
                                           oninput="document.getElementById('calidadValue').textContent = this.value + '%'">
                                    <small class="text-muted">Calidad actual: <span id="calidadValue">{{ old('calidad', $imageConfig->calidad) }}%</span></small>
                                    @error('calidad')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Atención:</strong> Los cambios solo afectarán a las imágenes que se suban después de modificar esta configuración.
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
                                        <input type="hidden" name="mantener_aspecto" value="0">
                                        <input type="checkbox" name="mantener_aspecto" id="mantener_aspecto" value="1"
                                               class="custom-control-input"
                                               {{ old('mantener_aspecto', $imageConfig->mantener_aspecto) ? 'checked' : '' }}>
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
                                        <input type="hidden" name="redimensionar" value="0">
                                        <input type="checkbox" name="redimensionar" id="redimensionar" value="1"
                                               class="custom-control-input"
                                               {{ old('redimensionar', $imageConfig->redimensionar) ? 'checked' : '' }}>
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
                                        <input type="hidden" name="activo" value="0">
                                        <input type="checkbox" name="activo" id="activo" value="1"
                                               class="custom-control-input"
                                               {{ old('activo', $imageConfig->activo) ? 'checked' : '' }}>
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
                            <strong>Configuración actual:</strong> 
                            {{ $imageConfig->ancho }}×{{ $imageConfig->alto }}px, 
                            {{ strtoupper($imageConfig->formato) }}, 
                            {{ $imageConfig->calidad }}% calidad
                            @if($imageConfig->mantener_aspecto)
                                , mantiene aspecto
                            @endif
                            @if($imageConfig->redimensionar)
                                , redimensiona
                            @endif
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualizar Configuración
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