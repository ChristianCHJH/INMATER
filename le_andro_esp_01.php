<?php session_start(); ?>
<!DOCTYPE HTML>
<html>
<head>
    <?php
        $login = $_SESSION['login'];
        $dir = $_SERVER['HTTP_HOST'] . substr(dirname(__FILE__), strlen($_SERVER['DOCUMENT_ROOT']));
        if (!$login) {
            echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=http://" . $dir . "'>";
        }
        require("_database/db_tools.php");

        if (isset($_POST['p_dni']) && !empty($_POST['p_dni']) && isset($_POST['idx']) && !empty($_POST['idx'])) {
            $resul_azo=$resul_cripto=0;

            if (isset($_POST['resul_cripto'])) {
                $resul_cripto=1;
            }

            if (isset($_POST['resul_azo'])) {
                $resul_azo=1;
            }

            $data = array(
                'cine_vap' => $_POST['cinetica_vap'],
                'cine_lin' => $_POST['cinetica_lin'],
                'cine_alh' => $_POST['cinetica_alh'],
                'cine_vsl' => $_POST['cinetica_vsl'],
                'cine_str' => $_POST['cinetica_str'],
                'cine_bcf' => $_POST['cinetica_bcf'],
                'cine_vcl' => $_POST['cinetica_vcl'],
                'cine_wob' => $_POST['cinetica_wob'],
                'normal_largocabeza_promedio' => $_POST['normal_largocabeza_promedio'],
                'normal_largocabeza_porcentaje' => $_POST['normal_largocabeza_porcentaje'],
                'normal_ancho_promedio' => $_POST['normal_ancho_promedio'],
                'normal_ancho_porcentaje' => $_POST['normal_ancho_porcentaje'],
                'normal_perimetro_promedio' => $_POST['normal_perimetro_promedio'],
                'normal_perimetro_porcentaje' => $_POST['normal_perimetro_porcentaje'],
                'normal_area_promedio' => $_POST['normal_area_promedio'],
                'normal_area_porcentaje' => $_POST['normal_area_porcentaje'],
                'normal_largocola_promedio' => $_POST['normal_largocola_promedio'],
                'normal_largocola_porcentaje' => $_POST['normal_largocola_porcentaje'],
                'abstinencia' => $_POST['abstinencia'],
                'antibioticos' => isset($_POST['antibioticos']) ? 1 : 0,
                'antidepresivos' => isset($_POST['antidepresivos']) ? 1 : 0,
                'antiinflamatorios' => isset($_POST['antiinflamatorios']) ? 1 : 0,
                'protectores' => isset($_POST['protectores']) ? 1 : 0,
                'otros_texto' => isset($_POST['otros_texto']) ? $_POST['otros_texto'] : '',
            );

            updateAndro_esp_02($_POST['img_movi'],$_POST['img_concen'],$_POST['img_mtotal'],$_POST['img_mclasi'],$_POST['archivo_id'] ? : 0,  
                $_POST['p_dni'], $_POST['idx'], $_POST['dni']
                ,$_POST['info_fmuestra'],$_POST['info_lobtencion'],$_POST['info_hentrega'],$_POST['info_mobtencion'],$_POST['info_dobtencion'],$_POST['info_medicacion']
                ,$_POST['macro_apariencia'],$_POST['macro_viscosidad'],$_POST['macro_liquefaccion'],$_POST['macro_aglutinacion'],$_POST['macro_aglutinacion_porc'],$_POST['macro_ph'],$_POST['macro_volumen']
                ,$_POST['movi_mprogresivo'],$_POST['movi_mnoprogresivo'],$_POST['movi_tvitalidad']
                ,$_POST['concen_exml'],$_POST['concen_credon']
                ,$_POST['morfo_normal'],$_POST['morfo_anormal_cabeza'],$_POST['morfo_anormal_pieza'],$_POST['morfo_anormal_cola'],$_POST['morfo_micro'],$_POST['morfo_macro'],$_POST['morfo_inmaduro'],$_POST['morfo_bicefalo'],$_POST['morfo_bicaudo']
                ,$resul_cripto, $resul_azo, $_POST['emb'], $_POST['observaciones'], $_POST['fec']
                ,$_POST['movi_mprogresivo_lineal_cantidad'],$_POST['movi_mprogresivo_no_lineal_cantidad'],$_POST['movi_mnoprogresivo_cantidad'],$_POST['movi_nmoviles_cantidad'], $data
            );

            header("Location: e_pare.php?id=".$_GET['dni']."&ip=".$_GET['p_dni']);
        }
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/jquery.timepicker.min.css">
    <link rel="stylesheet" href="css/bootstrap.v4/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="css/global.css" crossorigin="anonymous">
    <script src="js/jquery-1.11.0.min.js"></script>
    <script src="chart/dist/Chart.bundle.js"></script>
    <script src="chart/samples/utils.js"></script>
    <style>
        .footer {
            bottom: 0;
            width: 100%;
            height: 60px;
            line-height: 60px;
            background-color: #f5f5f5;
        }
        canvas{
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
        }
    </style>
</head>
<body>
    <?php //require ('_includes/menu_andrologia.php'); ?>
    <div data-role="page" class="ui-responsive-panel" id="le_andro_esp" data-dialog="true">
        <?php
        if ($_GET['p_dni'] <> "") {
            $p_dni = $_GET['p_dni']; //dni de la pareja
            $fec = $_GET['fec']; //fecha de solicitud de espermatograma
            $dni = $_GET['dni']; //dni de la paciente

            $rPare = $db->prepare("SELECT p_nom, p_ape, p_med FROM hc_pareja WHERE p_dni=?");
            $rPare->execute(array($p_dni));
            $pare = $rPare->fetch(PDO::FETCH_ASSOC);
            $pareja = $pare['p_ape']." ".$pare['p_nom'];

            $rPaci = $db->prepare("SELECT ape, nom FROM hc_paciente WHERE dni=?");
            $rPaci->execute(array($dni));
            $paci = $rPaci->fetch(PDO::FETCH_ASSOC);
            $paciente=''; 
            if(isset($paci['ape']) && isset($paci['nom']))$paciente=$paci['ape']." ".$paci['nom'];

            $rMedi = $db->prepare("SELECT nom FROM usuario WHERE userx=?");
            $rMedi->execute(array($pare['p_med']));
            $medi = $rMedi->fetch(PDO::FETCH_ASSOC);
            $medico='';
            if(isset($medi['nom']))$medico = $medi['nom'];

            $rLiqEspe = $db->prepare("SELECT id, nombre FROM licuefaccion_esperma where estado=1");
            $rLiqEspe->execute();
            $liqespe = $rLiqEspe->fetchAll();

            $rVisEspe = $db->prepare("SELECT id, nombre FROM viscosidad_esperma where estado=1");
            $rVisEspe->execute();
            $visespe = $rVisEspe->fetchAll();

            $rApaEspe = $db->prepare("SELECT id, nombre FROM apariencia_esperma where estado=1");
            $rApaEspe->execute();
            $apaespe = $rApaEspe->fetchAll();

            $rSino = $db->prepare("SELECT id, nombre FROM si_no where estado=1");
            $rSino->execute();
            $rows = $rSino->fetchAll();

            $rmobte1 = $db->prepare("SELECT id, nombre from man_aglutinacion where estado=1");
            $rmobte1->execute();
            $rows1 = $rmobte1->fetchAll();

            $rmobte = $db->prepare("SELECT id, nombre from metodo_obtencion where estado=1");
            $rmobte->execute();

            $rlobte = $db->prepare("SELECT id, nombre from lugar_obtencion where estado=1");
            $rlobte->execute();

            $rEmb = $db->prepare("SELECT id,nom FROM lab_user WHERE sta=0");
            $rEmb->execute();

            $Rpop = $db->prepare("SELECT * FROM lab_andro_esp WHERE p_dni=? AND fec=?");
            $Rpop->execute(array($p_dni, $fec));
            $pop = $Rpop->fetch(PDO::FETCH_ASSOC);

            $stmt = $db->prepare("SELECT * from man_archivo where id=?");
            $stmt->execute(array($pop['archivo_id']));
            $archivo = $stmt->fetch(PDO::FETCH_ASSOC);
        ?>
        <style>
            .ui-dialog-contain {
                max-width: 800px;
                margin: 2% auto 15px;
                padding: 0;
                position: relative;
                top: -15px;
            }
            .peke2 .ui-input-text, #pm_n, #pm_a {
                width: 80px !important;
                display: inline-block !important;
            }
            .scroll_h {
                overflow-x: scroll;
                overflow-y: hidden;
                white-space: nowrap;
            }
            .enlinea div {
                display: inline-block;
                vertical-align: middle;
            }
        </style>
        <script>
            $(document).ready(function () {
                $("#info_medicacion").change(function () {
                        if ($(this).val() == 2) {
                            $("#antibioticos").prop("disabled", true);
                            $("#antidepresivos").prop("disabled", true);
                            $("#antiinflamatorios").prop("disabled", true);
                            $("#protectores").prop("disabled", true);
                            $("#otros").prop("disabled", true);
                            $("#otros_texto").prop("disabled", true);
                        } else {
                            $("#antibioticos").prop("disabled", false);
                            $("#antidepresivos").prop("disabled", false);
                            $("#antiinflamatorios").prop("disabled", false);
                            $("#protectores").prop("disabled", false);
                            $("#otros").prop("disabled", false);
                            $("#otros_texto").prop("disabled", false);
                        }
                    });

                jQuery('#info_hentrega').timepicker({});
                jQuery('#info_hentrega').timepicker({
                    timeFormat: 'h:mm p',
                    interval: 60,
                    minTime: '10',
                    maxTime: '6:00pm',
                    defaultTime: '11',
                    startTime: '10:00',
                    dynamic: false,
                    dropdown: true,
                    scrollbar: true
                });
                $(document).ready(function () {
                  $("#uploadBtn").on("click", function(e) {
                    e.preventDefault();
                    $("#uploadStatus").html("<strong>Espre!</strong> Se está subiendo el archivo...");
                    var fileInput = document.getElementById('video');
                    var file = fileInput.files[0];
                    var formData = new FormData();
                    formData.append('video', file);
                
                    $.ajax({
                      url: '_operaciones/le_andro_esp_01.php',
                      type: 'POST',
                      data: formData,
                      processData: false,  // tell jQuery not to process the data
                      contentType: false,  // tell jQuery not to set contentType
                      success: function(data) {
                          var result = JSON.parse(data);  // parsear la data recibida del servidor
                          console.log("Archivo subido con éxito");
                          $("#uploadStatus").html("<strong>Éxito!</strong> Se registró el vídeo.");
                          $("#link_video").attr("href", "archivo/" + result.message.nombre_base);
                          $("#link_video").text(result.message.nombre_original);
                          $("#archivo_id").val(result.message.idarchivo);
                      },
                      error: function(jqXHR, textStatus, errorThrown) {
                        console.log(jqXHR);
                        console.log(textStatus);
                        console.log(errorThrown);
                        console.log("Hubo un error al subir el archivo");
                        $("#uploadStatus").html("<strong>Error!</strong> Comuníquese con el administrador de su sistema.");
                      }
                    });
                  });
                });

                //grafica1
                config_movi.data.datasets[0].data[0] = parseFloat($('#movi_mprogresivo').val());
                config_movi.data.labels[0] = "MP: ".concat(parseFloat($('#movi_mprogresivo').val()), " %");
                config_movi.data.datasets[0].data[1] = parseFloat($('#movi_mnoprogresivo').val());
                config_movi.data.labels[1] = "MNP: ".concat(parseFloat($('#movi_mnoprogresivo').val()), " %");
                config_movi.data.datasets[0].data[2] = parseFloat($('#movi_nmoviles').val());
                config_movi.data.labels[2] = "NM: ".concat(parseFloat($('#movi_nmoviles').val()), " %");
                //grafica2
                config.data.datasets[0].data[0] = parseFloat($('#concen_mprogresivo').val());
                config.data.labels[0] = "".concat(parseFloat($('#concen_mprogresivo').val()), " E/ml");
                config.data.datasets[1].data[0] = parseFloat($('#concen_mnoprogresivo').val());
                config.data.labels[1] = "".concat(parseFloat($('#concen_mnoprogresivo').val()), " E/ml");
                config.data.datasets[2].data[0] = parseFloat($('#concen_nmoviles').val());
                config.data.labels[2] = "".concat(parseFloat($('#concen_nmoviles').val()), " E/ml");
                //grafica3
                config_mtotal.data.datasets[0].data[0] = parseFloat($('#morfo_normal').val());
                config_mtotal.data.labels[0] = "".concat(parseFloat($('#morfo_normal').val()), " %");
                config_mtotal.data.datasets[0].data[1] = parseFloat($('#morfo_anormal').val());
                config_mtotal.data.labels[1] = "".concat(parseFloat($('#morfo_anormal').val()), " %");
                //grafica4
                config_mclasi.data.datasets[0].data[0] = parseFloat($('#morfo_normal').val());
                config_mclasi.data.labels[0] = "".concat(parseFloat($('#morfo_normal').val()), " %");
                config_mclasi.data.datasets[1].data[0] = parseFloat($('#morfo_anormal_cabeza').val()) + parseFloat($('#morfo_micro').val()) + parseFloat($('#morfo_macro').val()) + parseFloat($('#morfo_bicefalo').val());
                config_mclasi.data.labels[1] = "".concat(parseFloat($('#morfo_anormal_cabeza').val()) + parseFloat($('#morfo_micro').val()) + parseFloat($('#morfo_macro').val()) + parseFloat($('#morfo_bicefalo').val()), " %");
                config_mclasi.data.datasets[2].data[0] = parseFloat($('#morfo_anormal_pieza').val());
                config_mclasi.data.labels[2] = "".concat(parseFloat($('#morfo_anormal_pieza').val()), " %");
                config_mclasi.data.datasets[3].data[0] = parseFloat($('#morfo_anormal_cola').val()) + parseFloat($('#morfo_bicaudo').val());
                config_mclasi.data.labels[3] = "".concat(parseFloat($('#morfo_anormal_cola').val()) + parseFloat($('#morfo_bicaudo').val()), " %");
                config_mclasi.data.datasets[4].data[0] = parseFloat($('#morfo_inmaduro').val());
                config_mclasi.data.labels[4] = "".concat(parseFloat($('#morfo_inmaduro').val()), " %");
                //
                window.myLine0.update();
                window.myLine1.update();
                window.myLine2.update();
                window.myLine3.update();

                $('#form2').submit(function () {
                    var m_n = Number($('#m_n').val());
                    var m_a = Number($('#m_a').val());
                    if ((m_n + m_a) > 0 && (m_n + m_a) < 100) {
                        alert("El valor morfologico debe ser igual o mayor a 100 unidades");
                        return false;
                    }
                    if (confirm("¿Realmente desea guardar el informe?")) {
                        return true;
                    } else return false;
                });

                var vol_f = $('#vol_f').val();
                var con_f = $('#con_f').val();
                $('#spz_f').html((vol_f * con_f).toFixed(2));

                var m_n = Number($('#m_n').val());
                var m_a = Number($('#m_a').val());
                $('#pm_a').html(((m_a * 100) / (m_a + m_n)).toFixed(2) + '%');
                $('#pm_n').html((100 - ((m_a * 100) / (m_a + m_n))).toFixed(2) + '%');

                $(".total_spz").change(function () {
                    var vol_f = $('#vol_f').val();
                    var con_f = $('#con_f').val();

                    var pl_f = Number($('#pl_f').val());
                    var pnl_f = Number($('#pnl_f').val());
                    var ins_f = Number($('#ins_f').val());

                    $('#spz_f').html((vol_f * con_f).toFixed(2));

                    $("#inm_f").val(100 - (pl_f + pnl_f + ins_f));
                });

                $(".total_mor").change(function () {
                    var m_mic = Number($('#m_mic').val());
                    var m_mac = Number($('#m_mac').val());
                    var m_cab = Number($('#m_cab').val());
                    var m_col = Number($('#m_col').val());
                    var m_inm = Number($('#m_inm').val());
                    var m_bic = Number($('#m_bic').val());
                    var m_bic2 = Number($('#m_bic2').val());

                    $("#m_a").val(m_mic + m_mac + m_cab + m_col + m_inm + m_bic + m_bic2);

                    var m_n = Number($('#m_n').val());
                    var m_a = Number($('#m_a').val());
                    $('#pm_a').html(((m_a * 100) / (m_a + m_n)).toFixed(2) + '%');
                    $('#pm_n').html((100 - ((m_a * 100) / (m_a + m_n))).toFixed(2) + '%');

                });

                $("#azoos").change(function () {
                    if ($(this).prop('checked') ) {
                        $("#con_f,#pl_f,#pnl_f,#ins_f,#inm_f,#m_n,#m_a,#m_mic,#m_mac,#m_cab,#m_col,#m_inm,#m_bic,#m_bic2").val(0);
                        $("#nota").prop('required',true);
                    } else {
                        $("#con_f,#pl_f,#pnl_f,#ins_f,#inm_f,#m_n,#m_a,#m_mic,#m_mac,#m_cab,#m_col,#m_inm,#m_bic,#m_bic2").val('');
                        $("#nota").val('required',false);
                    }
                });

            });
        </script>
        <?php
            if ($pop['con_f']==0 and $pop['pl_f']==0 and $pop['pnl_f']==0 and $pop['ins_f']==0 and $pop['inm_f']==100 and $pop['m_n']==0 and $pop['m_a']==0 and $pop['m_mic']==0 and $pop['m_mac']==0 and $pop['m_cab']==0 and $pop['m_col']==0 and $pop['m_inm']==0 and $pop['m_bic']==0 and $pop['m_bic2']==0) {
        ?>
            <script>
                $(document).ready(function () {
                    $("#azoos").attr("checked", "checked");
                    $("#azoos").checkboxradio("refresh");
                });
            </script>
            <?php } ?>
            <div class="container" role="main">
                <nav aria-label="breadcrumb">
                    <?php print('<a class="breadcrumb" href="e_pare.php?id='.$_GET['dni'].'&ip='.$_GET['p_dni'].'" rel="external" class="no-underline"><img src="_libraries/open-iconic/svg/x.svg" height="18" width="18" alt="icon name"></a>'); ?>

                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="lista.php" rel="external">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="lista_and.php" rel="external">Andrología</a></li>
                        <li class="breadcrumb-item">
                            <?php print('<a href="e_pare.php?id='.$_GET['dni'].'&ip='.$_GET['p_dni'].'" rel="external">'.ucwords(mb_strtolower($pare['p_ape']))." ".ucwords(mb_strtolower($pare['p_nom'])).'</a>'); ?>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Espermatograma</li>
                    </ol>
                </nav>
                <form action="" method="post" data-ajax="false" id="form2" name="form2">
                    <input type="hidden" name="p_dni" value="<?php echo $p_dni; ?>">
                    <input type="hidden" name="idx" value="<?php echo $fec; ?>">
                    <input type="hidden" name="dni" value="<?php echo $dni; ?>">
                    <div class="card mb-3" id="info_general">
                        <h5 class="card-header">Información General</h5>
                        <div class="card-body">
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">F. de Resultado</span>
                                        <input class="form-control form-control-sm" tabindex=1 name="fec" id="fec" type="date" value="<?php print($pop["fec"]); ?>">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Paciente</span>
                                        <input type="text" class="form-control form-control-sm" name="pareja" id="pareja" value="<?php print($pareja); ?>" aria-describedby="basic-addon3" disabled>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Pareja</span>
                                        <input type="text" class="form-control form-control-sm" name="paciente" id="paciente" value="<?php print($paciente); ?>" aria-describedby="basic-addon3" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Médico</span>
                                        <input type="text" class="form-control form-control-sm" name="medico" id="medico" value="<?php print($medico); ?>" aria-describedby="basic-addon3" disabled>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Embriologo</span>
                                        <select class="form-control form-control-sm" tabindex=2 name="emb" id="emb" required>
                                            <option value="">Seleccionar</option>
                                            <?php
                                                while ($emb = $rEmb->fetch(PDO::FETCH_ASSOC)) { ?>
                                                    <option value=<?php echo $emb['id'];
                                                if ($pop['emb'] == $emb['id']) echo " selected"; ?>><?php echo $emb['nom']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-3" id="info_clinica">
                        <h5 class="card-header">Información Clínica</h5>
                        <div class="card-body">
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">F. de Obtención</span>
                                        <input class="form-control form-control-sm" tabindex=3 name="info_fmuestra" id="info_fmuestra" type="date" value="<?php if(empty($pop['info_fmuestra'])) {print(date('Y-m-d'));} else {print($pop['info_fmuestra']);} ?>">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Lugar de Obtención</span>
                                        <select class="form-control form-control-sm" tabindex=4 name="info_lobtencion" id="info_lobtencion">
                                            <option value="">Seleccionar</option>
                                            <?php
                                                if (empty($pop['info_lobtencion'])) $pop['info_lobtencion'] = "1";
                                                while ($lobte = $rlobte->fetch(PDO::FETCH_ASSOC)) { ?>
                                                    <option value=<?php echo $lobte['id'];
                                                if ($pop['info_lobtencion'] == $lobte['id']) echo " selected"; ?>><?php echo $lobte['nombre']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Hora de Entrega</span>
                                        <input type="text" tabindex=5 class="form-control form-control-sm" name="info_hentrega" id="info_hentrega" value="<?php print($pop['info_hentrega']) ;?>" aria-describedby="basic-addon3" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">M. de Obtención</span>
                                        <select class="form-control form-control-sm" tabindex=6 name="info_mobtencion" id="info_mobtencion">
                                            <option value="">Seleccionar</option>
                                            <?php
                                                if (empty($pop['info_mobtencion'])) $pop['info_mobtencion'] = "1";
                                                while ($mobte = $rmobte->fetch(PDO::FETCH_ASSOC)) { ?>
                                                    <option value=<?php echo $mobte['id'];
                                                if ($pop['info_mobtencion'] == $mobte['id']) echo " selected"; ?>><?php echo $mobte['nombre']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Dificultad para Obtención</span>
                                        <select class="form-control form-control-sm" tabindex=7  name="info_dobtencion" id="info_dobtencion">
                                            <option value="">Seleccionar</option>
                                            <?php
                                                if (empty($pop['info_dobtencion'])) $pop['info_dobtencion'] = "2";
                                                foreach ($rows as $sino) {?>
                                                    <option value=<?php echo $sino['id'];
                                                    if ($pop['info_dobtencion'] == $sino['id']) echo " selected"; ?>><?php echo $sino['nombre']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Abstinencia sexual</span>
                                        <input type="text" class="form-control form-control-sm" name="abstinencia" id="abstinencia" value="<?php print($pop['abstinencia']); ?>" required>
                                        <span class="input-group-text">días</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Medicación</span>
                                        <select class="form-control form-control-sm" tabindex=8 name="info_medicacion" id="info_medicacion">
                                            <option value="">Seleccionar</option>
                                            <?php
                                                if (empty($pop['info_medicacion'])) $pop['info_medicacion'] = "2";
                                                foreach ($rows as $sino) {?>
                                                    <option value=<?php echo $sino['id'];
                                                    if ($pop['info_medicacion'] == $sino['id']) echo " selected"; ?>><?php echo $sino['nombre']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                    <div class="form-check">
                                        <?php print('<input class="form-check-input" type="checkbox" id="antibioticos" name="antibioticos" ' . ($pop['antibioticos']==1 ? 'checked' : '') . ($pop['info_medicacion']==2 ? 'disabled' : '') . '>'); ?>
                                        <label class="form-check-label" for="antibioticos">Antibioticos o Antifúngicos</label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                    <div class="form-check">
                                        <?php print('<input class="form-check-input" type="checkbox" id="antidepresivos" name="antidepresivos" ' . ($pop['antidepresivos']==1 ? 'checked' : '') . ($pop['info_medicacion']==2 ? 'disabled' : '') . '>'); ?>
                                        <label class="form-check-label" for="antidepresivos">Antidepresivos o Anticonvulsivos</label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                    <div class="form-check">
                                        <?php print('<input class="form-check-input" type="checkbox" id="antiinflamatorios" name="antiinflamatorios" ' . ($pop['antiinflamatorios']==1 ? 'checked' : '') . ($pop['info_medicacion']==2 ? 'disabled' : '') . '>'); ?>
                                        <label class="form-check-label" for="antiinflamatorios">Antiinflamatorios</label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                    <div class="form-check">
                                        <?php print('<input class="form-check-input" type="checkbox" id="protectores" name="protectores" ' . ($pop['protectores']==1 ? 'checked' : '') . ($pop['info_medicacion']==2 ? 'disabled' : '') . '>'); ?>
                                        <label class="form-check-label" for="protectores">Protectores Gástricos o Hepáticos</label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                    <div class="form-check">
                                        <?php print('<input class="form-check-input" type="checkbox" id="otros" name="otros" ' . (!!$pop['otros_texto'] ? 'checked' : '') . ($pop['info_medicacion']==2 ? 'disabled' : '') . '>'); ?>
                                        <label class="form-check-label" for="otros">Otros</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 input-group-sm">
                                    <div class="input-group-prepend">
                                        <?php print('<textarea class="form-control form-control-sm" id="otros_texto" name="otros_texto" placeholder="Indicar que medicamentos" value="' . $pop['otros_texto'] . '" ' . ($pop['info_medicacion']==2 ? 'disabled' : '') . '>' . $pop['otros_texto'] . '</textarea>'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-3">
                        <h5 class="card-header">Análisis Macroscópico</h5>
                        <div class="card-body">
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Apariencia</span>
                                        <select class="form-control form-control-sm" tabindex=9 name="macro_apariencia" id="macro_apariencia">
                                            <option value="">Seleccionar</option>
                                            <?php
                                                foreach ($apaespe as $row) {?>
                                                    <option value=<?php echo $row['id'];
                                                    if ($pop['macro_apariencia'] == $row['id']) echo " selected"; ?>><?php echo $row['nombre']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm"></div>
                            </div>
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Viscosidad</span>
                                        <select class="form-control form-control-sm" tabindex=10 name="macro_viscosidad" id="macro_viscosidad">
                                            <option value="">Seleccionar</option>
                                            <?php
                                                foreach ($visespe as $row) {?>
                                                    <option value=<?php echo $row['id'];
                                                    if ($pop['macro_viscosidad'] == $row['id']) echo " selected"; ?>><?php echo $row['nombre']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm"></div>
                            </div>
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Licuefacción</span>
                                        <select class="form-control form-control-sm" tabindex=11 id="macro_liquefaccion" name="macro_liquefaccion">
                                            <option value="">Seleccionar</option>
                                            <?php
                                                foreach ($liqespe as $row) {?>
                                                    <option value=<?php echo $row['id'];
                                                    if ($pop['macro_liquefaccion'] == $row['id']) echo " selected"; ?>><?php echo $row['nombre']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Aglutinación</span>
                                        <select class="form-control form-control-sm" tabindex=12 name="macro_aglutinacion" id="macro_aglutinacion">
                                            <option value="">Seleccionar</option>
                                            <?php
                                                foreach ($rows1 as $sino) {?>
                                                    <option value=<?php echo $sino['id'];
                                                    if ($pop['macro_aglutinacion'] == $sino['id']) echo " selected"; ?>><?php echo $sino['nombre']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                    <div class="input-group-prepend">
                                        <input type="text" class="form-control form-control-sm" tabindex=13 name="macro_aglutinacion_porc" id="macro_aglutinacion_porc" value="<?php print($pop['macro_aglutinacion_porc']?:0); ?>" aria-describedby="basic-addon3" required>
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">pH</span>
                                        <input type="number" tabindex=14 step="0.1" min="0.0" max="9.9" class="form-control form-control-sm" name="macro_ph" id="macro_ph" value="<?php print($pop['macro_ph']); ?>" aria-describedby="basic-addon3">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                    <div class="input-group-prepend">
                                        <input type="text" class="form-control form-control-sm" value="≥ 7.2" aria-describedby="basic-addon3" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Volumen</span>
                                        <input type="number" tabindex=15 step="0.1" min="0.0" max="99.9" class="form-control form-control-sm" name="macro_volumen" id="macro_volumen" value="<?php print($pop['macro_volumen']); ?>" value="" aria-describedby="basic-addon3">
                                        <span class="input-group-text">ml</span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                    <div class="input-group-prepend">
                                        <input type="text" class="form-control form-control-sm" value="≥ 1.5ml" aria-describedby="basic-addon3" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-3">
                        <h5 class="card-header">Concentración</h5>
                        <div class="card-body">
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Espermas por ml</span>
                                        <input type="text" tabindex=16 class="form-control form-control-sm" name="concen_exml" id="concen_exml" value="<?php print($pop['concen_exml']); ?>" aria-describedby="basic-addon3">
                                        <span class="input-group-text">M/ml</span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                    <div class="input-group-prepend">
                                        <input type="text" class="form-control form-control-sm" value="≥ 15 Millones por ml" aria-describedby="basic-addon3" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Células redondas</span>
                                        <input type="text" tabindex=17 class="form-control form-control-sm" name="concen_credon" id="concen_credon" value="<?php print($pop['concen_credon']); ?>">
                                        <span class="input-group-text">M/ml</span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                    <div class="input-group-prepend">
                                        <input type="text" class="form-control form-control-sm" value="< 5 Millones x ml" aria-describedby="basic-addon3" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Espermas por eyaculado</span>
                                        <input type="text" tabindex=18 class="form-control form-control-sm" id="concen_exeyac" value="<?php print(number_format($pop['concen_exml']*$pop['macro_volumen'], 2, '.', '')); ?>" disabled>
                                        <span class="input-group-text">Millones</span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                    <div class="input-group-prepend">
                                        <input type="text" class="form-control form-control-sm" value="≥ 39 Millones" aria-describedby="basic-addon3" disabled>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Móvil Progresivo (P)</span>
                                        <input type="text" tabindex=19 class="form-control form-control-sm" id="concen_mprogresivo" value="<?php print(number_format($pop['concen_exml']*$pop['macro_volumen']*$pop['movi_mprogresivo']/100, 2, '.', '')); ?>" disabled>
                                        <span class="input-group-text">E/ml</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Móvil No progresivo (NP)</span>
                                        <input type="text" tabindex=20 class="form-control form-control-sm" id="concen_mnoprogresivo" value="<?php print(number_format($pop['concen_exml']*$pop['macro_volumen']*$pop['movi_mnoprogresivo']/100, 2, '.', '')); ?>" disabled>
                                        <span class="input-group-text">E/ml</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">No móviles</span>
                                        <input type="text" tabindex=21 class="form-control form-control-sm" id="concen_nmoviles" value="<?php print(number_format($pop['concen_exml']*$pop['macro_volumen']*(100-$pop['movi_mprogresivo']-$pop['movi_mnoprogresivo'])/100, 2, '.', '')); ?>" disabled>
                                        <span class="input-group-text">E/ml</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                    <input name="img_concen" id='img_concen' type="hidden"/>
                                    <div style="width: 600px !important; height: 250px !important;">
                                        <canvas id="canvas"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-3">
                        <h5 class="card-header">Movilidad y Vitalidad</h5>
                        <div class="card-body">
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><small>Total móviles (P + NP)</small></span>
                                        <input type="text" class="form-control form-control-sm" name="movi_tmoviles" id="movi_tmoviles" value="<?php print($pop['movi_mprogresivo']+$pop['movi_mnoprogresivo']); ?>" aria-describedby="basic-addon3" disabled>
                                        <span class="input-group-text"><small>%</small></span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend">
                                        <input type="text" tabindex=22 class="form-control form-control-sm" name="movi_tmoviles_cantidad" id="movi_tmoviles_cantidad" value="<?php print($pop['movi_mprogresivo_lineal_cantidad']+$pop['movi_mprogresivo_no_lineal_cantidad']+$pop['movi_mnoprogresivo_cantidad']); ?>" aria-describedby="basic-addon3" disabled>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend">
                                        <input type="text" class="form-control form-control-sm" value="≥ 40%" aria-describedby="basic-addon3" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><small>Móvil Progresivo (P)</small></span>
                                        <input type="text" class="form-control form-control-sm" name="movi_mprogresivo" id="movi_mprogresivo" value="<?php print($pop['movi_mprogresivo']); ?>" aria-describedby="basic-addon3" readonly>
                                        <span class="input-group-text"><small>%</small></span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend">
                                        <input type="text" tabindex=23 class="form-control form-control-sm" name="movi_mprogresivo_cantidad" id="movi_mprogresivo_cantidad" value="<?php print($pop['movi_mprogresivo_lineal_cantidad']+$pop['movi_mprogresivo_no_lineal_cantidad']); ?>" aria-describedby="basic-addon3" disabled>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend">
                                        <input type="text" class="form-control form-control-sm" value="≥ 32%" aria-describedby="basic-addon3" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><small>M.P. Lineal (VAP >= 25&#181;m/s)</small></span>
                                        <?php
                                            $denominador = $pop['movi_mprogresivo_lineal_cantidad'] + $pop['movi_mprogresivo_no_lineal_cantidad'] + $pop['movi_mnoprogresivo_cantidad'] + $pop['movi_nmoviles_cantidad'];
                                            $porcentaje = $denominador != 0 ? number_format($pop['movi_mprogresivo_lineal_cantidad'] * 100 / $denominador, 2) : 0;
                                            ?>
                                            <input type="text" class="form-control form-control-sm" name="movi_mprogresivo_lineal" id="movi_mprogresivo_lineal" value="<?php print($porcentaje); ?>" aria-describedby="basic-addon3" disabled>
                                        <span class="input-group-text"><small>%</small></span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend">
                                        <input type="text" tabindex=24 class="form-control form-control-sm" name="movi_mprogresivo_lineal_cantidad" id="movi_mprogresivo_lineal_cantidad" value="<?php print($pop['movi_mprogresivo_lineal_cantidad']); ?>" aria-describedby="basic-addon3">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm"></div>
                            </div>
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><small>M.P. No Lineal (5&#181;m/s <= VAP < 25&#181;m/s)</small></span>
                                        <?php
                                            $denominador = $pop['movi_mprogresivo_lineal_cantidad'] + $pop['movi_mprogresivo_no_lineal_cantidad'] + $pop['movi_mnoprogresivo_cantidad'] + $pop['movi_nmoviles_cantidad'];
                                            $porcentaje_no_lineal = $denominador != 0 ? number_format($pop['movi_mprogresivo_no_lineal_cantidad'] * 100 / $denominador, 2) : 0;
                                            ?>
                                            <input type="text" class="form-control form-control-sm" name="movi_mprogresivo_no_lineal" id="movi_mprogresivo_no_lineal" value="<?php print($porcentaje_no_lineal); ?>" aria-describedby="basic-addon3" disabled>
                                        <span class="input-group-text"><small>%</small></span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend">
                                        <input type="text" tabindex=25 class="form-control form-control-sm" name="movi_mprogresivo_no_lineal_cantidad" id="movi_mprogresivo_no_lineal_cantidad" value="<?php print($pop['movi_mprogresivo_no_lineal_cantidad']); ?>" aria-describedby="basic-addon3">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm"></div>
                            </div>
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><small>Móvil No progresivo (NP)</small></span>
                                        <input type="text" class="form-control form-control-sm" name="movi_mnoprogresivo" id="movi_mnoprogresivo" value="<?php print($pop['movi_mnoprogresivo']); ?>" aria-describedby="basic-addon3" readonly>
                                        <span class="input-group-text"><small>%</small></span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend">
                                        <input type="text" tabindex=26 class="form-control form-control-sm" name="movi_mnoprogresivo_cantidad" id="movi_mnoprogresivo_cantidad" value="<?php print($pop['movi_mnoprogresivo_cantidad']); ?>" aria-describedby="basic-addon3">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm"></div>
                            </div>
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon3"><small>No móviles</small></span>
                                        <input type="text" class="form-control form-control-sm" name="movi_nmoviles" id="movi_nmoviles" value="<?php print(100-$pop['movi_mprogresivo']-$pop['movi_mnoprogresivo']); ?>" aria-describedby="basic-addon3" readonly>
                                        <span class="input-group-text"><small>%</small></span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend">
                                        <input type="text" tabindex=27 class="form-control form-control-sm" name="movi_nmoviles_cantidad" id="movi_nmoviles_cantidad" value="<?php print($pop['movi_nmoviles_cantidad']); ?>" aria-describedby="basic-addon3">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend"></div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><small>Test de Vitalidad</small></span>
                                        <input type="text" tabindex=28 class="form-control form-control-sm" name="movi_tvitalidad" id="movi_tvitalidad" value="<?php print($pop['movi_tvitalidad']); ?>" aria-describedby="basic-addon3">
                                        <span class="input-group-text"><small>%</small></span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend"></div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend">
                                        <input type="text" class="form-control form-control-sm" value="≥ 58%" aria-describedby="basic-addon3" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                    <input name="img_movi" id='img_movi' type="hidden"/>
                                    <div style="width: 600px !important; height: 250px !important;">
                                        <canvas id="canvas_movi"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-3">
                        <h5 class="card-header">Cinética Espermática</h5>
                        <div class="card-body">
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><small>VAP</small></span>
                                        <input type="number" tabindex=29 step="1" min="0" max="99" class="form-control form-control-sm" name="cinetica_vap" id="cinetica_vap" value="<?php print(number_format($pop['cine_vap'])); ?>">
                                        <span class="input-group-text"><small>&#181;m/s</small></span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><small>LIN</small></span>
                                        <input type="number" tabindex=32 step="1" min="0" max="99" class="form-control form-control-sm" name="cinetica_lin" id="cinetica_lin" value="<?php print(number_format($pop['cine_lin'])); ?>">
                                        <span class="input-group-text"><small>%</small></span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><small>ALH</small></span>
                                        <input type="number" tabindex=35 step="0.1" min="0" max="99.9" class="form-control form-control-sm" name="cinetica_alh" id="cinetica_alh" value="<?php print(number_format($pop['cine_alh'], 1)); ?>">
                                        <span class="input-group-text"><small>&#181;m</small></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><small>VSL</small></span>
                                        <input type="number" tabindex=30 step="1" min="0" max="99" class="form-control form-control-sm" name="cinetica_vsl" id="cinetica_vsl" value="<?php print(number_format($pop['cine_vsl'])); ?>">
                                        <span class="input-group-text"><small>&#181;m/s</small></span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><small>STR</small></span>
                                        <input type="number" tabindex=33 step="1" min="0" max="99" class="form-control form-control-sm" name="cinetica_str" id="cinetica_str" value="<?php print(number_format($pop['cine_str'])); ?>">
                                        <span class="input-group-text"><small>%</small></span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><small>BCF</small></span>
                                        <input type="number" tabindex=36 step="0.1" min="0" max="99.9" class="form-control form-control-sm" name="cinetica_bcf" id="cinetica_bcf" value="<?php print(number_format($pop['cine_bcf'], 1)); ?>">
                                        <span class="input-group-text"><small>Hz</small></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><small>VCL</small></span>
                                        <input type="number" tabindex=31 step="1" min="0" max="99" class="form-control form-control-sm" name="cinetica_vcl" id="cinetica_vcl" value="<?php print(number_format($pop['cine_vcl'])); ?>">
                                        <span class="input-group-text"><small>&#181;m/s</small></span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><small>WOB</small></span>
                                        <input type="number" tabindex=34 step="1" min="0" max="99" class="form-control form-control-sm" name="cinetica_wob" id="cinetica_wob" value="<?php print(number_format($pop['cine_wob'])); ?>">
                                        <span class="input-group-text"><small>%</small></span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-3">
                        <h5 class="card-header">Morfología</h5>
                        <div class="card-body">
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Normales</span>
                                        <input type="text" tabindex=37 class="form-control form-control-sm" name="morfo_normal" id="morfo_normal" value="<?php print($pop['morfo_normal']); ?>">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                    <div class="input-group-prepend">
                                        <input type="text" class="form-control form-control-sm" value="≥ 4%" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Anormales</span>
                                        <?php
                                            if ($pop['resul_azo']==1) {
                                                $morfo_anormal=0;
                                            } else {
                                                $morfo_anormal=100-$pop['morfo_normal'];
                                            }
                                        ?>
                                        <input type="text" tabindex=38 class="form-control form-control-sm" id="morfo_anormal" value="<?php print($morfo_anormal); ?>" disabled>
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                    <div class="input-group-prepend">
                                    </div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Anormalidades de Cabeza</span>
                                        <input type="text" tabindex=39 class="form-control form-control-sm" name="morfo_anormal_cabeza" id="morfo_anormal_cabeza" value="<?php print($pop['morfo_anormal_cabeza']); ?>" aria-describedby="basic-addon3">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm"></div>
                            </div>
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Anormalidades de Pieza media</span>
                                        <input type="text" tabindex=40 class="form-control form-control-sm" name="morfo_anormal_pieza" id="morfo_anormal_pieza" value="<?php print($pop['morfo_anormal_pieza']); ?>">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm"></div>
                            </div>
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Anormalidades de Cola</span>
                                        <input type="text" tabindex=41 class="form-control form-control-sm" name="morfo_anormal_cola" id="morfo_anormal_cola" value="<?php print($pop['morfo_anormal_cola']); ?>">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm"></div>
                            </div>
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Microcefalo</span>
                                        <input type="text" tabindex=42 class="form-control form-control-sm" name="morfo_micro" id="morfo_micro" value="<?php echo $pop['morfo_micro']; ?>">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm"></div>
                            </div>
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Macrocefalo</span>
                                        <input type="text" tabindex=43 class="form-control form-control-sm" name="morfo_macro" id="morfo_macro" value="<?php echo $pop['morfo_macro']; ?>">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="input-group-prepend">
                                    </div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Inmaduro</span>
                                        <input type="text" tabindex=44 class="form-control form-control-sm" name="morfo_inmaduro" id="morfo_inmaduro" value="<?php echo $pop['morfo_inmaduro']; ?>" aria-describedby="basic-addon3">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm"></div>
                            </div>
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Bicefalo</span>
                                        <input type="text" tabindex=45 class="form-control form-control-sm" name="morfo_bicefalo" id="morfo_bicefalo" value="<?php echo $pop['morfo_bicefalo']; ?>">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm"></div>
                            </div>
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Bicaudo</span>
                                        <input type="text" tabindex=46 class="form-control form-control-sm" name="morfo_bicaudo" id="morfo_bicaudo" value="<?php echo $pop['morfo_bicaudo']; ?>">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                    <div class="input-group-prepend">
                                    </div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                    <input name="img_mtotal" id='img_mtotal' type="hidden"/>
                                    <div style="width: 600px !important; height: 250px !important;">
                                        <canvas id="canvas_mtotal"></canvas>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                    <input name="img_mclasi" id='img_mclasi' type="hidden"/>
                                    <div style="width: 600px !important; height: 250px !important;">
                                        <canvas id="canvas_mclasi"></canvas>
                                    </div>
                                </div>
                            </div><br>
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12 input-group-sm">
                                    <table class="table table-hover">
                                        <thead class="thead-dark">
                                            <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">PARÁMETROS EVALUADOS</th>
                                            <th class="text-center" scope="col">PROMEDIO</th>
                                            <th class="text-center" scope="col">PORCENTAJE DE ANORMALES</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th scope="row">1</th>
                                                <td>Largo de Cabeza</td>
                                                <td>
                                                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <input type="text" tabindex=47 class="form-control form-control-sm" name="normal_largocabeza_promedio" id="normal_largocabeza_promedio" value="<?php print($pop["normal_largocabeza_promedio"]); ?>">
                                                            <span class="input-group-text"><small>&#181;m</small></span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <input type="text" tabindex=48 class="form-control form-control-sm" name="normal_largocabeza_porcentaje" id="normal_largocabeza_porcentaje" value="<?php print($pop["normal_largocabeza_porcentaje"]); ?>">
                                                            <span class="input-group-text"><small>%</small></span>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">2</th>
                                                <td>Ancho de Cabeza</td>
                                                <td>
                                                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <input type="text" tabindex=49 class="form-control form-control-sm" name="normal_ancho_promedio" id="normal_ancho_promedio" value="<?php print($pop["normal_ancho_promedio"]); ?>">
                                                            <span class="input-group-text"><small>&#181;m</small></span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <input type="text" tabindex=50 class="form-control form-control-sm" name="normal_ancho_porcentaje" id="normal_ancho_porcentaje" value="<?php print($pop["normal_ancho_porcentaje"]); ?>">
                                                            <span class="input-group-text"><small>%</small></span>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">3</th>
                                                <td>Perímetro de Cabeza</td>
                                                <td>
                                                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <input type="text" tabindex=51 class="form-control form-control-sm" name="normal_perimetro_promedio" id="normal_perimetro_promedio" value="<?php print($pop["normal_perimetro_promedio"]); ?>">
                                                            <span class="input-group-text"><small>&#181;m</small></span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <input type="text" tabindex=52 class="form-control form-control-sm" name="normal_perimetro_porcentaje" id="normal_perimetro_porcentaje" value="<?php print($pop["normal_perimetro_porcentaje"]); ?>">
                                                            <span class="input-group-text"><small>%</small></span>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">4</th>
                                                <td>Área de Cabeza</td>
                                                <td>
                                                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <input type="text" tabindex=53 class="form-control form-control-sm" name="normal_area_promedio" id="normal_area_promedio" value="<?php print($pop["normal_area_promedio"]); ?>">
                                                            <span class="input-group-text"><small>&#181;m<sup>2</sup></small></span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <input type="text" tabindex=54 class="form-control form-control-sm" name="normal_area_porcentaje" id="normal_area_porcentaje" value="<?php print($pop["normal_area_porcentaje"]); ?>">
                                                            <span class="input-group-text"><small>%</small></span>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">5</th>
                                                <td>Largo de la Cola</td>
                                                <td>
                                                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <input type="text" tabindex=55 class="form-control form-control-sm" name="normal_largocola_promedio" id="normal_largocola_promedio" value="<?php print($pop["normal_largocola_promedio"]); ?>">
                                                            <span class="input-group-text"><small>&#181;m</small></span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <input type="text" tabindex=56 class="form-control form-control-sm" name="normal_largocola_porcentaje" id="normal_largocola_porcentaje" value="<?php print($pop["normal_largocola_porcentaje"]); ?>">
                                                            <span class="input-group-text"><small>%</small></span>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-3">
                        <h5 class="card-header">Diagnóstico</h5>
                        <div class="card-body">
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="resul_hipos" name="resul_hipos" disabled>
                                        <label class="form-check-label" for="resul_hipos">Hipospermia</label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="resul_oligo" disabled>
                                        <label class="form-check-label" for="resul_oligo">Oligozoospermia</label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="resul_aste" disabled>
                                        <label class="form-check-label" for="resul_aste">Astenozoospermia</label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="resul_tera" disabled>
                                        <label class="form-check-label" for="resul_tera">Teratozoospermia</label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="resul_cripto" name="resul_cripto" <?php echo ($pop['resul_cripto']==1 ? 'checked' : '');?>>
                                        <label class="form-check-label" for="resul_cripto">Criptozoospermia</label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="resul_azo" name="resul_azo" <?php echo ($pop['resul_azo']==1 ? 'checked' : '');?>>
                                        <label class="form-check-label" for="resul_azo">Azoospermia</label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6 col-lg-4 input-group-sm">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="resul_necro" disabled="">
                                        <label class="form-check-label" for="resul_necro">Necrozoospermia</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Observaciones</span>
                                        <textarea name="observaciones" class="form-control form-control-sm" value="<?php print($pop['nota']); ?>"><?php print($pop['nota']); ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-3">
                        <h5 class="card-header">Vídeo descripción</h5>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="video">Subir vídeo de Espermatograma</label>
                                <input type="file" class="form-control-file" name="video" id="video" accept="video/*">
                                <input type="hidden" name="archivo_id" id="archivo_id" value="<?php if(isset($archivo['id'])) echo "{$archivo['id']}"; ?>">
                                <a href="<?php if(isset($archivo['nombre_base'])) echo "/archivo/{$archivo['nombre_base']}"; ?>" id="link_video" target="_blank" rel="noopener noreferrer"><?php if(isset($archivo['nombre_base'])) echo "{$archivo['nombre_base']}"; ?></a>
                            </div>
                            <button class="btn btn-sm btn-dark" type="button" id="uploadBtn">Subir video</button>
                            <div id="uploadStatus"></div>
                        </div>
                    </div>
                    <?php
                    if ($_SESSION['role'] == 2) { ?>
                    <div class="text-right">
                        <?php
                            $date = "2019-10-10";

                            if ($date <= $_GET["fec"] ) {
                                print('<a href="le_andro_esp_pdf_1.php?p_dni='.$_GET["p_dni"].'&fec='.$_GET["fec"].'&dni='.$_GET["dni"].'" target="_blank"><img src="_images/pdf.png" height="20" width="20" alt="icon name"></a>&nbsp&nbsp');
                            } else {
                                print('<a href="le_andro_esp_pdf.php?p_dni='.$_GET["p_dni"].'&fec='.$_GET["fec"].'&dni='.$_GET["dni"].'" target="_blank"><img src="_images/pdf.png" height="20" width="20" alt="icon name"></a>&nbsp&nbsp');
                            } ?>
                        <button type="submit" name="guardar" class="btn btn-danger">Guardar Informe</button>
                    </div>
                    <?php } ?>
                </form>
            </div>
            <footer class="footer">
                <div class="container">
                    <span class="text-muted"></span>
                </div>
            </footer>
        <?php } ?>
    </div>
    <script src="js/popper.min.js" crossorigin="anonymous"></script>
    <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script>
        var config = {
            type: 'bar',
            data: {
                //meses en el grafico
                labels: ["Móvil Progresivo(P)", "Móvil No Progresivo(NP)", "No Móviles"],
                datasets: [{
                    //titulo al costado del cuadrado
                    label: "Móvil Progresivo(P)",
                    //color de cuadrado y puntos sobre la linea
                    backgroundColor: window.chartColors.red,
                    //borde de cuadrado y linea ondeada
                    borderColor: window.chartColors.red,
                    borderWidth: 1,
                    //data segun la cantidad de meses del grafico
                    data: [],
                    //pruebalo para que veas
                    fill: false,
                }, {
                    label: "Móvil No Progresivo(NP)",
                    fill: false,
                    backgroundColor: window.chartColors.blue,
                    borderColor: window.chartColors.blue,
                    borderWidth: 1,
                    data: [],
                }, {
                    label: "No Móviles",
                    fill: false,
                    backgroundColor: window.chartColors.green,
                    borderColor: window.chartColors.green,
                    borderWidth: 1,
                    data: [],
                }]
            },
            options: {
                bezierCurve : false,
                animation: {
                    onComplete: function(animation) {
                        var can4 = document.getElementById('canvas');
                        var img4 = can4.toDataURL();
                        document.getElementById('img_concen').value = img4;
                    }
                },
                responsive: true,
                //titulo de la parte superior
                title:{
                    display:true,
                    text:'Gráfica 1: Concentración de Espermatozoides Móviles en Eyaculado'
                },
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                        }
                    }],
                    yAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                        },
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        };
        var config_movi = {
            type: 'pie',
            data: {
                //meses en el grafico
                labels: ["Móvil Progresivo(P)", "Móvil No Progresivo(NP)", "No Móviles"],
                datasets: [{
                    label: "Total", //titulo al costado del cuadrado
                    backgroundColor: [window.chartColors.red, window.chartColors.green, window.chartColors.yellow], //color de cuadrado y puntos sobre la linea
                    //data segun la cantidad de meses del grafico
                    data: [
                        0,
                        0,
                        0
                    ],
                    fill: false,
                }]
            },
            options: {
                bezierCurve : false,
                animation: {
                    onComplete: function(animation) {
                        var can = document.getElementById('canvas_movi');
                        var img = can.toDataURL();
                        document.getElementById('img_movi').value = img;
                    }
                },
                responsive: true,
                title:{
                    display:true,
                    text:'Gráfica 2: Movilidad'
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                        }
                    }],
                    yAxes: [{
                        display: false,
                        scaleLabel: {
                            display: true,
                        }
                    }]
                }
            }
        };
        var config_mtotal = {
            type: 'pie',
            data: {
                //meses en el grafico
                labels: ["Normales", "Anormales"],
                datasets: [{
                    //titulo al costado del cuadrado
                    label: "Total",
                    //color de cuadrado y puntos sobre la linea
                    backgroundColor: [window.chartColors.red, window.chartColors.green, window.chartColors.yellow],
                    //data segun la cantidad de meses del grafico
                    data: [
                        0,
                        0
                    ],
                    //pruebalo para que veas
                    fill: false,
                }]
            },
            options: {
                bezierCurve : false,
                animation: {
                    onComplete: function(animation) {
                        var can2 = document.getElementById('canvas_mtotal');
                        var img2 = can2.toDataURL();
                        document.getElementById('img_mtotal').value = img2;
                    }
                },
                responsive: true,
                //titulo de la parte superior
                title:{
                    display:true,
                    text:'Gráfica 3: Morfología Espermática'
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            // labelString: 'Month'
                        }
                    }],
                    yAxes: [{
                        display: false,
                        scaleLabel: {
                            display: true,
                            // labelString: 'Value'
                        }
                    }]
                }
            }
        };
        var config_mclasi = {
            type: 'bar',
            data: {
                //meses en el grafico
                labels: ["Normales", "A. de Cabeza", "A. de Pieza media", "A. de Cola", "Inmaduro"],
                datasets: [{
                    label: "Normales",
                    fill: false,
                    backgroundColor: [window.chartColors.red],
                    borderColor: window.chartColors.red,
                    borderWidth: 1,
                    data: [],
                }, {
                    label: "A. de Cabeza",
                    fill: false,
                    backgroundColor: window.chartColors.blue,
                    borderColor: window.chartColors.blue,
                    borderWidth: 1,
                    data: [],
                }, {
                    label: "A. de Pieza media",
                    fill: false,
                    backgroundColor: window.chartColors.green,
                    borderColor: window.chartColors.green,
                    borderWidth: 1,
                    data: [],
                }, {
                    label: "A. de Cola",
                    fill: false,
                    backgroundColor: window.chartColors.yellow,
                    borderColor: window.chartColors.yellow,
                    borderWidth: 1,
                    data: [],
                }, {
                    label: "Inmaduro",
                    fill: false,
                    backgroundColor: window.chartColors.black,
                    borderColor: window.chartColors.black,
                    borderWidth: 1,
                    data: [],
                }]
            },
            options: {
                bezierCurve : false,
                animation: {
                    onComplete: function(animation) {
                        var can3 = document.getElementById('canvas_mclasi');
                        var img3 = can3.toDataURL();
                        document.getElementById('img_mclasi').value = img3;
                    }
                },
                responsive: true,
                //titulo de la parte superior
                title:{
                    display:true,
                    text:'Gráfica 4: Clasificación Morfológica'
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            // labelString: 'Month'
                        }
                    }],
                    yAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            // labelString: 'Value'
                        }
                    }]
                }
            }
        };
        //funcion principal
        var colorNames = Object.keys(window.chartColors);

        config.data.datasets[0].data[0] = parseFloat($('#concen_mprogresivo').val());
        config.data.datasets[1].data[0] = parseFloat($('#concen_mnoprogresivo').val());
        config.data.datasets[2].data[0] = parseFloat($('#concen_nmoviles').val());

        config_movi.data.datasets[0].data[0] = parseFloat($('#movi_mprogresivo').val());
        config_movi.data.datasets[0].data[1] = parseFloat($('#movi_mnoprogresivo').val());
        config_movi.data.datasets[0].data[2] = parseFloat($('#movi_nmoviles').val());
        //grafica3
        config_mtotal.data.datasets[0].data[0] = parseFloat($('#morfo_normal').val());
        config_mtotal.data.datasets[0].data[1] = parseFloat($('#morfo_anormal').val());
        //grafica4
        config_mclasi.data.datasets[0].data[0] = parseFloat($('#morfo_normal').val());
        config_mclasi.data.datasets[1].data[0] = parseFloat($('#morfo_anormal_cabeza').val()) + parseFloat($('#morfo_micro').val()) + parseFloat($('#morfo_macro').val()) + parseFloat($('#morfo_bicefalo').val());
        config_mclasi.data.datasets[2].data[0] = parseFloat($('#morfo_anormal_pieza').val());
        config_mclasi.data.datasets[3].data[0] = parseFloat($('#morfo_anormal_cola').val()) + parseFloat($('#morfo_bicaudo').val());
        config_mclasi.data.datasets[4].data[0] = parseFloat($('#morfo_inmaduro').val());

        var ctx0 = document.getElementById("canvas").getContext("2d");
        var ctx1 = document.getElementById("canvas_movi").getContext("2d");
        var ctx2 = document.getElementById("canvas_mtotal").getContext("2d");
        var ctx3 = document.getElementById("canvas_mclasi").getContext("2d");
        window.myLine0 = new Chart(ctx0, config);
        window.myLine1 = new Chart(ctx1, config_movi);
        window.myLine2 = new Chart(ctx2, config_mtotal);
        window.myLine3 = new Chart(ctx3, config_mclasi);
        hipospermia();
        oligozoospermia();
        astenozoospermia();
        teratozoospermia();
        necrozoospermia();

        $('#macro_volumen, #concen_exml').on('input',function(e){
            formula1();
            oligozoospermia();
        });
        $('#concen_exml, #movi_mprogresivo, #movi_mnoprogresivo, #movi_nmoviles').on('input',function(e){
            formula3();
            formula2();
            oligozoospermia();
        });
        $('#morfo_normal').on('input',function(e){
            formula4();
            formula5();
            teratozoospermia();
        });
        $('#morfo_anormal_cabeza, #morfo_micro, #morfo_macro, #morfo_bicefalo, #morfo_anormal_pieza, #morfo_anormal_cola, #morfo_bicaudo, #morfo_inmaduro').on('input',function(e){
            formula5();
        });
        $('#macro_volumen').on('input',function(e){
            hipospermia();
            oligozoospermia();
        });
        // validacion de azoospermia
        $('#resul_azo').on('click',function(e){
            $("#morfo_anormal").val("0");
            formula5();
            teratozoospermia();
        });

        $('#movi_mprogresivo_lineal_cantidad, #movi_mprogresivo_no_lineal_cantidad, #movi_mnoprogresivo_cantidad').on('input',function(e){
            formula6();
            formula7();
            formula8();
            $('#movi_mprogresivo').val(parseFloat($('#movi_mprogresivo_lineal').val()) + parseFloat($('#movi_mprogresivo_no_lineal').val()));
            formula2();
            formula3();
            oligozoospermia();
        });
        $('#movi_nmoviles_cantidad').on('input',function(e){
            formula8();
            $('#movi_mprogresivo').val(parseFloat($('#movi_mprogresivo_lineal').val()) + parseFloat($('#movi_mprogresivo_no_lineal').val()));
            formula2();
            formula3();
            oligozoospermia();
        });
        // validacion de funciones
        function formula1(){
            var form1 = $('#macro_volumen').val() * $('#concen_exml').val();
            $('#concen_exeyac').val(form1.toFixed(2));
        }
        function formula2(){
            var demo0 = $('#concen_exml').val() * $('#macro_volumen').val() * $('#movi_mprogresivo').val()/ 100;
            $('#concen_mprogresivo').val(demo0.toFixed(2));
            var demo1 = $('#concen_exml').val() * $('#macro_volumen').val() * $('#movi_mnoprogresivo').val()/ 100;
            $('#concen_mnoprogresivo').val(demo1.toFixed(2));
            var demo2 = $('#concen_exml').val() * $('#macro_volumen').val() * $('#movi_nmoviles').val()/ 100;
            $('#concen_nmoviles').val(demo2.toFixed(2));
            config.data.datasets[0].data[0] = parseFloat($('#concen_mprogresivo').val());
            config.data.labels[0] = "".concat(parseFloat($('#concen_mprogresivo').val()), " E/ml");
            config.data.datasets[1].data[0] = parseFloat($('#concen_mnoprogresivo').val());
            config.data.labels[1] = "".concat(parseFloat($('#concen_mnoprogresivo').val()), " E/ml");
            config.data.datasets[2].data[0] = parseFloat($('#concen_nmoviles').val());
            config.data.labels[2] = "".concat(parseFloat($('#concen_nmoviles').val()), " E/ml");
            window.myLine0.update();
            config_movi.data.datasets[0].data[0] = parseFloat($('#movi_mprogresivo').val());
            config_movi.data.labels[0] = "MP: ".concat(parseFloat($('#movi_mprogresivo').val()), " %");
            config_movi.data.datasets[0].data[1] = parseFloat($('#movi_mnoprogresivo').val());
            config_movi.data.labels[1] = "MNP: ".concat(parseFloat($('#movi_mnoprogresivo').val()), " %");
            config_movi.data.datasets[0].data[2] = parseFloat($('#movi_nmoviles').val());
            config_movi.data.labels[2] = "NM: ".concat(parseFloat($('#movi_nmoviles').val()), " %");
            window.myLine1.update();
        }
        function formula3(){
            $('#movi_tmoviles').val(parseFloat($('#movi_mprogresivo').val()) + parseFloat($('#movi_mnoprogresivo').val()));
            $('#movi_nmoviles').val(100 - $('#movi_tmoviles').val());
            astenozoospermia();
        }
        function formula4(){
            $('#morfo_anormal').val(100 - $('#morfo_normal').val());
            config_mtotal.data.datasets[0].data[0] = parseFloat($('#morfo_normal').val());
            config_mtotal.data.labels[0] = "".concat(parseFloat($('#morfo_normal').val()), " %");
            config_mtotal.data.datasets[0].data[1] = parseFloat($('#morfo_anormal').val());
            config_mtotal.data.labels[1] = "".concat(parseFloat($('#morfo_anormal').val()), " %");
            window.myLine2.update();
        }
        function formula5(){
            config_mclasi.data.datasets[0].data[0] = parseFloat($('#morfo_normal').val());
            config_mclasi.data.labels[0] = "".concat(parseFloat($('#morfo_normal').val()), " %");
            config_mclasi.data.datasets[1].data[0] = parseFloat($('#morfo_anormal_cabeza').val()) + parseFloat($('#morfo_micro').val()) + parseFloat($('#morfo_macro').val()) + parseFloat($('#morfo_bicefalo').val());
            config_mclasi.data.labels[1] = "".concat(parseFloat($('#morfo_anormal_cabeza').val()) + parseFloat($('#morfo_micro').val()) + parseFloat($('#morfo_macro').val()) + parseFloat($('#morfo_bicefalo').val()), " %");
            config_mclasi.data.datasets[2].data[0] = parseFloat($('#morfo_anormal_pieza').val());
            config_mclasi.data.labels[2] = "".concat(parseFloat($('#morfo_anormal_pieza').val()), " %");
            config_mclasi.data.datasets[3].data[0] = parseFloat($('#morfo_anormal_cola').val()) + parseFloat($('#morfo_bicaudo').val());
            config_mclasi.data.labels[3] = "".concat(parseFloat($('#morfo_anormal_cola').val()), " %");
            config_mclasi.data.datasets[4].data[0] = parseFloat($('#morfo_inmaduro').val());
            config_mclasi.data.labels[4] = "".concat(parseFloat($('#morfo_inmaduro').val()), " %");
            window.myLine3.update();
        }
        function formula6() {
            $('#movi_mprogresivo_cantidad').val(parseFloat($('#movi_mprogresivo_lineal_cantidad').val()) + parseFloat($('#movi_mprogresivo_no_lineal_cantidad').val()));
        }
        function formula7() {
            $('#movi_tmoviles_cantidad').val(parseFloat($('#movi_mprogresivo_lineal_cantidad').val()) + parseFloat($('#movi_mprogresivo_no_lineal_cantidad').val()) + parseFloat($('#movi_mnoprogresivo_cantidad').val()));
        }
        function formula8() {
            var total = parseFloat($('#movi_mprogresivo_lineal_cantidad').val()) + parseFloat($('#movi_mprogresivo_no_lineal_cantidad').val()) + parseFloat($('#movi_mnoprogresivo_cantidad').val()) + parseFloat($('#movi_nmoviles_cantidad').val())
            $('#movi_mprogresivo_lineal').val((parseFloat($('#movi_mprogresivo_lineal_cantidad').val()) * 100 / total).toFixed(2))
            $('#movi_mprogresivo_no_lineal').val((parseFloat($('#movi_mprogresivo_no_lineal_cantidad').val()) * 100 / total).toFixed(2))
            $('#movi_mnoprogresivo').val((parseFloat($('#movi_mnoprogresivo_cantidad').val()) * 100 / total).toFixed(2))
            $('#movi_nmoviles').val((parseFloat($('#movi_nmoviles_cantidad').val()) * 100 / total).toFixed(2))
        }
        //
        function hipospermia() {
            if ($('#macro_volumen').val() < 1.5) {
                $('#resul_hipos').attr('checked', true);
            } else {
                $('#resul_hipos').attr('checked', false);
            }
        }
        function oligozoospermia() {
            if ($('#concen_exml').val()<15 || $('concen_exeyac').val()<39 ) {
                $('#resul_oligo').attr('checked', true);
            } else {
                $('#resul_oligo').attr('checked', false);
            }
        }
        function astenozoospermia() {
            if ($('#movi_tmoviles').val()<40 || $('movi_mprogresivo').val()<32 ) {
                $('#resul_aste').attr('checked', true);
            } else {
                $('#resul_aste').attr('checked', false);
            }
        }
        function teratozoospermia() {
            if ($('#morfo_normal').val() < 4) {
                $('#resul_tera').attr('checked', true);
            } else {
                $('#resul_tera').attr('checked', false);
            }
        }
        function necrozoospermia() {
            if ($('#movi_tvitalidad').val() < 58) {
                $('#resul_necro').attr('checked', true);
            } else {
                $('#resul_necro').attr('checked', false);
            }
        }
    </script>
    <script src="js/popper.min.js" crossorigin="anonymous"></script>
    <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="js/jquery.timepicker.min.js"></script>
</body>
</html>