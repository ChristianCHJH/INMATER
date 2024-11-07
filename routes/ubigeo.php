<?php

use App\Http\Controllers\Usuario\UbigeoController; 

$router->group('ubigeo', function ($router) {
    $router->get('countries', [UbigeoController::class, 'getCountries'])->name('ubigeo.countries');
    $router->get('departments/{countryId}', [UbigeoController::class, 'getDepartments'])->name('ubigeo.departments');
    $router->get('provinces/{departmentId}', [UbigeoController::class, 'getProvinces'])->name('ubigeo.provinces');
    $router->get('districts/{provinceId}', [UbigeoController::class, 'getDistricts'])->name('ubigeo.districts');
});