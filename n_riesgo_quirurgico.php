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
		<!-- <a class="navbar-brand float-right" href="javascript:window.close();">
			<img src="_libraries/open-iconic/svg/x.svg" height="18" width="18" alt="icon name">
		</a><br> -->
		<div data-role="collapsible" id="Perfi">
			<?php print("<h5 class='color-primary'>Documento Riesgo Quirúrgico: <small>".mb_strtoupper($paci['ape'])." ".mb_strtoupper($paci['nom'])."</small></h5>");?>
			<div class="card mb-3">
				<h5 class="card-header">Detalle</h5>
				<div class="card-body collapse show">
					<a href="n_riesgo_quirurgico_add.php?dni=<?php echo $dni; ?>" rel="external" class="btn btn-danger">
						<!-- <img src="_libraries/open-iconic/svg/plus.svg" height="18" width="18" alt="icon name"> --> Agregar
					</a><br><br>
                    <?php
					$rLegal = $db->prepare("select * from hc_riesgo_quirurgico where numerodocumento=? and estado=1 order by createdate desc");
					$rLegal->execute(array($dni));
                    if (true) { ?>
						<table class="table table-responsive table-bordered align-middle">
							<thead class="thead-dark">
	                            <tr>
	                            	<th width="15%" class="text-center">Fecha Informe</th>
	                                <th width="35%">Nivel</th>
	                                <th width="25%">Observación</th>
	                                <th width="25%">Informe</th>
									<th width="10%">Operaciones</th>
	                            </tr>
                            </thead>
                            <tbody>
	                            <?php while ($legal = $rLegal->fetch(PDO::FETCH_ASSOC)) { ?>
	                                <tr>
	                                	<td class="text-center"><?php echo date("d-m-Y", strtotime($legal['fvigencia'])); ?></td>
	                                    <td><?php echo $legal['nivel'] ?></td>
	                                    <td><?php echo $legal['obs']; ?></td>
										
										<?php
										$ruta = "riesgo_quirurgico/" . $dni . "/" . $legal['nombre'];

										if (file_exists($ruta)) { ?>
											<td>
												<a href='<?php echo "riesgo_quirurgico/" . $dni . "/" . $legal['nombre']; ?>' target="new">Ver/ Descargar</a>
											</td>
										<?php }else{ ?>
											<td></td>
										<?php } ?>

	                                    <?php
											print("<td class='text-center'><img src='_libraries/open-iconic/svg/trash.svg' height='18' width='18' alt='icon name' class='btn_eliminar_informe' data-origen='rquirurgico' data-informe='".$legal["id"]."'></td>");
										?>
	                                </tr>
	                            <?php } ?>
                            </tbody>
                        </table>
                        <?php if ($rLegal->rowCount() < 1) echo '<h5>¡Aún no hay documentos cargados!</h5>'; ?>
                        <!-- <h3>Legal <span id="ultimo"></span></h3>
                        <script>
                            $(function () {
                                $("#ultimo").html("<?php // echo ($a_sta); ?>");
                            });
                        </script> -->
                    <?php } else echo "<h5>No hay Documentos</h5>"; ?>
				</div>
			</div>
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
	<script src="js/jquery-3.2.1.slim.min.js" crossorigin="anonymous"></script>
	<script src="js/jquery-1.11.1.min.js"></script>
	<script src="js/popper.min.js" crossorigin="anonymous"></script>
	<script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
	<script src="js/global.js" crossorigin="anonymous"></script>
</body>
</html>