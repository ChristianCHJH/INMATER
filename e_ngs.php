<!DOCTYPE HTML>
<html>
<head>
<?php
   include 'seguridad_login.php'
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/global.css" />
    <link rel="stylesheet" href="_themes/tema_inmater.min.css" />
    <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css" />
    <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css" />
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
</head>

<body>
	<div data-role="page" class="ui-responsive-panel" id="e_ngs" data-dialog="true">
		<?php
		if (isset($_POST['pro']) and !empty($_POST['pro']) and isset($_POST['c']) and $_POST['c'] > 0) {
			echo "<div id='alerta'>Los datos se guardaron correctamente!</div>";
			$c = $_POST['c'];
			for ($i = 1; $i <= $c; $i++) {
				if (isset($_POST['item'.$i])) {
					global $db;
					$stmt = $db->prepare("UPDATE lab_aspira_dias
						SET analizar=?, ngs1=?, ngs2=?, ngs3=?, valores_mitoscore=?, prioridad_transferencia=?
						where pro=? and ovo=? and estado is true;");
					$stmt->execute([
						1,
						!empty($_POST['ngs1'.$i])? $_POST['ngs1'.$i]: 0,
						$_POST['ngs2'.$i],
						!empty($_POST['ngs3'.$i])? $_POST['ngs3'.$i]: 0,
						!empty($_POST['valores-mitoscore'][$i-1])? $_POST['valores-mitoscore'][$i-1]: 0,
						!empty($_POST['prioridad-transferencia'][$i-1])? $_POST['prioridad-transferencia'][$i-1]: 0,
						$_POST['pro'],
						$_POST['ovo'.$i]
					]);
				} else {
					global $db;
					$stmt = $db->prepare("UPDATE lab_aspira_dias
						SET analizar=?
						where pro=? and ovo=? and estado is true;");
					$stmt->execute([
						0,
						$_POST['pro'],
						$_POST['ovo'.$i]
					]);
				}
			}
			$foto = $_FILES['informe'];
			if ($foto['name'] <> "") {
				if (is_uploaded_file($foto['tmp_name'])) {
					$ruta = 'analisis/ngs_'.$_POST['pro'].'.pdf';
					move_uploaded_file($foto['tmp_name'], $ruta);
				}
			} ?>
		<?php } ?>
		<?php
		$id = $_GET['id'];
		$Rpop = $db->prepare("SELECT
			hc_paciente.nom,hc_paciente.ape,lab_aspira.f_fin
			FROM hc_paciente,lab_aspira
			WHERE hc_paciente.dni=lab_aspira.dni and lab_aspira.estado is true AND lab_aspira.pro=?");
		$Rpop->execute([$id]);
		$pop = $Rpop->fetch(PDO::FETCH_ASSOC); 

		$rNgs = $db->prepare("SELECT
			ovo, d5cel, d6cel, analizar, ngs1, ngs2, ngs3, valores_mitoscore, prioridad_transferencia
			FROM lab_aspira_dias
			WHERE pro=? and (((d5d_bio<>0) and d5f_cic='C') or ((d6d_bio<>0) and d6f_cic='C')) and estado is true
            order by ovo;");
		$rNgs->execute([$id]);

		if (file_exists('analisis/ngs_'.$id.'.pdf')) {
			$pdf="";
			$descarga = '<a href="archivos_hcpacientes.php?idArchivo=ngs_'.$id.'" target="new">Ver/Descargar</a>';
		} else {
			$pdf="required"; $descarga='';
		} ?>
		<style>
			.ui-dialog-contain {
				max-width: 800px;
				margin: 2% auto 15px;
				padding: 0;
				position: relative;
				top: -15px;
			}
		</style>
		<div data-role="header" data-position="fixed">
				<?php
				if (!!$_GET && isset($_GET["path"]) && !empty($_GET["path"])) {
						print('<a href="'.$_GET["path"].'.php" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>');
				} else {
						print('<a href="lista.php" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>');
				} ?>
				<h3>NGS: <small><?php echo mb_strtoupper($pop['ape']) . ' ' . mb_strtoupper($pop['nom']); ?></small></h3>
		</div>
		<div class="ui-content" role="main">
				<form action="" method="post" enctype="multipart/form-data" data-ajax="false" id="form1">
						<input type="hidden" name="pro" id="pro" value="<?php echo $id;?>">
						<?php echo '<h4>Protocolo: '.$id.' Fecha: '.date("d-m-Y", strtotime($pop['f_fin'])).'</h4>'; ?>
						<table width="100%" align="center" style="margin: 0 auto;">
								<tr bgcolor="#BEE5E3">
										<td>ID Embion</td>
										<td>Analizar</td>
										<td>Dia 5</td>
										<td>Dia 6</td>
										<td>Resultado</td>
										<td>Genero</td>
										<td>Valores Mitoscore</td>
										<td>Prioridad transferencia</td>
										<td>Detalles</td>
								</tr>
								<?php
								$c=0;
								while ($ngs = $rNgs->fetch(PDO::FETCH_ASSOC)) {
									$c++; ?>
									<tr>
										<td><?php echo $ngs['ovo']; ?><input type="hidden" name="ovo<?php echo $c; ?>" value="<?php echo $ngs['ovo'];?>"></td>
										<td class="text-center">
											<input
												type="checkbox"
												id="item<?php echo $c; ?>"
												name="item<?php echo $c; ?>"
												onclick="analizar('item<?php echo $c; ?>');"
												data-mini="true"
												<?php echo ($ngs['analizar'] ? 'checked' : '');?> >
										</td>
										<td><?php echo $ngs['d5cel']; ?></td>
										<td><?php echo $ngs['d6cel']; ?></td>
										<td>
											<select name="ngs1<?php echo $c; ?>" data-mini="true" <?php print("class='item" . $c . "'"); ?>>
												<option value="">Seleccionar</option>
												<option value=1 <?php if ($ngs['ngs1']==1) echo 'selected';?>>Normal</option>
												<option value=2 <?php if ($ngs['ngs1']==2) echo 'selected';?>>Anormal</option>
												<option value=3 <?php if ($ngs['ngs1']==3) echo 'selected';?>>NR</option>
												<option value=4 <?php if ($ngs['ngs1']==4) echo 'selected';?>>Mosaico</option>
											</select>
										</td>
										<td>
											<select name="ngs3<?php echo $c; ?>" data-mini="true" <?php print("class='item" . $c . "'"); ?>>
												<option value="">Seleccionar</option>
												<option value=3 <?php if ($ngs['ngs3']==3) echo 'selected';?>>-</option>
												<option value=1 <?php if ($ngs['ngs3']==1) echo 'selected';?>>Hombre</option>
												<option value=2 <?php if ($ngs['ngs3']==2) echo 'selected';?>>Mujer</option>
											</select>
										</td>
										<td>
											<input
												type="number"
												<?php print("class='item" . $c . "'"); ?>
												name="valores-mitoscore[]"
												value="<?php echo $ngs['valores_mitoscore'];?>"
												placeholder="#.##git"
												step="0.01"
												data-mini="true" >
										</td>
										<td>
											<input
												type="number"
												<?php print("class='item" . $c . "'"); ?>
												name="prioridad-transferencia[]"
												id="prioridad-transferencia"
												value="<?php echo $ngs['prioridad_transferencia'];?>"
												placeholder="#"
												step="1"
												data-mini="true" >
										</td>
										<td>
											<input
												type="text"
												<?php print("class='item" . $c . "'"); ?>
                                                maxlength="100"
												name="ngs2<?php echo $c; ?>"
												value="<?php echo $ngs['ngs2'];?>"
												data-mini="true">
										</td>
									</tr>
								<?php } ?>
								<tr>
										<td><strong>Informe</strong></td>
										<td colspan="4">
											<input
												name="informe"
												type="file" <?php echo $pdf; ?>
												id="informe"
												accept="application/pdf"
												data-mini="true" />
										</td>
										<td><?php echo $descarga; ?></td>
								</tr>
						</table>
						<input type="hidden" name="c" value="<?php echo $c;?>">
						<div class="enlinea">
							<input
								name="guardar"
								type="Submit"
								id="guardar"
								value="GUARDAR DATOS"
								data-icon="check"
								data-iconpos="left"
								data-inline="true"
								data-theme="b"
								data-mini="true"/>
						</div>
						<div data-role="popup" id="cargador" data-overlay-theme="b" data-dismissible="false"><p>GUARDANDO DATOS..</p></div>
				</form>
		</div>
	</div>
	<script>
		$(document).ready(function () {
			$('#form1').submit(function() {
				$("#cargador").popup("open",{positionTo:"window"});
				return true;
			});
		});
		function analizar(id) {
			if (document.getElementById(id).checked) {
      	console.log(id, "checked");
				var demo='.' + id;
				$(demo).prop('readonly', false);
				$(demo).attr('disabled', false);
				$(demo).prop('required', true);
  		} else {
				var demo='.' + id;
				$(demo).prop('readonly', true);
				$(demo).attr('disabled', true);
				$(demo).prop('required', false);
  		}
		}
	</script>
</body>
</html>