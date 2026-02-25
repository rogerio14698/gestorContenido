@extends('layouts.app')

@section('title', __('Contacto') . ' - ' . ($configuracion->nombre_empresa ?? 'Nuntris Teatro'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <h1 class="mb-4 fw-bold">{{ __('Contacto') }}</h1>
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <form method="POST" action="{{ route('contacto.enviar') }}" class="card shadow-sm p-4 bg-light">
                @csrf
                <div class="mb-3">
                    <label for="nombre" class="form-label">{{ __('Nombre y apellidos') }}</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required maxlength="100" value="{{ old('nombre') }}">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('Correo electrónico') }}</label>
                    <input type="email" class="form-control" id="email" name="email" required maxlength="100" value="{{ old('email') }}">
                </div>
                <div class="mb-3">
                    <label for="telefono" class="form-label">{{ __('Teléfono') }}</label>
                    <input type="tel" class="form-control" id="telefono" name="telefono" maxlength="30" value="{{ old('telefono') }}">
                </div>
                <div class="mb-3">
                    <label for="mensaje" class="form-label">{{ __('Cuéntanos') }}</label>
                    <textarea class="form-control" id="mensaje" name="mensaje" rows="5" required>{{ old('mensaje') }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-lg">{{ __('Enviar') }}</button>
            </form>
        </div>
    </div>
</div>
@endsection
