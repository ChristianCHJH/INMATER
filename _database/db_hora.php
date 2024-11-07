<?php
date_default_timezone_set('America/Lima');
require("database.php");

function horaTodo()
{
    global $db;
    $stmt = $db->prepare("select id, codigo, nombre from man_hora where estado = 1");
    $stmt->execute();
    return $stmt->fetchAll();
}
?>