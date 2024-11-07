<?php
    session_start();
    ini_set("display_errors","1");
    error_reporting(E_ALL);
    $login = $_SESSION['login'];
    $dir = $_SERVER['HTTP_HOST'].substr(dirname(__FILE__), strlen($_SERVER['DOCUMENT_ROOT']));

    if (!$login) {
        print("<META HTTP-EQUIV='Refresh' CONTENT='0; URL=http://".$dir."'>");
    }

    require("_database/db_tools.php");

    // verificar id
    if (isset($_GET["id"]) && !empty($_GET["id"])) {
        $id = $_GET["id"];
        $consulta = $db->prepare("select id, nombre from man_poseidon where estado = 1 and id = ?");
        $consulta->execute(array($id));
        $data = $consulta->fetch(PDO::FETCH_ASSOC);
    } else {
        print("No seleccionó a ningún dato.");
        exit();
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/global.css">
</head>
<body>
    <?php require ('_includes/menu-admin.php'); ?>
    <div class="container">
        <div class="card mb-3">
            <?php print('
            <h5 class="card-header">
                Editar: <small>'.mb_strtoupper($data['nombre']).'</small>
            </h5>'); ?>
            <div class="card-body">
                <input type="hidden" id="principal" value="<?php print($data['id']); ?>">
                <div class="row pb-2">
                    <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                        <div class="input-group">
                            <span class="input-group-addon">Nombre*</span>
                            <input class="form-control" id="nombre" type="text" value="<?php print(mb_strtoupper($data["nombre"])); ?>">
                        </div>
                    </div>
                </div>
                <div class="row pb-2">
                    <div class="col-12 col-sm-12 col-md-12 text-center">
                        <input type="Submit" class="btn btn-danger" id="guardar" name="guardar" value="Guardar"/>
                        <a href="man_poseidon.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </div>
                <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" id="modal_editar">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Confirmar</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">¿Realmente desea editar los datos?</div>
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
	<script src="js/jquery-1.11.1.min.js"></script>
	<script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
	<script src="js/man_poseidon.js?v.18.11.7" crossorigin="anonymous"></script>
</body>
</html>