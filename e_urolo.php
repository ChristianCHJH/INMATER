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

            $(".ui-popup iframe")
                .attr("width", 0)
                .attr("height", "auto");

            $("#popupVideo").on({
                popupbeforeposition: function () {
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
            mywindow.document.write('<style> @page {size:A5;margin: 0px 0px 0px 0px;} .noPrint { display: none !important; } table, th, td { border: 0.5px solid black; border-collapse: collapse; } </style>');
            mywindow.document.write('</head><body><div style="margin: 0 auto;width:500px"><br><br><br><br><br><br><br><br><br>');
            if (tipo == 1) mywindow.document.write('<h2>Medicamentos</h2><p><i style="float:right">Fecha: ' + fec + '</i><br><b>PACIENTE:</b><br> ' + paci + '</p>');
            if (tipo == 2) mywindow.document.write('<h2>Orden de Análisis Clínicos</h2><p><i style="float:right">Fecha: ' + fec + '</i><br>Paciente: ' + paci + '</p>');
            mywindow.document.write(data);
            mywindow.document.write('<script type="text/javascript">window.print();<' + '/script>');
            mywindow.document.write('</div></body></html>');
            return true;
        }
    </script>

    <style>
        .controlgroup-textinput {
            padding-top: .10em;
            padding-bottom: .10em;
        }
    </style>
</head>

<body>
    <div data-role="page" class="ui-responsive-panel" id="e_urolo" data-dialog="true">
        <?php
        require("_database/db_medico_reproduccion.php");

        if (isset($_POST) && !empty($_POST)) {
            $coincidencias = validarAgendaUroTurno($_POST["idx"], $_POST['in_f2'].'T'.$_POST['in_hora'], $_POST["idturno"]);

            if ($coincidencias == 0) {
                if ($_POST['p_dni'] <> "" and $_POST['fec']) {
                    $hora = explode(":", $_POST['in_hora']);
                    updateUrolo($_POST['idx'], $_POST['fec'], $_POST['fec_h'], $_POST['fec_m'], $_POST['mot'], $_POST['dig'], $_POST['medi'], $_POST['aux'], $_POST['e_sol'],  $_POST['in_t'], $_POST['in_f2'], $hora[0], $hora[1], $_POST['idturno']);
                }

                $cancela = 0;
                if (isset($_POST["cancela"]) && $_POST["cancela"] == 1) {
                    $cancela = 1;
                }

                $stmt = $db->prepare("UPDATE hc_urolo SET cancela = ? WHERE id = ?;");
                $stmt->execute([$cancela, $_POST['idx']]);

                if ($_POST['in_t'] <> '' and $_POST['in_f2'] <> '' and $_POST['in_hora'] <> '') {
                    // consultar si la fecha de aspiracion esta blanco
                    $stmt = $db->prepare("SELECT codigo from googlecalendar where tipoprocedimiento_id = 3 and estado = 1 and procedimiento_id = ?");
                    $stmt->execute([$_GET['id']]);

                    require($_SERVER["DOCUMENT_ROOT"]."/config/environment.php");

                    $stmt1 = $db->prepare("SELECT nombre minutos FROM man_turno_reproduccion WHERE id = ? AND estado = 1 ORDER BY nombre;");
                    $stmt1->execute([$_POST['idturno']]);
                    $data_minutos = $stmt1->fetch(PDO::FETCH_ASSOC);

                    if ($stmt1->rowCount() == 0) {
                        $data_minutos["minutos"] = "0";
                    }

                    if ($stmt->rowCount() == 0) {
                        $googlecalendar = google_cal(
                        $_POST['in_t'].': '.$_POST['nombre'].' (' . $login . ')',
                        'Urologia INMATER',
                        $_POST['in_f2'].'T'.$hora[0].':'.$hora[1].':00.000-05:00',
                        date('Y-m-d', strtotime($_POST['in_f2'].'T'.$hora[0].':'.$hora[1].' + '.$data_minutos["minutos"].' minute')).'T'.date('H:i:s', strtotime($_POST['in_f2'].'T'.$hora[0].':'.$hora[1].' + '.$data_minutos["minutos"].' minute')).'.000-05:00',
                        $_ENV["googlecalendar_id"],
                        $_ENV["googlecalendar_accountname"],
                        $_ENV["googlecalendar_keyfilelocation"]
                        );

                        $stmt = $db->prepare("INSERT INTO googlecalendar (tipoprocedimiento_id, procedimiento_id, codigo, html_link, idusercreate) values (?, ?, ?, ?, ?)");
                        $stmt->execute([3, $_GET['id'], $googlecalendar->id, $googlecalendar->htmlLink, $login]);
                    } else {
                        $data_calendar = $stmt->fetch(PDO::FETCH_ASSOC);

                        if (isset($_POST["cancela"]) && $_POST["cancela"] == 1) {
                            $stmt = $db->prepare("UPDATE googlecalendar SET estado = 0, iduserupdate = ? WHERE estado = 1 AND tipoprocedimiento_id = ? AND procedimiento_id = ?;");
                            $stmt->execute([$login, 3, $_GET['id']]);

                            googlecalendar_eliminar(array(
                                'id' => $_ENV["googlecalendar_id"],
                                'accountname' => $_ENV["googlecalendar_accountname"],
                                'keyfilelocation' => $_ENV["googlecalendar_keyfilelocation"],
                                'applicationname' => $_ENV["googlecalendar_applicationname"],
                                'googlecalendar_codigo' => $data_calendar["codigo"],
                            ));
                        } else {
                            $data = array(
                                'id' => $_ENV["googlecalendar_id"],
                                'accountname' => $_ENV["googlecalendar_accountname"],
                                'keyfilelocation' => $_ENV["googlecalendar_keyfilelocation"],
                                'applicationname' => $_ENV["googlecalendar_applicationname"],
                                'googlecalendar_codigo' => $data_calendar["codigo"],
                                'googlecalendar_date_start' => $_POST['in_f2'].'T'.$hora[0].':'.$hora[1].':00.000-05:00',
                                'googlecalendar_date_end' => date('Y-m-d', strtotime($_POST['in_f2'].'T'.$hora[0].':'.$hora[1].' + '.$data_minutos["minutos"].' minute')).'T'.date('H:i:s', strtotime($_POST['in_f2'].'T'.$hora[0].':'.$hora[1].' + '.$data_minutos["minutos"].' minute')).'.000-05:00',
                                'description' => 'Urologia INMATER',
                            );

                            googlecalendar_actualizar($data);
                        }
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

        if ($_GET['ip'] <> "") {
            $dni = $_GET['dni'];
            $p_dni = $_GET['ip'];

            if ($_GET['id'] == '') {

                $programa_id = $db->prepare("SELECT programaid, sedeid from hc_pareja where p_dni = ?");
                $programa_id->execute([$_GET['ip']]);
                $programa_id = $programa_id->fetch(PDO::FETCH_ASSOC);

                $stmt = $db->prepare("INSERT INTO hc_urolo (p_dni,med,programaid,sedeid) VALUES (?,?,?,?)");
                $stmt->execute(array($_GET['ip'], $login,$programa_id['medios_comunicacion_id'],$programa_id['idsedes']));
                $id = $db->lastInsertId();

                $data = [
                    'tipo' => 'agregar_atencion',
                    'area_id' => 2,
                    'atencion_id' => $id,
                    'medico_id' => $login,
                    'paciente_id' => $_GET['ip'],
                    'detalle' => '',
                ];
        
                include ($_SERVER["DOCUMENT_ROOT"] . "/_operaciones/cli_atencion_unica.php");
            } else {
                $id = $_GET['id'];
            }

            // hora limite programacion
            $consulta = $db->prepare("SELECT valor FROM man_configuracion WHERE codigo=?");
            $consulta->execute(['fecha_programacion_urologia']);
            $fechaprogramacion_data = $consulta->fetch(PDO::FETCH_ASSOC);

            $rUro = $db->prepare("SELECT * FROM hc_urolo WHERE id=?");
            $rUro->execute(array($id));
            $uro = $rUro->fetch(PDO::FETCH_ASSOC);

            $rPaci = $db->prepare("SELECT p_nom,p_ape,p_fnac FROM hc_pareja WHERE p_dni=?");
            $rPaci->execute(array($p_dni));
            $paci = $rPaci->fetch(PDO::FETCH_ASSOC);

            $rAnal = $db->prepare("SELECT * FROM hc_analisis WHERE a_dni=? AND lab<>'legal' ORDER BY a_fec DESC");
            $rAnal->execute(array($p_dni)); ?>

            <style>
                .ui-dialog-contain {
                    max-width: 1400px;
                    margin: 1% auto 1%;
                    padding: 0;
                    position: relative;
                    top: -35px;
                }

                .aux_insert, .med_insert {
                    font-size: small;
                }

                .scroll_h {
                    overflow-x: scroll;
                    overflow-y: hidden;
                    white-space: nowrap;
                }

                .truncate {
                    width: 655px;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                }

                #alerta {
                    background-color: #FF9;
                    margin: 0 auto;
                    text-align: center;
                    padding: 4px;
                }

                .enlinea div {
                    display: inline-block;
                    vertical-align: middle;
                }

                .peke2 .ui-input-text {
                    width: 100px !important;
                }
            </style>

            <script>
                $(document).ready(function () {
                    // No close unsaved windows --------------------
                    var unsaved = false;
                    $(":input").change(function () {

                        unsaved = true;

                    });

                    $(window).on('beforeunload', function () {
                        if (unsaved) {
                            return 'UD. HA REALIZADO CAMBIOS';
                        }
                    });

                    // Form Submit
                    $(document).on("submit", "form", function (event) {
                        // disable unload warning
                        $(window).off('beforeunload');
                    });

                    $("#in_f2").on("change", function () {
                    var hoy = new Date();
                    hoy.setDate(hoy.getDate() + 1);
                    // format a date
                    var dia_next = hoy.getFullYear() + '-' + ("0" + (hoy.getMonth() + 1)).slice(-2) + '-' + ("0" + hoy.getDate()).slice(-2);
                    var dia_aspi = $("#in_f2").val();
                    var programacion = $("#fecha_programacion_urologia").val();

                    if (hoy.getHours() >= programacion && dia_next == dia_aspi) {
                        alert("Solo puede agendar para mañana hasta las " + programacion + " horas de hoy.");
                        $("#in_f2").val("");
                    }
                    });

                });

            </script>

            <div data-role="header" data-position="fixed">
                <a href="e_pare.php?id=<?php echo $dni . '&ip=' . $p_dni; ?>" rel="external" class="ui-btn">Cerrar</a>
                <h2>Urología:
                    <small>
                        <?php
                        echo $paci['p_ape'] . " " . $paci['p_nom'];
                        if ($paci['p_fnac'] <> "1899-12-30") echo ' (' . date_diff(date_create($paci['p_fnac']), date_create('today'))->y . ')'; ?>
                    0</small>
                </h2>
                <?php
                if ($dni <> '') { ?>
                    <a href="#popupVideo" data-rel="popup" data-position-to="window" class="ui-btn">Antecedentes Pareja</a>
                <?php } ?>
            </div>

            <?php
            if ($dni <> '') { ?>
                <div data-role="popup" id="popupVideo" data-overlay-theme="b" data-theme="a" data-tolerance="15,15"
                    class="ui-content">
                    <a href="#" data-rel="back"
                    class="ui-btn ui-btn-b ui-corner-all ui-shadow ui-btn-a ui-icon-delete ui-btn-icon-notext ui-btn-left">Close</a>
                        
                    <iframe src="e_paci.php?id=<?php echo $dni; ?>&pop=1" seamless></iframe>
                </div>
            <?php } ?>

            <div class="ui-content" role="main">
                <form action="e_urolo.php?dni=<?php echo $dni . "&ip=" . $p_dni . "&id=" . $id; ?>" method="post" data-ajax="false">
                    <input type="hidden" name="fecha_programacion_urologia" id="fecha_programacion_urologia" value="<?php print($fechaprogramacion_data['valor']); ?>">
                    <input type="hidden" name="nombre" value="<?php echo $paci['p_ape'] . " " . $paci['p_nom']; ?>">
                    <input type="hidden" name="idx" value="<?php echo $id; ?>">
                    <input type="hidden" name="dni" value="<?php echo $dni; ?>">
                    <input type="hidden" name="p_dni" value="<?php echo $p_dni; ?>">
                    <table width="100%" align="center" style="margin: 0 auto;">
                        <tr>
                            <td width="6%">Fecha</td>
                            <td width="94%">
                                <div data-role="controlgroup" data-type="horizontal" data-mini="true">
                                    <input name="fec" type="date" id="fec" value="<?php if(!isset($uro['fec']) || $uro['fec']!='1899-12-30')echo $uro['fec']; ?>" data-wrapper-class="controlgroup-textinput ui-btn">
                                    <select name="fec_h" id="fec_h">
                                        <option value="">Hra</option>
                                        <option value="07" <?php if ($uro['fec_h'] == "07") echo "selected"; ?>>07 hrs</option>
                                        <option value="08" <?php if ($uro['fec_h'] == "08") echo "selected"; ?>>08 hrs</option>
                                        <option value="09" <?php if ($uro['fec_h'] == "09") echo "selected"; ?>>09 hrs</option>
                                        <option value="10" <?php if ($uro['fec_h'] == "10") echo "selected"; ?>>10 hrs</option>
                                        <option value="11" <?php if ($uro['fec_h'] == "11") echo "selected"; ?>>11 hrs</option>
                                        <option value="12" <?php if ($uro['fec_h'] == "12") echo "selected"; ?>>12 hrs</option>
                                        <option value="13" <?php if ($uro['fec_h'] == "13") echo "selected"; ?>>13 hrs</option>
                                        <option value="14" <?php if ($uro['fec_h'] == "14") echo "selected"; ?>>14 hrs</option>
                                        <option value="15" <?php if ($uro['fec_h'] == "15") echo "selected"; ?>>15 hrs</option>
                                        <option value="16" <?php if ($uro['fec_h'] == "16") echo "selected"; ?>>16 hrs</option>
                                        <option value="17" <?php if ($uro['fec_h'] == "17") echo "selected"; ?>>17 hrs</option>
                                        <option value="18" <?php if ($uro['fec_h'] == "18") echo "selected"; ?>>18 hrs</option>
                                        <option value="19" <?php if ($uro['fec_h'] == "19") echo "selected"; ?>>19 hrs</option>
                                        <option value="20" <?php if ($uro['fec_h'] == "20") echo "selected"; ?>>20 hrs</option>
                                    </select>
                                    <select name="fec_m" id="fec_m">
                                        <option value="">Min</option>
                                        <option value="00" <?php if ($uro['fec_m'] == "00") echo "selected"; ?>>00 min</option>
                                        <option value="15" <?php if ($uro['fec_m'] == "15") echo "selected"; ?>>15 min</option>
                                        <option value="30" <?php if ($uro['fec_m'] == "30") echo "selected"; ?>>30 min</option>
                                        <option value="45" <?php if ($uro['fec_m'] == "45") echo "selected"; ?>>45 min</option>
                                    </select>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <div data-role="collapsibleset" data-theme="a" data-content-theme="a" data-mini="true">
                        <div data-role="collapsible" data-collapsed="false"><h3>Consulta</h3>
                            <div class="scroll_h">
                                <table width="100%" align="center" style="margin: 0 auto;max-width:800px;">
                                    <tr>
                                        <td>Motivo de Consulta
                                            <textarea name="mot" id="mot" data-mini="true"><?php echo $uro['mot']; ?></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Diagnóstico
                                            <textarea name="dig" id="dig" data-mini="true"><?php echo $uro['dig']; ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="medi">Medicamentos:</label>
                                            <div id="print_med">
                                                <textarea name="medi" id="medi" data-mini="true"><?php echo $uro['medi']; ?></textarea>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div data-role="collapsible"><h3>Orden de Análisis Clínicos</h3>
                            <label for="aux">Exámenes Auxiliares:</label>
                            <div id="print_aux">
                                <textarea name="aux" id="aux" data-mini="true"><?php echo $uro['aux']; ?></textarea>
                            </div>
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
                            OTROS <textarea name="e_sol" id="e_sol" data-mini="true"><?php echo $uro['e_sol']; ?></textarea>
                        </div>

                        <div data-role="collapsible"><h3>Orden de Internamiento</h3>
                            <table width="100%" align="center" style="margin: 0 auto;">
                                <tr>
                                    <td>Tipo:</td>
                                    <td width="23%">
                                        <select name="in_t" id="in_t" data-mini="true">
                                            <option value="">Seleccionar</option>
                                            <option value="Biopsia testicular" <?php if ($uro['in_t'] == "Biopsia testicular") echo "selected"; ?>>Biopsia testicular</option>
                                            <option value="Aspiración de epidídimo" <?php if ($uro['in_t'] == "Aspiración de epidídimo") echo "selected"; ?>>Aspiración de epidídimo</option>
                                        </select>
                                        </td>
                                    <td>Fecha/Hora de Intervención</td>
                                    <td>
                                        <div data-role="controlgroup" data-type="horizontal" data-mini="true">
                                            <input type="date" name="in_f2" id="in_f2" value="<?php echo $uro['in_f2']; ?>" data-mini="true" data-wrapper-class="controlgroup-textinput ui-btn">
                                            <select name="in_hora" id="in_hora">
                                                <option value="">Seleccione Hora</option>
                                                <?php
                                                $consulta = $db->prepare("SELECT nombre from man_hora where estado = 1 and urologia = 1 order by codigo asc");
                                                $consulta->execute();
                                                while ($data = $consulta->fetch(PDO::FETCH_ASSOC)) { ?>
                                                    <option value="<?php print($data['nombre']); ?>"
                                                        <?php
                                                        if ($data['nombre'] == $uro['in_h2'] . ':' . $uro['in_m2'])
                                                            print('selected'); ?>>
                                                        <?php print(mb_strtolower($data['nombre'])); ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                            <!-- turno -->
                                            <select name="idturno" id="idturno" data-mini="true">
                                                <option value="">Seleccione Turno</option>
                                                <?php
                                                    $consulta = $db->prepare("SELECT codigo, nombre from man_turno_reproduccion where estado = 1 order by nombre asc");
                                                    $consulta->execute();
                                                    while ($data = $consulta->fetch(PDO::FETCH_ASSOC)) { ?>
                                                    <option value="<?php echo $data['codigo']; ?>"
                                                        <?php
                                                        if ($data['codigo'] == $uro["idturno_inter"])
                                                            echo 'selected'; ?>>
                                                        <?php echo mb_strtolower($data['nombre'])." min"; ?>
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
                    if ($uro['med'] == $login) { ?>
                        <div class="enlinea">
                            <input type="Submit" value="GUARDAR DATOS" data-icon="check" data-iconpos="left" data-mini="true"
                                class="show-page-loading-msg" data-textonly="false" data-textvisible="true"
                                data-msgtext="Actualizando datos.." data-theme="b" data-inline="true"/>
                            <input type="checkbox" name="cancela" id="cancela" data-mini="true" value=1 <?php if ($uro['cancela'] == 1) { echo "checked"; } ?>>
                            <label for="cancela">Cancelar Consulta</label>
                            <a href="javascript:PrintElem('#print_med','<?php echo $paci['p_ape'] . " " . $paci['p_nom']; ?>',1,'<?php echo date("d-m-Y", strtotime($uro['fec'])); ?>')"
                                data-role="button" data-mini="true" data-inline="true" rel="external">Imprimir Medicamentos</a>
                            <?php
                            if ($uro['aux'] <> '') { ?>
                                <a href="javascript:PrintElem('#print_aux','<?php echo $paci['p_ape'] . " " . $paci['p_nom']; ?>',2,'<?php echo date("d-m-Y", strtotime($uro['fec'])); ?>')"
                                    data-role="button" data-mini="true" data-inline="true" rel="external">Imprimir Orden de Análisis Clínicos</a>
                            <?php } ?>
                    </div>
                    <?php } else {
                        echo '<font color="#E34446"><b>PERMISO DE EDICION SOLO PARA: </b> ' . $uro['med'] . '</font>';
                    } ?>
                </form>
            </div>
        <?php } ?>
    </div>
    <script src="js/e_urolo.js"></script>
</body>
</html>