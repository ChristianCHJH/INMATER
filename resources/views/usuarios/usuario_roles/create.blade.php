@extends('layouts.app')

@section('content')
    <h1>Crear Nuevo Rol de Usuario</h1>

    <form action="{{ route('usuario_roles.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="usuario_id">Usuario</label>
            <select class="form-control" id="usuario_id" name="usuario_id" required>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="role_id">Rol</label>
            <select class="form-control" id="role_id" name="role_id" required>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->nombre }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Crear Rol de Usuario</button>
    </form>
@endsection
