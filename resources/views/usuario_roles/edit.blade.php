@extends('layouts.app')

@section('content')
    <h1>Editar Rol de Usuario</h1>

    <form action="{{ route('usuario_roles.update', ['id' => $usuarioRole->id]) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="usuario_id">Usuario</label>
            <select class="form-control" id="usuario_id" name="usuario_id" required>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @if($usuarioRole->user_id == $user->id) selected @endif>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="role_id">Rol</label>
            <select class="form-control" id="role_id" name="role_id" required>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" @if($usuarioRole->role_id == $role->id) selected @endif>{{ $role->nombre }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar Rol de Usuario</button>
    </form>
@endsection
