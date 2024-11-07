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
                        case 'criopac': print("<h2>Vitrificación de Óvulos de Paciente</h2>"); break;
                        case 'criodon': print("<h2>Vitrificación de Óvulos de Donante</h2>"); break;
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
        $between = $ini = $fin = $edesde = $ehasta = $busqueda = $p_extras = "";
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
            if (isset($_GET["tipant"]) && !empty($_GET["tipant"])) {
                $tipant = $_GET['tipant'];
                $between.= " and la.tip = '$tipant'";
            }
            if (isset($_GET["p_extras"]) && !empty($_GET["p_extras"])) {
                if ($_GET["p_extras"] == "VACIO") {
                    $p_extras = $_GET['p_extras'];
                    $busqueda.= " and (hc_reprod.p_extras = '' or hc_reprod.p_extras is null) ";
                }else{
                    $p_extras = $_GET['p_extras'];
                    $busqueda.= " and hc_reprod.p_extras ilike '%$p_extras%' ";
                }
            }
        }
        $item = 1;
        $ambos =  $ins1 = $ins2 = 0;
        $fecha_inicio_band = true;
        if (isset($_GET["rep"]) && !empty($_GET["rep"])) {
	        switch ($_GET["rep"]) {
	        	case 'betapen': require "_consultareporte/repo_betapen.php"; break;
	        	case 'betapos': require "_consultareporte/repo_betapos.php"; break;
	        	case 'betaneg': require "_consultareporte/repo_betaneg.php"; break;
				case 'betabio': require "_consultareporte/repo_betabio.php"; break;
				case 'betabo': require "_consultareporte/repo_betabo.php"; break;
                case 'embriopen': require "_consultareporte/repo_embriopen.php"; break;
                case 'embriopos': require "_consultareporte/repo_embriopos.php"; break;
                case 'embrioneg': require "_consultareporte/repo_embrioneg.php"; break;
                case 'embriobio': require "_consultareporte/repo_embriobio.php"; break;
                case 'embrioabo': require "_consultareporte/repo_embrioabo.php"; break;
	        	default: break;
	        }
        }
        $rPaci->execute();
        print("<div><p>Total: ".$rPaci->rowCount()."</p></div>");
        print("
        <table class='table table-responsive table-bordered align-middle'>
            <thead class='thead-dark'>
                <tr>
                    <th class='text-center'>Item</th>
                    <th class='text-center'>Protocolo</th>
                    <th class='text-center'>DNI/ Paciente</th>
                    <th class='text-center'>DNI/ Pareja</th>
                    <!--<th>Procedimiento</th>-->
                    <th class='text-center'>Médico</th>
                    <th class='text-center'>Fecha</th>
                </tr>
            </thead>
            <tbody>
        ");
        while ($paci = $rPaci->fetch(PDO::FETCH_ASSOC)) {
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
            //procedimiento-ini
            /*print("<td>");
            if ($paci['p_cic'] >= 1)
                print("Ciclo Natural<br>");
            if ($paci['p_fiv'] >= 1)
                print("FIV<br>");
            if ($paci['p_icsi'] >= 1)
                print("ICSI<br>");
            if ($paci['p_od'] <> '')
                print("OD Fresco<br>");
            if ($paci['p_cri'] >= 1)
                print("Crio Ovulos<br>");
            if ($paci['p_iiu'] >= 1)
                print("IIU<br>");
            if ($paci['p_don'] == 1)
                print("Donación Fresco<br>");
            if ($paci['des_don'] == null and $paci['des_dia'] >= 1)
                print("TED<br>");
            if ($paci['des_don'] == null and $paci['des_dia'] === 0)
                print("<small>Descongelación Ovulos Propios</small><br>");
            if ($paci['des_don'] <> null and $paci['des_dia'] >= 1)
                print("EMBRIODONACIÓN<br>");
            if ($paci['des_don'] <> null and $paci['des_dia'] === 0)
                print("<small>Descongelación Ovulos Donados</small><br>");
            print("</td>");*/
            //procedimiento-fin
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
                        print(date("Y-m-d H:i:s"));
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