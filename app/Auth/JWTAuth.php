<?php

namespace App\Auth;
use App\Models\Usuario\Usuario;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTAuth {
    private static $secret_key;
    private static $encrypt = ['HS256'];
    private static $aud = null;

    public static function init() {
        if (session_status() == PHP_SESSION_NONE) {
                    session_start(); 
        }
        self::$secret_key = env('JWT_SECRET_KEY');
        //print_r(self::$secret_key); exit;
        if (self::$secret_key === false) {
            throw new \Exception('JWT_SECRET_KEY not set in .env file.');
        }
    }
    public static function getToken($data) {
        $time = time();

        $token = [
            'iat' => $time,
            'exp' => $time + (60 * 60), // 1 hora de expiración
            'data' => $data
        ];

        return JWT::encode($token, self::$secret_key, 'HS256');
    }

    public static function checkToken($token,$data) {
        if (empty($token)) {
            throw new \Exception("Invalid token supplied.");
        }

        $decoded = JWT::decode($token, new Key(self::$secret_key, 'HS256'));
         
        if ($decoded->exp < time()) {
            throw new \Exception("Token has expired.");
        }
        /******Seguridad extra con usuario Authenticado*******/
        if (!isset($_SESSION['login'])) {
            if (!isset($data['username'])) {
                throw new \Exception("El usuario es obligatorio!!!"); 
            }
            if (!isset($data['password'])) {
                throw new \Exception("La contraseña es obligatoria"); 
            }

        }
         
        $user = Usuario::where('email', $decoded->sub)->first();
        if (!$user) {
            throw new \Exception("Usuario no encontrado");
        }
        return $decoded;
    } 
}

JWTAuth::init();