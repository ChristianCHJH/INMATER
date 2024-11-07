<?php
require("_database/db_tools.php");
if ($_GET['p_dni'] <> "") {
    $p_dni = $_GET['p_dni']; //dni de la pareja
    $fec = $_GET['fec']; //fecha de solicitud de espermatograma
    $dni = $_GET['dni']; //dni de la paciente

    $rPare = $db->prepare("SELECT p_nom, p_ape, p_med FROM hc_pareja WHERE p_dni=?");
    $rPare->execute(array($p_dni));
    $pare = $rPare->fetch(PDO::FETCH_ASSOC);
    $pareja = $pare['p_ape']." ".$pare['p_nom'];

    $rPaci = $db->prepare("SELECT ape, nom FROM hc_paciente WHERE dni=?");
    $rPaci->execute(array($dni));
    $paci = $rPaci->fetch(PDO::FETCH_ASSOC);
    $paciente=''; 
    if(isset($paci['ape']) && isset($paci['nom']))$paciente=$paci['ape']." ".$paci['nom'];

    $rMedi = $db->prepare("SELECT nom FROM usuario WHERE userx=?");
    $rMedi->execute(array($pare['p_med']));
    $medi = $rMedi->fetch(PDO::FETCH_ASSOC);
    $medico='';
    if(isset($medi['nom']))$medico = $medi['nom'];

    $rLiqEspe = $db->prepare("SELECT id, nombre FROM licuefaccion_esperma where estado=1");
    $rLiqEspe->execute();
    $liqespe = $rLiqEspe->fetchAll();

    $rVisEspe = $db->prepare("SELECT id, nombre FROM viscosidad_esperma where estado=1");
    $rVisEspe->execute();
    $visespe = $rVisEspe->fetchAll();

    $rApaEspe = $db->prepare("SELECT id, nombre FROM apariencia_esperma where estado=1");
    $rApaEspe->execute();
    $apaespe = $rApaEspe->fetchAll();

    $rSino = $db->prepare("SELECT id, nombre FROM si_no where estado=1");
    $rSino->execute();
    $rows = $rSino->fetchAll();

    $rSino1 = $db->prepare("SELECT id, nombre FROM man_aglutinacion where estado=1");
    $rSino1->execute();
    $rows1 = $rSino1->fetchAll();

    $rmobte = $db->prepare("SELECT id, nombre from metodo_obtencion where estado=1");
    $rmobte->execute();

    $rlobte = $db->prepare("SELECT id, nombre from lugar_obtencion where estado=1");
    $rlobte->execute();

    $rEmb = $db->prepare("SELECT id,nom FROM lab_user WHERE sta=0");
    $rEmb->execute();

    $Rpop = $db->prepare("SELECT * FROM lab_andro_esp WHERE p_dni=? AND fec=?");
    $Rpop->execute(array($p_dni, $fec));
    $pop = $Rpop->fetch(PDO::FETCH_ASSOC);
}
?>