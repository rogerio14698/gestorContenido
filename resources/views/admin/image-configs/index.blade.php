@extends('admin.layouts.app')

@section('title', 'Configuración de Imágenes')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Configuración de Imágenes</h1>
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-right">
                        <a href="{{ route('admin.image-configs.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nueva Configuración
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {{ session('success') }}
                </div>
            @endif

            @foreach($configs as $tipoContenido => $configuraciones)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-images"></i>
                            Configuraciones para: <strong>{{ ucfirst($tipoContenido) }}</strong>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Tipo de Imagen</th>
                                        <th>Desktop</th>
                                        <th>Móvil</th>
                                        <th>Formato</th>
                                        <th>Calidad</th>
                                        <th>Opciones</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($configuraciones as $config)
                                        <tr>
                                            <td>
                                                <span class="badge badge-info">
                                                    {{ ucfirst(str_replace('_', ' ', $config->tipo_imagen)) }}
                                                </span>
                                                @if($config->descripcion)
                                                    <br><small class="text-muted">{{ $config->descripcion }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $config->ancho }} × {{ $config->alto }} px</strong>
                                                @if($config->mantener_aspecto)
                                                    <br><small class="text-success">✓ Mantener aspecto</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($config->generar_version_movil)
                                                    <strong>{{ $config->ancho_movil }} × {{ $config->alto_movil }} px</strong>
                                                    @if($config->mantener_aspecto_movil)
                                                        <br><small class="text-success">✓ Mantener aspecto</small>
                                                    @endif
                                                    <br><small class="text-info">Calidad: {{ $config->calidad_movil }}%</small>
                                                @else
                                                    <span class="text-muted">No generada</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-secondary">
                                                    {{ strtoupper($config->formato) }}
                                                </span>
                                            </td>
                                            <td>{{ $config->calidad }}%</td>
                                            <td>
                                                @if($config->redimensionar)
                                                    <span class="badge badge-success">Redimensionar</span>
                                                @endif
                                                @if($config->generar_version_movil)
                                                    <br><span class="badge badge-info">Responsive</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($config->activo)
                                                    <span class="badge badge-success">Activo</span>
                                                @else
                                                    <span class="badge badge-danger">Inactivo</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('admin.image-configs.edit', $config) }}" 
                                                       class="btn btn-sm btn-warning"
                                                       title="Editar configuración">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.image-configs.destroy', $config) }}" 
                                                          method="POST" 
                                                          style="display: inline;"
                                                          onsubmit="return confirm('¿Estás seguro de eliminar esta configuración?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach

            @if($configs->isEmpty())
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-images fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No hay configuraciones de imagen definidas.</p>
                        <a href="{{ route('admin.image-configs.create') }}" class="btn btn-primary">
                            Crear primera configuración
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Auto-hide success alerts
    setTimeout(function() {
        $('.alert-success').fadeOut();
    }, 3000);
});
</script>
@endsection