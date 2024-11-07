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
</head>
<script>
    id_sede = 0;
</script>
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
            $between = $nc_between = $mediopago = $tiposervicio = $medico = $empresa = $recibo = $ini = $fin = "";
            //
            if (isset($_POST) && !empty($_POST)) {
                if ( isset($_POST["ini"]) && !empty($_POST["ini"]) && isset($_POST["fin"]) && !empty($_POST["fin"]) ) {
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
    		$rRec = $db->prepare("SELECT
                upper(mc.abreviatura) medios_comunicacion
                , upper(s.nombre) sede_nombre
                , coalesce(pac.tip, par.p_tip) tipodocumentoidentidad
                , r.cpe_serie serie_cpe, r.cpe_correlativo correlativo_cpe
                , r.idusercreate usuario_emisor, r.condicion_pago_id, r.historico
                , r.ser, r.anu, r.fec, r.tip, r.id, r.dni, r.nom, r.t_ser, r.med, r.ruc, r.raz, r.gratuito, r.descuento, r.mon
                , r.banco1, r.banco2, r.banco3
                , r.tipotarjeta1, r.tipotarjeta2, r.tipotarjeta3
                , r.numerocuotas1, r.numerocuotas2, r.numerocuotas3
                , r.t1, r.m1, r.p1, r.t2, r.m2, r.p2, r.t3, r.m3, r.p3, r.pos1_id, r.pos2_id, r.pos3_id
                from recibos r
                inner join usuario u on u.userx = r.idusercreate
                left join hc_paciente pac on pac.dni=r.dni
                inner join sedes s on s.id = r.sede
                left join man_medios_comunicacion mc on mc.estado=1 and mc.id=r.programa_id
                left join hc_pareja par on par.p_dni=r.dni
                where 1=1$between
                order by r.fec asc, r.id asc");
            $rRec->execute();

            // realizar consulta de nc
    		$stmt = $db->prepare("SELECT
                upper(mc.abreviatura) medios_comunicacion
                , nc.id, upper(s.nombre) sede_nombre
                , nc.createdate fecha, nc.serie, nc.correlativo, nc.comprobantetipo_id, mf.errors
                , coalesce(pac.tip, par.p_tip) paciente_tipodocumento, r.dni paciente_documento, upper(r.nom) paciente_nombre, upper(r.med) medico
                , nc.moneda_id facturacion_moneda, nc.total facturacion_total, df.numero facturacion_documento, df.nombre facturacion_nombre, df.correo facturacion_correo
                , nc.idusercreate usuario_emisor
                from factu_notacredito nc
                inner join recibos r on r.tip = nc.recibo_tip and r.id = nc.recibo_id
                inner join sedes s on s.id = r.sede
                left join facturacion_recibo_mifact_response mf on mf.tip_recibo = 3 and mf.id_recibo = nc.id
                left join hc_paciente pac on pac.dni=r.dni
                left join man_medios_comunicacion mc on mc.estado=1 and mc.id=r.programa_id
                left join hc_pareja par on par.p_dni=r.dni
                inner join factu_datosfacturacion df on df.id = nc.datosfacturacion_id
                where nc.estado=1$nc_between
                order by nc.id desc");
            $stmt->execute(); ?>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Inicio</li>
                <li class="breadcrumb-item">Facturación</li>
                <li class="breadcrumb-item">Reportes</li>
                <li class="breadcrumb-item active" aria-current="page">Ventas Detallado</li>
            </ol>
        </nav>
        <div data-role="header">
            <div class="card mb-3">
                <h5 class="card-header">Filtros</h5>
                <div class="card-body">
                    <form action="" method="post" data-ajax="false" id="form1">
                        <div class="row pb-2">
                            <!-- mostrar desde hasta -->
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"> Mostrar Desde</span>
                                    <input class="form-control form-control-sm" name="ini" type="date"
                                        value="<?php print($ini); ?>" id="example-datetime-local-input">
                                    <span class="input-group-text">Hasta</span>
                                    <input class="form-control form-control-sm" name="fin" type="date"
                                        value="<?php print($fin); ?>" id="example-datetime-local-input">
                                </div>
                            </div>
                            <!-- medio de pago -->
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
                            <!-- tipo de servicio -->
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

                            <!-- medico -->
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
                            <!-- n° recibo -->
                            <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
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
    					<th style='min-width: 150px;'>Fecha</th>
    					<th>Mes</th>
    					<th>Tipo Documento</th>
    					<th>Tipo Documento Identidad</th>
							<th>N° Documento Identidad</th>
    					<th style='min-width: 400px;'>Nombre de Paciente</th>
    					<th style='min-width: 200px;'>Médico</th>
    					<th style='min-width: 180px;'>Tipo de Servicio</th>
    					<th>Cliente</th>
    					<th style='min-width: 400px;'>Razón Social</th>
                        <th style='min-width: 300px;'>Paquete</th>
                        <th style='min-width: 300px;'>Procedencia</th>
                        <th style='min-width: 200px;'>Programa</th>
                        <th style='min-width: 200px;'>Sede</th>
                        <th>Tarifario</th>
                        <th>Tipo de procedimiento</th>
                        <th>Centro de Costo</th>
                        <th>Subcentro de Costo</th>
                        <th>Cuenta Contable</th>
                        <th>Código<br>Servicio</th>
                        <th style='min-width: 500px;'>Servicio</th>
                        <th>Valor Venta Soles</th>
                        <th>Valor Venta Dólares</th>
                        <th>IGV</th>
                        <th>Total Soles</th>
                        <th>Total Dólares</th>
                        <th>Descuento</th>
                        <th>Moneda</th>
    					<th>Tipo cambio</th>
                        <th style='min-width: 120px;'>N° Electrónico</th>
                        <th style='min-width: 80px; text-align: center;'>Recibo Id</th>
                        <th style='min-width: 150px;'>Banco</th>
                        <th style='min-width: 150px;'>Tipo tarjeta</th>
                        <th style='min-width: 100px;'>Cuotas</th>
                        <th>Efectivo Soles</th>
                        <th>Efectivo Dólares</th>
                        <th>Depósito Soles</th>
    					<th>Depósito Dólares</th>
    					<th>Transferencia Soles</th>
    					<th>Transferencia Dólares</th>
    					<th>Link Tarjeta Soles</th>
    					<th>Link Tarjeta Dólares</th>
                        <th>Visa Soles</th>
                        <th>Visa Dólares</th>
    					<th>Mastercard Soles</th>
    					<th>Mastercard Dólares</th>
    					<th>Diners Soles</th>
    					<th>Diners Dólares</th>
    					<th>Amex Soles</th>
    					<th>Amex Dólares</th>
                        <th>Usuario<br>Emisor</th>
                        <th class='text-center' style='min-width: 120px;'>Condición de pago</th>
                    </tr>
                </thead>
                <tbody>
            ");

            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $nc_color = '#5eb7b7';

                if (!empty($data["errors"])) {
                    $nc_color = '#F9CCCD';
                }

                // consultar detalle nc
                $stmt1 = $db->prepare("SELECT
                ncd.id, ncd.nombre, ncd.precio, ncd.servicio_id, UPPER(mts.nombre) tiposervicio,
                upper(s.nombre) sede, ccc.codigo centrocosto_codigo, csco.codigo subcentrocosto_codigo, csco.descripcion subcentrocosto, cuc.codigo cuenta_contable
                , upper(sp.nombre) tipo_procedimiento, upper(tp.nombre) tarifario
                FROM factu_notacredito_detalle ncd
                INNER JOIN recibo_serv rs ON rs.id = ncd.servicio_id
                LEFT JOIN servicios_procedimiento sp on sp.id = rs.procedimiento_id
                LEFT JOIN tarifario tp on tp.id = rs.tarifario_id
                INNER JOIN man_tipo_servicio mts ON mts.id = rs.tip
                LEFT JOIN conta_sub_centro_costo csco ON csco.id = rs.conta_sub_centro_costo_id
                LEFT JOIN conta_centro_costo ccc ON ccc.id = csco.conta_centro_costo_id
                INNER JOIN sedes_contabilidad s on s.id = ccc.sede_id
                LEFT JOIN conta_cuenta_contable cuc on cuc.id = csco.conta_cuenta_contable_id
                WHERE ncd.cantidad <> 0 AND ncd.factu_notacredito_id = ?
                ORDER BY ncd.id ASC");
                $stmt1->execute([$data["id"]]);

                while ($data1 = $stmt1->fetch(PDO::FETCH_ASSOC)) {
                    print("<tr style='background-color: ".$nc_color.";'>
                        <td>".date("d-m-Y", strtotime($data['fecha']))."</td>
                        <td>".mb_strtoupper(strftime("%B", strtotime($data['fecha'])))."</td>
                        <td>".($data['comprobantetipo_id'] == 3 ? "NC" : "ND")."</td>
                        <td>".$data["paciente_tipodocumento"]."</td>
                        <td>'".$data["paciente_documento"]."</td>
                        <td>".$data["paciente_nombre"]."</td>
                        <td>".$data["medico"]."</td>
                        <td>".$data1["tiposervicio"]."</td>
                        <td>'".$data["facturacion_documento"]."</td>
                        <td>".$data["facturacion_nombre"]."</td>
                        <td></td>
                        <td>".$data["sede_nombre"]."</td>
                        <td>".$data["medios_comunicacion"]."</td>
                        <td>".$data1["sede"]."</td>
                        <td>".$data1["tarifario"]."</td>
                        <td>".$data1["tipo_procedimiento"]."</td>
                        <td>'".$data1["centrocosto_codigo"]."</td>
                        <td>'".$data1["subcentrocosto_codigo"]."</td>
                        <td>".$data1["cuenta_contable"]."</td>
                        <td>".$data1["servicio_id"]."</td>
                        <td>".$data1["nombre"]."</td>
                        <td>".(empty($data["errors"]) ? ($data["facturacion_moneda"] == 1 ? number_format((float)($data1["precio"])*-1/1.18, 2) : "") : "0")."</td>
                        <td>".(empty($data["errors"]) ? ($data["facturacion_moneda"] == 2 ? number_format((float)($data1["precio"])*-1/1.18, 2) : "") : "0")."</td>
                        <td>".(empty($data["errors"]) ? (number_format((float)($data1["precio"])*0.18*-1/1.18, 2)) : "0")."</td>
                        <td>".(empty($data["errors"]) ? ($data["facturacion_moneda"] == 1 ? number_format((float)$data1["precio"]*-1, 2) : "") : "0")."</td>
                        <td>".(empty($data["errors"]) ? ($data["facturacion_moneda"] == 2 ? number_format((float)$data1["precio"]*-1, 2) : "") : "0")."</td>
                        <td>0</td>
                        <td>".($data["facturacion_moneda"] == 1 ? "MN" : "US")."</td>
                        <td></td>
                        <td>".$data["serie"]." - ".$data["correlativo"]."</td>
                        <td class='text-center'></td>
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
                        <td>".$data["usuario_emisor"]."</td>
                        <td class='text-center'>NOTA DE CREDITO</td>
                    </tr>");
                }
            }

            while ($rec = $rRec->fetch(PDO::FETCH_ASSOC)) {
                // verificar numero de servicios
                $i=1; // $i=1
                $total=0;
                $anulado="";
                $total = substr_count($rec['ser'], "</tr>");
                $cadena=$rec['ser'];

                if ($rec['anu'] == 1) {
                    $anulado="bgcolor='#F9CCCD'";
                }

                while ($i <= $total) {
                    print("<tr $anulado>");
                    // fecha
                    print("<td>".date("d-m-Y", strtotime($rec['fec']))."</td>");
                    // mes
                    print("<td>".mb_strtoupper(strftime("%B", strtotime($rec['fec'])))."</td>");

                    // tipo documento
                    if ($rec['tip'] == 1) { print("<td>BV</td>"); }
                    if ($rec['tip'] == 2) { print("<td>FT</td>"); }
                    if ($rec['tip'] == 3) { print("<td>BV Fisica</td>"); }
                    if ($rec['tip'] == 4) { print("<td>FT Fisica</td>"); }
                    // tipo documento identidad
                    print("<td>".mb_strtoupper($rec['tipodocumentoidentidad'])."</td><td>'".$rec['dni']."</td>");
                    // paciente
                    print("<td>".mb_strtoupper($rec['nom'])."</td>");
                    // medico
                    print("<td>".mb_strtoupper($rec['med'])."</td>");

                    // tipo de servicio
                    if ($rec['t_ser'] == 1) { print('<td>REPRODUCCIÓN ASISTIDA</td>'); }
                    if ($rec['t_ser'] == 2) { print('<td>ANDROLOGÍA</td>'); }
                    if ($rec['t_ser'] == 3) { print('<td>PROCEDIMIENTOS SALA</td>'); }
                    if ($rec['t_ser'] == 4) { print('<td>ANÁLISIS SANGRE</td>'); }
                    if ($rec['t_ser'] == 5) { print('<td>PERFILES</td>'); }
                    if ($rec['t_ser'] == 6) { print('<td>ECOGRAFÍA</td>'); }
                    if ($rec['t_ser'] == 7) { print('<td>ADICIONALES</td>'); }
                    // ruc y razon social
                    print("<td>'".$rec['ruc']."</td>
                        <td>".$rec['raz']."</td>");
                    // servicio, valor venta
                    $pos = strpos($cadena, "</tr>");
                    $tam = strlen($cadena);
                    $servicios = substr($cadena, 4, $pos-4);
                    $cadena = substr($cadena, $pos+5, $tam-3);
                    $demo=$moneda="";
                    $valorventacod=0;

                    if (substr_count($servicios, "</td>") == 3) {
                        $idservicio="";
                        $idserviciopos = strpos($servicios, "</td>");
                        $tamservicio = strlen($servicios); // add
                        $idservicio = substr($servicios, 4, $idserviciopos-4);
                        $cadena1 = substr($servicios, $idserviciopos+5, $tamservicio-3); // add
                        //
                        $demopos = strpos($cadena1, "</td>"); // add
                        $tamdemo = strlen($cadena1); // add
                        $demo = substr($cadena1, 0, $demopos); // add
                        $valorventa = substr($cadena1, $demopos+5, $tamdemo-3); // add
                        $valorventacod = substr($valorventa, 4, strlen($valorventacod)-5);

                        if ($rec['anu'] == 1 or $rec['gratuito'] == 1) {
                            $valorventacod=0;
                        }

                        $consulta = $db->prepare("SELECT
                            r.pak, r.idmoneda, cco.codigo cc, sco.codigo scc, cuc.codigo cuenta_contable, upper(s.nombre) sede
                            , upper(sp.nombre) tipo_procedimiento, upper(tp.nombre) tarifario
                            from recibo_serv r
                            left join servicios_procedimiento sp on sp.id = r.procedimiento_id
                            left join tarifario tp on tp.id = r.tarifario_id
                            inner join conta_sub_centro_costo sco on sco.id = r.conta_sub_centro_costo_id 
                            inner join conta_centro_costo cco on cco.id = sco.conta_centro_costo_id 
                            inner join conta_cuenta_contable cuc on cuc.id = sco.conta_cuenta_contable_id 
                            inner join sedes_contabilidad s on s.id = cco.sede_id
                            where r.id = ?;");
                        $consulta->execute(array($idservicio));
                        $data = $consulta->fetch(PDO::FETCH_ASSOC);

                        if ($rec['t_ser'] == 1 or $rec['t_ser'] == 2 or $rec['t_ser'] == 3) {
                            if ($rec['mon'] == 1) $moneda="US"; else $moneda="MN";
                        } else {
                            if ($rec['mon'] == 1) $moneda="MN"; else $moneda="US";
                        }
                        $servicios="
                        <td>".mb_strtoupper($data["pak"])."</td>
                        <td>".$rec["sede_nombre"]."</td>
                        <td>".$rec["medios_comunicacion"]."</td>
                        <td>".$data["sede"]."</td>
                        <td>".$data["tarifario"]."</td>
                        <td>".$data["tipo_procedimiento"]."</td>
                        <td>'".$data["cc"]."</td>
                        <td>'".$data["scc"]."</td>
                        <td>".$data["cuenta_contable"]."</td>
                        <td>".$idservicio."</td>".mb_strtoupper($demo);
                    } else {
                        //
                        $idservicio="";
                        $idserviciopos = strpos($servicios, "</td>");
                        $tamservicio = strlen($servicios);
                        $idservicio = substr($servicios, 4, $idserviciopos-4);
                        $cadena1 = substr($servicios, $idserviciopos, $tamservicio-5);
                        $valorventacod1 = substr($servicios, $idserviciopos+5, $tamservicio-5);

                        if ($rec['anu'] == 1 or $rec['gratuito'] == 1) {
                            $valorventacod="0";
                        }

                        $servicios="<td></td><td>".$idservicio."</td>".$cadena1;
                    }

                    print($servicios);

                    // tip (1: boleta, 2: factura)
                    // t_ser (1: RA, 2: Andro, 3: Sala, 4: Analisis, 5: Perfiles, 6: Ecografia, 7: Adicionales)
                    // mon (1: moneda origen, 2 cambio de moneda)
                    // t_ser (1, 2, 3: dolares; 4, 5, 6, 7: soles)
                    // tip (1: boleta, 2: factura)
                    if ($rec["anu"] == 1){ ?>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    <?php }else{
                    if ($rec['tip'] == 1 or $rec['historico'] === 0) {
                        if ($rec['t_ser'] == 1 or $rec['t_ser'] == 2 or $rec['t_ser'] == 3) {
                                $valorventacod = isset($valorventacod) ? (float) $valorventacod : 0;
                                $descuento = isset($rec['descuento']) ? (float) $rec['descuento'] : 0;
                                $total = isset($total) ? (float) $total : 1; // Evita la división por cero, asumiendo que el valor mínimo para $total es 1

                                $base = ($valorventacod - ($descuento / $total)) / 1.18;
                                $igv = $base * 0.18;
                                $total_final = $valorventacod - ($descuento / $total);
                                if ($rec['mon'] == 1) { ?>
                                    <td></td>
                                    <td><?php echo number_format($base, 2); ?></td>
                                    <td><?php echo number_format($igv, 2); ?></td>
                                    <td></td>
                                    <td><?php echo number_format($total_final, 2); ?></td>
                                <?php } 
                                else { ?>
                                    <td><?php echo number_format($base, 2); ?></td>
                                    <td></td>
                                    <td><?php echo number_format($igv, 2); ?></td>
                                    <td><?php echo number_format($total_final, 2); ?></td>
                                    <td></td>
                                <?php } 
                        } else {
                            $valorventacod = isset($valorventacod) ? (float) $valorventacod : 0;
                            $descuento = isset($rec['descuento']) ? (float) $rec['descuento'] : 0;
                            $total = isset($total) ? (float) $total : 1; // Evita la división por cero, asumiendo que el valor mínimo para $total es 1
                            
                            $base = ($valorventacod - ($descuento / $total)) / 1.18;
                            $igv = $base * 0.18;
                            $total_final = $valorventacod - ($descuento / $total);
                            ?>
                            
                            <?php if ($rec['mon'] == 1) { ?>
                                <td><?php echo number_format($base, 2); ?></td>
                                <td></td>
                                <td><?php echo number_format($igv, 2); ?></td>
                                <td><?php echo number_format($total_final, 2); ?></td>
                                <td></td>
                            <?php } else { ?>
                                <td></td>
                                <td><?php echo number_format($base, 2); ?></td>
                                <td><?php echo number_format($igv, 2); ?></td>
                                <td></td>
                                <td><?php echo number_format($total_final, 2); ?></td>
                            <?php }                             
                        }
                    } else {
                        if ($rec['t_ser'] == 1 or $rec['t_ser'] == 2 or $rec['t_ser'] == 3) {
                            $valorventacod = floatval($valorventacod);
                            $rec['descuento'] = floatval($rec['descuento']);
                            $total = floatval($total);

                            if (!is_numeric($valorventacod) || !is_numeric($rec['descuento']) || !is_numeric($total)) {
                                echo "Error: uno o más valores no son numéricos.";
                            } else {
                                if ($rec['mon'] == 1) {
                                    print("
                                    <td></td>
                                    <td>".number_format((float)(($valorventacod)*1.18 - ($rec['descuento']/ $total))/ 1.18, 2)."</td>
                                    <td>".number_format((float)((($valorventacod)*1.18 - ($rec['descuento']/ $total))/ 1.18) * 0.18, 2)."</td>
                                    <td></td>
                                    <td>".number_format((float)($valorventacod)*1.18 - ($rec['descuento']/ $total), 2)."</td>");
                                } else {
                                    print("
                                    <td>".number_format((float)(($valorventacod)*1.18 - ($rec['descuento']/ $total))/ 1.18, 2)."</td>
                                    <td></td>
                                    <td>".number_format((float)((($valorventacod)*1.18 - ($rec['descuento']/ $total))/ 1.18) * 0.18, 2)."</td>
                                    <td>".number_format((float)($valorventacod)*1.18 - ($rec['descuento']/ $total), 2)."</td>
                                    <td></td>");
                                }
                            }
                        } else {
                           $valorventacod = floatval($valorventacod);
                            $rec['descuento'] = floatval($rec['descuento']);
                            $total = floatval($total);
                                                    
                            if (!is_numeric($valorventacod) || !is_numeric($rec['descuento']) || !is_numeric($total)) {
                                echo "Error: uno o más valores no son numéricos.";
                            } else {
                                if ($rec['mon'] == 1) {
                                    print("
                                    <td>".number_format((float)(($valorventacod)*1.18 - ($rec['descuento']/ $total))/ 1.18, 2)."</td>
                                    <td></td>
                                    <td>".number_format((float)((($valorventacod)*1.18 - ($rec['descuento']/ $total))/ 1.18) * 0.18, 2)."</td>
                                    <td>".number_format((float)($valorventacod)*1.18 - ($rec['descuento']/ $total), 2)."</td>
                                    <td></td>");
                                } else {
                                    print("
                                    <td></td>
                                    <td>".number_format((float)(($valorventacod)*1.18 - ($rec['descuento']/ $total))/ 1.18, 2)."</td>
                                    <td>".number_format((float)((($valorventacod)*1.18 - ($rec['descuento']/ $total))/ 1.18) * 0.18, 2)."</td>
                                    <td></td>
                                    <td>".number_format((float)($valorventacod)*1.18 - ($rec['descuento']/ $total), 2)."</td>");
                                }
                            }
                        }
                    }
                }

                    // descuento
                    if ($i == 1) {
                        print("<td>".$rec['descuento']."</td>");
                    } else {
                        print("<td>0</td>");
                    }

                    // tipo de cambio
                    if ($rec['anu'] == 1 or $rec['gratuito'] == 1) {
                        print("<td></td><td></td>");
                    } else {
                        print("<td>".$moneda."</td><td>" . $rec['mon'] . "</td>");
                    }
                    // n° electrónico
                    print("<td>" . $rec['serie_cpe'] . " - " . $rec['correlativo_cpe'] . "</td>");
                    print('<td style="text-align: center;">' . $rec['id'] . '</td>');

                    // defino array de banco, tipo tarjeta y numero de cuotas
                    $bancos_array = [
                        "1" => "BBVA",
                        "2" => "BCP",
                        "3" => "Dinners Club",
                        "4" => "Interbank",
                        "5" => "Otros",
                    ];

                    $tipotarjeta_array = [];
                    $tipoTarjeta = tipTarjeta();
                    foreach ($tipoTarjeta as $fila) {
                        $tipotarjeta_array[$fila['codigo_facturacion']] = $fila['formapago'];
                    }
                    if ($rec['anu'] == 1 or $rec['gratuito'] == 1) {
                        print('<td></td><td></td><td></td>');
                    } else {
                        print('<td>' . (!!$rec['banco1'] ? $bancos_array[$rec['banco1']] : '') . (!!$rec['banco2'] ?  ', ' . $bancos_array[$rec['banco2']] : '') . (!!$rec['banco3'] ?  ', ' . $bancos_array[$rec['banco3']] : '') . '</td>');
                        print('<td>' . (!!$rec['tipotarjeta1'] ? $tipotarjeta_array[$rec['tipotarjeta1']] : '') . (!!$rec['tipotarjeta2'] ?  ', ' . $tipotarjeta_array[$rec['tipotarjeta2']] : '') . (!!$rec['tipotarjeta3'] ?  ', ' . $tipotarjeta_array[$rec['tipotarjeta3']] : '') . '</td>');
                        print('<td>' . (!!$rec['numerocuotas1'] ? $rec['numerocuotas1'] : '') . (!!$rec['numerocuotas2'] ?  ', ' . $rec['numerocuotas2'] : '') . (!!$rec['numerocuotas3'] ?  ', ' . $rec['numerocuotas3'] : '') . '</td>');
                    }

                    if ($rec['anu'] == 1 or $rec['gratuito'] == 1) {
                        print('<td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td>');
                    }

                    if ($i != 1 and $rec['anu'] != 1 and $rec['gratuito'] != 1) {
                        print('<td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td>');
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
                            }elseif ($t == 8) {
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


                        
                        print("<td>$efectivo_soles</td>");
                        print("<td>$efectivo_dolares</td>");
                        print("<td>$deposito_soles</td>");
                        print("<td>$deposito_dolares</td>");
                        print("<td>$transferencia_soles</td>");
                        print("<td>$transferencia_dolares</td>");
                        print("<td>$linktarjeta_soles</td>");
                        print("<td>$linktarjeta_dolares</td>");
                        print("<td>$visa_soles</td>");
                        print("<td>$visa_dolares</td>");
                        print("<td>$mastercard_soles</td>");
                        print("<td>$mastercard_dolares</td>");
                        print("<td>$diners_soles</td>");
                        print("<td>$diners_dolares</td>");
                        print("<td>$amex_soles</td>");
                        print("<td>$amex_dolares</td>");
                    }

                    print('<td>'.$rec["usuario_emisor"].'</td>');
                    if ($rec["anu"] == 1) {
                      print("<td class='text-center'>ANULADO</td>");
                    } else {
                      if ($rec["gratuito"] == 1) {
                        print("<td class='text-center'>GRATUITO</td>");
                      } else {
                        print("<td class='text-center'>".($rec["condicion_pago_id"]=="2" ? "AL CRÉDITO": "AL CONTADO")."</td>");
                      }
                    }
                    $i++;
                    print("</tr>");
                    $item++;
                }
            }

            print('
            <div class="container1" style="position: absolute; margin-top:15px">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                        <b>Fecha y Hora de Reporte: </b>'.date("Y-m-d H:i:s").'
                        <b>, Total Recibos: </b>'.$rRec->rowCount().'
                        <b>, Total Registros: </b>'.$item.'
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