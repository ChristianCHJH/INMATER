<!DOCTYPE HTML>
<html>
    <head>
    <?php
        /* if ($login != 'testAdmin') {
            exit();
        } */
        include 'seguridad_login.php';
    ?>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="_images/favicon.png" type="image/x-icon">
        <link rel="stylesheet" href="css/bootstrap.v4/bootstrap.min.css" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="css/global.css">
    </head>
    <body>
        <?php require ('_includes/menu-admin.php'); ?>
        <div class="container">
            <?php
                if (isset($_POST['conf']) and !empty($_POST['conf']))
                {
                    require("_database/db_mantenimiento.php");
                    pruebaBiopsiaEliminar($_POST['conf'], mb_strtolower($login));
                }
            ?>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="lista_adminlab.php">Inicio</a></li>
                    <li class="breadcrumb-item">Historia Clínica</li>
                    <li class="breadcrumb-item">Reproducción Asistida</li>
                    <li class="breadcrumb-item active" aria-current="page">Disponibilidad de Horarios</li>
                </ol>
            </nav>
            <div data-role="header">
                <div class="card mb-3" id="imprime">
                    <h5 class="card-header" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">Urología, Ginecología, Aspiración, Transferencia</h5>
                    <div class="collapse show mx-auto" id="collapseExample">
                        <table width="100%" class="table table-responsive table-bordered align-middle" style="font-size: small; margin-bottom: 0 !important;" data-filter="true" data-input="#filtro">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Item</th>
                                    <th>Hora</th>
                                    <th>Urología</th>
                                    <th>Ginecología</th>
                                    <th>Aspiración</th>
                                    <th>Transferencia</th>
                                </tr>
                            </thead>
                        </table>
                        <table width="100%" class="table table-responsive table-bordered align-middle" style="font-size: small; margin-bottom: 0 !important; height: 66vh;" data-filter="true" data-input="#filtro">
                            <tbody>
                            <?php
                                $consulta = $db->prepare("select id, nombre, urologia, ginecologia, aspiracion, aspiracion_inyeccion, transferencia from man_hora where estado = 1 order by codigo");
                                $consulta->execute();
                                $i=1;
                                while ($item = $consulta->fetch(PDO::FETCH_ASSOC))
                                {
                                    $checked=$checkedGineco=$checkedAspira=$checkedTra='';
                                    if ($item['urologia']==1) {
                                        $checked='checked';
                                    }
                                    if ($item['ginecologia']==1) {
                                        $checkedGineco='checked';
                                    }
                                    if ($item['aspiracion']==1) {
                                        $checkedAspira='checked';
                                    }
                                    if ($item['transferencia']==1) {
                                        $checkedTra='checked';
                                    }
                                    print('
                                    <tr>
                                        <td width="5%" align="center">'.$i++.'</td>
                                        <td width="10%" align="center">'.mb_strtoupper($item["nombre"]).'</td>
                                        <td width="10%" align="center">
                                            <input type="checkbox" data-id="'.$item['id'].'" data-procedimiento="0" class="horarios" '.$checked.'>
                                        </td>
                                        <td width="10%" align="center">
                                            <input type="checkbox" data-id="'.$item['id'].'" data-procedimiento="1" class="horarios" '.$checkedGineco.'>
                                        </td>
                                        <td width="10%" align="center">
                                            <input type="checkbox" data-id="'.$item['id'].'" data-procedimiento="2" class="horarios" '.$checkedAspira.'>
                                        </td>
                                        <td width="10%" align="center">
                                            <input type="checkbox" data-id="'.$item['id'].'" data-procedimiento="3" class="horarios" '.$checkedTra.'>
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
        <script src="js/bootstrap.v4/bootstrap.min.js" crossorigin="anonymous"></script>
        <script src="js/man_horario.js"></script>
    </body>
</html>