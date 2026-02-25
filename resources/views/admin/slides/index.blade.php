@extends('admin.layouts.app')

@section('title', 'Gestión de Slides')
@section('page-title', 'Gestión de Slides')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Slides</li>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Acciones -->
        <div class="row mb-3">
            <div class="col-md-6">
                <a href="{{ route('admin.slides.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Slide
                </a>
            </div>
            <div class="col-md-6 text-right">
                <button id="saveOrder" class="btn btn-success" style="display: none;">
                    <i class="fas fa-save"></i> Guardar Orden
                </button>
            </div>
        </div>

        <!-- Tabla de slides -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Slides</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if($slides->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="slides-table">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">Orden</th>
                                    <th style="width: 120px;">Imagen</th>
                                    <th>Título</th>
                                    <th>Descripción</th>
                                    <th style="width: 120px;">Enlace</th>
                                    <th style="width: 80px;">Nueva Ventana</th>
                                    <th style="width: 80px;">Visible</th>
                                    <th style="width: 200px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="sortable-slides">
                                @foreach($slides as $slide)
                                    <tr data-slide-id="{{ $slide->id }}" class="slide-row">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="handle me-2" title="Arrastra para reordenar">
                                                    <i class="fas fa-grip-vertical"></i>
                                                </span>
                                                <span class="order-number">{{ $slide->orden }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if($slide->hasImage())
                                                <img src="{{ $slide->miniatura_url }}" 
                                                     alt="{{ $slide->currentTranslation()->alt_text ?? 'Slide' }}" 
                                                     class="img-thumbnail slide-thumbnail"
                                                     style="max-width: 80px; max-height: 60px;">
                                            @else
                                                <div class="no-image-placeholder">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $slide->currentTranslation()->titulo ?? 'Sin título' }}</strong>
                                            @if($slide->currentTranslation() && $slide->currentTranslation()->alt_text && $slide->currentTranslation()->alt_text !== $slide->currentTranslation()->titulo)
                                                <br><small class="text-muted">Alt: {{ Str::limit($slide->currentTranslation()->alt_text, 30) }}</small>
                                            @endif
                                            
                                            <!-- Mostrar idiomas disponibles -->
                                            <div class="mt-1">
                                                @foreach($slide->translations as $translation)
                                                    <span class="badge badge-secondary badge-sm me-1" title="{{ $translation->idioma->nombre }}">
                                                        {{ $translation->idioma->etiqueta }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>
                                            @if($slide->currentTranslation() && $slide->currentTranslation()->descripcion)
                                                {{ Str::limit($slide->currentTranslation()->descripcion, 100) }}
                                            @else
                                                <span class="text-muted">Sin descripción</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($slide->currentTranslation() && $slide->currentTranslation()->url)
                                                <a href="{{ $slide->currentTranslation()->url }}" target="_blank" class="text-primary" title="{{ $slide->currentTranslation()->url }}">
                                                    {{ Str::limit($slide->currentTranslation()->url, 20) }} <i class="fas fa-external-link-alt fa-xs"></i>
                                                </a>
                                            @else
                                                <span class="text-muted">Sin enlace</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($slide->nueva_ventana)
                                                <span class="badge badge-info"><i class="fas fa-external-link-alt"></i></span>
                                            @else
                                                <span class="badge badge-light"><i class="fas fa-times"></i></span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($slide->visible)
                                                <span class="badge badge-success"><i class="fas fa-eye"></i></span>
                                            @else
                                                <span class="badge badge-danger"><i class="fas fa-eye-slash"></i></span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.slides.edit', $slide) }}" 
                                                   class="btn btn-sm btn-warning" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger btn-eliminar" 
                                                        data-slide-id="{{ $slide->id }}" 
                                                        data-slide-title="{{ $slide->titulo }}" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-images fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay slides creados</h5>
                        <p class="text-muted">Crea tu primer slide para comenzar a mostrar contenido destacado en el sitio.</p>
                        <a href="{{ route('admin.slides.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Crear Primer Slide
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

{{-- Modal de confirmación de eliminación --}}
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                    <h6>¿Estás seguro de que deseas eliminar este slide?</h6>
                    <p class="text-muted"><strong id="slideToDelete"></strong></p>
                    <p class="text-muted">Esta acción eliminará también la imagen asociada y no se puede deshacer.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="confirmarEliminar">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<form id="deleteForm" style="display: none;" method="POST">
    @csrf
    @method('DELETE')
</form>
@stop

@push('styles')
<!-- Estilos específicos para gestión de slides -->
<link rel="stylesheet" href="{{ asset('css/admin-slides.css') }}">
@endpush

@push('scripts')
<!-- SortableJS para drag and drop -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<!-- Variables de configuración -->
<script>
    // Configuración global para el módulo de slides
    window.routes = {
        slideUpdateOrder: '{{ route('admin.slides.update-order') }}',
        slideIndex: '{{ route('admin.slides.index') }}'
    };
    window.csrfToken = '{{ csrf_token() }}';
</script>

<!-- Funcionalidad de gestión de slides -->
<script src="{{ asset('js/admin-slides.js') }}"></script>
@endpush