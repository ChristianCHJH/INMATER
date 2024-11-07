<!DOCTYPE HTML>
<html>
<head>
<?php
     include 'seguridad_login.php';
    require("config/environment.php");
    require("_database/database_log.php");
    require("_database/database.php");
    require("_database/database_farmacia.php"); ?>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/chosen.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
    <link rel="stylesheet" href="css/bootstrap.v4/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="css/global.css" crossorigin="anonymous">

    <style>
        canvas {
        -moz-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
        }
    </style>
</head>

<body>
    <div class="loader">
        <img src="_images/load.gif" alt="">
    </div>

    <div class="container">
        <nav aria-label="breadcrumb">
            <a class="breadcrumb" href="lista_and.php">
                <img src="_libraries/open-iconic/svg/x.svg" height="18" width="18" alt="icon name">
            </a>

            <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="lista_and.php">Inicio</a></li>
                <li class="breadcrumb-item"><a href="lista_and.php">Andrología</a></li>
                <li class="breadcrumb-item active" aria-current="page">Reporte Lenshooke</li>
            </ol>
        </nav>

        <?php
        // iniciar variables
        $ini = $fin = $protocolo = $between = $grafica_tipo = "";
        //
        if (isset($_POST) && !empty($_POST)) {
            if (isset($_POST["date_ini"]) && !empty($_POST["date_ini"]) && isset($_POST["date_fin"]) && !empty($_POST["date_fin"])) {
                $ini = $_POST['date_ini'];
                $fin = $_POST['date_fin'];
                $between .= " and lae.info_fmuestra between '$ini' and '$fin'";
            } else {
                /* $ini = $fin = date('Y-m-d'); */
                $ini = $fin = "";
            }
        } else {
            $ini = date('Y-01-01');
            $fin = date('Y-01-01');
            $between .= " and lae.info_fmuestra between '$ini' and '$fin'";
        }

        // si - no
        $stmt = $db->prepare("SELECT id, nombre FROM si_no;");
        $stmt->execute();
        $si_no = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        array_unshift($si_no, "-");
        // apariencias
        $stmt = $db->prepare("SELECT id, nombre FROM apariencia_esperma;");
        $stmt->execute();
        $apariencias = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        array_unshift($apariencias, "-");
        // viscocidades
        $stmt = $db->prepare("SELECT id, nombre FROM viscosidad_esperma;");
        $stmt->execute();
        $viscocidades = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        array_unshift($viscocidades, "-");
        // liquefacciones
        $stmt = $db->prepare("SELECT id, nombre FROM licuefaccion_esperma;");
        $stmt->execute();
        $lique_array = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        array_unshift($lique_array, "-");
        // aglutinaciones
        $stmt = $db->prepare("SELECT id, nombre from man_aglutinacion;");
        $stmt->execute();
        $aglu_array = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        array_unshift($aglu_array, "-");

        // consulta general
        $stmt = $db->prepare("SELECT
            hp.p_dni dni, concat(upper(hp.p_ape) , ' ', upper(hp.p_nom)) paciente
            , hp.p_fnac fecha_nacimiento
            , year(now()) - year(hp.p_fnac) - (date_format(now(), '%m%d') < date_format(hp.p_fnac, '%m%d')) edad_actual
            , case when hp.p_fnac = '1899-12-30' then '-' else  year(lae.info_fmuestra) - year(hp.p_fnac) - (date_format(lae.info_fmuestra, '%m%d') < date_format(hp.p_fnac, '%m%d')) end edad_del_momento
            , lae.abstinencia
            , lae.info_fmuestra, lae.info_medicacion
            , lae.macro_apariencia, lae.macro_viscosidad, lae.macro_liquefaccion, lae.macro_aglutinacion, lae.macro_ph, coalesce(lae.macro_volumen, 0) macro_volumen
            , coalesce(lae.concen_exml, 0) concen_exml, lae.concen_credon, lae.concen_exeyac
            , lae.movi_mprogresivo, lae.movi_mnoprogresivo
            , lae.movi_mprogresivo_lineal_cantidad, lae.movi_mprogresivo_no_lineal_cantidad, lae.movi_mnoprogresivo_cantidad, lae.movi_tvitalidad
            , lae.cine_vap cinetica_vap, lae.cine_vsl cinetica_vsl, lae.cine_vcl cinetica_vcl, lae.cine_lin cinetica_lin, lae.cine_str cinetica_str, lae.cine_wob cinetica_wob, lae.cine_alh cinetica_alh, lae.cine_bcf cinetica_bcf
            , lae.morfo_normal
            , lae.normal_largocabeza_porcentaje, lae.normal_ancho_porcentaje, lae.normal_perimetro_porcentaje, lae.normal_area_porcentaje, lae.normal_largocola_porcentaje
            , lae.resul_cripto, lae.resul_azo
            from lab_andro_esp lae
            inner join hc_pareja hp on hp.p_dni = lae.p_dni
            where lae.cine_vap is not null$between
            order by lae.info_fmuestra desc;
        ");

        $stmt->execute();
        $rows = $stmt->fetchAll(); ?>

        <div class="card mb-3">
            <input type="hidden" name="conf">
            <h5 class="card-header"><b>Filtros</b></h5>

            <div class="card-body">
                <form action="" method="post" data-ajax="false" name="form2">
                    <div class="row pb-2">
                        <div class="input-group input-group-sm col-12 col-sm-12 col-md-12 col-lg-6">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Mostrar Desde</span>
                            </div>
                            <input class="form-control form-control-sm mostrar" id="date_ini" name="date_ini" type="date" value="<?php print($ini); ?>">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Hasta</span>
                            </div>
                            <input class="form-control form-control-sm mostrar" id="date_fin" name="date_fin" type="date" value="<?php print($fin); ?>">
                        </div>

                        <div class="col-12 col-sm-12 col-md-12 col-lg-2 input-group-sm">
                            <input class="form-control btn btn-danger btn-sm" type="Submit" name="agregar" value="Buscar"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mb-3">
            <h5 class="card-header" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">
            <?php
            print("<small><b>Fecha y Hora de Reporte: </b>".date("Y-m-d H:i:s")."
                <b>, Total Registros: </b>".count($rows).'
                <b>, Descargar: </b>
                <form method="post" action="_operaciones/repo-lenshooke.php" target="_blank">
                    <input type="hidden" name="tipo" value="descargar_base">
                    <input type="hidden" name="pro_descargar" id="pro_descargar">
                    <input type="hidden" name="ini" id="ini" value="'.$ini.'">
                    <input type="hidden" name="fin" id="fin" value="'.$fin.'">
                    <input type="hidden" name="columnas" id="columnas">
                    <a href=\'javascript:void(0)\' onclick="$(\'#columnas\').val($(\'table input:checkbox:checked\').map(function(){return $(this).val();}).get()); this.closest(\'form\').submit(); return false;" style=\"font-size: 14px;\" class=\"font-italic\">
                        <img src="_images/excel.png" height="18" width="18" alt="icon name">
                    </a>
                </form></small>'); ?>
            </h5>

            <table width="100%" class='table table-sm table-responsive table-bordered align-middle header-fixed' style='height: 50vh; table-layout: fixed;' data-filter="true" data-input="#filtro" id="repo_fragmentacion">
                <thead class="thead-dark">
                    <tr>
                        <th colspan="4" class="text-center align-bottom">Datos Generales</th>
                        <th colspan="2" class="text-center align-bottom"></th>
                        <th colspan="6" class="text-center align-bottom">Análisis Macroscópico</th>
                        <th colspan="3" class="text-center align-bottom">Concentración</th>
                        <th colspan="7" class="text-center align-bottom">Movilidad y Vitalidad</th>
                        <th colspan="8" class="text-center align-bottom">Cinética espermática</th>
                        <th colspan="2" class="text-center align-bottom">Morfología</th>
                        <th colspan="5" class="text-center align-bottom">Anomalías</th>
                        <th rowspan="2" class="text-center align-bottom">Diagnóstico</th>
                    </tr>

                    <tr>
                        <!-- datos generales -->
                        <th class="text-center align-bottom">ID Paciente</th>
                        <th class="text-center align-bottom" style="min-width: 90px;">Edad(años)</th>
                        <th class="align-bottom" style="min-width: 300px;">Paciente</th>
                        <th class="text-center align-bottom">Dias Abstinencia</th>
                        <!--  -->
                        <th class="text-center align-bottom" style="min-width: 100px;">F. de obtención</th>
                        <th class="text-center align-bottom">Medicación</th>
                        <!-- analisis macroscopico -->
                        <th class="text-center align-bottom">Apariencia</th>
                        <th class="text-center align-bottom">Viscocidad</th>
                        <th class="text-center align-bottom">Licuefacción</th>
                        <th class="text-center align-bottom">Aglutinación</th>
                        <th class="text-center align-bottom" style="min-width: 50px;">pH</th>
                        <th class="text-center align-bottom" style="min-width: 50px;">Volumen (mL)</th>
                        <!-- concentracion -->
                        <th class="text-center align-bottom">Espermatozoides por ml (millones)</th>
                        <th class="text-center align-bottom">Células redondas</th>
                        <th class="text-center align-bottom">Espermatozoides por eyaculado (millones)</th>
                        <!-- movilidad y vitalidad -->
                        <th class="text-center align-bottom">Total móviles (P + NP) %</th>
                        <th class="text-center align-bottom">Móvil Progresivo (P) %</th>
                        <th class="text-center align-bottom">M.P. Lineal (VAP >= 25µm/s) %</th>
                        <th class="text-center align-bottom">M.P. No Lineal (5µm/s <= VAP < 25µm/s) %</th>
                        <th class="text-center align-bottom">Móvil No progresivo (NP) %</th>
                        <th class="text-center align-bottom">No móviles %</th>
                        <th class="text-center align-bottom">Test de Vitalidad %</th>
                        <!-- cinetica espermatica -->
                        <th class="text-center align-bottom" style="min-width: 50px;">VAP</th>
                        <th class="text-center align-bottom" style="min-width: 50px;">VSL</th>
                        <th class="text-center align-bottom" style="min-width: 50px;">VCL</th>
                        <th class="text-center align-bottom" style="min-width: 50px;">LIN</th>
                        <th class="text-center align-bottom" style="min-width: 50px;">STR</th>
                        <th class="text-center align-bottom" style="min-width: 50px;">WOB</th>
                        <th class="text-center align-bottom" style="min-width: 50px;">ALH</th>
                        <th class="text-center align-bottom" style="min-width: 50px;">BCF</th>
                        <!-- morfologias -->
                        <th class="text-center align-bottom">Normales %</th>
                        <th class="text-center align-bottom">Anormales %</th>
                        <!-- anomalias -->
                        <th class="text-center align-bottom">Largo de Cabeza %</th>
                        <th class="text-center align-bottom">Ancho de Cabeza %</th>
                        <th class="text-center align-bottom">Perímetro de Cabeza %</th>
                        <th class="text-center align-bottom">Área de Cabeza %</th>
                        <th class="text-center align-bottom">Largo de la Cola %</th>
                    </tr>

                    <tr>
                        <!-- datos generales -->
                        <th class="text-center align-bottom"><input type="checkbox" value="dni" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="edad" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="paciente" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="abstinencia" checked></th>
                        <!-- -->
                        <th class="text-center align-bottom"><input type="checkbox" value="info_fmuestra" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="info_medicacion" checked></th>
                        <!-- analisis macroscopico -->
                        <th class="text-center align-bottom"><input type="checkbox" value="macro_apariencia" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="macro_viscosidad" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="macro_liquefaccion" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="macro_aglutinacion" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="macro_ph" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="macro_volumen" checked></th>
                        <!-- concentracion -->
                        <th class="text-center align-bottom"><input type="checkbox" value="concen_exml" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="concen_credon" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="concen_exeyac" checked></th>
                        <!-- movilidad y vitalidad -->
                        <th class="text-center align-bottom"><input type="checkbox" value="movi_total_moviles" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="movi_mprogresivo" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="movi_mprogresivo_lineal_cantidad" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="movi_mprogresivo_no_lineal_cantidad" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="movi_mnoprogresivo_cantidad" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="movi_no_moviles" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="movi_tvitalidad" checked></th>
                        <!-- cinetica espermatica -->
                        <th class="text-center align-bottom"><input type="checkbox" value="cinetica_vap" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="cinetica_vsl" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="cinetica_vcl" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="cinetica_lin" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="cinetica_str" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="cinetica_wob" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="cinetica_alh" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="cinetica_bcf" checked></th>
                        <!-- morfologias -->
                        <th class="text-center align-bottom"><input type="checkbox" value="morfo_normal" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="morfo_anormal" checked></th>
                        <!-- anomalias -->
                        <th class="text-center align-bottom"><input type="checkbox" value="normal_largocabeza_porcentaje" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="normal_ancho_porcentaje" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="normal_perimetro_porcentaje" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="normal_area_porcentaje" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="normal_largocola_porcentaje" checked></th>
                        <!-- diagnostico -->
                        <th class="text-center align-bottom"><input type="checkbox" value="diagnostico" checked></th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $i=1;
                    foreach ($rows as $item) {
                        $diagnostico = '';
                        //diagnostico
                        if ($item['resul_cripto'] == 1) {
                            $diagnostico="CRIPTOzoospermia";
                        } else if ($item['resul_azo'] == 1) {
                            $diagnostico="Azoospermia";
                        } else {
                            // Hipospermia
                            if ($item['macro_volumen'] < 1.5) {
                                $diagnostico.="HIPO";
                            }
                            // Oligozoospermia
                            if ($item['concen_exml'] < 15 || ((!!$item['macro_volumen'] ? $item['macro_volumen'] : 0) * (!!$item['concen_exml'] ? $item['concen_exml'] : 0)) < 39) {
                                $diagnostico.="OLIGO";
                            }
                            // Astenozoospermia
                            if (($item['movi_mprogresivo']+$item['movi_mnoprogresivo']) < 40 || $item['movi_mprogresivo'] < 32) {
                                $diagnostico.="ASTENO";
                            }
                            // Teratozoospermia
                            if ($item['morfo_normal'] < 4) {
                                $diagnostico.="TERATO";
                            }
                            // Necrozoospermia
                            if ($item['movi_tvitalidad'] < 58) {
                                $diagnostico.="NECRO";
                            }
                            if (!empty($diagnostico)) {
                                $diagnostico.="zoospermia";
                                if (strpos($diagnostico, "OLIGO")!==false || strpos($diagnostico, "HIPO")!==false) {
                                    $diagnostico.=", se sugiere evaluación ecográfica (Ecografia testicular)";
                                }
                            } else {
                                $diagnostico="Normozoospermia";
                            }
                        }

                        //
                        print('<tr>
                            <!-- datos generales -->
                            <td class="text-center">' . $item["dni"] . '</td>
                            <td class="text-center">' . $item["edad_del_momento"] . '</td>
                            <td>' . $item["paciente"] . '</td>
                            <td class="text-center">' . $item["abstinencia"] . '</td>
                            <!-- -->
                            <td class="text-center">' . $item["info_fmuestra"] . '</td>
                            <td class="text-center">' . (isset($item["info_medicacion"]) ? $si_no[$item["info_medicacion"]] : '-') . '</td>
                            <!-- analisis macroscopico -->
                            <td class="text-center">' . (isset($item["macro_apariencia"]) ? $apariencias[$item["macro_apariencia"]] : '-') . '</td>
                            <td class="text-center">' . (isset($item["macro_viscosidad"]) ? $viscocidades[$item["macro_viscosidad"]] : '-') . '</td>
                            <td class="text-center">' . (isset($item["macro_liquefaccion"]) ? $lique_array[$item["macro_liquefaccion"]] : '-') . '</td>
                            <td class="text-center">' . (isset($item["macro_aglutinacion"]) ? $aglu_array[$item["macro_aglutinacion"]] : '-') . '</td>
                            <td class="text-center">' . $item["macro_ph"] . '</td>
                            <td class="text-center">' . $item["macro_volumen"] . '</td>
                            <!-- concentracion -->
                            <td class="text-center">' . $item["concen_exml"] . '</td>
                            <td class="text-center">' . $item["concen_credon"] . '</td>
                            <td class="text-center">' . ((is_numeric($item['concen_exml']) and is_numeric($item['macro_volumen'])) ? number_format($item['concen_exml'] * $item['macro_volumen'], 2, '.', '') : '0.00') . '</td>
                            <!-- movilidad y vitalidad -->
                            <td class="text-center">' . ($item["movi_mprogresivo"] + $item["movi_mnoprogresivo"]) . '</td>
                            <td class="text-center">' . ($item["movi_mprogresivo"]) . '</td>
                            <td class="text-center">' . $item["movi_mprogresivo_lineal_cantidad"] . '</td>
                            <td class="text-center">' . $item["movi_mprogresivo_no_lineal_cantidad"] . '</td>
                            <td class="text-center">' . $item["movi_mnoprogresivo_cantidad"] . '</td>
                            <td class="text-center">' . (100 - $item["movi_mprogresivo"] - $item["movi_mnoprogresivo"]) . '</td>
                            <td class="text-center">' . $item["movi_tvitalidad"] . '</td>
                            <!-- cinetica espermatica -->
                            <td class="text-center">' . $item["cinetica_vap"] . '</td>
                            <td class="text-center">' . $item["cinetica_vsl"] . '</td>
                            <td class="text-center">' . $item["cinetica_vcl"] . '</td>
                            <td class="text-center">' . $item["cinetica_lin"] . '</td>
                            <td class="text-center">' . $item["cinetica_str"] . '</td>
                            <td class="text-center">' . $item["cinetica_wob"] . '</td>
                            <td class="text-center">' . $item["cinetica_alh"] . '</td>
                            <td class="text-center">' . $item["cinetica_bcf"] . '</td>
                            <!-- morfologias -->
                            <td class="text-center">' . $item["morfo_normal"] . '</td>
                            <td class="text-center">' . (100 - $item["morfo_normal"]) . '</td>
                            <!-- anomalias -->
                            <td class="text-center">' . $item["normal_largocabeza_porcentaje"] . '</td>
                            <td class="text-center">' . $item["normal_ancho_porcentaje"] . '</td>
                            <td class="text-center">' . $item["normal_perimetro_porcentaje"] . '</td>
                            <td class="text-center">' . $item["normal_area_porcentaje"] . '</td>
                            <td class="text-center">' . $item["normal_largocola_porcentaje"] . '</td>
                            <td class="text-center">' . $diagnostico . '</td>
                        </tr>');
                    } ?>
                </tbody>
            </table>
        </div>
  </div>

    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.v4/bootstrap.min.js" crossorigin="anonymous"></script>

    <script>
        jQuery(window).load(function (event) {
            jQuery('.loader').fadeOut(1000);
        });

        $(document).ready(function () {
            $("#pro_descargar").val($("#protocolo").val());

            $(".mostrar").change(function () {
                $("#protocolo").val("");
                $("#ini").val($("#date_ini").val());
                $("#fin").val($("#date_fin").val());
            });

            $("#protocolo").change(function () {
                $(".mostrar").val("");
            });

            $(document).on('input paste', '#protocolo', function(e){
                $("#pro_descargar").val($("#protocolo").val());
            });
        });
    </script>
</body>
</html>
