<!DOCTYPE HTML>
<html>
    <head>
        <?php 
         include 'seguridad_login.php';
        $dni = $tipoinforme = "";

        if (!!$_POST) {
            if ($_POST["riesgo_confirmacion"] == "1") {
                $stmt = $db->prepare("UPDATE hc_riesgo_quirurgico SET iduserupdate=? WHERE id=?");
                $stmt->execute(array($login, $_POST["riesgo_id"]));
            } else {
                $stmt = $db->prepare("UPDATE hc_riesgo_quirurgico SET estado = 0, iduserupdate=? WHERE id=?");
                $stmt->execute(array($login, $_POST["riesgo_id"]));
            }
        }

        // verificar dni paciente
        if (isset($_GET["dni"]) && !empty($_GET["dni"])) {
            $dni = $_GET["dni"];
            
            $rRepro = $db->prepare("SELECT * FROM  hc_reprod WHERE estado = true and id=?");
            $rRepro->execute(array($_GET['repro_id']));
            $repro = $rRepro->fetch(PDO::FETCH_ASSOC);
            $consulta = $db->prepare("SELECT hc_riesgo_quirurgico.*, restricciones.tipo_vencimiento, restricciones.vencimiento, CASE p.nom IS NOT NULL WHEN true THEN 1 ELSE 0 END es_paciente,
                CASE
                    WHEN restricciones.tipo_vencimiento = 'dias' AND CAST(fvigencia as date) >= CAST(? as date) - (restricciones.vencimiento + 12) * INTERVAL '1 DAY' THEN false
                    WHEN restricciones.tipo_vencimiento = 'no_vence' THEN false
                    ELSE true
                END as vencido
                FROM hc_riesgo_quirurgico
                LEFT JOIN hc_paciente p on p.dni = hc_riesgo_quirurgico.iduserupdate
                INNER JOIN restricciones ON restricciones.nombre = 'riesgo_quirurgico'
                WHERE hc_riesgo_quirurgico.estado = 1 and numerodocumento = ?
                ORDER BY hc_riesgo_quirurgico.id DESC");
            $consulta->execute( array($repro['fec'], $dni) );
            $data = $consulta->fetch(PDO::FETCH_ASSOC);
        } else {
            print("No seleccionó a ningún paciente");
            exit();
        }
        // guardar datos
        if ( isset($_POST['dni']) && !empty($_POST['dni']) && isset($_POST['agregar']) && !empty($_POST['agregar']) && isset($_POST['fvigencia']) && !empty($_POST['fvigencia']) && isset($_FILES['informe']) && !empty($_FILES['informe']) ) {
            update_riesgoquirurgico_01(0, $_POST['dni'], $_POST['fvigencia'], $_FILES['informe'], $_POST['obs'], $_POST['nivel'], $login);
            header("Location: e_repro_02.php?id=" . $_GET['repro_id']);
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
        $user = $rUser->fetch(PDO::FETCH_ASSOC);
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
            $stmt = $db->prepare("SELECT * FROM hc_paciente_accesos WHERE dni=? AND estado=1");
            $stmt->execute(array($login));

            if ($stmt->rowCount() != 0) {
                $es_paciente = 1;
                print('<a href="paci_reproduccion.php?id=' . $_GET['repro_id'] . '" rel="external" class="ui-btn">Cerrar</a>');
            } else {
                $es_paciente = 0;
                print('<a href="e_repro_02.php?id=' . $_GET['repro_id'] . '" rel="external" class="ui-btn">Cerrar</a>');
            } ?>
            <h1>Documento Riesgo Quirúrgico: <small><?php echo mb_strtoupper($paci['ape'])." ".mb_strtoupper($paci['nom']) ?></small></h1>
        </div>

        <div role="main" class="ui-content">
            <?php if ( !$data || !!@$data['vencido'] ): ?>
                <div class="alarma vencida">
                    <span class="oi" data-glyph="warning"></span>
                    
                    <?php if ( !$data ): ?>
                        No hay documento de riesgo quirúrgico
                    <?php else: ?>
                        Documento vencido, reemplazarlo con documento vigente
                    <?php endif ?>
                </div>
            <?php endif ?>
            <a href="restriccion_riesgo_quirurgico_add.php?repro_id=<?php echo $_GET['repro_id'] ?>&dni=<?php echo $_GET['dni'] ?>" rel="external" class="ui-btn ui-btn-inline ui-mini" >Agregar</a><br><br>

            <?php if( $data ): ?>
                    <table data-role="table" class="ui-responsive table-stroke">
                        <thead class="thead-dark">
                            <tr>
                                <th width="15%" class="text-center">Fecha Informe</th>
                                <th width="35%">Nivel</th>
                                <th width="25%">Observación</th>
                                <th width="10%">Estado</th>
                                <th width="15%" class="text-center">Informe</th>
                                <th width="15%" class="text-center">Usuario</th>
                                <?php
                                    if ($user['role'] == 12 || $user['role'] == 15) {
                                ?>
                                    <th width="10%">Operaciones</th>
                                <?php
                                    }
                                ?>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center"><?php echo date("d-m-Y", strtotime($data['fvigencia'])); ?></td>
                                <td><?php echo $data['nivel']; ?></td>
                                <td><?php echo $data['obs']; ?></td>
                                <td><?php echo $data['vencido'] ? '<b>Vencido</b>' : 'Vigente' ?></td>
                                <td class="text-center">
                                    <a href='<?php print("riesgo_quirurgico/" . $_GET['dni'] . "/" . $data['nombre'] ); ?>' target="_blank">Ver/ Descargar</a>
                                </td>
                                <td>
                                    <?php echo $data['iduserupdate'] ?>
                                </td>
                                
                            </tr>
                        </tbody>
                    </table>

                    <?php
                        if ($data["es_paciente"] == 1 && $es_paciente == 0) {
                            print('<form action="restriccion_riesgo_quirurgico.php?repro_id=' . $_GET['repro_id'] . '&dni='.$_GET['dni'].'" method="post" data-ajax="false" id="riesgo_confirmar">
                                <input type="hidden" name="riesgo_confirmacion" id="riesgo_confirmacion">
                                <input type="hidden" name="riesgo_id" value="'.$data['id'].'">
                                <div>
                                    Este documento ha sido cargado por el paciente, confirme o descarte esta información:<br>
                                    <a href="javascript:void(0)" id="riesgo_aceptar" class="ui-btn ui-btn-inline ui-icon-check ui-btn-icon-left ui-mini" data-mini="true">Aceptar</a>
                                    <a href="javascript:void(0)" id="riesgo_descartar" class="ui-btn ui-btn-inline ui-icon-delete ui-btn-icon-left ui-mini" data-mini="true">Descartar</a>
                                </div>
                            </form>');
                        }
                    ?>

                    <?php else: echo '<h5>¡Aún no hay documentos cargados!</h5>' ?>
                    <?php endif ?>
        </div>
    </div>
    <script src="js/restriccion_riesgo.js"></script>
</body>
</html>