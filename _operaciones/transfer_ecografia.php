<?php
session_start();
require($_SERVER["DOCUMENT_ROOT"] . "/_database/database.php");
require($_SERVER["DOCUMENT_ROOT"] . "/config/environment.php");
$login = "";

if (!!$_SESSION) {
  $login = $_SESSION['login'];
} else {
  http_response_code(400);
  echo json_encode(["message" => "no se ha iniciado sesión."]);
  exit();
}

if (!!$_POST["tipo"]) {
  http_response_code(201);
  guardar($_POST["data"], $login);
} else {
  http_response_code(400);
  echo json_encode(["message" => "no se ha indicado el tipo."]);
  exit();
}

function guardar($data, $login)
{
  global $db;

  if ($data["id"] == 0) {
    // agregar
    // validar codigo no debe existir
    $stmt = $db->prepare("SELECT id FROM transfer_ecografia WHERE codigo=?;");
    $stmt->execute([$data['codigo']]);

    if ($stmt->rowCount() == 0) {
      $stmt = $db->prepare("INSERT INTO transfer_ecografia (codigo, nombre, idusercreate) VALUES (?, ?, ?);");
      $stmt->execute([$data["codigo"], $data["nombre"], $login]);
      echo json_encode(["message" => ""]);
    } else {
      echo json_encode(["message" => "<strong>Mensaje!</strong> El código ingresado ya existe."]);
    }
  } else {
    // actualizar
    $stmt = $db->prepare("UPDATE transfer_ecografia SET codigo = ?, nombre = ?, iduserupdate = ? WHERE id = ?;");
    $stmt->execute([$data["codigo"], $data["nombre"], $login, $data["id"]]);
    echo json_encode(["message" => ""]);
  }
}