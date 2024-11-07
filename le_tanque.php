<?php
ini_set("display_errors", "1");
error_reporting(E_ALL);
// error_reporting(error_reporting() & ~E_NOTICE);
require("_database/db_tools.php");
$salida = "";

if (isset($_POST['t']) && !empty($_POST['t'])) {
    $rTan = $db->prepare("SELECT * FROM lab_tanque WHERE tan=?");
    $rTan->execute(array($_POST['t']));
    $tan = $rTan->fetch(PDO::FETCH_ASSOC);

    echo "<option value='' selected>SELECCIONAR</option>";
    for ($c = 1; $c <= $tan['n_c']; $c++) {
        $rRes = $db->prepare("SELECT * FROM lab_tanque_res WHERE t=? AND c=? AND sta=?");
        $rRes->execute(array($_POST['t'], $c, ""));

        // imprime canister con espacio disponible
        if ($rRes->rowCount() > 0) echo "<option value=" . $c . " id='" . $tan['tan'] . "|" . $c . "|" . $tan['n_v'] . "|" . $tan['n_p'] . "' >" . $c . "</option>";
        else echo "<option value=" . $c . " id='" . $tan['tan'] . "|" . $c . "|" . $tan['n_v'] . "|" . $tan['n_p'] . "' >" . $c . " (Lleno)</option>";
    }
    echo $salida;
}

if (isset($_POST['c']) && !empty($_POST['c']) && isset($_POST['p_dni']) && !empty($_POST['p_dni'])) {

    $tan = explode("|", $_POST['c']);

    $t = $tan[0];
    $c = $tan[1];
    $v = $tan[2];
    $p = $tan[3];
    $count = 1;

    $rRes = $db->prepare("SELECT * FROM lab_tanque_res WHERE t=? AND c=? order by t, c, v, p;");
    $rRes->execute(array($t, $c));

    if ($_POST['tip'] == 3 or $_POST['tip'] == 4 or ($_POST['tip'] == 0 and $_POST['tip_id'] == "x")) {
        echo '<p class="ui-bar-b">MARQUE aquí las pajuelas para criopreservar:</p>';
        $pos = "pajuela";
        $col = "Gobelet";
    } else {
        echo '<p class="ui-bar-b">MARQUE aquí los viales para criopreservar:</p>';
        $pos = "vial";
        $col = "Varilla";
    }

    $n_pos = '';

    if ($p == 5) $n_pos = '<td>' . $pos . ' 5</td>';
    if ($p == 6) $n_pos = '<td>' . $pos . ' 5</td><td>' . $pos . ' 6</td>';

    echo '<table bordercolor="#72a2aa" style="text-align:center" class="tablex"><tr><td>' . $col . '</td><td>' . $pos . ' 1</td><td>' . $pos . ' 2</td><td>' . $pos . ' 3</td><td>' . $pos . ' 4</td>' . $n_pos . '</tr> <tr><td>' . $count . '</td>';


    if ($_POST['tip_id'] == "") $disabled = "disabled";
    else $disabled = ""; // tip_id=="" cuando es un nuevo ingreso, desabilita las posiciones

    $ovo_emb = "";

    while ($res = $rRes->fetch(PDO::FETCH_ASSOC)) {
        // de otro paciente
        if ($res['sta'] <> "" and $res['sta'] <> $_POST['p_dni']) {
            echo '<td class="chk_otro"><input type="checkbox" name="xxx" checked disabled><small style="color:white;">' . $res['sta'] . '</small></td>';
        }

        // 1=bio_tes 2=crio_sem 3=embrio 4=ovo 0=descongelados
        if ($_POST['tip'] <> 0) {
            if ($res['sta'] <> "" and $res['sta'] == $_POST['p_dni'] and $res['tip'] == 1) {    // bio_tes
                if ($_POST['tip_id'] == "" or $_POST['tip_id'] <> $res['tip_id'] or $_POST['tip'] <> 1) { // mismo paciente pero diferente tipo o id
                    echo '<td class="chk_bio"><input type="checkbox" name="xxx" value=1 checked disabled></td>';
                } else {
                    echo '<input type="hidden" name="' . $res['v'] . '_' . $res['p'] . '" value=0>';
                    echo '<td class="chk_bio"><input type="checkbox" name="' . $res['v'] . '_' . $res['p'] . '" value=1 checked ' . $disabled . '></td>';
                }
            }
            if ($res['sta'] <> "" and $res['sta'] == $_POST['p_dni'] and $res['tip'] == 2) {    // crio_sem
                if ($_POST['tip_id'] == "" or $_POST['tip_id'] <> $res['tip_id'] or $_POST['tip'] <> 2) { // mismo paciente pero diferente tipo o id
                    echo '<td class="chk_crio"><input type="checkbox" name="xxx" value=1 checked disabled></td>';
                } else {
                    echo '<input type="hidden" name="' . $res['v'] . '_' . $res['p'] . '" value=0>';
                    echo '<td class="chk_crio"><input type="checkbox" name="' . $res['v'] . '_' . $res['p'] . '" value=1 checked ' . $disabled . '></td>';
                }
            }
            if ($res['sta'] == "") {
                echo '<input type="hidden" name="' . $res['v'] . '_' . $res['p'] . '" value=0>';
                echo '<td class="chk_free"><input type="checkbox" name="' . $res['v'] . '_' . $res['p'] . '" value=1>' . $ovo_emb . '</td>';
            }
        } else { // Si es un proceso de descongelacion
            if ($res['sta'] <> "" and $res['sta'] == $_POST['p_dni'] and $res['tip'] == 1) {    // bio_tes
                //echo '<input type="hidden" name="'.$res['v'].'_'.$res['p'].'" value=1>';
                echo '<td class="chk_bio"><input type="checkbox" name="' . $res['v'] . '_' . $res['p'] . '" value="1|' . $res['tip_id'] . '" checked></td>';
            }
            if ($res['sta'] <> "" and $res['sta'] == $_POST['p_dni'] and $res['tip'] == 2) {    // crio_sem
                // echo '<input type="hidden" name="'.$res['v'].'_'.$res['p'].'" value=2>';
                echo '<td class="chk_crio"><input type="checkbox" name="' . $res['v'] . '_' . $res['p'] . '" value="2|' . $res['tip_id'] . '" checked></td>';
            }
            if ($res['sta'] == "") {
                echo '<td class="chk_free"><input type="checkbox" name="xxx" value=1 checked disabled></td>';
            }
        }

        if ($res['p'] == $p) {
            $count++;
            echo '</tr><tr><td>' . $count . '</td>';
        } // Salto de varilla
    }


    echo '</tr></table><input type="hidden" name="v_p" value="' . $v . '|' . $p . '">';
}

// descongelacion (capacitacion)
if (isset($_POST['d']) && !empty($_POST['d'])) {
    if (isset($_POST['het']) && $_POST['het'] == 1) {
        $rHete = $db->prepare("SELECT p_nom,p_ape FROM hc_pareja WHERE p_dni=?");
        $rHete->execute(array($_POST['d']));
        $het = $rHete->fetch(PDO::FETCH_ASSOC);
        echo 'DONANTE:<b> ' . $het['p_ape'] . ' ' . $het['p_nom'] . '</b>';
    }

    echo '<p class="ui-bar-b">MARQUE aquí los viales para descongelar:</p>';
    echo '<table bordercolor="#72a2aa" style="text-align:center">';
    echo '<tr class="ui-bar-b"> <td colspan="6">Viales Criopreservados:</td></tr>';
    echo '<tr class="ui-bar-c"><td>T</td><td>C</td><td>Vial</td><td>Pajuela</td><td>Tipo</td><td>Fecha</td></tr>';

    $rRes = $db->prepare("SELECT
        t.tan tanque_id, t.n_tan t, r.c, r.v, r.p, r.tip, r.tip_id
        from lab_tanque_res r
        inner join lab_tanque t on t.tan = r.t
        where r.sta = ?
        order by r.t, r.c, r.v, r.p;");
    $rRes->execute(array($_POST['d']));

    $c = 0;
    while ($res = $rRes->fetch(PDO::FETCH_ASSOC)) {
        $c++;
        echo '<tr>';
        echo '<td>' . $res['t'] . '</td>';
        echo '<td>' . $res['c'] . '</td>';

        if ($res['tip'] == 1) $bg = "#E99885";
        else $bg = "#9AC2F1";

        echo '<td bgcolor="' . $bg . '">' . $res['v'] . '</td>';
        echo '<td bgcolor="' . $bg . '">' . $res['p'] . '</td>';
        echo '<td><input type="checkbox" name="c' . $c . '" value="' . $res['tanque_id'] . '-' . $res['c'] . '-' . $res['v'] . '-' . $res['p'] . '" id="' . $res['tip'] . '|' . $res['tip_id'] . '" class="deschk">';

        if ($res['tip'] == 1) echo "Biop.";
        else echo "Crio.";

        echo '</td>';
        echo '<td>' . date("d-m-Y", strtotime($res['tip_id'])) . '</td>';
        echo '</tr>';
    }
    echo '</table>';
    echo '<input type="hidden" name="cont" value=' . $c . ' >';
}

if (isset($_POST['e']) && !empty($_POST['e']) && isset($_POST['f']) && !empty($_POST['f']) && isset($_POST['p_dni']) && !empty($_POST['p_dni'])) { // --- muestra los valores de crio o bio en capacitacion ---------

    if ($_POST['e'] == 1) {
        $Rpop = $db->prepare("SELECT vol,con_f,esp,pl_f,pnl_f,ins_f,inm_f FROM lab_andro_bio_tes WHERE p_dni=? AND fec=?");
        $Rpop->execute(array($_POST['p_dni'], $_POST['f']));
        $pop = $Rpop->fetch(PDO::FETCH_ASSOC);
        echo $pop['vol'] . "|" . $pop['con_f'] . "|" . $pop['esp'] . "|" . $pop['pl_f'] . "|" . $pop['pnl_f'] . "|" . $pop['ins_f'] . "|" . $pop['inm_f'];
    }

    if ($_POST['e'] == 2) {
        $Rpop = $db->prepare("SELECT vol_f,con_f,pl_f,pnl_f,ins_f,inm_f FROM lab_andro_crio_sem WHERE p_dni=? AND fec=?");
        $Rpop->execute(array($_POST['p_dni'], $_POST['f']));
        $pop = $Rpop->fetch(PDO::FETCH_ASSOC);
        echo $pop['vol_f'] . "|" . $pop['con_f'] . "|0|" . $pop['pl_f'] . "|" . $pop['pnl_f'] . "|" . $pop['ins_f'] . "|" . $pop['inm_f'];
    }
}

if (isset($_POST['h']) && !empty($_POST['h']) && isset($_POST['dni']) && !empty($_POST['dni']) && isset($_POST['paci']) && !empty($_POST['paci']) && isset($_POST['btn_guarda']) && !empty($_POST['btn_guarda'])) { // muestra ovos o embrio para Reserva ------------------------
    $rAsp = $db->prepare("SELECT
        lab_aspira.pro, lab_aspira.fec, lab_aspira.tip, hc_reprod.p_dni
        FROM hc_reprod, lab_aspira
        WHERE hc_reprod.estado = true and hc_reprod.id=lab_aspira.rep and lab_aspira.estado is true AND lab_aspira.dni=?;");
    $rAsp->execute(array($_POST['dni']));

    if ($rAsp->rowCount() > 0) {
        $c = 0;
        $vacio = 0;

        if ($_POST['h'] == 2 or $_POST['h'] == 4) $ovo_emb = 'Ovulos';
        if ($_POST['h'] == 1 or $_POST['h'] == 3) $ovo_emb = 'Embriones';
        echo '<p class="ui-bar-b" align="center">Seleccione los ' . $ovo_emb . ' para reservarlos:</p>';
        while ($asp = $rAsp->fetch(PDO::FETCH_ASSOC)) {
            if ($_POST['h'] == 1 or $_POST['h'] == 3) {
                $rRes = $db->prepare("SELECT lad.*, teo.id as idestadoebrio, teo.nombre
                FROM lab_aspira_dias lad
                inner join lab_aspira las on las.pro = lad.pro and las.estado is true
                inner join hc_reprod hcr on hcr.id = las.rep
                inner join tblestado_embrio_ovo teo on teo.id = lad.id_estado
                WHERE (hcr.cancela <> 5 or hcr.cancela is null) and lad.pro=? AND lad.des<>1 AND (lad.adju is null OR lad.adju='' OR lad.adju=?) AND (lad.d6f_cic='C' OR lad.d5f_cic='C' OR lad.d4f_cic='C' OR lad.d3f_cic='C' OR lad.d2f_cic='C') and lad.estado is true
                ORDER BY lad.ovo;");
            }
            if ($_POST['h'] == 2 or $_POST['h'] == 4) {
                $rRes = $db->prepare("SELECT lad.*, teo.id as idestadoebrio, teo.nombre
                FROM lab_aspira_dias lad
                inner join tblestado_embrio_ovo teo on teo.id = lad.id_estado
                WHERE lad.pro=? AND lad.des<>1 AND (lad.adju is null OR lad.adju='' OR lad.adju=?) AND lad.d0f_cic='C' and lad.estado is true
                ORDER BY lad.ovo;");
            }
            $rRes->execute(array($asp['pro'], $_POST['paci']));
                if ($rRes->rowCount() > 0) {
                    $ubica = '';
                    echo '
                <span class="ui-bar-b">Protocolo: ' . $asp['pro'] . ' (' . date("d-m-Y", strtotime($asp['fec'])) . ')</span>
                <a href="info_r.php?a=' . $asp['pro'] . '&b=' . $_POST['dni'] . '&c=' . $asp['p_dni'] . '" target="_blank">Ver Informe</a>
                <br><span class="ui-bar-b">VIDEO Embryos:</span>';
                    if (file_exists("emb_pic/embryoscope_" . $asp['pro'] . ".mp4")) {
                        echo "<a href='archivos_hcpacientes.php?idEmp=embryoscope_" . $asp['pro'] . ".mp4' target='_blank'>(VER)</a>";
                        $embry_requrid = '';
                    } else {
                        print('No se a cargado.');
                    }
                    print('<table cellpadding="5" style="text-align:center;font-size: small;border-collapse: collapse;border: 2px solid #72a2aa;">');
                    if ($_POST['h'] == 1 or $_POST['h'] == 3) {
                        print('
									<tr style="border: 1px solid #72a2aa;">
											<th></th>
											<th colspan="5" style="border: 1px solid #72a2aa;">Día 5</th>
											<th colspan="5" style="border: 1px solid #72a2aa;">Día 6</th>
											<th colspan="5"></th>
									</tr>');
                    }
                    print(
                        '<tr style="border: 1px solid #72a2aa;">
									<th>ID ' . $ovo_emb . '</th>'
                    );
                    if ($_POST['h'] == 1 or $_POST['h'] == 3) {
                        print('
										<!--<th>DIA 2</th>
										<th>DIA 3</th>
										<th>DIA 4</th>-->
										<th>Células</th><th>MCI</th><th>TROF.</th><th>KID/IDA Score</th><th>CONTRACCIÓN</th>
										<th>Células</th><th>MCI</th><th>TROF.</th><th>KID/IDA Score</th><th>CONTRACCIÓN</th>
										<th>NGS</th><th>Mitoscore</th><th>Prioridad<br>transferencia</th>');
                    }

                    print('<th>PAJUELA</th><th>RESERVAR</th><th>ESTADO</th></tr>');

                    while ($res = $rRes->fetch(PDO::FETCH_ASSOC)) {
                        $c++;
                        if ($ubica == $res['t'] . '-' . $res['c'] . '-' . $res['g'] . '-' . $res['p']) $borde = '';
                        else $borde = 'style="border-top: 1px solid #72a2aa;"';
                        echo '<tr ' . $borde . '><td>' . $res['ovo'] . '</td>';

                        if ($_POST['h'] == 1 or $_POST['h'] == 3) {
                            if ($res['d5f_cic'] <> '') {
                                if ($res['d5col'] == 1) {
                                    $res['d5col'] = "Si";
                                } else {
                                    $res['d5col'] = "No";
                                }
                                if ($res['d6col'] == 1) {
                                    $res['d6col'] = "Si";
                                } else {
                                    $res['d6col'] = "No";
                                }
                                $kidscore5 = 0;
                                if ($res['d5kid_tipo'] != 0) {
                                    $kidscore5 = $res['d5kid_decimal'];
                                } elseif ($res['d5kid_tipo'] == 0) {
                                    $kidscore5 = $res['d5kid'];
                                }
                                print('<td>' . $res['d5cel'] . ' </td><td> ' . $res['d5mci'] . ' </td><td> ' . $res['d5tro'] . ' </td><td> ' . $kidscore5 . ' </td><td> ' . $res['d5col'] . '</td>');
                                if ($res['d5f_cic'] == 'C') $des_dia = 5;
                            } else print('<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>');
                            if ($res['d6f_cic'] <> '') {
                                $kidscore6 = 0;
                                if ($res['d6kid_tipo'] != 0) {
                                    $kidscore6 = $res['d6kid_decimal'];
                                } elseif ($res['d6kid_tipo'] == 0) {
                                    $kidscore6 = $res['d6kid'];
                                }
                                print('<td>' . $res['d6cel'] . ' </td><td> ' . $res['d6mci'] . ' </td><td> ' . $res['d6tro'] . ' </td><td> ' . $kidscore6 . ' </td><td> ' . $res['d6col'] . '</td>');
                                if ($res['d6f_cic'] == 'C') $des_dia = 6;
                            } else print('<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>');
                            //ngs
                            $ngs = '-';
                            $ngs3 = '';
                            if ($res['ngs1'] == 1) $ngs = 'Normal';
                            if ($res['ngs1'] == 2) $ngs = '<font color="red">Anormal</font>';
                            if ($res['ngs1'] == 3) $ngs = 'NR';
                            if ($res['ngs1'] == 4) $ngs = '<font color="red">Mosaico</font>';
                            if ($res['ngs3'] == 1) $ngs3 = ' (H)';
                            if ($res['ngs3'] == 2) $ngs3 = ' (M)';
                            if ($res['ngs3'] == 3) $ngs3 = ' -';
                            echo '<td><a href="archivos_hcpacientes.php?idArchivo=ngs_' . $asp['pro'] . '" target="new">' . $ngs . $ngs3 . '</a></td>';
                            echo '<td>' . (!!$res['valores_mitoscore'] ? $res['valores_mitoscore'] : '-') . '</td>';
                            echo '<td>' . (!!$res['prioridad_transferencia'] ? $res['prioridad_transferencia'] : '-') . '</td>';
                        }

                        if ($res['d0f_cic'] == 'C') {
                            $des_dia = 0;
                        }

                        $ubica = $res['t'] . '-' . $res['c'] . '-' . $res['g'] . '-' . $res['p'];
                        echo '<td>' . $ubica . '</td>';
                        $estadoEmbrio = '';
                        if ($res['adju'] == '') $check = '';
                        else $check = 'checked';
                        if ($res['id_estado'] != 3) $disabled = '';
                        else $disabled = ' disabled';
                        if ($res['idestadoebrio'] == 3) $estadoEmbrio = '<td style="color:red"><strong>' . strtoupper($res['nombre']) . '</strong></td>';
                        else $estadoEmbrio = '<td>' . $res['nombre'] . '</td>';
                        echo '<input type="hidden" name="c' . $c . '" value="' . $asp['pro'] . '|' . $res['ovo'] . '">';
                        echo '<td><input type="checkbox" name="adju' . $c . '" id="' . $des_dia . '" class="deschk" value="' . $_POST['paci'] . '" data-mini="true" ' . $check . $disabled . '></td>' . $estadoEmbrio . '</tr>';
                    }
                    echo '</table>';
                } else $vacio++;
        }
        if ($vacio == $rAsp->rowCount()) {
            echo '<p>Sin ' . $ovo_emb . '</p>';
        } else {
            echo '<input type="hidden" name="cont" value="' . $c . '">';
            // 1=interface del medico 2=reserva de embiones ovulos
            if ($_POST['btn_guarda'] == 1) {
                echo '<input type="hidden" name="des_dia" id="des_dia">';
                if ($_POST['h'] == 1 or $_POST['h'] == 2) echo '<input type="hidden" name="des_don" id="des_don" value="' . $_POST['dni'] . '">'; // ovo/embrio donados
                echo '<textarea name="obs" id="obs" placeholder="Observaciones"></textarea>';
            } else {
                echo '<input type="submit" name="guardar" value="GUARDAR" data-icon="check" data-iconpos="left" data-mini="true" data-theme="b" data-inline="true"/><b class="color">Debe reservar los ' . $ovo_emb . ' agrupados por PAJUELA</b>';
            }
        }
    } else echo '<p>Sin Procedimientos</p>';
}

if (isset($_POST['h']) && !empty($_POST['h']) && isset($_POST['dni']) && !empty($_POST['dni']) && isset($_POST['paci']) && !empty($_POST['paci']) && isset($_POST['btn_guarda_retiro']) && !empty($_POST['btn_guarda_retiro'])) { // muestra ovos o embrio para RETIRO ------------------------
    $rAsp = $db->prepare("SELECT
        lab_aspira.pro, lab_aspira.fec, lab_aspira.tip, hc_reprod.p_dni
        FROM hc_reprod, lab_aspira
        WHERE hc_reprod.id=lab_aspira.rep and lab_aspira.estado is true AND lab_aspira.dni=?;");
    $rAsp->execute(array($_POST['dni']));

    if ($rAsp->rowCount() > 0) {
        $c = 0;
        $vacio = 0;

        if ($_POST['h'] == 1) $ovo_emb = 'Ovulos';
        if ($_POST['h'] == 2) $ovo_emb = 'Embriones';

        echo '<p class="ui-bar-b" align="center">Seleccione los ' . $ovo_emb . ' para retirarlos:</p>';
        while ($asp = $rAsp->fetch(PDO::FETCH_ASSOC)) {

            if ($_POST['h'] == 2) {
                $rRes = $db->prepare("SELECT lad.*, teo.id as idestadoebrio,teo.nombre
                    FROM lab_aspira_dias lad
                    inner join lab_aspira las on las.pro = lad.pro and las.estado is true
                    inner join hc_reprod hcr on hcr.id = las.rep
                    inner join tblestado_embrio_ovo teo on teo.id = lad.id_estado
                    WHERE (hcr.cancela <> 5 or hcr.cancela is null) and lad.pro=? AND lad.des<>1 AND (lad.adju is null OR lad.adju='' OR lad.adju=?) AND (lad.d6f_cic='C' OR lad.d5f_cic='C' OR lad.d4f_cic='C' OR lad.d3f_cic='C' OR lad.d2f_cic='C') and lad.estado is true
                    ORDER BY lad.ovo;");

                $rRes->execute(array($asp['pro'], $_POST['paci']));
                if ($rRes->rowCount() > 0) {
                    $ubica = '';
                    echo '
                    <span class="ui-bar-b">Protocolo: ' . $asp['pro'] . ' (' . date("d-m-Y", strtotime($asp['fec'])) . ')</span>
                    <a href="info_r.php?a=' . $asp['pro'] . '&b=' . $_POST['dni'] . '&c=' . $asp['p_dni'] . '" target="_blank">Ver Informe</a>
                    <br><span class="ui-bar-b">VIDEO Embryos:</span>';
                    if (file_exists("emb_pic/embryoscope_" . $asp['pro'] . ".mp4")) {
                        echo "<a href='archivos_hcpacientes.php?idEmp=embryoscope_" . $asp['pro'] . ".mp4' target='_blank'>(VER)</a>";
                        $embry_requrid = '';
                    } else {
                        print('No se a cargado.');
                    }
                    print('<table cellpadding="5" style="text-align:center;font-size: small;border-collapse: collapse;border: 2px solid #72a2aa;">');
                    print('
                        <tr style="border: 1px solid #72a2aa;">
                                <th></th>
                                <th colspan="5" style="border: 1px solid #72a2aa;">Día 5</th>
                                <th colspan="5" style="border: 1px solid #72a2aa;">Día 6</th>
                                <th colspan="5"></th>
                        </tr>');
                    print(
                        '<tr style="border: 1px solid #72a2aa;">
                        <th>ID ' . $ovo_emb . '</th>'
                    );
                    print('
                        <!--<th>DIA 2</th>
                        <th>DIA 3</th>
                        <th>DIA 4</th>-->
                        <th>Células</th><th>MCI</th><th>TROF.</th><th>KID/IDA Score</th><th>CONTRACCIÓN</th>
                        <th>Células</th><th>MCI</th><th>TROF.</th><th>KID/IDA Score</th><th>CONTRACCIÓN</th>
                        <th>NGS</th><th>Mitoscore</th><th>Prioridad<br>transferencia</th>');


                    $estado = '<th>ESTADO</th>';


                    print('<th>PAJUELA</th><th>RETIRAR</th>' . $estado . '</tr>');

                    while ($res = $rRes->fetch(PDO::FETCH_ASSOC)) {
                        $c++;
                        if ($ubica == $res['t'] . '-' . $res['c'] . '-' . $res['g'] . '-' . $res['p']) $borde = '';
                        else $borde = 'style="border-top: 1px solid #72a2aa;"';
                        echo '<tr ' . $borde . '><td>' . $res['ovo'] . '</td>';

                        if ($res['d5f_cic'] <> '') {
                            if ($res['d5col'] == 1) {
                                $res['d5col'] = "Si";
                            } else {
                                $res['d5col'] = "No";
                            }
                            if ($res['d6col'] == 1) {
                                $res['d6col'] = "Si";
                            } else {
                                $res['d6col'] = "No";
                            }
                            $kidscore5 = 0;
                            if ($res['d5kid_tipo'] != 0) {
                                $kidscore5 = $res['d5kid_decimal'];
                            } elseif ($res['d5kid_tipo'] == 0) {
                                $kidscore5 = $res['d5kid'];
                            }
                            print('<td>' . $res['d5cel'] . ' </td><td> ' . $res['d5mci'] . ' </td><td> ' . $res['d5tro'] . ' </td><td> ' . $kidscore5 . ' </td><td> ' . $res['d5col'] . '</td>');
                            if ($res['d5f_cic'] == 'C') $des_dia = 5;
                        } else print('<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>');
                        if ($res['d6f_cic'] <> '') {
                            $kidscore6 = 0;
                            if ($res['d6kid_tipo'] != 0) {
                                $kidscore6 = $res['d6kid_decimal'];
                            } elseif ($res['d6kid_tipo'] == 0) {
                                $kidscore6 = $res['d6kid'];
                            }
                            print('<td>' . $res['d6cel'] . ' </td><td> ' . $res['d6mci'] . ' </td><td> ' . $res['d6tro'] . ' </td><td> ' . $kidscore6 . ' </td><td> ' . $res['d6col'] . '</td>');
                            if ($res['d6f_cic'] == 'C') $des_dia = 6;
                        } else print('<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>');
                        //ngs
                        $ngs = '-';
                        $ngs3 = '';
                        if ($res['ngs1'] == 1) $ngs = 'Normal';
                        if ($res['ngs1'] == 2) $ngs = '<font color="red">Anormal</font>';
                        if ($res['ngs1'] == 3) $ngs = 'NR';
                        if ($res['ngs1'] == 4) $ngs = '<font color="red">Mosaico</font>';
                        if ($res['ngs3'] == 1) $ngs3 = ' (H)';
                        if ($res['ngs3'] == 2) $ngs3 = ' (M)';
                        if ($res['ngs3'] == 3) $ngs3 = ' -';
                        echo '<td><a href="archivos_hcpacientes.php?idArchivo=ngs_' . $asp['pro'] . '" target="new">' . $ngs . $ngs3 . '</a></td>';
                        echo '<td>' . (!!$res['valores_mitoscore'] ? $res['valores_mitoscore'] : '-') . '</td>';
                        echo '<td>' . (!!$res['prioridad_transferencia'] ? $res['prioridad_transferencia'] : '-') . '</td>';

                        if ($res['d0f_cic'] == 'C') {
                            $des_dia = 0;
                        }

                        $ubica = $res['t'] . '-' . $res['c'] . '-' . $res['g'] . '-' . $res['p'];
                        echo '<td>' . $ubica . '</td>';
                        if ($res['adju'] == '') $check = '';
                        else $check = '';
                        if ($res['id_estado'] == 3 || $res['id_estado'] == 2 || $res['adju'] != '') $disabled = ' disabled';
                        else $disabled = '';
                        if ($res['idestadoebrio'] == 3) $estadoEmbrio = '<td style="color:red"><strong>' . strtoupper($res['nombre']) . '</strong></td>';
                        else $estadoEmbrio = '<td>' . $res['nombre'] . '</td>';
                        echo '<input type="hidden" name="c' . $c . '" value="' . $asp['pro'] . '|' . $res['ovo'] . '">';
                        if ($res['id_estado'] == 3 || $res['id_estado'] == 2 || $res['adju'] != '') {
                            echo '<td></td>' . $estadoEmbrio . '</tr>';
                        } else {
                            echo '<td><input type="checkbox" onchange="embrionesRetirados()" name="adju' . $c . '" id="' . $des_dia . '" class="deschk" value="' . $_POST['paci'] . '" data-mini="true" ' . $check . $disabled . '></td>' . $estadoEmbrio . '</tr>';
                        }
                    }
                    echo '</table>';
                } else $vacio++;
            }

            if ($_POST['h'] == 1) {
                $rRes = $db->prepare("SELECT lad.*, teo.id as idestadoebrio, teo.nombre
                    FROM lab_aspira_dias lad
                    inner join lab_aspira las on las.pro = lad.pro and las.estado is true
                    inner join hc_reprod hcr on hcr.id = las.rep
                    inner join tblestado_embrio_ovo teo on teo.id = lad.id_estado
                    WHERE (hcr.cancela <> 5 or hcr.cancela is null) and lad.pro=? AND lad.des<>1 AND (lad.adju is null OR lad.adju='' OR lad.adju=?) AND lad.d0f_cic='C' and lad.estado is true
                    ORDER BY lad.ovo;");

                $rRes->execute(array($asp['pro'], $_POST['paci']));
                if ($rRes->rowCount() > 0) {
                    $ubica = '';
                    echo '
                    <span class="ui-bar-b">Protocolo: ' . $asp['pro'] . ' (' . date("d-m-Y", strtotime($asp['fec'])) . ')</span>
                    <a href="info_r.php?a=' . $asp['pro'] . '&b=' . $_POST['dni'] . '&c=' . $asp['p_dni'] . '" target="_blank">Ver Informe</a>
                    <br><span class="ui-bar-b">VIDEO Embryos:</span>';
                    if (file_exists("emb_pic/embryoscope_" . $asp['pro'] . ".mp4")) {
                        echo "<a href='archivos_hcpacientes.php?idEmp=embryoscope_" . $asp['pro'] . ".mp4' target='_blank'>(VER)</a>";
                        $embry_requrid = '';
                    } else {
                        print('No se a cargado.');
                    }
                    print('<table cellpadding="5" style="text-align:center;font-size: small;border-collapse: collapse;border: 2px solid #72a2aa;">');

                    print(
                        '<tr style="border: 1px solid #72a2aa;">
                        <th>ID ' . $ovo_emb . '</th>'
                    );

                    $estado = '<th>ESTADO</th>';


                    print('<th>PAJUELA</th><th>RETIRAR</th>' . $estado . '</tr>');

                    while ($res = $rRes->fetch(PDO::FETCH_ASSOC)) {
                        $c++;
                        if ($ubica == $res['t'] . '-' . $res['c'] . '-' . $res['g'] . '-' . $res['p']) $borde = '';
                        else $borde = 'style="border-top: 1px solid #72a2aa;"';
                        echo '<tr ' . $borde . '><td>' . $res['ovo'] . '</td>';

                        if ($res['d0f_cic'] == 'C') {
                            $des_dia = 0;
                        }

                        $ubica = $res['t'] . '-' . $res['c'] . '-' . $res['g'] . '-' . $res['p'];
                        echo '<td>' . $ubica . '</td>';
                        if ($res['adju'] == '') $check = '';
                        else $check = '';
                        if ($res['id_estado'] == 3 || $res['id_estado'] == 2 || $res['adju'] != '') $disabled = ' disabled';
                        else $disabled = '';
                        if ($res['idestadoebrio'] == 3) $estadoEmbrio = '<td style="color:red"><strong>' . strtoupper($res['nombre']) . '</strong></td>';
                        else $estadoEmbrio = '<td>' . $res['nombre'] . '</td>';
                        echo '<input type="hidden" name="c' . $c . '" value="' . $asp['pro'] . '|' . $res['ovo'] . '">';
                        if ($res['id_estado'] == 3 || $res['id_estado'] == 2 || $res['adju'] != '') {
                            echo '<td></td>' . $estadoEmbrio . '</tr>';
                        } else {
                            echo '<td><input type="checkbox" onchange="embrionesRetirados()" name="adju' . $c . '" id="' . $des_dia . '" class="deschk" value="' . $_POST['paci'] . '" data-mini="true" ' . $check . $disabled . '></td>' . $estadoEmbrio . '</tr>';
                        }
                    }
                    echo '</table>';
                } else $vacio++;
            }

        }
        if ($vacio == $rAsp->rowCount()) {
            echo '<p>Sin ' . $ovo_emb . '</p>';
        } else {
            echo '<input type="hidden" name="cont" value="' . $c . '">';
            // 1=interface del medico 2=reserva de embiones ovulos
            if ($_POST['btn_guarda_retiro'] == 1) {
                echo '<input type="hidden" name="des_dia" id="des_dia">';
                if ($_POST['h'] == 1 or $_POST['h'] == 2) echo '<input type="hidden" name="des_don" id="des_don" value="' . $_POST['dni'] . '">'; // ovo/embrio donados
            } else {
                echo '<input type="submit" name="guardar" value="GUARDAR" data-icon="check" data-iconpos="left" data-mini="true" data-theme="b" data-inline="true"/><b class="color">Debe reservar los ' . $ovo_emb . ' agrupados por PAJUELA</b>';
            }
        }
    } else echo '<p>Sin Procedimientos</p>';
}

if (isset($_POST['depa']) && !empty($_POST['depa'])) {

    $rProv = $db->prepare("SELECT * FROM provincias WHERE iddepartamento=? ORDER BY nomprovincia ASC");
    $rProv->execute(array($_POST['depa']));

    echo '<option value="">Provincia:</option>';
    while ($prov = $rProv->fetch(PDO::FETCH_ASSOC)) {
        echo "<option value=" . $prov['idprovincia'] . ">" . $prov['nomprovincia'] . "</option>";
    }
}

if (isset($_POST['prov']) && !empty($_POST['prov'])) {

    $rDist = $db->prepare("SELECT * FROM distritos WHERE idprovincia=? ORDER BY nomdistrito ASC");
    $rDist->execute(array($_POST['prov']));

    echo '<option value="">Distrito:</option>';
    while ($dist = $rDist->fetch(PDO::FETCH_ASSOC)) {
        echo "<option value=" . $dist['iddistrito'] . ">" . $dist['nomdistrito'] . "</option>";
    }
}

if (isset($_POST['pak']) && !empty($_POST['pak']) && isset($_POST['t_ser']) && !empty($_POST['t_ser']) && isset($_POST['mon']) && !empty($_POST['mon']) && isset($_POST['tip']) && !empty($_POST['tip']) && isset($_POST['sede_id']) && !empty($_POST['sede_id'])) {

    $rPak = $db->prepare("SELECT
    r.id, r.nom, r.costo, r.cod
    from recibo_serv r
    inner join conta_sub_centro_costo sco on sco.id = r.conta_sub_centro_costo_id and sco.estado = 1
    inner join conta_centro_costo cco on cco.id = sco.conta_centro_costo_id and cco.estado = 1
    inner join sedes_contabilidad s on s.id = cco.sede_id and s.id = ?
    where r.estado = 1 and r.tip = ? and r.pak = ?");
    $rPak->execute(array($_POST['sede_id'], $_POST['t_ser'], $_POST['pak']));
    $cadena = '';
    $total = 0;

    while ($pak = $rPak->fetch(PDO::FETCH_ASSOC)) {
        if ($_POST['t_ser'] == 3) {
            $costo = number_format($pak['costo'] * $_POST['mon'], 2, '.', '');
        } else {
            $costo = number_format($pak['costo'] / $_POST['mon'], 2, '.', '');
        }

        echo "<tr><td>" . $pak['id'] . "</td><td>" . $pak['nom'] . "</td><td>" . $costo . "</td></tr>";

        if ($cadena == '')
            $cadena = $pak['cod'];
        else
            $cadena = $cadena . "," . $pak['cod'];

        if ($_POST['t_ser'] == 1 or $_POST['t_ser'] == 2 or $_POST['t_ser'] == 3)
            $total = $total + ($pak['costo'] * $_POST['mon']);
        else
            $total = $total + ($pak['costo'] / $_POST['mon']);
    }
    echo '|' . number_format($total, 2, '.', '') . '|' . number_format($total - ($total / 1.18), 2, '.', '') . '|' . $cadena;
}


// servicios de procedimiento
if (
    isset($_POST['procedimiento_id']) && !empty($_POST['procedimiento_id'])
    && isset($_POST['tarifario_id']) && !empty($_POST['tarifario_id'])
    && isset($_POST['tipo_servicio']) && !empty($_POST['tipo_servicio'])
    && isset($_POST['tipo_comprobante']) && !empty($_POST['tipo_comprobante'])
    && isset($_POST['sede_id']) && !empty($_POST['sede_id'])
    && isset($_POST['tipo_cambio']) && !empty($_POST['tipo_cambio'])
) {
    $stmt = $db->prepare("SELECT
        r.id servicio_id, r.procedimiento_id, r.cod codigo, r.nom servicio, r.costo
        from recibo_serv r
        inner join moneda m on m.id=r.idmoneda
        inner join conta_sub_centro_costo sco on sco.id = r.conta_sub_centro_costo_id and sco.estado = 1
        inner join conta_centro_costo cco on cco.id = sco.conta_centro_costo_id and cco.estado = 1
        inner join sedes_contabilidad s on s.id = cco.sede_id
        where r.estado = 1 and r.tip = ? and r.procedimiento_id = ? and r.tarifario_id = ? and s.id = ? order by r.id;");
    $stmt->execute([$_POST['tipo_servicio'], $_POST['procedimiento_id'], $_POST['tarifario_id'], $_POST['sede_id']]);
    $cadena = '';
    $servicios_detalle = '';
    $servicios_tabla = '';
    $total = 0;
    $codigoAnglo = '';
    $checked = 'checked';
    if ($_POST['tipo_servicio'] == 2 || $_POST['tipo_servicio'] == 6) {
        $checked = '';
    }
    $servicios_tabla .= "<tr><td></td><td>MARCAR TODO</td><td></td><td><input type='checkbox' id='marcar_todo' " . $checked . "></td></tr>";


    while ($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $costo = $item['costo'] * $_POST['tipo_cambio'];
        $servicios_detalle .= "<tr><td>" . $item['servicio_id'] . "</td><td>" . $item['servicio'] . "</td><td>" . number_format($costo, 2, '.', '') . "</td></tr>";
        $servicios_tabla .= "<tr><td>" . $item['servicio_id'] . "</td><td>" . $item['servicio'] . "</td><td>" . number_format($costo, 2, '.', '') . "</td><td><input type='checkbox' class='eliminar-servicio' " . $checked . "></td></tr>";

        if ($cadena == '') {
            $cadena = $item['codigo'];
        } else {
            $cadena = $cadena . "," . $item['codigo'];
        }

        if ($_POST['tipo_servicio'] == 1 or $_POST['tipo_servicio'] == 3) {
            $total = $total + ($item['costo'] * $_POST['tipo_cambio']);
        } else if ($_POST['tipo_servicio'] == 4 || $_POST['tipo_servicio'] == 5 || $_POST['tipo_servicio'] == 7) {
            $total = $total + ($item['costo'] / $_POST['tipo_cambio']);
        }
    }
    echo $servicios_detalle . '|' . number_format($total, 2, '.', '') . '|' . $servicios_tabla . '|' . $cadena . '|' . $codigoAnglo;
}
if (isset($_POST['codAnglo']) && !empty($_POST['codAnglo'])) {
    $codigoA = $db->prepare("SELECT cod FROM recibo_serv WHERE id in (" . $_POST['codAnglo'] . ");");
    $codigoA->execute();
    $codi = $codigoA->fetchAll(PDO::FETCH_OBJ);
    $codigos = '';
    foreach ($codi as $value) {

        if ($codigos == '') {
            $codigos = $codigos . strval($value->cod);
        } else {
            $codigos = $codigos . "," . strval($value->cod);
        }
    }

    echo $codigos;
}

if (isset($_POST['med']) && !empty($_POST['med'])) {
    $rMed = $db->prepare("SELECT UPPER(nom) nom FROM usuario WHERE userx=?");
    $rMed->execute(array($_POST['med']));
    $med = $rMed->fetch(PDO::FETCH_ASSOC);
    echo $med['nom'];
}

if (isset($_POST['carga_paci']) && !empty($_POST['carga_paci'])) {
    if ($_POST['carga_paci'] == 1) {
        echo '<ul data-role="listview" data-theme="c" data-inset="true" data-filter="true"
        data-filter-reveal="true" data-filter-placeholder="Buscar por Nombre o DNI"
        data-mini="true"
        class="fil_paci">';
        echo '</ul>';
    }
}

if (isset($_POST['carga_paci_det']) && !empty($_POST['carga_paci_det'])) {
    $stmt = $db->prepare(
        "SELECT
        pac.tip, pac.dni dni, upper(trim(pac.ape)) ape, upper(trim(pac.nom)) nom, pac.med, pac.mai, pac.createdate
        , pac.medios_comunicacion_id programa_id
        , EXTRACT(YEAR FROM AGE(pac.fnac)) as fnac, idsedes procedencia, td.id tipo_documento
        FROM hc_paciente pac
        INNER JOIN man_tipo_documento_facturacion td on td.codigo = pac.tip
        WHERE (unaccent(pac.dni) ILIKE ? OR unaccent(pac.ape) ILIKE ? OR unaccent(pac.nom) ILIKE ?) AND pac.estado = 1
        UNION
        SELECT
        pare.p_tip tip, p_dni dni, upper(trim(p_ape)) ape, upper(trim(p_nom)) nom, p_med, pare.p_mai mai, pare.createdate
        , pare.programaid as programa_id
        , EXTRACT(YEAR FROM AGE(pare.p_fnac)) as fnac, pare.sedeid procedencia, td.id tipo_documento
        FROM hc_pareja pare
        INNER JOIN man_tipo_documento_facturacion td on td.codigo = pare.p_tip
        WHERE (unaccent(pare.p_dni) ILIKE ? OR unaccent(pare.p_ape) ILIKE ? OR unaccent(pare.p_nom) ILIKE ?) AND pare.estado = 1"
    );
    

    $stmt->execute(
        array(
            "%" . $_POST['carga_paci_det'] . "%",
            "%" . $_POST['carga_paci_det'] . "%",
            "%" . $_POST['carga_paci_det'] . "%",
            "%" . $_POST['carga_paci_det'] . "%",
            "%" . $_POST['carga_paci_det'] . "%",
            "%" . $_POST['carga_paci_det'] . "%"
        )
    );


    while ($paci = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $fechaFormateada = date('Y-m-d', strtotime($paci['createdate']));
        print(
            '<li class="ui-li-has-count">
                <a href="#" class="paci_insert ui-btn ui-icon-carat-r" createdate= "' . $fechaFormateada . '" programa="' . $paci['programa_id'] . '" fnac="' . $paci['fnac'] . '" dni="' . $paci['dni'] . '" med="' . $paci['med'] . '" nom="' . $paci['ape'] . ' ' . $paci['nom'] . '" sede="' . $paci["procedencia"] . '" tip="' . $paci['tipo_documento'] . '" mai="' . $paci['mai'] . '">
                <small>' . $paci['ape'] . ' ' . $paci['nom'] . '</small></a>
                <span class="ui-li-count">' . $paci['dni'] . '</span>
            </li>'
        );
    }
}
//
if (isset($_POST['carga_paci_det_01']) && !empty($_POST['carga_paci_det_01'])) {
    $rPaci = $db->prepare("
                            SELECT
                            dni, ape, nom, med
                            FROM hc_paciente
                            WHERE unaccent(dni) ILIKE ? OR unaccent(ape) ILIKE ? OR unaccent(nom) ILIKE ? OR unaccent(med) ILIKE ?
                            UNION
                            SELECT
                            p_dni, p_ape, p_nom, p_med
                            FROM hc_pareja
                            WHERE unaccent(p_dni) ILIKE ? OR unaccent(p_ape) ILIKE ? OR unaccent(p_nom) ILIKE ? OR unaccent(p_med) ILIKE ?");
                            
                        $rPaci->execute(array(
                            "%" . $_POST['carga_paci_det_01'] . "%", 
                            "%" . $_POST['carga_paci_det_01'] . "%", 
                            "%" . $_POST['carga_paci_det_01'] . "%", 
                            "%" . $_POST['carga_paci_det_01'] . "%", 
                            "%" . $_POST['carga_paci_det_01'] . "%", 
                            "%" . $_POST['carga_paci_det_01'] . "%", 
                            "%" . $_POST['carga_paci_det_01'] . "%", 
                            "%" . $_POST['carga_paci_det_01'] . "%"
                        ));

    while ($paci = $rPaci->fetch(PDO::FETCH_ASSOC)) {
        echo '
        <li class="active-result">
            <a href="#" class="paci_insert" dni="' . $paci['dni'] . '" med="' . $paci['med'] . '">' . $paci['ape'] . ' ' . $paci['nom'] . '</a>
            <span class="ui-li-count">' . $paci['dni'] . '</span>
        </li>';
    }
}
// 
if (isset($_POST['paciente']) && !empty($_POST['paciente'])) {

    $rPaci = $db->prepare("SELECT
                            paci.dni, paci.ape, paci.nom, paci.sta, paci.don, paci.san, '' m_ale, '' m_ets, paci.valid_reniec_api
                            FROM hc_paciente paci
                            LEFT JOIN hc_pare_paci pp ON pp.estado = 1 AND pp.dni = paci.dni
                            LEFT JOIN hc_pareja pare ON pare.estado = 1 AND pare.p_dni = pp.p_dni
                            WHERE (
                                unaccent(paci.dni) ILIKE ? OR unaccent(paci.ape) ILIKE ? OR unaccent(paci.nom) ILIKE ?
                                OR unaccent(paci.med) ILIKE ?
                                OR unaccent(pare.p_dni) ILIKE ? OR unaccent(pare.p_nom) ILIKE ? OR unaccent(pare.p_ape) ILIKE ?
                            ) AND paci.estado = 1
                            GROUP BY paci.dni, paci.ape, paci.nom, paci.sta, paci.don, paci.san
                            ORDER BY paci.ape, paci.nom ASC");

                        $rPaci->execute(array(
                            "%" . $_POST['paciente'] . "%", 
                            "%" . $_POST['paciente'] . "%", 
                            "%" . $_POST['paciente'] . "%", 
                            "%" . $_POST['paciente'] . "%", 
                            "%" . $_POST['paciente'] . "%", 
                            "%" . $_POST['paciente'] . "%", 
                            "%" . $_POST['paciente'] . "%"
                        ));

    while ($paci = $rPaci->fetch(PDO::FETCH_ASSOC)) {
        $sta = $m_ale = $san = $m_ets = "";
        if ($paci['sta'] <> "") $sta = '<p>Observaciones de Médico: ' . $paci['sta'] . '.</p>';
        if ($paci['m_ale'] == "Medicamentada") $m_ale = "<p>Paciente presenta alergia medicada.</p>";
        if (strpos($paci['san'], "-") !== false) $san = "<p>Grupo Sanguineo: Sangre Negativa.</p>";
        if (strpos($paci['m_ets'], "VIH") !== false) $m_ets = " <p>ETS: VIH</p>";
        if (strpos($paci['m_ets'], "Hepatitis C") !== false) $m_ets = "<p>ETS: Hepatitis C</p>";
        $textValid = '';
        if ($paci['valid_reniec_api'] == true) {
            $textValid = "<span id='unique-span' class='custom-label' style='margin-left: 20px;background-color: #C8E6C9;color: #256029;font-weight: 700;'>VALIDADO CON RENIEC</span>";
        }elseif ($paci['valid_reniec_api'] == false) {
            $textValid = "<span id='unique-span' class='custom-label' style='margin-left: 20px;background-color: #FFCDD2;color: #c63737;font-weight: 700;'>NO VALIDADO CON RENIEC</span>";
        }
        if (isset($_POST['tipo'])) {
            print("
            <li class='ui-first-child ui-last-child'>
                <a href='pedido.php?id=" . $paci['dni'] . "' rel='external' class='ui-btn ui-btn-icon-right ui-icon-carat-r'>
                    <h4>" . $paci['ape'] . " <small>" . $paci['nom'] . " (DNI: " . $paci['dni'] . ")</small></h4>                    
                    ".$textValid. $sta . $m_ale . $san . $m_ets . "
                </a>
            </li>");
        } else {
            print("
            <li class='ui-first-child ui-last-child'>
                <a href='e_paci.php?id=" . $paci['dni'] . "' rel='external' class='ui-btn ui-btn-icon-right ui-icon-carat-r'>
                    <h4>" . $paci['ape'] . " <small>" . $paci['nom'] . " (DNI: " . $paci['dni'] . ")</small></h4><br>
                    ".$textValid. $sta . $m_ale . $san . $m_ets . "
                </a>
            </li>");
        }
    }
}

if (isset($_POST['idproducto']) && !empty($_POST['idproducto'])) {
    $idproducto = $_POST['idproducto'];
    $rProducto = $farma->prepare("SELECT
    producto.id,producto.producto, puv.stock
                    FROM tblproducto producto
                    join tblproductounidadventa puv on producto.id=puv.idproducto
                    where producto.id=$idproducto and puv.stock>1 order by producto.producto asc ");
    $rProducto->execute();
    //cambio la manera de traer datos
    while ($producto = $rProducto->fetch(PDO::FETCH_ASSOC)) {
        //$nombre=$producto['producto'];
        print("<tr>
            <td width='70%' align='left'><span>" . $producto['producto'] . "</span>
            <input type='hidden' name='producto[]'  value='" . $idproducto . "'/></td><td width='15%'><input type='number' style='border-radius: 3px;' name='cantidad[]' value='1' data-mini='true' class='numeros' min='1' id=cantidad" . $idproducto . " onchange='checkstock(" . $idproducto . ")' required></td>

            <td width='15%' align='right'><input type='hidden'  value='" . $producto['stock'] . "' id=stock" . $idproducto . " /><span class='col0'>" . $producto['stock'] . "</span></td>
            </td></tr><tr>
            <td width='100%' colspan='3'><span id=alerta" . $idproducto . " hidden='true'><a href='' style='text-decoration: none;'><h4  style='color: #FF3333;'>La cantidad ingresada es superior al stock de este producto</h4></a></span></td>
            </td>
            </tr>
            "); //max='".$producto['stock']."'
    }
}

if (isset($_POST['producto']) && !empty($_POST['producto'])) {
    $rProducto = $farma->prepare("SELECT
                                    producto.id, unaccent(producto.producto) as producto, laboratorio.laboratorio, unidad.unidad, puv.stock
                                    FROM tblproducto producto
                                    JOIN tbllaboratorio laboratorio ON laboratorio.id = producto.id
                                    JOIN tblunidad unidad ON unidad.id = producto.idunidadcompra
                                    JOIN tblproductounidadventa puv ON puv.idproducto = producto.id
                                    WHERE unaccent(producto.producto) ILIKE ? 
                                    AND producto.estado = 1 
                                    AND puv.stock > 1 
                                    ORDER BY producto.producto ASC");

                                $rProducto->execute(array("%" . $_POST['producto'] . "%"));

    //cambio la manera de traer datos
    while ($producto = $rProducto->fetch(PDO::FETCH_ASSOC)) {
        $nombre = $producto['producto'];
        print("
        <li data-role='list-divider'>  
                <a style='text-decoration: none; background-color=#FFFF91;' rel='external'  class='ui-btn' onclick='agregarProducto(" . $producto['id'] . ")'>
                <h4>" . $producto['producto'] . " Laboratorio: <small>" . $producto['laboratorio'] . " (Unidad: " . $producto['unidad'] . ")</small><span class='ui-btn-right ui-btn ui-btn-inline ui-mini ui-corner-all'>STOCK: " . $producto['stock'] . "</span></h4>
                </a>
        </li>");
    } //<span class='ui-btn-right ui-btn ui-btn-inline ui-mini ui-corner-all' >Agregar Producto</span>
}

// 
if (isset($_POST['procedimiento']) && !empty($_POST['procedimiento'])) {
    $rPaci = $db->prepare("SELECT
            split_part(lab_aspira.pro,'-',1) AS p1
            , split_part(lab_aspira.pro,'-',-1) AS p2
            , hc_paciente.dni, hc_paciente.ape, hc_paciente.nom, san, m_ets, don, hc_reprod.p_dni, hc_reprod.p_dni_het, hc_reprod.p_od
            , hc_reprod.p_cic, hc_reprod.p_fiv, hc_reprod.p_icsi, hc_reprod.p_cri, hc_reprod.p_iiu, hc_reprod.p_don, hc_reprod.des_don, hc_reprod.des_dia, hc_reprod.pago_extras, hc_reprod.med, lab_aspira.pro, lab_aspira.tip, lab_aspira.vec, lab_aspira.dias, lab_aspira.fec
            FROM hc_antece, hc_paciente, lab_aspira, hc_reprod
            where hc_reprod.estado = true and lab_aspira.estado is true and hc_paciente.dni = hc_antece.dni and hc_paciente.dni=lab_aspira.dni and hc_reprod.id=lab_aspira.rep and lab_aspira.f_fin<>'1899-12-30' and lab_aspira.tip<>'T'
            and (lab_aspira.pro ilike ?)");
    $rPaci->execute(array("%" . $_POST['procedimiento'] . "%"));

    while ($paci = $rPaci->fetch(PDO::FETCH_ASSOC)) {
        $sta = $m_ale = $san = $m_ets = $p_san = $p_m_ets = $p_dni_het = $donante = "";
        $p_cic = $p_fiv = $p_icsi = $p_od = $p_cri = $p_iiu = $p_don = $ted = $despro = $embrio = $desdon = $embryoscope = "";
        //$paci['dias']= es el proximo dia por lo tanto se resta 1 para tener el dia actual:
        if ($paci['dias'] > 0) {
            $paci['dias'] = $paci['dias'] - 1;
            $diaActual = 'Dia ' . $paci['dias'];
        } else $diaActual = 'Dia 0';
        // tipo procedimiento
        if ($paci['p_cic'] >= 1) $p_cic = "<p>Ciclo Natural</p>";
        if ($paci['p_fiv'] >= 1) $p_fiv = "<p>FIV</p>";
        if ($paci['p_icsi'] >= 1) $p_icsi = "<p>" . $_ENV["VAR_ICSI"] . "</p>";
        if ($paci['p_od'] <> '') $p_od = "<p>OD Fresco</p>";
        if ($paci['p_cri'] >= 1) $p_cri = "<p>Crio Ovulos</p>";
        if ($paci['p_iiu'] >= 1) $p_iiu = "<p>IIU</p>";
        if ($paci['p_don'] == 1) $p_don = "<p>Donación Fresco</p>";
        if ($paci['des_don'] == null and $paci['des_dia'] >= 1) $ted = "<p>TED</p>";
        if ($paci['des_don'] == null and $paci['des_dia'] === 0) $despro = "<p>Descongelación Ovulos Propios</p>";
        if ($paci['des_don'] <> null and $paci['des_dia'] >= 1) $embrio = "<p>EMBRIODONACIÓN</p>";
        if ($paci['des_don'] <> null and $paci['des_dia'] === 0) $desdon = "<p>Descongelación Ovulos Donados</p>";
        if (strpos($paci['pago_extras'], "EMBRYOSCOPE") !== false) {
            $embryoscope = "<small>EMBRYOSCOPE";
            if (file_exists("emb_pic/embryoscope_" . $paci['pro'] . ".mp4"))
                $embryoscope .= "<a href='archivos_hcpacientes.php?idEmb=embryoscope_" . $paci['pro'] . ".mp4' target='new'>(Video)</a>";
            if (file_exists("emb_pic/embryoscope_" . $paci['pro'] . ".pdf"))
                $embryoscope .= "<a href='archivos_hcpacientes.php?idEmb=embryoscope_" . $paci['pro'] . ".pdf' target='new'>(PDF)</a>";
            $embryoscope .= "</small>";
        }
        // donante
        if ($paci['p_od'] <> '') {
            $rDon = $db->prepare("select dni,nom,ape from hc_paciente where dni=?");
            $rDon->execute(array($paci['p_od']));
            $don = $rDon->fetch(PDO::FETCH_ASSOC);
            $donante = $don['ape'] . " " . $don['nom'];
        } else {
            if ($paci['des_don'] <> null) {
                $rDon = $db->prepare("SELECT nom,ape FROM hc_paciente WHERE dni=?");
                $rDon->execute(array($paci['des_don']));
                $don = $rDon->fetch(PDO::FETCH_ASSOC);
                $donante = mb_strtoupper($don['ape']) . " " . mb_strtoupper($don['nom']);
            } else {
                if ($paci['don'] == 'D') {
                    $donante = 'SI';
                } else {
                    $donante = 'NO';
                }
            }
        }

        $rPare = $db->prepare("SELECT p_nom,p_ape,p_san,p_m_ets FROM hc_pareja WHERE p_dni=?");
        $rPare->execute(array($paci['p_dni']));
        $pare = $rPare->fetch(PDO::FETCH_ASSOC);

        if ($paci['p_dni'] == "")
            $pareja = "SOLTERA";
        else
            $pareja = $pare['p_ape'] . " " . $pare['p_nom'];
        // if ($paci['sta'] <> "") $sta='<p>Observaciones de Médico: '.$paci['sta'].'.</p>';
        // if ($paci['m_ale'] == "Medicamentada") $m_ale="<p>Paciente presenta alergia medicada.</p>";
        // info paciente
        if (strpos($paci['san'], "-") !== false) $san = "<p>Grupo Sanguineo: Sangre Negativa.</p>";
        if (strpos($paci['m_ets'], "VIH") !== false) $m_ets = "<p>ETS: VIH</p>";
        if (strpos($paci['m_ets'], "Hepatitis C") !== false) $m_ets = "<p>ETS: Hepatitis C</p>";
        // info pareja
        if (strpos($pare['p_san'], "-") !== false) $p_san = "<p>Grupo Sanguineo: Sangre Negativa.</p>";
        if (strpos($pare['p_m_ets'], "VIH") !== false) $p_m_ets = "<p>ETS: VIH</p>";
        if (strpos($pare['p_m_ets'], "Hepatitis C") !== false) $p_m_ets = "<p>ETS: Hepatitis C</p>";
        if ($paci['p_dni_het'] <> "") $p_dni_het = "<p>HETEROLOGO</p>";
        print("
            <tr>
                <th>
                    <a href='le_aspi" . $paci['dias'] . ".php?id=" . $paci['pro'] . "' rel='external' target='_blank'>
                        <p>" . $paci['tip'] . "-" . $paci['pro'] . "-" . $paci['vec'] . "</p>
                    </a>
                    <span>" . date("d-m-Y", strtotime($paci['fec'])) . "</span>
                </th>
                <td>
                    <p>" . mb_strtoupper($paci['ape']) . " " . mb_strtoupper($paci['nom']) . "</p>" .
            $san . $m_ets . "
                    <span>
                        <a href='info_r.php?a=" . $paci['pro'] . "&b=" . $paci['dni'] . "&c=" . $paci['p_dni'] . "' target='new' style='color:#48F06A'>info</a><br>
                        <a href='info_r1.php?a=" . $paci['pro'] . "&b=" . $paci['dni'] . "&c=" . $paci['p_dni'] . "' target='new'>informe antiguo</a>
                    </span>
                </td>
                <td>" . $pareja . $p_san . $p_m_ets . $p_dni_het . "</td>
                <td><p>" . $donante . "</p></td>
                <td><p>" . mb_strtoupper($paci['med']) . "</p></td>
                <td>" . //echo $diaActual;
            $p_cic . $p_fiv . $p_icsi . $p_od . $p_cri . $p_iiu . $p_don . $ted . $despro . $embrio . $desdon .
            $embryoscope . "</td>
            </tr>");
    }
}
// 
if (isset($_POST['filtronombres']) && !empty($_POST['filtronombres'])) {
    $rPaci = $db->prepare("SELECT
                            split_part(lab_aspira.pro,'-',1) AS p1,
                            split_part(lab_aspira.pro,'-',-1) AS p2,
                            hc_paciente.dni, unaccent(hc_paciente.ape) as ape, unaccent(hc_paciente.nom) as nom, san, m_ets, don, hc_reprod.p_dni, hc_reprod.p_dni_het, hc_reprod.p_od,
                            hc_reprod.p_cic, hc_reprod.p_fiv, hc_reprod.p_icsi, hc_reprod.p_cri, hc_reprod.p_iiu, hc_reprod.p_don, hc_reprod.des_don, hc_reprod.des_dia, hc_reprod.pago_extras, hc_reprod.med, lab_aspira.pro, lab_aspira.tip, lab_aspira.vec, lab_aspira.dias, lab_aspira.fec,
                            ABS(CAST(split_part(lab_aspira.pro, '-', 1) AS INTEGER)) AS abs_p1,
                            ABS(CAST(split_part(lab_aspira.pro, '-', -1) AS INTEGER)) AS abs_p2
                            FROM hc_antece, hc_paciente, lab_aspira, hc_reprod
                            WHERE hc_reprod.estado = true 
                            AND lab_aspira.estado IS TRUE 
                            AND hc_paciente.dni = hc_antece.dni 
                            AND hc_paciente.dni = lab_aspira.dni 
                            AND hc_reprod.id = lab_aspira.rep 
                            AND lab_aspira.f_fin <> '1899-12-30' 
                            AND lab_aspira.tip <> 'T'
                            AND (unaccent(hc_paciente.ape) ILIKE ? OR unaccent(hc_paciente.nom) ILIKE ?)
                            ORDER BY abs_p2 DESC, abs_p1 DESC");

                        $rPaci->execute(array("%" . $_POST['filtronombres'] . "%", "%" . $_POST['filtronombres'] . "%"));



    while ($paci = $rPaci->fetch(PDO::FETCH_ASSOC)) {
        $sta = $m_ale = $san = $m_ets = $p_san = $p_m_ets = $p_dni_het = $donante = "";
        $p_cic = $p_fiv = $p_icsi = $p_od = $p_cri = $p_iiu = $p_don = $ted = $despro = $embrio = $desdon = $embryoscope = "";
        //$paci['dias']= es el proximo dia por lo tanto se resta 1 para tener el dia actual:
        if ($paci['dias'] > 0) {
            $paci['dias'] = $paci['dias'] - 1;
            $diaActual = 'Dia ' . $paci['dias'];
        } else $diaActual = 'Dia 0';
        // tipo procedimiento
        if ($paci['p_cic'] >= 1) $p_cic = "<p>Ciclo Natural</p>";
        if ($paci['p_fiv'] >= 1) $p_fiv = "<p>FIV</p>";
        if ($paci['p_icsi'] >= 1) $p_icsi = "<p>" . $_ENV["VAR_ICSI"] . "</p>";
        if ($paci['p_od'] <> '') $p_od = "<p>OD Fresco</p>";
        if ($paci['p_cri'] >= 1) $p_cri = "<p>Crio Ovulos</p>";
        if ($paci['p_iiu'] >= 1) $p_iiu = "<p>IIU</p>";
        if ($paci['p_don'] == 1) $p_don = "<p>Donación Fresco</p>";
        if ($paci['des_don'] == null and $paci['des_dia'] >= 1) $ted = "<p>TED</p>";
        if ($paci['des_don'] == null and $paci['des_dia'] === 0) $despro = "<p>Descongelación Ovulos Propios</p>";
        if ($paci['des_don'] <> null and $paci['des_dia'] >= 1) $embrio = "<p>EMBRIODONACIÓN</p>";
        if ($paci['des_don'] <> null and $paci['des_dia'] === 0) $desdon = "<p>Descongelación Ovulos Donados</p>";
        if (strpos($paci['pago_extras'], "EMBRYOSCOPE") !== false) {
            $embryoscope = "<small>EMBRYOSCOPE";
            if (file_exists("emb_pic/embryoscope_" . $paci['pro'] . ".mp4"))
                $embryoscope .= "<a href='archivos_hcpacientes.php?idEmb=embryoscope_" . $paci['pro'] . ".mp4' target='new'>(Video)</a>";
            if (file_exists("emb_pic/embryoscope_" . $paci['pro'] . ".pdf"))
                $embryoscope .= "<a href='archivos_hcpacientes.php?idEmb=embryoscope_" . $paci['pro'] . ".pdf' target='new'>(PDF)</a>";
            $embryoscope .= "</small>";
        }
        // donante
        if ($paci['p_od'] <> '') {
            $rDon = $db->prepare("select dni,nom,ape from hc_paciente where dni=?");
            $rDon->execute(array($paci['p_od']));
            $don = $rDon->fetch(PDO::FETCH_ASSOC);
            $donante = $don['ape'] . " " . $don['nom'];
        } else {
            if ($paci['des_don'] <> null) {
                $rDon = $db->prepare("SELECT nom,ape FROM hc_paciente WHERE dni=?");
                $rDon->execute(array($paci['des_don']));
                $don = $rDon->fetch(PDO::FETCH_ASSOC);
                $donante = mb_strtoupper($don['ape']) . " " . mb_strtoupper($don['nom']);
            } else {
                if ($paci['don'] == 'D') {
                    $donante = 'SI';
                } else {
                    $donante = 'NO';
                }
            }
        }

        $rPare = $db->prepare("SELECT p_nom,p_ape,p_san,p_m_ets FROM hc_pareja WHERE p_dni=?");
        $rPare->execute(array($paci['p_dni']));
        $pare = $rPare->fetch(PDO::FETCH_ASSOC);
        $pareja = "";
        if ($rPare->rowCount() > 0) {
            $pareja = $pare['p_ape'] . " " . $pare['p_nom'];
        }
        // if ($paci['sta'] <> "") $sta='<p>Observaciones de Médico: '.$paci['sta'].'.</p>';
        // if ($paci['m_ale'] == "Medicamentada") $m_ale="<p>Paciente presenta alergia medicada.</p>";
        // info paciente
        if (strpos($paci['san'], "-") !== false) $san = "<p>Grupo Sanguineo: Sangre Negativa.</p>";
        if (strpos($paci['m_ets'], "VIH") !== false) $m_ets = "<p>ETS: VIH</p>";
        if (strpos($paci['m_ets'], "Hepatitis C") !== false) $m_ets = "<p>ETS: Hepatitis C</p>";
        // info pareja
        if (isset($pare['p_san']) && strpos($pare['p_san'], "-") !== false) $p_san = "<p>Grupo Sanguineo: Sangre Negativa.</p>";
        if (isset($pare['p_m_ets']) && strpos($pare['p_m_ets'], "VIH") !== false) $p_m_ets = "<p>ETS: VIH</p>";
        if (isset($pare['p_m_ets']) && strpos($pare['p_m_ets'], "Hepatitis C") !== false) $p_m_ets = "<p>ETS: Hepatitis C</p>";
        if ($paci['p_dni_het'] <> "") $p_dni_het = "<p>HETEROLOGO</p>";
        print("
            <tr>
                <th>
                    <a href='le_aspi" . $paci['dias'] . ".php?id=" . $paci['pro'] . "' rel='external' target='_blank'>
                        <p>" . $paci['tip'] . "-" . $paci['pro'] . "-" . $paci['vec'] . "</p>
                    </a>
                    <span>" . date("d-m-Y", strtotime($paci['fec'])) . "</span>
                </th>
                <td>
                    <p>" . mb_strtoupper($paci['ape']) . " " . mb_strtoupper($paci['nom']) . "</p>" .
            $san . $m_ets . "
                    <span>
                        <a href='info_r.php?a=" . $paci['pro'] . "&b=" . $paci['dni'] . "&c=" . $paci['p_dni'] . "' target='new' style='color:#48F06A'>info</a><br>
                        <a href='info_r1.php?a=" . $paci['pro'] . "&b=" . $paci['dni'] . "&c=" . $paci['p_dni'] . "' target='new'>informe antiguo</a>
                    </span>
                </td>
                <td>" . $pareja . $p_san . $p_m_ets . $p_dni_het . "</td>
                <td><p>" . $donante . "</p></td>
                <td><p>" . mb_strtoupper($paci['med']) . "</p></td>
                <td>" . //echo $diaActual;
            $p_cic . $p_fiv . $p_icsi . $p_od . $p_cri . $p_iiu . $p_don . $ted . $despro . $embrio . $desdon .
            $embryoscope . "</td>
            </tr>");
    }
}
// 
if (isset($_POST['andropac']) && !empty($_POST['andropac']) && isset($_POST['repo']) && !empty($_POST['repo'])) {
    $repo = $_POST['repo'];

    $rPaci = $db->prepare("SELECT
    hc_pare_paci.p_dni, hc_pare_paci.dni, hc_pareja.p_nom, hc_pareja.p_ape, hc_pareja.p_san, hc_pareja.p_m_ets, hc_pareja.p_m_ale, hc_pare_paci.p_het
    FROM hc_pareja, hc_pare_paci
    WHERE hc_pareja.p_dni = hc_pare_paci.p_dni
    AND (hc_pare_paci.p_dni ILIKE ? OR hc_pare_paci.dni ILIKE ? OR hc_pareja.p_nom ILIKE ? OR hc_pareja.p_ape ILIKE ?)
    AND hc_pareja.estado = 1 AND hc_pare_paci.estado = 1
    ORDER BY p_ape, p_nom ASC");
$rPaci->execute(array("%" . $_POST['andropac'] . "%", "%" . $_POST['andropac'] . "%", "%" . $_POST['andropac'] . "%", "%" . $_POST['andropac'] . "%"));

    // cambio la manera de traer datos
    $c = 0;

    while ($paci = $rPaci->fetch(PDO::FETCH_ASSOC)) {
        $examenes = $p_m_ale = $p_san = $vih = $hepa = $dapto = $don = $esperma = $capa = $test_capa = $sobre = $testi = $crio = $parti = "";
        $pareja = "";
        $c++;

        // espermatograma
        if ($repo == 1 || $repo == 2) {
            $esp_consulta = $db->prepare("SELECT
                max(fec) fec, count(case when emb = 0 then true end) pendientes, count(*) total
                FROM lab_andro_esp
                WHERE p_dni = ?
                group by p_dni;");
            $esp_consulta->execute(array($paci['p_dni']));
            $esp_data = $esp_consulta->fetch();

            if ($esp_consulta->rowCount() > 0) {
                if ($esp_data["total"] <> 0) {
                    $examenes .= $esp_data["fec"] . " Espermatogramas: " . $esp_data["total"];
                }

                if ($esp_data["pendientes"] <> 0) {
                    $examenes .= " (<i class='color'>" . $esp_data["pendientes"] . " pendiente(s)</i>)";
                }
            }
        }

        // capacitacion espermatica
        if ($repo == 1) {
            $consulta = $db->prepare("SELECT max(fec) fec, count(case when emb = 0 then true end) pendientes, count(*) total
                FROM lab_andro_cap
                WHERE p_dni=?
                group by p_dni");
            $consulta->execute(array($paci['p_dni']));
            $data = $consulta->fetch();

            if (isset($data["total"]) && $data["total"] <> 0) {
                if (!empty($examenes)) {
                    $examenes .= "<br>";
                }
                $examenes .= $data["fec"] . " Capacitaciones: " . $data["total"];
            }

            if (isset($data["pendientes"]) && $data["pendientes"] <> 0) {
                $examenes .= " (<i class='color'>" . $data["pendientes"] . " pendiente(s)</i>)";
            }
        }

        // test de capacitaciones espermaticas
        if ($repo == 1) {
            $consulta = $db->prepare("SELECT
                max(fec) fec, count(case when emb = 0 then true end) pendientes, count(*) total
                FROM lab_andro_tes_cap
                WHERE p_dni=?
                group by p_dni");
            $consulta->execute(array($paci['p_dni']));
            $data = $consulta->fetch();

            if (isset($data["total"]) && $data["total"] <> 0) {
                if (!empty($examenes)) {
                    $examenes .= "<br>";
                }
                $examenes .= $data["fec"] . " Test Capacitaciones: " . $data["total"];
            }

            if (isset($data["pendientes"]) && $data["pendientes"] <> 0) {
                $examenes .= " (<i class='color'>" . $data["pendientes"] . " pendiente(s)</i>)";
            }
        }

        // test de sobrevivencia
        if ($repo == 1) {
            $consulta = $db->prepare("SELECT
                max(fec) fec, count(case when emb = 0 then true end) pendientes, count(*) total
                FROM lab_andro_tes_sob
                WHERE p_dni=?
                group by p_dni");
            $consulta->execute(array($paci['p_dni']));
            $data = $consulta->fetch();

            if (isset($data["total"]) && $data["total"] <> 0) {
                if (!empty($examenes)) {
                    $examenes .= "<br>";
                }
                $examenes .= $data["fec"] . " Test Sobrevivencia: " . $data["total"];
            }

            if (isset($data["total"]) && $data["pendientes"] <> 0) {
                $examenes .= " (<i class='color'>" . $data["pendientes"] . " pendiente(s)</i>)";
            }
        }

        // biopsia testicular
        if ($repo == 1) {
            $consulta = $db->prepare("SELECT
                max(fec) fec, count(case when emb = 0 then true end) pendientes, count(*) total
                FROM lab_andro_bio_tes
                WHERE p_dni=?
                group by p_dni");
            $consulta->execute(array($paci['p_dni']));
            $data = $consulta->fetch();

            if (isset($data["total"]) && $data["total"] <> 0) {
                if (!empty($examenes)) {
                    $examenes .= "<br>";
                }
                $examenes .= $data["fec"] . " Biopsia Testicular: " . $data["total"];
            }

            if (isset($data["pendientes"]) && $data["pendientes"] <> 0) {
                $examenes .= " (<i class='color'>" . $data["pendientes"] . " pendiente(s)</i>)";
            }
        }

        // criopreservacion de semen
        if ($repo == 1 || $repo == 3) {
            $consulta = $db->prepare("SELECT max(fec) fec, count(case when emb = 0 then true end) pendientes, count(*) total
                FROM lab_andro_crio_sem
                WHERE p_dni=?
                group by p_dni");
            $consulta->execute(array($paci['p_dni']));
            $data = $consulta->fetch();

            if (isset($data["total"]) && $data["total"] <> 0) {
                if (!empty($examenes)) {
                    $examenes .= "<br>";
                }
                $examenes .= $data["fec"] . " Criopreservación Semen: " . $data["total"];
            }

            if (isset($data["pendientes"]) && $data["pendientes"] <> 0) {
                $examenes .= " (<i class='color'>" . $data["pendientes"] . " pendiente(s)</i>)";
            }
        }

        if ($paci['dni'] <> "") {
            $rPare = $db->prepare("SELECT nom,ape,med FROM hc_paciente WHERE dni=?");
            $rPare->execute(array($paci['dni']));
            $pare = $rPare->fetch(PDO::FETCH_ASSOC);
        }

        if ($paci['p_m_ale'] == "Medicamentada") $p_m_ale = " (ALERGIA MEDICAMENTADA)";
        if (strpos($paci['p_san'], "-") !== false) $p_san = " (SANGRE NEGATIVA)";
        if (strpos($paci['p_m_ets'], "VIH") !== false) $vih = " (VIH)";
        if (strpos($paci['p_m_ets'], "Hepatitis C") !== false) $hepa = " (Hepatitis C)";
        if ($paci['dni'] == "" and $paci['p_het'] == 1) $dapto = " (Donante APTO)";
        if ($paci['dni'] == "" and $paci['p_het'] == 2) $don = " (Donante)";
        //
        /* if ($Esp->rowCount()>0) $esperma = "<i class='color'> -ESPERMATOGRAMA</i>";
        if ($Cap->rowCount()>0) $capa = "<i class='color'> -CAPACITACIÓN</i>";
        if ($Tes_cap->rowCount()>0) $test_capa = "<i class='color'> -TEST CAPACITACIÓN</i>";
        if ($Tes_sob->rowCount()>0) $sobre = "<i class='color'> -TEST SOBREVIVENCIA</i>";
        if ($Bio_tes->rowCount()>0) $testi = "<i class='color'> -BIOPSIA TESTICULAR</i>";
        if ($Cri_sem->rowCount()>0) $crio = "<i class='color'> -CRIOPRESERVACIÓN SEMEN</i>"; */
        //
        if ($paci['dni'] <> "" || $paci['dni'] <> "na") {
            $parti = mb_strtolower($pare['med'] ?? '');
            $pareja = '<a href="e_paci.php?id=' . $paci['dni'] . '" rel="external" target="_blank">' . ucwords(mb_strtolower($pare['ape'] ?? '')) . ' ' . ucwords(mb_strtolower($pare['nom'] ?? '')) . '</a>';
        } else {
            $parti = '-';
            $pareja = '-';
        }

        print("
        <tr>
            <th style='text-align: center;'>" . $paci['p_dni'] . "</th>
            <th>
                <a href='e_pare.php?id=" . $paci['dni'] . "&ip=" . $paci['p_dni'] . "' rel='external'>" .
            ucwords(mb_strtolower($paci['p_ape'])) . ' ' . ucwords(mb_strtolower($paci['p_nom'])) . "
                </a><br>
                <small style='opacity:.5;'>" . $p_m_ale . $p_san . $vih . $hepa . $dapto . $don . "</small>
            </th>
            <td>" . $parti . "</td>
            <td>" . $pareja . "</td>
            <td>" . $examenes . "</td>
        </tr>");
    }
}
//
if (isset($_POST['repro_dni']) && !empty($_POST['repro_dni'])) {
    $consulta = $db->prepare("
    select
    id, fec, p_dni
    , p_cic, p_fiv, p_icsi, p_od, p_cri, p_iiu, p_don, des_don, des_dia
    from hc_reprod where estado = true and facturado = 0 and dni = ?");
    $consulta->execute(array($_POST['repro_dni']));
    while ($data = $consulta->fetch(PDO::FETCH_ASSOC)) {
        $proc = "";
        if ($data['p_cic'] >= 1) $proc .= "Ciclo Natural, ";
        if ($data['p_fiv'] >= 1) $proc .= "FIV, ";
        if ($data['p_icsi'] >= 1) $proc .= $_ENV["VAR_ICSI"] . ", ";
        if ($data['p_od'] <> '') $proc .= "OD Fresco, ";
        if ($data['p_cri'] >= 1) $proc .= "Crio Ovulos, ";
        if ($data['p_iiu'] >= 1) $proc .= "IIU, ";
        if ($data['p_don'] == 1) $proc .= "Donación Fresco, ";
        if ($data['des_don'] == null and $data['des_dia'] >= 1) $proc .= "TED, ";
        if ($data['des_don'] == null and $data['des_dia'] === 0) $proc .= "<small>Descongelación Ovulos Propios</small>, ";
        if ($data['des_don'] <> null and $data['des_dia'] >= 1) $proc .= "EMBRIODONACIÓN, ";
        if ($data['des_don'] <> null and $data['des_dia'] === 0 and $data['id'] <> 2192) $proc .= "<small>Descongelación Ovulos Donados</small>, ";
        print("<br><input type='checkbox' data-mini='true'> Fecha: " . $data["fec"] . " Pareja: " . $data["p_dni"] . " Procedimiento: " . $proc);
    }
}

if (isset($_POST['tipo_reporte']) && !empty($_POST['tipo_reporte']) && isset($_POST['dato']) && !empty($_POST['dato'])) {
    $consulta = $db->prepare("SELECT
    r.id rep, STRING_AGG(CAST(c.id AS TEXT), ',') ids,STRING_AGG(CAST(c.eliminado AS TEXT), ',') eliminados, STRING_AGG(to_char(c.fec, 'YYYY-MM-DD'), ',') fecha_capacitacion, r.f_asp fecha_aspiracion
    , r.fec fecha, a.pro, r.tipo_documento, r.dni, r.p_dni, r.des_dia, r.des_don, r.p_od, r.p_fiv, r.p_icsi,
    r.apellidos, r.nombres, r.fnacimiento, r.med medico, r.p_dni_het
    , r.p_dtri, r.p_cic, r.p_cri, r.p_iiu, r.p_don, r.pago_extras
    from (
        select r.id, r.fec, r.f_asp, p.tipo_documento, r.dni, r.p_dni, r.des_dia, r.des_don, r.p_od, r.p_fiv, r.p_icsi, r.med
        , p.ape apellidos, p.nom nombres, p.fnac fnacimiento, r.p_dni_het
        , r.p_dtri, r.p_cic, r.p_cri, r.p_iiu, r.p_don, r.pago_extras
        from hc_reprod r
        inner join (select p.*, di.nombre tipo_documento
            from hc_paciente p
            inner join man_tipo_documento_identidad di on di.codigo = p.tip
            where p.dni ilike ? or p.ape ilike ? or p.nom ilike ?) p on p.dni = r.dni
        where r.estado = true and 1=1) r
    left join lab_aspira a on a.rep = r.id and a.estado is true
    left join lab_andro_cap c on ((c.pro = a.pro) or (c.rep = r.id)) and c.eliminado is false
    group by r.id,r.f_asp,r.fec,a.pro, r.tipo_documento, r.dni, r.p_dni, r.des_dia, r.des_don, r.p_od, r.p_fiv, r.p_icsi,
    r.apellidos, r.nombres, r.fnacimiento, r.med, r.p_dni_het
    , r.p_dtri, r.p_cic, r.p_cri, r.p_iiu, r.p_don, r.pago_extras
    order by r.fec desc");
    $consulta->execute(array("%" . $_POST['dato'] . "%", "%" . $_POST['dato'] . "%", "%" . $_POST['dato'] . "%"));
    $item = 1;

    $path_url = "";
    if (strpos($_SERVER["REQUEST_URI"], "?") !== false) {
        $path_url = substr($_SERVER["REQUEST_URI"], strpos($_SERVER["REQUEST_URI"], "?"), strlen($_SERVER["REQUEST_URI"]));
        $path_url = urlencode($path_url);
    }

    while ($data = $consulta->fetch(PDO::FETCH_ASSOC)) {
        $var = "";
        if (!empty($data["ids"])) {
            $var = "";
            $pos = 0;
            $deletes = explode(",", $data["eliminados"]);

            foreach (explode(",", $data["ids"]) as $key => $value) {
                if ($deletes[$key] === 'false') {
                    $var .= '<div class="item_le">
                           <a title="Eliminar capacitación" href="le_andro_cap.php?path=andro_capacitaciones_invitro&path_url=' . $path_url . '&dni=' . $data['dni'] . '&ip=' . $data['p_dni'] . '&id=' . $value . '" rel="external">' . explode(",", $data["fecha_capacitacion"])[$pos] . '</a><a href="#" id-attr="' . $value . '" onclick="delTrng(this,\'invitro\')" class="box-inline-cent btn-delete-jq fa-solid fa-trash" attr-type="Eliminar"></a>
                         </div> 
                        ';
                }
                $pos++;
            }
        }

        print("<tr>
            <td class='text-center'>" . $item++ . "</td>
            <td class='text-center'>" . $data['fecha'] . "</td>
            <td class='text-center'>" . substr($data['fecha_aspiracion'], 0, 10) . "</td>
            <td>");
        if ($data['p_dtri'] >= 1) {
            echo "Dual Trigger<br>";
        }
        if ($data['p_cic'] >= 1) {
            echo "Ciclo Natural<br>";
        }
        if ($data['p_fiv'] >= 1) {
            echo "FIV<br>";
        }
        if ($data['p_icsi'] >= 1) {
            echo $_ENV["VAR_ICSI"] . "<br>";
        }
        if ($data['p_od'] <> '') {
            echo "OD Fresco<br>";
        }
        if ($data['p_cri'] >= 1) {
            echo "Crio Ovulos<br>";
        }
        if ($data['p_iiu'] >= 1) {
            echo "IIU<br>";
        }
        if ($data['p_don'] == 1) {
            echo "Donación Fresco<br>";
        }
        if ($data['des_don'] == null && $data['des_dia'] >= 1) {
            echo "TED<br>";
        }
        if ($data['des_don'] == null && $data['des_dia'] === 0) {
            echo "<small>Descongelación Ovulos Propios</small><br>";
        }
        if ($data['des_don'] <> null && $data['des_dia'] >= 1) {
            echo "EMBRIODONACIÓN<br>";
        }
        if ($data['des_don'] <> null && $data['des_dia'] === 0) {
            echo "<small>Descongelación Ovulos Donados</small><br>";
        }

        print('Extras: ' . $data['pago_extras']);
        print('
            </td>
            <td class="text-center">' . $data['pro'] . '</td>
            <td class="text-center">' . $data['tipo_documento'] . '</td>
            <td class="text-center">' . $data['dni'] . '</td>
            <td>' . mb_strtoupper($data['apellidos']) . ' ' . mb_strtoupper($data['nombres']) . ' (' . date_diff(date_create($data['fnacimiento']), date_create('today'))->y . ')</td>
            <td class="text-center">' . $data['medico'] . '</td>
            <td class="text-center">' . $var . '</td>
            <td class="text-center">
                <a href="le_andro_cap.php?path=andro_capacitaciones_invitro&path_url=' . $path_url . '&dni=' . $data['dni'] . '&ip=' . $data['p_dni'] . '&pro=' . $data['pro'] . '&rep=' . $data['rep'] . '&het=' . $data['p_dni_het'] . '&id=" rel="external" class="btn btn-danger">Agregar</a>
            </td>');
        print('</tr>');
    }
}

if (isset($_POST['capacitacion_filtro']) && !empty($_POST['capacitacion_filtro'])) {
    $Rcap = $db->prepare("SELECT lac.*
                            FROM lab_andro_cap lac
                            LEFT JOIN hc_pareja hp1 ON hp1.p_dni = lac.p_dni
                            LEFT JOIN hc_reprod rep on rep.id = lac.iiu
                            LEFT JOIN hc_pareja hp2 ON hp2.p_dni = rep.p_dni_het
                            WHERE rep.estado = true AND lac.iiu > 0 AND lac.eliminado is false
                            AND (hp1.p_dni ILIKE ? OR hp1.p_ape ILIKE ? OR hp1.p_nom ILIKE ? OR hp2.p_dni ILIKE ? OR hp2.p_ape ILIKE ? OR hp2.p_nom ILIKE ?)
                            ORDER BY fec DESC;"); 

    $Rcap->execute([
        "%" . $_POST['capacitacion_filtro'] . "%",
        "%" . $_POST['capacitacion_filtro'] . "%",
        "%" . $_POST['capacitacion_filtro'] . "%",
        "%" . $_POST['capacitacion_filtro'] . "%",
        "%" . $_POST['capacitacion_filtro'] . "%",
        "%" . $_POST['capacitacion_filtro'] . "%"
    ]);

    $path_url = "";
    if (strpos($_SERVER["REQUEST_URI"], "?") !== false) {
        $path_url = substr($_SERVER["REQUEST_URI"], strpos($_SERVER["REQUEST_URI"], "?"), strlen($_SERVER["REQUEST_URI"]));
        $path_url = urlencode($path_url);
    }

    while ($cap = $Rcap->fetch(PDO::FETCH_ASSOC)) {
        $rIIU = $db->prepare("SELECT dni, p_dni_het, med FROM hc_reprod WHERE hc_reprod.estado = true and id = ?;");
        $rIIU->execute(array($cap['iiu']));
        if ($rIIU->rowCount() == 0) {
            continue;
        }
        $iiu = $rIIU->fetch(PDO::FETCH_ASSOC);
        $het = $iiu['p_dni_het'];
        $dni = $iiu['dni'];

        $rMujer = $db->prepare("SELECT nom, ape, med FROM hc_paciente WHERE dni = ?;");
        $rMujer->execute(array($iiu['dni']));
        $mujer = $rMujer->fetch(PDO::FETCH_ASSOC);

        if (empty($cap['p_dni']) || $cap['p_dni'] == 1) {
            $rPare = $db->prepare("SELECT p_nom, p_ape, p_med FROM hc_pareja WHERE p_dni = ?;");
            $rPare->execute(array($iiu['p_dni_het']));
            $pare = $rPare->fetch(PDO::FETCH_ASSOC);
        } else {
            $rPare = $db->prepare("SELECT p_nom, p_ape, p_med FROM hc_pareja WHERE p_dni = ?;");
            $rPare->execute(array($cap['p_dni']));
            $pare = $rPare->fetch(PDO::FETCH_ASSOC);
        }

        $paciente = '';

        if ($pare) {
            $paciente = mb_strtoupper($pare['p_ape']) . ' ' . mb_strtoupper($pare['p_nom']);
        } else {
            $paciente = 'NO MARCADO';
        }

        if ($cap['emb'] == 0) {
            $nuevo = 'Nuevo';
        } else {
            $nuevo = 'Editar';
        }

        print('<tr>
            <td class="mayuscula">' . mb_strtoupper($mujer['ape']) . ' ' . mb_strtoupper($mujer['nom']) . '</td>
            <td class="mayuscula">' . $paciente . '</td>
            <!-- medico -->
            <td>' . $iiu['med'] . '</td>
            <!-- fecha -->
            <td>' . date("d-m-Y", strtotime($cap['fec'])) . '</td>
            <td><a href="le_andro_cap.php?path=lista_cap&path_url=' . $path_url . '&dni=&ip=' . $cap['p_dni'] . '&het=' . $het . '&id=' . $cap['id'] . '" rel="external">' . $nuevo . '</a>');

        if ($cap['emb'] > 0) {
            print('/ <a href="info.php?t=cap&a=' . $cap['p_dni'] . "&b=" . $cap['id'] . "&c=" . $dni . '" target="new">Informe</a>
                / <a href="info_s.php?t=cap&a=' . $cap['p_dni'] . "&b=" . $cap['id'] . "&c=" . $dni . '" target="new">Sobre</a>');
        }

        print('</td></tr>');
    }
}
