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

			$analisis = updatehisteroscopia($_POST['dni'],$_POST['nom'],$_POST['fnac'],$_POST['a_fecha'],$_POST['a_analisis_tipo'],$_POST['a_parrafo1'],$_FILES['imagen1parr1'],$_FILES['imagen2parr1'],$_FILES['imagen3parr1'],$_POST['a_parrafo2'],$_FILES['imagen1parr2'],$_FILES['imagen2parr2'],$_FILES['imagen3parr2'],$_POST['comentario'], $login,$_POST['idx'],$_POST['id']);


			header("Location: lista_histeroscopias.php");
		}
			
		$id = $_GET['id'];

		$Rpop = $db->prepare("SELECT analisis_histeroscopia.*,
            coalesce((SELECT man_archivo.nombre_base AS nombre_base FROM man_archivo WHERE man_archivo.id = analisis_histeroscopia.imagen1parr1), 'imagen1parr1') imagen1parr1,
            coalesce((SELECT man_archivo.nombre_base AS nombre_base FROM man_archivo WHERE man_archivo.id = analisis_histeroscopia.imagen2parr1), 'imagen2parr1') AS imagen2parr1,
            coalesce((SELECT man_archivo.nombre_base AS nombre_base FROM man_archivo WHERE man_archivo.id = analisis_histeroscopia.imagen3parr1), 'imagen3parr1') AS imagen3parr1,
            coalesce((SELECT man_archivo.nombre_base AS nombre_base FROM man_archivo WHERE man_archivo.id = analisis_histeroscopia.imagen1parr2), 'imagen1parr2') AS imagen1parr2,
            coalesce((SELECT man_archivo.nombre_base AS nombre_base FROM man_archivo WHERE man_archivo.id = analisis_histeroscopia.imagen2parr2), 'imagen2parr2') AS imagen2parr2,
            coalesce((SELECT man_archivo.nombre_base AS nombre_base FROM man_archivo WHERE man_archivo.id = analisis_histeroscopia.imagen3parr2), 'imagen3parr2') AS imagen3parr2
            FROM analisis_histeroscopia
			where analisis_histeroscopia.id = ?;"
		);
		$Rpop->execute(array($id));
		$pop = $Rpop->fetch(PDO::FETCH_ASSOC);

		// validar fragmentacion de adn
		$idf_mostrar = "style='display: none;'";

		if ($pop["a_exa"] == "Fragmentación de ADN espermático") {
			$idf_mostrar = "style='display: table-row;'";
		}

		$rMed = $db->prepare("SELECT id, descripcion FROM man_procedimientos_cli WHERE  estado = 1 ORDER by descripcion ASC");

		$rMed->execute();

		$ruta = 'analisis/'.$pop['id'].'_'.$pop['a_dni'].'.pdf';
//		if (file_exists($ruta)) { $pdf=""; } else { $pdf="required"; } ?>

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
                //
                var id=$('#id').val();
                if(id===""){
                    $('#a_parrafo2').val('Se realiza vagihisteroscopia observándose paredes vaginales de buen tono, cuello uterino de orientación posterior, orificio cervical externo de diametro transverso mayor con glándulas y criptas presentes, canal cervical permeable, cavidad uterina de aspecto y tamaño normal. ostims visibles permeables.');
                }
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
                $('#fnac').val($(this).attr("fnac"));


				if ($('#med').attr('type') == 'hidden') {
					$('#med').val($(this).attr("med"))
				} else {
					$('#med').val($(this).attr("med")).selectmenu("refresh", true);
				}
				$('#dni').val($(this).attr("dni"));
				$('#paci_nom').textinput('refresh');
				$('.fil_paci li').addClass('ui-screen-hidden');
				$('#paci_nom').focus();
                var nom=$('#nom').val();
                var fnac=$('#fnac').val();
                var dni=$('#dni').val();
                $('#a_parrafo1').val('Paciente ' + nom + ' de '+ fnac + ' años de edad, DNI o C. Ext o pasaporte, '+ dni +' quien acude para valoración de cavidad uterina por pólipos endometriales, miomas, extracción de DIU, Sinequia uterina, infertilidad, patología endometrial en estudio.');

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

					$('#dni').val('<?php echo $pop['dni']; ?>');
					$('#nom').val('<?php echo $pop['nombre']; ?>');
                    $('#fnac').val('<?php echo $pop['fnac']; ?>');
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
			<h1>Nuevo <?php if ($login=='hist') echo 'Histeroscopía'; else echo 'Exámen'; ?></h1>
		</div>

		<div class="ui-content" role="main">
			<form action="" method="post" enctype="multipart/form-data" data-ajax="false" id="form1">
				<input type="hidden" name="id" id="id" value="<?php echo $id;?>">
				<input type="hidden" name="dni" id="dni">
				<input type="hidden" name="nom" id="nom">
                <input type="hidden" name="fnac" id="fnac">
				<table width="100%" align="center" style="margin: 0 auto;">
					<tr>
						<td>Fecha* <?php if ($login<>'hist') echo 'de toma de muestra'; ?></td>
						<td width="1053"><input name="a_fecha" type="date" required id="a_fecha" value="<?php echo $pop['fecha'];?>" data-mini="true"></td>
						<td width="4">&nbsp;</td>
					</tr>

					<tr>
						<td>Tipo de Análisis*</td>
						<td colspan="2">
							<select name="a_analisis_tipo" id="a_analisis_tipo" required data-mini="true">
								<option value="">SELECCIONAR</option>
								<?php
								while($med = $rMed->fetch(PDO::FETCH_ASSOC)) { ?>
									<option value="<?php echo $med['id']; ?>" <?php if ($med['id']==$pop['tipo_analisis']) echo 'selected';?>><?php print(mb_strtoupper($med['descripcion'])); ?></option>
								<?php } ?>
							</select>
						</td>
					</tr>
                    <tr>
                        <td width="201">Paciente</td>
                        <td colspan="2" id="lista_pacientes">
                            <ul data-role="listview" data-theme="c" data-inset="true" data-filter="true" data-filter-reveal="true" data-filter-placeholder="Buscar paciente por Nombre o DNI..." data-mini="true" class="fil_paci"></ul>
                        </td>
                    </tr>
                    <tr>
                        <td>Párrafo 1</td>
                        <td colspan="2">
                            <textarea rows="5" name="a_parrafo1" id="a_parrafo1" data-mini="true"><?php echo $pop['a_parrafo1'];?></textarea>
                        </td>
                    </tr>



					<tr>
						<td>Imagen 1 párrafo 1</td>
						<td colspan="2">
							<input name="imagen1parr1" type="file" <?php echo $pdf; ?> id="imagen1parr1" accept="image/jpeg, image/jpg" data-mini="true"/>
							<?php
							if (file_exists('storage/analisis_archivo/histeroscopia/' . $pop['imagen1parr1'] )) {
								print('<em><a href="archivos_hcpacientes.php?idStorage=analisis_archivo/histeroscopia/' . $pop['imagen1parr1'] .'" target="new" style="margin: .446em; font-size: 12px;">Ver Imagen</a></em>');
							} ?>
						</td>
					</tr>

					<tr>
						<td>Imagen 2 párrafo 1</td>
                        <td colspan="2">
                            <input name="imagen2parr1" type="file" <?php echo $pdf; ?> id="imagen2parr1" accept="image/jpeg, image/jpg" data-mini="true"/>
                            <?php
                            if (file_exists('storage/analisis_archivo/histeroscopia/' . $pop['imagen2parr1'] )) {
                                print('<em><a href="archivos_hcpacientes.php?idStorage=analisis_archivo/histeroscopia/' . $pop['imagen2parr1'].'" target="new" style="margin: .446em; font-size: 12px;">Ver Imagen</a></em>');
                            } ?>
                        </td>
					</tr>



                    <tr>
                        <td>Imagen 3 párrafo 1</td>
                        <td colspan="2">
                            <input name="imagen3parr1" type="file" <?php echo $pdf; ?> id="imagen3parr1" accept="image/jpeg, image/jpg" data-mini="true"/>
                            <?php
                            if (file_exists('storage/analisis_archivo/histeroscopia/' . $pop['imagen3parr1'] )) {
                                print('<em><a href="archivos_hcpacientes.php?idStorage=analisis_archivo/histeroscopia/' . $pop['imagen3parr1'] . '" target="new" style="margin: .446em; font-size: 12px;">Ver Imagen</a></em>');
                            } ?>
                        </td>
                    </tr>


					<tr>
						<td>Párrafo 2</td>
						<td colspan="2">
							<textarea  name="a_parrafo2" id="a_parrafo2" data-mini="true"><?php echo $pop['a_parrafo2'];?></textarea>
						</td>
					</tr>

                    <tr>
                        <td>Imagen 1 párrafo 2</td>
                        <td colspan="2">
                            <input name="imagen1parr2" type="file" <?php echo $pdf; ?> id="imagen1parr2" accept="image/jpeg, image/jpg" data-mini="true"/>
                            <?php
                            if (file_exists('storage/analisis_archivo/histeroscopia/' . $pop['imagen1parr2'] )) {
                                print('<em><a href="archivos_hcpacientes.php?idStorage=analisis_archivo/histeroscopia/' . $pop['imagen1parr2'] . '" target="new" style="margin: .446em; font-size: 12px;">Ver Imagen</a></em>');
                            } ?>
                        </td>
                    </tr>


                    <tr>
                        <td>Imagen 2 párrafo 2</td>
                        <td colspan="2">
                            <input name="imagen2parr2" type="file" <?php echo $pdf; ?> id="imagen2parr2" accept="image/jpeg, image/jpg" data-mini="true"/>
                            <?php
                            if (file_exists('storage/analisis_archivo/histeroscopia/' . $pop['imagen2parr2'] )) {
                                print('<em><a href="archivos_hcpacientes.php?idStorage=analisis_archivo/histeroscopia/' . $pop['imagen2parr2'] . '" target="new" style="margin: .446em; font-size: 12px;">Ver Imagen</a></em>');
                            } ?>
                        </td>
                    </tr>

                    <tr>
                        <td>Imagen 3 párrafo 2</td>
                        <td colspan="2">
                            <input name="imagen3parr2" type="file" <?php echo $pdf; ?> id="imagen3parr2" accept="image/jpeg, image/jpg" data-mini="true"/>
                            <?php
                            if (file_exists('storage/analisis_archivo/histeroscopia/' . $pop['imagen3parr2'] )) {
                                print('<em><a href="archivos_hcpacientes.php?idStorage=analisis_archivo/histeroscopia/' . $pop['imagen3parr2'] .'" target="new" style="margin: .446em; font-size: 12px;">Ver Imagen</a></em>');
                            } ?>
                        </td>
                    </tr>

                    <tr>
                        <td>IDX</td>
                        <td><input name="idx" type="text" id="idx" value="<?php echo $pop['idx']; ?>" data-mini="true"></td>
                    </tr>
                    <tr>
                        <td>Comentario</td>
                        <td><input name="comentario" type="text" id="comentario" value="<?php echo $pop['comentario']; ?>" data-mini="true"></td>
                    </tr>
				</table>

				<div class="enlinea">
					<input name="guardar" type="Submit" id="guardar" value="GUARDAR DATOS" data-icon="check" data-iconpos="left" data-inline="true" data-theme="b" data-mini="true"/>
					<?php if ($_SESSION['role'] == '4') { echo '<input type="hidden" name="cor" id="cor">'; } else { ?>
					<input type="checkbox" name="cor" id="cor" data-mini="true" value=1 <?php if ($pop['cor']==1) echo "checked"; ?>><label for="cor">Exámen de Cortesía?</label>
					<?php } ?>
				</div>

				<div data-role="popup" id="cargador" data-overlay-theme="b" data-dismissible="false"><p>GUARDANDO DATOS..</p></div>
			</form>
		</div>
	</div>
	<script src="js/e_histeroscopia.js?v=191219" crossorigin="anonymous"></script>
</body>
</html>