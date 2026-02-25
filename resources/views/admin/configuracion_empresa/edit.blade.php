@extends('admin.layouts.app')

@section('title', 'Configuración de la Empresa')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Configuración de la Empresa</h1>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form action="{{ route('admin.configuracion_empresa.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header">Datos de la empresa</div>
                    <div class="card-body">
                        <!-- Tabs de idiomas para campos meta -->
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
                        <div class="tab-content mt-3" id="idiomasTabsContent">
                            @foreach($idiomas as $index => $idioma)
                                <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}" 
                                     id="idioma-{{ $idioma->id }}" 
                                     role="tabpanel">
                                    @php $texto = $textosMeta[$idioma->id] ?? null; @endphp
                                    <div class="mb-3">
                                        <label for="metatitulo_{{ $idioma->id }}" class="form-label">Meta título (SEO) [{{ strtoupper($idioma->etiqueta) }}]</label>
                                        <input type="text" name="metatitulo_{{ $idioma->id }}" id="metatitulo_{{ $idioma->id }}" class="form-control" value="{{ old('metatitulo_'.$idioma->id, $texto ? $texto->metatitulo : '') }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="metadescripcion_{{ $idioma->id }}" class="form-label">Meta descripción (SEO) [{{ strtoupper($idioma->etiqueta) }}]</label>
                                        <textarea name="metadescripcion_{{ $idioma->id }}" id="metadescripcion_{{ $idioma->id }}" class="form-control" rows="2">{{ old('metadescripcion_'.$idioma->id, $texto ? $texto->metadescripcion : '') }}</textarea>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" value="{{ old('nombre', $config->nombre ?? '') }}">
                        </div>
                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" name="direccion" id="direccion" class="form-control" value="{{ old('direccion', $config->direccion ?? '') }}">
                        </div>
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" name="telefono" id="telefono" class="form-control" value="{{ old('telefono', $config->telefono ?? '') }}">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $config->email ?? '') }}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header">Redes sociales</div>
                    <div class="card-body">
                        <div id="redes-sociales-list">
                            @php
                                $redes = old('redes_sociales', $config->redes_sociales ?? []);
                                if (!is_array($redes)) $redes = json_decode($redes, true) ?: [];
                            @endphp
                            @for($i = 0; $i < 6; $i++)
                                <div class="mb-3 border rounded p-2 mb-2 position-relative">
                                    <div class="row g-2 align-items-end">
                                        <div class="col-md-4 d-flex align-items-center gap-2">
                                            @if(isset($redes[$i]['icono']) && $redes[$i]['icono'])
                                                <img src="{{ asset('storage/' . $redes[$i]['icono']) }}" alt="icono" class="img-thumbnail me-2" style="max-width:40px; max-height:40px;">
                                            @endif
                                            <div class="w-100">
                                                <label class="form-label">Icono (150x150px)</label>
                                                <input type="file" name="redes_sociales[{{ $i }}][icono]" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label">URL</label>
                                            <input type="text" name="redes_sociales[{{ $i }}][url]" class="form-control" value="{{ $redes[$i]['url'] ?? '' }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Alt</label>
                                            <input type="text" name="redes_sociales[{{ $i }}][alt]" class="form-control" value="{{ $redes[$i]['alt'] ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="position-absolute top-0 end-0 mt-2 me-2">
                                        <button type="submit" name="eliminar_red" value="{{ $i }}" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta red social?')">Eliminar</button>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Guardar cambios</button>
    </form>
</div>
@stop
