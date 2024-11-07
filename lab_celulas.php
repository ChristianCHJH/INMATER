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
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="lista_adminlab.php">Inicio</a></li>
                <li class="breadcrumb-item"><a href="#">Laboratorio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Mantenimiento Células
                </li>
            </ol>
        </nav>
        <script>
            function anular(id) {
                if (confirm("¿Está seguro que quiere eliminar este registro?")) {
                    document.form2.conf.value = id;
                    document.form2.submit();
                } else {
                    return false;
                }
            }
        </script>
        <?php
            if (isset($_POST['conf']) and !empty($_POST['conf']))
            {
                require("_database/db_mantenimiento.php");
                celulasEliminar($_POST['conf'], mb_strtolower($login));
            }

            if (isset($_POST['nom']) and !empty($_POST['nom']))
            {
                require("_database/db_mantenimiento.php");
                celulasInsertar($_POST['codigo'], mb_strtoupper($_POST['nom']), mb_strtolower($login));
            }

            $consulta = $db->prepare("SELECT
            id, codigo, nombre, dia2, dia3, dia4, dia5, dia6
            FROM lab_celulas
            WHERE estado = 1
            ORDER BY id asc");
            $consulta->execute();
            $rows = $consulta->fetchAll();

            // datos predeterminados
            $cpredeterminado = $db->prepare("
            select
            p2.id dia2pred, p3.id dia3pred, p4.id dia4pred, p5.id dia5pred, p6.id dia6pred
            from lab_celulas p2
            left join lab_celulas p3 on p3.dia3predeterminado = 1 and p3.estado = 1
            left join lab_celulas p4 on p4.dia4predeterminado = 1 and p4.estado = 1
            left join lab_celulas p5 on p5.dia5predeterminado = 1 and p5.estado = 1
            left join lab_celulas p6 on p6.dia6predeterminado = 1 and p6.estado = 1
            where p2.dia2predeterminado = 1 and p2.estado = 1");
            $cpredeterminado->execute();
            $datospredeterminado = $cpredeterminado->fetchAll();
        ?>
        <div data-role="header">
            <div class="card mb-3">
                <h5 class="card-header" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">Nuevo</h5>
                <div class="card-body collapse show" id="collapseExample">
                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-3 col-lg-3">
                            <div class="input-group">
                                <span class="input-group-addon">Código</span>
                                <input class="form-control" id="codigo" type="text" required>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                            <div class="input-group">
                                <span class="input-group-addon">Nombre</span>
                                <input class="form-control" id="nombre" type="text" required>
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
                <div class="card-body collapse show mx-auto" id="collapseExample">
                    <form action="" method="post" data-ajax="false" name="form2">
                        <input type="hidden" name="conf">
                        <table width="100%" class="table table-responsive table-bordered align-middle" data-filter="true" data-input="#filtro">
                            <thead class="thead-dark">
                                <tr>
                                    <th class="text-center">Item</th>
                                    <th class="text-center">Código</th>
                                    <th class="text-center">Descripción</th>
                                    <th class="text-center">Día 2</th>
                                    <th class="text-center">Día 3</th>
                                    <th class="text-center">Día 4</th>
                                    <th class="text-center">Día 5</th>
                                    <th class="text-center">Día 6</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <select class="form-control" name="dia2_predeterminado" data-dia="2" id="predeterminado2">
                                            <option value="">PREDETERMINADO</option>
                                            <?php
                                                foreach ($rows as $data) {?>
                                                    <option value=<?php echo $data['id'];
                                                    if ($datospredeterminado[0]['dia2pred'] == $data['id']) {echo " selected";} ?>><?php echo $data['nombre']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control" name="dia3_predeterminado" data-dia="3" id="predeterminado3">
                                            <option value="">PREDETERMINADO</option>
                                            <?php
                                                foreach ($rows as $data) {?>
                                                    <option value=<?php echo $data['id'];
                                                    if ($datospredeterminado[0]['dia3pred'] == $data['id']) {echo " selected";} ?>><?php echo $data['nombre']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control" name="dia4_predeterminado" data-dia="4" id="predeterminado4">
                                            <option value="">PREDETERMINADO</option>
                                            <?php
                                                foreach ($rows as $data) {?>
                                                    <option value=<?php echo $data['id'];
                                                    if ($datospredeterminado[0]['dia4pred'] == $data['id']) {echo " selected";} ?>><?php echo $data['nombre']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control" name="dia5_predeterminado" data-dia="5" id="predeterminado5">
                                            <option value="">PREDETERMINADO</option>
                                            <?php
                                                foreach ($rows as $data) {?>
                                                    <option value=<?php echo $data['id'];
                                                    if ($datospredeterminado[0]['dia5pred'] == $data['id']) {echo " selected";} ?>><?php echo $data['nombre']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control" name="dia6_predeterminado" data-dia="6" id="predeterminado6">
                                            <option value="">PREDETERMINADO</option>
                                            <?php
                                                foreach ($rows as $data) {?>
                                                    <option value=<?php echo $data['id'];
                                                    if ($datospredeterminado[0]['dia6pred'] == $data['id']) {echo " selected";} ?>><?php echo $data['nombre']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td></td>
                                </tr>
                            <?php
                                $i=1;
                                foreach ($rows as $item)
                                {
                                    $checkedDia2 = $checkedDia3 = $checkedDia4 = $checkedDia5 = $checkedDia6 = '';
                                    if ($item['dia2']==1) {
                                        $checkedDia2='checked';
                                    }
                                    if ($item['dia3']==1) {
                                        $checkedDia3='checked';
                                    }
                                    if ($item['dia4']==1) {
                                        $checkedDia4='checked';
                                    }
                                    if ($item['dia5']==1) {
                                        $checkedDia5='checked';
                                    }
                                    if ($item['dia6']==1) {
                                        $checkedDia6='checked';
                                    }

                                    print('
                                    <tr>
                                        <td class="text-center">'.$i++.'</td>
                                        <td class="text-center">'.$item["codigo"].'</td>
                                        <td class="text-center">'.mb_strtoupper($item["nombre"]).'</td>
                                        <td class="text-center"><input type="checkbox" data-id="'.$item['id'].'" data-dia="2" class="celuladia" '.$checkedDia2.'></td>
                                        <td class="text-center"><input type="checkbox" data-id="'.$item['id'].'" data-dia="3" class="celuladia" '.$checkedDia3.'></td>
                                        <td class="text-center"><input type="checkbox" data-id="'.$item['id'].'" data-dia="4" class="celuladia" '.$checkedDia4.'></td>
                                        <td class="text-center"><input type="checkbox" data-id="'.$item['id'].'" data-dia="5" class="celuladia" '.$checkedDia5.'></td>
                                        <td class="text-center"><input type="checkbox" data-id="'.$item['id'].'" data-dia="6" class="celuladia" '.$checkedDia6.'></td>
                                        <td class="text-center"><a href="javascript:anular('.$item['id'].');"><img src="_libraries/open-iconic/svg/trash.svg" height="18" width="18" alt="icon name"></a></td>
                                    </tr>');
                                }
                            ?>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="js/lab_celulas.js?v=181119"></script>
</body>
</html>