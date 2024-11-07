<!DOCTYPE HTML>
<html>
<head>
    <?php
        include 'seguridad_login.php'
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="css/global.css">
    <!-- <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script> -->
    <script src="https://code.jquery.com/jquery-3.1.0.min.js"></script>
    <script src="chart/dist/Chart.bundle.js"></script>
    <script src="chart/samples/utils.js"></script>
    <script type="text/javascript">
        function PrintElem(elem) {
            var data = $(elem).html();
            var mywindow = window.open('', 'Imprimir', 'height=600,width=800');
            mywindow.document.write('<html><head><title>Imprimir</title>');
            mywindow.document.write('<style> @page {margin: 0px 0px 0px 5px;} table {border-collapse: collapse;font-size:10px;} .table-stripe td {border: 1px solid black;} .tablamas2 td {border: 1px solid white;} .mas2 {display: block !important;} .noVer, .ui-table-cell-label {display: none;} a:link {pointer-events: none; cursor: default;}</style>');
            mywindow.document.write("</head><body><p style='align: center'>Reporte Fecundación In Vitro</p>");
            mywindow.document.write(data);
            mywindow.document.write('<script type="text/javascript">window.print();<' + '/script>');
            mywindow.document.write('</body></html>');
            return true;
        }
    </script>
</head>
<body>
	<?php require ('_includes/repolab_menu.php'); ?>
    <div class='container'>
    	<?php
	        $between = $ini = $fin = $inc = "";
	        if (isset($_POST) && !empty($_POST)) {
	            if ( isset($_POST["ini"]) && !empty($_POST["ini"]) && isset($_POST["fin"]) && !empty($_POST["fin"]) ) {
	                $ini = $_POST['ini'];
	                $fin = $_POST['fin'];
	            }
                if (isset($_POST["inc"])) {
                    if ($_POST["inc"] != "") {
                        $inc = $_POST['inc'];
                        $between.= " and userx = '$inc'";
                    }
                }
	        } else {
                $ini = date('Y-m-d');
                $fin = date('Y-m-d', strtotime("+1 day"));
	        	/*$ini = date('Y-m-01');
	        	$fin = date('Y-m-t');*/
	        }
	        $between.=" and createdate between '$ini' and '$fin'";
    	?>
    	<input type="hidden" name="between" value="<?php print($between); ?>" id="between">
		<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLongTitle">Gráfica</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<canvas id="canvas"></canvas>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
						<!-- <button type="button" class="btn btn-primary">Save changes</button> -->
					</div>
				</div>
			</div>
		</div>
        <h4>Reporte Ingresos Usuario</h4>
        <div class="card mb-3">
            <h5 class="card-header" data-toggle="collapse" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">Filtros</h5>
            <div class="card-body collapse show" id="collapseExample">
                <form action="" method="post" data-ajax="false" id="form1">
                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label for="example-datetime-local-input" class="">Mostrar Desde:</label>
                            <div>
                                <input class="form-control" name="ini" type="date" value="<?php echo $ini; ?>">
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label for="example-datetime-local-input" class="">Hasta:</label>
                            <div>
                                <input class="form-control" name="fin" type="date" value="<?php echo $fin; ?>">
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label class="">Usuario</label>
                            <select name='inc' class="form-control">
                                <option value="">todos</option>
                                <?php
                                    $data = $db->prepare("select userx from usuario");
                                    $data->execute();
                                    while ($info = $data->fetch(PDO::FETCH_ASSOC)) {
                                        print("<option value=".$info['userx']);
                                    if ($inc === $info['userx'])
                                        echo " selected";
                                    print(">".$info['userx']."</option>");
                                } ?>
                            </select>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2 pt-2 d-flex align-items-end">
                            <input type="Submit" class="btn btn-danger" name="Mostrar" value="Mostrar"/>&nbsp
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter">Ver Gráfico</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
	<?php
    if ($_SESSION['role'] == "9") {
        $item = 1;
		$consulta1 = $db->prepare("
            select
            id, lower(userx) userx, createdate, date(createdate) fecha, extract(hour from createdate) || ':' || extract(minute from createdate) || ':' || extract(second from createdate) hora
            from usuario_log
            where estado=1 and extract(hour from createdate) < 6$between
            order by date(createdate) desc, extract(hour from createdate) desc, extract(minute from createdate) desc, extract(second from createdate) desc");
        $consulta1->execute();
        //
        $consulta2 = $db->prepare("
            SELECT
                id, LOWER(userx) userx, createdate, date(createdate) fecha,
                to_char(createdate, 'HH24:MI:SS') hora
            FROM usuario_log
            WHERE estado = 1 AND EXTRACT(hour FROM createdate) >= 19$between
            ORDER BY date(createdate) DESC, createdate DESC");
        $consulta2->execute();
        //
        $consulta3 = $db->prepare("
            select
            id, lower(userx) userx, createdate, date(createdate) fecha, to_char(createdate, 'HH24:MI:SS') hora
            from usuario_log
            where estado=1 and extract(hour from createdate) >= 6 and extract(hour from createdate) < 19 $between
            order by date(createdate) desc, extract(hour from createdate) desc, extract(minute from createdate) desc, extract(second from createdate) desc");
        $consulta3->execute();
        print("
		<div class='card mb-3'>
			<h5 class='card-header'>
                Detalles: <span style='color: red'>".$consulta1->rowcount()." ingresos 00:00 - 06:00</span>
                , <span style='color: green'>".$consulta3->rowcount()." ingresos 06:00 - 19:00</span>
                , <span style='color: orange'>".$consulta2->rowcount()." ingresos 19:00 - 24:00</span>
            </h5>
	    	<div class='card-body mx-auto'>
		        <table class='table table-responsive table-bordered align-middle'>
		            <thead class='thead-dark'>
		                <tr>
		                    <th class='text-center'>Item</th>
		                    <th class='text-center'>Usuario</th>
		                    <th class='text-center'>Hora de Ingreso</th>
		                </tr>
		            </thead>
		            <tbody>");
        $colorclass="";
        while ($data1 = $consulta1->fetch(PDO::FETCH_ASSOC)) {
        	$colorclass="red";
            print("
        	<tr>
                <td class='text-center' bgcolor='$colorclass'>".$item++."</td>
                <td class='text-center'>".$data1["userx"]."</td>
                <td class='text-center'>".$data1["createdate"]."</td>
            </tr>");
        }
        while ($data2 = $consulta2->fetch(PDO::FETCH_ASSOC)) {
            $colorclass="orange";
            print("
            <tr>
                <td class='text-center' bgcolor='$colorclass'>".$item++."</td>
                <td class='text-center'>".$data2["userx"]."</td>
                <td class='text-center'>".$data2["createdate"]."</td>
            </tr>");
        }
        while ($data3 = $consulta3->fetch(PDO::FETCH_ASSOC)) {
            $colorclass="green";
            print("
            <tr>
                <td class='text-center' bgcolor='$colorclass'>".$item++."</td>
                <td class='text-center'>".$data3["userx"]."</td>
                <td class='text-center'>".$data3["createdate"]."</td>
            </tr>");
        }
        print("</tbody></table></div></div>");
    ?>
        <div class="card mb-3">
            <div style="float:right">
                <p><b>Fecha y Hora de Reporte:</b>
                    <?php
                        date_default_timezone_set('America/Lima');
                        print(date("Y-m-d H:i:s"));
                    ?>
                </p>
            </div>
        </div>
    </div>
<?php } ?>
    <script>
        var idgrafico = document.getElementById("idgrafico");
        var between = document.getElementById("between");
        //var MONTHS = ["cart"];
        var config = {
            type: "bar",
            data: {
                labels: [],
                datasets: []
            },
            options: {
                responsive: true,
                //titulo de la parte superior
                title:{
                    display:true,
                    text:'Gráfica Ingresos Usuario'
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Usuario'
                        }
                    }],
                    yAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'N° Ingresos'
                        }
                    }]
                }
            }
        };
        //funcion principal
        window.onload = function() {
            var ctx = document.getElementById("canvas").getContext("2d");
            window.myLine = new Chart(ctx, config);
        }
        $.post("repo_model_07_consulta.php", {between: between.value, tipaspi: "1"}, function (demo) {
        	console.log(demo);
            var colorNames = Object.keys(window.chartColors);
            var colorName = colorNames[config.data.datasets.length % colorNames.length];
            var newColor = window.chartColors[colorName];
            var newDataset = {
                label: 'Aspiraciones Paciente',
                backgroundColor: newColor,
                borderColor: newColor,
                data: [],
                fill: false
            };
            var fields = demo.split("|");
            var i=1;
            while (i<fields.length) {
                config.data.labels.push(fields[i]);
                i++;
            }
            window.myLine.update();
        });
        $.post("repo_model_07_consulta.php", {between: between.value, tipaspi: "2"}, function (demo) {
        	console.log(demo);
            var colorNames = Object.keys(window.chartColors);
            var colorName = colorNames[config.data.datasets.length % colorNames.length];
            var newColor = window.chartColors[colorName];
            var newDataset = {
                label: 'Ingresos',
                backgroundColor: newColor,
                borderColor: newColor,
                data: [],
                fill: false
            };
            var fields = demo.split("|");
            // console.log(fields.length);
            var i=1;
            while (i<fields.length) {
                newDataset.data.push(fields[i]);
                i++;
            }
            config.data.datasets.push(newDataset);
            window.myLine.update();
        });
    </script>
    <script src="js/jquery-3.2.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="js/popper.min.js" crossorigin="anonymous"></script>
    <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>
</html>