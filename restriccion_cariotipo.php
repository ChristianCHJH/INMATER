<!DOCTYPE HTML>
<html>
<head>
    <?php
    include 'seguridad_login.php';
    $dni = $tipoinforme = "";

    if (!!$_POST) {
        if ($_POST["cariotipo_confirmacion"] == "1") {
            $stmt = $db->prepare("UPDATE hc_cariotipo SET iduserupdate=? WHERE id=?");
            $stmt->execute(array($login, $_POST["cariotipo_id"]));
        } else {
            $stmt = $db->prepare("UPDATE hc_cariotipo SET estado = 0, iduserupdate=? WHERE id=?");
            $stmt->execute(array($login, $_POST["cariotipo_id"]));
        }
    }
    // verificar dni paciente
    if (isset($_GET["dni"]) && !empty($_GET["dni"])) {
        $dni = $_GET["dni"];
        
        $rRepro = $db->prepare("SELECT * FROM hc_reprod WHERE estado = true and id=?");
        $rRepro->execute(array($_GET['repro_id']));
        $repro = $rRepro->fetch(PDO::FETCH_ASSOC);
        $consulta = $db->prepare("SELECT hc_cariotipo.*, restricciones.tipo_vencimiento_donante, restricciones.vencimiento_donante,
            CASE
                WHEN restricciones.tipo_vencimiento_donante = 'dias' AND CAST(fvigencia as date) >= CAST(? as date) - (restricciones.vencimiento_donante + 12) * INTERVAL '1 DAY' THEN false
                WHEN restricciones.tipo_vencimiento_donante = 'no_vence' THEN false
                ELSE true
            END as vencido, CASE p.nom IS NOT NULL WHEN true THEN 1 ELSE 0 END es_paciente
            FROM hc_cariotipo
            LEFT JOIN hc_paciente p on p.dni = hc_cariotipo.iduserupdate
            INNER JOIN restricciones ON restricciones.nombre = 'cariotipo'
            WHERE hc_cariotipo.estado = 1 and numerodocumento = ?
            ORDER BY hc_cariotipo.id DESC");
        $consulta->execute(array($repro['fec'], $dni));
        $data = $consulta->fetch(PDO::FETCH_ASSOC);
    } else {
        print("No seleccionó a ningún paciente");
        exit();
    }
    // guardar datos
    if ( isset($_POST['dni']) && !empty($_POST['dni']) && isset($_POST['agregar']) && !empty($_POST['agregar']) && isset($_POST['fvigencia']) && !empty($_POST['fvigencia']) && isset($_FILES['informe']) && !empty($_FILES['informe']) ) {
        update_cariotipo(0, $_POST['dni'], $_POST['fvigencia'], $_FILES['informe'], $_POST['obs'], $login);
        $stmt = $db->prepare("SELECT * from hc_paciente_accesos where dni=? and estado=1");
        $stmt->execute(array($login));

        if ($stmt->rowCount() != 0) {
            header("Location: paci_reproduccion.php?id=" . $_GET['repro_id']);
        } else {
            /* header("Location: e_repro_02.php?id=" . $_GET['repro_id']); */
            header("Refresh:0");
        }
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
    $user = $rUser->fetch(PDO::FETCH_ASSOC); ?>
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
            $stmt = $db->prepare("SELECT * FROM hc_paciente_accesos WHERE dni=? AND estado=1");
            $stmt->execute(array($login));

            if ($stmt->rowCount() != 0) {
                $es_paciente = 1;
                print('<a href="paci_reproduccion.php?id=' . $_GET['repro_id'] . '" rel="external" class="ui-btn">Cerrar</a>');
            } else {
                $es_paciente = 0;
                print('<a href="e_repro_02.php?id=' . $_GET['repro_id'] . '" rel="external" class="ui-btn">Cerrar</a>');
            } ?>
            <h1>Documento Cariotipo: <small><?php echo mb_strtoupper($paci['ape'])." ".mb_strtoupper($paci['nom']) ?></small></h1>
        </div>

        <div role="main" class="ui-content">
            <?php if ( !$data || !!@$data['vencido'] ): ?>
                <div class="alarma vencida">
                    <span class="oi" data-glyph="warning"></span>
                    
                    <?php if ( !$data ): ?>
                        No hay documento de cariotipo
                    <?php else: ?>
                        Documento vencido, reemplazarlo con documento vigente
                    <?php endif ?>
                </div>
            <?php endif ?>
            <form action="" method="post" enctype="multipart/form-data" data-ajax="false" id="form1">
                <input type="hidden" name="dni" value="<?php echo $dni; ?>">
                <div class="ui-grid-a">
                    <div class="ui-block-a">
                        <div class="ui-bar" data-role="fieldcontain">
                            <span class="input-group-addon">F. Informe*</span>
                            <input class="form-control" name="fvigencia" type="date" value="<?php print($data["fvigencia"]); ?>" data-mini="true" required>
                        </div>
                    </div>
                    <div class="ui-block-b">
                        <div class="ui-bar" data-role="fieldcontain">
                            <span class="input-group-addon">Informe* (PDF)</span>
                            <input class="form-control" name="informe" type="file" id="informe" accept="application/pdf" data-mini="true" required/>
                        </div>
                    </div>
                </div>
                <div class="row pb-2">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="input-group">
                            <span class="input-group-addon">Observación</span>
                            <textarea class="form-control" name="obs" data-mini="true"><?php print($data["obs"]); ?></textarea>
                        </div>
                    </div>
                </div>
                <?php
                    if ($consulta->rowCount() == 1) {
                        print ('<span class="color_red">Ver/ descargar Informe: </span><a href="cariotipo/'.$dni.'/'.$data["nombre"].'" target="_blank"><img src="_images/pdf.png" height="20" width="20" alt="icon name"></a>
                            <span style="float: right;">Usuario: '.$data['idusercreate'].'</span>');
                    }
                ?>
                
                <div class="row pb-2">
                    <div class="col-12 col-sm-12 col-md-12 text-center">
                        <?php
                        $accion="";
                        if ($consulta->rowCount() == 1) {
                            $accion="Reemplazar";
                        } else {
                            $accion="Guardar";
                        }
                        print('<input type="Submit" class="ui-btn ui-btn-inline ui-mini" name="agregar" value="'.$accion.'" data-mini="true"/>');
                        
                        ?>
                    </div>
                </div>
               
            </form>
            <?php
                if ($data["es_paciente"] == 1 && $es_paciente == 0) {
                    print('<form action="restriccion_cariotipo.php?repro_id=' . $_GET['repro_id'] . '&dni='.$_GET['dni'].'" method="post" data-ajax="false" id="cariotipo_confirmar">
                        <input type="hidden" name="cariotipo_confirmacion" id="cariotipo_confirmacion">
                        <input type="hidden" name="cariotipo_id" value="'.$data['id'].'">
                        <div>
                            Este documento ha sido cargado por el paciente, confirme o descarte esta información:<br>
                            <a href="javascript:void(0)" id="cariotipo_aceptar" class="ui-btn ui-btn-inline ui-icon-check ui-btn-icon-left ui-mini" data-mini="true">Aceptar</a>
                            <a href="javascript:void(0)" id="cariotipo_descartar" class="ui-btn ui-btn-inline ui-icon-delete ui-btn-icon-left ui-mini" data-mini="true">Descartar</a>
                        </div>
                    </form>');
                }
            ?>
        </div>
    </div>
    <script src="js/restriccion_cariotipo.js"></script>
</body>
</html>