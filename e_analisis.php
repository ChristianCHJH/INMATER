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
</head>
<body>
	<div data-role="page" class="ui-responsive-panel" id="e_analisis" data-dialog="true">
		<?php
		if (!!$_POST && isset($_POST['dni']) && !empty($_POST['dni'])) {
			$path = 'lista';
			if (!!$_GET && isset($_GET["path"]) && !empty($_GET["path"])) {
				$path = $_GET["path"];
			}
			
			$analisis = updateAnalisis($_POST['idx'],$_POST['dni'],$_POST['a_mue'],$_POST['nom'],$_POST['med'],$_POST['a_exa'],$_POST['a_sta'],$_POST['a_obs'],$_POST['cor'],$login,$_FILES['informe'],$_FILES['video_informe'],$_POST['idf'], $path);

			require $_SERVER["DOCUMENT_ROOT"] . '/config/environment.php';
			
			if (!empty($analisis["archivo_path"])) {
				upload_video(
					$_FILES['video_informe'], // archivo
					2, // tipo_procedimiento
					$analisis["procedimiento"], // procedimiento
					$analisis["archivo_path"], // archivo_path
					$_ENV["googlecalendar_accountname_ecografia"], // accountname
					$_ENV["googlecalendar_keyfilelocation_ecografia"], // keyfilelocation
					$_ENV["googlecalendar_applicationname_ecografia"], // applicationname
					$_ENV["google_drive_ecografia_path"], // google_drive_path
					$_ENV["google_drive_ecografia_description"], // google_drive_description
					$login // login
				);
			}

			header("Location: " . $path . ".php");
		}
			
		$id = $_GET['id']?:0;
		if(isset($_GET['id'])){
		$Rpop = $db->prepare("SELECT a.*, coalesce(ma.nombre_base, '-') nombre_base, coalesce(ma.nombre_original, '-') nombre_original
			FROM hc_analisis a
			left join man_archivo ma on ma.id = a.archivo_id
			where a.estado = 1 and a.id = ?;"
		);
		$Rpop->execute(array($id));
		$pop = $Rpop->fetch(PDO::FETCH_ASSOC);
	}
		// validar fragmentacion de adn
		$idf_mostrar = "style='display: none;'";

		if (isset($pop["a_exa"]) && $pop["a_exa"] == "Fragmentación de ADN espermático") {
			$idf_mostrar = "style='display: table-row;'";
		}

		$rMed = $db->prepare("SELECT id, nom FROM hc_analisis_tip WHERE lab=? and estado = 1 ORDER by nom ASC");
		$rMed->execute(array($login));
		
		if(isset($pop['id']))$ruta = 'analisis/'.$pop['id'].'_'.$pop['a_dni'].'.pdf' ? :"NO Existe";
		if (isset($pop['id']) && file_exists($ruta)) { $pdf=""; } else { $pdf="required"; } ?>

		<style>
			.ui-dialog-contain {
				max-width: 1000px;
				margin: 2% auto 15px;
				padding: 0;
				position: relative;
				top: -15px;
			}
			.scroll_h { overflow-x: scroll; overflow-y: hidden; white-space:nowrap; } 
			.paci_insert {
				text-transform: uppercase; font-size:small;
			}
			.enlinea .ui-checkbox {
				display : inline-block;
				float: right;
			}
		</style>

		<script>
			$(document).ready(function () {
				$('#form1').submit(function() {
					$("#cargador").popup("open", {positionTo: "window"});
					return true;
				});	

				$(".ui-input-search input").attr("id", "paci_nom");
				$('#paci_nom').prop('required', 'true');
			});

			$(document).on('click', '.paci_insert', function(e){
				$('#paci_nom').val($(this).attr("nom"));
				$('#nom').val($(this).attr("nom"));
				if ($('#med').attr('type') == 'hidden') {
					$('#med').val($(this).attr("med"))
				} else {
					$('#med').val($(this).attr("med")).selectmenu("refresh", true);
				}
				$('#dni').val($(this).attr("dni"));
				$('#paci_nom').textinput('refresh');
				$('.fil_paci li').addClass('ui-screen-hidden');
				$('#paci_nom').focus();
			});

			$(document).on('input paste', '#lista_pacientes .ui-input-search', function(e){
				var paciente = $('#lista_pacientes .ui-input-search :input')[0].value;

				if (paciente.length > 3) {
					$.post("le_tanque.php", {carga_paci_det: paciente}, function (data) {
						$("#lista_pacientes ul").html("");
						$("#lista_pacientes ul").append(data);
						$('.ui-page').trigger('create');
					});
				}
			});
		</script>

		<script>
			$(document).ready(function () {
				<?php
				if (!empty($pop['id'])) { ?>
					$('#paci_nom').val('<?php echo $pop['a_nom']; ?>');
					$('#dni').val('<?php echo $pop['a_dni']; ?>');
					$('#nom').val('<?php echo $pop['a_nom']; ?>');
					if ($('#med').attr('type') == 'hidden') {
						$('#med').val('<?php echo $pop['a_med']; ?>');
					} else {
						$('#med').val('<?php echo $pop['a_med']; ?>').selectmenu("refresh", true);
					}
				<?php } ?>
			});
		</script>

		<div data-role="header" data-theme="b" data-position="fixed">
			<?php
			if (!!$_GET && isset($_GET["path"]) && !empty($_GET["path"])) {
				print('<a href="'.$_GET["path"].'.php" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>');
			} else {
				print('<a href="lista.php" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>');
			} ?>
			<h1>Nuevo <?php if ($login=='eco') echo 'Ecografía'; else echo 'Exámen'; ?></h1>
		</div>

		<div class="ui-content" role="main">
			<form action="" method="post" enctype="multipart/form-data" data-ajax="false" id="form1">
				<input type="hidden" name="idx" id="idx" value="<?php echo $id;?>">
				<input type="hidden" name="dni" id="dni">
				<input type="hidden" name="nom" id="nom">

				<table width="100%" align="center" style="margin: 0 auto;">
					<tr>
						<td>Fecha* <?php if ($login<>'eco') echo 'de toma de muestra'; ?></td>
						<td width="1053"><input name="a_mue" type="date" required id="a_mue" value="<?php echo $pop['a_mue'];?>" data-mini="true"></td>
						<td width="4">&nbsp;</td>
					</tr>

					<tr>
						<td>Tipo de <?php if ($login=='eco') {echo 'Ecografía';} else {echo 'Exámen';} ?>*</td>
						<td colspan="2">
							<select name="a_exa" id="a_exa" required data-mini="true">
								<option value="">SELECCIONAR</option>
								<?php
								while($med = $rMed->fetch(PDO::FETCH_ASSOC)) { ?>
									<option value="<?php echo $med['nom']; ?>" <?php if (isset($pop['a_exa']) && $med['nom']==$pop['a_exa']) echo 'selected';?>><?php print(mb_strtoupper($med['nom'])); ?></option>
								<?php } ?>
							</select>
						</td>
					</tr>

					<?php
					if ($login=='eco') {
						echo '<input type="hidden" name="a_sta" id="a_sta">';
					} else { ?>
						<tr>
							<td>Resultado</td>
							<td colspan="2">
								<select name="a_sta" id="a_sta" required data-mini="true">
									<option value="">SELECCIONAR</option>
									<option value="Positivo" <?php if (isset($pop['a_sta']) && $pop['a_sta']=='Positivo') print('selected');?>>POSITIVO</option>
									<option value="Negativo" <?php if (isset($pop['a_sta']) && $pop['a_sta']=='Negativo') print('selected');?>>NEGATIVO</option>
									<option value="No Resultado" <?php if ($pop['a_sta']=='No Resultado') print('selected');?>>NO RESULTADO</option>
									<option value="Pre-Receptivo" <?php if ($pop['a_sta']=='Pre-Receptivo') print('selected');?>>PRE RECEPTIVO</option>
									<option value="Receptivo" <?php if ($pop['a_sta']=='Receptivo') print('selected');?>>RECEPTIVO</option>
									<option value="Post-Receptivo" <?php if ($pop['a_sta']=='Post-Receptivo') print('selected');?>>POST RECEPTIVO</option>
								</select>
							</td>
						</tr>
					<?php } ?>

					<tr>
						<td>Informe*</td>
						<td colspan="2">
							<input name="informe" type="file" <?php echo $pdf; ?> id="informe" accept="application/pdf" data-mini="true"/>
							<?php
							if (isset($pop['id']) && file_exists('analisis/' . $pop['id'] . '_' . $pop['a_dni'] . '.pdf')) {
								print('<em><a href="archivos_hcpacientes.php?idArchivo=' . $pop['id'] . '_' . $pop['a_dni']. '" target="new" style="margin: .446em; font-size: 12px;">Ver Informe</a></em>');
							} ?>
						</td>
					</tr>

					<tr>
						<td>Vídeo</td>
						<td colspan="2">
							<input name="video_informe" type="file" id="video_informe" accept="video/*" data-mini="true"/>
							<?php
                            $link_video = '';
							if(isset($pop['id'])){
                            $stmt = $db->prepare("SELECT * from google_drive_response where drive_id <> '0' and estado = 1 and tipo_procedimiento_id = 2 and procedimiento_id = ? order by id desc limit 1 offset 0;");
                            $stmt->execute([$pop['id']]);
                            if ($stmt->rowCount() > 0) {
                                $data = $stmt->fetch(PDO::FETCH_ASSOC);
								$link_video = "<em><a href='https://drive.google.com/open?id=" . $data['drive_id'] . "' style='margin: .446em; font-size: 12px;' target='new'>Ver Vídeo</a></em>";
								print($link_video);
                            } else{
								$stmt = $db->prepare("SELECT * from man_archivo where id=?");
								$stmt->execute([$pop['archivo_id']]);
								if ($stmt->rowCount() > 0) {
								    $archivo = $stmt->fetch(PDO::FETCH_ASSOC);
								    $link_video = "<em><a href='/storage/analisis_archivo/" . $archivo['nombre_base'] . "' style='margin: .446em; font-size: 12px;' target='new'>Ver Vídeo</a></em>";
								    print($link_video);
								}
							}
						}?>
						</td>
					</tr>

					<tr class="idf_resultado" <?php print($idf_mostrar); ?>>
						<td>Índice de Fragmentación</td>
						<td colspan="2">
							<input type="number" name="idf" id="idf" value="<?php echo $pop['idf'];?>" placeholder='0.00' step='0.01' data-mini="true">
						</td>
					</tr>

					<tr>
						<td width="201">Paciente</td>
						<td colspan="2" id="lista_pacientes">
							<ul data-role="listview" data-theme="c" data-inset="true" data-filter="true" data-filter-reveal="true" data-filter-placeholder="Buscar paciente por Nombre o DNI..." data-mini="true" class="fil_paci"></ul>
						</td>
					</tr>

					<?php
					if ($login == "eco") {

						print('<tr><td width="201">Médico</td>
							<td><select name="med" id="med" data-mini="true">');

						$stmt = $db->prepare("SELECT userx codigo, upper(nom) nombre from usuario where role = 1 order by nom;");
						$stmt->execute();
						while ($info = $stmt->fetch(PDO::FETCH_ASSOC)) {
							print("<option value=" . $info['codigo'] . ">" . mb_strtoupper($info['nombre']) . "</option>");
						}

						print('</select></td></tr>');
					} else {
						print('<input type="hidden" name="med" id="med">');
					} ?>

					<tr>
						<td>Observación</td>
						<td colspan="2">
							<textarea name="a_obs" id="a_obs" data-mini="true"><?php if(isset($pop['a_obs']))echo $pop['a_obs'];?></textarea>
						</td>
					</tr>
				</table>

				<div class="enlinea">
					<input name="guardar" type="Submit" id="guardar" value="GUARDAR DATOS" data-icon="check" data-iconpos="left" data-inline="true" data-theme="b" data-mini="true"/>
					<?php if ($login=='eco') { echo '<input type="hidden" name="cor" id="cor">'; } else { ?>
					<input type="checkbox" name="cor" id="cor" data-mini="true" value=1 <?php if (isset($pop['cor']) && $pop['cor']==1) echo "checked"; ?>><label for="cor">Exámen de Cortesía?</label>
					<?php } ?>
				</div>

				<div data-role="popup" id="cargador" data-overlay-theme="b" data-dismissible="false"><p>GUARDANDO DATOS..</p></div>
			</form>
		</div>
	</div>
	<script src="js/e_analisis.js?v=191219" crossorigin="anonymous"></script>
</body>
</html>