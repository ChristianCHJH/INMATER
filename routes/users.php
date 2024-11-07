<?php

use App\Core\Router;
use App\Middleware\AuthMiddleware;
use App\Middleware\CheckRole;

// Middleware configuration
$middlewares = [
    [CheckRole::class, [['Administrador']]],
];

// Rutas para Users
$router->get('/users', App\Http\Controllers\Usuario\UserController::class . '@index', $middlewares)->name('users.index');
$router->get('/users/create', App\Http\Controllers\Usuario\UserController::class . '@create', $middlewares)->name('users.create'); 
$router->post('/users', App\Http\Controllers\Usuario\UserController::class . '@store', $middlewares, App\Http\Requests\UsuarioRequest::class)->name('users.store');
$router->get('/users/{id}/edit', App\Http\Controllers\Usuario\UserController::class . '@edit', $middlewares)->name('users.edit');
$router->post('/users/update/{id}', App\Http\Controllers\Usuario\UserController::class . '@update', $middlewares)->name('users.update');
$router->delete('/users/{id}', App\Http\Controllers\Usuario\UserController::class . '@destroy', $middlewares)->name('users.destroy');

// Rutas para Roles
$router->get('/roles', App\Http\Controllers\Usuario\RoleController::class . '@index', $middlewares)->name('roles.index');
$router->get('/roles/create', App\Http\Controllers\Usuario\RoleController::class . '@create', $middlewares)->name('roles.create');
$router->post('/roles', App\Http\Controllers\Usuario\RoleController::class . '@store', $middlewares)->name('roles.store');
$router->get('/roles/{id}/edit', App\Http\Controllers\Usuario\RoleController::class . '@edit', $middlewares)->name('roles.edit');
$router->put('/roles/{id}', App\Http\Controllers\Usuario\RoleController::class . '@update', $middlewares)->name('roles.update');
$router->delete('/roles/{id}', App\Http\Controllers\Usuario\RoleController::class . '@destroy', $middlewares)->name('roles.destroy');

// Rutas para Permissions
// $router->get('/permissions', App\Http\Controllers\PermissionController::class . '@index', $middlewares)->name('permissions.index');
// $router->get('/permissions/create', App\Http\Controllers\PermissionController::class . '@create', $middlewares)->name('permissions.create');
// $router->post('/permissions', App\Http\Controllers\PermissionController::class . '@store', $middlewares)->name('permissions.store');
// $router->get('/permissions/{id}/edit', App\Http\Controllers\PermissionController::class . '@edit', $middlewares)->name('permissions.edit');
// $router->put('/permissions/{id}', App\Http\Controllers\PermissionController::class . '@update', $middlewares)->name('permissions.update');
// $router->delete('/permissions/{id}', App\Http\Controllers\PermissionController::class . '@destroy', $middlewares)->name('permissions.destroy');

// Rutas para UsuarioRoles
$router->get('/usuario_roles', App\Http\Controllers\Usuario\UsuarioRoleController::class . '@index', $middlewares)->name('usuario_roles.index');
$router->get('/usuario_roles/create', App\Http\Controllers\Usuario\UsuarioRoleController::class . '@create', $middlewares)->name('usuario_roles.create');
$router->post('/usuario_roles', App\Http\Controllers\Usuario\UsuarioRoleController::class . '@store', $middlewares)->name('usuario_roles.store');
$router->get('/usuario_roles/{id}/edit', App\Http\Controllers\Usuario\UsuarioRoleController::class . '@edit', $middlewares)->name('usuario_roles.edit');
$router->put('/usuario_roles/{id}', App\Http\Controllers\Usuario\UsuarioRoleController::class . '@update', $middlewares)->name('usuario_roles.update');
$router->delete('/usuario_roles/{id}', App\Http\Controllers\Usuario\UsuarioRoleController::class . '@destroy', $middlewares)->name('usuario_roles.destroy');
