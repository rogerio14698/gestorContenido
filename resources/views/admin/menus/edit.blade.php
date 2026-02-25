@extends('admin.layouts.app')

@section('title', 'Crear Menú')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                                <div class="col-sm-6">
                    <h1>Editar Menú</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.menus.index') }}">Menús</a></li>
                        <li class="breadcrumb-item active">Crear</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
        <div class="container-fluid">
            <section class="content">        <form action="{{ route('admin.menus.update', $menu) }}" method="POST">
            @csrf
            @method('PUT')
        
        <div class="row">
            <!-- Configuración Principal -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-cog"></i> Configuración Principal
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Menú Padre -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="parent_id">Menú Padre</label>
                                    <select name="parent_id" id="parent_id" class="form-control">
                                        <option value="">Menú Principal</option>
                                        @if(isset($menusParent))
                                            @foreach($menusParent as $menuParent)
                                                <option value="{{ $menuParent->id }}" {{ old('parent_id', $menu->parent_id) == $menuParent->id ? 'selected' : '' }}>
                                                    {{ $menuParent->titulo }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <!-- Tipo de Enlace -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tipo_enlace">Tipo de Enlace <span class="text-danger">*</span></label>
                                    <select name="tipo_enlace" id="tipo_enlace" class="form-control @error('tipo_enlace') is-invalid @enderror">
                                        <option value="">Seleccione una opción</option>
                                        <option value="contenido" {{ old('tipo_enlace', $menu->tipo_enlace) == 'contenido' ? 'selected' : '' }}>Enlace a contenido</option>
                                        <option value="url_externa" {{ old('tipo_enlace', $menu->tipo_enlace) == 'url_externa' ? 'selected' : '' }}>URL externa</option>
                                        <option value="ninguno" {{ old('tipo_enlace', $menu->tipo_enlace) == 'ninguno' ? 'selected' : '' }}>Sin enlace</option>
                                    </select>
                                    @error('tipo_enlace')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Configuración de Contenido -->
                        <div class="content-config" id="contenido-config" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tipo_contenido_id">Tipo de Contenido</label>
                                        <select name="tipo_contenido_id" id="tipo_contenido_id" class="form-control">
                                            <option value="">Seleccione un tipo</option>
                                            @if(isset($tiposContenido))
                                                @foreach($tiposContenido as $tipo)
                                                    <option value="{{ $tipo->id }}" {{ old('tipo_contenido_id', $menu->tipo_contenido_id) == $tipo->id ? 'selected' : '' }}>
                                                        {{ $tipo->nombre }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="content_id">Contenido Específico</label>
                                        <select name="content_id" id="content_id" class="form-control">
                                            <option value="">Seleccione primero un tipo</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Configuración de URL Externa -->
                        <div class="content-config" id="url-config" style="display: none;">
                            <div class="form-group">
                                <label for="url_externa">URL Externa</label>
                                <input type="url" name="url_externa" id="url_externa" class="form-control @error('url_externa') is-invalid @enderror" 
                                       value="{{ old('url_externa', $menu->url_externa) }}" placeholder="https://ejemplo.com">
                                @error('url_externa')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Icono -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="icon">Icono (Font Awesome)</label>
                                    <input type="text" name="icon" id="icon" class="form-control" 
                                           value="{{ old('icon', 'fas fa-link') }}" placeholder="fas fa-home">
                                </div>
                            </div>

                            <!-- Orden -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="orden">Orden</label>
                                    <input type="number" name="orden" id="orden" class="form-control" 
                                           value="{{ old('orden', $menu->orden) }}" min="1">
                                </div>
                            </div>

                            <!-- Opciones -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Opciones</label>
                                    <div class="form-check">
                                        <input type="checkbox" name="visible" id="visible" class="form-check-input" 
                                               value="1" {{ old('visible', $menu->visible) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="visible">Visible</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="abrir_nueva_ventana" id="abrir_nueva_ventana" 
                                               class="form-check-input" value="1" {{ old('abrir_nueva_ventana', $menu->abrir_nueva_ventana) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="abrir_nueva_ventana">Abrir en nueva ventana</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="menu_pie" id="menu_pie" class="form-check-input" 
                                               value="1" {{ old('menu_pie', $menu->menu_pie) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="menu_pie">Mostrar en pie de página</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Textos Multilingües -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-language"></i> Textos en Diferentes Idiomas
                        </h3>
                    </div>
                    <div class="card-body">
                        @if(isset($idiomas))
                            @foreach($idiomas as $idioma)
                                @php
                                    $textoExistente = $menu->textos->where('idioma_id', $idioma->id)->first();
                                @endphp
                                <div class="form-group">
                                    <label for="titulo_{{ $idioma->etiqueta }}">
                                        Título ({{ $idioma->nombre }})
                                        @if($idioma->es_principal)
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>
                                    <input type="text" 
                                           name="textos[{{ $idioma->id }}][titulo]" 
                                           id="titulo_{{ $idioma->etiqueta }}" 
                                           class="form-control @error('textos.'.$idioma->id.'.titulo') is-invalid @enderror" 
                                           value="{{ old('textos.'.$idioma->id.'.titulo', $textoExistente->titulo ?? '') }}" 
                                           placeholder="Título del menú en {{ $idioma->nombre }}">
                                    @error('textos.'.$idioma->id.'.titulo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                                                    <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Actualizar Menú
                            </button>
                        <a href="{{ route('admin.menus.index') }}" class="btn btn-secondary ml-2">
                            <i class="fas fa-arrow-left"></i> Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@stop

@push('scripts')
<script src="{{ asset('js/menu-create.js') }}"></script>

<script>
// Configuración específica para edición
document.addEventListener('DOMContentLoaded', function() {
    // Esperar a que el sistema base se cargue
    setTimeout(function() {
        console.log('🔧 MODO EDICIÓN - Configurando valores iniciales...');
        
        // Valores iniciales del menú
        const valoresIniciales = {
            tipoEnlace: '{{ $menu->tipo_enlace }}',
            tipoContenidoId: '{{ $menu->tipo_contenido_id ?? "" }}',
            contentId: '{{ $menu->content_id ?? "" }}'
        };
        
        console.log('Valores iniciales:', valoresIniciales);
        
        // Configurar tipo de enlace inicial
        const tipoEnlace = document.getElementById('tipo_enlace');
        if (tipoEnlace && valoresIniciales.tipoEnlace) {
            tipoEnlace.value = valoresIniciales.tipoEnlace;
            tipoEnlace.dispatchEvent(new Event('change'));
        }
        
        // Si hay tipo de contenido seleccionado, cargarlo
        if (valoresIniciales.tipoContenidoId) {
            setTimeout(function() {
                const tipoContenido = document.getElementById('tipo_contenido_id');
                if (tipoContenido) {
                    tipoContenido.value = valoresIniciales.tipoContenidoId;
                    tipoContenido.dispatchEvent(new Event('change'));
                    
                    // Después de cargar contenidos, seleccionar el específico
                    if (valoresIniciales.contentId) {
                        setTimeout(function() {
                            const contentSelect = document.getElementById('content_id');
                            if (contentSelect) {
                                contentSelect.value = valoresIniciales.contentId;
                                console.log('✅ Content ID inicial configurado:', valoresIniciales.contentId);
                            }
                        }, 1500); // Esperar a que se carguen los contenidos
                    }
                }
            }, 500);
        }
        
        console.log('✅ Configuración de edición completada');
    }, 1000); // Esperar a que el script base se cargue
});
</script>
@endpush