<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
include_once('../config/database.php');
include_once('../objects/analisis.php');

$data = json_decode(file_get_contents("php://input")); // get posted data

$database = new Database();
$database->setConnection();
$analisis = new Analisis($database->getConnection());

// enviar servidor local
if (!empty($data->a_dni)) {
    $analisis->a_dni = $data->a_dni;
    $analisis->a_mue = $data->a_mue;
    $analisis->a_nom = $data->a_nom;
    $analisis->a_med = $data->a_med;
    $analisis->a_exa = $data->a_exa;
    $analisis->a_sta = $data->a_sta;
    $analisis->a_obs = $data->a_obs;
    $analisis->cor = $data->cor;
    $analisis->lab = $data->lab;
    $analisis->idusercreate = $data->idusercreate;
    $analisis->iduserupdate = $data->iduserupdate;

    if ($analisis->create()) {
        http_response_code(201); // set response code - 201 created
        echo json_encode(array("message" => "object was created."));
    } else {
        http_response_code(503); // set response code - 503 service unavailable
        echo json_encode(array("message" => "Unable to create object."));
    }
} else {
    http_response_code(400); // set response code - 400 bad request
    echo json_encode(array("message" => "Unable to create object. Data is incomplete."));
}
?>