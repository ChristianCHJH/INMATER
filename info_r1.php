<?php
    require("_database/db_tools.php");
    $pro = $_GET['a'];
    $dni = $_GET['b'];
    $p_dni = $_GET['c'];

    $rPaci = $db->prepare("SELECT nom, ape FROM hc_paciente WHERE dni=?");
    $rPaci->execute(array($dni));
    $paci = $rPaci->fetch(PDO::FETCH_ASSOC);

    if ($p_dni <> "") {
        $rPare = $db->prepare("SELECT p_nom,p_ape,p_fnac FROM hc_pareja WHERE p_dni=?");
        $rPare->execute(array($p_dni));
        $pare = $rPare->fetch(PDO::FETCH_ASSOC);

        if ($pare['p_fnac'] == '1899-12-30')
            $p_edad = ' (Edad: -)';
        else
            $p_edad = ' (Edad: '.date_diff(date_create($pare['p_fnac']), date_create('today'))->y.')';
        $pareja = $pare['p_ape'].' '.$pare['p_nom'].$p_edad;
    } else {
        $pareja = 'Soltera';
    }

    $Rpop = $db->prepare("
    SELECT
        lab_aspira.*, hc_reprod.id, hc_reprod.eda, hc_reprod.p_cic, hc_reprod.p_fiv, hc_reprod.p_icsi, hc_reprod.p_od, hc_reprod.p_don, hc_reprod.p_cri, hc_reprod.pago_extras, hc_reprod.f_mas, hc_reprod.f_fem, hc_reprod.p_dni, hc_reprod.p_dni_het, hc_reprod.med, hc_reprod.des_dia, hc_reprod.des_don, hc_reprod.f_iny
    FROM lab_aspira
    LEFT JOIN hc_reprod ON hc_reprod.id=lab_aspira.rep
    WHERE hc_reprod.estado = true and lab_aspira.estado is true and lab_aspira.pro=?");
    $Rpop->execute(array($pro));
    $pop = $Rpop->fetch(PDO::FETCH_ASSOC);
    if ($pop['tip'] <> 'T') $edad = ' (Edad: '.$pop['eda'].')';

    $rMed = $db->prepare("SELECT nom,cmp FROM usuario WHERE userx=?");
    $rMed->execute(array($pop['med']));
    $med = $rMed->fetch(PDO::FETCH_ASSOC);
    $pro_nom = '';

    if(isset($pop['p_cic']))if ($pop['p_cic'] >= 1) $pro_nom .= "CICLO NATURAL ";
    if(isset($pop['p_fiv']))if ($pop['p_fiv'] >= 1) $pro_nom .= "FIV ";
    if(isset($pop['p_icsi']))if ($pop['p_icsi'] >= 1) $pro_nom .= $_ENV["VAR_ICSI"] . " ";
    if(isset($pop['p_od']))if ($pop['p_od'] <> '') $pro_nom .= "OD FRESCO ";
    if(isset($pop['p_cri']))if ($pop['p_cri'] >= 1) $pro_nom .= "CRIO ÓVULOS ";
    if(isset($pop['p_iiu']))if ($pop['p_iiu'] >= 1) $pro_nom .= "IIU ";
    if(isset($pop['p_don']))if ($pop['p_don'] == 1) $pro_nom .= "DONACIÓN FRESCO ";
    if (is_null($pop['des_don']) and $pop['des_dia'] >= 1) $pro_nom .= "TED ";
    if (is_null($pop['des_don']) and $pop['des_dia'] === 0) $pro_nom .= "DESCONGELACIÓN ÓVULOS ";
    if (!is_null($pop['des_don']) and $pop['des_dia'] >= 1) $pro_nom .= "EMBRIODONACIÓN ";
    if (!is_null($pop['des_don']) and $pop['des_dia'] === 0) $pro_nom .= "DESCONGELACIÓN ÓVULOS DONADOS ";
    if ($pop['tip'] == 'T') $pro_nom .= "TRASLADO ";
    if ($pop['pago_extras'] <> '') $pro_nom .= "(".substr($pop['pago_extras'], 0, -1).")"; //quita el ultimo caracter (la coma)

    $html = '<h1>LABORATORIO DE REPRODUCCIÓN ASISTIDA</h1><h4>DATOS DEL PROCEDIMIENTO</h4>';
    $html .= '<blockquote><table border="0" align="left">
    <tr>
    <th width="200" align="left">Paciente</th><td>'.$paci['ape'].' '.$paci['nom'].$edad.'</td>
    </tr>
    <tr>
    <th align="left">Pareja</th><td>'.$pareja.'</td>
    </tr>
    <tr>
    <th align="left">Médico</th><td>'.$med['nom'].' (CMP '.$med['cmp'].')</td>
    </tr>
    <tr>';

    if ($pop['des_dia'] >= 1) // si es TED o embrioadpocion
        $html .= '<th align="left">Fecha de Transferencia</th><td>'.date("d-m-Y", strtotime($pop['f_iny'])).'</td>';
    else if ($pop['des_dia'] === 0) // si es descongelacion de ovos
        $html .= '<th align="left">Fecha de Descongelación</th><td>'.date("d-m-Y", strtotime($pop['fec0'])).'</td>';
    else if ($pop['tip'] == 'T') // traslado
        $html .= '<th align="left"></th><td></td>';
    else
        $html .= '<th align="left">Fecha de Punción</th><td>'.date("d-m-Y", strtotime($pop['f_pun'])).'</td>';

    $html.='</tr>
    <tr>
    <th align="left">Tipo de procedimiento realizado</th><td>'.$pro_nom.'</td>
    </tr>
    <tr>';

    if ($pop['des_dia'] >= 1) // si es TED o embrioadpocion
        $html .= '<th align="left">Embriones Desvitrificados</th><td>'.$pop['n_ovo'].'</td>';
    else if ($pop['des_dia'] === 0) // si es Descongelacion Ovos
        $html .= '<th align="left">Óvulos Desvitrificados</th><td>'.$pop['n_ovo'].'</td>';
    else if ($pop['tip'] == 'T') // traslado
        $html .= '<th align="left"></th><td></td>';
    else
        $html .= '<th align="left">Óvulos aspirados</th><td>'.$pop['n_ovo'].'</td>';

    $html.='</tr>
    </table></blockquote>';
    $Cap = $db->prepare("SELECT * FROM lab_andro_cap WHERE (pro=? or rep =?) and eliminado is false");
    $Cap->execute(array($pro, $pop['id']));

    if ($Cap->rowCount() > 0) {
        $html .= '<h4>CARACTERÍSTICA DE LA MUESTRA SEMINAL</h4>';

        while ($cap = $Cap->fetch(PDO::FETCH_ASSOC)) {
            if ($cap['mue'] == 1) $mue = "Fresca + Homólogo";
            if ($cap['mue'] == 2) $mue = "Fresca + Heterólogo";
            if ($cap['mue'] == 3) $mue = "Criopreservada + Homólogo";
            if ($cap['mue'] == 4) $mue = "Criopreservada + Heterólogo";
            if ($cap['cap'] == 1) $t_cap = "Gradiente densidad";
            if ($cap['cap'] == 2) $t_cap = "Lavado";
            if ($cap['cap'] == 3) $t_cap = "Swim up";
            if ($cap['sel'] == 1) $sel = "Aleatoria";
            if ($cap['sel'] == 2) $sel = "Masculina";
            if ($cap['sel'] == 3) $sel = "Femenina";

            $html .= '<blockquote class="tabla"><b>Tipo de Muestra:</b> '.$mue.'<br><b>Método de capacitación:</b> '.$t_cap.'<br><b>Tipo de selección espermática:</b> '.$sel.'<br><br><table style="text-align:center;"><tr>
            <th width="150" align="left"></th><th>Semen Nativo</th><th>Capacitado</th>
            </tr>
            <tr>
            <th align="left">Volumen</th><td>'.$cap['vol_f'].'ml</td><td>0.3 ml</td>
            </tr>
            <tr>
            <th align="left">Concentración</th><td>'.$cap['con_f'].'x10<sup>6</td><td>'.$cap['con_c'].'x10<sup>6</td>
            </tr>
            <tr>
            <th align="left">Motilidad III + II</th><td>'.($cap['pl_f'] + $cap['pnl_f']).'%</td><td>'.($cap['pl_c'] + $cap['pnl_c']).'%</td>
            </tr>
            </table></blockquote>';
        }
    }

    // Inicio de Fecundacion y desarrollo
    if ($pop['dias'] >= 0) {
        $rAspi = $db->prepare("
        SELECT *
        FROM lab_aspira_dias
        WHERE pro=? and estado is true
        ORDER BY ovo ASC");
        $rAspi->execute(array($pro));
        $eval = '';
        $c_C = 0;
        $c_T = 0;
        $fotos = '';
        $no_fec = 0;
        $pn2 = 0;
        $pn3 = 0;
        $inma = 0;
        $atre = 0;
        $ct = 0;
        $vitri = 0;
        $m1 = 0;
        $vg = 0;
        $atr = 0;

        while ($asp = $rAspi->fetch(PDO::FETCH_ASSOC)) {
            if ($pop['p_cri'] >= 1)
            {
                // Para crio ovos MII es Numero de Vitrificados
                if ($asp['d0est'] == 'MII') $vitri++;
                if ($asp['d0est'] == 'MI') $m1++;
                if ($asp['d0est'] == 'VG') $vg++;
                if ($asp['d0est'] == 'ATR') $atr++;
                if ($asp['d0f_cic'] == 'C')
                    $eval .= '
                    <tr>
                        <th>'.$asp['ovo'].'</th>
                        <td>'.$asp['t'].'</td>
                        <td>'.$asp['c'].'</td>
                        <td>'.$asp['g'].'</td>
                        <td>'.$asp['p'].'</td>
                    </tr>';
            }

            if (file_exists("emb_pic/p".$pro."d0_".$asp['ovo'].".jpg")) {
                $fotos .= "
                <div style='float: left; width: 200px; margin: 0 5px'>
                    <small>Ovulo: ".$asp['ovo']." Día 0</small>
                    <img src='emb_pic/p".$pro."d0_".$asp['ovo'].".jpg' />
                </div>";
            }

            if (file_exists("emb_pic/p".$pro."d1_".$asp['ovo'].".jpg")) {
                $fotos .= "
                <div style='float: left; width: 200px; margin: 0 5px'>
                    <small>Embrión: ".$asp['ovo']." Día 1</small>
                    <img src='emb_pic/p".$pro."d1_".$asp['ovo'].".jpg' />
                </div>";
            }

            //Fecundados: MII y OBS
            if ($asp['d1est'] == 'MII' and $asp['d1f_cic'] == 'O' and $asp['d1c_pol'] == '2' and $asp['d1pron'] == '2') $pn2++;
            //NO Fecundados: MII y NV
            if ($asp['d1est'] == 'MII' and $asp['d1f_cic'] == 'N' and (($asp['d1c_pol'] == '0' or $asp['d1c_pol'] == '1' or $asp['d1c_pol'] == '2') and ($asp['d1pron'] == '0' or $asp['d1pron'] == '1' or $asp['d1pron'] == '2'))) $no_fec++;
            //Triploides / multinucleado: MII y NV y ademas cp y pn mayor q 2 
            if ($asp['d1est'] == 'MII' and $asp['d1f_cic'] == 'N' and (($asp['d1c_pol'] == '3' or $asp['d1c_pol'] == '4' or $asp['d1c_pol'] == 'mult' or $asp['d1pron'] == '3' or $asp['d1pron'] == '4' or $asp['d1pron'] == 'mult'))) $pn3++;
            //Inmaduros: MI o VG
            if ($asp['d1est'] == 'VG' or $asp['d1est'] == 'MI' or $asp['d0est'] == 'VG' or $asp['d0est'] == 'MI') $inma++;
            //Atresicos: ATR
            if ($asp['d1est'] == 'ATR' or $asp['d0est'] == 'ATR') $atre++;
            //Citolizados: CT
            if ($asp['d1est'] == 'CT' or $asp['d0est'] == 'CT') $ct++;
            // todos los ovos q pasan el dia 1 entran a la evaluacion del desarrollo
            if (($asp['d1f_cic'] == 'O' or $pop['tip'] == 'T' or $pop['des_dia'] >= 1) and $pop['dias'] >= 3) {
                $bio = 'No';
                $fin = '';

                if (is_null($pop['des_don']) and !is_null($pop['des_dia'])) { // si es TED o desc Ovulos muestra el id original
                    $Pro_c = $db->prepare("SELECT fec FROM lab_aspira WHERE pro=? and estado is true");
                    $Pro_c->execute(array($asp['pro_c']));
                    $pro_c = $Pro_c->fetch(PDO::FETCH_ASSOC);
                    $eval .= '<tr><td>'.$asp['ovo_c'].'<br><i style="font-size:xx-small;">('.date("d-m-Y", strtotime($pro_c['fec'])).')</i></td>';
                } else
                    $eval .= '<tr><td>'.$asp['ovo'].'</td>';

                if ($pop['dias'] >= 3) {
                    if ($asp['d2f_cic'] == 'C') {
                        $c_C++;
                        $fin = 'CRIO';
                    }

                    if ($asp['d2f_cic'] == 'T') {
                        $c_T++;
                        $fin = 'Transferido';
                    }

                    if ($asp['d2f_cic'] == 'N') $fin = 'NV';

                    if (file_exists("emb_pic/p".$pro."d2_".$asp['ovo'].".jpg"))
                    {
                        $fotos .= "
                        <div style='float: left; width: 200px; margin: 0 5px'>
                            <small>Embrión: ".$asp['ovo']." Día 2</small>
                            <img src='emb_pic/p".$pro."d2_".$asp['ovo'].".jpg' />
                        </div>";
                    }

                    if ($asp['d2f_cic'] <> '') {
                        $eval .= '<td>'.$asp['d2cel'].'-'.$asp['d2fra'].'%-'.$asp['d2sim'].'</td>';
                    } else $eval .= '<td>-</td>';
                }

                if ($pop['dias'] >= 4) {
                    if ($asp['d3f_cic'] == 'C') {
                        $c_C++;
                        $fin = 'CRIO';
                    }

                    if ($asp['d3f_cic'] == 'T') {
                        $c_T++;
                        $fin = 'Transferido';
                    }

                    if ($asp['d3f_cic'] == 'N') $fin = 'NV';

                    if (file_exists("emb_pic/p".$pro."d3_".$asp['ovo'].".jpg"))
                    {
                        $fotos .= "
                        <div style='float: left; width: 200px; margin: 0 5px'>
                            <small>Embrión: ".$asp['ovo']." Día 3</small>
                            <img src='emb_pic/p".$pro."d3_".$asp['ovo'].".jpg' />
                        </div>";
                    }

                    if ($asp['d3f_cic'] <> '') {
                        if ($asp['d3c_bio'] > 0) $bio = 'Si';
                        $eval .= '<td>'.$asp['d3cel'].'-'.$asp['d3fra'].'%-'.$asp['d3sim'].'</td>';
                    } else $eval .= '<td>-</td>';
                }

                if ($pop['dias'] >= 5) {
                    if ($asp['d4f_cic'] == 'C') {
                        $c_C++;
                        $fin = 'CRIO';
                    }

                    if ($asp['d4f_cic'] == 'T') {
                        $c_T++;
                        $fin = 'Transferido';
                    }

                    if ($asp['d4f_cic'] == 'N') $fin = 'NV';

                    if (file_exists("emb_pic/p".$pro."d4_".$asp['ovo'].".jpg"))
                    {
                        $fotos .= "
                        <div style='float: left; width: 200px; margin: 0 5px'>
                            <small>Embrión: ".$asp['ovo']." Día 4</small>
                            <img src='emb_pic/p".$pro."d4_".$asp['ovo'].".jpg' /><br>
                        </div>";
                    }

                    if ($asp['d4f_cic'] <> '') {
                        $eval .= '<td>'.$asp['d4cel'].'-'.$asp['d4fra'].'%-'.$asp['d4sim'].'</td>';
                    } else $eval .= '<td>-</td>';
                }

                if ($pop['dias'] >= 6) {
                    if ($asp['d5f_cic'] == 'C') {
                        $c_C++;
                        $fin = 'CRIO';
                    }

                    if ($asp['d5f_cic'] == 'T') {
                        $c_T++;
                        $fin = 'Transferido';
                    }

                    if ($asp['d5f_cic'] == 'N') $fin = 'NV';

                    if (file_exists("emb_pic/p".$pro."d5_".$asp['ovo'].".jpg"))
                    {
                        $fotos .= "
                        <div style='float: left; width: 200px; margin: 0 5px'>
                            <small>Embrión: ".$asp['ovo']." Día 5</small>
                            <img src='emb_pic/p".$pro."d5_".$asp['ovo'].".jpg' />
                        </div>";
                    }

                    if ($asp['d5f_cic'] <> '') {
                        if ($asp['d5d_bio'] != 0) {
                            $bio = 'Si';
                        } else {
                            $bio = 'No';
                        }

                        if ($asp['d5cel'] == 'BC' or $asp['d5cel'] == 'BE' or $asp['d5cel'] == 'BHI' or $asp['d5cel'] == 'BH')
                            $eval .= '<td>'.$asp['d5cel'].'-'.$asp['d5mci'].'-'.$asp['d5tro'].'</td>';
                        else
                            $eval .= '<td>'.$asp['d5cel'].'</td>';
                    } else $eval .= '<td>-</td>';
                }

                if ($pop['dias'] >= 7) {
                    if ($asp['d6f_cic'] == 'C') {
                        $c_C++;
                        $fin = 'CRIO';
                    }

                    if ($asp['d6f_cic'] == 'T') {
                        $c_T++;
                        $fin = 'Transferido';
                    }

                    if ($asp['d6f_cic'] == 'N')
                        $fin = 'NV';

                    if (file_exists("emb_pic/p".$pro."d6_".$asp['ovo'].".jpg"))
                        $fotos .= "
                        <div style='float: left; width: 200px; margin: 0 5px'>
                            <small>Embrión: ".$asp['ovo']." Día 6</small>
                            <img src='emb_pic/p".$pro."d6_".$asp['ovo'].".jpg' />
                        </div>";

                    if ($asp['d6f_cic'] <> '') {
                        if ($asp['d6d_bio'] != 0) {
                            $bio = 'Si';
                        } else {
                            $bio = 'No';
                        }

                        if ($asp['d6cel'] == 'BC' or $asp['d6cel'] == 'BE' or $asp['d6cel'] == 'BHI' or $asp['d6cel'] == 'BH')
                            $eval .= '<td>'.$asp['d6cel'].'-'.$asp['d6mci'].'-'.$asp['d6tro'].'</td>';
                        else
                            $eval .= '<td>'.$asp['d6cel'].'</td>';
                    } else $eval .= '<td>-</td>';
                }

                if ($fin == 'CRIO')
                    $tanque = ' ('.$asp['t'].'-'.$asp['c'].'-'.$asp['g'].'-'.$asp['p'].')';
                else
                    $tanque = '';

                $eval .= '<td>'.$bio.'</td>';

                if ($pop['dias'] == 6 and strpos($pop['pago_extras'], "EMBRYOSCOPE") !== false) {
                    $eval .= '<td>'.$asp['d5kid'].'</td>';
                }

                if ($pop['dias'] == 7 and strpos($pop['pago_extras'], "EMBRYOSCOPE") !== false) {
                    $eval .= '<td>'.$asp['d6kid'].'</td>';
                }

                $eval .= '<td>'.$fin.$tanque.'</td></tr>';
            }
        }

        // Para todos menos para descongelacion de embriones (des_dia>1) y Traslado
        if ((is_null($pop['des_dia']) or $pop['des_dia'] === 0) and $pop['tip'] <> 'T' and $pop['p_cri'] == 0) {
            if ($pop['n_ins'] == 0) $n_ins = $pop['n_ovo']; else $n_ins = $pop['n_ins'];
            $html .= '
            <h4>INSEMINACIÓN</h4>
            <blockquote>
                <table style="text-align:center;">
                    <tr><th width="200" align="left">Óvulos Inseminados</th><td>'.$n_ins.'</td></tr>
                    <tr><th align="left">Fecundados</th><td>'.$pn2.'</td></tr>
                    <tr><th align="left">NO Fecundados</th><td>'.$no_fec.'</td></tr>
                    <tr><th align="left">Triploides / Multinucleado</th><td>'.$pn3.'</td></tr>
                    <tr><th align="left">Inmaduros</th><td>'.$inma.'</td></tr>
                    <tr><th align="left">Atrésicos</th><td>'.$atre.'</td></tr>
                    <tr><th align="left">Citolizados</th><td>'.$ct.'</td></tr>
                </table>
            </blockquote>';
        }

        if ($pop['p_cri'] >= 1) {
            $html .= '
            <h4>DETALLE DE ÓVULOS</h4>
            <blockquote>
                <table style="text-align:center;">
                    <tr>
                    <th width="200" align="left">Vitrificados</th><td><b>'.$vitri.'</b></td>
                    </tr>
                    <tr>
                    <th align="left">MI</th><td>'.$m1.'</td>
                    </tr>
                    <tr>
                    <th align="left">VG</th><td>'.$vg.'</td>
                    </tr>
                    <tr>
                    <th align="left">Atrésicos</th><td>'.$atr.'</td>
                    </tr>
                </table>
            </blockquote>';
            $html .= '<h4>UBICACIÓN DE OVULOS VITRIFICADOS</h4><blockquote class="tabla"><table cellpadding="5" style="text-align:center;">';
            $html .= '<tr><th>ID Ovulo</th><th>Tanque</th><th>Canister</th><th>Gobelet</th><th>Pajuela</th></tr>'.$eval.'</table></blockquote>';
        }

        if ($pop['dias'] >= 3) {
            if ($pop['fec2'] == '1899-12-30') $fec2 = '-'; else $fec2 = date("d/m", strtotime($pop['fec2']));
            if ($pop['fec3'] == '1899-12-30') $fec3 = '-'; else $fec3 = date("d/m", strtotime($pop['fec3']));
            if ($pop['fec4'] == '1899-12-30') $fec4 = '-'; else $fec4 = date("d/m", strtotime($pop['fec4']));
            if ($pop['fec5'] == '1899-12-30') $fec5 = '-'; else $fec5 = date("d/m", strtotime($pop['fec5']));
            if ($pop['fec6'] == '1899-12-30') $fec6 = '-'; else $fec6 = date("d/m", strtotime($pop['fec6']));

            $html .= '<h4>EVALUACIÓN DEL DESARROLLO</h4><blockquote class="tabla"><table cellpadding="5" style="text-align:center;">';

            if ($pop['dias'] == 3)
                $head_eval = '<th>DIA 2</th><th rowspan="2">Biopsia</th><th rowspan="2">OUT</th></tr><tr><td>'.$fec2.'</td></tr>';

            if ($pop['dias'] == 4)
                $head_eval = '<th>DIA 2</th><th>DIA 3</th><th rowspan="2">Biopsia</th><th rowspan="2">OUT</th></tr><tr><td>'.$fec2.'</td><td>'.$fec3.'</td></tr>';

            if ($pop['dias'] == 5)
                $head_eval = '<th>DIA 2</th><th>DIA 3</th><th>DIA 4</th><th rowspan="2">Biopsia</th><th rowspan="2">OUT</th></tr><tr><td>'.$fec2.'</td><td>'.$fec3.'</td><td>'.$fec4.'</td></tr>';

            if ($pop['dias'] == 6) {
                $head_eval = '
                    <th>DIA 2</th>
                    <th>DIA 3</th>
                    <th>DIA 4</th>
                    <th>DIA 5</th>
                    <th rowspan="2">Biopsia</th>';

                if (strpos($pop['pago_extras'], "EMBRYOSCOPE") !== false) {
                    $head_eval .= '<th rowspan="2">EMBRYOSCOPE</th>';
                }

                $head_eval .= '<th rowspan="2">OUT</th>
                    </tr>
                    <tr>
                        <td>'.$fec2.'</td>
                        <td>'.$fec3.'</td>
                        <td>'.$fec4.'</td>
                        <td>'.$fec5.'</td>
                    </tr>';
            }

            if ($pop['dias'] == 7) {
                $head_eval = '
                    <th>DIA<br>2</th>
                    <th>DIA<br>3</th>
                    <th>DIA<br>4</th>
                    <th>DIA<br>5</th>
                    <th>DIA<br>6</th>
                    <th rowspan="2">Biopsia</th>';

                if (strpos($pop['pago_extras'], "EMBRYOSCOPE") !== false) {
                    $head_eval .= '<th rowspan="2">EMBRYOSCOPE</th>';
                }

                $head_eval .= '<th rowspan="2">OUT</th>
                </tr>
                <tr>
                    <td>'.$fec2.'</td>
                    <td>'.$fec3.'</td>
                    <td>'.$fec4.'</td>
                    <td>'.$fec5.'</td>
                    <td>'.$fec6.'</td>
                </tr>';
            }

            $html .= '
                <tr>
                    <th rowspan="2">ID<br>Embrión</th>'.$head_eval.$eval.'</table>
            <p style="font-weight: bold;">Total Transferidos: '.$c_T.' &nbsp;&nbsp;&nbsp;Total Criopreservados: '.$c_C.'</p></blockquote>';
        }
    }

    // Muestra ovulos o Embriones AUN VITRIFICADOS PROPIOS
    if (!is_null($pop['des_dia']) and is_null($pop['des_don'])) {
        $rAsp = $db->prepare("SELECT lab_aspira.pro,lab_aspira.fec,hc_reprod.p_dni,hc_reprod.p_dni_het FROM hc_reprod,lab_aspira WHERE hc_reprod.estado = true and lab_aspira.estado is true and hc_reprod.id=lab_aspira.rep AND lab_aspira.dni=? AND lab_aspira.fec < '".$pop['fec']."'");
        $rAsp->execute(array($dni));

        if ($rAsp->rowCount() > 0) {
            if ($pop['des_dia'] === 0) $ovo_emb = 'ÓVULOS';
            if ($pop['des_dia'] >= 1) $ovo_emb = 'EMBRIONES';
            $html .= '<h4>'.$ovo_emb.' AUN VITRIFICADOS</h4><blockquote class="tabla"><table cellpadding="5" style="text-align:center;"><tr><th>Cantidad</th><th>Fecha</th></tr>';

            while ($asp = $rAsp->fetch(PDO::FETCH_ASSOC)) {
                $rRes = $db->prepare("SELECT d0f_cic,d2f_cic,d3f_cic,d4f_cic,d5f_cic,d6f_cic FROM lab_aspira_dias WHERE pro=? AND des<>1 and estado is true");
                $rRes->execute(array($asp['pro']));

                if ($rRes->rowCount() > 0) {
                    $d0 = 0;
                    $d2 = 0;
                    $d3 = 0;
                    $d4 = 0;
                    $d5 = 0;
                    $d6 = 0;

                    while ($res = $rRes->fetch(PDO::FETCH_ASSOC)) {
                        if ($res['d0f_cic'] == 'C') $d0++;
                        if ($res['d2f_cic'] == 'C') $d2++;
                        if ($res['d3f_cic'] == 'C') $d3++;
                        if ($res['d4f_cic'] == 'C') $d4++;
                        if ($res['d5f_cic'] == 'C') $d5++;
                        if ($res['d6f_cic'] == 'C') $d6++;
                    }

                    $infoX = '<td>'.date("d-m-Y", strtotime($asp['fec'])).'</td>';
                    if ($pop['des_dia'] === 0 and $d0 > 0) $html .= '<tr><td>'.$d0.'</td>'.$infoX.'</tr>';
                    if ($pop['des_dia'] >= 1 and ($d2 > 0 or $d3 > 0 or $d4 > 0 or $d5 > 0 or $d6 > 0)) $html .= '<tr><td>'.($d2 + $d3 + $d4 + $d5 + $d6).'</td>'.$infoX.'</tr>';
                }
            }

            $html.='</table></blockquote>';
        }
    }

    if ($pop['obs'] <> "") $html .= '<h4>CONCLUSIONES</h4><blockquote>'.$pop['obs'].'</blockquote>';

    if ($pop['dias'] > 1) {
        $html .= '<h5>Leyenda</h5><ul style="font-size:10px;"><li>MCi: Mórula iniciando la compactación</li>
        <li>MCc: Mórula con compactación completa</li>
        <li>BT: Blastocisto temprano</li>
        <li>BC: Blastocisto cavitado</li>
        <li>BX: Blastocisto Expandido</li>
        <li>BHi: Blastocisto iniciando hatching</li>
        <li>BH: Blastocisto hatched</li>
        <li>NC: No Clivado</li>
        <li>VT: Vesícula trofoblástica</li></ul>';
    }

    if ($fotos <> '') $html .= '<h4>FOTOS DE OVULOS/EMBRIONES</h4>'.$fotos;

    $estilo = '<style>@page {
        margin-header: 0mm;
        margin-footer: 0mm;
        margin-left: 0cm;
        margin-right: 0cm;
        margin-top: 3cm;
        header: html_myHTMLHeader;
        footer: html_myHTMLFooter;
    } .xxx {margin-left: 2.3cm;margin-right: 1.7cm;} .tabla table {border-collapse: collapse;} .tabla table, .tabla th, .tabla td {border: 1px solid #72a2aa;}</style>';

    $head_foot = '<!--mpdf
    <htmlpageheader name="myHTMLHeader"><img src="_images/info_head.jpg" width="100%"></htmlpageheader>
    <htmlpagefooter name="myHTMLFooter"><img src="_images/info_foot.jpg" width="100%"></htmlpagefooter>
    mpdf-->';

    require($_SERVER["DOCUMENT_ROOT"] . "/config/environment.php");
    require_once __DIR__ . '/vendor/autoload.php';
    $mpdf = new \Mpdf\Mpdf($_ENV["pdf_regular_notfont"]);

    $mpdf->WriteHTML($estilo.'<body><div class="xxx">'.$head_foot.$html.'</div></body>');
    $mpdf->Output();
    //echo $head_foot.$html;
    exit;
?>