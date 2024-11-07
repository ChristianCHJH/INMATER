<!DOCTYPE HTML>
<html>
<head>
<?php
   include 'seguridad_login.php'
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"f8JKPC3mQxxOum4k>
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="_themes/tema_inmater.min.css?v=1.02"/>
    <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css"/>
    <link rel="stylesheet" href="_libraries/open-iconic/font/css/open-iconic.min.css"/>
    <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css"/>
    <link rel="stylesheet" href="css/e_repro.css"/>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
</head>
<body>
    <div data-role="page" class="ui-responsive-panel" id="e_repro" data-dialog="true">
        <?php
        if ($_GET['id'] <> "") {
            // hora limite programacion
            $consulta = $db->prepare("SELECT valor from man_configuracion where codigo=?");
            $consulta->execute(array('fecha_programacion'));
            $data = $consulta->fetch(PDO::FETCH_ASSOC);

            $id = $_GET['id'];

            $rRepro = $db->prepare("SELECT * from hc_reprod where estado = true and id=? and dni = ?");
            $rRepro->execute(array($id, $login));
            $repro = $rRepro->fetch(PDO::FETCH_ASSOC);

            if ($rRepro->rowCount() == 0) {
                print('
                            <div data-role="header" data-position="fixed"></div>
                            <div class="ui-content" role="main">No corresponde el procedimiento!<br><a href="lista_paciente.php">Volver</a></div>
                        </div>
                    </body>
                </html>'); exit();
            }

            $rPaci = $db->prepare("SELECT hc_paciente.san, nom, ape, m_ets, g_agh, g_his, don, fnac FROM hc_antece, hc_paciente WHERE hc_paciente.dni = hc_antece.dni AND hc_paciente.dni=?");
            $rPaci->execute(array($repro['dni']));
            $paci = $rPaci->fetch(PDO::FETCH_ASSOC);

            $rPare = $db->prepare("SELECT p_dni,p_nom,p_ape FROM hc_pareja WHERE p_dni=? ORDER BY p_ape DESC");
            $rPare->execute(array($repro['p_dni']));
            $pare = $rPare->fetch(PDO::FETCH_ASSOC);

            if ($repro['p_dni'] == "") { $pareja = "SOLTERA"; } else { $pareja = $pare['p_ape'] . " " . $pare['p_nom']; }

            $rAspi = $db->prepare("SELECT pro FROM lab_aspira WHERE lab_aspira.rep=? and lab_aspira.estado is true");
            $rAspi->execute(array($id));

            if ($rAspi->rowCount() > 0) {
                $lock = 1;
            } else {
                $lock = 0;
            }

            $rLegal = $db->prepare("SELECT legal.*, tipo.nombre nombretipodocumento, restricciones.tipo_vencimiento, restricciones.vencimiento, CASE p.nom IS NOT NULL WHEN true THEN 1 ELSE 0 END es_paciente
                FROM hc_legal_01 legal
                LEFT JOIN hc_paciente p on p.dni = legal.iduserupdate
                INNER JOIN man_legal_tipodocumento tipo ON tipo.codigo = legal.idlegaltipodocumento
                INNER JOIN restricciones ON restricciones.idtipo = legal.idlegaltipodocumento
                WHERE legal.estado = 1 AND legal.numerodocumento = ? AND restricciones.nombre = 'legal'
                ORDER BY legal.id DESC");

            $rLegal->execute([$repro['dni']]);

            $legal = $rLegal->fetch(PDO::FETCH_ASSOC);
            $tiene_legal = !!$legal;


            if( $tiene_legal && $legal['tipo_vencimiento'] == 'procedimientos' ){
                $intervenciones = $db->prepare("SELECT * FROM hc_reprod WHERE estado = true and dni=? and fec >= '". $legal['finforme'] ."'");
                $intervenciones->execute(array($repro['dni']));
                $tiene_legal = $intervenciones->rowCount() > $legal['vencimiento'] ? false : $legal;
            }

            $es_ted = !is_null($repro['des_dia']);
            $sero = getSero($repro[( $es_ted ? 'p_dni' : 'dni')], $repro['fec'], ( $es_ted ? 2 : 1) );

            $Hema = $db->prepare("SELECT *,
                CASE WHEN CAST(fresultado as date) >= CAST(? as date) - INTERVAL '102 days' THEN false
                    ELSE true
                END as vencido
                FROM hc_hematologia WHERE estado = 1 and tipopaciente=1 and numerodocumento=? order by fresultado desc");
            
            $Hema->execute(array($repro['fec'], $repro['dni']));

            $hema = $Hema->fetch(PDO::FETCH_ASSOC);

            if( !!$hema )
            {
                $link_hema = 'hematologia/'. $repro['dni'] . '/' . $hema['documento'];
            }

            $riesgo_quirurgico = $db->prepare("SELECT hc_riesgo_quirurgico.*, restricciones.tipo_vencimiento, restricciones.vencimiento, CASE p.nom IS NOT NULL WHEN true THEN 1 ELSE 0 END es_paciente,
                CASE
                    WHEN restricciones.tipo_vencimiento = 'dias' AND CAST(fvigencia as date) >= CAST(? as date) - INTERVAL (restricciones.vencimiento + 12) * INTERVAL '1 DAY' THEN false
                    WHEN restricciones.tipo_vencimiento = 'no_vence' THEN false
                    ELSE true
                END as vencido
                FROM hc_riesgo_quirurgico
                LEFT JOIN hc_paciente p on p.dni = hc_riesgo_quirurgico.iduserupdate
                INNER JOIN restricciones ON restricciones.nombre = 'riesgo_quirurgico'
                WHERE hc_riesgo_quirurgico.estado = 1 and numerodocumento = ?
                ORDER BY hc_riesgo_quirurgico.id DESC");

            $riesgo_quirurgico->execute([$repro['fec'], $repro['dni']]);
            $riesgo_quirurgico = $riesgo_quirurgico->fetch(PDO::FETCH_ASSOC);
            $tiene_pareja = $repro['p_dni_het'] == '' && (($repro['p_dni'] != '' && $repro['p_dni'] != '1') || $repro['p_icsi'] == '1' || $repro['p_fiv'] == '1');
            $es_descongelacion = !is_null($repro['des_dia']) && $repro['des_dia'] >= 0;
            $necesita_andrologia = $tiene_pareja;

            if( $necesita_andrologia )
            {
                $espermatograma = $db->prepare("SELECT lab_andro_esp.*, restricciones.tipo_vencimiento, restricciones.vencimiento,
                CASE
                    WHEN restricciones.tipo_vencimiento = 'dias' AND CAST(fec as date) >= CAST(? as date) - (restricciones.vencimiento + 12) * INTERVAL '1 DAY' THEN false
                    WHEN restricciones.tipo_vencimiento = 'no_vence' THEN false
                    ELSE true
                END as vencido
                    FROM lab_andro_esp
                    INNER JOIN restricciones ON restricciones.nombre = 'andrologia' AND restricciones.tipo = 'espermatograma' WHERE p_dni=? ORDER BY fec DESC");
                $espermatograma->execute([$repro['fec'], $repro['p_dni']]);

                $espermatograma = $espermatograma->fetch(PDO::FETCH_ASSOC);
                $espermacultivo = $db->prepare("SELECT hc_analisis.*, restricciones.tipo_vencimiento, restricciones.vencimiento,
                    CASE
                        WHEN restricciones.tipo_vencimiento = 'dias' AND CAST(a_mue as date) >= CAST(? as date) - (restricciones.vencimiento + 12) * INTERVAL '1 day' THEN false
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
            elseif( $es_donante ):
                $psicologico = $db->prepare("SELECT hc_analisis.*, restricciones.tipo_vencimiento_donante, restricciones.vencimiento_donante,
                    CASE
                        WHEN restricciones.tipo_vencimiento_donante = 'dias' AND CAST(a_mue as date) >= CAST(? as date) -  (restricciones.vencimiento_donante + 12) * INTERVAL '1 DAY' THEN false
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
                    WHEN restricciones.tipo_vencimiento_donante = 'dias' AND CAST(fvigencia as date) >= CAST(? as date) - (restricciones.vencimiento_donante + 12) * INTERVAL * '1 DAY' THEN false
                    WHEN restricciones.tipo_vencimiento_donante = 'no_vence' THEN false
                    ELSE true
                END as vencido, CASE p.nom IS NOT NULL WHEN true THEN 1 ELSE 0 END es_paciente
                FROM hc_cariotipo
                LEFT JOIN hc_paciente p on p.dni = hc_cariotipo.iduserupdate
                INNER JOIN restricciones ON restricciones.nombre = 'cariotipo'
                WHERE hc_cariotipo.estado = 1 and numerodocumento = ?");

            $cariotipo->execute([$repro['fec'], $repro['dni']]);
            
            $cariotipo = $cariotipo->fetch(PDO::FETCH_ASSOC); ?>
            <div data-role="header" data-position="fixed">
                <a href="lista_paciente.php" rel="external" class="ui-btn">Cerrar</a>
                <h1 style="color: #fff;"><?php echo "<small>(".date("d-m-Y", strtotime($repro['fec'])).")</small> " . ucwords(strtolower($paci['ape'])) . " " . ucwords(strtolower($paci['nom'])) . " / " . ucwords(strtolower($pareja)); ?></h1>
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
                    <b><a href="restriccion_legal.php?repro_id=<?php echo $repro['id'] ?>&dni=<?php echo $repro['dni'] ?>" data-transition="pop" rel="external" style="text-decoration: none;">Legal</a></b>
                </div>
                <?php
                $analisis_vencido = !$sero ||
                !!@$sero['hbsvencido'] ||
                !!@$sero['hcvvencido'] ||
                !!@$sero['hivvencido'] ||
                !!@$sero['rprvencido'] ||
                !!@$sero['rubvencido'] ||
                !!@$sero['toxvencido'] ||
                !!@$sero['cla_gvencido'] ||
                !!@$sero['cla_mvencido'] ||
                @$sero['hbs'] == 1 ||
                @$sero['hcv'] == 1 ||
                @$sero['hiv'] == 1 ||
                @$sero['rpr'] == 1 ||
                @$sero['rub'] == 1 ||
                @$sero['tox'] == 1 ||
                @$sero['cla_g'] == 1 ||
                @$sero['cla_m'] == 1 ?>
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
                <?php if (!$es_ted): ?>
                    <div id="analisis" class="alarma <?php echo $analisis_class ?>">
                        <span class="oi" data-glyph="<?php echo $analisis_icon ?>"></span>
                        <b><a href="restriccion_analisis_clinico.php?tipopaciente=1&repro_id=<?php echo $repro['id'] ?>&dni=<?php echo $repro['dni'] ?>" data-transition="pop" rel="external" style="text-decoration: none;">Análisis clínicos</a>&nbsp;♀</b>
                    </div>
                <?php else: ?>
                    <div id="analisis" class="alarma <?php echo $analisis_class ?>">
                        <span class="oi" data-glyph="<?php echo $analisis_icon ?>"></span>

                        <b><a href="restriccion_analisis_clinico.php?tipopaciente=2&repro_id=<?php echo $repro['id'] ?>&dni=<?php echo $repro['p_dni'] ?>" data-transition="pop" rel="external" style="text-decoration: none;">Análisis clínicos</a>&nbsp;♂</b>
                    </div>
                <?php endif ?>

                <?php if( $necesita_andrologia ) : ?>
                    <?php
                    $andrologia_vencido =
                    !$espermatograma ||
                    !!@$espermatograma['vencido'] ||
                    !$espermacultivo ||
                    !!@$espermacultivo['vencido'] ||
                    ( !!$espermacultivo && $espermacultivo['a_sta'] != 'Negativo' ) ?>
                    <div id="riesgo-quirurgico" class="alarma <?php echo $andrologia_vencido ? 'vencida' : 'vigente' ?>">
                        <span class="oi" data-glyph="<?php echo $andrologia_vencido ? 'warning' : 'circle-check' ?>"></span>
                        <b><a href="restriccion_andrologia.php?repro_id=<?php echo $repro['id'] ?>" data-transition="pop">Andrología</a></b>
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
                        <b><a href="restriccion_riesgo_quirurgico.php?repro_id=<?php echo $repro['id'] ?>&dni=<?php echo $repro['dni'] ?>" data-transition="pop" rel="external" style="text-decoration: none;">Riesgo quirúrgico</a></b>
                    </div>
                <?php endif ?>

                <?php if( $necesita_psicologico ) : ?>
                    <?php $psicologico_vencido = !$psicologico || !!@$psicologico['vencido'] || ( !!$psicologico && $psicologico['a_sta'] != 'Positivo' ) ?>
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
                        <b><a href="restriccion_psicologico.php?repro_id=<?php echo $repro['id'] ?>" data-transition="pop" rel="external" style="text-decoration: none;">Examen psicológico</a></b>
                    </div>
                <?php endif ?>

                <?php if ($es_donante): ?>
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
                        <b><a href="restriccion_cariotipo.php?repro_id=<?php echo $repro['id'] ?>&dni=<?php echo $repro['dni'] ?>" data-transition="pop" rel="external" style="text-decoration: none;">Cariotipo</a></b>
                    </div>
                <?php endif  ?>

            </div>
        <?php } ?>
    </div>
</body>
</html>