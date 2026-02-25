@extends('admin.layouts.app')

@section('page-title', 'Gestión de Usuarios')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Usuarios</li>
@endsection

@section('content')
    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @endif

        <!-- Acciones -->
        <div class="row mb-3">
            <div class="col-md-6">
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Usuario
                </a>
            </div>
            <div class="col-md-6 text-right">
                <span class="text-muted">Total: {{ $users->count() }} usuarios</span>
            </div>
        </div>

        <!-- Tabla de usuarios -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Usuarios</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>

            <div class="card-body">
                @if($users->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60px;">ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th style="width: 140px;">Fecha de Registro</th>
                                    <th style="width: 200px;">Acciones</th>
                                </tr>
                            </thead>
                                        <tbody>
                                            @foreach($users as $user)
                                                <tr>
                                                    <td>{{ $user->id }}</td>
                                                    <td>
                                                        <strong>{{ $user->name }}</strong>
                                                        @if($user->id === auth()->id())
                                                            <span class="badge badge-info ml-1">Tu cuenta</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $user->email }}</td>
                                                    <td>
                                                        @if($user->role)
                                                            <span class="badge badge-{{ 
                                                                $user->role->slug === 'administrator' ? 'danger' : 
                                                                ($user->role->slug === 'editor' ? 'warning' : 'secondary') 
                                                            }}">
                                                                {{ $user->role->nombre }}
                                                            </span>
                                                        @else
                                                            <span class="badge badge-light">Sin rol</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">
                                                            {{ $user->created_at->format('d/m/Y H:i') }}
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="{{ route('admin.users.show', $user) }}" 
                                                               class="btn btn-sm btn-outline-info" 
                                                               title="Ver">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('admin.users.edit', $user) }}" 
                                                               class="btn btn-sm btn-outline-primary" 
                                                               title="Editar">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            @if($user->id !== auth()->id())
                                                                <button type="button" 
                                                                        class="btn btn-sm btn-outline-danger" 
                                                                        title="Eliminar"
                                                                        onclick="confirmarEliminacion({{ $user->id }}, '{{ addslashes($user->name) }}')">
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
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No hay usuarios registrados.</p>
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Crear el Primer Usuario
                        </a>
                    </div>
                @endif
            </div>
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
                
                // Mostrar mensaje de éxito
                const alert = `
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        ${data.message}
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                `;
                document.querySelector('.content .container-fluid').insertAdjacentHTML('afterbegin', alert);
                
                // Recargar página después de 1 segundo
                setTimeout(() => {
                    location.reload();
                }, 1000);
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