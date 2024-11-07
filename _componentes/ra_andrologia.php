<?php
/* print('<pre>'); print_r($rPare); print('</pre>');
print('<pre>'); print_r($repro); print('</pre>'); */
if ($repro['des_dia'] < 1) {
        if ($repro['t_mue'] == 1) { $t_mue = '(Muestra Fresca)'; }
        if ($repro['t_mue'] == 2) { $t_mue = '(Muestra Congelada)'; } 
        if ($repro['t_mue'] == 4) { $t_mue = '(Muestra Banco)'; } ?>

    <div data-role="collapsible">
        <h3>Andrología <?php if(isset($t_mue))print($t_mue); ?></h3>
        <?php
        if (!empty($repro['p_dni']) && $repro['p_dni'] <> '1') { ?>
            <div data-role="collapsible" data-mini="true" data-collapsed-icon="user" data-expanded-icon="user" data-theme="c" data-content-theme="a">
                <h4>PAREJA: <?php echo $pareja ?></h4>
                <?php
                // buscamos espermatogramas
                $stmt = $db->prepare("SELECT * FROM lab_andro_esp WHERE p_dni = ? ORDER BY fec DESC;");
                $stmt->execute(array($repro['p_dni']));

                if ($stmt->rowCount() > 0) { ?>
                    <table width="80%" style="margin: 0 auto;" class="peke">
                        <tr>
                            <th width="11%" align="left">Vol.</th>
                            <th width="11%" align="left">Con.</th>
                            <th width="11%" align="left">Viabi.</th>
                            <th width="11%" align="left">Ph</th>
                            <th width="11%" align="left">Morfo.</th>
                            <th width="11%" align="left">Moti.</th>
                            <th width="11%" align="left">Fecha</th>
                        </tr>

                        <?php
                        while ($esp = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
                            <tr style="font-size:small">
                                <td><?php if ($esp['emb'] > 0) { echo $esp['vol_f'].'ml'; } ?></td>
                                <td>
                                    <?php
                                    if ($esp['emb'] > 0) {
                                        echo $esp['con_f'];
                                    } ?>
                                </td>
                                <td><?php if ($esp['emb'] > 0) { echo $esp['via'].'%'; } ?></td>
                                <td><?php if ($esp['emb'] > 0) { echo $esp['ph']; } ?></td>
                                <td><?php if ($esp['emb'] > 0 && $esp['m_a'] > 0) { echo round(100 - (($esp['m_a'] * 100) / ($esp['m_a'] + $esp['m_n'])), 2).'%'; } ?></td>
                                <td><?php if ($esp['emb'] > 0) { echo ($esp['pl_f'] + $esp['pnl_f']).'%'; } ?></td>
                                <td><?php echo date("d-m-Y", strtotime($esp['fec'])); ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                <?php } else { print('No tiene Espermatograma'); }

                // buscar en los tanques viales congelados o criopreservados
                $stmt = $db->prepare("SELECT tip FROM lab_tanque_res WHERE sta = ?;");
                $stmt->execute([$repro['p_dni']]);

                if ($stmt->rowCount() > 0) {
                    $c_bio = 0;
                    $c_cri = 0;

                    while ($con = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        if ($con['tip'] == 1) { $c_bio++; }
                        if ($con['tip'] == 2) { $c_cri++; }
                    }

                    print("<p>Congelados Biopsia: " . $c_bio . "<br>Congelados Criopreservación: " . $c_cri . "</p>");
                }

                $rDes = $db->prepare("SELECT des, des_tip, pro FROM lab_andro_cap WHERE des_dni = ? and eliminado is false ORDER BY des_tip;");
                $rDes->execute([$repro['p_dni']]);

                if ($rDes->rowCount() > 0) {
                    while ($des = $rDes->fetch(PDO::FETCH_ASSOC)) {
                        if ($des['des_tip'] > 0) {
                            $n_des = explode('|', $des['des']);
                            $total = count($n_des) - 1;
                            if ($des['des_tip'] == 1) { $des_tip = "Descongelado Biopsia: "; }
                            if ($des['des_tip'] == 2) { $des_tip = "Descongelado Criopreservación: "; }
                            if ($des['pro'] <> "" && $des['pro'] <> 0) { $des_pro = " (Protocolo ".$des['pro'].")"; }
                            echo $des_tip.$total.$des_pro."<br>";
                        }
                    }
                } ?>
            </div>
        <?php } ?>

        <div data-role="controlgroup" data-mini="true">
            <?php
            $stmt =  $db->prepare("SELECT
            hc_pareja.p_dni, p_nom, p_ape, p_san, p_raz, p_pes, p_tal, p_ojo, p_cab, p_ins, p_icq
            from hc_pareja, hc_pare_paci
            where hc_pareja.p_dni = hc_pare_paci.p_dni and hc_pare_paci.p_het = ? and hc_pare_paci.p_dni = ?
            order by hc_pare_paci.p_fec desc;");
            $stmt->execute([1, $repro['p_dni_het']]);
            $heteActual = $stmt->fetch(PDO::FETCH_ASSOC);
                        
            if (isset($heteActual['p_dni']) && is_string($heteActual['p_dni'])) {
            $arr = str_split($heteActual['p_dni']);
            $arrayLength = count($arr);
            $arr_dni = '';
            for ($i = 0; $i < $arrayLength - 2; $i++) {
                $arr_dni = $arr_dni . $arr[$i];
            } 
            } 

            ?>

            <label for="hete_chk">DONANTE (Heterólogo) <?php if(isset($heteActual['p_ape']))echo $heteActual['p_ape'] . $heteActual['p_nom'] . '-' . $arr_dni . $arr[$arrayLength - 1] . $arr[$arrayLength - 2] ?></label>
            <input type="checkbox" name="hete_chk" id="hete_chk" data-mini="true" <?php if ($repro['p_dni_het'] <> "") { echo "checked"; } ?> data-inline="true">
            <input type="hidden" name="p_dni_het" id="p_dni_het" value="<?php echo $repro['p_dni_het']; ?>">
            <input type="hidden" name="p_dni" id="p_dni" value="<?php echo $repro['p_dni']; ?>">

            <div class="hetes">
                <input id="searchForCollapsibleSet" data-type="search" data-inline="true">
                <div data-role="collapsible-set" data-filter="true" data-inset="true" id="collapsiblesetForFilter" data-input="#searchForCollapsibleSet">
                    <?php
                    $rHete = $db->prepare("SELECT hc_pareja.p_dni, upper(p_nom) p_nom, upper(p_ape) p_ape, p_san, p_raz, p_pes, p_tal, p_ojo, p_cab, p_ins, p_icq
                        from hc_pareja, hc_pare_paci
                        where hc_pareja.p_dni = hc_pare_paci.p_dni and hc_pare_paci.p_het = 1
                        and exists (select t, c, v from lab_tanque_res where sta = hc_pareja.p_dni)
                        order by hc_pare_paci.p_fec desc;");
                    $rHete->execute();

                    while ($hete = $rHete->fetch(PDO::FETCH_ASSOC)) {
                        #region
                            $p_raz = $p_san = $p_cab = $p_ojo = $p_tal = $p_pes = $p_ins = $p_icq = "";
                            if ($hete['p_cab'] == 1) { $p_cab = " negro"; }
                            if ($hete['p_cab'] == 2) { $p_cab = " castaño"; }
                            if ($hete['p_cab'] == 3) { $p_cab = " rubio"; }
                            if ($hete['p_cab'] == 4) { $p_cab = " pelirojo"; }
                            if ($hete['p_ojo'] == 1) { $p_ojo = " negro"; }
                            if ($hete['p_ojo'] == 2) { $p_ojo = " pardo"; }
                            if ($hete['p_ojo'] == 3) { $p_ojo = " verde"; }
                            if ($hete['p_ojo'] == 4) { $p_ojo = " azul"; }
                            if ($hete['p_ojo'] == 5) { $p_ojo = " gris"; }
                            if ($hete['p_ins'] == 1) { $p_ins = "instrucción Incial"; }
                            if ($hete['p_ins'] == 2) { $p_ins = "instrucción Secundaria"; }
                            if ($hete['p_ins'] == 3) { $p_ins = "instrucción Tecnico"; }
                            if ($hete['p_ins'] == 4) { $p_ins = "instrucción Universitaria"; }
                            if ($hete['p_ins'] == 5) { $p_ins = "instrucción Postgrado"; }
                            if ($hete['p_raz'] <> '') { $p_raz = $hete['p_raz']; }
                            if ($hete['p_san'] <> '') { $p_san = ", ".$hete['p_san']; }
                            if ($hete['p_tal'] <> '') { $p_tal = $hete['p_tal']."m"; }
                            if ($hete['p_pes'] <> '') { $p_pes = $hete['p_pes']."kg"; }
                            if ($hete['p_icq'] <> '') { $p_icq = ", IQ: ".$hete['p_icq']; }
                        #endregion ?>
                        <div data-role="collapsible" class="hetes2" id="<?php echo $hete['p_dni']; ?>">
                            <h3>
                                <?php
                                // echo $p_raz . $p_san . $p_cab . $p_ojo . $p_tal . $p_pes;
                                $arr = str_split($hete['p_dni']);
                                $arrayLength = count($arr);
                                $arr_dni = '';

                                for ($i = 0; $i < $arrayLength - 2; $i++) {
                                    $arr_dni = $arr_dni . $arr[$i];
                                }

                                // echo 'Nombres: ' . $hete['p_ape'] . $hete['p_nom'] . ' - ' . $arr_dni . $arr[$arrayLength-1] . $arr[$arrayLength - 2]; ?>
                                <div class="ui-grid-d">
                                    <?php
                                    $foto1 = "pare/" . $hete['p_dni'] . "/foto1.jpg";
                                    $foto2 = "pare/" . $hete['p_dni'] . "/foto2.jpg";

                                    if (file_exists($foto1)) {
                                        $foto1_id = 'foto1_';
                                        print('<div class="ui-block-a" style="text-align: center;">' . "<img src='" . $foto1 . "' width='60px' height='60px'/>" . '</div>');
                                    } else {
                                        print('<div class="ui-block-a" style="text-align: center;">-</div>');
                                    } ?>

                                    <?php print('<div class="ui-block-b" style="text-align: center;"><span><em>Nombres:<br>' . $hete['p_ape'] . $hete['p_nom'] . '</em></span></div>'); ?>
                                    <?php print('<div class="ui-block-c" style="text-align: center;"><span><em>Color pelo' . (!empty($p_cab) ? $p_cab : ": -") . '</em></span></div>'); ?>
                                    <?php print('<div class="ui-block-d" style="text-align: center;"><span><em>Color ojos' . (!empty($p_ojo) ? $p_ojo : ": -") . '</em></span></div>'); ?>
                                    <?php print('<div class="ui-block-e" style="text-align: center;"><span><em>Talla: ' . (!empty($p_tal) ? $p_tal : "-") . '</em></span></div>'); ?>
                                    <?php print('<div class="ui-block-f" style="text-align: center;"><span><em>Peso: ' . (!empty($p_pes) ? $p_pes : "-") . '</em></span></div>'); ?>
                                </div>
                            </h3>
                            <?php
                            if (file_exists($foto1)) {
                                $foto1_id = 'foto1_';
                                echo "<a href='#" . $foto1_id . "' data-rel='popup' data-position-to='window' style='float:left'><img src='" . $foto1 . "' width='60px' height='60px' /></a>";
                                echo '<div data-role="popup" id="' . $foto1_id . '" data-overlay-theme="a" style="max-width:1000px;"><img src="' . $foto1 . '"/></div>';
                            }
                            if (file_exists($foto2)) {
                                $foto2_id = 'foto2_';
                                echo "<a href='#" . $foto2_id . "' data-rel='popup' data-position-to='window' style='float:left'><img src='" . $foto2 . "' width='60px' height='60px' /></a>";
                                echo ' <div data-role="popup" id="' . $foto2_id . '" data-overlay-theme="a" style="max-width:1000px;"><img src="' . $foto2 . '"/></div>';
                            }

                            echo $p_ins . $p_icq . "<br>";
                            $Esp = $db->prepare("SELECT * from lab_andro_esp where p_dni = ? order by fec desc;");
                            $Esp->execute([$hete['p_dni']]);

                            if ($Esp->rowCount() > 0) { ?>
                                Espermatogramas:
                                <table data-role="table" id="movie-table" data-mode="reflow" class="ui-responsive ra_andrologia">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Volumen</th>
                                            <th>PH</th>
                                            <th>Espermas por ml</th>
                                            <th>Total móviles (P + NP)</th>
                                            <th>Normales</th>
                                        </tr>
                                    </thead>

                                    <?php
                                    while ($esp = $Esp->fetch(PDO::FETCH_ASSOC)) { ?>
                                        <tbody>
                                            <tr style="font-size:small">
                                                <?php print('<td>' . date("d-m-Y", strtotime($esp['fec'])) . '</td>') ?>
                                                <td><?php if ($esp['emb'] > 0) { echo $esp['macro_volumen'] . 'ml'; } ?></td>
                                                <td><?php if ($esp['emb'] > 0) { echo $esp['macro_ph']; } ?></td>
                                                <td><?php if ($esp['emb'] > 0) { echo $esp['concen_exml']; } ?></td>
                                                <td><?php if ($esp['emb'] > 0) { echo $esp['movi_mprogresivo'] + $esp['movi_mnoprogresivo']; } ?></td>
                                                <td><?php if ($esp['emb'] > 0) { if(isset($esp['movi_normal']))echo $esp['movi_normal']; } ?></td>
                                            </tr>
                                        </tbody>
                                    <?php } ?>
                                </table>

                                <?php
                                $select_het_id = "";
                                print('<div class="enlinea">
                                    <input type="checkbox" name="sel_het_' . $select_het_id . '" title="' . $hete['p_dni'] . '" class="sel_het" id="sel_het_' . $select_het_id . '" data-mini="true"><label for="sel_het_' . $select_het_id . '">Seleccionar este Donante</label>
                                </div>'); ?>
                            <?php } else {
                                echo 'No tiene Espermatograma';
                            }

                            $stmt = $db->prepare("SELECT tip FROM lab_tanque_res WHERE sta=?");
                            $stmt->execute([$hete['p_dni']]);

                            if ($stmt->rowCount() > 0) {
                                $c_bio = 0;
                                $c_cri = 0;

                                while ($con = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    if ($con['tip'] == 1) { $c_bio++; }
                                    if ($con['tip'] == 2) { $c_cri++; }
                                }

                                echo "<p>Congelados Biopsia: " . $c_bio . "<br>Congelados Criopreservación: " . $c_cri . "</p>";
                            } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>