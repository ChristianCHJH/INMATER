<?php
session_start();
/* ini_set("display_errors", "1");
error_reporting(E_ALL); */
error_reporting(error_reporting() & ~E_NOTICE);
date_default_timezone_set('America/Lima'); ?>
<!DOCTYPE HTML>
<html>
<head>
    <?php
    $login = $_SESSION['login'];
    if (!$login) {
        echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=http://" . $_SERVER['HTTP_HOST'].substr(dirname(__FILE__), strlen($_SERVER['DOCUMENT_ROOT'])) . "'>";
    }

    require($_SERVER["DOCUMENT_ROOT"] . "/config/environment.php");
    require($_SERVER["DOCUMENT_ROOT"] . "/_database/database.php");
    require($_SERVER["DOCUMENT_ROOT"] . "/_database/database_log.php"); ?>

    <title>Inmater Clínica de Fertilidad | Auditoría de Facturación</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/chosen.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
    <link rel="stylesheet" href="css/bootstrap.v4/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="css/global.css" crossorigin="anonymous">
</head>

<body>
    <div class="loader"><img src="_images/load.gif" alt="inmater "></div>

    <div class="container">
        <nav aria-label="breadcrumb"><a class="breadcrumb" href="/"><img src="_libraries/open-iconic/svg/x.svg" height="18" width="18" alt="icon name"></a></nav>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Inicio</li>
                <li class="breadcrumb-item active" aria-current="page"><em>Reporte de Auditoría</em></li>
            </ol>
        </nav>

        <?php
        // iniciar variables
        $where = "";
        $ini = "";
        $fin = "";
        $serie_cpe = "";
        $correlativo_cpe = "";
        $recibo_tip = "";
        $recibo_id = "";

        if (isset($_POST) && !empty($_POST)) {
            // print("<pre>"); print_r($_POST); print("</pre>");
            $where = " and r.id = '0'";
            /* if (isset($_POST["ini"]) && !empty($_POST["ini"]) && isset($_POST["fin"]) && !empty($_POST["fin"])) {
                $ini = $_POST["ini"];
                $fin = $_POST["fin"];
                $where .= " and r.fec between '$ini' and '$fin'";
            } else {
                $ini = $fin = "";
            }

            if (isset($_POST["serie_cpe"]) && !empty($_POST["serie_cpe"]) && isset($_POST["correlativo_cpe"]) && !empty($_POST["correlativo_cpe"])) {
                $stmt = $db->prepare("SELECT tip_recibo recibo_tip, id_recibo recibo_id, serie_cpe, correlativo_cpe
                from facturacion_recibo_mifact_response frmr
                where correlativo_cpe ilike ? and serie_cpe ilike ? and estado_documento in ('101', '102', '103')
                limit 1 offset 0");
                $stmt->execute(["%" . $_POST['correlativo_cpe'] . "%", "%" . $_POST['serie_cpe'] . "%"]);

                if ($stmt->rowCount() > 0) {
                    $data = $stmt->fetch(PDO::FETCH_ASSOC);
                    $serie_cpe = $data["serie_cpe"];
                    $correlativo_cpe = $data["correlativo_cpe"];
                    $where .= " and r.recibo_tip = " . $data["recibo_tip"] . " and r.recibo_id = " . $data["recibo_id"];
                } else {
                    $where .= " and r.id = 0";
                }
            }

            if (isset($_POST["recibo_tip"]) && !empty($_POST["recibo_tip"]) && isset($_POST["recibo_id"]) && !empty($_POST["recibo_id"])) {
                $recibo_tip = $_POST["recibo_tip"];
                $recibo_id = $_POST["recibo_id"];
                $where .= " and r.recibo_tip = $recibo_tip and r.recibo_id = $recibo_id";
            }

            if ($where == "") {
                $where = " and r.id = 0";
            } */
        } else {
            $where .= " and r.id = '0'";
        }

        // print($where);
        /* $stmt = $dblog->prepare("SELECT
            upper(mc.nombre) tipo_comprobante, mts.nombre tipo_servicio, mtf.abreviatura tipo_documento_facturacion, r.*
            from recibos r
            inner join appinmater_modulo.man_comprobantes mc on mc.id = r.recibo_tip
            inner join appinmater_modulo.man_tipo_servicio mts on mts.id = r.t_ser
            inner join appinmater_modulo.man_tipo_documento_facturacion mtf on mtf.id = r.id_tipo_documento_facturacion
            where r.action = 'U' and 1=1$where
            order by r.id desc"); */

        $stmt = $db->prepare("SELECT id codigo, abreviatura nombre
            from man_tipo_documento_facturacion
            where estado = 1;");
        $stmt->execute();
        $info_tipodocumentofacturacion = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        array_unshift($info_tipodocumentofacturacion, "-");
        
        $stmt = $db->prepare("SELECT id, nombre
            from man_comprobantes
            where estado = 1;");
        $stmt->execute();
        $info_tipocomprobante = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        array_unshift($info_tipocomprobante, "-");

        $info_mediopago = [
            "-",
            "Efectivo",
            "-",
            "Depósito",
            "VISA",
            "MASTERCARD",
            "Link",
        ];
        $info_banco = [
            "-",
            "BBVA",
            "BCP",
            "Dinners Club",
            "Interbank",
            "Otros",
        ];
        $info_moneda = [
            "S/.",
            "USD",
        ];

        $stmt = $dblog->prepare("SELECT
            r.id, r.recibo_tip, r.recibo_id
            , r.dni, r.nom
            , r.fec, log1.fec fec_log
            , r.id_tipo_documento_facturacion, log1.id_tipo_documento_facturacion id_tipo_documento_facturacion_log
            , r.ruc, log1.ruc ruc_log, r.raz, log1.raz raz_log, r.direccionfiscal, log1.direccionfiscal direccionfiscal_log
            , r.t1, log1.t1 t1_log, r.t2, log1.t2 t2_log, r.t3, log1.t3 t3_log
            , r.banco1, log1.banco1 banco1_log, r.banco2, log1.banco2 banco2_log, r.banco3, log1.banco3 banco3_log
            , r.m1, log1.m1 m1_log, r.m2, log1.m2 m2_log, r.m3, log1.m3 m3_log
            , r.p1, log1.p1 p1_log, r.p2, log1.p2 p2_log, r.p3, log1.p3 p3_log
            , r.anu, log1.anu anu_log, r.comentarios, log1.comentarios comentarios_log, r.comprobante_referencia, log1.comprobante_referencia comprobante_referencia_log
            , r.action, log1.action action_log
            , log1.createdate, r.idusercreate
            from appinmater_log.recibos r
            inner join appinmater_log.recibos log1 on log1.recibo_tip = r.recibo_tip and log1.recibo_id = r.recibo_id
            and (
                (r.action = 'I')
                or (r.fec <> log1.fec)
                or (r.id_tipo_documento_facturacion <> log1.id_tipo_documento_facturacion) or (r.ruc <> log1.ruc) or (r.raz <> log1.raz) or (r.direccionfiscal <> log1.direccionfiscal)
                or (r.comprobante_referencia <> log1.comprobante_referencia)
                or (r.t1 <> log1.t1) or (r.t2 <> log1.t2) or (r.t3 <> log1.t3)
                or (r.banco1 <> log1.banco1) or (r.banco2 <> log1.banco2) or (r.banco3 <> log1.banco3)
                or (r.m1 <> log1.m1) or (r.m2 <> log1.m2) or (r.m3 <> log1.m3)
                or (r.p1 <> log1.p1) or (r.p2 <> log1.p2) or (r.p3 <> log1.p3)
                or (r.anu <> log1.anu) or (r.comentarios <> log1.comentarios) or (r.comprobante_referencia <> log1.comprobante_referencia)
            )
            where r.action = 'I'
            order by r.recibo_tip, r.recibo_id, log1.createdate desc;");
        $stmt->execute();
        $rows = $stmt->fetchAll(); ?>

        <div class="card mb-3">
            <input type="hidden" name="conf">
            <h5 class="card-header"><b>Filtros</b></h5>

            <div class="card-body">
                <form action="" method="post" data-ajax="false" name="form2">
                    <!-- <div class="row pb-2">
                        <div class="input-group input-group-sm col-12 col-sm-12 col-md-12 col-lg-6">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Tipo comprobante</span>
                            </div>

                            <select class="form-control form-control-sm mostrar" name="recibo_tip" id="recibo_tip">
                                <option value="">Seleccionar</option>
                                <?php
                                $stmt1 = $db->prepare("SELECT id, nombre from man_comprobantes mc where estado = 1;");
                                $stmt1->execute();
                                $rows1 = $stmt1->fetchAll();
                                foreach ($rows1 as $info) {
                                    print("<option value='" . $info['id'] . "' " . ($recibo_tip == $info['id'] ? " selected" : "") .  ">" . mb_strtoupper($info['nombre']) . "</option>");
                                } ?>
                            </select>

                            <div class="input-group-prepend">
                                <span class="input-group-text">N° comprobante</span>
                            </div>
                            <input class="form-control form-control-sm mostrar" id="recibo_id" name="recibo_id" type="text" value="<?php print($recibo_id); ?>">
                        </div>

                        <div class="input-group input-group-sm col-12 col-sm-12 col-md-12 col-lg-6">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Serie electrónico</span>
                            </div>
                            <input class="form-control form-control-sm mostrar" id="serie_cpe" name="serie_cpe" type="text" value="<?php print($serie_cpe); ?>">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Correlativo</span>
                            </div>
                            <input class="form-control form-control-sm mostrar" id="correlativo_cpe" name="correlativo_cpe" type="text" value="<?php print($correlativo_cpe); ?>">
                        </div>
                    </div> -->

                    <div class="row pb-2">
                        <div class="input-group input-group-sm col-12 col-sm-12 col-md-12 col-lg-6">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Mostrar desde</span>
                            </div>
                            <input class="form-control form-control-sm mostrar" id="ini" name="ini" type="date" value="<?php print($ini); ?>">
                            <div class="input-group-prepend">
                                <span class="input-group-text">hasta</span>
                            </div>
                            <input class="form-control form-control-sm mostrar" id="fin" name="fin" type="date" value="<?php print($fin); ?>">
                        </div>

                        <div class="col-12 col-sm-12 col-md-12 col-lg-2 input-group-sm">
                            <input class="form-control btn btn-danger btn-sm" type="Submit" name="agregar" value="Buscar"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mb-3">
            <h5 class="card-header" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">
            <?php
            print("<small><b>Fecha y hora de reporte: </b>" . date("Y-m-d H:i:s") . "
                <b>, Total Registros: </b>".count($rows).'
                <form method="post" action="_operaciones/auditoria-facturacion.php" target="_blank">
                    <input type="hidden" name="tipo" value="descargar_reporte">
                    <input type="hidden" name="repo_ini" id="repo_ini" value="' . $ini . '">
                    <input type="hidden" name="repo_fin" id="repo_fin" value="' . $fin . '">
                    <input type="hidden" name="repo_recibotip" id="repo_recibotip" value="' . $recibo_tip . '">
                    <input type="hidden" name="repo_reciboid" id="repo_reciboid" value="' . $recibo_id . '">
                    <input type="hidden" name="repo_seriecpe" id="repo_seriecpe" value="' . $serie_cpe . '">
                    <input type="hidden" name="repo_correlativocpe" id="repo_correlativocpe" value="' . $correlativo_cpe . '">
                    <input type="hidden" name="columnas" id="columnas">
                    <!-- <b>Descargar: </b><a href=\'javascript:void(0)\' onclick="$(\'#columnas\').val($(\'table input:checkbox:checked\').map(function(){return $(this).val();}).get()); this.closest(\'form\').submit(); return false;" style=\"font-size: 14px;\" class=\"font-italic\">
                        <img src="_images/excel.png" height="18" width="18" alt="icon name">
                    </a> -->
                </form></small>'); ?>
            </h5>

            <table width="100%" class='table table-sm table-responsive table-bordered align-middle header-fixed' style='height: 50vh; table-layout: fixed;' data-filter="true" data-input="#filtro" id="repo_fragmentacion">
                <thead class="thead-dark">
                    <tr>
                        <th colspan="3" class="text-center align-bottom">Auditoría</th>
                        <th colspan="3" class="text-center align-bottom">Información de comprobante</th>
                        <th colspan="2" class="text-center align-bottom">Datos del paciente</th>
                        <!-- <th colspan="3" class="text-center align-bottom">Servicios contratados</th> -->
                        <th colspan="4" class="text-center align-bottom">Datos de facturación</th>
                        <!-- <th colspan="4" class="text-center align-bottom">Total a pagar</th> -->
                        <th colspan="12" class="text-center align-bottom">Formas de pago</th>
                        <!-- <th colspan="1" class="text-center align-bottom" style="min-width: 200px;">Otros servicios</th> -->
                        <th colspan="3" class="text-center align-bottom">Estado de comprobante</th>
                    </tr>

                    <tr>
                        <!-- informacion de auditoria -->
                        <th class="text-center align-bottom" style="min-width: 160px;">Fecha</th>
                        <th class="text-center align-bottom" style="min-width: 50px;">Estado</th>
                        <th class="text-center align-bottom" style="min-width: 160px;">Usuario</th>
                        <!-- informacion de comprobante -->
                        <th class="text-center align-bottom" style="min-width: 100px;">Fecha de emisión</th>
                        <th class="text-center align-bottom" style="min-width: 140px;">Tipo Número comprobante</th>
                        <th class="text-center align-bottom" style="min-width: 140px;">Serie Correlativo electrónico</th>
                        <!-- datos de paciente -->
                        <th class="text-center align-bottom">Número documento</th>
                        <th class="align-bottom" style="min-width: 300px;">Apellidos y nombres</th>
                        <!-- servicios contratados -->
                        <!-- <th class="text-center align-bottom" style="min-width: 200px;">Tipo de servicio</th> -->
                        <!-- <th class="text-center align-bottom">Paquetes</th> -->
                        <!-- <th class="text-center align-bottom" style="min-width: 500px;">Servicios</th> -->
                        <!-- datos de facturacion -->
                        <!-- <th class="text-center align-bottom">Correo electrónico</th> -->
                        <th class="text-center align-bottom">Tipo de documento</th>
                        <th class="text-center align-bottom">Número de documento</th>
                        <th class="text-center align-bottom" style="min-width: 300px;">Nombres</th>
                        <th class="text-center align-bottom" style="min-width: 300px;">Dirección</th>
                        <!-- total a pagar -->
                        <!-- <th class="text-center align-bottom">Moneda</th> -->
                        <!-- <th class="text-center align-bottom">Total</th> -->
                        <!-- <th class="text-center align-bottom">Descuento</th> -->
                        <!-- <th class="text-center align-bottom">Total a cancelar</th> -->
                        <!-- formas de pago -->
                        <th class="text-center align-bottom" style="min-width: 100px;">Medio de pago 1</th>
                        <th class="text-center align-bottom" style="min-width: 100px;">Moneda 1</th>
                        <th class="text-center align-bottom" style="min-width: 100px;">Monto 1</th>
                        <th class="text-center align-bottom" style="min-width: 100px;">Banco 1</th>
                        <!-- <th class="text-center align-bottom" style="min-width: 100px;">Tipo de tarjeta 1</th>
                        <th class="text-center align-bottom" style="min-width: 100px;">N° de cuotas 1</th> -->
                        <th class="text-center align-bottom" style="min-width: 100px;">Medio de pago 2</th>
                        <th class="text-center align-bottom" style="min-width: 100px;">Moneda 2</th>
                        <th class="text-center align-bottom" style="min-width: 100px;">Monto 2</th>
                        <th class="text-center align-bottom" style="min-width: 100px;">Banco 2</th>
                        <!-- <th class="text-center align-bottom" style="min-width: 100px;">Tipo de tarjeta 2</th>
                        <th class="text-center align-bottom" style="min-width: 100px;">N° de cuotas 2</th> -->
                        <th class="text-center align-bottom" style="min-width: 100px;">Medio de pago 3</th>
                        <th class="text-center align-bottom" style="min-width: 100px;">Moneda 3</th>
                        <th class="text-center align-bottom" style="min-width: 100px;">Monto 3</th>
                        <th class="text-center align-bottom" style="min-width: 100px;">Banco 3</th>
                        <!-- <th class="text-center align-bottom" style="min-width: 100px;">Tipo de tarjeta 3</th>
                        <th class="text-center align-bottom" style="min-width: 100px;">N° de cuotas 3</th> -->
                        <!-- otros servicios -->
                        <!-- <th class="text-center align-bottom">Estado Anglolab</th> -->
                        <!-- estado de comprobante -->
                        <th class="text-center align-bottom">Comentarios</th>
                        <th class="text-center align-bottom">Comprobante de referencia</th>
                        <th class="text-center align-bottom">Anulado</th>
                    </tr>

                    <tr>
                        <!-- informacion de auditoria -->
                        <th class="text-center align-bottom"><input type="checkbox" value="createdate" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="action" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="idusercreate" checked></th>
                        <!-- informacion de comprobante -->
                        <th class="text-center align-bottom"><input type="checkbox" value="fecha_emision" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="tipo_numero_comprobante" checked></th>
                        <!-- <th class="text-center align-bottom"><input type="checkbox" value="numero_comprobante" checked></th> -->
                        <th class="text-center align-bottom"><input type="checkbox" value="serie_correlativo_cpe" checked></th>
                        <!-- <th class="text-center align-bottom"><input type="checkbox" value="correlativo_cpe" checked></th> -->
                        <!-- datos de paciente -->
                        <th class="text-center align-bottom"><input type="checkbox" value="numero_documento" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="apellidos_nombres" checked></th>
                        <!-- <th class="text-center align-bottom"><input type="checkbox" value="medico" checked></th> -->
                        <!-- servicios contratados -->
                        <!-- <th class="text-center align-bottom"><input type="checkbox" value="t_ser" checked></th> -->
                        <!-- <th class="text-center align-bottom"><input type="checkbox" value="pak" checked></th> -->
                        <!-- <th class="text-center align-bottom"><input type="checkbox" value="ser" checked></th> -->
                        <!-- datos de facturacion -->
                        <!-- <th class="text-center align-bottom"><input type="checkbox" value="correo_electronico" checked></th> -->
                        <th class="text-center align-bottom"><input type="checkbox" value="id_tipo_documento_facturacion" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="ruc" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="raz" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="direccionfiscal" checked></th>
                        <!-- total a pagar -->
                        <!-- <th class="text-center align-bottom"><input type="checkbox" value="mon" checked></th> -->
                        <!-- <th class="text-center align-bottom"><input type="checkbox" value="tot" checked></th> -->
                        <!-- <th class="text-center align-bottom"><input type="checkbox" value="descuento" checked></th> -->
                        <!-- <th class="text-center align-bottom"><input type="checkbox" value="total_cancelar" checked></th> -->
                        <!-- formas de pago -->
                        <th class="text-center align-bottom"><input type="checkbox" value="t1" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="m1" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="p1" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="banco1" checked></th>
                        <!-- <th class="text-center align-bottom"><input type="checkbox" value="tipotarjeta1" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="numerocuotas1" checked></th> -->
                        <th class="text-center align-bottom"><input type="checkbox" value="t2" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="m2" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="p2" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="banco2" checked></th>
                        <!-- <th class="text-center align-bottom"><input type="checkbox" value="tipotarjeta2" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="numerocuotas2" checked></th> -->
                        <th class="text-center align-bottom"><input type="checkbox" value="t3" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="m3" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="p3" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="banco3" checked></th>
                        <!-- <th class="text-center align-bottom"><input type="checkbox" value="tipotarjeta3" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="numerocuotas3" checked></th> -->
                        <!-- otros servicios -->
                        <!-- <th class="text-center align-bottom"><input type="checkbox" value="anglo" checked></th> -->
                        <!-- estado de comprobante -->
                        <th class="text-center align-bottom"><input type="checkbox" value="comentarios" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="comprobante_referencia" checked></th>
                        <th class="text-center align-bottom"><input type="checkbox" value="anu" checked></th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $i=1;
                    foreach ($rows as $item) {
                        $servicio = "";
                        if (isset($item["ser"])) {
                            $servicio = str_replace("</td></tr><tr><td>", "<br>", $item["ser"]);
                            
                        } 
                            $servicio = str_replace("</td><td>", " - ", $servicio);
                            $servicio = str_replace("<tr><td>", "", $servicio);
                            $servicio = str_replace("</td></tr>", "", $servicio);

                        // buscar el correlativo electronico
                        $stmt = $db->prepare("SELECT correlativo_cpe, serie_cpe
                        from facturacion_recibo_mifact_response frmr
                        where tip_recibo = ? and id_recibo = ? and estado_documento in ('101', '102', '103')
                        limit 1 offset 0");
                        $stmt->execute([$item["recibo_tip"], $item["recibo_id"]]);
                        $correlativo_cpe = "";
                        $serie_cpe = "";

                        if ($stmt->rowCount() > 0) {
                            $data = $stmt->fetch(PDO::FETCH_ASSOC);
                            $correlativo_cpe = $data["correlativo_cpe"];
                            $serie_cpe = $data["serie_cpe"];
                        }

                        print('<tr>
                            <!-- informacion de auditoria -->
                            <td class="text-center">' . date('Y-m-d H:i:s', strtotime($item["createdate"])) . '</td>
                            <td class="text-center">' . $item["action_log"] . '</td>
                            <td class="text-center">' . $item["idusercreate"] . '</td>
                            <!-- informacion de comprobante -->
                            <td class="'.($item["fec"] != $item["fec_log"] ? "alert-danger": "").'">' . date('Y-m-d', strtotime($item["fec_log"])) . '</td>
                            <td class="text-center">' . mb_strtoupper($info_tipocomprobante[$item["recibo_tip"]]) . "-" . $item["recibo_id"] . '</td>
                            <!-- <td class="text-center">' . $item["recibo_id"] . '</td> -->
                            <td class="text-center">' . $serie_cpe . "-" . $correlativo_cpe . '</td>
                            <!-- <td class="text-center">' . $serie_cpe . '</td> -->
                            <!-- datos de paciente -->
                            <td class="text-center">' . $item["dni"] . '</td>
                            <td>' . mb_strtoupper($item["nom"]) . '</td>
                            <!-- <td>' . mb_strtoupper($item["med"]) . '</td> -->
                            <!-- servicios contratados -->
                            <!-- <td class="text-center">' . mb_strtoupper($item["tipo_servicio"]) . '</td> -->
                            <!-- <td>' . $item["pak"] . '</td> -->
                            <!-- <td class="text-center">' . $servicio . '</td> -->
                            <!-- datos de facturacion -->
                            <!-- <td class="text-center">' . mb_strtolower($item["correo_electronico"]) . '</td> -->
                            <td class="'.($item["id_tipo_documento_facturacion"] != $item["id_tipo_documento_facturacion_log"] ? "alert-danger": "").'">' . $info_tipodocumentofacturacion[$item["id_tipo_documento_facturacion_log"]] . '</td>
                            <td class="'.($item["ruc"] != $item["ruc_log"] ? "alert-danger": "").'">' . $item["ruc_log"] . '</td>
                            <td class="'.($item["raz"] != $item["raz_log"] ? "alert-danger": "").'">' . mb_strtoupper($item["raz_log"]) . '</td>
                            <td class="'.($item["direccionfiscal"] != $item["direccionfiscal_log"] ? "alert-danger": "").'">' . $item["direccionfiscal_log"] . '</td>
                            <!-- total a pagar -->
                            <!-- <td>' . $item["mon"] . '</td> -->
                            <!-- <td>' . $item["tot"] . '</td> -->
                            <!-- <td>' . $item["descuento"] . '</td> -->
                            <!-- <td>' . $item["total_cancelar"] . '</td> -->
                            <!-- formas de pago -->
                            <td class="text-center '.($item["t1"] != $item["t1_log"] ? "alert-danger": "").'">' . $info_mediopago[$item["t1_log"]] . '</td>
                            <td class="text-center '.($item["m1"] != $item["m1_log"] ? "alert-danger": "").'">' . $info_moneda[$item["m1_log"]] . '</td>
                            <td class="text-center '.($item["p1"] != $item["p1_log"] ? "alert-danger": "").'">' . number_format($item["p1_log"], 2) . '</td>
                            <td class="text-center '.($item["banco1"] != $item["banco1_log"] ? "alert-danger": "").'">' . $info_banco[$item["banco1_log"]] . '</td>
                            <td class="text-center '.($item["t2"] != $item["t2_log"] ? "alert-danger": "").'">' . $info_mediopago[$item["t2_log"]] . '</td>
                            <td class="text-center '.($item["m2"] != $item["m2_log"] ? "alert-danger": "").'">' . $info_moneda[$item["m2_log"]] . '</td>
                            <td class="text-center '.($item["p2"] != $item["p2_log"] ? "alert-danger": "").'">' . number_format($item["p2_log"], 2) . '</td>
                            <td class="text-center '.($item["banco2"] != $item["banco2_log"] ? "alert-danger": "").'">' . $info_banco[$item["banco2_log"]] . '</td>
                            <td class="text-center '.($item["t3"] != $item["t3_log"] ? "alert-danger": "").'">' . $info_mediopago[$item["t3_log"]] . '</td>
                            <td class="text-center '.($item["m3"] != $item["m3_log"] ? "alert-danger": "").'">' . $info_moneda[$item["m3_log"]] . '</td>
                            <td class="text-center '.($item["p3"] != $item["p3_log"] ? "alert-danger": "").'">' . number_format($item["p3_log"], 2) . '</td>
                            <td class="text-center '.($item["banco3"] != $item["banco3_log"] ? "alert-danger": "").'">' . $info_banco[$item["banco3_log"]] . '</td>
                            <!-- otros servicios -->
                            <!-- <td class="text-center">' . substr($item["anglo"], 0, 50) . '</td> -->
                            <!-- estado de comprobante -->
                            <td class="text-center '.($item["comentarios"] != $item["comentarios_log"] ? "alert-danger": "").'">' . $item["comentarios_log"] . '</td>
                            <td class="text-center '.($item["comprobante_referencia"] != $item["comprobante_referencia_log"] ? "alert-danger": "").'">' . $item["comprobante_referencia"] . '</td>
                            <td class="text-center '.($item["anu"] != $item["anu_log"] ? "alert-danger": "").'">' . ($item["anu_log"] == 0 ? "No": "Si") . '</td>
                        </tr>');
                    } ?>
                </tbody>
            </table>
        </div>
  </div>

    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.v4/bootstrap.min.js" crossorigin="anonymous"></script>

    <script>
        jQuery(window).load(function (event) {
            jQuery('.loader').fadeOut(1000);
        });

        $(document).ready(function () {
            $("#pro_descargar").val($("#protocolo").val());

            $(".mostrar").change(function () {
                $("#repo_ini").val($("#ini").val());
                $("#repo_fin").val($("#fin").val());
                $("#repo_recibotip").val($("#recibo_tip").val());
                $("#repo_reciboid").val($("#recibo_id").val());
                $("#repo_seriecpe").val($("#serie_cpe").val());
                $("#repo_correlativocpe").val($("#correlativo_cpe").val());
            });

            $("#protocolo").change(function () {
                $(".mostrar").val("");
            });

            $(document).on('input paste', '#protocolo', function(e){
                $("#pro_descargar").val($("#protocolo").val());
            });
        });
    </script>
</body>
</html>