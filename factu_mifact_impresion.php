<?php
if (isset($_GET["id"]) && !empty($_GET["id"]) && isset($_GET["tip"]) && !empty($_GET["tip"])) {

    require($_SERVER["DOCUMENT_ROOT"] . "/_database/db_facturacion_electronica.php");
    require($_SERVER["DOCUMENT_ROOT"] . "/config/environment.php");
    require_once __DIR__ . '/vendor/autoload.php';

    $data = array(
        'tip' => $_GET["tip"],
        'id' => $_GET["id"]
    );

    $response = impresion_facturacion_electronica($data);

    header('Content-type: application/pdf');
    header('Content-Disposition: inline; filename="service.pdf"');
    header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
    echo base64_decode($response["pdf_bytes"]);

} else {
    print("no encontrado");
}
?>
