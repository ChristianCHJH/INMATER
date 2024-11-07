<!DOCTYPE html>
<html lang="es">
<head>
<?php
   include 'seguridad_login.php'
    ?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Clínica Inmater | Lista Genomics</title>
	<link rel="icon" href="_images/favicon.png" type="image/x-icon">
	<link rel="stylesheet" href="_themes/tema_inmater.min.css" />
	<link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css" />
	<link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css" />
	<link rel="stylesheet" href="css/global.css" />
	<script src="js/jquery-1.11.1.min.js"></script>
	<script src="js/jquery.mobile-1.4.5.min.js"></script>
</head>
<body>
	<?php
	$stmt = $db->prepare("SELECT role, sede_id FROM usuario WHERE userx=?");
	$stmt->execute(array($login));
	$data_user = $stmt->fetch(PDO::FETCH_ASSOC); ?>

	<div data-role="page" class="ui-responsive-panel" id="lista">

		<div data-role="header" data-position="fixed">
			<?php print("<h1>Análisis Clínico (" . $login . ")</h1>"); ?>
			<a href="salir.php" class="ui-btn-right ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-right ui-icon-power" rel="external">Salir</a>
		</div>

		<div data-role="content">
			<form action="" method="post" data-ajax="false" name="form1" id="form1">
				<input name="anu_ngs" type="hidden">
				<input name="dni_ngs" type="hidden">

				<?php
				if (isset($_POST['anu_ngs']) and !empty($_POST['anu_ngs']) and isset($_POST['dni_ngs']) and !empty($_POST['dni_ngs'])) {
					$stmt = $db->prepare("DELETE FROM hc_analisis WHERE id=?;");
					$stmt->execute(array($_POST['anu_ngs']));

					unlink("analisis/".$_POST['anu_ngs']."_".$_POST['dni_ngs'].".pdf");
				} ?>

				<div data-role="tabs" id="tabs">
					<div data-role="navbar">
						<ul>
							<li><a href="#one" data-ajax="false" class="ui-btn-active">OTROS</a></li>
							<li><a href="#two" data-ajax="false">NGS</a></li>
							<li><a href="#three" data-ajax="false">ERA</a></li>
						</ul>
					</div>
					<div id="one">
						<a href="e_analisis.php?path=lista_genomics&id=" class="ui-btn ui-mini ui-btn-inline" data-theme="a" rel="external">Nuevo Examen</a>
						<input id="filtro" data-type="search" placeholder="Escriba los nombres o apellidos del paciente...">
						<table data-role="table" data-filter="true" data-input="#filtro" class="table-stripe ui-responsive">
							<thead>
								<tr>
									<th>Examen</th>
									<th>Apellidos y Nombres</th>
									<th>Médico</th>
									<th>Resultado</th>
									<th>Informe</th>
									<th>Fecha</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$stmt = $db->prepare("SELECT * FROM hc_analisis WHERE lab=? AND a_exa <> ? ORDER BY a_mue DESC LIMIT 10;");
								$stmt->execute([$login, "ERA"]);

								while ($anal = $stmt->fetch(PDO::FETCH_ASSOC)) {
									$borrar_ngs = "";

									if ($anal['a_exa'] == 'NGS') {
										$borrar_ngs = " - <a href='javascript:borrarNGS(".$anal['id'].", ".$anal['a_dni'].");'>Eliminar</a>";
									}

									print('<tr>
										<th><a href="e_analisis.php?path=lista_genomics&id='.$anal['id'].'" rel="external">'.mb_strtoupper($anal['a_exa']).'</a></th>
										<td>'.mb_strtoupper($anal['a_nom']).'</td>
										<td>'.mb_strtoupper($anal['a_med']).'</td>
										<td>'.mb_strtoupper($anal['a_sta']).'</td>
										<th><a href="archivos_hcpacientes.php?idArchivo=' . $anal['id'] . '_' . $anal['a_dni'] . '" target="new">Ver/Descargar</a>'.$borrar_ngs.'</th>
										<td>'.date("d-m-Y", strtotime($anal['a_mue'])).'</td></tr>');
								} ?>
							</tbody>
						</table>
					</div>
					<div id="two">
						<?php
						$rNgs = $db->prepare("SELECT
							hc_paciente.dni, ape, nom, hc_reprod.med, lab_aspira.pro, lab_aspira.f_fin
							FROM hc_paciente,lab_aspira, hc_reprod
							WHERE hc_paciente.dni = lab_aspira.dni and lab_aspira.estado is true AND hc_reprod.id = lab_aspira.rep AND lab_aspira.f_fin <> '1899-12-30' AND lab_aspira.tip <> 'T' AND hc_reprod.pago_extras ILIKE '%NGS%' AND lab_aspira.dias >= 5
							ORDER BY pro DESC
							LIMIT 20;");
						$rNgs->execute();

						if ($rNgs->rowCount() > 0) { ?>
							<input id="filtrongs" data-type="search" placeholder="Escriba los nombres o apellidos del paciente...">
							<table data-role="table" data-filter="true" data-input="#filtrongs" class="table-stripe ui-responsive">
								<thead>
									<tr>
										<th>ID Protocolo</th>
										<th>Fecha Protocolo</th>
										<th>Apellidos y Nombres</th>
										<th>Médico</th>
										<th>Informe</th>
										<th>Resultado</th>
									</tr>
								</thead>
								<tbody>
									<?php
									while ($ngs = $rNgs->fetch(PDO::FETCH_ASSOC)) {
										$stmt = $db->prepare("SELECT ngs1
											FROM lab_aspira_dias
											WHERE pro=? and estado is true AND (((d5d_bio<>0) AND d5f_cic='C') OR ((d6d_bio<>0) AND d6f_cic='C'))");
										$stmt->execute(array($ngs['pro']));

										if ($stmt->rowCount() > 0) {
											if (file_exists("analisis/ngs_".$ngs['pro'].".pdf")) {
												$res = 'NEGATIVO';

												while ($ovo = $stmt->fetch(PDO::FETCH_ASSOC)) {
													if ($ovo['ngs1'] == 1) {
														$res = 'POSITIVO';
														break;
													}
												}

												$pdf = '<a href="archivos_hcpacientes.php?idArchivo=ngs_'.$ngs['pro'].'" target="new">Ver/Descargar</a>';
											} else {
												$res = '-';
												$pdf = 'PENDIENTE';
											}

											print("<tr>
												<td>".$ngs['pro']."</td>
												<td><a href='e_ngs.php?path=lista_genomics&id=".$ngs['pro']."' rel='external'>".date("d-m-Y", strtotime($ngs['f_fin']))."</a></td>
												<td>".mb_strtoupper($ngs['ape'].' '.$ngs['nom'])."</td>
												<td>".mb_strtoupper($ngs['med'])."</td>
												<th>".$pdf."</th>
												<th>".$res."</th>
											</tr>");
										}
									} ?>
								</tbody>
							</table>
						<?php } else {
								print('<p><h3>¡ No hay Registros !</h3></p>');
						} ?>
					</div>
					<div id="three">
						<a href="e_analisis.php?path=lista_genomics&id=" class="ui-btn ui-mini ui-btn-inline" data-theme="a" rel="external">Nuevo Examen</a>
						<input id="filtro_era" data-type="search" placeholder="Escriba los nombres o apellidos del paciente...">
						<table data-role="table" data-filter="true" data-input="#filtro_era" class="table-stripe ui-responsive">
							<thead>
								<tr>
									<th>Examen</th>
									<th>Apellidos y Nombres</th>
									<th>Médico</th>
									<th>Resultado</th>
									<th>Informe</th>
									<th>Fecha</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$stmt = $db->prepare("SELECT * FROM hc_analisis WHERE lab=? AND a_exa = ? ORDER BY a_mue DESC LIMIT 10;");
								$stmt->execute([$login, "ERA"]);

								while ($anal = $stmt->fetch(PDO::FETCH_ASSOC)) {
									print('<tr>
										<th><a href="e_analisis.php?path=lista_genomics&id='.$anal['id'].'" rel="external">'.mb_strtoupper($anal['a_exa']).'</a></th>
										<td>'.mb_strtoupper($anal['a_nom']).'</td>
										<td>'.mb_strtoupper($anal['a_med']).'</td>
										<td>'.mb_strtoupper($anal['a_sta']).'</td>
										<th><a href="archivos_hcpacientes.php?idArchivo=' . $anal['id'] . '_' . $anal['a_dni'] . '" target="new">Ver/Descargar</a></th>
										<td>'.date("d-m-Y", strtotime($anal['a_mue'])).'</td></tr>');
								} ?>
							</tbody>
						</table>
					</div>
				</div>
			</form>
		</div>

		<div data-role="footer">
			<h4>Clínica Inmater</h4>
		</div>

	</div>
	<script>
		$(document).ready(function () {
			$('#one > .ui-input-search').keydown(function(e) {
				if(e.keyCode == 13) {
					var buscar = $('#one > .ui-input-search > :input')[0].value;

					if (buscar.length > 3) {
						$("#one > .ui-input-search > :input").prop("disabled", true);
						$("#one > table > tbody").html("");

						$.post("_operaciones/lista_genomics.php", {tipo_operacion: "visualizar_analisisgenomics", buscar: buscar}, function (data) {
								console.log(data);
								$("#one > table > tbody").append(data);
								$("#one > table > thead").removeClass("ui-screen-hidden");
								$("#one > table > tbody").removeClass("ui-screen-hidden");
								$('#filtro').val("");
						}).done(function() {
								$("#one > .ui-input-search > :input").prop("disabled", false);
								$("#one > .ui-input-search > :input").focus();
								$('.ui-page').trigger('create');
						});
					}
				}
			});

			$('#two > .ui-input-search').keydown(function(e) {
				if(e.keyCode == 13) {
					var buscar = $('#two > .ui-input-search > :input')[0].value;

					if (buscar.length > 3) {
						$("#two > .ui-input-search > :input").prop("disabled", true);
						$("#two > table > tbody").html("");

						$.post("_operaciones/lista_genomics.php", {tipo_operacion: "visualizar_ngsgenomics", buscar: buscar}, function (data) {
								console.log(data);
								$("#two > table > tbody").append(data);
								$("#two > table > thead").removeClass("ui-screen-hidden");
								$("#two > table > tbody").removeClass("ui-screen-hidden");
								$('#filtrongs').val("");
						}).done(function() {
								$("#two > .ui-input-search > :input").prop("disabled", false);
								$("#two > .ui-input-search > :input").focus();
								$('.ui-page').trigger('create');
						});
					}
				}
			});

			$('#three > .ui-input-search').keydown(function(e) {
				if(e.keyCode == 13) {
					var buscar = $('#three > .ui-input-search > :input')[0].value;

					if (buscar.length > 3) {
						$("#three > .ui-input-search > :input").prop("disabled", true);
						$("#three > table > tbody").html("");

						$.post("_operaciones/lista_genomics.php", {tipo_operacion: "visualizar_analisisera", buscar: buscar}, function (data) {
								console.log(data);
								$("#three > table > tbody").append(data);
								$("#three > table > thead").removeClass("ui-screen-hidden");
								$("#three > table > tbody").removeClass("ui-screen-hidden");
								$('#filtro_era').val("");
						}).done(function() {
								$("#three > .ui-input-search > :input").prop("disabled", false);
								$("#three > .ui-input-search > :input").focus();
								$('.ui-page').trigger('create');
						});
					}
				}
			});

		});

		function borrarNGS(x, y) {
			if (confirm("CONFIRMA ELIMINAR?")) {
				document.form1.anu_ngs.value = x;
				document.form1.dni_ngs.value = y;
				document.form1.submit();
				return true;
			} else return false;
		}
	</script>	
</body>
</html>