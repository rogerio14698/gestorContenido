@extends('admin.layouts.app')

@section('title', 'Nueva Galería')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Nueva Galería</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.galleries.index') }}">Galerías</a></li>
                        <li class="breadcrumb-item active">Nueva Galería</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-plus me-2"></i>
                                Información de la Galería
                            </h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.galleries.store') }}" method="POST">
                                @csrf
                                
                                <!-- Nombre de la galería -->
                                <div class="form-group mb-3">
                                    <label for="nombre" class="form-label">
                                        <i class="fas fa-tag me-2"></i>
                                        Nombre de la Galería *
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('nombre') is-invalid @enderror" 
                                           id="nombre" 
                                           name="nombre" 
                                           value="{{ old('nombre') }}" 
                                           required 
                                           placeholder="Ej: Obra de teatro 2024">
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Descripción -->
                                <div class="form-group mb-3">
                                    <label for="descripcion" class="form-label">
                                        <i class="fas fa-align-left me-2"></i>
                                        Descripción
                                    </label>
                                    <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                              id="descripcion" 
                                              name="descripcion" 
                                              rows="4" 
                                              placeholder="Descripción de la galería...">{{ old('descripcion') }}</textarea>
                                    @error('descripcion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Estado de la galería -->
                                <div class="form-group mb-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="activa" 
                                               name="activa" 
                                               value="1" 
                                               {{ old('activa', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="activa">
                                            <i class="fas fa-eye me-2"></i>
                                            Galería activa (visible en el sitio web)
                                        </label>
                                    </div>
                                </div>

                                <!-- Botones de acción -->
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>
                                        Crear Galería
                                    </button>
                                    <a href="{{ route('admin.galleries.index') }}" class="btn btn-secondary ms-2">
                                        <i class="fas fa-times me-2"></i>
                                        Cancelar
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Panel de ayuda -->
                <div class="col-lg-4">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle"></i>
                                Información
                            </h3>
                        </div>
                        <div class="card-body">
                            <h6><i class="fas fa-lightbulb text-yellow"></i> Consejos:</h6>
                            <ul class="text-sm">
                                <li><strong>Nombre:</strong> Usa un nombre descriptivo y único</li>
                                <li><strong>Descripción:</strong> Añade contexto sobre qué contiene la galería</li>
                                <li><strong>Imagen de portada:</strong> Se usará automáticamente la primera imagen que subas</li>
                                <li><strong>Después de crear:</strong> Podrás subir múltiples imágenes usando drag & drop</li>
                            </ul>
                            
                            <hr>
                            
                            <h6><i class="fas fa-images text-blue"></i> Próximos pasos:</h6>
                            <ol class="text-sm">
                                <li>Crear la galería</li>
                                <li>Subir imágenes</li>
                                <li>Organizar el orden de las imágenes</li>
                                <li>Activar para hacerla visible</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
    // Auto-focus en el campo nombre
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('nombre').focus();
    });
</script>
@endsection