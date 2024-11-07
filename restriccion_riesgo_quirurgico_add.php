
<!DOCTYPE HTML>
<html>
<head>
    <?php
     include 'seguridad_login.php';

    if ( isset($_POST['dni']) && !empty($_POST['dni']) && isset($_POST['agregar']) ) {
        update_riesgoquirurgico_01(0, $_POST['dni'], $_POST['fvigencia'], $_FILES['informe'], $_POST['obs'], $_POST['nivel'], $login);

        $stmt = $db->prepare("SELECT * from hc_paciente_accesos where dni=? and estado=1");
        $stmt->execute(array($login));

        if ($stmt->rowCount() != 0) {
            header("Location: paci_reproduccion.php?id=" . $_GET['repro_id']);
        } else {
            header("Location: e_repro_02.php?id=" . $_GET['repro_id']);
        }
    }

    $dni=$dni_mujer="";
        if ( isset($_GET['dni']) && !empty($_GET['dni']) ) {
            $dni = $_GET['dni'];
        }

        $rPaci = $db->prepare("
            select * from hc_antece, hc_paciente
            where hc_paciente.dni=? AND hc_antece.dni=?");
        $rPaci->execute(array($dni, $dni));
        $paci = $rPaci->fetch(PDO::FETCH_ASSOC);
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
            <h1>Agregar documento legal: <small><?php echo mb_strtoupper($paci['ape'])." ".mb_strtoupper($paci['nom']) ?></small></h1>
        </div>

        <div role="main" class="ui-content">
            <form action="" method="post" enctype="multipart/form-data" data-ajax="false" id="form1">
                <input type="hidden" name="dni" value="<?php echo $dni; ?>">
                <div class="ui-grid-b">
                    <div class="ui-block-a">
                        <div class="ui-bar" data-role="fieldcontain">
                            <span class="input-group-addon">F. Informe*</span>
                            <input class="form-control" name="fvigencia" type="date" value="" data-mini="true" required>
                        </div>
                    </div>
                    <div class="ui-block-b">
                        <div class="ui-bar" data-role="fieldcontain">
                            <span class="input-group-addon">Informe* (PDF)</span>
                            <input class="form-control" name="informe" type="file" id="informe" accept="application/pdf" data-mini="true" required/>
                        </div>
                    </div>
                    <div class="ui-block-c">
                        <div class="ui-bar" data-role="fieldcontain">
                            <span class="input-group-addon">Nivel</span>
                                <select name="nivel" required="required" class="form-control" data-mini="true">
                                    <option value="">Seleccionar</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                </select>
                        </div>
                    </div>
                </div>
                <div class="row pb-2">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="input-group">
                            <span class="input-group-addon">Observación</span>
                            <textarea class="form-control" name="obs" data-mini="true"></textarea>
                        </div>
                    </div>
                </div>

                
                <div class="row pb-2">
                    <div class="col-12 col-sm-12 col-md-12 text-center">
                        <input type="Submit" class="ui-btn ui-btn-inline ui-mini" name="agregar" data-mini="true" value="Guardar"/>
                    </div>
                </div>
               
            </form>
        </div>
    </div>
</body>
</html>