@extends('admin.layouts.app')

@section('title', 'Crear Contenido')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Crear Contenido</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.contents.index') }}">Contenidos</a></li>
                        <li class="breadcrumb-item active">Crear</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <!-- Mostrar errores de validación -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <h5><i class="fas fa-exclamation-triangle"></i> Errores de validación:</h5>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.contents.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <!-- Información principal -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Información Principal</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tipo_contenido">Tipo de Contenido *</label>
                                        <select name="tipo_contenido" id="tipo_contenido" class="form-control @error('tipo_contenido') is-invalid @enderror" required>
                                            <option value="">Seleccionar tipo</option>
                                            @foreach($tiposContenido as $tipo)
                                                <option value="{{ strtolower($tipo->tipo_contenido) }}" {{ old('tipo_contenido') == strtolower($tipo->tipo_contenido) ? 'selected' : '' }}>
                                                    {{ $tipo->tipo_contenido }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('tipo_contenido')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Los tipos se gestionan desde <a href="{{ route('admin.tipos-contenido.index') }}" target="_blank">Tipos de Contenido</a>
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fecha_publicacion">Fecha de Publicación</label>
                                        <input type="date" name="fecha_publicacion" id="fecha_publicacion" 
                                               class="form-control @error('fecha_publicacion') is-invalid @enderror"
                                               value="{{ old('fecha_publicacion') }}">
                                        @error('fecha_publicacion')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="lugar">Lugar</label>
                                        <input type="text" name="lugar" id="lugar" 
                                               class="form-control @error('lugar') is-invalid @enderror"
                                               value="{{ old('lugar') }}" placeholder="Ej: Avilés, Gijón...">
                                        @error('lugar')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="orden">Orden</label>
                                        <input type="number" name="orden" id="orden" 
                                               class="form-control @error('orden') is-invalid @enderror"
                                               value="{{ old('orden', 0) }}">
                                        @error('orden')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contenido multiidioma -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Contenido en Múltiples Idiomas</h3>
                            <p class="card-text mt-2 mb-0">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    Solo es obligatorio completar el contenido en <strong>Español</strong>. 
                                    El contenido en <strong>Asturianu</strong> es opcional.
                                </small>
                            </p>
                        </div>
                        <div class="card-body">
                            <!-- Tabs de idiomas -->
                            <ul class="nav nav-tabs" id="idiomasTabs" role="tablist">
                                @foreach($idiomas as $index => $idioma)
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link {{ $index == 0 ? 'active' : '' }}" 
                                           id="idioma-{{ $idioma->id }}-tab" 
                                           data-bs-toggle="tab" 
                                           href="#idioma-{{ $idioma->id }}" 
                                           role="tab">
                                            {{ strtoupper($idioma->etiqueta) }} - {{ $idioma->nombre }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>

                            <!-- Contenido de tabs -->
                            <div class="tab-content mt-3" id="idiomasTabsContent">
                                @foreach($idiomas as $index => $idioma)
                                    <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}" 
                                         id="idioma-{{ $idioma->id }}" 
                                         role="tabpanel">
                                        
                                        <div class="form-group">
                                            <label for="textos_{{ $idioma->id }}_titulo">
                                                Título {{ $index == 0 ? '*' : '' }}
                                                @if($index == 0)
                                                    <small class="text-muted">(Obligatorio)</small>
                                                @else
                                                    <small class="text-muted">(Opcional)</small>
                                                @endif
                                            </label>
                                            <input type="text" 
                                                   name="textos[{{ $idioma->id }}][titulo]" 
                                                   id="textos_{{ $idioma->id }}_titulo"
                                                   class="form-control @error('textos.'.$idioma->id.'.titulo') is-invalid @enderror"
                                                   value="{{ old('textos.'.$idioma->id.'.titulo') }}"
                                                   onkeyup="generateSlug({{ $idioma->id }})">
                                            @error('textos.'.$idioma->id.'.titulo')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="textos_{{ $idioma->id }}_slug">URL Amigable (Slug)</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">/{{ $idioma->codigo }}/</span>
                                                </div>
                                                <input type="text" 
                                                       name="textos[{{ $idioma->id }}][slug]" 
                                                       id="textos_{{ $idioma->id }}_slug"
                                                       class="form-control @error('textos.'.$idioma->id.'.slug') is-invalid @enderror"
                                                       value="{{ old('textos.'.$idioma->id.'.slug') }}"
                                                       placeholder="se-genera-automaticamente-del-titulo">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-outline-secondary" 
                                                            onclick="generateSlug({{ $idioma->id }})" 
                                                            title="Regenerar desde título">
                                                        <i class="fas fa-sync"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">
                                                Se genera automáticamente del título. Solo usar letras, números y guiones.
                                            </small>
                                            @error('textos.'.$idioma->id.'.slug')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="textos_{{ $idioma->id }}_subtitulo">Subtítulo</label>
                                            <input type="text" 
                                                   name="textos[{{ $idioma->id }}][subtitulo]" 
                                                   id="textos_{{ $idioma->id }}_subtitulo"
                                                   class="form-control"
                                                   value="{{ old('textos.'.$idioma->id.'.subtitulo') }}">
                                        </div>

                                        <div class="form-group">
                                            <label for="textos_{{ $idioma->id }}_resumen">Resumen</label>
                                            <textarea name="textos[{{ $idioma->id }}][resumen]" 
                                                      id="textos_{{ $idioma->id }}_resumen"
                                                      class="form-control tinymce-simple" 
                                                      rows="3">{{ old('textos.'.$idioma->id.'.resumen') }}</textarea>
                                            <small class="text-muted">Resumen corto que aparecerá en listados</small>
                                        </div>

                                        <div class="form-group">
                                            <label for="textos_{{ $idioma->id }}_contenido">
                                                Contenido {{ $index == 0 ? '*' : '' }}
                                                @if($index == 0)
                                                    <small class="text-muted">(Obligatorio)</small>
                                                @else
                                                    <small class="text-muted">(Opcional)</small>
                                                @endif
                                            </label>
                                            <textarea name="textos[{{ $idioma->id }}][contenido]" 
                                                      id="textos_{{ $idioma->id }}_contenido"
                                                      class="form-control tinymce-editor @error('textos.'.$idioma->id.'.contenido') is-invalid @enderror" 
                                                      rows="10">{{ old('textos.'.$idioma->id.'.contenido') }}</textarea>
                                            <small class="text-muted">Contenido completo del artículo con formato HTML</small>
                                            @error('textos.'.$idioma->id.'.contenido')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="textos_{{ $idioma->id }}_metatitulo">Meta Título (SEO)</label>
                                                    <input type="text" 
                                                           name="textos[{{ $idioma->id }}][metatitulo]" 
                                                           id="textos_{{ $idioma->id }}_metatitulo"
                                                           class="form-control"
                                                           value="{{ old('textos.'.$idioma->id.'.metatitulo') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="textos_{{ $idioma->id }}_metadescripcion">Meta Descripción (SEO)</label>
                                                    <textarea name="textos[{{ $idioma->id }}][metadescripcion]" 
                                                              id="textos_{{ $idioma->id }}_metadescripcion"
                                                              class="form-control" 
                                                              rows="2">{{ old('textos.'.$idioma->id.'.metadescripcion') }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Descripciones ALT para Accesibilidad -->
                                        <div class="row">
                                            <div class="col-12">
                                                <h6 class="text-muted mt-3 mb-2">
                                                    <i class="fas fa-universal-access"></i> 
                                                    Accesibilidad - Descripciones ALT en {{ $idioma->nombre }}
                                                </h6>
                                                <small class="text-muted">
                                                    Estas descripciones ayudan a usuarios con discapacidad visual
                                                </small>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="textos_{{ $idioma->id }}_imagen_alt">
                                                        Descripción Imagen Principal (ALT)
                                                    </label>
                                                    <input type="text" 
                                                           name="textos[{{ $idioma->id }}][imagen_alt]" 
                                                           id="textos_{{ $idioma->id }}_imagen_alt"
                                                           class="form-control"
                                                           value="{{ old('textos.'.$idioma->id.'.imagen_alt') }}"
                                                           placeholder="Describe brevemente la imagen principal">
                                                    <small class="form-text text-muted">
                                                        Para lectores de pantalla (ej: "Actor interpretando a Don Quijote")
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="textos_{{ $idioma->id }}_imagen_portada_alt">
                                                        Descripción Imagen Portada (ALT)
                                                    </label>
                                                    <input type="text" 
                                                           name="textos[{{ $idioma->id }}][imagen_portada_alt]" 
                                                           id="textos_{{ $idioma->id }}_imagen_portada_alt"
                                                           class="form-control"
                                                           value="{{ old('textos.'.$idioma->id.'.imagen_portada_alt') }}"
                                                           placeholder="Describe brevemente la imagen de portada">
                                                    <small class="form-text text-muted">
                                                        Para listados y portada (ej: "Escena de la obra Don Quijote")
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-check">
                                            <input type="checkbox" 
                                                   name="textos[{{ $idioma->id }}][visible]" 
                                                   id="textos_{{ $idioma->id }}_visible"
                                                   class="form-check-input" 
                                                   value="1"
                                                   {{ old('textos.'.$idioma->id.'.visible', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="textos_{{ $idioma->id }}_visible">
                                                Visible en {{ $idioma->nombre }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Opciones adicionales -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Opciones</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-check mb-3">
                                <input type="checkbox" name="portada" id="portada" class="form-check-input" value="1"
                                       {{ old('portada') ? 'checked' : '' }}>
                                <label class="form-check-label" for="portada">
                                    Mostrar en portada
                                </label>
                            </div>

                            <div class="form-check mb-3">
                                <input type="checkbox" name="pagina_estatica" id="pagina_estatica" class="form-check-input" value="1"
                                       {{ old('pagina_estatica') ? 'checked' : '' }}>
                                <label class="form-check-label" for="pagina_estatica">
                                    Página estática
                                </label>
                            </div>

                            @if(count($galerias) > 0)
                                <div class="form-group">
                                    <label for="galeria_id">Galería</label>
                                    <select name="galeria_id" id="galeria_id" class="form-control">
                                        <option value="">Sin galería</option>
                                        @foreach($galerias as $galeria)
                                            <option value="{{ $galeria->id }}" 
                                                    {{ old('galeria_id') == $galeria->id ? 'selected' : '' }}>
                                                {{ $galeria->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <!-- Sección de Imágenes -->
                            <hr class="my-4">
                            <h5 class="mb-3"><i class="fas fa-images"></i> Gestión de Imágenes</h5>
                            
                            @if(!extension_loaded('gd'))
                                <div class="alert alert-warning alert-sm">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Aviso:</strong> Las imágenes se guardarán sin procesamiento automático (extensión GD no disponible).
                                </div>
                            @endif
                            
                            <div class="form-group">
                                <label for="imagen">Imagen Principal</label>
                                <input type="file" name="imagen" id="imagen" 
                                       class="form-control-file @error('imagen') is-invalid @enderror"
                                       accept="image/*">
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    <span id="imagen-info">Seleccione un tipo de contenido para ver las especificaciones</span>
                                </small>
                                @error('imagen')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="imagen_portada">Imagen de Portada</label>
                                <input type="file" name="imagen_portada" id="imagen_portada" 
                                       class="form-control-file @error('imagen_portada') is-invalid @enderror"
                                       accept="image/*">
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    <span id="imagen-portada-info">Seleccione un tipo de contenido para ver las especificaciones</span>
                                </small>
                                @error('imagen_portada')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="actions">Acciones Adicionales</label>
                                <textarea name="actions" id="actions" class="form-control" rows="3" 
                                          placeholder="Datos javascript control campañas">{{ old('actions') }}</textarea>
                                <small class="form-text text-muted">Datos javascript control campañas</small>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="card">
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-save"></i> Guardar Contenido
                                </button>
                                <a href="{{ route('admin.contents.index') }}" class="btn btn-secondary btn-block">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop

@section('css')
    <style>
        .nav-tabs .nav-link {
            border-bottom: 1px solid transparent;
        }
        .nav-tabs .nav-link.active {
            border-bottom-color: #007bff;
        }
        .form-check-input:checked {
            background-color: #007bff;
            border-color: #007bff;
        }
    </style>
@stop

@section('js')
    <script>
        // Configuraciones de imagen por tipo de contenido
        const imageConfigs = @json($imageConfigs ?? []);

        // Función para generar slug desde el título
        function generateSlug(idiomaId) {
            const titulo = document.getElementById('textos_' + idiomaId + '_titulo').value;
            const slug = titulo
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '') // Eliminar acentos
                .replace(/[^a-z0-9\s-]/g, '') // Solo letras, números, espacios y guiones
                .replace(/\s+/g, '-') // Espacios a guiones
                .replace(/-+/g, '-') // Múltiples guiones a uno solo
                .replace(/^-|-$/g, ''); // Eliminar guiones al inicio y final
            
            document.getElementById('textos_' + idiomaId + '_slug').value = slug;
        }

        // Función para actualizar información de imágenes según tipo de contenido
        function updateImageInfo() {
            const tipoContenido = document.getElementById('tipo_contenido').value;
            const imagenInfo = document.getElementById('imagen-info');
            const imagenPortadaInfo = document.getElementById('imagen-portada-info');

            if (tipoContenido && imageConfigs[tipoContenido]) {
                const configs = imageConfigs[tipoContenido];
                
                if (configs.imagen) {
                    const config = configs.imagen;
                    imagenInfo.innerHTML = `Tamaño recomendado: ${config.ancho}x${config.alto}px, Formato: ${config.formato.toUpperCase()}, Calidad: ${config.calidad}%`;
                }
                
                if (configs.imagen_portada) {
                    const config = configs.imagen_portada;
                    imagenPortadaInfo.innerHTML = `Tamaño recomendado: ${config.ancho}x${config.alto}px, Formato: ${config.formato.toUpperCase()}, Calidad: ${config.calidad}%`;
                }
            } else {
                imagenInfo.innerHTML = 'Seleccione un tipo de contenido para ver las especificaciones';
                imagenPortadaInfo.innerHTML = 'Seleccione un tipo de contenido para ver las especificaciones';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            console.log('🌐 Configurando formulario de creación de contenidos');
            
            // Inicializar pestañas de Bootstrap
            const triggerTabList = [].slice.call(document.querySelectorAll('#idiomasTabs a'));
            triggerTabList.forEach(function (triggerEl) {
                const tabTrigger = new bootstrap.Tab(triggerEl);
                
                triggerEl.addEventListener('click', function (event) {
                    event.preventDefault();
                    tabTrigger.show();
                    console.log('📋 Cambiando a pestaña:', triggerEl.textContent);
                });
            });

            // Auto-generar slug cuando se escriba el título para cada idioma
            @foreach($idiomas as $idioma)
                const tituloField{{ $idioma->id }} = document.getElementById('textos_{{ $idioma->id }}_titulo');
                if (tituloField{{ $idioma->id }}) {
                    tituloField{{ $idioma->id }}.addEventListener('input', function() {
                        generateSlug({{ $idioma->id }});
                    });
                }
            @endforeach
            
            // Actualizar información de imágenes cuando cambie el tipo de contenido
            document.getElementById('tipo_contenido').addEventListener('change', updateImageInfo);
            
            // Actualizar al cargar la página si ya hay un tipo seleccionado
            updateImageInfo();

            // Inicializar TinyMCE usando la configuración personalizada
            if (typeof window.initTinyMCE === 'function') {
                window.initTinyMCE();
            }

            console.log('✅ Formulario de creación configurado correctamente');
        });
    </script>
@stop