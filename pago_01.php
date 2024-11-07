<!DOCTYPE HTML>
<html>
<head>
<?php
   include 'seguridad_login.php';
    include('nusoap/lib/nusoap.php');
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/bootstrap.v4/bootstrap.min.css"/>
    <link rel="stylesheet" href="css/chosen.min.css">
    <link rel="stylesheet" href="css/global.css"/>
    <style>
        .controlgroup-textinput {
            padding-top: .10em;
            padding-bottom: .10em;
        }

        .enlinea div {
            display: inline-block;
            vertical-align: middle;
        }

        #ser {
            font-size: 12px;
        }
        .ui-dialog-contain {
            max-width: 1000px;
            margin: 1% auto 1%;
            padding: 0;
            position: relative;
            top: -35px;
        }

        .peke2 .ui-input-text {
            width: 60px !important;
        }

        .scroll_h {
            overflow-x: scroll;
            overflow-y: hidden;
            white-space: nowrap;
        }

        .controlgroup-textinput {
                padding-top: .22em;
                padding-bottom: .22em;
        }

        .NoEdita {
            pointer-events: none;
        }
    </style>
</head>
<body>
    <?php require ('_includes/menu_pagos.php'); ?>
    <div class="container" id="pago">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="lista.php">Inicio</a></li>
                <li class="breadcrumb-item">Facturación</li>
                <li class="breadcrumb-item">Nuevo</li>
                <li class="breadcrumb-item active" aria-current="page">Recibo</li>
            </ol>
        </nav>
        <?php
        if (isset($_POST['tip']) and !empty($_POST['tip']) and isset($_POST['fec']) and !empty($_POST['fec']) and isset($_POST['dni']) and !empty($_POST['dni']) and isset($_POST['nom']) and !empty($_POST['nom'])) {
            Recibo($_POST['idx'], date("Y-m-d H:i:s"), $_POST['dni'], $_POST['nom'], $_POST['med'], $_POST['sede'], $_POST['tip'], $_POST['ruc'], $_POST['raz'], $_POST['t_ser'], $_POST['pak'], $_POST['ser'], $_POST['mon'], $_POST['tot'], $_POST['t1'], $_POST['m1'], $_POST['p1'], $_POST['t2'], $_POST['m2'], $_POST['p2'], $_POST['t3'], $_POST['m3'], $_POST['p3'], $_POST['man_ini'], $_POST['man_fin'], $_POST['cadena'], $login, $_POST['comentarios'], $_POST['direccionfiscal']);
        }

        $rMed = $db->prepare("SELECT nom FROM usuario WHERE role=1");
        $rMed->execute();

        $Rpop = $db->prepare("SELECT * FROM recibos WHERE id=? AND tip=?");
        $Rpop->execute(array($_GET['id'], $_GET['t']));
        $pop = $Rpop->fetch(PDO::FETCH_ASSOC); ?>
        <script>
            $(document).ready(function () {
                $("#tip").change(function () {
                    if ($(this).val() != '') {
                        $(".servicio").show();
                        if ($(this).val() == 2) {
                            $(".factura").show();
                        } else {
                            $(".factura").hide();
                        }
                    } else {
                        $(".servicio").hide();
                        $(".factura").hide();
                    }
                });

                var total = 0;

                if ($('#tot').val() > 0) { // calcula el igv y subtotal cada vez q abre la venana
                    $('#igv').html(($('#tot').val() - ($('#tot').val() / 1.18)).toFixed(2));
                    $('#subtot').html(($('#tot').val() / 1.18).toFixed(2));
                }
                $(".med_insert").change(function () {
                    // console.log("demo0");
                    var t_ser = $("#t_ser").val();
                    if (t_ser == 3 || t_ser == 5) {
                        if ($(this).val() != '') {
                            var pak = $(this).val()
                            var mon = $("#mon").val();
                            var tip = $("#tip").val();
                            $('#servicios').html('<h3>CARGANDO DATOS...</h3>');
                            $.post("le_tanque.php", {pak: pak, t_ser: t_ser, mon: mon, tip: tip}, function (data) {
                                var data = data.split('|');
                                $('#servicios').empty();
                                $('#ser').val(data[0]);
                                $("#servicios").append(data[0]);
                                $('#tot,#p1').val(data[1]);
                                $('#igv').html(data[2]);
                                $('#subtot').html(data[1] - data[2]);
                                $('#cadena').val(data[3]);
                            });
                        }
                    } else {
                        var str = $('#ser').val();
                        var items = $(this).val();
                        var idcc = $('option:selected', this).attr('id');
                        var cc = $('option:selected', this).attr('cc');
                        if (cc != '' && (t_ser == 1 || t_ser == 2 || t_ser == 7)) cc = 'CC' + cc + ':';
                        var n = str.indexOf(items);
                        var mante = items.indexOf("MANTENIMIENTO"); // verifca que exista esta palabra

                        if (n == -1) {	// no agrega duplicados
                            if (t_ser == 1 || t_ser == 2 || t_ser == 3)
                                var costo = $('option:selected', this).attr('costo') * $('#mon').val();
                            else
                                var costo = $('option:selected', this).attr('costo') / $('#mon').val();
                            if ($('#tip').val() == 2) costo = (costo / 1.18); //muestra costo unitario con igv
                            $('#ser').val(str + "<tr><td>" + idcc + "</td><td>" + items + "</td><td>" + costo.toFixed(2) + "</td></tr>");
                            $('#servicios').html(str + "<tr><td>" + idcc + "</td><td>" + items + "</td><td>" + costo.toFixed(2) + "</td></tr>");
                            if (t_ser == 1 || t_ser == 2 || t_ser == 3)
                                total = total + parseFloat($('option:selected', this).attr('costo') * $('#mon').val());
                            else
                                total = total + parseFloat($('option:selected', this).attr('costo') / $('#mon').val());
                            $('#tot,#p1').val(total.toFixed(2));
                            $('#igv').html((total - (total / 1.18)).toFixed(2));
                            $('#subtot').html((total / 1.18).toFixed(2));
                            $(this).prop('selectedIndex', 0);
                            $(this).selectmenu("refresh", true);
                        }

                        if (mante >= 0) {
                            $(".mantenimiento").show();
                            $('#man_ini,#man_fin').prop("required", true);
                        }
                    }
                });

                $("#razon").change(function () {
                    if ($(this).val()!=''){
                        var data = $(this).val().split('|');
                        $(this).val(data[0]);
                        $('#ruc').val(data[1]);
                        $('#direccionfiscal').val(data[2]);
                    }
                });

                $('.extra_insert').click(function (e) {
                    var str = $('#ser').val();
                    var items = $(this).attr('data');
                    var idcc = $(this).attr('id');
                    if ($('#cadena').val() == '') $('#cadena').val($(this).attr('cod'));
                    else
                        $('#cadena').val($('#cadena').val() + "," + $(this).attr('cod')); //genera la cadena para anglolab
                    var costo = $(this).attr('costo') / $('#mon').val(); // aqui siempre divide porque este servicio es solo para t_ser analisis de sangres
                    if ($('#tip').val() == 2) costo = (costo / 1.18); //muestra costo unitario con igv

                    $('#ser').val(str + "<tr><td>" + idcc + "</td><td>" + items + "</td><td>" + costo.toFixed(2) + "</td></tr>");
                    $('#servicios').html(str + "<tr><td>" + idcc + "</td><td>" + items + "</td><td>" + costo.toFixed(2) + "</td></tr>");
                    $('.fil_extra li').addClass('ui-screen-hidden');
                    total = total + parseFloat($(this).attr('costo') / $('#mon').val());
                    $('#tot,#p1').val(total.toFixed(2));
                    $('#igv').html((total - (total / 1.18)).toFixed(2));
                    $('#subtot').html((total / 1.18).toFixed(2));
                });


                $('#borrar').click(function (e) {
                    $('#ser').val('');
                    $('#servicios').empty();
                    $('#tot,#p1').val('');
                    $('#subtot,#igv').html('-');
                    $('#man_ini,#man_fin,#cadena').val('');
                    $('#man_ini,#man_fin').prop("required", false);
                    $(".mantenimiento").hide();
                    total = 0;
                });

                $("#cambio").change(function () {
                    if ($(this).attr("data") == 1 || $(this).attr("data") == 2 || $(this).attr("data") == 1) {
                        var mon1 = 'S/.';
                        var mon2 = '$';
                    } else {
                        var mon1 = '$';
                        var mon2 = 'S/.';
                    }
                    if ($(this).prop('checked')) {
                        $(".mon").show();
                        $('#labelmon').html(mon1);
                    } else {
                        $(".mon").hide();
                        $('#labelmon').html(mon2);
                        $('#mon').val(1);
                    }

                    $('#ser').val('');
                    $('#servicios').empty();
                    $('#tot,#p1').val('');
                    $('#subtot,#igv').html('-');
                    total = 0;
                });

            });
        </script>
        <script>
            $(document).ready(function () {
                <?php if ($pop['tip'] > 0) { ?>
                $(".servicio").show();
                $('#dni').val('<?php echo $pop['dni']; ?>');
                $('#nom').val('<?php echo $pop['nom']; ?>');
                <?php } ?>
                <?php if ($pop['tip'] == 2) { ?>
                $(".factura").show();
                <?php } ?>

                <?php if ($pop['man_ini'] && $pop['man_ini'] <> '1899-12-30') { ?>
                $(".mantenimiento").show();
                <?php } ?>

                $(".mon").hide();
                <?php if ($_GET['s'] == 1 || $_GET['s'] == 2 || $_GET['s'] == 3) {
                if ($pop['mon'] > 1) { ?>
                $('#labelmon').html('S/.');
                $(".mon").show();
                <?php } else { ?>
                $('#labelmon').html('$');
                <?php }
                } else {
                if ($pop['mon'] > 1) { ?>
                $('#labelmon').html('$');
                $(".mon").show();
                <?php } else { ?>
                $('#labelmon').html('S/.');
                <?php }
                } ?>

                if ($("#idx").val() == '') {
                    $('.carga_msj').html('<h3>CARGANDO PACIENTES...</h3>');
                    $.post("le_tanque.php", {carga_paci: 1}, function (data) {
                        $('.carga_msj').html('');
                        $(".carga_paci").append(data);
                        $('.ui-page').trigger('create'); //recarga los css del jqm
                    });
                }
            });

            $(document).on('click', '.paci_insert', function(ev){
                $(".carga_paci .ui-input-search input").attr("id", "paci_nom");
                $("#paci_nom").prop('required', true);

                $('#paci_nom').val($(this).text());
                $('#nom').val($(this).text());
                $('#dni').val($(this).attr("dni"));
                $('#paci_nom').textinput('refresh');
                $('.fil_paci li').addClass('ui-screen-hidden');
                $('#paci_nom').focus();
                $('#med').val('');
                med = $(this).attr("med");
                $.post("le_tanque.php", {med: med}, function (data) {
                    //$("#med select").val(data);
                    $("#med").val(data).change();
                    //$("#med").selectmenu( "refresh", true );
                });
            });
            $(document).on('click', '.carga_paci .ui-input-search .ui-input-clear', function(ev){
                $('#nom').val('');
                $('#dni').val('');
            });
            $(document).on('input paste', '.carga_paci .ui-input-search', function(e){
                var paciente = $('.carga_paci .ui-input-search :input')[0].value;
                if (paciente.length > 3) {
                    $.post("le_tanque.php", {carga_paci_det: paciente}, function (data) {
                        // $('.carga_msj').html('');
                        $(".carga_paci ul").html("");
                        $(".carga_paci ul").append(data);
                        $('.ui-page').trigger('create'); //recarga los css del jqm
                    });
                }
            });
        </script>
        <form action="" method="post" data-ajax="false" id="form2">
            <input type="hidden" name="idx" id="idx" value="<?php echo $_GET['id']; ?>">
            <input type="hidden" name="dni" id="dni" value="<?php echo $pop['dni']; ?>">
            <input type="hidden" name="nom" id="nom" value="<?php echo $pop['nom']; ?>">
            <input type="hidden" name="cadena" id="cadena">
            <input type="hidden" name="t_ser" id="t_ser" value="<?php echo $_GET['s']; ?>">
            <div class="card mb-3">
                <h5 class="card-header">Recibo <?php echo sprintf('%05d', $pop['id']) . ': ';
                    if ($_GET['s'] == 1) {echo 'Reproducción Asistida';}
                    if ($_GET['s'] == 2) {echo 'Andrología';}
                    if ($_GET['s'] == 3) {echo 'Procedimientos Sala';}
                    if ($_GET['s'] == 4) {echo 'Analisis Sangre';}
                    if ($_GET['s'] == 5) {echo 'Perfiles';}
                    if ($_GET['s'] == 6) {echo 'Ecografía';}
                    if ($_GET['s'] == 7) {echo 'Adicionales';} ?></h5>
                <div class="card-body">                    
                    <div class="form-group">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 input-group-sm">
                            <div class="input-group-prepend" class="carga_paci">
                                <span class="input-group-text">Paciente<?php if ($_GET['id'] <> '') echo $pop['nom']; else echo '<div class="carga_msj"></div>';?></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Fecha</span>
                                <!-- <input class="form-control form-control-sm" name="desde" type="number" value="<?php // echo $_POST['desde']; ?>" id="desde"> -->
                                <input name="fec" type="date" class="form-control form-control-sm" required id="fec" placeholder="Fecha" value="<?php if ($pop['fec']) {print(substr($pop['fec'], 0, 10));} else {echo date("Y-m-d");} ?>" data-mini="true">
                            </div>
                        </div>
                    </div>
                    <table width="100%" border="0" style="margin: 0 auto; font-size: small;" class="ui-bar-b">
                        <tr>
                            <td width="10%">
                                Fecha 
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class="enlinea">
                                    Médico
                                    <select name="med" id="med" data-mini="true" data-inline="true" required>
                                        <option value="" selected>Seleccionar</option>
                                        <?php while ($med = $rMed->fetch(PDO::FETCH_ASSOC)) { ?>
                                            <option value="<?php echo $med['nom']; ?>" <?php if ($med['nom'] == $pop['med']) echo "selected"; ?>><?php echo $med['nom']; ?></option>
                                        <?php } ?>
                                    </select>
                                    Sede
                                    <select name="sede" id="sede" data-mini="true" data-inline="true" required>
                                        <option value=0 selected>Clinica</option>
                                        <option value=1 <?php if ($pop['sede'] == 1) echo "selected"; ?>>Cono NORTE</option>
                                        <option value=2 <?php if ($pop['sede'] == 2) echo "selected"; ?>>Cono SUR</option>
                                        <option value=3 <?php if ($pop['sede'] == 3) echo "selected"; ?>>Tacna</option>
                                        <option value=4 <?php if ($pop['sede'] == 4) echo "selected"; ?>>Arequipa</option>
                                    </select>
                                    Tipo Recibo
                                    <?php if ($pop['tip'] > 0) {
                                        if ($pop['tip'] == 1) echo "BOLETA";
                                        if ($pop['tip'] == 2) echo "FACTURA"; ?>
                                        <input type="hidden" name="tip" id="tip" value="<?php echo $pop['tip']; ?>">
                                    <?php } else { ?>
                                        <select name="tip" required id="tip" data-mini="true">
                                            <option value="" selected>Seleccionar</option>
                                            <option value=1 <?php if ($pop['tip'] == 1) echo "selected"; ?>>BOLETA</option>
                                            <option value=2 <?php if ($pop['tip'] == 2) echo "selected"; ?>>FACTURA</option>
                                        </select>
                                    <?php } ?>
                                </div>
                            </td>
                            <td></td>
                        </tr>
                        <tr class="factura">
                            <td width="30%">
                                Razón Social
                                <input list="raz" name="raz" id="razon" placeholder="Buscar..." autocomplete="on" value="<?php echo $pop['raz']; ?>">
                                <datalist id="raz">
                                    <?php
                                        $consulta = $db->prepare("SELECT nombre, ruc, direccionfiscal from man_razonsocial_pagos where estado=1");
                                        $consulta->execute();

                                        while ($data = $consulta->fetch(PDO::FETCH_ASSOC)) {
                                            print('<option value="'.$data["nombre"]."|".$data["ruc"]."|".$data["direccionfiscal"].'">');
                                        } ?>
                                </datalist>
                            </td>
                            <td width="60%">
                                Dirección <input type="text" id="direccionfiscal" name="direccionfiscal" data-mini="true" value="<?php echo $pop['direccionfiscal']; ?>">
                            </td>
                            <td width="10%">
                                RUC<input name="ruc" type="text" id="ruc" data-mini="true" value="<?php echo $pop['ruc']; ?>" autocomplete="on">
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </form>
        <div class="ui-content" role="main">
            <div class="servicio">
                <table width="100%" border="0" align="center" style="margin: 0 auto; font-size: small;">
                    <tr>
                        <td colspan="3" align="center" class="ui-bar-c">LISTA DE SERVICIOS</td>
                    </tr>
                    <tr <?php if ($_GET['id'] <> '') {echo 'class="NoEdita"';} ?>>
                        <td width="30%" align="center" valign="top" id="contenedor_servicios">
                            <?php
                            if ($_GET['s'] == 1) {$rSer = $db->prepare("SELECT * FROM recibo_serv WHERE tip=1");}
                            if ($_GET['s'] == 2) {$rSer = $db->prepare("SELECT * FROM recibo_serv WHERE tip=2");}
                            if ($_GET['s'] == 3) {$rSer = $db->prepare("SELECT * FROM recibo_serv WHERE tip=3 ORDER BY pak ASC");}
                            if ($_GET['s'] == 4) {$rSer = $db->prepare("SELECT * FROM recibo_serv WHERE tip=4");}
                            if ($_GET['s'] == 5) {$rSer = $db->prepare("SELECT DISTINCT pak FROM recibo_serv WHERE tip=5 ORDER BY pak ASC");}
                            if ($_GET['s'] == 6) {$rSer = $db->prepare("SELECT * FROM recibo_serv WHERE tip=6");}
                            if ($_GET['s'] == 7) {$rSer = $db->prepare("SELECT * FROM recibo_serv WHERE tip=7");}
                            $rSer->execute();

                            if ($_GET['s'] == 1 || $_GET['s'] == 2 || $_GET['s'] == 3) {$moneda = "soles";}
                            else {$moneda = "dolares";} ?>
                            <label for="cambio">Cambiar a <?php echo $moneda; ?></label>
                            <input type="checkbox" name="cambio" id="cambio" data="<?php echo $_GET['s']; ?>" data-mini="true" <?php if ($pop['mon'] > 1) echo "checked"; ?>>
                            <div class="enlinea mon peke2">Tipo cambio:
                                <input type="number" step="any" min="1" name="mon" id="mon" data-mini="true" value=<?php if ($pop['mon'] > 1) echo $pop['mon']; else echo 1; ?>>
                            </div>

                            <?php if ($_GET['s'] == 4) { // usa filtro para buscar los analisis de sangre ya q son muchisimos---- ?>
                                <ul data-role="listview" data-theme="c" data-inset="true" data-filter="true" data-filter-reveal="true" data-filter-placeholder="Buscar servicio.." data-mini="true" class="fil_extra" data-icon="false">
                                    <?php while ($ser = $rSer->fetch(PDO::FETCH_ASSOC)) { ?>
                                        <li><a href="#" class="extra_insert" cod="<?php echo $ser['cod']; ?>"
                                                costo="<?php echo $ser['costo']; ?>"
                                                id="<?php echo $ser['id']; ?>"
                                                data="<?php echo $ser['nom']; ?>"><?php echo $ser['nom']; ?><span class="ui-li-count"><?php echo ' S/.' . $ser['costo']; ?></span></a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            <?php } else { // usa comobox nomas ?>
                                <select class="med_insert chosen-select" data-mini="true" <?php if ($_GET['s'] == 3 or $_GET['s'] == 5) echo 'name="pak"'; ?>>
                                    <option value="" selected>SELECCIONAR SERVICIO</option>
                                    <?php while ($ser = $rSer->fetch(PDO::FETCH_ASSOC)) {
                                        if ($_GET['s'] == 3 or $_GET['s'] == 5) {
                                            if ($ser['pak'] == $pop['pak']) {$pak_sel = "selected";} else {$pak_sel = "";}
                                            echo '<option value="' . mb_strtoupper($ser['pak']) . '" ' . $pak_sel . '>' . mb_strtoupper($ser['pak']) . '</option>';
                                        } else echo '<option value="' . mb_strtoupper($ser['nom']) . '" costo="' . $ser['costo'] . '" cc="' . $ser['cc'] . '" id="' . $ser['id'] . '">' .mb_strtoupper($ser['nom']) . '</option>';

                                    } ?>
                                </select>
                            <?php } ?>
                        </td>
                        <td colspan="2" align="left" valign="top">
                            <table id="servicios" border="0"><?php echo $pop['ser']; ?></table>
                            <input type="hidden" name="ser" id="ser" value="<?php echo $pop['ser']; ?>">
                    </tr>
                    <tr>
                        <td colspan="3" align="center" class="ui-bar-c">MEDIO DE PAGO</td>
                    </tr>
                    <tr>
                        <!-- <td>&nbsp;</td> -->
                        <td width="30%" colspan="2">
                            <div class="enlinea mantenimiento" style="display: none;">
                                Inicio <input name="man_ini" id="man_ini" type="date" value="<?php echo $pop['man_ini']; ?>">
                                Fin <input name="man_fin" id="man_fin" type="date" value="<?php echo $pop['man_fin']; ?>">
                            </div>
                            <div class="factura">SubTotal: <b id="subtot">-</b> <br> IGV(18%): <b id="igv">-</b></div>
                            <div class="enlinea">
                                <b>Total (<span id="labelmon">-</span>)</b><input name="tot" id="tot" type="text" readonly data-mini="true" value="<?php echo $pop['tot']; ?>">
                            </div>
                            <input type="button" id="borrar" <?php if ($_GET['id'] <> '') echo 'class="NoEdita"'; ?> value="Resetear Servicios" data-mini="true"/>
                        </td>
                        <td class="peke2">
                            <!-- medio de pago 1 -->
                            <div data-role="controlgroup" data-type="horizontal" data-mini="true">
                                <select name="t1" id="t1" data-mini="true" required>
                                    <option value="" selected>Medio Pago 1</option>
                                    <option value=1 <?php if ($pop['t1'] == 1) echo "selected"; ?>>EFECTIVO</option>
                                    <option value=3 <?php if ($pop['t1'] == 3) echo "selected"; ?>>DEPOSITO</option>
                                    <option value=4 <?php if ($pop['t1'] == 4) echo "selected"; ?>>TARJETA VISA</option>
                                    <option value=5 <?php if ($pop['t1'] == 5) echo "selected"; ?>>TARJETA MASTERCARD</option>
                                </select>
                                <select name="m1" id="m1" data-mini="true" required>
                                    <option value="" selected>Moneda</option>
                                    <option value=1 <?php if ($pop['m1'] == 1) echo "selected"; ?>>$</option>
                                    <option value=0 <?php if ($pop['m1'] === 0) echo "selected"; ?>>S/.</option>
                                </select>
                                <input name="p1" id="p1" type="number" step="any" value="<?php echo $pop['p1']; ?>" data-wrapper-class="controlgroup-textinput ui-btn" required>
                            </div>
                            <!-- medio de pago 2 -->
                            <div data-role="controlgroup" data-type="horizontal" data-mini="true">
                                <select name="t2" id="t2" data-mini="true">
                                    <option value="" selected>Medio Pago 2</option>
                                    <option value=1 <?php if ($pop['t2'] == 1) echo "selected"; ?>>EFECTIVO</option>
                                    <option value=3 <?php if ($pop['t2'] == 3) echo "selected"; ?>>DEPOSITO</option>
                                    <option value=4 <?php if ($pop['t2'] == 4) echo "selected"; ?>>TARJETA VISA</option>
                                    <option value=5 <?php if ($pop['t2'] == 5) echo "selected"; ?>>TARJETA MASTERCARD</option>
                                </select>
                                <select name="m2" id="m2" data-mini="true">
                                    <option value="" selected>Moneda</option>
                                    <option value=1 <?php if ($pop['m2'] == 1) echo "selected"; ?>>$</option>
                                    <option value=0 <?php if ($pop['m2'] === 0) echo "selected"; ?>>S/.</option>
                                </select>
                                <input name="p2" id="p2" type="number" step="any" value="<?php echo $pop['p2']; ?>"
                                        data-wrapper-class="controlgroup-textinput ui-btn">
                            </div>
                            <!-- medio de pago 3 -->
                            <div data-role="controlgroup" data-type="horizontal" data-mini="true">
                                <select name="t3" id="t3" data-mini="true">
                                    <option value="" selected>Medio Pago 3</option>
                                    <option value=1 <?php if ($pop['t3'] == 1) echo "selected"; ?>>EFECTIVO</option>
                                    <option value=3 <?php if ($pop['t3'] == 3) echo "selected"; ?>>DEPOSITO</option>
                                    <option value=4 <?php if ($pop['t3'] == 4) echo "selected"; ?>>TARJETA VISA</option>
                                    <option value=5 <?php if ($pop['t3'] == 5) echo "selected"; ?>>TARJETA MASTERCARD</option>
                                </select>
                                <select name="m3" id="m3" data-mini="true">
                                    <option value="" selected>Moneda</option>
                                    <option value=1 <?php if ($pop['m3'] == 1) echo "selected"; ?>>$</option>
                                    <option value=0 <?php if ($pop['m3'] === 0) echo "selected"; ?>>S/.</option>
                                </select>
                                <input name="p3" id="p3" type="number" step="any" value="<?php echo $pop['p3']; ?>" data-wrapper-class="controlgroup-textinput ui-btn">
                            </div>
                        </td>
                    </tr>
                    <tr><td colspan="3"><hr></td></tr>
                    <tr>
                        <td colspan="3" style="">
                            Referencia (este campo no es obligatorio):
                            <textarea name="comentarios" cols="40" rows="5"></textarea>
                        </td>
                        <td></td>
                    </tr>
                </table>
                <input type="Submit" name="guardar" value="GUARDAR" data-icon="check" data-iconpos="left" data-mini="true" data-theme="b" data-inline="true"/>
            </div>
        </div>
    </div>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.v4/bootstrap.min.js"></script>
    <script src="js/chosen.jquery.min.js"></script>
    <script>
        $(".chosen-select").chosen();
    </script>
</body>
</html>