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
</head>

<body>
<div data-role="page" class="ui-responsive-panel" id="pago_ser" data-dialog="true">

    <?php if ($_POST['conf'] <> "") {
        $stmt = $db->prepare("DELETE FROM recibo_serv WHERE id=?");
        $stmt->execute(array($_POST['conf']));
    }

    if ($_POST['nom'] <> "" and $_POST['costo'] <> "" and $_POST['tip'] <> ""  and $_POST['cc'] <> "") {

        Recibo_serv($_POST['nom'], $_POST['pak'], $_POST['costo'], $_POST['cc'], $_POST['tip'], $_POST['cod']);

    }

    $Rpop = $db->prepare("SELECT * FROM recibo_serv WHERE tip=? ORDER BY pak ASC");
    $Rpop->execute(array($_GET['s']));

    if ($_GET['s']==5) {
        $rPaci = $db->prepare("SELECT cod,nom,costo FROM recibo_serv WHERE tip=4");
        $rPaci->execute();
    } ?>
    <style>
        .ui-dialog-contain {
            max-width: 1500px;
            margin: 1% auto 1%;
            padding: 0;
            position: relative;
            top: -35px;
        }

        .peke2 .ui-input-text {
            width: 120px !important;
        }

        .scroll_h {
            overflow-x: scroll;
            overflow-y: hidden;
            white-space: nowrap;
        }

        #alerta {
            background-color: #FF9;
            margin: 0 auto;
            text-align: center;
            padding: 4px;
        }
    </style>
    <script>

        $(document).ready(function () {

            $(".ui-input-search input").attr("id", "paci_nom");
            $("#paci_nom").prop('required', true);

            $('.paci_insert').click(function (e) {
                $('#paci_nom').val($(this).text());
                $('#nom').val($(this).text());
                $('#cod').val($(this).attr("cod"));
                $('#costo').val($(this).attr("costo"));
                $('#paci_nom').textinput('refresh');
                $('.fil_paci li').addClass('ui-screen-hidden');
                $('#paci_nom').focus();
            });

            $('.ui-input-search .ui-input-clear').click(function (e) {
                $('#nom').val('');
                $('#cod').val('');
                $('#costo').val('');
            });
        });
        function anular(x) {
            document.form2.conf.value = x;
            document.form2.nom.value = ""; //para que no inserte registros
            document.form2.submit();
        }

    </script>
    <div data-role="header" data-position="fixed">
        <a href="lista.php" rel="external"
           class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>
        <h3>ADMIN <?php if ($_GET['s'] == 1) echo 'Reproducción Asistida';
            if ($_GET['s'] == 2) echo 'Andrología';
            if ($_GET['s'] == 3) echo 'Procedimientos Sala';
            if ($_GET['s'] == 4) echo 'Analisis Sangre';
            if ($_GET['s'] == 5) echo 'Perfiles';
            if ($_GET['s'] == 6) echo 'Ecografía';
            if ($_GET['s'] == 7) echo 'Adicionales'; ?></h3>
    </div><!-- /header -->

    <div class="ui-content" role="main">
        <form action="pago_ser.php?s=<?php echo $_GET['s']; ?>" method="post" data-ajax="false" name="form2">
            <input type="hidden" name="conf">
            <input type="hidden" name="tip" value="<?php echo $_GET['s']; ?>">
            <input type="hidden" name="cod" id="cod">
            <input type="hidden" name="nom" id="nom">
            <div class="ui-bar ui-bar-a">
                <table style="margin: 0 auto;" width="100%">
                    <tr>
                        <td width="48%">Nombre
                            <?php if($_GET['s'] == 5 ) { ?>
                            <ul data-role="listview" data-theme="c" data-inset="true" data-filter="true"
                                data-filter-reveal="true" data-filter-placeholder="Buscar aqui.."
                                data-mini="true"
                                class="fil_paci">
                                <?php while ($paci = $rPaci->fetch(PDO::FETCH_ASSOC)) { ?>
                                    <li><a href="#" class="paci_insert" cod="<?php echo $paci['cod']; ?>" costo="<?php echo $paci['costo']; ?>"><?php echo '<small>' . $paci['nom'] . '</small>'; ?></a></li>
                                <?php } ?>
                            </ul>
                            <?php } else { ?>
                            <input type="text" name="nom" id="nom" data-mini="true" required/>
                            <?php } ?>

                        </td>
                        <td width="5%"><?php if ($_GET['s'] == 1 or $_GET['s'] == 2 or $_GET['s'] == 3 or $_GET['s'] == 7) { ?>
                            <select name="cc" id="cc" required>
                                <option value="" selected>CC</option>
                                <?php if($_GET['s'] == 1 ) { ?><option value="01">01</option><?php } ?>
                                <option value="03">03</option>
                                <?php if($_GET['s'] == 3 ) { ?><option value="02">02</option><?php } ?>
                                <?php if($_GET['s'] == 2 ) { ?><option value="04">04</option><?php } ?>
                                <option value="08">08</option>
                                <option value="09">09</option>
                            </select>
                            <?php }
                            if ($_GET['s'] == 4) echo '<input type="hidden" name="cc" value="07">';
                            if ($_GET['s'] == 5) echo '<input type="hidden" name="cc" value="07">';
                            if ($_GET['s'] == 6) echo '<input type="hidden" name="cc" value="06">'; ?>
                        </td>
                        <td width="4%">Costo
                            (<?php if ($_GET['s'] == 1 or $_GET['s'] == 2 or $_GET['s'] == 3) echo "$"; else echo "S/. "; ?>
                            )
                        </td>
                        <td width="12%" class="peke2"><input type="number" step="any" min="1" name="costo" id="costo"
                                                             data-mini="true" required/></td>
                        <td width="24%"><?php if ($_GET['s'] == 3 or $_GET['s'] == 5) {
                                $Rpak = $db->prepare("SELECT DISTINCT pak FROM recibo_serv WHERE tip=? ORDER BY pak ASC");
                                $Rpak->execute(array($_GET['s'])); ?>
                                <input list="pak" name="pak" placeholder="Paquete.." autocomplete="off" required>
                                <datalist id="pak">
                                    <?php while ($pak = $Rpak->fetch(PDO::FETCH_ASSOC)) {
                                        echo '<option value="' . $pak['pak'] . '">';
                                    } ?>
                                </datalist>

                            <?php } ?>
                            <?php if($_GET['s'] == 4 ) { ?>
                                <input type="number" step="any" min="1000" name="cod" id="cod"
                                       data-mini="true" placeholder="CODIGO" required/>
                            <?php } ?>
                        </td>
                        <td width="7%">
                            <input type="Submit" name="guardar" value="AGREGAR" data-icon="check" data-iconpos="left"
                                   data-mini="true" data-theme="b" data-inline="true"/>
                        </td>
                    </tr>

                </table>
            </div>

            <ul data-role="listview" data-filter="true" data-filter-placeholder="Filtro..." data-inset="true"
                data-mini="true">
                <?php while ($pop = $Rpop->fetch(PDO::FETCH_ASSOC)) {
                    if ($pop['tip'] == 1 or $pop['tip'] == 2 or $pop['tip'] == 3) $mon = "$ "; else $mon = "S/. "; ?>
                    <li>CC<?php echo $pop['cc'];?>
                        <small><?php echo $pop['nom'] . ' <b>(' . $mon . $pop['costo'] . ')</b>'; ?></small>
                        <span class="ui-li-count">
    <?php echo $pop['pak']; ?>
                            <a href="javascript:anular(<?php echo $pop['id']; ?>);">(borrar)</a>
    </span>
                    </li>
                <?php }

                if ($Rpop->rowCount() < 1)

                    echo '<p><h3 class="text_buscar">¡ No hay Servicios !</h3></p>';

                ?>
            </ul>
        </form>

    </div><!-- /content -->

</div><!-- /page -->
<script>
    $(function () {
        $("#alerta").prependTo(".ui-content");
        $('#alerta').delay(3000).fadeOut('slow');

    });//]]>
</script>
</body>
</html>