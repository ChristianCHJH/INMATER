<?php

namespace App\Core; 
use App\Core\FormRequest;
class Request
{
    protected $headers;
    protected $request;
    public $user; 
    public $currentRoute;
     

    public function __construct($currentRoute = null)
    {
        $this->request = $_REQUEST;
        $this->headers = $this->getHeaders(); 
        $this->currentRoute = $currentRoute; 
    }

    public function all()
    {
        return $this->request;
    }

    public function get($key, $default = null)
    {
        return $this->input($key, $default);
    }
    
    public function input($key, $default = null)
    {
        return $this->request[$key] ?? $default;
    }

    public function has($key)
    {
        return isset($this->request[$key]);
    }

    public function only(array $keys)
    {
        return array_intersect_key($this->request, array_flip($keys));
    }

    public function except(array $keys)
    {
        return array_diff_key($this->request, array_flip($keys));
    }
    public function getHeaders() { 
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (strpos($name, 'HTTP_') === 0) {
                $headerName = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                $headers[$headerName] = $value;
            }
        }
    
        // También intenta acceder a encabezados usando 'X-' prefijos si no encuentras Authorization
        if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $headers['Authorization'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }
    
        return $headers;
    }

    public function getHeader($name) {
        $name = ucwords(strtolower($name), '-');
        return isset($this->headers[$name]) ? $this->headers[$name] : null;
    }

    public function allParams()
    {
        return $this->params;
    }
    public function param($key)
    {
        return $this->params[$key] ?? null;
    }
    public function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }
    public function getParams(Request $request){
        return $request->params;
    }
    public function getPath()
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }
    public function user()
    {
        // Aquí deberías implementar la lógica para obtener el usuario actualmente autenticado
        // Puedes adaptar esto según cómo manejas la autenticación en tu aplicación PHP nativa
        // Por ejemplo, podrías usar sesiones, JWT, o cualquier otro método de autenticación
        return $_SESSION['user'] ?? null; // Ejemplo básico usando sesión
    }
    public function getRequestUri()
    {
        // Devuelve la URI de la solicitud
        return $_SERVER['REQUEST_URI'] ?? '';
    }
    
    public function validateForm()
    {
        $form = new FormRequest();
        $form->validate();
    }
}
