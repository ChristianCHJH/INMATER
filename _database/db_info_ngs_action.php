<?php
    date_default_timezone_set('America/Lima');
    require("database.php");

    function testigoBiopsiaDesarrolloInsertar($idrepro, $dia, $idtestigo, $login)
    {
        // eliminar anteriores
        global $db;
        $stmt = $db->prepare("update lab_aspira_testigo_biopsia set estado=0, iduserupdate=? where estado = 1 and idrepro=? and dia=?");
        $stmt->execute(array($login, $idrepro, $dia));

        // ingresar nuevo registro
        $stmt = $db->prepare("
        insert into lab_aspira_testigo_biopsia
        (idrepro, dia, idtestigobiopsia, idusercreate, createdate) VALUES
        (?, ?, ?, ?, ?)");
        $stmt->execute(array($idrepro, $dia, $idtestigo, $login, date("Y-m-d H:i:s")));
    }

    function pruebaBiopsiaDesarrolloInsertar($idrepro, $idprueba, $correlativo, $observacion, $login)
    {
        // eliminar anteriores
        global $db;
        $stmt = $db->prepare("update lab_aspira_prueba_biopsia set estado=0, iduserupdate=? where estado = 1 and idrepro=?");
        $stmt->execute(array($login, $idrepro));

        // ingresar nuevo registro
        $stmt = $db->prepare("
        insert into lab_aspira_prueba_biopsia
        (idrepro, idpruebabiopsia, correlativo, observacion, idusercreate, createdate) VALUES
        (?, ?, ?, ?, ?, ?)");
        $stmt->execute(array($idrepro, $idprueba, $correlativo, $observacion, $login, date("Y-m-d H:i:s")));
    }

    function observacionBiopsiaDesarrolloInsertar($idrepro, $ovo, $nombre, $login)
    {
        // eliminar anteriores
        global $db;
        $stmt = $db->prepare("update lab_aspira_dias_observacion_biopsia set estado=0, iduserupdate=? where estado = 1 and idrepro=? and ovo=?");
        $stmt->execute(array($login, $idrepro, $ovo));

        // ingresar nuevo registro
        $stmt = $db->prepare("
        insert into lab_aspira_dias_observacion_biopsia
        (idrepro, ovo, nombre, idusercreate, createdate) VALUES
        (?, ?, ?, ?, ?)");
        $stmt->execute(array($idrepro, $ovo, $nombre, $login, date("Y-m-d H:i:s")));
    }
?>