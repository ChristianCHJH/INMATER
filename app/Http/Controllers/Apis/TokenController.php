<?php

namespace App\Http\Controllers\Apis;

use App\Http\Requests\TokenUserRequest;
use App\Http\Controllers\Controller;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Jenssegers\Blade\Blade;
use App\Core\Request;
use App\Helpers\ResponseHelper;

class TokenController extends Controller
{
    private $secretKey;
    protected $blade;

    public function __construct(Blade $blade)
    {
        $this->blade = $blade;
        
		if (session_status() == PHP_SESSION_NONE) {
            session_start(); // Inicia la sesión si no está iniciada
        }
        $this->secretKey = env('JWT_SECRET_KEY');
    }

    /**
     * Muestra el formulario para generar un token JWT.
     */
    public function showGenerateTokenForm()
    {
        echo $this->blade->make('apis.generate-token')->render(); exit; 
    }
    /**
     * Genera un token JWT.
     * @param Request $request La solicitud que contiene los parámetros para el token.
     * @return string JSON con el token generado.
     */
    public function generateToken(TokenUserRequest $request2)
    {  
        $response['status'] = false;
        try {

            $validatemsj = [];
            $validatedData = $request2->validate($validatemsj); 
            //print_r($validatedData); exit;
            $algorithm = $validatedData['algorithm']; 
            $expiresIn = $validatedData['expires_in']; 
            // Configura la fecha de expiración
            $issuedAt = time();
            $expiration = $expiresIn ? $issuedAt + $expiresIn : 0;

            $token = [
                'iat' => $issuedAt,
                'exp' => $expiration,
                'data' => $validatedData['payload']
            ];

            $jwt = JWT::encode($token, $this->secretKey, $algorithm);
            $response['token'] = $jwt;
            $response['status'] = true;
            $response['message'] = 'Token creado correctamente'; 
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage();
        }

        return ResponseHelper::json($response); 
    }
}
