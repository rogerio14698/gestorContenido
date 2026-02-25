<!DOCTYPE html>
<html>
<head>
    <title>Matrix Debug - Event Listeners</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .debug { background: #f0f0f0; padding: 10px; margin: 10px 0; border: 1px solid #ddd; }
        .error { background: #ffebee; border-color: #f44336; }
        .success { background: #e8f5e9; border-color: #4caf50; }
        button { padding: 10px 20px; margin: 5px; cursor: pointer; }
        .matrix-data { max-height: 200px; overflow-y: auto; }
    </style>
</head>
<body>
    <h1>Matrix Debug - Verificar Event Listeners</h1>
    
    @php
        $roles = App\Models\Role::with('permissions')->orderBy('nombre')->get();
        $permissions = App\Models\Permission::where('activo', true)->orderBy('id')->take(5)->get();
        
        $matrix = [];
        foreach ($roles as $role) {
            $rolePermissions = $role->permissions->pluck('id')->toArray();
            $matrix[$role->id] = $rolePermissions;
        }
    @endphp

    <div class="debug">
        <strong>Datos del servidor:</strong><br>
        Roles: {{ $roles->count() }}<br>
        Permissions (primeros 5): {{ $permissions->count() }}<br>
        Matrix: {{ json_encode($matrix) }}
    </div>

    <div>
        <button id="testButton1">Test Button 1</button>
        <button id="testButton2" onclick="testFunction2()">Test Button 2 (onclick)</button>
        <button id="btnGuardarTest">Guardar Test</button>
    </div>

    <div id="output" class="debug"></div>

    <!-- Checkboxes de prueba -->
    <h3>Checkboxes de Prueba:</h3>
    <form id="testForm">
        @foreach($roles as $role)
            <h4>{{ $role->nombre }} (ID: {{ $role->id }})</h4>
            @foreach($permissions as $permission)
                @php
                    $isChecked = in_array($permission->id, $matrix[$role->id] ?? []);
                @endphp
                <label>
                    <input type="checkbox" 
                           class="permission-checkbox role-{{ $role->id }}" 
                           name="matrix[{{ $role->id }}][]" 
                           value="{{ $permission->id }}"
                           {{ $isChecked ? 'checked' : '' }}>
                    {{ $permission->nombre }} ({{ $isChecked ? 'MARCADO' : 'NO MARCADO' }})
                </label><br>
            @endforeach
            <br>
        @endforeach
    </form>

    <script>
        // Log de todo lo que pasa
        function log(message, type = 'info') {
            console.log(message);
            const output = document.getElementById('output');
            const div = document.createElement('div');
            div.className = 'debug ' + (type === 'error' ? 'error' : type === 'success' ? 'success' : '');
            div.innerHTML = '<strong>' + new Date().toLocaleTimeString() + '</strong>: ' + message;
            output.appendChild(div);
        }

        // Test básico de event listeners
        document.addEventListener('DOMContentLoaded', function() {
            log('DOMContentLoaded ejecutado', 'success');
            
            // Test Button 1 - addEventListener
            const btn1 = document.getElementById('testButton1');
            if (btn1) {
                btn1.addEventListener('click', function() {
                    log('Test Button 1 clickeado!', 'success');
                });
                log('Event listener agregado a Test Button 1');
            } else {
                log('Test Button 1 no encontrado', 'error');
            }

            // Test Button Guardar
            const btnGuardar = document.getElementById('btnGuardarTest');
            if (btnGuardar) {
                btnGuardar.addEventListener('click', async function() {
                    log('Botón Guardar Test clickeado!', 'success');
                    
                    try {
                        // Construir matriz
                        const matrix = {};
                        @foreach($roles as $role)
                            matrix[{{ $role->id }}] = [];
                            const role{{ $role->id }}Checkboxes = document.querySelectorAll('.role-{{ $role->id }}:checked');
                            log('Rol {{ $role->id }}: ' + role{{ $role->id }}Checkboxes.length + ' checkboxes marcados');
                            role{{ $role->id }}Checkboxes.forEach(checkbox => {
                                matrix[{{ $role->id }}].push(parseInt(checkbox.value));
                            });
                        @endforeach
                        
                        log('Matrix construida: ' + JSON.stringify(matrix));
                        
                        // Probar fetch
                        for (let roleId in matrix) {
                            const permissionIds = matrix[roleId].length > 0 ? matrix[roleId].join(',') : '0';
                            const url = '/test-save-permissions/' + roleId + '/' + permissionIds;
                            
                            log('Enviando: ' + url);
                            
                            const response = await fetch(url);
                            const result = await response.json();
                            
                            log('Respuesta: ' + JSON.stringify(result), result.success ? 'success' : 'error');
                        }
                        
                    } catch (error) {
                        log('Error: ' + error.message, 'error');
                    }
                });
                log('Event listener agregado a btnGuardarTest');
            } else {
                log('btnGuardarTest no encontrado', 'error');
            }
        });

        // Test Function 2 - onclick directo
        function testFunction2() {
            log('Test Function 2 ejecutada!', 'success');
        }

        // Log de errores globales
        window.addEventListener('error', function(e) {
            log('Error JavaScript: ' + e.message + ' en ' + e.filename + ':' + e.lineno, 'error');
        });

        log('Script cargado');
    </script>
</body>
</html>