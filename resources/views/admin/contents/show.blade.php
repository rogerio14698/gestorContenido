@extends('admin.layouts.app')

@section('page-title', 'Detalles del Contenido')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.contents.index') }}">Contenidos</a></li>
    <li class="breadcrumb-item active">{{ $content->textos->first()->titulo ?? 'Contenido #'.$content->id }}</li>
@endsection

@section('content')
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <!-- Información básica del contenido -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-newspaper"></i> 
                                {{ $content->textos->first()->titulo ?? 'Sin título' }}
                                @if($content->portada)
                                    <span class="badge badge-warning ml-2"><i class="fas fa-star"></i> Portada</span>
                                @endif
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('admin.contents.edit', $content) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6><strong>Información General</strong></h6>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td><strong>ID:</strong></td>
                                            <td>{{ $content->id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tipo:</strong></td>
                                            <td>
                                                <span class="badge badge-{{
                                                    $content->tipo_contenido === 'noticia' ? 'success' :
                                                    ($content->tipo_contenido === 'pagina' ? 'info' : 'warning')
                                                }}">
                                                    {{ ucfirst($content->tipo_contenido) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Fecha de publicación:</strong></td>
                                            <td>{{ $content->fecha_publicacion ? \Carbon\Carbon::parse($content->fecha_publicacion)->format('d/m/Y H:i') : 'Sin fecha' }}</td>
                                        </tr>
                                        @if($content->lugar)
                                        <tr>
                                            <td><strong>Lugar:</strong></td>
                                            <td>{{ $content->lugar }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td><strong>Estado:</strong></td>
                                            <td>
                                                @if($content->portada)
                                                    <i class="fas fa-star text-warning" title="En portada"></i> En portada
                                                @endif
                                                @if($content->pagina_estatica)
                                                    <span class="badge badge-secondary">Página estática</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6><strong>Fechas del Sistema</strong></h6>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td><strong>Creado:</strong></td>
                                            <td>{{ $content->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Actualizado:</strong></td>
                                            <td>{{ $content->updated_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Orden:</strong></td>
                                            <td>{{ $content->orden ?? 'Sin orden' }}</td>
                                        </tr>
                                        @if($content->galeria_id)
                                        <tr>
                                            <td><strong>Galería:</strong></td>
                                            <td>{{ $content->galeria->nombre ?? 'Galería #'.$content->galeria_id }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Imágenes del contenido -->
                    @if($content->imagen || $content->imagen_portada)
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-images"></i> Imágenes
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if($content->imagen)
                                <div class="col-md-6">
                                    <h6>Imagen principal</h6>
                                    <div class="text-center">
                                        <img src="{{ asset('storage/' . $content->imagen) }}" 
                                             alt="Imagen principal" 
                                             class="img-fluid rounded shadow"
                                             style="max-height: 300px;">
                                    </div>
                                </div>
                                @endif
                                @if($content->imagen_portada)
                                <div class="col-md-6">
                                    <h6>Imagen de portada</h6>
                                    <div class="text-center">
                                        <img src="{{ asset('storage/' . $content->imagen_portada) }}" 
                                             alt="Imagen de portada" 
                                             class="img-fluid rounded shadow"
                                             style="max-height: 300px;">
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Textos en diferentes idiomas -->
                    @if($content->textos->count() > 0)
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-language"></i> Contenido por Idiomas
                            </h3>
                        </div>
                        <div class="card-body">
                            @php
                                $idiomasActivos = \App\Models\Idioma::where('activo', true)->orderBy('orden')->get();
                            @endphp
                            
                            <ul class="nav nav-tabs" id="idiomasTab" role="tablist">
                                @foreach($idiomasActivos as $index => $idioma)
                                    @php
                                        $texto = $content->textos->where('idioma_id', $idioma->id)->first();
                                    @endphp
                                    @if($texto)
                                    <li class="nav-item">
                                        <a class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                           id="idioma-{{ $idioma->id }}-tab" 
                                           data-toggle="tab" 
                                           href="#idioma-{{ $idioma->id }}" 
                                           role="tab">
                                            {{ strtoupper($idioma->codigo) }}
                                            @if($idioma->es_principal)
                                                <i class="fas fa-star text-warning ml-1" title="Idioma principal"></i>
                                            @endif
                                        </a>
                                    </li>
                                    @endif
                                @endforeach
                            </ul>

                            <div class="tab-content" id="idiomasTabContent">
                                @foreach($idiomasActivos as $index => $idioma)
                                    @php
                                        $texto = $content->textos->where('idioma_id', $idioma->id)->first();
                                    @endphp
                                    @if($texto)
                                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                         id="idioma-{{ $idioma->id }}" 
                                         role="tabpanel">
                                        <div class="mt-3">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <h5><strong>{{ $texto->titulo }}</strong></h5>
                                                    @if($texto->subtitulo)
                                                        <h6 class="text-muted">{{ $texto->subtitulo }}</h6>
                                                    @endif
                                                    @if($texto->resumen)
                                                        <div class="alert alert-info">
                                                            <strong>Resumen:</strong> {{ $texto->resumen }}
                                                        </div>
                                                    @endif
                                                    <div class="mt-3">
                                                        {!! nl2br(e($texto->contenido)) !!}
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <h6><strong>Información SEO y URLs</strong></h6>
                                                    <table class="table table-sm table-borderless">
                                                        <tr>
                                                            <td><strong>Slug:</strong></td>
                                                            <td><code>{{ $texto->slug }}</code></td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Visible:</strong></td>
                                                            <td>
                                                                @if($texto->visible)
                                                                    <span class="badge badge-success">Sí</span>
                                                                @else
                                                                    <span class="badge badge-danger">No</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @if($texto->metatitulo)
                                                        <tr>
                                                            <td><strong>Meta título:</strong></td>
                                                            <td>{{ $texto->metatitulo }}</td>
                                                        </tr>
                                                        @endif
                                                        @if($texto->metadescripcion)
                                                        <tr>
                                                            <td><strong>Meta descripción:</strong></td>
                                                            <td>{{ $texto->metadescripcion }}</td>
                                                        </tr>
                                                        @endif
                                                        @if($texto->imagen_alt)
                                                        <tr>
                                                            <td><strong>Alt imagen:</strong></td>
                                                            <td>{{ $texto->imagen_alt }}</td>
                                                        </tr>
                                                        @endif
                                                        @if($texto->imagen_portada_alt)
                                                        <tr>
                                                            <td><strong>Alt portada:</strong></td>
                                                            <td>{{ $texto->imagen_portada_alt }}</td>
                                                        </tr>
                                                        @endif
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Acciones -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('admin.contents.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Volver a la lista
                                </a>
                                
                                <div class="btn-group">
                                    <a href="{{ route('admin.contents.edit', $content) }}" class="btn btn-primary">
                                        <i class="fas fa-edit"></i> Editar Contenido
                                    </a>
                                    
                                    <button type="button" 
                                            class="btn btn-danger" 
                                            onclick="confirmarEliminacion({{ $content->id }}, '{{ addslashes($content->textos->first()->titulo ?? 'Contenido #'.$content->id) }}')">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal de Confirmación -->
<div class="modal fade" id="modalConfirmarEliminacion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Confirmar Eliminación</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar el contenido <strong id="nombreContenido"></strong>?</p>
                <p class="text-danger"><small>Esta acción no se puede deshacer y eliminará también todos los textos en diferentes idiomas.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarEliminar">Eliminar Contenido</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let contenidoAEliminar = null;

    function confirmarEliminacion(id, nombre) {
        contenidoAEliminar = id;
        document.getElementById('nombreContenido').textContent = nombre;
        $('#modalConfirmarEliminacion').modal('show');
    }

    document.getElementById('btnConfirmarEliminar').addEventListener('click', function() {
        if (!contenidoAEliminar) return;

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Eliminando...';

        fetch(`{{ route('admin.contents.index') }}/${contenidoAEliminar}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#modalConfirmarEliminacion').modal('hide');
                
                // Redirigir a la lista de contenidos
                window.location.href = '{{ route('admin.contents.index') }}';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar el contenido');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = 'Eliminar Contenido';
        });
    });
</script>
@endsection