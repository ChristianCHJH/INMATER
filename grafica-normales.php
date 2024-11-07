<!DOCTYPE HTML>
<html>
<head>
<?php
   include 'seguridad_login.php';
   require("_database/database_log.php");
   require("_database/database.php");
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/chosen.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
    <link rel="stylesheet" href="css/bootstrap.v4/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="css/global.css" crossorigin="anonymous">

    <style>
        canvas {
        -moz-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
        }
    </style>
</head>
<body>
    <div class="loader">
        <img src="_images/load.gif" alt="">			
    </div>

    <?php require ('_includes/repolab_menu.php'); ?>

    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="lista_adminlab.php">Inicio</a></li>
                <li class="breadcrumb-item">Gráficas</li>
                <li class="breadcrumb-item active" aria-current="page">Tasa de Normalidad vs Edad del Óvulo</li>
            </ol>
        </nav>

        <?php
        // iniciar variables
        $anio = $grafica_tipo = "";
        //
        if (!!$_POST) {
            if (isset($_POST["anio"]) && !empty($_POST["anio"])) {
                $anio = $_POST['anio'];
            } else {
                $anio = date("Y");
            }

            if (isset($_POST["grafica_tipo"]) && !empty($_POST["grafica_tipo"])) {
                $grafica_tipo = $_POST['grafica_tipo'];
            } else {
                $grafica_tipo = 'line';
            }
        } else {
            $anio = date("Y");
            $grafica_tipo = 'line';
        } ?>

        <div class="card mb-3">
            <input type="hidden" name="conf">
            <h5 class="card-header"><b>Filtros</b></h5>

            <div class="card-body">
                <form action="" method="post" data-ajax="false" name="form2">
                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-2 col-lg-2 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Año</span>
                                <select class="form-control form-control-sm" name="anio" id="anio">
                                    <option value="-">Todos</option>
                                    <option value="2015" <?php if($anio == "2015") print("selected"); ?>>2015</option>
                                    <option value="2016" <?php if($anio == "2016") print("selected"); ?>>2016</option>
                                    <option value="2017" <?php if($anio == "2017") print("selected"); ?>>2017</option>
                                    <option value="2018" <?php if($anio == "2018") print("selected"); ?>>2018</option>
                                    <option value="2019" <?php if($anio == "2019") print("selected"); ?>>2019</option>
                                    <option value="2020" <?php if($anio == "2020") print("selected"); ?>>2020</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Tipo Gráfica</span>
                                <select class="form-control form-control-sm" name="grafica_tipo" id="grafica_tipo">
                                    <option value='line' <?php if($grafica_tipo == "line") print("selected"); ?>>Lineal</option>
                                    <option value='radar' <?php if($grafica_tipo == "radar") print("selected"); ?>>Radar</option>
                                    <option value='bar' <?php if($grafica_tipo == "bar") print("selected"); ?>>Barras</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-sm-12 col-md-4 col-lg-2 input-group-sm">
                            <input class="form-control btn btn-danger btn-sm" type="Submit" name="agregar" value="Buscar"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mb-3">
            <h5 class="card-header" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">
                <?php print('<small><b>Fecha y Hora: </b>' . date("Y-m-d H:i:s"). '</small>'); ?>
                <form method="post" action="_operaciones/grafica-normales.php" target="_blank">
                    <input type="hidden" name="tipo" value="descargar_base">
                    <input type="hidden" name="anio_consulta" id="anio_consulta">
                    <a href="javascript:void(0)" onclick="this.closest('form').submit(); return false;"  style="font-size: 14px;" class="font-italic">Descargar Datos</a>
                </form>
            </h5>

            <div class="card-body">
                <canvas id="canvas"></canvas>
            </div>
        </div>
  </div>

    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.v4/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="chart/dist/Chart.bundle.js"></script>
    <script src="chart/samples/utils.js"></script>

    <script>
        jQuery(window).load(function (event) {
            jQuery('.loader').fadeOut(1000);
        });

        var grafica_tipo = document.getElementById("grafica_tipo");
        document.getElementById('anio_consulta').value=document.getElementById("anio").value;

        var config = {
            type: grafica_tipo.value,
            data: {
                labels: [],
                datasets: []
            },
            options: {
                responsive: true,
                title:{
                    display:true,
                    text:'Gráfica: Tasa de Normalidad vs Edad del Óvulo'
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
                            labelString: 'Edad del Óvulo(años)'
                        }
                    }],
                    yAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Tasa de Normalidad(%)'
                        }
                    }]
                }
            }
        };

        window.myLine = new Chart(document.getElementById("canvas").getContext("2d"), config);

        $.ajax({
            type: 'POST',
            url: '_operaciones/grafica-normales.php',
            async: false,
            data: {
                tipo: "cargar_grafica",
                anio: document.getElementById("anio").value
            },
            dataType: "JSON",
            success: function (result) {
                console.log(result.message);
                var newDataset = {
                    label: 'Curva',
                    backgroundColor: "purple",
                    borderColor: "purple",
                    data: result.message.data,
                    fill: false
                };

                config.data.datasets.push(newDataset);
                config.data.labels = result.message.labels;
                window.myLine.update();
            },
            error: function (jqXHR, exception) {
                var msg = '';
                console.log(jqXHR);
                console.log(exception);

                if (jqXHR.status === 0) {
                    msg = 'Not connect.\n Verify Network.';
                } else if (jqXHR.status == 404) {
                    msg = 'Requested page not found. [404]';
                } else if (jqXHR.status == 500) {
                    msg = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    msg = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    msg = 'Time out error.';
                } else if (exception === 'abort') {
                    msg = 'Ajax request aborted.';
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
            },
        });
    </script>
</body>
</html>