<?php
date_default_timezone_set('America/Lima');
require("database.php");
require($_SERVER["DOCUMENT_ROOT"] . "/_database/database_log.php");
function add_log_inmater($user, $createdate)
{
    global $dblog;
    $nombre_modulo="login";
    $ruta="login.php";
    $tipo_operacion="ingreso";
    $sql = "INSERT INTO log_inmater
          (nombre_modulo, ruta, tipo_operacion, idusercreate,createdate)
          VALUES
          (?, ?, ?, ?,?)";
    $statement = $dblog->prepare($sql);
    $statement->execute(array($nombre_modulo,$ruta,$tipo_operacion,$user,$createdate));

}

function add_usuario_log($user, $idusercreate, $createdate)
{
  global $db;
  $stmt = $db->prepare("INSERT into usuario_log (userx, idusercreate, createdate) values (?, ?, ?)");
  $stmt->execute(array($user, $idusercreate, $createdate));
}

function authentification($user, $pass)
{
  if (isset($user) and isset($pass) ) {
    global $db;
    $stmt = $db->prepare("SELECT * FROM usuario WHERE userx=? AND pass=? AND estado=1");
    $stmt->execute(array($user, $pass));
    if ($stmt->rowCount() > 0) {
      return true;
    } else {
      return false;
    }
  } else {
    return false;
  }
}

function getUsuario($user)
{
  global $db;
  $stmt = $db->prepare("SELECT * FROM usuario WHERE userx=?");
  $stmt->execute(array($user));
  $data = $stmt->fetch(PDO::FETCH_ASSOC);
  return $data;
}
?>