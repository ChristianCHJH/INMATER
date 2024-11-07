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
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" + integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
    <link rel="stylesheet" href="css/e_repro.css?v=3" />
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

        .columnahc input,
        .columnahc textarea {
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
        window.addEventListener('popstate', function(event) {
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
        $ciclo = obtenerCicloEstimulacionDetalle($id);
        $iniciclo = inicioCicloEstimulacionDetalle($id);
        $total_semanas = 60;
        if (isset($_POST['idx']) && !empty(isset($_POST['idx']))) {
            $con_fec = "";
            if ($_POST['con_fec0'] <> "") {
                $con_fec = $_POST['con_fec0'];
                for ($i = 1; $i < $total_semanas; $i++) {
                    $con_fec = $con_fec . "|" . endCycle($_POST['con_fec0'], $i);
                }
            }

            if (isset($_POST['con_od0'])) $con_od = $_POST['con_od0'];
            if (isset($_POST['con_oi0'])) $con_oi = $_POST['con_oi0'];
            if (isset($_POST['con_end0'])) $con_end = $_POST['con_end0'];
            if (isset($_POST['con_obs0'])) $con_obs = $_POST['con_obs0'];
            $con_iny = '';

            for ($i = 0; $i <= $total_semanas; $i++) {
                $con_iny .= ($_POST['con_iny' . $i] . "|");
            }

            require("_database/db_medico_reproduccion.php");
            if (isset($_POST["f_asp"]) && $_POST["f_asp"] <> '') {

                $coincidencias = validarAgendaReproTurno($_POST["idx"], $_POST["f_asp"], $_POST["idturno"] ?? 0);
            } else {
                $coincidencias = 0;
            }

            if ($coincidencias == 0) {
                if ($_POST['f_asp'] <> '') {
                    $titcal = '';
                    if (isset($_POST['p_fiv']) && $_POST['p_fiv'] == 1) {
                        $titcal .= 'FIV ';
                    }
                    if (isset($_POST['p_icsi']) && $_POST['p_icsi'] == 1) {
                        $titcal .= $_ENV["VAR_ICSI"] . ' ';
                    }
                    if (isset($_POST['p_od']) && $_POST['p_od'] == 1) {
                        $titcal .= 'OD Fresco ';
                    }
                    if (isset($_POST['p_don']) && $_POST['p_don'] == 1) {
                        $titcal .= 'DONACION Fresco ';
                    }
                    if (isset($_POST['p_cri']) && $_POST['p_cri'] == 1) {
                        $titcal .= 'CRIO OVOS ';
                    }
                    if (isset($_POST['des_dia']) && $_POST['des_dia'] === 0) {
                        $titcal .= 'DESCONGELA OVO ';
                    }
                    if (isset($_POST['des_dia']) && $_POST['des_dia'] > 0) {
                        $titcal .= 'DESCONGELA EMBRION ';
                    }

                    // consultar si la fecha de aspiracion esta blanco
                    $stmt = $db->prepare("SELECT codigo from googlecalendar where tipoprocedimiento_id = 1 and estado = 1 and procedimiento_id = ?");
                    $stmt->execute(array($_POST['idx']));

                    require($_SERVER["DOCUMENT_ROOT"] . "/config/environment.php");

                    $stmt1 = $db->prepare("SELECT nombre minutos FROM man_turno_reproduccion WHERE id = ? AND estado = 1 ORDER BY nombre;");
                    $stmt1->execute([$_POST['idturno']]);
                    $data_minutos = $stmt1->fetch(PDO::FETCH_ASSOC);

                    if ($stmt1->rowCount() == 0) {
                        $data_minutos["minutos"] = "0";
                    }

                    if ($stmt->rowCount() == 0) {
                        $googlecalendar = google_cal(
                            $titcal . $_POST['nombre'] . ' (' . $login . ')',
                            $_POST['obs'],
                            $_POST['f_asp'] . ':00.000-05:00',
                            date('Y-m-d', strtotime($_POST['f_asp'] . ' + ' . $data_minutos["minutos"] . ' minute')) . 'T' . date('H:i:s', strtotime($_POST['f_asp'] . ' + ' . $data_minutos["minutos"] . ' minute')) . '.000-05:00',
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


                updateRepro($_POST['p_dnix'], $_POST['t_muex'], $_POST['idx'], $_POST['eda'], isset($_POST['p_dni_het']) ? $_POST['p_dni_het'] : '', $_POST['poseidon'], isset($_POST['p_dtri']) ? $_POST['p_dtri'] : null, isset($_POST['p_cic']) ? $_POST['p_cic'] : null, isset($_POST['p_fiv']) ? $_POST['p_fiv'] : null, isset($_POST['p_icsi']) ? $_POST['p_icsi'] : null, isset($_POST['p_od']) ? $_POST['p_od'] : null, isset($_POST['p_don']) ? $_POST['p_don'] : null, isset($_POST['p_cri']) ? $_POST['p_cri'] : null, isset($_POST['p_iiu']) ? $_POST['p_iiu'] : null, $_POST['p_extras'], isset($_POST['n_fol']) && !empty($_POST['n_fol']) ? $_POST['n_fol'] : 0, isset($_POST['fur']) ? $_POST['fur'] : null, $_POST['f_aco'], $_POST['fsh'], $_POST['lh'], $_POST['est'], $_POST['prol'], isset($_POST['ins']) ? $_POST['ins'] : '', $_POST['amh'], $_POST['inh'], $_POST['t3'], $_POST['t4'], $_POST['tsh'], $_POST['m_agh'], $_POST['m_vdrl'], $_POST['m_clam'], $_POST['m_his'], $_POST['m_hsg'], $_POST['f_fem'], $_POST['f_mas'], $con_iny, $_POST['obs'], $_POST['f_iny'], !!$_POST['h_iny'] ? $_POST['h_iny'] : '', $_POST['f_asp'], isset($_POST['cancela']) ? $_POST['cancela'] : 0, $_POST['repro'], $_POST['complicacionesparto_id'], $_POST['complicacionesparto_motivo'], $anestesia, $login, $_POST['idturno']);

                if (isset($_POST['p_iiu']) && $_POST['p_iiu'] > 0 && $_POST['f_cap'] && $_POST['fec_h'] && $_POST['fec_m']) {
                    $h_cap = $_POST['fec_h'] . ':' . $_POST['fec_m'];
                    $stmt2 = $db->prepare("INSERT INTO lab_andro_cap (p_dni,fec,h_cap,iiu) VALUES (?,?,?,?)");
                    $stmt2->execute(array($_POST['p_dni'], $_POST['f_cap'], $h_cap, $_POST['idx']));
                }

                if (isset($_POST['p_don']) && $_POST['p_don'] == 1 && $_POST['f_asp'] <> '' && $_POST['recep_num'] > 0) {
                    for ($p = 1; $p <= $_POST['recep_num']; $p++) {
                        $stmt2 = $db->prepare("UPDATE hc_reprod SET f_iny=?, h_iny=?, f_asp=?, idturno=?, iduserupdate=?, updatex=?  WHERE id=?");
                        $hora_actual = date("Y-m-d H:i:s");
                        $stmt2->execute(array($_POST['f_iny'], $_POST['h_iny'], $_POST['f_asp'], $_POST['idturno'], $login, $hora_actual, $_POST['recep' . $p])); //Fija la f_iny y f_asp para las receptoras

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
                                            WHERE id=?"
                        );
                        $log_Reprod->execute(array($_POST['recep' . $p]));
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
            // medicamentos de farmacia
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

            $fsh = "";
            $lh = "";
            $est = "";
            $prol = "";
            $ins = "";
            $t3 = "";
            $t4 = "";
            $tsh = "";
            $amh = "";
            $inh = "";
            while ($perfi = $Rperfi->fetch(PDO::FETCH_ASSOC)) {
                if ($perfi['fsh'] <> "") {
                    $fsh = $perfi['fsh'];
                }
                if ($perfi['lh'] <> "") {
                    $lh = $perfi['lh'];
                }
                if ($perfi['est'] <> "") {
                    $est = $perfi['est'];
                }
                if ($perfi['prol'] <> "") {
                    $prol = $perfi['prol'];
                }
                if ($perfi['ins'] <> "") {
                    $ins = $perfi['ins'];
                }
                if ($perfi['t3'] <> "") {
                    $t3 = $perfi['t3'];
                }
                if ($perfi['t4'] <> "") {
                    $t4 = $perfi['t4'];
                }
                if ($perfi['tsh'] <> "") {
                    $tsh = $perfi['tsh'];
                }
                if ($perfi['amh'] <> "") {
                    $amh = $perfi['amh'];
                }
                if ($perfi['inh'] <> "") {
                    $inh = $perfi['inh'];
                }
            }

            $hsghes = $db->prepare("SELECT con FROM hc_antece_hsghes WHERE dni=? AND tip='HSG' and estado = true ORDER BY fec DESC LIMIT 1");
            $hsghes->execute(array($repro['dni']));
            $hsg = $hsghes->fetch(PDO::FETCH_ASSOC);
            $rPare = $db->prepare("SELECT p_dni,p_nom,p_ape FROM hc_pareja WHERE p_dni=? ORDER BY p_ape DESC");
            $rPare->execute(array($repro['p_dni']));
            $pare = $rPare->fetch(PDO::FETCH_ASSOC);

            if ($repro['p_dni'] == "" || $repro['p_dni'] == '1') {
                $pareja = "SOLTERA";
            } else {
                $pareja = $pare['p_ape'] . " " . $pare['p_nom'];
            }

            $rAspi = $db->prepare("SELECT pro FROM lab_aspira WHERE lab_aspira.rep=? and lab_aspira.estado is true");
            $rAspi->execute(array($id));

            if ($rAspi->rowCount() > 0) {
                $lock = 1;
            } else {
                $lock = 0;
            }

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

            if ($tiene_legal && $legal['tipo_vencimiento'] == 'procedimientos') {
                $intervenciones = $db->prepare("SELECT * FROM hc_reprod WHERE estado = true and dni=? and fec >= '" . $legal['finforme'] . "'");
                $intervenciones->execute(array($repro['dni']));
                $tiene_legal = $intervenciones->rowCount() > $legal['vencimiento'] ? false : $legal;
            }

            $es_ted = !is_null($repro['des_dia']);
            $sero = getSero($repro[($es_ted ? 'p_dni' : 'dni')], $repro['fec'], ($es_ted ? 2 : 1));

            function print_sero($tipo, $nombre)
            {
                global $repro;
                global $sero;

                $link_sero = 'archivos_hcpacientes.php?idArchivo=sero_' . $repro['dni'] . "_" . $sero[$tipo . 'fec'];
                return '<tr>
                    <td>
                        ' . $nombre . '
                    </td>
                    <td>
                        ' . ($sero[$tipo] == 1 ? 'Positivo' : ($sero[$tipo] == 2 ? 'Negativo' : '-')) . '
                    </td>
                    <td>
                        ' . ($sero[$tipo] == 0 || !file_exists('analisis/sero_' . $repro['dni'] . "_" . $sero[$tipo . 'fec'] . ".pdf") ? '-' : '<a href="' . $link_sero . '" target="_blank">Ver/Descargar</a>') . '
                    </td>
                    <td>
                        ' . $sero[$tipo . 'fec'] . '
                    </td>
                    <td>
                        ' . ($sero[$tipo] == 0 ? '-' : (!$sero[$tipo . 'vencido'] ? 'Vigente' : '<b>Vencido</b>')) . '
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

            if (!!$hema) {
                $link_hema = 'hematologia/' . $repro['dni'] . '/' . $hema['documento'];
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

            if ($receptora) :
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
            elseif ($es_donante) :
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
                        $("#<?php echo $repro['p_dni_het']; ?>").show();
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
                <h2><?php echo "<small>(" . date("d-m-Y", strtotime($repro['fec'])) . ")</small> " . $paci['ape'] . " " . $paci['nom'] . " / <small>" . $pareja . "</small>"; ?>
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



                <?php
                $key = $_ENV["apikey"];
                ?>
                <input type="hidden" name="login" id="login" value="<?php echo $login; ?>">
                <input type="hidden" name="key" id="key" value="<?php echo $key; ?>">
                <form action="" method="post" data-ajax="false" id="form1" name="form1">
                    <input type="hidden" name="fecha_programacion" id="fecha_programacion" value="<?php print($data['valor']); ?>">
                    <input type="hidden" name="idx" value="<?php echo $repro['id']; ?>">
                    <input type="hidden" name="dni" value="<?php echo $repro['dni']; ?>">
                    <input type="hidden" name="nombre" value="<?php echo $paci['ape'] . " " . $paci['nom']; ?>">
                    <input type="hidden" name="fec_iny_activo" id="fec_iny_activo">
                    <input type="hidden" name="des_dia" id="des_dia" value="<?php echo $repro['des_dia']; ?>">
                    <div class="medico-tratante-titulo">
                    </div>
                    <div data-role="collapsibleset" data-theme="a" data-content-theme="a" data-mini="true">


                        <?php include($_SERVER["DOCUMENT_ROOT"] . "/_componentes/ra_andrologia.php"); ?>


                        <?php
                        $puede_inyeccion =
                            !$legal_vencida
                            && (!$analisis_vencido || $es_ted)
                            && (!$necesita_andrologia || !$andrologia_vencido)
                            && !$riesgo_vencido
                            && (!$necesita_psicologico || !$psicologico_vencido);
                        include('_includes/e_repro_fechaprogramacion.php'); ?>
                    </div>



                    <?php
                    $medicoValidar = $db->prepare("SELECT usuario_creacion_id FROM ciclo_estimulacion_detalle where  hcreprod_id = ? AND eliminado=0 AND usuario_creacion_id = ? LIMIT 1;");
                    $medicoValidar->execute([$id, $login]);
                    $medicoValidado = $medicoValidar->fetch(PDO::FETCH_ASSOC);

                    if ($repro['med'] == $login || $repro['idusercreate'] == $login || !empty($medicoValidado)) {
                        if ($lock <> 1 && $repro['cancela'] <> 2) { //Puede editar hasta la fecha de inyeccion 
                    ?>
                            <div class="enlinea">
                                <?php
                                $validator = "";

                                print('<input type="Submit" value="GUARDAR DATOS" data-icon="check" data-iconpos="left" data-mini="true" class="show-page-loading-msg" data-textonly="false" data-textvisible="true" data-msgtext="Actualizando datos.." data-theme="b" data-inline="true" ' . $validator . '/>');
                                ?>
                                <input type="checkbox" name="cancela" id="cancela" data-mini="true" value=1 <?php if ($repro['cancela'] == 1) {
                                                                                                                echo "checked";
                                                                                                            } ?>>
                                <label for="cancela">Cancelar Reproducción</label>
                            </div>
                    <?php } else {
                            if (is_null($repro['des_dia'])) {
                                $titulo = "Aspiración";
                            } else {
                                $titulo = "Descongelación";
                            }
                            echo '<font color="#E34446"><b>SOLO LECTURA!</b> ' . $titulo . ' programada, para modificaciones llamar a Jefa de Sala</font>';
                        }
                    } else {
                        echo '<br><br><font color="#E34446"><b>PERMISO DE EDICION SOLO PARA: </b> ' . $repro['med'] . '</font>';
                    } ?>
                </form>
            </div>
        <?php } ?>
    </div>

    <?php include($_SERVER["DOCUMENT_ROOT"] . "/_componentes/e_repro.php"); ?>

    <script>
        $(".chosen-select").chosen();
    </script>
</body>

</html>