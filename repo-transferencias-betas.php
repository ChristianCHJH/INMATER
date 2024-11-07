<!DOCTYPE HTML>
<html>
<head>
<?php
     include 'seguridad_login.php';
    require "/_database/database.php"; 
    require("_database/database_log.php");?>
    
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

    <style>
        canvas {
        -moz-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
        }
    </style>
</head>

<body>
    <div class="loader">
        <img src="_images/load.gif" alt="">			
    </div>

    <?php require ('_includes/repolab_menu.php'); ?>

    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="lista_adminlab.php">Inicio</a></li>
                <li class="breadcrumb-item">Reportes</li>
                <li class="breadcrumb-item active" aria-current="page">Transferencias Betas</li>
            </ol>
        </nav>

        <?php
        // iniciar variables
        $ini = $fin = $between = $grafica_tipo = "";
        //
        if (!!$_POST) {
            if (isset($_POST["ini"]) && !empty($_POST["ini"]) && isset($_POST["fin"]) && !empty($_POST["fin"])) {
                $ini = $_POST['ini'];
                $fin = $_POST['fin'];
              } else {
                $ini = $fin = date('Y-m-d');
              }
  
              $between .= " and hr.fec between '$ini' and '$fin'";
        } else {
            $ini = $fin = date('Y-m-d');
            $between .= " and hr.fec between '$ini' and '$fin'";
        }

        $stmt = $db->prepare("SELECT
            la.pro protocolo
            , hp.dni
            , concat(upper(rtrim(ltrim(hp.ape))), ' ', upper(rtrim(ltrim(hp.nom)))) paciente
            , EXTRACT(YEAR FROM hr.fec) - EXTRACT(YEAR FROM hp.fnac) - (CASE WHEN TO_CHAR(hr.fec, 'MMDD') < TO_CHAR(hp.fnac, 'MMDD') THEN 1 ELSE 0 END) edad_calculada
            , case when hr.des_dia >= 1 and hr.des_don is null then 'si' else 'no' end ted
            , case when hr.pago_extras ilike ('%TRANSFERENCIA FRESCO%') then 'si' else 'no' end fresco
            , case lat.beta when 1 then 'positivo'  when 2 then 'negativo'  when 3 then 'bioquimico'  when 4 then 'aborto' else 'pendiente' end beta
            , hr.med medico
            , count(case when lad.d2f_cic = 't' or  lad.d3f_cic = 't' or  lad.d4f_cic = 't' or  lad.d5f_cic = 't' or lad.d6f_cic = 't' then true end) transferidos
            from hc_reprod hr
            inner join hc_paciente hp on hp.dni = hr.dni and hp.fnac <> '1899-12-30'
            inner join lab_aspira la on la.rep = hr.id and la.estado is true
            left join lab_aspira_t lat on lat.pro = la.pro and lat.estado is true
            inner join lab_aspira_dias lad on lad.pro = la.pro and lad.estado is true
            where hr.estado = true and la.f_fin <> '1899-12-30'$between
            and (hr.des_dia >= 1 and hr.des_don is null || hr.pago_extras ilike ('%TRANSFERENCIA FRESCO%'))
            group by hr.id, lad.pro,la.pro,hp.dni,lat.beta
            order by hr.id desc;");
        $stmt->execute();
        $rows = $stmt->fetchAll(); ?>

        <div class="card mb-3">
            <input type="hidden" name="conf">
            <h5 class="card-header"><b>Filtros</b></h5>

            <div class="card-body">
                <form action="" method="post" data-ajax="false" name="form2">
                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                            <div class="input-group-prepend">
                            <span class="input-group-text">Mostrar Desde</span>
                            <input class="form-control form-control-sm" name="ini" type="date" value="<?php print($ini); ?>" id="example-datetime-local-input">
                            <span class="input-group-text">Hasta</span>
                            <input class="form-control form-control-sm" name="fin" type="date" value="<?php print($fin); ?>" id="example-datetime-local-input">
                            </div>
                        </div>

                        <div class="col-12 col-sm-12 col-md-2 col-lg-2 input-group-sm">
                            <input class="form-control btn btn-danger btn-sm" type="Submit" name="agregar" value="Buscar"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mb-3">
            <h5 class="card-header" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">
            <?php
            print('<small><b>Fecha y Hora de Reporte: </b>'.date("Y-m-d H:i:s").'
                <b>, Total Registros: </b>'.count($rows).'
                <b>, Descargar: </b>
                <a href="#" onclick="tableToExcel(\'repo_fragmentacion\', \'fragmentacion\')" class="ui-btn ui-mini ui-btn-inline">
                    <img src="_images/excel.png" height="18" width="18" alt="icon name">
                </a></small>'); ?>
            </h5>

            <table width="100%" class='table table-responsive table-bordered align-middle header-fixed' style='height: 50vh;' data-filter="true" data-input="#filtro" id="repo_fragmentacion">
                <thead class="thead-dark">
                    <tr>
                        <th class="text-center">Protocolo</th>
                        <th>N° Documento</th>
                        <th class="text-center">Paciente</th>
                        <th class="text-center">Edad</th>
                        <th class="text-center">Médico</th>
                        <th class="text-center">TED</th>
                        <th class="text-center">Fresco</th>
                        <th class="text-center">Beta</th>
                        <th class="text-center">Transferidos</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $i=1;
                    foreach ($rows as $item) {
                        print('<tr>
                            <td class="text-center">' . $item["protocolo"] . '</td>
                            <td>' . $item["dni"] . '</td>
                            <td class="text-center">' . $item["paciente"] . '</td>
                            <td class="text-center">' . $item["edad_calculada"] . '</td>
                            <td class="text-center">' . $item["medico"] . '</td>
                            <td class="text-center">' . $item["ted"] . '</td>
                            <td class="text-center">' . $item["fresco"] . '</td>
                            <td class="text-center">' . $item["beta"] . '</td>
                            <td class="text-center">' . $item["transferidos"] . '</td>
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
  </script>
</body>
</html>