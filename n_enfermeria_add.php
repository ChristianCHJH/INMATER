<?php
    include 'seguridad_login.php';
    $tipoinforme="";
    // guardar datos
    if ( isset($_POST['dni']) && !empty($_POST['dni']) && isset($_POST['agregar']) ) {
        /*var_dump($_POST);
        print("<br><br>");
        var_dump($_FILES["informe"]);*/
        update_enfermeria(0, $_POST['dni'], $_POST['finforme'], $_FILES['informe'], $_POST['medico'], $_POST['reproasistida'], $_POST['procesala'], $_POST['obs'], $login);
    }

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
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=gb18030">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="css/bootstrap.min.css" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="css/global.css">
	<link rel="icon" href="_images/favicon.png" type="image/x-icon">
</head>
<body>
<?php require ('_includes/menu_salaprocedimientos.php'); ?>
    <div class="container">
        <div class="card mb-3">
                <?php print('
                <h5 class="card-header">
                    Agregar documentos Enfermeria: <small>'.mb_strtoupper($paci['ape'])." ".mb_strtoupper($paci['nom']).'</small>
                    <a class="navbar-brand float-right" href="n_enfermeria.php?dni='.$dni.'">
                        <img src="_libraries/open-iconic/svg/x.svg" height="18" width="18" alt="icon name">
                    </a>
                </h5>'); ?>
            <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data" data-ajax="false" id="form1">
                    <input type="hidden" name="dni" value="<?php echo $dni; ?>">
                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-3 col-lg-3">
                            <div class="input-group">
                                <span class="input-group-addon">F. informe*</span>
                                <input class="form-control" name="finforme" type="date" id="finforme" required>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-9 col-lg-9">
                            <div class="input-group">
                                <span class="input-group-addon">Informe* (PDF)</span>
                                <input class="form-control" name="informe" type="file" id="informe" accept="application/pdf" required/>
                            </div>
                        </div>
                    </div>
                    <div class="row pb-2">
                        <!-- medico -->
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                            <div class="input-group">
                                <span class="input-group-addon">Médico</span>
                                <select name='medico' class="form-control">
                                    <option value="">SELECCIONAR</option>
                                    <?php
                                        $data = $db->prepare("select codigo, nombre from man_medico where estado_enfermeria=1 order by nombre");
                                        $data->execute();
                                        while ($info = $data->fetch(PDO::FETCH_ASSOC)) {
                                            print("<option value='".$info['codigo']."'");
                                        if ($tipoinforme == $info['codigo'])
                                            print(" selected");
                                        print(">".mb_strtoupper($info['nombre'])."</option>");
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <!-- reproduccion asistida -->
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                            <div class="input-group">
                                <span class="input-group-addon">Reproducción Asistida</span>
                                <select name='reproasistida' class="form-control">
                                    <option value="">SELECCIONAR</option>
                                    <?php
                                        $data = $db->prepare("select codigo, nombre from man_enfermeria_repro where estado = 1 order by nombre");
                                        $data->execute();
                                        while ($info = $data->fetch(PDO::FETCH_ASSOC)) {
                                            print("<option value='".$info['codigo']."'");
                                        if ($tipoinforme == $info['codigo'])
                                            print(" selected");
                                        print(">".mb_strtoupper($info['nombre'])."</option>");
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <!-- procedimiento sala -->
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                            <div class="input-group">
                                <span class="input-group-addon">Procedimiento de Sala</span>
                                <select name='procesala' class="form-control">
                                    <option value="">SELECCIONAR</option>
                                    <?php
                                        $data = $db->prepare("select codigo, nombre from man_enfermeria_proce where estado = 1 order by nombre");
                                        $data->execute();
                                        while ($info = $data->fetch(PDO::FETCH_ASSOC)) {
                                            print("<option value='".$info['codigo']."'");
                                        if ($tipoinforme == $info['codigo'])
                                            print(" selected");
                                        print(">".mb_strtoupper($info['nombre'])."</option>");
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="input-group">
                                <span class="input-group-addon">Observación</span>
                                <textarea class="form-control" name="obs" id="obs"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-12 text-center">
                            <input type="Submit" class="btn btn-danger" name="agregar" value="Agregar"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="js/jquery-3.2.1.slim.min.js" crossorigin="anonymous"></script>
	<script src="js/popper.min.js" crossorigin="anonymous"></script>
	<script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>
</html>