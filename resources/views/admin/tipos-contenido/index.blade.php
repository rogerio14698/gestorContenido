@extends('admin.layouts.app')

@section('page-title', 'Tipos de Contenido')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Tipos de Contenido</li>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Acciones -->
        <div class="row mb-3">
            <div class="col-md-6">
                <a href="{{ route('admin.tipos-contenido.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Tipo de Contenido
                </a>
            </div>
            <div class="col-md-6 text-right">
                <span class="text-muted">Total: {{ $tipos->count() }} tipos</span>
            </div>
        </div>

        <!-- Tabla de tipos de contenido -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Tipos de Contenido</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if($tipos->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60px;">ID</th>
                                    <th>Tipo de Contenido</th>
                                    <th>Descripción</th>
                                    <th style="width: 120px;">Contenidos</th>
                                    <th style="width: 140px;">Fecha de Creación</th>
                                    <th style="width: 200px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tipos as $tipo)
                                    <tr>
                                        <td>{{ $tipo->id }}</td>
                                        <td>
                                            <strong>{{ $tipo->tipo_contenido }}</strong>
                                        </td>
                                        <td>
                                            @if($tipo->descripcion)
                                                {{ $tipo->descripcion }}
                                            @else
                                                <span class="text-muted">Sin descripción</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $contenidos = \App\Models\TextoIdioma::where('tipo_contenido_id', $tipo->id)->count();
                                            @endphp
                                            @if($contenidos > 0)
                                                <span class="badge badge-success">{{ $contenidos }}</span>
                                            @else
                                                <span class="badge badge-light">0</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $tipo->created_at->format('d/m/Y H:i') }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.tipos-contenido.show', $tipo) }}" 
                                                   class="btn btn-sm btn-outline-info" 
                                                   title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.tipos-contenido.edit', $tipo) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @php
                                                    $contenidos = \App\Models\TextoIdioma::where('tipo_contenido_id', $tipo->id)->count();
                                                @endphp
                                                @if($contenidos == 0)
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-danger" 
                                                            title="Eliminar"
                                                            onclick="confirmarEliminacion({{ $tipo->id }}, '{{ addslashes($tipo->tipo_contenido) }}')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @else
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-secondary" 
                                                            title="No se puede eliminar: tiene {{ $contenidos }} contenido(s)"
                                                            disabled>
                                                        <i class="fas fa-lock"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No hay tipos de contenido creados.</p>
                        <a href="{{ route('admin.tipos-contenido.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Crear el Primer Tipo
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Modal de Confirmación -->
        <div class="modal fade" id="modalConfirmarEliminacion" tabindex="-1" aria-labelledby="modalEliminacionLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="modalEliminacionLabel">Confirmar Eliminación</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Estás seguro de que deseas eliminar el tipo <strong id="nombreTipo"></strong>?</p>
                        <p class="text-danger"><small>Esta acción no se puede deshacer.</small></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-danger" id="btnConfirmarEliminar">Eliminar Tipo</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    let tipoAEliminar = null;
    let modalInstance = null;

    // Función global para confirmar eliminación
    window.confirmarEliminacion = function(id, nombre) {
        tipoAEliminar = id;
        document.getElementById('nombreTipo').textContent = nombre;
        
        if (!modalInstance) {
            const modalElement = document.getElementById('modalConfirmarEliminacion');
            modalInstance = new bootstrap.Modal(modalElement);
        }
        modalInstance.show();
    };

    // Esperar a que el DOM esté cargado
    document.addEventListener('DOMContentLoaded', function() {
        const btnConfirmar = document.getElementById('btnConfirmarEliminar');
        
        if (btnConfirmar) {
            btnConfirmar.addEventListener('click', function() {
                if (!tipoAEliminar) return;

                const btn = this;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Eliminando...';

                fetch(`/admin/tipos-contenido/${tipoAEliminar}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Cerrar modal
                        if (modalInstance) {
                            modalInstance.hide();
                        }
                        
                        // Mostrar mensaje de éxito
                        const alertHtml = `
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle"></i> ${data.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `;
                        
                        const container = document.querySelector('.container-fluid');
                        if (container) {
                            container.insertAdjacentHTML('afterbegin', alertHtml);
                        }
                        
                        // Recargar página después de 1 segundo
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        alert('Error: ' + data.message);
                        btn.disabled = false;
                        btn.innerHTML = 'Eliminar Tipo';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al eliminar el tipo de contenido');
                    btn.disabled = false;
                    btn.innerHTML = 'Eliminar Tipo';
                });
            });
        }
    });
</script>
@endsection