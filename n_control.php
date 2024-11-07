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
  <link rel="icon" href="_images/favicon.png" type="image/x-icon">
  <script src="js/jquery-1.11.1.min.js"></script>
  <script src="js/jquery.mobile-1.4.5.min.js"></script>
</head>
<body>
  <div data-role="page" class="ui-responsive-panel" id="n_control" data-dialog="true">

  <?php if ($_POST['conf']<>"") {
  	$stmt = $db->prepare("DELETE FROM lab_control WHERE id=?");
  	$stmt->execute(array($_POST['conf']));
  } 

  if ($_POST['nom'] <> "" and $_POST['f_uso'] <> "" and $_POST['cat'] <> "") {
  	$rgbColor = array();
   	foreach(array('r', 'g', 'b') as $color){
      $rgbColor[$color] = mt_rand(0, 255); //Generate a random number between 0 and 255.
  	}
  	$color=$rgbColor['r'].",".$rgbColor['g'].",".$rgbColor['b'];

  Control($_POST['nom'],strtoupper($_POST['cat']),$_POST['pres'],$_POST['lote'],$_POST['f_ven'],$_POST['f_ing'],$_POST['f_uso'],$_POST['sob1'],$_POST['sob2'],$_POST['sob3'],$color);

  }

  $Rpop = $db->prepare("SELECT * FROM lab_control ORDER BY cat ASC, f_uso DESC");
  $Rpop->execute(); 
  $rEmb = $db->prepare("SELECT nom FROM lab_user WHERE sta=0");
  $rEmb->execute(); ?>
  <style>
  .ui-dialog-contain {
    	max-width: 1500px;
  	margin: 1% auto 1%;
  	padding: 0;
  	position: relative;
  	top: -35px;
  }
  .peke2 .ui-input-text {
  	width: 120px !important;
  }
  .mayuscula { text-transform: uppercase; }
  #alerta { background-color:#FF9;margin: 0 auto; text-align:center; padding:4px;}
  </style>
  <script>
  $(document).ready(function () {
   $('.sinEspacio').keyup(function() {
  		var $th = $(this);
      $th.val( $th.val().replace(/[^a-z0-9]/g, function(str) { 
        //$('#cod small').replaceWith('<small>Error: Porfavor ingrese solo letras y números</small>');
        return ''; } ) );
  			//$('#cod small').replaceWith('<small>Aqui ingrese siglas o un nombre corto de letras y números</small>');
    });

    $(".sobre").hide();
    $("#sobre").change(function () {	
  		
  		if ($(this).prop('checked') ) {
  			$(".sobre").show();
  			$("#sob1,#sob2,#sob3").prop('required',true); 
  		} else {
  			$(".sobre").hide();
  			$("#sob1,#sob2,#sob3").prop('required',false); 
  			$("#sob1,#sob2,#sob3").val(''); 
  		}
  	});
  });

  function anular(x) {
  	document.form2.conf.value=x;
  	document.form2.nom.value=""; //para que no inserte registros
  	if (confirm("Esta seguro que desea borrar esta Referencia?"))
  	document.form2.submit();
  	else
  	return false;
  }

  </script>
  <div data-role="header" data-position="fixed">
  <a href="lista_con.php" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>
      <h3>INSUMOS</h3>
  </div><!-- /header -->

  <div class="ui-content" role="main">
  <form action="n_control.php" method="post" data-ajax="false" name="form2">
  <input type="hidden" name="conf">
  <div class="ui-bar ui-bar-a">
  <table style="margin: 0 auto;" width="100%">
  <tr>
    <th width="27%">PRODUCTO</th>
    <th width="12%">Referencia</th>
    <th width="12%">Presentación</th>
    <th width="12%">Lote</th>
    <th width="12%">Vencimiento</th>
    <th width="12%">Ingreso</th>
    <th width="13%">USO</th>
    </tr>
  <tr>
    <th><?php $Rpak = $db->prepare("SELECT DISTINCT cat FROM lab_control ORDER BY cat ASC"); $Rpak->execute(); ?>
      <input name="cat" required list="cate" maxlength="20" class="mayuscula sinEspacio">
      <datalist id="cate">
        <?php while ($pak = $Rpak->fetch(PDO::FETCH_ASSOC)) { 
  		  echo '<option value="'.$pak['cat'].'">';
  		} ?>
      </datalist>
      </th>
    <td><input name="nom" type="text" required id="nom" maxlength="7" data-mini="true"/></td>
    <td class="peke2"><input name="pres" type="text" required id="pres" maxlength="20" data-mini="true"/></td>
    <td class="peke2"><input name="lote" type="text" required id="lote" maxlength="20" data-mini="true"/></td>
    <td><input type="date" name="f_ven" id="f_ven" data-mini="true" required/></td>
    <td><input type="date" name="f_ing" id="f_ing" data-mini="true" required/></td>
    <td><input type="date" name="f_uso" id="f_uso" data-mini="true" required/></td>
    </tr>
  <tr>
    <th>&nbsp;</th>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td><label for="sobre">Sobrevida</label><input type="checkbox" name="sobre" id="sobre" data-mini="true" value=1></td>
    <td>&nbsp;</td>
    <td><input type="Submit" name="guardar" value="AGREGAR" data-icon="check" data-iconpos="left" data-mini="true" data-inline="true"/></td>
  </tr>
  <tr class="sobre">
    <th>&nbsp;</th>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>Fecha</td>
    <td>Embriologo</td>
    <td>Porcentaje (%)</td>
    <td>&nbsp;</td>
  </tr>
  <tr class="sobre">
    <th>&nbsp;</th>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td><input name="sob1" type="date" id="sob1" data-mini="true"/></td>
    <td><select name="sob2" required id="sob2" data-mini="true">
      <option value="">Seleccione..</option>
        <?php  while ($embrio = $rEmb->fetch(PDO::FETCH_ASSOC)) { ?>
        <option value="<?php echo $embrio['nom']; ?>"><?php echo $embrio['nom']; ?></option>
        <?php } ?>
    </select></td>
    <td class="peke2"><input name="sob3" type="number" id="sob3" max="100" min="0" data-mini="true"/></td>
    <td>&nbsp;</td>
    </tr>
  </table>
  </div>
  <?php if ($Rpop->rowCount()>0) { ?>
  <input id="filtrongs" data-type="search" placeholder="Filtro..">
  <table data-role="table" data-filter="true" data-input="#filtrongs" class="table-stripe ui-responsive">
  <thead>
  <tr>
  <th>PRODUCTO</th>
  <th>Referencia</th>
  <th>Presentación</th>
  <th>Lote</th>
  <th>Vencimiento</th>
  <th>Ingreso</th>
  <th>USO</th>
  <th>Sobrevida</th>
  </tr>
  </thead>
  <tbody>
  <?php while ($pop = $Rpop->fetch(PDO::FETCH_ASSOC)) { ?>
  <tr>
  <td>
    <?php
      print("<a href='n_control_edit.php?id=".$pop['id']."'>".$pop['cat']."</a>");
    ?>
  </td>
  <td><?php echo $pop['nom'];?> <a href="javascript:anular(<?php echo $pop['id'];?>);"><small>(borrar)</small></a></td>
  <td><?php echo $pop['pres'];?></td>
  <td><?php echo $pop['lote'];?></td>
  <td><?php echo date("d-m-Y", strtotime($pop['f_ven'])); ?></td>
  <td><?php echo date("d-m-Y", strtotime($pop['f_ing'])); ?></td>
  <td><?php echo date("d-m-Y", strtotime($pop['f_uso'])); ?></td>
  <td style="font-size:small">
  <?php if($pop['sob2']<>'') echo $pop['sob3']."% (".date("d-m-Y", strtotime($pop['sob1']))." ".$pop['sob2'].")" ;?></td>
  </tr>
  <?php } ?>
  </tbody>
  </table>
  <?php } else { echo '<p><h3>¡ No hay Insumos !</h3></p>'; } ?>
  </form>
   
  </div><!-- /content -->

  </div><!-- /page -->
  <script>
  $(function(){
  	$("#alerta").prependTo(".ui-content");
  	$('#alerta').delay(3000).fadeOut('slow');

  });//]]> 
  </script>
</body>
</html>