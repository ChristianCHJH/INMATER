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
    <?php require ('_includes/menu_sistemas.php'); ?>
    <div class="container">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="lista_sistemas.php">Inicio</a></li>
          <li class="breadcrumb-item">Log</li>
          <li class="breadcrumb-item active" aria-current="page">RA</li>
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

              $between .= " and (r.createdate - INTERVAL '-5 hours') between '$ini' and '$fin'";

              if (isset($_POST["accion"]) && $_POST["accion"] != "") {
                $accion = $_POST["accion"];
                $between .= " and a.codigo = '$accion'";
              }

              if (isset($_POST["tipodocumento"]) && $_POST["tipodocumento"] != "") {
                  $tipodocumento = $_POST["tipodocumento"];
                  $between .= " and ti.codigo = '$tipodocumento'";
              }

              if (isset($_POST["numerodocumento"]) && $_POST["numerodocumento"] != "") {
                  $numerodocumento = $_POST["numerodocumento"];
                  $between .= " and r.dni ilike ('%$numerodocumento%')";
              }

              if (isset($_POST["apellidos"]) && $_POST["apellidos"] != "") {
                  $apellidos = $_POST["apellidos"];
                  $between .= " and unaccent(p.ape) ilike ('%$apellidos%')";
              }

              if (isset($_POST["nombres"]) && $_POST["nombres"] != "") {
                  $nombres = $_POST["nombres"];
                  $between .= " and unaccent(p.nom) ilike ('%$nombres%')";
              }

              /* if (isset($_POST["usuario"]) && $_POST["usuario"] != "") {
                  $usuario = $_POST["usuario"];
                  $between .= " and idusercreate = '$usuario'";
              } */
          } else {
            $ini = $fin = date('Y-m-d');
            $between .= " and (r.createdate - INTERVAL '-5 hours') between '$ini' and '$fin'";
          }

          /* print($between); */

          $consulta = $db->prepare("SELECT
            r.reprod_id,
            upper(ti.codigo) tipo_documento_identidad, r.dni,
            upper(p.ape) apellidos, upper(p.nom) nombres,
            r.p_dni, r.t_mue, r.p_dni_het, r.fec, r.med, r.eda, r.poseidon, r.p_dtri, r.p_cic, r.p_fiv, r.p_icsi, r.des_dia, r.des_don, r.p_od, r.p_don, r.don_todo, r.p_cri, r.p_iiu, r.p_extras, r.p_notas, r.n_fol, r.fur, r.f_aco, r.fsh, r.lh, r.est, r.prol, r.ins, r.t3, r.t4, r.tsh, r.amh, r.inh, r.m_agh, r.m_vdrl, r.m_clam, r.m_his, r.m_hsg, r.f_fem, r.f_mas, r.con_fec, r.con_od, r.con_oi, r.con_end, r.con1_med, r.con2_med, r.con3_med, r.con4_med, r.con5_med, r.con_iny, r.con_obs, r.obs, r.f_iny, r.h_iny, r.f_asp, r.idturno, r.f_tra, r.h_tra, r.complicacionesparto_id, r.complicacionesparto_motivo, r.idturno_tra, r.cancela, r.pago_extras, r.pago_notas, r.pago_obs, r.repro,
            a.descripcion accion,
            r.idusercreate, (r.createdate - INTERVAL '-5 hours') as createdate
            from inmater_lg_intranet.hc_reprod r
            inner join inmater_prod_intranet.hc_paciente p on p.dni = r.dni
            inner join inmater_prod_intranet.man_tipo_documento_identidad ti on ti.codigo = p.tip
            inner join inmater_prod_intranet.man_action a on a.codigo = r.action
            where r.estado = true and 1=1$between
            order by r.createdate desc");
          $consulta->execute();
          $rows = $consulta->fetchAll(); ?>
      <div data-role="header">
          <div class="card mb-3">
              <h5 class="card-header">Filtros</h5>
              <div class="card-body">
                  <form action="" method="post" data-ajax="false" id="form1">
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
                          <!-- accion -->
                          <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                              <div class="input-group-prepend">
                                  <span class="input-group-text">Acción</span>
                                  <select class="form-control form-control-sm" name="accion">
                                      <option value="">SELECCIONAR</option>
                                      <?php
                                        $consulta = $db->prepare("SELECT codigo, descripcion nombre
                                        FROM man_action
                                        WHERE estado=1");
                                        $consulta->execute();
                                        $consulta->setFetchMode(PDO::FETCH_ASSOC);
                                        $data1 = $consulta->fetchAll();

                                        foreach ($data1 as $row) {
                                            $sel="";
                                            if ($accion == $row['codigo']) {
                                                $sel="selected";
                                            }

                                            print("<option value=".$row['codigo']." $sel>".mb_strtoupper($row['nombre'])." (".mb_strtoupper($row['codigo']).")</option>");
                                        }
                                      ?>
                                  </select>
                              </div>
                          </div>
                      </div>
                      <div class="row pb-2">
                        <!-- apellidos -->
                        <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                          <div class="input-group-prepend">
                            <span class="input-group-text">Apellidos</span>
                            <input type="text" class="form-control form-control-sm" name="apellidos" value="<?php print($apellidos); ?>">
                          </div>
                        </div>
                        <!-- apellidos -->
                        <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                          <div class="input-group-prepend">
                            <span class="input-group-text">Nombres</span>
                            <input type="text" class="form-control form-control-sm" name="nombres" value="<?php print($nombres); ?>">
                          </div>
                        </div>
                        <!-- tipo documento -->
                        <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">T. Documento</span>
                                <select class="form-control form-control-sm" name="tipodocumento">
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

                                          print("<option value=".$row['codigo']." $sel>".mb_strtoupper($row['nombre'])." (".mb_strtoupper($row['codigo']).")</option>");
                                      }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <!-- numero documento -->
                        <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">N° Documento</span>
                                <input class="form-control form-control-sm" name="numerodocumento" type="text" value="<?php print($numerodocumento); ?>">
                            </div>
                        </div>
                        <!-- usuario -->
                        <!-- <div class="col-12 col-sm-12 col-md-3 col-lg-3">
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
                        </div> -->
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
                          <th>Apellidos y Nombres</th>
                          <th>p_dni</th>
                          <th>t_mue</th>
                          <th>p_dni_het</th>
                          <th>fec</th>
                          <th>med</th>
                          <th>eda</th>
                          <th>poseidon</th>
                          <th>p_dtri</th>
                          <th>p_cic</th>
                          <th>p_fiv</th>
                          <th>p_icsi</th>
                          <th>des_dia</th>
                          <th>des_don</th>
                          <th>p_od</th>
                          <th>p_don</th>
                          <th>don_todo</th>
                          <th>p_cri</th>
                          <th>p_iiu</th>
                          <th>p_extras</th>
                          <th>p_notas</th>
                          <th>n_fol</th>
                          <th>fur</th>
                          <th>f_aco</th>
                          <th>fsh</th>
                          <th>lh</th>
                          <th>est</th>
                          <th>prol</th>
                          <th>ins</th>
                          <th>t3</th>
                          <th>t4</th>
                          <th>tsh</th>
                          <th>amh</th>
                          <th>inh</th>
                          <th>m_agh</th>
                          <th>m_vdrl</th>
                          <th>m_clam</th>
                          <th>m_his</th>
                          <th>m_hsg</th>
                          <th>f_fem</th>
                          <th>f_mas</th>
                          <!-- <th>con_fec</th> -->
                          <th>con_od</th>
                          <th>con_oi</th>
                          <th>con_end</th>
                          <th>con1_med</th>
                          <th>con2_med</th>
                          <th>con3_med</th>
                          <th>con4_med</th>
                          <th>con5_med</th>
                          <th>con_iny</th>
                          <th>con_obs</th>
                          <th>obs</th>
                          <th>f_iny</th>
                          <th>h_iny</th>
                          <th>f_asp</th>
                          <th>idturno</th>
                          <th>f_tra</th>
                          <th>h_tra</th>
                          <th>complicacionesparto_id</th>
                          <th>complicacionesparto_motivo</th>
                          <th>idturno_tra</th>
                          <th>cancela</th>
                          <th>pago_extras</th>
                          <th>pago_notas</th>
                          <th>pago_obs</th>
                          <th>repro</th>
                          <th class="text-center">Acción</th>
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
                            <td class="text-center">'.$item["tipo_documento_identidad"].'</td>
                            <td class="text-center">\''.$item["dni"].'</td>
                            <td>'.mb_strtoupper($item["apellidos"]).' '.mb_strtoupper($item["nombres"]).'</td>
                            <td>'.$item["p_dni"].'</td>
                            <td>'.$item["t_mue"].'</td>
                            <td>'.$item["p_dni_het"].'</td>
                            <td>'.$item["fec"].'</td>
                            <td>'.$item["med"].'</td>
                            <td>'.$item["eda"].'</td>
                            <td>'.$item["poseidon"].'</td>
                            <td>'.$item["p_dtri"].'</td>
                            <td>'.$item["p_cic"].'</td>
                            <td>'.$item["p_fiv"].'</td>
                            <td>'.$item["p_icsi"].'</td>
                            <td>'.$item["des_dia"].'</td>
                            <td>'.$item["des_don"].'</td>
                            <td>'.$item["p_od"].'</td>
                            <td>'.$item["p_don"].'</td>
                            <td>'.$item["don_todo"].'</td>
                            <td>'.$item["p_cri"].'</td>
                            <td>'.$item["p_iiu"].'</td>
                            <td>'.$item["p_extras"].'</td>
                            <td>'.$item["p_notas"].'</td>
                            <td>'.$item["n_fol"].'</td>
                            <td>'.$item["fur"].'</td>
                            <td>'.$item["f_aco"].'</td>
                            <td>'.$item["fsh"].'</td>
                            <td>'.$item["lh"].'</td>
                            <td>'.$item["est"].'</td>
                            <td>'.$item["prol"].'</td>
                            <td>'.$item["ins"].'</td>
                            <td>'.$item["t3"].'</td>
                            <td>'.$item["t4"].'</td>
                            <td>'.$item["tsh"].'</td>
                            <td>'.$item["amh"].'</td>
                            <td>'.$item["inh"].'</td>
                            <td>'.$item["m_agh"].'</td>
                            <td>'.$item["m_vdrl"].'</td>
                            <td>'.$item["m_clam"].'</td>
                            <td>'.$item["m_his"].'</td>
                            <td>'.$item["m_hsg"].'</td>
                            <td>'.$item["f_fem"].'</td>
                            <td>'.$item["f_mas"].'</td>
                            <!-- <td>'.$item["con_fec"].'</td> -->
                            <td>'.$item["con_od"].'</td>
                            <td>'.$item["con_oi"].'</td>
                            <td>'.$item["con_end"].'</td>
                            <td>'.$item["con1_med"].'</td>
                            <td>'.$item["con2_med"].'</td>
                            <td>'.$item["con3_med"].'</td>
                            <td>'.$item["con4_med"].'</td>
                            <td>'.$item["con5_med"].'</td>
                            <td>'.$item["con_iny"].'</td>
                            <td>'.$item["con_obs"].'</td>
                            <td>'.$item["obs"].'</td>
                            <td>'.$item["f_iny"].'</td>
                            <td>'.$item["h_iny"].'</td>
                            <td>'.$item["f_asp"].'</td>
                            <td>'.$item["idturno"].'</td>
                            <td>'.$item["f_tra"].'</td>
                            <td>'.$item["h_tra"].'</td>
                            <td>'.$item["complicacionesparto_id"].'</td>
                            <td>'.$item["complicacionesparto_motivo"].'</td>
                            <td>'.$item["idturno_tra"].'</td>
                            <td>'.$item["cancela"].'</td>
                            <td>'.$item["pago_extras"].'</td>
                            <td>'.$item["pago_notas"].'</td>
                            <td>'.$item["pago_obs"].'</td>
                            <td>'.$item["repro"].'</td>
                            <td class="text-center">'.mb_strtolower($item["accion"]).'</td>
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
  <script src="js/chosen.jquery.min.js"></script>
  <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>
</html>