@extends('admin.layouts.app')

@section('page-title', 'Detalles del Usuario')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Usuarios</a></li>
    <li class="breadcrumb-item active">{{ $user->name }}</li>
@endsection

@section('content')
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!-- Información básica del usuario -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-user"></i> {{ $user->name }}
                                @if($user->id === auth()->id())
                                    <span class="badge badge-info ml-2">Tu cuenta</span>
                                @endif
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6><strong>Información Personal</strong></h6>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td><strong>ID:</strong></td>
                                            <td>{{ $user->id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Nombre:</strong></td>
                                            <td>{{ $user->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{{ $user->email }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Rol:</strong></td>
                                            <td>
                                                @if($user->role)
                                                    <span class="badge badge-{{ 
                                                        $user->role->slug === 'administrator' ? 'danger' : 
                                                        ($user->role->slug === 'editor' ? 'warning' : 'secondary') 
                                                    }}">
                                                        {{ $user->role->nombre }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-light">Sin rol asignado</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6><strong>Fechas</strong></h6>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td><strong>Registrado:</strong></td>
                                            <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Actualizado:</strong></td>
                                            <td>{{ $user->updated_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tiempo registrado:</strong></td>
                                            <td>{{ $user->created_at->diffForHumans() }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Permisos del usuario (si tiene rol) -->
                    @if($user->role)
                        <div class="card mt-3">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-shield-alt"></i> Permisos del Rol "{{ $user->role->nombre }}"
                                </h3>
                            </div>
                            <div class="card-body">
                                @if($user->role->descripcion)
                                    <p class="text-muted mb-3">{{ $user->role->descripcion }}</p>
                                @endif

                                @if($user->role->permissions->count() > 0)
                                    <div class="row">
                                        @php
                                            $permisosPorModulo = $user->role->permissions->groupBy('modulo');
                                        @endphp
                                        @foreach($permisosPorModulo as $modulo => $permisos)
                                            <div class="col-md-6 mb-3">
                                                <h6 class="text-primary">
                                                    <i class="fas fa-folder"></i> {{ ucfirst($modulo) }}
                                                </h6>
                                                <ul class="list-unstyled ml-3">
                                                    @foreach($permisos as $permiso)
                                                        <li class="mb-1">
                                                            <i class="fas fa-check-circle text-success"></i>
                                                            {{ $permiso->nombre }}
                                                            <small class="text-muted">({{ $permiso->tipo_permiso }})</small>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-3">
                                        <i class="fas fa-exclamation-circle text-warning fa-2x mb-2"></i>
                                        <p class="text-muted">Este rol no tiene permisos asignados.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Acciones -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Volver a la lista
                                </a>
                                
                                <div class="btn-group">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                                        <i class="fas fa-edit"></i> Editar Usuario
                                    </a>
                                    
                                    @if($user->id !== auth()->id())
                                        <button type="button" 
                                                class="btn btn-danger" 
                                                onclick="confirmarEliminacion({{ $user->id }}, '{{ addslashes($user->name) }}')">
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
                <p>¿Estás seguro de que deseas eliminar al usuario <strong id="nombreUsuario"></strong>?</p>
                <p class="text-danger"><small>Esta acción no se puede deshacer.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarEliminar">Eliminar Usuario</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    let usuarioAEliminar = null;

    function confirmarEliminacion(id, nombre) {
        usuarioAEliminar = id;
        document.getElementById('nombreUsuario').textContent = nombre;
        $('#modalConfirmarEliminacion').modal('show');
    }

    document.getElementById('btnConfirmarEliminar').addEventListener('click', function() {
        if (!usuarioAEliminar) return;

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Eliminando...';

        fetch(`{{ route('admin.users.index') }}/${usuarioAEliminar}`, {
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
                
                // Redirigir a la lista de usuarios
                window.location.href = '{{ route('admin.users.index') }}';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar el usuario');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = 'Eliminar Usuario';
        });
    });
</script>
@endsection