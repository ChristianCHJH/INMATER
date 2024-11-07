<!DOCTYPE HTML>
<html>
<head>
    <?php 
   include 'seguridad_login.php' ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.v4/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/global.css">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <script src="js/jquery-3.1.0.min.js"></script>
    <script src="chart/dist/Chart.bundle.js"></script>
    <script src="chart/samples/utils.js"></script>
    <style>
        canvas{
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
        }
    </style>
</head>
<body>
    <?php require ('_includes/repolab_menu.php'); ?>
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="lista_sistemas.php">Inicio</a></li>
                <li class="breadcrumb-item">Adminlab</li>
                <li class="breadcrumb-item">Gráficas</li>
                <li class="breadcrumb-item active" aria-current="page">Tasa blastulación por incubadora</li>
            </ol>
        </nav>
        <?php
        if ($_SESSION['role'] == "9") {
            $med = $anio = $ovo = $tipa = $inc = $idgrafico = $between = "";
            if (isset($_POST) && !empty($_POST)) {
	            if (isset($_POST["anio"]) && !empty($_POST["anio"])) {
	                $anio = $_POST['anio'];
	            }
	            if (isset($_POST["idgrafico"]) && !empty($_POST["idgrafico"])) {
	                $idgrafico = $_POST['idgrafico'];
	            }
                if (isset($_POST["med"]) && !empty($_POST["med"])) {
                    $med = $_POST['med'];
                    $between.= " and hc_reprod.med = '$med'";
                }
                if (isset($_POST["embins"]) && !empty($_POST["embins"])) {
                    $embins = $_POST['embins'];
                    $between.= " and lab_aspira.emb0 = $embins";
                }
                if (isset($_POST["ovo"]) && !empty($_POST["ovo"])) {
                    $ovo = $_POST['ovo'];
                    $between.= " and lab_aspira.o_ovo ilike '%$ovo%'";
                }
                if (isset($_POST["tipa"]) && !empty($_POST["tipa"])) {
                    $tipa = $_POST['tipa'];
                    $between.= " and lab_aspira.tip = '$tipa'";
                }
                if (isset($_POST["inc"]) /*&& !empty($_POST["inc"])*/) {
                    if ($_POST["inc"] != "") {
                        $inc = $_POST['inc'];
                        $between.= " and lab_aspira.inc1 = $inc";
                    }
                }
            } else {
            	$anio = date("Y");
            }
        ?>
        <div class="card mb-3">
            <h5 class="card-header" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample"><b>Filtros</b></h5>
            <div class="card-body collapse show" id="collapseExample">
                <input type="hidden" name="between" value="<?php print($between); ?>" id="between">
                <form action="" method="post" data-ajax="false" id="form1">
                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-4 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Edad cumplida</span>
                                <input class="form-control" name="edesde" type="number" value="<?php echo $_POST['edesde']??''; ?>">
                                <span class="input-group-text">y menor a</span>
                                <input class="form-control" name="ehasta" type="number" value="<?php echo $_POST['ehasta']??''; ?>">
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Médico</span>
                                <select name='med' class="form-control form-control-sm">
                                    <option value='' >Todos</option>
                                    <option value='mvelit' <?php if($med == "mvelit") print("selected"); ?> >mvelit</option>
                                    <option value='eescudero' <?php if($med == "eescudero") print("selected"); ?> >eescudero</option>
                                    <option value='mascenzo' <?php if($med == "mascenzo") print("selected"); ?> >mascenzo</option>
                                    <option value='cbonomini' <?php if($med == "cbonomini") print("selected"); ?> >cbonomini</option>
                                    <option value='tacna' <?php if($med == "tacna") print("selected"); ?> >tacna</option>
                                    <option value='cosorio' <?php if($med == "cosorio") print("selected"); ?> >cosorio</option>
                                    <option value='lab' <?php if($med == "lab") print("selected"); ?> >lab</option>
                                    <option value='rbozzo' <?php if($med == "rbozzo") print("selected"); ?> >rbozzo</option>
                                    <option value='apuertas' <?php if($med == "apuertas") print("selected"); ?> >apuertas</option>
                                    <option value='jolivas' <?php if($med == "jolivas") print("selected"); ?> >jolivas</option>
                                    <option value='humanidad' <?php if($med == "humanidad") print("selected"); ?> >humanidad</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-3 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Emb. Inseminación</span>
                                <select name='embins' class="form-control form-control-sm">
                                    <option value="">todos</option>
                                    <?php
                                        $embinsi="";
                                        $consulta = $db->prepare("SELECT id, nom from lab_user where sta=0");
                                        $consulta->execute();
                                        $consulta->setFetchMode(PDO::FETCH_ASSOC);
                                        $datos = $consulta->fetchAll();
                                        foreach ($datos as $row) {
                                            if ($embins == $row['id']) $embinsi="selected";
                                            else $embinsi="";
                                            print("<option value='".$row['id']."' $embinsi>".strtolower($row['nom'])."</option>");
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-3 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Origen Ovocitos</span>
                                <label for="example-datetime-local-input" class=""></label>
                                <select name='ovo' class="form-control form-control-sm">
                                    <option value='' >todos</option>
                                    <option value='fresco' <?php if($ovo == "fresco") print("selected"); ?> >fresco</option>
                                    <option value='vitrificado' <?php if($ovo == "vitrificado") print("selected"); ?>>vitrificado</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-3 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Tipo Paciente</span>
                                <select name='tipa' class="form-control form-control-sm">
                                    <option value='' >todos</option>
                                    <option value='P' <?php if($tipa == "P") print("selected"); ?>>paciente</option>
                                    <option value='R' <?php if($tipa == "R") print("selected"); ?>>receptora</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-3 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Tipo Gráfica</span>
                                <select name='idgrafico' id="idgrafico" class="form-control form-control-sm">
                                    <option value='line' <?php if($idgrafico == "line") print("selected"); ?> >Lineal</option>
                                    <option value='radar' <?php if($idgrafico == "radar") print("selected"); ?> >Radar</option>
                                    <option value='bar' <?php if($idgrafico == "bar") print("selected"); ?> >Barras</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Incubadora</span>
                                <select name='inc' class="form-control form-control-sm">
                                    <option value="">ninguno</option>
                                    <?php
                                        $data = $db->prepare("SELECT codigo from lab_incubadora");
                                        $data->execute();
                                        while ($info = $data->fetch(PDO::FETCH_ASSOC)) {
                                            print("<option value='".$info['codigo']."'");
                                        if ($inc == $info['codigo'])
                                            echo " selected";
                                        print(">".$info['codigo']."</option>");
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Año</span>
                                <select name='anio' id="anio" class="form-control form-control-sm">
                                    <option value='2019' <?php if($anio == "2020") print("selected"); ?>>2020</option>
                                    <option value='2019' <?php if($anio == "2019") print("selected"); ?>>2019</option>
                                    <option value='2018' <?php if($anio == "2018") print("selected"); ?>>2018</option>
                                    <option value='2017' <?php if($anio == "2017") print("selected"); ?>>2017</option>
                                    <option value='2016' <?php if($anio == "2016") print("selected"); ?>>2016</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2 input-group-sm">
                            <input type="submit" id ="mostrar" class="form-control btn btn-danger btn-sm" value="Mostrar"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
            <a href="#" data-toggle="modal" data-target="#modal_cuadroresumen">Ver Cuadro Resumen</a>
        </div>
        <div class="card mb-3">
            <canvas id="canvas"></canvas>
        </div>
        <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="modal_cuadroresumen">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <input type="hidden" name="recibo_tip" id="recibo_tip">
                    <input type="hidden" name="recibo_id" id="recibo_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Cuadro Resumen</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="card-body">
                            <div class="row pb-2">
                                <div class="mx-auto">
                                    <table class="table table-responsive table-bordered align-middle" style="margin-bottom: 0 !important; font-size: small;" id="table_vercuadroresumen">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th class="text-center"></th>
                                                <th class="text-center">FIV</th>
                                                <th class="text-center"><?php print($_ENV["VAR_ICSI"]); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr><td>Enero</td><td></td><td></td></tr>
                                            <tr><td>Febrero</td><td></td><td></td></tr>
                                            <tr><td>Marzo</td><td></td><td></td></tr>
                                            <tr><td>Abril</td><td></td><td></td></tr>
                                            <tr><td>Mayo</td><td></td><td></td></tr>
                                            <tr><td>Junio</td><td></td><td></td></tr>
                                            <tr><td>Julio</td><td></td><td></td></tr>
                                            <tr><td>Agosto</td><td></td><td></td></tr>
                                            <tr><td>Septiembre</td><td></td><td></td></tr>
                                            <tr><td>Octubre</td><td></td><td></td></tr>
                                            <tr><td>Noviembre</td><td></td><td></td></tr>
                                            <tr><td>Diciembre</td><td></td><td></td></tr>
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
        <br>
    <?php } ?>
    </div>
    <script>
        var idgrafico = document.getElementById("idgrafico");
        var anio = document.getElementById("anio");
        var between = document.getElementById("between");
        //
        var config = {
            type: idgrafico.value,
            data: {
                //meses en el grafico
                labels: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
                datasets: []
            },
            options: {
                responsive: true,
                //titulo de la parte superior
                title:{
                    display:true,
                    text:'Blastulación Total por Meses'
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
                            labelString: 'Meses'
                        }
                    }],
                    yAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Total Blastocistos Global'
                        }
                    }]
                }
            }
        };
        var ctx = document.getElementById("canvas").getContext("2d");
        window.myLine = new Chart(ctx, config);
        $.post("repo_model_04_incu_consulta.php", {anio: anio.value, between: between.value, tiprepo: 1}, function (demo) {
            var colorNames = Object.keys(window.chartColors);
            var colorName = colorNames[config.data.datasets.length % colorNames.length];
            // var newColor = window.chartColors[colorName];
            var newDataset = {
                label: 'Total FIV',
                backgroundColor: "red",
                borderColor: "red",
                data: [],
                fill: false
            };
            var fields = demo.split("|");
            var i=1;
            $('table > tbody  > tr').each(function(index, tr) {
                $(this).find('td').each (function(index, td) {
                    if (index == 1) {
                        $(this).text(fields[i]);
                        i++;
                    }
                });
            });

            var i=1;
            while (i<fields.length) {
                newDataset.data.push(fields[i]);
                i++;
            }
            config.data.datasets.push(newDataset);
            window.myLine.update();
        });
        $.post("repo_model_04_incu_consulta.php", {anio: anio.value, between: between.value, tiprepo: 2}, function (demo) {
            var colorNames = Object.keys(window.chartColors);
            var colorName = colorNames[config.data.datasets.length % colorNames.length];
            var newColor = window.chartColors[colorName];
            var newDataset = {
                label: 'Total ICSI',
                backgroundColor: "green",
                borderColor: "green",
                data: [],
                fill: false
            };
            var fields = demo.split("|");
            var i=1;
            $('table > tbody  > tr').each(function(index, tr) {
                $(this).find('td').each (function(index, td) {
                    if (index == 2) {
                        $(this).text(fields[i]);
                        i++;
                    }
                });
            });
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
    <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="js/popper.min.js" crossorigin="anonymous"></script>
    <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>
</html>