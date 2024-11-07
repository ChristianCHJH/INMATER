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
            $consulta = $db->prepare("update man_gineco_tipo_intervencion set estado = 0, iduserupdate=? where id = ?");
            $consulta->execute(array($login, $id));
            break;
        case 2:
            $codigo = $_POST["codigo"];
            $descripcion = $_POST["descripcion"];
            $consulta = $db->prepare("insert into man_gineco_tipo_intervencion (codigo, nombre, idusercreate) values (?, ?, ?)");
            $consulta->execute(array($codigo, $descripcion, $login));
            break;
        default: break;
    }
?>