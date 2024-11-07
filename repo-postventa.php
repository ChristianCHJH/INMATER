<!DOCTYPE html>
<html lang="en">
<head>
<?php
     include 'seguridad_login.php';
    require "/_database/database.php"; ?>
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
				<a class="breadcrumb" href="lista-marketing.php" style="background-color: #72a2aa;">
					<img src="_libraries/open-iconic/svg/x.svg" height="18" width="18" alt="icon name">
				</a>
			</nav>
			<form action="" method="post" name="form" id="form">
				<div class="card mb-3">
					<h5 class="card-header"><small><b>Reporte PostVenta</b></small></h5>
					<div class="card-body">
						<div class="row pb-2">
							<div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
								<div class="input-group-prepend">
									<span class="input-group-text">Desde</span>
									<input class="form-control form-control-sm" name="ini" type="date" id="ini"style="width: 150px;">
									<span class="input-group-text">hasta</span>
									<input class="form-control form-control-sm" name="fin" type="date" id="fin"style="width: 150px;">
								</div>
							</div>
							<div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
								<div class="input-group-prepend">
									<input type="Submit" class="btn btn-danger" value="Mostrar"/>
									<a href="javascript:void(0)" style="margin: 6px 10px 0;" id="btn_descargar_reporte"><img src="_images/excel.png" height="18" width="18" alt="icon name"></a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
		<div class="card row content">
			<div class="card-body">
				<table class="table table-sm table-bordered" id="table_main">
					<thead class="thead-dark">
						<tr>
							<th class="text-center">F. Consulta</th>
							<th class="text-center" style="min-width: 150px;">Protocolo</th>
							<th class="text-center">FIV</th>
							<th class="text-center">F. Transferencia FIV</th>
							<th class="text-center">TED</th>
							<th class="text-center">F. Transferencia TED</th>
							<th class="text-center">Embriodonación</th>
							<th class="text-center">F. Transferencia Embriodonación</th>
							<th class="text-center">En Fresco</th>
							<th class="text-center">F. Transferencia en Fresco</th>
							<th class="text-center">Crio</th>
							<th class="text-center">N° documento</th>
							<th class="text-center" style="min-width: 300px;">Paciente</th>
							<th class="text-center">F. Nacimiento</th>
							<th class="text-center" style="min-width: 250px;">Sede</th>
							<th class="text-center">Distrito</th>
							<th class="text-center" style="min-width: 400px;">Dirección</th>
							<th class="text-center">N° Personal</th>
							<th class="text-center">N° Casa</th>
							<th class="text-center">N° Oficina</th>
							<th class="text-center">Email</th>
							<th class="text-center" style="min-width: 150px;">F. Ingreso</th>
							<th class="text-center">Médico Beta</th>
							<th class="text-center" style="min-width: 150px;">F. Beta</th>
							<th class="text-center">Resultado Beta</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
		<div class="row footer">@2021 Clínica Inmater</div>
	</div>

	<script src="js/bootstrap.v4/bootstrap.min.js" crossorigin="anonymous"></script>
	<script src="js/shared.js"></script>
	<script src="js/repo-postventa.js"></script>
</body>
</html>
