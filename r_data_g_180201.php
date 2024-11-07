<?php session_start(); ?>
<!DOCTYPE HTML>
<html>
<head>
    <?php $login = $_SESSION['login'];
    $dir = $_SERVER['HTTP_HOST'] . substr(dirname(__FILE__), strlen($_SERVER['DOCUMENT_ROOT']));
    if ($_SESSION['role'] <> 2) {
        echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=http://" . $dir . "'>";
    }
    require("_database/db_tools.php"); ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <script src="https://code.jquery.com/jquery-3.1.0.min.js"></script>
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
    <div class="container">
        <div data-role="header">
            <a href="javascript:window.close();">Cerrar</a>
            <h2>Reporte de Aspiraciones</h2>
        </div>
        <?php
        if ($_SESSION['role'] == 2) {
            $med = $anio = $grafico = "";
            //
            if (isset($_GET["med"]) && !empty($_GET["med"])) {
                $med = $_GET['med'];
            }
            if (isset($_GET["anio"]) && !empty($_GET["anio"])) {
                $anio = $_GET['anio'];
            }
            if (isset($_GET["grafico"]) && !empty($_GET["grafico"])) {
                $grafico = $_GET['grafico'];
            }
        ?>
        <div class="card mb-3">
            <h5 class="card-header" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">Filtros</h5>
            <div class="card-body collapse show" id="collapseExample">
                <form action="" method="get" data-ajax="false" id="form1">
                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label for="example-datetime-local-input" class="">Año</label>
                            <select name='anio' id="idanio" class="form-control">
                                <option value='' >Ninguno</option>
                                <option value='2019' <?php if($anio == "2019") print("selected"); ?> >2019</option>
                                <option value='2017' <?php if($anio == "2017") print("selected"); ?> >2017</option>
                                <option value='2016' <?php if($anio == "2016") print("selected"); ?> >2016</option>
                                <option value='2015' <?php if($anio == "2015") print("selected"); ?> >2015</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label for="example-datetime-local-input" class="">Médico</label>
                            <select name='med' id="idmedico" class="form-control">
                                <option value='' >ninguno</option>
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
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                            <label for="example-datetime-local-input" class="">Tipo Gráfica</label>
                            <select name='grafico' id="idgrafico" class="form-control">
                                <option value='line' <?php if($grafico == "line") print("selected"); ?> >Lineal</option>
                                <option value='radar' <?php if($grafico == "radar") print("selected"); ?> >Radar</option>
                                <option value='bar' <?php if($grafico == "bar") print("selected"); ?> >Barras</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2 pt-2 d-flex align-items-end">
                            <input type="Submit" id ="mostrar" class="btn btn-primary" value="Mostrar"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div>
            <canvas id="canvas"></canvas>
        </div>
        <br>
        <br>
        <!-- <button id="randomizeData">Randomize Data</button> -->
        <!-- <button id="addDataset">Add Dataset</button> -->
        <!-- <button id="removeDataset">Remove Dataset</button> -->
        <!-- <button id="addData">Add Data</button> -->
        <!-- <button id="removeData">Remove Data</button> -->
    <?php } ?>
    </div>
    <script>
        var idmedico = document.getElementById("idmedico");
        var idanio = document.getElementById("idanio");
        var idgrafico = document.getElementById("idgrafico");
        //var MONTHS = ["cart"];
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
                    text:'Avance de Aspiraciones'
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
                            labelString: 'Valores'
                        }
                    }]
                }
            }
        };
        //FUNCION PRINCIPAL
        window.onload = function() {
            var ctx = document.getElementById("canvas").getContext("2d");
            window.myLine = new Chart(ctx, config);
        };

        //funcion al darle clic al bottom randomizeData - NO ES NECESARIA QUITALA
        /*document.getElementById('randomizeData').addEventListener('click', function() {
            config.data.datasets.forEach(function(dataset) {
                dataset.data = dataset.data.map(function() {
                    return randomScalingFactor();
                });
            });
            window.myLine.update();
        });*/

        //
        /*var colorNames = Object.keys(window.chartColors);
        var colorName = colorNames[config.data.datasets.length % colorNames.length];
        var newColor = window.chartColors[colorName];
        var newDataset = {
            label: 'Dataset ' + config.data.datasets.length,
            backgroundColor: newColor,
            borderColor: newColor,
            data: [],
            fill: false
        };
        $.post("repo_consulta_aspiracion.php", {data: newDataset.data, idmedico: "mvelit", anio: "2017", mes: "01", tipaspi: "1"}, function (data) {
            // console.log(data);
            newDataset.data.push(data);
            // console.log(newDataset);
        });
        config.data.datasets.push(newDataset);
        // window.myLine.update();*/
        //prueba-ini
        var colorNames = Object.keys(window.chartColors);
        $.post("repo_consulta_aspiracion_01.php", {idmedico: idmedico.value, anio: idanio.value, tipaspi: "1"}, function (demo) {
            // console.log(demo);
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
            newDataset.data.push(fields[1]);
            newDataset.data.push(fields[2]);
            newDataset.data.push(fields[3]);
            newDataset.data.push(fields[4]);
            newDataset.data.push(fields[5]);
            newDataset.data.push(fields[6]);
            newDataset.data.push(fields[7]);
            newDataset.data.push(fields[8]);
            newDataset.data.push(fields[9]);
            newDataset.data.push(fields[10]);
            newDataset.data.push(fields[11]);
            newDataset.data.push(fields[12]);
            newDataset.data.push(fields[13]);
            config.data.datasets.push(newDataset);
            window.myLine.update();
        });
        $.post("repo_consulta_aspiracion_01.php", {idmedico: idmedico.value, anio: idanio.value, tipaspi: "2"}, function (demo) {
            var colorName = colorNames[config.data.datasets.length % colorNames.length];
            var newColor = window.chartColors[colorName];
            var newDataset = {
                label: 'Donante',
                backgroundColor: newColor,
                borderColor: newColor,
                data: [],
                fill: false
            };
            var fields = demo.split("|");
            newDataset.data.push(fields[1]);
            newDataset.data.push(fields[2]);
            newDataset.data.push(fields[3]);
            newDataset.data.push(fields[4]);
            newDataset.data.push(fields[5]);
            newDataset.data.push(fields[6]);
            newDataset.data.push(fields[7]);
            newDataset.data.push(fields[8]);
            newDataset.data.push(fields[9]);
            newDataset.data.push(fields[10]);
            newDataset.data.push(fields[11]);
            newDataset.data.push(fields[12]);
            newDataset.data.push(fields[13]);
            config.data.datasets.push(newDataset);
            window.myLine.update();
        });
        $.post("repo_consulta_aspiracion_01.php", {idmedico: idmedico.value, anio: idanio.value, tipaspi: "4"}, function (demo) {
            var colorName = colorNames[config.data.datasets.length % colorNames.length];
            var newColor = window.chartColors[colorName];
            var newDataset = {
                label: 'Crio ovos Paciente',
                backgroundColor: newColor,
                borderColor: newColor,
                data: [],
                fill: false
            };
            var fields = demo.split("|");
            newDataset.data.push(fields[1]);
            newDataset.data.push(fields[2]);
            newDataset.data.push(fields[3]);
            newDataset.data.push(fields[4]);
            newDataset.data.push(fields[5]);
            newDataset.data.push(fields[6]);
            newDataset.data.push(fields[7]);
            newDataset.data.push(fields[8]);
            newDataset.data.push(fields[9]);
            newDataset.data.push(fields[10]);
            newDataset.data.push(fields[11]);
            newDataset.data.push(fields[12]);
            newDataset.data.push(fields[13]);
            config.data.datasets.push(newDataset);
            window.myLine.update();
        });
        $.post("repo_consulta_aspiracion_01.php", {idmedico: idmedico.value, anio: idanio.value, tipaspi: "5"}, function (demo) {
            var colorName = colorNames[config.data.datasets.length % colorNames.length];
            var newColor = window.chartColors[colorName];
            var newDataset = {
                label: 'Crio Ovo Donantes',
                backgroundColor: newColor,
                borderColor: newColor,
                data: [],
                fill: false
            };
            var fields = demo.split("|");
            newDataset.data.push(fields[1]);
            newDataset.data.push(fields[2]);
            newDataset.data.push(fields[3]);
            newDataset.data.push(fields[4]);
            newDataset.data.push(fields[5]);
            newDataset.data.push(fields[6]);
            newDataset.data.push(fields[7]);
            newDataset.data.push(fields[8]);
            newDataset.data.push(fields[9]);
            newDataset.data.push(fields[10]);
            newDataset.data.push(fields[11]);
            newDataset.data.push(fields[12]);
            newDataset.data.push(fields[13]);
            config.data.datasets.push(newDataset);
            window.myLine.update();
        });
        //prueba-fin
        document.getElementById('idmedico').addEventListener('click', function() {
            var idmedico = document.getElementById("idmedico");
            // console.log(idmedico.value);
        });
        //funcion al darle clic al bottom addDataSet - NO ES NECESARIA QUITALA
        /*
        var colorNames = Object.keys(window.chartColors);
        document.getElementById('addDataset').addEventListener('click', function() {
            // console.log(demo);
            var colorName = colorNames[config.data.datasets.length % colorNames.length];
            var newColor = window.chartColors[colorName];
            var newDataset = {
                label: 'Dataset ' + config.data.datasets.length,
                backgroundColor: newColor,
                borderColor: newColor,
                data: [],
                fill: false
            };

            for (var index = 0; index < config.data.labels.length; ++index) {
                newDataset.data.push(randomScalingFactor());
            }
            // console.log(newDataset.data);
            config.data.datasets.push(newDataset);
            window.myLine.update();
        });*/

        //funcion al darle clic al bottom addData - NO ES NECESARIA QUITALA
        /*document.getElementById('addData').addEventListener('click', function() {
            if (config.data.datasets.length > 0) {
                var month = MONTHS[config.data.labels.length % MONTHS.length];
                config.data.labels.push(month);

                config.data.datasets.forEach(function(dataset) {
                    dataset.data.push(randomScalingFactor());
                });

                window.myLine.update();
            }
        });*/

        //funcion al darle clic al bottom removeDataset - NO ES NECESARIA QUITALA
        /*document.getElementById('removeDataset').addEventListener('click', function() {
            config.data.datasets.splice(0, 1);
            window.myLine.update();
        });*/

        //funcion al darle clic al bottom removeData - NO ES NECESARIA QUITALA
        /*document.getElementById('removeData').addEventListener('click', function() {
            config.data.labels.splice(-1, 1); // remove the label first

            config.data.datasets.forEach(function(dataset, datasetIndex) {
                dataset.data.pop();
            });

            window.myLine.update();
        });*/
    </script>
    <script src="js/jquery-3.2.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="js/popper.min.js" crossorigin="anonymous"></script>
    <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>
</html>