@extends('layouts.app')

@section('content')
    <h1>Crear Nuevo Permiso</h1>

    <form action="{{ route('permissions.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="form-group">
            <label for="descripcion">Descripción</label>
            <input type="text" class="form-control" id="descripcion" name="descripcion" required>
        </div>
        <button type="submit" class="btn btn-primary">Crear Permiso</button>
    </form>
@endsection
