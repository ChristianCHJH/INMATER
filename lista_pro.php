<!DOCTYPE HTML>
<html>

<head>
    <?php
   include 'seguridad_login.php'
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="_themes/tema_inmater.min.css" />
    <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css" />
    <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css" />
    <link rel="stylesheet" href="css/lista_pro.css" />
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
    <script>
    $(document).ready(function() {

        $(".traslada").change(function() {
            var num = $(this).attr("title");
            $('#c').val(num);

            if ($('#i' + num).val() != "" && $('#pro_tra' + num).val() != "" && $('#fec_tra' + num).val() != "" && $('#cri' + num).val() != "") {
                if (confirm("Presione ACEPTAR para proceder con el traslado, los cambios seran IRREVERSIBLES por Ud.")) {
                    $("#cargador").popup("open", {
                        positionTo: "window"
                    });
                    $('#form1').submit();
                }
            } else {
                alert("Debe ingresar: \n - Número de Traslado \n - Fecha de Traslado \n - Ovulos o Embriones Criopreservados \n - Informe en PDF");
            }
        });
    });
    </script>
</head>

<body>
    <?php
$c = isset($_POST['c']) ? $_POST['c']: "";
if ($c <> "" and $_POST['cri' . $c] <> "" and $_FILES['i' . $c] <> "" and $_POST['tras' . $c] >= 0 and $_POST['pro_tra' . $c] > 0) {
    lab_inserAspiTraslado($_POST['id_tra' . $c], $_POST['dni' . $c], $_POST['cri' . $c], $_POST['tras' . $c], "T" . $_POST['pro_tra' . $c], $_POST['fec_tra' . $c], $_POST['med' . $c], $_FILES['i' . $c]);
}
?>
    <div data-role="page" class="ui-responsive-panel" id="lista">
        <?php
    $rUser = $db->prepare("SELECT role FROM usuario WHERE userx=?");
    $rUser->execute(array($login));
    $user = $rUser->fetch(PDO::FETCH_ASSOC);
    $fe_fin= date("Y-m-d", strtotime('1899-12-30'));
    if ($user['role'] == 2)
        $rRepro = $db->prepare("SELECT *
        FROM (
            SELECT
            hc_paciente.ape, hc_paciente.nom, hc_paciente.san, hc_paciente.don, hc_antece.m_ets, hc_reprod.id, hc_reprod.dni, hc_reprod.p_od, hc_reprod.p_don, hc_reprod.p_dni, hc_reprod.t_mue, hc_reprod.p_dni_het, hc_reprod.f_asp, hc_reprod.des_don, hc_reprod.des_dia, hc_reprod.med
            FROM hc_antece, hc_paciente, hc_reprod, lab_aspira
            WHERE hc_reprod.id=lab_aspira.rep and lab_aspira.estado is true AND lab_aspira.f_fin=? AND hc_paciente.dni = hc_antece.dni AND hc_paciente.dni=hc_reprod.dni
            AND hc_reprod.f_asp <> '' AND hc_reprod.don_todo IS NULL AND coalesce(hc_reprod.cancela, 0) <> 1

            union

            SELECT
            hc_paciente.ape, hc_paciente.nom, hc_paciente.san, hc_paciente.don, hc_antece.m_ets, hc_reprod.id, hc_reprod.dni, hc_reprod.p_od, hc_reprod.p_don, hc_reprod.p_dni, hc_reprod.t_mue, hc_reprod.p_dni_het, hc_reprod.f_asp, hc_reprod.des_don, hc_reprod.des_dia, hc_reprod.med
            FROM hc_reprod
            INNER JOIN hc_paciente on hc_paciente.dni=hc_reprod.dni
            INNER JOIN hc_antece on hc_antece.dni = hc_paciente.dni
            where hc_reprod.estado = true and not exists (select l.pro from lab_aspira l where l.rep = hc_reprod.id and l.estado is true)
            and hc_reprod.f_asp <> '' and hc_reprod.don_todo is null and coalesce(hc_reprod.cancela, 0) <> 1
        ) as a
        order by a.f_asp desc");

    $rRepro->execute([$fe_fin]);

    $a_trata = $db->prepare("SELECT
			hc_paciente.nom, hc_paciente.ape, hc_paciente.med, hc_antece_trata.id, hc_antece_trata.dni, hc_antece_trata.fec, hc_antece_trata.pro, hc_antece_trata.cri
			FROM hc_antece_trata, hc_paciente
			WHERE hc_antece_trata.tras=1 AND hc_antece_trata.eliminado=0 AND hc_paciente.dni=hc_antece_trata.dni
			ORDER BY hc_antece_trata.fec DESC;");
    $a_trata->execute();
    ?>
        <div data-role="header" data-position="fixed">

            <h1>Procedimientos Invitro</h1>

            <?php if ($user['role'] == 2) { ?>
            <div data-role="controlgroup" data-type="horizontal" class="ui-mini ui-btn-left">
                <a href='lista.php' class="ui-btn ui-btn-c ui-icon-home ui-btn-icon-left" rel="external">Inicio</a>
                <a href='lista_pro_8.php' class="ui-btn" rel="external">Próximos</a>
                <a href='lista_pro_f.php' class="ui-btn" rel="external">Finalizados</a>
                <a href='javascript:void(0)' class="ui-btn" id="link_transferencia_betas" rel="external">Transferencia Betas</a>
                <a href='lista_pro_t.php' class="ui-btn" rel="external">Traslados</a>
                <a href='lista_pro_x.php' class="ui-btn" rel="external">Retiros</a>
                <a href='#popupBasic' data-rel="popup" class="ui-btn" data-transition="pop">REDLARA</a>
            </div>
            <div data-role="popup" id="popupBasic" data-arrow="true">
                <ul data-role="listview" data-inset="true">
                    <li><a href='repo_redlara.php' target="_blank" rel="external">Todo</a></li>
                    <li><a href='repo_redlara_fet.php' target="_blank" rel="external">Reporte FET</a></li>
                    <li><a href='repo_redlara_fp.php' target="_blank" rel="external">Reporte FP</a></li>
                    <li><a href='repo_redlara_fresh.php' target="_blank" rel="external">Reporte Fresh</a></li>
                    <li><a href='repo_redlara_fto.php' target="_blank" rel="external">Reporte FTO</a></li>
                    <li><a href='repo_redlara_od.php' target="_blank" rel="external">Reporte OD</a></li>
                </ul>
            </div>
            <?php } ?>

            <a href="salir.php" class="ui-btn-right ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-right ui-icon-power" rel="external">Salir</a>
        </div>

        <div class="ui-content" role="main">

            <form action="lista_pro.php" method="post" enctype="multipart/form-data" data-ajax="false" id="form1">
                <input type="hidden" name="c" id="c">
                <?php if ($a_trata->rowCount() > 0) { ?>
                <div class="scroll_h">
                    <table width="100%" bordercolor="#D76163" style="margin:0 auto;font-size:small;" class="ui-responsive table-stroke">
                        <thead>
                            <tr>
                                <th colspan="12" align="center">TRASLADOS PENDIENTES:</th>
                            </tr>
                            <tr align="center">
                                <th>Apellidos y nombres</th>
                                <th>Fecha de Solicitud</th>
                                <th>Procedimiento</th>
                                <th>Médico</th>
                                <th>Criopreservados</th>
                                <th>Informe (PDF)</th>
                                <th>Nº Traslado</th>
                                <th>Fecha deTraslado</th>
                                <th>Dia Traslado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $c = 0;
                        while ($trata = $a_trata->fetch(PDO::FETCH_ASSOC)) {
                            $c++; ?>
                            <tr align="center">
                                <td><?php echo $trata['ape'] . " " . $trata['nom']; ?></td>
                                <td><?php echo date("d-m-Y", strtotime($trata['fec'])); ?></td>
                                <td><?php echo $trata['pro']; ?></td>
                                <td><input type="text" name="med<?php echo $c; ?>" id="med<?php echo $c; ?>" value="<?php echo $trata['med']; ?>"></td>
                                <td class="peke2">
                                    <input name="cri<?php echo $c; ?>" type="number" step="any" min="0" value="<?php echo $trata['cri']; ?>" data-mini="true"> <input name="dni<?php echo $c; ?>" type="hidden" value="<?php echo $trata['dni']; ?>">
                                    <input name="id_tra<?php echo $c; ?>" type="hidden" value="<?php echo $trata['id']; ?>">
                                </td>
                                <td><input name="i<?php echo $c; ?>" id="i<?php echo $c; ?>" type="file" accept="application/pdf" data-mini="true" /></td>
                                <td><input type="number" name="pro_tra<?php echo $c; ?>" id="pro_tra<?php echo $c; ?>" min="0"></td>
                                <td><input type="date" name="fec_tra<?php echo $c; ?>" id="fec_tra<?php echo $c; ?>"></td>
                                <td><select name="tras<?php echo $c; ?>" class="traslada" title="<?php echo $c; ?>" data-mini="true">
                                        <option value="">---</option>
                                        <option value=0>Dia 0</option>
                                        <option value=2>Dia 2</option>
                                        <option value=3>Dia 3</option>
                                        <option value=4>Dia 4</option>
                                        <option value=5>Dia 5</option>
                                        <option value=6>Dia 6</option>
                                    </select></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <?php } ?>

                <input id="filtro" data-type="search" placeholder="Filtro..">
                <table data-role="table" data-filter="true" data-input="#filtro" class="table-stripe ui-responsive">
                    <thead>
                        <tr>
                            <th align="center">Protocolo</th>
                            <th align="center">Paciente</th>
                            <th align="center">Pareja</th>
                            <th align="center">Donante</th>
                            <th align="center">Médico</th>
                            <th align="center">Fase</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                while ($rep = $rRepro->fetch(PDO::FETCH_ASSOC))
                {
                    $rAspi = $db->prepare("SELECT
                        hc_reprod.dni, pro, sta, tip, vec, dias, fec0, fec1, fec2, fec3, fec4, fec5, fec6, f_fin
                        FROM lab_aspira, hc_reprod
                        WHERE hc_reprod.estado = true and lab_aspira.estado is true and hc_reprod.id=lab_aspira.rep AND hc_reprod.id=?
                        ORDER BY lab_aspira.fec DESC");
                    $rAspi->execute(array($rep['id']));
                    $asp = $rAspi->fetch(PDO::FETCH_ASSOC);
                    //
                    if ((isset($asp['f_fin'])&&$asp['f_fin'] == '1899-12-30') || !isset($asp['pro'])) {
                        $rPare = $db->prepare("SELECT p_nom,p_ape,p_san,p_m_ets FROM hc_pareja WHERE p_dni=?");
                        $rPare->execute(array($rep['p_dni']));
                        $pare = $rPare->fetch(PDO::FETCH_ASSOC);

                        if ($rep['p_dni'] == "" or $rep['p_dni'] == "1") {
                            $pareja = "SOLTERA";
                        } else if(isset($pare['p_ape'] )) {
                            $pareja = $pare['p_ape'] . " " . $pare['p_nom'];
                        }

                        if (isset($asp['pro'])) {
                            // consulta total de ted
                            $consulta = $db->prepare("SELECT
                                r.dni, count(*) totalted
                                from hc_reprod r
                                inner join lab_aspira l on l.rep = r.id and l.estado is true
                                inner join lab_aspira_t t on t.pro = l.pro and t.estado is true
                                group by r.dni");
                            $consulta->execute(/*array($asp['dni'])*/);
                            $data = $consulta->fetch(PDO::FETCH_ASSOC);
                            if ( !isset($data['totalted']) ) {
                                $data['totalted']=0;
                            }
                            $url = "le_aspi" . $asp['dias'] . ".php?id=" . $asp['pro'];
                            // $fondo_rep = '';
                            $Npro = $asp['tip'] . '-' . $asp['pro'] . '-' . $asp['vec'] . '-' . $data['totalted'];
                            $fec_asp = '';
                            $fec = "fec" . ($asp['dias']);
                            $var="1";
                        } else {
                            if ($rep['des_dia'] === 0 or $rep['des_dia'] >= 1) {$dias = 9;} else {$dias = 0;} // si es una descogelacion enlaza a le_aspi9.php
                            $url = "le_aspi" . $dias . ".php?rep=" . $rep['id'];
                            // $fondo_rep = 'data-theme="c"';
                            $Npro = 'Nuevo';
                            $fec_asp = explode("T", $rep['f_asp']);
                            $fec = '';
                        } ?>
                        <?php
                        $color="";
                        if (
                            (isset($asp[$fec]) && $asp[$fec] == date("Y-m-d"))
                            or (isset($fec_asp[0]) && $fec_asp[0] == date("Y-m-d"))
                        ) {
                            $color = 'class="color"';
                        }
                        print("<tr $color>") ?>
                        <th><a href='<?php echo $url; ?>' rel="external"><?php echo $Npro; ?></a></th>
                        <td class="mayuscula"><?php echo $rep['ape'] . ' ' . $rep['nom'] . ' (' . $rep['dni'] . ')'; ?>
                            <?php if (strpos($rep['san'], "-") !== false) echo " <b>(SANGRE NEGATIVA) </b>";
                                if (strpos($rep['m_ets'], "VIH") !== false) echo " <b>(VIH) </b>";
                                if (strpos($rep['m_ets'], "Hepatitis C") !== false) echo " <b>(Hepatitis C) </b>"; ?> </td>
                        <td class="mayuscula"><?php if(isset($pareja))echo $pareja;
                                if(isset($pare['p_san']))if (strpos($pare['p_san'], "-") !== false) echo " <b>(SANGRE NEGATIVA) </b>";
                                if(isset($pare['p_m_ets']))if (strpos($pare['p_m_ets'], "VIH") !== false) echo " <b>(VIH) </b>";
                                if(isset($pare['p_m_ets']))if (strpos($pare['p_m_ets'], "Hepatitis C") !== false) echo " <b>(Hepatitis C) </b>";
                                if(isset($pare['p_dni_het']))if ($rep['p_dni_het'] <> "") echo " <b>(HETEROLOGO) </b>";
                                if ($rep['t_mue'] == 1) echo '<br>(Fresca)';
                                if ($rep['t_mue'] == 2) echo '<br>(Congelada)';
                                if ($rep['t_mue'] == 4) echo '<br>(Banco)'; ?></td>
                        <td class="mayuscula">
                            <?php
                                if (!$rep['p_od'] || $rep['p_od'] <> '')
                                {
                                    $rDon = $db->prepare("SELECT dni, nom, ape FROM hc_paciente WHERE dni=?");
                                    $rDon->execute(array($rep['p_od']));
                                    $don = $rDon->fetch(PDO::FETCH_ASSOC);
                                    echo $don['ape'] . " " . $don['nom'];
                                } else {
                                    if ($rep['don'] == 'D')
                                    {
                                        echo 'Si';
                                    }
                                    else
                                    {
                                        // descongelacion de ovulos donados
                                        if ($rep['des_don'] <> null and $rep['des_dia'] === 0)
                                        {
                                            $consulta = $db->prepare("SELECT
                                                hc_paciente.ape, hc_paciente.nom
                                                from hc_reprod, hc_paciente, lab_aspira, lab_aspira_dias
                                                where hc_reprod.estado = true and lab_aspira.estado is true and hc_reprod.dni = hc_paciente.dni and hc_reprod.id = lab_aspira.rep and lab_aspira.pro=lab_aspira_dias.pro and adju=? and lab_aspira_dias.estado is true
                                                group by hc_paciente.ape, hc_paciente.nom");
                                            $consulta->execute(array($rep['dni']));
                                            if ($consulta->rowCount() > 0) {
                                                $data = $consulta->fetch(PDO::FETCH_ASSOC);
                                                print($data["ape"]." ".$data["nom"]);
                                            } else {
                                                print('No');
                                            }
                                        } else {
                                            // para las ted: buscar od fresco en procedimientos anteriores
                                            if ($rep['des_don'] == null and $rep['des_dia'] >= 1) {
                                                $consulta = $db->prepare("SELECT
                                                p.ape, p.nom
                                                from hc_reprod h
                                                inner join hc_paciente p on p.dni = h.p_od
                                                inner join lab_aspira a on a.rep = h.id and a.estado is true
                                                inner join lab_aspira_dias d on d.pro = a.pro and d.estado is true and d.des <> 1 and (d.adju='' or d.adju=h.dni) and (d.d6f_cic='C' or d.d5f_cic='C' or d.d4f_cic='C' or d.d3f_cic='C' or d.d2f_cic='C')
                                                where hc_reprod.estado = true and h.dni = ? and h.p_od <> '' and h.cancela <> 1");
                                                $consulta->execute(array($rep['dni']));
                                                if ($consulta->rowCount() > 0) {
                                                    $data = $consulta->fetch(PDO::FETCH_ASSOC);
                                                    print($data["ape"]." ".$data["nom"]);
                                                } else {
                                                    $consulta = $db->prepare("SELECT
                                                    p.ape, p.nom
                                                    from hc_reprod h
                                                    inner join hc_paciente p on p.dni = h.des_don
                                                    inner join lab_aspira a on a.rep = h.id and a.estado is true
                                                    inner join lab_aspira_dias d on d.pro = a.pro and d.estado is true and d.des <> 1 and (d.adju='' or d.adju=h.dni) and (d.d6f_cic='C' or d.d5f_cic='C' or d.d4f_cic='C' or d.d3f_cic='C' or d.d2f_cic='C')
                                                    where hc_reprod.estado = true and h.dni = ? and h.des_don is not null and h.des_dia = 0 and h.cancela <> 1");
                                                    $consulta->execute(array($rep['dni']));
                                                    if ($consulta->rowCount() > 0) {
                                                        $data = $consulta->fetch(PDO::FETCH_ASSOC);
                                                        print($data["ape"]." ".$data["nom"]);
                                                    } else {
                                                        print('No');
                                                    }
                                                }
                                            } else {
                                                print('No');
                                            }
                                        }
                                    }
                                } ?>
                        </td>
                        <td><?php echo $rep['med']; ?></td>
                        <td><?php if (isset($asp[$fec]) && $asp[$fec] == "") {
                                    $stado = 'Dia 0';
                                    if ($rep['des_dia'] >= 1) $stado = 'Dia ' . $rep['des_dia'];

                                } else {
                                    if(isset($asp['sta']))$stado = $asp['sta'];
                                }

                                if(isset($stado))echo $stado . ' <i class="color2">';
                                if (!isset($asp[$fec]) || $asp[$fec] == "1899-12-30" || !$asp['pro']) echo date("d-m-Y", strtotime($fec_asp[0])) . ' ' . $fec_asp[1]; else echo date("d-m-Y", strtotime($asp[$fec]));
                                echo '</i>';
                                if ($rep['des_dia'] >= 1) echo " (D)";
                                if ($rep['des_dia'] === 0) echo " (D)";
                                ?></td>
                        </tr>
                        <?php }
                } ?>
                    </tbody>
                </table>
            </form>
            <div data-role="popup" id="cargador" data-overlay-theme="b" data-dismissible="false">
                <p>GUARDANDO DATOS..</p>
            </div>
        </div>
    </div>
    <script>
    $(document).ready(function() {
        $(document).on("click", "#link_transferencia_betas", function() {
            localStorage.setItem('back_url', 'lista_pro.php');
            location.href = "labo-betas-resumen.php";
        });
    });
    </script>
</body>

</html>