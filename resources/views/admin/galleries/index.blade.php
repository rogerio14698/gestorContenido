@extends('admin.layouts.app')

@section('title', 'Galerías - Eunomia CMS')

@section('page-title', 'Gestión de Galerías')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Galerías</li>
@endsection

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="float-right">
                    <a href="{{ route('admin.galleries.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nueva Galería
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-images me-2"></i>
                        Lista de Galerías
                    </h3>
                </div>
                <div class="card-body p-0">
                    @if($galleries->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nombre</th>
                                        <th width="100" class="text-center">Imágenes</th>
                                        <th width="100" class="text-center">Estado</th>
                                        <th width="150" class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($galleries as $gallery)
                                        <tr>
                                            <td>
                                                <h6 class="mb-1">
                                                    <a href="{{ route('admin.galleries.show', $gallery) }}" 
                                                       class="text-decoration-none">
                                                        {{ $gallery->nombre }}
                                                    </a>
                                                </h6>
                                                @if($gallery->descripcion)
                                                    <p class="text-muted small mb-0">
                                                        {{ Str::limit($gallery->descripcion, 80) }}
                                                    </p>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info">
                                                    {{ $gallery->images_count ?? 0 }} fotos
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if($gallery->activa)
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-eye me-1"></i>Activa
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">
                                                        <i class="fas fa-eye-slash me-1"></i>Inactiva
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.galleries.edit', $gallery) }}" 
                                                       class="btn btn-sm btn-warning" 
                                                       title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger" 
                                                            onclick="confirmDelete({{ $gallery->id }}, '{{ addslashes($gallery->nombre) }}')"
                                                            title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if($galleries->hasPages())
                            <div class="card-footer">
                                {{ $galleries->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-images fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">No hay galerías creadas</h4>
                            <p class="text-muted">Comienza creando tu primera galería de imágenes.</p>
                            <a href="{{ route('admin.galleries.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                Crear Primera Galería
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Eliminar la galería <strong id="gallery-name"></strong>?</p>
                <p class="text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Se eliminarán también todas las imágenes asociadas.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="delete-form" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
console.log('🚀 Script de galerías cargado correctamente');

function confirmDelete(galleryId, galleryName) {
    console.log('🗑️ Intentando eliminar galería:', galleryId, galleryName);
    
    document.getElementById('gallery-name').textContent = galleryName;
    const deleteUrl = '{{ route("admin.galleries.destroy", ":id") }}'.replace(':id', galleryId);
    document.getElementById('delete-form').action = deleteUrl;
    
    console.log('📡 URL de eliminación:', deleteUrl);
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

// Agregar listener al formulario de eliminación para debug
document.addEventListener('DOMContentLoaded', function() {
    console.log('📋 DOM cargado - configurando eventos');
    
    const deleteForm = document.getElementById('delete-form');
    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) {
            console.log('📤 Enviando formulario de eliminación:', this.action);
            console.log('📝 Método:', this.method);
            console.log('🎯 Target:', this.action);
        });
    }
    
    // Auto-cerrar alertas después de 5 segundos
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});
</script>
@endpush