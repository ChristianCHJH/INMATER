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
        // popup PARA LA AGENDA ---------------------------------------------------
        $(document).on("pagecreate", function () {
            // The window width and height are decreased by 30 to take the tolerance of 15 pixels at each side into account
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

            $('#sacogestacional').on('change', function(){
                const max = $(this).val();

                $('input.latidos').closest('td').addClass('hidden');
                $('.label-latidos').addClass('hidden');
                $('.label-condicion').addClass('hidden');
                $('.condicion').addClass('hidden');
                $('#proxima-ecografia').text('Próxima ecografía');

                if( max == 0 ){
                    $('.label-condicion').removeClass('hidden');
                    $('.condicion').removeClass('hidden');
                    $('#proxima-ecografia').text('Próxima ecografía de confirmación');
                }

                $.each($('input.latidos'), function(i, input) {
                    if( i >= max ) return false;

                    $(input).closest('td').removeClass('hidden');
                    $('.label-latidos').removeClass('hidden');
                });
            }).change();
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
    if (isset($_GET['pop']) and !empty($_GET['pop'])) { ?>
        <script>
            $(document).ready(function () {
                $("#Plan").collapsible({collapsed: false});
            });
        </script>
    <?php } ?>
    <style>
        .controlgroup-textinput {
            padding-top: .10em;
            padding-bottom: .10em;
        }
        .hidden {
            display: none!important;
        }
    </style>
</head>
<body>
    <div data-role="page" class="ui-responsive-panel" id="e_gine" data-dialog="true">
        <?php
        if (isset($_GET['id']) and !empty($_GET['id'])) {
            $id = $_GET['id'];
            $rGine = $db->prepare("SELECT hc_eco_beta_positivo.*, lab_aspira.fec2, lab_aspira.fec3, lab_aspira.fec4, lab_aspira.fec5, lab_aspira.fec6,
            lab_aspira_t.beta, lab_aspira_t.dia FROM hc_eco_beta_positivo
                JOIN lab_aspira ON lab_aspira.pro = hc_eco_beta_positivo.pro and lab_aspira.estado is true
                JOIN lab_aspira_t ON lab_aspira_t.pro = hc_eco_beta_positivo.pro and lab_aspira_t.estado is true
                
            WHERE hc_eco_beta_positivo.id=?");
            $rGine->execute(array($id));
            $gine = $rGine->fetch(PDO::FETCH_ASSOC);

            if (isset($_POST) && !empty($_POST) && isset($_POST['idx'])) {
                $hora = explode(":", $_POST['in_hora']);
                updateEcoBetaPositivo($_POST['idx'], $_POST['fec'], $_POST['fec_h'], $_POST['fec_m'], $_POST['mot'], $_POST['dig'], $_POST['aux'], $_POST['efec'], $_POST['betacuantitativa'], $_POST['sacogestacional'], $_POST['condicion'], $_POST['ubicacionsaco'], $_POST['latidos1'], $_POST['latidos2'], $_POST['latidos3'], $_POST['umc'], $_POST['semanas_umc'], $_POST['decision_siguiente_ecografia'], $_POST['siguiente_ecografia'], $_POST['lcc'], $_POST['progesterona'], $_POST['observaciones']);

                if( $gine['efec'] == '1899-12-30' && $_POST['sacogestacional'] == 0 ) {
                    insertBetaCitaEco($gine['dni'], $gine['pro'], $_POST['siguiente_ecografia'], $login, $gine['fec_h'], $gine['fec_m'], 'Ecografía confirmación por condición bioquímico', true);
                }

                $rGine->execute(array($id));
                $gine = $rGine->fetch(PDO::FETCH_ASSOC);

            }

            $beta = $gine['fec'.$gine['dia']];
            $umc = date('Y-m-d', strtotime($beta.' - 19 days'));

            $semanas_umc = strtotime(date('Y-m-d', strtotime('now'))) - strtotime($beta.' - 19 days');

            $posible_umc = date('Y-m-d', strtotime($beta.' - 19 days + 12 weeks') );
            $semanas_umc /= 604800;

            $rPaci = $db->prepare("SELECT nom,ape,fnac,talla,peso FROM hc_paciente WHERE dni=?");
            $rPaci->execute(array($gine['dni']));
            $paci = $rPaci->fetch(PDO::FETCH_ASSOC);

            $rAmh = $db->prepare("SELECT amh FROM hc_antece_perfi WHERE dni=? and amh<>''");
            $rAmh->execute(array($gine['dni']));

            $a_medi = $db->prepare("SELECT * FROM hc_agenda WHERE id=?");
            $a_medi->execute(array($id));

            $a_plan = $db->prepare("SELECT * FROM hc_gineco_plan WHERE idp=?");
            $a_plan->execute(array($id));

            $rAux = $db->prepare("SELECT nom FROM hc_gineco_aux");
            $rAux->execute();

            $rMed = $db->prepare("SELECT nom,des FROM hc_gineco_med");
            $rMed->execute();

            $rInt = $db->prepare("SELECT id,med,esp FROM hc_gineco_int");
            $rInt->execute();

            $rAnal = $db->prepare("SELECT * FROM hc_analisis WHERE a_dni=? AND lab<>'legal' ORDER BY a_fec DESC");
            $rAnal->execute(array($gine['dni'])); ?>

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

                .input-with-metric{
                    width: 100px!important;
                    display: inline-block;
                }
            </style>

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

                    $("#repro").on('keydown paste', function(e){ // otra forma de readonly porque este campo tiene q ser readonly y reuired
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
                        // format a date
                        var dia_next = hoy.getFullYear() + '-' + ("0" + (hoy.getMonth() + 1)).slice(-2) + '-' + ("0" + hoy.getDate()).slice(-2);
                        var dia_aspi = $("#in_f2").val();
                        //alert (dia_next+' XXX '+dia_aspi);
                        if (hoy.getHours() >= 15 && dia_next == dia_aspi) {
                            alert("Solo puede agendar para mañana hasta las 3pm de hoy");
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
                    <?php if ($gine['in_c'] == 1) { ?>
                        $('#in_t1-button').show();
                        $('#in_t2').hide();
                        $('#in_t2').textinput('disable');
                    <?php } if ($gine['in_c'] == 2) { ?>
                        $('#in_t2').textinput('enable');
                        $('#in_t2').show();
                        $('#in_t1-button').hide();
                    <?php } if ($gine['in_c'] == 0) { ?>
                        $('#in_t2').hide();
                        $('#in_t1-button').hide();
                    <?php } ?>

                    $('#decision_siguiente_ecografia, #lcc, #umc').on('change', function() {
                        if( $('#decision_siguiente_ecografia').val() == 'umc' )
                        {
                            const date = new Date($('#umc').val());
                            date.setDate(date.getDate() + (12 *7));

                            return $('#siguiente_ecografia').val(date.toISOString().substr(0, 10));
                        }

                        if( $('#lcc').val() == '' ) return;

                        const lccdate = new Date();

                        lccdate.setDate(lccdate.getDate() + ((12 - $('#lcc').val()) * 7)  );

                        return $('#siguiente_ecografia').val(lccdate.toISOString().substr(0, 10));

                    });

                    $('#umc').on('change', function() {
                        const date = new Date($(this).val());

                        const semanas_umc = ( (new Date()).getTime() - date.getTime() ) / 604800000;
                        $('#semanas_umc').val( semanas_umc.toFixed(2) );
                    });
                });

            </script>

            <div data-role="header" data-position="fixed">
                <?php if ( isset( $_GET['n_gine'] ) ): ?>
                    <a href="n_gine.php?id=<?php echo $gine['dni'] ?>" rel="external" class="ui-btn">Cerrar</a>
                <?php elseif ( isset( $_GET['id_gine'] ) ): ?>
                    <a href="e_gine.php?id=<?php echo @$_GET['id_gine'] ?>" rel="external" class="ui-btn">Cerrar</a>
                <?php else: ?>
                    <a href="med_betas_positivas.php" rel="external" class="ui-btn">Cerrar</a>
                <?php endif ?>
                <h2>Paciente:
                    <small><?php echo $paci['ape'] . " " . $paci['nom'];
                        if ($paci['fnac'] <> "1899-12-30") echo '(' . date_diff(date_create($paci['fnac']), date_create('today'))->y . ')'; ?></small>
                </h2>
            </div><!-- /header -->
            <div data-role="popup" id="popupVideo" data-overlay-theme="b" data-theme="a" data-tolerance="15,15"
                class="ui-content">
                <a href="#" data-rel="back"
                class="ui-btn ui-btn-b ui-corner-all ui-shadow ui-btn-a ui-icon-delete ui-btn-icon-notext ui-btn-left">Close</a>
                    
                <iframe src="e_paci.php?id=<?php echo $gine['dni']; ?>&pop=1" seamless></iframe>
            </div>
            <div class="ui-content" role="main">

                <form action="eco_beta_positivo.php?med=<?php echo @$_GET['med'] ?>&id=<?php echo $gine['id']; ?>" method="post" data-ajax="false">
                    <input type="hidden" name="nombre" value="<?php echo $paci['ape'] . " " . $paci['nom']; ?>">
                    <input type="hidden" name="idx" value="<?php echo $gine['id']; ?>">
                    <input type="hidden" name="dni" value="<?php echo $gine['dni']; ?>">
                    <table width="100%" align="center" style="margin: 0 auto;">
                        <tr>
                            <td>Fecha</td>
                            <td>
                                <div data-role="controlgroup" data-type="horizontal" data-mini="true">
                                    <input name="fec" type="date" id="fec" value="<?php echo $gine['fec']; ?>" data-wrapper-class="controlgroup-textinput ui-btn">
                                    <select name="fec_h" id="fec_h">
                                        <option value="">Hra</option>
                                        <option value="07" <?php if ($gine['fec_h'] == "07") echo "selected"; ?>>07 hrs
                                        </option>
                                        <option value="08" <?php if ($gine['fec_h'] == "08") echo "selected"; ?>>08 hrs
                                        </option>
                                        <option value="09" <?php if ($gine['fec_h'] == "09") echo "selected"; ?>>09 hrs
                                        </option>
                                        <option value="10" <?php if ($gine['fec_h'] == "10") echo "selected"; ?>>10 hrs
                                        </option>
                                        <option value="11" <?php if ($gine['fec_h'] == "11") echo "selected"; ?>>11 hrs
                                        </option>
                                        <option value="12" <?php if ($gine['fec_h'] == "12") echo "selected"; ?>>12 hrs
                                        </option>
                                        <option value="13" <?php if ($gine['fec_h'] == "13") echo "selected"; ?>>13 hrs
                                        </option>
                                        <option value="14" <?php if ($gine['fec_h'] == "14") echo "selected"; ?>>14 hrs
                                        </option>
                                        <option value="15" <?php if ($gine['fec_h'] == "15") echo "selected"; ?>>15 hrs
                                        </option>
                                        <option value="16" <?php if ($gine['fec_h'] == "16") echo "selected"; ?>>16 hrs
                                        </option>
                                        <option value="17" <?php if ($gine['fec_h'] == "17") echo "selected"; ?>>17 hrs
                                        </option>
                                        <option value="18" <?php if ($gine['fec_h'] == "18") echo "selected"; ?>>18 hrs
                                        </option>
                                        <option value="19" <?php if ($gine['fec_h'] == "19") echo "selected"; ?>>19 hrs
                                        </option>
                                        <option value="20" <?php if ($gine['fec_h'] == "20") echo "selected"; ?>>20 hrs
                                        </option>
                                    </select>
                                    <select name="fec_m" id="fec_m">
                                        <option value="">Min</option>
                                        <option value="00" <?php if ($gine['fec_m'] == "00") echo "selected"; ?>>00 min
                                        </option>
                                        <option value="15" <?php if ($gine['fec_m'] == "15") echo "selected"; ?>>15 min
                                        </option>
                                        <option value="30" <?php if ($gine['fec_m'] == "30") echo "selected"; ?>>30 min
                                        </option>
                                        <option value="45" <?php if ($gine['fec_m'] == "45") echo "selected"; ?>>45 min
                                        </option>
                                    </select>
                                </div>
                            </td>
                            
                        </tr>
                    </table>
                    <div data-role="collapsibleset" data-theme="a" data-content-theme="a" data-mini="true">
                        <div data-role="collapsible" ><h3>Consulta</h3>
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
                                            <!--<ul data-role="listview" data-theme="c" data-inset="true" data-filter="true" data-filter-reveal="true" data-filter-placeholder="Agregar medicamentos..." data-mini="true" class="fil_med" data-icon="false">
                                            <?php //while($med = $rMed->fetch(PDO::FETCH_ASSOC)) { ?>
                                                    <li><a href="#" class="med_insert" data="<?php //echo $med['des']; ?>"><?php //echo $med['nom']; ?></a></li>
                                            <?php //} ?>
                                            </ul>-->
                                            <div class="enlinea" style="border:dotted"><i style="margin: 0 auto;">AGREGAR
                                                    MEDICAMENTOS:</i><br>
                                                <?php
                                                    /*//$farma = new PDO('mysql:host=localhost;dbname=farmacia;charset=utf8', 'root', '');
                                                    $farma = new PDO('mysql:host=localhost;dbname=vigose5_farmacia;charset=utf8', 'vigose5_farma', 'f4rm4.2017');
                                                    $farma->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                                    $farma->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);*/
                                                    require("_database/database_farmacia.php");
                                                    $Rmedi = $farma->prepare("SELECT id,producto FROM tblproducto"); $Rmedi->execute();
                                                ?>
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
                                                <input type="Submit" name="medi_add" value="AGREGAR" data-mini="true"
                                                    data-theme="b" data-inline="true"/>
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
                                                                    <a href="e_gine_medi.php?id=<?php echo $medi['id_agenda']; ?>"
                                                                    rel="external"><?php echo $medi['medi_name']; ?></a>
                                                                    <?php if ($medi['medi_obs'] <> '') echo '<br>Observaciones: ' . $medi['medi_obs']; ?>
                                                                </td>
                                                                <td align="center"><?php echo $medi['medi_dosis']; ?></td>
                                                                <td align="center"><?php echo $medi['medi_frecuencia']; ?></td>
                                                                <td align="center"><?php echo $medi['medi_cant_dias']; ?></td>
                                                                <td align="center"><?php echo date("d-m-Y", strtotime($medi['medi_init_fec'])) . ' ' . $medi['medi_init_h'] . ':' . $medi['medi_init_m']; ?></td>
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
                        <div data-role="collapsible" data-collapsed="false"><h3>Ecografía N° 1</h3>
                            <div class="scroll_h">
                                <table width="100%" align="center" style="margin: 0 auto;">
                                    <tr>
                                        <td width="4%">Fecha</td>
                                        <td width="11%"><input name="efec" type="date" id="efec" data-mini="true"
                                                            value="<?php echo $gine['efec'] == '0000-00-00' ? date('Y-m-d') : $gine['efec'] ?>"/></td>
                                        <td width="8%">Beta cuantitativa</td>
                                        <td width="11%">
                                            <input data-wrapper-class="input-with-metric" name="betacuantitativa" type="text" id="betacuantitativa" data-mini="true"
                                                            value="<?php echo $gine['betacuantitativa']; ?>"><small><i>mUI/ml</i></small>
                                        </td>
                                        <td><select name="sacogestacional" id="sacogestacional" data-mini="true">
                                                <option value="" selected="selected">Saco gestacional:</option>
                                            <?php foreach (range(0, 3) as $saco): ?>
                                                <option value="<?php echo $saco ?>" <?php if($gine['sacogestacional'] == $saco) echo "selected" ?>>
                                                    <?php echo $saco ?> saco<?php if($saco > 1) echo 's' ?>
                                                </option>
                                            <?php endforeach ?>
                                            </select></td>
                                        <td><select name="ubicacionsaco" id="ubicacionsaco" data-mini="true">
                                                <option value="eutopico">
                                                    Ubicación del saco: Eutópico
                                                </option>
                                                <option value="ectopico" <?php if ($gine['ubicacionsaco'] == "ectopico") echo "selected"; ?>>
                                                    Ubicación del saco: Ectópico
                                                </option>
                                                <option value="hetereotopico" <?php if ($gine['ubicacionsaco'] == "hetereotopico") echo "selected"; ?>>
                                                    Ubicación del saco: Hetereotópico
                                                </option>
                                            </select></td>
                                    </tr>
                                    <tr>
                                        <td class="label-latidos <?php if(!$gine['sacogestacional'] || 0 == $gine['sacogestacional']) echo 'hidden' ?>">Latidos cardiacos (LT/min)</td>
                                        <td class="label-condicion <?php if(!$gine['sacogestacional'] || 0 < $gine['sacogestacional']) echo 'hidden' ?>">Condición</td>

                                        <td class="condicion <?php if(!$gine['sacogestacional'] || 0 < $gine['sacogestacional']) echo 'hidden' ?>"><input type="text" name="condicion" value="<?php echo !$gine['condicion'] ? 'Bioquímico' : $gine['condicion'] ?>"></td>
                                        <?php foreach (range(1, 3) as $saco): ?>
                                            <td class="<?php if(!$gine['sacogestacional'] || $saco < $gine['sacogestacional']) echo 'hidden' ?>">
                                                <input name="latidos<?php echo $saco ?>" id="latidos<?php echo $saco ?>" class="latidos" type="text" placeholder="Latidos saco <?php echo $saco ?>" value="<?php echo $gine['latidos'.$saco]; ?>">
                                            </td>
                                        <?php endforeach ?>
                                    </tr>
                                    <tr>
                                        <td><b>Semana gestacional</b></td>
                                    </tr>

                                    <tr>
                                        <td>Última menstruación calculada</td>
                                        <td><input name="umc" type="date" id="umc" data-mini="true"
                                                            value="<?php echo $gine['umc'] == '0000-00-00' ? $umc : $gine['umc'] ?>"/></td>
                                        <td><input type="number" max="12" name="semanas_umc" id="semanas_umc" value="<?php echo !$gine['semanas_umc'] ? number_format( $semanas_umc, 2 ) : $gine['semanas_umc'] ?>"></td>
                                        <td><small><i>semanas/UMC</i></small></td>
                                        <td>Longitud corono-caudal</td>
                                        <td><input data-wrapper-class="input-with-metric" name="lcc" type="number" max="12" min="0" maxlength="2"  oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" id="lcc" data-mini="true" value="<?php echo $gine['lcc']; ?>"><small><i>semanas/LCC</i></small></td>
                                    </tr>
                                    <tr>
                                        <td>Decisión siguiente ecografía</td>
                                        <td>
                                            <select name="decision_siguiente_ecografia" id="decision_siguiente_ecografia">
                                                <option value="umc">UMC</option>
                                                <option value="lcc">LCC</option>
                                            </select>
                                        </td>
                                        <td id="proxima-ecografia">Próxima ecografía</td>
                                        <td>
                                            <input name="siguiente_ecografia" type="date" id="siguiente_ecografia" data-mini="true" value="<?php echo $gine['siguiente_ecografia'] == '0000-00-00' ? $posible_umc : $gine['siguiente_ecografia'] ?>" />
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Progesterona</td>
                                        <td><input data-wrapper-class="input-with-metric" name="progesterona" type="text" id="progesterona" data-mini="true"value="<?php echo $gine['progesterona']; ?>"><small><i>mgr/día</i></small></td>
                                        
                                    </tr>
                                    <tr>
                                        <td>Observaciones</td>
                                        <td colspan="2"><textarea name="observaciones" id="" cols="30" rows="10"><?php echo $gine['observaciones']; ?></textarea></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        
                    <?php if ($gine['med'] == $login) { ?>
                        <input type="Submit" value="GUARDAR DATOS" data-icon="check" data-iconpos="left" data-mini="true"
                            class="show-page-loading-msg" data-textonly="false" data-textvisible="true"
                            data-msgtext="Actualizando datos.." data-theme="b" data-inline="true"/>

                        <?php if ($gine['aux'] <> '') { ?>
                            <a href="javascript:PrintElem('#print_aux','<?php echo $paci['ape'] . " " . $paci['nom']; ?>',2,'<?php echo date("d-m-Y", strtotime($gine['fec'])); ?>')"
                            data-role="button" data-mini="true" data-inline="true" rel="external">Imprimir Orden de
                                Análisis Clínicos</a>
                        <?php } ?>

                    <?php } else {
                        echo '<font color="#E34446"><b>PERMISO DE EDICION SOLO PARA: </b> ' . $gine['med'] . '</font>';
                    } ?>
                </form>

            </div><!-- /content -->
        <?php } ?>
    </div>
    <script>
        $(document).on("click", ".show-page-loading-msg", function () {

            if (document.getElementById("mot").value == "") {
                // alert("Debe llenar el campo 'Motivo de consulta'");
                // return false;
            }
            if (document.getElementById("fec").value == "") {
                alert("Debe llenar el campo 'Fecha'");
                return false;
            }

            if (document.getElementById("fec_h").value == "") {
                alert("Debe llenar el campo 'Hora'");
                return false;
            }

            if (document.getElementById("fec_m").value == "") {
                alert("Debe llenar el campo 'Minuto'");
                return false;
            }

            if (document.getElementById("in_c").value != "" || document.getElementById("in_f2").value != "") {

                if (document.getElementById("in_f1").value == "" || document.getElementById("in_h1").value == "" || document.getElementById("in_m1").value == "" || document.getElementById("in_f2").value == "" || document.getElementById("in_hora").value == "") {
                    alert("Debe ingresar las fechas de Internamiento e Intervención");
                    return false;
                }

                if (document.getElementById("in_c").value == "") {
                    alert("Debe ingresar la Clínica");
                    return false;
                }
                if (document.getElementById("in_t").value == "") {
                    alert("Debe ingresar el Tipo de Intervención");
                    return false;
                }
            }

            if (document.getElementById("mam").value == "Anormal") {

                if (document.getElementById("mam1").value == "") {
                    alert("Debe especificar el Ex. Mama Anormal");
                    return false;
                }
            }
            if (document.getElementById("cer").value == "Anormal") {

                if (document.getElementById("cer1").value == "") {
                    alert("Debe especificar el Cervix Anormal");
                    return false;
                }
            }

            var $this = $(this),
                theme = $this.jqmData("theme") || $.mobile.loader.prototype.options.theme,
                msgText = $this.jqmData("msgtext") || $.mobile.loader.prototype.options.text,
                textVisible = $this.jqmData("textvisible") || $.mobile.loader.prototype.options.textVisible,
                textonly = !!$this.jqmData("textonly");
            html = $this.jqmData("html") || "";
            $.mobile.loading("show", {
                text: msgText,
                textVisible: textVisible,
                theme: theme,
                textonly: textonly,
                html: html
            });
        })
            .on("click", ".hide-page-loading-msg", function () {
                $.mobile.loading("hide");
            });
        $(function () {
            $('#alerta').delay(10000).fadeOut('slow');
        });//]]>
    </script>
</body>
</html>