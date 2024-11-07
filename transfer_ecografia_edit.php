<?php
 include 'seguridad_login.php';

  // verificar id
  if (!!$_GET["id"]) {
    $id = $_GET["id"];
    require("_database/db_transfer_ecografia.php");
    $data = traer_transfer_ecografia($id);
  } else {
    print("No seleccionó ningún dato.");
    exit();
  } ?>
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="_images/favicon.png" type="image/x-icon">
  <link rel="stylesheet" href="css/bootstrap.v4/bootstrap.min.css" crossorigin="anonymous">
  <link rel="stylesheet" type="text/css" href="css/global.css">
</head>
<body>
  <?php require ('_includes/menu-admin.php'); ?>
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="lista-admin.php" class="no-underline">Inicio</a></li>
        <li class="breadcrumb-item">Laboratorio</li>
        <li class="breadcrumb-item"><a href="transfer_ecografia.php" class="no-underline">Transferencia - Ecografía</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php print(mb_strtoupper($data["nombre"])); ?></li>
      </ol>
    </nav>
    <div class="card mb-3">
      <?php print('
      <h5 class="card-header">
        <small><b>Editar: </b>'.mb_strtoupper($data["nombre"]).'</small>
      </h5>'); ?>
      <div class="card-body">
          <input type="hidden" id="entidad_id" value="<?php print($id); ?>">
          <div class="row pb-2">
            <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
              <div class="input-group-prepend">
                <span class="input-group-text">Código</span>
                <input type="number" class="form-control form-control-sm text-center" name="codigo" id="codigo" value="<?php echo $data["codigo"]; ?>" required>
              </div>
            </div>
            <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
              <div class="input-group-prepend">
                <span class="input-group-text">Nombre</span>
                <input type="text" class="form-control form-control-sm"  name="nombre" id="nombre" value="<?php print(mb_strtoupper($data["nombre"])); ?>" required>
              </div>
            </div>
          </div>
          <div class="row pb-2">
            <div class="col-12 col-sm-12 col-md-12 text-center">
              <input type="submit" class="btn btn-danger" id="guardar" name="guardar" value="Guardar"/>
              <a href="transfer_ecografia.php" class="btn btn-secondary">Cancelar</a>
            </div>
          </div>
          <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="modal_confirmar">
            <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Confirmar</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">¿Realmente desea editar los datos?</div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" id="modal-btn-no">Cancelar</button>
                  <button type="button" class="btn btn-dark" id="modal-btn-si">Confirmar</button>
                </div>
              </div>
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