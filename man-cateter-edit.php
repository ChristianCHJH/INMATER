<!DOCTYPE html>
<html lang="en">
<head>
<?php
   include 'seguridad_login.php';
    require "_database/database.php"; ?>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" href="_images/favicon.png" type="image/x-icon">
	<link rel="stylesheet" href="css/chosen.min.css">
	<link rel="stylesheet" href="css/bootstrap.v4/bootstrap.min.css" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="css/shared.css">
	<link rel="stylesheet" type="text/css" href="css/global.css">
	<title>Clínica Inmater | Mantenimiento de Cateter</title>
	<script src="js/jquery-1.11.1.min.js"></script>
	<script src="js/chosen.jquery.min.js"></script>
</head>
<body>
	<div class="loader"><img src="_images/load.gif" alt="Inmater Loading"><label>Cargando...</label></div>
	<div class="box container">
		<div>
			<nav aria-label="breadcrumb">
				<a class="breadcrumb" id="close-edit" href="javascript:void(0)" style="background-color: #72a2aa;">
					<img src="_libraries/open-iconic/svg/x.svg" height="18" width="18" alt="icon name">
				</a>
			</nav>
		</div>
		<div class="card row content">
			<h5 class="card-header"><small><b>Editar</b></small></h5>
			<div class="card-body">
				<form id="form-confirm-edit">
					<div class="row pb-2">
						<div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
							<div class="input-group-prepend">
								<span class="input-group-text">Nombre</span>
								<input class="form-control form-control-sm" name="nombre" type="text" required>
							</div>
						</div>
					</div>
					<div class="row pb-2">
						<div class="col-12 col-sm-12 col-md-12 col-lg-12 input-group-sm">
							<input type="submit" class="btn btn-sm btn-danger" name="actualizar" value="Actualizar"/>
						</div>
					</div>
				</form>
			</div>
		</div>
		<div class="row footer">@2021 Clínica Inmater</div>
	</div>

	<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="modal-confirm-edit">
		<div class="modal-dialog modal-dialog-centered modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Confirmar Editar</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<p>¿Está seguro que desea editar este contenido?</p>
				</div>
				<div class="modal-footer">
					<button type="button" id="update" class="btn btn-sm btn-danger">Actualizar</button>
					<button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
				</div>
			</div>
		</div>
	</div>

	<script src="js/bootstrap.v4/bootstrap.min.js" crossorigin="anonymous"></script>
	<script src="js/shared.js"></script>
	<script src="js/man-cateter.js"></script>
</body>
</html>
