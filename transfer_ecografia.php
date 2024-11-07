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
    $stmt = $db->prepare("SELECT * from
      transfer_ecografia
      where estado = 1
      order by nombre;");
    $stmt->execute();
    $i=1; ?>
    <?php require ('_includes/menu-admin.php'); ?>
    <div class="container">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="lista-admin.php" class="no-underline">Inicio</a></li>
          <li class="breadcrumb-item">Laboratorio</li>
          <li class="breadcrumb-item active" aria-current="page">Transferencia - Ecografía</li>
        </ol>
      </nav>
      <div data-role="header">
        <div class="alert alert-warning alert-dismissible fade show" id="mensaje" style="display: none;" role="alert">
          <div><strong>Mensaje!</strong> .</div>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="card mb-3">
          <h5 class="card-header" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample"><small><b>Nuevo</b></small></h5>
          <div class="card-body">
            <input type="hidden" id="entidad_id" value="0">
            <div class="row pb-2">
              <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">Código</span>
                  <input type="number" class="form-control form-control-sm" name="codigo" id="codigo" required>
                </div>
              </div>
              <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">Nombre</span>
                  <input type="text" class="form-control form-control-sm" name="nombre" id="nombre" required>
                </div>
              </div>
              <div class="col-12 col-sm-12 col-md-2 col-lg-2 input-group-sm">
                <input type="submit" name="agregar" id="agregar" value="Agregar" class="btn btn-danger btn-sm"/>
              </div>
            </div>
          </div>
        </div>
        <div class="card mb-3">
          <h5 class="card-header" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">
            <small><b>Lista: </b><i>Total registros: <?php print($stmt->rowCount()); ?></i></small>
          </h5>
          <div class="card-body collapse show mx-auto" id="collapseExample">
            <table width="100%" class="table table-hover table-responsive table-bordered align-middle" data-filter="true" data-input="#filtro">
              <thead class="thead-dark">
                <tr>
                  <th>Item</th>
                  <th>Código</th>
                  <th>Nombre</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
              <?php
                while ($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  print('
                  <tr>
                    <td align="center">'.$i++.'</td>
                    <td align="center">'.$item["codigo"].'</td>
                    <td>'.mb_strtoupper($item["nombre"]).'</td>
                    <td align="center">
                      <a href="transfer_ecografia_edit.php?id='.$item["id"].'"><img src="_libraries/open-iconic/svg/pencil.svg" height="18" width="18" alt="icon name"></a>
                    </td>
                  </tr>');
                } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <!-- modal confirmar -->
      <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="modal_confirmar">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Confirmar</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">¿Realmente desea agregar los datos?</div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default btn-sm" id="modal-btn-no">Cancelar</button>
              <button type="button" class="btn btn-dark btn-sm" id="modal-btn-si">Confirmar</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.v4/bootstrap.min.js" crossorigin="anonymous"></script>
	<script src="js/transfer_ecografia.js?v=200319" crossorigin="anonymous"></script>
  </body>
</html>