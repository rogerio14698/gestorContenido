@extends('admin.layouts.app')

@section('title', 'Gestión de Menús')
@section('page-title', 'Gestión de Menús')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Menús</li>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Acciones -->
        <div class="row mb-3">
            <div class="col-md-6">
                <a href="{{ route('admin.menus.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Menú
                </a>
            </div>
            <div class="col-md-6 text-right">
                <button id="saveOrder" class="btn btn-success" style="display: none;">
                    <i class="fas fa-save"></i> Guardar Orden
                </button>
            </div>
        </div>

        <!-- Tabla de menús -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Menús</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if($menus->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="menus-table">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">Orden</th>
                                    <th>Título</th>
                                    <th style="width: 120px;">Tipo Enlace</th>
                                    <th>Enlace</th>
                                    <th style="width: 80px;">Visible</th>
                                    <th style="width: 80px;">Pie</th>
                                    <th style="width: 80px;">Submenú</th>
                                    <th style="width: 200px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="sortable-menus">
                                @foreach($menus as $menu)
                                    <tr data-menu-id="{{ $menu->id }}" class="menu-row">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="handle me-2" title="Arrastra para reordenar">
                                                    <i class="fas fa-grip-vertical"></i>
                                                </span>
                                                <span class="order-number">{{ $menu->orden }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>{{ $menu->titulo }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge 
                                                @if($menu->tipo_enlace == 'contenido') badge-primary
                                                @elseif($menu->tipo_enlace == 'url_externa') badge-success  
                                                @else badge-secondary @endif">
                                                {{ ucfirst(str_replace('_', ' ', $menu->tipo_enlace)) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($menu->tipo_enlace == 'contenido' && $menu->content)
                                                <small class="text-muted">
                                                    {{ $menu->tipoContenido->nombre ?? 'Contenido' }}: 
                                                    {{ $menu->content->titulo }}
                                                </small>
                                            @elseif($menu->tipo_enlace == 'url_externa')
                                                <a href="{{ $menu->url_externa }}" target="_blank" class="text-primary">
                                                    {{ $menu->url_externa }} <i class="fas fa-external-link-alt fa-xs"></i>
                                                </a>
                                            @else
                                                <span class="text-muted">Sin enlace</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($menu->visible)
                                                <span class="badge badge-success"><i class="fas fa-eye"></i></span>
                                            @else
                                                <span class="badge badge-danger"><i class="fas fa-eye-slash"></i></span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($menu->menu_pie)
                                                <span class="badge badge-info"><i class="fas fa-check"></i></span>
                                            @else
                                                <span class="badge badge-light"><i class="fas fa-times"></i></span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-primary">{{ $menu->children->count() }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.menus.edit', $menu) }}" 
                                                   class="btn btn-sm btn-warning" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger btn-eliminar" 
                                                        data-menu-id="{{ $menu->id }}" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    {{-- Mostrar submenús --}}
                                    @foreach($menu->children as $submenu)
                                        <tr data-menu-id="{{ $submenu->id }}" class="submenu-row" data-parent-id="{{ $menu->id }}">
                                            <td class="submenu-order-cell">
                                                <div class="d-flex align-items-center">
                                                    <span class="submenu-handle handle" title="Arrastrar para reordenar">
                                                        <i class="fas fa-grip-vertical"></i>
                                                    </span>
                                                    <span class="submenu-indicator">└</span>
                                                    <span class="submenu-order-number">{{ $submenu->orden }}</span>
                                                </div>
                                            </td>
                                            <td class="submenu-title-cell">
                                                <div class="submenu-title">
                                                    {{ $submenu->titulo }}
                                                    @if($submenu->icon)
                                                        <i class="{{ $submenu->icon }} ml-1"></i>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge 
                                                    @if($submenu->tipo_enlace == 'contenido') badge-primary
                                                    @elseif($submenu->tipo_enlace == 'url_externa') badge-success  
                                                    @else badge-secondary @endif">
                                                    {{ ucfirst(str_replace('_', ' ', $submenu->tipo_enlace)) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($submenu->tipo_enlace == 'contenido' && $submenu->content)
                                                    <small class="text-muted">
                                                        {{ $submenu->tipoContenido->nombre ?? 'Contenido' }}: 
                                                        {{ $submenu->content->titulo }}
                                                    </small>
                                                @elseif($submenu->tipo_enlace == 'url_externa')
                                                    <a href="{{ $submenu->url_externa }}" target="_blank" class="text-primary">
                                                        {{ $submenu->url_externa }} <i class="fas fa-external-link-alt fa-xs"></i>
                                                    </a>
                                                @else
                                                    <span class="text-muted">Sin enlace</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($submenu->visible)
                                                    <span class="badge badge-success"><i class="fas fa-eye"></i></span>
                                                @else
                                                    <span class="badge badge-danger"><i class="fas fa-eye-slash"></i></span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($submenu->menu_pie)
                                                    <span class="badge badge-info"><i class="fas fa-check"></i></span>
                                                @else
                                                    <span class="badge badge-light"><i class="fas fa-times"></i></span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-secondary">0</span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.menus.edit', $submenu) }}" 
                                                       class="btn btn-sm btn-warning" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger btn-eliminar" 
                                                            data-menu-id="{{ $submenu->id }}" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-bars fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay menús creados</h5>
                        <p class="text-muted">Crea tu primer menú para comenzar a estructurar la navegación del sitio.</p>
                        <a href="{{ route('admin.menus.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Crear Primer Menú
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
                    <h6>¿Estás seguro de que deseas eliminar este menú?</h6>
                    <p class="text-muted">Esta acción no se puede deshacer.</p>
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
<!-- Estilos específicos para gestión de menús -->
<link rel="stylesheet" href="{{ asset('css/admin-menus.css') }}">
@endpush

@push('scripts')
<!-- SortableJS para drag and drop -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<!-- Variables de configuración -->
<script>
    // Configuración global para el módulo de menús
    window.routes = {
        menuUpdateOrder: '{{ route('admin.menus.update-order') }}',
        menuIndex: '{{ route('admin.menus.index') }}'
    };
    window.csrfToken = '{{ csrf_token() }}';
</script>

<!-- Funcionalidad de gestión de menús -->
<script src="{{ asset('js/admin-menus.js') }}"></script>
@endpush