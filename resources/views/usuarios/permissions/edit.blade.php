@extends('layouts.app')

@section('content')
    <h1>Editar Permiso</h1>

    <form action="{{ route('permissions.update', ['id' => $permission->id]) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $permission->nombre }}" required>
        </div>
        <div class="form-group">
            <label for="descripcion">Descripción</label>
            <input type="text" class="form-control" id="descripcion" name="descripcion" value="{{ $permission->descripcion }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar Permiso</button>
    </form>
@endsection
