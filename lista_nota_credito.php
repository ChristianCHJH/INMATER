<?php
include 'seguridad_login.php'
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <title>Inmater Clínica de Fertilidad | Lista de Creditos</title>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <script src="js/jquery-3.2.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="js/jquery-1.11.1.min.js"></script>
    <link rel="stylesheet" href="js/datatables.net-bs/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="css/bootstrap.v4/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/repo_conta.css">
</head>

<body style>
    <?php require('_includes/menu_facturacion.php'); ?>


    <section class="container-fluid">
        <h1>Lista de notas de Creditos</h1>
        <div class="card mb-3">
            <div class="card-header"><strong>Filtros</strong></div>
            <div class="card-body">
                <div class="row pb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Paciente</span>
                            <input type="text" class="column_filter" id="paciente" data-index="0">
                        </div>
                        <div class="input-group-prepend">
                            <span class="input-group-text">Médico</span>
                            <input type="text" class="column_filter" id="medico" data-index="1">
                        </div>
                        <div class="input-group-prepend">
                            <span class="input-group-text">N° de Serie</span>
                            <input type="text" class="column_filter" id="serie" data-index="2">
                        </div>
                        <div class="input-group-prepend">
                            <span class="input-group-text">N° del correlativo</span>
                            <input type="text" class="column_filter" id="correlativo" data-index="3">
                        </div>
                </div>
                <div class="row pb-2">
                    <div class="col-12 col-sm-6 col-md-6 col-lg-6 input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Ver desde</span>
                            <input name="ini" id="min" type="date" class="form-control form-control-sm" data-mini="true">
                            <span class="input-group-text">hasta</span>
                            <input name="fin" id="max" type="date" class="form-control form-control-sm" data-mini="true">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">

            <div class="col-auto">
                <table id="tbl_credito" class="table table-striped table-bordered table-responsive" style="width:100%">
                    <thead class="thead-dark">
                        <tr>
                            <th>Nombre del Paciente</th>
                            <th>Medico asignado</th>
                            <th>N° Serie</th>
                            <th>Correlativo</th>
                            <th>Fecha</th>
                            <th>Operaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>

    </section>
   <div class="modal fade nc" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true"
        id="ver_documento_credito">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <!-- <form method="post" name="frm_documento_credito" id="frm_documento_credito" action="#"> -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Nota de Credito</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Tipo</span>
                                    <select name="comprobantetipo_id" id="comprobantetipo_id"
                                        class="form-control form-control-sm">
                                        <option value="">Seleccionar</option>
                                        <option value="3">Nota de crédito</option>
                                        <option value="4">Nota de débito</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Serie</span>
                                    <input name="serie" type="text" class="form-control form-control-sm" id="serie"
                                        data-mini="true">
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Correlativo</span>
                                    <input name="correlativo" type="text" class="form-control form-control-sm"
                                        id="filtro_correlativo" data-mini="true">
                                </div>
                            </div>
                        </div>
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Motivo</span>
                                    <select name="motivotipo_id" id="motivotipo_id"
                                        class="form-control form-control-sm">
                                        <option value="">Seleccionar</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Tipo Documento</span>
                                    <select name="documentotipo_id" id="documentotipo_id"
                                        class="form-control form-control-sm">
                                        <option value="">Seleccionar</option>
                                        <option value="2">No domiciliado</option>
                                        <option value="1">DNI</option>
                                        <option value="3">Carnet Extranjería</option>
                                        <option value="4">RUC</option>
                                        <option value="5">Pasaporte</option>
                                        <option value="6">Cédula Diplomática</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">N° Documento</span>
                                    <input name="numero" type="text" class="form-control form-control-sm" id="numero"
                                        value="" data-mini="true">
                                </div>
                            </div>
                        </div>
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Nombre</span>
                                    <input name="nombre" type="text" class="form-control form-control-sm" id="nombre"
                                        value="" data-mini="true">
                                </div>
                            </div>
                        </div>
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-8 col-lg-6 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Dirección</span>
                                    <input name="direccion" type="text" class="form-control form-control-sm"
                                        id="direccion" value="" data-mini="true">
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-4 col-lg-6 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Correo Electrónico</span>
                                    <input name="correo" type="text" class="form-control form-control-sm" id="correo"
                                        value="" data-mini="true">
                                </div>
                            </div>
                        </div>
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Observaciones</span>
                                    <textarea name="observacion" cols="120" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row pb-2" id="detalle_servicios">
                            <div class="mx-auto">
                                <table class="table table-responsive table-bordered align-middle"
                                    style="margin-bottom: 0 !important; font-size: small;" id="detalle_servicios_tabla">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th class="text-center">Código</th>
                                            <th class="text-center">Cantidad</th>
                                            <th>Descripción</th>
                                            <th class="text-center">Cantidad agregar</th>
                                            <th class="text-center">Precio Unitario</th>
                                            <th class="text-center">Valor de venta</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default modal-btn-no" data-dismiss="modal">Cerrar</button>
                </div>
            </input>
            <!-- </form> -->
        </div>
    </div> 
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="js/datatables.net/jquery.dataTables.min.js"></script>
    <script src="js/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <script src="js/datatables.net/dataTables.buttons.min.js"></script>
    <script src="js/datatables.net/jszip.min.js"></script>
    <script src="js/datatables.net/buttons.html5.min.js"></script>
    <script src="js/nota_credito.js"></script>
</body>

</html>