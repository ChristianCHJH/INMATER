<?php
// app/Core/Route.php

namespace App\Core;

class Route {
    protected $uri;
    protected $route;
    protected $router; // Propiedad para almacenar la instancia de Router

    public function __construct($uri, $route, Router $router) {
        $this->uri = $uri;
        $this->route = $route;
        $this->router = $router; // Asignar la instancia de Router
    }

    public function name($name) {
        $this->router->name($name, $this->uri); // Llamar al método name de Router
        return $this;
    }
}
