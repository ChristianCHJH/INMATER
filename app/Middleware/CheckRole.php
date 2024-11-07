<?php
namespace App\Middleware;

use App\Models\Usuario\Usuario;

class CheckRole extends Middleware
{
    protected $roles;

    public function __construct($roles = [])
    { 
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $this->roles = $roles;
    }

    public function handle($request, $next)
    {
        $Usuarioname = isset($_SESSION['login']) ? $_SESSION['login'] : '';

        if (!$Usuarioname) {
            header("Location: /404");
            exit;
        }

        $Usuario = Usuario::where('userx', $Usuarioname)->first();

        if (!$Usuario || !$this->UsuarioHasRoles($Usuario, $this->roles)) { 
             header("Location: /404");
             exit;
        } 
        
        return $next($request);
    }

    protected function UsuarioHasRoles($Usuario, $roles)
    {
        foreach ($roles as $role) {
            if ($Usuario->roles()->where('nombre', $role)->exists()) { 
                return true;
            }
        }
        return false;
    }
}
