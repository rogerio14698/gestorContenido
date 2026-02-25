@extends('admin.layouts.app')

@section('page-title', 'Editar Tipo de Contenido')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.tipos-contenido.index') }}">Tipos de Contenido</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <!-- Formulario principal -->
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">Editar Tipo de Contenido: {{ $tipo->tipo_contenido }}</h3>
                    </div>
                    <form action="{{ route('admin.tipos-contenido.update', $tipo->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <!-- Tipo de Contenido -->
                            <div class="form-group">
                                <label for="tipo_contenido">Tipo de Contenido *</label>
                                <input type="text" 
                                       id="tipo_contenido" 
                                       name="tipo_contenido" 
                                       class="form-control @error('tipo_contenido') is-invalid @enderror" 
                                       value="{{ old('tipo_contenido', $tipo->tipo_contenido) }}" 
                                       placeholder="Ej: Artículo, Noticia, Página, etc."
                                       required>
                                @error('tipo_contenido')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">
                                    Nombre identificativo del tipo de contenido
                                </small>
                            </div>

                            <!-- Descripción -->
                            <div class="form-group">
                                <label for="descripcion">Descripción</label>
                                <textarea id="descripcion" 
                                          name="descripcion" 
                                          class="form-control @error('descripcion') is-invalid @enderror" 
                                          rows="3" 
                                          placeholder="Descripción opcional del tipo de contenido">{{ old('descripcion', $tipo->descripcion) }}</textarea>
                                @error('descripcion')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">
                                    Descripción detallada para identificar el uso de este tipo de contenido (opcional)
                                </small>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Actualizar Tipo
                            </button>
                            <a href="{{ route('admin.tipos-contenido.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <a href="{{ route('admin.tipos-contenido.show', $tipo->id) }}" class="btn btn-info">
                                <i class="fas fa-eye"></i> Ver Detalles
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Panel lateral con información -->
            <div class="col-md-4">
                <!-- Información del tipo -->
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Información del Tipo</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>ID:</strong></td>
                                <td>{{ $tipo->id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Creado:</strong></td>
                                <td>{{ $tipo->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Actualizado:</strong></td>
                                <td>{{ $tipo->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Contenidos:</strong></td>
                                <td>
                                    @php
                                        $contenidos = \App\Models\TextoIdioma::where('tipo_contenido_id', $tipo->id)->count();
                                    @endphp
                                    <span class="badge badge-{{ $contenidos > 0 ? 'success' : 'light' }}">
                                        {{ $contenidos }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Advertencias -->
                @php
                    $contenidos = \App\Models\TextoIdioma::where('tipo_contenido_id', $tipo->id)->count();
                @endphp
                @if($contenidos > 0)
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">Atención</h3>
                        </div>
                        <div class="card-body">
                            <p class="text-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                Este tipo de contenido tiene <strong>{{ $contenidos }}</strong> contenidos asociados.
                            </p>
                            <p class="text-sm">
                                Los cambios en el nombre afectarán la visualización de todos los contenidos asociados.
                            </p>
                        </div>
                    </div>
                @endif

                <!-- Consejos -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Consejos</h3>
                    </div>
                    <div class="card-body">
                        <ul class="text-sm">
                            <li>Mantén nombres descriptivos y únicos</li>
                            <li>Si cambias el nombre, asegúrate de que siga siendo comprensible</li>
                            <li>La descripción ayuda a identificar el propósito del tipo</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Auto-format del campo tipo_contenido
    document.getElementById('tipo_contenido').addEventListener('input', function(e) {
        // Capitalizar primera letra de cada palabra
        this.value = this.value.toLowerCase().replace(/\b\w/g, l => l.toUpperCase());
    });

    // Validación en tiempo real
    document.getElementById('tipo_contenido').addEventListener('blur', function() {
        const valor = this.value.trim();
        if (valor.length < 2) {
            this.classList.add('is-invalid');
            if (!this.parentNode.querySelector('.invalid-feedback')) {
                const feedback = document.createElement('span');
                feedback.className = 'invalid-feedback';
                feedback.textContent = 'El tipo de contenido debe tener al menos 2 caracteres';
                this.parentNode.appendChild(feedback);
            }
        } else {
            this.classList.remove('is-invalid');
            const feedback = this.parentNode.querySelector('.invalid-feedback');
            if (feedback && feedback.textContent.includes('al menos 2 caracteres')) {
                feedback.remove();
            }
        }
    });

    // Confirmar cambios si hay contenidos asociados
    @if($contenidos > 0)
        document.querySelector('form').addEventListener('submit', function(e) {
            const tipoOriginal = '{{ $tipo->tipo_contenido }}';
            const tipoNuevo = document.getElementById('tipo_contenido').value;
            
            if (tipoOriginal !== tipoNuevo) {
                if (!confirm(`¿Estás seguro de cambiar "${tipoOriginal}" por "${tipoNuevo}"? Esto afectará a {{ $contenidos }} contenidos.`)) {
                    e.preventDefault();
                    return false;
                }
            }
        });
    @endif
</script>
@endsection