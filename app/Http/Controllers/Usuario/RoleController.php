<?php

namespace App\Http\Controllers\Usuario;
use App\Http\Controllers\Controller;
use App\Models\Usuario\Role;
use Illuminate\Http\Request;
use Jenssegers\Blade\Blade;
use App\Http\Requests\RoleRequest;

class RoleController extends Controller
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
        $roles = Role::all();
        echo $this->blade->make('roles.index', compact('roles'));
    }

    public function create()
    {
        echo $this->blade->make('roles.create');
    }

    public function store(RoleRequest $request)
    {
        try {
            $validated = $request->validated();

            Role::create($validated);

            echo "Rol creado exitosamente.";
        } catch (\Exception $e) {
            echo "Errores de validación: " . $e->getMessage();
        }
    }

    public function edit($id)
    {
        $role = Role::find($id);
        echo $this->blade->make('roles.edit', compact('role'));
    }

    public function update(RoleRequest $request, $id)
    {
        try {
            $validated = $request->validated();

            $role = Role::find($id);
            $role->update($validated);

            echo "Rol actualizado exitosamente.";
        } catch (\Exception $e) {
            echo "Errores de validación: " . $e->getMessage();
        }
    }

    public function destroy($id)
    {
        try {
            $role = Role::find($id);
            $role->delete();

            echo "Rol eliminado exitosamente.";
        } catch (\Exception $e) {
            echo "Error al eliminar el rol: " . $e->getMessage();
        }
    }
}
