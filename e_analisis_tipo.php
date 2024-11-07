<!DOCTYPE HTML>
<html>
<head>
	<?php
   include 'seguridad_login.php'
    ?>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" href="_images/favicon.png" type="image/x-icon">
	<link rel="stylesheet" href="_themes/tema_inmater.min.css" />
	<link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css" />
	<link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css" />
	<script src="js/jquery-1.11.1.min.js"></script>
	<script src="js/jquery.mobile-1.4.5.min.js"></script>

	<style>
		.ui-dialog-contain {
			width: 100%;
			max-width: 800px;
			margin: 2% auto 15px;
			padding: 0;
			position: relative;
			top: -15px;
			
		}
		#alerta { background-color:#FF9;margin: 0 auto; text-align:center; padding:4px;}
	</style>

	<script language="JavaScript" type="text/javascript">
		function anular(x) {
			document.form2.borrar.value=x;
			document.form2.nom.value=""; //para que no inserte registros
			document.form2.submit();
		}
	</script>
</head>

<body>
	<div data-role="page" class="ui-responsive-panel" id="e_analisis_tip" data-dialog="true">
		<div data-role="header" data-position="fixed">
			<?php
			if (!!$_GET && isset($_GET["path"]) && !empty($_GET["path"])) {
				print('<a href="'.$_GET["path"].'.php" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>');
			} else {
				print('<a href="lista.php" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>');
			} ?>
			<h1>Tipos de <?php if ($login=='eco') echo 'Ecografía'; else if ($login=='legal') echo 'Documentos'; else echo 'Exámenes'; ?></h1>
		</div>

		<div class="ui-content" role="main">
			<?php
			if (isset($_POST['borrar']) and !empty($_POST['borrar'])) {
				$stmt = $db->prepare("DELETE FROM hc_analisis_tip WHERE id = ?;");
				$stmt->execute(array($_POST['borrar']));
			}

			if (isset($_POST['nom']) and !empty($_POST['nom'])) {
				global $db;
				$stmt = $db->prepare("INSERT INTO hc_analisis_tip (nom, lab) VALUES (?, ?);");
				$stmt->execute(array($_POST['nom'], $login));
				echo "<div id='alerta'> Examen Agregado! </div>";

				$path = 'lista';
				if (!!$_GET && isset($_GET["path"]) && !empty($_GET["path"])) {
					$path = $_GET["path"];
				}

				header("Location: " . $path . ".php");
			}

			$rMed = $db->prepare("SELECT * FROM hc_analisis_tip WHERE lab = ? ORDER by nom ASC;");
			$rMed->execute(array($login)); ?>

			<form action="" method="post" name="form2" data-ajax="false">
				<input type="hidden" name="borrar">

				<div class="ui-bar ui-bar-a">
					<table style="margin: 0 auto;" width="100%">
						<tr>
							<td width="16%">Nombre de <?php if ($login=='eco') echo 'Ecografía'; else if ($login=='legal') echo 'Documento'; else echo 'Exámen'; ?></td>
							<td width="71%"><input name="nom" type="text" id="nom" data-mini="true" required/></td>
							<td width="13%"><input name="guardar" type="Submit" id="guardar" value="AGREGAR"  data-icon="check" data-iconpos="left" data-inline="true" data-mini="true"/></td>
						</tr>
					</table>
				</div>

				<ul data-role="listview" data-inset="true" data-mini="true" >
					<?php
					while ($med = $rMed->fetch(PDO::FETCH_ASSOC)) { ?>
						<li style="margin: 0 auto;width:90%;">
							<h5><?php echo mb_strtoupper($med['nom']);?></h5>
							<span class="ui-li-count">
								<a href='javascript:anular(<?php echo $med['id'];?>);' data-theme="a">[Eliminar]</a>
							</span>
						</li>
					<?php }
					if ($rMed->rowCount()<1) {
						echo '<p><h3 class="text_buscar">¡ No hay registros !</h3></p>';
					} ?>
				</ul> 
			</form>
		</div>

		<script>
			$(function() {
				$('#alerta').delay(3000).fadeOut('slow');
			});
		</script>
	</div>
</body>
</html>