<?php
	session_start();
    ini_set("display_errors","1");
	error_reporting(E_ALL);
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
		<br>
		<div data-role="collapsible" id="Perfi">
			<?php print("<h4 class='color-primary'>Documentos Psicologia: <small>".mb_strtoupper($paci['ape'])." ".mb_strtoupper($paci['nom'])."</small></h4>");?>
			<div class="card mb-3">
				<h5 class="card-header">Agregar documentos Psicologia</h5>
				<div class="card-body collapse show">
				<?php
					// datos usuario
					$rUser = $db->prepare("select role from usuario where estado = 1 and userx=?");
					$rUser->execute(array($login));
					$user = $rUser->fetch(PDO::FETCH_ASSOC);
					if ($user['role'] == 1 || $user['role'] == 12 || $user['role'] == 13 || $user['role'] == 15) {
						print('<a href="n_psicologia_add.php?dni='. $dni . '" rel="external" class="btn btn-danger">Agregar</a><br><br>');
					}
					$rLegal = $db->prepare("
                    select
                        pd.id, pd.finforme, pd.documento
                        from hc_psicologia_doc pd
                        where pd.estado = 1 and pd.numerodocumento = ?
                        order by pd.finforme desc");
					$rLegal->execute(array($dni));
					$i=$rLegal->rowCount();
                    if (true) { ?>
						<table class="table table-responsive table-bordered align-middle">
							<thead class="thead-dark">
	                            <tr>
									<th width="5%" class="text-center">#</th>
	                            	<th width="10%" class="text-center">Fecha Informe</th>
	                                <th width="15%" class="text-center">Informe</th>
									<?php
									if ($user['role'] == 1 || $user['role'] == 12 || $user['role'] == 13 || $user['role'] == 15) {
										print('<th width="5%" class="text-center">Operaciones</th>');
									} ?>
	                            </tr>
                            </thead>
                            <tbody>
	                            <?php while ($legal = $rLegal->fetch(PDO::FETCH_ASSOC)) { 
									
										$ruta = "psicologia/" . $dni . "/" . $legal['documento'];
										if (file_exists($ruta)) { 
											$enlace = '<td> <a href='.$ruta.' target="new">Ver/ Descargar</a> </td>';
										 }else{ 
											$enlace = "<td></td>";
										 } ?>
										
	                                <tr>
										<td class="text-center"><?php print($i--); ?></td>
	                                	<td class="text-center"><?php print(date("d-m-Y", strtotime($legal['finforme']))); ?></td>
	                                    <?php echo $enlace; ?>
										<td class="text-center">
											<?php
											if ($user['role'] == 1 || $user['role'] == 12 || $user['role'] == 13 || $user['role'] == 15) {
												print("<img src='_libraries/open-iconic/svg/trash.svg' height='18' width='18' alt='icon name' class='btn_eliminar_informe' data-origen='psicologia' data-informe='".$legal["id"]."'>");
											} ?>
										</td>
	                                </tr>
	                            <?php } ?>
                            </tbody>
                        </table>
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
                        <?php if ($rLegal->rowCount() < 1) echo '<h5>¡Aún no hay documentos cargados!</h5>'; ?>
                    <?php } else echo "<h5>No hay Documentos</h5>"; ?>
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