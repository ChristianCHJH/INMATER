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

<div data-role="page" class="ui-responsive-panel" id="e_gine_medi" data-dialog="true">

<?php if ($_POST['id_agenda'] <> "") {

updateMedi($_POST['idx'],'',$_POST['medi_name'],$_POST['medi_dosis'],$_POST['medi_frecuencia'],$_POST['medi_cant_dias'],$_POST['medi_init_fec'],$_POST['medi_init_h'],$_POST['medi_init_m'],$_POST['medi_obs'],$_POST['id_agenda']);

}

if ($_GET['id'] <> "") {
$id = $_GET['id'];

$Rpop = $db->prepare("SELECT * FROM hc_agenda WHERE id_agenda=?");
$Rpop->execute(array($id));
$pop = $Rpop->fetch(PDO::FETCH_ASSOC); ?>

<style>
.ui-dialog-contain {
  	
  	max-width: 700px;
	margin: 2% auto 15px;
	padding: 0;
	position: relative;
	top: -15px;
	
}
.scroll_h { overflow-x: scroll; overflow-y: hidden; white-space:nowrap; }
.enlinea div { display : inline-block; vertical-align : middle; }
.peke2 .ui-input-text {
	width: 70px !important;
}
</style>


<div data-role="header" data-position="fixed">
    <h3>Modificar Medicamento</h3>
</div><!-- /header -->

<div class="ui-content" role="main">

<form action="e_gine_medi.php" method="post" data-ajax="false" name="form2">
<input type="hidden" name="id_agenda" value="<?php echo $id;?>">
<input type="hidden" name="idx" value="<?php echo $pop['id'];?>">
<table width="100%" align="center" style="margin: 0 auto;">
		<tr>
		  <td width="109">Medicamento</td>
		  <td colspan="5">
              <?php
              	require("_database/database_farmacia.php");
              	/*//$farma = new PDO('mysql:host=localhost;dbname=farmacia;charset=utf8', 'root', '');
	              $farma = new PDO('mysql:host=localhost;dbname=vigose5_farmacia;charset=utf8', 'vigose5_farma', 'f4rm4.2017');
	              $farma->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	              $farma->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);*/
	              $Rmedi = $farma->prepare("SELECT id,producto FROM tblproducto"); $Rmedi->execute();
              ?>
              <input name="medi_name" list="cate" placeholder="Medicamento.."  value="<?php echo $pop['medi_id'].''.$pop['medi_name'];?>" required>
              <datalist id="cate">
                  <?php while ($medic = $Rmedi->fetch(PDO::FETCH_ASSOC)) {
                      echo '<option value="'.$medic['id'].'|'.$medic['producto'].'"></option>';
                  } ?>
              </datalist>
		  </td>
		  </tr>
		<tr>
		  <td>Dosis</td>
		  <td width="709">
		    <input name="medi_dosis" type="text" data-mini="true" value="<?php echo $pop['medi_dosis'];?>" required/>
		  </td>
		  <td width="78">Frecuencia</td>
		  <td width="144" class="peke2">
		    <input name="medi_frecuencia" type="number" data-mini="true" value="<?php echo $pop['medi_frecuencia'];?>" required/>
		  </td>
		  <td width="33">Dias</td>
		  <td width="144" class="peke2">
		    <input name="medi_cant_dias" type="number" data-mini="true" value="<?php echo $pop['medi_cant_dias'];?>" required/>
		  </td>
	    </tr>
		<tr>
		  <td>Fecha inicio</td>
		  <td colspan="5" class="enlinea"><input name="medi_init_fec" type="date" data-mini="true" value="<?php echo $pop['medi_init_fec'];?>" required/>
        <span class="peke2">
        <input name="medi_init_h" type="number" data-mini="true" value="<?php echo $pop['medi_init_h'];?>" required placeholder="Hora.."/>
        <input name="medi_init_m" type="number" data-mini="true" value="<?php echo $pop['medi_init_m'];?>" required placeholder="Minuto.."/>
        </span></td>
	    </tr>
		<tr>
		  <td>
         Observasiones
          </td>
		  <td colspan="5"><textarea name="medi_obs" id="plan" data-mini="true"><?php echo $pop['medi_obs'];?></textarea></td>
		  </tr>
		</table>
   
<input type="Submit" value="GUARDAR"  data-icon="check" data-iconpos="left" data-mini="true" data-theme="b" data-inline="true"/>
 
 </form>
 
</div><!-- /content -->


<?php } ?>
</div><!-- /page -->


</body>
</html>
