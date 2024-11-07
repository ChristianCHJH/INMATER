<!DOCTYPE HTML>
<html>

<head>
    <?php
   include 'seguridad_login.php';
   include "_database/pago.php";
   include "nusoap/lib/nusoap.php";
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/chosen.min.css">
    <link rel="stylesheet" href="_themes/tema_inmater.min.css" />
    <link rel="stylesheet" href="_themes/tema_empresa.min.css" />
    <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css" />
    <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css" />
    <link rel="stylesheet" href="css/pago.css?v=3" />
    <link rel="stylesheet" href="css/global.css" />
    <title>Clínica Inmater | Emisión de Comprobantes</title>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
    <script src="js/chosen.jquery.min.js"></script>
</head>

<body>
    <div data-role="page" class="ui-responsive-panel" id="pago" data-dialog="true">
        <?php
        if (isset($_POST['dni']) && !empty($_POST['dni'] )) {
            $data = array(
                'comprobante_referencia' => $_POST['comprobante_referencia'],
                'total_cancelar' => $_POST['total_cancelar'],
                'numero_contacto' => isset($_POST['numero_contacto']) ? $_POST['numero_contacto'] :0,
                'tipo_documento_facturacion' => isset($_POST['tipo_documento_facturacion']) ? $_POST['tipo_documento_facturacion'] : 0,
                'descuento' => isset($_POST['descuento']) ? $_POST['descuento'] : 0,
                'gratuito' => isset($_POST['check_serviciogratuito']) ? 1 : 0,
                'bolsa_plastico' => isset($_POST['check_bolsa_plastico']) ? $_POST["cantidad_bolsaplastico"] : 0,
                'banco1' => isset($_POST["banco1"]) ? $_POST["banco1"] : 0,
                'tipotarjeta1' => isset($_POST["tipotarjeta1"]) ? $_POST["tipotarjeta1"] : 0,
                'numerocuotas1' => isset($_POST["numerocuotas1"]) && is_numeric($_POST["numerocuotas1"]) ? $_POST["numerocuotas1"] : 0,
                'pos1' => isset($_POST["pos1"]) ? $_POST["pos1"] : 1,
                'banco2' => isset($_POST["banco2"]) ? $_POST["banco2"] : 0,
                'tipotarjeta2' => isset($_POST["tipotarjeta2"]) ? $_POST["tipotarjeta2"] : 0,
                'numerocuotas2' => isset($_POST["numerocuotas2"]) && is_numeric($_POST["numerocuotas2"]) ? $_POST["numerocuotas2"] : 0,
                'pos2' => isset($_POST["pos2"]) ? $_POST["pos2"] : 1,
                'banco3' => isset($_POST["banco3"]) ? $_POST["banco3"] : 0,
                'tipotarjeta3' => isset($_POST["tipotarjeta3"]) ? $_POST["tipotarjeta3"] : 0,
                'numerocuotas3' => isset($_POST["numerocuotas3"]) && is_numeric($_POST["numerocuotas3"]) ? $_POST["numerocuotas3"] : 0,
                'pos3' => isset($_POST["pos3"]) ? $_POST["pos3"] : 1,
                'cli_atencion_unica_id' => isset($_POST["cli_atencion_unica_id"]) ? $_POST["cli_atencion_unica_id"] : 0,
                'condicion_pago_id' => isset($_POST["condicion_pago_id"]) ? $_POST["condicion_pago_id"]:0,
                'fecha_vencimiento' => $_POST["fecha_vencimiento"],
                'procedimiento_id' => $_POST["procedimiento_id"],
                'programa_id' => isset($_POST["programa_id"]) ? intval($_POST["programa_id"]) : 0
            );
            try {
                $validacion =  isset($_POST['verificacion_reniec']) ? $_POST['verificacion_reniec'] : null;
                $id_empresa =  isset($_POST['id_empresa']) ? $_POST['id_empresa'] : null;
                $id_sede =  isset($_POST['id_sede']) ? $_POST['id_sede'] : null;

                recibo($_POST['idx'], date("Y-m-d H:i:s"), $_POST['dni'], $_POST['nom'], $_POST['med'], $_POST['sede'], $validacion, $_POST['tip'], $_POST['ruc'], $_POST['raz'], $_POST['t_ser'], isset($_POST['pak']) ? $_POST['pak'] : "", $_POST['ser'], $_POST['mon'], $_POST['tot'], $_POST['t1'], $_POST['m1'], $_POST['p1'], $_POST['t2'], $_POST['m2'], $_POST['p2'], $_POST['t3'], $_POST['m3'], $_POST['p3'], (!!($_POST['man_ini']) ? $_POST['man_ini'] : "1970-01-02"), (!!($_POST['man_fin']) ? $_POST['man_fin'] : "1970-01-02"), $_POST['cadena'], $login, $id_empresa, $id_sede, $_POST['comentarios'] ?? '', $_POST['comprobante_referencia'] ?? '', $_POST['direccionfiscal'], $_POST['correo_electronico'], $data);
            } catch (Exception $e) {
                var_dump($e);
            }
           
        } 
        ?>
        <?php
        $stmt = $db->prepare("SELECT * from usuario where userx=?");
        $stmt->execute([$login]);
        $data_user = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $db->prepare("SELECT tipo_cambio_compra compra, tipo_cambio_venta venta from tipo_cambio where estado=1 and fecha=?;");
        $stmt->execute([date("Y-m-d")]);
        $tipo_cambio = $stmt->fetch(PDO::FETCH_ASSOC);
        $rMed = $db->prepare("SELECT UPPER(nom) nom FROM usuario WHERE role = 1;");
        $rMed->execute();

        $id = (isset($_GET['id']) && intval($_GET['id'])) ? $_GET['id'] : 0;
        $tip = (isset($_GET['t']) && intval($_GET['t'])) ? $_GET['t'] : 0;
        $Rpop = $db->prepare("SELECT * FROM recibos WHERE id=? AND tip=?");
        $Rpop->execute(array($id, $tip));
        $pop = $Rpop->fetch(PDO::FETCH_ASSOC); 
        $readonly='';
        $disabled='';
        if(isset($pop['dni'])){
            $readonly=' readonly ';
            $disabled=' disabled ';
        }
    ?>
    <div class="campus">
        <input type="hiden" id="posTip" value="<?php if(isset($pop['tip'])) echo $pop['tip'] ?>">
        <input type="hiden" id="posDni" value="<?php if(isset($pop['dni'])) echo $pop['dni'] ?>">
        <input type="hiden" id="posNom" value="<?php if(isset($pop['nom'])) echo $pop['nom'] ?>">
        <input type="hiden" id="posMan_ini" value="<?php if(isset($pop['man_ini'])) echo $pop['man_ini'] ?>">
        <input type="hiden" id="posDescuento" value="<?php if(isset($pop['descuento'])) echo $pop['descuento'] ?>">
        <input type="hiden" id="getS" value="<?php if(isset($_GET['s'])) echo $_GET['s'] ?>">
        <input type="hidden"id="posIdx" value="<?php if(isset($_GET['id']))print(trim($_GET['id'])); ?>">
    </div>
    <script>
            $('.campus').hide();
        $(document).ready(function() {
            if($('#posTip').val() > 0) { 
                $(".servicio").show();
                $(".factura").show();
            } else { 
                $(".servicio").hide();
                $(".factura").hide();
            } if ($('#posTip').val()== 2) { $(".factura").show(); }
            if ($('#posMan_ini').val() != '1970-01-02') {  
                $(".mantenimiento").show(); 
            } 
            $(".mon").hide();
            $(".div_descuento").hide();
            $(".div_bolsa").hide();
            $(".contenido_fecha_vencimiento").hide();

            if ($('#posDescuento').val() != 0) { 
                $(".div_descuento").show();
            } else { 
                $(".div_descuento").hide(); 
            } 
            if ($('#getS').val() == 1 || $('#getS').val() == 2 || $('#getS').val() == 3) {
                if ($('#posNom').val() > 1) { 
                    $('#labelmon').html('S/.');
                    $('#labelmondes').html('S/.');
                    $(".mon").show();
                } else { 
                    $('#labelmon').html('$');
                    $('#labelmondes').html('$');
                } 
            } else { 
                if ($('#posNom').val() > 1) {
                    $('#labelmon').html('$');
                    $('#labelmondes').html('$');
                    $(".mon").show();
                } else {
                    $('#labelmon').html('S/.');
                    $('#labelmondes').html('S/.');
                } 
            } if ($("#posIdx").val() == '') {
                $.post("le_tanque.php", {
                    carga_paci: 1
                }, function(data) {
                    $('.carga_msj').html('');
                    $(".carga_paci").append(data);
                    $('.ui-page').trigger('create'); //recarga los css del jqm
                });
            }
        });

        function cambioTip(){
            dni = $('#dni').val()
            if($('#tip').val()==1) { 
                $(".servicio").show();
                $(".factura").show();
            } else if($('#tip').val()==2) { 
                $(".servicio").show();
                $(".factura").show();
            }else{
                $(".servicio").hide();
                $(".factura").hide();
            }
        }
        </script>
        <!-- titulo de la pagina -->
        <div data-role="header" data-position="fixed">
            <a href="lista_facturacion.php" rel="external"
                class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>
            <?php
            $id = (isset($_GET['s']) && is_numeric($_GET['s'])) ? $_GET['s'] : 0;

            $stmt = $db->prepare("SELECT id, upper(nombre) nombre from man_tipo_servicio where id=?;");
            $stmt->execute([$id]);
            $servicio = $stmt->fetch(PDO::FETCH_ASSOC);
            if (isset($pop['id']) && !is_bool($pop['id'])){
                echo '<h3>Comprobante ' . sprintf('%05d', $pop['id']) . ': ' . $servicio["nombre"] . '</h3>';
            } else {
                echo '<h3>Comprobante ' . sprintf('%05d',0) . ': ' . $servicio["nombre"] . '</h3>';
            }
            
            ?>
        </div>

        <div class="ui-content" role="main">
            <?php
            $consulta = $db->prepare("SELECT codigo, nombre from man_tipo_servicio where estado=1");
            $consulta->execute();

            $empresas = $db->prepare("SELECT id as codigo,nom_comercial as nombre from man_empresas where estado='1'");
            $empresas->execute();

            $idSedeEmp = (isset($pop['id_empresa_sede']) && is_numeric($pop['id_empresa_sede'])) ? $pop['id_empresa_sede'] : 0;
            $sede_id = $db->prepare("SELECT id, nombre FROM sedes WHERE id=?");
            $sede_id->execute(array($idSedeEmp));
            ?>
            <form action="" method="post" data-ajax="false" id="form2" novalidate>
                
            <div id="demo1">
                <div data-role="controlgroup" data-type="horizontal" data-mini="true">
                    <a href="#lista_vouchers" data-rel="dialog" data-role="button" data-mini="true">Vouchers
                        virtuales</a>
                        
                        <?php 
                        $disabledE="";
                        if(isset($pop['id_empresa'])){
                            $disabledE = 'style ="pointer-events: none;"';
                        }?>

                    <select id="id_empresa" name="id_empresa" data-mini="true" data-inline="true" required <?php echo $disabledE?>>

                        <option value="" selected>SELECCIONAR</option>
                        <?php while ($data = $empresas->fetch(PDO::FETCH_ASSOC)) { ?>
                        <?php
                            $seleted ="";
                            if(isset($pop['id_empresa']) && $data['codigo'] == $pop['id_empresa']) {
                                $seleted ="selected";
                            }elseif (!isset($pop['id_empresa']) && $data['codigo'] == 4){
                                $seleted ="selected";
                            } ?>

                        <option value="<?php echo mb_strtoupper($data['codigo']); ?>" <?php echo $seleted?>><?php echo mb_strtoupper($data['nombre']); ?></option>
                        <?php } ?>
                    </select>

                    <script>
                        id_sede = "<?php echo $pop['id_empresa_sede']??0;?>"
                    </script>

                    <select id="id_sede" name="id_sede" data-mini="true" data-inline="true" required <?php echo $disabledE?>>

                        <?php if (isset($pop['id_empresa_sede']) && intval($pop['id_empresa_sede'])) {
                            $sede_id = $sede_id->fetch(PDO::FETCH_ASSOC);
                            echo '<option value="'.$sede_id['id'].'" selected>'.$sede_id['nombre'].'</option>';
                        }else { ?>
                            <option value="" selected>SELECCIONAR</option>
                        <?php } ?>

                    </select>

                    <select id="tipo_servicio" data-mini="true" data-inline="true" required>
                        <option value="" selected>SELECCIONAR</option>
                        <?php while ($data = $consulta->fetch(PDO::FETCH_ASSOC)) { ?>
                        <option value="<?php echo mb_strtoupper($data['codigo']); ?>"
                            <?php if ($data['codigo'] == $_GET['s']) {echo "selected";} ?>>
                            <?php echo mb_strtoupper($data['nombre']); ?></option>
                        <?php } ?>
                    </select>
                </div>

            </div>


            
            <?php
                if (isset($pop['cli_atencion_unica_id'])) {
                    print('<input type="hidden" name="cli_atencion_unica_id" id="cli_atencion_unica_id" value="' . $pop['cli_atencion_unica_id'] . '">');
                }
                ?>
                <input type="hidden" name="idx" id="idx" value="<?php if(isset($_GET['id']))print(trim($_GET['id'])); ?>">
                <input type="hidden" name="dni" id="dni" value="<?php if(isset($pop['dni']))print(trim($pop['dni'])); ?>">
                <input type="hidden" name="nom" id="nom" value="<?php if(isset($pop['nom']))print(trim($pop['nom'])); ?>">
                <input type="hidden" name="sede_id" id="sede_id" value="<?php print($data_user["sede_id"]); ?>">
                <input type="hidden" name="cadena" id="cadena">
                <input type="hidden" name="t_ser" id="t_ser" value="<?php echo $_GET['s']; ?>">

                <table border="0" style="margin: 0 auto; font-size: small;" class="ui-bar-b">
                    <tr>
                        <td width="90%" class="carga_paci">
                            Paciente <?php if(isset($pop['nom'])) {echo $pop['nom'];} 
                            else {echo '<div class="carga_msj"></div>';
                            
                            }?>
                        </td>
                        <td width="10%">
                            Fecha <input name="fec" type="date" required id="fec" placeholder="Fecha"
                                value="<?php if (isset($pop['fec'])) print(substr($pop['fec'], 0, 10)); else echo date("Y-m-d"); ?>"
                                data-mini="true">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="enlinea">
                                Médico
                                <select name="med" id="med" data-mini="true" data-inline="true" required>
                                    <option value="" selected>SELECCIONAR</option>
                                    <?php
                                    $rMed = $db->prepare("SELECT codigo, upper(trim(nombre)) nombre from man_medico where estado=1 order by nombre asc;");
                                    $rMed->execute();
                                    while ($med = $rMed->fetch(PDO::FETCH_ASSOC)) { ?>
                                    <option value="<?php echo $med['codigo']; ?>"
                                        <?php if(isset($pop['med']))if ($med['codigo'] == $pop['med']) {echo "selected";} ?>>
                                        <?php echo mb_strtoupper($med['nombre']); ?></option>
                                    <?php } ?>
                                </select>
                                Procedencia
                                <?php
                                $consulta_sede = $db->prepare("SELECT id codigo, nombre FROM sedes WHERE estado=1 ORDER BY nombre;");
                                $consulta_sede->execute();
                                ?>
                                <select name="sede" id="sede" data-mini="true" data-inline="true" required>
                                    <option value="" selected>SELECCIONAR</option>
                                    <?php
                                    while ($dato_sede = $consulta_sede->fetch(PDO::FETCH_ASSOC)) { ?>
                                    <option value=<?php echo $dato_sede['codigo']; ?>
                                        <?php if(isset($pop['sede']))if ($dato_sede['codigo'] == $pop['sede']) {echo "selected";} ?>>
                                        <?php echo mb_strtoupper($dato_sede['nombre']); ?></option>
                                    <?php } ?>
                                </select>

                                Programa
                                <?php
                                $stmt_programa = $db->prepare("SELECT id, abreviatura nombre from man_medios_comunicacion mmc where estado = 1 order by nombre;");
                                $stmt_programa->execute();
                                ?>
                                <select name="programa_id" id="programa_id" data-mini="true" data-inline="true" required>
                                    <option value="" selected>SELECCIONAR</option>
                                    <?php
                                    while ($dato_sede = $stmt_programa->fetch(PDO::FETCH_ASSOC)) { ?>
                                    <option value="<?php echo $dato_sede['id']; ?>" <?php if(isset($pop['programa_id']))if ($dato_sede['id'] == $pop['programa_id']) {echo "selected";} ?>>
                                        <?php echo mb_strtoupper($dato_sede['nombre']); ?></option>
                                    <?php } ?>
                                </select>

                                Tipo Recibo
                                <?php
                                if (isset($pop['tip']) && !is_bool($pop['tip'])){
                                    if ($pop['tip'] > 0) {
                                        if ($pop['tip'] == 1) {
                                            echo "BOLETA";
                                        }
    
                                        if ($pop['tip'] == 2) {
                                            echo "FACTURA";
                                        } 
                                }
                                ?>
                                <input type="hidden" name="tip" id="tip" value="<?php echo $pop['tip']; ?>">
                                <?php } else { ?>
                                <select name="tip" required id="tip" data-mini="true" onchange="cambioTip()">
                                    <option value=0 selected>SELECCIONAR</option>
                                    <option value=1 <?php if(isset($pop['tip']))if ($pop['tip'] == 1) {echo "selected";} ?>>BOLETA</option>
                                    <option value=2 <?php if(isset($pop['tip']))if ($pop['tip'] == 2) {echo "selected";} ?>>FACTURA</option>
                                </select>
                                <?php } ?>
                            </div>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <div class="numero_contacto_contenedor">
                            <td width="50%">Correo Electrónico: <input type="email" id="correo_electronico"
                                    name="correo_electronico" data-mini="true" maxlength="50"
                                    value="<?php if(isset($pop['correo_electronico']))print($pop['correo_electronico']); ?>" required></td>
                        </div>
                    </tr>
                    <tr>

                    </tr>
                    <?php
                        if ($_GET['s'] == 4) {
                            
                                ?> <td width="50%">Número de teléfono:
                                    <div class="numero_contacto_contenedor">
                                        <input type="text" id="numero_contacto" name="numero_contacto" data-mini="true" value="<?php if(isset($pop['numero_contacto'])) echo $pop['numero_contacto']; ?>">
                                    </div>
                                </td>
                                <?php
                        } else {
                            print('<input type="hidden" name="numero_contacto" id="numero_contacto" value="">');
                        }
                    ?>
                </table>
                <table width="100%" border="0" style="margin: 0 auto; font-size: small;" class="ui-bar-b">
                    <tr class="factura">
                        <td width="15%">
                            <?php
                            $consulta = $db->prepare("SELECT id codigo, abreviatura nombre FROM man_tipo_documento_facturacion WHERE estado=1");
                            $consulta->execute(); ?>
                            <span id="tipo_documento_facturacion_texto">Tipo Documento</span>:
                            <select name="tipo_documento_facturacion" id="tipo_documento_facturacion" data-mini="true"
                                data-inline="true" <?php echo $disabled ?> required>
                                <option value="" selected>SELECCIONAR</option>
                                <?php while ($data_tipo_documento_facturacion = $consulta->fetch(PDO::FETCH_ASSOC)) { ?>
                                <option value=<?php echo $data_tipo_documento_facturacion['codigo']; ?>
                                    <?php if(isset($pop['id_tipo_documento_facturacion']))if ($data_tipo_documento_facturacion['codigo'] == $pop['id_tipo_documento_facturacion']) {echo "selected";} ?>>
                                    <?php echo mb_strtoupper($data_tipo_documento_facturacion['nombre']); ?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td width="15%">
                            <span id="numero_documento_facturacion_texto">Número</span>: <input name="ruc" type="text"
                                id="ruc" data-mini="true" value="<?php if(isset($pop['ruc']))print($pop['ruc']); ?>" autocomplete="on" <?php echo $readonly ?>
                                required>
                        </td>
                        <td width="30%" >
                            <div style="display: flex; flex-wrap: nowrap; align-items: center;">
                                <div id = "ocultar" style="display: block;width: 150px;">
                                    <span id="verificar_texto">Verificar:</span> 
                                    <input type="button" value="Validar" id="verificar" data-mini="true">
                                </div>
                                <div>
                                    <input type="checkbox" id="verificacion_reniec" name="verificacion_reniec" value=1>
                                </div>
                            </div>
                        </td>
                        <td width="10%" style="display: inline;">
                            <div>
                                <span>Fecha de creacion: </span>
                                <input name="textFechaCreacion" type="text" id="textFechaCreacion" data-mini="true" value="" autocomplete="on" readonly>
                            </div>
                        </td>
                    </tr>
                    <tr class="factura">
                        <td width="50%" colspan="3">
                            <span id="raz_texto">Nombre o Razón Social</span>
                            <input list="raz" name="raz" id="razon" placeholder="Buscar..." autocomplete="on"
                                value="<?php if(isset($pop['raz']))echo $pop['raz']; ?>" <?php echo $readonly ?> required>
                            <datalist id="raz"></datalist>
                        </td>
                        <td width="50%">
                            Dirección <input type="text" id="direccionfiscal" name="direccionfiscal" data-mini="true"
                                value="<?php if(isset($pop['direccionfiscal']))print($pop['direccionfiscal']); ?>" <?php echo $readonly ?>>
                        </td>
                    </tr>
                    <tr class="factura">
                        <td width="50%" colspan="3">
                            Estado del Contribuyente: <input type="text" id="estado_contribuyente"
                                name="estado_contribuyente" data-mini="true" readonly>
                        </td>
                        <td width="50%">
                            Condición del Contribuyente: <input type="text" id="condicion_contribuyente"
                                name="condicion_contribuyente" data-mini="true" readonly>
                        </td>
                    </tr>
                </table>

                <div class="servicio">
                    <table width="100%" border="0" align="center" style="margin: 0 auto; font-size: small;">
                        <tr>
                            <td colspan="3" align="center" class="ui-bar-c">CONDICIONES DE PAGO</td>
                        </tr>
                        <tr>
                            <td>
                                <table width="100%" border="0" align="center"
                                    style="margin: 0 auto; font-size: small; table-layout: fixed;">
                                    <tr>
                                        <td class="">
                                            <select name="condicion_pago_id" id="condicion_pago_id" style="width: 100%;"
                                                data-mini="true" data-inline="true" <?php echo $disabled ?> required>
                                                <option value="" selected>Condición de pago</option>
                                                <option value="1" <?php if(isset($pop['condicion_pago_id']))if($pop['condicion_pago_id'] == 1) echo 'selected' ?> >Al Contado</option>
                                                <option value="2" <?php if(isset($pop['condicion_pago_id']))if($pop['condicion_pago_id'] == 2) echo 'selected' ?> >Al Crédito</option>
                                            </select>
                                            <div class="contenido_fecha_vencimiento">
                                                <input type="date" name="fecha_vencimiento" id="fecha_vencimiento"
                                                    data-mini="true" <?php echo $readonly ?>>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <!-- Servicio Gratuito -->
                                        <td style="vertical-align: top;">
                                            <label for="check_serviciogratuito">Servicio gratuito</label>
                                            <input type="checkbox" name="check_serviciogratuito[]"
                                                id="check_serviciogratuito" data-mini="true"
                                                <?php if(isset($pop['descuento']))if ($pop['descuento'] != 0) {echo "checked";} ?> <?php echo $disabled ?>>
                                        </td>

                                        <!-- cambiar moneda -->
                                        <td style="vertical-align: top;">
                                            <?php
                                            $cambio="";
                                            if ($_GET['s'] == 1 || $_GET['s'] == 2 || $_GET['s'] == 3) {
                                                $moneda = "soles";
                                                $cambio = $tipo_cambio["compra"];
                                            } else {
                                                $moneda = "dolares";
                                                $cambio = $tipo_cambio["venta"];
                                            } ?>
                                            <input type="hidden" name="tipo_cambio" id="tipo_cambio"
                                                value="<?php print($cambio); ?>">
                                            <label for="cambio" id="cambioo">Cambiar a <?php echo $moneda; ?></label>
                                            <input type="checkbox" name="cambio" id="cambio"
                                                data="<?php echo $_GET['s']; ?>" data-mini="true"
                                                <?php if(isset($pop['mon']))if ($pop['mon'] > 1) {echo "checked";} ?> <?php echo $disabled ?>>
                                            <div class="enlinea mon peke2">Tipo cambio:
                                                <input type="number" step="any" min="1" name="mon" id="mon"
                                                    data-mini="true"
                                                    value=<?php if (isset($pop['mon']) && $pop['mon'] > 1) {echo $pop['mon'];} else {echo "1";} ?> <?php echo $readonly ?>>
                                            </div>
                                        </td>
                                        <!-- bolsas de plastico -->
                                        <td style="vertical-align: top;">
                                            <label for="check_bolsa_plastico">Bolsas de plástico</label>
                                            <input type="checkbox" name="check_bolsa_plastico[]"
                                                id="check_bolsa_plastico" data-mini="true" <?php echo $disabled ?>>
                                            <div class="enlinea div_bolsa peke2">Cantidad:
                                                <input type="number" step="any" min="1" name="cantidad_bolsaplastico"
                                                    id="cantidad_bolsaplastico" data-mini="true" value="0" <?php echo $readonly ?>>
                                            </div>
                                        </td>
                                        <!-- descuento -->
                                        <td style="vertical-align: top;">
                                            <label for="check_descuento">Indicar descuento</label>
                                            <input type="checkbox" name="check_descuento" id="check_descuento"
                                                data-mini="true" <?php if(isset($pop['descuento']))if ($pop['descuento'] != 0) {echo "checked";} ?> <?php echo $disabled ?>>
                                            <div class="enlinea div_descuento">
                                                <b>Descuento % </b><input name="porcentaje_descuento"
                                                    id="porcentaje_descuento" type="number" data-mini="true"
                                                    value="<?php if(isset($pop['descuento']) && isset($pop['tot']))echo number_format((float)$pop['descuento'] / $pop['tot'], 2, '.', '') * 100; ?>" <?php echo $readonly ?>><br>
                                                <b>Descuento (<span id="labelmondes">-</span>)</b><input
                                                    name="descuento" id="descuento" type="number" data-mini="true"
                                                    value="<?php if(isset($pop['descuento']))echo $pop['descuento']; ?>">
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="servicio">
                    <table width="100%" border="0" align="center" style="margin: 0 auto; font-size: small;">
                        <tr>
                            <td colspan="4" align="center" class="ui-bar-c">LISTA DE SERVICIOS</td>
                        </tr>
                        <tr <?php if ($_GET['id'] <> '') {echo 'class="NoEdita"';} ?>>
                            <td width="30%" align="center" valign="top" id="contenedor_servicios">
                                <div class="container-sede">
                                    <select name="sede_contabilidad_id" id="sede_contabilidad_id" class="chosen-select">
                                        <option value="" selected>SELECCIONAR SEDE</option>
                                    </select>
                                </div>
                                <div class="container-tarifario">
                                    <select name="tarifario_id" id="tarifario_id" class="chosen-select">
                                        <option value="" selected>SELECCIONAR TARIFARIO</option>
                                    </select>
                                </div>
                                <div class="container-procedimiento">
                                    <select name="procedimiento_id" id="procedimiento_id" class="chosen-select">
                                        <option value="" selected>SELECCIONAR PROCEDIMIENTO</option>
                                    </select>
                                </div>

                                <?php
                                if ($_GET['s'] == 1 || $_GET['s'] == 2 || $_GET['s'] == 4 || $_GET['s'] == 6 || $_GET['s'] == 7) {
                                    $rSer = $db->prepare("SELECT r.*
                                        from recibo_serv r
                                        inner join conta_sub_centro_costo sco on sco.id = r.conta_sub_centro_costo_id and sco.estado = 1
                                        inner join conta_centro_costo cco on cco.id = sco.conta_centro_costo_id and cco.estado = 1
                                        inner join sedes_contabilidad s on s.id = cco.sede_id and s.eliminado = 0
                                        where r.estado = 1 and r.tip = ?
                                        and coalesce(r.procedimiento_id, 0) = 0 and coalesce(r.tarifario_id, 0) = 0
                                        order by r.nom asc;");
                                }

                                if ($_GET['s'] == 3 || $_GET['s'] == 5) {
                                    $rSer = $db->prepare("SELECT distinct(r.pak)
                                        from recibo_serv r
                                        inner join conta_sub_centro_costo sco on sco.id = r.conta_sub_centro_costo_id and sco.estado = 1
                                        inner join conta_centro_costo cco on cco.id = sco.conta_centro_costo_id and cco.estado = 1
                                        inner join sedes_contabilidad s on s.id = cco.sede_id and s.eliminado = 0
                                        where r.estado = 1 and r.tip = ? and r.pak <> ''
                                        and coalesce(r.procedimiento_id, 0) = 0 and coalesce(r.tarifario_id, 0) = 0
                                        order by r.pak asc;");
                                }

                                $rSer->execute([$_GET['s']]);

                                if ($_GET['s'] == 4) { ?>
                                    <ul data-role="listview" data-theme="c" data-inset="true" data-filter="true"
                                        data-filter-reveal="true" data-filter-placeholder="Buscar servicio.."
                                        data-mini="true" class="fil_extra" data-icon="false">
                                        <?php while ($ser = $rSer->fetch(PDO::FETCH_ASSOC)) { ?>
                                        <li>
                                            <a href="#" class="extra_insert" cod="<?php echo $ser['cod']; ?>"
                                                costo="<?php echo $ser['costo']; ?>" id="<?php echo $ser['id']; ?>"
                                                data="<?php echo mb_strtoupper($ser['nom']); ?>"><?php echo mb_strtoupper($ser['nom']); ?><span
                                                    class="ui-li-count"><?php echo ' S/.' . $ser['costo']; ?></span></a>
                                        </li>
                                        <?php } ?>
                                    </ul>
                                <?php } else { ?>
                                <div class="container-servicios">
                                    <select class="med_insert chosen-select" data-mini="true"
                                        <?php if ($_GET['s'] == 3 or $_GET['s'] == 5) {echo 'name="pak"';} ?>>
                                        <option value="" selected>SELECCIONAR SERVICIO</option>
                                        <?php
                                            while ($ser = $rSer->fetch(PDO::FETCH_ASSOC)) {
                                                if ($_GET['s'] == 3 || $_GET['s'] == 5) {
                                                    if ($ser['pak'] == $pop['pak']) {
                                                        $pak_sel = "selected";
                                                    } else {
                                                        $pak_sel = "";
                                                    }
                                                    
                                                    echo '<option value="' . mb_strtoupper($ser['pak']) . '" ' . $pak_sel . '>' . mb_strtoupper($ser['pak']) . '</option>';
                                                } else {
                                                    echo '<option value="' . mb_strtoupper($ser['nom']) . '" costo="' . $ser['costo'] . '" id="' . $ser['id'] . '">' .mb_strtoupper($ser['nom']) . '</option>';
                                                }
                                            } ?>
                                    </select>
                                </div>
                                <?php } ?>

                            </td>
                            <td width="70%" align="left" valign="top">
                                <table width="100%" id="servicios" border="1"><?php if(isset($pop['ser']))echo $pop['ser']; ?></table>
                                <input type="hidden" name="ser" id="ser" value="<?php if(isset($pop['ser']))echo $pop['ser']; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" align="center" class="ui-bar-c">MEDIOS DE PAGO</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="contenido_pago">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <table width="100%" border="0" align="center"
                                    style="margin: 0 auto; font-size: small; table-layout: fixed;">
                                    <tr>
                                        <td>
                                            <table width="100%" border="0" align="center" style="margin: 0 auto; font-size: small; table-layout: fixed;">

                                            <?php 
                                            $displey = 'none';
                                            if (isset($pop['man_ini']) || isset($pop['man_fin'])){
                                                $man_ini = date('Y-m-d', strtotime($pop['man_ini']));
                                                $man_fin = date('Y-m-d', strtotime($pop['man_fin']));
                                                if ($man_ini > "2000-01-01" || $man_fin  > "2000-01-01") {
                                                    $displey = 'show';
                                                }
                                            }
                                            ?>

                                                <tr>
                                                    <td width="30%">
                                                        <div class="mantenimiento"><b>Inicio</b></div>
                                                    </td>
                                                    <td>
                                                        <div class="enlinea mantenimiento" style="display: <?php echo $displey; ?>;">
                                                            <input name="man_ini" id="man_ini" type="date" value="<?php echo $man_ini; ?>">
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <div class="mantenimiento"><b>Fin</b></div>
                                                    </td>
                                                    <td>
                                                        <div class="enlinea mantenimiento" style="display: <?php echo $displey; ?>;">
                                                            <input name="man_fin" id="man_fin" type="date" value="<?php echo $man_fin; ?>">
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td colspan="2">
                                                        <div class="factura" style="display: block;font-size: 16px;"><em
                                                                style="font-size: 12px;">SUBTOTAL:</em> <b
                                                                id="subtot">-</b><br><em
                                                                style="font-size: 12px;">IGV(18%):</em> <b
                                                                id="igv">-</b></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><b>Total (<span id="labelmon">-</span>)</b></td>
                                                    <td><input name="tot" id="tot" type="text" readonly data-mini="true"
                                                            style="text-align: right;"
                                                            value="<?php if(isset($pop['tot']))echo $pop['tot']; ?>"></td>
                                                </tr>
                                                <tr>
                                                    <td><b>Total a pagar</b></td>
                                                    <td><input name="total_descuento" id="total_descuento" type="text"
                                                            readonly data-mini="true" style="text-align: right;"
                                                            value="<?php if(isset($pop['tot']) && isset($pop['descuento']))echo ($pop['tot'] - $pop['descuento']); ?>">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="enlinea"><b>Total a cancelar</b></div>
                                                    </td>
                                                    <td><input name="total_cancelar" id="total_cancelar" type="text"
                                                            data-mini="true" style="text-align: right;"
                                                            value="<?php if(isset($pop['total_cancelar']))echo ($pop['total_cancelar']); ?>" <?php echo $readonly ?>></td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="enlinea"><b>Vuelto</b></div>
                                                    </td>
                                                    <td><input name="vuelto" id="vuelto" type="text" readonly
                                                            data-mini="true" style="text-align: right;"
                                                            value="<?php echo (!empty($pop['total_cancelar']) ? $pop['total_cancelar'] - $pop['tot'] - $pop['descuento'] : 0); ?>">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" style="text-align: center;">
                                                        <div class="enlinea">
                                                            <input type="button" id="borrar"
                                                                <?php if ($_GET['id'] <> '') {echo 'class="NoEdita"';} ?>
                                                                value="Resetear Servicios" data-mini="true" />
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    
                                        <td style="vertical-align: top;">
                                            <!-- medio de pago 1 -->
                                            <div data-role="controlgroup" data-mini="true" >
                                                <select name="t1" id="t1" data-mini="true" required>
                                                    <option value="" selected>Forma de Pago 1</option>
                                                   <optgroup label="Seleccionar">
                                                   <?php
                                                   $formaPago = forPago();
                                                   $seleted ='';
                                                    foreach ($formaPago as $fila) {
                                                    $seleted='';
                                                    if($pop['t1']== $fila['codigo_facturacion'])$seleted='selected';
                                                        echo '<option value="'.$fila['codigo_facturacion'].'"'.$seleted.'>'.$fila['tipotarjeta'].'</option>';
                                                    }
                                                    ?>
                                                   </optgroup>
                                                </select>
                                                <select name="banco1" id="banco1" data-mini="true" required>
                                                    <option value="">Banco</option>
                                                    <option value=1
                                                        <?php if(isset($pop['banco1']))$pop["banco1"] == 1 ? print("selected") : print(""); ?>>
                                                        BBVA</option>
                                                    <option value=2
                                                        <?php if(isset($pop['banco1']))$pop["banco1"] == 2 ? print("selected") : print(""); ?>>
                                                        BCP</option>
                                                    <option value=3
                                                        <?php if(isset($pop['banco1']))$pop["banco1"] == 3 ? print("selected") : print(""); ?>>
                                                        Dinners Club</option>
                                                    <option value=4
                                                        <?php if(isset($pop['banco1']))$pop["banco1"] == 4 ? print("selected") : print(""); ?>>
                                                        Interbank</option>
                                                    <option value=6
                                                        <?php if(isset($pop['banco1']))$pop["banco1"] == 6 ? print("selected") : print(""); ?>>
                                                        OH</option>
                                                    <option value=7
                                                        <?php if(isset($pop['banco1']))$pop["banco1"] == 7 ? print("selected") : print(""); ?>>
                                                        Scotiabank</option>
                                                    <option value=5
                                                        <?php if(isset($pop['banco1']))$pop["banco1"] == 5 ? print("selected") : print(""); ?>>
                                                        Otros</option>
                                                </select>
                                                <select name="tipotarjeta1" id="tipotarjeta1" data-mini="true" required>
                                                    <option value="">Tarjeta</option>
                                                    <optgroup label="Seleccionar">
                                                   <?php
                                                    $tipoTarjeta = tipTarjeta();
                                                    foreach ($tipoTarjeta as $fila) {
                                                        $seleted='';
                                                        if($pop['tipotarjeta1']== $fila['codigo_facturacion'])$seleted='selected';
                                                        echo '<option value="'.$fila['codigo_facturacion'].'"'.$seleted.'>'.$fila['formapago'].'</option>';
                                                    }
                                                    ?>
                                                   </optgroup>
                                                </select>
                                                <input name="numerocuotas1" id="numerocuotas1" type="number" step="any"
                                                    value="<?php if(isset($pop['numerocuotas1']))echo $pop['numerocuotas1']; ?>" placeholder="Cuotas"
                                                    data-wrapper-class="controlgroup-textinput ui-btn"
                                                    style="text-align: center;" required>
                                                <select name="m1" id="m1" data-mini="true" required>
                                                    <option value="" selected>Moneda</option>
                                                    <option value=1 <?php if(isset($pop['m1']))if ($pop['m1'] == 1) {echo "selected";} ?>>$
                                                    </option>
                                                    <option value=0 <?php if(isset($pop['m1']))if ($pop['m1'] === 0) {echo "selected";} ?>>
                                                        S/.</option>
                                                </select>
                                                <input name="p1" id="p1" type="number" step="any"
                                                    value="<?php if(isset($pop['p1']))echo $pop['p1']; ?>" placeholder="Monto"
                                                    data-wrapper-class="controlgroup-textinput ui-btn"
                                                    style="text-align: center;" required>

                                                 
                                                    

                                                <select name="pos1" id="poss1" data-mini="true" required>
                                                    
                                                    <?php if (isset($pop['pos1_id'])) {
                                                            $listarPos = listarPos($pop['t1'],$pop['id_empresa_sede'],$pop['pos1_id']);
                                                            foreach ($listarPos as $fila) {
                                                                echo '<option value="'.$fila['id'].'" selected>'.$fila['id']." - ".$fila['nombrepos']." - ".$fila['moneda'].'</option>';
                                                            }
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </td>
                                        <td style="vertical-align: top;">
                                            <!-- medio de pago 2 -->
                                            <div data-role="controlgroup" data-mini="true">
                                                <select name="t2" id="t2" data-mini="true">
                                                    <option value="" selected>Forma de Pago 2</option>
                                                    <optgroup label="Seleccionar">
                                                   <?php
                                                    $formaPago = forPago();
                                                    $seleted='';
                                                    foreach ($formaPago as $fila) {
                                                    $seleted='';
                                                    if($pop['t2']== $fila['codigo_facturacion'])$seleted='selected';
                                                        echo '<option value="'.$fila['codigo_facturacion'].'"'.$seleted.'>'.$fila['tipotarjeta'].'</option>';
                                                    }
                                                    ?>
                                                   </optgroup>
                                                </select>
                                                <select name="banco2" id="banco2" data-mini="true">
                                                    <option value="">Banco</option>
                                                    <option value=1
                                                        <?php if(isset($pop["banco2"]))$pop["banco2"] == 1 ? print("selected") : print(""); ?>>
                                                        BBVA</option>
                                                    <option value=2
                                                        <?php if(isset($pop['banco2']))$pop["banco2"] == 2 ? print("selected") : print(""); ?>>
                                                        BCP</option>
                                                    <option value=3
                                                        <?php if(isset($pop['banco2']))$pop["banco2"] == 3 ? print("selected") : print(""); ?>>
                                                        Dinners Club</option>
                                                    <option value=4
                                                        <?php if(isset($pop['banco2']))$pop["banco2"] == 4 ? print("selected") : print(""); ?>>
                                                        Interbank</option>
                                                    <option value=5
                                                        <?php if(isset($pop['banco2']))$pop["banco2"] == 5 ? print("selected") : print(""); ?>>
                                                        Otros</option>
                                                </select>
                                                <select name="tipotarjeta2" id="tipotarjeta2" data-mini="true">
                                                    <option value="">Tarjeta</option>
                                                    <optgroup label="Seleccionar">
                                                   <?php
                                                    $tipoTarjeta = tipTarjeta();
                                                    foreach ($tipoTarjeta as $fila) {
                                                        $seleted='';
                                                        if($pop['tipotarjeta2']== $fila['codigo_facturacion'])$seleted='selected';
                                                        echo '<option value="'.$fila['codigo_facturacion'].'"'.$seleted.'>'.$fila['formapago'].'</option>';
                                                    }
                                                    ?>
                                                   </optgroup>
                                                </select>

                                                <?php 
                                                    $numerocuotas2 = null;
                                                
                                                    if(isset($pop['numerocuotas2']) && $pop['p2'] != 0){
                                                        $numerocuotas2 = $pop['numerocuotas2'];
                                                    } 
                                                ?>

                                                <input name="numerocuotas2" id="numerocuotas2" type="number" step="any"
                                                    value="<?php echo $numerocuotas2; ?>" placeholder="Cuotas"
                                                    data-wrapper-class="controlgroup-textinput ui-btn"
                                                    style="text-align: center;">

                                                <?php 
                                                    $m1Select = "";
                                                    $m0Select = "";
                                                
                                                    if(isset($pop['m2'])){
                                                        if ($pop['m2'] == 1) {
                                                            $m1Select = "selected";
                                                        } else if ($pop['m2'] === 0 && $pop['p2'] != 0){
                                                            $m0Select = "selected";
                                                        }
                                                    } 
                                                ?>
                                                <select name="m2" id="m2" data-mini="true">
                                                    <option value="" selected>Moneda</option>
                                                    <option value=1 <?php echo $m1Select; ?>>$
                                                    </option>
                                                    <option value=0 <?php echo $m0Select;?>>
                                                        S/.</option>
                                                </select>
                                                <?php 
                                                    $p2 = null;
                                                
                                                    if(isset($pop['p2']) && $pop['p2'] != 0){
                                                        $p2 = $pop['p2'];
                                                    } 
                                                ?>

                                                <input name="p2" id="p2" type="number" step="any"
                                                    value="<?php echo $p2; ?>" placeholder="Monto"
                                                    data-wrapper-class="controlgroup-textinput ui-btn"
                                                    style="text-align: center;">

                                                <select name="pos2" id="poss2" data-mini="true" required>
                                                    <?php if (isset($pop['pos2_id'])) {
                                                            $listarPos = listarPos($pop['t2'],$pop['id_empresa_sede'],$pop['pos2_id']);
                                                            foreach ($listarPos as $fila) {
                                                                echo '<option value="'.$fila['id'].'" selected>'.$fila['id']." - ".$fila['nombrepos']." - ".$fila['moneda'].'</option>';
                                                            }
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </td>
                                        <td style="vertical-align: top;">
                                            <!-- medio de pago 3 -->
                                            <div data-role="controlgroup" data-mini="true">
                                                <select name="t3" id="t3" data-mini="true">
                                                    <option value="" selected>Forma de Pago 3</option>
                                                    <optgroup label="Seleccionar">
                                                   <?php
                                                    $formaPago = forPago();
                                                    $seleted='';
                                                    foreach ($formaPago as $fila) {
                                                    $seleted='';
                                                    if($pop['t3']== $fila['codigo_facturacion'])$seleted='selected';
                                                        echo '<option value="'.$fila['codigo_facturacion'].'"'.$seleted.'>'.$fila['tipotarjeta'].'</option>';
                                                    }
                                                    ?>
                                                   </optgroup>
                                                </select>
                                                <select name="banco3" id="banco3" data-mini="true">
                                                    <option value="">Banco</option>
                                                    <option value=1
                                                        <?php if(isset($pop['banco3']))$pop["banco3"] == 1 ? print("selected") : print(""); ?>>
                                                        BBVA</option>
                                                    <option value=2
                                                        <?php if(isset($pop['banco3']))$pop["banco3"] == 2 ? print("selected") : print(""); ?>>
                                                        BCP</option>
                                                    <option value=3
                                                        <?php if(isset($pop['banco3']))$pop["banco3"] == 3 ? print("selected") : print(""); ?>>
                                                        Dinners Club</option>
                                                    <option value=4
                                                        <?php if(isset($pop['banco3']))$pop["banco3"] == 4 ? print("selected") : print(""); ?>>
                                                        Interbank</option>
                                                    <option value=5
                                                        <?php if(isset($pop['banco3']))$pop["banco3"] == 5 ? print("selected") : print(""); ?>>
                                                        Otros</option>
                                                </select>
                                                <select name="tipotarjeta3" id="tipotarjeta3" data-mini="true">
                                                    <option value="">Tarjeta</option>
                                                    <optgroup label="Seleccionar">
                                                   <?php
                                                    $tipoTarjeta = tipTarjeta();
                                                    foreach ($tipoTarjeta as $fila) {
                                                        $seleted='';
                                                        if($pop['tipotarjeta3']== $fila['codigo_facturacion'])$seleted='selected';
                                                        echo '<option value="'.$fila['codigo_facturacion'].'"'.$seleted.'>'.$fila['formapago'].'</option>';
                                                    }
                                                    ?>
                                                   </optgroup>
                                                </select>

                                                <?php 
                                                    $numerocuotas3 = null;
                                                
                                                    if(isset($pop['numerocuotas3']) && $pop['p3'] != 0){
                                                        $numerocuotas3 = $pop['numerocuotas3'];
                                                    } 
                                                ?>

                                                <input name="numerocuotas3" id="numerocuotas3" type="number" step="any"
                                                    value="<?php echo $numerocuotas3; ?>" placeholder="Cuotas"
                                                    data-wrapper-class="controlgroup-textinput ui-btn"
                                                    style="text-align: center;">

                                                <?php 
                                                    $m1Select = "";
                                                    $m0Select = "";
                                                
                                                    if(isset($pop['m3'])){
                                                        if ($pop['m3'] == 1) {
                                                            $m1Select = "selected";
                                                        } else if ($pop['m3'] === 0 && $pop['p3'] != 0){
                                                            $m0Select = "selected";
                                                        }
                                                    } 
                                                ?>
                                                <select name="m3" id="m3" data-mini="true">
                                                    <option value="" selected>Moneda</option>
                                                    <option value=1 <?php echo $m1Select; ?>>$
                                                    </option>
                                                    <option value=0 <?php echo $m0Select; ?>>
                                                        S/.</option>
                                                </select>

                                                <?php 
                                                    $p3 = null;
                                                
                                                    if(isset($pop['p3']) && $pop['p3'] != 0){
                                                        $p3 = $pop['p3'];
                                                    } 
                                                ?>

                                                <input name="p3" id="p3" type="number" step="any"
                                                    value="<?php echo $p3; ?>" placeholder="Monto"
                                                    data-wrapper-class="controlgroup-textinput ui-btn"
                                                    style="text-align: center;">

                                                <select name="pos3" id="poss3" data-mini="true" required>
                                                    <?php if (isset($pop['pos3_id'])) {
                                                            $listarPos = listarPos($pop['t3'],$pop['id_empresa_sede'],$pop['pos3_id']);
                                                            foreach ($listarPos as $fila) {
                                                                echo '<option value="'.$fila['id'].'" selected>'.$fila['id']." - ".$fila['nombrepos']." - ".$fila['moneda'].'</option>';
                                                            }
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                <div id="alerta-medios-pago"><em>La suma de montos de los medios de pago no hacen el
                                        total a pagar*</em></div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <hr>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                Referencia <em>(este campo no es obligatorio)</em>:
                                <textarea name="comentarios" id="comentarios" cols="40" rows="5"><?php if(isset($pop['comentarios']))echo $pop['comentarios']; ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>Número de comprobante de referencia <em>(este campo no es obligatorio)</em>: <input
                                    type="text" name="comprobante_referencia" id="comprobante_referencia" maxlength="50"
                                    value="<?php if(isset($pop['comprobante_referencia']))echo $pop['comprobante_referencia']; ?>"></td>
                        </tr>
                    </table>
                    <div id="ocultarBoton">
                        <?php
                        if ($data_user["role"] == '3' or $data_user["role"] == '10') {
                            print('<input type="submit" name="guardar" value="GUARDAR" data-icon="check" data-iconpos="left" data-mini="true" data-theme="b" data-inline="true" />');
                        } ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div data-role="page" id="lista_vouchers" data-add-back-btn="true">
        <div data-role="header">
            <h1>Lista de Vouchers Virtuales</h1>
        </div>

        <div data-role="content">
            <table data-role="table" id="movie-table" data-mode="reflow" class="ui-responsive" style="width: 100%;">
                <thead>
                    <tr>
                        <th data-priority="1">#</th>
                        <th data-priority="2">Voucher</th>
                        <th data-priority="3">Paciente</th>
                        <th data-priority="4">Fecha</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $consulta = $db->prepare(
                        "SELECT
                        ma.nombre_base, ma.nombre_original, hg.fecha_voucher, hp.ape apellidos, hp.nom nombres, hg.id
                        from hc_gineco hg
                        inner join hc_paciente hp on hp.dni = hg.dni
                        inner join man_archivo ma on ma.id = hg.voucher_id
                        where hg.estadoconsulta_ginecologia_id = 4
                        order by hg.id desc;"
                    );

                    $consulta->execute();
                    $index = 1;

                    while ($data = $consulta->fetch(PDO::FETCH_ASSOC)) {
                        print(
                            '<tr>
                                <th>' . $index++ . '</th>
                                <td><a href="archivo/' . $data['nombre_base'] . '" data-rel="external" target="_blank">' . mb_strtolower($data['nombre_original']) . '</a></td>
                                <td>' . ucwords(mb_strtolower($data['apellidos'])) . ' ' . ucwords(mb_strtolower($data['nombres'])) . '</td>
                                <td>' . $data['fecha_voucher'] . '</td>
                            </tr>'
                        );
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div data-role="dialog" id="lista_codigo_atencion" data-add-back-btn="true">
        <div data-role="header">
            <h1>Código Único de Atención</h1>
        </div>

        <div data-role="content">
            <table data-role="table" id="table_codigo_atencion" data-mode="reflow" class="ui-responsive">
                <thead>
                    <tr>
                        <th data-priority="1">Item</th>
                        <th data-priority="2">Código</th>
                        <th data-priority="3">Área</th>
                        <th data-priority="4">Médico</th>
                        <th data-priority="5">Paciente</th>
                        <th data-priority="6">Fecha</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <input type="button" value="Seleccionar" id="seleccionar_atencion" data-inline="true" data-icon="check"
                data-iconpos="left" data-mini="true" data-theme="b">
        </div>
    </div>

    <script>
        var tipoDniG = $(this).attr("tip");
    </script>

    <?php include ($_SERVER["DOCUMENT_ROOT"] . "/_componentes/pago.php"); ?>
    <?php include ($_SERVER["DOCUMENT_ROOT"] . "/_componentes/pago_ini.php"); ?>
    <?php include ($_SERVER["DOCUMENT_ROOT"] . "/_componentes/pago_form.php"); ?>
    <?php include ($_SERVER["DOCUMENT_ROOT"] . "/_componentes/pago_total.php"); ?>
    <?php include ($_SERVER["DOCUMENT_ROOT"] . "/js/facturacion_empresa.php"); ?>
</body>

</html>