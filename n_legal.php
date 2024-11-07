<?php
	session_start();
	$id = "";
	if (isset($_GET['dni']) && !empty($_GET['dni'])) {
		$id = $_GET['dni'];
	} else {
		print("No seleccionó a ningún paciente");
		exit();
	}
?>
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
</head>
<body>
	<?php require ('_includes/menu_salaprocedimientos.php'); ?>
	<div class="container">
		<div data-role="collapsible" id="Perfi">
			<?php print("<h4 class='color-primary'>Documentos Legales: <small>".mb_strtoupper($paci['ape'])." ".mb_strtoupper($paci['nom'])."</small></h4>");?>
			<div class="card mb-3">
				<h5 class="card-header">Agregar documentos Legal</h5>
				<div class="card-body collapse show">
                    <?php
					$rUser = $db->prepare("SELECT role, userx from usuario where estado = 1 and userx=?");
					$rUser->execute(array($login));
					$user = $rUser->fetch(PDO::FETCH_ASSOC);
					if ( $user['role'] == 1 ||$user['role'] == 12 || $user['role'] == 15) {
						print('<a href="n_legal_add.php?dni=' . $dni . '" rel="external" class="btn btn-danger">Agregar</a><br><br>');
					}
					$rLegal = $db->prepare("SELECT
						a.*, b.nombre nombretipodocumento
						from hc_legal_01 a
						inner join man_legal_tipodocumento b on b.codigo = a.idlegaltipodocumento
						where a.estado =1 and a.numerodocumento = ?
						order by a.finforme desc");
					$rLegal->execute( array($dni) );
					$i=$rLegal->rowCount();
					$rows = $rLegal->fetchAll(); ?>

					<table class="table table-responsive table-bordered align-middle">
						<thead class="thead-dark">
							<tr>
								<th width="5%" class="text-center">#</th>
								<th width="15%" class="text-center">Fecha Informe</th>
								<th width="35%">Tipo Informe</th>
								<th width="25%">Observación</th>
								<th width="15%" class="text-center">Informe</th>
								<th width="15%" class="text-center">Usuario</th>
								<?php
								if ($user['role'] == 12 || $user['role'] == 15 || (count($rows) != 0 && $user['userx'] == $rows[0]["idusercreate"])) {
									print('<th width="10%">Operaciones</th>');
								} ?>
							</tr>
						</thead>
						<tbody>
							<?php
							$file = "";
							foreach ($rows as $data) {
								$path_file = "legal_01/" . $dni . "/" . $data['nombre']; ?>
								<tr>
									<td class="text-center"><?php print($i--); ?></td>
									<td class="text-center"><?php echo date("d-m-Y", strtotime($data['finforme'])); ?></td>
									<td><?php print( mb_strtoupper($data['nombretipodocumento']) ); ?></td>
									<td><?php echo $data['obs']; ?></td>
									<td class="text-center">
										<?php if (file_exists($path_file)): ?>
											<a href='<?php print("legal_01/" . $dni . "/" . $data['nombre']); ?>' target="_blank">Ver/ Descargar</a>
										<?php endif ?>
									</td>
									<td>
										<?php echo $data['idusercreate'] ?>
									</td>
									<?php
									if ($user['role'] == 12 || $user['role'] == 15 || $user['userx'] == $data['idusercreate']) {
										print("<td class='text-center'><img src='_libraries/open-iconic/svg/trash.svg' height='18' width='18' alt='icon name' class='btn_eliminar_informe' data-origen='legal' data-informe='".$data["id"]."'></td>");
									} ?>
								</tr>
							<?php } ?>
						</tbody>
					</table>

					<?php if ($rLegal->rowCount() < 1) echo '<h5>¡Aún no hay documentos cargados!</h5>'; ?>
				</div>
			</div>

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
							<button type="button" class="btn btn-default" id="modal-btn-no">Cancelar</button>
							<button type="button" class="btn btn-dark" id="modal-btn-si">Confirmar</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script src="js/jquery-3.2.1.slim.min.js" crossorigin="anonymous"></script>
	<script src="js/jquery-1.11.1.min.js"></script>
	<script src="js/popper.min.js" crossorigin="anonymous"></script>
	<script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
	<script src="js/global.js" crossorigin="anonymous"></script>
</body>
</html>