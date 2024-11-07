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
        <?php require ('_includes/menu-admin.php'); ?>
        <div class="container">
            <div data-role="header">
                <h4 class='color-primary'>Configuración: Hora límite de programación</h4>
                <div class="card mb-3">
                    <h5 class="card-header" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">Nuevo</h5>
                    <div class="card-body collapse show" id="collapseExample">
                        <div class="row pb-2">
                            <div class="col-12 col-sm-12 col-md-2 col-lg-2">
                                <div class="input-group">
                                    <span class="input-group-addon">Código</span>
                                    <input class="form-control" id="codigo" type="text" required>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                <div class="input-group">
                                    <span class="input-group-addon">Descripción</span>
                                    <input class="form-control" id="descripcion" type="text" required>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-2 col-lg-2">
                                <div class="input-group">
                                    <span class="input-group-addon">Valor</span>
                                    <input class="form-control" id="valor" type="text" required>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-3 col-lg-3">
                                <input type="button" class="btn btn-danger" id="agregar" name="agregar" value="Agregar" disabled/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-3">
                    <h5 class="card-header" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">Lista</h5>
                    <div class="card-body collapse show mx-auto" id="collapseExample">
                        <table width="100%" class="table table-responsive table-bordered align-middle" data-filter="true" data-input="#filtro">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Item</th>
                                    <th>Código</th>
                                    <th>Descripción</th>
                                    <th>Valor</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                $consulta = $db->prepare("select id, codigo, descripcion, valor from man_configuracion where estado = 1 order by codigo");
                                $consulta->execute();
                                $i=1;
                                while ($item = $consulta->fetch(PDO::FETCH_ASSOC))
                                {
                                    print('
                                    <tr>
                                        <td align="center">'.$i++.'</td>
                                        <td align="center">'.mb_strtoupper($item["codigo"]).'</td>
                                        <td align="center">'.mb_strtoupper($item["descripcion"]).'</td>
                                        <td align="center">'.mb_strtoupper($item["valor"]).'</td>
                                        <td align="center">
                                            <a href="man_configuracion_edit.php?id='.$item["id"].'"><img src="_libraries/open-iconic/svg/pencil.svg" height="18" width="18" alt="icon name"></a>
                                        </td>
                                    </tr>');
                                }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <script src="js/jquery-1.11.1.min.js"></script>
        <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
        <script src="js/man_configuracion.js?v=1.0.0"></script>
    </body>
</html>