<?php

require($_SERVER["DOCUMENT_ROOT"] . "/_database/database.php");
require($_SERVER["DOCUMENT_ROOT"] . "/_database/database_log.php");
require($_SERVER["DOCUMENT_ROOT"] . "/config/environment.php");

function autenticar($user, $password) {
    if (!empty($user) && !empty($password)) {
        global $db;
        $stmt = $db->prepare("SELECT * FROM hc_paciente_accesos where dni=? and acceso=? and estado=1;");
        $stmt->execute([$user, $password]);

        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function ingresar_log($dni) {
    global $dblog;
    $stmt = $dblog->prepare("INSERT INTO hc_paciente_login (dni, createdate) values (?, ?);");
    $stmt->execute([$dni, date("Y-m-d H:i:s")]);
}