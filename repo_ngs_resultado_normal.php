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
            <h2>Reporte NGS - Embriones Normales</h2>
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
                $between.= " and lab_aspira.o_ovo ilike '%$ovo%'";
            }
        }
        $item = 1;
        $ambos =  $ins1 = $ins2 = 0;
        $normal =  $anormal = $mosaico = 0;
        $fecha_inicio_band = true;
        $rPaci = $db->prepare("SELECT
            hc_reprod.id, hc_reprod.med, lab_aspira.tip, lab_aspira.pro, lab_aspira.vec, case coalesce(lab_aspira.dias, 0) when 0 then 0 else (lab_aspira.dias-1) end dias
            , hc_reprod.des_dia, hc_reprod.des_don
            , hc_reprod.dni, hc_paciente.ape, hc_paciente.nom
            , hc_pareja.p_dni, hc_pareja.p_ape, hc_pareja.p_nom
            , lab_aspira.tip, hc_reprod.p_cic, hc_reprod.p_fiv, hc_reprod.p_icsi, hc_reprod.p_od, hc_reprod.p_don, hc_reprod.p_cri, hc_reprod.p_iiu
            , lab_aspira.fec, count(lab_aspira_dias.pro) total
            , count( case when lab_aspira_dias.ngs1 = 1 then true end ) normal, count( case when lab_aspira_dias.ngs1 = 2 then true end ) anormal, count( case when lab_aspira_dias.ngs1 = 4 then true end ) mosaico
            from hc_reprod
            inner join lab_aspira on lab_aspira.rep = hc_reprod.id and lab_aspira.estado is true and lab_aspira.f_fin is not null and lab_aspira.tip <> 'T' AND lab_aspira.dias>=5$between
            inner join lab_aspira_dias on lab_aspira_dias.pro = lab_aspira.pro and lab_aspira_dias.estado is true and lab_aspira_dias.ngs1 in (1) and ((lab_aspira_dias.d5d_bio<>0 and lab_aspira_dias.d5f_cic='C') or (lab_aspira_dias.d6d_bio<>0 and lab_aspira_dias.d6f_cic='C'))
            left join hc_paciente on hc_paciente.dni = hc_reprod.dni
            left join hc_pareja on hc_pareja.p_dni = hc_reprod.p_dni
            where hc_reprod.estado = true and hc_reprod.cancela=0 and hc_reprod.pago_extras ilike '%NGS%'
            group by hc_reprod.id, hc_reprod.med, lab_aspira.tip, lab_aspira.pro, lab_aspira.vec, case coalesce(lab_aspira.dias, 0) when 0 then 0 else (lab_aspira.dias-1) end
            , hc_reprod.des_dia, hc_reprod.des_don
            , hc_reprod.dni, hc_paciente.ape, hc_paciente.nom
            , hc_pareja.p_dni, hc_pareja.p_ape, hc_pareja.p_nom
            , lab_aspira.tip, hc_reprod.p_cic, hc_reprod.p_fiv, hc_reprod.p_icsi, hc_reprod.p_od, hc_reprod.p_don, hc_reprod.p_cri, hc_reprod.p_iiu
            , lab_aspira.fec
            order by lab_aspira.fec asc");
        $rPaci->execute();
        print("<div><p>Total de Procedimientos: ".$rPaci->rowCount()."</p></div>");
        print("
        <table class='table table-responsive table-bordered align-middle'>
            <thead class='thead-dark'>
                <tr>
                    <th class='text-center'>Item</th>
                    <th class='text-center'>Protocolo</th>
                    <th class='text-center'>DNI/ Paciente</th>
                    <th class='text-center'>DNI/ Pareja</th>
                    <th class='text-center'>Procedimiento</th>
                    <th class='text-center'>Médico</th>
                    <th class='text-center'>Fecha</th>
                    <!--<th>Total<br>Embriones</th>-->
                    <th class='text-center'>Embriones<br>Normales</th>
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
                $ambos++;
            } else if ($paci['p_fiv'] == 1) {
                $ins1++;
            } else {
               $ins2++;
            }
            $normal+=$paci['normal'];
            $anormal+=$paci['anormal'];
            $mosaico+=$paci['mosaico'];
            print("<tr>
                <td class='text-center'>".$item++."</td>
                <td>
                    <a href='le_aspi".$paci['dias'].".php?id=".$paci['pro']."' target='_blank'>".$paci["tip"]."-".$paci['pro']."-".$paci['vec']."</a><br>
                    <a href='e_ngs.php?id=".$paci['pro']."' target='_blank'>resultado</a>
                </td>
                <td>".$paci["dni"]." - ".$paci["ape"]." ".$paci["nom"]."</td>
                <td>".$paci["p_dni"]." - ".$paci["p_ape"]." ".$paci["p_nom"]."</td>");
            //procedimiento-ini
            print("<td>");
            if ($paci['p_cic'] >= 1)
                print("Ciclo Natural<br>");
            if ($paci['p_fiv'] >= 1)
                print("FIV<br>");
            if ($paci['p_icsi'] >= 1)
                print($_ENV["VAR_ICSI"] . "<br>");
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
            print("</td>");
            //procedimiento-fin
            print("
                <td>".$paci["med"]."</td>
                <td>".$paci["fec"]."</td>
                <!--<td>".$paci["total"]."</td>-->
                <td class='text-center'>".$paci["normal"]."</td></tr>");
        }
        print("
            </tbody>
        </table>");
?>
            <div style="float:right">
                <?php print("<p><b>Total Embriones:</b> ".$normal."</p>"); ?>
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