@extends('layouts.forbidden')

@section('title', 'Error 404 - Acceso no autorizado')

@section('content')
    <div class="d-flex justify-content-center align-items-center min-vh-100 flex-column text-center">
        <h1 class="display-1"><code>Acceso Denegado</code></h1>
        <hr class="my-4" style="width: 50%; margin: auto;">
        <h3>No tienes permiso para ver esta página</h3>
        <h3>🚫🚫🚫🚫</h3>
        <h6><strong>Codigo de error</strong>: 403 Forbidden</h6>
    </div>
@endsection