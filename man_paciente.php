<!DOCTYPE HTML>
<html>
<head>
<?php
   include 'seguridad_login.php'
    ?>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="_images/favicon.png" type="image/x-icon">
  <link rel="stylesheet" href="css/bootstrap.v4/bootstrap.min.css" crossorigin="anonymous">
  <link rel="stylesheet" type="text/css" href="css/global.css">
</head>
  <body>
    <?php
      $numero_documento = "";
      $apellidos_nombres = "";
      $condicion_historia_1 = "";
      $condicion_historia_2 = "";

      if (!!$_POST) {
        // var_dump($_POST);
        if (!!$_POST["numero_documento"]) {
          $numero_documento = $_POST["numero_documento"];
          $condicion_historia_1 = " and (hp.dni ilike ('%$numero_documento%'))";
          $condicion_historia_2 = " and (hpa.p_dni ilike ('%$numero_documento%'))";
        }
        if (!!$_POST["apellidos_nombres"]) {
          $apellidos_nombres = $_POST["apellidos_nombres"];
          $condicion_historia_1 = " and (unaccent(hp.ape) ilike ('%$apellidos_nombres%') or hp.nom ilike ('%$apellidos_nombres%'))";
          $condicion_historia_2 = " and (unaccent(hpa.p_ape) ilike ('%$apellidos_nombres%') or hpa.p_nom ilike ('%$apellidos_nombres%'))";
        }
      } else {
        $condicion_historia_1 = " and 1 = 2";
        $condicion_historia_2 = " and 1 = 2";
      }

      $stmt = $db->prepare("SELECT
				1 historia, 'PACIENTE GINECOLOGICA' historia_descripcion,  hp.dni numero_documento, hp.ape apellidos, hp.nom nombres
				from hc_paciente hp
				where hp.estado = 1$condicion_historia_1
				order by hp.ape;");
      $stmt->execute();
			$data_historia_1 = $stmt->fetchAll(PDO::FETCH_ASSOC);

			$stmt = $db->prepare("SELECT
				2 historia, 'PACIENTE ANDROLOGIA' historia_descripcion, hpa.p_dni numero_documento, hpa.p_ape apellidos, hpa.p_nom nombres
				from hc_pareja hpa
				where hpa.estado = 1$condicion_historia_2
				order by hpa.p_ape;");
      $stmt->execute();
			$data_historia_2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$data = array_merge($data_historia_1, $data_historia_2);
      $i=1; ?>
    <?php require ('_includes/menu-admin.php'); ?>
    <div class="container">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="lista_adminlab.php">Inicio</a></li>
          <li class="breadcrumb-item">Mantenimiento</li>
          <li class="breadcrumb-item active" aria-current="page">Datos Generales</li>
        </ol>
      </nav>
      <div data-role="header">
        <div class="card mb-3">
          <h5 class="card-header" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample"><small><b>Filtros</b></small></h5>
          <div class="card-body">
            <form action="" method="post">
              <div class="row pb-2">
                <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                  <div class="input-group-prepend">
                    <span class="input-group-text">N° de documento</span>
                    <input type="text" class="form-control form-control-sm" name="numero_documento" id="numero_documento" value="<?php echo $numero_documento; ?>" data-mini="true">
                  </div>
                </div>
                <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Apellidos o Nombres</span>
                    <input type="text" class="form-control form-control-sm"  name="apellidos_nombres" value="<?php print($apellidos_nombres); ?>">
                  </div>
                </div>
                <div class="col-12 col-sm-12 col-md-2 col-lg-2 input-group-sm">
                  <input type="Submit" name="filtrar" id="filtrar" value="Filtrar" class="btn btn-danger btn-sm"/>
                </div>
              </div>
            </form>
          </div>
        </div>
        <div class="card mb-3">
          <h5 class="card-header" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">
            <small><b>Lista: </b><i>Total registros: <?php print(count($data)); ?></i></small>
          </h5>
          <div class="card-body collapse show mx-auto" id="collapseExample">
            <table width="100%" class="table table-hover table-responsive table-bordered align-middle" data-filter="true" data-input="#filtro">
              <thead class="thead-dark">
                <tr>
                  <th>Item</th>
                  <th class="text-center">Historia</th>
                  <th>N° Documento</th>
                  <th>Apellidos y Nombres</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
              <?php
								foreach ($data as $key => $item) {
                  print('
                  <tr>
                    <td align="center">'.$i++.'</td>
                    <td align="center">'.$item["historia_descripcion"].'</td>
                    <td align="center">'.$item["numero_documento"].'</td>
                    <td>'.mb_strtoupper($item["apellidos"].' '.$item["nombres"]).'</td>
                    <td align="center">
                      <a href="man_paciente_edit.php?historia='.$item["historia"].'&id='.$item["numero_documento"].'"><img src="_libraries/open-iconic/svg/pencil.svg" height="18" width="18" alt="icon name"></a>
                    </td>
                  </tr>');
                } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.v4/bootstrap.min.js" crossorigin="anonymous"></script>
  </body>
</html>