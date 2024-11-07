<?php
session_start();

// require $_SERVER["DOCUMENT_ROOT"] . "/_database/database.php";
// require $_SERVER["DOCUMENT_ROOT"] . "/config/environment.php";
$login = "";
$tipo_operacion ="";

if (!!$_SESSION) {
    $login = $_SESSION['login'];
} else {
    http_response_code(400);
    echo json_encode(["message" => "no se ha iniciado sesión."]);
    exit();
}

if (isset($_POST["tipo_operacion"]) && !empty($_POST["tipo_operacion"])) {
    $tipo_operacion = $_POST["tipo_operacion"];
} else {
    exit();
}

require $_SERVER["DOCUMENT_ROOT"] . "/_database/seguimiento-consulta.php";

switch ($tipo_operacion) {
case 1:
    actualizarConfirmacion($login, $_POST["id"]);
    break;
case 2:
    actualizarAnulacion($login, $_POST["id"]);
    break;
case 3:
    ;
    http_response_code(201);
    echo json_encode(["message" => subirVoucher($login, $_POST['id'], $_FILES['file'])]);
    break;

default:
    http_response_code(400);
    echo json_encode(["message" => "no se ha indicado ninguna operacion."]);
    // exit();
    break;
}
?>