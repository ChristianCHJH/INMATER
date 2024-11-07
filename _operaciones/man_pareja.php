<?php
    ini_set("display_errors","1");
    error_reporting(E_ALL);
    session_start();
    $login = $_SESSION['login'];
    $dni = 0;
    $tipo_operacion=0;

    if (isset($_POST["tipo_operacion"]) && !empty($_POST["tipo_operacion"])) {
        $tipo_operacion = $_POST["tipo_operacion"];
    } else {
        exit();
    }

    require("../_database/database.php");
    switch ($tipo_operacion) {
        // actualizar la pareja actual
        case 1:
            $dni = $_POST["dni"];
            $p_dni = $_POST["p_dni"];
            // actualizar todas las parejas
            $consulta = $db->prepare("update hc_pare_paci set actual = 0, iduserupdate=? where dni = ?");
            $consulta->execute(array($login, $dni));
            // actualizar solo la pareja actual
            $consulta = $db->prepare("update hc_pare_paci set actual = 1, iduserupdate=? where dni = ? and p_dni = ?");
            $consulta->execute(array($login, $dni, $p_dni));
            break;
        default: break;
    }
?>