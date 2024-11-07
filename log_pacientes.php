<!DOCTYPE HTML>
<html>
<head>
<?php
    include 'seguridad_login.php';    
    require("_database/database_log.php");
    require("_database/database.php");
	?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/bootstrap.min.css" crossorigin="anonymous">
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
    <?php require ('_includes/menu_sistemas.php'); ?>
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="lista_sistemas.php">Inicio</a></li>
                <li class="breadcrumb-item">Log</li>
                <li class="breadcrumb-item active" aria-current="page">Pacientes</li>
            </ol>
        </nav>
        <?php
            // iniciar variables
            $between = $tipodocumento = $numerodocumento = $apellidos = $nombres = $ini = $fin = "";
            //
            if (isset($_POST) && !empty($_POST)) {
                if (isset($_POST["ini"]) && !empty($_POST["ini"]) && isset($_POST["fin"]) && !empty($_POST["fin"])) {
                    $ini = $_POST['ini'];
                    $fin = $_POST['fin'];
                } else {
                    $ini = $fin = date('Y-m-d');
                }

                $between .= " and (createdate - INTERVAL '5 hours') between '$ini' and '$fin'";

                if (isset($_POST["tipodocumento"]) && $_POST["tipodocumento"] != "") {
                    $tipodocumento = $_POST["tipodocumento"];
                    $between .= " and tip = '$tipodocumento'";
                }

                if (isset($_POST["numerodocumento"]) && $_POST["numerodocumento"] != "") {
                    $numerodocumento = $_POST["numerodocumento"];
                    $between .= " and dni ilike ('%$numerodocumento%')";
                }

                if (isset($_POST["apellidos"]) && $_POST["apellidos"] != "") {
                    $apellidos = $_POST["apellidos"];
                    $between .= " and unaccent(ape) ilike ('%$apellidos%')";
                }

                if (isset($_POST["nombres"]) && $_POST["nombres"] != "") {
                    $nombres = $_POST["nombres"];
                    $between .= " and unaccent(nom) ilike ('%$nombres%')";
                }

                if (isset($_POST["usuario"]) && $_POST["usuario"] != "") {
                    $usuario = $_POST["usuario"];
                    $between .= " and idusercreate = '$usuario'";
                }
            } else {
                $ini = $fin = date('Y-m-d');
                $between .= " and (createdate - INTERVAL '-5 hours') between '$ini' and '$fin'";
            }

            $consulta = $dblog->prepare("SELECT
                tip, dni, ape, nom, rem, fnac, tcel, tcas, tofi, mai, dir, sta, idusercreate, (createdate - INTERVAL '-5 hours') as createdate
                FROM hc_paciente
                WHERE 1=1$between
                ORDER BY createdate DESC");
            $consulta->execute();
            $rows = $consulta->fetchAll(); ?>
        <div data-role="header">
            <div class="card mb-3">
                <h5 class="card-header">Filtros</h5>
                <div class="card-body">
                    <form action="" method="post" data-ajax="false" id="form1">
                        <div class="row pb-2">
                            <!-- mostrar desde hasta -->
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="input-group">
                                    <span class="input-group-addon"> Mostrar Desde</span>
                                    <input class="form-control" name="ini" type="date" value="<?php print($ini); ?>" id="example-datetime-local-input">
                                    <span class="input-group-addon">Hasta</span>
                                    <input class="form-control" name="fin" type="date" value="<?php print($fin); ?>" id="example-datetime-local-input">
                                </div>
                            </div>
                            <!-- tipo documento -->
                            <div class="col-12 col-sm-12 col-md-3 col-lg-3">
                                <div class="input-group">
                                    <span class="input-group-addon">T. Documento</span>
                                    <select class="form-control" name="tipodocumento">
                                        <option value="">SELECCIONAR</option>
                                        <?php
                                            $consulta = $db->prepare("SELECT codigo, nombre
                                            FROM man_tipo_documento_identidad
                                            WHERE estado=1");
                                            $consulta->execute();
                                            $consulta->setFetchMode(PDO::FETCH_ASSOC);
                                            $data1 = $consulta->fetchAll();
                                            foreach ($data1 as $row) {
                                                $sel="";
                                                if ($tipodocumento == $row['codigo']) {
                                                    $sel="selected";
                                                }

                                                print("<option value=".$row['codigo']." $sel>".mb_strtoupper($row['nombre'])."</option>");
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <!-- numero documento -->
                            <div class="col-12 col-sm-12 col-md-3 col-lg-3">
                                <div class="input-group">
                                    <span class="input-group-addon">N° Documento</span>
                                    <input class="form-control" name="numerodocumento" type="text" value="<?php print($numerodocumento); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row pb-2">
                            <!-- apellidos -->
                            <div class="col-12 col-sm-12 col-md-3 col-lg-3">
                                <div class="input-group">
                                    <span class="input-group-addon">Apellidos</span>
                                    <input type="text" class="form-control" name="apellidos" value="<?php print($apellidos); ?>">
                                </div>
                            </div>
                            <!-- apellidos -->
                            <div class="col-12 col-sm-12 col-md-3 col-lg-3">
                                <div class="input-group">
                                    <span class="input-group-addon">Nombres</span>
                                    <input type="text" class="form-control" name="nombres" value="<?php print($nombres); ?>">
                                </div>
                            </div>
                            <!-- usuario -->
                            <div class="col-12 col-sm-12 col-md-3 col-lg-3">
                                <div class="input-group">
                                    <span class="input-group-addon">Usuario</span>
                                    <select class="form-control" name="usuario">
                                        <option value="">SELECCIONAR</option>
                                        <?php
                                            $consulta = $db->prepare("SELECT userX FROM usuario");
                                            $consulta->execute();
                                            $consulta->setFetchMode(PDO::FETCH_ASSOC);
                                            $data1 = $consulta->fetchAll();
                                            foreach ($data1 as $row) {
                                                $sel="";
                                                if ($usuario == $row['userx']) {
                                                    $sel="selected";
                                                }

                                                print("<option value=".$row['userx']." $sel>".mb_strtolower($row['userx'])."</option>");
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 text-center">
                                <input type="Submit" class="btn btn-danger" name="Mostrar" value="Mostrar"/>
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
                            <th class="text-center">Item</th>
                            <th class="text-center">Tipo Documento</th>
                            <th class="text-center">N° Documento</th>
                            <th class="text-center">Apellidos y Nombres</th>
                            <th class="text-center">Fecha Nacimiento</th>
                            <th class="text-center">Teléfono<br>Celular</th>
                            <th class="text-center">Teléfono<br>Casa</th>
                            <th class="text-center">Teléfono<br>Oficina</th>
                            <th class="text-center">Correo Electrónico</th>
                            <th class="text-center">Dirección</th>
                            <th class="text-center">Referido por</th>
                            <th class="text-center">Observaciones</th>
                            <th class="text-center">Usuario</th>
                            <th class="text-center">Fecha Modificación</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $i=1;
                        foreach ($rows as $item)
                        {
                            print('
                            <tr>
                                <td class="text-center">'.$i++.'</td>
                                <td class="text-center">'.$item["tip"].'</td>
                                <td class="text-center">\''.$item["dni"].'</td>
                                <td class="text-center">'.mb_strtoupper($item["ape"]).' '.mb_strtoupper($item["nom"]).'</td>
                                <td class="text-center">'.$item["fnac"].'</td>
                                <td class="text-center">'.$item["tcel"].'</td>
                                <td class="text-center">'.$item["tcas"].'</td>
                                <td class="text-center">'.$item["tofi"].'</td>
                                <td class="text-center">'.$item["mai"].'</td>
                                <td class="text-center">'.$item["dir"].'</td>
                                <td class="text-center">'.$item["rem"].'</td>
                                <td class="text-center">'.$item["sta"].'</td>
                                <td class="text-center">'.mb_strtolower($item["idusercreate"]).'</td>
                                <td class="text-center">'.mb_strtoupper($item["createdate"]).'</td>
                            </tr>');
                        }
                    ?>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="js/lab_celulas.js?v=181119"></script>
</body>
</html>