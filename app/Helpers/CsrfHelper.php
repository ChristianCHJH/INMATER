<?php

namespace App\Helpers;

class CsrfHelper
{
    public static function generateToken()
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        // Generar un token único
        $token = bin2hex(random_bytes(32));

        // Almacenar el token en la sesión
        $_SESSION['_csrf_token'] = $token;

        return $token;
    }

    public static function validateToken($token)
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        if (isset($_SESSION['_csrf_token']) && hash_equals($_SESSION['_csrf_token'], $token)) {
            unset($_SESSION['_csrf_token']); // Eliminar el token después de la validación
            return true;
        }

        return false;
    }

    public static function getToken()
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        return $_SESSION['_csrf_token'] ?? null;
    }
}
