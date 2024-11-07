<?php

namespace App\Http\Controllers\Usuario;
use App\Http\Controllers\Controller;
use App\Models\Usuario\UsuarioRole;
use App\Models\Usuario\Usuario;
use App\Models\Usuario\Role;
use Illuminate\Http\Request;
use Jenssegers\Blade\Blade;
use App\Http\Requests\UsuarioRoleRequest;

class UsuarioRoleController extends Controller
{
    protected $blade;

    public function __construct(Blade $blade)
    {
        $this->blade = $blade;
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index()
    {
        $usuarioRoles = UsuarioRole::all();
        echo $this->blade->make('usuario_roles.index', compact('usuarioRoles'));
    }

    public function create()
    {
        $Usuarios = Usuario::all();
        $roles = Role::all();
        echo $this->blade->make('usuario_roles.create', compact('Usuarios', 'roles'));
    }

    public function store(UsuarioRoleRequest $request)
    {
        try {
            $validated = $request->validated();

            UsuarioRole::create($validated);

            echo "Rol de usuario almacenado exitosamente.";
        } catch (\Exception $e) {
            echo "Errores de validación: " . $e->getMessage();
        }
    }

    public function edit($id)
    {
        $usuarioRole = UsuarioRole::find($id);
        $Usuarios = Usuario::all();
        $roles = Role::all();
        echo $this->blade->make('usuario_roles.edit', compact('usuarioRole', 'Usuarios', 'roles'));
    }

    public function update(UsuarioRoleRequest $request, $id)
    {
        try {
            $validated = $request->validated();

            $usuarioRole = UsuarioRole::find($id);
            $usuarioRole->update($validated);

            echo "Rol de usuario actualizado exitosamente.";
        } catch (\Exception $e) {
            echo "Errores de validación: " . $e->getMessage();
        }
    }

    public function destroy($id)
    {
        try {
            $usuarioRole = UsuarioRole::find($id);
            $usuarioRole->delete();

            echo "Rol de usuario eliminado exitosamente.";
        } catch (\Exception $e) {
            echo "Error al eliminar el rol de usuario: " . $e->getMessage();
        }
    }
}
