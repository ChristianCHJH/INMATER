<?php
    include 'seguridad_login.php';
    $tipoinforme=$tipopaciente="";
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
        // update_serologia($id, $tipopaciente, $dni, $fec, $hbs, $hcv, $hiv, $rpr, $rub, $tox, $clag, $clam, $lab, $informe)
        update_serologia(0, $tipopaciente, $_POST['dni'], $_POST['fec'], $_POST['hbs'], $_POST['hcv'], $_POST['hiv'], $_POST['rpr'], $_POST['rub'], $_POST['tox'], $_POST['clag'], $_POST['clam'], "", $_FILES['informe']);
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
    }
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
                <?php
                    print('
                    <h5 class="card-header">Agregar exámenes de Serología: <small>' . mb_strtoupper($paci['ape']) . " " . mb_strtoupper($paci['nom']) . '</small>
                        <a class="navbar-brand float-right" href="n_enfermeria.php?dni='.$dni.'">
                            <img src="_libraries/open-iconic/svg/x.svg" height="18" width="18" alt="icon name">
                        </a>
                    </h5>');?>
            <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data" data-ajax="false">
                    <input type="hidden" name="dni" value="<?php echo $dni; ?>">
                    <div class="row pb-2">
                        <!-- fecha de examen -->
                        <div class="col-12 col-sm-12 col-md-3 col-lg-3">
                            <div class="input-group">
                                <span class="input-group-addon">F. Exámen*</span>
                                <input class="form-control" name="fec" type="date" required>
                            </div>
                        </div>
                        <!-- informe -->
                        <div class="col-12 col-sm-12 col-md-9 col-lg-9">
                            <div class="input-group">
                                <span class="input-group-addon">Informe (PDF)</span>
                                <input class="form-control" name="informe" type="file" accept="application/pdf"/>
                            </div>
                        </div>
                    </div>
                    <div class="row pb-2">
                        <!-- Hepatitis B - HBs Ag -->
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                            <div class="input-group">
                                <span class="input-group-addon">Hepatitis B - HBs Ag</span>
                                <select name='hbs' class="form-control">
                                    <option value="">SELECCIONAR</option>
                                    <?php
                                    $data = $db->prepare("select codigo, nombre from man_positivo_negativo where estado=1 order by nombre");
                                    $data->execute();
                                    $rows = $data->fetchAll();
                                    foreach ($rows as $info) {
                                        print("<option value='".$info['codigo']."'");
                                        if ($tipoinforme == $info['codigo'])
                                            print(" selected");
                                        print(">".mb_strtoupper($info['nombre'])."</option>");
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <!-- Hepatitis C - HCV Ac -->
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                            <div class="input-group">
                                <span class="input-group-addon">Hepatitis C - HCV Ac</span>
                                <select name='hcv' class="form-control">
                                    <option value="">SELECCIONAR</option>
                                    <?php
                                    foreach ($rows as $info) {
                                        print("<option value='".$info['codigo']."'");
                                        if ($tipoinforme == $info['codigo'])
                                            print(" selected");
                                        print(">".mb_strtoupper($info['nombre'])."</option>");
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <!-- HIV Ac/Ag -->
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                            <div class="input-group">
                                <span class="input-group-addon">HIV Ac/Ag</span>
                                <select name='hiv' class="form-control">
                                    <option value="">SELECCIONAR</option>
                                    <?php
                                    foreach ($rows as $info) {
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
                        <!-- RPR -->
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                            <div class="input-group">
                                <span class="input-group-addon">RPR</span>
                                <select name='rpr' class="form-control">
                                    <option value="">SELECCIONAR</option>
                                    <?php
                                    foreach ($rows as $info) {
                                        print("<option value='".$info['codigo']."'");
                                        if ($tipoinforme == $info['codigo'])
                                            print(" selected");
                                        print(">".mb_strtoupper($info['nombre'])."</option>");
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <!-- Rubeola IgG -->
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                            <div class="input-group">
                                <span class="input-group-addon">Rubeola IgG</span>
                                <select name='rub' class="form-control">
                                    <option value="">SELECCIONAR</option>
                                    <?php
                                    foreach ($rows as $info) {
                                        print("<option value='".$info['codigo']."'");
                                        if ($tipoinforme == $info['codigo'])
                                            print(" selected");
                                        print(">".mb_strtoupper($info['nombre'])."</option>");
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <!-- Toxoplasma IgG -->
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                            <div class="input-group">
                                <span class="input-group-addon">Toxoplasma IgG</span>
                                <select name='tox' class="form-control">
                                    <option value="">SELECCIONAR</option>
                                    <?php
                                    foreach ($rows as $info) {
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
                        <!-- Clamidia IgG -->
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                            <div class="input-group">
                                <span class="input-group-addon">Clamidia IgG</span>
                                <select name='clag' class="form-control">
                                    <option value="">SELECCIONAR</option>
                                    <?php
                                    foreach ($rows as $info) {
                                        print("<option value='".$info['codigo']."'");
                                        if ($tipoinforme == $info['codigo'])
                                            print(" selected");
                                        print(">".mb_strtoupper($info['nombre'])."</option>");
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <!-- Clamidia IgM -->
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                            <div class="input-group">
                                <span class="input-group-addon">Clamidia IgM</span>
                                <select name='clam' class="form-control">
                                    <option value="">SELECCIONAR</option>
                                    <?php
                                    foreach ($rows as $info) {
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