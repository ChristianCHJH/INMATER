<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
include_once('../config/database.php');
include_once('../objects/riesgo_quirurgico.php');

$data = json_decode(file_get_contents("php://input")); // get posted data

$database = new Database();
$database->setConnection();
$riesgo_quirurgico = new RiesgoQuirurgico($database->getConnection());

// enviar servidor local
if(!empty($data->tipodocumento) && !empty($data->numerodocumento)) {
    $riesgo_quirurgico->tipodocumento = $data->tipodocumento;
    $riesgo_quirurgico->numerodocumento = $data->numerodocumento;
    $riesgo_quirurgico->nivel = $data->nivel;
    $riesgo_quirurgico->fvigencia = $data->fvigencia;
    $riesgo_quirurgico->nombre = $data->nombre;
    $riesgo_quirurgico->obs = $data->obs;
    $riesgo_quirurgico->estado = $data->estado;
    $riesgo_quirurgico->idusercreate = $data->idusercreate;
 
    if ($riesgo_quirurgico->create()) {
        http_response_code(201); // set response code - 201 created
        echo json_encode(array("message" => "object was created.")); // tell the user
    } else { // if unable to create the object, tell the user
        http_response_code(503); // set response code - 503 service unavailable
        echo json_encode(array("message" => "Unable to create object.")); // tell the user
    }
} else { // tell the user data is incomplete
    http_response_code(400); // set response code - 400 bad request
    echo json_encode(array("message" => "Unable to create object. Data is incomplete.")); // tell the user
}
?>