<?php
  ini_set("display_errors","1");
  error_reporting(E_ALL);
  session_start();

  $login = "";

  if (!!$_SESSION) {
    $login = $_SESSION['login'];
  } else {
    http_response_code(400);
    echo json_encode(array("message" => "no se ha iniciado sesión"));
    exit();
  }

  require($_SERVER["DOCUMENT_ROOT"] . "/_database/database.php");
  require($_SERVER["DOCUMENT_ROOT"] . "/config/environment.php");
  $tipo_operacion = 0;

  if (isset($_POST["tipo_operacion"]) && !empty($_POST["tipo_operacion"])) {
    $tipo_operacion = $_POST["tipo_operacion"];
  } else {
    exit();
  }

  switch ($tipo_operacion) {
    case 'agregar':
      agregar($_POST, $login);
      http_response_code(200);
      echo json_encode(array("message" => "se ingresó la información correctamente"));
      break;
    case 'cambiarestado':
        cambiarestado($_POST, $login);
      http_response_code(200);
      echo json_encode(array("message" => "se actualizó la información correctamente"));
      break;
    case 'actualizar':
      actualizar($_POST, $login);
      http_response_code(200);
      echo json_encode(array("message" => "se actualizó la información correctamente"));
      break;
    
    default:
      break;
  }

  function agregar($data, $login)
  {
    global $db;
    $stmt = $db->prepare("INSERT INTO man_empresas (service_mifact,
service_mifact,
cod_tip_nif_emis,
num_nif_emis,
nom_comer_emis,
cod_ubi_emis,
txt_dmcl_fisc_emis,
enviar_a_sunat,
estado,idusercreate,nom_rzn_soc_emis) VALUES (?, ?, ?, ?, ?,?, ?, ?, ?, ?,?)");
    $stmt->execute(array($data["service_mifact"], $data["token"], $data["cod_tip_nif_emis"],$data["num_nif_emis"],$data["nom_comer_emis"], $data["cod_ubi_emis"], $data["txt_dmcl_fisc_emis"], $data["enviar_a_sunat"], $data["estado"], $login, $data["nom_rzn_soc_emis"]));
  }

  function cambiarestado($data, $login)
  {
      global $db;
      if($data["estado"]==1){
          $consulta = $db->prepare("update man_empresas set estado = 0, idusercreate=? where id = ?");
          $consulta->execute(array($login, $data["id"]));
      }else{
          $consulta = $db->prepare("update man_empresas set estado = 1, idusercreate=? where id = ?");
          $consulta->execute(array($login, $data["id"]));
      }

  }

  function actualizar($data, $login)
  {

    global $db;
    $stmt = $db->prepare("UPDATE man_empresas set service_mifact = ?, token = ?, 
                 cod_tip_nif_emis = ?, num_nif_emis = ? , nom_comer_emis = ?, 
                 cod_ubi_emis = ?, txt_dmcl_fisc_emis = ? , enviar_a_sunat = ?
, estado = ?, idusercreate = ?,nom_rzn_soc_emis=? where id = ?");
    $stmt->execute(array($data["service_mifact"], $data["token"], $data["cod_tip_nif_emis"],$data["num_nif_emis"],$data["nom_comer_emis"], $data["cod_ubi_emis"], $data["txt_dmcl_fisc_emis"], $data["enviar_a_sunat"], $data["estado"], $login, $data["nom_rzn_soc_emis"], $data["id"], $data["id"]));
  }
?>