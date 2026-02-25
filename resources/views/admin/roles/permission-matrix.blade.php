@extends('admin.layouts.app')

@section('page-title', 'Matriz de Permisos')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
    <li class="breadcrumb-item active">Matriz de Permisos</li>
@endsection

@section('content')
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <!-- Alert container for messages -->
            <div id="alertContainer"></div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-check"></i> Éxito</h5>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-ban"></i> Error</h5>
                    {{ session('error') }}
                </div>
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-table"></i> Asignación de Permisos por Rol
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-success btn-sm" id="btnGuardarMatrix">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Instrucciones:</strong> Marca los checkboxes para asignar permisos a cada rol. 
                        Los cambios se guardan al presionar "Guardar Cambios".
                    </div>

                    @if($roles->count() > 0 && $permissions->count() > 0)
                        <form id="permissionMatrixForm">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm" id="permissionTable">
                                    <thead class="table-dark">
                                        <tr>
                                            <th style="width: 250px;">
                                                <i class="fas fa-key"></i> Permiso
                                            </th>
                                            @foreach($roles as $role)
                                                <th class="text-center" style="min-width: 120px;">
                                                    <div>
                                                        <strong>{{ $role->nombre }}</strong>
                                                        <br><small class="text-muted">ID: {{ $role->id }}</small>
                                                        <br><span class="badge badge-info" id="count-{{ $role->id }}">0 / {{ $permissions->count() }}</span>
                                                        <br><button type="button" class="btn btn-outline-light btn-xs mt-1" onclick="toggleRolePermissions({{ $role->id }})">
                                                            <i class="fas fa-toggle-on"></i> Toggle
                                                        </button>
                                                    </div>
                                                </th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($permissionsByModule as $modulo => $permisos)
                                            <tr class="table-secondary">
                                                <td colspan="{{ $roles->count() + 1 }}">
                                                    <strong>
                                                        <i class="fas fa-folder"></i> {{ strtoupper($modulo) }}
                                                        <small class="text-muted">({{ $permisos->count() }} permisos)</small>
                                                    </strong>
                                                </td>
                                            </tr>
                                            @foreach($permisos as $permission)
                                                <tr>
                                                    <td>
                                                        <div>
                                                            <strong>{{ $permission->nombre }}</strong>
                                                            <br><small class="text-muted">
                                                                <i class="fas fa-tag"></i> {{ $permission->tipo_permiso }}
                                                                | ID: {{ $permission->id }}
                                                            </small>
                                                        </div>
                                                    </td>
                                                    @foreach($roles as $role)
                                                        <td class="text-center">
                                                            <div class="form-check d-flex justify-content-center">
                                                                <input type="checkbox" 
                                                                       class="form-check-input permission-checkbox role-{{ $role->id }} module-{{ $modulo }}" 
                                                                       name="matrix[{{ $role->id }}][]" 
                                                                       value="{{ $permission->id }}"
                                                                       id="permission_{{ $permission->id }}_role_{{ $role->id }}"
                                                                       {{ in_array($permission->id, $matrix[$role->id] ?? []) ? 'checked' : '' }}>
                                                            </div>
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </form>

                        <!-- Resumen de cambios -->
                        <div class="mt-3">
                            <div class="card card-outline card-info">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-chart-pie"></i> Resumen de Permisos por Rol
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @foreach($roles as $role)
                                            <div class="col-md-4 mb-2">
                                                <div class="info-box">
                                                    <span class="info-box-icon bg-primary">
                                                        <i class="fas fa-user"></i>
                                                    </span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">{{ $role->nombre }}</span>
                                                        <span class="info-box-number" id="summary-{{ $role->id }}">
                                                            {{ count($matrix[$role->id] ?? []) }} / {{ $permissions->count() }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>No hay datos disponibles.</strong>
                            Necesitas crear al menos un rol y un permiso para gestionar la matriz.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
// Evitar conflictos jQuery - usar vanilla JS únicamente
document.addEventListener('DOMContentLoaded', function() {
    console.log('Matrix AdminLTE - Inicializando...');
    console.log('Matrix data:', @json($matrix));
    
    // Función para mostrar alertas usando AdminLTE
    function showAlert(message, type = 'success') {
        const alertContainer = document.getElementById('alertContainer');
        const alert = document.createElement('div');
        const alertClass = type === 'success' ? 'alert-success' : type === 'error' ? 'alert-danger' : 'alert-info';
        
        alert.className = `alert ${alertClass} alert-dismissible fade show`;
        alert.innerHTML = `
            ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        `;
        alertContainer.appendChild(alert);
        
        // Auto-remover después de 6 segundos
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 6000);
    }

    // Toggle todos los permisos de un rol
    window.toggleRolePermissions = function(roleId) {
        const checkboxes = document.querySelectorAll(`.role-${roleId}`);
        const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = !allChecked;
        });
        updateCounters();
        
        const action = allChecked ? 'desmarcados' : 'marcados';
        showAlert(`<i class="fas fa-info-circle"></i> Permisos ${action} para el rol ID: ${roleId}`, 'info');
    };

    // Actualizar contadores
    function updateCounters() {
        @foreach($roles as $role)
            const checkedCount{{ $role->id }} = document.querySelectorAll('.role-{{ $role->id }}:checked').length;
            const totalCount{{ $role->id }} = document.querySelectorAll('.role-{{ $role->id }}').length;
            const countElement{{ $role->id }} = document.getElementById('count-{{ $role->id }}');
            const summaryElement{{ $role->id }} = document.getElementById('summary-{{ $role->id }}');
            
            if (countElement{{ $role->id }}) {
                countElement{{ $role->id }}.textContent = `${checkedCount{{ $role->id }}} / ${totalCount{{ $role->id }}}`;
            }
            if (summaryElement{{ $role->id }}) {
                summaryElement{{ $role->id }}.textContent = `${checkedCount{{ $role->id }}} / ${totalCount{{ $role->id }}}`;
            }
        @endforeach
    }

    // Event listener para cambios en checkboxes
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('permission-checkbox')) {
            updateCounters();
        }
    });

    // Guardar matriz de permisos
    const btnGuardar = document.getElementById('btnGuardarMatrix');
    if (btnGuardar) {
        btnGuardar.addEventListener('click', async function() {
            const btn = this;
            const originalText = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

            try {
                // Construir objeto matrix
                const matrix = {};
                
                @foreach($roles as $role)
                    matrix[{{ $role->id }}] = [];
                    const role{{ $role->id }}Checkboxes = document.querySelectorAll('.role-{{ $role->id }}:checked');
                    role{{ $role->id }}Checkboxes.forEach(checkbox => {
                        matrix[{{ $role->id }}].push(parseInt(checkbox.value));
                    });
                @endforeach

                console.log('Matrix a enviar:', matrix);

                // Enviar datos usando fetch
                const response = await fetch('{{ route("admin.roles.permission-matrix.update") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ matrix: matrix })
                });

                const result = await response.json();
                console.log('Respuesta del servidor:', result);

                if (result.success) {
                    showAlert('<i class="fas fa-check-circle"></i> <strong>¡Éxito!</strong> ' + result.message, 'success');
                    
                    // Actualizar contadores después del guardado
                    setTimeout(updateCounters, 500);
                } else {
                    throw new Error(result.message || 'Error desconocido');
                }
            } catch (error) {
                console.error('Error al guardar:', error);
                showAlert('<i class="fas fa-exclamation-triangle"></i> <strong>Error:</strong> ' + error.message, 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });
    }

    // Inicializar contadores al cargar
    updateCounters();
    
    console.log('Matrix AdminLTE - Inicialización completada');
});
</script>
@endpush

@push('styles')
<style>
    .table td {
        vertical-align: middle;
    }
    .permission-table .form-check-input {
        cursor: pointer;
        transform: scale(1.2);
    }
    .info-box {
        box-shadow: 0 0 1px rgba(0,0,0,0.125), 0 1px 3px rgba(0,0,0,0.2);
    }
    .btn-xs {
        font-size: 0.75rem;
        padding: 0.125rem 0.25rem;
    }
    #permissionTable thead th {
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .table-responsive {
        max-height: 70vh;
        overflow-y: auto;
    }
</style>
@endpush