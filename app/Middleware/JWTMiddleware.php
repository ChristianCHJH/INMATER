<?php 
namespace App\Middleware;

use App\Auth\JWTAuth;
use App\Core\Request;
use App\Core\Router;
use Exception;

class JWTMiddleware
{
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function handle($request, $next)
    {
        $authorizationHeader = $request->getHeader('Authorization');
        $data = $request->getHeaders();
        
        $currentRoute = $request->getPath(); // Usa getPath() para obtener la ruta actual

        // Obtener todas las rutas que tienen el JWTMiddleware
        $protectedRoutes = $this->router->getRoutesWithMiddleware(JWTMiddleware::class);
        //print_r($protectedRoutes ); exit;
        // Verifica si la ruta actual está protegida
        if (in_array($currentRoute, $protectedRoutes)) {
            $token = str_replace('Bearer ', '', $authorizationHeader);

            try {
                if (!$authorizationHeader) {
                    http_response_code(401);
                    echo json_encode(['error' => 'Token not provided JWT']);
                    exit;
                }
                $user = JWTAuth::checkToken($token, $data);
                $request->user = $user;
                return $next($request);
            } catch (Exception $e) {
                http_response_code(401);
                echo json_encode(['error' => $e->getMessage()]);
                exit;
            }
        } else {
            return $next($request);
        }
    }
}
