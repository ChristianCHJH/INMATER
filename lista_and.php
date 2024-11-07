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

    <style type="text/css">
    input[data-type=search]:enabled {
        background: #fcfcfc;
    }

    input[data-type=search]:disabled {
        background: #dddddd;
    }
    </style>

    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>

    <style>
    .color {
        color: #F4062B !important;
    }

    .ui-btn {
        font-size: 14px;
    }
    </style>
</head>

<body>
    <div data-role="page" class="ui-responsive-panel" id="lista">
        <?php
		// verificar el reporte
		$repo = 1;

		if (!!$_GET && isset($_GET['repo']) && !empty($_GET['repo'])) {
			switch ($_GET['repo']) {
				case 'esp':
					$repo = 2;
					break;

				case 'crio':
					$repo = 3;
					break;
				
				default:
					$repo = 1;
					break;
			}
		}

		$rUser = $db->prepare("SELECT role FROM usuario WHERE userx = ?;");
		$rUser->execute([$login]);
		$user = $rUser->fetch(PDO::FETCH_ASSOC);

		if ($user['role'] == 2) {
			// total de parejas
			$rPaci = $db->prepare("SELECT
				hc_pare_paci.p_dni, hc_pare_paci.dni, p_nom, p_ape, p_san, p_m_ets, p_m_ale, hc_pare_paci.p_het
				FROM hc_pareja, hc_pare_paci
				WHERE hc_pareja.p_dni = hc_pare_paci.p_dni AND hc_pareja.estado = 1 AND hc_pare_paci.estado = 1
				ORDER by p_ape,p_nom ASC");
			$rPaci->execute();

			// espermas pendientes
			switch ($repo) {
				case 1:
					$stmt = $db->prepare("SELECT * FROM
					(
						SELECT
						hc_pare_paci.p_dni, hc_pare_paci.dni, hc_pareja.p_nom, hc_pareja.p_ape, hc_pareja.p_san, hc_pareja.p_m_ets, hc_pareja.p_m_ale, hc_pare_paci.p_het
						FROM hc_pareja
						INNER JOIN hc_pare_paci ON hc_pare_paci.p_dni = hc_pareja.p_dni
						INNER JOIN lab_andro_esp ON lab_andro_esp.p_dni = hc_pare_paci.p_dni
						WHERE lab_andro_esp.emb = 0 OR lab_andro_esp.fec BETWEEN (current_date - interval '15 days') AND (current_date + interval '1 day')
	
						UNION
	
						SELECT
						hc_pare_paci.p_dni, hc_pare_paci.dni, hc_pareja.p_nom, hc_pareja.p_ape, hc_pareja.p_san, hc_pareja.p_m_ets, hc_pareja.p_m_ale, hc_pare_paci.p_het
						FROM hc_pareja
						INNER JOIN hc_pare_paci ON hc_pare_paci.p_dni = hc_pareja.p_dni
						INNER JOIN lab_andro_cap ON lab_andro_cap.p_dni = hc_pare_paci.p_dni and lab_andro_cap.eliminado is false
						WHERE lab_andro_cap.emb = 0 OR lab_andro_cap.fec BETWEEN (current_date - interval '15 days') AND (current_date + interval '1 day')
	
						UNION
	
						SELECT
						hc_pare_paci.p_dni, hc_pare_paci.dni, hc_pareja.p_nom, hc_pareja.p_ape, hc_pareja.p_san, hc_pareja.p_m_ets, hc_pareja.p_m_ale, hc_pare_paci.p_het
						FROM hc_pareja
						INNER JOIN hc_pare_paci ON hc_pare_paci.p_dni = hc_pareja.p_dni
						INNER JOIN lab_andro_tes_cap ON lab_andro_tes_cap.p_dni = hc_pare_paci.p_dni
						WHERE lab_andro_tes_cap.emb = 0 OR lab_andro_tes_cap.fec BETWEEN (current_date - interval '15 days') AND (current_date + interval '1 day')
	
						UNION
	
						SELECT
						hc_pare_paci.p_dni, hc_pare_paci.dni, hc_pareja.p_nom, hc_pareja.p_ape, hc_pareja.p_san, hc_pareja.p_m_ets, hc_pareja.p_m_ale, hc_pare_paci.p_het
						FROM hc_pareja
						INNER JOIN hc_pare_paci ON hc_pare_paci.p_dni = hc_pareja.p_dni
						INNER JOIN lab_andro_tes_sob ON lab_andro_tes_sob.p_dni = hc_pare_paci.p_dni
						WHERE lab_andro_tes_sob.emb = 0 OR lab_andro_tes_sob.fec BETWEEN (current_date - interval '15 days') AND (current_date + interval '1 day')
	
						UNION
	
						SELECT
						hc_pare_paci.p_dni, hc_pare_paci.dni, hc_pareja.p_nom, hc_pareja.p_ape, hc_pareja.p_san, hc_pareja.p_m_ets, hc_pareja.p_m_ale, hc_pare_paci.p_het
						FROM hc_pareja
						INNER JOIN hc_pare_paci ON hc_pare_paci.p_dni = hc_pareja.p_dni
						INNER JOIN lab_andro_bio_tes ON lab_andro_bio_tes.p_dni = hc_pare_paci.p_dni
						WHERE lab_andro_bio_tes.emb = 0 OR lab_andro_bio_tes.fec BETWEEN (current_date - interval '15 days') AND (current_date + interval '1 day')
	
						UNION
	
						SELECT
						hc_pare_paci.p_dni, hc_pare_paci.dni, hc_pareja.p_nom, hc_pareja.p_ape, hc_pareja.p_san, hc_pareja.p_m_ets, hc_pareja.p_m_ale, hc_pare_paci.p_het
						FROM hc_pareja
						INNER JOIN hc_pare_paci ON hc_pare_paci.p_dni = hc_pareja.p_dni
						INNER JOIN lab_andro_crio_sem ON lab_andro_crio_sem.p_dni = hc_pare_paci.p_dni
						WHERE lab_andro_crio_sem.emb = 0 OR lab_andro_crio_sem.fec BETWEEN (current_date - interval '15 days') AND (current_date + interval '1 day')
					) AS a
					ORDER BY a.p_dni
					LIMIT 20 OFFSET 0;");

					break;
				
				case 2:
					$stmt = $db->prepare("SELECT
					hc_pare_paci.p_dni, hc_pare_paci.dni, hc_pareja.p_nom, hc_pareja.p_ape, hc_pareja.p_san, hc_pareja.p_m_ets, hc_pareja.p_m_ale, hc_pare_paci.p_het, lab_andro_esp.fec
					FROM hc_pareja
					INNER JOIN hc_pare_paci ON hc_pare_paci.p_dni = hc_pareja.p_dni
					INNER JOIN lab_andro_esp ON lab_andro_esp.p_dni = hc_pare_paci.p_dni
					-- WHERE lab_andro_esp.emb = 0 OR lab_andro_esp.fec BETWEEN date_add(curdate(), interval - 15 day) AND date_add(curdate(), interval + 1 day)
					ORDER BY lab_andro_esp.fec DESC
					LIMIT 20 OFFSET 0;");

					break;

				case 3:
					$stmt = $db->prepare("SELECT
					hc_pare_paci.p_dni, hc_pare_paci.dni, hc_pareja.p_nom, hc_pareja.p_ape, hc_pareja.p_san, hc_pareja.p_m_ets, hc_pareja.p_m_ale, hc_pare_paci.p_het
					FROM hc_pareja
					INNER JOIN hc_pare_paci ON hc_pare_paci.p_dni = hc_pareja.p_dni
					INNER JOIN lab_andro_crio_sem ON lab_andro_crio_sem.p_dni = hc_pare_paci.p_dni
					-- WHERE lab_andro_crio_sem.emb = 0 OR lab_andro_crio_sem.fec BETWEEN date_add(curdate(), interval - 15 day) AND date_add(curdate(), interval + 1 day)
					ORDER BY lab_andro_crio_sem.fec DESC
					LIMIT 20 OFFSET 0;");

					break;

				default: break;
			}

			$stmt->execute();

			// capacitaciones pendientes
			$Cap = $db->prepare("SELECT id FROM lab_andro_cap WHERE emb=0 and eliminado is false"); 
			$Cap->execute();

			if ($Cap->rowCount() > 0) {
				$new_cap = "<i class='color'>(" . $Cap->rowCount() . " Pendientes)</i>";
			} else {
				$new_cap = "";
			}
		} ?>

        <div data-role="header" data-position="fixed">

            <h1>Andrología</h1>
            <?php
			if ($user['role']==2) { ?>
            <a href="#indice_paci" data-icon="bars" id="b_indice" class="ui-icon-alt" data-theme="a">Menú Andrología</a>
            <?php } ?>
            <a href="salir.php"
                class="ui-btn-right ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-right ui-icon-power"
                rel="external">Salir</a>
        </div>

        <div data-role="panel" id="indice_paci">
            <img src="_images/logo.jpg" />

            <ul data-role="listview" data-inset="true" data-theme="a">
                <li data-icon="home"><a href="lista.php" rel="external">Inicio</a></li>
                <li data-icon="user"><a href="n_pare_and.php" rel="external">Nuevo Paciente</a></li>
                <li data-icon="check"><a href="lista_and.php?repo=esp" rel="external">Espermatogramas</a></li>
                <li data-icon="check"><a href="lista_and.php?repo=crio" rel="external">Criopreservación</a></li>
                <li data-icon="check"><a href="lista_cap.php" rel="external">Capacitaciones IIU
                        <?php echo $new_cap; ?></a></li>
                <li data-icon="check"><a href="andro_capacitaciones_invitro.php" rel="external">Capacitaciones
                        Invitro</a></li>
                <li data-icon="bullets"><a href="repo_viales_congelados.php" rel="external">Viales Congelados</a></li>
                <li data-icon="bullets"><a href="lista_tanque_descarga.php" rel="external">Estado de Tanque</a></li>
                <li data-icon="bullets"><a href="lista_tan.php" rel="external">Mantenimiento Tanques</a></li>
                <li data-icon="bullets"><a href="tanque_descarga.php" rel="external">Descarga de Tanque</a></li>
                <li data-icon="bullets"><a href="repo-lenshooke.php" rel="external">Reporte Lenshooke</a></li>
            </ul>
        </div>

        <div class="ui-content" role="main">
            <form id="listapaciente" action="" method="post" name="form1">
                <?php
				print('<input type="hidden" value="' . $repo . '" id="repo">');

				if ($rPaci->rowCount()>0) { ?>
                <input class="filtro" data-type="search" placeholder="Filtro..">

                <table id="detallepaciente" data-role="table" data-filter="true" data-input=".filtro"
                    class="table-stripe ui-responsive lista_orden">
                    <thead id="prochead">
                        <tr>
                            <th style="text-align: center;">DNI/ Pasaporte</th>
                            <th>Apellidos y Nombres</th>
                            <th>Médico</th>
                            <th>Pareja</th>
                            <th>Exámenes</th>
                        </tr>
                    </thead>

                    <tbody id="procbody">
                        <?php
							$c = 0;
							while ($paci = $stmt->fetch(PDO::FETCH_ASSOC)) {
								$examenes = $dataposition = $p_m_ale = $p_san = $vih = $hepa = $dapto = $don = $esperma = $capa = $test_capa = $sobre = $testi = $crio = $medico = "";
								$pareja = "";
								$c++;

								// espermatograma
								if ($repo == 1 || $repo == 2) {
									$esp_consulta = $db->prepare("SELECT
										max(fec) fec, count(case when emb = 0 then true end) pendientes, count(*) total
										FROM lab_andro_esp
										WHERE p_dni = ?
										group by p_dni;");
									$esp_consulta->execute(array($paci['p_dni']));
									$esp_data = $esp_consulta->fetch();

									if (isset($esp_data["total"]) && $esp_data["total"] <> 0) {
										$examenes .= $esp_data["fec"]." Espermatogramas: " . $esp_data["total"];
									}

									if (isset($esp_data["pendientes"]) && $esp_data["pendientes"] <> 0) {
										$examenes .= " (<i class='color'>".$esp_data["pendientes"]." pendiente(s)</i>)";
									}
								}

								// capacitacion espermatica
								if ($repo == 1) {
									$consulta = $db->prepare("SELECT max(fec) fec, count(case when emb = 0 then true end) pendientes, count(*) total
										FROM lab_andro_cap
										WHERE p_dni=?
										group by p_dni");
									$consulta->execute(array($paci['p_dni']));
									$data = $consulta->fetch();

									if (isset($data["total"]) && $data["total"] <> 0) {
										if (!empty($examenes)) {
											$examenes.="<br>";
										}
										$examenes .= $data["fec"]." Capacitaciones: " . $data["total"];
									}

									if (isset($data["pendientes"]) && $data["pendientes"] <> 0) {
										$examenes .= " (<i class='color'>".$data["pendientes"]." pendiente(s)</i>)";
									}
								}

								// test de capacitaciones espermaticas
								if ($repo == 1) {
									$consulta = $db->prepare("SELECT
										max(fec) fec, count(case when emb = 0 then true end) pendientes, count(*) total
										FROM lab_andro_tes_cap
										WHERE p_dni=?
										group by p_dni");
									$consulta->execute(array($paci['p_dni']));
									$data = $consulta->fetch();

									if (isset($data["total"]) && $data["total"] <> 0) {
										if (!empty($examenes)) {
											$examenes.="<br>";
										}
										$examenes .= $data["fec"]." Test Capacitaciones: " . $data["total"];
									}

									if (isset($data["total"]) && $data["pendientes"] <> 0) {
										$examenes .= " (<i class='color'>".$data["pendientes"]." pendiente(s)</i>)";
									}
								}

								// test de sobrevivencia
								if ($repo == 1) {
									$consulta = $db->prepare("SELECT
										max(fec) fec, count(case when emb = 0 then true end) pendientes, count(*) total
										FROM lab_andro_tes_sob
										WHERE p_dni=?
										group by p_dni");
									$consulta->execute(array($paci['p_dni']));
									$data = $consulta->fetch();

									if (isset($data["total"]) && $data["total"] <> 0) {
										if (!empty($examenes)) {
											$examenes.="<br>";
										}
										$examenes .= $data["fec"]." Test Sobrevivencia: " . $data["total"];
									}

									if (isset($data["pendientes"]) && $data["pendientes"] <> 0) {
										$examenes .= " (<i class='color'>".$data["pendientes"]." pendiente(s)</i>)";
									}
								}

								// biopsia testicular
								if ($repo == 1) {
									$consulta = $db->prepare("SELECT
										max(fec) fec, count(case when emb = 0 then true end) pendientes, count(*) total
										FROM lab_andro_bio_tes
										WHERE p_dni=?
										group by p_dni");
									$consulta->execute(array($paci['p_dni']));
									$data = $consulta->fetch();

									if (isset($data["total"]) && $data["total"] <> 0) {
										if (!empty($examenes)) {
											$examenes.="<br>";
										}
										$examenes .= $data["fec"]." Biopsia Testicular: " . $data["total"];
									}

									if (isset($data["pendientes"]) && $data["pendientes"] <> 0) {
										$examenes .= " (<i class='color'>".$data["pendientes"]." pendiente(s)</i>)";
									}
								}

								// criopreservacion de semen
								if ($repo == 1 || $repo == 3) {
									$consulta = $db->prepare("SELECT max(fec) fec, count(case when emb = 0 then true end) pendientes, count(*) total
										FROM lab_andro_crio_sem
										WHERE p_dni=?
										group by p_dni");
									$consulta->execute(array($paci['p_dni']));
									$data = $consulta->fetch();

									if (isset($data["total"]) && $data["total"] <> 0) {
										if (!empty($examenes)) {
											$examenes.="<br>";
										}
										$examenes .= $data["fec"]." Criopreservación Semen: " . $data["total"];
									}

									if (isset($data["pendientes"]) && $data["pendientes"] <> 0) {
										$examenes .= " (<i class='color'>".$data["pendientes"]." pendiente(s)</i>)";
									}
								}

								// 
								if (!empty($paci['dni'])) {
									$rPare = $db->prepare("SELECT nom, ape, med FROM hc_paciente WHERE dni=?;");
									$rPare->execute([$paci['dni']]);
									$pare = $rPare->fetch(PDO::FETCH_ASSOC);
								}

								if ($paci['p_m_ale'] == "Medicamentada") $p_m_ale = " (ALERGIA MEDICAMENTADA)";
								if (strpos($paci['p_san'], "-") !== false) $p_san = " (SANGRE NEGATIVA)";
								if (strpos($paci['p_m_ets'], "VIH") !== false) $vih = " (VIH)"; 
								if (strpos($paci['p_m_ets'], "Hepatitis C") !== false) $hepa = " (Hepatitis C)";
								if ($paci['dni']=="" AND $paci['p_het']==1) $dapto = " (Donante APTO)";
								if ($paci['dni']=="" AND $paci['p_het']==2) $don = " (Donante)";

								//
								if ($paci['dni']<>"") {
									$medico = mb_strtolower($pare['med']);
									$pareja = '<a href="e_paci.php?id=' . $paci['dni'] . '" rel="external" target="_blank">' . ucwords(mb_strtolower($pare['ape'])) . ' ' . ucwords(mb_strtolower($pare['nom'])) . '</a>';
								} else {
									$medico = '-';
									$pareja = '-';
								}

								print("
								<tr" . $dataposition . ">
									<th style='text-align: center;'>" . $paci['p_dni'] . "</th>
									<th>
										<a href='e_pare.php?id=" . $paci['dni'] . "&ip=" . $paci['p_dni'] . "' rel='external'>" . ucwords(mb_strtolower($paci['p_ape'])) . ' ' . ucwords(mb_strtolower($paci['p_nom'])) . "</a><br>
										<small style='opacity:.5;'>" . $p_m_ale . $p_san . $vih . $hepa . $dapto . $don . "</small>
									</th>
									<td>" . $medico . "</td>
									<td>" . $pareja . "</td>
									<td>" . $examenes . "</td>
								</tr>");
							} ?>
                    </tbody>
                </table>
                <?php } else { echo '<p><h3>¡ No hay Pacientes !</h3></p>'; } ?>
            </form>
        </div>

        <div data-role="footer" data-position="fixed" id="footer">
            <p><small><?php echo $rPaci->rowCount();?> Pacientes</small></p>
        </div>
    </div>

    <script>
    $(document).keyup('#listapaciente .ui-input-search', function(e) {
        if (e.which == 13) {
            var paciente = $('#listapaciente .ui-input-search :input')[0].value;
            $("#listapaciente .ui-input-search :input").prop("disabled", true);

            $.post("le_tanque.php", {
                andropac: paciente,
                repo: $('#repo').val()
            }, function(data) {
                $("#detallepaciente tbody").html("");
                $("#detallepaciente tbody").append(data);
                $('.ui-page').trigger('create');
            }).done(function() {
                document.getElementById("prochead").classList.remove("ui-screen-hidden");
                document.getElementById("procbody").classList.remove("ui-screen-hidden");
                $("#listapaciente .ui-input-search :input").prop("disabled", false);
                $("#listapaciente .ui-input-search :input").focus();
            });
        }
    });
    </script>
</body>

</html>