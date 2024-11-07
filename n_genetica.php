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
	<?php
		$id = "";
		if (isset($_GET['dni']) && !empty($_GET['dni'])) {
			$id = $_GET['dni'];
		}
		$consulta = $db->prepare("select * from hc_antece, hc_paciente WHERE hc_paciente.dni=? AND hc_antece.dni=?");
		$consulta->execute(array($id, $id));
		$paci = $consulta->fetch(PDO::FETCH_ASSOC);
    ?>
    <?php require ('_includes/menu_medico.php'); ?>
	<div class="container">
		<a class="navbar-brand float-right" href="javascript:window.close();">
			<img src="_libraries/open-iconic/svg/x.svg" height="18" width="18" alt="icon name">
		</a><br>
		<div data-role="collapsible" id="Perfi">
			<?php print("<h4 class='color-primary'>Informes Genética: <small>".mb_strtoupper($paci['ape'])." ".mb_strtoupper($paci['nom'])."</small></h4>");?>
			<div class="card mb-3" id="imprime">
				<h5 class="card-header" data-toggle="collapse" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">Detalle</h5>
				<div class="card-body collapse show" id="collapseExample">
                    <?php
                        $contenido = file_get_contents('http://inmater.pe/inmater.intranet/public/listas/genetica/'.$id);
                        if ( isset($contenido) ) {
                            if ( strpos($contenido, "NO EXISTE INFORME") === FALSE ) {
                                print('
                                <div class="row pb-2">
                                    <div class="col-12 col-sm-12 col-md-3 col-lg-3">
                                        <div class="input-group">
                                            <span class="input-group-addon">Ver Informes</span>
                                            <label for="" class="form-control">
                                                <a href="http://inmater.pe/inmater.intranet/public/listas/genetica/'.$id.'" target="_blank">
                                                    <img src="_libraries/open-iconic/svg/magnifying-glass.svg" height="28" width="28" alt="icon name">
                                                </a>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                ');
                            } else {
                                print('<h5>¡Aún no hay informes cargados!</h5>');
                            }
                        }
                    ?>
				</div>
			</div>
		</div>
	</div>
</body>
</html>