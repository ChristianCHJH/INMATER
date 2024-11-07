<!DOCTYPE HTML>
<html>
<head>
    <?php
   include 'seguridad_login.php'
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="_themes/tema_inmater.min.css"/>
    <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css"/>
    <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css"/>
    <link rel="stylesheet" href="css/e_gine.css"/>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>

    <script>
        $(document).on("pagecreate", function () {
            function scale(width, height, padding, border) {
                var scrWidth = $(window).width() - 30,
                    scrHeight = $(window).height() - 30,
                    ifrPadding = 2 * padding,
                    ifrBorder = 2 * border,
                    ifrWidth = width + ifrPadding + ifrBorder,
                    ifrHeight = height + ifrPadding + ifrBorder,
                    h, w;

                if (ifrWidth < scrWidth && ifrHeight < scrHeight) {
                    w = ifrWidth;
                    h = ifrHeight;
                } else if (( ifrWidth / scrWidth ) > ( ifrHeight / scrHeight )) {
                    w = scrWidth;
                    h = ( scrWidth / ifrWidth ) * ifrHeight;
                } else {
                    h = scrHeight;
                    w = ( scrHeight / ifrHeight ) * ifrWidth;
                }

                return {
                    'width': w - ( ifrPadding + ifrBorder ),
                    'height': h - ( ifrPadding + ifrBorder )
                };
            };

            $(".ui-popup iframe").attr("width", 0).attr("height", "auto");

            $("#popupVideo").on({
                popupbeforeposition: function () {
                    // call our custom function scale() to get the width and height
                    var size = scale(1200, 600, 15, 1),
                        w = size.width,
                        h = size.height;

                    $("#popupVideo iframe")
                        .attr("width", w)
                        .attr("height", h);
                },
                popupafterclose: function () {
                    $("#popupVideo iframe")
                        .attr("width", 0)
                        .attr("height", 0);
                }
            });
        });

        function PrintElem(elem, paci, tipo, fec) {
            var data = $(elem).html();
            var mywindow = window.open('', 'Imprimir', 'height=400,width=800');
            mywindow.document.write('<html><head><title>Imprimir</title>');
            mywindow.document.write('<link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css" />');
            mywindow.document.write('<style> @media print{@page {size:A5;margin: 0px 0px 0px 0px;}} .noPrint { display: none !important; } table, th, td { border: 0.5px solid black; border-collapse: collapse; } </style>');
            mywindow.document.write('</head><body><div style="margin: 0 auto;width:500px"><br><br><br><br><br><br><br><br><br>');
            if (tipo == 1) mywindow.document.write('<h2>Medicamentos</h2><p><i style="float:right">Fecha: ' + fec + '</i><br><b>PACIENTE:</b><br> ' + paci + '</p>');
            if (tipo == 2) mywindow.document.write('<h2>Orden de Análisis Clínicos</h2><p><i style="float:right">Fecha: ' + fec + '</i><br>Paciente: ' + paci + '</p>');
            mywindow.document.write(data);
            mywindow.document.write('<script type="text/javascript">window.print();<' + '/script>');
            mywindow.document.write('</div></body></html>');
            return true;
        }
    </script>

    <?php
    if (!!$_GET && isset($_GET['pop']) && !empty($_GET['pop'])) { ?>
        <script>
            $(document).ready(function () {
                $("#Plan").collapsible({collapsed: false});
            });
        </script>
    <?php } ?>
</head>

<body>
    <div data-role="page" class="ui-responsive-panel" id="e_gine" data-dialog="true">
        <?php
        if (isset($_POST) && !empty($_POST)) {
            // registrar la interconsulta
            $interconsulta_id = 0;

            if (isset($_POST['interconsulta_tipoconsulta'])) {

                if ($_POST['interconsulta_id'] != 0) {
                    $interconsulta_id = $_POST['interconsulta_id'];

                    $stmt = $db->prepare(
                        "UPDATE hc_gineco SET 
                        dni=?, fec=?, med=?, fec_h=?, fec_m=?, mot=?, cupon=?, tipoconsulta_ginecologia_id=?, man_motivoconsulta_id=?, iduserupdate=?, updatex=?
                        WHERE id=?;"
                    );
                    $hora_actual = date("Y-m-d H:i:s");
                    $stmt->execute([
                        $_POST['dni'],
                        $_POST['interconsulta_fecha'],
                        $_POST['interconsulta_medico'],
                        $_POST['interconsulta_hora'],
                        $_POST['interconsulta_minutos'],
                        $_POST['interconsulta_observacion'],
                        !empty($_POST['interconsulta_sede']) ? $_POST['interconsulta_sede'] : 0,
                        $_POST['interconsulta_tipoconsulta'],
                        $_POST['interconsulta_motivo'],
                        $login,
                        $hora_actual,
                        $_POST['interconsulta_id']
                    ]);

                    $log_Gineco = $db->prepare(
                                "INSERT INTO appinmater_log.hc_gineco (
                                    gineco_id, estadoconsulta_ginecologia_id, tipoconsulta_ginecologia_id,
                                    man_motivoconsulta_id, voucher_id, interconsulta_id, dni, fec, med, fec_h,
                                    fec_m, fecha_confirmacion, fecha_voucher, mot, dig, medi, aux, efec, cic,
                                    vag, vul, cer, cer1, mam, mam1, t_vag, eco, e_sol, i_med, i_fec, i_obs, 
                                    in_t, in_f1, in_h1, in_m1, in_f2, in_h2, in_m2, idturno_inter, in_c,
                                    cupon, repro, legal, cancela, cancela_motivo,
                                    isuser_log, date_log,
                                    asesor_medico_id, 
                                    action
                                    )
                                SELECT
                                    id, estadoconsulta_ginecologia_id, tipoconsulta_ginecologia_id,
                                    man_motivoconsulta_id, voucher_id, interconsulta_id, dni, fec, med, fec_h, 
                                    fec_m, fecha_confirmacion, fecha_voucher, mot, dig, medi, aux, efec, cic, 
                                    vag, vul, cer, cer1, mam, mam1, t_vag, eco, e_sol, i_med, i_fec, i_obs, 
                                    in_t, in_f1, in_h1, in_m1, in_f2, in_h2, in_m2, idturno_inter, in_c, 
                                    cupon, repro, legal, cancela, cancela_motivo,
                                    iduserupdate, updatex, 
                                    asesor_medico_id,
                                    'U'
                                FROM appinmater_modulo.hc_gineco
                                WHERE id =?");
                    $log_Gineco->execute(array($_POST['interconsulta_id']));
                } else {

                    $programa_id = $db->prepare("SELECT medios_comunicacion_id, idsedes from hc_paciente where dni = ?");
                    $programa_id->execute([$_POST['dni']]);
                    $programa_id = $programa_id->fetch(PDO::FETCH_ASSOC);
                    

                    $stmt = $db->prepare(
                        "INSERT INTO hc_gineco
                        (dni, fec, med, fec_h, fec_m, mot, cupon, tipoconsulta_ginecologia_id, man_motivoconsulta_id, idusercreate,programaid,idsedes) VALUES
                        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
                    );

                    $stmt->execute([
                        $_POST['dni'],
                        $_POST['interconsulta_fecha'],
                        $_POST['interconsulta_medico'],
                        $_POST['interconsulta_hora'],
                        $_POST['interconsulta_minutos'],
                        $_POST['interconsulta_observacion'],
                        !empty($_POST['interconsulta_sede']) ? $_POST['interconsulta_sede'] : 0,
                        $_POST['interconsulta_tipoconsulta'],
                        $_POST['interconsulta_motivo'],
                        $login,
                        $programa_id['medios_comunicacion_id'],
                        $programa_id['idsedes']
                    ]);

                    $interconsulta_id = $db->lastInsertId();
                    $log_Gineco = $db->prepare(
                        "INSERT INTO appinmater_log.hc_gineco (
                            gineco_id, estadoconsulta_ginecologia_id, tipoconsulta_ginecologia_id,
                            man_motivoconsulta_id, voucher_id, interconsulta_id, dni, fec, med, fec_h,
                            fec_m, fecha_confirmacion, fecha_voucher, mot, dig, medi, aux, efec, cic,
                            vag, vul, cer, cer1, mam, mam1, t_vag, eco, e_sol, i_med, i_fec, i_obs, 
                            in_t, in_f1, in_h1, in_m1, in_f2, in_h2, in_m2, idturno_inter, in_c,
                            cupon, repro, legal, cancela, cancela_motivo,
                            isuser_log, date_log,
                            asesor_medico_id, 
                            action
                            )
                        SELECT
                            id, estadoconsulta_ginecologia_id, tipoconsulta_ginecologia_id,
                            man_motivoconsulta_id, voucher_id, interconsulta_id, dni, fec, med, fec_h, 
                            fec_m, fecha_confirmacion, fecha_voucher, mot, dig, medi, aux, efec, cic, 
                            vag, vul, cer, cer1, mam, mam1, t_vag, eco, e_sol, i_med, i_fec, i_obs, 
                            in_t, in_f1, in_h1, in_m1, in_f2, in_h2, in_m2, idturno_inter, in_c, 
                            cupon, repro, legal, cancela, cancela_motivo,
                            idusercreate, createdate,
                            asesor_medico_id,
                            'I'
                        FROM appinmater_modulo.hc_gineco
                        WHERE id =?");
                    
                    $log_Gineco->execute(array($interconsulta_id));
                    // registro de atencion unica
                    $data = [
                        'tipo' => 'agregar_atencion',
                        'area_id' => 1,
                        'atencion_id' => $interconsulta_id,
                        'medico_id' => $_POST['interconsulta_medico'],
                        'paciente_id' => $_POST['dni'],
                        'detalle' => $_POST['interconsulta_motivo'],
                    ];

                    include ($_SERVER["DOCUMENT_ROOT"] . "/_operaciones/cli_atencion_unica.php");
                }
            }

            require("_database/db_medico_reproduccion.php");
            $coincidencias = validarAgendaGineTurno($_POST["idx"], $_POST['in_f2'].'T'.$_POST['in_hora'], $_POST["idturno"]);

            if ($coincidencias == 0) {
                if (isset($_POST['idx'])) {
                    $hora = explode(":", $_POST['in_hora']);
                    
                    updateGine($_POST['idx'], $_POST['fec'], $_POST['fec_h'], $_POST['fec_m'], $_POST['mot'], $_POST['m_tratante'], $_POST['asesora'], $_POST['cupon'], $_POST['dig'], $_POST['aux'], $_POST['efec'], $_POST['cic'], $_POST['vag'], $_POST['vul'], $_POST['cer'], $_POST['cer1'], $_POST['mam'], $_POST['mam1'], $_POST['t_vag'], $_POST['eco'], $_POST['e_sol'], $_POST['i_med'] ?? 0, $_POST['i_fec'] ?? '1899-12-30', $_POST['i_obs'] ?? '', $_POST['in_t'], $_POST['in_f1'], $_POST['in_h1'], $_POST['in_m1'], $_POST['in_f2'], $hora[0]?? '', $hora[1]?? '', $_POST['in_c'], $_POST['repro'], $_POST['idturno'], $interconsulta_id, $_POST['cancela_motivo'],$login);
                   
                }

                $cancela = 0;
                if (isset($_POST["cancela"]) && $_POST["cancela"] == 1) {
                    $cancela = 1;
                

                $stmt = $db->prepare("UPDATE hc_gineco SET cancela = ?, updatex=? WHERE id = ?;");
                $hora_actual = date("Y-m-d H:i:s");
                $stmt->execute([$cancela, $hora_actual, $_POST['idx']]);

                $log_Gineco = $db->prepare(
                    "INSERT INTO appinmater_log.hc_gineco (
                        gineco_id, estadoconsulta_ginecologia_id, tipoconsulta_ginecologia_id,
                        man_motivoconsulta_id, voucher_id, interconsulta_id, dni, fec, med, fec_h,
                        fec_m, fecha_confirmacion, fecha_voucher, mot, dig, medi, aux, efec, cic,
                        vag, vul, cer, cer1, mam, mam1, t_vag, eco, e_sol, i_med, i_fec, i_obs, 
                        in_t, in_f1, in_h1, in_m1, in_f2, in_h2, in_m2, idturno_inter, in_c,
                        cupon, repro, legal, cancela, cancela_motivo,
                        isuser_log, date_log,
                        asesor_medico_id, 
                        action
                        )
                    SELECT
                        id, estadoconsulta_ginecologia_id, tipoconsulta_ginecologia_id,
                        man_motivoconsulta_id, voucher_id, interconsulta_id, dni, fec, med, fec_h, 
                        fec_m, fecha_confirmacion, fecha_voucher, mot, dig, medi, aux, efec, cic, 
                        vag, vul, cer, cer1, mam, mam1, t_vag, eco, e_sol, i_med, i_fec, i_obs, 
                        in_t, in_f1, in_h1, in_m1, in_f2, in_h2, in_m2, idturno_inter, in_c, 
                        cupon, repro, legal, cancela, cancela_motivo,
                        iduserupdate, updatex, 
                        asesor_medico_id,
                        'U'
                    FROM appinmater_modulo.hc_gineco
                    WHERE id =?");
                $log_Gineco->execute(array($_POST['idx']));

                }

                if ($_POST['in_c'] == 1 and $_POST['in_t'] <> '' and $_POST['in_f2'] <> '' and $_POST['in_hora'] <> '') {
                    // consultar la fecha de transferencia
                    $stmt = $db->prepare("SELECT codigo from googlecalendar where tipoprocedimiento_id = 2 and estado = 1 and procedimiento_id = ?");
                    $stmt->execute([$_GET['id']]);

                    require($_SERVER["DOCUMENT_ROOT"]."/config/environment.php");

                    $stmt1 = $db->prepare("SELECT nombre minutos FROM man_turno_reproduccion WHERE id = ? AND estado = 1 ORDER BY nombre;");
                    $stmt1->execute([$_POST['idturno']]);
                    $data_minutos = $stmt1->fetch(PDO::FETCH_ASSOC);

                    if ($stmt1->rowCount() == 0) {
                        $data_minutos["minutos"] = "0";
                    }

                    if ($stmt->rowCount() == 0) {
                        //buscar color de intervencion
                        $codigo_color = 0;
                        $stmt = $db->prepare("SELECT id from man_gineco_tipo_intervencion mgti where nombre=?;");
                        $stmt->execute([$_POST['in_t']]);
                        if ($stmt->rowCount() > 0) {
                            $data_color = $stmt->fetch(PDO::FETCH_ASSOC);
                            $codigo_color = $data_color["id"];
                        }
                        /* $googlecalendar = google_cal(
                            $_POST['in_t'] . ': ' . $_POST['nombre'] . ' (' . $login . ')',
                            'Ginecologia INMATER',
                            $_POST['in_f2'].'T'.$hora[0].':'.$hora[1].':00.000-05:00',
                            date('Y-m-d', strtotime($_POST['in_f2'].'T'.$hora[0].':'.$hora[1].' + '.$data_minutos["minutos"].' minute')).'T'.date('H:i:s', strtotime($_POST['in_f2'].'T'.$hora[0].':'.$hora[1].' + '.$data_minutos["minutos"].' minute')).'.000-05:00',
                            $_ENV["googlecalendar_id"],
                            $_ENV["googlecalendar_accountname"],
                            $_ENV["googlecalendar_keyfilelocation"],
                            'inmater-app',
                            [],
                            $codigo_color
                        );
                        
                        $stmt = $db->prepare("INSERT INTO googlecalendar (tipoprocedimiento_id, procedimiento_id, codigo, html_link, idusercreate) values (?, ?, ?, ?, ?)");
                        $stmt->execute([2, $_GET['id'], $googlecalendar->id, $googlecalendar->htmlLink, $login]); */
                    } else {
                        $data_calendar = $stmt->fetch(PDO::FETCH_ASSOC);

                        if (isset($_POST["cancela"]) && $_POST["cancela"] == 1) {
                            /* $stmt = $db->prepare("UPDATE googlecalendar SET estado = 0, iduserupdate = ? WHERE estado = 1 AND tipoprocedimiento_id = ? AND procedimiento_id = ?;");
                            $stmt->execute([$login, 2, $_GET['id']]); */

                            $stmt = $db->prepare("UPDATE hc_gineco SET in_f2 = NULL, in_h2 = NULL, in_m2 = NULL, iduserupdate = ?, updatex=? WHERE id = ?;");
                            $hora_actual = date("Y-m-d H:i:s");
                            $stmt->execute([$login, $hora_actual, $_GET['id']]);

                            $log_Gineco = $db->prepare(
                                "INSERT INTO appinmater_log.hc_gineco (
                                    gineco_id, estadoconsulta_ginecologia_id, tipoconsulta_ginecologia_id,
                                    man_motivoconsulta_id, voucher_id, interconsulta_id, dni, fec, med, fec_h,
                                    fec_m, fecha_confirmacion, fecha_voucher, mot, dig, medi, aux, efec, cic,
                                    vag, vul, cer, cer1, mam, mam1, t_vag, eco, e_sol, i_med, i_fec, i_obs, 
                                    in_t, in_f1, in_h1, in_m1, in_f2, in_h2, in_m2, idturno_inter, in_c,
                                    cupon, repro, legal, cancela, cancela_motivo,
                                    isuser_log, date_log,
                                    asesor_medico_id, 
                                    action
                                    )
                                SELECT
                                    id, estadoconsulta_ginecologia_id, tipoconsulta_ginecologia_id,
                                    man_motivoconsulta_id, voucher_id, interconsulta_id, dni, fec, med, fec_h, 
                                    fec_m, fecha_confirmacion, fecha_voucher, mot, dig, medi, aux, efec, cic, 
                                    vag, vul, cer, cer1, mam, mam1, t_vag, eco, e_sol, i_med, i_fec, i_obs, 
                                    in_t, in_f1, in_h1, in_m1, in_f2, in_h2, in_m2, idturno_inter, in_c, 
                                    cupon, repro, legal, cancela, cancela_motivo,
                                    iduserupdate, updatex, 
                                    asesor_medico_id,
                                    'U'
                                FROM appinmater_modulo.hc_gineco
                                WHERE id =?");
                            $log_Gineco->execute(array($_GET['id']));

                            /* googlecalendar_eliminar(array(
                                'id' => $_ENV["googlecalendar_id"],
                                'accountname' => $_ENV["googlecalendar_accountname"],
                                'keyfilelocation' => $_ENV["googlecalendar_keyfilelocation"],
                                'applicationname' => $_ENV["googlecalendar_applicationname"],
                                'googlecalendar_codigo' => $data_calendar["codigo"],
                            )); */
                        } /* else {
                            $data = array(
                                'id' => $_ENV["googlecalendar_id"],
                                'accountname' => $_ENV["googlecalendar_accountname"],
                                'keyfilelocation' => $_ENV["googlecalendar_keyfilelocation"],
                                'applicationname' => $_ENV["googlecalendar_applicationname"],
                                'googlecalendar_codigo' => $data_calendar["codigo"],
                                'googlecalendar_date_start' => $_POST['in_f2'].'T'.$hora[0].':'.$hora[1].':00.000-05:00',
                                'googlecalendar_date_end' => date('Y-m-d', strtotime($_POST['in_f2'].'T'.$hora[0].':'.$hora[1].' + '.$data_minutos["minutos"].' minute')).'T'.date('H:i:s', strtotime($_POST['in_f2'].'T'.$hora[0].':'.$hora[1].' + '.$data_minutos["minutos"].' minute')).'.000-05:00',
                                'description' => 'Ginecologia INMATER',
                            );

                            googlecalendar_actualizar($data);
                        } */
                    }
                }

                if (isset($_POST['medi_add']) && $_POST['medi_add'] == "AGREGAR") {
                    if ($_POST['medi_name'] <> '|' and $_POST['medi_dosis'] <> '' and $_POST['medi_frecuencia'] <> '' and $_POST['medi_cant_dias'] <> '' and $_POST['medi_init_fec'] <> '' and $_POST['medi_init_h'] <> '' and $_POST['medi_init_m'] <> '') {
                        updateMedi($_POST['idx'], $_POST['dni'], $_POST['medi_name'], $_POST['medi_dosis'], $_POST['medi_frecuencia'], $_POST['medi_cant_dias'], $_POST['medi_init_fec'], $_POST['medi_init_h'], $_POST['medi_init_m'], $_POST['medi_obs'], 0);
                    } else {
                        echo "<div id='alerta'> DEBE INGRESAR TODOS LOS CAMPOS DEL MEDICAMENTO </div>";
                    }
                }
            } else { ?>
                <script type="text/javascript">
                    var c = "<?php echo $coincidencias; ?>";
                    alert('Existe(n) ' + c + ' cruce(s) de horario. Vuelva a elegir otra fecha.');
                    reload();
                </script>
            <?php }
        }

        if ($_GET['id'] <> "") {
            $id = $_GET['id'];
            $rGine = $db->prepare("SELECT * FROM hc_gineco WHERE id=?");
            $rGine->execute(array($id));
            $gine = $rGine->fetch(PDO::FETCH_ASSOC);

            //userx de asesora
            $rGineAse = $db->prepare("SELECT ases.codigo cod 
                                    FROM appinmater_modulo.hc_gineco gine
                                    left JOIN appinmater_modulo.asesor_medico ases ON gine.asesor_medico_id = ases.id
                                    WHERE gine.id=?");
            $rGineAse->execute(array($id));
            $gineAse = $rGineAse->fetch(PDO::FETCH_ASSOC);

            // hora limite programacion
            $consulta = $db->prepare("SELECT valor FROM man_configuracion WHERE codigo=?");
            $consulta->execute(['fecha_programacion_ginecologia']);
            $fechaprogramacion_data = $consulta->fetch(PDO::FETCH_ASSOC);

            $rPaci = $db->prepare("SELECT nom,ape,fnac,talla,peso FROM hc_paciente WHERE dni=?");
            $rPaci->execute(array($gine['dni']));
            $paci = $rPaci->fetch(PDO::FETCH_ASSOC);

            $rAmh = $db->prepare("SELECT amh FROM hc_antece_perfi WHERE dni=? and amh<>''");
            $rAmh->execute(array($gine['dni']));

            $a_medi = $db->prepare("SELECT * FROM hc_agenda WHERE id=?");
            $a_medi->execute(array($id));

            $a_plan = $db->prepare("SELECT id, archivo_id, idp, fec, trim(plan) plan FROM hc_gineco_plan WHERE idp=?");
            $a_plan->execute(array($id));

            $rAux = $db->prepare("SELECT nom FROM hc_gineco_aux");
            $rAux->execute();

            $rMed = $db->prepare("SELECT nom,des FROM hc_gineco_med");
            $rMed->execute();

            $rInt = $db->prepare("SELECT id,med,esp FROM hc_gineco_int");
            $rInt->execute();

            $rAnal = $db->prepare("SELECT * FROM hc_analisis WHERE a_dni=? AND lab<>'eco' ORDER BY a_fec DESC");
            $rAnal->execute(array($gine['dni']));

            $rEco = $db->prepare("SELECT a_exa, a_sta, a_obs, CONCAT('analisis/', id, '_', a_dni, '.pdf') as link, a_fec FROM hc_analisis WHERE a_dni=? AND lab='eco' 
                UNION 
                SELECT CASE WHEN mot = '' THEN 'Primera ecografía beta positivo' ELSE mot END as a_exa, '' as a_sta, observaciones as a_obs, CONCAT('eco_beta_positivo.php?id_gine=".$id."&id=', id) as link, fec as a_fec FROM hc_eco_beta_positivo WHERE dni = ?
                UNION
                SELECT 'Eco consultorio externo' as a_exa, '' as a_sta, obs as a_obs, CONCAT('eco_consultorio/', documento, '/', nombre) as link, fconsulta as a_fec 
                FROM hc_eco_consultorio c INNER JOIN hc_eco_consultorio_img ci ON c.id = ci.id_eco_consultorio
                WHERE c.documento = ?
                GROUP BY ci.id_eco_consultorio,c.obs,c.documento,ci.nombre,c.fconsulta
                ORDER BY a_fec DESC");

            $rEco->execute(array($gine['dni'], $gine['dni'], $gine['dni'])); ?>

            <script>
                $(document).ready(function () {
                    var unsaved = false;

                    $(":input").change(function () {
                        unsaved = true;
                    });

                    $(window).on('beforeunload', function () {
                        if (unsaved) {
                            return 'UD. HA REALIZADO CAMBIOS';
                        }
                    });

                    $(document).on("submit", "form", function (event) {
                        $(window).off('beforeunload');
                    });

                    $("#repro").on('keydown paste', function(e) {
                        e.preventDefault();
                    });

                    $('.aux_insert').click(function (e) {
                        var tav = $('#aux').val(),
                            strPos = $('#aux')[0].selectionStart;
                        front = (tav).substring(0, strPos);
                        back = (tav).substring(strPos, tav.length);

                        $('#aux').val(front + '- ' + $(this).text() + '\n' + back);
                        $('#aux').textinput('refresh');
                        $('.fil_med li').addClass('ui-screen-hidden');
                        $('#aux').focus();
                    });

                    $('.med_insert').click(function (e) {
                        var tav = $('#medi').val(),
                            strPos = $('#medi')[0].selectionStart;
                        front = (tav).substring(0, strPos);
                        back = (tav).substring(strPos, tav.length);

                        $('#medi').val(front + '- ' + $(this).text() + ' ' + $(this).attr("data") + ' \n' + back);
                        $('#medi').textinput('refresh');
                        $('.fil_med li').addClass('ui-screen-hidden');
                        $('#medi').focus();
                    });

                    $("#repro_lista").change(function () {
                        var str = $('#repro').val();
                        var items = $(this).val();
                        var n = str.indexOf(items);

                        if (n == -1) {
                            $('#repro').val(items + ", " + str);
                            $('#repro').textinput('refresh');
                        }

                        if (items == "borrar_p") {
                            $('#repro').val("");
                        }

                        if (items == "NINGUNA") {
                            $('#repro').val("NINGUNA");
                        }

                        $(this).prop('selectedIndex', 0);
                        $(this).selectmenu("refresh", true);
                    });

                    $('.chekes').change(function () {
                        var temp = '#' + $(this).attr("id") + '1';

                        if ($(this).prop('checked') || $(this).val() == "Anormal") {
                            $(temp).prop('readonly', false);
                        } else {
                            $(temp).prop('readonly', true);
                            $(temp).val('');
                        }
                    });

                    $("#in_f2").on("change", function () {
                        var hoy = new Date();
                        hoy.setDate(hoy.getDate() + 1);
                        var dia_next = hoy.getFullYear() + '-' + ("0" + (hoy.getMonth() + 1)).slice(-2) + '-' + ("0" + hoy.getDate()).slice(-2);
                        var dia_aspi = $("#in_f2").val();
                        var programacion = $("#fecha_programacion_ginecologia").val();
                        
                        if (hoy.getHours() >= programacion && dia_next == dia_aspi) {
                            alert("Solo puede agendar para mañana hasta las " + programacion + " horas de hoy.");
                            $("#in_f2").val("");
                        }
                    });

                    $('#in_c').change(function () {
                        if ($(this).val() == 1) {
                            $('#in_t1-button').show();
                            $('#in_t2').hide();
                            $('#in_t2').textinput('disable');
                        }

                        if ($(this).val() == 2) {
                            $('#in_t2').textinput('enable');
                            $('#in_t2').show();
                            $('#in_t2').val('');
                            $('#in_t1-button').hide();
                        }
                    });
                    <?php
                    if ($gine['in_c'] == 1) { ?>
                        $('#in_t1-button').show();
                        $('#in_t2').hide();
                        $('#in_t2').textinput('disable');
                    <?php
                    } if ($gine['in_c'] == 2) { ?>
                        $('#in_t2').textinput('enable');
                        $('#in_t2').show();
                        $('#in_t1-button').hide();
                    <?php
                    } if ($gine['in_c'] == 0) { ?>
                        $('#in_t2').hide();
                        $('#in_t1-button').hide();
                    <?php }
                    $key=$_ENV["apikey"];
                    ?>
                });
            </script>

            <div data-role="header" data-position="fixed">
                <a href="n_gine.php?id=<?php echo $gine['dni']; ?>" rel="external" class="ui-btn">Cerrar</a>
                <h2>Ginecología:
                    <small><?php echo $paci['ape'] . " " . $paci['nom'];
                        if ($paci['fnac'] <> "1899-12-30") echo '(' . date_diff(date_create($paci['fnac']), date_create('today'))->y . ')'; ?></small>
                </h2>
                <a href="#popupVideo" data-rel="popup" data-position-to="window" class="ui-btn">Ver Antecedentes</a>
            </div>

            <div data-role="popup" id="popupVideo" data-overlay-theme="b" data-theme="a" data-tolerance="15,15" class="ui-content">
                <a href="#" data-rel="back" class="ui-btn ui-btn-b ui-corner-all ui-shadow ui-btn-a ui-icon-delete ui-btn-icon-notext ui-btn-left">Close</a>
                <iframe src="e_paci.php?id=<?php echo $gine['dni']; ?>&pop=1" seamless></iframe>
            </div>

            <div class="ui-content" role="main">
                <input type="hidden" name="login" id="login" value="<?php echo $login;?>">
                <input type="hidden" name="key" id="key" value="<?php echo $key;?>">
                <form action="" method="post" data-ajax="false" id="form2" name="form2">
                    <input type="hidden" name="fecha_programacion_ginecologia" id="fecha_programacion_ginecologia" value="<?php print($fechaprogramacion_data['valor']); ?>">
                    <input type="hidden" name="nombre" value="<?php echo $paci['ape'] . " " . $paci['nom']; ?>">
                    <input type="hidden" name="idx" value="<?php echo $gine['id']; ?>">
                    <input type="hidden" name="dni" value="<?php echo $gine['dni']; ?>">

                    <div style="display: inline-block;">
                        <div style="display: inline-block;" >
                                <div style="display: inline-block;">Medico Tratante:</div>
                                <div data-role="controlgroup" data-type="horizontal" data-mini="true" style="display: inline-block;">
                                    <select name="m_tratante" id="m_tratante" data-mini="true" required>
                                        <optgroup label="Lista de Medicos">
                                            <option value="">Seleccionar</option>
                                            <?php
                                            $medicos = listarMedicos();
                                            $selected = "";
                                            foreach ($medicos as $medico) {

                                                if ($gine['med'] == $medico['codigo']) {
                                                    $selected = "selected";
                                                } else {
                                                    $selected = "";
                                                }
                                                
                                                echo '<option value="'.$medico['codigo'].'" '.$selected.'>'.strtoupper($medico['nombre']).'</option>';
                                            }
                                            ?>
                                        </optgroup>
                                    </select>
                                </div>
                        </div>
                        
                        <div style="display: inline-block;">
                                <div style="display: inline-block;">Asesora:</div>
                                <div style="display: inline-block;" data-role="controlgroup" data-type="horizontal" data-mini="true">
                                        <select name="asesora" id="asesora" data-mini="true">
                                            <option value="">Seleccionar</option>
                                            <?php
                                                        $aMedico = $db->prepare("SELECT id,upper(apellidos || ' ' || nombres)nombre FROM asesor_medico where eliminado=0 ");
                                                        $aMedico->execute();
                                                        $selected = "";
                                                        while ($asesor = $aMedico->fetch(PDO::FETCH_ASSOC)) {

                                                            if ($gine['asesor_medico_id'] == $asesor['id']) {
                                                                $selected = "selected";
                                                            } else {
                                                                $selected = "";
                                                            }

                                                            print("<option value=".$asesor['id']." $selected>".$asesor['nombre']."</option>");
                                                        }
                                                    ?>
                                        </select>
                                </div>
                        </div>

                        <div style="display: inline-block;">
                                <div style="display: inline-block;">Sede:</div>
                                <div style="display: inline-block;" data-role="controlgroup" data-type="horizontal" data-mini="true">
                                        <select name="cupon" id="cupon" data-mini="true">
                                            <option value="0" selected>Seleccione Sede</option>
                                            <?php
                                                $stmt = $db->prepare("SELECT codigo_facturacion codigo, nombre from sedes where estado_consulta = 1 order by nombre");
                                                $stmt->execute();
                                                $selected = "";

                                                while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {

                                                    if ($gine['cupon'] == $data['codigo'] ) {
                                                        $selected = "selected";
                                                    } else {
                                                        $selected = "";
                                                    }

                                                    print('<option value="' . $data['codigo'] . '" '. $selected.'>'. ucwords(mb_strtolower($data['nombre'])) . '</option>');
                                                } ?>
                                        </select>
                                </div>
                        </div>
                    </div>
                    
                    <table width="100%" align="center" style="margin: 0 auto;">
                        <tr>
                            <td>Fecha</td>
                            <td>
                                <div data-role="controlgroup" data-type="horizontal" data-mini="true">
                                    <input name="fec" type="date" id="fec" value="<?php echo $gine['fec']; ?>" data-wrapper-class="controlgroup-textinput ui-btn">
                                    <select name="fec_h" id="fec_h">
                                        <option value="">Hra</option>
                                        <option value="07" <?php if ($gine['fec_h'] == "07") echo "selected"; ?>>07 hrs</option>
                                        <option value="08" <?php if ($gine['fec_h'] == "08") echo "selected"; ?>>08 hrs</option>
                                        <option value="09" <?php if ($gine['fec_h'] == "09") echo "selected"; ?>>09 hrs</option>
                                        <option value="10" <?php if ($gine['fec_h'] == "10") echo "selected"; ?>>10 hrs</option>
                                        <option value="11" <?php if ($gine['fec_h'] == "11") echo "selected"; ?>>11 hrs</option>
                                        <option value="12" <?php if ($gine['fec_h'] == "12") echo "selected"; ?>>12 hrs</option>
                                        <option value="13" <?php if ($gine['fec_h'] == "13") echo "selected"; ?>>13 hrs</option>
                                        <option value="14" <?php if ($gine['fec_h'] == "14") echo "selected"; ?>>14 hrs</option>
                                        <option value="15" <?php if ($gine['fec_h'] == "15") echo "selected"; ?>>15 hrs</option>
                                        <option value="16" <?php if ($gine['fec_h'] == "16") echo "selected"; ?>>16 hrs</option>
                                        <option value="17" <?php if ($gine['fec_h'] == "17") echo "selected"; ?>>17 hrs</option>
                                        <option value="18" <?php if ($gine['fec_h'] == "18") echo "selected"; ?>>18 hrs</option>
                                        <option value="19" <?php if ($gine['fec_h'] == "19") echo "selected"; ?>>19 hrs</option>
                                        <option value="20" <?php if ($gine['fec_h'] == "20") echo "selected"; ?>>20 hrs</option>
                                    </select>
                                    <select name="fec_m" id="fec_m">
                                        <option value="">Min</option>
                                        <option value="00" <?php if ($gine['fec_m'] == "00") echo "selected"; ?>>00 min</option>
                                        <option value="15" <?php if ($gine['fec_m'] == "15") echo "selected"; ?>>15 min</option>
                                        <option value="30" <?php if ($gine['fec_m'] == "30") echo "selected"; ?>>30 min</option>
                                        <option value="45" <?php if ($gine['fec_m'] == "45") echo "selected"; ?>>45 min</option>
                                    </select>
                                </div>
                            </td>
                            <td align="right">
                                <select name="select" id="repro_lista" data-mini="true" data-inline="true">
                                    <option value="" selected>Reproducción Asistida:</option>
                                    <option value="NINGUNA">*** NINGUNA ***</option>
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
                            </td>
                            <td><textarea name="repro" id="repro" data-mini="true"><?php echo $gine['repro']; ?></textarea></td>
                        </tr>
                    </table>

                    <div data-role="collapsibleset" data-theme="a" data-content-theme="a" data-mini="true">
                        <div data-role="collapsible" data-collapsed="false"><h3>Consulta</h3>
                            <div class="scroll_h">
                                <table width="100%" align="center" style="margin: 0 auto;max-width:800px;">
                                    <tr>
                                        <td>Motivo de Consulta
                                            <textarea name="mot" id="mot" data-mini="true"><?php echo $gine['mot']; ?></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Diagnóstico
                                            <textarea name="dig" id="dig" data-mini="true"><?php echo $gine['dig']; ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="enlinea" style="border:dotted"><i style="margin: 0 auto;">AGREGAR MEDICAMENTOS:</i><br>
                                                <?php
                                                require("_database/database_farmacia.php");
                                                $Rmedi = $farma->prepare("SELECT id,producto FROM tblproducto"); $Rmedi->execute(); ?>
                                                <input name="medi_name" list="cate" placeholder="Medicamento..">
                                                <datalist id="cate">
                                                    <?php while ($medic = $Rmedi->fetch(PDO::FETCH_ASSOC)) {
                                                        echo '<option value="'.$medic['id'].'|'.$medic['producto'].'"></option>';
                                                    } ?>
                                                </datalist>
                                                <input name="medi_dosis" type="text" data-mini="true" placeholder="Dosis.."/>
                                                <span class="peke2">
                                                    <input name="medi_frecuencia" type="number" data-mini="true" placeholder="Frecuencia.."/>
                                                    <input name="medi_cant_dias" type="number" data-mini="true" placeholder="Dias.."/>
                                                </span><br>
                                                <small>Fecha de inicio</small>
                                                <br><input name="medi_init_fec" type="date" data-mini="true" placeholder="Fecha de inicio.."/>
                                                <span class="peke2">
                                                    <input name="medi_init_h" type="number" data-mini="true" placeholder="Hora.."/>
                                                    <input name="medi_init_m" type="number" data-mini="true" placeholder="Minutos.."/>
                                                </span>
                                                <div><textarea name="medi_obs" data-mini="true" data-inline="true"
                                                            placeholder="Observaciones.."></textarea></div>

                                                <?php if ($gine['med'] == $login || $gineAse['cod'] == $login || $gine['idusercreate'] == $login || date('Y-m-d') == $gine['fec']) { ?>
                                                <input type="Submit" name="medi_add" value="AGREGAR" data-mini="true"
                                                    data-theme="b" data-inline="true"/>
                                                <?php } ?>
                                            </div>

                                            <div id="print_med" style="border:dotted;">
                                                <?php if ($a_medi->rowCount() > 0) { // la tabla tiene 559px = ancho de hoja A5 ?>
                                                    <table width="100%" cellspacing="4" style="margin: 0 auto;"
                                                        class="ui-responsive table-stroke">
                                                        <tr>
                                                            <th width="39%">Medicamento</th>
                                                            <th width="6%">Dosis</th>
                                                            <th width="11%">Frecuencia</th>
                                                            <th width="5%">Dias</th>
                                                            <th width="14%">Fecha de inicio</th>
                                                        </tr>
                                                        <?php while ($medi = $a_medi->fetch(PDO::FETCH_ASSOC)) { ?>
                                                            <tr style="font-size:small">
                                                                <td>
                                                                    <a href="e_gine_medi.php?id=<?php echo $medi['id_agenda']; ?>" rel="external"><?php echo $medi['medi_name']; ?></a>
                                                                    <?php if ($medi['medi_obs'] <> '') echo '<br>Observaciones: '.$medi['medi_obs']; ?>
                                                                </td>
                                                                <td align="center"><?php echo $medi['medi_dosis']; ?></td>
                                                                <td align="center"><?php echo $medi['medi_frecuencia']; ?></td>
                                                                <td align="center"><?php echo $medi['medi_cant_dias']; ?></td>
                                                                <td align="center"><?php echo date("d-m-Y", strtotime($medi['medi_init_fec'])).' '.$medi['medi_init_h'].':'.$medi['medi_init_m']; ?></td>
                                                            </tr>
                                                        <?php } ?>
                                                    </table>
                                                <?php } ?>
                                                <pre><?php echo $gine['medi']; ?></pre>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div data-role="collapsible"><h3>Exámenes Realizados por el Médico</h3>
                            <div class="scroll_h">
                                <table width="100%" align="center" style="margin: 0 auto;max-width:800px;">
                                    <tr>
                                        <td width="4%">Fecha</td>
                                        <td width="11%">
                                            <input name="efec" type="date" id="efec" data-mini="true" value="<?php echo $gine['efec']; ?>"/>
                                        </td>
                                        <td width="8%">Ciclo</td>
                                        <td width="11%">
                                            <input name="cic" type="text" id="cic" data-mini="true" value="<?php echo $gine['cic']; ?>">
                                        </td>
                                        <td colspan="2">
                                            <select name="eco" id="eco" data-mini="true">
                                                <option value="" selected="selected">Ecografía:</option>
                                                <option value="Normal" <?php if ($gine['eco'] == "Normal") echo "selected"; ?>>
                                                    Normal
                                                </option>
                                                <optgroup label="Anormal: SI">
                                                    <option value="Fondo saco vaginal" <?php if ($gine['eco'] == "Fondo saco vaginal") echo "selected"; ?>>
                                                        Anormal: Fondo saco vaginal
                                                    </option>
                                                    <option value="Cuerpo uterino" <?php if ($gine['eco'] == "Cuerpo uterino") echo "selected"; ?>>
                                                        Anormal: Cuerpo uterino
                                                    </option>
                                                    <option value="Anexo Derecho" <?php if ($gine['eco'] == "Anexo Derecho") echo "selected"; ?>>
                                                        Anormal: Anexo Derecho
                                                    </option>
                                                    <option value="Anexo Izquierdo" <?php if ($gine['eco'] == "Anexo Izquierdo") echo "selected"; ?>>
                                                        Anormal: Anexo Izquierdo
                                                    </option>
                                                </optgroup>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <select name="vul" id="vul" data-mini="true">
                                                <option value="" selected="selected">Vulva:</option>
                                                <option value="Normal" <?php if ($gine['vul'] == "Normal") echo "selected"; ?>>Vulva: Normal</option>
                                                <option value="Anormal" <?php if ($gine['vul'] == "Anormal") echo "selected"; ?>>Vulva: Anormal</option>
                                            </select>
                                        </td>
                                        <td colspan="2">
                                            <select name="vag" id="vag" data-mini="true">
                                                <option value="" selected="selected">Vagina:</option>
                                                <option value="tipo 1" <?php if ($gine['vag'] == "tipo 1") echo "selected"; ?>>Vagina: tipo 1</option>
                                                <option value="tipo 2" <?php if ($gine['vag'] == "tipo 2") echo "selected"; ?>>Vagina: tipo 2</option>
                                                <option value="tipo 3" <?php if ($gine['vag'] == "tipo 3") echo "selected"; ?>>Vagina: tipo 3</option>
                                                <option value="tipo 4" <?php if ($gine['vag'] == "tipo 4") echo "selected"; ?>>Vagina: tipo 4</option>
                                            </select>
                                        </td>
                                        <td width="9%">
                                            <fieldset data-role="controlgroup" data-type="horizontal">
                                                <select name="mam" id="mam" data-mini="true" class="chekes">
                                                    <option value="" selected="selected">Ex. Mama:</option>
                                                    <option value="Normal" <?php if ($gine['mam'] == "Normal") echo "selected"; ?>>Normal</option>
                                                    <option value="Anormal" <?php if ($gine['mam'] == "Anormal") echo "selected"; ?>>Anormal</option>
                                                </select>
                                            </fieldset>
                                        </td>
                                        <td width="57%"><input name="mam1" type="text" id="mam1" data-mini="true"
                                                            placeholder="Especifique.." readonly
                                                            value="<?php echo $gine['mam1']; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">Tacto vaginal</td>
                                        <td colspan="2">
                                            <input name="t_vag" type="text" id="t_vag" data-mini="true" value="<?php echo $gine['t_vag']; ?>">
                                        </td>
                                        <td>
                                            <fieldset data-role="controlgroup" data-type="horizontal">
                                                <select name="cer" id="cer" data-mini="true" class="chekes">
                                                    <option value="" selected="selected">Cervix:</option>
                                                    <option value="Normal" <?php if ($gine['cer'] == "Normal") echo "selected"; ?>>
                                                        Normal
                                                    </option>
                                                    <option value="Anormal" <?php if ($gine['cer'] == "Anormal") echo "selected"; ?>>
                                                        Anormal
                                                    </option>
                                                </select>
                                            </fieldset>
                                        </td>
                                        <td>
                                            <input name="cer1" type="text" id="cer1" data-mini="true" placeholder="Especifique.." readonly value="<?php echo $gine['cer1']; ?>">
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div data-role="collapsible"><h3>Orden de Análisis Clínicos</h3>
                            <ul data-role="listview" data-theme="c" data-inset="true" data-filter="true"
                                data-filter-reveal="true" data-filter-placeholder="Agregar exámenes auxiliares..."
                                data-mini="true" class="fil_med" data-icon="false">
                                <?php
                                while ($aux = $rAux->fetch(PDO::FETCH_ASSOC)) { ?>
                                    <li><a href="#" class="aux_insert"><?php echo $aux['nom']; ?></a></li>
                                <?php } ?>
                            </ul>
                            <label for="aux">Exámenes Auxiliares seleccionados:</label>
                            <div id="print_aux"><textarea name="aux" id="aux" data-mini="true"><?php echo $gine['aux']; ?></textarea></div>
                        </div>

                        <div data-role="collapsible"><h3>Resultados de Análisis Clínicos</h3>
                            <?php if ($rAnal->rowCount() > 0) { ?>
                                <table style="font-size:small;" data-role="table" class="ui-responsive table-stroke">
                                    <thead>
                                    <tr>
                                        <th>OTROS EXAMENES</th>
                                        <th>RESULTADO</th>
                                        <th>OBSERVACION</th>
                                        <th>INFORME</th>
                                        <th>FECHA</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php while ($anal = $rAnal->fetch(PDO::FETCH_ASSOC)) { ?>
                                        <tr>
                                            <th><?php echo $anal['a_exa']; ?></th>
                                            <td><?php echo $anal['a_sta']; ?></td>
                                            <td><?php echo $anal['a_obs']; ?></td>
                                            <th>
                                                <a href='<?php echo "archivos_hcpacientes.php?idArchivo=" . $anal['id'] . "_" . $anal['a_dni']; ?>'
                                                target="new">Ver/Descargar</a></th>
                                            <td><?php echo date("d-m-Y", strtotime($anal['a_fec'])); ?></td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            <?php } ?>
                            OTROS <textarea name="e_sol" id="e_sol" data-mini="true"><?php echo $gine['e_sol']; ?></textarea>
                        </div>

                        <div data-role="collapsible"><h3>Resultados de Ecografías</h3>
                            <?php
                            if ($rEco->rowCount() > 0) { ?>
                                <table style="font-size:small;" data-role="table" class="ui-responsive table-stroke">
                                    <thead>
                                    <tr>
                                        <th>ECOGRAFÍA</th>
                                        <th>RESULTADO</th>
                                        <th>OBSERVACION</th>
                                        <th>INFORME</th>
                                        <th>FECHA</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php while ($anal = $rEco->fetch(PDO::FETCH_ASSOC)) { ?>
                                        <tr>
                                            <th><?php echo $anal['a_exa']; ?></th>
                                            <td><?php echo $anal['a_sta']; ?></td>
                                            <td><?php echo $anal['a_obs']; ?></td>
                                            <th>
                                                <a href='<?php echo $anal['link'] ?>'
                                                target="new">Ver/Descargar</a></th>
                                            <td><?php echo date("d-m-Y", strtotime($anal['a_fec'])); ?></td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                                <a href="n_eco_add.php?dni=<?php echo $gine['dni']; ?>&id_gine=<?php echo $gine['id']; ?>" rel="external" class="ui-btn ui-btn-inline ui-mini">
                                    <!-- <img src="_libraries/open-iconic/svg/plus.svg" height="18" width="18" alt="icon name"> -->
                                    Agregar
                                </a>
                            <?php } ?>
                        </div>

                        <div data-role="collapsible" id="Plan"><h3>Plan de Trabajo</h3>
                            <a href="e_gine_plan.php?dni=<?php echo $gine['dni'] . "&idp=" . $gine['id'] . "&id="; ?>"
                                rel="external" class="ui-btn ui-btn-inline ui-mini" style="float:left">Agregar</a>
                            <table data-role="table" data-mode="reflow" width="800" style="margin: 0 auto;max-width:800px;" class="ui-responsive table-stroke">
                                <thead>
                                    <tr>
                                        <th data-priority="1" align="left">Fecha</th>
                                        <th data-priority="2" align="left">Plan Trabajo</th>
                                        <th data-priority="3" align="left">Imagen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($plan = $a_plan->fetch(PDO::FETCH_ASSOC)) { ?>
                                        <tr style="font-size:small">
                                            <td><a href="e_gine_plan.php?dni=<?php echo $gine['dni'] . "&idp=" . $gine['id'] . "&id=" . $plan['id']; ?>" rel="external"><?php echo date("d-m-Y", strtotime($plan['fec'])); ?></a></td>
                                            <td><div><?php echo $plan['plan']; ?></div></td>
                                            <td>
                                                <?php
                                                $stmt = $db->prepare("SELECT * from man_archivo where id = ?;");
                                                $stmt->execute([$plan["archivo_id"]]);
                                                $foto = $stmt->fetch(PDO::FETCH_ASSOC);
                                                $foto_url = 'archivos_hcpacientes.php?idPaci=' . $gine['dni'] . '/' . $gine['id'] . '/' . $plan['id'] . '/foto.jpg';

                                                if (file_exists('storage/ginecologia_plan_trabajo/' . $foto['nombre_base'])) {
                                                    print('<em><a href="archivos_hcpacientes.php?idStorage=ginecologia_plan_trabajo/' . $foto['nombre_base'] . '" target="new" style="margin: .446em; font-size: 12px;">' . $foto['nombre_original'] . '</a></em>');
                                                }

                                                if (file_exists('paci/' . $gine['dni'] . '/' . $gine['id'] . '/' . $plan['id'] . '/foto.jpg')) {
                                                    echo '<br><em><a href=' . $foto_url . ' target="_blank" style="margin: .446em; font-size: 12px;">Ver</a></em>';
                                                }?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <?php if ($a_plan->rowCount() < 1) echo '<p><h3>¡ No hay datos aún !</h3></p>'; ?>
                        </div>

                        <div data-role="collapsible">
                            <h3>Agendar una Interconsulta</h3>
                            <?php
                            $demo=$db->prepare("SELECT interconsulta_id FROM hc_gineco WHERE id=?;");
                            $demo->execute([$id]);
                            $data_demo=$demo->fetch(PDO::FETCH_ASSOC);
                            $interconsulta_id=0;

                            if ($data_demo['interconsulta_id'] == 0) {
                                $info_demo=[];
                            } else {
                                $demo=$db->prepare("SELECT * FROM hc_gineco WHERE id=?;");
                                $demo->execute([$data_demo['interconsulta_id']]);
                                $info_demo=$demo->fetch(PDO::FETCH_ASSOC);
                                $interconsulta_id=$data_demo['interconsulta_id'];
                            }

                            print('<input type="hidden" name="interconsulta_id" id="interconsulta_id" value="' . $data_demo['interconsulta_id'] . '">'); ?>

                            <div class="ui-grid-a">
                                <div class="ui-block-a">
                                    <legend><small>Tipo de consulta:</small></legend>
                                    <fieldset data-role="controlgroup" data-type="horizontal" id="interconsulta_tipoconsulta">
                                        <?php
                                            print('
                                            <input type="radio" name="interconsulta_tipoconsulta" id="tipoconsulta_presencial" data-mini="true" value="1" '.(isset($info_demo['tipoconsulta_ginecologia_id']) && $info_demo['tipoconsulta_ginecologia_id'] == 1 ? 'checked' : '').'>
                                            <label for="tipoconsulta_presencial">Presencial</label>
                                            <input type="radio" name="interconsulta_tipoconsulta" id="tipoconsulta_virtual" data-mini="true" value="2" '.(isset($info_demo['tipoconsulta_ginecologia_id']) && $info_demo['tipoconsulta_ginecologia_id'] == 2 ? 'checked' : '').'>
                                            <label for="tipoconsulta_virtual">Virtual</label>');
                                        ?>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="ui-grid-a">
                                <div class="ui-block-a">
                                    <legend><small>Sede y médico:</small></legend>
                                    <div data-role="controlgroup" data-type="horizontal" data-mini="true">
                                        <select name="interconsulta_sede" id="interconsulta_sede">
                                            <option value="0" selected>Seleccione Sede</option>
                                            <?php
                                            $demo=$db->prepare("SELECT codigo_facturacion codigo, nombre FROM sedes WHERE estado_consulta = 1 ORDER BY nombre;");
                                            $demo->execute();

                                            while ($data_demo = $demo->fetch(PDO::FETCH_ASSOC)) {
                                                $selected='';
                                                if ($data_demo['codigo'] == $info_demo['cupon']) {
                                                    $selected='selected';
                                                }
                                                print('<option value="' . $data_demo['codigo'] . '" ' . $selected . '> ' . ucwords(mb_strtolower($data_demo['nombre'])) . '</option>');
                                            } ?>
                                        </select>

                                        <select name="interconsulta_medico" id="interconsulta_medico" data-mini="true">
                                            <option value="" selected>Seleccione Médico</option>
                                            <?php
                                            $demo=$db->prepare("SELECT codigo, nombre FROM man_medico WHERE estado_consulta = 1 ORDER BY nombre;");
                                            $demo->execute();

                                            while ($data_demo = $demo->fetch(PDO::FETCH_ASSOC)) {
                                                $selected='';
                                                if ($data_demo['codigo'] == $info_demo['med']) {
                                                    $selected='selected';
                                                }
                                                print('<option value="' . $data_demo['codigo'] . '" ' . $selected . '> ' . ucwords(mb_strtolower($data_demo['nombre'])) . '</option>');
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="ui-grid-a">
                                <div class="ui-block-a">
                                    <legend><small>Fecha y hora:</small></legend>
                                    <div data-role="controlgroup" data-type="horizontal" data-mini="true">
                                        <?php
                                        print('<input name="interconsulta_fecha" type="date" id="interconsulta_fecha" value="'.($interconsulta_id == 0 ? date("Y-m-d") : $info_demo['fec'] ).'" data-wrapper-class="controlgroup-textinput ui-btn">'); ?>

                                        <select name="interconsulta_hora" id="interconsulta_hora">
                                            <option value="">Hra</option>
                                            <?php
                                            print('<option value="07" '.($info_demo['fec_h'] == '07' ? 'selected' : '').'>07 hrs</option>');
                                            print('<option value="08" '.($info_demo['fec_h'] == '08' ? 'selected' : '').'>08 hrs</option>');
                                            print('<option value="09" '.($info_demo['fec_h'] == '09' ? 'selected' : '').'>09 hrs</option>');
                                            print('<option value="10" '.($info_demo['fec_h'] == '10' ? 'selected' : '').'>10 hrs</option>');
                                            print('<option value="11" '.($info_demo['fec_h'] == '11' ? 'selected' : '').'>11 hrs</option>');
                                            print('<option value="12" '.($info_demo['fec_h'] == '12' ? 'selected' : '').'>12 hrs</option>');
                                            print('<option value="13" '.($info_demo['fec_h'] == '13' ? 'selected' : '').'>13 hrs</option>');
                                            print('<option value="14" '.($info_demo['fec_h'] == '14' ? 'selected' : '').'>14 hrs</option>');
                                            print('<option value="15" '.($info_demo['fec_h'] == '15' ? 'selected' : '').'>15 hrs</option>');
                                            print('<option value="16" '.($info_demo['fec_h'] == '16' ? 'selected' : '').'>16 hrs</option>');
                                            print('<option value="17" '.($info_demo['fec_h'] == '17' ? 'selected' : '').'>17 hrs</option>');
                                            print('<option value="18" '.($info_demo['fec_h'] == '18' ? 'selected' : '').'>18 hrs</option>');
                                            print('<option value="19" '.($info_demo['fec_h'] == '19' ? 'selected' : '').'>19 hrs</option>');
                                            print('<option value="20" '.($info_demo['fec_h'] == '20' ? 'selected' : '').'>20 hrs</option>'); ?>
                                        </select>

                                        <select name="interconsulta_minutos" id="interconsulta_minutos">
                                            <option value="">Min</option>
                                            <?php
                                            print('<option value="00" '.($info_demo['fec_m'] == '00' ? 'selected' : '').'>00 min</option>');
                                            print('<option value="15" '.($info_demo['fec_m'] == '15' ? 'selected' : '').'>15 min</option>');
                                            print('<option value="30" '.($info_demo['fec_m'] == '30' ? 'selected' : '').'>30 min</option>');
                                            print('<option value="45" '.($info_demo['fec_m'] == '45' ? 'selected' : '').'>45 min</option>'); ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="ui-grid-a">
                                <div class="ui-block-a">
                                    <legend><small>Procedimientos :</small></legend>
                                    <select name="interconsulta_motivo" id="interconsulta_motivo" data-mini="true">
                                        <option value="" selected>Seleccionar</option>
                                        <?php
                                        $demo=$db->prepare("SELECT id, nombre from man_gine_motivoconsulta where estado = 1 order by nombre;");
                                        $demo->execute();

                                        while ($data_demo = $demo->fetch(PDO::FETCH_ASSOC)) {
                                            $selected='';
                                            if ($data_demo['id'] == $info_demo['man_motivoconsulta_id']) {
                                                $selected='selected';
                                            }
                                            print('<option value="'.$data_demo['id'].'" '.$selected.'>'.ucwords(mb_strtolower($data_demo['nombre'])).'</option>');
                                        } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="ui-grid-a">
                                <div class="ui-block-a">
                                    <textarea name="interconsulta_observacion" id="interconsulta_observacion" data-mini="true"><?php if(isset($info_demo['mot'])) echo $info_demo['mot']; ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div data-role="collapsible">
                            <h3>Orden de Internamiento</h3>

                            <table width="100%" align="center" style="margin: 0 auto;">
                                <tr>
                                    <td width="5%">Lugar:</td>
                                    <td width="23%">
                                        <select name="in_c" id="in_c" data-mini="true">
                                            <option value="">Seleccionar</option>
                                            <option value=1 <?php if ($gine['in_c'] == 1) echo "selected"; ?>>SALA DE PROCEDIMIENTOS</option>
                                            <option value=2 <?php if ($gine['in_c'] == 2) echo "selected"; ?>>CONSULTORIO</option>
                                        </select>
                                    </td>
                                    <td width="21%">Fecha/Hora de Internamiento</td>
                                    <td width="51%">
                                        <div data-role="controlgroup" data-type="horizontal" data-mini="true">
                                            <input type="date" name="in_f1" id="in_f1" value="<?php if($gine['in_f1']!='1899-12-30')echo $gine['in_f1']; ?>"
                                                data-mini="true" data-wrapper-class="controlgroup-textinput ui-btn">
                                            <select name="in_h1" id="in_h1">
                                                <option value="">Hora</option>
                                                <option value="07" <?php if ($gine['in_h1'] == "07") echo "selected"; ?>>07 hrs</option>
                                                <option value="08" <?php if ($gine['in_h1'] == "08") echo "selected"; ?>>08 hrs</option>
                                                <option value="09" <?php if ($gine['in_h1'] == "09") echo "selected"; ?>>09 hrs</option>
                                                <option value="10" <?php if ($gine['in_h1'] == "10") echo "selected"; ?>>10 hrs</option>
                                                <option value="11" <?php if ($gine['in_h1'] == "11") echo "selected"; ?>>11 hrs</option>
                                                <option value="12" <?php if ($gine['in_h1'] == "12") echo "selected"; ?>>12 hrs</option>
                                                <option value="13" <?php if ($gine['in_h1'] == "13") echo "selected"; ?>>13 hrs</option>
                                                <option value="14" <?php if ($gine['in_h1'] == "14") echo "selected"; ?>>14 hrs</option>
                                                <option value="15" <?php if ($gine['in_h1'] == "15") echo "selected"; ?>>15 hrs</option>
                                                <option value="16" <?php if ($gine['in_h1'] == "16") echo "selected"; ?>>16 hrs</option>
                                                <option value="17" <?php if ($gine['in_h1'] == "17") echo "selected"; ?>>17 hrs</option>
                                                <option value="18" <?php if ($gine['in_h1'] == "18") echo "selected"; ?>>18 hrs</option>
                                                <option value="19" <?php if ($gine['in_h1'] == "19") echo "selected"; ?>>19 hrs</option>
                                                <option value="20" <?php if ($gine['in_h1'] == "20") echo "selected"; ?>>20 hrs</option>
                                            </select>
                                            <select name="in_m1" id="in_m1">
                                                <option value="">Min</option>
                                                <option value="00" <?php if ($gine['in_m1'] == "00") echo "selected"; ?>>00 min </option>
                                                <option value="15" <?php if ($gine['in_m1'] == "15") echo "selected"; ?>>15 min </option>
                                                <option value="30" <?php if ($gine['in_m1'] == "30") echo "selected"; ?>>30 min</option>
                                                <option value="45" <?php if ($gine['in_m1'] == "45") echo "selected"; ?>>45 min </option>
                                            </select>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Procedimientos:</td>
                                    <td width="23%">
                                        <select name="in_t" id="in_t1" data-mini="true">
                                            <option value="">SELECCIONAR</option>
                                            <?php
                                            $consulta = $db->prepare("SELECT id, nombre from man_gineco_tipo_intervencion where estado = 1 order by nombre asc");
                                            $consulta->execute();

                                            while ($data = $consulta->fetch(PDO::FETCH_ASSOC)) {
                                                if ($data['id'] != 11 || ($data['id'] == 11 && $rAmh->rowCount() > 0 && $paci['talla']<>'' && $paci['peso']<>'')) { ?>
                                                    <option value="<?php echo $data['nombre']; ?>"
                                                        <?php if ($data['nombre'] == $gine['in_t']) print(' selected'); ?>>
                                                        <?php print(mb_strtoupper($data['nombre'])); ?>
                                                    </option>
                                                <?php }
                                            } ?>
                                        </select>
                                        <input name="in_t" type="text" id="in_t2" data-mini="true" value="<?php echo $gine['in_t']; ?>"/></td>
                                    <td>Fecha/Hora de Intervención</td>
                                    <td>
                                        <div data-role="controlgroup" data-type="horizontal" data-mini="true">
                                            <input type="date" name="in_f2" id="in_f2" value="<?php if($gine['in_f2']!='1899-12-30')echo $gine['in_f2']; ?>" data-mini="true" data-wrapper-class="controlgroup-textinput ui-btn">
                                            <select name="in_hora" id="in_hora">
                                                <option value="">Seleccione Hora</option>
                                                <?php
                                                $gine_permisos = 'and ginecologia = 1';
                                                if ($login == 'jose.goncalves') {
                                                    $gine_permisos = 'and id <= 55 and id >= 30';
                                                }

                                                $consulta = $db->prepare("SELECT nombre from man_hora where estado = 1 $gine_permisos order by codigo asc");
                                                $consulta->execute();
                                                while ($data = $consulta->fetch(PDO::FETCH_ASSOC)) { ?>
                                                    <option value="<?php echo $data['nombre']; ?>"
                                                        <?php
                                                        if ($data['nombre'] == $gine['in_h2'].':'.$gine['in_m2'])
                                                            echo 'selected'; ?>>
                                                        <?php echo mb_strtolower($data['nombre']); ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                            <!-- turno -->
                                            <select name="idturno" id="idturno" data-mini="true">
                                                <option value="">Seleccione Turno</option>
                                                <?php
                                                    $idturno = intval($gine["idturno_inter"]) ?? 0;
                                                    $consulta = $db->prepare("SELECT codigo, nombre from man_turno_reproduccion where estado = 1 and aspiracion = true order by nombre asc");
                                                    $consulta->execute();

                                                    while ($data = $consulta->fetch(PDO::FETCH_ASSOC)) { ?>
                                                    <option value="<?php echo $data['codigo']; ?>"
                                                        <?php
                                                        if ($data['codigo'] == $gine["idturno_inter"])
                                                            echo 'selected'; ?>>
                                                        <?php echo mb_strtolower($data['nombre']); ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        
                                        <font color="#E34446">Solo puede agendar para mañana hasta las 3pm de hoy</font>
                                    </td>
                                </tr>
                            </table>
                            <iframe src="agenda.php?med=" width="100%" height="800" seamless></iframe>
                        </div>
                    </div>

                    <?php
                    if ($gine['med'] == $login || $gineAse['cod'] == $login || $gine['idusercreate'] == $login || date('Y-m-d') == $gine['fec']) { ?>
                        <div class="enlinea">
                            Motivo de la cancelación
                            <textarea name="cancela_motivo" id="cancela_motivo" data-mini="true"><?php echo $gine['cancela_motivo']; ?></textarea></td>
                            <input type="Submit" value="GUARDAR DATOS" data-icon="check" data-iconpos="left" data-mini="true"
                                class="show-page-loading-msg" data-textonly="false" data-textvisible="true"
                                data-msgtext="Actualizando datos.." data-theme="b" data-inline="true"/>
                            <input type="checkbox" name="cancela" id="cancela" data-mini="true" value=1 <?php if ($gine['cancela'] == 1) { echo "checked"; } ?>>
                            <label for="cancela">Cancelar Procedimiento</label>
                            <a href="javascript:PrintElem('#print_med','<?php echo $paci['ape'] . " " . $paci['nom']; ?>',1,'<?php echo date("d-m-Y", strtotime($gine['fec'])); ?>')"
                                data-role="button" data-mini="true" data-inline="true" rel="external">Imprimir Medicamentos</a>
                            <?php
                            if ($gine['aux'] <> '') { ?>
                                <a href="javascript:PrintElem('#print_aux','<?php echo $paci['ape'] . " " . $paci['nom']; ?>',2,'<?php echo date("d-m-Y", strtotime($gine['fec'])); ?>')"
                                    data-role="button" data-mini="true" data-inline="true" rel="external">Imprimir Orden de Análisis Clínicos</a>
                            <?php } ?>
                        </div>
                    <?php } else {
                        echo '<font color="#E34446"><b>PERMISO DE EDICION SÓLO PARA: </b> ' . $gine['med'] . ', '.$gineAse['cod']. ', '.$gine['idusercreate'].'</font>';
                    } ?>
                </form>
            </div>
        <?php } ?>
    </div>
    <script src="js/e_gine.js"></script>
</body>
</html>