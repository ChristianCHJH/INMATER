<?php
session_start();
// test run
require($_SERVER["DOCUMENT_ROOT"] . "/_database/database.php");
require($_SERVER["DOCUMENT_ROOT"] . "/_database/database_farmacia.php");
require($_SERVER["DOCUMENT_ROOT"] . "/config/environment.php");
$login = "";

if (!!$_SESSION) {
    $login = $_SESSION['login'];
} else {
    http_response_code(400);
    echo json_encode(["message" => "no se ha iniciado sesión"]);
    exit();
}

if (isset($_POST["tipo"]) && !empty($_POST["tipo"])) {
	switch ($_POST["tipo"]) {
		case 'cargar_data':
				http_response_code(200);
				echo json_encode(["message" => cargar_data($_POST["data"])]);
				break;
		
		default:
				http_response_code(400);
				echo json_encode(["message" => "la operacion no existe"]);
				break;
	}
} else {
    http_response_code(400);
    echo json_encode(["message" => "no se enviaron los parametros correctamente"]);
    exit();
}


