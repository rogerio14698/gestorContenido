@extends('admin.layouts.app')

@section('page-title', 'Tipo de Contenido: ' . $tipo->tipo_contenido)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.tipos-contenido.index') }}">Tipos de Contenido</a></li>
    <li class="breadcrumb-item active">{{ $tipo->tipo_contenido }}</li>
@endsection

@section('content')
    @php
        $textosAsociados = \App\Models\TextoIdioma::where('tipo_contenido_id', $tipo->id)
            ->with(['idioma', 'contenidoModel'])
            ->orderByDesc('updated_at')
            ->get();
        $totalContenidos = $textosAsociados->count();
        $idiomasUsados = $textosAsociados->pluck('idioma_id')->unique()->count();
        $contenidosRecientes = $textosAsociados->take(10);
        $puedeEliminar = $totalContenidos === 0;
    @endphp

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8">
                <div class="card card-primary">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-info-circle"></i> Detalles del Tipo de Contenido
                        </h3>
                        <a href="{{ route('admin.tipos-contenido.edit', $tipo->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5 class="text-muted text-uppercase">Nombre</h5>
                                <p class="h4 mb-0">{{ $tipo->tipo_contenido }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="text-muted text-uppercase">Descripción</h5>
                                <p class="mb-0">
                                    {{ $tipo->descripcion ?: 'Sin descripción definida' }}
                                </p>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="info-box bg-info">
                                    <span class="info-box-icon"><i class="fas fa-file-alt"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Contenidos</span>
                                        <span class="info-box-number">{{ $totalContenidos }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box bg-success">
                                    <span class="info-box-icon"><i class="fas fa-language"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Idiomas</span>
                                        <span class="info-box-number">{{ $idiomasUsados }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box bg-secondary">
                                    <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Última edición</span>
                                        <span class="info-box-number text-sm">{{ $tipo->updated_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($totalContenidos > 0)
                            <h5 class="mb-3"><i class="fas fa-list"></i> Contenidos Recientes</h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Título</th>
                                            <th>Idioma</th>
                                            <th>Actualizado</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($contenidosRecientes as $contenido)
                                            <tr>
                                                <td>
                                                    <strong>{{ $contenido->titulo ?: 'Sin título' }}</strong>
                                                    @if($contenido->slug)
                                                        <br><small class="text-muted">/{{ $contenido->slug }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($contenido->idioma)
                                                        <span class="badge bg-primary">
                                                            {{ $contenido->idioma->nombre ?? $contenido->idioma->idioma ?? strtoupper($contenido->idioma->etiqueta ?? $contenido->idioma->codigo ?? 'N/D') }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary">N/D</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small>{{ $contenido->updated_at->format('d/m/Y H:i') }}</small>
                                                </td>
                                                <td class="text-center">
                                                    @if($contenido->contenidoModel)
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <a href="{{ route('admin.contents.show', $contenido->contenidoModel) }}" class="btn btn-info">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('admin.contents.edit', $contenido->contenidoModel) }}" class="btn btn-primary">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">Sin vínculo</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if($totalContenidos > 10)
                                <div class="text-center mt-3">
                                    <a href="{{ route('admin.contents.index', ['tipo' => strtolower($tipo->tipo_contenido)]) }}" class="btn btn-outline-primary btn-sm">
                                        Ver todos los contenidos ({{ $totalContenidos }})
                                    </a>
                                </div>
                            @endif
                        @else
                            <div class="alert alert-light border text-center mb-0">
                                <i class="fas fa-info-circle text-muted"></i>
                                <span class="ms-2">Este tipo de contenido todavía no tiene entradas asociadas.</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title mb-0">Acciones Rápidas</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.tipos-contenido.edit', $tipo->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Editar Tipo
                            </a>
                            <a href="{{ route('admin.tipos-contenido.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver al listado
                            </a>
                            @if($puedeEliminar)
                                <button type="button" class="btn btn-danger" onclick="confirmarEliminacion()">
                                    <i class="fas fa-trash"></i> Eliminar tipo
                                </button>
                            @else
                                <button type="button" class="btn btn-outline-danger" disabled>
                                    <i class="fas fa-ban"></i> No se puede eliminar
                                </button>
                                <small class="text-muted d-block text-center mt-2">
                                    Hay contenidos asociados que dependen de este tipo.
                                </small>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title mb-0">Información Técnica</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm mb-0">
                            <tr>
                                <th width="45%">ID</th>
                                <td>{{ $tipo->id }}</td>
                            </tr>
                            <tr>
                                <th>Fecha de creación</th>
                                <td>{{ $tipo->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Última actualización</th>
                                <td>{{ $tipo->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Total contenidos</th>
                                <td>{{ $totalContenidos }}</td>
                            </tr>
                            <tr>
                                <th>Idiomas utilizados</th>
                                <td>{{ $idiomasUsados }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @if($puedeEliminar)
            <div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirmar eliminación</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>¿Seguro que deseas eliminar el tipo <strong>{{ $tipo->tipo_contenido }}</strong>?</p>
                            <p class="text-muted mb-0"><small>Esta acción no se puede deshacer.</small></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-danger" id="btnConfirmarEliminar">Eliminar</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@if($puedeEliminar)
    @push('scripts')
        <script>
            function confirmarEliminacion() {
                $('#modalEliminar').modal('show');
            }

            document.addEventListener('DOMContentLoaded', function () {
                const btnEliminar = document.getElementById('btnConfirmarEliminar');
                if (!btnEliminar) {
                    return;
                }

                btnEliminar.addEventListener('click', function () {
                    const button = this;
                    button.disabled = true;
                    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Eliminando...';

                    fetch('{{ route('admin.tipos-contenido.destroy', $tipo->id) }}', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.href = '{{ route('admin.tipos-contenido.index') }}';
                                return;
                            }

                            alert(data.message || 'No se pudo eliminar el tipo de contenido.');
                            button.disabled = false;
                            button.innerHTML = 'Eliminar';
                        })
                        .catch(() => {
                            alert('Ocurrió un error inesperado al eliminar.');
                            button.disabled = false;
                            button.innerHTML = 'Eliminar';
                        });
                });
            });
        </script>
    @endpush
@endif
