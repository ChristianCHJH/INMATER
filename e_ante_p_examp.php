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

<div data-role="page" class="ui-responsive-panel" id="e_ante_p_examp" data-dialog="true">

<?php if(isset($_POST['p_dni']) || isset($_POST['fec']))if (($_POST['p_dni'] <> "" || $_POST['p_dni'] <> 0)  && $_POST['fec']) {

updateAnte_p_examp($_POST['idx'],$_POST['dni'],$_POST['p_dni'],$_POST['fec'],$_POST['tip'],$_POST['con'],$_FILES['pdf']);

}

if ($_GET['ip'] <> "") {

$dni = $_GET['dni'];
$p_dni = $_GET['ip'];	
$id = $_GET['id'];

$rPare = $db->prepare("SELECT p_nom,p_ape FROM hc_pareja WHERE p_dni=?");
$rPare->execute(array($p_dni));
$pare = $rPare->fetch(PDO::FETCH_ASSOC);

$Rpop = $db->prepare("SELECT * FROM hc_antece_p_examp WHERE id=?");
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
    <h3>Exámenes previos<small> (<?php echo $pare['p_ape']." ".$pare['p_nom']; ?>)</small></h3>
</div><!-- /header -->

<div class="ui-content" role="main">

<form action="e_ante_p_examp.php" method="post" enctype="multipart/form-data" data-ajax="false" name="form2">
<input type="hidden" name="idx" value="<?php echo $id;?>">
<input type="hidden" name="dni" value="<?php echo $dni;?>">
<input type="hidden" name="p_dni" value="<?php echo $p_dni;?>">

<table width="100%" align="center" style="margin: 0 auto;max-width:800px;">
		<tr>
		  <td width="268">Fecha</td>
		  <td width="269">
		    <input name="fec" type="date" id="fec" data-mini="true" value="<?php echo $pop['fec'];?>">
		    </td>
		  <td width="211"><span class="ui-corner-all" style="padding:5px">
		    <select name="tip" id="tip" data-mini="true">
		      <option value="" selected="selected">Seleccione tipo:</option>
                <option value="Espermatograma" <?php if(isset($pop['tip']))if ($pop['tip']=="Espermatograma") echo "selected"; ?>>Espermatograma</option>
                <option value="FISH" <?php if(isset($pop['tip']))if ($pop['tip']=="FISH") echo "selected"; ?>>FISH</option>
                <option value="Test de fragmentación" <?php if(isset($pop['tip']))if ($pop['tip']=="Test de fragmentación") echo "selected"; ?>>Test de fragmentación</option>
                <option value="Cultivo de semen" <?php if(isset($pop['tip']))if ($pop['tip']=="Cultivo de semen") echo "selected"; ?>>Cultivo de semen</option>
	        </select>
		  </span></td>
		  <td width="607">&nbsp;</td>
		  </tr>
		<tr>
		  <td>Condición</td>
		  <td colspan="3"><textarea name="con" id="con" data-mini="true"><?php if(isset($pop['con']))echo $pop['con'];?></textarea></td>
	    </tr>
		<tr>
		  <td>ADJUNTAR RESULTADO (PDF)</td>
		  <td colspan="3"><input name="pdf" type="file" accept="application/pdf" id="pdf"/>
		    <?php if (file_exists("analisis/p_examp_".$id.".pdf")) echo "<a href='archivos_hcpacientes.php?idArchivo=p_examp_".$id."' target='new'>Ver Resultado</a>"; ?></td>
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
	if (document.getElementById("tip").value == "") {
				alert ("Debe especificar el Tipo");
				return false;
			}
	if (document.getElementById("con").value == "") {
				alert ("Debe especificar la Condición");
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
