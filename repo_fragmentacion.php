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
  <?php require ('_includes/repolab_menu.php'); ?>
  <div class="container">
      <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="lista_sistemas.php">Inicio</a></li>
              <li class="breadcrumb-item">Adminlab</li>
              <li class="breadcrumb-item">Reportes</li>
              <li class="breadcrumb-item active" aria-current="page">Fragmentación de ADN Espermático</li>
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

            $between .= " and a_mue between '$ini' and '$fin'";
          } else {
            $ini = $fin = date('Y-m-d');
            $between .= " and a_mue between '$ini' and '$fin'";
          }

          $consulta = $db->prepare("SELECT
            a_mue fecha, a_nom paciente, a_med medico, a_sta resultado
            from hc_analisis
            where a_exa = ?$between
            order by a_mue desc");
          $consulta->execute(['Fragmentación de ADN espermático']);
          $rows = $consulta->fetchAll(); ?>
      <div data-role="header">
      <form action="" method="post" data-ajax="false" name="form2">
        <div class="card mb-3">
          <input type="hidden" name="conf">
          <h5 class="card-header"><b>Filtros</b></h5>
          <div class="card-body">
            <div class="row pb-2">
              <!-- mostrar desde hasta -->
              <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">Mostrar Desde</span>
                  <input class="form-control form-control-sm" name="ini" type="date" value="<?php print($ini); ?>" id="example-datetime-local-input">
                  <span class="input-group-text">Hasta</span>
                  <input class="form-control form-control-sm" name="fin" type="date" value="<?php print($fin); ?>" id="example-datetime-local-input">
                </div>
              </div>
              <div class="col-12 col-sm-12 col-md-1 col-lg-1 input-group-sm">
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
            <a href="#" onclick="tableToExcel(\'repo_fragmentacion\', \'fragmentacion\')" class="ui-btn ui-mini ui-btn-inline">
                <img src="_images/excel.png" height="18" width="18" alt="icon name">
            </a></small>'); ?>
        </h5>
        <form action="" method="post" data-ajax="false" name="form2">
            <table width="100%" class='table table-responsive table-bordered align-middle header-fixed' style='height: 50vh;' data-filter="true" data-input="#filtro" id="repo_fragmentacion">
                <thead class="thead-dark">
                    <tr>
                        <th class="text-center">Item</th>
                        <th>Paciente</th>
                        <th class="text-center">Médico</th>
                        <th class="text-center">Fecha</th>
                        <th class="text-center">Resultado</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                  $i=1;
                  foreach ($rows as $item) {
                    print('
                    <tr>
                      <td class="text-center">'.$i++.'</td>
                      <td>'.mb_strtoupper($item["paciente"]).'</td>
                      <td class="text-center">'.mb_strtoupper($item["medico"]).'</td>
                      <td class="text-center">'.$item["fecha"].'</td>
                      <td class="text-center">'.mb_strtoupper($item["resultado"]).'</td>
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