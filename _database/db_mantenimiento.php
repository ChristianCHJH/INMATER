<?php
date_default_timezone_set('America/Lima');
require("database.php");

// mantenimiento de celulas
function incubadoraEliminar($id, $login)
{
    global $db;
    $stmt = $db->prepare("update lab_incubadora set estado = 0, iduserupdate = ? where id = ?");
    $stmt->execute(array($login, $id));
}
function incubadoraInsertar($codigo, $nombre, $login)
{
    global $db;
    $stmt = $db->prepare("insert into lab_incubadora (codigo, nombre, idusercreate, createdate) VALUES (?, ?, ?, ?)");
    $stmt->execute(array($codigo, $nombre, $login, date("Y-m-d H:i:s")));
    print("<div id='alerta'> Registro guardado!</div>");
}

// mantenimiento de contraccion
function contraccionEliminar($id, $login)
{
    global $db;
    $stmt = $db->prepare("update lab_contraccion set estado = 0, iduserupdate = ? where id = ?");
    $stmt->execute(array($login, $id));
}
function contraccionInsertar($codigo, $nombre, $nombre_corto, $login)
{
    global $db;
    $stmt = $db->prepare("insert into lab_contraccion (codigo, nombre, nombre_corto, idusercreate, createdate) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute(array($codigo, $nombre, $nombre_corto, $login, date("Y-m-d H:i:s")));
    print("<div id='alerta'> Registro guardado! </div>");
}

// mantenimiento de celulas
function celulasEliminar($id, $login)
{
    global $db;
    $stmt = $db->prepare("update lab_celulas set estado = 0, iduserupdate = ? where id = ?");
    $stmt->execute(array($login, $id));
}
function celulasInsertar($codigo, $nombre, $login)
{
    global $db;
    $stmt = $db->prepare("insert into lab_celulas (codigo, nombre, idusercreate, createdate) VALUES (?, ?, ?, ?)");
    $stmt->execute(array($codigo, $nombre, $login, date("Y-m-d H:i:s")));
    print("<div id='alerta'> Registro guardado! </div>");
}

// mantenimiento de testigos de biopsia
function testigoBiopsiaEliminar($id, $login)
{
    global $db;
    $stmt = $db->prepare("update labo_testigo_biopsia set estado = 0, iduserupdate = ? where id = ?");
    $stmt->execute(array($login, $id));
}
function testigoBiopsiaInsertar($nombre, $login)
{
    global $db;
    $stmt = $db->prepare("insert into labo_testigo_biopsia (nombre, idusercreate, createdate) VALUES (?, ?, ?)");
    $stmt->execute(array($nombre, $login, date("Y-m-d H:i:s")));
    print("<div id='alerta'> Registro guardado! </div>");
}
function testigoBiopsiaListar()
{
    global $db;
    $data = null;

    $stmt = $db->prepare("select id, nombre from labo_testigo_biopsia where estado = 1");
    $stmt->execute();
    if ($stmt->rowCount() > 0)
    {
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $data = $stmt->fetchAll();
    }
    return $data;
}

// mantenimiento de prueba de biopsia
function pruebaBiopsiaEliminar($id, $login)
{
    global $db;
    $stmt = $db->prepare("update labo_prueba_biopsia set estado = 0, iduserupdate = ? where id = ?");
    $stmt->execute(array($login, $id));
}
function pruebaBiopsiaInsertar($nombre, $login)
{
    global $db;
    $stmt = $db->prepare("insert into labo_prueba_biopsia (nombre, idusercreate, createdate) VALUES (?, ?, ?)");
    $stmt->execute(array($nombre, $login, date("Y-m-d H:i:s")));
    print("<div id='alerta'> Registro guardado! </div>");
}
function pruebaBiopsiaListar()
{
    global $db;
    $data = null;

    $stmt = $db->prepare("select id, nombre from labo_prueba_biopsia where estado = 1");
    $stmt->execute();
    if ($stmt->rowCount() > 0)
    {
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $data = $stmt->fetchAll();
    }
    return $data;
}
?>