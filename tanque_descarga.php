<?php
session_start();
ini_set("display_errors","1");
error_reporting(E_ALL);
?>
<!DOCTYPE HTML>
<html>
<head>
    <?php
        $login = $_SESSION['login'];
        $dir = $_SERVER['HTTP_HOST'] . substr(dirname(__FILE__), strlen($_SERVER['DOCUMENT_ROOT']));
        if ($_SESSION['role'] <> 2) {
            echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=http://" . $dir . "'>";
        }
        require("_database/db_tools.php");
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="css/global.css" crossorigin="anonymous">
</head>
<body>
    <?php require ('_includes/menu_andrologia.php'); ?>
    <div class="container">
        <nav aria-label="breadcrumb">
            <a class="breadcrumb" href="lista_and.php">
                <img src="_libraries/open-iconic/svg/x.svg" height="18" width="18" alt="icon name">
            </a>

            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="lista.php">Inicio</a></li>
                <li class="breadcrumb-item"><a href="lista_and.php">Andrología</a></li>
                <li class="breadcrumb-item active" aria-current="page">Descarga de Tanque</li>
            </ol>
        </nav>
        <div class="card mb-3">
            <h5 class="card-header" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample"><small><b>Descarga de viales seleccionados</b></small></h5>
            <div class="mx-auto">
                <form action="tanque_descarga_final.php" id="form_tanque_seleccionados" method="post">
                    <table width="100%" class="table table-responsive table-bordered align-middle" style="margin-bottom: 0 !important;" id="items_seleccionados">
                        <thead class="thead-dark">
                            <tr>
                                <th width="30%" class="text-center">Tanque</th>
                                <th width="30%" class="text-center">DNI</th>
                                <th width="40%" class="text-center">Apellidos</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table><br>
                    <div class="text-center"><input type="submit" class="btn btn-danger" value="Descargar las posiciones seleccionadas" id="descargar_tanque" style="display: none;"></div>
                </form><br>
            </div>
            <form action="3" id="form_tanque" method="post">
                <table width="100%" class="table table-responsive table-bordered align-middle" id="detalle_tanque" style="margin-bottom: 0 !important;">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center">Tanque</th>
                            <th class="text-center">Canister</th>
                            <th class="text-center">Varilla</th>
                            <th class="text-center">Vial</th>
                            <th class="text-center">DNI</th>
                            <th class="text-center">Apellidos</th>
                            <th class="text-center">Seleccionar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr id="buscar_tanque">
                            <td><input type="text" class="form-control caracteristica_tanque text-center" name="tanque" id="tanque"></td>
                            <td><input type="text" class="form-control caracteristica_tanque text-center" name="canister" id="canister"></td>
                            <td><input type="text" class="form-control caracteristica_tanque text-center" name="varilla" id="varilla"></td>
                            <td><input type="text" class="form-control caracteristica_tanque text-center" name="vial" id="vial"></td>
                            <td><input type="text" class="form-control caracteristica_tanque text-center" name="dni" id="dni"></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script type="text/javascript" src="js/tanque_descarga.js"></script>
</body>
</html>