<!DOCTYPE HTML>
<html>
<head>
    <?php
        include 'seguridad_login.php'
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
    <script type="text/javascript">
        function PrintElem(elem) {
            var data = $(elem).html();
            var mywindow = window.open('', 'Imprimir', 'height=600,width=800');
            mywindow.document.write('<html><head><title>Imprimir</title>');
            mywindow.document.write('<style> @page {margin: 0px 0px 0px 5px;} table {border-collapse: collapse;font-size:10px;} .table-stripe td {border: 1px solid black;} .tablamas2 td {border: 1px solid white;} .mas2 {display: block !important;} .noVer, .ui-table-cell-label {display: none;} a:link {pointer-events: none; cursor: default;}</style>');
            mywindow.document.write("</head><body><p style='align: center'>Reporte Fecundación In Vitro</p>");
            mywindow.document.write(data);
            mywindow.document.write('<script type="text/javascript">window.print();<' + '/script>');
            mywindow.document.write('</body></html>');
            return true;
        }
    </script>
    <style>
        .scroll_h {
            overflow: auto;
        }
        .mayuscula {
            text-transform: uppercase;
            font-size: small;
        }
        .enlinea div {
            display: inline-block;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class='container'>
        <div data-role="header">
            <a href="javascript:window.close();">Cerrar</a>
            <?php
                if (isset($_GET["rep"]) && !empty($_GET["rep"])) {
                    switch ($_GET["rep"]) {
                        case 'fiv': print("<h2>Fecundación In Vitro (FIV-ICSI)</h2>"); break;
                        case 'icsi': print("<h2Inyección intracitoplasmática de espermatozoides (ICSI)</h2>"); break;
                        case 'od': print("<h2>Donación de Óvulos (OD Fresco)</h2>"); break;
                        case 'don': print("<h2>Donación de Óvulos (DESCONGELACIÓN OVULOS DONADOS)</h2>"); break;
                        case 'ted': print("<h2>Transferencia de Embriones criopreservados</h2>"); break;
                        case 'dgp': print("<h2>Diagnóstico Genético Preimplantacional (DGP)</h2>"); break;
                        case 'crio': print("<h2>Vitrificación de Óvulos</h2>"); break;
                        case 'iiu': print("<h2>Inseminación Artificial</h2>"); break;
                        case 'emb': print("<h2>Embriodonación</h2>"); break;
                        default: break;
                    }
                }
            ?>
        </div>
        <!-- <a href="javascript:PrintElem('#imprime')" data-role="button" data-mini="true" data-inline="true" rel="external">Imprimir</a> -->
    <?php
    if ($_SESSION['role'] == "9") {
        $between = $ini = $fin = "";
        if (isset($_GET) && !empty($_GET)) {
            if ( isset($_GET["ini"]) && !empty($_GET["ini"]) && isset($_GET["fin"]) && !empty($_GET["fin"]) ) {
                $ini = $_GET['ini'];
                $fin = $_GET['fin'];
                $between = " and CAST(lab_aspira.fec as date) between '$ini' and '$fin'";
            }
            if (isset($_GET["med"]) && !empty($_GET["med"])) {
                $med = $_GET['med'];
                $between.= " and hc_reprod.med = '$med'";
            }
            if (isset($_GET["embins"]) && !empty($_GET["embins"])) {
                $embins = $_GET['embins'];
                $between.= " and lab_aspira.emb0 = $embins";
            }
            if (isset($_GET["ovo"]) && !empty($_GET["ovo"])) {
                $ovo = $_GET['ovo'];
                $between.= " and unaccent(lab_aspira.o_ovo) ilike '%$ovo%'";
            }
            if (isset($_GET["inc"]) && !empty($_GET["inc"])) {
                $inc = $_GET['inc'];
                $between.= " and lab_aspira.inc1 = $inc";
            }
        }
        $item = 1;
        $ambos =  $ins1 = $ins2 = 0;
        $fecha_inicio_band = true;
        $blascalaa = $blascalab = $blascalac = $blascalad = $blascalba = $blascalbb = $blascalbc = $blascalbd = $blascalca = $blascalcb = $blascalcc = $blascalcd = $blascalda = $blascaldb = $blascaldc = $blascaldd = 0;

        require "repo_blas_calidad.php";

        // print("<div><p>Total: ".$rPaci->rowCount()."</p></div>");
        $paci = $rPaci->fetch(PDO::FETCH_ASSOC);
        if (isset($paci) && !empty($paci)) {
            $blascalaa=$paci['blascalaa'];
            $blascalab=$paci['blascalab'];
            $blascalac=$paci['blascalac'];
            $blascalad=$paci['blascalad'];
            $blascalba=$paci['blascalba'];
            $blascalbb=$paci['blascalbb'];
            $blascalbc=$paci['blascalbc'];
            $blascalbd=$paci['blascalbd'];
            $blascalca=$paci['blascalca'];
            $blascalcb=$paci['blascalcb'];
            $blascalcc=$paci['blascalcc'];
            $blascalcd=$paci['blascalcd'];
            $blascalda=$paci['blascalda'];
            $blascaldb=$paci['blascaldb'];
            $blascaldc=$paci['blascaldc'];
            $blascaldd=$paci['blascaldd'];
        }
        print("
        <div class='card mb-3'>
            <div class='card-body mx-auto'>
                <table class='table table-responsive table-bordered align-middle'>
                    <thead class='thead-dark'>
                        <tr>
                            <th class='text-center'></th>
                            <th class='text-center'></th>
                            <th class='text-center' colspan='4'>Trofoectodermo</th>
                        </tr>
                    </thead>
                    <tr>
                        <th class='text-center'></th>
                        <th class='text-center'></th>
                        <th class='text-center'>A</th>
                        <th class='text-center'>B</th>
                        <th class='text-center'>C</th>
                        <th class='text-center'>D</th>
                    </tr>
                    <tbody>
                        <tr>
                            <th rowspan='5' class='text-center'>M<br>C<br>I</th>
                            <th>A</th>
                            <td>$blascalaa</td>
                            <td>$blascalab</td>
                            <td>$blascalac</td>
                            <td>$blascalad</td>
                        </tr>
                        <tr>
                            <th>B</th>
                            <td>$blascalba</td>
                            <td>$blascalbb</td>
                            <td>$blascalbc</td>
                            <td>$blascalbd</td>
                        </tr>
                        <tr>
                            <th>C</th>
                            <td>$blascalca</td>
                            <td>$blascalcb</td>
                            <td>$blascalcc</td>
                            <td>$blascalcd</td>
                        </tr>
                        <tr>
                            <th>D</th>
                            <td>$blascalda</td>
                            <td>$blascaldb</td>
                            <td>$blascaldc</td>
                            <td>$blascaldd</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>");
        while ($paci = $rPaci->fetch(PDO::FETCH_ASSOC)) {
            break;
            if ($fecha_inicio_band and isset($paci["fec"])) {
                $fecha_inicio = $paci["fec"];
                $fecha_inicio_band = false;
            }
            if ($paci['p_fiv'] == 1 and $paci['p_icsi'] == 1) {
                $ins1++;
            } else if ($paci['p_fiv'] == 1) {
                $ins1++;
            } else {
               $ins2++;
            }
            print("<tr>
                <td class='text-center'>".$item++."</td>
                <td>
                    <a target='_blank' href='le_aspi".$paci['dias'].".php?id=".$paci['pro']."'>".$paci["tip"]."-".$paci['pro']."-".$paci['vec']."</a><br>
                    <a target='_blank' href='info_r.php?a=".$paci['pro']."&b=".$paci['dni']."&c=".$paci['p_dni']."'>Informe</a>
                </td>
                <td>".$paci["dni"]." - ".$paci["ape"]." ".$paci["nom"]."</td>
                <td>".$paci["p_dni"]." - ".$paci["p_ape"]." ".$paci["p_nom"]."</td>");
            print("
                <td>".$paci["med"]."</td>
                <td>".substr($paci["fec"], 0, 10)."</td></tr>");
        }
        print("
            </tbody>
        </table>");
    ?>
        <div class="ui-content" role="main" id="imprime">
            <div style="float:right">
                <p><b>Fecha y Hora de Reporte:</b>
                    <?php
                        date_default_timezone_set('America/Lima');
                        print(date("Y-m-d H:m:s"));
                    ?>
                </p>
            </div>
        </div>
    </div>
<?php } ?>
    <script src="js/jquery-3.2.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="js/popper.min.js" crossorigin="anonymous"></script>
    <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>
</html>