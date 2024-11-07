<?php
    ini_set("display_errors","1");
    error_reporting(E_ALL);
    $titulo_programacion = "";
    $primera_programacion=$segunda_programacion="";
    $primera_programacion_readonly = $segunda_programacion_readonly = $primera_programacion_hidden = "";
    $primera_programacion_disabled = $segunda_programacion_disabled = "";
    $segunda_programacion_hidden = "text";
    $consulta_turno = "";

    if (is_null($repro['des_dia'])) {
        $titulo_programacion = "Fecha de Inyección"; // aspiracion
        $primera_programacion = "Fecha de Aspiración";
        $segunda_programacion="Fecha de Inyección (36 horas antes)";
        $segunda_programacion_readonly="readonly";
        $segunda_programacion_disabled="disabled";
        $segunda_programacion_hidden="hidden";
        $consulta_turno = "and aspiracion = true";
    } else {
        $primera_programacion = "Fecha de Descongelación";
        if ($repro['des_dia'] === 0) {
            $titulo_programacion = "Fecha de Inseminación"; // descongelacion de ovulos donados o propios
            $segunda_programacion="Fecha de Inseminación";
            $primera_programacion_readonly="readonly";
            $primera_programacion_disabled="disabled";
        } else {
            $titulo_programacion = "Fecha de Transferencia"; // embrioadopcion o ted
            $segunda_programacion="Fecha de Transferencia";
            $primera_programacion_readonly="readonly";
            $primera_programacion_disabled="disabled";
            $consulta_turno = "and transferencia = true";
        }
    }

    // TODO verificar horario de todo el dia
    $eventodia_disabled="";
    if ($repro['des_dia'] === 0) {
        $eventodia_disabled="disabled";
    }
?>
<div data-role="collapsible" id="aspira_block">
    <?php print('<h3>'.$titulo_programacion.'</h3>'); ?>
    <div class="enlinea">
        <?php if( true ): ?>
            <?php $requisitos = [] ?>
            <?php if( $legal_vencida ) $requisitos[] = 'legal' ?> 
            <?php if( $analisis_vencido ) $requisitos[] = 'análisis clínicos' ?> 
            <?php if( $tiene_pareja && $andrologia_vencido ) $requisitos[] = 'andrología' ?> 
            <?php if( $riesgo_vencido ) $requisitos[] = 'riesgo quirúrgico' ?> 
            <?php if( $receptora && $psicologico_vencido ) $requisitos[] = 'exámen psicológico' ?> 
        <?php endif ?>
        <?php print('
        <table>
            <tbody>
                <tr>
                    <td>Tiempo estimado de uso sala (min)</td>
                    <td>'); ?>
                        <select name="idturno" id="idturno" data-mini="true" <?php print($eventodia_disabled); ?>>
                            <option value="">Seleccione Turno</option>
                            <?php
                                $idturno = intval($repro["idturno"]) ?? 0;
                                $consulta = $db->prepare("SELECT codigo, nombre from man_turno_reproduccion where estado = 1 $consulta_turno or id = $idturno order by nombre asc");
                                $consulta->execute();
                                while ($data = $consulta->fetch(PDO::FETCH_ASSOC)) { ?>
                                <option value="<?php echo $data['codigo']; ?>"
                                    <?php
                                    if ($data['codigo'] == $repro["idturno"]) {echo 'selected';} ?>>
                                    <?php print(mb_strtolower($data['nombre'])); ?>
                                </option>
                            <?php } ?>
                        </select>
                    <?php
                    print('</td>
                </tr>
                <tr>
                    <td>'.$primera_programacion.'</td>
                    <td>'); ?>
                        <div data-role="controlgroup" data-type="horizontal" data-mini="true" class="peke">
                            <input name="f_asp" type="hidden" id="f_asp" value="<?php echo $repro['f_asp']; ?>"/>
                            <input name="f_ini" type="hidden" id="f_ini" value="<?php echo @$repro['f_ini']; ?>"/>
                            <input name="f_fin" type="hidden" id="f_fin" value="<?php echo @$repro['f_fin']; ?>"/>
                            <input type="date" name="f_asp1" id="f_asp1" value="<?php echo substr($repro['f_asp'], 0, 10); ?>" data-wrapper-class="controlgroup-textinput ui-btn" class="inyeccion1" data-mini="true">
                            <select name="h_asp1" id="h_asp1" class="inyeccion1" data-mini="true" <?php print($eventodia_disabled); ?>>
                                <option value="">Hora Inicio</option>
                                <?php
                                $aspira_permisos = 'and aspiracion = 1';
                                $transf_permisos = 'and transferencia = 1';
                                if ($login == 'jose.goncalves') {
                                    $aspira_permisos = 'and id <= 55 and id >= 30';
                                    //$aspira_permisos = 'and transferencia = 1 or aspiracion = 1 or ginecologia = 1';
                                    $transf_permisos = 'and id <= 55 and id >= 30';
                                    //$transf_permisos = 'and transferencia = 1 or aspiracion = 1 or ginecologia = 1';
                                    }
                                if (is_null($repro['des_dia']))
                                {
                                    // aspiracion
                                    $consulta = $db->prepare("SELECT nombre from man_hora where estado = 1 $aspira_permisos order by codigo asc");
                                    $consulta->execute();
                                    while ($data = $consulta->fetch(PDO::FETCH_ASSOC)) { ?>
                                        <option value="<?php echo $data['nombre']; ?>"
                                            <?php
                                            if ($data['nombre'] == substr($repro['f_asp'], 11, 5)) {print('selected');} ?>>
                                            <?php print(mb_strtolower($data['nombre'])); ?>
                                        </option>
                                    <?php }
                                } else {
                                    // descongelacion
                                    $consulta = $db->prepare("SELECT nombre from man_hora where estado = 1 $transf_permisos order by codigo asc");
                                    $consulta->execute();
                                    while ($data = $consulta->fetch(PDO::FETCH_ASSOC)) { ?>
                                        <option value="<?php echo $data['nombre']; ?>"
                                            <?php
                                            if ($data['nombre'] == $repro["h_iny"]) {echo 'selected';} ?>>
                                            <?php print(mb_strtolower($data['nombre'])); ?>
                                        </option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
        <?php print('</td>
                </tr>
                <tr>
                    <td>'.$segunda_programacion.'</td>
                    <td>'); ?>
                    <div data-role="controlgroup" data-type="horizontal" data-mini="true" class="peke">
                        <input type="date" name="f_iny" id="f_iny" value="<?php echo $repro['f_iny']; ?>" data-wrapper-class="controlgroup-textinput ui-btn" class="inyeccion" data-mini="true">
                        <select name="h_iny" id="h_iny" class="inyeccion" data-mini="true" <?php print($eventodia_disabled); ?>>
                            <option value="">Hora Inicio</option>
                            <?php
                            if (!is_null($repro['des_dia']))
                            {
                                // descongelacion
                                $consulta = $db->prepare("SELECT nombre from man_hora where estado = 1 $transf_permisos order by codigo asc");
                                $consulta->execute();
                                while ($data = $consulta->fetch(PDO::FETCH_ASSOC)) { ?>
                                    <option value="<?php print($data['nombre']); ?>"
                                        <?php
                                        print($segunda_programacion_readonly);
                                        if ($data['nombre'] == $repro["h_iny"]) {print('selected');} ?>>
                                        <?php print(mb_strtolower($data['nombre'])); ?>
                                    </option>
                                <?php }
                            } else {
                                // inyeccion
                                $asp_inyec_permisos = 'and aspiracion_inyeccion = 1';
                                if ($login == 'jose.goncalves') {
                                    $asp_inyec_permisos = 'and id <= 7 or id >= 78';
                                    }
                                $consulta = $db->prepare("SELECT nombre from man_hora where estado = 1 $asp_inyec_permisos order by codigo asc");
                                $consulta->execute();
                                while ($data = $consulta->fetch(PDO::FETCH_ASSOC)) { ?>
                                    <option value="<?php echo $data['nombre']; ?>"
                                        <?php
                                        if ($data['nombre'] == $repro["h_iny"]) {echo 'selected';} ?> readonly>
                                        <?php print(mb_strtolower($data['nombre'])); ?>
                                    </option>
                                <?php }
                            } ?>
                        </select>
                    </div>
        <?php
            print('</td>
                </tr>');

            if (!is_null($repro['des_dia']) && $repro['des_dia'] != 0) {
                print("<tr>
                <td>Anestesia</td>
                <td>
                    <select name=\"anestesia\" id=\"anestesia\" data-mini=\"true\">
                        <option value=\"\">Seleccione</option>
                        <option value=\"1\" ".($repro['anestesia'] == "1" ? "selected": "").">Procedimiento sin sedación</option>
                        <option value=\"2\" ".($repro['anestesia'] == "2" ? "selected": "").">Procedimiento bajo sedación</option>
                    </select>
                </td></tr>");
            } else {
                print('<input type="hidden" name="anestesia" id="anestesia" value="0">');
            }

            print('</tbody>
        </table>'); ?>
        <font color="#E34446">(Revisar disponibilidad en Sala) <b>Solo puede agendar para mañana hasta las 3pm de hoy</b></font>
    </div>
    <iframe src="agenda.php?med=" width="100%" height="800" seamless></iframe>
</div>