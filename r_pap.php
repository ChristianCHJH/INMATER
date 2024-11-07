<!DOCTYPE HTML>
<html>
<head>
<?php
     include 'seguridad_login.php'
    ?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="_themes/tema_inmater.min.css" />
<link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css" />
<link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css" />
<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/jquery.mobile-1.4.5.min.js"></script>

</head>

<body>

<div data-role="page" class="ui-responsive-panel" id="r_pap" data-dialog="true">
<script type="text/javascript">

    function PrintElem(elem) {
        var data = $(elem).html();
		var mywindow = window.open('', 'Imprimir', 'height=400,width=800');
        mywindow.document.write('<html><head><title>Imprimir</title>');
        mywindow.document.write('<link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css" />');
		//mywindow.document.write('<style> @page {margin: 0px 0px 0px 5px;}</style>');
        mywindow.document.write('</head><body><h3>Reporte PAP</h3>');
        mywindow.document.write(data);
		mywindow.document.write('<script type="text/javascript">window.print();<'+'/script>');
        mywindow.document.write('</body></html>');
		return true;
    }

</script>
<style>
.ui-dialog-contain {
  	max-width: 1200px;
	margin: 1% auto 1%;
	padding: 0;
	position: relative;
	top: -35px;
}
</style>
<?php	
$rUser = $db->prepare("SELECT role FROM usuario WHERE userx=?");
$rUser->execute(array($login));
$user = $rUser->fetch(PDO::FETCH_ASSOC);
 
if ($user["role"]==1) {
$rPaci = $db->prepare("SELECT dni,ape,nom FROM hc_paciente WHERE unaccent(med) ILIKE ? ORDER by ape,nom ASC");
$rPaci->execute(array("%$login%"));
}

?> 
<div data-role="header" data-position="fixed">
<h1>Reporte PAP</h1>
<a href="javascript:PrintElem('#imprime')" class="ui-btn-right ui-btn ui-btn-inline ui-mini ui-corner-all" rel="external" data-theme="a">Imprimir</a>
</div><!-- /header -->

<div class="ui-content" role="main">

<FORM ACTION="r_pap.php" method="post" name="form1">
<input id="filterTable-input" data-type="search" placeholder="Filtro por paciente, resultado o año ">
<div id="imprime">
<table width="100%" data-filter="true" data-input="#filterTable-input" class="table-stripe ui-responsive">
    <thead>
        <tr>
            <th data-priority="1">Apellidos y Nombres</th>
            <th data-priority="persist">Resultado</th>
<th data-priority="2">Último Año</th>
            <th data-priority="2">Observaciones</th>
        </tr>
        </thead>
        <tbody>
<?php while($paci = $rPaci->fetch(PDO::FETCH_ASSOC)) { 
	$rPap = $db->prepare("SELECT * FROM hc_antece_pap WHERE dni=? ORDER BY fec DESC LIMIT 1");
	$rPap->execute(array($paci["dni"]));
	$pap = $rPap->fetch(PDO::FETCH_ASSOC);
	
	if ($rPap->rowCount()>0) {
?>
	<tr>
    	<td><?php echo $paci["ape"];?> <small><?php echo $paci["nom"];?></small></td>
        <td><?php echo $pap["tip"];?></td>
        <td><?php echo $pap["obs"];?></td>
        <td><?php echo $pap["fec"];?></td>
    </tr>
    
<?php  } } ?>
</tbody>
</table>
</div>

</FORM>
</div><!-- /content -->

</div><!-- /page -->

</body>

</html>