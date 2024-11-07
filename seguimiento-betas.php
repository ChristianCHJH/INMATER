<!DOCTYPE html>
<html lang="es">

<head>
    <?php
   include 'seguridad_login.php'
    ?>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/chosen.min.css">
    <link rel="stylesheet" href="css/bootstrap.v4/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/shared.css">
    <link rel="stylesheet" type="text/css" href="css/global.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap-inmater.css">
    <title>Inmater Clínica de Fertilidad | Seguimiento de Betas positivas</title>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/chosen.jquery.min.js"></script>
</head>

<body>
    <div class="box container">
        <?php require ('componentes/menu/laboratorio.php'); ?>
        <div>
            <div class="alert alert-success alert-dismissible" role="alert" id="alert-success" style="display:none;">
                <label></label>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="alert alert-danger alert-dismissible" role="alert" id="alert-deleted" style="display:none;">
                <label></label>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="card mb-3">
                <h5 class="card-header" aria-expanded="true"><small><b>Seguimiento de Betas positivas</b></small>
                    <small> - <a href="javascript:PrintElem('imprime')" class="ui-btn ui-mini ui-btn-inline"
                            rel="external"><img src="_images/excel.png" height="18" width="18"
                                alt="icon name"></a></small>
                </h5>
                <div class="card-body collapse show">
                    <form id="form-filtros">
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 input-group-sm">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="tipo_fecha"
                                        id="fecha_transferencia" value="1" checked>
                                    <label class="form-check-label" for="fecha_transferencia">F. transferencia</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="tipo_fecha"
                                        id="fecha_posible_parto" value="2">
                                    <label class="form-check-label" for="fecha_posible_parto">F. posible parto</label>
                                </div>
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Mostrar desde</span>
                                    <input class="form-control form-control-sm" name="ini" type="date"
                                        value="<?php print($ini); ?>" id="ini">
                                    <span class="input-group-text">Hasta</span>
                                    <input class="form-control form-control-sm" name="fin" type="date"
                                        value="<?php print($fin); ?>" id="fin">
                                </div>
                            </div>
                        </div>
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                                <input type="submit" form="form-filtros" class="btn btn-sm btn-danger" name="agregar"
                                    value="Buscar" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card row content">
            <div class="col1">
                <div class="loader-content"><img src="_images/load.gif" alt="Inmater Loading"><label>Cargando...</label>
                </div>
                <div class="card-body1">
                    <table class="table table-sm table-hover table-bordered" id="table_main">
                        <thead class="thead-dark row-sticky">
                            <tr>
                                <th class="text-center">Protocolo</th>
                                <th class="text-center" style="min-width: 150px;">Tipo de procedimiento</th>
                                <th class="text-center">F. Transferencia</th>
                                <th class="text-center">F. posible parto</th>
                                <th class="text-center">Medico</th>
                                <th class="text-center">Numero documento</th>
                                <th class="text-center">Paciente</th>
                                <th class="text-center">Celular</th>
                                <th class="text-center">N° casa</th>
                                <th class="text-center">Operaciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row footer">@2022 Clínica Inmater</div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="modal-confirm-add">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Nuevo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro que desea agregar un nuevo contenido?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" id="add" class="btn btn-sm btn-danger">Agregar</button>
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="modal-confirm-delete">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar datos de la Beta</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-6 input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">N° semanas al parto</span>
                            </div>
                            <input class="form-control form-control-sm mostrar" id="semanas_parto" name="semanas_parto"
                                type="number" value="">
                        </div>
                    </div>
                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-6 input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Peso recien nacido</span>
                            </div>
                            <input class="form-control form-control-sm mostrar" id="peso_recien_nacido"
                                name="peso_recien_nacido" type="number" value="">
                        </div>
                    </div>
                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-6 input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">APGAR</span>
                            </div>
                            <input class="form-control form-control-sm mostrar" id="apgar" name="apgar" type="number"
                                value="">
                        </div>
                    </div>
                    <!-- Numero de semana al parto
                    <input type="number" name="" id="">
                    Peso del recien nacido
                    <input type="number" name="" id="">
                    APGAR
                    <input type="number" name="" id=""> -->
                </div>
                <div class="modal-footer">
                    <button type="button" id="delete" class="btn btn-sm btn-danger">Agregar</button>
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="js/bootstrap.v4/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="js/shared.js"></script>
    <script src="js/seguimiento-betas.js?v=3"></script>
</body>

</html>