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
            <h1>Óvulos Blastulación BH - Día 5</h1>
            <a href="index.php"
               class="ui-btn-right ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-right ui-icon-power"
               rel="external">Salir</a>
        </div>
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
                $between.= " and unaccent(lab_aspira.o_ovo) ilike '%$ovo%'";
            }
            if (isset($_GET["tipa"]) && !empty($_GET["tipa"])) {
                $tipa = $_GET['tipa'];
                $between.= " and lab_aspira.tip = '$tipa'";
            }
            if (isset($_GET["inc"]) && !empty($_GET["inc"])) {
                $inc = $_GET['inc'];
                $between.= " and lab_aspira.inc1 = $inc";
            }
        }
        $item = 1;
        $ambos =  $ins1 = $ins2 = 0;
        $blashatd5 = 0;
        $fecha_inicio_band = true;
        $rPaci = $db->prepare("
            select
            hc_reprod.id, hc_reprod.med, hc_reprod.pago_extras, lab_aspira.tip, lab_aspira.pro, lab_aspira.vec, case coalesce(lab_aspira.dias, 0) when 0 then 0 else (lab_aspira.dias-1) end dias
            , hc_reprod.des_dia, hc_reprod.des_don
            , hc_reprod.dni, hc_paciente.ape, hc_paciente.nom
            , hc_pareja.p_dni, hc_pareja.p_ape, hc_pareja.p_nom
            , lab_aspira.tip, hc_reprod.p_cic, hc_reprod.p_fiv, hc_reprod.p_icsi, hc_reprod.p_od, hc_reprod.p_don, hc_reprod.p_cri, hc_reprod.p_iiu
            , lab_aspira.fec
            , count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d5cel = 'BH' then true end ) blashatd5
            from hc_reprod
            inner join hc_paciente on hc_paciente.dni = hc_reprod.dni
            inner join lab_aspira on lab_aspira.rep = hc_reprod.id and lab_aspira.estado is true and lab_aspira.f_fin is not null and lab_aspira.tip <> 'T'$between
            inner join lab_aspira_dias on lab_aspira_dias.pro = lab_aspira.pro and lab_aspira_dias.estado is true
            left join hc_pareja on hc_pareja.p_dni = hc_reprod.p_dni
            where hc_reprod.estado = true and hc_reprod.p_fiv >= 1
            group by hc_reprod.id, hc_reprod.med, hc_reprod.pago_extras, lab_aspira.tip, lab_aspira.pro, lab_aspira.vec, case coalesce(lab_aspira.dias, 0) when 0 then 0 else (lab_aspira.dias-1) end
            , hc_reprod.des_dia, hc_reprod.des_don
            , hc_reprod.dni, hc_paciente.ape, hc_paciente.nom
            , hc_pareja.p_dni, hc_pareja.p_ape, hc_pareja.p_nom
            , lab_aspira.tip, hc_reprod.p_cic, hc_reprod.p_fiv, hc_reprod.p_icsi, hc_reprod.p_od, hc_reprod.p_don, hc_reprod.p_cri, hc_reprod.p_iiu
            , lab_aspira.fec
            having count( case when lab_aspira_dias.d1est = 'MII' and lab_aspira_dias.d1f_cic = 'O' and lab_aspira_dias.d1c_pol = '2' and lab_aspira_dias.d1pron = '2' and lab_aspira_dias.d5cel = 'BH' then true end ) > 0
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
                    <th>Fecha</th>
                    <th>BH</th>
                </tr>
            </thead>
            <tbody>
        ");
        while ($paci = $rPaci->fetch(PDO::FETCH_ASSOC)) {
            $blashatd5+=$paci['blashatd5'];
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
            print("<tr>
                <td>".$item++."</td>
                <td><a href='le_aspi".$paci['dias'].".php?id=".$paci['pro']."' target='_blank'>".$paci["tip"]."-".$paci['pro']."-".$paci['vec']."</a></td>
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
                <td><a href='le_aspi5.php?id=".$paci['pro']."' target='_blank'>".$paci["blashatd5"]."</a></td></tr>");
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
                    print '<tr><td>Blastulación BH</td><td>'.$blashatd5.'</td></tr>';
                ?>
                    <tr>
                        <td colspan="2" bgcolor="#ffe4c4"><?php print("Total:".($blashatd5) ); ?></td>
                    </tr>
                </tbody>
            </table><br/><br/>
        </div>
        <div style="float:right"><p><b>Fecha y Hora de Reporte:</b> <?php
            date_default_timezone_set('America/Lima');
            print(date("Y-m-d H:m:s"));
        ?></p></div>
    </div>
    </div>
<?php } ?>
</body>
</html>