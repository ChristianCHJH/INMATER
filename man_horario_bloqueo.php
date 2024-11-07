<!DOCTYPE HTML>
<html>
    <head>
    <?php
   include 'seguridad_login.php'
    ?>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="_images/favicon.png" type="image/x-icon">
        <link rel="stylesheet" href="css/chosen.min.css">
        <link rel="stylesheet" href="css/bootstrap.min.css" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="css/global.css">
    </head>
    <body>
        <?php
        require ('_includes/menu-admin.php');
        require ('_database/db_agenda_bloqueo.php');
        require ('_database/db_hora.php');
        require ('_database/db_turno.php');
        ?>
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="lista_adminlab.php">Inicio</a></li>
                    <li class="breadcrumb-item">Historia Clínica</li>
                    <li class="breadcrumb-item">Reproducción Asistida</li>
                    <li class="breadcrumb-item active" aria-current="page">Bloqueo de Horarios</li>
                </ol>
            </nav>
            <div data-role="header">
                <div class="card mb-3">
                    <h5 class="card-header" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">Nuevo</h5>
                    <div class="card-body collapse show" id="collapseExample">
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Fecha</span>
                                    <input class="form-control"  aria-label="Small" aria-describedby="inputGroup-sizing-sm" id="fecha" type="date" required>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Hora</span>
                                    <select name="tiposervicio" id="hora" class="form-control chosen-select">
                                        <option value="">SELECCIONAR</option>
                                        <?php
                                            foreach (horaTodo() as $row) {
                                                $sel="";
                                                if ($tiposervicio == $row['codigo']) {
                                                    $sel="selected";
                                                }

                                                print("<option value=".$row['codigo']." $sel>".mb_strtoupper($row['nombre'])."</option>");
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-3 col-lg-3 input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Turno (min)</span>
                                    <select name="idturno" id="turno" class="form-control chosen-select">
                                        <option value="">SELECCIONAR</option>
                                        <?php
                                            foreach (turnoTodo() as $data) {
                                                print('<option value="'.$data['id'].'">'.$data['nombre'].'</option>');
                                            } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-3 col-lg-3">
                                <input type="button" class="btn btn-danger" id="agregar" name="agregar" value="Agregar"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-3">
                    <h5 class="card-header" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">Lista</h5>
                    <div class="collapse show mx-auto" id="collapseExample">
                        <table width="100%" class="table table-responsive table-bordered align-middle" style="margin-bottom: 0 !important; font-size: small;" data-filter="true" data-input="#filtro">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Item</th>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Turno (min)</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                        </table>
                        <table width="100%" class="table table-responsive table-bordered align-middle"  style='height: 44vh; font-size: small; margin-bottom: 0 !important;' data-filter="true" data-input="#filtro">
                            <tbody>
                            <?php
                                $i=1;
                                foreach (agendaBloqueoTodo() as $item)
                                {
                                    print('
                                    <tr>
                                        <td width="5%" align="center">'.$i++.'</td>
                                        <td width="20%" class="text-center">'.$item["fecha"].'</td>
                                        <td width="10%" class="text-center">'.$item["hora"].'</td>
                                        <td width="10%" class="text-center">'.$item["turno"].'</td>
                                        <td width="10%" align="center">
                                            <img src="_libraries/open-iconic/svg/trash.svg" height="18" width="18" alt="icon name" class="eliminar" data-id="'.$item["id"].'">
                                        </td>
                                    </tr>');
                                }
                            ?>
                            </tbody>
                        </table>
                        <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" id="modal_eliminar">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLongTitle">Confirmar Eliminar</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    </div>
                                    <div class="modal-body">¿Realmente desea eliminar el registro?</div>
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
        <script src="js/chosen.jquery.min.js"></script>
        <script src="js/man_horario_bloqueo.js?v=18.11.22"></script>
        <script>
            $(".chosen-select").chosen();
        </script>
    </body>
</html>