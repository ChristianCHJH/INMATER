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
        var tableToExcel = (function () {
            var uri = 'data:application/vnd.ms-excel;base64,'
                ,
                template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
                , base64 = function (s) {
                    return window.btoa(unescape(encodeURIComponent(s)))
                }
                , format = function (s, c) {
                    return s.replace(/{(\w+)}/g, function (m, p) {
                        return c[p];
                    })
                }
            return function (table, visita) {
                if (!table.nodeType) table = document.getElementById(table)
                var ctx = {worksheet: 'reporte_' + visita || 'reporte', table: table.innerHTML}
                window.location.href = uri + base64(format(template, ctx))
            }
        })();
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
                        case 'criopac': print("<h2>Vitrificación de Óvulos de Paciente</h2>"); break;
                        case 'criodon': print("<h2>Vitrificación de Óvulos de Donante</h2>"); break;
						case 'iiu': print("<h2>Inseminación Artificial</h2>"); break;
						case 'emb': print("<h2>Embriodonación</h2>"); break;
			        	default: break;
			        }
		        }
			?>
        </div>
        <a href="#" onclick="tableToExcel('repo_model_01', 'crio_paciente')" class="ui-btn ui-mini ui-btn-inline">Exportar</a>
        <!-- <a href="javascript:PrintElem('#imprime')" data-role="button" data-mini="true" data-inline="true" rel="external">Imprimir</a> -->
<?php
    if ($_SESSION['role'] == "9") {
        $between = $ini = $fin = $edesde = $ehasta = "";
        if (isset($_GET) && !empty($_GET)) {
            if ( isset($_GET["edesde"]) && !empty($_GET["edesde"]) && isset($_GET["ehasta"]) && !empty($_GET["ehasta"]) ) {
                $edesde = $_GET['edesde'];
                $ehasta = $_GET['ehasta'];
                $between.=" and datediff(lab_aspira.fec, hc_paciente.fnac) between $edesde and $ehasta";
            }
            if ( isset($_GET["ini"]) && !empty($_GET["ini"]) && isset($_GET["fin"]) && !empty($_GET["fin"]) ) {
                $ini = $_GET['ini'];
                $fin = $_GET['fin'];
                $between.=" and CAST(lab_aspira.fec as date) between '$ini' and '$fin'";
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
                $between.= " and lab_aspira.o_ovo ilike '%$ovo%'";
            }
            if (isset($_GET["tipa"]) && !empty($_GET["tipa"])) {
                $tipa = $_GET['tipa'];
                $between.= " and lab_aspira.tip = '$tipa'";
            }
        }
        $item = 1;
        $ambos =  $ins1 = $ins2 = 0;
        $con1_med=$con2_med=$con3_med=$con4_med=$con5_med="";
        $Tcon1_med=$Tcon2_med=$Tcon3_med=$Tcon4_med=$Tcon5_med=0;
        $fecha_inicio_band = true;
        if (isset($_GET["rep"]) && !empty($_GET["rep"])) {
	        switch ($_GET["rep"]) {
	        	case 'fiv': require "repo_fiv.php"; break;
	        	case 'icsi': require "repo_icsi.php"; break;
	        	case 'od': require "repo_recep_od.php"; break;
				case 'don': require "repo_recep_don.php"; break;
				case 'ted': require "repo_ted.php"; break;
				case 'dgp': require "repo_dgp.php"; break;
				case 'crio': require "repo_crio.php"; break;
                case 'criopac': require "repo_crio_pac.php"; break;
                case 'criodon': require "repo_crio_don.php"; break;
				case 'iiu': require "repo_iiu.php"; break;
				case 'emb': require "repo_emb.php"; break;
	        	default: break;
	        }
        }
        $rPaci->execute();
        print("<div><p>Total: ".$rPaci->rowCount()."</p></div>");
        print("
        <table class='table table-responsive table-bordered align-middle' id='repo_model_01'>
            <thead class='thead-dark'>
                <tr>
                    <th class='text-center'>Item</th>
                    <th class='text-center'>Protocolo</th>
                    <th class='text-center'>DNI/ CE</th>
                    <th class='text-center'>Paciente</th>
                    <th class='text-center'>Edad</th>
                    <!--<th class='text-center'>DNI/ Pareja</th>-->
                    <th class='text-center'>Medicamento 1</th>
                    <th class='text-center'>Total</th>
                    <th class='text-center'>Medicamento 2</th>
                    <th class='text-center'>Total</th>
                    <th class='text-center'>Medicamento 3</th>
                    <th class='text-center'>Total</th>
                    <th class='text-center'>Medicamento 4</th>
                    <th class='text-center'>Total</th>
                    <th class='text-center'>Medicamento 5</th>
                    <th class='text-center'>Total</th>
                    <th class='text-center'>Óvulos Aspirados</th>
                    <th class='text-center'>Embriones<br>Criopreservados</th>
                    <!--<th>Procedimiento</th>-->
                    <th class='text-center'>Médico</th>
                    <th class='text-center'>Fecha</th>
                </tr>
            </thead>
            <tbody>
        ");
        while ($paci = $rPaci->fetch(PDO::FETCH_ASSOC)) {
            $con1_med = explode("|", $paci['con1_med']);
            $con2_med = explode("|", $paci['con2_med']);
            $con3_med = explode("|", $paci['con3_med']);
            $con4_med = explode("|", $paci['con4_med']);
            $con5_med = explode("|", $paci['con5_med']);
            $Tcon1_med = (int)$con1_med[1] + (int)$con1_med[2] + (int)$con1_med[3] + (int)$con1_med[4] + (int)$con1_med[5] + (int)$con1_med[6] + (int)$con1_med[7] + (int)$con1_med[8] + (int)$con1_med[9] + (int)$con1_med[10] + (int)$con1_med[11] + (int)$con1_med[12] + (int)$con1_med[13] + (int)$con1_med[14] + (int)$con1_med[15] + (int)$con1_med[16] + (int)$con1_med[17] + (int)$con1_med[18] + (int)$con1_med[19] + (int)$con1_med[20] + (int)$con1_med[21] + (int)$con1_med[22] + (int)$con1_med[23] + (int)$con1_med[24] + (int)$con1_med[25] + (int)$con1_med[26] + (int)$con1_med[27] + (int)$con1_med[28] + (int)$con1_med[29] + (int)$con1_med[30];
            $Tcon2_med = (int)$con2_med[1] + (int)$con2_med[2] + (int)$con2_med[3] + (int)$con2_med[4] + (int)$con2_med[5] + (int)$con2_med[6] + (int)$con2_med[7] + (int)$con2_med[8] + (int)$con2_med[9] + (int)$con2_med[10] + (int)$con2_med[11] + (int)$con2_med[12] + (int)$con2_med[13] + (int)$con2_med[14] + (int)$con2_med[15] + (int)$con2_med[16] + (int)$con2_med[17] + (int)$con2_med[18] + (int)$con2_med[19] + (int)$con2_med[20] + (int)$con2_med[21] + (int)$con2_med[22] + (int)$con2_med[23] + (int)$con2_med[24] + (int)$con2_med[25] + (int)$con2_med[26] + (int)$con2_med[27] + (int)$con2_med[28] + (int)$con2_med[29] + (int)$con2_med[30];
            $Tcon3_med = (int)$con3_med[1] + (int)$con3_med[2] + (int)$con3_med[3] + (int)$con3_med[4] + (int)$con3_med[5] + (int)$con3_med[6] + (int)$con3_med[7] + (int)$con3_med[8] + (int)$con3_med[9] + (int)$con3_med[10] + (int)$con3_med[11] + (int)$con3_med[12] + (int)$con3_med[13] + (int)$con3_med[14] + (int)$con3_med[15] + (int)$con3_med[16] + (int)$con3_med[17] + (int)$con3_med[18] + (int)$con3_med[19] + (int)$con3_med[20] + (int)$con3_med[21] + (int)$con3_med[22] + (int)$con3_med[23] + (int)$con3_med[24] + (int)$con3_med[25] + (int)$con3_med[26] + (int)$con3_med[27] + (int)$con3_med[28] + (int)$con3_med[29] + (int)$con3_med[30];
            $Tcon4_med = (int)$con4_med[1] + (int)$con4_med[2] + (int)$con4_med[3] + (int)$con4_med[4] + (int)$con4_med[5] + (int)$con4_med[6] + (int)$con4_med[7] + (int)$con4_med[8] + (int)$con4_med[9] + (int)$con4_med[10] + (int)$con4_med[11] + (int)$con4_med[12] + (int)$con4_med[13] + (int)$con4_med[14] + (int)$con4_med[15] + (int)$con4_med[16] + (int)$con4_med[17] + (int)$con4_med[18] + (int)$con4_med[19] + (int)$con4_med[20] + (int)$con4_med[21] + (int)$con4_med[22] + (int)$con4_med[23] + (int)$con4_med[24] + (int)$con4_med[25] + (int)$con4_med[26] + (int)$con4_med[27] + (int)$con4_med[28] + (int)$con4_med[29] + (int)$con4_med[30];
            $Tcon5_med = (int)$con5_med[1] + (int)$con5_med[2] + (int)$con5_med[3] + (int)$con5_med[4] + (int)$con5_med[5] + (int)$con5_med[6] + (int)$con5_med[7] + (int)$con5_med[8] + (int)$con5_med[9] + (int)$con5_med[10] + (int)$con5_med[11] + (int)$con5_med[12] + (int)$con5_med[13] + (int)$con5_med[14] + (int)$con5_med[15] + (int)$con5_med[16] + (int)$con5_med[17] + (int)$con5_med[18] + (int)$con5_med[19] + (int)$con5_med[20] + (int)$con5_med[21] + (int)$con5_med[22] + (int)$con5_med[23] + (int)$con5_med[24] + (int)$con5_med[25] + (int)$con5_med[26] + (int)$con5_med[27] + (int)$con5_med[28] + (int)$con5_med[29] + (int)$con5_med[30];
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
                <td>".$paci["dni"]."</td>
                <td>".$paci["ape"]." ".$paci["nom"]."</td>
                <td>".$paci["edad"]."</td>
                <td>".$paci["medicamento 1"]."</td>
                <td>".$Tcon1_med."</td>
                <td>".$paci["medicamento 2"]."</td>
                <td>".$Tcon2_med."</td>
                <td>".$paci["medicamento 3"]."</td>
                <td>".$Tcon3_med."</td>
                <td>".$paci["medicamento 4"]."</td>
                <td>".$Tcon4_med."</td>
                <td>".$paci["medicamento 5"]."</td>
                <td>".$Tcon5_med."</td>
                <td>".$paci["n_ovo"]."</td>
                <td>".$paci["crio"]."</td>
                <!--<td>".$paci["p_dni"]." - ".$paci["p_ape"]." ".$paci["p_nom"]."</td>-->");
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