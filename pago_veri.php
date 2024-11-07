<!DOCTYPE HTML>
<html>
<head>
   <?php
   include 'seguridad_login.php'
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="_themes/tema_inmater.min.css"/>
    <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css"/>
    <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css"/>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
    <script src="jstickytableheaders.js"></script>
    <style>
        .controlgroup-textinput {
            padding-top: .10em;
            padding-bottom: .10em;
        }

        .enlinea div {
            display: inline-block;
            vertical-align: middle;
        }

        #ser {
            font-size: 12px;
        }
    </style>
</head>

<body>
<div data-role="page" class="ui-responsive-panel" id="pago_veri" <?php if ($_GET['x'] <> 'x') echo 'data-dialog="true"'; ?>>
    <script>
        $(document).ready(function () {
            $('.paci_insert').click(function (e) {
                med = $(this).attr("med");
                dni = $(this).attr("dni");
                window.parent.location.href = "pago_veri.php?x=x&id=" + dni + "&t=" + med;
            });

            $(".table-stripe").stickyTableHeaders(); // Cabecera flotante o fija en la tabla
        });

    </script>
    <?php
    if ($_GET['x'] == 'x') {
        if (isset($_GET['todo']) && $_GET['todo'] == 1) $todo = ''; else $todo = ' LIMIT 100';
        $rRepro = $db->prepare("SELECT hc_paciente.nom,hc_paciente.ape,hc_reprod.id,hc_reprod.fec,hc_reprod.med,hc_reprod.dni,p_dni,p_dni_het,des_dia,des_don,p_cic,p_fiv,p_icsi,p_od,p_don,p_cri,p_iiu,don_todo,f_iny,cancela,p_extras,pago_extras FROM hc_reprod,hc_paciente WHERE hc_reprod.estado = true and hc_reprod.dni=hc_paciente.dni ORDER BY hc_reprod.fec DESC".$todo);
        $rRepro->execute();
    } else {
        $Rpop = $db->prepare("SELECT * FROM recibos WHERE id=? AND tip=?");
        $Rpop->execute(array($_GET['id'], $_GET['t']));
        $pop = $Rpop->fetch(PDO::FETCH_ASSOC);

        $rRepro = $db->prepare("SELECT id,fec,p_dni,p_dni_het,des_dia,des_don,p_cic,p_fiv,p_icsi,p_od,p_don,p_cri,p_iiu,don_todo,f_iny,cancela,p_extras,pago_extras FROM hc_reprod WHERE estado = true and dni=? ORDER BY fec DESC");
        $rRepro->execute(array($pop['dni']));
    } ?>
    <style>
        .ui-dialog-contain {
            max-width: 1200px;
            margin: 1% auto 1%;
            padding: 0;
            position: relative;
            top: -35px;
        }

        .peke2 .ui-input-text {
            width: 60px !important;
        }

        .controlgroup-textinput {
                padding-top: .22em;
                padding-bottom: .22em;
        }
    </style>
    <script>
        $(document).ready(function () {


        });
    </script>

    <div data-role="header">
        <?php
            if (!!$_GET && isset($_GET["path"]) && !empty($_GET["path"])) {
                print('<a href="'.$_GET["path"].'.php" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>');
            } else {
                print('<a href="lista.php" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>');
            }
        ?>
        <h3><?php if ($_GET['x'] == 'x') echo 'ULTIMOS 100 PROCEDIMIENTOS:'; else echo 'VERIFICAR PAGO DE RECIBO: ' . sprintf('%05d', $pop['id']); ?></h3>
        <?php if ($_GET['x'] == 'x') { ?>
        <a href="pago_veri.php?x=x&todo=1" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all">VerTodo</a>
        <?php } ?>
    </div>

    <div class="ui-content" role="main">

        <form action="" method="post" data-ajax="false" id="form2">
            <?php if ($_GET['x'] == 'x') { ?>
                <input id="filtro" data-type="search" placeholder="Filtro..">
            <?php } else { ?>
                <table width="100%" border="0" style="margin: 0 auto; font-size: small;" class="ui-bar-b">
                    <tr>
                        <td width="28%">Paciente: <?php echo $pop['nom']; ?><br>
                            Médico: <?php echo $pop['med']; ?><br>
                            Fecha: <?php echo date("d-m-Y", strtotime($pop['fec'])); ?></td>
                        <td width="72%">
                            Servicios Pagados:<br>
                            <table style="font-size:10px; background-color:#FFFFFF;width:100%;"
                                   class="tablamas2"><?php echo $pop['ser']; ?></table>
                        </td>
                    </tr>
                </table>
            <?php } ?>
            <?php if ($rRepro->rowCount() > 0) { ?>
                <table width="100%" class="table-stripe" style="font-size: small" <?php if ($_GET['x'] == 'x') echo 'data-filter="true" data-input="#filtro"'; ?> >
                    <thead>
                    <tr class="ui-bar-b">
                        <th colspan="11">Reproducciones Realizadas</th>
                    </tr>
                    <tr class="ui-bar-b"><?php if ($_GET['x'] == 'x') echo '<th>APELLIDOS Y NOMBRES</th><th>DNI</th><th>MEDICO</th>'; ?>
                        <th>FECHA</th>
                        <th>PROCEDIMIENTO</th>
                        <th>EXTRAS</th>
                        <th>TRANSFERIDOS</th>
                        <th>CRIO</th>
                        <th>BIOPSIA</th>
                        <th>DETALLE</th>
                        <th>ESTADO</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while ($repro = $rRepro->fetch(PDO::FETCH_ASSOC)) {
                        $crio = '-';
                        $bio = '-';

                        if ($repro['cancela'] == 1) {
                            $estado = 'Cancelado';
                            $informe = '-';
                            $fecha = $repro['fec'];
                        } else if ($repro['cancela'] == 2) {
                            $estado = 'Trasladado';
                            $rAspi = $db->prepare("SELECT pro,fec FROM lab_aspira WHERE lab_aspira.rep=? and lab_aspira.estado is true");
                            $rAspi->execute(array($repro['id']));
                            $aspi = $rAspi->fetch(PDO::FETCH_ASSOC);
                            $informe = '<a href="archivos_hcpacientes.php?idEmb=traslado_' . $aspi['pro'] . '.pdf" target="new">Ver traslados</a>';
                            $fecha = $aspi['fec'];
                        } else {
                            if ($repro['p_iiu'] >= 1) {
                                $Rcap = $db->prepare("SELECT * FROM lab_andro_cap WHERE iiu=? and eliminado is false");
                                $Rcap->execute(array($repro['id']));
                                $cap = $Rcap->fetch(PDO::FETCH_ASSOC);
                                if(isset($cap['emb']))if ($cap['emb'] == 0) $estado = 'En Proceso'; else {
                                    $estado = 'Finalizado';
                                    $informe = '<a href="info.php?t=cap&a=' . $cap['p_dni'] . '&b=' . $cap['id'] . '&c=' . $repro['dni'] . '" target="new">Ver IIU</a>';
                                }
                                $fecha = $repro['fec'];

                            } else {
                                $rAspi = $db->prepare("SELECT pro,f_fin,dias,fec2,fec3,fec4,fec5,fec6,tip,n_ovo,n_ins,fec FROM lab_aspira WHERE lab_aspira.rep=? and lab_aspira.estado is true");
                                $rAspi->execute(array($repro['id']));
                                $aspi = $rAspi->fetch(PDO::FETCH_ASSOC);

                                $fecha = $aspi['fec'] ?? '';
                                if(isset($aspi['f_fin']))if ($aspi['f_fin'] == "1899-12-30") $estado = 'En Laboratorio';
                                if(isset($aspi['f_fin']))if ($aspi['f_fin'] and $aspi['f_fin'] <> "1899-12-30") $estado = 'Finalizado <i class="color2">' . date("d-m-Y", strtotime($aspi['f_fin'])) . '</i>';
                                if ($repro['don_todo'] == 1) $estado = 'Finalizado <i class="color2">' . date("d-m-Y", strtotime($repro['f_iny'])) . '</i>';
                                if(isset($aspi['f_fin']))if (!$aspi['f_fin'] and $repro['don_todo'] <> 1) { $estado = 'En Proceso'; $fecha = $repro['fec']; }

                                $rFalso = $db->prepare("SELECT * FROM hc_reprod_info WHERE pro=?");
                                $rFalso->execute(array($aspi['pro']??''));
                                if ($rFalso->rowCount() == 1) { // verifica si hay un reporte falso -----------------------------------
                                    $falso = $rFalso->fetch(PDO::FETCH_ASSOC);
                                    $tran = $falso['c_t'];
                                    $crio = $falso['c_c'];
                                    $bio = substr_count(strtoupper($falso['bio']), strtoupper("si"));
                                    if ($repro['des_dia'] >= 1) // si es TED o embrioadpocion
                                        $informe = 'Embriones Desvitrificados: ' . $falso['n_ovo'];
                                    else if ($repro['des_dia'] === 0) // si es Descongelacion Ovos
                                        $informe = 'Óvulos Desvitrificados: ' . $falso['n_ovo'];
                                    else
                                        $informe = 'Óvulos aspirados: ' . $falso['n_ovo'];

                                    if (($repro['des_dia'] == null or $repro['des_dia'] === 0) and $aspi['tip'] <> 'T')
                                        $informe .= '<br>Óvulos Inseminados: ' . $falso['ins'];

                                    // $informe = '<a href="info_r.php?a='.$aspi['pro'].'&b='.$id.'&c='.$repro['p_dni'].'" target="new">Ver</a>';

                                } else { // reporte original
                                    $rCrio = $db->prepare("SELECT pro FROM lab_aspira_dias WHERE pro=? and estado is true AND (d6f_cic='C' OR d5f_cic='C' OR d4f_cic='C' OR d3f_cic='C' OR d2f_cic='C' OR d1f_cic='C' OR d0f_cic='C')");
                                    $rCrio->execute(array($aspi['pro']??''));
                                    $crio = $rCrio->rowCount();

                                    $rBio = $db->prepare("SELECT pro FROM lab_aspira_dias WHERE pro=? and estado is true AND (d6d_bio<>0 OR d5d_bio<>0 OR d3c_bio>0)");
                                    $rBio->execute(array($aspi['pro']??''));
                                    $bio = $rBio->rowCount();

                                    $rTran = $db->prepare("SELECT pro FROM lab_aspira_dias WHERE pro=? and estado is true AND (d6f_cic='T' OR d5f_cic='T' OR d4f_cic='T' OR d3f_cic='T' OR d2f_cic='T')");
                                    $rTran->execute(array($aspi['pro']??''));
                                    if ($rTran->rowCount() > 0) {
                                       //xxxxxxx
                                    }
                                    $tran = $rTran->rowCount();

                                    if ($repro['don_todo'] == 1) {
                                        $informe = 'Se donó Todo';
                                        $fecha = $repro['fec'];
                                    }else if ($aspi['pro']??'' > 0) {
                                        if ($repro['des_dia'] >= 1) // si es TED o embrioadpocion
                                            $informe = 'Embriones Desvitrificados: ' . $aspi['n_ovo'];
                                        else if ($repro['des_dia'] === 0) // si es Descongelacion Ovos
                                            $informe = 'Óvulos Desvitrificados: ' . $aspi['n_ovo'];
                                        else if ($aspi['tip'] == 'T') // traslado
                                            $informe = '-';
                                        else
                                            $informe = 'Óvulos aspirados: ' . $aspi['n_ovo'];

                                        if (($repro['des_dia'] == null or $repro['des_dia'] === 0) and $aspi['tip'] <> 'T') {
                                            if ($aspi['n_ins'] == 0) $n_ins = $aspi['n_ovo']; else $n_ins = $aspi['n_ins'];
                                            $informe .= '<br>Óvulos Inseminados: ' . $n_ins;
                                        }

                                        // $informe = '<a href="info_r.php?a='.$aspi['pro'].'&b='.$id.'&c='.$repro['p_dni'].'" target="new">Ver</a>';
                                    } else $informe = '-';
                                }
                            }
                        } ?>
                        <tr>
                            <?php if ($_GET['x'] == 'x') echo '<td>'.$repro['ape'].' '.$repro['nom'].'</td><td>'.$repro['dni'].'</td><td>'.$repro['med'].'</td>'; ?>

                            <td><?php echo date("d-m-Y", strtotime($fecha)); ?></a></td>
                            <td><?php if ($repro['p_cic'] >= 1) echo "Ciclo Natural<br>";
                                if ($repro['p_fiv'] >= 1) echo "FIV<br>";
                                if ($repro['p_icsi'] >= 1) echo $_ENV["VAR_ICSI"] . "<br>";
                                if ($repro['p_od'] <> '') echo "OD Fresco<br>";
                                if ($repro['p_cri'] >= 1) echo "Crio Ovulos<br>";
                                if ($repro['p_iiu'] >= 1) echo "IIU<br>";
                                if ($repro['p_don'] == 1) echo "Donación Fresco<br>";
                                if ($repro['des_don'] == null and $repro['des_dia'] >= 1) echo "TED<br>";
                                if ($repro['des_don'] == null and $repro['des_dia'] === 0) echo "<small>Descongelación Ovulos Propios</small><br>";
                                if ($repro['des_don'] <> null and $repro['des_dia'] >= 1) echo "EMBRIODONACIÓN<br>";
                                if ($repro['des_don'] <> null and $repro['des_dia'] === 0) echo "<small>Descongelación Ovulos Donados</small><br>"; ?></td>
                            <td><?php if ($repro['pago_extras'] == '') echo '-'; else echo $repro['pago_extras']; ?></td>
                            <td align="center"><?php echo $tran; ?></td>
                            <td align="center"><?php echo $crio; ?></td>
                            <td align="center"><?php echo $bio; ?></td>
                            <td><?php echo $informe; ?></td>
                            <td><?php if(isset($estado))echo $estado; ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            <?php } else if ($_GET['x'] == 'x') echo ''; else echo '<p><h3>¡ No tiene Reproducciones Asistidas !</h3></p>'; ?>

        </form>

    </div><!-- /content -->

</div><!-- /page -->

</body>
</html>