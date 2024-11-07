<!DOCTYPE HTML>
<html>
<head>
    <title>Inmater Clínica de Fertilidad | Reporte de Tracking Pacientes</title>
    <?php
     include 'seguridad_login.php';
    require("_database/database.php");
    require("_database/database_farmacia.php"); ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.v4/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/global.css">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <script type="text/javascript">
        var tableToExcel = (function () {
            var uri = 'data:application/vnd.ms-excel;base64,'
                ,
                template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
                , base64 = function (s) {
                    return window.btoa(unescape(encodeURIComponent(s)))
                }
                , format = function (s, c) {
                    return s.replace(/{(\w+)}/g, function (m, p) {
                        return c[p];
                    })
                }
            return function (table, visita) {
                if (!table.nodeType) table = document.getElementById(table)
                var ctx = {worksheet: 'reporte_' + visita || 'reporte', table: table.innerHTML}
                window.location.href = uri + base64(format(template, ctx))
            }
        })();
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
        $between = $nc_between = $mediopago = $tiposervicio = $medico = $recibo = $ini = $fin = "";
        //
        if (isset($_POST) && !empty($_POST)) {
            if (isset($_POST["ini"]) && !empty($_POST["ini"]) && isset($_POST["fin"]) && !empty($_POST["fin"])) {
                $ini = $_POST['ini'];
                $fin = $_POST['fin'];
            } else {
                $ini = $fin = date('Y-m-d');
            }

            $between .= " and r.fec between '$ini' and '$fin'";
            $between_farma .= " and d.fechacreacion between '$ini' and '$fin'";

            if (isset($_POST["medios_comunicacion"]) && !empty($_POST["medios_comunicacion"])) {
                $medios_comunicacion = $_POST["medios_comunicacion"];
                /* $between .= " and r.cli_atencion_unica_id = $medios_comunicacion";
                $between_farma .= " and n.idcliatencionunica = $medios_comunicacion"; */
            }
        } else {
            $ini = $fin = date('Y-m-d');
            $medios_comunicacion = 2;
            $between .= " and r.fec between '$ini' and '$fin'";
            $between_farma .= " and d.fechacreacion between '$ini' and '$fin'";
        } ?>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="lista.php">Inicio</a></li>
                <li class="breadcrumb-item">Reportes</li>
                <li class="breadcrumb-item active" aria-current="page">Tracking Pacientes</li>
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
                                    <input class="form-control form-control-sm" name="ini" type="date" value="<?php print($ini); ?>" id="example-datetime-local-input">
                                    <span class="input-group-text">Hasta</span>
                                    <input class="form-control form-control-sm" name="fin" type="date" value="<?php print($fin); ?>" id="example-datetime-local-input">
                                </div>
                            </div>
                        </div>

                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Campaña</span>
                                    <select class="form-control form-control-sm" name="medios_comunicacion">
                                        <option value="">SELECCIONAR</option>
                                        <?php
                                            $stmt = $db->prepare("SELECT id, nombre from man_medios_comunicacion where estado = 1;");
                                            $stmt->execute();
                                            $stmt->setFetchMode(PDO::FETCH_ASSOC);
                                            $data1 = $stmt->fetchAll();

                                            foreach ($data1 as $row) {
                                                $selected = "";
                                                if ($row['id'] == $medios_comunicacion) {
                                                    $selected = "selected";
                                                }

                                                print("<option value='" . $row['id'] . "' $selected>" . mb_strtoupper($row['nombre']) . "</option>");
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                                <input type="Submit" class="btn btn-danger btn-sm" name="Mostrar" value="Mostrar"/>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php
        $item = 0;
        print("
        <table class='table table-responsive table-bordered align-middle header-fixed table-sm' style='height: 50vh;' id='tracking_pacientes_table'>
            <thead class='thead-dark'>
                <tr>
                    <th class='text-center' style='min-width: 100px;'>Fecha</th>
                    <th class='text-center'>Tipo<br>Documento</th>
                    <th class='text-center'>N° Documento</th>
                    <th style='min-width: 200px;'>Paciente</th>
                    <th class='text-center'>Médico</th>
                    <th class='text-center' style='min-width: 200px;'>Servicio Facturado</th>
                    <th class='text-center' style='min-width: 200px;'>Código Atención</th>
                    <th class='text-center'>Área Atención</th>
                    <th class='text-center'>Médico Atención</th>
                    <th class='text-center'>Valor Venta<br>Soles</th>
                    <th class='text-center'>Valor Venta<br>Dólares</th>
                    <th>IGV</th>
                    <th class='text-center'>Total<br>Soles</th>
                    <th class='text-center'>Total<br>Dólares</th>
                </tr>
            </thead>
            <tbody>");

        // realizar consulta
        $stmt = $db->prepare("SELECT *
            from hc_paciente hp
            where hp.medios_comunicacion_id = $medios_comunicacion
            order by hp.createdate desc;");
            $stmt->execute();

        $total_recibos = 0;

        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // consulta comprobantes de facturacion
            $stmt_recibos = $db->prepare("SELECT r.*, mts.nombre servicio
                from recibos r
                inner join man_tipo_servicio mts on mts.codigo = r.t_ser
                where r.dni = ? $between;");
            $stmt_recibos->execute([$data['dni']]);

            $total_recibos += $stmt_recibos->rowCount();

            if ($stmt_recibos->rowCount() == 0) {
                print("<tr $anulado>");
                    print('<td class="text-center">-</td>');
                    print('<td class="text-center">' . (mb_strtoupper($data['tip']) == 'CE' ? 'CEX' : mb_strtoupper($data['tip'])) . '</td>');
                    print('<td class="text-center">' . mb_strtoupper($data['dni']) . '</td>');
                    print('<td>' . mb_strtoupper($data['ape']) . ' ' . mb_strtoupper($data['nom']) . '</td>');
                    print('<td class="text-center">-</td>');
                    print('<td class="text-center">-</td>');
                    print('<td class="text-center">-</td>');
                    print('<td class="text-center">-</td>');
                    print('<td class="text-center">-</td>');
                    print('<td class="text-center">0</td><td class="text-center">0</td><td class="text-center">0</td><td class="text-center">0</td><td class="text-center">0</td>');
                print('</tr>');
            }

            while ($recibo = $stmt_recibos->fetch(PDO::FETCH_ASSOC)) {
                // consulta codigo de atencion
                $stmt_atencion = $db->prepare("SELECT cau.*, upper(ma.nombre) area
                    from cli_atencion_unica cau
                    inner join man_area ma on ma.estado = 1 and ma.id = cau.area_id
                    where cau.id = ?;");
                $stmt_atencion->execute([$recibo['cli_atencion_unica_id']]);
                $codigo_atencion = '';
                $area_atencion = '';
                $medico_atencion = '';

                if ($stmt_atencion->rowCount() > 0) {
                    $atencion = $stmt_atencion->fetch(PDO::FETCH_ASSOC);
                    $codigo_atencion = $atencion["codigo"];
                    $area_atencion = $atencion["area"];
                    $medico_atencion = $atencion["medico_id"];
                }

                // 
                $anulado = "";
                if ($recibo['anu'] == 1) {
                    $anulado = "bgcolor='#F9CCCD'";
                }

                print("<tr $anulado>");
                    print('<td class="text-center">' . date("d-m-Y", strtotime($recibo['fec'])) . '</td>');
                    print('<td class="text-center">' . (mb_strtoupper($data['tip']) == 'CE' ? 'CEX' : mb_strtoupper($data['tip'])) . '</td>');
                    print('<td class="text-center">' . mb_strtoupper($data['dni']) . '</td>');
                    print('<td>' . mb_strtoupper($data['ape']) . ' ' . mb_strtoupper($data['nom']) . '</td>');
                    print('<td class="text-center">' . mb_strtoupper($recibo['med']) . '</td>');
                    print('<td class="text-center">' . mb_strtoupper($recibo['servicio']) . '</td>');
                    print('<td class="text-center">' . $codigo_atencion . '</td>');
                    print('<td class="text-center">' . $area_atencion . '</td>');
                    print('<td class="text-center">' . mb_strtoupper($medico_atencion) . '</td>');

                    if ($recibo['anu'] == 1) {
                        print('<td class="text-center">0</td><td class="text-center">0</td><td class="text-center">0</td><td class="text-center">0</td><td class="text-center">0</td>');
                    } else {
                        if ($recibo['tip'] == 1) {
                            if ($recibo['t_ser'] == 1 or $recibo['t_ser'] == 2 or $recibo['t_ser'] == 3) {
                                if ($recibo['mon'] == 1) {
                                    print("
                                    <td class='text-center'>0</td>
                                    <td class='text-center'>" . number_format((float)($recibo['tot'] - ($recibo['descuento']))/1.18, 2) . "</td>
                                    <td class='text-center'>" . number_format((float)($recibo['tot'] - ($recibo['descuento']))/1.18*0.18, 2) . "</td>
                                    <td class='text-center'>0</td>
                                    <td class='text-center'>" . number_format((float)($recibo['tot'] - ($recibo['descuento'])), 2) . "</td>");
                                } else {
                                    print("
                                    <td class='text-center'>" . number_format((float)($recibo['tot'] - ($recibo['descuento']))/1.18, 2) . "</td>
                                    <td class='text-center'>0</td>
                                    <td class='text-center'>" . number_format((float)($recibo['tot'] - ($recibo['descuento']))/1.18*0.18, 2) . "</td>
                                    <td class='text-center'>" . number_format((float)($recibo['tot'] - ($recibo['descuento'])), 2) . "</td>
                                    <td class='text-center'>0</td>");
                                }
                            } else {
                                if ($recibo['mon'] == 1) {
                                    print("
                                    <td class='text-center'>" . number_format((float)($recibo['tot'] - ($recibo['descuento']))/1.18, 2) . "</td>
                                    <td class='text-center'>0</td>
                                    <td class='text-center'>" . number_format((float)($recibo['tot'] - ($recibo['descuento']))/1.18*0.18, 2) . "</td>
                                    <td class='text-center'>" . number_format((float)($recibo['tot'] - ($recibo['descuento'])), 2) . "</td>
                                    <td class='text-center'>0</td>");
                                } else {
                                    print("
                                    <td class='text-center'>0</td>
                                    <td class='text-center'>" . number_format((float)($recibo['tot'] - ($recibo['descuento']))/1.18, 2) . "</td>
                                    <td class='text-center'>" . number_format((float)($recibo['tot'] - ($recibo['descuento']))/1.18*0.18, 2) . "</td>
                                    <td class='text-center'>0</td>
                                    <td class='text-center'>" . number_format((float)($recibo['tot'] - ($recibo['descuento'])), 2) . "</td>");
                                }
                            }
                        } else {
                            if ($recibo['t_ser'] == 1 or $recibo['t_ser'] == 2 or $recibo['t_ser'] == 3) {
                                if ($recibo['mon'] == 1) {
                                    print("
                                    <td class='text-center'>0</td>
                                    <td class='text-center'>" . number_format((float)($recibo['tot'] - ($recibo['descuento']))/1.18, 2) . "</td>
                                    <td class='text-center'>" . number_format((float)($recibo['tot'] - ($recibo['descuento']))/1.18*0.18, 2) . "</td>
                                    <td class='text-center'>0</td>
                                    <td class='text-center'>" . number_format((float)($recibo['tot'] - ($recibo['descuento'])), 2) . "</td>");
                                } else {
                                    print("
                                    <td class='text-center'>" . number_format((float)($recibo['tot'] - ($recibo['descuento']))/1.18, 2) . "</td>
                                    <td class='text-center'>0</td>
                                    <td class='text-center'>" . number_format((float)($recibo['tot'] - ($recibo['descuento']))/1.18*0.18, 2) . "</td>
                                    <td class='text-center'>" . number_format((float)($recibo['tot'] - ($recibo['descuento'])), 2) . "</td>
                                    <td class='text-center'>0</td>");
                                }
                            } else {
                                if ($recibo['mon'] == 1) {
                                    print("
                                    <td class='text-center'>" . number_format((float)($recibo['tot'] - ($recibo['descuento']))/1.18, 2) . "</td>
                                    <td class='text-center'>0</td>
                                    <td class='text-center'>" . number_format((float)($recibo['tot'] - ($recibo['descuento']))/1.18*0.18, 2) . "</td>
                                    <td class='text-center'>" . number_format((float)($recibo['tot'] - ($recibo['descuento'])), 2) . "</td>
                                    <td class='text-center'>0</td>");
                                } else {
                                    print("
                                    <td class='text-center'>0</td>
                                    <td class='text-center'>" . number_format((float)($recibo['tot'] - ($recibo['descuento']))/1.18, 2) . "</td>
                                    <td class='text-center'>" . number_format((float)($recibo['tot'] - ($recibo['descuento']))/1.18*0.18, 2) . "</td>
                                    <td class='text-center'>0</td>
                                    <td class='text-center'>" . number_format((float)($recibo['tot'] - ($recibo['descuento'])), 2) . "</td>");
                                }
                            }
                        }
                    }

                print('</tr>');
            }

            // consulta comprobantes de facturacion
            $stmt_recibos_farma = $farma->prepare("SELECT d.fechacreacion fecha, c.dni, upper(c.nombres) paciente, t.nombres medico, d.igv, p.monto total, dt.nombre documento_identidad, n.idcliatencionunica
                from tbldocumento d
                inner join tblpago p on p.iddocumento = d.id
                inner join tblnota n on d.idnota = n.id
                inner join tblmedico t on t.id = n.idmedico
                inner join tblcliente c on n.idcliente = c.id and c.dni = ?
                inner join tbldocumentoidentidadtipo dt on dt.id = c.tipodocumentoidentidad_id
                where 1=1$between_farma;");
            $stmt_recibos_farma->execute([$data['dni']]);

            $total_recibos += $stmt_recibos_farma->rowCount();

            while ($recibo = $stmt_recibos_farma->fetch(PDO::FETCH_ASSOC)) {
                // consulta codigo de atencion
                $stmt_atencion = $db->prepare("SELECT cau.*, upper(ma.nombre) area
                    from cli_atencion_unica cau
                    inner join man_area ma on ma.estado = 1 and ma.id = cau.area_id
                    where cau.id = ?;");
                $stmt_atencion->execute([$recibo['idcliatencionunica']]);
                $codigo_atencion = '';
                $area_atencion = '';
                $medico_atencion = '';

                if ($stmt_atencion->rowCount() > 0) {
                    $atencion = $stmt_atencion->fetch(PDO::FETCH_ASSOC);
                    $codigo_atencion = $atencion["codigo"];
                    $area_atencion = $atencion["area"];
                    $medico_atencion = $atencion["medico_id"];
                }

                print("<tr>");
                    print('<td class="text-center">' . date("d-m-Y", strtotime($recibo['fecha'])) . '</td>');
                    print('<td class="text-center">' . $recibo['documento_identidad'] . '</td>');
                    print('<td class="text-center">' . $recibo['dni'] . '</td>');
                    print('<td>' . $recibo['paciente'] . '</td>');
                    print('<td class="text-center">' . mb_strtoupper($recibo['medico']) . '</td>');
                    print('<td class="text-center">FARMACIA</td>');
                    print('<td class="text-center">' . $codigo_atencion . '</td>');
                    print('<td class="text-center">' . $area_atencion . '</td>');
                    print('<td class="text-center">' . mb_strtoupper($medico_atencion) . '</td>');
                    print('<td class="text-center">' . $recibo['total'] . '</td>');
                    print('<td class="text-center">0</td>');
                    print('<td class="text-center">' . $recibo['igv'] . '</td>');
                    print('<td class="text-center">' . $recibo['total'] . '</td>');
                    print('<td class="text-center">0</td>');
                print("</tr>");
            }
        }

        print('</tbody></table>');

        print('
        <div class="container1">
            <div class="row pb-2">
                <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                    <b>Fecha y Hora de Reporte: </b>'.date("Y-m-d H:i:s").'
                    <b>, Total Registros: </b>' . ($total_recibos) . '
                    <b>, Descargar: </b>
                    <a href="#" onclick="tableToExcel(\'tracking_pacientes_table\', \'tracking_pacientes\')" class="ui-btn ui-mini ui-btn-inline">
                        <img src="_images/excel.png" height="18" width="18" alt="icon name">
                    </a>
                </div>
            </div>
        </div>'); ?>
    </div>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.v4/bootstrap.min.js" crossorigin="anonymous"></script>
    <script>
        jQuery(window).load(function (event) {
            jQuery('.loader').fadeOut(1000);
        });
    </script>
</body>
</html>