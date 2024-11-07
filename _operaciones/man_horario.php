<?php
    session_start();
    $login = $_SESSION['login'];
    /* ini_set("display_errors","1");
    error_reporting(E_ALL); */
    $id=0;
    $procedimiento=0;
    $valor=0;

    if (isset($_POST["id"]) && isset($_POST["procedimiento"]) && isset($_POST["valor"])) {
        $id=$_POST["id"];
        $procedimiento=$_POST["procedimiento"];
        $valor=$_POST["valor"];
    } else {
        exit();
    }

    require("../_database/database.php");

    switch ($procedimiento) {
        case 0:
            $consulta = $db->prepare("update man_hora set urologia = ?, iduserupdate=? where id = ?");
        break;
        case 1:
            $consulta = $db->prepare("update man_hora set ginecologia = ?, iduserupdate=? where id = ?");
        break;
        case 2:
            $consulta = $db->prepare("update man_hora set aspiracion = ?, iduserupdate=? where id = ?");
        break;
        case 3:
            $consulta = $db->prepare("update man_hora set transferencia = ?, iduserupdate=? where id = ?");
        break;
        default: break;
    }

    if ($procedimiento == 2) {
        $consulta_aspiracion = $db->prepare("update appinmater_modulo.man_hora set aspiracion_inyeccion = ? 
        where nombre = (
            SELECT TO_CHAR(nombre::time - INTERVAL '36 hours', 'HH24:MI') as resta 
            FROM appinmater_modulo.man_hora
            WHERE id = ?)");
            
            $consulta_aspiracion->execute(array($valor,$id));
        }
        
    $consulta->execute(array($valor, $login, $id));
?>