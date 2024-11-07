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
    <style type="text/css">
        input[data-type=search]:enabled { background: #fcfcfc; }
        input[data-type=search]:disabled { background: #dddddd; }
    </style>
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
    <script type="text/javascript">
        $(document).keydown('#filtroproc', function(e){
            if(e.which == 13) {
                var procedimiento = $('#filtroproc')[0].value;
                $("#filtroproc").prop("disabled", true);
                $.post("le_tanque.php", {procedimiento: procedimiento}, function (data) {
                    $("#detalleprocedimiento tbody").html("");
                    $("#detalleprocedimiento tbody").append(data);
                    $('.ui-page').trigger('create');
                })
                .done(function() {
                    document.getElementById("prochead").classList.remove("ui-screen-hidden");
                    document.getElementById("procbody").classList.remove("ui-screen-hidden");
                    // $('#filtroproc').val("");
                    $("#filtroproc").prop("disabled", false);
                    // $("#filtroproc").focus();
                });
            }
        });
        $(document).keydown('#filtronombres', function(e){
            if(e.which == 13) {
                var filtronombres = $('#filtronombres')[0].value;
                $("#filtronombres").prop("disabled", true);
                $.post("le_tanque.php", {filtronombres: filtronombres}, function (data) {
                    $("#detalleprocedimiento tbody").html("");
                    $("#detalleprocedimiento tbody").append(data);
                    $('.ui-page').trigger('create');
                })
                .done(function() {
                    document.getElementById("prochead").classList.remove("ui-screen-hidden");
                    document.getElementById("procbody").classList.remove("ui-screen-hidden");
                    // $('#filtronombres').val("");
                    $("#filtronombres").prop("disabled", false);
                    // $("#filtronombres").focus();
                });
            }
        });
    </script>
</head>
<body>
    <form action="lista_pro_f.php" method="post" data-ajax="false" id="form1">
        <div id="detallepaciente">
            <div data-role="page" class="ui-responsive-panel" id="lista_pro_f">
                <?php
                $rUser = $db->prepare("SELECT role FROM usuario WHERE userx=?");
                $rUser->execute(array($login));
                $user = $rUser->fetch(PDO::FETCH_ASSOC);
                //
                $ini = date('Y-m-d', strtotime(date('Y-m-d'). ' + 1 days'));
                $fin = date('Y-m-d', strtotime($ini. ' - 15 days'));
                $between = " and lab_aspira.fec between '$fin' and '$ini'";
                // 
                $rPaci = $db->prepare("SELECT
                split_part(lab_aspira.pro, '-', 1) AS p1, split_part(lab_aspira.pro, '-', -1) AS p2, hc_paciente.dni, ape, nom, san, m_ets, don, hc_reprod.p_dni, hc_reprod.p_dni_het, hc_reprod.p_od, hc_reprod.p_dtri, hc_reprod.p_cic, hc_reprod.p_fiv, hc_reprod.p_icsi, hc_reprod.p_cri, hc_reprod.p_iiu, hc_reprod.p_don, hc_reprod.des_don, hc_reprod.des_dia, hc_reprod.pago_extras, hc_reprod.med, lab_aspira.pro, lab_aspira.tip, lab_aspira.vec, lab_aspira.dias, lab_aspira.fec,
                ABS(CAST(split_part(lab_aspira.pro, '-', 1) AS INTEGER)) AS abs_p1,
                ABS(CAST(split_part(lab_aspira.pro, '-', -1) AS INTEGER)) AS abs_p2
                FROM hc_antece, hc_paciente, lab_aspira, hc_reprod
                WHERE hc_reprod.estado = true and lab_aspira.estado is true and hc_paciente.dni = hc_antece.dni AND hc_paciente.dni=lab_aspira.dni AND hc_reprod.id=lab_aspira.rep AND lab_aspira.f_fin<>'1899-12-30' AND lab_aspira.tip<>'T'$between
                ORDER BY lab_aspira.updatex DESC, abs_p2 DESC, abs_p1 DESC
                LIMIT 50  offset 0");
                $rPaci->execute();

                ?>
                <style>
                    .ui-dialog-contain {
                        max-width: 100%;
                        margin: 1% auto 1%;
                        padding: 0;
                        position: relative;
                        top: -35px;
                    }

                    .mayuscula {
                        text-transform: uppercase;
                        font-size: small;
                    }

                    #cargador {
                        color: Red;
                    }
                </style>
                <div data-role="header" data-position="fixed">
                    <a href="lista_pro.php" rel="external"
                       class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-back">Regresar</a>
                    <h2>Protocolos Finalizados</h2>
                    <!-- <a href="lista_pro_f.php?todo=1" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all">Ver Todo</a> -->
                </div>
                <div class="ui-content" role="main">
                    <input class="filtro" id="filtroproc" data-type="search" placeholder="Digite desde 2 caracteres del número de procedimiento para empezar la búsqueda.">
                    <input class="filtro" id="filtronombres" data-type="search" placeholder="Digite desde 3 caracteres de los nombres y/o apellidos para empezar la búsqueda.">
                    <table id="detalleprocedimiento" data-role="table" data-filter="true" data-input=".filtro" class="table-stripe ui-responsive">
                        <thead id="prochead">
                            <tr>
                                <th align="center" width="110">Protocolo</th>
                                <th align="center">Paciente</th>
                                <th align="center">Pareja</th>
                                <th align="center">Donante</th>
                                <th align="center">Médico</th>
                                <th align="center">Procedimiento</th>
                            </tr>
                        </thead>
                        <tbody id="procbody">
                        <?php
                        while ($paci = $rPaci->fetch(PDO::FETCH_ASSOC)) {
                            $rPare = $db->prepare("SELECT upper(p_nom) p_nom, upper(p_ape) p_ape, p_san, p_m_ets FROM hc_pareja WHERE p_dni=?");
                            $rPare->execute(array($paci['p_dni']));
                            $pare = $rPare->fetch(PDO::FETCH_ASSOC);
                            $pareja="";
                            if ($rPare->rowCount() > 0) {
                                $pareja = $pare['p_ape'] . " " . $pare['p_nom'];
                            }

                            //$paci['dias']= es el proximo dia por lo tanto se resta 1 para tener el dia actual:
                            if ($paci['dias'] > 0) {
                                $paci['dias'] = $paci['dias'] - 1;
                                $diaActual = 'Dia ' . $paci['dias'];
                            } else $diaActual = 'Dia 0'; ?>
                            <tr>
                                <th>
                                    <a href='<?php echo "le_aspi" . $paci['dias'] . ".php?id=" . $paci['pro']; ?>' rel="external" target='_blank'>
                                        <?php echo $paci['tip'] . '-' . $paci['pro'] . '-' . $paci['vec']; ?>
                                    </a><br>
                                    <span><?php echo date("d-m-Y", strtotime($paci['fec'])); ?></span>
                                </th>
                                <td>
                                    <?php echo $paci['ape'] . ' ' . $paci['nom']; ?>
                                    <?php if (strpos($paci['san'], "-") !== false) echo " <b>(SANGRE NEGATIVA) </b>";
                                    if (strpos($paci['m_ets'], "VIH") !== false) echo " <b>(VIH) </b>";
                                    if (strpos($paci['m_ets'], "Hepatitis C") !== false) echo " <b>(Hepatitis C) </b>"; ?>
                                    <br>
                                    <span>
                                        <a href="info_r.php?a=<?php echo $paci['pro'] . "&b=" . $paci['dni'] . "&c=" . $paci['p_dni']; ?>" target="new" style="color:#48F06A">info</a><br>
                                        <a href="info_r1.php?a=<?php echo $paci['pro'] . "&b=" . $paci['dni'] . "&c=" . $paci['p_dni']; ?>" target="new">informe antiguo</a>
                                    </span>
                                </td>
                                <td>
                                    <?php echo $pareja; ?>
                                    <?php
                                    if (isset($pare['p_san']) && strpos($pare['p_san'], "-") !== false) echo " <b>(SANGRE NEGATIVA) </b>";
                                    if (isset($pare['p_m_ets']) && strpos($pare['p_m_ets'], "VIH") !== false) echo " <b>(VIH) </b>";
                                    if (isset($pare['p_m_ets']) && strpos($pare['p_m_ets'], "Hepatitis C") !== false) echo " <b>(Hepatitis C) </b>";
                                    if ($paci['p_dni_het'] <> "") echo " <b>(HETEROLOGO) </b>"; ?></td>
                                <td>
                                    <?php if ($paci['p_od'] <> '') {
                                        $rDon = $db->prepare("SELECT dni,nom,ape FROM hc_paciente WHERE dni=?");
                                        $rDon->execute(array($paci['p_od']));
                                        $don = $rDon->fetch(PDO::FETCH_ASSOC);
                                        echo $don['ape'] . " " . $don['nom'];
                                    } else if ($paci['don'] == 'D') echo 'Si'; else echo 'No'; ?>
                                </td>
                                <td><?php echo $paci['med']; ?></td>
                                <td><?php //echo $diaActual; ?>
                                    <?php
                                    if ($paci['p_dtri'] >= 1) echo "DUAL TRIGGER<br>";
                                    if ($paci['p_cic'] >= 1) echo "CICLO NATURAL<br>";
                                    if ($paci['p_fiv'] >= 1) echo "FIV<br>";
                                    if ($paci['p_icsi'] >= 1) echo $_ENV["VAR_ICSI"] . "<br>";
                                    if ($paci['p_od'] <> '') echo "OD FRESCO<br>";
                                    if ($paci['p_cri'] >= 1) echo "CRIO ÓVULOS<br>";
                                    if ($paci['p_iiu'] >= 1) echo "IIU<br>";
                                    if ($paci['p_don'] == 1) echo "DONACIÓN FRESCO<br>";
                                    if ($paci['des_don'] == null and $paci['des_dia'] >= 1) echo "TED<br>";
                                    if ($paci['des_don'] == null and $paci['des_dia'] === 0) echo "<small>Descongelación Ovulos Propios</small><br>";
                                    if ($paci['des_don'] <> null and $paci['des_dia'] >= 1) echo "EMBRIODONACIÓN<br>";
                                    if ($paci['des_don'] <> null and $paci['des_dia'] === 0) echo "<small>Descongelación Ovulos Donados</small><br>";
                                    if (strpos($paci['pago_extras'], "EMBRYOSCOPE") !== false) { ?>
                                        <small>EMBRYOSCOPE
                                        <?php
                                        if (file_exists("emb_pic/embryoscope_" . $paci['pro'] . ".mp4"))
                                            echo "<a href='archivos_hcpacientes.php?idEmb=embryoscope_" . $paci['pro'] . ".mp4' target='new'>(Video)</a>";
                                        if (file_exists("emb_pic/embryoscope_" . $paci['pro'] . ".pdf"))
                                            echo "<a href='archivos_hcpacientes.php?idEmb=embryoscope_" . $paci['pro'] . ".pdf' target='new'>(PDF)</a>"; ?>
                                        </small>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php
                        // break;
                        } ?>
                        </tbody>
                    </table>
                </div>
                <div data-role="footer" data-position="fixed" id="footer" style="text-align:center;">
                    Número de protocólos finalizados: <?php echo $rPaci->rowCount(); ?>
                </div>
            </div>
        </div>
    </form>
</body>
</html>