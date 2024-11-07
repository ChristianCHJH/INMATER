<?php
namespace App\Core;

use App\Middleware\JWTMiddleware;

class Router {
    private $routes = [];
    private $namedRoutes = [];
    private $container;
    private $prefix = '';

    public function __construct(Container $container) {
        $this->container = $container;
    }

    public function add($route, $action) {
        $this->routes[$route] = $action;
    }

    public function get($uri, $controller, $middleware = [], $request = null) {
        return $this->addRoute('GET', $uri, $controller, $middleware, $request);
    }

    public function post($uri, $controller, $middleware = [], $request = null) {
        return $this->addRoute('POST', $uri, $controller, $middleware, $request);
    }

    public function put($uri, $controller, $middleware = [], $request = null) {
        return $this->addRoute('PUT', $uri, $controller, $middleware, $request);
    }

    public function delete($uri, $controller, $middleware = [], $request = null) {
        return $this->addRoute('DELETE', $uri, $controller, $middleware, $request);
    }

    public function group($prefix, $callback, $middleware = []) {
        if (!is_array($middleware)) {
            throw new \InvalidArgumentException("El middleware debe ser un array.");
        }

        $originalPrefix = $this->prefix;
        $this->prefix = rtrim($originalPrefix, '/') . '/' . trim($prefix, '/') . '/';

        $callback($this);

        $this->prefix = $originalPrefix;

        // Aplicar middleware después de definir las rutas
        $this->applyGroupMiddleware($middleware);
    }

    protected function applyGroupMiddleware($middleware) {
        foreach ($this->routes as &$methodRoutes) {
            if (!is_array($methodRoutes)) {
                continue;
            }
            
            foreach ($methodRoutes as &$route) {
                if (is_string($route)) {
                    $route = [
                        'controller' => $route,
                        'middleware' => [],
                        'request' => null,
                    ];
                }
    
                // Aplicar el middleware del grupo a cada ruta
                $route['middleware'] = array_merge($middleware, $route['middleware'] ?? []);
            }
        }
    }

    public function prefix($prefix) {
        $this->prefix = rtrim($prefix, '/') . '/';
    }

    protected function addRoute($method, $uri, $controller, $middleware, $request = null) {
        // Normalizar URI
        $uri = '/' . trim($uri, '/');

        // Añadir prefijo a la URI si está configurado
        $uri = $this->prefix ? rtrim($this->prefix, '/') . '/' . ltrim($uri, '/') : $uri;

        $route = [
            'controller' => $controller,
            'middleware' => $middleware,
            'request' => $request,
        ];

        $this->routes[$method][$uri] = $route;

        return new Route($uri, $route, $this); // Pasar la instancia de Router a la clase Route
    }

    public function dispatch(Request $request) {
        // echo '<pre>';
        // print_r($this->routes);
        // echo '</pre>'; 
        // exit;
        $method = $request->getMethod();
        $uri = $request->getPath();
        $route = $this->matchRoute($method, $uri);

        if ($route) {
            $this->handleRoute($route, $request);
        } else {
            header("Location: /404");
            exit;
        }
    }

    private function handleRoute($route, Request $request) {
        $controllerInfo = $route['controller'];

        if (is_string($controllerInfo)) {
            $controllerInfo = explode('@', $controllerInfo);
        }

        $controllerName = $controllerInfo[0];
        $controllerMethod = $controllerInfo[1];

        try {
            $controller = $this->container->get($controllerName);
        } catch (\Exception $e) {
            echo "Error al obtener el controlador: " . $e->getMessage();
            return;
        }

        $middleware = $route['middleware'] ?? [];
        $params = $route['params'] ?? [];
        $requestInstance = $this->resolveRequestInstance($route, $request);

        if (!empty($middleware)) {
            $this->applyMiddleware($middleware, $controller, $controllerMethod, $requestInstance, $params);
        } else {
            $controller->$controllerMethod(...$params);
        }
    }

    private function resolveRequestInstance($route, Request $request) {
        if (isset($route['request']) && !empty($route['request'])) {
            $requestClass = $route['request'];
            try {
                return $this->container->get($requestClass);
            } catch (\Exception $e) {
                echo "Error al obtener la clase de request: " . $e->getMessage();
                return $request;
            }
        }

        return $request;
    }

    private function matchRoute($method, $uri) {
        if (!isset($this->routes[$method])) {
            return null;
        }

        foreach ($this->routes[$method] as $routeUri => $route) {
            $regex = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[a-zA-Z0-9_]+)', $routeUri);
            $regex = '#^' . $regex . '$#';
            if (preg_match($regex, $uri, $matches)) {
                $route['params'] = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                return $route;
            }
        }

        return null;
    }

    public function applyMiddleware($middleware, $controller, $controllerMethod, $request) {
        $next = function ($request) use ($controller, $controllerMethod) {
            return $controller->$controllerMethod($request);
        };

        $middlewareHandler = array_reduce(array_reverse($middleware), function ($next, $middleware) {
            return function ($request) use ($next, $middleware) {
                $middlewareInstance = $this->createMiddlewareInstance($middleware);
                return $middlewareInstance->handle($request, $next);
            };
        }, $next);

        $middlewareHandler($request);
    }

    private function createMiddlewareInstance($middleware) {
        if (is_array($middleware)) {
            $middlewareClass = $middleware[0];
            $middlewareParams = $middleware[1];
        } else {
            $middlewareClass = $middleware;
            $middlewareParams = [];
        }

        if ($middlewareClass === JWTMiddleware::class) {
            return new $middlewareClass($this); // Pasa el Router al JWTMiddleware
        }

        return new $middlewareClass(...$middlewareParams);
    }

    public function getRoutesWithMiddleware($middlewareClass) {
        $routesWithMiddleware = [];
        
        foreach ($this->routes as $method => $routes) {
            if (!is_array($routes)) {
                continue; // Ignorar valores que no sean arrays
            }
            foreach ($routes as $uri => $route) { 
                if (isset($route['middleware']) && is_array($route['middleware'])) { 
                    foreach ($route['middleware'] as $middleware) {
                        $middlewareClassInstance = is_array($middleware) ? $middleware[0] : $middleware;
                        if ($this->getMiddlewareClassName($middlewareClassInstance) === $middlewareClass) {
                            $routesWithMiddleware[] = $uri;
                            break 2; // Salir del loop ya que hemos encontrado la coincidencia
                        }
                    }
                }
            }
        }
        
        return $routesWithMiddleware;
    }

    private function getMiddlewareClassName($middleware) {
        if (is_object($middleware)) {
            return get_class($middleware);
        } elseif (is_string($middleware)) {
            return $middleware;
        }
        return null;
    } 

    public function name($name, $uri) {
        $this->namedRoutes[$name] = $uri;
    }

    public function getNamedRoute($name) {
        return isset($this->namedRoutes[$name]) ? $this->namedRoutes[$name] : null;
    }
}
