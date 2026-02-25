@extends('admin.layouts.app')

@section('title', 'Editar Galería: ' . $gallery->nombre)

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Editar Galería</h1>
                    <p class="text-muted">{{ $gallery->nombre }}</p>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.galleries.index') }}">Galerías</a></li>
                        <li class="breadcrumb-item active">Editar</li>
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
                                <i class="fas fa-edit me-2"></i>
                                Información de la Galería
                            </h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.galleries.update', $gallery) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
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
                                           value="{{ old('nombre', $gallery->nombre) }}" 
                                           required>
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
                                              placeholder="Descripción de la galería...">{{ old('descripcion', $gallery->descripcion) }}</textarea>
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
                                               {{ old('activa', $gallery->activa) ? 'checked' : '' }}>
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
                                        Guardar Cambios
                                    </button>
                                    <a href="{{ route('admin.galleries.show', $gallery) }}" class="btn btn-info ms-2">
                                        <i class="fas fa-images me-2"></i>
                                        Gestionar Imágenes
                                    </a>
                                    <a href="{{ route('admin.galleries.index') }}" class="btn btn-secondary ms-2">
                                        <i class="fas fa-times me-2"></i>
                                        Cancelar
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Panel de información -->
                <div class="col-lg-4">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle"></i>
                                Estadísticas
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span><i class="fas fa-images text-blue"></i> Total de imágenes:</span>
                                <span class="badge bg-primary">{{ $gallery->images->count() }}</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span><i class="fas fa-calendar text-green"></i> Creada:</span>
                                <span>{{ $gallery->created_at->format('d/m/Y') }}</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span><i class="fas fa-edit text-yellow"></i> Actualizada:</span>
                                <span>{{ $gallery->updated_at->format('d/m/Y') }}</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span><i class="fas fa-eye text-purple"></i> Estado:</span>
                                <span class="badge {{ $gallery->activa ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $gallery->activa ? 'Activa' : 'Inactiva' }}
                                </span>
                            </div>

                            @if($gallery->imagen_portada)
                                <hr>
                                <h6><i class="fas fa-image"></i> Imagen de portada:</h6>
                                <img src="{{ $gallery->portada_desktop_url ?: asset('storage/' . $gallery->imagen_portada) }}" 
                                     alt="{{ $gallery->nombre }}" 
                                     class="img-fluid rounded">
                            @else
                                <hr>
                                <div class="text-center text-muted">
                                    <i class="fas fa-image fa-2x mb-2"></i>
                                    <p>No hay imagen de portada.<br>
                                    <small>Se usará la primera imagen subida.</small></p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Acciones rápidas -->
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-bolt"></i>
                                Acciones Rápidas
                            </h3>
                        </div>
                        <div class="card-body">
                            <a href="{{ route('admin.galleries.show', $gallery) }}" 
                               class="btn btn-success btn-block mb-2">
                                <i class="fas fa-upload me-2"></i>
                                Subir Imágenes
                            </a>
                            
                            @if($gallery->activa)
                                <a href="{{ route('galleries.show', ['idioma' => 'es', 'slug' => $gallery->slug]) }}" 
                                   class="btn btn-info btn-block mb-2" 
                                   target="_blank">
                                    <i class="fas fa-external-link-alt me-2"></i>
                                    Ver en Web
                                </a>
                            @endif
                            
                            <button type="button" 
                                    class="btn btn-danger btn-block" 
                                    onclick="confirmDelete()">
                                <i class="fas fa-trash me-2"></i>
                                Eliminar Galería
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal de confirmación de eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que quieres eliminar la galería <strong>"{{ $gallery->nombre }}"</strong>?</p>
                <p class="text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Esta acción eliminará también todas las imágenes asociadas y no se puede deshacer.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="{{ route('admin.galleries.destroy', $gallery) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar Galería</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function confirmDelete() {
        $('#deleteModal').modal('show');
    }
</script>
@endsection