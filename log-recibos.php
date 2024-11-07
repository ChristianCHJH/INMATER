<!DOCTYPE HTML>
<html>
<head>
<?php
     include 'seguridad_login.php';
     require("_database/database_log.php");
     require("_database/database.php"); ?>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/bootstrap.v4/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/global.css">

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

    <div class="container1">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="lista_facturacion.php">Inicio</a></li>
                <li class="breadcrumb-item">Log</li>
                <li class="breadcrumb-item active" aria-current="page">Recibos</li>
            </ol>
        </nav>

        <?php
        $between = $tipo_comprobante = $numero_comprobante = '';

        if (isset($_POST) && !empty($_POST)) {
            if (isset($_POST["tipo_comprobante"]) && $_POST["tipo_comprobante"] != "") {
                $between .= " AND r.recibo_tip = ".$_POST["tipo_comprobante"];
            }

            if (isset($_POST["numero_comprobante"]) && $_POST["numero_comprobante"] != "") {
                $numero_comprobante = $_POST["numero_comprobante"];
                $between .= " AND r.recibo_id = $numero_comprobante";
            }
        } else {
            // $between .= " AND 1=2";
        }

        $consulta = $dblog->prepare("SELECT
            r.recibo_id, r.recibo_tip, r.nom paciente, r.med medico, s.nombre sede, r.idusercreate, r.action, (r.createdate - INTERVAL '-5 hours') as createdate
            , r.t_ser tipo_servicio
            , r.anglo estado_anglolab
            , r.mon tipo_cambio, r.tot total, r.descuento
            , r.t1 metodo_pago_1, r.numerocuotas1, r.p1 total1, r.m1 moneda1, coalesce(r.banco1, 0) banco1, coalesce(r.tipotarjeta1, 0) tipotarjeta1
            , r.t2 metodo_pago_2, r.numerocuotas2, r.p2 total2, r.m2 moneda2, coalesce(r.banco2, 0) banco2, coalesce(r.tipotarjeta2, 0) tipotarjeta2
            , r.t3 metodo_pago_3, r.numerocuotas3, r.p3 total3, r.m3 moneda3, coalesce(r.banco3, 0) banco3, coalesce(r.tipotarjeta3, 0) tipotarjeta3
            FROM recibos r
            INNER JOIN appinmater_modulo.sedes s ON s.codigo_facturacion = r.sede
            WHERE 1=1$between
            ORDER BY r.createdate DESC");
        $consulta->execute();
        $rows = $consulta->fetchAll(); ?>

        <div data-role="header">
            <div class="card mb-3">
                <h5 class="card-header">Filtros</h5>
                <div class="card-body">
                    <form action="" method="post" data-ajax="false" id="form1">
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Tipo Comprobante</span>
                                    <select class="form-control form-control-sm" name="tipo_comprobante">
                                        <option value="">SELECCIONAR</option>
                                        <?php print('<option value="1" '.($_POST["tipo_comprobante"] == 1 ? ' selected' : '').'>BOLETA</option>') ?>
                                        <?php print('<option value="2" '.($_POST["tipo_comprobante"] == 2 ? ' selected' : '').'>FACTURA</option>') ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-sm-12 col-md-2 col-lg-2 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Id Comprobante</span>
                                    <input type="text" class="form-control form-control-sm" name="numero_comprobante" value="<?php print($numero_comprobante); ?>">
                                </div>
                            </div>

                            <div class="col-12 col-sm-12 col-md-2 col-lg-2">
                                <input type="Submit" class="btn btn-sm btn-danger" name="Mostrar" value="Mostrar"/>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <h5 class="card-header" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">
                <?php
                print('
                    <small><b>Fecha y Hora de Reporte: </b>'.date("Y-m-d H:i:s").'
                    <b>, Total Registros: </b>'.count($rows).'
                    <b>, Descargar: </b>
                    <a href="#" onclick="tableToExcel(\'repo_pacientes\', \'pacientes\')" class="ui-btn ui-mini ui-btn-inline">
                        <img src="_images/excel.png" height="18" width="18" alt="icon name">
                    </a></small>'); ?>
            </h5>
 
            <form action="" method="post" data-ajax="false" name="form2">
                <table width="100%" class='table table-responsive table-bordered align-middle header-fixed' style='height: 50vh;' data-filter="true" data-input="#filtro" id="repo_pacientes">
                    <thead class="thead-dark">
                        <tr>
                            <th rowspan="2" class="text-center">Item</th>
                            <th rowspan="2" class="text-center">Fecha Log</th>
                            <th rowspan="2" class="text-center">Operación</th>
                            <th rowspan="2" class="text-center">Usuario</th>
                            <th rowspan="2" class="text-center">Tipo<br>Comprobante</th>
                            <th rowspan="2" class="text-center">Id Comprobante</th>
                            <th rowspan="2" style="min-width: 400px;">Paciente</th>
                            <th rowspan="2">Médico</th>
                            <th rowspan="2" style="min-width: 150px;">Sede</th>
                            <th rowspan="2" class="text-center">Moneda</th>
                            <th rowspan="2" class="text-center">Tipo Cambio</th>
                            <th rowspan="2" class="text-center">Total</th>
                            <th rowspan="2" class="text-center">Descuento</th>
                            <th rowspan="2" class="text-center">Estado Anglolab</th>
                            <th colspan="6" class="text-center">Medio Pago 1</th>
                            <th colspan="6" class="text-center">Medio Pago 2</th>
                            <th colspan="6" class="text-center">Medio Pago 3</th>
                        </tr>
                        <tr>
                            <th class="text-center" style="min-width: 150px;">Método Pago</th>
                            <th class="text-center">Banco</th>
                            <th class="text-center">Tipo Tarjeta</th>
                            <th class="text-center">Cuotas</th>
                            <th class="text-center">Moneda</th>
                            <th class="text-center">Total</th>
                            <th class="text-center" style="min-width: 150px;">Método Pago</th>
                            <th class="text-center">Banco</th>
                            <th class="text-center">Tipo Tarjeta</th>
                            <th class="text-center">Cuotas</th>
                            <th class="text-center">Moneda</th>
                            <th class="text-center">Total</th>
                            <th class="text-center" style="min-width: 150px;">Método Pago</th>
                            <th class="text-center">Banco</th>
                            <th class="text-center">Tipo Tarjeta</th>
                            <th class="text-center">Cuotas</th>
                            <th class="text-center">Moneda</th>
                            <th class="text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $i=1;
                        $estados = ['I' => 'Generado', 'U' => 'Actualizado', 'D' => 'ELiminado'];
                        $tipo_comprobante_values = ['1' => 'BOLETA', '2' => 'FACTURA'];
                        $bancos_values = ["0" => "-", "1" => "BBVA", "2" => "BCP", "3" => "Dinners Club", "4" => "Interbank", "5" => "Otros"];
                        $tipotarjeta_values = ["0" => "-", "1" => "Débito", "2" => "Crédito"];
                        $moneda_values = ["0" => "S/.", "1" => "$"];
                        $metodopago_value = ["0" => "-", "1" => "Efectivo", "3" => "Depósito", "4" => "Tarjeta VISA", "5" => "Tarjeta MASTERCARD", "6" => "Link Tarjeta"];

                        foreach ($rows as $item) {
                            $moneda = '';

                            if ($item['tipo_servicio'] == 1 or $item['tipo_servicio'] == 2 or $item['tipo_servicio'] == 3) {
                                if ($item['tipo_cambio'] == 1) {$moneda = "USD";} else {$moneda = "MN";}
                            } else {
                                if ($item['tipo_cambio'] == 1) {$moneda = "MN";} else {$moneda = "USD";}
                            }

                            print('
                            <tr>
                                <td class="text-center">' . $i++ . '</td>
                                <td class="text-center">' . date('H:i:s', strtotime($item["createdate"])) . '</td>
                                <td class="text-center">' . mb_strtoupper($estados[$item["action"]]) . '</td>
                                <td class="text-center">' . mb_strtolower($item["idusercreate"]) . '</td>
                                <td class="text-center">' . $tipo_comprobante_values[$item["recibo_tip"]] . '</td>
                                <td class="text-center">' . $item["recibo_id"] . '</td>
                                <td>' . mb_strtoupper($item["paciente"]) . '</td>
                                <td>' . mb_strtoupper($item["medico"]) . '</td>
                                <td>' . mb_strtoupper($item["sede"]) . '</td>
                                <td class="text-center">' . $moneda . '</td>
                                <td class="text-center">' . $item["tipo_cambio"] . '</td>
                                <td class="text-center">' . $item["total"] . '</td>
                                <td class="text-center">' . $item["descuento"] . '</td>
                                <td class="text-center">' . $item["estado_anglolab"] . '</td>
                                <td class="text-center">' . $metodopago_value[$item["metodo_pago_1"]] . '</td>
                                <td class="text-center">' . $bancos_values[$item["banco1"]] . '</td>
                                <td class="text-center">' . $tipotarjeta_values[$item["tipotarjeta1"]] . '</td>
                                <td class="text-center">' . mb_strtoupper($item["numerocuotas1"]) . '</td>
                                <td class="text-center">' . $moneda_values[$item["moneda1"]] . '</td>
                                <td class="text-center">' . mb_strtoupper($item["total1"]) . '</td>
                                <td class="text-center">' . $metodopago_value[$item["metodo_pago_2"]] . '</td>
                                <td class="text-center">' . $bancos_values[$item["banco2"]] . '</td>
                                <td class="text-center">' . $tipotarjeta_values[$item["tipotarjeta2"]] . '</td>
                                <td class="text-center">' . mb_strtoupper($item["numerocuotas2"]) . '</td>
                                <td class="text-center">' . $moneda_values[$item["moneda2"]] . '</td>
                                <td class="text-center">' . mb_strtoupper($item["total2"]) . '</td>
                                <td class="text-center">' . $metodopago_value[$item["metodo_pago_3"]] . '</td>
                                <td class="text-center">' . $bancos_values[$item["banco3"]] . '</td>
                                <td class="text-center">' . $tipotarjeta_values[$item["tipotarjeta3"]] . '</td>
                                <td class="text-center">' . mb_strtoupper($item["numerocuotas3"]) . '</td>
                                <td class="text-center">' . $moneda_values[$item["moneda3"]] . '</td>
                                <td class="text-center">' . mb_strtoupper($item["total3"]) . '</td>
                            </tr>');
                        }
                    ?>
                    </tbody>
                </table>
            </form>
        </div>
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