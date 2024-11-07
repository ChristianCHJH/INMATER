<!DOCTYPE HTML>
<html>
<head>
<?php
   include 'seguridad_login.php'
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="_themes/tema_inmater.min.css"/>
    <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css"/>
    <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css"/>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>

    <style>
        .enlinea div {
            display: inline-block;
            vertical-align: middle;
        }

        .peke .ui-input-text {
            width: 80px !important;
        }

        .scroll_h {
            overflow-x: scroll;
            overflow-y: hidden;
            white-space: nowrap;
        }
    </style>

</head>
<body>
    <div data-role="page" class="ui-responsive-panel" id="le_aspi9" data-dialog="true">
        <?php
        if (isset($_POST['des_dia']) and isset($_POST['nuevo_pro']) and !empty($_POST['nuevo_pro']) and isset($_POST['cont']) and $_POST['cont'] > 0) {
            Descongela_Ovo_Emb($_POST['des_dia'], $_POST['nuevo_pro'] . "-" . date("y"), $_POST['dni'], $_POST['tip'], $_POST['rep'], $_POST['cont'], $_POST['obs_med']);
        }

        if ($_GET['rep'] <> "") {
            $rep = $_GET['rep'];
            $rPaci = $db->prepare(
                "SELECT
                hc_paciente.dni, hc_paciente.ape, hc_paciente.nom, hc_reprod.des_dia, hc_reprod.des_don, hc_reprod.obs
                FROM hc_paciente, hc_reprod
                WHERE hc_reprod.estado = true and hc_paciente.dni=hc_reprod.dni AND hc_reprod.id=?");
            $rPaci->execute(array($rep));
            $paci = $rPaci->fetch(PDO::FETCH_ASSOC);

            $dia = $paci['des_dia'];

            if ($paci['des_don'] <> '') {
                $tip = "R";
                $dni = $paci['des_don'];
                $rDon = $db->prepare("SELECT ape,nom FROM hc_paciente WHERE dni=?");
                $rDon->execute(array($dni));
                $don = $rDon->fetch(PDO::FETCH_ASSOC);
                $donante = "(DONANTE: " . $don['ape'] . " " . $don['nom'] . ")";
            } else {
                $tip = "P";
                $dni = $paci['dni'];
                $donante = "";
                // solo son R para descongelacion TED con ciclo previo de descongelacion
                $consulta = $db->prepare(
                    "SELECT
                    a.*
                    from hc_reprod h
                    inner join lab_aspira a on a.rep = h.id and a.estado is true
                    inner join lab_aspira_dias d on d.pro = a.pro and d.des <> 1 and (d.adju is null or d.adju='' or d.adju=h.dni) and (d.d6f_cic='C' or d.d5f_cic='C' or d.d4f_cic='C' or d.d3f_cic='C' or d.d2f_cic='C') and d.estado is true
                    where h.estado = true and h.dni = ? and h.p_od <> '' and h.cancela <> 1
                ");

                $consulta->execute(array($dni));

                if ($consulta->rowCount() > 0) {
                    $tip = "R";
                }

                // 
                $consulta = $db->prepare(
                    "SELECT
                    a.*
                    from hc_reprod h
                    inner join lab_aspira a on a.rep = h.id and a.estado is true
                    inner join lab_aspira_dias d on d.pro = a.pro and d.des <> 1 and (d.adju is null or d.adju='' or d.adju=h.dni) and (d.d6f_cic='C' or d.d5f_cic='C' or d.d4f_cic='C' or d.d3f_cic='C' or d.d2f_cic='C') and d.estado is true
                    where h.estado = true and h.dni = ? and h.des_don is not null and h.des_dia = 0 and h.cancela <> 1"
                );

                $consulta->execute(array($dni));
                if ($consulta->rowCount() > 0) {
                    $tip = "R";
                }
            }
            ?>

            <style>
                .ui-dialog-contain {
                    max-width: 1200px;
                    margin: 1% auto 1%;
                    padding: 0;
                    position: relative;
                    top: -35px;
                }

                .varillas_cel input[type="checkbox"] { display:none; }
                .varillas_cel td {
                    width: 80px;
                }

                .varillas_cel tr:last-child {
                    display: none;
                }

            </style>

            <script>
                $(document).ready(function () {
                    $('#form2').submit(function () {
                        if (confirm("Presione ACEPTAR para guardar, los cambios seran IRREVERSIBLES por Ud.")) {
                            return true;
                        } else {
                            return false;
                        }
                    });
                });
            </script>

            <div data-role="header" data-position="fixed">
                <h3>Descongelación <?php if ($paci['des_don'] == null and $dia === 0) echo "ÓVULOS PROPIOS";
                    if ($paci['des_don'] == null and $dia >= 1) echo "TED (Dia " . $dia . ")";
                    if ($paci['des_don'] <> null and $dia === 0) echo "ÓVULOS DONADOS";
                    if ($paci['des_don'] <> null and $dia >= 1) echo "EMBRIODONACIÓN (Dia " . $dia . ")"; ?></h3>
            </div>
            <div class="ui-content" role="main">
                <form action="" method="post" data-ajax="false" id="form2">
                    <input type="hidden" name="rep" value="<?php echo $rep; ?>">
                    <input type="hidden" name="dni" value="<?php echo $paci['dni']; ?>">
                    <!--DNI del paciente (No del donante)-->
                    <input type="hidden" name="des_dia" value="<?php echo $dia; ?>">

                    <div class="enlinea ui-bar-a" style="margin: 0 auto;font-size:small;text-align:center;">
                        PACIENTE:
                        <small> <?php echo $paci['ape'] . " " . $paci['nom']; ?></small>
                        N. Protocólo
                        <span><input type="hidden" name="tip" value="<?php echo $tip; ?>" required><?php echo $tip; ?>-</span>
                        <span class="peke"><input type="number" name="nuevo_pro" min="0" required><?php echo "-" . date("y"); ?></span>
                        <a href="e_paci.php?id=<?php echo $paci['dni']; ?>" target="_blank" rel="external" class="ui-btn ui-mini ui-btn-inline">Historia Clinica</a>
                    </div>
                    <?php echo '<p class="ui-bar-b" align="center">Detalle CRIO ' . $donante . '</p>';

                    if ($dia > 1) $ovo_emb = 'Embrión'; else $ovo_emb = 'Ovulo';

                    echo '<div class="scroll_h">';

                    $rAsp = $db->prepare(
                        "SELECT
                        lab_aspira.pro, lab_aspira.fec, lab_aspira.book, lab_aspira.hoja, hc_reprod.dni, hc_reprod.p_dni, hc_reprod.p_dni_het, hc_reprod.des_dia
                        FROM hc_reprod, lab_aspira
                        WHERE hc_reprod.estado = true and hc_reprod.id=lab_aspira.rep and lab_aspira.estado is true AND lab_aspira.dni=?");
                    $rAsp->execute(array($dni));
                    $c = 0;

                    while ($asp = $rAsp->fetch(PDO::FETCH_ASSOC)) {
                        if ($dia > 1) {
                            $rRes = $db->prepare("SELECT * FROM lab_aspira_dias WHERE pro=? and estado is true AND des<>1 AND (adju is null OR adju='' OR adju=?) AND (d6f_cic='C' OR d5f_cic='C' OR d4f_cic='C' OR d3f_cic='C' OR d2f_cic='C') order by ovo");
                        } else {
                            $rRes = $db->prepare("SELECT * FROM lab_aspira_dias WHERE pro=? and estado is true AND des<>1 AND (adju is null OR adju='' OR adju=?) AND d0f_cic='C' order by ovo");
                        }

                        $rRes->execute(array($asp['pro'], $paci['dni']));

                        if ($rRes->rowCount() > 0) {
                            $ubica = '';
                            echo '<span class="ui-bar-b">Protocolo: ' . $asp['pro'] . ' (' . date("d-m-Y", strtotime($asp['fec'])) . ') Cuaderno:' . $asp['book'] . ' Hoja:' . $asp['hoja'] . '</span> <a href="info_r.php?a=' . $asp['pro'] . '&b=' . $asp['dni'] . '&c=' . $asp['p_dni'] . '" target="new">Ver Informe</a>';
                            echo '<table width="100%" cellpadding="5" style="text-align:center;font-size: small;border-collapse: collapse;border: 2px solid #72a2aa;">';
                            echo '<tr style="border: 1px solid #72a2aa;"><th>ID ' . $ovo_emb . '</th>';

                            if ($dia > 1) echo '<th>DIA 2</th><th>DIA 3</th><th>DIA 4</th><th>DIA 5</th><th>DIA 6</th><th>NGS</th><th>MitoScore</th><th>Prioridad<br>transferencia</th>';

                            echo '<th>PAJUELA</th><th></th></tr>';

                            while ($res = $rRes->fetch(PDO::FETCH_ASSOC)) {
                                $c++;
                                if ($ubica == $res['t'] . '-' . $res['c'] . '-' . $res['g'] . '-' . $res['p']) $borde = ''; else $borde = 'style="border-top: 1px solid #72a2aa;"';
                                echo '<tr ' . $borde . '><td>' . $res['ovo'] . '</td>';
                                if ($dia > 1) {
                                    if ($res['d2f_cic'] <> '') {
                                        echo '<td>' . $res['d2cel'] . '-' . $res['d2fra'] . '%-' . $res['d2sim'] . '</td>';
                                    } else echo '<td>-</td>';
                                    if ($res['d3f_cic'] <> '') {
                                        echo '<td>' . $res['d3cel'] . '-' . $res['d3fra'] . '%-' . $res['d3sim'] . '</td>';
                                    } else echo '<td>-</td>';
                                    if ($res['d4f_cic'] <> '') {
                                        echo '<td>' . $res['d4cel'] . '-' . $res['d4fra'] . '%-' . $res['d4sim'] . '</td>';
                                    } else echo '<td>-</td>';
                                    if ($res['d5f_cic'] <> '') {
                                        echo '<td>' . $res['d5cel'] . '</td>';
                                    } else echo '<td>-</td>';
                                    if ($res['d6f_cic'] <> '') {
                                        echo '<td>' . $res['d6cel'] . '</td>';
                                    } else echo '<td>-</td>';
                                    $ngs = '-';
                                    $ngs3 = '';
                                    if ($res['ngs1'] == 1) $ngs = 'Normal';
                                    if ($res['ngs1'] == 2) $ngs = '<font color="red">Anormal</font>';
                                    if ($res['ngs1'] == 3) $ngs = 'NR';
                                    if ($res['ngs1'] == 4) $ngs = 'Mosaico';
                                    if ($res['ngs3'] == 1) $ngs3 = ' (H)';
                                    if ($res['ngs3'] == 2) $ngs3 = ' (M)';
                                    if ($res['ngs3'] == 3) $ngs3 = ' -';
                                    echo '<td><a href="archivos_hcpacientes.php?idArchivo=ngs_' . $asp['pro'] .'" target="new">' . $ngs . $ngs3 . '</a></td>';
                                    echo '<td>' . (!!$res['valores_mitoscore'] ? $res['valores_mitoscore'] : '-' ) . '</td>';
                                    echo '<td>' . (!!$res['prioridad_transferencia'] ? $res['prioridad_transferencia'] : '-') . '</td>';
                                }

                                $ubica = $res['t'] . '-' . $res['c'] . '-' . $res['g'] . '-' . $res['p'];
                                $color = '';

                                if ($res['col'] == 5) $color = '<br><span style="font-size: x-small;">Verde</span>';
                                if ($res['col'] == 4) $color = '<br><span style="font-size: x-small;">Rosado</span>';
                                if ($res['col'] == 3) $color = '<br><span style="font-size: x-small;">Blanco</span>';
                                if ($res['col'] == 2) $color = '<br><span style="font-size: x-small;">Amarillo</span>';
                                if ($res['col'] == 1) $color = '<br><span style="font-size: x-small;">Azul</span>';
                                echo '<td>' . $ubica . $color . '</td>';
                                if ($res['adju'] == '') {
                                    $check = '';
                                    $reserva = '';
                                } else {
                                    $reserva = '<span style="color: #EC511E;font-size: x-small;">RESERVADO</span>';
                                    $check = 'checked';
                                }
                                echo '<td align="left"><input type="checkbox" name="c' . $c . '" id="c' . $c . '"  value="' . $asp['pro'] . '|' . $res['ovo'] . '" data-role="none" ' . $check . '>' . $reserva . '</td></tr>';

                            }
                            echo '</table>';
                        }
                    }
                    echo '</div>';
                    echo '<input type="hidden" name="cont" id="cont" value=' . $c . '>'; ?>
                    <p>Observaciones:<textarea name="obs_med" id="obs_med"><?php echo $paci['obs']; ?></textarea></p>
                    <input type="submit" name="guardar" value="GUARDAR" data-icon="check" data-iconpos="left" data-mini="true" data-theme="b" data-inline="true"/>
                </form>
            </div>
        <?php } ?>
    </div>
</body>
</html>