<?php
session_start();

if (empty($_SESSION['login'])) {
    http_response_code(400);
    echo json_encode(array("message" => "No se ha iniciado sesión"));
    exit();
}

$login = $_SESSION['login'];

require($_SERVER["DOCUMENT_ROOT"] . "/_database/database.php");
require($_SERVER["DOCUMENT_ROOT"] . "/config/environment.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['video'])) {
        $video = $_FILES['video'];

        $nombre_original = $video['name'];
        $nombre_base = time()."-".$nombre_original;
        $path = "/archivo";
        $ruta = $_SERVER["DOCUMENT_ROOT"] . $path . '/' . $nombre_base;

        // verifica que el archivo haya sido subido correctamente
        if (move_uploaded_file($video['tmp_name'], $ruta)) {
            // registrar video
            $stmt = $db->prepare("INSERT INTO man_archivo (nombre_base, nombre_original, idusercreate) values (?, ?, ?)");
            $stmt->execute(array($nombre_base, $nombre_original, $login));
            $id = $db->lastInsertId();

            http_response_code(201);
            echo json_encode(array("message" => ["nombre_base" => $nombre_base, "nombre_original" => $nombre_original, "idarchivo" => $id]));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Error al subir el archivo"));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "No se ha recibido ningún archivo"));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Método no permitido"));
}
?>
