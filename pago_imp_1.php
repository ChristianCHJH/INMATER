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
</head>
<body>
<?php
if ($_GET['id'] <> "" and $_GET['t'] <> "") {
    $Rpop = $db->prepare("SELECT * FROM recibos WHERE id=? AND tip=?");
    $Rpop->execute(array($_GET['id'], $_GET['t']));
    $pop = $Rpop->fetch(PDO::FETCH_ASSOC); ?>

    <div data-role="page" class="ui-responsive-panel" id="pago_imp" data-dialog="true">
        <script type="text/javascript">
            function PrintElem(elem) {
                var data = $(elem).html();
                var mywindow = window.open('', 'Imprimir', 'height=400,width=400');
                mywindow.document.write('<html><head><title>Imprimir</title>');
                mywindow.document.write('<link rel="stylesheet" href="css/jquery.mobile-1.4.5.min.css" />');
                mywindow.document.write('<style> @page {margin: 0px 0px 0px 5px;} table td + td {text-align:right;}</style>');
                mywindow.document.write('</head><body>');
                mywindow.document.write(data);
                mywindow.document.write('<script type="text/javascript">window.print();<' + '/script>');
                mywindow.document.write('</body></html>');
                return true;
            }

        </script>
        <style>
            li p {
                overflow: visible !important;
                white-space: normal !important;
            }

            .ui-dialog-contain {
                max-width: 400px;
                margin: 1% auto 1%;
                padding: 0;
                position: relative;
                top: -35px;
            }
            /*table td + td {
                text-align: right;
            }*/
            table td + td + td{
                text-align: right;
            }
        </style>
        <div data-role="header" data-theme="b" data-position="fixed">
            <a href="lista.php" rel="external"
               class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>
            <a href="javascript:PrintElem('#imprimeRec')" data-role="button" data-mini="true" data-inline="true"
               rel="external" data-theme="a">Imprimir</a>
            <h3>Recibo</h3>
        </div>
        <div class="ui-content" role="main" id="imprimeRec">
            <?php if ($pop['anu'] == 1) { ?>
            <div style="position: absolute;opacity: 0.4;float: left;text-align: center">
                <h1>ANULADO</h1>
                <img src="https://issues.jenkins-ci.org/images/icons/priorities/blocker.svg" width="250">
                <h1>ANULADO</h1>
            </div>
            <?php } ?>
            <div style="font-size:13px; width:250px;">
                <?php
                echo "
                    <div style='text-align:center;'>
                        <i>
                            Especialistas en Medicina Reproductiva SAC<br>
                            RUC 20544478096<br>
                            Av.Guardia Civil 655 Urb. Corpac - San borja - Lima - Lima - Tlf.4762727
                        </i><br><br>
                        <b>";
                if ($pop['tip'] == 1) echo "TICKET BOLETA";
                if ($pop['tip'] == 2) echo "TICKET FACTURA";
                echo '<br>001-' . sprintf('%05d', $pop['id']) . '</b></div><br>';
                echo 'Señor(a): ' . $pop['nom'] . '<br>';
                if ($pop['dni'] <> '') echo 'N° Documento: ' . $pop['dni'] . '<br>';
                if ($pop['med'] <> '') echo 'Médico: ' . $pop['med'] . '<br>';
                echo 'F. Emisión: ' . date("d-m-Y H:i:s", strtotime($pop['fec'])) .'<br>';
                if ($pop['man_fin'] <> '1899-12-30') echo 'Mantenimiento hasta: ' . date("d-m-Y", strtotime($pop['man_fin'])) . '<br>';
                if ($pop['tip'] == 2) {
                    if ($pop['raz'] <> '') print('Razón Social: ' . $pop['raz'] . '<br>');
                    print('RUC: ' . $pop['ruc'] . '<br>');
                    if ($pop['direccionfiscal'] <> '') print('Dirección Fiscal: '.mb_strtoupper($pop['direccionfiscal']).'<br>');
                }
                if ($pop['t_ser'] == 1) $titu = 'REPRODUCCION ASISTIDA';
                if ($pop['t_ser'] == 2) $titu = 'ANDROLOGIA';
                if ($pop['t_ser'] == 3) $titu = 'PROCEDIMIENTO SALA: ' . $pop['pak'];
                if ($pop['t_ser'] == 4) $titu = 'ANALISIS SANGRE';
                if ($pop['t_ser'] == 5) $titu = 'PERFIL: ' . $pop['pak'];
                if ($pop['t_ser'] == 6) $titu = 'ECOGRAFIA';
                if ($pop['t_ser'] == 7) $titu = 'ADICIONALES';

                if ($pop['t1'] == 1) $t1 = 'EFECTIVO';
                if ($pop['t1'] == 2) $t1 = 'TARJETA';
                if ($pop['t1'] == 3) $t1 = 'DEPOSITO';
                if ($pop['t1'] == 4) $t1 = 'TARJETA VISA';
                if ($pop['t1'] == 5) $t1 = 'TARJETA MASTERCARD';
                if ($pop['m1'] == 1) $m1 = '$'; else $m1 = 'S/.';

                echo '<br>
                <table style="font-size:12px;border-collapse: collapse;">
                    <tr><td colspan="2" align="center">' . $titu . '</td></tr>
                    <tr style="border-bottom:1pt solid black;border-top:1pt solid black;font-weight: bold;">
                        <td>CÓDIGO</td>
                        <td>DESCRIPCION</td>
                        <td>TOTAL</td>
                    </tr>
                    ' . $pop['ser'];
                if ($pop['t_ser'] == 1 or $pop['t_ser'] == 2 or $pop['t_ser'] == 3) {
                    if ($pop['mon'] == 1) $mon = "$";
                    else $mon = "S/.";
                } else {
                    if ($pop['mon'] == 1) $mon = "S/.";
                    else $mon = "$";
                }


                if ($pop['tip'] == 2) {
                    echo '<tr><td>SUBTOTAL</td><td></td><td>' . $mon . number_format(round($pop['tot'] / 1.18, 2), 2) . '</td></tr>';
                    echo '<tr><td>I.G.V (18%)</td><td></td><td>' . $mon . number_format(round($pop['tot'] - ($pop['tot'] / 1.18), 2), 2) . '</td></tr>';
                }

                echo '
                <tr style="font-weight: bold;">
                    <td>TOTAL</td>
                    <td></td>
                    <td>' . $mon . number_format($pop['tot'], 2) . '</td>
                </tr>
                <tr style="font-weight: bold;">
                    <td>MEDIO DE PAGO:</td><td></td>
                </tr>
                <tr>
                    <td>' . $t1 . '</td><td></td><td>' . $m1 . number_format($pop['p1'], 2) . '</td>
                </tr>';
                if ($pop['t2'] <> "") {
                    if ($pop['t2'] == 1) $t2 = 'EFECTIVO';
                    if ($pop['t2'] == 2) $t2 = 'TARJETA';
                    if ($pop['t2'] == 3) $t2 = 'DEPOSITO';
                    if ($pop['t2'] == 4) $t2 = 'TARJETA VISA';
                    if ($pop['t2'] == 5) $t2 = 'TARJETA MASTERCARD';
                    if ($pop['m2'] == 1) $m2 = '$'; else $m2 = 'S/.';
                    echo '<tr><td>' . $t2 . '</td><td>' . $m2 . number_format($pop['p2'], 2) . '</td></tr>';
                }
                if ($pop['t3'] <> "") {
                    if ($pop['t3'] == 1) $t3 = 'EFECTIVO';
                    if ($pop['t3'] == 2) $t3 = 'TARJETA';
                    if ($pop['t3'] == 3) $t3 = 'DEPOSITO';
                    if ($pop['t3'] == 4) $t3 = 'TARJETA VISA';
                    if ($pop['t3'] == 5) $t3 = 'TARJETA MASTERCARD';
                    if ($pop['m3'] == 1) $m3 = '$'; else $m3 = 'S/.';
                    echo '<tr><td>' . $t3 . '</td><td>' . $m3 . number_format($pop['p3'], 2) . '</td></tr>';
                }
                echo '</table><br>';
                /*
                $pattern = "/<td>(.*?)<\/td>/";
                preg_match_all($pattern, $pop['ser'], $matches);

                if (!empty($matches)) {
                    foreach ($matches[1] as $value) {
                        echo $value."<br>";
                    }
                }
                */
                if ($pop['mon'] > 1) echo "Tipo de Cambio: " . $pop['mon']; ?>
                <?php
                    if (!empty($pop['comentarios'])) {
                        print('
                        <table style="font-size:11px;border-collapse: collapse;">
                            <tr><td>Ref.: '.$pop['comentarios'].'</td></tr>
                        </table>');
                    }
                        
                ?>
                <p align="center" style="font-size:10px;">Representación impresa de
                    la <?php if ($pop['tip'] == 1) echo "Boleta";
                    if ($pop['tip'] == 2) echo "Factura"; ?> de Venta.<br>Autorización № 0023845136723 Serie: FFCF297034 - SUNAT<br>
                    Cualquier consulta puede comunicarse al 4762727
                </p>
            </div>
        </div>
    </div>
    <?php
}
?>
    <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>
</html>