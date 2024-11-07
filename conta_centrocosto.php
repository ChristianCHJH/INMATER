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
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css"
        integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
    <link rel="stylesheet" href="css/bootstrap.v4/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/shared.css">
    <link rel="stylesheet" type="text/css" href="css/global.css">
    <title>Clínica Inmater | Mantenimiento de Centros de Costo</title>
</head>

<body>
    <div class="box container">
        <?php require ('_includes/menu_facturacion.php'); ?>
        <?php
          $stmt = $db->prepare("SELECT * FROM usuario WHERE userx=?");
          $stmt->execute(array($login));
          $data = $stmt->fetch(PDO::FETCH_ASSOC); ?>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="lista_facturacion.php">Inicio</a></li>
                <li class="breadcrumb-item" aria-current="page">Mantenimiento</li>
                <li class="breadcrumb-item active" aria-current="page">Centros de costo</li>
            </ol>
        </nav>
        <div class="card mb-3">
            <input type="hidden" name="conf">
            <h5 class="card-header">Agregar Centro de Costo</h5>
            <div class="card-body">
                <div class="row pb-2">
                    <!-- sede -->
                    <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Sede*</span>
                            <select name='sede' id="sede" class="form-control form-control-sm chosen-select" required>
                                <option value="">SELECCIONAR</option>
                                <?php
                              $stmt = $db->prepare("SELECT id, codigo, nombre from sedes_contabilidad where eliminado = 0 order by nombre;");
                              $stmt->execute();

                              while ($sede = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                print('<option value="' . $sede['id'] . '">'.mb_strtoupper($sede['nombre']).' ('.$sede['codigo'].')</option>');
                              } ?>
                            </select>
                        </div>
                    </div>
                    <!-- codigo -->
                    <div class="col-12 col-sm-12 col-md-2 col-lg-2 input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Código*</span>
                            <input class="form-control form-control-sm" type="text" name="codigo" id="codigo"
                                required />
                        </div>
                    </div>
                    <!-- nombre -->
                    <div class="col-12 col-sm-12 col-md-4 col-lg-4 input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Nombre*</span>
                            <input class="form-control form-control-sm" type="text" name="nombre" id="nombre"
                                required />
                        </div>
                    </div>
                    <?php
                  if($data["role"] == "3" or $data["role"] == "10") { ?>
                    <!-- agregar -->
                    <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                        <input class="form-control btn btn-danger btn-sm" type="Submit" name="agregar"
                            id="agregar_centrocosto" value="Agregar" />
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="card row content">
            <div class="col">
                <div class="card-body1">
                    <table class="table table-sm table-hover table-bordered" id="tb_servicios">
                        <thead class="thead-dark row-sticky">
                            <th class="text-center">Id</th>
                            <th>Sede</th>
                            <th>Centro de Costo</th>
                            <?php
                            if($data["role"] == "3" or $data["role"] == "10") { ?>
                            <th class="text-center">Acción</th>
                            <?php } ?>
                        </thead>
                        <tbody>
                            <?php
                            $Rpop = $db->prepare("SELECT cc.id, s.codigo codigo_sede, s.nombre sede, cc.codigo, cc.descripcion nombre
                            from conta_centro_costo cc
                            inner join sedes_contabilidad s on s.id = cc.sede_id
                            where cc.estado = 1
                            order by s.codigo, cc.codigo");
                            $Rpop->execute();
                            while ($info = $Rpop->fetch(PDO::FETCH_ASSOC)) {
                                print("<tr>
                                <td class='text-center'>".$info['id']."</td>
                                <td>(".$info['codigo_sede'].") ".mb_strtoupper($info['sede'])."</td>
                                <td>(".$info['codigo'].") ".$info['nombre']."</td>");

                                if($data["role"] == "3" or $data["role"] == "10") {
                                print("<td class='text-center'>
                                    <a href='conta_centrocosto_edit.php?id=".$info['id']."'><i class='far fa-edit'></i></a>
                                    <a href='javascript:eliminar(".$info['id'].");'><i class='fas fa-trash-alt'></i></a>
                                </td>");
                                }

                                print("</tr>");
                            }

                            if ($Rpop->rowCount() < 1) {echo '<p><h3 class="text_buscar">¡ No existen datos !</h3></p>';} ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row footer">@2022 Clínica Inmater</div>
    </div>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/chosen.jquery.min.js"></script>
    <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="js/conta_centrocosto.js"></script>
</body>

</html>