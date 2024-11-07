<!DOCTYPE HTML>
<html>
<head>
    <?php
       include 'seguridad_login.php'
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="_themes/tema_inmater.min.css"/>
    <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css"/>
    <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css"/>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
    <script type="text/javascript">
        function PrintElem(elem) {
            var data = $(elem).html();
            var mywindow = window.open('', 'Imprimir', 'height=600,width=800');
            mywindow.document.write('<html><head><title>Imprimir</title>');
            mywindow.document.write('<style> @page {margin: 0px 0px 0px 5px;} table {border-collapse: collapse;font-size:10px;} .table-stripe td {border: 1px solid black;} .tablamas2 td {border: 1px solid white;} .mas2 {display: block !important;} .noVer, .ui-table-cell-label {display: none;} a:link {pointer-events: none; cursor: default;}</style>');
            mywindow.document.write("</head><body><p style='align: center'>Reporte Diagnóstico Genético Preimplantación</p>");
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
    <div data-role="page" class="ui-responsive-panel">
        <div data-role="header">
            <div data-role="controlgroup" data-type="horizontal" class="ui-mini ui-btn-left">
                <a href='lista_adminlab.php' class="ui-btn ui-btn-c ui-icon-home ui-btn-icon-left" rel="external">Inicio</a>
            </div>
            <h1>Transferencia - Betas - NGS</h1>
            <a href="salir.php"
               class="ui-btn-right ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-right ui-icon-power"
               rel="external">Salir</a>
        </div>
        <!-- /header -->
        <a href="javascript:PrintElem('#imprime')" data-role="button" data-mini="true" data-inline="true" rel="external">Imprimir</a>
<?php
    if ($_SESSION['role'] == "9") {
        $pen = $pos = $neg = $bio = $abo = 0;
        $item = 1;
        $fecha_inicio_band = true;
        $rPaci = $db->prepare("
            select
            hc_reprod.id, hc_reprod.med, lab_aspira.tip, lab_aspira.pro, lab_aspira.vec, case coalesce(lab_aspira.dias, 0) when 0 then 0 else (lab_aspira.dias-1) end dias
            , hc_reprod.des_dia, hc_reprod.des_don
            , hc_reprod.dni, hc_paciente.ape, hc_paciente.nom
            , hc_pareja.p_dni, hc_pareja.p_ape, hc_pareja.p_nom
            , lab_aspira.tip, hc_reprod.p_cic, hc_reprod.p_fiv, hc_reprod.p_icsi, hc_reprod.p_od, hc_reprod.p_don, hc_reprod.p_cri, hc_reprod.p_iiu
            , lab_aspira.fec, lab_aspira_t.beta
            from hc_reprod
            inner join lab_aspira on lab_aspira.rep = hc_reprod.id and lab_aspira.estado is true and lab_aspira.f_fin is not null and lab_aspira.tip <> 'T'
            inner join lab_aspira_t on lab_aspira.pro=lab_aspira_t.pro and lab_aspira_t.estado is true
            left join hc_paciente on hc_paciente.dni = hc_reprod.dni
            left join hc_pareja on hc_pareja.p_dni = hc_reprod.p_dni
            where hc_reprod.estado = true and hc_reprod.cancela=0 and hc_reprod.pago_extras ilike '%NGS%'
            order by lab_aspira.fec asc");
        $rPaci->execute();
        print("
        <table data-role='table' class='table-stripe ui-responsive mayuscula'>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Protocolo</th>
                    <th>DNI/ Paciente</th>
                    <th>DNI/ Pareja</th>
                    <th>Procedimiento</th>
                    <th>Médico</th>
                    <th>Tipo Beta<br>(pendiente<br>positivo<br>negativo<br>bioquimico<br>aborto)</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
        ");
        while ($paci = $rPaci->fetch(PDO::FETCH_ASSOC)) {
            if ($fecha_inicio_band and isset($paci["fec"])) {
                $fecha_inicio = $paci["fec"];
                $fecha_inicio_band = false;
            }
            if ($paci["beta"] == 0) {
                $pen++;
            }
            if ($paci["beta"] == 1) {
                $pos++;
            }
            if ($paci["beta"] == 2) {
                $neg++;
            }
            if ($paci["beta"] == 3) {
                $bio++;
            }
            if ($paci["beta"] == 4) {
                $abo++;
            }
            print("<tr>
                <td>".$item++."</td>
                <td><a href='le_aspi".$paci['dias'].".php?id=".$paci['pro']."'>".$paci["tip"]."-".$paci['pro']."-".$paci['vec']."</a></td>
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
            print("<td>".$paci["med"]."</td>");

            print("<td>");
            if ($paci["beta"] == 0) {
                print("1 - ");
            } else {
                print("0 - ");
            }
            if ($paci["beta"] == 1) {
                print("1 - ");
            } else {
                print("0 - ");
            }
            if ($paci["beta"] == 2) {
                print("1 - ");
            } else {
                print("0 - ");
            }
            if ($paci["beta"] == 3) {
                print("1 - ");
            } else {
                print("0 - ");
            }
            if ($paci["beta"] == 4) {
                print("1");
            } else {
                print("0");
            }
            print("</td>");

            print("<td>".$paci["fec"]."</td></tr>");
        }
        print("
            </tbody>
        </table>");
?>
        <div class="ui-content" role="main" id="imprime">
            <div><p>Fecha de Inicio de la primera Reproducción: <?php print($fecha_inicio); ?></p></div>
            <div class="scroll_h">
                <table style="border: 1px solid;" cellpadding="5">
                    <tbody>
                    <?php
                        print("
                            <tr><td>Betas Pendientes</td><td>".$pen."</td></tr>
                            <tr><td>Betas Positivas</td><td>".$pos."</td></tr>
                            <tr><td>Betas Negativas</td><td>".$neg."</td></tr>
                            <tr><td>Betas Bioquimico</td><td>".$bio."</td></tr>
                            <tr><td>Betas Aborto</td><td>".$abo."</td></tr>");
                    ?>
                        <tr>
                            <td colspan="2" bgcolor="#ffe4c4"><?php print("Total:".($item-1)); ?></td>
                        </tr>
                    </tbody>
                </table><br/><br/>
            </div>
            <div style="float:right"><p><b>Fecha y Hora de Reporte:</b> <?php
                date_default_timezone_set("America/Lima");
                print(date("Y-m-d H:m:s"));
            ?></p></div>
        </div>
    </div>
<?php } ?>
</body>
</html>