<!DOCTYPE html>
<html>
<head>
<?php
   include 'seguridad_login.php'
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" media="screen" href="css/dataTables.bootstrap4.min.css" />
    <link rel="stylesheet" href="css/bootstrap.min.css" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="css/global.css">
	<link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <script src="js/jquery-1.12.4.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap4.min.js"></script>
	<script>
        $(document).ready(function() {
            $('#myTable').DataTable();
        } );
	</script>
</head>
<body>
	<?php
		$id = "";
		if (isset($_GET['dni']) && !empty($_GET['dni'])) {
			$id = $_GET['dni'];
		}

	    $between = $ini = $fin = "";
	    if (isset($_POST) && !empty($_POST)) {
	        if ( isset($_POST["ini"]) && !empty($_POST["ini"]) && isset($_POST["fin"]) && !empty($_POST["fin"]) ) {
	            $ini = $_POST['ini'];
	            $fin = $_POST['fin'];
	        } else {
	            // $ini = $fin = date('Y-m-d');
	            $ini = date('Y-m-01');
	            $fin = date('Y-m-t');
	        }
	    } else {
	        // $ini = $fin = date('Y-m-d');
	        $ini = date('Y-m-01');
	        $fin = date('Y-m-t');;
	    }

		$rPaci = $db->prepare("SELECT * FROM hc_antece,hc_paciente WHERE hc_paciente.dni=? AND hc_antece.dni=?");
		$rPaci->execute(array($id, $id));
		$paci = $rPaci->fetch(PDO::FETCH_ASSOC);
		$rAndro = $db->prepare("
			SELECT
			hc_paciente.tip, hc_paciente.dni, hc_paciente.nom, hc_paciente.ape, 
			hc_pareja.p_tip, hc_pareja.p_dni, hc_pareja.p_nom, hc_pareja.p_ape, hc_pare_paci.p_het
			from hc_pareja
			inner join hc_pare_paci on hc_pare_paci.p_dni = hc_pareja.p_dni
			left join hc_paciente on hc_paciente.dni = hc_pare_paci.dni
			order by hc_pareja.p_ape desc, hc_pareja.p_nom desc");
        /* $rAndro = $db->prepare("
        	SELECT
        	hc_pare_paci.p_dni, hc_pare_paci.dni, p_nom, p_ape, hc_pare_paci.p_het
        	from hc_pareja, hc_pare_paci
        	where hc_pareja.p_dni=hc_pare_paci.p_dni
        	order by p_ape, p_nom ASC
        	limit 10 offset 0"); */
        $rAndro->execute();
	?>
	<div class="container">
		<a class="navbar-brand float-right" href="lista.php">
			<img src="_libraries/open-iconic/svg/x.svg" height="18" width="18" alt="icon name">
		</a><br>
		<div data-role="collapsible" id="Perfi">
			<h3>Consulta Andrología</h3>
			<div class="card mb-3" id="imprime">
				<h5 class="card-header" data-toggle="collapse" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">Resultados de la búsqueda: <small><?php print($rAndro->rowCount()." registros encontrados"); ?></small></h5>
				<div class="card-body collapse show" id="collapseExample">
					<table class="table table-responsive table-bordered align-middle" id="myTable">
						<thead class="thead-dark">
							<tr>
								<th class='text-center' width="5%" rowspan="2">Item</th>
                                <th class='text-center' width="50%" colspan="3">Pareja</th>
								<!-- <th class='text-center' width="20%" colspan="2">Documento</th> -->
								<th class='text-center' width="45%" colspan="3">Paciente</th>
								<!-- <th class='text-center' width="20%" colspan="2">Documento</th> -->
							</tr>
							<tr>
								<th class='text-center'>Apellidos y Nombres</th>
								<th class='text-center'>Tipo Documento</th>
								<th class='text-center'>N° Documento</th>
								<th class='text-center'>Apellidos y Nombres</th>
								<th class='text-center'>Tipo Documento</th>
								<th class='text-center'>N° Documento</th>
							</tr>
						</thead>
						<tbody>
                            <?php
                			$item=1;
                            while ($andro = $rAndro->fetch(PDO::FETCH_ASSOC)) { ?>
                                <tr>
                                	<?php print("<td class='text-center'>".$item++."</td>") ?>
                                    <td>
                                    	<a href='<?php echo "e_legal_01.php?andro=" . $andro['p_dni']; ?>' rel="external">
	                                        <?php print( mb_strtoupper($andro['p_ape']) . ' ' . mb_strtoupper($andro['p_nom']) ); ?>
                                		</a>
                                    	<?php if ($andro['p_het'] > 0) print('<span>Donante</span>'); ?>
                                    </td>
									<?php
									print("
									<td class='text-center'>".$andro['p_tip']."</td>
									<td class='text-center'>".$andro['p_dni']."</td>
									<td>".mb_strtoupper($andro['ape']) . ' ' . mb_strtoupper($andro['nom']) . "</td>
									<td class='text-center'>".$andro['tip']."</td>
									<td class='text-center'>".$andro['dni']."</td>
									"); ?>
                                </tr>
                            <?php
                            }
                            ?>
						</tbody>
					</table>
					<?php if ($rAndro->rowCount() < 1) print('<h5>¡Aún no parejas cargadas en el sistema!</h5>'); ?>
				</div>
			</div>
			<br><br>
		</div>
	</div>
</body>
</html>