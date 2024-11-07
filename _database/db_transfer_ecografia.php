<?php
require($_SERVER["DOCUMENT_ROOT"] . "/_database/database.php");
require($_SERVER["DOCUMENT_ROOT"] . "/config/environment.php");

function traer_transfer_ecografia($id)
{
    global $db;
    $stmt = $db->prepare("SELECT * from transfer_ecografia where id=?;");
    $stmt->execute([$id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}