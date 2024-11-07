<!DOCTYPE HTML>
<html>
    <head>
        <?php 
         include 'seguridad_login.php';
        $tipoinforme="";
        // verificar dni paciente
        if ( isset($_GET["dni"]) && !empty($_GET["dni"]) ) {
            $dni = $_GET["dni"];
        } else {
            print("No existe información");
            exit();
        }
        if (isset($_POST['dni']) && !empty($_POST['dni'])) {
            $rPaci = $db->prepare("SELECT dni,ape,nom,med FROM hc_paciente WHERE dni = ?");
            $rPaci->execute([$_POST['dni']]);
            $paci = $rPaci->fetch(PDO::FETCH_ASSOC);

            ob_start();
            updateAnalisis($_POST['idx'],$_POST['dni'],$_POST['a_mue'],$paci['ape'].' '.$paci['nom'],$paci['med'],$_POST['a_exa'],$_POST['a_sta'],$_POST['a_obs'],$_POST['cor'],$login,$_FILES['informe']);
            ob_get_clean();

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
            <h1>Agregar exámen psicológico</h1>
        </div>

        <div role="main" class="ui-content">
            <form action="" method="post" enctype="multipart/form-data" data-ajax="false">
                <input type="hidden" name="dni" value="<?php echo $dni; ?>">
                <input type="hidden" name="a_exa" value="Examen Psicologico">
                <div class="ui-grid-b">
                    <!-- fecha de examen -->
                    <div class="ui-block-a">
                        <div class="ui-bar" data-role="fieldcontain">
                            <span class="input-group-addon">Fecha de informe*</span>
                            <input class="form-control" name="a_mue" type="date" required>
                        </div>
                    </div>
                    <!-- resultado -->
                    <div class="ui-block-b">
                        <div class="ui-bar" data-role="fieldcontain">
                            <span class="input-group-addon">Resultado</span>
                            <select name="a_sta" id="a_sta" data-mini="true" required>
                                <option value="">Seleccionar</option>
                                <option value="Positivo">Apto</option>
                                <option value="Negativo">No apto</option>
                              </select>
                        </div>
                    </div>
                    <!-- informe -->
                    <div class="ui-block-c">
                        <div class="ui-bar" data-role="fieldcontain">
                            <span class="input-group-addon">Informe</span>
                            <input name="informe" type="file" id="informe" accept="application/pdf" data-mini="true"/>
                        </div>
                    </div>
                </div>
                <div class="row pb-2">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="input-group">
                            <span class="input-group-addon">Observación</span>
                            <textarea name="a_obs" id="a_obs" data-mini="true"></textarea>
                        </div>
                    </div>
                </div>

                <div class="row pb-2">
                    <div class="col-12 col-sm-12 col-md-12 text-center">
                        <input type="Submit" class="btn btn-danger" name="agregar" data-mini="true" value="Agregar"/>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>

</html>