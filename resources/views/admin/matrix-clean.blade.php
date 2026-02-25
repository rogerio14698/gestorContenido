<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Matrix de Permisos - Versión Limpia</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .container { margin-top: 20px; }
        .table-container { background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .permission-table th { background-color: #495057; color: white; font-weight: 600; }
        .permission-table td { vertical-align: middle; }
        .module-header { background-color: #e9ecef; font-weight: bold; }
        .debug-info { background-color: #d1ecf1; border: 1px solid #bee5eb; border-radius: 4px; padding: 10px; margin-bottom: 20px; }
        .btn-group-custom { gap: 10px; margin-bottom: 20px; }
        .status-message { position: fixed; top: 20px; right: 20px; z-index: 1050; min-width: 300px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">
                    <i class="fas fa-shield-alt"></i> 
                    Matrix de Permisos - Versión Limpia
                </h1>

                <div class="debug-info">
                    <h6><i class="fas fa-info-circle"></i> Información de Debug:</h6>
                    <div class="row">
                        <div class="col-md-4"><strong>Roles:</strong> {{ $roles->count() }}</div>
                        <div class="col-md-4"><strong>Permisos:</strong> {{ $permissions->count() }}</div>
                        <div class="col-md-4"><strong>CSRF Token:</strong> {{ csrf_token() }}</div>
                    </div>
                    <div class="mt-2">
                        <strong>Matrix actual:</strong> 
                        <code>{{ json_encode($matrix) }}</code>
                    </div>
                </div>

                <div class="d-flex btn-group-custom">
                    <button type="button" class="btn btn-success" id="btnGuardarMatrix">
                        <i class="fas fa-save"></i> Guardar Original
                    </button>
                    <button type="button" class="btn btn-warning" id="btnGuardarTest">
                        <i class="fas fa-flask"></i> Guardar Test
                    </button>
                    <button type="button" class="btn btn-info" id="btnRecargar">
                        <i class="fas fa-sync"></i> Recargar
                    </button>
                    <a href="/admin/roles" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>

                <div id="alertContainer"></div>

                <div class="table-container p-3">
                    <form id="permissionMatrixForm">
                        <table class="table table-bordered table-sm permission-table">
                            <thead>
                                <tr>
                                    <th style="width: 250px;">Permiso</th>
                                    @foreach($roles as $role)
                                        <th class="text-center" style="width: 120px;">
                                            {{ $role->nombre }}<br>
                                            <small class="fw-normal">ID: {{ $role->id }}</small><br>
                                            <span class="badge bg-info" id="count-{{ $role->id }}">0/{{ $permissions->count() }}</span>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($permissionsByModule as $modulo => $permisos)
                                    <tr class="module-header">
                                        <td colspan="{{ $roles->count() + 1 }}">
                                            <strong><i class="fas fa-folder"></i> {{ ucfirst($modulo) }}</strong>
                                        </td>
                                    </tr>
                                    @foreach($permisos as $permission)
                                        <tr>
                                            <td>
                                                <strong>{{ $permission->nombre }}</strong><br>
                                                <small class="text-muted">{{ $permission->tipo_permiso }}</small>
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
                    </form>
                </div>

                <!-- Resumen por roles -->
                <div class="row mt-4">
                    @foreach($roles as $role)
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">{{ $role->nombre }}</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-1">
                                        <strong>Permisos asignados:</strong> 
                                        <span id="summary-{{ $role->id }}">{{ count($matrix[$role->id] ?? []) }}</span> 
                                        / {{ $permissions->count() }}
                                    </p>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleRole({{ $role->id }})">
                                        <i class="fas fa-toggle-on"></i> Toggle All
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Debug inicial
        console.log('Matrix data:', @json($matrix));
        console.log('Roles data:', @json($roles->map(function($role) { 
            return ['id' => $role->id, 'nombre' => $role->nombre]; 
        })));

        // Función para mostrar alertas
        function showAlert(message, type = 'success') {
            const alertContainer = document.getElementById('alertContainer');
            const alert = document.createElement('div');
            alert.className = `alert alert-${type} alert-dismissible fade show`;
            alert.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            alertContainer.appendChild(alert);
            
            // Auto-remover después de 5 segundos
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.remove();
                }
            }, 5000);
        }

        // Toggle todos los permisos de un rol
        function toggleRole(roleId) {
            const checkboxes = document.querySelectorAll(`.role-${roleId}`);
            const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = !allChecked;
            });
            updateCounters();
        }

        // Actualizar contadores
        function updateCounters() {
            @foreach($roles as $role)
                const checkedCount{{ $role->id }} = document.querySelectorAll('.role-{{ $role->id }}:checked').length;
                const totalCount{{ $role->id }} = document.querySelectorAll('.role-{{ $role->id }}').length;
                document.getElementById('count-{{ $role->id }}').textContent = `${checkedCount{{ $role->id }}} / ${totalCount{{ $role->id }}}`;
                document.getElementById('summary-{{ $role->id }}').textContent = checkedCount{{ $role->id }};
            @endforeach
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM cargado - agregando event listeners');
            
            // Actualizar contadores cuando cambie algún checkbox
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('permission-checkbox')) {
                    updateCounters();
                }
            });

            // Botón guardar original
            document.getElementById('btnGuardarMatrix').addEventListener('click', function() {
                console.log('Botón Guardar Original clickeado');
                guardarMatrixOriginal();
            });

            // Botón guardar test
            document.getElementById('btnGuardarTest').addEventListener('click', function() {
                console.log('Botón Guardar Test clickeado');
                guardarMatrixTest();
            });

            // Botón recargar
            document.getElementById('btnRecargar').addEventListener('click', function() {
                window.location.reload();
            });

            // Inicializar contadores
            updateCounters();
            
            console.log('Event listeners agregados exitosamente');
        });

        // Función guardar original
        async function guardarMatrixOriginal() {
            const btn = document.getElementById('btnGuardarMatrix');
            const originalText = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

            try {
                const matrix = {};
                
                @foreach($roles as $role)
                    matrix[{{ $role->id }}] = [];
                    const role{{ $role->id }}Checkboxes = document.querySelectorAll('.role-{{ $role->id }}:checked');
                    role{{ $role->id }}Checkboxes.forEach(checkbox => {
                        matrix[{{ $role->id }}].push(parseInt(checkbox.value));
                    });
                @endforeach

                console.log('Matrix a enviar (original):', matrix);

                const response = await fetch('/admin/roles/permission-matrix/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ matrix: matrix })
                });

                const result = await response.json();
                console.log('Respuesta original:', result);

                if (result.success) {
                    showAlert('<i class="fas fa-check"></i> ' + result.message, 'success');
                } else {
                    showAlert('<i class="fas fa-exclamation-triangle"></i> Error: ' + (result.message || 'Error desconocido'), 'danger');
                }
            } catch (error) {
                console.error('Error en guardado original:', error);
                showAlert('<i class="fas fa-exclamation-triangle"></i> Error: ' + error.message, 'danger');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        }

        // Función guardar test
        async function guardarMatrixTest() {
            const btn = document.getElementById('btnGuardarTest');
            const originalText = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando Test...';

            try {
                const matrix = {};
                
                @foreach($roles as $role)
                    matrix[{{ $role->id }}] = [];
                    const role{{ $role->id }}Checkboxes = document.querySelectorAll('.role-{{ $role->id }}:checked');
                    role{{ $role->id }}Checkboxes.forEach(checkbox => {
                        matrix[{{ $role->id }}].push(parseInt(checkbox.value));
                    });
                @endforeach

                console.log('Matrix a enviar (test):', matrix);
                
                let messages = [];
                
                for (let roleId in matrix) {
                    const permissionIds = matrix[roleId].length > 0 ? matrix[roleId].join(',') : '0';
                    const url = '/test-save-permissions/' + roleId + '/' + permissionIds;
                    
                    const response = await fetch(url);
                    const result = await response.json();
                    
                    if (result.success) {
                        messages.push('✓ ' + result.message);
                    } else {
                        messages.push('✗ ' + result.message);
                    }
                }

                showAlert('<strong>Resultado Test:</strong><br>' + messages.join('<br>'), 'info');
                
            } catch (error) {
                console.error('Error en guardado test:', error);
                showAlert('<i class="fas fa-exclamation-triangle"></i> Error Test: ' + error.message, 'danger');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        }
    </script>
</body>
</html>