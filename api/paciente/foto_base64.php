<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
include_once('../config/database.php'); // get database connection
include_once('../objects/paciente.php'); // instantiate paciente object

$data = json_decode(file_get_contents("php://input")); // get posted data

$database = new Database();
$database->setConnection();
$paciente = new Paciente($database->getConnection());

// enviar servidor local
if(isset($data->dni) && !empty($data->dni)) {
  $paciente->dni = $data->dni;

  $paciente->foto_base64();
    http_response_code(201);
    echo json_encode(array("message" => $paciente->foto_base64), JSON_UNESCAPED_SLASHES);
} else {
  http_response_code(400); // 400 bad request
  echo json_encode(array("message" => "Unable to access paciente. Data is incomplete."), JSON_UNESCAPED_SLASHES);
} ?>