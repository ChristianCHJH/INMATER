<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
include_once('../config/database.php'); // get database connection
include_once('../objects/recibo.php'); // instantiate paciente object

$data = json_decode(file_get_contents("php://input")); // get posted data

$database = new Database();
$database->setConnection();
$recibo = new Recibo($database->getConnection());

// enviar servidor local
if(!empty($data->id) && !empty($data->tip)) {
    $recibo->id = $data->id;
    $recibo->tip = $data->tip;
    $recibo->fec = $data->fec;
    $recibo->dni = $data->dni;
    $recibo->nom = $data->nom;
    $recibo->med = $data->med;
    $recibo->sede = $data->sede;
    $recibo->correo_electronico = $data->correo_electronico;
    $recibo->id_tipo_documento_facturacion = $data->id_tipo_documento_facturacion;
    $recibo->ruc = $data->ruc;
    $recibo->raz = $data->raz;
    $recibo->direccionfiscal = $data->direccionfiscal;
    $recibo->t_ser = $data->t_ser;
    $recibo->pak = $data->pak;
    $recibo->ser = $data->ser;
    $recibo->mon = $data->mon;
    $recibo->tot = $data->tot;
    $recibo->t1 = $data->t1;
    $recibo->m1 = $data->m1;
    $recibo->p1 = $data->p1;
    $recibo->t2 = $data->t2;
    $recibo->m2 = $data->m2;
    $recibo->p2 = $data->p2;
    $recibo->t3 = $data->t3;
    $recibo->m3 = $data->m3;
    $recibo->p3 = $data->p3;
    $recibo->anu = $data->anu;
    $recibo->veri = $data->veri;
    $recibo->man_ini = $data->man_ini;
    $recibo->man_fin = $data->man_fin;
    $recibo->anglo = $data->anglo;
    $recibo->user = $data->user;
    $recibo->comentarios = $data->comentarios;
    $recibo->estado = $data->estado;
    $recibo->idusercreate = $data->idusercreate;
 
    // create the recibo
    if ($recibo->create()) {
        http_response_code(201); // set response code - 201 created
        echo json_encode(array("message" => "recibo was created.")); // tell the user
    } else { // if unable to create the recibo, tell the user
        http_response_code(503); // set response code - 503 service unavailable
        echo json_encode(array("message" => "Unable to create recibo.")); // tell the user
    }
} else { // tell the user data is incomplete
    http_response_code(400); // set response code - 400 bad request
    echo json_encode(array("message" => "Unable to create recibo. Data is incomplete.")); // tell the user
}
?>