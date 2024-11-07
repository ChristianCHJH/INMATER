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

<div data-role="page" class="ui-responsive-panel" id="le_aspi9" data-dialog="true">

<?php if ($_POST['cont'] <> "" and $_POST['dni'] <> "") {
Donar_Ovo_Emb($login,$_POST['dni'],$_POST['cont']);		
}

if ($_GET['id'] <> "") {
$rUser = $db->prepare("SELECT role FROM usuario WHERE userx=?");
$rUser->execute(array($login));
$user = $rUser->fetch(PDO::FETCH_ASSOC);
	
$dni = $_GET['id'];
$rPaci = $db->prepare("SELECT nom,ape FROM hc_paciente WHERE dni=?");
$rPaci->execute(array($dni));
$paci = $rPaci->fetch(PDO::FETCH_ASSOC); 
?>

<style>
.ui-dialog-contain {
  	
  	max-width: 700px;
	margin: 2% auto 15px;
	padding: 0;
	position: relative;
	top: -15px;
	
}
.color { color:#F4062B; }
.chk_ovo  {background-color:#E4E075;}
.chk_emb  {background-color:#8ADB9F;}
</style>
<script>
$(document).ready(function () {
	$('#form2').submit(function() {
        if (confirm("Presione ACEPTAR para efectuar la donación, los cambios seran IRREVERSIBLES por Ud.")) {
        } else return false;
    });
});
</script>
<div data-role="header" data-position="fixed">
<h3>Ovulos-Embriones Criopreservados</h3>
</div><!-- /header -->

<div class="ui-content" role="main">

<form action="e_repro_don.php" method="post" data-ajax="false" id="form2">
<input type="hidden" name="dni" value="<?php echo $dni;?>">

<p class="ui-bar-c">PACIENTE:<small> <?php echo $paci['ape']." ".$paci['nom']; ?></small></p>
<?php $rRes = $db->prepare("SELECT t,c,v,p,tip_id,pro,ovo_emb,don FROM lab_tanque_res WHERE sta=? and med=0 ORDER by tip_id ASC");
$rRes->execute(array($dni));
	if ($rRes->rowCount()>0) {
	echo '<table bordercolor="#72a2aa" style="margin: 0 auto; font-size: small; width:80%">';
	$temp_dia = "";	
	$c = 0;
	while($res = $rRes->fetch(PDO::FETCH_ASSOC)) { 
	$c++;
		  if ($res["tip_id"]<>$temp_dia) { 
		  if ($res["tip_id"]>=1) { $bg='class="chk_emb"'; $ovo_emb='Embriones';}
		  if ($res["tip_id"]==0) { $bg='class="chk_ovo"'; $ovo_emb='Ovulos';}
			
          echo '<tr class="ui-bar-c">';
          echo '<td width="2%">T</td><td width="2%">C</td><td width="2%">Gobelet</td><td width="3%">Pajuela</td><td width="50%"> # '.$ovo_emb.' - ';
          echo ' DIA '.$res["tip_id"].'</td>';
		  echo '<td>Fecha</td>';
          echo '</tr>'; 
          $temp_dia = $res["tip_id"];
		  }
	if ($res["don"]<>"") { $don='<b class="color">DONADO</b>'; $donchek='disabled'; } else { $don=''; $donchek='';}
		   echo '<tr>';
           echo '<td>'.$res["T"].'</td>';
           echo '<td>'.$res["C"].'</td>';
           echo '<td '.$bg.'>'.$res["V"].'</td>';
           echo '<td '.$bg.'><label for="c'.$c.'">'.$res["P"].'</label>';
		   echo '<input type="checkbox" name="c'.$c.'" id="c'.$c.'" value="'.$res["T"].'-'.$res["C"].'-'.$res["V"].'-'.$res["P"].'-'.$res["pro"].'-'.$res["tip_id"].'" data-mini="true" data-iconpos="right" '.$donchek.'>'.$don.'<input type="hidden" name="d'.$c.'" value='.$res["ovo_emb"].' ></td>';
           echo '<td>';
		   
		   $o_e = explode(",", $res["ovo_emb"]);
		   
		   $c2 = 0;
		   if ($res["tip_id"]>=1) echo '<ul>';
			  foreach ($o_e as $oe) {
				  $c2++;
				  if ($res["tip_id"]>=1) { // embriones
				  
				  $rOvo = $db->prepare("SELECT * FROM lab_aspira_dias WHERE pro=? and ovo=? and estado is true");
				  $rOvo->execute(array($res['pro'],$oe));
				  $ovo = $rOvo->fetch(PDO::FETCH_ASSOC);
				  
				  if ($res["tip_id"]==1) { $campos='CP:'.$ovo["d1c_pol"].' PN:'.$ovo["d1pron"]; }
				  if ($res["tip_id"]==2) { $campos=$ovo["d2cel"].' - '.$ovo["d2fra"].'%'; }
				  if ($res["tip_id"]==3) { $campos=$ovo["d3cel"].' - '.$ovo["d3fra"].'%'; }
				  if ($res["tip_id"]==4) { $campos=$ovo["d4cel"].' - '.$ovo["d4fra"].'%'; }
				  if ($res["tip_id"]==5) { $campos=$ovo["d5cel"].' - '.$ovo["d5fra"].'%'; }
				  if ($res["tip_id"]==6) { $campos=$ovo["d6cel"].' - '.$ovo["d6fra"].'%'; }
				  echo '<li>'.$campos.'</li>';
				  }
			  }
			if ($res["tip_id"]>=1) echo '</ul>'; 
			if ($res["tip_id"]==0) echo $c2; // ovulos
		   echo '</td>';
		   $rAsp = $db->prepare("SELECT fec".$res["tip_id"]." FROM lab_aspira WHERE pro=? and lab_aspira.estado is true"); // selecciona la fecha del dia correspondiente ----
		   $rAsp->execute(array($res["pro"]));	
		   $asp = $rAsp->fetch(PDO::FETCH_ASSOC);
		   echo '<td>'.date("d-m-Y", strtotime($asp["fec".$res["tip_id"]])).'</td>';
      	   echo '</tr>';
		   
	} echo '<input type="hidden" name="cont" value='.$c.' >'; 
	echo '</table>'; 
	if ($user["role"]==1) { ?>
    <h4 style="display:inline-block;">Seleccione las pajuelas que desee donar y luego presione aquí: </h4><input type="submit" name="guardar" value="DONAR" data-icon="check" data-iconpos="left" data-mini="true" data-theme="b" data-inline="true"/>
    <?php }
	} else { echo '<h3>La paciente no tiene Ovulos y/o Embriones criopreservados</h3>'; } ?>
</form>
</div><!-- /content -->
<?php } ?>	
</div><!-- /page -->
</body>
</html>
