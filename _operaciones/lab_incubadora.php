<?php
    ini_set("display_errors","1");
    error_reporting(E_ALL);
    $id=0;
    $dia=0;
    $valor=0;

    if (isset($_POST["dia"]) && isset($_POST["id"]) && isset($_POST["valor"])) {
        $dia=$_POST["dia"];
        $id=$_POST["id"];
        $valor=$_POST["valor"];
    } else {
        exit();
    }

    require("../_database/database.php");

    switch ($dia) {
        case 0:
            $consulta = $db->prepare("update lab_incubadora set dia0 = ? where id = ?");
        break;
        case 1:
            $consulta = $db->prepare("update lab_incubadora set dia1 = ? where id = ?");
        break;
        default:
        break;
    }

    $consulta->execute(array($valor, $id));
?>