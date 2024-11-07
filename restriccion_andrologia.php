<!DOCTYPE HTML>
<html>
    <head>
        <?php  
        include 'seguridad_login.php';
        $dni=$dni_mujer="";
          if ( isset($_GET['dni']) && !empty($_GET['dni']) ) {
              $dni = $_GET['dni'];
          }

          if (isset($_POST['p_Esp']) && $_POST['p_Esp'] == "Solicitar espermatograma")
            updateAndro_esp('', $_POST['dni'], $_POST['p_dni'], date("Y-m-d"), '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0);
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
                print('<a href="paci_reproduccion.php?id=' . $_GET['repro_id'] . '" rel="external" class="ui-btn">Cerrar</a>');
            } else {
                print('<a href="e_repro_02.php?id=' . $_GET['repro_id'] . '" rel="external" class="ui-btn">Cerrar</a>');
            } ?>
            <h1>Andrología</h1>
        </div>

        <div role="main" class="ui-content">
            <div class="card-body collapse show">
                <form action="" method="post" enctype="multipart/form-data" data-ajax="false" name="form2" novalidate>
                    <?php
                    $rRepro = $db->prepare("SELECT * FROM hc_reprod WHERE estado = true and id=?");
                    $rRepro->execute(array($_GET['repro_id']));
                    $repro = $rRepro->fetch(PDO::FETCH_ASSOC);

                    $tiene_pareja = $repro['p_dni'] != '' && $repro['p_dni'] != '1';

                    if( $tiene_pareja )
                    {
                        $espermatograma = $db->prepare("SELECT lab_andro_esp.*, restricciones.tipo_vencimiento, restricciones.vencimiento,
                        CASE
                            WHEN restricciones.tipo_vencimiento = 'dias' AND CAST(fec as date) >= CAST(? as date) - (restricciones.vencimiento + 12) * INTERVAL '1 DAY' THEN false
                            WHEN restricciones.tipo_vencimiento = 'no_vence' THEN false
                            ELSE true
                        END as vencido
                            FROM lab_andro_esp
                            INNER JOIN restricciones ON restricciones.nombre = 'andrologia' AND restricciones.tipo = 'espermatograma' WHERE p_dni=? ORDER BY fec DESC");
                        $espermatograma->execute([$repro['fec'], $repro['p_dni']]);

                        $espermatograma = $espermatograma->fetch(PDO::FETCH_ASSOC);


                        $espermacultivo = $db->prepare("SELECT hc_analisis.*, restricciones.tipo_vencimiento, restricciones.vencimiento,
                            CASE
                                WHEN restricciones.tipo_vencimiento = 'dias' AND CAST(a_mue as date) >= CAST(? as date) - (restricciones.vencimiento + 12) * INTERVAL '1 DAY' THEN false
                                WHEN restricciones.tipo_vencimiento = 'no_vence' THEN false
                                ELSE true
                            END as vencido
                                FROM hc_analisis
                                INNER JOIN restricciones ON restricciones.nombre = 'andrologia' AND restricciones.tipo = 'espermacultivo' WHERE a_exa = 'ESPERMACULTIVO' AND a_dni=? ORDER BY a_mue DESC");

                        $espermacultivo->execute([$repro['fec'], $repro['p_dni']]);
                        $espermacultivo = $espermacultivo->fetch(PDO::FETCH_ASSOC);

                    }

                    ?>
                    <?php if( $tiene_pareja ): ?>
                    <table data-role="table" class="ui-responsive table-stroke">
                        <thead class="thead-dark">
                            <tr>
                                <th width="20%" class="text-center">Prueba</th>
                                <th width="20%">Resultado</th>
                                <th width="25%">Informe</th>
                                <th width="20%">Fecha</th>
                                <th width="15%" class="text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ( !!$espermatograma ): ?>
                                <tr>
                                    <td>
                                        Espermatograma
                                    </td>
                                    <td>
                                        <b>Vol.:</b> <?php if ($espermatograma['emb'] > 0) echo $espermatograma['macro_volumen'] . 'ml'; ?>
                                        <br>
                                        <b>Con.:</b> <?php if ($espermatograma['emb'] > 0) {
                                                    echo $espermatograma['con_f'];
                                                    if ($espermatograma['espermatograma'] == 1) echo " Spz/Camp"; else echo " x10<sup>6";
                                                } ?>
                                        <br>
                                        <b>Viabi.:</b> <?php if ($espermatograma['emb'] > 0) echo $espermatograma['via'] . '%'; ?>
                                        <br>
                                        <b>Ph:</b> <?php if ($espermatograma['emb'] > 0) echo $espermatograma['macro_ph']; ?>
                                        <br>
                                        <b>Morfo.:</b> <?php if ($espermatograma['emb'] > 0 and $espermatograma['morfo_normal'] > 0) echo round(100 - (
                                            ((100 - $espermatograma['morfo_normal']) * 100) / 
                                            ((100 - $espermatograma['morfo_normal']) + $espermatograma['morfo_normal']))
                                            , 2) . '%'; // % normal = pm_n ?>
                                        <br>
                                        <b>Moti.:</b> <?php if ($espermatograma['emb'] > 0) echo ($espermatograma['movi_mprogresivo'] + $espermatograma['movi_mnoprogresivo']) . '%'; ?>
                                        
                                    </td>
                                    <td>
                                        <a href="le_andro_esp_pdf.php?p_dni=<?php echo $espermatograma['p_dni'] . '&fec=' . $espermatograma['fec']. '&dni=' . $repro['dni'] ?>" target="_blank">Ver/Descargar</a>
                                    </td>
                                    <td>
                                        <?php echo $espermatograma['fec'] ?>
                                    </td>
                                    <td>
                                        <?php echo !$espermatograma['vencido'] ? 'Vigente' : '<b>Vencido</b>' ?>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5">
                                        No tiene espermatograma
                                    </td>
                                </tr>
                            <?php endif ?>
                            <?php if( !!$espermacultivo ): ?>
                                <tr>
                                    
                                    <td>
                                        Espermacultivo
                                    </td>
                                    <td>
                                        <?php echo $espermacultivo['a_sta'] ?>
                                    </td>
                                    <td>
                                        <?php $ruta_espermacultivo = 'archivos_hcpacientes.php?idArchivo='.$espermacultivo['id'].'_'.$espermacultivo['a_dni'] ?>
                                        <?php if( file_exists( "analisis/".$espermacultivo['id'].'_'.$espermacultivo['a_dni'].'.pdf' ) ): ?>
                                            <a href="<?php echo $ruta_espermacultivo ?>" target="_blank">Ver/Descargar</a>
                                        <?php else: ?>
                                            -
                                        <?php endif ?>
                                    </td>
                                    <td>
                                        <?php echo $espermacultivo['a_mue'] ?>
                                    </td>
                                    <td>
                                        <?php echo !$espermacultivo['vencido'] ? 'Vigente' : '<b>Vencido</b>' ?>
                                    </td>
                                </tr>
                            <?php endif ?>
                        </tbody>
                    </table>
                    <?php else: echo '<h5>¡Aún no hay exámenes!</h5>' ?>
                    <?php endif ?>
                </form>
            </div>
        </div>
    </div>

</body>

</html>