<?php

namespace App\Http\Controllers\Usuario;
use App\Http\Controllers\Controller;
use App\Models\Usuario\Permission;
use Illuminate\Http\Request;
use Jenssegers\Blade\Blade;
use App\Http\Requests\PermissionRequest;

class PermissionController extends Controller
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
        $permissions = Permission::all();
        echo $this->blade->make('permissions.index', compact('permissions'));
    }

    public function create()
    {
        echo $this->blade->make('permissions.create');
    }

    public function store(PermissionRequest $request)
    {
        try {
            $validated = $request->validated();

            Permission::create($validated);

            echo "Permiso creado exitosamente.";
        } catch (\Exception $e) {
            echo "Errores de validación: " . $e->getMessage();
        }
    }

    public function edit($id)
    {
        $permission = Permission::find($id);
        echo $this->blade->make('permissions.edit', compact('permission'));
    }

    public function update(PermissionRequest $request, $id)
    {
        try {
            $validated = $request->validated();

            $permission = Permission::find($id);
            $permission->update($validated);

            echo "Permiso actualizado exitosamente.";
        } catch (\Exception $e) {
            echo "Errores de validación: " . $e->getMessage();
        }
    }

    public function destroy($id)
    {
        try {
            $permission = Permission::find($id);
            $permission->delete();

            echo "Permiso eliminado exitosamente.";
        } catch (\Exception $e) {
            echo "Error al eliminar el permiso: " . $e->getMessage();
        }
    }
}
