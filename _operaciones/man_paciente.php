<?php
session_start();
require($_SERVER["DOCUMENT_ROOT"] . "/_database/database.php");
require($_SERVER["DOCUMENT_ROOT"] . "/config/environment.php");
$login = "";

if (!!$_SESSION) {
  $login = $_SESSION['login'];
} else {
  http_response_code(400);
  echo json_encode(array("message" => "no se ha iniciado sesión"));
  exit();
}

if (!!$_POST["tipo"]) {
  switch ($_POST["tipo"]) {
    case 'actualizar_sede':
      http_response_code(201);
      echo json_encode(["message" => actualizar_sede($login, $_POST["data"])]);
      break;
    default: exit(); break;
  }
}

function actualizar_sede($login, $data) {
  global $db;
	$dni = $data["numero_documento"];
  $stmt = $db->prepare("UPDATE hc_paciente SET dni = ?, idsedes = ?, ape = ?, nom = ?, medios_comunicacion_id = ?, don = ?, iduserupdate = ?,updatex=? WHERE dni = ?;");
  $hora_actual = date("Y-m-d H:i:s");
  $stmt->execute([$data["numero_documento"], $data["sede_id"], $data["apellidos"], $data["nombres"], $data["tipo_paciente_id"], $data["condicion_paciente_id"], $login,$hora_actual, $data["numero_documento"]]);
  $log_Paciente = $db->prepare(
    "INSERT INTO appinmater_log.hc_paciente (
                dni, pass, sta, med, tip, nom, ape, fnac, tcel,
                tcas, tofi, mai, dir, nac, depa, prov, dist, prof,
                san, don, raz, talla, peso, rem, nota, fec, idsedes,
                idusercreate, createdate, 
                action
        )
    SELECT 
        dni, pass, sta, med, tip, nom, ape, fnac, tcel, 
        tcas, tofi, mai, dir, nac, depa, prov, dist, prof,
        san, don, raz, talla, peso, rem, nota, fec, idsedes,
        iduserupdate,updatex, 'U'
    FROM appinmater_modulo.hc_paciente
    WHERE dni=?");
  $log_Paciente->execute(array($data["numero_documento"]));

  if ($stmt->rowCount() > 0) {
    return $dni;
  } else {
    return '0';
  }
} ?>