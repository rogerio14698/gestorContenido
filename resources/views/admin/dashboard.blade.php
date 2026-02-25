@extends('admin.layouts.app')

@section('title', 'Dashboard - Eunomia CMS')
@section('page-title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<!-- Small boxes (Stat box) -->
<div class="row">
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $stats['contenidos'] }}</h3>
                <p>Contenidos Totales</p>
            </div>
            <div class="icon">
                <i class="ion ion-document-text"></i>
                <i class="fas fa-file-alt"></i>
            </div>
            <a href="{{ route('admin.contents.index') }}" class="small-box-footer">
                Más info <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $stats['noticias'] }}</h3>
                <p>Noticias</p>
            </div>
            <div class="icon">
                <i class="fas fa-newspaper"></i>
            </div>
            <a href="{{ route('admin.contents.index') }}?tipo=noticia" class="small-box-footer">
                Más info <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $stats['paginas'] }}</h3>
                <p>Páginas</p>
            </div>
            <div class="icon">
                <i class="fas fa-copy"></i>
            </div>
            <a href="{{ route('admin.contents.index') }}?tipo=pagina" class="small-box-footer">
                Más info <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $stats['menus'] }}</h3>
                <p>Elementos de Menú</p>
            </div>
            <div class="icon">
                <i class="fas fa-bars"></i>
            </div>
            <a href="{{ route('admin.menus.index') }}" class="small-box-footer">
                Más info <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Últimas Noticias -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-newspaper mr-1"></i>
                    Últimas Noticias
                </h3>
                <div class="card-tools">
                    <a href="{{ route('admin.contents.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Crear Noticia
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($ultimasNoticias->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ultimasNoticias as $noticia)
                                    <tr>
                                        <td>
                                            @php
                                                $texto = $noticia->textos->first();
                                            @endphp
                                            {{ $texto ? $texto->titulo : 'Sin título' }}
                                        </td>
                                        <td>
                                            {{ $noticia->fecha_publicacion ? $noticia->fecha_publicacion->format('d/m/Y') : '-' }}
                                        </td>
                                        <td>
                                            @if($noticia->portada)
                                                <span class="badge badge-success">Portada</span>
                                            @else
                                                <span class="badge badge-secondary">Normal</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.contents.edit', $noticia->id) }}" 
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($texto && $texto->slug)
                                                <a href="{{ route('contenido', ['es', $texto->slug]) }}" 
                                                   target="_blank" 
                                                   class="btn btn-sm btn-success">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                        <h5>No hay noticias aún</h5>
                        <p class="text-muted">Crea tu primera noticia para comenzar.</p>
                        <a href="{{ route('admin.contents.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Crear Primera Noticia
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Información del Sistema -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle mr-1"></i>
                    Información del Sistema
                </h3>
            </div>
            <div class="card-body">
                <strong>
                    <i class="fas fa-language mr-1"></i> Idiomas Activos
                </strong>
                <p class="text-muted">{{ $stats['idiomas'] }} idiomas configurados</p>
                <hr>

                <strong>
                    <i class="fas fa-users mr-1"></i> Usuarios
                </strong>
                <p class="text-muted">{{ $stats['usuarios'] }} usuarios registrados</p>
                <hr>

                <strong>
                    <i class="fas fa-server mr-1"></i> Estado del Sistema
                </strong>
                <p class="text-muted">
                    <span class="badge badge-success">Operativo</span>
                    Laravel {{ app()->version() }}
                </p>
            </div>
        </div>

        <!-- Accesos Rápidos -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-bolt mr-1"></i>
                    Accesos Rápidos
                </h3>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.contents.create') }}" class="btn btn-primary btn-block mb-2">
                        <i class="fas fa-plus"></i> Crear Contenido
                    </a>
                    <a href="{{ route('inicio', 'es') }}" target="_blank" class="btn btn-success btn-block mb-2">
                        <i class="fas fa-external-link-alt"></i> Ver Sitio Web
                    </a>
                    <a href="#" class="btn btn-info btn-block mb-2">
                        <i class="fas fa-cogs"></i> Configuración
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gráfico de Contenidos (opcional para futuras mejoras) -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar mr-1"></i>
                    Resumen de Actividad
                </h3>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="description-block border-right">
                            <span class="description-percentage text-success">
                                <i class="fas fa-caret-up"></i> +{{ $stats['noticias'] }}
                            </span>
                            <h5 class="description-header">{{ $stats['contenidos'] }}</h5>
                            <span class="description-text">TOTAL CONTENIDOS</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="description-block border-right">
                            <span class="description-percentage text-warning">
                                <i class="fas fa-caret-left"></i> {{ $stats['idiomas'] }}
                            </span>
                            <h5 class="description-header">{{ $stats['idiomas'] }}</h5>
                            <span class="description-text">IDIOMAS</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="description-block border-right">
                            <span class="description-percentage text-success">
                                <i class="fas fa-caret-up"></i> {{ $stats['menus'] }}
                            </span>
                            <h5 class="description-header">{{ $stats['menus'] }}</h5>
                            <span class="description-text">MENÚS</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="description-block">
                            <span class="description-percentage text-info">
                                <i class="fas fa-caret-up"></i> {{ $stats['usuarios'] }}
                            </span>
                            <h5 class="description-header">{{ $stats['usuarios'] }}</h5>
                            <span class="description-text">USUARIOS</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Actualizar estadísticas cada 30 segundos (opcional)
    // setInterval(function() {
    //     // AJAX call to update stats
    // }, 30000);
</script>
@endpush