<?php 
use App\Http\Controllers\MedicalRecords\PatientController;
use App\Middleware\AuthMiddleware;

$router->group('paciente', function ($router) {
    $router->get('nuevo', [PatientController::class, 'create'])->name('paciente.paciente_create');
    $router->post('guardar', [PatientController::class, 'store'])->name('paciente.paciente_save');
},[AuthMiddleware::class]);