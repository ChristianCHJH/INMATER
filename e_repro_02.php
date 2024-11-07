<!DOCTYPE HTML>
<html>

<head>
<?php
   include 'seguridad_login.php';
   require("_database/database_farmacia.php");
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" f8JKPC3mQxxOum4k>
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/chosen.min.css">
    <link rel="stylesheet" href="_themes/tema_inmater.min.css" />
    <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css" />
    <link rel="stylesheet" href="_libraries/open-iconic/font/css/open-iconic.min.css" />
    <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css"
+        integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
    <link rel="stylesheet" href="css/e_repro.css?v=4" />
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
    <script src="js/chosen.jquery.min.js"></script>
    <style>
    .title-secondary-bold {
        font-size: 14px;
        font-weight: bold;
    }
    .in-input .ui-mini {
        margin: 0 !important;
    }
	.columnahc input ,.columnahc textarea{
		width: 6rem;
	}
    .medico-tratante-titulo {
        padding: 10px 0;
    }
    .ra_andrologia th {
        border-bottom: 1px solid #d6d6d6;
    }

    .ra_andrologia tr:nth-child(even) {
        background: #e9e9e9;
    }
    </style>
</head>
<script>
        function init() {
            window.addEventListener('popstate', function (event) {
                event.preventDefault();
                history.pushState(null, document.title, location.href);
            }, false);
            history.pushState(null, document.title, location.href);
        }
    </script>
<body onload="init()">
    <div data-role="page" class="ui-responsive-panel" id="e_repro" data-dialog="true">
        <?php
        $id = $_GET['id'];
        $ciclo=obtenerCicloEstimulacionDetalle($id);
        $iniciclo=inicioCicloEstimulacionDetalle($id);
			$total_semanas=60;
			if (isset($_POST['idx']) && !empty(isset($_POST['idx']))) {
				$con_fec = "";
				if ($_POST['con_fec0'] <> "") {
					$con_fec = $_POST['con_fec0'];
					for ($i = 1; $i < $total_semanas; $i++) {
						$con_fec = $con_fec."|".endCycle($_POST['con_fec0'], $i);
					}
				}

				if(isset($_POST['con_od0']))$con_od = $_POST['con_od0'];
				if(isset($_POST['con_oi0']))$con_oi = $_POST['con_oi0'];
				if(isset($_POST['con_end0']))$con_end = $_POST['con_end0'];
				if(isset($_POST['con_obs0']))$con_obs = $_POST['con_obs0'];
                $con_iny='';
			
				for ($i = 0; $i <= $total_semanas; $i++) { $con_iny.=($_POST['con_iny'.$i]."|"); }

				require("_database/db_medico_reproduccion.php");
                if(isset($_POST["f_asp"]) && $_POST["f_asp"] <> ''){

                    $coincidencias = validarAgendaReproTurno($_POST["idx"], $_POST["f_asp"], $_POST["idturno"]?? 0);
                }else{
                    $coincidencias=0;
                }

				if ($coincidencias == 0) {
						if ($_POST['f_asp'] <> '') {
								$titcal = '';
								if (isset($_POST['p_fiv']) && $_POST['p_fiv'] == 1) { $titcal .= 'FIV '; }
								if (isset($_POST['p_icsi']) && $_POST['p_icsi'] == 1) { $titcal .= $_ENV["VAR_ICSI"] . ' '; }
								if (isset($_POST['p_od']) && $_POST['p_od'] == 1) { $titcal .= 'OD Fresco '; }
								if (isset($_POST['p_don']) && $_POST['p_don'] == 1) { $titcal .= 'DONACION Fresco '; }
								if (isset($_POST['p_cri']) && $_POST['p_cri'] == 1) { $titcal .= 'CRIO OVOS '; }
								if (isset($_POST['des_dia']) && $_POST['des_dia'] === 0) { $titcal .= 'DESCONGELA OVO '; }
								if (isset($_POST['des_dia']) && $_POST['des_dia'] > 0) { $titcal .= 'DESCONGELA EMBRION '; }

								// consultar si la fecha de aspiracion esta blanco
								$stmt = $db->prepare("SELECT codigo from googlecalendar where tipoprocedimiento_id = 1 and estado = 1 and procedimiento_id = ?");
								$stmt->execute(array($_POST['idx']));

								require($_SERVER["DOCUMENT_ROOT"]."/config/environment.php");

								$stmt1 = $db->prepare("SELECT nombre minutos FROM man_turno_reproduccion WHERE id = ? AND estado = 1 ORDER BY nombre;");
								$stmt1->execute([$_POST['idturno']]);
								$data_minutos = $stmt1->fetch(PDO::FETCH_ASSOC);

								if ($stmt1->rowCount() == 0) {
										$data_minutos["minutos"] = "0";
								}

								if ($stmt->rowCount() == 0) {
									$googlecalendar = google_cal(
										$titcal.$_POST['nombre'].' ('.$login.')',
										$_POST['obs'],
										$_POST['f_asp'].':00.000-05:00',
										date('Y-m-d', strtotime($_POST['f_asp'].' + '.$data_minutos["minutos"].' minute')).'T'.date('H:i:s', strtotime($_POST['f_asp'].' + '.$data_minutos["minutos"].' minute')).'.000-05:00',
										$_ENV["googlecalendar_id"],
										$_ENV["googlecalendar_accountname"],
										$_ENV["googlecalendar_keyfilelocation"],
										'inmater-app',
										$_ENV["googlecalendar_adicionales"]
									);

									$stmt = $db->prepare("INSERT INTO googlecalendar (tipoprocedimiento_id, procedimiento_id, codigo, html_link, idusercreate) values (?, ?, ?, ?, ?)");
									$stmt->execute([1, $_POST['idx'], $googlecalendar->id, $googlecalendar->htmlLink, $login]);
								} 
						}

						$anestesia = isset($_POST['anestesia']) && !empty($_POST['anestesia']) ? $_POST['anestesia'] : null;
                        

						updateRepro($_POST['p_dnix'], $_POST['t_muex'], $_POST['idx'], $_POST['eda'], isset($_POST['p_dni_het']) ? $_POST['p_dni_het'] : '', $_POST['poseidon'], isset($_POST['p_dtri']) ? $_POST['p_dtri'] : null, isset($_POST['p_cic']) ? $_POST['p_cic'] : null, isset($_POST['p_fiv']) ? $_POST['p_fiv'] : null, isset($_POST['p_icsi']) ? $_POST['p_icsi'] : null, isset($_POST['p_od']) ? $_POST['p_od'] : null, isset($_POST['p_don']) ? $_POST['p_don'] : null, isset($_POST['p_cri']) ? $_POST['p_cri'] : null, isset($_POST['p_iiu']) ? $_POST['p_iiu'] : null, $_POST['p_extras'], isset($_POST['n_fol']) && !empty($_POST['n_fol']) ? $_POST['n_fol'] : 0, isset($_POST['fur']) ? $_POST['fur'] : null, isset($_POST['fecha_lorelina']) ? $_POST['fecha_lorelina'] : null, $_POST['f_aco'], $_POST['fsh'], $_POST['lh'], $_POST['est'], $_POST['prol'], isset($_POST['ins']) ? $_POST['ins'] : '', $_POST['amh'], $_POST['inh'], $_POST['t3'], $_POST['t4'], $_POST['tsh'], $_POST['m_agh'], $_POST['m_vdrl'], $_POST['m_clam'], $_POST['m_his'], $_POST['m_hsg'], $_POST['f_fem'], $_POST['f_mas'], $con_iny, $_POST['obs'], $_POST['motivo_cancelacion'], $_POST['f_iny'], !!$_POST['h_iny'] ? $_POST['h_iny'] : '', $_POST['f_asp'], isset($_POST['cancela']) ? $_POST['cancela'] : 0, $_POST['repro'], $_POST['complicacionesparto_id'], $_POST['complicacionesparto_motivo'],$anestesia, $login, $_POST['idturno']);

						if (isset($_POST['p_iiu']) && $_POST['p_iiu'] > 0 && $_POST['f_cap'] && $_POST['fec_h'] && $_POST['fec_m'])
						{
								$h_cap = $_POST['fec_h'].':'.$_POST['fec_m'];
								$stmt2 = $db->prepare("INSERT INTO lab_andro_cap (p_dni,fec,h_cap,iiu) VALUES (?,?,?,?)");
								$stmt2->execute(array($_POST['p_dni'], $_POST['f_cap'], $h_cap, $_POST['idx']));
						}

						if (isset($_POST['p_don']) && $_POST['p_don'] == 1 && $_POST['f_asp'] <> '' && $_POST['recep_num'] > 0)
						{
								for ($p = 1; $p <= $_POST['recep_num']; $p++) {
										$stmt2 = $db->prepare("UPDATE hc_reprod SET f_iny=?, h_iny=?, f_asp=?, idturno=?, iduserupdate=?, updatex=?  WHERE id=?");
                                        $hora_actual = date("Y-m-d H:i:s");
										$stmt2->execute(array($_POST['f_iny'], $_POST['h_iny'], $_POST['f_asp'], $_POST['idturno'], $login, $hora_actual, $_POST['recep'.$p])); //Fija la f_iny y f_asp para las receptoras

										$log_Reprod = $db->prepare(
                                            "INSERT INTO appinmater_log.hc_reprod (
                                                        reprod_id, dni, p_dni, t_mue, p_dni_het, fec, med, eda, poseidon,
                                                        p_dtri, p_cic, p_fiv, p_icsi, des_dia, des_don, p_od, p_don, don_todo, p_cri, 
                                                        p_iiu, p_extras, p_notas, n_fol, fur, f_aco, fsh, lh, est, prol, ins, t3, t4,
                                                        tsh, amh, inh, m_agh, m_vdrl, m_clam, m_his, m_hsg, f_fem, f_mas, con_fec, con_od,
                                                        con_oi, con_end,
                                                        con1_med, 
                                                        con2_med, 
                                                        con3_med, 
                                                        con4_med, 
                                                        con5_med, 
                                                        con_iny, con_obs, obs, f_iny, h_iny, f_asp, idturno, f_tra, h_tra,
                                                        complicacionesparto_id, complicacionesparto_motivo, idturno_tra, cancela,
                                                        pago_extras, pago_notas, pago_obs, repro, 
                                                        idusercreate, createdate, action
                                                )
                                            SELECT 
                                                id, dni, p_dni, t_mue, p_dni_het, fec, med, eda, poseidon,
                                                p_dtri, p_cic, p_fiv, p_icsi, des_dia, des_don, p_od, p_don, don_todo, p_cri, 
                                                p_iiu, p_extras, p_notas, n_fol, fur, f_aco, fsh, lh, est, prol, ins, t3, t4,
                                                tsh, amh, inh, m_agh, m_vdrl, m_clam, m_his, m_hsg, f_fem, f_mas, con_fec, con_od, 
                                                con_oi, con_end,
                                                con1_med, 
                                                con2_med, 
                                                con3_med, 
                                                con4_med, 
                                                con5_med,
                                                con_iny, con_obs, obs, f_iny, h_iny, f_asp, idturno, f_tra, h_tra,
                                                complicacionesparto_id, complicacionesparto_motivo, idturno_tra, cancela,
                                                pago_extras, pago_notas, pago_obs, repro, 
                                                iduserupdate, updatex, 'U'
                                            FROM appinmater_modulo.hc_reprod
                                            WHERE id=?");
										$log_Reprod->execute(array($_POST['recep'.$p]));
                                        


								}
					    } ?>
        <script type="text/javascript">
        var x = "<?php echo $_POST['dni']; ?>";
        window.parent.location.href = "n_repro.php?id=" + x;
        </script>
        <?php } else { ?>
        <script type="text/javascript">
        var c = "<?php echo $coincidencias; ?>";
        alert('Existe(n) ' + c + ' cruce(s) de horario. Vuelva a elegir otra fecha.');
        reload();
        </script>
        <?php } ?>
        <?php }

        if ($_GET['id'] <> "") {

            $stmt_medicamentos = $farma->prepare("SELECT id value, producto text from tblproducto where estado = 1 order by producto asc");
            $stmt_medicamentos->execute();
            $data_medicamentos = $stmt_medicamentos->fetchAll();

            // hora limite programacion
            $consulta = $db->prepare("SELECT valor FROM man_configuracion WHERE codigo=?");
            $consulta->execute(array('fecha_programacion'));
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
            //
            

            $rRepro = $db->prepare("SELECT * FROM hc_reprod WHERE estado = true and id=?");
            $rRepro->execute(array($id));
            $repro = $rRepro->fetch(PDO::FETCH_ASSOC);

            $rPaci = $db->prepare("SELECT hc_paciente.san, nom, ape, m_ets, g_agh, g_his, don, fnac FROM hc_antece, hc_paciente WHERE hc_paciente.dni = hc_antece.dni AND hc_paciente.dni=?");
            $rPaci->execute(array($repro['dni']));
            $paci = $rPaci->fetch(PDO::FETCH_ASSOC);

            $Rperfi = $db->prepare("SELECT * FROM hc_antece_perfi WHERE dni=? ORDER BY fec ASC");
            $Rperfi->execute(array($repro['dni']));
            
            $fsh="";
            $lh="";
            $est="";
            $prol="";
            $ins="";
            $t3="";
            $t4="";
            $tsh="";
            $amh="";
            $inh="";
            while ($perfi = $Rperfi->fetch(PDO::FETCH_ASSOC)) {
                if ($perfi['fsh'] <> "") { $fsh = $perfi['fsh']; }
                if ($perfi['lh'] <> "") { $lh = $perfi['lh']; }
                if ($perfi['est'] <> "") { $est = $perfi['est']; }
                if ($perfi['prol'] <> "") { $prol = $perfi['prol']; }
                if ($perfi['ins'] <> "") { $ins = $perfi['ins']; }
                if ($perfi['t3'] <> "") { $t3 = $perfi['t3']; }
                if ($perfi['t4'] <> "") { $t4 = $perfi['t4']; }
                if ($perfi['tsh'] <> "") { $tsh = $perfi['tsh']; }
                if ($perfi['amh'] <> "") { $amh = $perfi['amh']; }
                if ($perfi['inh'] <> "") { $inh = $perfi['inh']; }
            }

            $hsghes = $db->prepare("SELECT con FROM hc_antece_hsghes WHERE dni=? AND tip='HSG' and estado = true ORDER BY fec DESC LIMIT 1");
            $hsghes->execute(array($repro['dni']));
            $hsg = $hsghes->fetch(PDO::FETCH_ASSOC);
            $rPare = $db->prepare("SELECT p_dni,p_nom,p_ape FROM hc_pareja WHERE p_dni=? ORDER BY p_ape DESC");
            $rPare->execute(array($repro['p_dni']));
            $pare = $rPare->fetch(PDO::FETCH_ASSOC);

            if ($repro['p_dni'] == "" || $repro['p_dni'] == '1') { $pareja = "SOLTERA"; } else { $pareja = $pare['p_ape']." ".$pare['p_nom']; }

            $rAspi = $db->prepare("SELECT pro FROM lab_aspira WHERE lab_aspira.rep=? and lab_aspira.estado is true");
            $rAspi->execute(array($id));

            if ($rAspi->rowCount() > 0) { $lock = 1; }
						else { $lock = 0; }

            $rLegal = $db->prepare("SELECT
                legal.*, tipo.nombre nombretipodocumento, restricciones.tipo_vencimiento, restricciones.vencimiento, CASE p.nom IS NOT NULL WHEN true THEN 1 ELSE 0 END es_paciente
                FROM hc_legal_01 legal
                LEFT JOIN hc_paciente p on p.dni = legal.iduserupdate
                INNER JOIN man_legal_tipodocumento tipo ON tipo.codigo = legal.idlegaltipodocumento
                INNER JOIN restricciones ON restricciones.idtipo = CAST(legal.idlegaltipodocumento AS INTEGER)
                WHERE legal.estado = 1 AND legal.numerodocumento = ? AND restricciones.nombre = 'legal'
                ORDER BY legal.id DESC");

            $rLegal->execute([$repro['dni']]);
            $legal = $rLegal->fetch(PDO::FETCH_ASSOC);
            $tiene_legal = !!$legal;

            if ( $tiene_legal && $legal['tipo_vencimiento'] == 'procedimientos' ) {
							$intervenciones = $db->prepare("SELECT * FROM hc_reprod WHERE estado = true and dni=? and fec >= '". $legal['finforme'] ."'");
							$intervenciones->execute(array($repro['dni']));
							$tiene_legal = $intervenciones->rowCount() > $legal['vencimiento'] ? false : $legal;
            }

            $es_ted = !is_null($repro['des_dia']);
            $sero = getSero($repro[( $es_ted ? 'p_dni' : 'dni')], $repro['fec'], ( $es_ted ? 2 : 1) );

            function print_sero($tipo, $nombre) {
                global $repro;
                global $sero;

                $link_sero = 'archivos_hcpacientes.php?idArchivo=sero_' . $repro['dni'] . "_" . $sero[$tipo.'fec'];
                return '<tr>
                    <td>
                        '.$nombre.'
                    </td>
                    <td>
                        '.($sero[$tipo] == 1 ? 'Positivo' : ($sero[$tipo] == 2 ? 'Negativo' : '-')).'
                    </td>
                    <td>
                        '.($sero[$tipo] == 0 || !file_exists('analisis/sero_' . $repro['dni'] . "_" . $sero[$tipo.'fec'].".pdf") ? '-' : '<a href="'.$link_sero.'" target="_blank">Ver/Descargar</a>') .'
                    </td>
                    <td>
                        '.$sero[$tipo.'fec'].'
                    </td>
                    <td>
                        '.($sero[$tipo] == 0 ? '-' : (!$sero[$tipo.'vencido'] ? 'Vigente' : '<b>Vencido</b>')).'
                    </td>
                </tr>';
            }

            $Hema = $db->prepare("SELECT *,
                CASE WHEN CAST(fresultado as date) >= CAST(? as date) - INTERVAL '102 days' THEN false
                    ELSE true
                END as vencido
                FROM hc_hematologia WHERE estado = 1 and tipopaciente=1 and numerodocumento=? order by fresultado desc");

            $Hema->execute(array($repro['fec'], $repro['dni']));

            $hema = $Hema->fetch(PDO::FETCH_ASSOC);

            if( !!$hema ) {
                $link_hema = 'hematologia/'. $repro['dni'] . '/' . $hema['documento'];
            }

            $riesgo_quirurgico = $db->prepare("SELECT hc_riesgo_quirurgico.*, restricciones.tipo_vencimiento, restricciones.vencimiento, CASE p.nom IS NOT NULL WHEN true THEN 1 ELSE 0 END es_paciente,
                CASE
                    WHEN restricciones.tipo_vencimiento = 'dias' AND CAST(fvigencia as date) >= CAST(? as date) - (restricciones.vencimiento + 12) * INTERVAL '1 DAY' THEN false
                    WHEN restricciones.tipo_vencimiento = 'no_vence' THEN false
                    ELSE true
                END as vencido
                FROM hc_riesgo_quirurgico
                LEFT JOIN hc_paciente p on p.dni = hc_riesgo_quirurgico.iduserupdate
                INNER JOIN restricciones ON restricciones.nombre = 'riesgo_quirurgico'
                WHERE hc_riesgo_quirurgico.estado = 1 and numerodocumento = ?
                order by hc_riesgo_quirurgico.id DESC");

            $riesgo_quirurgico->execute([$repro['fec'], $repro['dni']]);
            $riesgo_quirurgico = $riesgo_quirurgico->fetch(PDO::FETCH_ASSOC);
            $tiene_pareja = $repro['p_dni_het'] == '' && (($repro['p_dni'] != '' && $repro['p_dni'] != '1') || $repro['p_icsi'] == '1' || $repro['p_fiv'] == '1');
            $es_descongelacion = !is_null($repro['des_dia']) && $repro['des_dia'] >= 0;

            $necesita_andrologia = $tiene_pareja;

            if ($necesita_andrologia) {
                $espermatograma = $db->prepare("SELECT lab_andro_esp.*, restricciones.tipo_vencimiento, restricciones.vencimiento,
                CASE
                    WHEN restricciones.tipo_vencimiento = 'dias' AND CAST(fec as date) >= CAST(? as date) - (restricciones.vencimiento + 12) * INTERVAL '1 day' THEN false
                    WHEN restricciones.tipo_vencimiento = 'no_vence' THEN false
                    ELSE true
                END as vencido
                    FROM lab_andro_esp
                    INNER JOIN restricciones ON restricciones.nombre = 'andrologia' AND restricciones.tipo = 'espermatograma' WHERE p_dni=? ORDER BY fec DESC");
                $espermatograma->execute([$repro['fec'], $repro['p_dni']]);

                $espermatograma = $espermatograma->fetch(PDO::FETCH_ASSOC);

                $espermacultivo = $db->prepare("SELECT hc_analisis.*, restricciones.tipo_vencimiento, restricciones.vencimiento,
                    CASE
                        WHEN restricciones.tipo_vencimiento = 'dias' AND CAST(a_mue AS DATE) >= CAST(? AS DATE) - (restricciones.vencimiento + 12) * INTERVAL '1 DAY' THEN false
                        WHEN restricciones.tipo_vencimiento = 'no_vence' THEN false
                        ELSE true
                    END as vencido
                        FROM hc_analisis
                        INNER JOIN restricciones ON restricciones.nombre = 'andrologia' AND restricciones.tipo = 'espermacultivo' WHERE a_exa = 'ESPERMACULTIVO' AND a_dni=? ORDER BY a_mue DESC");
                $espermacultivo->execute([$repro['fec'], $repro['p_dni']]);
                $espermacultivo = $espermacultivo->fetch(PDO::FETCH_ASSOC);

            }

            $receptora = $repro['p_dni_het'] != '' || $repro['des_don'] <> null;
            $es_donante = $paci['don'] == 'D';
            $necesita_psicologico = $receptora || $es_donante;

            if ($receptora):
                $psicologico = $db->prepare("SELECT hc_analisis.*, restricciones.tipo_vencimiento, restricciones.vencimiento,
                    CASE
                        WHEN restricciones.tipo_vencimiento = 'dias' AND CAST(a_mue as date) >= CAST(? as date) - (restricciones.vencimiento + 12) * INTERVAL '1 DAY' THEN false
                        WHEN restricciones.tipo_vencimiento = 'no_vence' THEN false
                        ELSE true END as vencido, CASE p.nom IS NOT NULL WHEN true THEN 1 ELSE 0 END es_paciente
                    FROM hc_analisis
                    LEFT JOIN hc_paciente p on p.dni = hc_analisis.iduserupdate
                    INNER JOIN restricciones ON restricciones.nombre = 'psicologico'
                    WHERE hc_analisis.estado = 1 AND a_exa = 'Examen Psicologico' AND a_dni=?
                    ORDER BY hc_analisis.id DESC");
                $psicologico->execute([$repro['fec'], $repro['dni']]);
                $psicologico = $psicologico->fetch(PDO::FETCH_ASSOC);
            elseif($es_donante):
                $psicologico = $db->prepare("SELECT hc_analisis.*, restricciones.tipo_vencimiento_donante, restricciones.vencimiento_donante,
                    CASE
                        WHEN restricciones.tipo_vencimiento_donante = 'dias' AND CAST(a_mue as date) >= CAST(? as date) - (restricciones.vencimiento_donante + 12) * INTERVAL '1 DAY' THEN false
                        WHEN restricciones.tipo_vencimiento_donante = 'no_vence' THEN false
                        ELSE true END as vencido, CASE p.nom IS NOT NULL WHEN true THEN 1 ELSE 0 END es_paciente
                    FROM hc_analisis
                    LEFT JOIN hc_paciente p on p.dni = hc_analisis.iduserupdate
                    INNER JOIN restricciones ON restricciones.nombre = 'psicologico'
                    WHERE hc_analisis.estado = 1 AND a_exa = 'Examen Psicologico' AND a_dni=?
                    ORDER BY hc_analisis.id DESC");
                $psicologico->execute([$repro['fec'], $repro['dni']]);
                $psicologico = $psicologico->fetch(PDO::FETCH_ASSOC);
            endif;

            $cariotipo = $db->prepare("SELECT hc_cariotipo.*, restricciones.tipo_vencimiento_donante, restricciones.vencimiento_donante,
                CASE
                    WHEN restricciones.tipo_vencimiento_donante = 'dias' AND CAST(fvigencia as date) >= CAST(? as date) - (restricciones.vencimiento_donante + 12) * INTERVAL '1 DAY' THEN false
                    WHEN restricciones.tipo_vencimiento_donante = 'no_vence' THEN false
                    ELSE true
                END as vencido, CASE p.nom IS NOT NULL WHEN true THEN 1 ELSE 0 END es_paciente
                FROM hc_cariotipo
                LEFT JOIN hc_paciente p on p.dni = hc_cariotipo.iduserupdate
                INNER JOIN restricciones ON restricciones.nombre = 'cariotipo'
                WHERE hc_cariotipo.estado = 1 and numerodocumento = ?
                ORDER BY hc_cariotipo.id DESC");

            $cariotipo->execute([$repro['fec'], $repro['dni']]);
            $cariotipo = $cariotipo->fetch(PDO::FETCH_ASSOC);
            ?>
        <script>
        $(document).ready(function() {
            $("#aspira_block").hide();
            <?php
                    if ($repro['p_iiu'] > 0) { ?>
            $('#p_fiv,#p_icsi,#p_cri,#p_don').checkboxradio("disable");
            $('#p_od,#p_des').selectmenu("disable");
            <?php } ?>

            <?php
                    if (!is_null($repro['des_dia']) && $repro['des_dia'] >= 0) { ?>
            $("#aspira_block").show();
            <?php } ?>

            <?php
                    if ($lock == 1) { ?>
            $("#form1 :input").attr("disabled", true);
            <?php } ?>

            <?php
                    if ($repro['f_asp'] <> "") { ?>
            $("#aspira_block").show();
            $("#fec_iny_activo").val(1);
            <?php } ?>

            <?php
                    if ($repro['p_dni_het'] <> "") { ?>
            $(".hetes").show();
            $(".hetes2,.sel_het,.ui-input-search").hide();
            $("#<?php echo $repro['p_dni_het'];?>").show();
            <?php } ?>

            $("#con_iny0").change(function() {
                if ($(this).val() == "") {
                    $("#fec_iny_activo").val(0);
                    $("#aspira_block").hide();
                } else {
                    if ($('#p_iiu').prop('checked')) {} else {
                        alert(
                            "Ahora debe ingresar la Fecha de Inyección y revisar la disponibilidad en la Sala de Aspiraciones!"
                        );
                        $("#fec_iny_activo").val(1);
                        $("#aspira_block").show();
                    }
                }
            });
        });
        </script>
        <div data-role="header" data-position="fixed">
            <a href="n_repro.php?id=<?php echo $repro['dni']; ?>" rel="external" class="ui-btn">Cerrar</a>
            <h2><?php echo "<small>(".date("d-m-Y", strtotime($repro['fec'])).")</small> ".$paci['ape']." ".$paci['nom']." / <small>".$pareja."</small>"; ?>
            </h2>
        </div>
        <div class="ui-content" role="main">
            <?php $legal_vencida = false ?>
            <?php $andrologia_vencido = false ?>
            <?php $riesgo_vencido = false ?>
            <?php $psicologico_vencido = false ?>
            <?php $cariotipo_vencido = false ?>

            <?php $legal_vencida = (!!$legal && !$tiene_legal) || !$legal ?>
            <?php
                    $legal_class = "vencida";
                    $legal_icon = "warning";

                    if (!$legal_vencida && $legal["es_paciente"] == 0) {
                        $legal_class = "vigente";
                        $legal_icon = "circle-check";
                    }

                    if (!$legal_vencida && $legal["es_paciente"] == 1) {
                        $legal_class = "pendiente";
                        $legal_icon = "warning";
                    }
                ?>
            <div id="legal" class="alarma <?php echo $legal_class ?>">
                <span class="oi" data-glyph="<?php echo $legal_icon ?>"></span>
                <b><a href="restriccion_legal.php?repro_id=<?php echo $repro['id'] ?>&dni=<?php echo $repro['dni'] ?>"
                        data-transition="pop" rel="external" style="text-decoration: none;">Legal</a></b>
            </div>
            <?php
                $analisis_vencido = !$sero
                || !!@$sero['hbsvencido']
                || !!@$sero['hcvvencido']
                || !!@$sero['hivvencido']
                || !!@$sero['rprvencido']
                || !!@$sero['rubvencido']
                || !!@$sero['toxvencido']
                || !!@$sero['cla_gvencido']
                || !!@$sero['cla_mvencido']
                || @$sero['hbs'] == 1
                || @$sero['hcv'] == 1
                || @$sero['hiv'] == 1
                || @$sero['rpr'] == 1
                || @$sero['rub'] == 1
                || @$sero['tox'] == 1
                || @$sero['cla_g'] == 1
                || @$sero['cla_m'] == 1
                ?>
            <?php
                    $analisis_class = "vencida";
                    $analisis_icon = "warning";

                    if (!$analisis_vencido && $sero["es_paciente"] == 0) {
                        $analisis_class = "vigente";
                        $analisis_icon = "circle-check";
                    }

                    if (!$analisis_vencido && $sero["es_paciente"] == 1) {
                        $analisis_class = "pendiente";
                        $analisis_icon = "warning";
                    }
                ?>
            <?php if ( !$es_ted ): ?>
            <div id="analisis" class="alarma <?php echo $analisis_class ?>">
                <span class="oi" data-glyph="<?php echo $analisis_icon ?>"></span>

                <b><a href="restriccion_analisis_clinico.php?tipopaciente=1&repro_id=<?php echo $repro['id'] ?>&dni=<?php echo $repro['dni'] ?>"
                        data-transition="pop" rel="external" style="text-decoration: none;">Análisis
                        clínicos</a>&nbsp;♀</b>
            </div>
            <?php else: ?>
            <div id="analisis" class="alarma <?php echo $analisis_class ?>">
                <span class="oi" data-glyph="<?php echo $analisis_icon ?>"></span>

                <b><a href="restriccion_analisis_clinico.php?tipopaciente=2&repro_id=<?php echo $repro['id'] ?>&dni=<?php echo $repro['p_dni'] ?>"
                        data-transition="pop" rel="external" style="text-decoration: none;">Análisis
                        clínicos</a>&nbsp;♂</b>
            </div>
            <?php endif ?>

            <?php if( $necesita_andrologia ) : ?>
            <?php $andrologia_vencido = !$espermatograma || !!@$espermatograma['vencido'] || !$espermacultivo || !!@$espermacultivo['vencido'] || ( !!$espermacultivo && $espermacultivo['a_sta'] != 'Negativo' )
                         ?>
            <div id="riesgo-quirurgico" class="alarma <?php echo $andrologia_vencido ? 'vencida' : 'vigente' ?>">
                <span class="oi" data-glyph="<?php echo $andrologia_vencido ? 'warning' : 'circle-check' ?>"></span>
                <b><a href="restriccion_andrologia.php?repro_id=<?php echo $repro['id'] ?>" data-transition="pop"
                        rel="external" style="text-decoration: none;">Andrología</a></b>
            </div>
            <?php endif ?>

            <?php if( is_null( $repro['des_dia'] ) && !$receptora ): ?>
            <?php $riesgo_vencido = !$riesgo_quirurgico || !!@$riesgo_quirurgico['vencido'] ?>
            <?php
                        $riesgo_class = "vencida";
                        $riesgo_icon = "warning";

                        if (!$riesgo_vencido && $riesgo_quirurgico["es_paciente"] == 0) {
                            $riesgo_class = "vigente";
                            $riesgo_icon = "circle-check";
                        }

                        if (!$riesgo_vencido && $riesgo_quirurgico["es_paciente"] == 1) {
                            $riesgo_class = "pendiente";
                            $riesgo_icon = "warning";
                        } ?>

            <div id="riesgo-quirurgico" class="alarma <?php echo $riesgo_class ?>">
                <span class="oi" data-glyph="<?php echo $riesgo_icon ?>"></span>
                <b><a href="restriccion_riesgo_quirurgico.php?repro_id=<?php echo $repro['id'] ?>&dni=<?php echo $repro['dni'] ?>"
                        data-transition="pop" rel="external" style="text-decoration: none;">Riesgo quirúrgico</a></b>
            </div>
            <?php endif ?>

            <?php if($necesita_psicologico) : ?>
            <?php $psicologico_vencido = !$psicologico || !!@$psicologico['vencido'] || (!!$psicologico && $psicologico['a_sta'] != 'Positivo') ?>
            <?php
                        $psicologico_class = "vencida";
                        $psicologico_icon = "warning";

                        if (!$psicologico_vencido && $psicologico["es_paciente"] == 0) {
                            $psicologico_class = "vigente";
                            $psicologico_icon = "circle-check";
                        }

                        if (!$psicologico_vencido && $psicologico["es_paciente"] == 1) {
                            $psicologico_class = "pendiente";
                            $psicologico_icon = "warning";
                        }
                    ?>

            <div id="riesgo-quirurgico" class="alarma <?php echo $psicologico_class ?>">
                <span class="oi" data-glyph="<?php echo $psicologico_icon ?>"></span>
                <b><a href="restriccion_psicologico.php?repro_id=<?php echo $repro['id'] ?>" data-transition="pop"
                        rel="external" style="text-decoration: none;">Examen psicológico</a></b>
            </div>
            <?php endif ?>

            <?php
                    if ($es_donante): ?>
            <?php $cariotipo_vencido = !$cariotipo || !!@$cariotipo['vencido'] ?>
            <?php
                        $cariotipo_class = "vencida";
                        $cariotipo_icon = "warning";

                        if (!$cariotipo_vencido && $cariotipo["es_paciente"] == 0) {
                            $cariotipo_class = "vigente";
                            $cariotipo_icon = "circle-check";
                        }

                        if (!$cariotipo_vencido && $cariotipo["es_paciente"] == 1) {
                            $cariotipo_class = "pendiente";
                            $cariotipo_icon = "warning";
                        }
                    ?>
            <div id="cariotipo" class="alarma <?php echo $cariotipo_class ?>">
                <span class="oi" data-glyph="<?php echo $cariotipo_icon ?>"></span>
                <b><a href="restriccion_cariotipo.php?repro_id=<?php echo $repro['id'] ?>&dni=<?php echo $repro['dni'] ?>"
                        data-transition="pop" rel="external" style="text-decoration: none;">Cariotipo</a></b>
            </div>
            <?php endif ?>
            <?php
                $key=$_ENV["apikey"];
                ?>
            <input type="hidden" name="login" id="login" value="<?php echo $login;?>">
            <input type="hidden" name="key" id="key" value="<?php echo $key;?>">
            <form action="" method="post" data-ajax="false" id="form1" name="form1">
                <input type="hidden" name="fecha_programacion" id="fecha_programacion"
                    value="<?php print($data['valor']); ?>">
                <input type="hidden" name="idx" value="<?php echo $repro['id']; ?>">
                <input type="hidden" name="dni" value="<?php echo $repro['dni']; ?>">
                <input type="hidden" name="nombre" value="<?php echo $paci['ape']." ".$paci['nom']; ?>">
                <input type="hidden" name="fec_iny_activo" id="fec_iny_activo">
                <input type="hidden" name="des_dia" id="des_dia" value="<?php echo $repro['des_dia']; ?>">
                <div class="medico-tratante-titulo">
                <strong>Médico Tratante: </strong>
                <select name="m_tratante" id="m_tratante" data-mini="true" required <?='onchange="updateMedicamento(this,\''.$id.'\',\''.$login.'\',\'med\');"'?>>
                    <optgroup label="Lista de Medicos">
                        <option value="">Seleccionar</option>
                        <?php
                          $medicos = listarMedicos();
                          foreach ($medicos as $medico) {
                            $selected = '';
                            if (listarMedicosByCodigo($repro['med']) == strtoupper($medico['nombre'])) {
                              $selected = 'selected';
                            }
                            echo '<option value="'.$medico['codigo'].'"'.$selected.'>'.strtoupper($medico['nombre']).'</option>';
                          }
                        ?>
                    </optgroup>
                </select></div>
                <div data-role="collapsibleset" data-theme="a" data-content-theme="a" data-mini="true">
                    <div data-role="collapsible">
                        <h3>Procedimientos / Extras</h3>
                        <table width="100%" align="center" style="margin: 0 auto;">
                            <tr>
                                <td>
                                    <select name="p_dnix" id="p_dnix" data-mini="true" data-inline="true">
                                        <option value="">SELECCIONAR</option>
                                        <?php
                                                $rPP = $db->prepare("SELECT p_dni from hc_pare_paci where dni=? ORDER BY p_fec DESC");
                                                $rPP->execute(array($repro['dni']));
                                                $indpareja=false;

                                                while ($pp = $rPP->fetch(PDO::FETCH_ASSOC)) {
                                                    $p_dni_selected="";
                                                    $rPare = $db->prepare("SELECT p_nom, p_ape from hc_pareja where p_dni=?");
                                                    $rPare->execute(array($pp['p_dni']));
                                                    $data = $rPare->fetch(PDO::FETCH_ASSOC);

                                                    if ($repro['p_dni'] == $pp['p_dni']) {
                                                        $indpareja=true;
                                                        $p_dni_selected="selected";
                                                    }

                                                    print("<option value=".$pp['p_dni']." $p_dni_selected>" . mb_strtoupper($data['p_ape']) . " " . mb_strtoupper($data['p_nom'])."</option>");
                                                }

                                                $p_dni_selected="";

                                                if ($indpareja == false) {
                                                    $p_dni_selected="selected";
                                                }

                                                print("<option value='1' $p_dni_selected>SOLTERA</option>");
                                            ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <select name="t_muex" id="t_muex" data-mini="true" data-inline="true">
                                        <option value="">SELECCIONE TIPO DE MUESTRA</option>
                                        <option value=1 <?php if ($repro['t_mue'] == 1) {print("selected");} ?>>MUESTRA FRESCA</option>
                                        <option value=2 <?php if ($repro['t_mue'] == 2) {print("selected");} ?>>MUESTRA CONGELADA</option>
                                        <option value=4 <?php if ($repro['t_mue'] == 4) {print("selected");} ?>>MUESTRA BANCO</option>
                                        <option value=3 <?php if ($repro['t_mue'] == 3) {print("selected");} ?>>NO APLICA</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <select name="select" id="repro_lista" data-mini="true" data-inline="true">
                                        <option value="" selected>SELECCIONE REPRODUCCIÓN ASISTIDA</option>
                                        <option value="NINGUNA">NINGUNA</option>
                                        <option value="borrar_p">*** BORRAR TODO ***</option>
                                        <optgroup label="Agrege Procedimientos:">
                                            <option value="FIV">FIV</option>
                                            <option value="OD">OD</option>
                                            <option value="SD">SD</option>
                                            <option value="EMBRIODONACION">EMBRIODONACION</option>
                                            <option value="TED">TED</option>
                                            <option value="CRIO OVOS">CRIO OVOS</option>
                                            <option value="IIU">IIU</option>
                                        </optgroup>
                                        <optgroup label="Agrege Extras">
                                            <option value="TRANSFERENCIA FRESCO">TRANSFERENCIA FRESCO</option>
                                            <option value="NGS">NGS</option>
                                            <option value="CRIO TOTAL">CRIO TOTAL</option>
                                            <option value="EMBRYOGLUE">EMBRYOGLUE</option>
                                            <option value="EMBRYOSCOPE">EMBRYOSCOPE</option>
                                            <option value="PICSI">PICSI</option>
                                            <option value="BANKING EMBRIONES">BANKING EMBRIONES</option>
                                        </optgroup>
                                    </select>
                                    <textarea name="repro" required id="repro"
                                        data-mini="true"><?php echo $repro['repro']; ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td width="63%">
                                    <div class="enlinea">
                                        <!-- Poseidon -->
                                        <select name="poseidon" id="poseidon" data-mini="true">
                                            <option value="" selected>SELECCIONAR POSEIDON</option>
                                            <?php
                                                $consulta = $db->prepare("select id, nombre from man_poseidon where estado = 1 order by nombre asc");
                                                $consulta->execute();
                                                while ($data = $consulta->fetch(PDO::FETCH_ASSOC)) {
                                                    $selected='';
                                                    if ($repro['poseidon'] == $data['id']) { $selected='selected'; }
                                                    print("<option value=".$data['id']." $selected>".mb_strtoupper($data['nombre'])."</option>");
                                                } ?>
                                        </select>
                                        <!-- Dual Trigger -->
                                        <input type="checkbox" name="p_dtri" id="p_dtri" data-mini="true" value=1
                                            <?php if ($repro['p_dtri'] == 1) { echo "checked"; } ?>>
                                        <label for="p_dtri">Dual Trigger</label>
                                        <!-- C. Natural -->
                                        <input type="checkbox" name="p_cic" id="p_cic" data-mini="true" value=1
                                            <?php if ($repro['p_cic'] == 1) { echo "checked"; } ?>>
                                        <label for="p_cic">C. Natural</label>
                                        <!-- FIV -->
                                        <input type="checkbox" name="p_fiv" id="p_fiv" data-mini="true" value=1 <?php if ($repro['p_fiv'] == 1) { echo "checked"; }
                                            if ($repro['des_dia'] >= 1) { echo " disabled"; } ?>><label
                                            for="p_fiv">FIV</label>
                                        <!-- ICSI -->
                                        <input type="checkbox" name="p_icsi" id="p_icsi" data-mini="true" value=1 <?php if ($repro['p_icsi'] == 1) { echo "checked"; }
                                            if ($repro['des_dia'] >= 1) { echo " disabled"; } ?>><label
                                            for="p_icsi"><?php print($_ENV["VAR_ICSI"]); ?></label>
                                        <!-- Crio Ovos -->
                                        <input type="checkbox" name="p_cri" id="p_cri" data-mini="true" value=1
                                            <?php if ($repro['p_cri'] == 1) { echo "checked"; }
                                            if (!is_null($repro['des_dia']) && $repro['des_dia'] >= 0) { echo " disabled"; } ?>><label for="p_cri">Crio Ovos</label>
                                        <?php
                                            if ($repro['p_iiu'] == 1) { ?>
                                        <input type="checkbox" name="p_iiu" id="p_iiu" data-mini="true" value=1
                                            checked><label for="p_iiu">IIU</label>
                                        <?php } ?>
                                        <?php if ($es_donante) { ?>
                                        <input type="checkbox" name="p_don" id="p_don" data-mini="true" value=1
                                            <?php if ($repro['p_don'] == 1) { echo "checked"; }
                                                if (!is_null($repro['des_dia']) && $repro['des_dia'] >= 0) { echo " disabled"; } ?>>
                                        <label for="p_don">Donación Fresco</label>
                                        <?php } else { // si es paciente puede ser receptora ---------
                                                $rDon = $db->prepare("SELECT dni,nom,ape,don,med FROM hc_paciente WHERE don=? ORDER BY ape ASC");
                                                $rDon->execute(array('D')); ?>
                                        <!-- OD Fresco -->
                                        <select name="p_od" id="p_od" data-mini="true"
                                            <?php if (!is_null($repro['des_dia']) && $repro['des_dia'] >= 0) { echo " disabled"; } ?>>
                                            <option value="">OD Fresco</option>
                                            <optgroup label="Seleccione la Donante:">
                                                <?php
                                                            while ($don = $rDon->fetch(PDO::FETCH_ASSOC)) { ?>
                                                <option value=<?php echo $don['dni'];
                                                                    if ($repro['p_od'] == $don['dni']) {
                                                                        echo " selected";
                                                                    } ?>>
                                                    <?php echo 'OD Fresco: '.$don['ape'].' '.$don['nom'].' ('.$don['med'].')'; ?>
                                                </option>
                                                <?php } ?>
                                            </optgroup>
                                        </select>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <select name="select" class="med_insert" title="p_extras" data-mini="true"
                                        data-inline="true">
                                        <option value="" selected>SELECCIONE EXTRAS</option>
                                        <option value="borrar_p">*** BORRAR TODO ***</option>
                                        <?php
                                                $consulta = $db->prepare("select nombre from man_extras_medico where estado = 1 order by nombre");
                                                $consulta->execute();
                                                while ($data = $consulta->fetch(PDO::FETCH_ASSOC)) {
                                                    print("<option value='".$data['nombre']."' $selected>".$data['nombre']."</option>");
                                                }
                                            ?>
                                    </select>
                                    <textarea name="p_extras" readonly id="p_extras"
                                        data-mini="true"><?php echo $repro['p_extras']; ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <?php
                                        if ($repro['p_don'] == 1) {
                                            $dado="";
                                            if($repro['f_asp']===null) $dado=" AND (hc_reprod.f_asp='' or hc_reprod.f_asp is null)";
                                            if($repro['f_asp']==='') $dado=" AND (hc_reprod.f_asp='' or hc_reprod.f_asp is null) ";
                                            if($repro['f_asp'] !== null && $repro['f_asp'] !== '') $dado = " AND hc_reprod.f_asp='" . $repro['f_asp'] . "' ";
                                            $rRecep = $db->prepare("SELECT hc_reprod.id,hc_paciente.nom,hc_paciente.ape
                                                FROM hc_reprod,hc_paciente
                                                WHERE hc_reprod.estado = true and hc_reprod.p_od=? $dado AND hc_paciente.dni=hc_reprod.dni
                                                ORDER BY hc_paciente.ape ASC");
                                            $rRecep->execute(array($repro['dni']));
                                            $recep_num = 0;
                                            print('RECEPTORA(S):<br>');

                                            if ($rRecep->rowCount() > 0) {
                                                while ($recep = $rRecep->fetch(PDO::FETCH_ASSOC)) {
                                                    $recep_num++;
                                                    echo '<b>'.$recep['ape'].' '.$recep['nom'].'</b><input type="hidden" name="recep'.$recep_num.'" value="'.$recep['id'].'"><br>';
                                                }
                                            }

                                            if ($recep_num == 0) { echo ' Vacio'; }
                                            echo '<input type="hidden" name="recep_num" id="recep_num" value="'.$recep_num.'">';
                                        }

                                        if ($es_descongelacion) {
                                            if ($repro['des_don'] == null && $repro['des_dia'] >= 1) {
                                                echo "<b>Descongelación:</b> TED (Dia ".$repro['des_dia'].")";
                                            }

                                            if ($repro['des_don'] == null && $repro['des_dia'] === 0) {
                                                echo "<b>Descongelación:</b> ÓVULOS PROPIOS";
                                            }

                                            if ($repro['des_don'] <> null) { // si son donandos
                                                if ($repro['des_dia'] === 0) { echo "<b>Descongelación:</b> ÓVULOS DONADOS"; }
                                                if ($repro['des_dia'] >= 1) { echo "<b>Descongelación:</b> EMBRIODONACIÓN (Dia ".$repro['des_dia'].")"; }
                                                $rPaciDon = $db->prepare("SELECT nom,ape FROM hc_paciente WHERE dni=?");
                                                $rPaciDon->execute(array($repro['des_don']));
                                                $paciDon = $rPaciDon->fetch(PDO::FETCH_ASSOC);
                                                echo " <b>Donante</b>: ".$paciDon['ape']." ".$paciDon['nom'];
                                            }
                                        }?>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div data-role="collapsible">
                        <h3>Perfil Hormonal / Etiologías</h3>
                        <table width="100%" align="center" style="margin: 0 auto;" class="peke">
                            <tr>
                                <td width="24%">FSH
                                    <input name="fsh" type="text" id="fsh"
                                        value="<?php if(isset($repro['f_asp']))if ($repro['f_asp'] <> "") { echo $repro['fsh']; } else { echo $fsh; } ?>"
                                        readonly data-mini="true">
                                </td>
                                <td width="28%">Prolac
                                    <input name="prol" type="text" id="prol"
                                        value="<?php if(isset($repro['f_asp']))if ($repro['f_asp'] <> "") { echo $repro['prol']; } else { echo $prol; } ?>"
                                        readonly data-mini="true">
                                </td>
                                <td width="27%">Tiroides T3
                                    <input name="t3" type="text" id="t3"
                                        value="<?php if(isset($repro['f_asp']))if ($repro['f_asp'] <> "") { echo $repro['t3']; } else { echo $t3; } ?>"
                                        readonly data-mini="true">
                                </td>
                                <td width="27%" rowspan="3" bgcolor="#E7E7E7">
                                    <p>Etiología Femenina
                                        <input type="text" name="f_fem" id="f_fem"
                                            value="<?php if(isset($repro['f_fem']))echo $repro['f_fem']; ?>" data-mini="true">
                                    </p>
                                    <p>Etiología Masculina
                                        <input type="text" name="f_mas" id="f_mas"
                                            value="<?php if(isset($repro['f_mas']))echo $repro['f_mas']; ?>" data-mini="true">
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td> LH
                                    <input name="lh" type="text" id="lh"
                                        value="<?php if(isset($repro['f_asp']))if ($repro['f_asp'] <> "") { echo $repro['lh']; } else { echo $lh; } ?>"
                                        readonly data-mini="true">
                                </td>
                                <td>AMH
                                    <input name="amh" type="text" id="amh"
                                        value="<?php if(isset($repro['f_asp']))if ($repro['f_asp'] <> "") { echo $repro['amh']; } else { echo $amh; } ?>"
                                        readonly data-mini="true">
                                </td>
                                <td>Tiroides T4
                                    <input name="t4" type="text" id="t4"
                                        value="<?php if(isset($repro['f_asp']))if ($repro['f_asp'] <> "") { echo $repro['t4']; } else { echo $t4; } ?>"
                                        readonly data-mini="true">
                                </td>
                            </tr>
                            <tr>
                                <td>Estradiol
                                    <input name="est" type="text" id="est"
                                        value="<?php if(isset($repro['f_asp']))if ($repro['f_asp'] <> "") { echo $repro['est']; } else { echo $est; } ?>"
                                        readonly data-mini="true">
                                </td>
                                <td>Inhibina B
                                    <input name="inh" type="text" id="inh"
                                        value="<?php if(isset($repro['f_asp']))if ($repro['f_asp'] <> "") { echo $repro['inh']; } else { echo $inh; } ?>"
                                        readonly data-mini="true">
                                </td>
                                <td>Tiroides Tsh
                                    <input name="tsh" type="text" id="tsh"
                                        value="<?php if(isset($repro['f_asp']))if ($repro['f_asp'] <> "") { echo $repro['tsh']; } else { echo $tsh; } ?>"
                                        readonly data-mini="true">
                                </td>
                            </tr>
                        </table>
                    </div>

                    <?php include ($_SERVER["DOCUMENT_ROOT"] . "/_componentes/ra_andrologia.php"); ?>

                    <div data-role="collapsible">
                        <h3>Mujer antecedentes</h3>
                        <table width="100%" align="center" style="margin: 0 auto;" class="peke">
                            <tr>
                                <td>AgHbs<input type="text" name="m_agh" id="m_agh"
                                        value=""
                                        readonly data-mini="true"></td>
                                <td width="26%">Histero.<input type="text" name="m_his" id="m_his"
                                        value=""
                                        readonly data-mini="true"></td>
                                <td width="24%">HSG<input type="text" name="m_hsg" id="m_hsg"
                                        value=""
                                        readonly data-mini="true"></td>
                                <td width="18%">VDRL
                                    <select name="m_vdrl" id="m_vdrl" data-mini="true">
                                        <option value="">SELECCIONAR</option>
                                        <option value="Positivo"
                                            <?php if ($repro['m_vdrl'] == "Positivo") { echo "selected"; } ?>>Positivo
                                        </option>
                                        <option value="Negativo"
                                            <?php if ($repro['m_vdrl'] == "Negativo") { echo "selected"; } ?>>Negativo
                                        </option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td width="20%">Grupo Sanguineo: <b><?php echo $paci['san']; ?></b></td>
                                <td>
                                    HIV
                                    <?php if (strpos($paci['m_ets'], "VIH") !== false) { echo '<font color="#F8080C">Positivo</font>'; } else { echo 'Negativo'; } ?>
                                </td>
                                <td>Clamidia<input type="text" name="m_clam" id="m_clam"
                                        <?php if (strpos($paci['m_ets'], "Clamidiasis") !== false) { echo 'value="Positivo"'; } else { echo 'value="Negativo"'; } ?>
                                        readonly data-mini="true"></td>
                                <td>Edad
                                    <input type="text" name="eda" id="eda"
                                        value="<?php if ($repro['eda']==0) { echo date_diff(date_create($paci['fnac']), date_create('today'))->y; } else { echo $repro['eda']; } ?>"
                                        data-mini="true">
                                </td>
                            </tr>
                        </table>
                    </div>
                    <?php
                            $puede_inyeccion =
                                !$legal_vencida
                                && ( !$analisis_vencido || $es_ted )
                                && ( !$necesita_andrologia || !$andrologia_vencido )
                                && !$riesgo_vencido
                                && ( !$necesita_psicologico || !$psicologico_vencido );
                            include('_includes/e_repro_cicloestimulacion.php');
                            include('_includes/e_repro_fechaprogramacion.php'); ?>
                </div>
                <?php
                    if ($repro['p_iiu'] > 0) {
                        $Rcap = $db->prepare("SELECT id,fec,p_dni,emb,h_cap FROM lab_andro_cap WHERE iiu=? and eliminado is false");
                        $Rcap->execute(array($repro['id']));
                        $cap = $Rcap->fetch(PDO::FETCH_ASSOC); ?>
                <div class="ui-block-b" style="width:20%; margin: 0 auto; float: none;">
                    <div class="ui-bar ui-bar-b" align="center">
                        Fecha de Capacitación:
                        <?php
                                if ($Rcap->rowCount() > 0) {
                                    echo date("d-m-Y", strtotime($cap['fec'])).' <small>'.$cap['h_cap'].'</small>';

                                    if ($cap['emb'] > 0) {
                                        print('<a href="info.php?t=cap&a='.$cap['p_dni'].'&b='.$cap['id'].'&c='.$repro['dni'].'" target="new">Ver Informe</a>');
                                    } else {
                                        echo '<br>Resultado Pendiente..';
                                    }
                                } else { ?>
                        <input type="date" name="f_cap" id="f_cap">
                        <select name="fec_h" id="fec_h" data-mini="true">
                            <option value="08">08 hrs</option>
                            <option value="09">09 hrs</option>
                            <option value="10">10 hrs</option>
                            <option value="11">11 hrs</option>
                            <option value="12">12 hrs</option>
                            <option value="13">13 hrs</option>
                            <option value="14">14 hrs</option>
                            <option value="15">15 hrs</option>
                            <option value="16">16 hrs</option>
                        </select>
                        <select name="fec_m" id="fec_m" data-mini="true">
                            <option value="00">00 min</option>
                            <option value="15">15 min</option>
                            <option value="30">30 min</option>
                            <option value="45">45 min</option>
                        </select>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>
                <span data-mini="true">Observaciones:</span>
                <textarea name="obs" id="obs"><?php echo $repro['obs']; ?></textarea>
                <div id="motivo_cancelacion_cont" style="display: none;">
                    <span data-mini="true">Motivo de Cancelacion:</span>
                    <textarea name="motivo_cancelacion" id="motivo_cancelacion"><?php echo $repro['motivo_cancelacion']; ?></textarea>
                </div>
                <!-- <div class="alarma vencida">
                        <span class="oi" data-glyph="warning"></span>
                        A partir del 10 de Mayo de 2019 no se podrá proceder sino se cumple con los requisitos de <?php echo implode(', ', $requisitos) ?>
                    </div> -->

                <?php
                        $medicoValidar = $db->prepare("SELECT usuario_creacion_id FROM ciclo_estimulacion_detalle where  hcreprod_id = ? AND eliminado=0 AND usuario_creacion_id = ? LIMIT 1;");
                        $medicoValidar->execute([$id, $login]);
                        $medicoValidado = $medicoValidar->fetch(PDO::FETCH_ASSOC);
                           
                    $div_mensaje = 'none';
                    $div_guardar = 'none';
                    if ($repro['med'] == $login || $repro['idusercreate'] == $login || !empty($medicoValidado) ) {
                        $div_guardar = 'block';
                    } else {
                        $div_mensaje = 'block';
                    } 

                    ?>
                    <div id ="bloqGuardar" style="display: <?php echo $div_guardar;?>;">
                        <?php 

                                if ($lock <> 1 && $repro['cancela'] <> 1) { //Puede editar hasta la fecha de inyeccion ?>
                        <div class="enlinea">
                            <?php
                                            $validator = "";

                                            print('<input type="Submit" value="GUARDAR DATOS" data-icon="check" data-iconpos="left" data-mini="true" class="show-page-loading-msg" data-textonly="false" data-textvisible="true" data-msgtext="Actualizando datos.." data-theme="b" data-inline="true" ' . $validator . '/>');
                                        ?>
                            <input type="checkbox" name="cancela" id="cancela" data-mini="true" value=1
                                <?php if ($repro['cancela'] == 1) { echo "checked"; } ?>>
                            <label for="cancela">Cancelar Reproducción</label>
                        </div>
                        <?php } else {
                                    if (is_null($repro['des_dia'])) { $titulo = "Aspiración"; } else { $titulo = "Descongelación"; }
                                    echo '<font color="#E34446"><b>SOLO LECTURA!</b> '.$titulo.' realizada, para modificaciones llamar a Jefa de Sala</font>';
                                }
                                ?>
                    </div>
                    <div id = "bloqMensaje" style="display: <?php echo $div_mensaje;?>;">
                        <?php 
                            echo '<br><br><font color="#E34446"><b>PERMISO DE EDICION SOLO PARA: </b> '.$repro['med'].'</font>';
                        ?>
                    </div>
            </form>
        </div>
        <?php } ?>
    </div>

    <?php include ($_SERVER["DOCUMENT_ROOT"] . "/_componentes/e_repro.php"); ?>

    <script>
    $(".chosen-select").chosen();
    </script>
</body>

</html>