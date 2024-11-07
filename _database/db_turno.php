<?php
date_default_timezone_set('America/Lima');
require("database.php");

function turnoTodo()
{
    global $db;
    $stmt = $db->prepare("SELECT id, nombre from man_turno_reproduccion where estado = 1 order by nombre asc");
    $stmt->execute();
    return $stmt->fetchAll();
}
?>