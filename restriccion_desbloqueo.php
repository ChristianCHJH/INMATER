<!DOCTYPE HTML>
<html>
<head>
<?php
     include 'seguridad_login.php'
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/global.css">
</head>
<body>
    <?php require ('_includes/repolab_menu.php'); ?>
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="lista_adminlab.php" class="no-underline">Inicio</a></li>
                <li class="breadcrumb-item">Administración Laboratorio</li>
                <li class="breadcrumb-item" aria-current="page">Reproducción Asistida</li>
                <li class="breadcrumb-item active">Restricciones desbloqueo</li>
            </ol>
        </nav>
        <div data-role="header">
            <div class="card mb-3">
                <h5 class="card-header" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">Lista procedimientos</h5>
                <input type="text" class="form-control" id="buscar_paciente" placeholder="escribir el nombre de paciente y presione enter">
                <div class="card-body collapse show mx-auto" id="collapseExample">
                    <table width="100%" class="table table-responsive table-bordered align-middle" data-filter="true" data-input="#filtro">
                        <thead class="thead-dark">
                            <tr>
                                <th class="text-center">Item</th>
                                <th class="text-center">Fecha</th>
                                <th>Apellidos y Nombres</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="buscarpaciente_tabla"></tbody>
                    </table>
                    <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" id="modal_bloquear">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">Confirmar</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                </div>
                                <div class="modal-body">¿Realmente desea desbloquear las restricciones?</div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" id="modal-btn-no">Cancelar</button>
                                    <button type="button" class="btn btn-dark" id="modal-btn-si">Confirmar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="js/restriccion_desbloqueo.js"></script>
</body>
</html>