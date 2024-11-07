<?php session_start(); ?>
<!DOCTYPE HTML>
<html>

<head>
    <?php $login = $_SESSION['login'];
    $dir = $_SERVER['HTTP_HOST'] . substr(dirname(__FILE__), strlen($_SERVER['DOCUMENT_ROOT']));
    if ($_SESSION['role'] != 2) {
        echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=http://" . $dir . "'>";
    }
    require("_database/db_tools.php"); ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="_themes/tema_inmater.min.css" />
    <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css" />
    <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css" />
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
    <script src="jstickytableheaders.js"></script>
    <link rel="stylesheet" href="http://tablesorter.com/themes/blue/style.css" type="text/css" media="print, projection, screen">
    <script type="text/javascript" src="http://tablesorter.com/__jquery.tablesorter.min.js"></script>
    <style>
    .scroll_h {
        overflow: auto;
    }

    #alerta {
        background-color: #FF9;
        margin: 0 auto;
        text-align: center;
        padding: 4px;
    }

    .ui-icon-craneo {
        background: url(https://www.iconexperience.com/_img/o_collection_png/green_dark_grey/64x64/plain/skull2.png) 50% 50% no-repeat;
        background-size: 22px 22px;
    }

    .mayuscula {
        text-transform: uppercase;
        font-size: small;
    }

    .Mostrar {
        background-color: #F0DF96 !important;
    }

    #num_pro {
        color: red;
    }
    </style>
    <script>
    $(document).ready(function() {
        $(".Mostrar").click(function() {
            $("#reporte").val(1);
            $('#form1').submit();
        });

        $("#t4").change(function() { // Traslado

            if ($(this).prop('checked')) {
                $("#t1,#t2,#t3").checkboxradio("disable");
                $('.Traslado :checkbox').each(function() { //loop all checkbox in dvMain div
                    $(this).checkboxradio("disable");
                });
            } else {
                $("#t1,#t2,#t3").checkboxradio("enable");
                $('.Traslado :checkbox').each(function() { //loop all checkbox in dvMain div
                    $(this).checkboxradio("enable");
                });
            }
        });

        $(".table-stripe").stickyTableHeaders(); // Cabecera flotante o fija en la tabla
        $(".table-stripe").tablesorter(); // table sort

    });
    </script>
</head>

<body>
    <?php if ($_SESSION['role'] == 2) { ?>

    <script>
    $(function() {
        <?php if($_POST['reporte'] <> 1) { ?>
        $('.Cheqeados :checkbox').each(function() { //loop all checkbox in dvMain div
            $(this).prop("checked", true).checkboxradio("refresh");
        });
        <?php } ?>
        <?php if($_POST['t4'] == 1) { ?>
        $("#t1,#t2,#t3").checkboxradio("disable");
        $('.Traslado :checkbox').each(function() { //loop all checkbox in dvMain div
            $(this).checkboxradio("disable");
        });
        <?php } ?>

    });
    </script>


    <div data-role="page" class="ui-responsive-panel">

        <div data-role="header">
            <div data-role="controlgroup" data-type="horizontal" class="ui-mini ui-btn-left">
                <a href='lista.php' class="ui-btn ui-btn-c ui-icon-home ui-btn-icon-left" rel="external">Inicio</a>
                <a href='pro_admin.php' class="ui-btn ui-shadow ui-corner-all ui-icon-craneo ui-btn-icon-notext" rel="external">Admin</a>
            </div>
            <h1>Reporte Procedimientos</h1>
            <a href="salir.php" class="ui-btn-right ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-right ui-icon-power" rel="external">Salir</a>
        </div><!-- /header -->

        <div class="ui-content" role="main">
            <form action="r_pro.php" method="post" data-ajax="false" id="form1">
                <input type="hidden" name="reporte" id="reporte">
                <table style="margin: 0 auto;" width="100%">
                    <tr>
                        <td width="6%" align="center" valign="top">Tipo
                            <div data-role="controlgroup" data-mini="true" class="Cheqeados">
                                <input type="checkbox" name="t1" id="t1" <?php if(isset($_POST['t1']))if ($_POST['t1'] == 1) echo "checked"; ?> value=1> <label for="t1">Paciente</label>
                                <input type="checkbox" name="t2" id="t2" <?php if(isset($_POST['t2']))if ($_POST['t2'] == 1) echo "checked"; ?> value=1> <label for="t2">Receptora</label>
                                <input type="checkbox" name="t3" id="t3" <?php if(isset($_POST['t3']))if ($_POST['t3'] == 1) echo "checked"; ?> value=1> <label for="t3">Donante</label>
                            </div>
                            <input type="checkbox" name="t4" id="t4" <?php if(isset($_POST['t4']))if ($_POST['t4'] == 1) echo "checked"; ?> value=1 data-mini="true"> <label for="t4">Traslado</label>
                        </td>
                        <td width="15%" align="center" valign="top">Procedimiento Pricipal
                            <div data-role="controlgroup" data-mini="true" class="Cheqeados Traslado">
                                <input type="checkbox" name="p0" id="p0" <?php if(isset($_POST['p0']))if ($_POST['p0'] == 1) echo "checked"; ?> value=1> <label for="p0">Sin Procedimiento</label>
                                <input type="checkbox" name="p1" id="p1" <?php if(isset($_POST['p1']))if ($_POST['p1'] == 1) echo "checked"; ?> value=1> <label for="p1">FIV</label>
                                <input type="checkbox" name="p2" id="p2" <?php if(isset($_POST['p2']))if ($_POST['p2'] == 1) echo "checked"; ?> value=1> <label for="p2"><?php print($_ENV["VAR_ICSI"]); ?></label>
                                <input type="checkbox" name="p3" id="p3" <?php if(isset($_POST['p3']))if ($_POST['p3'] == 1) echo "checked"; ?> value=1> <label for="p3">CRIO OVOS</label>
                                <input type="checkbox" name="p4" id="p4" <?php if(isset($_POST['p4']))if ($_POST['p4'] == 1) echo "checked"; ?> value=1> <label for="p4">DonaciÃ³n Fresco</label>
                                <input type="checkbox" name="p5" id="p5" <?php if(isset($_POST['p5']))if ($_POST['p5'] == 1) echo "checked"; ?> value=1> <label for="p5">OD Fresco</label>
                                <input type="checkbox" name="p6" id="p6" <?php if(isset($_POST['p6']))if ($_POST['p6'] == 1) echo "checked"; ?> value=1> <label for="p6">DescongelaciÃ³n Ovulos</label>
                                <input type="checkbox" name="p7" id="p7" <?php if(isset($_POST['p7']))if ($_POST['p7'] == 1) echo "checked"; ?> value=1> <label for="p7">TED</label>
                            </div>
                        </td>
                        <td width="12%" align="center" valign="top">Procedimiento Extra
                            <div data-role="controlgroup" data-mini="true" class="Cheqeados Traslado">
                                <input type="checkbox" name="pa0" id="pa0" <?php if(isset($_POST['pa0']))if ($_POST['pa0'] == 1) echo "checked"; ?> value=1><label for="pa0">Sin Extra</label>
                                <input type="checkbox" name="pa1" id="pa1" <?php if(isset($_POST['pa1']))if ($_POST['pa1'] == 1) echo "checked"; ?> value=1><label for="pa1">SNP</label>
                                <input type="checkbox" name="pa2" id="pa2" <?php if(isset($_POST['pa2']))if ($_POST['pa2'] == 1) echo "checked"; ?> value=1><label for="pa2">TRANSFERENCIA FRESCO</label>
                                <input type="checkbox" name="pa3" id="pa3" <?php if(isset($_POST['pa3']))if ($_POST['pa3'] == 1) echo "checked"; ?> value=1><label for="pa3">PCR</label>
                                <input type="checkbox" name="pa4" id="pa4" <?php if(isset($_POST['pa4']))if ($_POST['pa4'] == 1) echo "checked"; ?> value=1><label for="pa4">NGS</label>
                                <input type="checkbox" name="pa5" id="pa5" <?php if(isset($_POST['pa5']))if ($_POST['pa5'] == 1) echo "checked"; ?> value=1><label for="pa5">CRIO TOTAL</label>
                                <input type="checkbox" name="pa6" id="pa6" <?php if(isset($_POST['pa6']))if ($_POST['pa6'] == 1) echo "checked"; ?> value=1><label for="pa6">EMBRYOGLUE</label>
                                <input type="checkbox" name="pa7" id="pa7" <?php if(isset($_POST['pa7']))if ($_POST['pa7'] == 1) echo "checked"; ?> value=1><label for="pa7">EMBRYOSCOPE</label>
                                <input type="checkbox" name="pa8" id="pa8" <?php if(isset($_POST['pa8']))if ($_POST['pa8'] == 1) echo "checked"; ?> value=1><label for="pa8">PICSI</label>
                                <input type="checkbox" name="pa9" id="pa9" <?php if(isset($_POST['pa9']))if ($_POST['pa9'] == 1) echo "checked"; ?> value=1><label for="pa9">BANKING EMBRIONES</label>
                            </div>
                        </td>
                        <td width="12%" align="center" valign="top">Betas
                            <div data-role="controlgroup" data-mini="true" class="Cheqeados Traslado">
                                <input type="checkbox" name="b0" id="b0" <?php if(isset($_POST['b0']))if ($_POST['b0'] == 1) echo "checked"; ?> value=1> <label for="b0">No Transferido</label>
                                <input type="checkbox" name="b1" id="b1" <?php if(isset($_POST['b1']))if ($_POST['b1'] == 1) echo "checked"; ?> value=1> <label for="b1">Pendiente</label>
                                <input type="checkbox" name="b2" id="b2" <?php if(isset($_POST['b2']))if ($_POST['b2'] == 1) echo "checked"; ?> value=1> <label for="b2">Positivo</label>
                                <input type="checkbox" name="b3" id="b3" <?php if(isset($_POST['b3']))if ($_POST['b3'] == 1) echo "checked"; ?> value=1> <label for="b3">Negativo</label>
                                <input type="checkbox" name="b4" id="b4" <?php if(isset($_POST['b4']))if ($_POST['b4'] == 1) echo "checked"; ?> value=1> <label for="b4">Bioquimico</label>
                                <input type="checkbox" name="b5" id="b5" <?php if(isset($_POST['b5']))if ($_POST['b5'] == 1) echo "checked"; ?> value=1> <label for="b5">Aborto</label>
                                <input type="checkbox" name="b6" id="b6" <?php if(isset($_POST['b6']))if ($_POST['b6'] == 1) echo "checked"; ?> value=1> <label for="b6">Anembrionado</label>
                                <input type="checkbox" name="b7" id="b7" <?php if(isset($_POST['b7']))if ($_POST['b7'] == 1) echo "checked"; ?> value=1> <label for="b7">Ectópico</label>
                            </div>
                        </td>
                        <td width="26%" align="center" valign="top">Medicos
                            <div data-role="controlgroup" data-mini="true" class="Cheqeados">
                                <?php $rUser = $db->prepare("SELECT userX FROM usuario WHERE role=1");
                                $rUser->execute();
                                $i = 0;
                                while ($user = $rUser->fetch(PDO::FETCH_ASSOC)) {
                                    $i++;
                                    if (isset($_POST['u' . $i]) && $_POST['u' . $i] <> "") $check = "checked"; else $check = "";
                                    echo '<input type="checkbox" name="u' . $i . '" id="u' . $i . '" ' . $check . ' value="' . $user['userx'] . '"><label for="u' . $i . '">' . $user['userx'] . '</label>';
                                } ?>
                                <input type="hidden" name="numUser" value=<?php echo $i; ?>>
                            </div>
                        </td>
                        <td width="29%" align="center" valign="top">Estado
                            <div data-role="controlgroup" data-mini="true">
                                <input type="radio" name="sta" id="sta1" <?php if(isset($_POST['sta']))if ($_POST['sta'] == "and lab_aspira.f_fin<>1899-12-30") echo "checked"; ?> value="and lab_aspira.f_fin<>1899-12-30"> <label for="sta1">Finalizados</label>
                                <input type="radio" name="sta" id="sta2" <?php if(isset($_POST['sta']))if ($_POST['sta'] == "and lab_aspira.f_fin=1899-12-30") echo "checked"; ?> value="and lab_aspira.f_fin=1899-12-30"> <label for="sta2">En Curso</label>
                                <input type="radio" name="sta" id="sta3" <?php if(isset($_POST['sta']))if ($_POST['sta'] == "") echo "checked"; ?> value=""> <label for="sta3">Todos</label>
                            </div>
                            Mostrar Desde<input name="ini" type="date" id="ini" value="<?php if(isset($_POST['ini']))echo $_POST['ini']; ?>" data-mini="true">
                            Hasta<input name="fin" type="date" id="fin" value="<?php if(isset($_POST['fin']))echo $_POST['fin']; ?>" data-mini="true">
                            <h6>Dejar en blanco para mostrar todas las fechas</h6>
                            <p><a href="#" class="Mostrar ui-btn ui-icon-bullets ui-btn-icon-right ui-btn-inline" rel="external">Mostrar</a></p>
                        </td>
                    </tr>
                </table>
                <?php if (isset($_POST['reporte']) && $_POST['reporte'] <> "" && $_POST['numUser'] > 0) {

                    $pro1 = " and (";
                    if(isset($_POST['p1']))if ($_POST['p1'] == 1) $pro1 .= 'p_fiv=1 OR ';
                    if(isset($_POST['p2']))if ($_POST['p2'] == 1) $pro1 .= 'p_icsi=1 OR ';
                    if(isset($_POST['p3']))if ($_POST['p3'] == 1) $pro1 .= 'p_cri=1 OR ';
                    if(isset($_POST['p4']))if ($_POST['p4'] == 1) $pro1 .= 'p_don=1 OR ';
                    if(isset($_POST['p5']))if ($_POST['p5'] == 1) $pro1 .= 'p_od<>"" OR ';
                    if(isset($_POST['p6']))if ($_POST['p6'] == 1) $pro1 .= 'des_dia=0 OR ';
                    if(isset($_POST['p7']))if ($_POST['p7'] == 1) $pro1 .= 'des_dia>0 OR ';

                    if (isset($_POST['p0']) && $_POST['p0'] == 1) $pro1 .= "p_cic IS NULL AND p_fiv IS NULL AND p_icsi IS NULL AND des_dia IS NULL AND p_don IS NULL and p_cri IS NULL and (p_od IS NULL or p_od='') AND (des_don IS NULL or des_don=''))"; //Muestra los q no tienen procedimientos
                    else
                        $pro1 .= "hc_reprod.med='1')"; //es solo para cerrar la condicion y el OR no que vacio

                    $pro2 = " and (";
                    if(isset($_POST['pa1']))if ($_POST['pa1'] == 1) $pro2 .= 'pago_extras ILIKE "%SNP%" OR ';
                    if(isset($_POST['pa2']))if ($_POST['pa2'] == 1) $pro2 .= 'pago_extras ILIKE "%TRANSFERENCIA FRESCO%" OR ';
                    if(isset($_POST['pa3']))if ($_POST['pa3'] == 1) $pro2 .= 'pago_extras ILIKE "%PCR%" OR ';
                    if(isset($_POST['pa4']))if ($_POST['pa4'] == 1) $pro2 .= 'pago_extras ILIKE "%NGS%" OR ';
                    if(isset($_POST['pa5']))if ($_POST['pa5'] == 1) $pro2 .= 'pago_extras ILIKE "%CRIO TOTAL%" OR ';
                    if(isset($_POST['pa6']))if ($_POST['pa6'] == 1) $pro2 .= 'pago_extras ILIKE "%EMBRYOGLUE%" OR ';
                    if(isset($_POST['pa7']))if ($_POST['pa7'] == 1) $pro2 .= 'pago_extras ILIKE "%EMBRYOSCOPE%" OR ';
                    if(isset($_POST['pa8']))if ($_POST['pa8'] == 1) $pro2 .= 'pago_extras ILIKE "%PICSI%" OR ';
                    if(isset($_POST['pa9']))if ($_POST['pa9'] == 1) $pro2 .= 'pago_extras ILIKE "%BANKING EMBRIONES%" OR ';

                    if (isset($_POST['pa0']) && $_POST['pa0'] == 1) $sinExtra = ""; else $sinExtra = "xxx";
                    $pro2 .= "pago_extras='" . $sinExtra . "')"; //es solo para cerrar la condicion y el OR no que vacio

                    $pac = " and (";
                    if(isset($_POST['t1']))if ($_POST['t1'] == 1) $pac .= 'lab_aspira.tip="P" OR ';
                    if(isset($_POST['t2']))if ($_POST['t2'] == 1) $pac .= 'lab_aspira.tip="R" OR ';
                    if(isset($_POST['t3']))if ($_POST['t3'] == 1) $pac .= 'lab_aspira.tip="D" OR ';
                    if(isset($_POST['t4']))if ($_POST['t4'] == 1) {
                        $pac .= 'lab_aspira.tip="T" OR ';
                        $f_asp = "";
                        $pro1 = "";
                        $pro2 = "";
                    } else $f_asp = " and hc_reprod.f_asp <> ''";
                    $pac .= "hc_reprod.med='1')"; //es solo para cerrar la condicion y el OR no que vacio

                    $medico = " and (";
                    for ($i = 1; $i <= $_POST['numUser']; $i++) {
                        if(isset($_POST['u' . $i]))if ($_POST['u' . $i] <> "") $medico .= "hc_reprod.med='" . $_POST['u' . $i] . "' OR ";
                    }
                    $medico .= "hc_reprod.med='1')"; //es solo para cerrar la condicion y el OR no que vacio

                    $rango = "";
                    if ($_POST['ini'] <> "" && $_POST['fin'] <> "")
                        $rango = " and lab_aspira.fec between '" . $_POST['ini'] . "' and '" . $_POST['fin'] . "'";
                    

                    $sta = isset($_POST['sta']) ? $_POST['sta'] : '';
                    if (!isset($f_asp)) {
                        $f_asp = '';
                    }

                    $rPaci = $db->prepare("SELECT ape,nom,hc_reprod.id,hc_reprod.dni,hc_reprod.eda,hc_reprod.p_cic,hc_reprod.p_fiv,hc_reprod.p_icsi,hc_reprod.p_od,hc_reprod.p_don,hc_reprod.p_cri,hc_reprod.des_don,hc_reprod.pago_extras,hc_reprod.p_dni,hc_reprod.p_dni_het,hc_reprod.f_asp,hc_reprod.des_dia,hc_reprod.med,lab_aspira.pro,lab_aspira.tip,lab_aspira.dias,lab_aspira.fec0,lab_aspira.fec1,lab_aspira.fec2,lab_aspira.fec3,lab_aspira.fec4,lab_aspira.fec5,lab_aspira.fec6,lab_aspira.f_fin FROM hc_paciente,hc_reprod,lab_aspira WHERE hc_reprod.estado = true and lab_aspira.estado is true and hc_paciente.dni=lab_aspira.dni AND hc_reprod.id=lab_aspira.rep " . $sta . $f_asp . $pac . $pro1 . $pro2 . $medico . $rango . " ORDER BY ABS(CAST(SUBSTRING(pro FROM '([0-9]+)') AS INTEGER)) DESC");

                    $rPaci->execute();

                    if ($rPaci->rowCount() > 0) { ?>
                <a href="#" id="exporta" data="<?php echo $_POST['reporte']; ?>" style="display:none;" class="ui-btn ui-mini ui-btn-inline">Exportar a Excel</a>
                <div class="scroll_h">
                    <table width="100%" bordercolor="#F0DF96" style="margin:0 auto;font-size:small;" class="table-stripe tablesorter">
                        <thead>
                            <tr class="ui-bar-b">
                                <th colspan="2" bgcolor="#F0DF96">PROTOCOLOS (<span id="num_pro"></span>)</th>
                                <th>APELLIDOS Y NOMBRES</th>
                                <th>MEDICO</th>
                                <?php if(isset($_POST['p1']))if ($_POST['p1'] == 1) { ?>
                                <th>FIV</th><?php } ?>
                                <?php if(isset($_POST['p2']))if ($_POST['p2'] == 1) { ?>
                                <th><?php print($_ENV["VAR_ICSI"]); ?></th><?php } ?>
                                <?php if(isset($_POST['p3']))if ($_POST['p3'] == 1) { ?>
                                <th>CRIO<br>OVOS</th><?php } ?>
                                <?php if(isset($_POST['p4']))if ($_POST['p4'] == 1) { ?>
                                <th>DONACION<br>FRESCO</th><?php } ?>
                                <?php if(isset($_POST['p5']))if ($_POST['p5'] == 1) { ?>
                                <th>OD<br>FRESCO</th><?php } ?>
                                <?php if(isset($_POST['p6']))if ($_POST['p6'] == 1 || $_POST['p7'] == 1) { ?>
                                <th>DES. OVO<br>TED</th><?php } ?>
                                <?php if(isset($_POST['pa1']))if ($_POST['pa1'] == 1 || $_POST['pa2'] == 1 || $_POST['pa3'] == 1 || $_POST['pa4'] == 1 || $_POST['pa5'] == 1 || $_POST['pa6'] == 1 || $_POST['pa7'] == 1 || $_POST['pa8'] == 1 || $_POST['pa9'] == 1) { ?>
                                <th>EXTRAS</th><?php } ?>
                                <?php if(isset($_POST['t4']))if ($_POST['t4'] <> 1) { ?>
                                <th align="center">F. PUNCION<br>F. DESCONGELA</th><?php } ?>
                                <?php if(isset($_POST['b1']))if ($_POST['b1'] == 1 || $_POST['b2'] == 1 || $_POST['b3'] == 1 || $_POST['b4'] == 1) { ?>
                                <th>BETA</th><?php } ?>
                                <th align="center">F. FIN</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $c = 0;
                                while ($paci = $rPaci->fetch(PDO::FETCH_ASSOC)) {

                                    $beta = " and (";
                                    if(isset($_POST['b1']))if ($_POST['b1'] == 1) $beta .= 'beta=0 OR ';
                                    if(isset($_POST['b2']))if ($_POST['b2'] == 1) $beta .= 'beta=1 OR ';
                                    if(isset($_POST['b3']))if ($_POST['b3'] == 1) $beta .= 'beta=2 OR ';
                                    if(isset($_POST['b4']))if ($_POST['b4'] == 1) $beta .= 'beta=3 OR ';
                                    $beta .= "beta=777)"; //es solo para cerrar la condicion y el OR no que vacio

//if ($_POST['b0']==1 or ($_POST['b1']<>1 and $_POST['b2']<>1 and $_POST['b3']<>1 and $_POST['b4']<>1)) { // para q solo aparezca los Sin Transferencia
//$beta="";
//}

                                    $rBeta = $db->prepare("SELECT beta FROM lab_aspira_t WHERE pro=? and lab_aspira_t.estado is true " . $beta . "");
                                    $rBeta->execute(array($paci['pro']));
                                    $bet = $rBeta->fetch(PDO::FETCH_ASSOC);
                                    $num_bet = $rBeta->rowCount();

//if ($_POST['b0']==1 or ($_POST['b1']<>1 and $_POST['b2']<>1 and $_POST['b3']<>1 and $_POST['b4']<>1)) { // para q solo aparezca los Sin Transferencia
//if($rBeta->rowCount()==1) $num_bet=0;
//if($rBeta->rowCount()==0) $num_bet=1;
//}

                                    if ($num_bet > 0 || $_POST['b0'] == 1 || (isset($_POST['t4']) && $_POST['t4'] == 1)) {

                                        $c++;

                                        if ($paci['dias'] > 0) $paci['dias'] = $paci['dias'] - 1;

                                        echo '<tr bgcolor="#FFFFFF"><td>' . $c . '</td><td><a href="le_aspi' . $paci['dias'] . '.php?id=' . $paci['pro'] . '" target="new">' . $paci['tip'] . '-' . $paci['pro'] . '</a><br><a href="info_r.php?a=' . $paci['pro'] . '&b=' . $paci['dni'] . '&c=' . $paci['p_dni'] . '" target="new" style="font-size:10px">Informe</a></td><td class="mayuscula">' . $paci['ape'] . ' ' . $paci['nom'] . ' (' . $paci['dni'] . ')</td><td>' . $paci['med'] . '</td>';

                                        if ($_POST['p1'] == 1) echo '<td>' . $paci['p_fiv'] . '</td>';
                                        if ($_POST['p2'] == 1) echo '<td>' . $paci['p_icsi'] . '</td>';
                                        if ($_POST['p3'] == 1) echo '<td>' . $paci['p_cri'] . '</td>';
                                        if ($_POST['p4'] == 1) echo '<td>' . $paci['p_don'] . '</td>';
                                        if ($_POST['p5'] == 1) echo '<td>' . $paci['p_od'] . '</td>';
                                        if ($_POST['p6'] == 1 || $_POST['p7'] == 1) echo '<td>' . $paci['des_dia'] . '</td>';
                                        if ($_POST['pa1'] == 1 || $_POST['pa2'] == 1 || $_POST['pa3'] == 1 || $_POST['pa4'] == 1 || $_POST['pa5'] == 1 || $_POST['pa6'] == 1 || $_POST['pa7'] == 1 || $_POST['pa8'] == 1 || $_POST['pa9'] == 1) echo '<td>' . $paci['pago_extras'] . '</td>';

                                        if ($_POST['t4'] <> 1) {
                                            if ($paci['des_dia'] > 0) $dia = $paci['des_dia']; else $dia = 0;
                                            echo '<td>' . date("d-m-Y", strtotime($paci['fec' . $dia])) . '</td>';
                                        }

                                        if ($_POST['b1'] == 1 or $_POST['b2'] == 1 or $_POST['b3'] == 1 or $_POST['b4'] == 1) { // solo si marca betas ----------------------
                                            if ($bet['beta'] === 0) echo '<td>Pendiente</td>';
                                            else if ($bet['beta'] == 1) echo '<td>Positivo</td>';
                                            else if ($bet['beta'] == 2) echo '<td>Negativo</td>';
                                            else if ($bet['beta'] == 3) echo '<td>Bioquimico</td>';
                                            else if ($bet['beta'] == 4) echo '<td>Aborto</td>';
                                            else if ($bet['beta'] == 5) echo '<td>Anembrionado</td>';
                                            else if ($bet['beta'] == 6) echo '<td>Ectópico</td>';
                                            else echo '<td></td>';
                                        }

                                        if ($paci['f_fin'] == '1899-12-30') $f_fin = 'En curso'; else $f_fin = date("d-m-Y", strtotime($paci['f_fin']));
                                        echo '<td>' . $f_fin . '</td></tr>';

                                    }
                                } ?>
                        </tbody>
                    </table>
                </div>
                <?php }
                } ?>

            </form>
        </div><!-- /content -->

    </div><!-- /page -->
    <?php } ?>
    <script>
    $(function() {
        // $('#num_pro').html(<?php echo $c; ?>);
        $('#alerta').delay(3000).fadeOut('slow');

    }); //]]>
    </script>
</body>

</html>