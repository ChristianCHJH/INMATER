<!DOCTYPE HTML>
<html>
<head>
<?php
   include 'seguridad_login.php'
    ?>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Editar Insumo</title>
	<link rel="stylesheet" href="_themes/tema_inmater.min.css" />
	<link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css" />
	<link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css" />
	<link rel="icon" href="_images/favicon.png" type="image/x-icon">
	<script src="js/jquery-1.11.1.min.js"></script>
	<script src="js/jquery.mobile-1.4.5.min.js"></script>
</head>
<body>
	<?php
		if(isset($_POST["f_uso"]) && !empty($_POST["f_uso"]) && isset($_GET["id"]) && !empty($_GET["id"])){
			update_control($_GET["id"], $_POST["f_uso"]);
		}
		//
		if (isset($_GET["id"]) && !empty($_GET["id"])) {
			// print("demo".$_GET["id"]);
			$stmt = $db->prepare("SELECT * from lab_control where id=?");
			$stmt->execute(array($_GET["id"]));
			$data = $stmt->fetch(PDO::FETCH_ASSOC);
			// var_dump($data);
		} else {
			print("No existe el Insumo.");
			exit;
		}
	?>
	<div data-role="page" class="ui-responsive-panel" id="perfil" data-dialog="true">
	<style>
		.ui-dialog-contain {
		  	max-width: 700px;
			margin: 2% auto 15px;
			padding: 0;
			position: relative;
			top: -15px;
		}
		.scroll_h { overflow-x: scroll; overflow-y: hidden; white-space:nowrap; } 
	</style>
	<script>
		$(document).ready(function () {
		    var unsaved = false;
		    $(":input").change(function () {
				unsaved = true;
		    });
			$(window).on('beforeunload', function(){
				if (unsaved) { 
				return 'UD. HA REALIZADO CAMBIOS';
				}
			});
			$(document).on("submit", "form", function(event){
				$(window).off('beforeunload');
			});
		});
	</script>
	<div data-role="header" data-position="fixed">
		<h1>Editar Insumo</h1>
	</div>
	<div class="ui-content" role="main">
		<form action="" method="post" data-ajax="false">
		    <table width="100%" align="center" style="margin: 0 auto;">
				<tr>
					<td width="20%">Producto</td>
					<td width="30%">
						<input name="cat" type="text" id="cat" value="<?php echo $data['cat']; ?>" data-mini="true" disabled required/>
					</td>
					<td width="20%">
						Referencia
					</td>
					<td width="30%">
						<input name="nom" type="text" id="nom" value="<?php echo $data['nom']; ?>" data-mini="true" disabled/>
					</td>
				</tr>
				<tr>
					<td>Presentación</td>
					<td>
						<input name="pres" type="text" id="pres" value="<?php echo $data['pres']; ?>" data-mini="true" disabled>
					</td>
					<td>Lote</td>
					<td>
						<input name="lote" type="text" id="lote" value="<?php echo $data['lote']; ?>" data-mini="true" disabled/>
					</td>
				</tr>
				<tr>
					<td>F. de Vencimiento</td>
					<td>
						<input name="f_ven" type="date" id="f_ven" value="<?php echo $data['f_ven']; ?>" data-mini="true" disabled required>
					</td>
					<td>F. de Ingreso</td>
					<td>
						<input name="f_ing" type="date" id="f_ing" value="<?php echo $data['f_ing']; ?>" data-mini="true" disabled required>
					</td>
				</tr>
				<tr>
					<td>F. de Uso</td>
					<td>
						<input name="f_uso" type="date" id="f_uso" value="<?php echo $data['f_uso']; ?>" data-mini="true">
					</td>
				</tr>
		    </table>
			<input type="Submit" value="Guardar" data-icon="check" data-iconpos="left" data-mini="true" data-theme="b" data-inline="true"/>
		</form>
	</div>
	</div>
</body>
</html>