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
if(!empty($data->tip) && !empty($data->dni)) {
    $paciente->dni = $data->dni;
    $paciente->pass = $data->pass;
    $paciente->sta = $data->sta;
    $paciente->med = $data->med;
    $paciente->tip = $data->tip;
    $paciente->nom = $data->nom;
    $paciente->ape = $data->ape;
    $paciente->fnac = $data->fnac;
    $paciente->tcel = $data->tcel;
    $paciente->tcas = $data->tcas;
    $paciente->tofi = $data->tofi;
    $paciente->mai = $data->mai;
    $paciente->dir = $data->dir;
    $paciente->nac = $data->nac;
    $paciente->depa = $data->depa;
    $paciente->prov = $data->prov;
    $paciente->dist = $data->dist;
    $paciente->prof = $data->prof;
    $paciente->san = $data->san;
    $paciente->don = $data->don;
    $paciente->raz = $data->raz;
    $paciente->talla = $data->talla;
    $paciente->peso = $data->peso;
    $paciente->rem = $data->rem;
    $paciente->nota = $data->nota;
    $paciente->fec = $data->fec;
    $paciente->idsedes = $data->idsedes;
    $paciente->estado = $data->estado;
    $paciente->idusercreate = $data->idusercreate;
 
    // create the paciente
    if ($paciente->create()) {
        http_response_code(201); // set response code - 201 created
        echo json_encode(array("message" => "paciente was created.")); // tell the user
    } else { // if unable to create the paciente, tell the user
        http_response_code(503); // set response code - 503 service unavailable
        echo json_encode(array("message" => "Unable to create paciente.")); // tell the user
    }
} else { // tell the user data is incomplete
    http_response_code(400); // set response code - 400 bad request
    echo json_encode(array("message" => "Unable to create paciente. Data is incomplete.")); // tell the user
}
?>