<?php

namespace App\Http\Controllers\Usuario;
use App\Http\Controllers\Controller;
use App\Models\Usuario\Usuario;
use App\Models\Usuario\UsuarioLog;
use Jenssegers\Blade\Blade;
use App\Http\Requests\UsuarioRequest;
use App\Models\Usuario\Role;
use App\Helpers\ResponseHelper;
use App\Services\ApiSunatService; 
use App\Core\Request;

class UserController extends Controller
{
    protected $blade;
    protected $apiService;

    public function __construct(Blade $blade)
    {
        $this->blade = $blade;
        if (session_status() == PHP_SESSION_NONE) {
            session_start(); 
        } 
    }
    
    public function index()
    {
        $search = $_GET['search'] ?? '';
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    
        if ($search) {
            $Usuarios = Usuario::where('Usuario', 'like', '%' . $search . '%')
                ->orWhere('mail', 'like', '%' . $search . '%')
                ->orderBy('id', 'desc')
                ->get();
        } else {
            $Usuarios = Usuario::orderBy('id', 'desc')->get();
        }
     
        $UsuariosArray = $Usuarios->toArray();
    
        $pagination = paginate($UsuariosArray, 10, $currentPage, '/Usuarios');
        echo $this->blade->make('Usuarios.index', compact('pagination'))->render();
        exit;
    }

    public function show(Request $request)
    {
        $data = $request->getParams($request); 
        $Usuario = Usuario::find($data['id']);

        if (!$Usuario) { 
            echo $this->blade->make('errors.404')->render();
            exit;
        }

        echo $this->blade->make('Usuarios.show', compact('Usuario'))->render();
        exit;
    }
    public function create()
    {
        $roles = Role::all();
        echo $this->blade->make('Usuarios.create', compact('roles'))->render(); exit;
    }

    public function store(UsuarioRequest $request)
    {
        $response['status'] = false;
        try {
            $validatemsj = [];
            $validatedData = $request->validate($validatemsj); 
            
            $Usuario = Usuario::create([
                'Usuario' => $validatedData['userx'],
                'mail' => $validatedData['mail'],
                'pass' => $validatedData['pass'],
                'nom' => $validatedData['nom'],
                'cmp' => $validatedData['cmp'],
                'role' => $validatedData['role'],
            ]); 

            if (isset($validatedData['role'])) {
                $Usuario->roles()->attach($validatedData['role']);
            }

            $response['status'] = true;
            $response['message'] = 'Usuario creado correctamente';
        } catch (\Exception $e) { 
            $response['message'] = $e->getMessage();
        }
    
        return ResponseHelper::json([
            'success' => $response['status'],
            'message' => $response['message'],
            'html' => $validatemsj
        ]); 
    }

    public function edit(Request $request)
    {
        $roles = Role::all();
        $data = $request->getParams($request); 
        $Usuario = Usuario::find($data['id']); 
        //print_r($Usuario->id); exit;
        echo $this->blade->make('Usuarios.edit', compact('Usuario','roles'))->render();exit;
    }

    public function update($req)
    {
        $request = new UsuarioRequest(); 
        $id = $req->param('id');
        $validatemsj = [];
        $response['status'] = false;

        try { 
            $request->setUpdate(true);                                   
            $request->validate($validatemsj); 
            $Usuario = Usuario::find($id);
            $Usuario->update($request->all());
            $response['status'] = true;
            $response['message'] = 'Usuario modificado correctamente';
        } catch (\Exception $e) { 
            $response['message'] = $e->getMessage();
        }

        return ResponseHelper::json([
            'success' => $response['status'],
            'message' => $response['message'],
            'html' => $validatemsj
        ]);  
    }

    public function destroy($id)
    {
        $Usuario = Usuario::find($id);
        $Usuario->delete();
 
        header("Location: /Usuarios");
        exit;
    }

    
}
