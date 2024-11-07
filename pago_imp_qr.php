<?php 

    if (isset($_GET["qr"]) && !empty($_GET["qr"])) {
        include('_libraries/phpqrcode/qrlib.php');
        QRcode::png($_GET["qr"]);
    } else {
        print("No se encontraron datos.");
    }
?>