<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Matrix Debug Clean</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .checked { color: green; font-weight: bold; }
        .unchecked { color: red; }
    </style>
</head>
<body>
    <h1>Matrix de Permisos - Debug Clean</h1>
    
    @php
        $roles = App\Models\Role::with('permissions')->orderBy('nombre')->get();
        $permissions = App\Models\Permission::where('activo', true)->orderBy('id')->take(10)->get();
        
        $matrix = [];
        foreach ($roles as $role) {
            $rolePermissions = $role->permissions->pluck('id')->toArray();
            $matrix[$role->id] = $rolePermissions;
        }
    @endphp

    <div style="background: #f0f0f0; padding: 10px; margin: 10px 0;">
        <strong>Debug Info:</strong><br>
        Roles: {{ $roles->count() }}<br>
        Permissions: {{ $permissions->count() }}<br>
        Matrix: {{ json_encode($matrix) }}<br>
        CSRF: {{ csrf_token() }}
    </div>

    <form id="cleanForm">
        <table>
            <thead>
                <tr>
                    <th style="width: 200px;">Permiso</th>
                    @foreach($roles as $role)
                        <th>{{ $role->nombre }}<br><small>(ID: {{ $role->id }})</small></th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($permissions as $permission)
                    <tr>
                        <td>
                            <strong>{{ $permission->nombre }}</strong><br>
                            <small>ID: {{ $permission->id }} | Módulo: {{ $permission->modulo }}</small>
                        </td>
                        @foreach($roles as $role)
                            @php
                                $rolePerms = $matrix[$role->id] ?? [];
                                $isChecked = in_array($permission->id, $rolePerms);
                            @endphp
                            <td style="text-align: center;">
                                <input type="checkbox" 
                                       class="role-{{ $role->id }}"
                                       name="matrix[{{ $role->id }}][]"
                                       value="{{ $permission->id }}"
                                       id="perm_{{ $permission->id }}_role_{{ $role->id }}"
                                       {{ $isChecked ? 'checked' : '' }}>
                                <br>
                                <small class="{{ $isChecked ? 'checked' : 'unchecked' }}">
                                    {{ $isChecked ? 'MARCADO' : 'no marcado' }}
                                </small>
                                <br>
                                <small>Role perms: [{{ implode(',', $rolePerms) }}]</small>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin: 20px 0;">
            <button type="button" onclick="checkStatus()">Ver Estado Actual</button>
            <button type="button" onclick="saveChanges()" style="background: green; color: white; padding: 10px;">Guardar Cambios</button>
            <button type="button" onclick="location.reload()">Recargar Página</button>
        </div>

        <div id="status" style="background: #e0e0e0; padding: 10px; margin: 10px 0;"></div>
        <div id="result" style="margin: 10px 0;"></div>
    </form>

    <script>
    // Datos generados por PHP
    const rolesData = @json($roles->map(function($role) { return ['id' => $role->id]; }));
    const matrixData = @json($matrix);
    const updateUrl = '/test-matrix-save'; // Ruta temporal sin auth
    const csrfToken = '{{ csrf_token() }}';

    console.log('Datos iniciales cargados:', {
        rolesData: rolesData,
        matrixData: matrixData,
        updateUrl: updateUrl
    });

    function checkStatus() {
        const matrix = {};
        
        rolesData.forEach(role => {
            matrix[role.id] = [];
            const checkboxes = document.querySelectorAll('.role-' + role.id + ':checked');
            console.log('Role ' + role.id + ' checked boxes:', checkboxes.length);
            checkboxes.forEach(cb => {
                matrix[role.id].push(parseInt(cb.value));
            });
        });
        
        console.log('Current matrix state:', matrix);
        document.getElementById('status').innerHTML = '<pre>' + JSON.stringify(matrix, null, 2) + '</pre>';
        return matrix;
    }

    function saveChanges() {
        try {
            console.log('saveChanges function called');
            const matrix = checkStatus(); // Mostrar estado primero

            console.log('Saving matrix:', matrix);

            fetch(updateUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ matrix: matrix })
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                const resultDiv = document.getElementById('result');
                if (data.success) {
                    resultDiv.innerHTML = '<div style="background: green; color: white; padding: 10px;">' + data.message + '</div>';
                    setTimeout(() => location.reload(), 3000);
                } else {
                    resultDiv.innerHTML = '<div style="background: red; color: white; padding: 10px;">Error: ' + data.message + '</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('result').innerHTML = '<div style="background: red; color: white; padding: 10px;">Error: ' + error.message + '</div>';
            });
        } catch (error) {
            console.error('Error in saveChanges function:', error);
            alert('Error: ' + error.message);
        }
    }

    // Verificar estado al cargar
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Página cargada, verificando estado inicial...');
        checkStatus();
    });
    </script>
</body>
</html>