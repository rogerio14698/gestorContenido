@extends('admin.layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Editar Rol</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
                        <li class="breadcrumb-item active">Editar</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Editar Rol: {{ $role->nombre }}</h3>
                        </div>

                        <form action="{{ route('admin.roles.update', $role) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nombre">Nombre del Rol <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   name="nombre" 
                                                   id="nombre" 
                                                   class="form-control @error('nombre') is-invalid @enderror" 
                                                   value="{{ old('nombre', $role->nombre) }}" 
                                                   placeholder="Ej: Editor"
                                                   required>
                                            @error('nombre')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="slug">Slug <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   name="slug" 
                                                   id="slug" 
                                                   class="form-control @error('slug') is-invalid @enderror" 
                                                   value="{{ old('slug', $role->slug) }}" 
                                                   placeholder="Ej: editor">
                                            @error('slug')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                Se puede editar manualmente o se generará automáticamente.
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="descripcion">Descripción</label>
                                    <textarea name="descripcion" 
                                              id="descripcion" 
                                              class="form-control @error('descripcion') is-invalid @enderror" 
                                              rows="3" 
                                              placeholder="Descripción del rol y sus responsabilidades...">{{ old('descripcion', $role->descripcion) }}</textarea>
                                    @error('descripcion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" 
                                               name="activo" 
                                               id="activo" 
                                               class="form-check-input" 
                                               value="1" 
                                               {{ old('activo', $role->activo) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="activo">
                                            Rol activo
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Los roles inactivos no podrán ser asignados a usuarios.
                                    </small>
                                </div>

                                <!-- Información del rol -->
                                @if($role->slug === 'administrador')
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong>Rol de Sistema:</strong> Este es el rol administrador. Ten cuidado al modificar sus permisos.
                                    </div>
                                @endif

                                <hr>

                                <!-- Sección de Permisos -->
                                <h5 class="mb-3">Permisos del Rol</h5>
                                <p class="text-muted">Selecciona los permisos que tendrá este rol:</p>

                                @if($permissions->count() > 0)
                                    @php
                                        $permisosPorModulo = $permissions->groupBy('modulo');
                                    @endphp
                                    
                                    <div class="row">
                                        @foreach($permisosPorModulo as $modulo => $permisos)
                                            <div class="col-md-6 mb-4">
                                                <div class="card card-outline card-primary">
                                                    <div class="card-header">
                                                        <h6 class="card-title mb-0">
                                                            <i class="fas fa-folder"></i>
                                                            {{ ucfirst($modulo) }}
                                                            <small class="text-muted">({{ $permisos->count() }} permisos)</small>
                                                        </h6>
                                                        <div class="card-tools">
                                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                    onclick="toggleModulePermissions('{{ $modulo }}')">
                                                                Seleccionar todo
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        @foreach($permisos as $permiso)
                                                            <div class="form-check mb-2">
                                                                <input type="checkbox" 
                                                                       name="permissions[]" 
                                                                       value="{{ $permiso->id }}" 
                                                                       class="form-check-input permission-checkbox module-{{ $modulo }}" 
                                                                       id="permission_{{ $permiso->id }}"
                                                                       {{ in_array($permiso->id, old('permissions', $selectedPermissions)) ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="permission_{{ $permiso->id }}">
                                                                    <strong>{{ $permiso->nombre }}</strong>
                                                                    <small class="text-muted d-block">
                                                                        {{ $permiso->tipo_permiso }} - {{ $permiso->descripcion }}
                                                                    </small>
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        No hay permisos disponibles. 
                                        <a href="{{ route('admin.permissions.create') }}" class="alert-link">Crear algunos permisos primero</a>.
                                    </div>
                                @endif
                            </div>

                            <div class="card-footer">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Volver
                                    </a>
                                    <div>
                                        <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-info mr-2">
                                            <i class="fas fa-eye"></i> Ver Detalles
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Actualizar Rol
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Información adicional -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Información del Rol</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <p><strong>ID:</strong> {{ $role->id }}</p>
                                    <p><strong>Slug:</strong> <code>{{ $role->slug }}</code></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Usuarios con este rol:</strong> {{ $role->users->count() }}</p>
                                    <p><strong>Permisos asignados:</strong> {{ $role->permissions->count() }}</p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Creado:</strong> {{ $role->created_at->format('d/m/Y H:i') }}</p>
                                    <p><strong>Actualizado:</strong> {{ $role->updated_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
    // Generar slug automáticamente
    document.getElementById('nombre').addEventListener('input', function() {
        const nombre = this.value;
        const slug = nombre.toLowerCase()
            .replace(/[^\w\s-]/g, '') // Remover caracteres especiales
            .replace(/[\s_-]+/g, '-') // Reemplazar espacios y guiones por guión simple
            .replace(/^-+|-+$/g, ''); // Remover guiones del inicio y final
        
        document.getElementById('slug').value = slug;
    });

    // Función para seleccionar/deseleccionar todos los permisos de un módulo
    function toggleModulePermissions(modulo) {
        const checkboxes = document.querySelectorAll(`.module-${modulo}`);
        const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = !allChecked;
        });
        
        updateSelectAllText(modulo);
    }

    // Actualizar texto del botón "Seleccionar todo"
    function updateSelectAllText(modulo) {
        const checkboxes = document.querySelectorAll(`.module-${modulo}`);
        const checkedCount = document.querySelectorAll(`.module-${modulo}:checked`).length;
        const button = document.querySelector(`[onclick="toggleModulePermissions('${modulo}')"]`);
        
        if (checkedCount === checkboxes.length) {
            button.textContent = 'Deseleccionar todo';
        } else {
            button.textContent = 'Seleccionar todo';
        }
    }

    // Inicializar textos de botones
    document.addEventListener('DOMContentLoaded', function() {
        @foreach($permisosPorModulo ?? [] as $modulo => $permisos)
            updateSelectAllText('{{ $modulo }}');
        @endforeach
    });
</script>
@endsection