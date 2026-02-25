@extends('admin.layouts.app')

@section('title', 'Ver Slide')
@section('page-title', 'Ver Slide')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.slides.index') }}">Slides</a></li>
    <li class="breadcrumb-item active">Ver</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- Información principal -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ $slide->titulo }}</h3>
                        <div class="card-tools">
                            @if($slide->visible)
                                <span class="badge badge-success">Visible</span>
                            @else
                                <span class="badge badge-danger">Oculto</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        @if($slide->hasImage())
                            <div class="text-center mb-4">
                                <img src="{{ $slide->imagen_url }}" alt="{{ $slide->alt_text }}" 
                                     class="img-fluid rounded shadow" style="max-width: 100%; height: auto;">
                            </div>
                        @else
                            <div class="text-center mb-4">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    No hay imagen asociada a este slide.
                                </div>
                            </div>
                        @endif

                        @if($slide->descripcion)
                            <div class="mb-3">
                                <h5>Descripción:</h5>
                                <p class="text-muted">{{ $slide->descripcion }}</p>
                            </div>
                        @endif

                        @if($slide->url)
                            <div class="mb-3">
                                <h5>Enlace:</h5>
                                <p>
                                    <a href="{{ $slide->url }}" 
                                       target="{{ $slide->nueva_ventana ? '_blank' : '_self' }}" 
                                       class="btn btn-outline-primary">
                                        <i class="fas fa-link"></i> {{ $slide->url }}
                                        @if($slide->nueva_ventana)
                                            <i class="fas fa-external-link-alt ml-1"></i>
                                        @endif
                                    </a>
                                </p>
                            </div>
                        @endif

                        @if($slide->alt_text)
                            <div class="mb-3">
                                <h5>Texto alternativo:</h5>
                                <p class="text-muted">{{ $slide->alt_text }}</p>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('admin.slides.edit', $slide) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('admin.slides.index') }}" class="btn btn-secondary">
                            <i class="fas fa-list"></i> Volver al Listado
                        </a>
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Panel lateral -->
            <div class="col-md-4">
                <!-- Información técnica -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Información Técnica</h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-4"><strong>ID:</strong></div>
                            <div class="col-8">#{{ $slide->id }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4"><strong>Orden:</strong></div>
                            <div class="col-8">{{ $slide->orden }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4"><strong>Estado:</strong></div>
                            <div class="col-8">
                                @if($slide->activo)
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge badge-secondary">Inactivo</span>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4"><strong>Visible:</strong></div>
                            <div class="col-8">
                                @if($slide->visible)
                                    <span class="badge badge-success">Sí</span>
                                @else
                                    <span class="badge badge-danger">No</span>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4"><strong>Nueva ventana:</strong></div>
                            <div class="col-8">
                                @if($slide->nueva_ventana)
                                    <span class="badge badge-info">Sí</span>
                                @else
                                    <span class="badge badge-light">No</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if($slide->metadatos)
                <!-- Información de imagen -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Información de Imagen</h3>
                    </div>
                    <div class="card-body">
                        @if(isset($slide->metadatos['ancho']) && isset($slide->metadatos['alto']))
                            <div class="row mb-2">
                                <div class="col-5"><strong>Dimensiones:</strong></div>
                                <div class="col-7">{{ $slide->metadatos['ancho'] }}×{{ $slide->metadatos['alto'] }}px</div>
                            </div>
                        @endif
                        
                        @if(isset($slide->metadatos['tamaño']))
                            <div class="row mb-2">
                                <div class="col-5"><strong>Tamaño:</strong></div>
                                <div class="col-7">{{ $slide->metadatos['tamaño'] }}</div>
                            </div>
                        @endif
                        
                        @if(isset($slide->metadatos['formato']))
                            <div class="row mb-2">
                                <div class="col-5"><strong>Formato:</strong></div>
                                <div class="col-7">{{ strtoupper($slide->metadatos['formato']) }}</div>
                            </div>
                        @endif
                        
                        @if($slide->imagen_miniatura)
                            <div class="row mb-2">
                                <div class="col-5"><strong>Miniatura:</strong></div>
                                <div class="col-7">
                                    <i class="fas fa-check text-success"></i> Disponible
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Fechas -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Fechas</h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-4"><strong>Creado:</strong></div>
                            <div class="col-8">
                                <span title="{{ $slide->created_at->format('d/m/Y H:i:s') }}">
                                    {{ $slide->created_at->format('d/m/Y') }}<br>
                                    <small class="text-muted">{{ $slide->created_at->format('H:i') }}</small>
                                </span>
                            </div>
                        </div>
                        
                        @if($slide->updated_at != $slide->created_at)
                            <div class="row mb-2">
                                <div class="col-4"><strong>Actualizado:</strong></div>
                                <div class="col-8">
                                    <span title="{{ $slide->updated_at->format('d/m/Y H:i:s') }}">
                                        {{ $slide->updated_at->format('d/m/Y') }}<br>
                                        <small class="text-muted">{{ $slide->updated_at->format('H:i') }}</small>
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación de eliminación -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                        <h6>¿Estás seguro de que deseas eliminar este slide?</h6>
                        <p class="text-muted"><strong>{{ $slide->titulo }}</strong></p>
                        <p class="text-muted">Esta acción eliminará también la imagen asociada y no se puede deshacer.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <form action="{{ route('admin.slides.destroy', $slide) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop