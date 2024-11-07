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

	<script>
		$(document).ready(function () {
			function sort_li(a, b) {
				return ($(b).data('position')) < ($(a).data('position')) ? 1 : -1;    
			}
		});	
	</script>
</head>

<body>
	<div data-role="page" class="ui-responsive-panel" id="lista_cap" data-dialog="true">
		<?php
		$Rcap = $db->prepare("SELECT *
			from lab_andro_cap
			where iiu > 0
			order by fec desc
			limit 20 offset 0;");
		$Rcap->execute(); ?>

		<style>
			.ui-dialog-contain {
				max-width: 1500px;
				margin: 1% auto 1%;
				padding: 0;
				position: relative;
				top: -35px;
			}
			.color { color:#F4062B !important; }
			.mayuscula {
				text-transform: uppercase;
			}
		</style>

		<div data-role="header" data-position="fixed">
			<a href="lista_and.php" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>
			<h2>LISTA DE CAPACITACIONES IIU</h2>
		</div>

		<div class="ui-content" role="main">
			<form action="lista_cap.php" method="post" data-ajax="false">
				<?php if ($Rcap->rowCount()>0) { ?>
					<div id="capacitaciones-filtro">
						<input id="filtro" data-type="search" placeholder="Buscar por apellidos o nombres de la pareja" data-mini="true">
					</div>

					<table data-role="table" data-filter="true" data-input="#filtro" class="table-stripe ui-responsive lista_orden" style="font-size: small;">
						<thead>
							<tr>
								<th>PACIENTE</th>
								<th>PAREJA</th>
								<th>MÉDICO</th>
								<th>FECHA</th>
								<th>ESTADO</th>
							</tr>
						</thead>

						<tbody id="capacitaciones-detalle">
							<?php
							while($cap = $Rcap->fetch(PDO::FETCH_ASSOC)) {
								$rIIU = $db->prepare("SELECT dni, p_dni_het, med FROM hc_reprod WHERE hc_reprod.estado = true and id = ?;");
								$rIIU->execute(array($cap['iiu']));
								if ($rIIU->rowCount() == 0) {
									continue;
								}
								$iiu = $rIIU->fetch(PDO::FETCH_ASSOC);
								$het = $iiu['p_dni_het'];
								$dni = $iiu['dni'];

								$rMujer = $db->prepare("SELECT nom, ape, med FROM hc_paciente WHERE dni = ?;");
								$rMujer->execute(array($iiu['dni']));
								$mujer = $rMujer->fetch(PDO::FETCH_ASSOC);

								if (empty($cap['p_dni']) || $cap['p_dni'] == 1) {
									$rPare = $db->prepare("SELECT p_nom, p_ape, p_med FROM hc_pareja WHERE p_dni = ?;");
									$rPare->execute(array($iiu['p_dni_het']));
									$pare = $rPare->fetch(PDO::FETCH_ASSOC);
								} else {
									$rPare = $db->prepare("SELECT p_nom, p_ape, p_med FROM hc_pareja WHERE p_dni = ?;");
									$rPare->execute(array($cap['p_dni']));
									$pare = $rPare->fetch(PDO::FETCH_ASSOC);
								} ?>

								<tr>
									<td class="mayuscula">
										<?php echo mb_strtoupper($mujer['ape']) . ' ' . mb_strtoupper($mujer['nom']); ?>
									</td>
									<td class="mayuscula">
										<?php
										if ($pare) {
											echo mb_strtoupper($pare['p_ape']) . ' ' . mb_strtoupper($pare['p_nom']);
										} else {
											echo 'NO MARCADO';
										} ?>
									</td>
									<td><?php echo $iiu['med']; ?></td>
									<td><?php echo date("d-m-Y", strtotime($cap['fec'])); ?></td>
									<td>
										<?php
										$path_url = "";
										if (strpos($_SERVER["REQUEST_URI"], "?") !== false) {
											$path_url = substr($_SERVER["REQUEST_URI"], strpos($_SERVER["REQUEST_URI"], "?"), strlen($_SERVER["REQUEST_URI"]));
											$path_url = urlencode($path_url);
										} ?>
										<a href="le_andro_cap.php<?php echo "?path=lista_cap&path_url=" . $path_url . "&dni=&ip=" . $cap['p_dni'] . "&het=" . $het . "&id=" . $cap['id']; ?>" rel="external"><?php if ($cap['emb'] == 0) echo 'Nuevo'; else echo 'Editar'; ?></a>
										<?php if ($cap['emb'] > 0) { ?>
											/<a href="info.php?t=cap&a=<?php echo $cap['p_dni'] . "&b=" . $cap['id'] . "&c=" . $dni; ?>" target="new">Informe</a>/<a href="info_s.php?t=cap&a=<?php echo $cap['p_dni'] . "&b=" . $cap['id'] . "&c=" . $dni; ?>" target="new">Sobre</a>
										<?php } ?>
									</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				<?php } else { echo '<p><h3>¡ No hay Pacientes !</h3></p>'; } ?>
			</form>
		</div>
	</div>

	<script>
		$(document).ready(function () {
            $(document).on('input paste keydown keyup', '#capacitaciones-filtro .ui-input-search', function(e){
				var filtro = $('#capacitaciones-filtro .ui-input-search :input')[0].value;

				if (filtro.length > 3 && e.which == 13) {
					$("#capacitaciones-filtro .ui-input-search :input").prop("disabled", true);

                    $.post("le_tanque.php", {capacitacion_filtro: filtro}, function (data) {
                        $("#capacitaciones-detalle").html("");
                        $("#capacitaciones-detalle").append(data);
                        $('.ui-page').trigger('create');
                    })
                    .done(function() {
                        $("#capacitaciones-filtro .ui-input-search :input").prop("disabled", false);
                        $("#capacitaciones-filtro .ui-input-search :input").focus();
                    });
                }
            });
		});
	</script>
</body>
</html>