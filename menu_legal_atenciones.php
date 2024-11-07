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
        $rLegal = $db->prepare("
        	SELECT *
        	from hc_legal
        	where a_mue between ? and ?
        	order by a_mue desc");
        $rLegal->execute(array( $ini, $fin ));
	?>
	<div class="container">
		<a class="navbar-brand float-right" href="lista.php">
			<img src="_libraries/open-iconic/svg/x.svg" height="18" width="18" alt="icon name">
		</a><br>
		<div data-role="collapsible" id="Perfi">
			<h3>Consulta Atenciones Legales</h3>
	        <div class="card mb-3">
	            <h5 class="card-header">Filtros</h5>
	            <div class="card-body">
	                <form action="" method="post" data-ajax="false" id="form1">
	                    <div class="row pb-2">
	                        <!-- mostrar desde hasta -->
	                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
	                            <label for="example-datetime-local-input" class="">Mostrar Desde</label>
	                            <div>
	                                <input class="form-control" name="ini" type="date" value="<?php print($ini); ?>" id="example-datetime-local-input">
	                            </div>
	                        </div>
	                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
	                            <label for="example-datetime-local-input" class="">Hasta</label>
	                            <div>
	                                <input class="form-control" name="fin" type="date" value="<?php print($fin); ?>" id="example-datetime-local-input">
	                            </div>
	                        </div>
	                        <div class="col-12 col-sm-12 col-md-12 col-lg-2 pt-2 d-flex align-items-end">
	                            <input type="Submit" class="btn btn-danger" name="Mostrar" value="Mostrar"/>
	                        </div>
	                    </div>
	                </form>
	            </div>
	        </div>
			<div class="card mb-3" id="imprime">
				<h5 class="card-header" data-toggle="collapse" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">Resultados de la búsqueda: <small><?php print($rLegal->rowCount()." registros encontrados"); ?></small></h5>
				<div class="card-body collapse show" id="collapseExample">
					<input type="text" class="form-control" id="myInput" onkeyup="myFunction()" placeholder="Buscar apellidos y nombres.." title="buscar apellidos y nombres">
					<table class="table table-responsive table-bordered align-middle" id="myTable">
						<thead class="thead-dark">
							<tr>
								<th width="5%">Item</th>
                                <th width="35%">Documento</th>
                                <th width="20%">Apellidos y Nombres</th>
                                <th width="10%">Médico</th>
                                <th width="10%">Resultado</th>
                                <th width="10%">Informe</th>
                                <th width="10%">Fecha</th>
							</tr>
						</thead>
						<tbody>
                            <?php
                            $item=1;
                            while ($lega = $rLegal->fetch(PDO::FETCH_ASSOC)) { ?>
                                <tr>
                                	<?php print("<td class='text-center'>".$item++."</td>") ?>
                                    <td>
                                    	<a href='<?php echo "e_legal_01.php?id=" . $lega['id']; ?>' rel="external"><?php print(mb_strtoupper($lega['a_exa'])); ?></a>
                                    </td>
									<?php print("
										<td>".mb_strtoupper($lega['a_nom'])."</td>
										<td>".$lega['a_med']."</td>"); ?>
                                    <td><?php
                                    	if ($lega['a_sta'] == 0) echo 'ATENDIDO';
                                        if ($lega['a_sta'] == 1) echo 'APTO';
                                        if ($lega['a_sta'] == 2) echo 'OBSERVADO';
                                        if ($lega['a_sta'] == 3) echo 'NO APTO'; ?>
                                    </td>
                                    <td>
                                    	<?php $ruta = 'legal/' . $lega['id'] . '_' . $lega['a_dni'] . '.pdf';
                                        if (file_exists($ruta)) { ?>
                                            <a href='<?php echo "archivos_hcpacientes.php?idLegal=" . $lega['id'] . "_" . $lega['a_dni'] . ".pdf"; ?>' target="new">Ver/Descargar</a>
                                            <?php if ($lega['fec_doc'] <> '1899-12-30') echo '<br>' . date("d-m-Y", strtotime($lega['fec_doc']));
                                        } else echo '-'; ?>
                                    </td>
                                    <td><?php echo date("d-m-Y", strtotime($lega['a_mue'])); ?></td>
                                </tr>
                            <?php
                            } ?>
						</tbody>
					</table>
					<?php if ($rLegal->rowCount() < 1) print('<h5>¡Aún no hay exámenes cargados!</h5>'); ?>
				</div>
			</div>
			<br><br>
		</div>
	</div>
	<script type="text/javascript">
        function myFunction() {
          var input, filter, table, tr, td, i;
          input = document.getElementById("myInput");
          filter = input.value.toUpperCase();
          table = document.getElementById("myTable");
          tr = table.getElementsByTagName("tr");
          for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[1];
            if (td) {
              if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
              } else {
                tr[i].style.display = "none";
              }
            }       
          }
        }
	</script>
</body>
</html>