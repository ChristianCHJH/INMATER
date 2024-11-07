<?php
    include 'seguridad_login.php';
    $dni = $tipoinforme = "";
    // verificar dni paciente
    if (isset($_GET["dni"]) && !empty($_GET["dni"])) {
        $dni = $_GET["dni"];
        // 
        $consulta = $db->prepare("select * from hc_riesgo_quirurgico where estado = 1 and numerodocumento = ?");
        $consulta->execute( array($dni) );
        $data = $consulta->fetch(PDO::FETCH_ASSOC);
    } else {
        print("No seleccionó a ningún paciente");
        exit();
    }
    // guardar datos
    if ( isset($_POST['dni']) && !empty($_POST['dni']) && isset($_POST['agregar']) && !empty($_POST['agregar']) && isset($_POST['fvigencia']) && !empty($_POST['fvigencia']) && isset($_FILES['informe']) && !empty($_FILES['informe']) ) {
        update_riesgoquirurgico_01(0, $_POST['dni'], $_POST['fvigencia'], $_FILES['informe'], $_POST['obs'], $_POST['nivel'], $login);
        header("Location: n_riesgo_quirurgico_01.php?dni=" . $dni);
    }
    // datos paciente
    $rPaci = $db->prepare("
        select * from hc_antece, hc_paciente
        where hc_paciente.dni=? AND hc_antece.dni=?");
    $rPaci->execute( array($dni, $dni) );
    $paci = $rPaci->fetch(PDO::FETCH_ASSOC);
    // datos usuario
    $rUser = $db->prepare("select role from usuario where userx=?");
    $rUser->execute( array($login) );
    $user = $rUser->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/global.css">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
</head>
<body>
    <?php require ('_includes/menu_salaprocedimientos.php'); ?>
    <div class="container">
        <div class="card mb-3">
            <?php
            print('
                <h5 class="card-header">Documento Riesgo Quirúrgico: <small>'.mb_strtoupper($paci['ape'])." ".mb_strtoupper($paci['nom']).'</small></h5>');
            ?>
            <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data" data-ajax="false" id="form1">
                    <input type="hidden" name="dni" value="<?php echo $dni; ?>">
                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-3 col-lg-3">
                            <div class="input-group">
                                <span class="input-group-addon">F. Informe*</span>
                                <input class="form-control" name="fvigencia" type="date" value="<?php print($data["fvigencia"]); ?>" required>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                            <div class="input-group">
                                <span class="input-group-addon">Informe* (PDF)</span>
                                <input class="form-control" name="informe" type="file" id="informe" accept="application/pdf" required/>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-3 col-lg-3">
                            <div class="input-group">
                                <span class="input-group-addon">Nivel</span>
                                <select name="nivel" required="required" class="form-control">
                                    <option value="">Seleccionar</option>
                                    <option value="1" <?php if( $data['nivel'] == 1 ) echo 'selected' ?>>1</option>
                                    <option value="2" <?php if( $data['nivel'] == 2 ) echo 'selected' ?>>2</option>
                                    <option value="3" <?php if( $data['nivel'] == 3 ) echo 'selected' ?>>3</option>
                                    <option value="4" <?php if( $data['nivel'] == 4 ) echo 'selected' ?>>4</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="input-group">
                                <span class="input-group-addon">Observación</span>
                                <textarea class="form-control" name="obs"><?php print($data["obs"]); ?></textarea>
                            </div>
                        </div>
                    </div>
                    <?php
                        if ($consulta->rowCount() == 1) {
                            print ('<span class="color_red">Ver/ descargar Informe: </span><a href="riesgo_quirurgico/'.$dni.'/'.$data["nombre"].'" target="_blank"><img src="_images/pdf.png" height="20" width="20" alt="icon name"></a>
                                <span style="float: right;">Usuario: '.$data['idusercreate'].'</span>');
                        }
                    ?>
                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-12 text-center">
                            <?php
                            $accion="";
                            if ($user['role'] == 1 || $user['role'] == 12 || $user['role'] == 15) {
                                if ($consulta->rowCount() == 1) {
                                    $accion="Reemplazar";
                                } else {
                                    $accion="Guardar";
                                }
                                print('<input type="Submit" class="btn btn-danger" name="agregar" value="'.$accion.'"/>');
                                if ($consulta->rowCount() == 1) {
                                    print(' <input type="button" class="btn btn-secondary btn_eliminar_informe" value="Eliminar documento" data-origen="rquirurgico" data-informe="'.$data["id"].'" />');
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" id="eliminar_informe">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">Confirmar Eliminar</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">¿Realmente desea eliminar el informe?</div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" id="modal-btn-no">Cancelar</button>
                                    <button type="button" class="btn btn-dark" id="modal-btn-si">Confirmar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
	<script src="js/jquery-3.2.1.slim.min.js" crossorigin="anonymous"></script>
	<script src="js/jquery-1.11.1.min.js"></script>
	<script src="js/popper.min.js" crossorigin="anonymous"></script>
	<script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
	<script src="js/global.js" crossorigin="anonymous"></script>
</body>
</html>