<?php
require 'vendor/autoload.php';
use Illuminate\Database\Capsule\Manager as Capsule;
use App\Core\Container;
use App\Core\Router; 
use Jenssegers\Blade\Blade;
use App\Core\Request;
use App\Core\ValidationException; 
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$capsule = new Capsule;
$config = require 'config/database.php';

foreach ($config['connections'] as $key => $item) {
    $db_config[$key] = $capsule->addConnection($item); 
}

$capsule->setAsGlobal();
$capsule->bootEloquent();

// Definir constantes para los esquemas
define('SCHEMA_APPINMATER_MODULO', $config['connections']['appinmater_modulo']['schema']);
define('SCHEMA_APPINMATER_GENESIS', $config['connections']['appinmater_genesis']['schema']);
define('SCHEMA_APPINMATER_MIFACT', $config['connections']['appinmater_mifact']['schema']);
define('SCHEMA_APPINMATER_LOG', $config['connections']['appinmater_log']['schema']);
define('SCHEMA_INMATER_FARMACIA_01', $config['connections']['inmater_farmacia_01']['schema']);

// Configura el contenedor de inyección de dependencias
$container = new Container();

// Configura la instancia de Blade
$viewsPath = __DIR__ . '/resources/views';
$cachePath = __DIR__ . '/storage/cache/views';
$blade = new Blade($viewsPath, $cachePath);

// Incluye el helper asset en Blade
require_once __DIR__ . '/app/Helpers/helpers.php';
$blade->directive('asset', function($path) {
    return "<?php echo asset($path); ?>";
});

//manejo de sessiones como el laravel
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Inicia la sesión si no está iniciada
}

// bootstrap/helpers.php
if (!function_exists('session')) {
    function session($key = null, $default = null) {
        if (is_null($key)) {
            return $_SESSION;
        }

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                App\Helpers\SessionHelper::put($k, $v);
            }
        } else {
            return App\Helpers\SessionHelper::get($key, $default);
        }
    }
}

// Incluir el helper globalmente
require_once __DIR__ . '/app/Helpers/SessionHelper.php';

// Registra Blade en el contenedor
$container->set(Blade::class, function() use ($blade) {
    return $blade;
});

// Función para registrar controladores automáticamente
function registerControllers(Container $container, Blade $blade, $namespace, $directory) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $relativePath = substr($file->getPathname(), strlen($directory) + 1, -4);
            $className = str_replace(DIRECTORY_SEPARATOR, '\\', $relativePath);
            $fullClassName = $namespace . '\\' . $className;

            // Verificar si la clase es un controlador
            if (is_subclass_of($fullClassName, 'App\Http\Controllers\Controller')) {
                $container->set($fullClassName, function() use ($blade, $fullClassName) {
                    return new $fullClassName($blade);
                });
            }
        }
    }
}

// Registrar todos los controladores en el directorio app/Http/Controllers
registerControllers($container, $blade, 'App\Http\Controllers', __DIR__ . '/app/Http/Controllers');

// Registra Request en el contenedor
$container->set(Request::class, function() {
    return new Request();
});

// Función para registrar automáticamente todos los requests
function registerRequests(Container $container, $namespace, $directory) {
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
    foreach ($files as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $className = $namespace . '\\' . str_replace('/', '\\', substr($file->getPathname(), strlen($directory) + 1, -4));
            if (class_exists($className)) {
                $container->set($className, function() use ($className) {
                    return new $className();
                });
            }
        }
    }
}

// Registrar automáticamente todos los requests en el contenedor
registerRequests($container, 'App\\Http\\Requests', __DIR__ . '/app/Http/Requests');

// Cargar automáticamente todos los helpers
$helperDir = __DIR__ . '/app/Helpers';
foreach (glob($helperDir . '/*.php') as $filename) {
    require_once $filename;
}

// Crea una instancia del Router y pasa el contenedor
$router = new Router($container);
$container->set(Router::class, function() use ($router) {
    return $router;
});

// Crear instancia de Request
$request = new Request();
 
 
// Incluye las rutas definidas en routes\web.php
require __DIR__ . '/routes/web.php';

// Procesa la solicitud actual
$router->dispatch($request);
?>
