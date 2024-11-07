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
            print("<META HTTP-EQUIV='Refresh' CONTENT='0; URL=http://" . $dir . "'>");
        } ?>
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
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="lista_and.php">Inicio</a></li>
                <li class="breadcrumb-item">Operaciones</li>
                <li class="breadcrumb-item active" aria-current="page">Descarga de Tanque</li>
            </ol>
        </nav>
        <?php
            if (!!$_POST) { ?>
                <h5 class="card-header" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">Items seleccionados</h5>
                <div class="card mb-3">
                    <div class="card-body">
                        <a href="tanque_descarga.php">volver</a>
                        <form action="_operaciones/tanque_descarga.php" method="post" enctype="multipart/form-data" data-ajax="false" id="tanque_descarga">
                            <input type="hidden" name="tipo" value="guardar_tanque">
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-5 col-lg-5">
                                    <div class="input-group">
                                        <span class="input-group-addon">Informe (PDF)</span>
                                        <input class="form-control" name="informe" type="file" id="informe" accept="application/pdf"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                    <div class="input-group">
                                        <span class="input-group-addon">Observación*</span>
                                        <textarea class="form-control" name="observacion" id="observacion" required></textarea>
                                    </div>
                                </div>
                            </div>
                            <?php
                                foreach ($_POST as $key => $value) {
                                    print('<input type="hidden" name="tanque-'.$key.'" value="'.$key.'">');
                                }
                            ?>
                            <div class="row pb-2">
                                <div class="mx-auto">
                                    <table width="100%" class="table table-responsive table-bordered align-middle" id="detalle_tanque" style="margin-bottom: 0 !important;">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th class="text-center">Tanque</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                            foreach ($_POST as $key => $value) {
                                                print('<tr><td>'.$key.'</td></tr>');
                                            }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="text-center">
                                <input type="submit" class="btn btn-danger" value="Descargar" id="tanque_descarga">
                            </div>
                        </form>
                    </div>
                </div>
        <?php
            } else {
                print("no hay items seleccionados");
            }
        ?>
    </div>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>
</html>