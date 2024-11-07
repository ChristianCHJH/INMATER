<!DOCTYPE HTML>
<html>
<head>
<?php
   include 'seguridad_login.php'
    ?>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="_images/favicon.png" type="image/x-icon">
  <link rel="stylesheet" href="css/chosen.min.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
  <link rel="stylesheet" href="css/bootstrap.v4/bootstrap.min.css" crossorigin="anonymous">
  <link rel="stylesheet" href="css/global.css" crossorigin="anonymous">
</head>
<body>
  <?php require ('_includes/menu_facturacion.php'); ?>
  <div class="container">
    <?php
      if (isset($_GET["id"]) && !empty($_GET["id"])) {
        $stmt = $db->prepare("SELECT * FROM sedes_contabilidad s where id = ?;");
        $stmt->execute([$_GET["id"]]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
      }
    ?>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">Inicio</li>
        <li class="breadcrumb-item"><a href="lista_facturacion.php">Facturación</a></li>
        <li class="breadcrumb-item" aria-current="page">Mantenimiento</li>
        <li class="breadcrumb-item"><a href="sede_man.php">Sedes</a></li>
        <li class="breadcrumb-item active" aria-current="page">Editar</li>
      </ol>
    </nav>
    <div class="card mb-3">
      <input type="hidden" name="conf">
      <h5 class="card-header">Información General</h5>
      <div class="card-body">
        <input type="hidden" name="id" id="sede_man_id" value="<?php print($_GET["id"]); ?>">
        <div class="row pb-2">
            <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                <div class="input-group-prepend">
                    <span class="input-group-text">Código*</span>
                    <input class="form-control form-control-sm" type="text" name="codigo" id="codigo" value="<?php print($data["codigo"]); ?>" required/>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                <div class="input-group-prepend">
                    <span class="input-group-text">Nombre*</span>
                    <input class="form-control form-control-sm" type="text" name="nombre" id="nombre" value="<?php print($data["nombre"]); ?>" required/>
                </div>
            </div>
        </div>
        <div class="row pb-2">
          <!-- agregar -->
          <div class="col-12 col-sm-12 col-md-12 col-lg-2 pt-2 d-flex align-items-end">
              <input class="form-control btn btn-danger btn-sm" type="Submit" name="actualizar" id="actualizar" value="Actualizar"/>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="js/jquery-1.11.1.min.js"></script>
  <script src="js/chosen.jquery.min.js"></script>
  <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
  <script src="js/sede_man.js?v=1"></script>
</body>
</html>