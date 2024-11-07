<?php 
use App\Http\Controllers\MedicalRecords\PartnerController;

$router->group('parejas', function ($router) {
    $router->get('/', [PartnerController::class, 'index'])->name('pareja.create');
    $router->post('guardar', [PartnerController::class, 'storage'])->name('pareja.save');
});