<?php 
use App\Middleware\AuthMiddleware;  
use App\Middleware\CheckRole; 

// Rutas públicas
$router->get('/', App\Http\Controllers\HomeController::class . '@index')->name("home");
$router->get('/index.php', App\Http\Controllers\HomeController::class . '@index')->name("home");
$router->post('/validar-login', App\Http\Controllers\HomeController::class . '@validate');
$router->get('/forbidden', App\Http\Controllers\HomeController::class . '@forbidden');
$router->get('/404', App\Http\Controllers\HomeController::class . '@unauthorized')->name('404');


$router->get('/medios-comunicacion/{elegido}', App\Http\Controllers\MediaController::class . '@list')->name('media.list');

// Rutas protegidas
$router->get('/admin', App\Http\Controllers\HomeController::class . '@admin', [
    [AuthMiddleware::class, []],
    [CheckRole::class, [['Administrador', 'editor']]]
])->name("admin");

require "users.php";
require "listas.php";
require "paciente.php";
require "ubigeo.php";
require "apis.php";
require "mails.php";

return $router;