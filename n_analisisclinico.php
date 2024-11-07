<!DOCTYPE html>
<html>
<head>
<?php
   include 'seguridad_login.php'
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/global.css">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
</head>
<body>
	<?php
		$id = "";
		if (isset($_GET['dni']) && !empty($_GET['dni'])) {
			$id = $_GET['dni'];
		} else {
			print("No seleccionó a ningún paciente");
			exit();
		}
		$rPaci = $db->prepare("SELECT * FROM hc_antece,hc_paciente WHERE hc_paciente.dni=? AND hc_antece.dni=?");
		$rPaci->execute(array($id, $id));
		$paci = $rPaci->fetch(PDO::FETCH_ASSOC);
		//
		$rUser = $db->prepare("select role from usuario where estado = 1 and userx=?");
		$rUser->execute(array($login));
		$user = $rUser->fetch(PDO::FETCH_ASSOC);
	?>
	<?php require ('_includes/menu_salaprocedimientos.php'); ?>
	<div class="container">
		<div data-role="collapsible" id="Perfi">
		<?php
			print("<h4 class='color-primary'>Resultados de Análisis Clínico: <small>".mb_strtoupper($paci['ape'])." ".mb_strtoupper($paci['nom'])."</small></h4>");
			if ( false ) {
		?>
			<!-- HSG - HES -->
			<div class="card mb-3" id="imprime">
				<h5 class="card-header" data-toggle="collapse" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">HSG - HES</h5>
				<div class="card-body collapse show" id="collapseExample">
					<a href="e_ante_hsghes.php?dni=<?php echo $paci['dni'] . "&id="; ?>" class="btn btn-danger">
						<!-- <img src="_libraries/open-iconic/svg/plus.svg" height="18" width="18" alt="icon name"> -->
						Agregar
					</a><br><br>
					<table class="table table-responsive table-bordered align-middle">
						<thead class="thead-dark">
							<tr>
								<th width="25%" align="center">Fecha</th>
								<th width="5%" align="center">Tipo</th>
								<th width="70%" align="center">Conclusión</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$a_hsghes = $db->prepare("select * from hc_antece_hsghes where dni=? and estado = true order by fec desc");
								$a_hsghes->execute(array($id));
								while ($hsghes = $a_hsghes->fetch(PDO::FETCH_ASSOC)) {
							?>
							<tr>
								<td><?php if ($hsghes['lab'] <> "") echo date("d-m-Y", strtotime($hsghes['fec'])) . ' (' . $hsghes['lab'] . ')'; else { ?>
									<a href="e_ante_hsghes.php?dni=<?php echo $paci['dni'] . "&id=" . $hsghes['fec']; ?>" rel="external"><?php echo date("d-m-Y", strtotime($hsghes['fec'])); ?></a><?php } ?>
									<?php if (file_exists("analisis/hsghes_" . $paci['dni'] . "_" . $hsghes['fec'] . ".pdf")) echo "<br><a href='archivos_hcpacientes.php?idArchivo=hsghes_" . $paci['dni'] . "_" . $hsghes['fec'] . "' target='new'>Descargar</a>"; ?>
								</td>
								<td><?php echo $hsghes['tip']; ?></td>
								<td><?php if ($hsghes['con'] == 'P') echo 'En proceso..'; else echo $hsghes['con']; ?></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
					<?php if ($a_hsghes->rowCount() < 1) echo '<h5>¡Aún no hay exámenes cargados!</h5>'; ?>
				</div>
			</div>
			<!-- Perfil Hormonal -->
			<div class="card mb-3" id="imprime">
				<h5 class="card-header" data-toggle="collapse" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">Perfil Hormonal</h5>
				<div class="card-body collapse show" id="collapseExample">
					<a href="e_ante_perfi.php?dni=<?php echo $paci['dni'] . "&id="; ?>" rel="external" class="btn btn-danger">Agregar</a><br><br>
					<table class="table table-responsive table-bordered align-middle">
						<thead class="thead-dark">
							<tr>
								<th width="5%">Fecha</th>
								<th width="10%">FSH</th>
								<th width="10%">LH</th>
								<th width="16%">Estradiol</th>
								<th width="3%">Prolactina</th>
								<th width="7%">Insulina</th>
								<th width="8%">T3</th>
								<th width="8%">T4</th>
								<th width="11%">TSH</th>
								<th width="14%">AMH</th>
								<th width="8%">Inhibina</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$a_perfi = $db->prepare("SELECT * FROM hc_antece_perfi WHERE dni=? ORDER BY fec DESC");
								$a_perfi->execute(array($id));
								while ($perfi = $a_perfi->fetch(PDO::FETCH_ASSOC)) {
							?>
							<tr>
								<td><?php if ($perfi['lab'] <> "") echo date("d-m-Y", strtotime($perfi['fec'])) . ' (' . $perfi['lab'] . ')'; else { ?>
									<a href="e_ante_perfi.php?dni=<?php echo $paci['dni'] . "&id=" . $perfi['fec']; ?>" rel="external"><?php echo date("d-m-Y", strtotime($perfi['fec'])); ?></a><?php } ?><?php if (file_exists("analisis/perfil_" . $paci['dni'] . "_" . $perfi['fec'] . ".pdf")) echo "<br><a href='archivos_hcpacientes.php?idArchivo=perfil_" . $paci['dni'] . "_" . $perfi['fec'] . "' target='new'>Descargar</a>"; ?>
								</td>
								<td><?php if ($perfi['fsh'] == 'P') echo 'En proceso..'; else echo $perfi['fsh']; ?></td>
								<td><?php if ($perfi['lh'] == 'P') echo 'En proceso..'; else echo $perfi['lh']; ?></td>
								<td><?php if ($perfi['est'] == 'P') echo 'En proceso..'; else echo $perfi['est']; ?></td>
								<td><?php if ($perfi['prol'] == 'P') echo 'En proceso..'; else echo $perfi['prol']; ?></td>
								<td><?php if ($perfi['ins'] == 'P') echo 'En proceso..'; else echo $perfi['ins']; ?></td>
								<td><?php if ($perfi['t3'] == 'P') echo 'En proceso..'; else echo $perfi['t3']; ?></td>
								<td><?php if ($perfi['t4'] == 'P') echo 'En proceso..'; else echo $perfi['t4']; ?></td>
								<td><?php if ($perfi['tsh'] == 'P') echo 'En proceso..'; else echo $perfi['tsh']; ?></td>
								<td><?php if ($perfi['amh'] == 'P') echo 'En proceso..'; else echo $perfi['amh']; ?></td>
								<td><?php if ($perfi['inh'] == 'P') echo 'En proceso..'; else echo $perfi['inh']; ?></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
					<?php if ($a_perfi->rowCount() < 1) echo '<h5>¡Aún no hay exámenes cargados!</h5>'; ?>
				</div>
			</div>
		<?php
			}
		?>
			<!-- serologias -->
			<div class="card mb-3" id="imprime">
				<h5 class="card-header" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">Serologías</h5>
				<div class="card-body collapse show" id="collapseExample">
					<?php
						if ($user['role'] == 1 || $user['role'] == 12 || $user['role'] == 13 || $user['role'] == 15) { ?>
						<a href="n_serologia_add.php?tipopaciente=1<?php print("&dni=" . $id . ""); ?>" rel="external" class="btn btn-danger">Agregar</a><br><br>
					<?php
						}
					?>
					<table class="table table-responsive table-bordered align-middle">
						<thead class="thead-dark">
							<tr>
								<th width="5%" class="text-center">#</th>
								<th width="10%" class="text-center">Fecha</th>
								<th width="10%" class="text-center">Hepatitis B HBs Ag</th>
								<th width="10%" class="text-center">Hepatitis C HCV Ac</th>
								<th width="10%" class="text-center">HIV</th>
								<th width="10%" class="text-center">RPR</th>
								<th width="10%" class="text-center">Rubeola IgG</th>
								<th width="10%" class="text-center">Toxoplasma IgG</th>
								<th width="10%" class="text-center">Clamidia IgG</th>
								<th width="10%" class="text-center">Clamidia IgM</th>
								<th width="10%" class="text-center">Usuario</th>
								<?php
								if ($user['role'] == 1 || $user['role'] == 12 || $user['role'] == 13 || $user['role'] == 15) { ?>
									<th width="5%" class="text-center">Operaciones</th>
								<?php } ?>
							</tr>
						</thead>
						<tbody>
							<?php
							$Sero = $db->prepare("SELECT * FROM hc_antece_p_sero WHERE tipo_paciente=1 and estado=1 and p_dni=? ORDER BY fec DESC");
							$Sero->execute(array($id));
							$item=1;
							while ($sero = $Sero->fetch(PDO::FETCH_ASSOC)) { ?>
								<tr>
									<?php print("<td class='text-center'>".$item++."</td>"); ?>
									<td valign="top" class="text-center"><?php if ($sero['lab'] <> "") echo date("d-m-Y", strtotime($sero['fec'])) . ' (' . $sero['lab'] . ')'; else { ?>
										<a href="e_ante_p_sero.php?dni=mujer<?php echo "&ip=" . $paci['dni'] . "&id=" . $sero['fec']; ?>" rel="external"><?php echo date("d-m-Y", strtotime($sero['fec'])); ?></a><?php } ?><?php if (file_exists("analisis/sero_" . $paci['dni'] . "_" . $sero['fec'] . ".pdf")) echo "<br><a href='archivos_hcpacientes.php?idArchivo=sero_" . $paci['dni'] . "_" . $sero['fec'] . "' target='new'>Descargar</a>"; ?>
									</td>
									<td valign="top" class="text-center" <?php if ($sero['hbs'] == 1) echo 'class="color"'; ?>><?php if ($sero['hbs'] == 1) echo "POSITIVO";
									if ($sero['hbs'] == 2) echo "NEGATIVO";
									if ($sero['hbs'] == 3) echo "EN PROCESO";
									if ($sero['hbs'] == 4) echo "INDETERMINADO";
									if ($sero['hbs'] == 0) echo "-"; ?></td>
									<td valign="top" class="text-center" <?php if ($sero['hcv'] == 1) echo 'class="color"'; ?>><?php if ($sero['hcv'] == 1) echo "POSITIVO";
									if ($sero['hcv'] == 2) echo "NEGATIVO";
									if ($sero['hcv'] == 3) echo "EN PROCESO";
									if ($sero['hcv'] == 4) echo "INDETERMINADO";
									if ($sero['hcv'] == 0) echo "-"; ?></td>
									<td valign="top" class="text-center" <?php if ($sero['hiv'] == 1) echo 'class="color"'; ?>><?php if ($sero['hiv'] == 1) echo "POSITIVO";
									if ($sero['hiv'] == 2) echo "NEGATIVO";
									if ($sero['hiv'] == 3) echo "EN PROCESO";
									if ($sero['hiv'] == 4) echo "INDETERMINADO";
									if ($sero['hiv'] == 0) echo "-"; ?></td>
									<td valign="top" class="text-center" <?php if ($sero['rpr'] == 1) echo 'class="color"'; ?>><?php if ($sero['rpr'] == 1) echo "POSITIVO";
									if ($sero['rpr'] == 2) echo "NEGATIVO";
									if ($sero['rpr'] == 3) echo "EN PROCESO";
									if ($sero['rpr'] == 4) echo "INDETERMINADO";
									if ($sero['rpr'] == 0) echo "-"; ?></td>
									<td valign="top" class="text-center" <?php if ($sero['rub'] == 1) echo 'class="color"'; ?>><?php if ($sero['rub'] == 1) echo "POSITIVO";
									if ($sero['rub'] == 2) echo "NEGATIVO";
									if ($sero['rub'] == 3) echo "EN PROCESO";
									if ($sero['rub'] == 4) echo "INDETERMINADO";
									if ($sero['rub'] == 0) echo "-"; ?></td>
									<td valign="top" class="text-center" <?php if ($sero['tox'] == 1) echo 'class="color"'; ?>><?php if ($sero['tox'] == 1) echo "POSITIVO";
									if ($sero['tox'] == 2) echo "NEGATIVO";
									if ($sero['tox'] == 3) echo "EN PROCESO";
									if ($sero['tox'] == 4) echo "INDETERMINADO";
									if ($sero['tox'] == 0) echo "-"; ?></td>
									<td valign="top" class="text-center" <?php if ($sero['cla_g'] == 1) echo 'class="color"'; ?>><?php if ($sero['cla_g'] == 1) echo "POSITIVO";
									if ($sero['cla_g'] == 2) echo "NEGATIVO";
									if ($sero['cla_g'] == 3) echo "EN PROCESO";
									if ($sero['cla_g'] == 4) echo "INDETERMINADO";
									if ($sero['cla_g'] == 0) echo "-"; ?></td>
									<td valign="top" class="text-center" <?php if ($sero['cla_m'] == 1) echo 'class="color"'; ?>><?php if ($sero['cla_m'] == 1) echo "POSITIVO";
									if ($sero['cla_m'] == 2) echo "NEGATIVO";
									if ($sero['cla_m'] == 3) echo "EN PROCESO";
									if ($sero['cla_m'] == 4) echo "INDETERMINADO";
									if ($sero['cla_m'] == 0) echo "-"; ?></td>
									<td><?php echo $sero['idusercreate'] ?></td>
									<?php
									if ($user['role'] == 1 || $user['role'] == 12 || $user['role'] == 13 || $user['role'] == 15) {
										print("<td class='text-center'>
											<img src='_libraries/open-iconic/svg/trash.svg' height='18' width='18' alt='icon name' class='btn_eliminar_informe' data-origen='serologia' data-informe='".$sero["id"]."'>
										</td>");
									} ?>
								</tr>
							<?php } ?>
						</tbody>
					</table>
					<?php if ($Sero->rowCount() < 1) echo '<h5>¡Aún no hay exámenes cargados!</h5>'; ?>
				</div>
			</div>
			<!-- serologias pareja -->
			<div class="card mb-3" id="imprime">
				<h5 class="card-header" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">Serologías para pareja</h5>
				<div class="card-body collapse show" id="collapseExample">
					<?php
					if ($user['role'] == 1 || $user['role'] == 12 || $user['role'] == 13 || $user['role'] == 15) {
						// agregar parejas
						$data = $db->prepare("
							select a.p_dni dni, a.p_ape ape, a.p_nom nom
							from hc_pareja a
							inner join hc_pare_paci b on b.p_dni = a.p_dni
							inner join hc_paciente c on c.dni = b.dni and c.dni = ?");
						$data->execute( array($id) );
						while ($info = $data->fetch(PDO::FETCH_ASSOC)) {
							print('<a href="n_serologia_add.php?tipopaciente=2&dni=' . $info['dni'] . '" rel="external" class="btn btn-danger">Agregar: ' . mb_strtoupper($info['ape']) . ' ' . mb_strtoupper($info['nom']) . '</a><br><br>');
						}
					}
					?>
					<table class="table table-responsive table-bordered align-middle">
						<thead class="thead-dark">
							<tr>
								<th width="5%" class="text-center">#</th>
								<th width="5%" class="text-center">Pareja</th>
								<th width="5%" class="text-center">Fecha</th>
								<th width="10%" class="text-center">Hepatitis B HBs Ag</th>
								<th width="10%" class="text-center">Hepatitis C HCV Ac</th>
								<th width="10%" class="text-center">HIV</th>
								<th width="10%" class="text-center">RPR</th>
								<th width="10%" class="text-center">Rubeola IgG</th>
								<th width="10%" class="text-center">Toxoplasma IgG</th>
								<th width="10%" class="text-center">Clamidia IgG</th>
								<th width="10%" class="text-center">Clamidia IgM</th>
								<th width="10%" class="text-center">Usuario</th>
								<?php
								if ($user['role'] == 1 || $user['role'] == 12 || $user['role'] == 13 || $user['role'] == 15) { ?>
									<th width="5%" class="text-center">Operaciones</th>
								<?php } ?>
							</tr>
						</thead>
						<tbody>
							<?php
							$Sero = $db->prepare("
								select a.*, b.p_ape ape, b.p_nom nom
								from hc_antece_p_sero a
								inner join hc_pareja b on b.p_dni = a.p_dni
								inner join hc_pare_paci c on c.p_dni = b.p_dni
								inner join hc_paciente d on d.dni = c.dni
								where a.tipo_paciente = 2 and a.estado=1 and d.dni = ?
								order by a.fec desc");
							$Sero->execute( array($id) );
							$item=1;
							while ($sero = $Sero->fetch(PDO::FETCH_ASSOC)) { ?>
								<tr>
									<?php
									print("
										<td class='text-center'>" . $item++ . "</td>
										<td>" . mb_strtoupper($sero["ape"]) . " " . mb_strtoupper($sero["nom"]) . "</td>");
									?>
									<td valign="top" class="text-center">
										<?php
										if ($sero['lab'] <> "") echo date("d-m-Y", strtotime($sero['fec'])) . ' (' . $sero['lab'] . ')';
										else { ?>
											<a href="e_ante_p_sero.php?tipopaciente=2<?php echo "&ip=" . $paci['dni'] . "&id=" . $sero['fec']; ?>" rel="external">
												<?php echo date("d-m-Y", strtotime($sero['fec'])); ?>
											</a>
										<?php } ?>
										<?php
											if (file_exists("analisis/sero_" . $paci['dni'] . "_" . $sero['fec'] . ".pdf")) echo "<br>
											<a href='archivos_hcpacientes.php?idArchivo=hsero_" . $paci['dni'] . "_" . $sero['fec'] . "' target='new'>Descargar</a>";
										?>
									</td>
									<td valign="top" class="text-center" <?php if ($sero['hbs'] == 1) echo 'class="color"'; ?>><?php if ($sero['hbs'] == 1) echo "POSITIVO";
									if ($sero['hbs'] == 2) echo "NEGATIVO";
									if ($sero['hbs'] == 3) echo "EN PROCESO";
									if ($sero['hbs'] == 4) echo "INDETERMINADO";
									if ($sero['hbs'] == 0) echo "-"; ?></td>
									<td valign="top" class="text-center" <?php if ($sero['hcv'] == 1) echo 'class="color"'; ?>><?php if ($sero['hcv'] == 1) echo "POSITIVO";
									if ($sero['hcv'] == 2) echo "NEGATIVO";
									if ($sero['hcv'] == 3) echo "EN PROCESO";
									if ($sero['hcv'] == 4) echo "INDETERMINADO";
									if ($sero['hcv'] == 0) echo "-"; ?></td>
									<td valign="top" class="text-center" <?php if ($sero['hiv'] == 1) echo 'class="color"'; ?>><?php if ($sero['hiv'] == 1) echo "POSITIVO";
									if ($sero['hiv'] == 2) echo "NEGATIVO";
									if ($sero['hiv'] == 3) echo "EN PROCESO";
									if ($sero['hiv'] == 4) echo "INDETERMINADO";
									if ($sero['hiv'] == 0) echo "-"; ?></td>
									<td valign="top" class="text-center" <?php if ($sero['rpr'] == 1) echo 'class="color"'; ?>><?php if ($sero['rpr'] == 1) echo "POSITIVO";
									if ($sero['rpr'] == 2) echo "NEGATIVO";
									if ($sero['rpr'] == 3) echo "EN PROCESO";
									if ($sero['rpr'] == 4) echo "INDETERMINADO";
									if ($sero['rpr'] == 0) echo "-"; ?></td>
									<td valign="top" class="text-center" <?php if ($sero['rub'] == 1) echo 'class="color"'; ?>><?php if ($sero['rub'] == 1) echo "POSITIVO";
									if ($sero['rub'] == 2) echo "NEGATIVO";
									if ($sero['rub'] == 3) echo "EN PROCESO";
									if ($sero['rub'] == 4) echo "INDETERMINADO";
									if ($sero['rub'] == 0) echo "-"; ?></td>
									<td valign="top" class="text-center" <?php if ($sero['tox'] == 1) echo 'class="color"'; ?>><?php if ($sero['tox'] == 1) echo "Positivo";
									if ($sero['tox'] == 2) echo "NEGATIVO";
									if ($sero['tox'] == 3) echo "EN PROCESO";
									if ($sero['tox'] == 4) echo "INDETERMINADO";
									if ($sero['tox'] == 0) echo "-"; ?></td>
									<td valign="top" class="text-center" <?php if ($sero['cla_g'] == 1) echo 'class="color"'; ?>><?php if ($sero['cla_g'] == 1) echo "Positivo";
									if ($sero['cla_g'] == 2) echo "NEGATIVO";
									if ($sero['cla_g'] == 3) echo "EN PROCESO";
									if ($sero['cla_g'] == 4) echo "INDETERMINADO";
									if ($sero['cla_g'] == 0) echo "-"; ?></td>
									<td valign="top" class="text-center" <?php if ($sero['cla_m'] == 1) echo 'class="color"'; ?>><?php if ($sero['cla_m'] == 1) echo "Positivo";
									if ($sero['cla_m'] == 2) echo "NEGATIVO";
									if ($sero['cla_m'] == 3) echo "EN PROCESO";
									if ($sero['cla_m'] == 4) echo "INDETERMINADO";
									if ($sero['cla_m'] == 0) echo "-"; ?></td>
									<td><?php echo $sero['idusercreate'] ?></td>
									<?php
									if ($user['role'] == 1 || $user['role'] == 12 || $user['role'] == 13 || $user['role'] == 15) {
										print("<td class='text-center'>
											<img src='_libraries/open-iconic/svg/trash.svg' height='18' width='18' alt='icon name' class='btn_eliminar_informe' data-origen='serologia' data-informe='".$sero["id"]."'>
										</td>");
									} ?>
								</tr>
							<?php } ?>
						</tbody>
					</table>
					<?php if ($Sero->rowCount() < 1) echo '<h5>¡Aún no hay exámenes cargados!</h5>'; ?>
				</div>
			</div>
			<!-- Hematologia -->
			<div class="card mb-3" id="imprime">
				<h5 class="card-header" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">Hematología</h5>
				<div class="card-body collapse show" id="collapseExample">
					<?php
						if ($user['role'] == 1 || $user['role'] == 12 || $user['role'] == 13 || $user['role'] == 15) {
					?>
						<a href="n_hematologia_add.php?tipopaciente=1<?php print("&dni=" . $id . ""); ?>" rel="external" class="btn btn-danger">Agregar</a><br><br>
					<?php
						}
					?>
					<table class="table table-responsive table-bordered align-middle">
						<thead class="thead-dark">
							<tr>
								<th width="5%" class="text-center">#</th>
								<th width="10%" class="text-center">Fecha</th>
								<th width="50%" class="text-center">Observación</th>
								<th width="35%" class="text-center">Informe</th>
								<?php if ($user['role'] == 1 || $user['role'] == 12 || $user['role'] == 13 || $user['role'] == 15) { ?>
									<th width="10%" class="text-center">Operaciones</th>
								<?php } ?>
							</tr>
						</thead>
						<tbody>
							<?php
							$data = $db->prepare("select * from hc_hematologia where estado = 1 and tipopaciente=1 and numerodocumento=? order by fresultado desc");
							$data->execute( array($id) );
							$item=1;
							while ($info = $data->fetch(PDO::FETCH_ASSOC)) { ?>
								<tr>
									<?php

										$ruta = "hematologia/" . $id . "/" . $info['documento'];
										$enlace = "<td></td>";
										if (file_exists($ruta)) { 
											$enlace = "<td class='text-center'><a href='" . $ruta ."' target='_blank'>Ver/ Descargar</a></td>";
 										}

									print("
										<td class='text-center'>".$item++."</td>
										<td class='text-center'>" . $info["fresultado"] . "</td>
										<td class='text-center'>" . $info["obs"] . "</td>".$enlace);
										if ($user['role'] == 1 || $user['role'] == 12 || $user['role'] == 13 || $user['role'] == 15) {
											print("<td class='text-center'>
												<img src='_libraries/open-iconic/svg/trash.svg' height='18' width='18' alt='icon name' class='btn_eliminar_informe' data-origen='hematologia' data-informe='".$info["id"]."'>
											</td>");
										} ?>
								</tr>
							<?php } ?>
						</tbody>
					</table>
					<?php if ($data->rowCount() < 1) echo '<h5>¡Aún no hay exámenes cargados!</h5>'; ?>
				</div>
			</div>
			<!-- Hematología para pareja -->
			<div class="card mb-3" id="imprime">
				<h5 class="card-header" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">Hematología para pareja</h5>
				<div class="card-body collapse show" id="collapseExample">
					<!-- <a href="n_hematologia_add.php?tipopaciente=1<?php print("&dni=" . $id . ""); ?>" rel="external" class="btn btn-danger">Agregar</a><br><br> -->
					<?php
					if ($user['role'] == 1 || $user['role'] == 12 || $user['role'] == 13 || $user['role'] == 15) {
						// agregar parejas
						$data = $db->prepare("
							select a.p_dni dni, a.p_ape ape, a.p_nom nom
							from hc_pareja a
							inner join hc_pare_paci b on b.p_dni = a.p_dni
							inner join hc_paciente c on c.dni = b.dni and c.dni = ?");
						$data->execute( array($id) );
						while ($info = $data->fetch(PDO::FETCH_ASSOC)) {
							print('<a href="n_hematologia_add.php?tipopaciente=2&dni=' . $info['dni'] . '" rel="external" class="btn btn-danger">Agregar: ' . mb_strtoupper($info['ape']) . ' ' . mb_strtoupper($info['nom']) . '</a><br><br>');
						}
					} ?>
					<table class="table table-responsive table-bordered align-middle">
						<thead class="thead-dark">
							<tr>
								<th width="5%" class="text-center">#</th>
								<th width="10%" class="text-center">Pareja</th>
								<th width="10%" class="text-center">Fecha</th>
								<th width="50%" class="text-center">Observación</th>
								<th width="35%" class="text-center">Informe</th>
								<?php
									if ($user['role'] == 1 || $user['role'] == 12 || $user['role'] == 13 || $user['role'] == 15) {
								?>
									<th width="10%" class="text-center">Operaciones</th>
								<?php
									}
								?>
							</tr>
						</thead>
						<tbody>
							<?php
							$data = $db->prepare("
								select a.*, b.p_ape ape, b.p_nom nom
								from hc_hematologia a
								inner join hc_pareja b on b.p_dni = a.numerodocumento
								inner join hc_pare_paci c on c.p_dni = b.p_dni
								inner join hc_paciente d on d.dni = c.dni
								where a.estado = 1 and a.tipopaciente=2 and d.dni = ?
								order by a.fresultado asc");
							$data->execute( array($id) );
							$item=1;
							while ($info = $data->fetch(PDO::FETCH_ASSOC)) { ?>
								<tr>
									<?php

									$ruta = "hematologia/" . $id . "/" . $info['documento'];
									$enlace = "<td></td>";
									if (file_exists($ruta)) { 
										$enlace = "<td class='text-center'><a href='hematologia/" . $id . "/" . $info['documento']."' target='_blank'>Ver/ Descargar</a></td>";
									}

									print("
										<td class='text-center'>" . $item++ . "</td>
										<td>" . mb_strtoupper($info["ape"]) . " " . mb_strtoupper($info["nom"]) . "</td>
										<td class='text-center'>" . $info["fresultado"] . "</td>
										<td class='text-center'>" . $info["obs"] . "</td>".$enlace);
									if ($user['role'] == 1 || $user['role'] == 12 || $user['role'] == 13 || $user['role'] == 15) {
										print("
										<td class='text-center'>
												<img src='_libraries/open-iconic/svg/trash.svg' height='18' width='18' alt='icon name' class='btn_eliminar_informe' data-origen='hematologia' data-informe='".$info["id"]."'>
										</td>");
									}
									?>
								</tr>
							<?php } ?>
						</tbody>
					</table>
					<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" id="eliminar_informe">
						<div class="modal-dialog modal-dialog-centered" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title" id="exampleModalLongTitle">Confirmar Eliminar</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">¿Realmente desea eliminar el informe?</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" id="modal-btn-no">Cancelar</button>
									<button type="button" class="btn btn-dark" id="modal-btn-si">Confirmar</button>
								</div>
							</div>
						</div>
					</div>
					<?php if ($data->rowCount() < 1) echo '<h5>¡Aún no hay exámenes cargados!</h5>'; ?>
				</div>
			</div>
			<hr>
			<!-- examenes anglolab -->
			<div class="card mb-3" id="imprime">
				<h5 class="card-header" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">Análisis Clínicos (genomics, biogenesis) y Exámenes Anglolab</h5>
				<div class="card-body collapse show" id="collapseExample">
					<?php
						$rAnal = $db->prepare("SELECT * FROM hc_analisis WHERE a_dni=? AND lab<>'legal' AND lab<>'eco' AND lab <> 'analisis' ORDER BY a_fec DESC");
						$rAnal->execute(array($id));
						$rAglo = $db->prepare("SELECT variabledescripcion,Resultado,Unidad,observacion,fechavalidacion FROM lab_anglo WHERE numdoc=? ORDER BY fechavalidacion DESC");
						$rAglo->execute(array($id));
					if ($rAnal->rowCount() > 0 or $rAglo->rowCount() > 0) { ?>
						<table class="table table-responsive table-bordered align-middle">
							<thead class="thead-dark">
								<tr>
									<th width="25%">Otros Exámenes</th>
									<th width="25%">Resultado</th>
									<th width="25%">Observación</th>
									<th width="25%">Informe</th>
									<th width="25%">Fecha</th>
								</tr>
							</thead>
							<tbody>
								<?php
								if ($rAnal->rowCount() > 0) {
								while ($anal = $rAnal->fetch(PDO::FETCH_ASSOC)) { ?>
									<tr>
									<th><?php echo $anal['a_exa']; ?></th>
									<td><?php echo $anal['a_sta']; ?></td>
									<td><?php echo $anal['a_obs']; ?></td>
									<th>
									<a href='<?php echo "archivos_hcpacientes.php?idArchivo=" . $anal['id'] . "_" . $anal['a_dni']; ?>'
									target="new">Ver/Descargar</a></th>
									<td><?php echo date("d-m-Y", strtotime($anal['a_fec'])); ?></td>
									</tr>
									<?php }
									} ?>
									<?php if ($rAglo->rowCount() > 0) {
									while ($aglo = $rAglo->fetch(PDO::FETCH_ASSOC)) { ?>
									<tr>
										<th><?php echo $aglo['variabledescripcion']; ?></th>
										<td><?php echo $aglo['resultado'] . ' ' . $aglo['unidad']; ?></td>
										<td><?php echo $aglo['observacion']; ?></td>
										<th>-</th>
										<td><?php echo date("d-m-Y", strtotime($aglo['fechavalidacion'])); ?></td>
									</tr>
								<?php }
								} ?>
							</tbody>
						</table>
					<?php } ?>
				</div>
			</div>
			<!-- <label for="fe_exa">Otros Exámenes:</label>
			<textarea name="fe_exa" id="fe_exa" class="form-control"><?php echo $paci['fe_exa']; ?></textarea> -->
			<br><br>
		</div>
	</div>
	<script src="js/jquery-3.2.1.slim.min.js" crossorigin="anonymous"></script>
	<script src="js/jquery-1.11.1.min.js"></script>
	<script src="js/popper.min.js" crossorigin="anonymous"></script>
	<script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
	<script src="js/global.js" crossorigin="anonymous"></script>
</body>
</html>