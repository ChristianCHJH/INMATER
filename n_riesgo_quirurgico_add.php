
<?php
       include 'seguridad_login.php';
    // guardar datos
    if ( isset($_POST['dni']) && !empty($_POST['dni']) && isset($_POST['agregar']) ) {
        /* var_dump($_POST);
        print("<br><br>");
        var_dump($_FILES["informe"]); */
        update_riesgoquirurgico_01(0, $_POST['dni'], $_POST['fvigencia'], $_FILES['informe'], $_POST['obs'], $_POST['nivel'], $login);
        header("Location: n_riesgo_quirurgico.php?dni=" . $_POST['dni']);

    }
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
    <?php
    

    // verificar dni paciente
    if (isset($_GET["dni"]) && !empty($_GET["dni"])) {
        $dni = $_GET["dni"];
    } else {
        print("No existe información");
        exit();
    }
    // datos paciente
    $rPaci = $db->prepare("
        select * from hc_antece, hc_paciente
        where hc_paciente.dni=? AND hc_antece.dni=?");
    $rPaci->execute(array($dni, $dni));
    $paci = $rPaci->fetch(PDO::FETCH_ASSOC);
    ?>
    <?php require ('_includes/menu_salaprocedimientos.php'); ?>
    <div class="container">
        <div class="card mb-3">
            <?php print('
                <h5 class="card-header">
                    Agregar documentos Legal: <small>'.mb_strtoupper($paci['ape'])." ".mb_strtoupper($paci['nom']).'</small>
                    <a class="navbar-brand float-right" href="n_riesgo_quirurgico.php?dni='.$dni.'">
                        <img src="_libraries/open-iconic/svg/x.svg" height="18" width="18" alt="icon name">
                    </a>
                </h5>'); ?>
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
                                    <option value="1" <?php if(isset($data['nivel'])){if( $data['nivel'] == 1 ) echo 'selected'; }?>>1</option>
                                    <option value="2" <?php if(isset($data['nivel'])){if( $data['nivel'] == 2 ) echo 'selected'; }?>>2</option>
                                    <option value="3" <?php if(isset($data['nivel'])){if( $data['nivel'] == 3 ) echo 'selected'; }?>>3</option>
                                    <option value="4" <?php if(isset($data['nivel'])){if( $data['nivel'] == 4 ) echo 'selected'; }?>>4</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="input-group">
                                <span class="input-group-addon">Observación</span>
                                <textarea class="form-control" name="obs"><?php if(isset($data["obs"])){print($data["obs"]); }?></textarea>
                            </div>
                        </div>
                    </div>
                    <?php
                        
                    ?>
                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-12 text-center">
                            <input type="Submit" class="btn btn-danger" name="agregar" value="Guardar"/>
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
</body>
</html>