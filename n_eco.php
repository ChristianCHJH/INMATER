<!DOCTYPE html>
<html>
<head>
<?php
   include 'seguridad_login.php'
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/global.css">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
	<script src="js/jquery-1.11.1.min.js" crossorigin="anonymous"></script>
	<script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
</head>
<body>
    <?php
    // verificar dni paciente
    if (isset($_GET["dni"]) && !empty($_GET["dni"])) {
        $dni = $_GET["dni"];
    } else {
        print("No existe información");
        exit();
    }
	// 
    $rPaci = $db->prepare("
		select *
		from hc_antece, hc_paciente
        where hc_paciente.dni=? and hc_antece.dni=?");
    $rPaci->execute(array($dni, $dni));
    $paci = $rPaci->fetch(PDO::FETCH_ASSOC);
    ?>
	<?php require ('_includes/menu_medico.php'); ?>
    <div class="container">
		<div class="card mb-3" id="imprime">
			<h5 class="card-header">
				Ecografía en Consultorio: <?php print("<small>".mb_strtoupper($paci['ape'])." ".mb_strtoupper($paci['nom'])."</small>") ?>
			</h5>
			<div class="card-body collapse show" id="collapseExample">
				<a href="n_eco_add.php?dni=<?php echo $dni; ?>" rel="external" class="btn btn-danger">
					<!-- <img src="_libraries/open-iconic/svg/plus.svg" height="18" width="18" alt="icon name"> -->
					Agregar
				</a><br><br>
				<table class="table table-responsive table-bordered align-middle">
					<thead class="thead-dark">
						<tr>
							<th width="5%" class="text-center">Item</th>
							<th width="10%" class="text-center">F. Consulta</th>
							<th width="20%">Informe</th>
							<th width="30%">Ecografías</th>
							<th width="30%">Observación</th>
							<th width="5%">Opciones</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$item=1;
							$path="eco_consultorio/".$dni."/";
							$consulta = $db->prepare("
							select
							id, fconsulta, informe, obs
							from hc_eco_consultorio
							where documento=?
							order by fconsulta desc");
							$consulta->execute(array($dni));
							while ($data = $consulta->fetch(PDO::FETCH_ASSOC)) {
								// datos de informe
								$informe="";
								if ( !empty($data["informe"]) ) {
									$informe=$data["informe"];
								}
								// imagenes de ecografias
								$consulta_ecos = $db->prepare("
								select
								id, nombre
								from hc_eco_consultorio_img
								where id_eco_consultorio=?
								order by createdate desc");
								$consulta_ecos->execute(array($data["id"]));
								$ecos="";
								while ($data_ecos = $consulta_ecos->fetch(PDO::FETCH_ASSOC)) {
									$ecos.="<a href='$path".$data_ecos["nombre"]."' target='_blank'>".$data_ecos["nombre"]."</a><br>";
								}
								print("
								<tr>
									<td class='text-center'>".$item++."</td>
									<td>".$data["fconsulta"]."</td>
									<td><a href='$path".$informe."' target='_blank'>".$informe."</a></td>
									<td>".$ecos."</td>
									<td>".$data["obs"]."</td>
									<td class='text-center'>
										<!-- <a href=''><img src='_libraries/open-iconic/svg/pencil.svg' height='18' width='18' alt='icon name' data-informe='".$data["id"]."'></a> -->
										<img src='_libraries/open-iconic/svg/trash.svg' height='18' width='18' alt='icon name' class='btn_eliminar_informe' data-informe='".$data["id"]."'>
									</td>
								</tr>");
							} ?>
					</tbody>
				</table>
				<?php if ($consulta->rowCount() < 1) echo '<h5>¡Aún no hay consultas cargadas!</h5>'; ?>

				<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" id="eliminar_informe">
					<div class="modal-dialog modal-dialog-centered" role="document">
						<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="exampleModalLongTitle">Confirmar Eliminar</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">¿Realmente desea eliminar el informe?</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal" id="modal-btn-no">Cancelar</button>
							<button type="button" class="btn btn-danger" id="modal-btn-si">Confirmar</button>
						</div>
						</div>
					</div>
				</div>

			</div>
		</div>
    </div>
	<script src="js/global.js" crossorigin="anonymous"></script>
</body>
</html>