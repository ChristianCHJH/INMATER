<?php
 

namespace App\Helpers;

class SessionHelper
{
    public static function get($key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function put($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function flash($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function has($key)
    {
        return isset($_SESSION[$key]);
    }

    public static function pull($key, $default = null)
    {
        $value = $_SESSION[$key] ?? $default;
        unset($_SESSION[$key]);
        return $value;
    }
}

// Crear una función global `session()`
if (!function_exists('session')) {
    function session($key = null, $default = null) {
        if (is_null($key)) {
            return $_SESSION;
        }

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                SessionHelper::put($k, $v);
            }
        } else {
            return SessionHelper::get($key, $default);
        }
    }
}
 
require_once __DIR__ . '/SessionHelper.php';
