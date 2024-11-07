<!DOCTYPE HTML>
<html>

<head>
    <title>Inmater Clínica de Fertilidad | Reporte de Ventas</title>
    <?php
     include 'seguridad_login.php'
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.v4/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/repo_conta.css">
    <link rel="stylesheet" type="text/css" href="css/global.css">
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="_themes/tema_lista_empresa.min.css" />
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <script type="text/javascript">
    var tableToExcel = (function() {
        var uri = 'data:application/vnd.ms-excel;base64,',
            template =
            '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>',
            base64 = function(s) {
                return window.btoa(unescape(encodeURIComponent(s)))
            },
            format = function(s, c) {
                return s.replace(/{(\w+)}/g, function(m, p) {
                    return c[p];
                })
            }
        return function(table, visita) {
            if (!table.nodeType) table = document.getElementById(table)
            var ctx = {
                worksheet: 'reporte_' + visita || 'reporte',
                table: table.innerHTML
            }
            window.location.href = uri + base64(format(template, ctx))
        }
    })();

    id_sede = 0;
    </script>
</head>

<body>
    <div class="loader">
        <img src="_images/load.gif" alt="">
    </div>
    <?php require '_includes/menu_facturacion.php'; ?>
    <div class='container1'>
        <?php
        $stmt = $db->prepare("SELECT * FROM usuario WHERE userx=?");
        $stmt->execute(array($login));
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        // iniciar variables
        $between = $nc_between = $mediopago = $tiposervicio = $medico = $recibo = $empresa = $ini = $fin = "";
        //
        if (isset($_POST) && !empty($_POST)) {
            if (isset($_POST["ini"]) && !empty($_POST["ini"]) && isset($_POST["fin"]) && !empty($_POST["fin"])) {
                $ini = $_POST['ini'];
                $fin = $_POST['fin'];
            } else {
                $ini = $fin = date('Y-m-d');
            }

            $between .= " and r.fec between '$ini' and ('$fin'::date + INTERVAL '1 day')::date";
            $nc_between .= " and nc.createdate between '$ini' and ('$fin'::date + INTERVAL '1 day')::date";
            

            if (isset($_POST["mediopago"]) && $_POST["mediopago"] != '') {
                $mediopago = $_POST["mediopago"];
                $between .= " and ( t1= $mediopago or t2=$mediopago or t3=$mediopago)";
                $nc_between .= " and ( t1= $mediopago or t2=$mediopago or t3=$mediopago)";
            }

            if (isset($_POST["tiposervicio"]) && $_POST["tiposervicio"] != "") {
                $tiposervicio = (int)$_POST["tiposervicio"];
                $between .= " and r.t_ser = $tiposervicio";
                $nc_between .= " and r.t_ser = $tiposervicio";
            }

            if ( isset($_POST["medico"]) && !empty($_POST["medico"]) ) {
                $medico = $_POST["medico"];
                $between .= " and r.med = '$medico'";
                $nc_between .= " and r.med = '$medico'";
            }

            if ( isset($_POST["recibo"]) && !empty($_POST["recibo"]) ) {
                $recibo = (int)$_POST["recibo"];
                $between .= " and r.id = $recibo";
                $nc_between .= " and r.id = $recibo";
            }

            if ( isset($_POST["empresa"]) && !empty($_POST["empresa"]) ) {
                $empresa = (int)$_POST["empresa"];
                $between .= " and r.id_empresa = $empresa";
                $nc_between .= " and r.id_empresa = $empresa";
            }

            if ( isset($_POST["id_sede"]) && !empty($_POST["id_sede"]) ) {
                $id_sede = (int)$_POST["id_sede"];
                $between .= " and r.id_empresa_sede = $id_sede";
                $nc_between .= " and r.id_empresa_sede = $id_sede";
            }
        } else {
            $ini = date('Y-m-d');
            $fin = date('Y-m-d', strtotime($ini . ' +1 day'));
            $between .= " and r.fec between '$ini' and '$fin' and r.id_empresa = 4";
            $nc_between .= " and nc.createdate between '$ini' and '$fin' and r.id_empresa = 4";
        }

        // realizar consulta
        $rRec = $db->prepare("SELECT * from (
                SELECT
                tdf.abreviatura tipo_documento_facturacion, r.cpe_serie serie_cpe, r.cpe_correlativo correlativo_cpe, r.idusercreate usuario_emisor, r.*
                from recibos r
                left join man_tipo_documento_facturacion tdf on tdf.id = r.id_tipo_documento_facturacion
                inner join usuario u on u.userx = r.idusercreate
                left join hc_paciente pac on pac.dni=r.dni
                left join hc_pareja par on par.p_dni=r.dni
                where 1=1$between
                group by tdf.abreviatura, r.cpe_serie, r.cpe_correlativo, r.idusercreate, r.tip, r.id
                ) t
                order by t.fec asc, t.id asc");
        $rRec->execute();    

            // realizar consulta de nc
    		$stmt = $db->prepare("SELECT
                    nc.id
                    , r.tip tip_comprobante_modifica, r.fec fecha_comprobante_modifica, res.serie_cpe, res.correlativo_cpe
                    , nc.createdate fecha, nc.serie, nc.correlativo, nc.comprobantetipo_id, mf.errors
                    , coalesce(pac.tip, par.p_tip) paciente_tipodocumento, r.dni paciente_documento, upper(r.nom) paciente_nombre, upper(r.med) medico
                    , nc.moneda_id facturacion_moneda, nc.total facturacion_total, df.numero facturacion_documento, df.nombre facturacion_nombre, df.correo facturacion_correo
                    , r.t_ser
                    , nc.idusercreate usuario_emisor
                    from factu_notacredito nc
                    inner join recibos r on r.tip = nc.recibo_tip and r.id = nc.recibo_id
                    left join facturacion_recibo_mifact_response res on res.id = (
                        select
                        a.id
                        from facturacion_recibo_mifact_response a
                        where a.estado = 1 and a.serie_cpe <> '' and a.correlativo_cpe <> '' and a.id_recibo = r.id and a.tip_recibo = r.tip
                        limit 1 offset 0
                    )
                    left join facturacion_recibo_mifact_response mf on mf.tip_recibo = 3 and mf.id_recibo = nc.id
                    left join hc_paciente pac on pac.dni=r.dni
                    left join hc_pareja par on par.p_dni=r.dni
                    inner join factu_datosfacturacion df on df.id = nc.datosfacturacion_id
                    where nc.estado = 1$nc_between
                    order by nc.id desc");
        $stmt->execute();
        ?>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="lista_facturacion.php">Inicio</a></li>
                <li class="breadcrumb-item">Reportes</li>
                <li class="breadcrumb-item active" aria-current="page">Ventas Consolidado</li>
            </ol>
        </nav>

        <div data-role="header">
            <div class="card mb-3">
                <h5 class="card-header">Filtros</h5>
                <div class="card-body">
                    <form action="" method="post" data-ajax="false" id="form1">
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Mostrar Desde</span>
                                    <input class="form-control form-control-sm" name="ini" type="date"
                                        value="<?php print($ini); ?>" id="example-datetime-local-input">
                                    <span class="input-group-text">Hasta</span>
                                    <input class="form-control form-control-sm" name="fin" type="date"
                                        value="<?php print($fin); ?>" id="example-datetime-local-input">
                                </div>
                            </div>

                            <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Medio Pago</span>
                                    <select class="form-control form-control-sm" name="mediopago">
                                        <option value="">Todos</option>
                                        <optgroup label="Seleccionar">
                                        <?php
                                                $formaPago = forPago();
                                                foreach ($formaPago as $fila) {
                                                    echo '<option value="' . $fila['codigo_facturacion'] . '">' . $fila['tipotarjeta'] . '</option>';
                                                }
                                                ?>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Tipo Servicio</span>
                                    <select class="form-control form-control-sm" name="tiposervicio">
                                        <option value="">Todos</option>
                                        <optgroup label="Seleccionar">
                                        <?php
                                            $consulta = $db->prepare("select codigo, nombre from man_tipo_servicio where estado=1");
                                            $consulta->execute();
                                            $consulta->setFetchMode(PDO::FETCH_ASSOC);
                                            $data1 = $consulta->fetchAll();
                                            foreach ($data1 as $row) {
                                                $sel="";
                                                if ($tiposervicio == $row['codigo']) {
                                                    $sel="selected";
                                                }

                                                print("<option value=".$row['codigo']." $sel>".mb_strtoupper($row['nombre'])."</option>");
                                            }
                                        ?>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row pb-2">

                        <div class="col-12 col-sm-12 col-md-3 col-lg-2 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">EMPRESA</span>
                                    <select class="form-control form-control-sm" name="empresa" id="empresa" >
                                        <optgroup label="Seleccionar">
                                        <?php
                                            $consulta = $db->prepare("SELECT id, nom_comercial FROM man_empresas ");
                                            $consulta->execute();
                                            while ($row = $consulta->fetch(PDO::FETCH_ASSOC)) {
                                                $selected = "";

                                                if (isset($_POST['empresa']) && $_POST['empresa'] == $row['id']) {
                                                    $selected = "selected";
                                                }
                                                if (!isset($_POST['empresa']) && $row['id'] === 4){
                                                    $selected = "selected";
                                                }

                                                print("<option value='" . $row['id'] . "' $selected>" . $row['nom_comercial'] . "</option>");
                                            }
                                        ?>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                        
                            <?php if (isset($_POST['id_sede']) && $_POST['id_sede']) {?>
                                <script>
                                    id_sede = <?php echo $_POST['id_sede']; ?>;
                                </script>
                            <?php } ?>
                            <div class="col-12 col-sm-12 col-md-3 col-lg-2 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">SEDE</span>
                                    <select class="form-control form-control-sm" name="id_sede" id="id_sede" >
                                        <optgroup label="Seleccionar"></optgroup>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Médico</span>
                                    <select class="form-control form-control-sm" name="medico">
                                        <option value="">Todos</option>
                                        <optgroup label="Seleccionar">
                                        <?php
                                            $consulta = $db->prepare("select nombre from man_medico where estado=1");
                                            $consulta->execute();
                                            $consulta->setFetchMode(PDO::FETCH_ASSOC);
                                            $data1 = $consulta->fetchAll();
                                            foreach ($data1 as $row) {
                                                $sel="";
                                                if ($medico == $row['nombre']) {
                                                    $sel="selected";
                                                }

                                                print("<option value='".$row['nombre']."' $sel>".mb_strtoupper($row['nombre'])."</option>");
                                            }
                                        ?>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>

                            <div class="col-2 col-sm-2 col-md-3 col-lg-3 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">N° Recibo</span>
                                    <input type="text" class="form-control form-control-sm" name="recibo"
                                        value="<?php print($recibo); ?>">
                                </div>
                            </div>

                            <input type="Submit" class="btn btn-danger btn-sm" name="Mostrar" value="Mostrar" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
            $item = 0;
            print("
            <table class='table table-responsive table-bordered align-middle header-fixed table-sm Datos'>
                <thead class='thead-dark'>
                    <tr>
    					<th class='text-center' style='min-width: 150px;'>Fecha</th>
                        <th>Tipo Documento</th>
                        <th style='min-width: 120px;'>N° Electrónico</th>
                        <th style='min-width: 150px;'>Voucher Referencia</th>
                        <th class='text-center'>Usuario<br>Emisor</th>
                        <th>Tipo Comprobante Electrónico<br>que Modifica</th>
                        <th style='min-width: 120px;'>Comprobante Electrónico<br>que Modifica</th>
                        <th>F. Comprobante Electrónico</th>
    					<th>Tipo Documento</th>
    					<th>Cliente</th>
    					<th style='min-width: 400px;'>Razón Social</th>
                        <th>Total Soles</th>
                        <th>Total Dólares</th>
                        <th>Moneda</th>
                        <th>Efectivo Soles</th>
                        <th>Depósito Soles</th>
    					<th>Transferencia Soles</th>
    					<th>Link Tarjeta Soles</th>
                        <th>Visa Soles</th>
    					<th>Mastercard Soles</th>
    					<th>Diners Soles</th>
    					<th>Amex Soles</th>
                        <th>Efectivo Dólares</th>
    					<th>Depósito Dólares</th>
    					<th>Transferencia Dólares</th>
    					<th>Link Tarjeta Dólares</th>
                        <th>Visa Dólares</th>
    					<th>Mastercard Dólares</th>
    					<th>Diners Dólares</th>
    					<th>Amex Dólares</th>
    					<th>ZIPAY COD COMERCIO</th>
    					<th>NIUBIZ COD COMERCIO</th>
    					<th class='text-center' style='min-width: 120px;'>Condición de pago</th>
                    </tr>
                </thead>
                <tbody>
            ");

            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $nc_color = '#5eb7b7';
                $tiposervicio = '';
                $total1 = 0;

                if (!empty($data["errors"])) {
                    $nc_color = '#F9CCCD';
                }

                // consultar detalle nc
                $stmt1 = $db->prepare("SELECT
                ncd.id, ncd.nombre, ncd.precio, ncd.servicio_id, UPPER(mts.nombre) tiposervicio,
                s.nombre sede, ccc.codigo centrocosto_codigo, csco.codigo subcentrocosto_codigo, csco.descripcion subcentrocosto, cuc.codigo cuenta_contable
                FROM factu_notacredito_detalle ncd
                INNER JOIN recibo_serv rs ON rs.id = ncd.servicio_id
                INNER JOIN man_tipo_servicio mts ON mts.id = rs.tip
                LEFT JOIN conta_sub_centro_costo csco ON csco.id = rs.conta_sub_centro_costo_id
                LEFT JOIN conta_centro_costo ccc ON ccc.id = csco.conta_centro_costo_id
                LEFT JOIN sedes s ON s.id = ccc.sede_id AND s.codigo_contabilidad IS NOT NULL
                LEFT JOIN conta_cuenta_contable cuc on cuc.id = csco.conta_cuenta_contable_id
                WHERE ncd.cantidad <> 0 AND ncd.factu_notacredito_id = ?
                ORDER BY ncd.id ASC");
                $stmt1->execute([$data["id"]]);

                while ($data1 = $stmt1->fetch(PDO::FETCH_ASSOC)) {
                    $total1 = (float)$total1 + (float)$data1["precio"];
                }

                if ($data['t_ser'] == 1) { $tiposervicio = 'REPRODUCCIÓN ASISTIDA'; }
                if ($data['t_ser'] == 2) { $tiposervicio = 'ANDROLOGÍA'; }
                if ($data['t_ser'] == 3) { $tiposervicio = 'PROCEDIMIENTOS SALA'; }
                if ($data['t_ser'] == 4) { $tiposervicio = 'ANÁLISIS SANGRE'; }
                if ($data['t_ser'] == 5) { $tiposervicio = 'PERFILES'; }
                if ($data['t_ser'] == 6) { $tiposervicio = 'ECOGRAFÍA'; }
                if ($data['t_ser'] == 7) { $tiposervicio = 'ADICIONALES'; }

                print("<tr style='background-color: ".$nc_color.";'>
                    <td class='text-center'>".date("d-m-Y", strtotime($data['fecha']))."</td>
                    <td>".($data['comprobantetipo_id'] == 3 ? "NC" : "ND")."</td>");

                print("<td>".$data["serie"]." - ".$data["correlativo"]."</td>");
                print("<td></td><td class='text-center'>".$data["usuario_emisor"]."</td>");

                if ($data['tip_comprobante_modifica'] == 1) { print("<td>BV</td>"); }
                if ($data['tip_comprobante_modifica'] == 2) { print("<td>FT</td>"); }

                print("<td>" . $data['serie_cpe'] . " - " . $data['correlativo_cpe'] . "</td>
                    <td>".date("d-m-Y", strtotime($data['fecha_comprobante_modifica']))."</td>
                    <td>".$data["paciente_tipodocumento"]."</td>
                    <td>'".$data["facturacion_documento"]."</td>
                    <td>".$data["facturacion_nombre"]."</td>
                    <td>".(empty($data["errors"]) ? ($data["facturacion_moneda"] == 1 ? number_format((float)$total1*-1, 2) : "") : "0.00")."</td>
                    <td>".(empty($data["errors"]) ? ($data["facturacion_moneda"] == 2 ? number_format((float)$total1*-1, 2) : "") : "0.00")."</td>
                    <td>".($data["facturacion_moneda"] == 1 ? "MN" : "US")."</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>");
            }

            while ($rec = $rRec->fetch(PDO::FETCH_ASSOC)) {
                // verificar numero de servicios
                $i=1; // $i=1
                $total=0;
                $anulado="";
                $total = 1;
                $cadena=$rec['ser'];

                if ($rec['anu'] == 1) {
                    $anulado="bgcolor='#F9CCCD'";
                }

                while ($i == 1) {
                    print("<tr $anulado>");
                    // fecha
                    print("<td class='text-center'>".date("d-m-Y", strtotime($rec['fec']))."</td>");
                    // tipo documento
                    if ($rec['tip'] == 1) { print("<td>BV</td>"); }
                    if ($rec['tip'] == 2) { print("<td>FT</td>"); }
                    if ($rec['tip'] == 3) { print("<td>BV Fisica</td>"); }
                    if ($rec['tip'] == 4) { print("<td>FT Fisica</td>"); }
                    print("<td>" . $rec['serie_cpe'] . " - " . $rec['correlativo_cpe'] . "</td>");
                    print("<td class='text-center'>" . $rec["comprobante_referencia"] . "</td>");
                    print('<td class="text-center">'.$rec["usuario_emisor"].'</td>');
                    // comprobante que modifica
                    print('<td></td><td></td><td></td>');
                    // tipo documento facturacion
                    print("<td class='text-center'>".mb_strtoupper($rec['tipo_documento_facturacion'])."</td>");
                    // ruc y razon social
                    print("<td>'".$rec['ruc']."</td><td>".mb_strtoupper($rec['raz'])."</td>");
                    // servicio, valor venta
                    $pos = strpos($cadena, "</tr>");
                    $tam = strlen($cadena);
                    $servicios = substr($cadena, 4, $pos-4);
                    $cadena = substr($cadena, $pos+5, $tam-3);
                    $demo=$moneda="";
                    $valorventacod=0;

                    if ($rec['anu'] == 1 or $rec['gratuito'] == 1) {
                        $valorventacod="0";
                    } else {
                        $valorventacod=$rec['tot'];
                    }

                    // tip (1: boleta, 2: factura)
                    // t_ser (1: RA, 2: Andro, 3: Sala, 4: Analisis, 5: Perfiles, 6: Ecografia, 7: Adicionales)
                    // mon (1: moneda origen, 2 cambio de moneda)
                    // t_ser (1, 2, 3: dolares; 4, 5, 6, 7: soles)
                    // tip (1: boleta, 2: factura)
                    if ($rec['anu'] == 1 or $rec['gratuito'] == 1) {
                        print('<td></td><td></td><td></td>');
                    } else {
                        if ($rec['tip'] == 1) {
                            if ($rec['t_ser'] == 1 or $rec['t_ser'] == 2 or $rec['t_ser'] == 3) {
                                if ($rec['mon'] == 1) {
                                    print("
                                    <td></td>
                                    <td>".number_format((float)($valorventacod - ($rec['descuento']/ $total)), 2)."</td>");
                                } else {
                                    print("
                                    <td>".number_format((float)($valorventacod - ($rec['descuento']/ $total)), 2)."</td>
                                    <td></td>");
                                }
                            } else {
                                if ($rec['mon'] == 1) {
                                    print("
                                    <td>".number_format((float)($valorventacod - ($rec['descuento']/ $total)), 2)."</td>
                                    <td></td>");
                                } else {
                                    print("
                                    <td></td>
                                    <td>".number_format((float)($valorventacod - ($rec['descuento']/ $total)), 2)."</td>");
                                }
                            }
                        } else {
                            if ($rec['t_ser'] == 1 or $rec['t_ser'] == 2 or $rec['t_ser'] == 3) {
                                if ($rec['mon'] == 1) {
                                    print("
                                    <td></td>
                                    <td>".number_format((float)($valorventacod - ($rec['descuento']/ $total)), 2)."</td>");
                                } else {
                                    print("
                                    <td>".number_format((float)($valorventacod - ($rec['descuento']/ $total)), 2)."</td>
                                    <td></td>");
                                }
                            } else {
                                if ($rec['mon'] == 1) {
                                    print("
                                    <td>".number_format((float)($valorventacod - ($rec['descuento']/ $total)), 2)."</td>
                                    <td></td>");
                                } else {
                                    print("
                                    <td></td>
                                    <td>".number_format((float)($valorventacod - ($rec['descuento']/ $total)), 2)."</td>");
                                }
                            }
                        }
                    }

                    
                    //moneda
                    if ($rec['t_ser'] == 1 || $rec['t_ser'] == 2 || $rec['t_ser'] == 3) {
                        if ($rec['mon'] == 1) {
                            $moneda = 'US';
                        } else {
                            $moneda = 'MN';
                        }
                    } else {
                        if ($rec['mon'] == 1) {
                            $moneda = 'MN';
                        } else {
                            $moneda = 'US';
                        }
                    }

                    // tipo de cambio
                    if ($rec['anu'] == 1 or $rec['gratuito'] == 1) {
                        print("<td></td>");
                    } else {
                        print("<td>".$moneda."</td>");
                    }

                    $anulado = "";
                    if ($rec['anu'] == 1) {
                        $anulado = "ANULADO";
                    }

                    $gratuito = "";
                    if ($rec['gratuito'] == 1) {
                        $gratuito = "GRATUITO";
                    }

                    if ($rec['anu'] == 1 or $rec['gratuito'] == 1) {
                        print('<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>');
                        print("<td class='text-center'>$anulado $gratuito</td>");
                    }

                    if ($i != 1 and $rec['anu'] != 1 and $rec['gratuito'] != 1) {
                        print('<td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>DEMO</td>');
                    }

                    if ($i == 1 and $rec['anu'] != 1 and $rec['gratuito'] != 1) {
                        $efectivo_soles = $efectivo_dolares = $deposito_soles = $deposito_dolares = $visa_soles = $visa_dolares = $mastercard_soles = $linktarjeta_soles = $linktarjeta_dolares = $mastercard_dolares = $transferencia_soles = $transferencia_dolares = 0;
                        $diners_dolares= $diners_soles = $amex_dolares= $amex_soles = 0;
                        for ($i2 = 1; $i2 <= 3; $i2++) {
                            $t = $rec["t$i2"];
                            $m = $rec["m$i2"];
                            $p = $rec["p$i2"];
                            if ($t == 1) {
                                if ($m == 1) {
                                    $efectivo_dolares += $p;
                                } else {
                                    $efectivo_soles += $p;
                                }
                            } elseif ($t == 3) {
                                if ($m == 1) {
                                    $deposito_dolares += $p;
                                } else {
                                    $deposito_soles += $p;
                                }
                            } elseif ($t == 4) {
                                if ($m == 1) {
                                    $visa_dolares += $p;
                                } else {
                                    $visa_soles += $p;
                                }
                            } elseif ($t == 5) {
                                if ($m == 1) {
                                    $mastercard_dolares += $p;
                                } else {
                                    $mastercard_soles += $p;
                                }
                            } elseif ($t == 6) {
                                if ($m == 1) {
                                    $linktarjeta_dolares += $p;
                                } else {
                                    $linktarjeta_soles += $p;
                                }
                            } elseif ($t == 7) {
                                if ($m == 1) {
                                    $transferencia_dolares += $p;
                                } else {
                                    $transferencia_soles += $p;
                                }
                            } elseif ($t == 8) {
                                if ($m == 1) {
                                    $diners_dolares += $p;
                                } else {
                                    $diners_soles += $p;
                                }
                            }elseif ($t == 9) {
                                if ($m == 1) {      
                                    $amex_dolares += $p;
                                } else {
                                    $amex_soles += $p;
                                }
                            }
                            
                        }

                                    

                                    $pos1 = !empty($rec["pos1_id"]) ? intval($rec["pos1_id"]) : 1;
                                    $pos2 = !empty($rec["pos2_id"]) ? intval($rec["pos2_id"]) : 1;
                                    $pos3 = !empty($rec["pos3_id"]) ? intval($rec["pos3_id"]) : 1;
                                    $niuCodCom1= posCodComercio($pos1,'NIUBIZ');
                                    $niuCodCom2= posCodComercio($pos2,'NIUBIZ');
                                    $niuCodCom3= posCodComercio($pos3,'NIUBIZ');
                                    $niuCod1=$niuCodCom1[0]['codigo']??' - ';
                                    $niuCod2=$niuCodCom2[0]['codigo']??' - ';
                                    $niuCod3=$niuCodCom3[0]['codigo']??' - ';
                                    $iziCodCom1= posCodComercio($pos1,'IZIPAY');
                                    $iziCodCom2= posCodComercio($pos2,'IZIPAY');
                                    $iziCodCom3= posCodComercio($pos3,'IZIPAY');
                                    $iziCod1=$iziCodCom1[0]['codigo']??' - ';
                                    $iziCod2=$iziCodCom2[0]['codigo']??' - ';
                                    $iziCod3=$iziCodCom3[0]['codigo']??' - ';

                                    

                        print("<td>$efectivo_soles</td>");
                        print("<td>$deposito_soles</td>");
                        print("<td>$transferencia_soles</td>");
                        print("<td>$linktarjeta_soles</td>");
                        print("<td>$visa_soles</td>");
                        print("<td>$mastercard_soles</td>");
                        print("<td>$diners_soles</td>");
                        print("<td>$amex_soles</td>");

                        print("<td>$efectivo_dolares</td>");
                        print("<td>$deposito_dolares</td>");
                        print("<td>$transferencia_dolares</td>");
                        print("<td>$linktarjeta_dolares</td>");
                        print("<td>$visa_dolares</td>");
                        print("<td>$mastercard_dolares</td>");
                        print("<td>$diners_dolares</td>");
                        print("<td>$amex_dolares</td>");
                        print("<td>$iziCod1 , $iziCod2 , $iziCod3</td>");
                        print("<td>$niuCod1 , $niuCod2 , $niuCod3</td>");
                        print("<td class='text-center'>".($rec["condicion_pago_id"]=="2" ? "AL CRÉDITO": "AL CONTADO"). "</td>");
                    }

                    $i++;
                    print("</tr>");
                    $item++;
                }
            }

            print('
            <div class="container1" style="position: absolute; margin-top:15px">
                <div class="row pb-2">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                        <b>Fecha y Hora de Reporte: </b>'.date("Y-m-d H:i:s").'
                        <b>, Total Recibos: </b>'.$rRec->rowCount().'
                        <b>, Total Registros: </b>'.$item.'
                    </div>
                </div>
            </div>'); ?>
    </div>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.v4/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="js/datatables.net/jquery.dataTables.min.js"></script>
    <script src="js/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <script src="js/datatables.net/dataTables.buttons.min.js"></script>
    <script src="js/datatables.net/jszip.min.js"></script>
    <script src="js/datatables.net/buttons.html5.min.js"></script>
    <?php include ($_SERVER["DOCUMENT_ROOT"] . "/js/facturacion_empresa.php"); ?>
    <script>
    jQuery(window).load(function(event) {
        jQuery('.loader').fadeOut(1000);
        multiSedeEmpresa($("#empresa").val());
    });

    $('.Datos').DataTable({
        language: {
            "lengthMenu": "Mostrar _MENU_ registros",
            "zeroRecords": "No se encontraron resultados",
            "info": "Registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "infoEmpty": "Registros del 0 al 0 de un total de 0 registros",
            "infoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sSearch": "Buscar:",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "sProcessing": "Procesando...",
        },
        responsive: "true",
        dom: 'Bfrtilp',
        buttons: [{
            extend: 'excelHtml5',
            text: '<img src="_images/excel.png" height="18" width="18" alt="icon name"> ',
            titleAttr: 'Clic para Exportar a Excel',
            className: 'btn-excel'

        }]
    });

    $("#empresa").change(function() {
        multiSedeEmpresa($(this).val());
    });

    function multiSedeEmpresa(idEmpresa){
        $.ajax({
                type: "POST",
                url: "_database/pago.php",
                dataType: "json",
                data: {
                    action: "sedeEmpresa",
                    idEmpresa: idEmpresa,
                },
                success: function (data) {
                    var select = $("#id_sede");

                    select.empty();

                    $.each(data, function (index, sede) {
                        if(id_sede == sede.id){
                            select.append('<option value="' + sede.id + '"selected>' + sede.nombre + '</option>');
                        }else{
                            select.append('<option value="' + sede.id + '">' + sede.nombre + '</option>');
                        }
                    });
                },
                error: function(jqXHR, exception) {
                    console.log(jqXHR, exception);
                    console.log('Error: '+exception);
                },
            });
    }
    </script>
</body>

</html>