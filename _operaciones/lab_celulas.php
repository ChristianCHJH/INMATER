<?php
    ini_set("display_errors","1");
    error_reporting(E_ALL);
    session_start();
    $login = $_SESSION['login'];
    $id=0;
    $dia=0;
    $valor=0;
    $tipo_operacion=0;

    if (isset($_POST["tipo_operacion"]) && !empty($_POST["tipo_operacion"])) {
        $tipo_operacion = $_POST["tipo_operacion"];
    } else {
        exit();
    }

    require("../_database/database.php");

    switch ($tipo_operacion) {
        case 1: // actualizar dias
            if (isset($_POST["id"]) && isset($_POST["dia"]) && isset($_POST["valor"])) {
                $id=$_POST["id"];
                $dia=$_POST["dia"];
                $valor=$_POST["valor"];
            } else {
                exit();
            }

            switch ($dia) {
                case 2: $consulta = $db->prepare("update lab_celulas set dia2 = ?, iduserupdate = ? where id = ?"); break;
                case 3: $consulta = $db->prepare("update lab_celulas set dia3 = ?, iduserupdate = ? where id = ?"); break;
                case 4: $consulta = $db->prepare("update lab_celulas set dia4 = ?, iduserupdate = ? where id = ?"); break;
                case 5: $consulta = $db->prepare("update lab_celulas set dia5 = ?, iduserupdate = ? where id = ?"); break;
                case 6: $consulta = $db->prepare("update lab_celulas set dia6 = ?, iduserupdate = ? where id = ?"); break;
                default: break;
            }

            $consulta->execute(array($valor, $login, $id));
        break;
        case 2: // ingresar
            $nombre = $_POST["nombre"];
            $codigo = $_POST["codigo"];
            $consulta = $db->prepare("insert into lab_celulas (codigo, nombre, idusercreate) values (?, ?, ?)");
            $consulta->execute(array($codigo, $nombre, $login));
        break;
        case 3: // valor predeterminado
            $id = $_POST["id"];
            $dia = $_POST["dia"];

            switch ($dia) {
                case 2:
                    $consulta = $db->prepare("update lab_celulas set dia2predeterminado = 0, iduserupdate = ?");
                    $consulta->execute(array($login));
                    $consulta = $db->prepare("update lab_celulas set dia2predeterminado = 1, iduserupdate = ? where id = ?");
                    break;
                case 3:
                    $consulta = $db->prepare("update lab_celulas set dia3predeterminado = 0, iduserupdate = ?");
                    $consulta->execute(array($login));
                    $consulta = $db->prepare("update lab_celulas set dia3predeterminado = 1, iduserupdate = ? where id = ?");
                    break;
                case 4:
                    $consulta = $db->prepare("update lab_celulas set dia4predeterminado = 0, iduserupdate = ?");
                    $consulta->execute(array($login));
                    $consulta = $db->prepare("update lab_celulas set dia4predeterminado = 1, iduserupdate = ? where id = ?");
                    break;
                case 5:
                    $consulta = $db->prepare("update lab_celulas set dia5predeterminado = 0, iduserupdate = ?");
                    $consulta->execute(array($login));
                    $consulta = $db->prepare("update lab_celulas set dia5predeterminado = 1, iduserupdate = ? where id = ?");
                    break;
                case 6:
                    $consulta = $db->prepare("update lab_celulas set dia6predeterminado = 0, iduserupdate = ?");
                    $consulta->execute(array($login));
                    $consulta = $db->prepare("update lab_celulas set dia6predeterminado = 1, iduserupdate = ? where id = ?");
                break;
                default: break;
            }

            $consulta->execute(array($login, $id));
        break;
        default: break;
    }
?>