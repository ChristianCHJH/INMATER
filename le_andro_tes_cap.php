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

<div data-role="page" class="ui-responsive-panel" id="le_andro_tes_cap" data-dialog="true">

<?php if ($_POST['p_dni'] <> "" and $_POST['fec']) {

updateAndro_tes_cap($_POST['idx'],$_POST['dni'],$_POST['p_dni'],$_POST['fec'],$_POST['vol_f'],$_POST['con_f'],$_POST['con_c'],$_POST['pl_f'],$_POST['pl_c'],$_POST['pnl_f'],$_POST['pnl_c'],$_POST['ins_f'],$_POST['ins_c'],$_POST['inm_f'],$_POST['inm_c'],$_POST['cap'],$_POST['sel'],$_POST['emb']);

}

if ($_GET['ip'] <> "") {

$dni = $_GET['dni'];
$p_dni = $_GET['ip'];	
$id = $_GET['id'];

$rPare = $db->prepare("SELECT p_nom,p_ape FROM hc_pareja WHERE p_dni=?");
$rPare->execute(array($p_dni));
$pare = $rPare->fetch(PDO::FETCH_ASSOC);

$rEmb = $db->prepare("SELECT id,nom FROM lab_user WHERE sta=0");
$rEmb->execute();

$Rpop = $db->prepare("SELECT * FROM lab_andro_tes_cap WHERE p_dni=? and fec=?");
$Rpop->execute(array($p_dni,$id));
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

.pequeno2 .ui-input-text {
	width: 80px !important; display:inline-block !important;
}

.scroll_h { overflow-x: scroll; overflow-y: hidden; white-space:nowrap; } 
</style>
<script>
$(document).ready(function () {
		
	var vol_f=$('#vol_f').val();
	var con_f=$('#con_f').val();
	var con_c=$('#con_c').val();
	$('#spz_f').html((vol_f*con_f).toFixed(2));  
	$('#spz_c').html((0.3*con_c).toFixed(2));  
    
	 $(".total_spz").change(function () {
		var vol_f=$('#vol_f').val();
		var con_f=$('#con_f').val();
		var con_c=$('#con_c').val();
		
		var pl_f=Number($('#pl_f').val());
		var pl_c=Number($('#pl_c').val());
		var pnl_f=Number($('#pnl_f').val());
		var pnl_c=Number($('#pnl_c').val());
		var ins_f=Number($('#ins_f').val());
		var ins_c=Number($('#ins_c').val());
		
			$('#spz_f').html((vol_f*con_f).toFixed(2));  
			$('#spz_c').html((0.3*con_c).toFixed(2)); 
			
			$("#inm_f").val(100-(pl_f+pnl_f+ins_f));
			$("#inm_c").val(100-(pl_c+pnl_c+ins_c));
		});

	
});
</script>

<div data-role="header" data-position="fixed">
    <h3>Test de capacitación<small> (<?php echo $pare['p_ape']." ".$pare['p_nom']; ?>)</small></h3>
</div><!-- /header -->

<div class="ui-content" role="main">

<form action="le_andro_tes_cap.php" method="post" data-ajax="false" name="form2">
<input type="hidden" name="idx" value="<?php echo $id;?>">
<input type="hidden" name="dni" value="<?php echo $dni;?>">
<input type="hidden" name="p_dni" value="<?php echo $p_dni;?>">

<table width="100%" align="center" style="margin: 0 auto; font-size: small;">
		<tr>
		  <td width="25%">Fecha</td>
		  <td width="32%"><input name="fec" type="date" required id="fec" value="<?php if ($pop['fec']) echo $pop['fec']; else echo date("Y-m-d"); ?>" data-mini="true"></td>
		  <td width="7%">Embriologo</td>
		  <td width="36%"><select name="emb" required id="emb" data-mini="true">
		    <option value="">---</option>
		    <?php  while ($embrio = $rEmb->fetch(PDO::FETCH_ASSOC)) { ?>
		    <option value=<?php echo $embrio['id']; if ($pop['emb']==$embrio['id']) echo " selected"; ?>><?php echo $embrio['nom']; ?></option>
		    <?php } ?>
		    </select></td>
		  </tr>
		<tr>
		  <td>&nbsp;</td>
		  <td colspan="2" align="center" class="ui-bar-a">FRESCO</td>
		  <td align="center" class="ui-bar-a">CAPACITADO</td>
		  </tr>
		<tr>
		  <td>Volumen	      </td>
		  <td colspan="2" align="center" class="pequeno2"><input name="vol_f" type="number" step="any" required class="total_spz" id="vol_f" min="0" tabindex="1" value="<?php echo $pop['vol_f'];?>" data-mini="true"> 
		  (ml)</td>
		  <td align="center" class="pequeno2">0.3 ml</td>
	    </tr>
		<tr>
		  <td>Concentración</td>
		  <td colspan="2" align="center" class="pequeno2"><input name="con_f" type="number" step="any" required class="total_spz" id="con_f" min="0" tabindex="2" value="<?php echo $pop['con_f'];?>" data-mini="true"> 
	      x10<sup>6</td>
		  <td align="center" class="pequeno2"><input name="con_c" type="number" step="any" required class="total_spz" id="con_c" min="0" tabindex="7" value="<?php echo $pop['con_c'];?>" data-mini="true">
		    x10<sup>6</td>
	    </tr>
		<tr>
		  <td>Total de spz</td>
		  <td colspan="2" align="center" class="pequeno2"><div id="spz_f" style="color:#900"></div></td>
		  <td align="center" class="pequeno2"><div id="spz_c" style="color:#900"></div></td>
	    </tr>
		<tr>
		  <td>Progresivo Lineal</td>
		  <td colspan="2" align="center" class="pequeno2"><input name="pl_f" type="number" step="any" required class="total_spz" id="pl_f" tabindex="3" value="<?php echo $pop['pl_f'];?>" data-mini="true"> 
		  %</td>
		  <td align="center" class="pequeno2"><input name="pl_c" type="number" step="any" required class="total_spz" id="pl_c" tabindex="8" value="<?php echo $pop['pl_c'];?>" data-mini="true"> 
	      %</td>
	    </tr>
		<tr>
		  <td>Progresivo No Lineal</td>
		  <td colspan="2" align="center" class="pequeno2"><input name="pnl_f" type="number" step="any" required class="total_spz" id="pnl_f" tabindex="4" value="<?php echo $pop['pnl_f'];?>" data-mini="true"> 
		  %</td>
		  <td align="center" class="pequeno2"><input name="pnl_c" type="number" step="any" required class="total_spz" id="pnl_c" tabindex="9" value="<?php echo $pop['pnl_c'];?>" data-mini="true"> 
		    %</td>
	    </tr>
		<tr>
		  <td>Insitu</td>
		  <td colspan="2" align="center" class="pequeno2"><input name="ins_f" type="number" step="any" required class="total_spz" id="ins_f" tabindex="5" value="<?php echo $pop['ins_f'];?>" data-mini="true">
		  %</td>
		  <td align="center" class="pequeno2"><input name="ins_c" type="number" step="any" required class="total_spz" id="ins_c" tabindex="10" value="<?php echo $pop['ins_c'];?>" data-mini="true"> 
		    %</td>
	    </tr>
		<tr>
		  <td>No Motil</td>
		  <td colspan="2" align="center" class="pequeno2"><input name="inm_f" type="number" step="any" required id="inm_f" tabindex="6" value="<?php echo $pop['inm_f'];?>" readonly data-mini="true">
		    %</td>
		  <td align="center" class="pequeno2"><input name="inm_c" type="number" step="any" required id="inm_c" tabindex="11" value="<?php echo $pop['inm_c'];?>" readonly data-mini="true">
		    %</td>
	    </tr>
		<tr>
		  <td align="center">Tipo de Capacitación
		    <select name="cap" required id="cap" title="12" data-mini="true">
		    <option value=1 <?php if ($pop['cap']==1 or $pop['cap']=="") echo "selected"; ?>>Gradiente densidad</option>
		    <option value=2 <?php if ($pop['cap']==2) echo "selected"; ?>>Lavado</option>
		    <option value=3 <?php if ($pop['cap']==3) echo "selected"; ?>>Swim up</option>
	      </select></td>
		  <td colspan="2" align="center">Tipo de Selección Espermática
		    <select name="sel" required id="sel" tabindex="13" data-mini="true">
		    <option value=1 <?php if ($pop['sel']==1 or $pop['sel']=="") echo "selected"; ?>>Aleatoria</option>
		    <option value=2 <?php if ($pop['sel']==2) echo "selected"; ?>>Masculina</option>
		    <option value=3 <?php if ($pop['sel']==3) echo "selected"; ?>>Femenina</option>
	      </select></td>
		  <td align="center">&nbsp;</td>
	    </tr>
</table>
<?php //if ($login=="lab" and $pop['emb']=="") { 
if ($_SESSION['role'] == 2) {?>
<input type="Submit" name="guardar" value="GUARDAR" data-icon="check" data-iconpos="left" data-mini="true" data-theme="b" data-inline="true"/>
<?php } ?>
 </form>
 
</div><!-- /content -->


<?php } ?>
</div><!-- /page -->

</body>
</html>
