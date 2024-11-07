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
    case 'eliminar':
      eliminar($_POST, $login);
      http_response_code(200);
      echo json_encode(array("message" => "se eliminó la información correctamente"));
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
    $stmt = $db->prepare("INSERT INTO conta_centro_costo (sede_id, codigo, descripcion, idusercreate) VALUES (?, ?, ?, ?)");
    $stmt->execute(array($data["sede"], $data["codigo"], mb_strtoupper($data["nombre"]), $login));
  }

  function eliminar($data, $login)
  {
    global $db;
    $stmt = $db->prepare("UPDATE conta_centro_costo set estado = 0, iduserupdate = ? where id = ?");
    $stmt->execute(array($login, $data["id"]));
  }

  function actualizar($data, $login)
  {
    global $db;
    $stmt = $db->prepare("UPDATE conta_centro_costo set sede_id = ?, codigo = ?, descripcion = ?, iduserupdate = ? where id = ?");
    $stmt->execute(array($data["sede"], $data["codigo"], $data["nombre"], $login, $data["id"]));
  }
?>