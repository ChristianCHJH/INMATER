<?php
  session_start();
  ini_set("display_errors","1");
  error_reporting(E_ALL);
  $login = $_SESSION['login'];
  $dir = $_SERVER['HTTP_HOST'].substr(dirname(__FILE__), strlen($_SERVER['DOCUMENT_ROOT']));
  if (!$login) { print("<META HTTP-EQUIV='Refresh' CONTENT='0; URL=http://".$dir."'>"); }

  // verificar id
	$historia = 0;
  if (isset($_GET["id"]) && !empty($_GET["id"]) && isset($_GET["historia"]) && !empty($_GET["historia"])) {
    $numero_documento = $_GET["id"];
		$historia = $_GET["historia"];
    require("_database/db_paciente.php");
		if ($historia == 1) {
			$data = traer_paciente($numero_documento);
		} else {
			$data = traer_paciente_andro($numero_documento);
		}
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
  <?php
  if (isset($_POST['numero_documento']) and isset($_POST['boton_datos']) && $_POST['boton_datos'] == 'GUARDAR') {
		try {
			actualizar_paciente($data["dni"], $_POST['numero_documento'], $_POST['apellidos'], $_POST['nombres'], $_POST['sede_id'], $_POST['tipo_paciente_id'], $_POST['condicion_paciente_id'], $login);
		}catch (Exception $e) {
			var_dump($e);
		}
	}
	?>
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="lista_adminlab.php">Inicio</a></li>
        <li class="breadcrumb-item">Mantenimiento</li>
        <li class="breadcrumb-item"><a href="man_paciente.php">Datos Generales</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php print(mb_strtoupper($data["ape"].' '.$data["nom"])); ?></li>
      </ol>
    </nav>
    <div class="card mb-3">
      <?php print('
      <h5 class="card-header">
        <small><b>Editar: </b>'.mb_strtoupper($data["ape"].' '.$data["nom"]).'</small>
      </h5>'); ?>
      <div class="card-body">
		
	  
				<form action="" id="form_paciente" enctype="multipart/form-data" method="post" data-ajax="false" >
					
					<div class="row pb-2">
						<div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
						<div class="input-group-prepend">
							<span class="input-group-text">N° de documento</span>
							<input type="text" class="form-control form-control-sm text-center" name="numero_documento" id="numero_documento" value="<?php echo $numero_documento; ?>">
						</div>
						</div>
						<div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
						<div class="input-group-prepend">
							<span class="input-group-text">Apellidos</span>
							<input type="text" class="form-control form-control-sm"  name="apellidos" value="<?php print(mb_strtoupper($data["ape"])); ?>">
						</div>
						</div>
						<div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
						<div class="input-group-prepend">
							<span class="input-group-text">Nombres</span>
							<input type="text" class="form-control form-control-sm"  name="nombres" value="<?php print(mb_strtoupper($data["nom"])); ?>">
						</div>
						</div>
					</div>
					<div class="row pb-2">
									<?php if ($historia == 1) { ?>
										<div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
											<div class="input-group-prepend">
												<span class="input-group-text">Sede</span>
												<select class="form-control form-control-sm" name="sede_id" id="sede_id">
													<option value="">SELECCIONAR</option>
													<?php
														$consulta = $db->prepare("SELECT * FROM sedes WHERE estado = 1;");
														$consulta->execute();
														$consulta->setFetchMode(PDO::FETCH_ASSOC);
														$data_sede = $consulta->fetchAll();
														foreach ($data_sede as $row) {
															$selected = "";
															if ($data["idsedes"] == $row['id']) { $selected = "selected"; }
															print("<option value=".$row['id']." $selected>".$row['id'].mb_strtoupper($row['nombre'])."</option>");
														} ?>
												</select>
											</div>
										</div>
									<?php } ?>
									<?php if ($historia == 1) { ?>
										<div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
											<div class="input-group-prepend">
												<span class="input-group-text">Tipo Paciente</span>
												<select class="form-control form-control-sm" name="tipo_paciente_id" id="tipo_paciente_id">
													<option value="">SELECCIONAR</option>
													<?php
														$consulta = $db->prepare("SELECT id, abreviatura nombre from man_medios_comunicacion where estado = 1;");
														$consulta->execute();
														$consulta->setFetchMode(PDO::FETCH_ASSOC);
														$data_tipo_paciente = $consulta->fetchAll();
														foreach ($data_tipo_paciente as $row) {
															$selected = "";
															if ($data["medios_comunicacion_id"] == $row['id']) { $selected = "selected"; }
															print("<option value=".$row['id']." $selected>".mb_strtoupper($row['nombre'])."</option>");
														} ?>
												</select>
											</div>
										</div>
									<?php } ?>
									<?php if ($historia == 1) { ?>
										<div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
											<div class="input-group-prepend">
												<span class="input-group-text">Condicion Paciente</span>
												<select class="form-control form-control-sm" name="condicion_paciente_id" id="condicion_paciente_id">
													<option value="">SELECCIONAR</option>
													<option value="P" <?php if ($data['don'] == "P") echo "selected"; ?>>Paciente</option>
													<option value="D" <?php if ($data['don'] == "D") echo "selected"; ?>>Donante</option>
												</select>
											</div>
										</div>
									<?php } ?>
					</div>
					<div class="row pb-2">
						
						<div class="col-12 col-sm-12 col-md-12 text-center">
						<input type="submit" value="GUARDAR" name="boton_datos" data-icon="check" data-iconpos="left"
							data-mini="true" class="btn btn-danger" data-textonly="false" data-textvisible="true"
							data-msgtext="Agregando datos.." data-theme="b" data-inline="true" />
						<a href="man_paciente.php" class="btn btn-secondary">Cancelar</a>
						</div>
					</div>
				</form>
				<!-- modal confirm -->
				<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="modal_editar">
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
	<script src="js/man_paciente.js?v=200118" crossorigin="anonymous"></script>
</body>
</html>