<?php
namespace App\Middleware;

use App\Core\Request;

class AuthMiddleware
{
    public function handle(Request $request, $next)
    {
         // Iniciar la sesión si no está iniciada
         if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Verificar si el usuario está autenticado
        if (!isset($_SESSION['login'])) {
            // Evitar redirigir si ya estamos en la página de inicio de sesión
            if ($request->getPath() !== '/index.php' && $request->getPath() !== '/validar-login') {
                // Registrar la redirección para depuración
                error_log('No autenticado. Redirigiendo a /index.php'); 
                // Redirigir al usuario a la página de inicio de sesión
                header('Location: /index.php');
                exit;
            }
        }

        // Si el usuario está autenticado, continuar con la solicitud
        return $next($request);
    }
}
