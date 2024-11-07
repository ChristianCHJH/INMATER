<!DOCTYPE HTML>
<html>
<head>
    <?php 
    include 'seguridad_login.php' ?>
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
            <h2>Gráfica Edad vs N° Vitrificación</h2>
        </div>
        <?php
        if ($_SESSION['role'] == "9") {
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
            $between = $ini = $fin = $edesde = $ehasta = "";
            if (isset($_GET) && !empty($_GET)) {
                if ( isset($_GET["edesde"]) && !empty($_GET["edesde"]) && isset($_GET["ehasta"]) && !empty($_GET["ehasta"]) ) {
                    $edesde = $_GET['edesde'];
                    $ehasta = $_GET['ehasta'];
                    $between.=" and datediff(lab_aspira.fec, hc_paciente.fnac) between $edesde and $ehasta";
                }
                if ( isset($_GET["ini"]) && !empty($_GET["ini"]) && isset($_GET["fin"]) && !empty($_GET["fin"]) ) {
                    $ini = $_GET['ini'];
                    $fin = $_GET['fin'];
                    $between.=" and CAST(lab_aspira.fec as date) between '$ini' and '$fin'";
                }
                if (isset($_GET["med"]) && !empty($_GET["med"])) {
                    $med = $_GET['med'];
                    $between.= " and hc_reprod.med = '$med'";
                }
                if (isset($_GET["embins"]) && !empty($_GET["embins"])) {
                    $embins = $_GET['embins'];
                    $between.= " and lab_aspira.emb0 = $embins";
                }
                if (isset($_GET["ovo"]) && !empty($_GET["ovo"])) {
                    $ovo = $_GET['ovo'];
                    $between.= " and lab_aspira.o_ovo ilike '%$ovo%'";
                }
                if (isset($_GET["tipa"]) && !empty($_GET["tipa"])) {
                    $tipa = $_GET['tipa'];
                    $between.= " and lab_aspira.tip = '$tipa'";
                }
            }
        ?>
        <div class="card mb-3">
            <h5 class="card-header" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">Filtros</h5>
            <div class="card-body collapse show" id="collapseExample">
                <form action="" method="get" data-ajax="false" id="form1">
                    <div class="row pb-2">
                        <input type="hidden" name="between" value="<?php print($between); ?>" id="between">
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
    <?php } ?>
    </div>
    <script>
        var idgrafico = document.getElementById("idgrafico");
        var between = document.getElementById("between");
        console.log(between.value);
        //var MONTHS = ["cart"];
        var config = {
            type: idgrafico.value,
            data: {
                //meses en el grafico
                // labels: ["0", "10", "20", "30"],
                labels: [],
                datasets: []
            },
            options: {
                responsive: true,
                //titulo de la parte superior
                title:{
                    display:true,
                    text:'Promedio Óvulos Criopreservados'
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
                            labelString: 'Edades'
                        }
                    }],
                    yAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'N° Óvulos Criopreservados'
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
        $.post("repo_model_01_crio_gra_consulta.php", {between: between.value, tipaspi: "1"}, function (demo) {
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
        $.post("repo_model_01_crio_gra_consulta.php", {between: between.value, tipaspi: "2"}, function (demo) {
            console.log(demo);
            var colorNames = Object.keys(window.chartColors);
            var colorName = colorNames[config.data.datasets.length % colorNames.length];
            var newColor = window.chartColors[colorName];
            var newDataset = {
                label: 'Crio',
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
        //prueba-fin
        /*document.getElementById('idmedico').addEventListener('click', function() {
            var idmedico = document.getElementById("idmedico");
            // console.log(idmedico.value);
        });*/
    </script>
    <script src="js/jquery-3.2.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="js/popper.min.js" crossorigin="anonymous"></script>
    <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>
</html>