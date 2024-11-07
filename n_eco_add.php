<!DOCTYPE html>
<html>
<head>
<?php
   include 'seguridad_login.php'
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/global.css">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
</head>
<body>
    <?php
    // guardar datos
    if ( isset($_POST['dni']) && !empty($_POST['dni']) && isset($_POST['agregar']) ) {
        /* var_dump($_POST);
        print("<br><br>");
        var_dump($_FILES["informe"]);
        print("<br><br>");
        var_dump($_FILES["ecos"]); */
        update_eco_consultorio(0, $_POST['dni'], $_POST['fconsulta'], $_FILES['informe'], $_FILES['ecos'], $_POST['obs'], $login);
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
    <?php require ('_includes/menu_medico.php'); ?>
    <div class="container">
        <div class="card mb-3">
            <?php print('
                <h5 class="card-header">
                    Agregar Ecografía en Consultorio: <small>'.mb_strtoupper($paci['ape'])." ".mb_strtoupper($paci['nom']).'</small>
                    <a class="navbar-brand float-right" href="n_eco.php?dni='.$dni.'">
                        <img src="_libraries/open-iconic/svg/x.svg" height="18" width="18" alt="icon name">
                    </a>
                </h5>'); ?>
            <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data" data-ajax="false" id="form1">
                    <input type="hidden" name="dni" value="<?php echo $dni; ?>">
                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-3 col-lg-3">
                            <div class="input-group">
                                <span class="input-group-addon">F. Consulta*</span>
                                <input class="form-control" name="fconsulta" type="date" value=""  id="fconsulta" required>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-9 col-lg-9">
                            <div class="input-group">
                                <span class="input-group-addon">Informe</span>
                                <input class="form-control" name="informe" type="file" id="informe" accept="application/msword, application/pdf"/>
                            </div>
                        </div>
                    </div>
                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="input-group">
                                <span class="input-group-addon">Ecografías*</span>
                                <input class="form-control" name="ecos[]" type="file" id="ecos" accept="image/*" multiple="multiple" required/>
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
</body>
</html>