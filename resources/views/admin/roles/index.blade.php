@extends('admin.layouts.app')

@section('title', 'Gestión de Roles')
@section('page-title', 'Gestión de Roles')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Roles</li>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Acciones -->
        <div class="row mb-3">
            <div class="col-md-6">
                <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Rol
                </a>
                <a href="{{ route('admin.roles.permission-matrix') }}" class="btn btn-info ml-2">
                    <i class="fas fa-table"></i> Matriz de Permisos
                </a>
            </div>
            <div class="col-md-6 text-right">
                <span class="text-muted">Total: {{ $roles->count() }} roles</span>
            </div>
        </div>

        <!-- Tabla de roles -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Roles</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if($roles->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60px;">ID</th>
                                    <th>Nombre</th>
                                    <th>Slug</th>
                                    <th>Descripción</th>
                                    <th style="width: 100px;">Usuarios</th>
                                    <th style="width: 100px;">Permisos</th>
                                    <th style="width: 80px;">Estado</th>
                                    <th style="width: 200px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($roles as $role)
                                    <tr>
                                        <td>{{ $role->id }}</td>
                                        <td>
                                            <strong>{{ $role->nombre }}</strong>
                                            @if(in_array($role->slug, ['administrador', 'usuario', 'editor']))
                                                <span class="badge badge-info ml-1">Sistema</span>
                                            @endif
                                        </td>
                                        <td>
                                            <code>{{ $role->slug }}</code>
                                        </td>
                                        <td>
                                            @if($role->descripcion)
                                                {{ Str::limit($role->descripcion, 100) }}
                                            @else
                                                <span class="text-muted">Sin descripción</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($role->users_count > 0)
                                                <span class="badge badge-success">{{ $role->users_count }}</span>
                                            @else
                                                <span class="badge badge-light">0</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($role->permissions_count > 0)
                                                <span class="badge badge-primary">{{ $role->permissions_count }}</span>
                                            @else
                                                <span class="badge badge-warning">0</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($role->activo)
                                                <span class="badge badge-success"><i class="fas fa-check"></i></span>
                                            @else
                                                <span class="badge badge-danger"><i class="fas fa-times"></i></span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.roles.show', $role) }}" 
                                                   class="btn btn-sm btn-info" title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.roles.edit', $role) }}" 
                                                   class="btn btn-sm btn-warning" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if(!in_array($role->slug, ['administrador', 'usuario', 'editor']))
                                                    <button type="button" class="btn btn-sm btn-danger btn-eliminar" 
                                                            data-role-id="{{ $role->id }}" 
                                                            data-role-name="{{ $role->nombre }}" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
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
                    <div class="text-center py-4">
                        <i class="fas fa-users-cog fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay roles creados</h5>
                        <p class="text-muted">Crea tu primer rol para comenzar a organizar los permisos del sistema.</p>
                        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Crear Primer Rol
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

{{-- Modal de confirmación de eliminación --}}
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                    <h6>¿Estás seguro de que deseas eliminar este rol?</h6>
                    <p class="text-muted"><strong id="roleToDelete"></strong></p>
                    <p class="text-muted">Esta acción eliminará también todos los permisos asociados y no se puede deshacer.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="confirmarEliminar">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<form id="deleteForm" style="display: none;" method="POST">
    @csrf
    @method('DELETE')
</form>
@stop

@push('scripts')
<script>
$(document).ready(function() {
    // Manejar click en botón eliminar
    $(document).on('click', '.btn-eliminar', function() {
        console.log('🗑️ Botón eliminar clickeado');
        
        const roleId = $(this).data('role-id');
        const roleName = $(this).data('role-name');
        
        console.log('Rol a eliminar:', { id: roleId, name: roleName });
        
        if (!roleId) {
            console.error('❌ No se encontró el role-id en el botón');
            alert('Error: No se pudo identificar el rol a eliminar');
            return;
        }
        
        // Configurar modal de confirmación
        const deleteUrl = '{{ route("admin.roles.index") }}/' + roleId;
        
        $('#deleteForm').attr('action', deleteUrl);
        $('#roleToDelete').text(roleName || 'Rol #' + roleId);
        
        console.log('URL de eliminación:', deleteUrl);
        
        // Mostrar modal
        if ($('#deleteModal').length === 0) {
            console.error('❌ Modal de eliminación no encontrado');
            alert('Error: Modal de eliminación no disponible');
            return;
        }
        
        $('#deleteModal').modal('show');
        console.log('✅ Modal de eliminación mostrado');
    });

    // Confirmar eliminación
    $('#confirmarEliminar').click(function() {
        console.log('🔥 Confirmar eliminación clickeado');
        
        const form = $('#deleteForm');
        const action = form.attr('action');
        
        console.log('Enviando eliminación a:', action);
        
        if (!action || action === '') {
            console.error('❌ No hay URL de acción en el formulario');
            alert('Error: No se pudo configurar la eliminación');
            return;
        }
        
        // Mostrar indicador de carga
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Eliminando...');
        
        form.submit();
        console.log('✅ Formulario de eliminación enviado');
    });
});
</script>
@endpush