@extends('admin.layouts.app')

@section('title', 'Gestión de Idiomas - Eunomia CMS')

@section('page-title', 'Gestión de Idiomas')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Idiomas</li>
@endsection

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="float-right">
                    <a href="{{ route('admin.idiomas.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nuevo Idioma
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

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-language me-2"></i>
                    Lista de Idiomas del Sistema
                </h3>
                <div class="card-tools">
                    <span class="badge badge-info">Total: {{ $idiomas->count() }}</span>
                </div>
            </div>
            <div class="card-body p-0">
                @if($idiomas->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0" id="idiomas-table">
                            <thead class="table-dark">
                                <tr>
                                    <th width="40" class="text-center"><i class="fas fa-sort" title="Arrastrar para ordenar"></i></th>
                                    <th width="60" class="text-center">Imagen</th>
                                    <th>Idioma</th>
                                    <th width="120" class="text-center">Etiqueta</th>
                                    <th width="100" class="text-center">Principal</th>
                                    <th width="100" class="text-center">Estado</th>
                                    <th width="150" class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="sortable-idiomas">
                                @foreach($idiomas as $idioma)
                                    <tr data-id="{{ $idioma->id }}">
                                        <td class="text-center">
                                            <i class="fas fa-grip-vertical text-muted drag-handle" style="cursor: move;"></i>
                                        </td>
                                        <td class="text-center">
                                            @if($idioma->imagen_url)
                                                <img src="{{ $idioma->imagen_url }}" 
                                                     alt="{{ $idioma->nombre }}" 
                                                     class="img-thumbnail rounded"
                                                     style="width: 40px; height: 30px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                     style="width: 40px; height: 30px;">
                                                    <i class="fas fa-flag text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <h6 class="mb-1">
                                                {{ $idioma->nombre }}
                                            </h6>
                                            <small class="text-muted">
                                                <code>lang="{{ $idioma->codigo_html }}"</code>
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary fs-6">
                                                {{ strtoupper($idioma->etiqueta) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if($idioma->es_principal)
                                                <span class="badge bg-primary">
                                                    <i class="fas fa-star me-1"></i>Principal
                                                </span>
                                            @else
                                                <span class="badge bg-light text-dark">
                                                    <i class="fas fa-star-o me-1"></i>Secundario
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input toggle-active" 
                                                       type="checkbox" 
                                                       data-id="{{ $idioma->id }}"
                                                       {{ $idioma->activo ? 'checked' : '' }}
                                                       style="cursor: pointer;">
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.idiomas.edit', $idioma) }}" 
                                                   class="btn btn-sm btn-warning" 
                                                   title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger" 
                                                        onclick="confirmDelete({{ $idioma->id }}, '{{ addslashes($idioma->nombre) }}')"
                                                        title="Eliminar"
                                                        {{ $idioma->es_principal && $idiomas->where('activo', true)->count() === 1 ? 'disabled' : '' }}>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-language fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No hay idiomas configurados</h4>
                        <p class="text-muted">Comienza agregando el primer idioma para tu sitio web.</p>
                        <a href="{{ route('admin.idiomas.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Agregar Primer Idioma
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Modal de confirmación de eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de eliminar el idioma <strong id="idioma-name"></strong>?</p>
                <p class="text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Esta acción no se puede deshacer y puede afectar el contenido multiidioma.
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

@push('styles')
<style>
.drag-handle:hover {
    color: #007bff !important;
}

.sortable-ghost {
    opacity: 0.5;
    background-color: #f8f9fa;
}

.form-check-input:checked {
    background-color: #28a745;
    border-color: #28a745;
}

.form-check-input:focus {
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
console.log('🌍 Script de gestión de idiomas cargado');

const toggleActiveUrlTemplate = '{{ route('admin.idiomas.toggle-active', ['idioma_id' => '__ID__']) }}';

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar drag & drop
    const sortableElement = document.getElementById('sortable-idiomas');
    if (sortableElement) {
        const sortable = Sortable.create(sortableElement, {
            handle: '.drag-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: function(evt) {
                updateOrder();
            }
        });
    }

    // Manejar cambios de estado activo
    document.querySelectorAll('.toggle-active').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            toggleActiveState(this.dataset.id, this.checked);
        });
    });

    // Auto-cerrar alertas después de 5 segundos
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});

function updateOrder() {
    const filas = document.querySelectorAll('#sortable-idiomas tr');
    const orden = Array.from(filas).map(fila => fila.dataset.id);

    fetch('{{ route("admin.idiomas.update-order") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ orden: orden })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('✅ Orden actualizado correctamente');
        } else {
            console.error('❌ Error al actualizar orden:', data.message);
            showAlert('error', 'Error al actualizar el orden de los idiomas');
        }
    })
    .catch(error => {
        console.error('❌ Error en la petición:', error);
        showAlert('error', 'Error de conexión al actualizar el orden');
    });
}

function toggleActiveState(idiomaId, activo) {
    const toggleUrl = toggleActiveUrlTemplate.replace('__ID__', idiomaId);

    fetch(toggleUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ activo: activo })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('✅ Estado actualizado:', data.message);
            showAlert('success', data.message);
        } else {
            console.error('❌ Error al cambiar estado:', data.message);
            showAlert('error', data.message);
            // Revertir el checkbox si falló
            const checkbox = document.querySelector(`[data-id="${idiomaId}"]`);
            checkbox.checked = !activo;
        }
    })
    .catch(error => {
        console.error('❌ Error en la petición:', error);
        showAlert('error', 'Error de conexión al cambiar el estado');
        // Revertir el checkbox si falló
        const checkbox = document.querySelector(`[data-id="${idiomaId}"]`);
        checkbox.checked = !activo;
    });
}

function confirmDelete(idiomaId, idiomaName) {
    document.getElementById('idioma-name').textContent = idiomaName;
    const deleteUrl = '{{ route("admin.idiomas.destroy", ":id") }}'.replace(':id', idiomaId);
    document.getElementById('delete-form').action = deleteUrl;
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="fas ${iconClass} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Insertar al principio de la sección de contenido
    const content = document.querySelector('.content .container-fluid');
    content.insertAdjacentHTML('afterbegin', alertHtml);
    
    // Auto-cerrar después de 5 segundos
    setTimeout(() => {
        const alert = content.querySelector('.alert');
        if (alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }
    }, 5000);
}
</script>
@endpush