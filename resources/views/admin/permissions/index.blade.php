@extends('admin.layouts.app')

@section('title', 'Gestión de Permisos')
@section('page-title', 'Gestión de Permisos')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Permisos</li>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Acciones -->
        <div class="row mb-3">
            <div class="col-md-6">
                <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Permiso
                </a>
            </div>
            <div class="col-md-6 text-right">
                <span class="text-muted">Total: {{ $permissions->count() }} permisos</span>
            </div>
        </div>

        <!-- Permisos agrupados por módulo -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Permisos por Módulo</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if($permissionsByModule->count() > 0)
                    <div class="accordion" id="permissionsAccordion">
                        @foreach($permissionsByModule as $moduloKey => $permisos)
                            @php
                                $moduloNombre = \App\Models\Permission::MODULOS[$moduloKey] ?? ucfirst($moduloKey);
                                $accordionId = 'accordion-' . Str::slug($moduloKey);
                            @endphp
                            <div class="card">
                                <div class="card-header" id="heading-{{ $accordionId }}">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" 
                                                data-target="#{{ $accordionId }}" aria-expanded="true" 
                                                aria-controls="{{ $accordionId }}">
                                            <i class="fas fa-folder mr-2"></i>
                                            {{ $moduloNombre }} 
                                            <span class="badge badge-info ml-2">{{ $permisos->count() }}</span>
                                        </button>
                                    </h5>
                                </div>

                                <div id="{{ $accordionId }}" class="collapse {{ $loop->first ? 'show' : '' }}" 
                                     aria-labelledby="heading-{{ $accordionId }}" 
                                     data-parent="#permissionsAccordion">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Permiso</th>
                                                        <th>Slug</th>
                                                        <th>Tipo</th>
                                                        <th>Descripción</th>
                                                        <th style="width: 80px;">Roles</th>
                                                        <th style="width: 80px;">Estado</th>
                                                        <th style="width: 150px;">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($permisos as $permission)
                                                        <tr>
                                                            <td><strong>{{ $permission->nombre }}</strong></td>
                                                            <td><code>{{ $permission->slug }}</code></td>
                                                            <td>
                                                                <span class="badge badge-secondary">
                                                                    {{ ucfirst($permission->tipo_permiso) }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                @if($permission->descripcion)
                                                                    {{ Str::limit($permission->descripcion, 60) }}
                                                                @else
                                                                    <span class="text-muted">Sin descripción</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                @if($permission->roles_count > 0)
                                                                    <span class="badge badge-success">{{ $permission->roles_count }}</span>
                                                                @else
                                                                    <span class="badge badge-light">0</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                @if($permission->activo)
                                                                    <span class="badge badge-success"><i class="fas fa-check"></i></span>
                                                                @else
                                                                    <span class="badge badge-danger"><i class="fas fa-times"></i></span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <div class="btn-group" role="group">
                                                                    <a href="{{ route('admin.permissions.show', $permission) }}" 
                                                                       class="btn btn-sm btn-info" title="Ver detalles">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                    <a href="{{ route('admin.permissions.edit', $permission) }}" 
                                                                       class="btn btn-sm btn-warning" title="Editar">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                    <button type="button" class="btn btn-sm btn-danger btn-eliminar" 
                                                                            data-permission-id="{{ $permission->id }}" 
                                                                            data-permission-name="{{ $permission->nombre }}" title="Eliminar">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-key fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay permisos creados</h5>
                        <p class="text-muted">Crea tu primer permiso para comenzar a controlar el acceso al sistema.</p>
                        <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Crear Primer Permiso
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Resumen por tipo de permiso -->
        @if($permissions->count() > 0)
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Resumen por Tipo de Permiso</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach(\App\Models\Permission::TIPOS_PERMISO as $tipo)
                                    @php
                                        $count = $permissions->where('tipo_permiso', $tipo)->count();
                                    @endphp
                                    <div class="col-md-3 col-6">
                                        <div class="info-box">
                                            <span class="info-box-icon 
                                                @if($tipo === 'crear') bg-success
                                                @elseif($tipo === 'mostrar') bg-info
                                                @elseif($tipo === 'editar') bg-warning
                                                @else bg-danger
                                                @endif
                                            ">
                                                <i class="fas 
                                                    @if($tipo === 'crear') fa-plus
                                                    @elseif($tipo === 'mostrar') fa-eye
                                                    @elseif($tipo === 'editar') fa-edit
                                                    @else fa-trash
                                                    @endif
                                                "></i>
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">{{ ucfirst($tipo) }}</span>
                                                <span class="info-box-number">{{ $count }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
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
                    <h6>¿Estás seguro de que deseas eliminar este permiso?</h6>
                    <p class="text-muted"><strong id="permissionToDelete"></strong></p>
                    <p class="text-muted">Esta acción no se puede deshacer y afectará a los roles que tienen este permiso.</p>
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
        const permissionId = $(this).data('permission-id');
        const permissionName = $(this).data('permission-name');
        
        if (!permissionId) {
            alert('Error: No se pudo identificar el permiso a eliminar');
            return;
        }
        
        // Configurar modal de confirmación
        const deleteUrl = '{{ route("admin.permissions.index") }}/' + permissionId;
        
        $('#deleteForm').attr('action', deleteUrl);
        $('#permissionToDelete').text(permissionName || 'Permiso #' + permissionId);
        
        // Mostrar modal
        $('#deleteModal').modal('show');
    });

    // Confirmar eliminación
    $('#confirmarEliminar').click(function() {
        const form = $('#deleteForm');
        const action = form.attr('action');
        
        if (!action || action === '') {
            alert('Error: No se pudo configurar la eliminación');
            return;
        }
        
        // Mostrar indicador de carga
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Eliminando...');
        
        form.submit();
    });
});
</script>
@endpush