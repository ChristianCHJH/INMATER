<?php
    ini_set("display_errors","1");
    error_reporting(E_ALL);
    session_start();
    $login = $_SESSION['login'];
    $id = 0;
    $tipo_operacion=0;

    if (isset($_POST["tipo_operacion"]) && !empty($_POST["tipo_operacion"])) {
        $tipo_operacion = $_POST["tipo_operacion"];
    } else {
        exit();
    }

    require("../_database/database.php");
    switch ($tipo_operacion) {
        case 1:
            $id = $_POST["id"];
            $consulta = $db->prepare("update man_poseidon set estado = 0, iduserupdate=? where id = ?");
            $consulta->execute(array($login, $id));
            break;
        case 2:
            $nombre = $_POST["nombre"];
            $consulta = $db->prepare("insert into man_poseidon (nombre, idusercreate) values (?, ?)");
            $consulta->execute(array($nombre, $login));
            break;
        case 3:
            $nombre = $_POST["nombre"];
            $id = $_POST["id"];
            $consulta = $db->prepare("update man_poseidon set nombre = ?, iduserupdate =? where id = ?");
            $consulta->execute(array($nombre, $login, $id));
            break;
        default: break;
    }
?>