<?php
/* ini_set("display_errors","1");
error_reporting(E_ALL); */
error_reporting(error_reporting() & ~E_NOTICE);
require("_database/db_tools.php");

$id_pros=explode('|',$_GET['id']);
$html='';

foreach ($id_pros as $id) {
    if ($id<>'') {
        $rRepro = $db->prepare("SELECT
        nom, ape, fnac, tip, hc_reprod.dni, hc_reprod.p_dni, hc_reprod.p_dni_het, hc_reprod.p_od, hc_reprod.med, n_fol, f_asp, f_tra, h_tra, con_iny, des_dia, des_don, obs, p_extras, t_mue
        FROM hc_paciente, hc_reprod
        WHERE hc_reprod.estado = true and hc_reprod.dni=hc_paciente.dni and hc_reprod.id=?");
        $rRepro->execute(array($id));
        $rep = $rRepro->fetch(PDO::FETCH_ASSOC);

        $calculo = $db->prepare("SELECT appinmater_modulo.calcular_total_concentraciones(?)");
        $calculo->execute(array($id));
        $calCon = $calculo->fetch(PDO::FETCH_ASSOC);

        $rPareja = $db->prepare("SELECT
        p_nom, p_ape, p_fnac, p_tip
        FROM hc_pareja
        WHERE p_dni=?");
        $rPareja->execute(array($rep['p_dni']));
        $pare = $rPareja->fetch(PDO::FETCH_ASSOC);

        $hetero='---';
        $donador = $edad1 = $edad3 = '';
        $pareja_nombres = "";
        $pareja_tipo_documento = "";
        $pareja_edad = "";

        if ($rep['fnac'] <> '1899-12-30') {
            $edad1 = '- '.date_diff(date_create($rep['fnac']), date_create('today'))->y;
        }

        if ($rPareja->rowCount() != 0) {
            $pareja_nombres = $pare['p_ape'] . " " . $pare['p_nom'];
            $pareja_tipo_documento = $pare['p_tip'];
            $pareja_edad = '- '.date_diff(date_create($pare['p_fnac']), date_create('today'))->y;
        }

        $dni_esperma=$rep['p_dni'];

        if ($rep['p_dni_het']<>'') {
            $rDonante = $db->prepare("SELECT p_nom,p_ape,p_fnac,p_tip FROM hc_pareja WHERE p_dni=?");
            $rDonante->execute(array($rep['p_dni_het']));
            $donador = $rDonante->fetch(PDO::FETCH_ASSOC);
            $hetero='';

            if ($donador['p_fnac'] <> '1899-12-30') {
                $edad3 = '- '.date_diff(date_create($donador['p_fnac']), date_create('today'))->y;
            }

            $dni_esperma=$rep['p_dni_het'];
        }

        if ($rep['p_od']<>'') {
            $rDonante = $db->prepare("SELECT nom AS p_nom,ape AS p_ape,fnac AS p_fnac,tip AS p_tip FROM hc_paciente WHERE dni=?");
            $rDonante->execute(array($rep['p_od']));
            $donador = $rDonante->fetch(PDO::FETCH_ASSOC);
            $hetero='';

            if ($donador['p_fnac'] <> '1899-12-30') {
                $edad3 = '- '.date_diff(date_create($donador['p_fnac']), date_create('today'))->y;
            }
        }

        if ($rep['des_don'] <> '') {
            $rDonante = $db->prepare("SELECT nom AS p_nom, ape AS p_ape, fnac AS p_fnac, tip AS p_tip FROM hc_paciente WHERE dni=?");
            $rDonante->execute(array($rep['des_don']));
            $donador = $rDonante->fetch(PDO::FETCH_ASSOC);
            $hetero = '';
            $edad3 = '';

            if ($donador && $donador['p_fnac'] <> '1899-12-30') {
                $edad3 = '- '.date_diff(date_create($donador['p_fnac']), date_create('today'))->y;
            }
        }

        $rMed = $db->prepare("SELECT nom FROM usuario WHERE userx=?");
        $rMed->execute(array($rep['med']));
        $med = $rMed->fetch(PDO::FETCH_ASSOC);
        $medico='';
        if(isset($med['nom']))$medico=$med['nom'];
        $html .= '<div style="width: 150px;float: right; text-align: right; font-weight: bold; font-size: 13px; padding: 8px">CICLO N° ...............<br/>FECHA: '.date("d-m-Y", strtotime($_GET['fec'])).'<br/>Dr. <small>'.$medico.'</small></div>';
        $html .= '<h2>FICHA DE LABORATORIO</h2><br/><br/><br/><br/>';
        $html .= '
        <table width="100%">
            <tr>
                <td width="50%" class="tabla">
                    <table width="100%" cellpadding="5" style="text-align:left;">
                    <tr>
                    <th width="50" align="left">PACIENTE</th>
                    <td>'.$rep['ape'].' '.$rep['nom'].' '.$edad1.'</td>
                    <td width="30">'.$rep['tip'].'</td>
                    <td width="80">'.$rep['dni'].'</td>
                    </tr>
                    <tr>
                    <th width="50" align="left">ESPOSO</th>
                    <td>'.$pareja_nombres. '  ' . $pareja_edad . '</td>
                    <td>'.$pareja_tipo_documento.'</td>
                    <td>'.$rep['p_dni'].'</td>
                    </tr>
                    <tr>
                    <th width="50" align="left">DONANTE</th>
                    <td>'.($donador ? $hetero.$donador['p_ape'].' '.$donador['p_nom'].' '.$edad3 : '').'</td>
                    <td>'.($donador ? $donador['p_tip'] : '').'</td>
                    <td>'.$rep['p_dni_het'].'</td>
                    </tr>
                    <tr>
                    <th width="50" colspan="3" align="left">Total de dosis usada de Gonadotropinas</th>
                    <td>'.$calCon['calcular_total_concentraciones'].'</td>
                    </tr>
                    </table>
                </td>
                <td class="tabla">
                    <table width="100%" cellpadding="5" style="text-align:left;">
                    <tr>
                    <td colspan="7" style="font-size: x-small;text-align: center;">'.$rep['obs'].' '.$rep['p_extras'].'</td>
                    </tr>
                    <tr>
                    <td style="font-size: x-small;text-align: center;">FIV</td>
                    <td style="font-size: x-small;text-align: center;">ICSI PIEZO</td>
                    <td style="font-size: x-small;text-align: center;">TED</td>
                    <td style="font-size: x-small;text-align: center;">CRIO OVOS</td>
                    <td style="font-size: x-small;text-align: center;">Transfer FRESCO</td>
                    <td style="font-size: x-small;text-align: center;">BANK EMBRION</td>
                    <td style="font-size: x-small;text-align: center;">DESVITRI BLASTO</td>
                    </tr>
                    <tr>
                    <td style="font-size: x-small;text-align: center;">OD</td>
                    <td style="font-size: x-small;text-align: center;">SD</td>
                    <td style="font-size: x-small;text-align: center;">NGS</td>
                    <td style="font-size: x-small;text-align: center;">DESVITRI OVOS</td>
                    <td style="font-size: x-small;text-align: center;">CRIO TOTAL</td>
                    <td style="font-size: x-small;text-align: center;">EMBRYOSC</td>
                    <td style="font-size: x-small;text-align: center;">NGS 24H</td>
                    </tr>
                    </table>
                </td>
            </tr>
        </table>';

        if ($rep['des_don'] == null) {
            $dni=$rep['dni'];
        } else {
            $dni=$rep['des_don'];
        }

        $tot_emb=0;
        $pro_con = $biolo = $hojas = $ngs1 = $ngs3 = $col = '';
        $ubi = [];
        $ngs = [];
        $rAsp = $db->prepare("SELECT
        pro, book, hoja, emb0c, emb2c, emb3c, emb4c, emb5c, emb6c
        FROM lab_aspira
        WHERE dni=? and estado is true");
        $rAsp->execute(array($dni));
        $veces=$rAsp->rowCount();

        if ($veces>0) {
            $c=0;

            while ($asp = $rAsp->fetch(PDO::FETCH_ASSOC)) {
                if ($rep['des_dia'] > 1) {
                    $rRes = $db->prepare("SELECT
                    pro, t, c, g, p, col, ngs3, ngs2, ngs1
                    from lab_aspira_dias
                    where pro=? AND des<>1 AND adju=? AND (d6f_cic='C' OR d5f_cic='C' OR d4f_cic='C' OR d3f_cic='C' OR d2f_cic='C') and estado is true
                    group by pro, t, c, g, p, col, ngs3, ngs2, ngs1");
                } else {
                    $rRes = $db->prepare("SELECT
                    pro, t, c, g, p, col, ngs3, ngs2, ngs1
                    from lab_aspira_dias
                    where pro=? AND des<>1 AND adju=? AND d0f_cic='C' and estado is true
                    group by pro, t, c, g, p, col, ngs3, ngs2, ngs1");
                }

                $rRes->execute(array($asp['pro'], $rep['dni']));

                if ($rRes->rowCount()>0) { //si existe embrion o vulo reservado
                    $tot_emb=$tot_emb+$rRes->rowCount();
                    $pro_con.=$asp['pro'].'<br>';
                    $hojas.=$asp['book'].'-'.$asp['hoja'].' '.$hojas;
                    $rEmb = $db->prepare("SELECT nom FROM lab_user WHERE id=?");
                    $rEmb->execute(array($asp['emb'.$rep['des_dia'].'c']));
                    $emb = $rEmb->fetch(PDO::FETCH_ASSOC);
                    $biolo.=$emb['nom'].'<br>';

                    while ($res = $rRes->fetch(PDO::FETCH_ASSOC)) {
                        if ($res['col'] == 1) {$col='Azul';}
                        if ($res['col'] == 2) {$col='Amarillo';}
                        if ($res['col'] == 3) {$col='Blanco';}
                        if ($res['col'] == 4) {$col='Rosado';}
                        if ($res['col'] == 5) {$col='Verde';}
                        if ($res['ngs1'] == 1) {$ngs1='Normal';}
                        if ($res['ngs1'] == 2) {$ngs1='Anormal';}
                        if ($res['ngs1'] == 3) {$ngs1='NR';}
                        if ($res['ngs1'] == 4) {$ngs1='Mosaico';}
                        if ($res['ngs3'] == 1) {$ngs3 = '(H) ';}
                        if ($res['ngs3'] == 2) {$ngs3 = '(M) ';}
                        if ($res['ngs3'] == 3) {$ngs3 = '- ';}
                        $ubi[$c] = $res['t'].'-'.$res['c'].'-'.$res['g'].'-<span style="font-size: 16px; font-weight: bold;">'.$res['p'].'</span> <span style="font-weight: bold;">'.$col.'</span>';
                        $ngs[$c] =$ngs1.$ngs3.$res['ngs2'];
                        $c++;
                    }
                }
            }
        }
        if($rep['f_tra']==$_GET['fec']) {
            $h_asp=$rep['h_tra'];
        } else {
            $f_asp = explode("T", $rep['f_asp']);
            $h_asp=$f_asp[1];
        }

        $html .= '<br/>
        <table width="100%">
            <tr>
                <td width="50%" class="tabla">
                    <table cellpadding="5" width="100%" bgcolor="#ffe4c4">
                        <tr>
                            <td style="font-size: x-small;" width="30">N° Emb/Ovu a desvitrificar</td>
                            <td style="font-size: x-small;">'.$tot_emb.'</td>
                            <td style="font-size: x-small;" width="30">Sobrevida Ovos / Emb</td>
                            <td style="font-size: x-small;"></td>
                        </tr>
                        <tr>
                            <td style="font-size: x-small;">N° Protocolo crio</td>
                            <td style="font-size: x-small;">'.$pro_con.'</td>
                            <td style="font-size: x-small;">Blgo Descongelo</td>
                            <td style="font-size: x-small;"></td>
                        </tr>
                        <tr>
                            <td style="font-size: x-small;">Blgo que congelo</td>
                            <td style="font-size: x-small;">'.$biolo.'</td>
                            <td style="font-size: x-small;">Cuaderno <br> Hoja</td>
                            <td style="font-size: x-small;">'.$hojas.'</td>
                        </tr>
                    </table>
                </td>
                <td class="tabla">
                    <table bgcolor="#e6e6fa" width="100%" cellpadding="5">
                        <tr>
                            <td width="50" style="font-size: x-small;">N° Folículos</td>
                            <td width="30" style="font-size: x-small;">'.$rep['n_fol'].'</td>
                            <td width="50" style="font-size: x-small;">Total aspirado</td>
                            <td width="70"></td>
                            <td rowspan="4" valign="top" style="font-size: x-small;">OBS:<p style="font-size: x-small;"></p></td>
                        </tr>
                        <tr>
                            <td style="font-size: x-small;">HORA</td>
                            <td style="font-size: x-small;">'.$h_asp.'</td>
                            <td style="font-size: x-small;">Hora de aspiración</td>
                            <td style="font-size: x-small; text-align: center;">:</td>
                        </tr>
                        <tr>
                            <td style="font-size: x-small;">Inyección HCG</td>
                            <td style="font-size: x-small;">'.str_replace("|", "", $rep['con_iny']).'</td>
                            <td style="font-size: x-small;">Blgo / Testigo</td>
                            <td style="font-size: x-small; text-align: center;">/</td>
                        </tr>
                        <tr>
                            <td style="font-size: x-small;">N° de RA</td>
                            <td style="font-size: x-small;">'.$veces.'</td>
                            <td style="font-size: x-small;">INCUBADORA</td>
                            <td></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>';

        $html .= '<br/>
        <div class="tabla">
            <table bgcolor="#ffe4c4" width="100%" cellpadding="10">';
        $i=0;

        while ($i < $c) {
            $html .= '
            <tr>
                <td width="50" style="font-size: x-small;">CRIO TOP</td>
                <td width="80">'.$ubi[$i].'</td>
                <td width="30" style="font-size: x-small;">NGS</td>
                <td width="50" style="font-size: x-small;">'.$ngs[$i].'</td>
                <td></td>
                <td width="30" style="font-size: x-small;">Hora</td>
                <td width="50"></td>
                <td width="50" style="font-size: x-small;">Firma<br>Blgo. Desvitri</td>
                <td width="90"></td>
                <td width="50" style="font-size: x-small;">Firma<br>Testigo</td>
                <td width="90"></td>
            </tr>';
            $i++;
        }
        
        $html .='</table></div>';
        $html .= '<br>
        <div class="tabla">
            <table bgcolor="#f0fff0" width="100%" cellpadding="10">
                <tr>
                    <td width="10%" style="font-size: x-small;">Fecha</td>
                    <td width="10%" style="font-size: x-small;">Hora</td>
                    <td width="10%" style="font-size: x-small;">Medico</td>
                    <td width="10%" style="font-size: x-small;">Blgo / Test</td>
                    <td width="10%" style="font-size: x-small;">Tipo Catéter</td>
                    <td width="10%" style="font-size: x-small;">Sangre GUIA</td>
                    <td width="10%" style="font-size: x-small;">Sangre CATETER</td>
                    <td width="10%" style="font-size: x-small;">N° INTENTOS</td>
                    <td width="10%" style="font-size: x-small;">Ecografía</td>
                    <td width="10%" style="font-size: x-small;">Endometrio</td>
                </tr>
                <tr>
                    <td></td><td style="font-size: x-small; text-align: center;">:</td><td></td><td style="font-size: x-small; text-align: center;">/</td><td></td><td></td><td></td><td></td><td></td>
                    <td style="font-size: x-small; text-align: right;">mm</td>
                </tr>
                <tr>
                    <td colspan="10">OBS:<br><br><br></td>
                </tr>
            </table>
        </div><pagebreak />';
        $Esp = $db->prepare("SELECT fec,vol_f,con_f,ins_f,pl_f,pnl_f,m_a,m_n FROM lab_andro_esp WHERE p_dni=? ORDER by fec DESC LIMIT 1");
        $Esp->execute(array($dni_esperma));
        $esp = $Esp->fetch(PDO::FETCH_ASSOC);
        $espermatograma = '';

        if ($rep['t_mue'] == 1) {$t_mue='Fresca';}
        if ($rep['t_mue'] == 2) {$t_mue='Congelada';}
        if ($rep['t_mue'] == 3) {$t_mue='No aplica';}
        if ($rep['t_mue'] == 4) {$t_mue='Banco';}
        if ($hetero=='---') {$hetero1='Homologo';} else {$hetero1='Heterologo';}
        if (isset($esp['esp']) && $esp['esp'] == 1) {$con_uni=" Spz/Camp";} else {$con_uni=" x10<sup>6";}
        if (isset($esp['fec']) && $esp['fec']<>'') {$esp_fec=date("d-m-Y", strtotime($esp['fec']));} else {$esp_fec='---';}

        if ($Esp->rowCount() > 0) {
            $espermatograma = '<tr>
                <td width="10%" style="font-size: x-small;">'.$hetero1.'</td>
                <td width="10%" style="font-size: x-small;">'.$t_mue.'</td>
                <td width="10%" style="font-size: x-small;">'.$esp_fec.'</td>
                <td width="10%" style="font-size: x-small;">'.$esp['vol_f'].'</td>
                <td width="10%" style="font-size: x-small;">'.$esp['con_f'].$con_uni.'</td>
                <td width="10%" style="font-size: x-small;">'.$esp['pl_f'].'</td>
                <td width="10%" style="font-size: x-small;">'.$esp['pnl_f'].'</td>
                <td width="10%" style="font-size: x-small;">'.$esp['ins_f'].'</td>
                <td width="10%" style="font-size: x-small;">'.$esp['m_n'].'</td>
                <td width="10%" style="font-size: x-small;">'.$esp['m_a'].'</td>
            </tr>';
        } else {
            $espermatograma = '<tr>
                <td width="10%" style="font-size: x-small;"></td>
                <td width="10%" style="font-size: x-small;"></td>
                <td width="10%" style="font-size: x-small;"></td>
                <td width="10%" style="font-size: x-small;"></td>
                <td width="10%" style="font-size: x-small;"></td>
                <td width="10%" style="font-size: x-small;"></td>
                <td width="10%" style="font-size: x-small;"></td>
                <td width="10%" style="font-size: x-small;"></td>
                <td width="10%" style="font-size: x-small;"></td>
                <td width="10%" style="font-size: x-small;"></td>
            </tr>';
        }

        $html .='
        <h3>ESPERMATOGRAMA MAS RECIENTE</h3>
        <div class="tabla">
            <table width="100%" cellpadding="10">
                <tr>
                    <td width="10%" style="font-size: x-small;">Procedencia</td>
                    <td width="10%" style="font-size: x-small;">Tipo</td>
                    <td width="10%" style="font-size: x-small;">Fecha</td>
                    <td width="10%" style="font-size: x-small;">Volumen</td>
                    <td width="10%" style="font-size: x-small;">[ ]</td>
                    <td width="10%" style="font-size: x-small;">G III</td>
                    <td width="10%" style="font-size: x-small;">G II</td>
                    <td width="10%" style="font-size: x-small;">G I</td>
                    <td width="10%" style="font-size: x-small;">Normales</td>
                    <td width="10%" style="font-size: x-small;">Anormales</td>
                </tr>
                '.$espermatograma.'
            </table><br/>
            <table bgcolor="#e6e6fa" width="100%" cellpadding="10">
                <tr>
                    <td width="50" style="font-size: x-small;">HOMOLOGO</td>
                    <td width="30" style="font-size: x-small;">HETEROLOGO</td>
                    <td width="70" style="font-size: x-small;">CODIGO SD</td>
                    <td width="70" style="font-size: x-small;">T / C / V / P</td>
                    <td rowspan="4" valign="top">OBS:</td>
                </tr>
                <tr>
                    <td style="font-size: x-small;">FRESCO</td>
                    <td style="font-size: x-small;">CONGELADO</td>
                    <td style="font-size: x-small;"></td>
                    <td style="font-size: x-small;"></td>
                </tr>
            </table>
        </div>';

        $html .= '<br/>
        <table width="100%">
            <tr>
            <td width="60%" class="tabla">
                <h3>DECUMULACIÓN</h3>
                <table width="100%" cellpadding="10">
                    <tr>
                        <td style="font-size: x-small;">BLGO</td>
                        <td style="font-size: x-small;">HORA</td>
                        <td style="font-size: x-small;">MII</td>
                        <td style="font-size: x-small;">MI</td>
                        <td style="font-size: x-small;">VG</td>
                        <td style="font-size: x-small;">ATR</td>
                        <td style="font-size: x-small;">TOTAL</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td style="font-size: x-small; text-align: center;">:</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
                <h3>ICSI – PIEZO ICSI - PICSI</h3>
                <table width="100%" cellpadding="10">
                    <tr>
                        <td style="font-size: x-small;" width="100">BLGO / TESTIGO</td>
                        <td style="font-size: x-small;" width="50">HORA</td>
                        <td style="font-size: x-small;" width="50">INS</td>
                        <td rowspan="4" valign="top" style="font-size: x-small;">OBS:</td>
                    </tr>
                    <tr>
                        <td style="font-size: x-small; text-align: center;">/</td>
                        <td style="font-size: x-small; text-align: center;">:</td>
                        <td></td>
                    </tr>
                </table>
                <h3>FIV</h3>
                <table width="100%" cellpadding="10">
                    <tr>
                        <td style="font-size: x-small;" width="100">BLGO / TESTIGO</td>
                        <td style="font-size: x-small;" width="50">HORA</td>
                        <td style="font-size: x-small;" width="50">INS</td>
                        <td rowspan="4" valign="top" style="font-size: x-small;">OBS:</td>
                    </tr>
                    <tr>
                        <td style="font-size: x-small; text-align: center;">/</td>
                        <td style="font-size: x-small; text-align: center;">:</td>
                        <td></td>
                    </tr>
                </table>
            </td>
            <td class="tabla">
                <table bgcolor="#e6e6fa" width="100%" cellpadding="10">
                    <tr>
                        <td colspan="2" width="50%" style="font-size: x-small; text-align: center;">Blgo / Testigo:</td>
                        <td colspan="2" width="50%" style="font-size: x-small; text-align: center;">/</td>
                    </tr>
                    <tr>
                        <td colspan="2" width="50%" style="font-size: x-small; text-align: center;">Nativo</td>
                        <td colspan="2" width="50%" style="font-size: x-small; text-align: center;">Capacitado</td>
                    </tr>
                    <tr>
                        <td style="font-size: x-small;">Vol.</td>
                        <td style="font-size: x-small; text-align: right;">ml</td>
                        <td style="font-size: x-small;">Vol.</td>
                        <td style="font-size: x-small; text-align: right;">ml</td>
                    </tr>
                    <tr>
                        <td style="font-size: x-small;">[ ] x 10<sup>6</td>
                        <td></td>
                        <td style="font-size: x-small;">[ ] x 10<sup>6</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="font-size: x-small;">G III</td>
                        <td></td>
                        <td style="font-size: x-small;">G III</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="font-size: x-small;">G II</td>
                        <td></td>
                        <td style="font-size: x-small;">G II</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="font-size: x-small;">G I</td>
                        <td></td>
                        <td style="font-size: x-small;">G I</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="font-size: x-small;">G 0</td>
                        <td></td>
                        <td style="font-size: x-small;">G 0</td>
                        <td></td>
                    </tr>
                </table>
            </td>
            </tr>
        </table>';
        $html .= '<br/>
        <h3>FECUNDACIÓN</h3>
        <div class="tabla">
            <table width="100%" cellpadding="10">
                <tr>
                    <td width="10%" style="font-size: x-small;">BLGO / TESTIGO</td>
                    <td width="10%" style="font-size: x-small;">FECHA</td>
                    <td width="10%" style="font-size: x-small;">HORA</td>
                    <td width="10%" style="font-size: x-small;">Fecun</td>
                    <td width="10%" style="font-size: x-small;">NF</td>
                    <td width="10%" style="font-size: x-small;">MI</td>
                    <td width="10%" style="font-size: x-small;">VG</td>
                    <td width="10%" style="font-size: x-small;">ATR</td>
                    <td width="10%" style="font-size: x-small;">CT</td>
                    <td width="10%" style="font-size: x-small;">3PN</td>
                </tr>
                <tr>
                    <td></td>
                    <td style="font-size: x-small;">'.date('d-m-Y', strtotime($_GET['fec'] . ' + 1 days')).'</td><td style="font-size: x-small; text-align: center;">:</td><td></td><td></td><td></td><td></td><td></td><td></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="10" style="font-size: x-small;">OBS:<br><br><br></td>
                </tr>
            </table>
        </div><pagebreak />';
        $fila_1a20='';

        for ($i=1;$i<=20;$i++) {
            $fila_1a20.='
            <tr>
                <td width="10%" style="font-size: x-small;text-align: center;">'.$i.'</td>
                <td width="10%"></td>
                <td width="10%"></td>
                <td width="10%"></td>
                <td width="10%"></td>
                <td width="10%"></td>
                <td width="10%"></td>
                <td width="10%"></td>
                <td width="10%"></td>
                <td width="10%"></td>
            </tr>';
        }

        $html .= '
        <div class="tabla">
            <table width="100%" cellpadding="8">
                <tr>
                    <td width="10%" style="font-size: x-small;text-align: center;"></td>
                    <td width="10%" style="font-size: x-small;text-align: center;">DIA 2</td>
                    <td width="10%" style="font-size: x-small;text-align: center;">DIA 3</td>
                    <td width="10%" style="font-size: x-small;text-align: center;">DIA 4</td>
                    <td width="10%" style="font-size: x-small;text-align: center;">DIA 5</td>
                    <td width="10%" style="font-size: x-small;text-align: center;">DIA 6</td>
                    <td width="10%" style="font-size: x-small;text-align: center;">DIA 7</td>
                    <td width="10%" style="font-size: x-small;text-align: center;">Tanque:</td>
                    <td width="10%" style="font-size: x-small;text-align: center;"></td>
                    <td rowspan="4" width="10%" style="vertical-align: middle;text-align: center;">OUT</td>
                </tr>
                <tr>
                    <td width="10%" style="font-size: x-small;text-align: center;">FECHA</td>
                    <td width="10%" style="font-size: x-small;text-align: center;">'.date('d-m-Y', strtotime($_GET['fec'] . ' + 2 days')).'</td>
                    <td width="10%" style="font-size: x-small;text-align: center;">'.date('d-m-Y', strtotime($_GET['fec'] . ' + 3 days')).'</td>
                    <td width="10%" style="font-size: x-small;text-align: center;">'.date('d-m-Y', strtotime($_GET['fec'] . ' + 4 days')).'</td>
                    <td width="10%" style="font-size: x-small;text-align: center;">'.date('d-m-Y', strtotime($_GET['fec'] . ' + 5 days')).'</td>
                    <td width="10%" style="font-size: x-small;text-align: center;">'.date('d-m-Y', strtotime($_GET['fec'] . ' + 6 days')).'</td>
                    <td width="10%" style="font-size: x-small;text-align: center;">'.date('d-m-Y', strtotime($_GET['fec'] . ' + 7 days')).'</td>
                    <td width="10%" style="font-size: x-small;text-align: center;">Canister:</td>
                    <td width="10%" style="font-size: x-small;text-align: center;"></td>
                </tr>
                <tr>
                    <td width="10%" style="font-size: x-small;text-align: center;">DIA</td>
                    <td width="10%" style="font-size: x-small;text-align: center;">'.date('l', strtotime($_GET['fec'] . ' + 2 days')).'</td>
                    <td width="10%" style="font-size: x-small;text-align: center;">'.date('l', strtotime($_GET['fec'] . ' + 3 days')).'</td>
                    <td width="10%" style="font-size: x-small;text-align: center;">'.date('l', strtotime($_GET['fec'] . ' + 4 days')).'</td>
                    <td width="10%" style="font-size: x-small;text-align: center;">'.date('l', strtotime($_GET['fec'] . ' + 5 days')).'</td>
                    <td width="10%" style="font-size: x-small;text-align: center;">'.date('l', strtotime($_GET['fec'] . ' + 6 days')).'</td>
                    <td width="10%" style="font-size: x-small;text-align: center;">'.date('l', strtotime($_GET['fec'] . ' + 7 days')).'</td>
                    <td width="10%" style="font-size: x-small;text-align: center;">Varilla:</td>
                    <td width="10%" style="font-size: x-small;text-align: center;"></td>
                </tr>
                <tr>
                    <td width="10%" style="font-size: x-small;text-align: center;">BLGO</td>
                    <td width="10%" style="font-size: x-small;text-align: center;"></td>
                    <td width="10%" style="font-size: x-small;text-align: center;"></td>
                    <td width="10%" style="font-size: x-small;text-align: center;"></td>
                    <td width="10%" style="font-size: x-small;text-align: center;"></td>
                    <td width="10%" style="font-size: x-small;text-align: center;"></td>
                    <td width="10%" style="font-size: x-small;text-align: center;"></td>
                    <td width="10%" style="font-size: x-small;text-align: center;">Criotop</td>
                    <td width="10%" style="font-size: x-small;text-align: center;">Color</td>
                </tr>'.$fila_1a20.'
            </table>
        </div>
        <pagebreak />
        <h3>OBSERVACIONES/FOTOS:</h3><pagebreak />';
    }
}

$estilo = '<style>@page {
	margin-header: 0mm;
	margin-footer: 0mm;
	margin-left: 0cm;
	margin-right: 0cm;
	header: html_myHTMLHeader;
	footer: html_myHTMLFooter;
} .xxx {margin-left: 2.3cm;margin-right: 1.7cm;} body {font-size: small;} .tabla table {border-collapse: collapse;} .tabla table, .tabla th, .tabla td {border: 1px solid #72a2aa;}</style>';
$head_foot = '<!--mpdf
<htmlpageheader name="myHTMLHeader"><img src="_images/info_head.jpg" width="100%"></htmlpageheader>
<htmlpagefooter name="myHTMLFooter"><img src="_images/info_foot.jpg" width="100%"></htmlpagefooter>
mpdf-->';

require($_SERVER["DOCUMENT_ROOT"] . "/config/environment.php");
require_once __DIR__ . '/vendor/autoload.php';
$mpdf = new \Mpdf\Mpdf($_ENV["pdf_regular_font9l"]);

$mpdf->WriteHTML($estilo . '<body><div class="xxx">' . $html . '</div></body>');
$mpdf->Output();
exit; ?>