<?php 

namespace App\Core;

class Container {
    private $bindings = [];
    protected $instances = [];

    public function has($abstract) {
        return isset($this->bindings[$abstract]);
    }

    public function set($name, $resolver)
    {
        $this->instances[$name] = $resolver;
    }

    public function get($name)
    {
        if (isset($this->instances[$name])) {
            return call_user_func($this->instances[$name]);
        }

        throw new \Exception("{$name} not found in container.");
    }
}

// namespace App\Core;

// use Psr\Container\ContainerInterface;

// class Container implements ContainerInterface {
//     protected static $instance;
//     protected $bindings = [];

//     public function __construct() {
//         static::$instance = $this;
//     }

//     public static function getInstance() {
//         return static::$instance;
//     }

//     public function set($name, $resolver) {
//         $this->bindings[$name] = $resolver;
//     }

//     public function get($name) {
//         if (isset($this->bindings[$name])) {
//             return $this->bindings[$name]($this);
//         }

//         throw new \Exception("No entry or class found for '$name'");
//     }

//     public function has($name) {
//         return isset($this->bindings[$name]);
//     }
// }
