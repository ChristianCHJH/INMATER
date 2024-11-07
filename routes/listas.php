<?php
$router->get('/lista.php', 'App\Http\Controllers\HomeController@lista');
$router->add('/lista_facturacion.php', 'App\Http\Controllers\HomeController@listaFacturacion');
$router->add('/lista_genomics.php', 'App\Http\Controllers\HomeController@listaGenomics');
$router->add('/lista_ecografia.php', 'App\Http\Controllers\HomeController@listaEcografia');
$router->add('/lista_histeroscopias.php', 'App\Http\Controllers\HomeController@listaHisteroscopias');
$router->add('/lista_consulta.php', 'App\Http\Controllers\HomeController@listaConsulta');
$router->add('/lista_adminlab.php', 'App\Http\Controllers\HomeController@listaAdminlab'); 
$router->add('/lista_admin.php', 'App\Http\Controllers\HomeController@listaAdmin');
$router->add('/lista_sistemas.php', 'App\Http\Controllers\HomeController@listaSistemas');
$router->add('/auditoria_facturacion.php', 'App\Http\Controllers\HomeController@auditoriaFacturacion');
$router->add('/lista_transferencias.php', 'App\Http\Controllers\HomeController@listaTransferencias');
$router->add('/lista_marketing.php', 'App\Http\Controllers\HomeController@listaMarketing'); 