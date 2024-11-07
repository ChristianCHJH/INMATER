<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
include_once('../config/database.php');
include_once('../objects/lab_aspira_t.php');

$data = json_decode(file_get_contents("php://input")); // get posted data

$database = new Database();
$database->setConnection();
$lab_aspira_t = new LabAspiraT($database->getConnection());

// enviar servidor local
if(!empty($data->pro)) {
    $lab_aspira_t->pro = $data->pro;
    $lab_aspira_t->dia = $data->dia;
    $lab_aspira_t->beta = $data->beta;
    $lab_aspira_t->beta_rinicial = $data->beta_rinicial;
    $lab_aspira_t->beta_evolucion = $data->beta_evolucion;
    $lab_aspira_t->beta_sembarazo = $data->beta_sembarazo;
    $lab_aspira_t->nsacos = $data->nsacos;
    $lab_aspira_t->sembarazo_semanas = $data->sembarazo_semanas;
    $lab_aspira_t->sembarazo_nnacidos = $data->sembarazo_nnacidos;
    $lab_aspira_t->sembarazo_peso = $data->sembarazo_peso;
    $lab_aspira_t->t_cat = $data->t_cat;
    $lab_aspira_t->s_gui = $data->s_gui;
    $lab_aspira_t->s_cat = $data->s_cat;
    $lab_aspira_t->endo = $data->endo;
    $lab_aspira_t->inte = $data->inte;
    $lab_aspira_t->eco = $data->eco;
    $lab_aspira_t->med = $data->med;
    $lab_aspira_t->emb = $data->emb;
    $lab_aspira_t->obs = $data->obs;

    if ($lab_aspira_t->create()) {
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