<!DOCTYPE HTML>
<html>
    <head>
        <?php 
         include 'seguridad_login.php';
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
                if ($_POST["legal_confirmacion"] == "1") {
                    $stmt = $db->prepare("UPDATE hc_legal_01 SET iduserupdate=? WHERE id=?");
                    $stmt->execute(array($login, $_POST["legal_id"]));
                } else {
                    $stmt = $db->prepare("UPDATE hc_legal_01 SET estado = 0, iduserupdate=? WHERE id=?");
                    $stmt->execute(array($login, $_POST["legal_id"]));
                }
            }

            $stmt = $db->prepare("SELECT * FROM hc_paciente_accesos WHERE dni=? AND estado=1");
            $stmt->execute(array($login));

            if ($stmt->rowCount() != 0) {
                print('<a href="paci_reproduccion.php?id=' . $_GET['repro_id'] . '" rel="external" class="ui-btn">Cerrar</a>');
            } else {
                print('<a href="e_repro_02.php?id=' . $_GET['repro_id'] . '" rel="external" class="ui-btn">Cerrar</a>');
            } ?>
            <h1>Legal</h1>
        </div>

        <div role="main" class="ui-content">
            <div class="card-body collapse show">
                    <?php
                    // datos usuario
                    $rUser = $db->prepare("select role from usuario where estado = 1 and userx=?");
                    $rUser->execute(array($login));
                    $user = $rUser->fetch(PDO::FETCH_ASSOC);

                    // validar usuario paciente
                    if ($rUser->rowCount() == 0) {
                        $stmt = $db->prepare("select id from hc_paciente_accesos where estado = 1 and dni = ?");
                        $stmt->execute(array($login));

                        if ($stmt->rowCount() != 0) {
                            $user['role'] = 18;
                        }
                    }

                    //
                    if ($user['role'] == 1 || $user['role'] == 18) {
                        print('<a href="restriccion_legal_add.php?repro_id='.$_GET['repro_id'].'&dni=' . $dni . '" rel="external" class="ui-btn ui-btn-inline ui-mini" >Agregar</a><br><br>');
                    }

                    $rLegal = $db->prepare("SELECT legal.*, tipo.nombre nombretipodocumento, restricciones.tipo_vencimiento, restricciones.vencimiento, CASE p.nom IS NOT NULL WHEN true THEN 1 ELSE 0 END es_paciente
                        FROM hc_legal_01 legal
                        LEFT JOIN hc_paciente p on p.dni = legal.iduserupdate
                        INNER JOIN man_legal_tipodocumento tipo ON tipo.codigo = legal.idlegaltipodocumento
                        INNER JOIN restricciones ON restricciones.idtipo = CAST(legal.idlegaltipodocumento AS INTEGER)
                        WHERE legal.estado = 1 AND legal.numerodocumento = ? AND restricciones.nombre = 'legal'
                        ORDER BY legal.id DESC");

                    $rLegal->execute([$dni]);
                    $legal = $rLegal->fetch(PDO::FETCH_ASSOC);
                    $tiene_legal = !!$legal;

                    if( $tiene_legal && $legal['tipo_vencimiento'] == 'procedimientos' ){
                        $intervenciones = $db->prepare("SELECT * FROM hc_reprod WHERE estado = true and dni=? and fec >= '". $legal['finforme'] ."'");
                        $intervenciones->execute(array($dni));
                        $tiene_legal = $intervenciones->rowCount() > $legal['vencimiento'] ? false : $legal;
                    }

                    $legal_vencida = (!!$legal && !$tiene_legal) || !$legal
                    ?>
                    <?php if( $tiene_legal ): ?>
                    <table data-role="table" class="ui-responsive table-stroke">
                        <thead class="thead-dark">
                            <tr>
                                <th width="15%" class="text-center">Fecha Informe</th>
                                <th width="35%">Tipo Informe</th>
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
                                <td class="text-center"><?php echo date("d-m-Y", strtotime($legal['finforme'])); ?></td>
                                <td><?php print( mb_strtoupper($legal['nombretipodocumento']) ); ?></td>
                                <td><?php echo $legal['obs']; ?></td>
                                <td><?php echo $legal_vencida ? '<b>Vencido</b>' : 'Vigente' ?></td>
                                <td class="text-center">
                                    <a href='<?php print("legal_01/" . $dni . "/" . $legal['nombre'] ); ?>' target="_blank">Ver/ Descargar</a>
                                </td>
                                <td>
                                    <?php echo $legal['iduserupdate'] ?>
                                </td>
                                <?php
                                if ($user['role'] == 12 || $user['role'] == 15) {
                                    print("<td class='text-center'><img src='_libraries/open-iconic/svg/trash.svg' height='18' width='18' alt='icon name' class='btn_eliminar_informe' data-origen='legal' data-informe='".$legal["id"]."'></td>");
                                }
                                ?>
                            </tr>
                        </tbody>
                    </table>
                    
                    <?php
                        if ($legal["es_paciente"] == 1 && $user['role'] != 18) {
                            print('<form action="restriccion_legal.php?repro_id=' . $_GET['repro_id'] . '&dni='.$_GET['dni'].'" method="post" data-ajax="false" id="legal_confirmar">
                                <input type="hidden" name="legal_confirmacion" id="legal_confirmacion">
                                <input type="hidden" name="legal_id" value="'.$legal['id'].'">
                                <div>
                                    Este documento ha sido cargado por el paciente, confirme o descarte esta información:<br>
                                    <a href="javascript:void(0)" id="legal_aceptar" class="ui-btn ui-btn-inline ui-icon-check ui-btn-icon-left ui-mini" data-mini="true">Aceptar</a>
                                    <a href="javascript:void(0)" id="legal_descartar" class="ui-btn ui-btn-inline ui-icon-delete ui-btn-icon-left ui-mini" data-mini="true">Descartar</a>
                                </div>
                            </form>');
                        }
                    ?>
                    <?php else: echo '<h5>¡Aún no hay documentos cargados!</h5>' ?>
                    <?php endif ?>
                </div>
        </div>
    </div>
    <script src="js/restriccion_legal.js"></script>
</body>
</html>