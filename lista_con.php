<?php session_start(); ?>
<!DOCTYPE HTML>
<html>
<head>
<?php $login = $_SESSION['login'];
$dir = $_SERVER['HTTP_HOST'] . substr(dirname(__FILE__), strlen($_SERVER['DOCUMENT_ROOT']));
if ($_SESSION['role'] <> 2) {
echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=http://".$dir."'>";
}
require("_database/db_tools.php"); ?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="_themes/tema_inmater.min.css" />
<link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css" />
<link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css" />
<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/jquery.mobile-1.4.5.min.js"></script>
<script src="jstickytableheaders.js"></script>
<link rel="stylesheet" href="http://tablesorter.com/themes/blue/style.css" type="text/css" media="print, projection, screen">
<script type="text/javascript" src="http://tablesorter.com/__jquery.tablesorter.min.js"></script>
<style>
.scroll_h { overflow: auto;}
#alerta { background-color:#FF9;margin: 0 auto; text-align:center; padding:4px;}
.ui-icon-craneo {
	background: url(https://www.iconexperience.com/_img/o_collection_png/green_dark_grey/64x64/plain/skull2.png) 50% 50% no-repeat;
	background-size: 22px 22px;
}
.mayuscula { text-transform: uppercase; font-size:small; }

</style>
<script>
$(document).ready(function () {	
	
    $(".tablesorter").stickyTableHeaders(); // Cabecera flotante o fija en la tabla
	$(".tablesorter").tablesorter();  // table sort

});
</script>
</head>

<body>
<?php if ($_SESSION['role'] == 2) { ?>


<div data-role="page" class="ui-responsive-panel">

<div data-role="header">
	<div data-role="controlgroup" data-type="horizontal" class="ui-mini ui-btn-left">
	<a href='lista.php' class="ui-btn ui-btn-c ui-icon-home ui-btn-icon-left" rel="external">Inicio</a>
    <a href='n_control.php' class="ui-btn ui-mini ui-btn-inline" rel="external">Agregar Insumo</a>
    </div>
    <h1>Control de Calidad de Insumos</h1>
    <a href="salir.php" class="ui-btn-right ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-right ui-icon-power" rel="external">Salir</a>
</div><!-- /header -->

<div class="ui-content" role="main">
<form action="r_pro.php" method="post" data-ajax="false" id="form1">

<?php $Rpak = $db->prepare("SELECT DISTINCT cat FROM lab_control ORDER BY cat ASC"); 
$Rpak->execute();
$Rpak->setFetchMode(PDO::FETCH_ASSOC);
$pak = $Rpak->fetchAll();

 $rPaci = $db->prepare("SELECT hc_paciente.dni,ape,nom,lab_aspira.pro,lab_aspira.tip,lab_aspira.vec,lab_aspira.dias,lab_aspira.f_fin FROM hc_paciente,lab_aspira WHERE hc_paciente.dni=lab_aspira.dni and lab_aspira.estado is true and lab_aspira.tip<>'T' and lab_aspira.f_fin<>'1899-12-30' ORDER by lab_aspira.f_fin DESC");
$rPaci->execute();


//print_r($pak);
if ($rPaci->rowCount()>0 and $Rpak->rowCount()>0) { ?>

<div class="scroll_h">
<table width="100%" border="0" style="margin:0 auto;font-size:small;" class="tablesorter">
<thead>
<tr class="ui-bar-b">
<th width="5%" bgcolor="#F0DF96">PROTOCOLOS (<?php echo $rPaci->rowCount(); ?>)</th>
<th width="5%" align="center">Finalizado</th>
<?php foreach ($pak as $pak2)  { 
echo "<th>".$pak2['cat']."</th>";
} ?>

</tr></thead>
<tbody>
<?php while($paci = $rPaci->fetch(PDO::FETCH_ASSOC)) {

echo '<tr><td>'.$paci['tip'].'-'.$paci['pro'].'</td>';
echo '<td>'.date("d-m-Y", strtotime($paci['f_fin'])).'</td>'; 

foreach ($pak as $pak2)  { 
	$Ruso = $db->prepare("SELECT nom,f_uso,color FROM lab_control WHERE cat=? and f_uso<=? ORDER BY f_uso DESC LIMIT 1"); 
	$Ruso->execute(array($pak2['cat'],$paci['f_fin']));
	$uso = $Ruso->fetch(PDO::FETCH_ASSOC); 
	if($uso['f_uso']<>"") { $f_uso=date("d-m-Y", strtotime($uso['f_uso']))." ".$uso['nom']; } else $f_uso="No existe";
	echo "<td style='background-color:rgba(".$uso['color'].",0.20);'>".$f_uso."</td>";
}

echo '</tr>';

 } ?>
</tbody></table></div>  
<?php } else echo "<h4>No hay Insumos!</h4>"; ?>

</form>
</div><!-- /content -->

</div><!-- /page -->
<?php } ?>

</body>
</html>
