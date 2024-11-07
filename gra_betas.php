<!DOCTYPE html>
<html lang="en">
<head>
<?php
   include 'seguridad_login.php'
    ?>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clínica Inmater | Gráfica de Betas</title>
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/global.css">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">

	<script src="js/jquery-3.2.1.slim.min.js" crossorigin="anonymous"></script>
	<script src="js/jquery-1.11.1.min.js"></script>
    <!-- <script src="js/jquery-3.1.0.min.js"></script> -->
    <script src="chart/dist/Chart.bundle.js"></script>
    <script src="chart/samples/utils.js"></script>

    <style>
        canvas{
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
        }
        html, body {
            height: 100%;
            margin: 0;
        }
        .box {
            display: flex;
            flex-flow: column;
            height: 100%;
        }
        .box .row.header {
            flex: 0 1 auto;
        }
        .box .row.content {
            flex: 1 1 auto;
            overflow-y: scroll;
            margin: 0 0 15px 0;
        }
        .box .row.footer {
            flex: 0 1 40px;
            background-color: #72a2aa;
            padding: 0 1.25rem;
            margin: 0 0 15px 0;
            border-radius: .25rem;
        }
        .breadcrumb {
            background-color: #72a2aa;
        }
        .breadcrumb a{
            color: #000;
        }
    </style>
</head>
<body>
    <div class="loader">
        <div class="demo" style="animation: showloader ease-out 1s forwards;">
            <!-- <img src="_images/logo_login.jpg" alt="" style="padding-bottom: 150px;"> -->
            <img src="_images/load.gif" alt="">
        </div>
    </div>
    <?php
    // filtros
    $between = $beta = $medico = $embriologo_transferencia = $tipo_paciente = $edesde_ovulo = $ehasta_ovulo = $edesde_utero = $ehasta_utero = $ngs = "";
    if (isset($_GET) && !empty($_GET)) {
        if (isset($_GET["beta"]) && $_GET["beta"] != "") {
            $beta = (int)$_GET["beta"];
            if ($beta == 1) {
                $between .= " and lab_aspira_t.beta not in (0, 2)";
            } else {
                $between .= " and lab_aspira_t.beta = $beta";
            }
        }

        if (isset($_GET["ini"]) && !empty($_GET["ini"]) && isset($_GET["fin"]) && !empty($_GET["fin"])) {
            $between .= " and hc_reprod.f_iny between '".$_GET["ini"]."' and '".$_GET["fin"]."'";
        }

        if (isset($_GET["edesde_ovulo"]) && $_GET["edesde_ovulo"] != 0 && isset($_GET["ehasta_ovulo"]) && $_GET["ehasta_ovulo"] != 0) {
            $edesde_ovulo = new DateTime($_GET['edesde_ovulo']);
            $ehasta_ovulo = new DateTime($_GET['ehasta_ovulo']);
        }
        
        if (isset($_GET["edesde_utero"]) && $_GET["edesde_utero"] != 0 && isset($_GET["ehasta_utero"]) && $_GET["ehasta_utero"] != 0) {
            $edesde_utero = new DateTime($_GET['edesde_utero']);
            $ehasta_utero = new DateTime($_GET['ehasta_utero']);
        }        

        if (isset($_GET["tipo_paciente"]) && !empty($_GET["tipo_paciente"])) {
            $tipo_paciente = $_GET['tipo_paciente'];
        }

        if (isset($_GET["ngs"]) and !empty($_GET["ngs"])) {
            $ngs = $_GET["ngs"];
        }

        if (isset($_GET["medico"]) and !empty($_GET["medico"])) {
            $medico = $_GET["medico"];
        } 

        if (isset($_GET["embriologo_transferencia"]) and !empty($_GET["embriologo_transferencia"])) {
            $embriologo_transferencia = $_GET["embriologo_transferencia"];
        }
    }
    ?>

    <div class="box container">
        <div class="row1 header1">
            <nav aria-label="breadcrumb"><a class="breadcrumb" href="lista.php"><img src="_libraries/open-iconic/svg/x.svg" height="18" width="18" alt="icon name"></a></nav>

            <form action="" method="get" name="form2">
                <div class="card mb-3">
                    <h5 class="card-header">Filtros</h5>
                    <div class="card-body">
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Edad del óvulo</span>
                                    <input class="form-control" name="edesde_ovulo" type="number" value="<?php if(isset($_GET['edesde_ovulo'])) echo $_GET['edesde_ovulo']; ?>">
                                    <span class="input-group-addon">menor a</span>
                                    <input class="form-control" name="ehasta_ovulo" type="number" value="<?php if(isset($_GET['ehasta_ovulo'])) echo $_GET['ehasta_ovulo']; ?>">
                                </div>
                            </div>

                            <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Edad del útero</span>
                                    <input class="form-control" name="edesde_utero" type="number" value="<?php if(isset($_GET['edesde_utero']))echo $_GET['edesde_utero']; ?>">
                                    <span class="input-group-addon">menor a</span>
                                    <input class="form-control" name="ehasta_utero" type="number" value="<?php if(isset($_GET['ehasta_utero']))echo $_GET['ehasta_utero']; ?>">
                                </div>
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Tipo paciente</span>
                                    <select class="form-control" name='tipo_paciente'>
                                        <option value=''>TODOS</option>
                                        <option value='P' <?php if($tipo_paciente == "P") print("selected"); ?>>PACIENTE</option>
                                        <option value='R' <?php if($tipo_paciente == "R") print("selected"); ?>>RECEPTORA</option>
                                        <option value='D' <?php if($tipo_paciente == "D") print("selected"); ?>>DONANTE</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Médico</span>
                                    <select class="form-control" name="medico" id="medico">
                                        <option value="">TODOS</option>
                                        <?php print('<option value="' . $login . '" ' . (empty($medico) ? '' : 'selected') . '>' . mb_strtoupper($login) . '</option>'); ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">NGS</span>
                                    <select class="form-control" name='ngs'>
                                        <option value='' >SELECCIONAR</option>
                                        <option value='s' <?php if($ngs == "s") print("selected"); ?>>SI</option>
                                        <option value='n' <?php if($ngs == "n") print("selected"); ?>>NO</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Embriologo Transferencia</span>
                                    <select class="form-control" name="embriologo_transferencia" id="embriologo_transferencia">
                                        <option value="">TODOS</option>
                                        <?php
																					$data_emb = $db->prepare("SELECT id codigo, nom nombre from lab_user order by nom;");
																					$data_emb->execute();
																					while ($info = $data_emb->fetch(PDO::FETCH_ASSOC)) {
																						print("<option value=".$info['codigo'] . ($embriologo_transferencia == $info['codigo'] ? " selected": "") .">".mb_strtoupper($info['nombre'])."</option>");
                                        	} ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                <div class="input-group input-group-sm">
                                    <a href="javascript:void(0)" style="font-size: 14px;" class="font-italic">
                                        <img src="_images/excel.png" height="18" id="descargar_excel" width="18" alt="descargar excel">
                                    </a>&nbsp;<input type="Submit" class="btn btn-sm btn-danger" value="Mostrar"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="card mb-3">
            <!-- <h5 class="card-header">Gráfica</h5> -->
            <div class="card-body mx-auto">
                <div style="width: 800px !important; height: 450px !important;">
                    <canvas id="canvas"></canvas>
                </div>
                <div class="text-center">
                    <table class="table table-responsive table-bordered align-middle" id="table_betas"style="margin: auto;width: 85%;">
                        <thead class="thead-dark">
                            <th class="text-center">Item</th>
                            <th class="text-center" style="min-width: 100px;">2021</th>
                            <th class="text-center" style="min-width: 100px;">2020</th>
                            <th class="text-center" style="min-width: 100px;">2019</th>
                            <th class="text-center" style="min-width: 100px;">2018</th>
                            <th class="text-center" style="min-width: 100px;">2017</th>
                            <th class="text-center" style="min-width: 100px;">2016</th>
                            <th class="text-center" style="min-width: 100px;">2015</th>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row footer"></div>
    </div>

    <script src="js/bootstrap.v4/bootstrap.min.js" crossorigin="anonymous"></script>
    <script type="text/javascript">
        jQuery(window).load(function (event) {
            jQuery('.loader').fadeOut(1000);
        });

        var config = {
            type: 'bar',
            data: {
                labels: [],
                datasets: []
            },
            options: {
                responsive: true,
                title:{
                    display:true,
                    text:'Gráfica: Betas por año'
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
                            labelString: 'Años'
                        }
                    }],
                    yAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Porcentaje'
                        }
                    }]
                }
            }
        };

        var getUrlParameter = function getUrlParameter(sParam) {
            var sPageURL = window.location.search.substring(1),
                sURLVariables = sPageURL.split('&'),
                sParameterName,
                i;

            for (i = 0; i < sURLVariables.length; i++) {
                sParameterName = sURLVariables[i].split('=');

                if (sParameterName[0] === sParam) {
                    return typeof sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
                }
            }
            return false;
        };

        window.myLine = new Chart(document.getElementById("canvas").getContext("2d"), config);

        $.ajax({
            type: 'POST',
            url: '_operaciones/gra-betas.php',
            async: false,
            data: {
                tipo: "cargar_grafica",
                medico: getUrlParameter('medico'),
                embriologo_transferencia: getUrlParameter('embriologo_transferencia'),
                ngs: getUrlParameter('ngs'),
                edesde_ovulo: getUrlParameter('edesde_ovulo'),
                ehasta_ovulo: getUrlParameter('ehasta_ovulo'),
                edesde_utero: getUrlParameter('edesde_utero'),
                ehasta_utero: getUrlParameter('ehasta_utero'),
                tipo_paciente: getUrlParameter('tipo_paciente'),
            },
            dataType: "JSON",
            success: function (result) {
                result.message.total.forEach((element, index) => {
                    var datos = '';
                    element[1].forEach((beta, index2) => {
                        datos += ('<td>' + beta + ' (' +  ((beta * 100 / result.message.total_anio[index2]).toFixed(2)) + '%)</td>');
                    });
                    var demo = '<tr><td class="text-center">' + (index == 0 ? "Pendiente" : (index == 1 ? "Positivo" : (index == 2 ? "Negativo" : (index == 3 ? "Bioquímico" : (index == 4 ? "Aborto" : (index == 5 ? "Anembrionado" : (index == 6 ? "Ectópico" : ""))))))) + '</td>' + datos + '</tr>';
                    $('#table_betas').append(demo);
                    var colorNames = Object.keys(window.chartColors);
                    var colorName = colorNames[config.data.datasets.length % colorNames.length];
                    var newColor = window.chartColors[colorName];
                    var demo2 = [];
                    $.each(element[1], function(index, value) {
                        demo2.push((value* 100 /result.message.total_anio[index]).toFixed(2));
                    });
                    var newDataset = {
                        label: index == 0 ? "Pendiente" : (index == 1 ? "Positivo" : (index == 2 ? "Negativo" : (index == 3 ? "Bioquímico" : (index == 4 ? "Aborto" : (index == 5 ? "Anembrionado" : (index == 6 ? "Ectópico" : "")))))),
                        backgroundColor: newColor,
                        borderColor: newColor,
                        data: demo2,
                        fill: false
                    };
                    config.data.datasets.push(newDataset);
                    config.data.labels = element[0];
                    window.myLine.update();
                });

                var demo3 = '<td>Total</td>';
                result.message.total_anio.forEach(element => {
                    demo3 += '<td>' + element + '</td>';
                });

                $('#table_betas').append(demo3);
            }, error: function (jqXHR, exception) {
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

        $('#descargar_excel').click(function (e) {
            $.ajax({
            type: 'POST',
            url: '_operaciones/gra-betas.php',
            async: false,
            data: {
                tipo: "descargar_excel",
                medico: getUrlParameter('medico'),
                embriologo_transferencia: getUrlParameter('embriologo_transferencia'),
                ngs: getUrlParameter('ngs'),
                edesde_ovulo: getUrlParameter('edesde_ovulo'),
                ehasta_ovulo: getUrlParameter('ehasta_ovulo'),
                edesde_utero: getUrlParameter('edesde_utero'),
                ehasta_utero: getUrlParameter('ehasta_utero'),
                tipo_paciente: getUrlParameter('tipo_paciente'),
            },
            dataType: "JSON",
            success: function (data) {
                var $a = $("<a>");
                $a.attr("href", data.file);
                $("body").append($a);
                $a.attr("download", "reporte-grafica-excel.xlsx");
                $a[0].click();
                $a.remove();
            }, error: function (jqXHR, exception) {
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
        });
    </script>
</body>
</html>