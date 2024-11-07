@extends('layouts.app')

@section('content')
    <h1>Roles</h1>

    <a href="{{ route('roles.create') }}" class="btn btn-primary">Crear Nuevo Rol</a>

    <table class="table mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($roles as $role)
            <tr>
                <td>{{ $role->id }}</td>
                <td>{{ $role->nombre }}</td>
                <td>{{ $role->descripcion }}</td>
                <td>
                    <a href="{{ route('roles.edit', ['id' => $role->id]) }}" class="btn btn-sm btn-primary">Editar</a>
                    <form action="{{ route('roles.destroy', ['id' => $role->id]) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este rol?')">Eliminar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection
