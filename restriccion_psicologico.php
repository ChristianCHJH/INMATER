<!DOCTYPE HTML>
<html>
    <head>
        <?php
         include 'seguridad_login.php';
        $dni = $dni_mujer = "";
        if (isset($_GET['dni']) && !empty($_GET['dni'])) {
            $dni = $_GET['dni'];
        } ?>
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
                if ($_POST["psicologico_confirmacion"] == "1") {
                    $stmt = $db->prepare("UPDATE hc_analisis SET iduserupdate=? WHERE id=?");
                    $stmt->execute(array($login, $_POST["psicologico_id"]));
                } else {
                    $stmt = $db->prepare("UPDATE hc_analisis SET estado = 0, iduserupdate=? WHERE id=?");
                    $stmt->execute(array($login, $_POST["psicologico_id"]));
                }
            }

            $stmt = $db->prepare("SELECT * FROM hc_paciente_accesos WHERE dni=? AND estado=1");
            $stmt->execute(array($login));

            if ($stmt->rowCount() != 0) {
                $es_paciente = 1;
                print('<a href="paci_reproduccion.php?id=' . $_GET['repro_id'] . '" rel="external" class="ui-btn">Cerrar</a>');
            } else {
                $es_paciente = 0;
                print('<a href="e_repro_02.php?id=' . $_GET['repro_id'] . '" rel="external" class="ui-btn">Cerrar</a>');
            } ?>
            <h1>Exámen psicológico</h1>
        </div>

        <div role="main" class="ui-content">
            <div class="card-body collapse show">
                <?php

                $rRepro = $db->prepare("SELECT * FROM hc_reprod WHERE estado = true and id=?");
                $rRepro->execute(array($_GET['repro_id']));
                $repro = $rRepro->fetch(PDO::FETCH_ASSOC);

                print('<a href="restriccion_psicologico_add.php?repro_id='.$_GET['repro_id'].'&dni=' . $repro['dni'] . '" rel="external" class="ui-btn ui-btn-inline ui-mini" >Agregar</a><br><br>');
                
                /* $receptora = $repro['p_dni_het'] != ''; */
                $receptora = true;

                if ($receptora) :
                    $psicologico = $db->prepare("SELECT hc_analisis.*, restricciones.tipo_vencimiento, restricciones.vencimiento,
                        CASE
                            WHEN restricciones.tipo_vencimiento = 'dias' AND CAST(a_mue as date) >= CAST(? as date) - (restricciones.vencimiento + 12) * INTERVAL '1 DAY' THEN false
                            WHEN restricciones.tipo_vencimiento = 'no_vence' THEN false
                            ELSE true END as vencido, CASE p.nom IS NOT NULL WHEN true THEN 1 ELSE 0 END es_paciente
                        FROM hc_analisis
                        LEFT JOIN hc_paciente p on p.dni = hc_analisis.iduserupdate
                        INNER JOIN restricciones ON restricciones.nombre = 'psicologico'
                        WHERE hc_analisis.estado = 1 AND a_exa = 'Examen Psicologico' AND a_dni=?
                        ORDER BY hc_analisis.id DESC");
                    $psicologico->execute([$repro['fec'], $repro['dni']]);
                    $psicologico = $psicologico->fetch(PDO::FETCH_ASSOC);
                    
                endif;
                ?>
                <?php if( $receptora ): ?>
                <table data-role="table" class="ui-responsive table-stroke">
                    <thead class="thead-dark">
                        <tr>
                            <th width="20%" class="text-center">Prueba</th>
                            <th width="20%">Resultado</th>
                            <th width="25%">Informe</th>
                            <th width="25%">Observación</th>
                            <th width="20%">Fecha</th>
                            <th width="15%" class="text-center">Estado</th>
                            <th width="15%" class="text-center">Usuario</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!!$psicologico): ?>
                            <tr>
                                <td>
                                    Examen psicológico
                                </td>
                                <td>
                                    <?php echo $psicologico['a_sta'] == 'Positivo' ? 'Apto' : '<div class="alarma vencida"><span class="oi" data-glyph="warning"></span> No apto</div>' ?>
                                </td>
                                <td>
                                    <?php $ruta_psicologico = 'archivos_hcpacientes.php?idArchivo='.$psicologico['id'].'_'.$psicologico['a_dni'] ?>
                                    <?php if( file_exists( 'analisis/'.$psicologico['id'].'_'.$psicologico['a_dni'].'.pdf' ) ): ?>
                                        <a href="<?php echo $ruta_psicologico ?>" target="_blank">Ver/Descargar</a>
                                    <?php else: ?>
                                        -
                                    <?php endif ?>
                                </td>
                                <td><?php echo $psicologico['a_obs'] ?></td>
                                <td>
                                    <?php echo $psicologico['a_mue'] ?>
                                </td>
                                <td>
                                    <?php echo !$psicologico['vencido'] ? 'Vigente' : '<b><div class="alarma vencida"><span class="oi" data-glyph="warning"></span> Vencido</div></b>' ?>
                                </td>
                                <td>
                                    <?php echo $psicologico['iduserupdate'] ?>
                                </td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">-</td>
                            </tr>
                        <?php endif ?>
                    </tbody>
                </table>
                <?php
                    if ($psicologico["es_paciente"] == 1 && $es_paciente == 0) {
                        print('<form action="restriccion_psicologico.php?repro_id=' . $_GET['repro_id'] . '" method="post" data-ajax="false" id="psicologico_confirmar">
                            <input type="hidden" name="psicologico_confirmacion" id="psicologico_confirmacion">
                            <input type="hidden" name="psicologico_id" value="'.$psicologico['id'].'">
                            <div>
                                Este documento ha sido cargado por el paciente, confirme o descarte esta información:<br>
                                <a href="javascript:void(0)" id="psicologico_aceptar" class="ui-btn ui-btn-inline ui-icon-check ui-btn-icon-left ui-mini" data-mini="true">Aceptar</a>
                                <a href="javascript:void(0)" id="psicologico_descartar" class="ui-btn ui-btn-inline ui-icon-delete ui-btn-icon-left ui-mini" data-mini="true">Descartar</a>
                            </div>
                        </form>');
                    }
                ?>
                <?php else: echo '<h5>¡Aún no hay exámenes!</h5>' ?>
                <?php endif ?>
            </div>
        </div>
    </div>
    <script src="js/restriccion_psicologico.js"></script>
</body>
</html>