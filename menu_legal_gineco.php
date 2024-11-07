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
        $rGine = $db->prepare("
        	SELECT hc_paciente.dni, ape, nom, hc_gineco.id, hc_gineco.med, hc_gineco.repro, hc_gineco.fec
        	from hc_paciente, hc_gineco
        	where hc_paciente.dni=hc_gineco.dni and hc_gineco.repro<>'' and hc_gineco.repro<>'NINGUNA' and hc_gineco.legal=0 and hc_gineco.fec between ? and ?
        	order by hc_gineco.fec desc");
		$rGine->execute(array($ini, $fin));
	?>
	<div class="container">
		<a class="navbar-brand float-right" href="lista.php">
			<img src="_libraries/open-iconic/svg/x.svg" height="18" width="18" alt="icon name">
		</a><br>
		<div data-role="collapsible" id="Perfi">
			<h3>Consulta Ginecología</h3>
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
				<h5 class="card-header" data-toggle="collapse" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">Resultados de la búsqueda: <small><?php print($rGine->rowCount()." registros encontrados"); ?></small></h5>
				<div class="card-body collapse show" id="collapseExample">
					<input type="text" class="form-control" id="myInput" onkeyup="myFunction()" placeholder="Buscar apellidos y nombres.." title="buscar apellidos y nombres">
					<table class="table table-responsive table-bordered align-middle" id="myTable">
						<thead class="thead-dark">
							<tr>
								<th class='text-center' width="5%">Item</th>
								<th class='text-center' width="15%">Consulta ginecológica</th>
                                <th width="50%">Apellidos y Nombres</th>
                                <th width="10%">Médico</th>
                                <th width="20%">Reproducción Asistida</th>
							</tr>
						</thead>
						<tbody>
                            <?php
                			$item=1;
                            while ($gine = $rGine->fetch(PDO::FETCH_ASSOC)) { ?>
                                <tr>
                                	<?php print("<td class='text-center'>".$item++."</td>") ?>
                                	<td class="text-center"><?php echo date("d-m-Y", strtotime($gine['fec'])); ?></td>
                                    <td>
                                    	<a href='<?php echo "e_legal_01.php?gin=" . $gine['id']; ?>' rel="external"><?php print(mb_strtoupper($gine['ape']) . ' ' . mb_strtoupper($gine['nom'])); ?></a>
                                    	<?php // echo $gine['ape'] . ' ' . $gine['nom']; ?>
                                    </td>
                                    <td><?php echo $gine['med']; ?></td>
                                    <td><?php echo $gine['repro']; ?></td>
                                </tr>
                            <?php
                            }
                            ?>
						</tbody>
					</table>
					<?php if ($rGine->rowCount() < 1) echo '<h5>¡Aún no hay exámenes cargados!</h5>'; ?>
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
            td = tr[i].getElementsByTagName("td")[2];
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