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

    require("../_database/db_agenda_bloqueo.php");

    switch ($tipo_operacion) {
        case 1: agendaBloqueoEliminar($_POST["id"], $login); break;
        case 2: agendaBloqueoInsertar($_POST["hora"], $_POST["turno"], $_POST["fecha"], "", "", $login); break;
        default: break;
    }
?>