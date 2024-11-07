@extends('layouts.app')

@section('content')
    <h1>Editar Rol</h1>

    <form action="{{ route('roles.update', ['id' => $role->id]) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $role->nombre }}" required>
        </div>
        <div class="form-group">
            <label for="descripcion">Descripción</label>
            <input type="text" class="form-control" id="descripcion" name="descripcion" value="{{ $role->descripcion }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar Rol</button>
    </form>
@endsection
