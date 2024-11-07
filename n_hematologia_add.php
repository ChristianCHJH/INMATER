<?php
    include 'seguridad_login.php';
    $tipopaciente="";
    // verificar dni paciente
    if ( isset($_GET["dni"]) && !empty($_GET["dni"]) && isset($_GET["tipopaciente"]) && !empty($_GET["tipopaciente"]) ) {
        $dni = $_GET["dni"];
        $tipopaciente = $_GET["tipopaciente"];
    } else {
        print("No existe información");
        exit();
    }
    // guardar datos
    if ( isset($_POST['dni']) && !empty($_POST['dni']) && isset($_POST['agregar']) ) {
        /*var_dump($_POST);
        print("<br><br>");
        var_dump($_FILES["informe"]);*/
        // update_hematologia($id, $tipopaciente, $dni, $fresultado, $informe, $obs, $login)
        update_hematologia(0, $tipopaciente, $_POST['dni'], $_POST['fresultado'], $_FILES['resultado'], $_POST['obs'], $login);
        switch ($tipopaciente) {
            case 1: header("Location: n_analisisclinico.php?dni=" . $dni); break;
            case 2:
            $data = $db->prepare("
                select c.dni
                from hc_pareja a
                inner join hc_pare_paci b on b.p_dni = a.p_dni
                inner join hc_paciente c on c.dni = b.dni
                where a.p_dni=?");
            $data->execute( array($dni) );
            $info = $data->fetch(PDO::FETCH_ASSOC);
            header("Location: n_analisisclinico.php?dni=" . $info["dni"]);
            break; default: break;
        } 
        // header("Location: n_analisisclinico.php?dni=" . $_POST['dni']);
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
                    Agregar documentos Hematología: <small>'.mb_strtoupper($paci['ape'])." ".mb_strtoupper($paci['nom']).'</small>
                    <a class="navbar-brand float-right" href="n_legal.php?dni='.$dni.'">
                        <img src="_libraries/open-iconic/svg/x.svg" height="18" width="18" alt="icon name">
                    </a>
                </h5>'); ?>
            <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data" data-ajax="false">
                    <input type="hidden" name="dni" value="<?php echo $dni; ?>">
                    <div class="row pb-2">
                        <div class="col-12 col-sm-12 col-md-3 col-lg-3">
                            <div class="input-group">
                                <span class="input-group-addon">F. resultado*</span>
                                <input class="form-control" name="fresultado" type="date" id="fresultado" required>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-9 col-lg-9">
                            <div class="input-group">
                                <span class="input-group-addon">Resultado* (PDF)</span>
                                <input class="form-control" name="resultado" type="file" accept="application/pdf" required/>
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