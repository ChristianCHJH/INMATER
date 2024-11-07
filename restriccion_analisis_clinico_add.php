<!DOCTYPE HTML>
<html>
    <head>
        <?php  include 'seguridad_login.php';
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
            update_serologia(0, $tipopaciente, $_POST['dni'], $_POST['fec'], $_POST['hbs'], $_POST['hcv'], $_POST['hiv'], $_POST['rpr'], $_POST['rub'], $_POST['tox'], $_POST['clag'], $_POST['clam'], "", $_FILES['informe']);
            $stmt = $db->prepare("SELECT * from hc_paciente_accesos where dni=? and estado=1");
            $stmt->execute(array($login));

            if ($stmt->rowCount() != 0) {
                header("Location: paci_reproduccion.php?id=" . $_GET['repro_id']);
            } else {
                header("Location: e_repro_02.php?id=" . $_GET['repro_id']);
            }
        }
         ?>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="_images/favicon.png" type="image/x-icon">
        <link rel="stylesheet" href="_themes/tema_inmater.min.css?v=1.01"/>
        <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css"/>
        <link rel="stylesheet" href="_libraries/open-iconic/font/css/open-iconic.min.css"/>
        <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css"/>
        <link rel="stylesheet" href="css/e_repro.css?v=1.00"/>
        <script src="js/jquery-1.11.1.min.js"></script>
        <script src="js/jquery.mobile-1.4.5.min.js"></script>
    </head>
<body>
    <div data-role="page" class="page-restriccion" data-dialog="true">
        <div data-role="header" data-theme="b">
            <h1>Análisis clínico</h1>
        </div>

        <div role="main" class="ui-content">
            <form action="" method="post" enctype="multipart/form-data" data-ajax="false">
                <input type="hidden" name="dni" value="<?php echo $dni; ?>">
                <div class="ui-grid-a">
                    <!-- fecha de examen -->
                    <div class="ui-block-a">
                        <div class="ui-bar" data-role="fieldcontain">
                            <span class="input-group-addon">F. Exámen*</span>
                            <input class="form-control" name="fec" type="date" data-mini="true" required>
                        </div>
                    </div>
                    <!-- informe -->
                    <div class="ui-block-b">
                        <div class="ui-bar" data-role="fieldcontain">
                            <span class="input-group-addon">Informe (PDF)</span>
                            <input class="form-control" name="informe" type="file" accept="application/pdf" data-mini="true"/>
                        </div>
                    </div>
                </div>
                <div class="ui-grid-b">
                    <!-- Hepatitis B - HBs Ag -->
                    <div class="ui-block-a">
                        <div class="input-group">
                            <span class="input-group-addon">Hepatitis B - HBs Ag</span>
                            <select name='hbs' class="form-control" data-mini="true">
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
                    <div class="ui-block-b">
                        <div class="input-group">
                            <span class="input-group-addon">Hepatitis C - HCV Ac</span>
                            <select name='hcv' class="form-control" data-mini="true">
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
                    <div class="ui-block-c">
                        <div class="input-group">
                            <span class="input-group-addon">HIV Ac/Ag</span>
                            <select name='hiv' class="form-control" data-mini="true">
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
                <div class="ui-grid-b">
                    <!-- RPR -->
                    <div class="ui-block-a">
                        <div class="input-group">
                            <span class="input-group-addon">RPR</span>
                            <select name='rpr' class="form-control" data-mini="true">
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
                    <div class="ui-block-b">
                        <div class="input-group">
                            <span class="input-group-addon">Rubeola IgG</span>
                            <select name='rub' class="form-control" data-mini="true">
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
                    <div class="ui-block-c">
                        <div class="input-group">
                            <span class="input-group-addon">Toxoplasma IgG</span>
                            <select name='tox' class="form-control" data-mini="true">
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
                <div class="ui-grid-b">
                    <!-- Clamidia IgG -->
                    <div class="ui-block-a">
                        <div class="input-group">
                            <span class="input-group-addon">Clamidia IgG</span>
                            <select name='clag' class="form-control" data-mini="true">
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
                    <div class="ui-block-b">
                        <div class="input-group">
                            <span class="input-group-addon">Clamidia IgM</span>
                            <select name='clam' class="form-control" data-mini="true">
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
                        <input type="Submit" class="btn btn-danger" name="agregar" value="Agregar" data-mini="true"/>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>

</html>