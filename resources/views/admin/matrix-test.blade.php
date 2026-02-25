<!DOCTYPE html>
<html>
<head>
    <title>Test Matrix - JavaScript Full</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .matrix { border-collapse: collapse; width: 100%; }
        .matrix th, .matrix td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        .matrix th { background-color: #f2f2f2; }
        .controls { margin: 20px 0; }
        button { padding: 10px 20px; margin: 5px; cursor: pointer; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
    </style>
</head>
<body>
    <h1>Matrix Test - JavaScript Complete</h1>
    
    <div class="controls">
        <button onclick="loadMatrix()">Cargar Matrix</button>
        <button onclick="saveChanges()">Guardar Cambios</button>
        <button onclick="resetMatrix()">Reset</button>
        <button onclick="testSpecific()">Test Específico</button>
    </div>

    <div id="status"></div>
    
    <div id="matrix-container">
        <p>Presiona "Cargar Matrix" para empezar...</p>
    </div>

    <script>
        let matrixData = {};
        let originalMatrix = {};

        // Cargar datos desde API
        async function loadMatrix() {
            try {
                updateStatus('Cargando datos...', 'info');
                
                const response = await fetch('/api/matrix-data');
                const data = await response.json();
                
                matrixData = data;
                originalMatrix = JSON.parse(JSON.stringify(data.matrix)); // Deep copy
                
                renderMatrix();
                updateStatus('Datos cargados correctamente. Total roles: ' + data.roles.length + ', permisos: ' + data.permissions.length, 'success');
                
                console.log('Matrix data:', data);
            } catch (error) {
                updateStatus('Error cargando datos: ' + error.message, 'error');
                console.error('Error:', error);
            }
        }

        // Renderizar matriz
        function renderMatrix() {
            if (!matrixData.roles || !matrixData.permissions) return;
            
            let html = '<table class="matrix">';
            
            // Encabezados
            html += '<thead><tr><th>Rol \\ Permiso</th>';
            for (let permission of matrixData.permissions.slice(0, 10)) { // Solo primeros 10 para debug
                html += '<th title="' + permission.modulo + '">' + permission.nombre + '<br><small>ID:' + permission.id + '</small></th>';
            }
            html += '</tr></thead>';
            
            // Filas de roles
            html += '<tbody>';
            for (let role of matrixData.roles) {
                html += '<tr>';
                html += '<th>' + role.nombre + '<br><small>ID:' + role.id + '</small></th>';
                
                for (let permission of matrixData.permissions.slice(0, 10)) {
                    const isChecked = (matrixData.matrix[role.id] || []).includes(permission.id);
                    const checkedAttr = isChecked ? 'checked' : '';
                    
                    html += '<td>';
                    html += '<input type="checkbox" ';
                    html += 'data-role="' + role.id + '" ';
                    html += 'data-permission="' + permission.id + '" ';
                    html += checkedAttr + ' ';
                    html += 'onchange="updateMatrix(' + role.id + ', ' + permission.id + ', this.checked)"';
                    html += '>';
                    html += '<br><small style="color: ' + (isChecked ? 'green' : 'red') + '">' + (isChecked ? 'SÍ' : 'NO') + '</small>';
                    html += '</td>';
                }
                
                html += '</tr>';
            }
            html += '</tbody></table>';
            
            html += '<div style="margin-top: 20px; background: #f9f9f9; padding: 10px;">';
            html += '<strong>Estado actual de la matriz:</strong><br>';
            html += '<pre>' + JSON.stringify(matrixData.matrix, null, 2) + '</pre>';
            html += '</div>';
            
            document.getElementById('matrix-container').innerHTML = html;
        }

        // Actualizar matriz en memoria
        function updateMatrix(roleId, permissionId, isChecked) {
            if (!matrixData.matrix[roleId]) {
                matrixData.matrix[roleId] = [];
            }
            
            if (isChecked) {
                if (!matrixData.matrix[roleId].includes(permissionId)) {
                    matrixData.matrix[roleId].push(permissionId);
                }
            } else {
                matrixData.matrix[roleId] = matrixData.matrix[roleId].filter(id => id !== permissionId);
            }
            
            // Actualizar la vista del JSON
            const statusDiv = document.querySelector('#matrix-container pre');
            if (statusDiv) {
                statusDiv.textContent = JSON.stringify(matrixData.matrix, null, 2);
            }
            
            updateStatus('Matriz actualizada: Rol ' + roleId + ' permiso ' + permissionId + ' = ' + (isChecked ? 'SI' : 'NO'), 'info');
            console.log('Updated matrix:', matrixData.matrix);
        }

        // Guardar cambios usando GET para evitar CSRF
        async function saveChanges() {
            try {
                updateStatus('Guardando cambios...', 'info');
                
                let savePromises = [];
                let messages = [];
                
                for (let roleId in matrixData.matrix) {
                    const permissionIds = matrixData.matrix[roleId].length > 0 ? matrixData.matrix[roleId].join(',') : '0';
                    const url = '/test-save-permissions/' + roleId + '/' + permissionIds;
                    
                    messages.push('Guardando rol ' + roleId + ' con permisos: [' + permissionIds + ']');
                    savePromises.push(fetch(url).then(r => r.json()));
                }
                
                const results = await Promise.all(savePromises);
                
                let allSuccess = true;
                let finalMessages = [];
                
                for (let i = 0; i < results.length; i++) {
                    const result = results[i];
                    if (result.success) {
                        finalMessages.push('✓ ' + result.message);
                    } else {
                        finalMessages.push('✗ ' + result.message);
                        allSuccess = false;
                    }
                }
                
                updateStatus(finalMessages.join('<br>'), allSuccess ? 'success' : 'error');
                
                if (allSuccess) {
                    updateStatus('¡Todos los cambios guardados! Recargando en 2 segundos...', 'success');
                    setTimeout(loadMatrix, 2000);
                }
                
            } catch (error) {
                updateStatus('Error guardando: ' + error.message, 'error');
                console.error('Error:', error);
            }
        }

        // Test específico
        async function testSpecific() {
            try {
                updateStatus('Ejecutando test específico...', 'info');
                
                // Test: Asignar permisos 1,2 al rol 1
                const response = await fetch('/test-save-permissions/1/1,2');
                const result = await response.json();
                
                updateStatus('Test específico: ' + result.message, result.success ? 'success' : 'error');
                
                if (result.success) {
                    setTimeout(loadMatrix, 1000);
                }
                
            } catch (error) {
                updateStatus('Error en test específico: ' + error.message, 'error');
            }
        }

        // Reset a valores originales
        function resetMatrix() {
            if (originalMatrix) {
                matrixData.matrix = JSON.parse(JSON.stringify(originalMatrix));
                renderMatrix();
                updateStatus('Matrix reseteada a valores originales', 'info');
            } else {
                updateStatus('No hay matriz original cargada', 'error');
            }
        }

        // Helper para actualizar status
        function updateStatus(message, type) {
            const statusDiv = document.getElementById('status');
            statusDiv.innerHTML = '<p class="' + type + '">' + message + '</p>';
        }

        // Cargar automáticamente al iniciar
        document.addEventListener('DOMContentLoaded', function() {
            updateStatus('Página cargada. Presiona "Cargar Matrix" para empezar.', 'info');
        });
    </script>
</body>
</html>