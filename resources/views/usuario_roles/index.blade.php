@extends('layouts.app')

@section('content')
    <h1>Roles de Usuario</h1>

    <a href="{{ route('usuario_roles.create') }}" class="btn btn-primary">Crear Nuevo Rol de Usuario</a>

    <table class="table mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($usuarioRoles as $usuarioRole)
            <tr>
                <td>{{ $usuarioRole->id }}</td>
                <td>{{ $usuarioRole->user->name }}</td>
                <td>{{ $usuarioRole->role->nombre }}</td>
                <td>
                    <a href="{{ route('usuario_roles.edit', ['id' => $usuarioRole->id]) }}" class="btn btn-sm btn-primary">Editar</a>
                    <form action="{{ route('usuario_roles.destroy', ['id' => $usuarioRole->id]) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este rol de usuario?')">Eliminar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection
