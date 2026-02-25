@extends('admin.layouts.app')

@section('page-title', 'Detalles del Rol')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
    <li class="breadcrumb-item active">{{ $role->nombre }}</li>
@endsection

@section('content')
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <!-- Información básica del rol -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-user-tag"></i> {{ $role->nombre }}
                                @if(!$role->activo)
                                    <span class="badge badge-secondary ml-2">Inactivo</span>
                                @endif
                                @if($role->slug === 'administrador')
                                    <span class="badge badge-danger ml-2">Rol de Sistema</span>
                                @endif
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6><strong>Información General</strong></h6>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td><strong>ID:</strong></td>
                                            <td>{{ $role->id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Nombre:</strong></td>
                                            <td>{{ $role->nombre }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Slug:</strong></td>
                                            <td><code>{{ $role->slug }}</code></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Estado:</strong></td>
                                            <td>
                                                @if($role->activo)
                                                    <span class="badge badge-success">Activo</span>
                                                @else
                                                    <span class="badge badge-secondary">Inactivo</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6><strong>Estadísticas</strong></h6>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td><strong>Usuarios con este rol:</strong></td>
                                            <td>{{ $role->users->count() }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Permisos asignados:</strong></td>
                                            <td>{{ $role->permissions->count() }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Creado:</strong></td>
                                            <td>{{ $role->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Última actualización:</strong></td>
                                            <td>{{ $role->updated_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            
                            @if($role->descripcion)
                                <div class="mt-3">
                                    <h6><strong>Descripción</strong></h6>
                                    <p class="text-muted">{{ $role->descripcion }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Usuarios con este rol -->
                    @if($role->users->count() > 0)
                        <div class="card mt-3">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-users"></i> Usuarios con este Rol ({{ $role->users->count() }})
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Nombre</th>
                                                <th>Email</th>
                                                <th>Fecha de Registro</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($role->users as $user)
                                                <tr>
                                                    <td>{{ $user->id }}</td>
                                                    <td>{{ $user->name }}</td>
                                                    <td>{{ $user->email }}</td>
                                                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                                    <td>
                                                        <a href="{{ route('admin.users.show', $user) }}" 
                                                           class="btn btn-sm btn-outline-info">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Permisos del rol -->
                    @if($role->permissions->count() > 0)
                        <div class="card mt-3">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-shield-alt"></i> Permisos Asignados ({{ $role->permissions->count() }})
                                </h3>
                            </div>
                            <div class="card-body">
                                @php
                                    $permissionsByModule = $role->permissions->groupBy('modulo');
                                @endphp
                                
                                <div class="row">
                                    @foreach($permissionsByModule as $modulo => $permisos)
                                        <div class="col-md-6 mb-3">
                                            <div class="card card-outline card-success">
                                                <div class="card-header">
                                                    <h6 class="card-title mb-0">
                                                        <i class="fas fa-folder"></i> {{ ucfirst($modulo) }}
                                                        <small class="text-muted">({{ $permisos->count() }} permisos)</small>
                                                    </h6>
                                                </div>
                                                <div class="card-body">
                                                    @foreach($permisos as $permiso)
                                                        <div class="mb-2">
                                                            <i class="fas fa-check-circle text-success"></i>
                                                            <strong>{{ $permiso->nombre }}</strong>
                                                            <small class="text-muted d-block ml-3">
                                                                {{ $permiso->tipo_permiso }} - {{ $permiso->descripcion }}
                                                            </small>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="card mt-3">
                            <div class="card-body text-center">
                                <i class="fas fa-exclamation-circle text-warning fa-3x mb-3"></i>
                                <h5>Sin Permisos Asignados</h5>
                                <p class="text-muted">Este rol no tiene permisos asignados.</p>
                                <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Asignar Permisos
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Acciones -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Volver a la lista
                                </a>
                                
                                <div class="btn-group">
                                    <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary">
                                        <i class="fas fa-edit"></i> Editar Rol
                                    </a>
                                    
                                    @if($role->slug !== 'administrador')
                                        <button type="button" 
                                                class="btn btn-danger" 
                                                onclick="confirmarEliminacion({{ $role->id }}, '{{ addslashes($role->nombre) }}')">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal de Confirmación -->
<div class="modal fade" id="modalConfirmarEliminacion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Confirmar Eliminación</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar el rol <strong id="nombreRol"></strong>?</p>
                @if($role->users->count() > 0)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Atención:</strong> Este rol tiene {{ $role->users->count() }} usuario(s) asignado(s). 
                        Considera reasignar estos usuarios a otro rol antes de eliminar.
                    </div>
                @endif
                <p class="text-danger"><small>Esta acción no se puede deshacer.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarEliminar">Eliminar Rol</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    let rolAEliminar = null;

    function confirmarEliminacion(id, nombre) {
        rolAEliminar = id;
        document.getElementById('nombreRol').textContent = nombre;
        $('#modalConfirmarEliminacion').modal('show');
    }

    document.getElementById('btnConfirmarEliminar').addEventListener('click', function() {
        if (!rolAEliminar) return;

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Eliminando...';

        fetch(`{{ route('admin.roles.index') }}/${rolAEliminar}`, {
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
                $('#modalConfirmarEliminacion').modal('hide');
                
                // Redirigir a la lista de roles
                window.location.href = '{{ route('admin.roles.index') }}';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar el rol');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = 'Eliminar Rol';
        });
    });
</script>
@endsection