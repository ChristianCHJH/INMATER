<!DOCTYPE HTML>
<html lang="es">
<head>
	
	<?php
     include 'seguridad_login.php';
	 require "/_database/database.php";
    ?>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" href="_images/favicon.png" type="image/x-icon">
	<link rel="stylesheet" href="css/chosen.min.css">
	<link rel="stylesheet" href="css/bootstrap.v4/bootstrap.min.css" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="css/shared.css">
	<link rel="stylesheet" type="text/css" href="css/global.css">
	<title>Clínica Inmater | Reporte Biologos</title>
	<script src="js/jquery-1.11.1.min.js"></script>
	<script src="js/chosen.jquery.min.js"></script>
</head>
<body>
	<div class="box container">
		<?php require ('_includes/repolab_menu.php'); ?>

		<nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="lista_adminlab.php">Inicio</a></li>
        <li class="breadcrumb-item active" aria-current="page">Biólogos</li>
      </ol>
    </nav>

		<div id="header-content">
			<div class="card mb-3">
				<input type="hidden" name="conf">
				<h5 class="card-header"><small><b>Filtros</b></small></h5>
				<div class="card-body">
					<form id="form-filters">
						<div class="row pb-2">
							<div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
								<div class="input-group-prepend">
									<span class="input-group-text">Protocolo</span>
									<input class="form-control form-control-sm" type="text" name="protocolo" />
								</div>
							</div>
						</div>
						<div class="row pb-2">
							<div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
								<div class="input-group-prepend">
									<span class="input-group-text">Desde</span>
									<input class="form-control form-control-sm" type="date" name="ini" value="<?php print($ini); ?> "/>
								</div>
							</div>
							<div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
								<div class="input-group-prepend">
									<span class="input-group-text">Hasta</span>
									<input class="form-control form-control-sm" type="date" name="fin" value="<?php print($fin); ?>" />
								</div>
							</div>
							<div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
								<div class="input-group-prepend">
									<span class="input-group-text">Dia de semana</span>
									<select name="dias_semana" id="dias_semana" class="form-control chosen-select" multiple>
										<option value="">SELECCIONAR</option>
										<?php
										$stmt = $db->prepare("SELECT codigo, nombre from man_dias_semana where estado=1;");
										$stmt->execute();
										while ($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
											print('<option value="'.$item["codigo"].'" '. ($item["codigo"] == 10 ? "selected" : "") .'>' . mb_strtoupper($item["nombre"]) . '</option>');
										} ?>
									</select>
								</div>
							</div>
						</div>
						<div class="row pb-2">
							<div class="col-12 col-sm-12 col-md-2 col-lg-2 input-group-sm">
								<input class="form-control btn btn-danger btn-sm" form="form-filters" type="submit" value="Buscar"/>
							</div>
						</div>
					</form>
				</div>
			</div>

			<h5 class="card-header" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">
				<?php
				print('
					<small>
						<b>Fecha y Hora de Reporte: </b>'.date("Y-m-d H:i:s").'
						<b>, Total Registros: </b>-
						<b>, Resumen: </b>
						<a href="#" data-toggle="modal" data-target="#modal-resumen">
								<img src="_images/grafica_barras.png" height="18" width="18" alt="icon name">
						</a>
						<b>, Resumen x Procedimientos: </b>
						<a href="#" data-toggle="modal" data-target="#modal-resumen-procedimiento">
								<img src="_images/grafica_barras.png" height="18" width="18" alt="icon name">
						</a>
					</small>'); ?>
			</h5>
		</div>

		<div class="card row content">
			<div class="loader-content"><img src="_images/load.gif" alt="Inmater Loading"><label>Cargando...</label></div>
			<div class="card-body">
				<table
					width="100%"
					class="table table-sm table-bordered"
					id="table-main">
					<thead class="thead-dark">
						<tr>
							<th rowspan="2" class="text-center vertical-center">ID</th>
							<th rowspan="2" class="text-center vertical-center"style="min-width: 100px;">Fecha</th>
							<th rowspan="2" class="text-center vertical-center">Protocolo</th>
							<th rowspan="2" class="vertical-center" style="min-width: 200px;">Procedimiento</th>
							<th rowspan="2" class="text-center vertical-center" style="min-width: 80px;">Casos FIV</th>
							<th rowspan="2" class="text-center vertical-center" style="min-width: 80px;">Casos ICSI</th>
							<th rowspan="2" class="text-center vertical-center" style="min-width: 120px;">Ovocito Fresco</th>
							<th rowspan="2" class="text-center vertical-center" style="min-width: 120px;">Ovocito Desvitri</th>
							<th rowspan="2" class="vertical-center" style="min-width: 400px;">Paciente</th>
							<th rowspan="2" class="text-center vertical-center">Aspirados</th>
							<th rowspan="2" class="text-center vertical-center">Atrésicos</th>
							<th rowspan="2" class="text-center vertical-center">Citolizados</th>
							<th rowspan="2" class="text-center vertical-center">Inmaduros</th>
							<th rowspan="2" class="text-center vertical-center">Fecundados</th>
							<th rowspan="2" class="text-center vertical-center">No fecundados</th>
							<th rowspan="2" class="text-center vertical-center">Triploides</th>
							<th colspan="3" class="text-center vertical-center" style="min-width: 150px;">Crio Embriones</th>
							<th colspan="3" class="text-center vertical-center">Transferencias</th>
							<th colspan="3" class="text-center vertical-center">Crio Óvulos</th>
							<th colspan="6" class="text-center vertical-center">NGS</th>
						</tr>
						<tr>
							<th class="text-center vertical-center" style="min-width: 150px;">Embriologo</th>
							<th class="text-center vertical-center">Casos</th>
							<th class="text-center vertical-center">Total</th>
							<th class="text-center vertical-center" style="min-width: 150px;">Embriologo</th>
							<th class="text-center vertical-center">Casos</th>
							<th class="text-center vertical-center">Total</th>
							<th class="text-center vertical-center" style="min-width: 150px;">Embriologo</th>
							<th class="text-center vertical-center">Casos</th>
							<th class="text-center vertical-center">Total</th>
							<th class="text-center vertical-center">Casos</th>
							<th class="text-center vertical-center">Total</th>
							<th class="text-center vertical-center">Normal</th>
							<th class="text-center vertical-center">Anormal</th>
							<th class="text-center vertical-center">NR</th>
							<th class="text-center vertical-center">Mosaico</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>

		<div class="row footer">@2021 Clínica Inmater</div>
  </div>

	<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="modal-resumen">
			<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Resumen</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="card-body">
							<div class="row pb-2">
									<div class="mx-auto table-responsive">
											<table
												class="table table-responsive table-bordered align-middle"
												style="margin-bottom: 0 !important; font-size: small;"
												id="table-modal">
													<thead class="thead-dark">
														<tr>
															<th class="text-center"></th>
															<th class="text-center">Aspi.</th>
															<th class="text-center">Desvitri.</th>
															<th class="text-center">FIV</th>
															<th class="text-center">ICSI</th>
															<th class="text-center">Biopsiados</th>
															<th class="text-center">Total Crio Embriones</th>
															<th class="text-center">Total Trans.</th>
															<th class="text-center">Total Crio Ovos</th>
															<th class="text-center">Total</th>
														</tr>
													</thead>
													<tbody>
													</tbody>
											</table>
									</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
					</div>
				</div>
			</div>
	</div>

	<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="modal-resumen-procedimiento">
			<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Resumen x Procedimientos</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="card-body">
							<div class="row pb-2">
									<div class="mx-auto table-responsive">
											<table
												class="table table-responsive table-bordered align-middle"
												style="margin-bottom: 0 !important; font-size: small;"
												id="table-modal-procedimiento">
													<thead class="thead-dark">
														<tr>
															<th class="text-center"></th>
															<th class="text-center">Aspi.</th>
															<th class="text-center">Desvitri.</th>
															<th class="text-center">FIV</th>
															<th class="text-center">ICSI</th>
															<th class="text-center">Biopsiados</th>
															<th class="text-center">Total Crio Embriones</th>
															<th class="text-center">Total Trans.</th>
															<th class="text-center">Total Crio Ovos</th>
															<th class="text-center">Total</th>
														</tr>
													</thead>
													<tbody>
													</tbody>
											</table>
									</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
					</div>
				</div>
			</div>
	</div>

	<script src="js/bootstrap.v4/bootstrap.min.js" crossorigin="anonymous"></script>
	<script src="js/shared.js"></script>
	<script src="js/chosen.jquery.min.js"></script>
	<script src="js/repo-biologos.js"></script>
	<script>
		$(".chosen-select").chosen();
	</script>
</body>
</html>