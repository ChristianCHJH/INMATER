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
            <h2>Reporte NGS - Ciclos realizados (Transferencias)</h2>
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
        $fecha_inicio_band = true;
        $rPaci = $db->prepare("
            select
            hc_reprod.id, hc_reprod.med, lab_aspira.tip, lab_aspira.pro, lab_aspira.vec, case coalesce(lab_aspira.dias, 0) when 0 then 0 else (lab_aspira.dias-1) end dias
            , hc_reprod.des_dia, hc_reprod.des_don
            , hc_reprod.dni, hc_paciente.ape, hc_paciente.nom
            , hc_pareja.p_dni, hc_pareja.p_ape, hc_pareja.p_nom
            , lab_aspira.tip, hc_reprod.p_cic, hc_reprod.p_fiv, hc_reprod.p_icsi, hc_reprod.p_od, hc_reprod.p_don, hc_reprod.p_cri, hc_reprod.p_iiu
            , lab_aspira.fec
            from hc_reprod
            inner join lab_aspira on lab_aspira.rep = hc_reprod.id and lab_aspira.estado is true and lab_aspira.f_fin is not null and lab_aspira.tip <> 'T'$between
            left join hc_paciente on hc_paciente.dni = hc_reprod.dni
            left join hc_pareja on hc_pareja.p_dni = hc_reprod.p_dni
            where hc_reprod.estado = true and hc_reprod.cancela=0 and hc_reprod.des_don is null and hc_reprod.des_dia >= 1 and hc_reprod.pago_extras ilike ('%NGS%')
            order by lab_aspira.fec asc");
        $rPaci->execute();
        print("<div><p>Total: ".$rPaci->rowCount()."</p></div>");
        print("
        <table class='table table-responsive table-bordered align-middle'>
            <thead class='thead-dark'>
                <tr>
                    <th>Item</th>
                    <th>Protocolo</th>
                    <th>DNI/ Paciente</th>
                    <th>DNI/ Pareja</th>
                    <th>Procedimiento</th>
                    <th>Médico</th>
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
            /*if ($paci['p_fiv'] == 1 and $paci['p_icsi'] == 1) {
                $ted++;
            }*/
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

            print("<td>".$paci["med"]."</td>
                <td>".$paci["fec"]."</td></tr>");
        }
        print("
            </tbody>
        </table>");
?>
        <div class="ui-content" role="main" id="imprime">
            <div><p>Fecha de Inicio de la primera Reproducción: <?php print($fecha_inicio); ?></p></div>
            <div class="scroll_h">
            </div>
        </div>
    </div>
<?php } ?>
    <script src="js/jquery-3.2.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="js/popper.min.js" crossorigin="anonymous"></script>
    <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>
</html>