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

<div data-role="page" class="ui-responsive-panel" id="e_ante_quiru" data-dialog="true">

<?php if(isset($_POST['dni']))if ($_POST['dni'] <> "" && $_POST['fec']) {

updateAnte_quiru($_POST['idx'],$_POST['dni'],$_POST['fec'],$_POST['pro'],$_POST['med'],$_POST['dia'],$_POST['lug'],$_FILES['pdf']);

}

if ($_GET['dni'] <> "") {

$dni = $_GET['dni'];	
$id = $_GET['id'];

$Rpop = $db->prepare("SELECT * FROM hc_antece_quiru WHERE id=?");
$Rpop->execute(array($id ? : 0));
$pop = $Rpop->fetch(PDO::FETCH_ASSOC);
 ?>


<style>
.ui-dialog-contain {
  	
  	max-width: 800px;
	margin: 2% auto 15px;
	padding: 0;
	position: relative;
	top: -15px;
	
}
.scroll_h { overflow-x: scroll; overflow-y: hidden; white-space:nowrap; } 
</style>
<script>
$(document).ready(function () {
    
    $('.numeros').keyup(function() {
		
        var $th = $(this);
        $th.val( $th.val().replace(/[^0-9]/g, function(str) { 
            //$('#cod small').replaceWith('<small>Error: Porfavor ingrese solo letras y números</small>');
            
            return ''; } ) );
			
			//$('#cod small').replaceWith('<small>Aqui ingrese siglas o un nombre corto de letras y números</small>');
    });
	
});
</script>

<div data-role="header" data-position="fixed">
    <h3>Quirúrgicos</h3>
</div><!-- /header -->

<div class="ui-content" role="main">

<form action="e_ante_quiru.php" method="post" enctype="multipart/form-data" data-ajax="false" name="form2">
<input type="hidden" name="idx" value="<?php echo $id;?>">
<input type="hidden" name="dni" value="<?php echo $dni;?>">

<table width="100%" align="center" style="margin: 0 auto;max-width:800px;">
		<tr>
		  <td width="268">Fecha</td>
		  <td width="269">
		    <input name="fec" type="date" id="fec" data-mini="true" value="<?php echo $pop['fec'];?>">
		    </td>
		  <td width="211">Médico</td>
		  <td width="607"><input name="med" type="text" id="med" data-mini="true" value="<?php if(isset($pop['med']))echo $pop['med'];?>"></td>
		  </tr>
		<tr>
		  <td> Procedimiento </td>
		  <td><textarea name="pro" id="pro" data-mini="true"><?php if(isset($pop['pro']))echo $pop['pro'];?></textarea></td>
		  <td>Lugar</td>
		  <td><input name="lug" type="text" id="lug" data-mini="true" value="<?php if(isset($pop['lug']))echo $pop['lug'];?>"></td>
		  </tr>
		<tr>
		  <td>Diagnóstico</td>
		  <td><textarea name="dia" id="dia" data-mini="true"><?php if(isset($pop['dia']))echo $pop['dia'];?></textarea></td>
		  <td>ADJUNTAR RESULTADO (PDF)</td>
		  <td><input name="pdf" type="file" accept="application/pdf" id="pdf"/>
		    <?php if (file_exists("analisis/quiru_".$id.".pdf")) echo "<a href='archivos_hcpacientes.php?idArchivo=quiru_".$id."' target='new'>Ver Resultado</a>"; ?></td>
		  </tr>
		</table>
   
<input type="Submit" name="guardar" value="GUARDAR" data-icon="check" data-iconpos="left" data-mini="true" class="show-page-loading-msg" data-textonly="false" data-textvisible="true" data-msgtext="Guardando datos.." data-theme="b" data-inline="true"/>
 
 </form>
 
</div><!-- /content -->


<?php } ?>
</div><!-- /page -->


<script>
$( document ).on( "click", ".show-page-loading-msg", function() {
	
	if (document.getElementById("fec").value == "") {
				alert ("Debe especificar la Fecha");
				return false;
			}
	if (document.getElementById("pro").value == "") {
				alert ("Debe especificar el Procedimiento");
				return false;
			}		
    var $this = $( this ),
        theme = $this.jqmData( "theme" ) || $.mobile.loader.prototype.options.theme,
        msgText = $this.jqmData( "msgtext" ) || $.mobile.loader.prototype.options.text,
        textVisible = $this.jqmData( "textvisible" ) || $.mobile.loader.prototype.options.textVisible,
        textonly = !!$this.jqmData( "textonly" );
        html = $this.jqmData( "html" ) || "";
    $.mobile.loading( "show", {
            text: msgText,
            textVisible: textVisible,
            theme: theme,
            textonly: textonly,
            html: html
    });
})
.on( "click", ".hide-page-loading-msg", function() {
    $.mobile.loading( "hide" );
});
 

</script>
</body>
</html>
