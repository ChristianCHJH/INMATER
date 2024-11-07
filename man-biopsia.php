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
	<title>Clínica Inmater | Reporte PostVenta</title>
	<script src="js/jquery-1.11.1.min.js"></script>
	<script src="js/chosen.jquery.min.js"></script>
</head>
<body>
	<div class="loader"><img src="_images/load.gif" alt="Inmater Loading"><label>Cargando...</label></div>
	<div class="box container">
		<div>
			<nav aria-label="breadcrumb">
				<a class="breadcrumb" id="close" href="javascript:void(0)" style="background-color: #72a2aa;">
					<img src="_libraries/open-iconic/svg/x.svg" height="18" width="18" alt="icon name">
				</a>
			</nav>
			<div class="alert alert-success alert-dismissible" role="alert" id="alert-success" style="display:none;">
  			<b>Excelente!</b> Agregamos correctamente un Tipo de Biopsia.
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
    			<span aria-hidden="true">&times;</span>
  			</button>
			</div>
			<div class="alert alert-success alert-dismissible" role="alert" id="alert-deleted" style="display:none;">
  			<b>Excelente!</b> Eliminamos correctamente un Tipo de Biopsia.
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
    			<span aria-hidden="true">&times;</span>
  			</button>
			</div>
			<div class="card mb-3">
				<h5 class="card-header" aria-expanded="true"><small><b>Nuevo</b></small></h5>
				<div class="card-body collapse show">
					<form id="form-confirm-add">
						<div class="row pb-2">
							<div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
								<div class="input-group-prepend">
									<span class="input-group-text">Nombre</span>
									<input class="form-control form-control-sm" name="nombre" type="text" required>
								</div>
							</div>
							<div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
								<input type="submit" class="btn btn-sm btn-danger" name="agregar" value="Agregar"/>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="card row content">
			<h5 class="card-header"><small><b>Mantenimiento de Biopsia</b></small></h5>
			<div class="card-body">
				<table class="table table-sm table-bordered" id="table_main">
					<thead class="thead-dark">
						<tr>
							<th class="text-center" style="min-width: 10px;">Id</th>
							<th class="text-center">Nombre</th>
							<th class="text-center" style="min-width: 10px;">Opciones</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
		<div class="row footer">@2021 Clínica Inmater</div>
	</div>

	<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="modal-confirm-add">
		<div class="modal-dialog modal-dialog-centered modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Confirmar Nuevo</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<p>¿Está seguro que desea agregar un nuevo Tipo de Biopsia?</p>
				</div>
				<div class="modal-footer">
					<button type="button" id="add" class="btn btn-sm btn-danger">Agregar</button>
					<button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="modal-confirm-delete">
		<div class="modal-dialog modal-dialog-centered modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Confirmar Eliminar</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<p>¿Está seguro que desea eliminar este Tipo de Biopsia?</p>
				</div>
				<div class="modal-footer">
					<button type="button" id="delete" class="btn btn-sm btn-danger">Eliminar</button>
					<button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
				</div>
			</div>
		</div>
	</div>

	<script src="js/bootstrap.v4/bootstrap.min.js" crossorigin="anonymous"></script>
	<script src="js/shared.js"></script>
	<script src="js/man-biopsia.js"></script>
</body>
</html>
