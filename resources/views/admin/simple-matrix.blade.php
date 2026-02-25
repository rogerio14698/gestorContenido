@extends('admin.layouts.app')

@section('title', 'Matrix Simple')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Matrix de Permisos - Versión Simple</h3>
                </div>
                <div class="card-body">
                    @php
                        $roles = App\Models\Role::with('permissions')->orderBy('nombre')->get();
                        $permissions = App\Models\Permission::where('activo', true)->orderBy('id')->take(10)->get();
                        
                        $matrix = [];
                        foreach ($roles as $role) {
                            $rolePermissions = $role->permissions->pluck('id')->toArray();
                            $matrix[$role->id] = $rolePermissions;
                        }
                    @endphp

                    <div id="debug-info" class="mb-3">
                        <strong>Debug Info:</strong> 
                        Roles: {{ $roles->count() }}, 
                        Permissions: {{ $permissions->count() }}, 
                        Matrix: {{ json_encode($matrix) }}
                    </div>

                    <form id="simpleMatrixForm">
                        @csrf
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Permiso</th>
                                    @foreach($roles as $role)
                                        <th>{{ $role->nombre }} (ID: {{ $role->id }})</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($permissions as $permission)
                                    <tr>
                                        <td>{{ $permission->nombre }} (ID: {{ $permission->id }})</td>
                                        @foreach($roles as $role)
                                            @php
                                                $isChecked = in_array($permission->id, $matrix[$role->id] ?? []);
                                            @endphp
                                            <td>
                                                <input type="checkbox" 
                                                       class="role-{{ $role->id }}"
                                                       name="matrix[{{ $role->id }}][]"
                                                       value="{{ $permission->id }}"
                                                       {{ $isChecked ? 'checked' : '' }}>
                                                <small>({{ $isChecked ? 'Marcado' : 'No marcado' }})</small>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <button type="button" onclick="saveMatrix()" class="btn btn-success">Guardar Cambios</button>
                        <button type="button" onclick="showCurrentState()" class="btn btn-info">Ver Estado Actual</button>
                    </form>

                    <div id="result" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showCurrentState() {
    const matrix = {};
    @foreach($roles as $role)
        matrix[{{ $role->id }}] = [];
        const checkboxes = document.querySelectorAll('.role-{{ $role->id }}:checked');
        checkboxes.forEach(cb => {
            matrix[{{ $role->id }}].push(parseInt(cb.value));
        });
    @endforeach
    
    console.log('Estado actual de la matrix:', matrix);
    document.getElementById('result').innerHTML = '<pre>' + JSON.stringify(matrix, null, 2) + '</pre>';
}

function saveMatrix() {
    const matrix = {};
    @foreach($roles as $role)
        matrix[{{ $role->id }}] = [];
        const checkboxes = document.querySelectorAll('.role-{{ $role->id }}:checked');
        checkboxes.forEach(cb => {
            matrix[{{ $role->id }}].push(parseInt(cb.value));
        });
    @endforeach

    console.log('Guardando matrix:', matrix);

    fetch('{{ route("admin.roles.permission-matrix.update") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
            resultDiv.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
            // Recargar después de 2 segundos para ver cambios
            setTimeout(() => location.reload(), 2000);
        } else {
            resultDiv.innerHTML = '<div class="alert alert-danger">Error: ' + data.message + '</div>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('result').innerHTML = '<div class="alert alert-danger">Error: ' + error.message + '</div>';
    });
}

// Mostrar estado inicial al cargar
document.addEventListener('DOMContentLoaded', showCurrentState);
</script>
@endsection