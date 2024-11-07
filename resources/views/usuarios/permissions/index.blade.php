@extends('layouts.app')

@section('content')
    <h1>Permisos</h1>

    <a href="{{ route('permissions.create') }}" class="btn btn-primary">Crear Nuevo Permiso</a>

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
            @foreach($permissions as $permission)
            <tr>
                <td>{{ $permission->id }}</td>
                <td>{{ $permission->nombre }}</td>
                <td>{{ $permission->descripcion }}</td>
                <td>
                    <a href="{{ route('permissions.edit', ['id' => $permission->id]) }}" class="btn btn-sm btn-primary">Editar</a>
                    <form action="{{ route('permissions.destroy', ['id' => $permission->id]) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este permiso?')">Eliminar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection
