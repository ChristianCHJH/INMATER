<!DOCTYPE HTML>
<html>
    <head>
        <?php  
        include 'seguridad_login.php';
        require("_database/db_paciente.php");
        $dni=$dni_mujer="";
          if ( isset($_GET['dni']) && !empty($_GET['dni']) ) {
              $dni = $_GET['dni'];
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
            <?php
            if (!!$_POST) {
                if ($_POST["analisis_confirmacion"] == "1") {
                    $stmt = $db->prepare("UPDATE hc_antece_p_sero SET iduserupdate=? WHERE id=?");
                    $stmt->execute(array($login, $_POST["analisis_id"]));
                } else {
                    $stmt = $db->prepare("UPDATE hc_antece_p_sero SET estado = 0, iduserupdate=? WHERE id=?");
                    $stmt->execute(array($login, $_POST["analisis_id"]));
                }
            }

            $stmt = $db->prepare("SELECT * FROM hc_paciente_accesos WHERE dni=? AND estado = 1;");
            $stmt->execute(array($login));

            if ($stmt->rowCount() != 0) {
                $es_paciente = 1;
                print('<a href="paci_reproduccion.php?id=' . $_GET['repro_id'] . '" rel="external" class="ui-btn">Cerrar</a>');
            } else {
                $es_paciente = 0;
                print('<a href="e_repro_02.php?id=' . $_GET['repro_id'] . '" rel="external" class="ui-btn">Cerrar</a>');
            } ?>
            <h1>Análisis clínico</h1>
        </div>

        <div role="main" class="ui-content">
            <div class="card-body collapse show">
                    <?php
                    print('<a href="restriccion_analisis_clinico_add.php?tipopaciente='.$_GET['tipopaciente'].'&repro_id='.$_GET['repro_id'].'&dni=' . $dni . '" rel="external" class="ui-btn ui-btn-inline ui-mini" >Agregar</a><br><br>');
                    
                    $rRepro = $db->prepare("SELECT * FROM hc_reprod WHERE estado = true and id=?");
                    $rRepro->execute(array($_GET['repro_id']));
                    $repro = $rRepro->fetch(PDO::FETCH_ASSOC);

                    $sero = getSero($dni, $repro['fec'], $_GET['tipopaciente']); ?>

                    <?php if( $sero ): ?>
                    <table data-role="table" class="ui-responsive table-stroke">
                        <thead class="thead-dark">
                            <tr>
                                <th width="20%" class="text-center">Exámen</th>
                                <th width="20%">Resultado</th>
                                <th width="25%">Informe</th>
                                <th width="20%">Fecha</th>
                                <th width="15%" class="text-center">Estado</th>
                                <th width="15%" class="text-center">Usuario</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php echo print_sero_1('hbs', 'Hepatitis B - HBs Ag') ?>
                            <?php echo print_sero_1('hcv', 'Hepatitis C - HCV Ac') ?>
                            <?php echo print_sero_1('hiv', 'HIV Ac/Ag') ?>
                            <?php echo print_sero_1('rpr', 'RPR') ?>
                            <?php echo print_sero_1('rub', 'Rubeola IgG') ?>
                            <?php echo print_sero_1('tox', 'Toxoplasma IgG') ?>
                            <?php echo print_sero_1('cla_g', 'Clamidia IgG') ?>
                            <?php echo print_sero_1('cla_m', 'Clamidia IgM') ?>
                        </tbody>
                    </table>

                    <?php
                        if ($sero["es_paciente"] == 1 && $es_paciente == 0) {
                            print('<form action="restriccion_analisis_clinico.php?tipopaciente=1&repro_id=' . $_GET['repro_id'] . '&dni='.$_GET['dni'].'" method="post" data-ajax="false" id="analisis_confirmar">
                                <input type="hidden" name="analisis_confirmacion" id="analisis_confirmacion">
                                <input type="hidden" name="analisis_id" value="'.$sero['id'].'">
                                <div>
                                    Este documento ha sido cargado por el paciente, confirme o descarte esta información:<br>
                                    <a href="javascript:void(0)" id="analisis_aceptar" class="ui-btn ui-btn-inline ui-icon-check ui-btn-icon-left ui-mini" data-mini="true">Aceptar</a>
                                    <a href="javascript:void(0)" id="analisis_descartar" class="ui-btn ui-btn-inline ui-icon-delete ui-btn-icon-left ui-mini" data-mini="true">Descartar</a>
                                </div>
                            </form>');
                        }
                    ?>

                    <?php else: echo '<h5>¡Aún no hay análisis clínicos!</h5>' ?>
                    <?php endif ?>
                </div>
        </div>
    </div>
    <script src="js/restriccion_analisis.js"></script>
</body>
</html>