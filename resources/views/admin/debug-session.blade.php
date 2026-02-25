@extends('admin.layouts.app')

@section('title', 'Debug Session')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Debug de Sesión y Datos</h3>
                </div>
                <div class="card-body">
                    <h4>Usuario Actual:</h4>
                    @auth
                        <p><strong>ID:</strong> {{ auth()->id() }}</p>
                        <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                        <p><strong>Nombre:</strong> {{ auth()->user()->nombre ?? 'N/A' }}</p>
                        <p><strong>Role ID:</strong> {{ auth()->user()->role_id ?? 'N/A' }}</p>
                    @else
                        <p class="text-danger">NO HAY USUARIO AUTENTICADO</p>
                    @endauth

                    <h4>Datos de la Base:</h4>
                    @php
                        $roles = App\Models\Role::with('permissions')->get();
                        $permissions = App\Models\Permission::where('activo', true)->get();
                        $matrix = [];
                        foreach ($roles as $role) {
                            $rolePermissions = $role->permissions->pluck('id')->toArray();
                            $matrix[$role->id] = $rolePermissions;
                        }
                    @endphp
                    
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Roles ({{ $roles->count() }}):</h5>
                            @foreach($roles as $role)
                                <p>{{ $role->id }}. {{ $role->nombre }} - {{ $role->permissions->count() }} permisos</p>
                            @endforeach
                        </div>
                        
                        <div class="col-md-4">
                            <h5>Permisos ({{ $permissions->count() }}):</h5>
                            @foreach($permissions->take(5) as $permission)
                                <p>{{ $permission->id }}. {{ $permission->nombre }}</p>
                            @endforeach
                            @if($permissions->count() > 5)
                                <p>... y {{ $permissions->count() - 5 }} más</p>
                            @endif
                        </div>
                        
                        <div class="col-md-4">
                            <h5>Matrix:</h5>
                            @foreach($matrix as $roleId => $permissionIds)
                                <p><strong>Rol {{ $roleId }}:</strong> [{{ implode(', ', $permissionIds) }}]</p>
                            @endforeach
                        </div>
                    </div>

                    <h4>Test Checkboxes:</h4>
                    @if($roles->count() > 0 && $permissions->count() > 0)
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Permiso</th>
                                    @foreach($roles as $role)
                                        <th>{{ $role->nombre }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($permissions->take(5) as $permission)
                                    <tr>
                                        <td>{{ $permission->nombre }}</td>
                                        @foreach($roles as $role)
                                            @php
                                                $isChecked = in_array($permission->id, $matrix[$role->id] ?? []);
                                            @endphp
                                            <td>
                                                <input type="checkbox" 
                                                       value="{{ $permission->id }}"
                                                       {{ $isChecked ? 'checked' : '' }}>
                                                {{ $isChecked ? 'SÍ' : 'NO' }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                    <h4>Test CSRF:</h4>
                    <p><strong>CSRF Token:</strong> {{ csrf_token() }}</p>
                    <p><strong>Meta CSRF:</strong> <span id="csrf-meta"></span></p>

                    <h4>Acciones:</h4>
                    <button onclick="testMatrix()" class="btn btn-primary">Test Matrix AJAX</button>
                    <button onclick="location.reload()" class="btn btn-secondary">Reload Page</button>
                    <a href="{{ route('admin.roles.permission-matrix') }}" class="btn btn-info">Ir a Matrix Real</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    document.getElementById('csrf-meta').textContent = csrfMeta ? csrfMeta.getAttribute('content') : 'NO ENCONTRADO';
});

function testMatrix() {
    const matrix = {
        1: [1, 2],
        2: [3],
        3: [4]
    };
    
    console.log('Testing matrix save...', matrix);
    
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
        alert(data.success ? 'Éxito: ' + data.message : 'Error: ' + data.message);
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error: ' + error.message);
    });
}
</script>
@endsection