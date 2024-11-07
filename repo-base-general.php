<!DOCTYPE HTML>
<html>

<head>
<?php
     include 'seguridad_login.php'
    ?>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">

    <link rel="stylesheet" href="css/chosen.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css"
        integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
    <link rel="stylesheet" href="css/bootstrap.v4/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="css/shared.css?v=0" crossorigin="anonymous">
    <link rel="stylesheet" href="css/global.css?v=1" crossorigin="anonymous">
    <title>Clínica Inmater | Reporte de Base General</title>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/chosen.jquery.min.js"></script>
</head>

<body>
    <div class="box container">
        <?php require ('_includes/repolab_menu.php'); ?>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="lista_adminlab.php">Inicio</a></li>
                <li class="breadcrumb-item">Reportes</li>
                <li class="breadcrumb-item active" aria-current="page">Base General</li>
            </ol>
        </nav>

        <div class="alert alert-success alert-dismissible" role="alert" id="alert-success" style="display:none;">
            <label></label>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="alert alert-danger alert-dismissible" role="alert" id="alert-error" style="display:none;">
            <label></label>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="card mb-3">
            <input type="hidden" name="conf">
            <h5 class="card-header"><small><b>Filtros</b></small></h5>

            <div class="card-body">
                <form id="form-filters">
                    <div class="row pb-2">
                        <div class="input-group input-group-sm col-12 col-sm-12 col-md-6 col-lg-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Protocolo</span>
                                <input class="form-control form-control-sm" id="protocolo" name="protocolo" type="text">
                            </div>
                        </div>

                        <div class="input-group input-group-sm col-12 col-sm-12 col-md-12 col-lg-6">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Mostrar Desde</span>
                            </div>
                            <input class="form-control form-control-sm mostrar" id="fecha_inicio" name="fecha_inicio"
                                type="date">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Hasta</span>
                            </div>
                            <input class="form-control form-control-sm mostrar" id="fecha_fin" name="fecha_fin"
                                type="date">
                        </div>

                    </div>

                    <div class="row pb-2">
                        <div class="input-group-sm col-12 col-sm-12 col-md-12 col-lg-2">
                            <input class="form-control btn btn-danger btn-sm" type="submit" name="agregar"
                                value="Buscar" />
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php print('<small><b>Fecha y Hora de Reporte: </b> '.date("Y-m-d H:i:s").', Descargar:                 <a href="javascript:void(0)" id="btn-descargar-reporte">
                    <img src="_images/excel.png" height="18" width="18" alt="descargar reporte">
                </a></small>'); ?>
        <div class="row content">
            <div class="card-body1">
                <div class="loader-content"><img src="_images/load.gif"><label>Cargando...</label></div>
                <table width="100%" class='table table-sm table-bordered' id="report">
                    <thead class="thead-dark">
                        <tr class="row-sticky">
                            <?php
                            require($_SERVER["DOCUMENT_ROOT"] . "/data/base-general.php");
                            foreach ($base_general_columnas as $value) {
                                print('<th class="text-center align-bottom">'.$value["texto"].'</th>');
                            }
                            print("</tr><tr>");
                            foreach ($base_general_columnas as $value) {
                                print('<th class="text-center align-bottom"><input type="checkbox" value="'.$value["columna"].'" checked></th>');
                            }
                            print("</tr>"); ?>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <div class="row footer">@2022 Clínica Inmater</div>
    </div>

    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.v4/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="js/repo-base-general.js?v=4"></script>
</body>

</html>