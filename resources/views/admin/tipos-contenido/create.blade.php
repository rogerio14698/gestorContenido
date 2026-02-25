@extends('admin.layouts.app')

@section('page-title', 'Crear Tipo de Contenido')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.tipos-contenido.index') }}">Tipos de Contenido</a></li>
    <li class="breadcrumb-item active">Crear</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <!-- Formulario principal -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Crear Nuevo Tipo de Contenido</h3>
                    </div>
                    <form action="{{ route('admin.tipos-contenido.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <!-- Tipo de Contenido -->
                            <div class="form-group">
                                <label for="tipo_contenido">Tipo de Contenido *</label>
                                <input type="text" 
                                       id="tipo_contenido" 
                                       name="tipo_contenido" 
                                       class="form-control @error('tipo_contenido') is-invalid @enderror" 
                                       value="{{ old('tipo_contenido') }}" 
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
                                          placeholder="Descripción opcional del tipo de contenido">{{ old('descripcion') }}</textarea>
                                @error('descripcion')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">
                                    Descripción detallada para identificar el uso de este tipo de contenido (opcional)
                                </small>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Tipo
                            </button>
                            <a href="{{ route('admin.tipos-contenido.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Panel lateral con información -->
            <div class="col-md-4">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Información</h3>
                    </div>
                    <div class="card-body">
                        <h6><i class="fas fa-info-circle"></i> ¿Qué son los tipos de contenido?</h6>
                        <p class="text-sm">
                            Los tipos de contenido permiten clasificar y organizar el contenido del sitio web en diferentes categorías como artículos, noticias, páginas, etc.
                        </p>
                        
                        <h6><i class="fas fa-lightbulb"></i> Consejos:</h6>
                        <ul class="text-sm">
                            <li>Usa nombres descriptivos y únicos</li>
                            <li>Piensa en cómo organizarás tu contenido</li>
                            <li>La descripción ayuda a otros usuarios a entender el propósito</li>
                        </ul>

                        <h6><i class="fas fa-exclamation-triangle"></i> Importante:</h6>
                        <p class="text-sm text-warning">
                            Una vez creado y usado, evita cambiar el nombre del tipo de contenido para mantener la consistencia.
                        </p>
                    </div>
                </div>

                <!-- Tipos existentes -->
                @php
                    $tiposExistentes = \App\Models\TipoContenido::select('tipo_contenido')->take(5)->get();
                @endphp
                @if($tiposExistentes->count() > 0)
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Tipos Existentes</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                @foreach($tiposExistentes as $tipo)
                                    <li><i class="fas fa-tag text-muted"></i> {{ $tipo->tipo_contenido }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
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
</script>
@endsection