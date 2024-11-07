<?php
use App\Http\Controllers\Apis\SunatController; 
Use App\Http\Controllers\Apis\TokenController;
use App\Http\Requests\TokenUserRequest; 

//api para crear token
$router->get('/generate-token', [App\Http\Controllers\Apis\TokenController::class, 'showGenerateTokenForm'])->name('generate-token-form');
//$router->post('/generate-token', [App\Http\Controllers\Apis\TokenController::class, 'generateToken'])->name('generate-token');
$router->post('/generate-token', TokenController::class . '@generateToken',$middlewares,TokenUserRequest::class)->name('generate-token');;
 


// Rutas de la API de Sunat
$router->group('sunat', function ($router) {
    $router->get('api-sunat', SunatController::class . '@apisunat')->name("apisunat"); 
    $router->get('document-type/{tipo}/{numero}', SunatController::class . '@showDocumentData');
    $router->get('showDniData/{dni}', SunatController::class . '@showDniData'); 
    $router->get('showPassportData/{passport}', SunatController::class . '@showPassportData');
    $router->get('getResidencyCard/{card}', SunatController::class . '@getResidencyCard');
    $router->get('showRucData/{ruc}', SunatController::class . '@showRucData');
    $router->get('validar-persona/{tipo}/{numero}', [SunatController::class, 'validarPersona']);
}); // , [App\Middleware\JWTMiddleware::class]

