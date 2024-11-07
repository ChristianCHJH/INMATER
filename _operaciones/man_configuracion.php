<?php
    ini_set("display_errors","1");
    error_reporting(E_ALL);
    session_start();
    $login = $_SESSION['login'];
    $tipo_operacion=0;

    if (isset($_POST["tipo_operacion"]) && !empty($_POST["tipo_operacion"])) {
        $tipo_operacion = $_POST["tipo_operacion"];
    } else {
        exit();
    }

    require("../_database/database.php");
    switch ($tipo_operacion) {
        case 3:
            $id = $_POST["id"];
            $descripcion = $_POST["descripcion"];
            $valor = $_POST["valor"];
            $consulta = $db->prepare("update man_configuracion set descripcion = ?, valor = ?, iduserupdate = ? where id = ?");
            $consulta->execute(array($descripcion, $valor, $login, $id));
            break;
        default: exit(); break;
    }
?>