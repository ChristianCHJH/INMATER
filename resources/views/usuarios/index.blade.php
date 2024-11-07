@extends('layouts.app')

@section('content')
<div class="container2 mx-auto mt-5">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Usuarios</h1>
        {!! pagination_links($pagination, 'tailwind') !!}
        <a href="{{ route('users.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Crear Usuario</a>
    </div>

    <form method="GET" action="{{ route('users.index') }}" class="mb-4">
        <input type="text" name="search" value="" placeholder="Buscar usuarios..." class="px-4 py-2 border rounded-md">
        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Buscar</button>
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200">
            <thead class="bg-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($pagination['data'] as $user)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $user['userx'] }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $user['mail'] }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('users.edit', ['id' => $user['id']]) }}" class="px-4 py-1 bg-blue-500 text-white rounded-md hover:bg-blue-600">Editar</a>
                        <form action="{{ route('users.destroy', ['id' => $user['id']]) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-1 bg-red-500 text-white rounded-md hover:bg-red-600">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {!! pagination_links($pagination, 'tailwind') !!}
    </div>
</div>
@endsection
