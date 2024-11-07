<?php require("_database/db_tools.php");
$p_dni = $_GET['a'];	
$id = $_GET['b'];
$dni = $_GET['c'];

$rPare = $db->prepare("SELECT p_nom,p_ape,p_fnac,p_med FROM hc_pareja WHERE p_dni=?");
$rPare->execute(array($p_dni));
$pare = $rPare->fetch(PDO::FETCH_ASSOC);

if ($dni<>"") {
	$rPaci = $db->prepare("SELECT nom,ape,med FROM hc_paciente WHERE dni=?");
	$rPaci->execute(array($dni));
	$paci = $rPaci->fetch(PDO::FETCH_ASSOC);
	
	$o_e = explode(",", $paci['med']);
	foreach ($o_e as $oe) {
	$rMed = $db->prepare("SELECT nom,cmp FROM usuario WHERE userx=?");
	$rMed->execute(array($oe));
	$med = $rMed->fetch(PDO::FETCH_ASSOC);
	$pare_med=$med['nom'].' (CMP '.$med['cmp'].') '.$pare_med;
	}
$pareja='<h5>CONYUGE: <smal><i>'.$paci['ape'].' '.$paci['nom'].'</i></small></h5><h5>MEDICO: <smal><i>'.$pare_med.'</i></small></h5>';
}
if ($pare['p_med']<>"" and $dni=="") {
	$medico= '<h5>MEDICO: <smal><i>'.$pare['p_med'].'</i></small></h5>';
}

if ($_GET['T'] == "esp") $tipo="Espermatograma";
if ($_GET['T'] == "cap") $tipo="Capacitación espermática";
if ($_GET['T'] == "tes_cap") $tipo="Test de Capacitación";
if ($_GET['T'] == "tes_sob") $tipo="Test de Sobrevivencia Espermática";
if ($_GET['T'] == "crio_sem") $tipo="Criopreservación de Semen";
if ($_GET['T'] == "bio_tes") $tipo="Biopsia Testicular";
$html = '<div width="300" style="position: fixed; left: 60mm; top: 5mm; rotate: -90;"><blockquote><h3>'.$tipo.'</h3>
<h5>PACIENTE: <smal><i>'.$pare['p_ape'].' '.$pare['p_nom'].'</i></small></h5>'.$pareja.$medico.'</blockquote></div>';

require($_SERVER["DOCUMENT_ROOT"] . "/config/environment.php");
require_once __DIR__ . '/vendor/autoload.php';
$mpdf = new \Mpdf\Mpdf($_ENV["pdf_regular"]);

$mpdf->WriteHTML($html);
$mpdf->Output();
exit; ?>