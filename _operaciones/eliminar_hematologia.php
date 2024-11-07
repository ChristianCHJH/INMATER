<?php
    $idinforme="";
    if ( isset($_POST["idinforme"]) && !empty($_POST["idinforme"]) ) {
        $idinforme=$_POST["idinforme"];
    } else {
        exit();
    }
    require("../_database/database.php");
    $consulta = $db->prepare("update hc_hematologia set estado = 0 where id = ?");
    $consulta->execute( array($idinforme) );
?>