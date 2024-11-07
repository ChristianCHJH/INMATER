<?php
date_default_timezone_set('America/Lima');
require("database.php");

function agendaBloqueoEliminar($id, $login)
{
    global $db;
    $stmt = $db->prepare("update lab_agenda_bloqueo set estado = 0, iduserupdate = ? where id = ?");
    $stmt->execute(array($login, $id));
}

function agendaBloqueoInsertar($idhora, $idturno, $fecha, $avatar, $observacion, $login)
{
    global $db;
    $stmt = $db->prepare("insert into lab_agenda_bloqueo (idhora, idturno, fecha, avatar, observacion, idusercreate, createdate) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute(array($idhora, $idturno, $fecha, $avatar, $observacion, $login, date("Y-m-d H:i:s")));
}

function agendaBloqueoTodo()
{
    global $db;
    $stmt = $db->prepare("
    select
    b.id, b.fecha, h.nombre hora, t.nombre turno, b.avatar, b.observacion
    from lab_agenda_bloqueo b
    inner join man_turno_reproduccion t on t.id = b.idturno and t.estado = 1
    inner join man_hora h on h.id = b.idhora and h.estado = 1
    where b.estado = 1
    order by id desc");
    $stmt->execute();
    return $stmt->fetchAll();
}
?>