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
    <link rel="stylesheet" href="css/chosen.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
    <link rel="stylesheet" href="css/bootstrap.v4/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="css/global.css" crossorigin="anonymous">
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
    <?php
    $stmt = $db->prepare("SELECT role FROM usuario WHERE userx=?");
    $stmt->execute(array($login));
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data["role"] == "9") {
        require ('_includes/repolab_menu.php');
    }

    if ($data["role"] == '3' or $data["role"] == '10' or $data["role"] == '19' or $data["role"] == '20') {
        require ('_includes/menu_facturacion.php');
    } ?>
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Inicio</li>
                <li class="breadcrumb-item">Reportes</li>
                <li class="breadcrumb-item active" aria-current="page">Crio</li>
            </ol>
        </nav>
        <?php
            // iniciar variables
            $between = $tipodocumento = $numerodocumento = $apellidos = $nombres = $ini = $fin = $tipo_fecha_value = "";
            //
            if (isset($_POST) && !empty($_POST)) {
                if (isset($_POST["ini"]) && !empty($_POST["ini"]) && isset($_POST["fin"]) && !empty($_POST["fin"])) {
                    $ini = $_POST['ini'];
                    $fin = $_POST['fin'];
                } else {
                    $ini = $fin = date('Y-m-d');
                }

                if (isset($_POST["tipo_fecha"]) && !empty($_POST["tipo_fecha"])) {
                    switch ($_POST["tipo_fecha"]) {
                        case 'fecha_aspi':
                            $between .= " and (a.f_pun between '$ini' and '$fin' or (r.des_dia = 0 and a.fec0 between '$ini' and '$fin'))";
                            $tipo_fecha_value = "fecha_aspi";
                            break;
                        case 'fecha_crio':
                            $between .= " and (a.fec5 between '$ini' and '$fin' or a.fec6 between '$ini' and '$fin')";
                            $tipo_fecha_value = "fecha_crio";
                            break;
                        case 'fecha_todas':
                            $between .= " and ((a.f_pun between '$ini' and '$fin' or (r.des_dia = 0 and a.fec0 between '$ini' and '$fin')) or (a.fec5 between '$ini' and '$fin' or a.fec6 between '$ini' and '$fin'))";
                            $tipo_fecha_value = "fecha_todas";
                            break;
                        
                        default:
                            break;
                    }
                }
            } else {
                $ini = $fin = date('Y-m-d');
                $between .= " and (a.f_pun between '$ini' and '$fin' or (r.des_dia = 0 and a.fec0 between '$ini' and '$fin'))";
                $tipo_fecha_value = "fecha_aspi";
            }

            $consulta = $db->prepare("SELECT
                  r.id,
                  r.des_dia,
                  r.p_extras,
                  case when a.f_pun = '4713-01-01 BC' then a.fec0 else a.f_pun end fecha_aspiracion,
                  case when count(b.d5f_cic = 'C') > 0 then a.fec5 else a.fec6 end fecha_crio,
                  pac.tip documento_identidad, pac.dni numero_documento, coalesce(pac.tcel, '') telefono, pac.ape apellidos, pac.nom nombres, coalesce(pac.mai, '-') correo,
                  par.p_ape apellidos_pareja, par.p_nom nombres_pareja,
                  r.med medico, b.pro protocolo, a.book cuaderno, a.hoja, count(b.pro) cantidad_crio,
                  count(case when b.ngs1 = 1 then true end) cantidad_normal,
                  count(case when b.ngs1 = 2 then true end) cantidad_anormal,
                  count(case when b.ngs1 = 4 then true end) cantidad_mosaico
                  from hc_reprod r
                  inner join lab_aspira a on a.rep = r.id and a.estado is true
                  inner join lab_aspira_dias b on b.pro = a.pro and (b.d5f_cic = 'C' or b.d6f_cic = 'C') and b.estado is true
                  inner join hc_paciente pac on pac.dni = r.dni
                  inner join man_tipo_documento_identidad td on td.codigo = pac.tip
                  left join hc_pareja par on par.p_dni = r.p_dni
                  where r.estado = true and 1 = 1$between
                  group by r.id, pac.tip, pac.dni, td.codigo, r.p_dni, a.rep, b.pro, a.pro, par.p_ape, par.p_nom
                  order by a.f_pun desc");
$consulta->execute();


            $rows = $consulta->fetchAll(); ?>
        <div data-role="header">
        <form action="" method="post" data-ajax="false" name="form2">
            <div class="card mb-3">
                <input type="hidden" name="conf">
                <h5 class="card-header">Información General</h5>
                <div class="card-body">
                    <div class="row pb-2">
                          <!-- mostrar desde hasta -->
                          <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tipo_fecha" id="fecha_aspi" value="fecha_aspi" <?php if($tipo_fecha_value == "fecha_aspi") { print("checked"); } ?>>
                                <label class="form-check-label" for="fecha_aspi">Fecha Aspiración</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tipo_fecha" id="fecha_crio" value="fecha_crio" <?php if($tipo_fecha_value == "fecha_crio") { print("checked"); } ?>>
                                <label class="form-check-label" for="fecha_crio">Fecha Crio</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tipo_fecha" id="fecha_todas" value="fecha_todas" <?php if($tipo_fecha_value == "fecha_todas") { print("checked"); } ?>>
                                <label class="form-check-label" for="fecha_todas">Todas</label>
                            </div>
                            <div class="input-group-prepend">
                              <span class="input-group-text">Mostrar Desde</span>
                              <input class="form-control form-control-sm" name="ini" type="date" value="<?php print($ini); ?>" id="example-datetime-local-input">
                              <span class="input-group-text">Hasta</span>
                              <input class="form-control form-control-sm" name="fin" type="date" value="<?php print($fin); ?>" id="example-datetime-local-input">
                            </div>
                          </div>
                          <div class="col-12 col-sm-12 col-md-12 col-lg-2 pt-2 d-flex align-items-end">
                            <input class="form-control btn btn-danger btn-sm" type="Submit" name="agregar" value="Buscar"/>
                          </div>
                    </div>
                </div>
            </div>
        </form>
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
                            <th class="text-center">Fecha<br>Aspiración</th>
                            <th class="text-center">Fecha Crio</th>
                            <th class="text-center">Tipo<br>Documento</th>
                            <th class="text-center">N° Documento</th>
                            <th class="text-center">Teléfono</th>
                            <th class="text-center">Apellidos y Nombres</th>
                            <th class="text-center">Correo</th>
                            <th class="text-center">Apellidos y Nombres</th>
                            <th class="text-center">Médico</th>
                            <th class="text-center">Número<br>protocolo</th>
                            <th class="text-center">Cuaderno</th>
                            <th class="text-center">Hoja</th>
                            <th class="text-center">Total Crio</th>
                            <th class="text-center">Embriones aun congelados</th>
                            <th class="text-center">NGS</th>
                            <th class="text-center">Normales</th>
                            <th class="text-center">Anormales</th>
                            <th class="text-center">Mosaico</th>
                            <th class="text-center">Protocolo TED</th>
                            <th class="text-center">Embriones<br>transferidos</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                      $i=1;
                      foreach ($rows as $item) {
                        $ngs = "no";

                        if (strpos($item["p_extras"], "NGS") !== false) {
                            $ngs = "si";
                        } else {
                            $ngs = "no";
                        }

                        if ($ngs == "si" && !file_exists('analisis/ngs_'.$item["protocolo"].'.pdf')) {
                          $ngs = "no cargado";
                        }

                        // consulta de embriones transferidos
                        $stmt = $db->prepare("SELECT
                            STRING_AGG(pro, ',') pro, COUNT(*) cantidad_ted
                            FROM lab_aspira_dias
                            WHERE pro_c = ? and estado is true");
                        $stmt->execute([$item["protocolo"]]);

                        $data = $stmt->fetch(PDO::FETCH_ASSOC);

                        print('
                        <tr>
                          <td class="text-center">'.$i++.'</td>
                          <td class="text-center">'.$item["fecha_aspiracion"].'</td>
                          <td class="text-center">'.$item["fecha_crio"].'</td>
                          <td class="text-center">\''.$item["documento_identidad"].'</td>
                          <td class="text-center">\''.$item["numero_documento"].'</td>
                          <td class="text-center">'.$item["telefono"].'</td>
                          <td class="text-center">'.mb_strtoupper($item["apellidos"]).' '.mb_strtoupper($item["nombres"]).'</td>
                          <td class="text-center">'.mb_strtolower($item["correo"]).'</td>
                          <td class="text-center">'.mb_strtoupper($item["apellidos_pareja"]).' '.mb_strtoupper($item["nombres_pareja"]).'</td>
                          <td class="text-center">'.$item["medico"].'</td>
                          <td class="text-center">\''.$item["protocolo"].'</td>
                          <td class="text-center">'.$item["cuaderno"].'</td>
                          <td class="text-center">'.$item["hoja"].'</td>
                          <td class="text-center">'.$item["cantidad_crio"].'</td>
                          <td class="text-center">'.($item["cantidad_crio"] - $data["cantidad_ted"]).'</td>
                          <td class="text-center">'.$ngs.'</td>
                          <td class="text-center">'.$item["cantidad_normal"].'</td>
                          <td class="text-center">'.$item["cantidad_anormal"].'</td>
                          <td class="text-center">'.$item["cantidad_mosaico"].'</td>
                          <td class="text-center">'.$data["pro"].'</td>
                          <td class="text-center">'.$data["cantidad_ted"].'</td>
                        </tr>');
                      } ?>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="js/lab_celulas.js?v=181119"></script>
    <script>
        jQuery(window).load(function (event) {
            jQuery('.loader').fadeOut(1000);
        });
    </script>
</body>
</html>