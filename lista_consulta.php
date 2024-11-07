<!DOCTYPE HTML>
<html>
<head>
<?php
   include 'seguridad_login.php'
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="_themes/tema_inmater.min.css"/>
    <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css"/>
    <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css"/>
    <link rel="stylesheet" href="css/lista_consulta.css"/>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
    <script src="js/lista_consulta.js?v=1"></script>
</head>
<body>
    <?php
    $rUser = $db->prepare("SELECT role FROM usuario WHERE userx=?");
    $rUser->execute(array($login));
    $user = $rUser->fetch(PDO::FETCH_ASSOC);

    if (isset($_POST['btn_consulta']) and $_POST['btn_consulta'] == "AGENDAR CONSULTA" and isset($_POST['dni']) and isset($_POST['fec']) and isset($_POST['fec_h'])) {
        $rPaci = $db->prepare("SELECT med, medios_comunicacion_id, idsedes FROM hc_paciente WHERE dni=?");
        $rPaci->execute(array($_POST['dni']));
        $paci = $rPaci->fetch(PDO::FETCH_ASSOC);

        // si el medico NO esta en la lista de medicos, entonces lo agrega
        if (strpos($paci['med'], $_POST['med_agenda']) == false) {
            $stmt = $db->prepare("UPDATE hc_paciente SET med=?, iduserupdate=?,updatex=? WHERE dni=?");
            $hora_actual = date("Y-m-d H:i:s");
            $stmt->execute(array($_POST['med_agenda'] . ',' . $paci['med'],$login, $hora_actual, $_POST['dni']));
            $log_Paciente = $db->prepare(
                "INSERT INTO appinmater_log.hc_paciente (
                            dni, pass, sta, med, tip, nom, ape, fnac, tcel,
                            tcas, tofi, mai, dir, nac, depa, prov, dist, prof,
                            san, don, raz, talla, peso, rem, nota, fec, idsedes,
                            idusercreate, createdate, 
                            action
                    )
                SELECT 
                    dni, pass, sta, med, tip, nom, ape, fnac, tcel, 
                    tcas, tofi, mai, dir, nac, depa, prov, dist, prof,
                    san, don, raz, talla, peso, rem, nota, fec, idsedes,
                    iduserupdate,updatex, 'U'
                FROM appinmater_modulo.hc_paciente
                WHERE dni=?");
            $log_Paciente->execute(array($_POST['dni']));
        }

        $stmt = $db->prepare(
            "INSERT INTO hc_gineco
            (dni, fec, med, fec_h, fec_m, mot, cupon, tipoconsulta_ginecologia_id, man_motivoconsulta_id, idusercreate,programaid,sedeid) VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->execute([$_POST['dni'], $_POST['fec'], $_POST['med_agenda'], $_POST['fec_h'], $_POST['fec_m'], $_POST['mot'], (isset($_POST['cupon']) ? $_POST['cupon'] : 11), $_POST['tipoconsulta_id'], $_POST['man_motivoconsulta_id'], $login, $programa_id['medios_comunicacion_id'], $programa_id['idsedes']]);
        $idp = $db->lastInsertId();
        $log_Gineco = $db->prepare(
            "INSERT INTO appinmater_log.hc_gineco (
                gineco_id, estadoconsulta_ginecologia_id, tipoconsulta_ginecologia_id,
                man_motivoconsulta_id, voucher_id, interconsulta_id, dni, fec, med, fec_h,
                fec_m, fecha_confirmacion, fecha_voucher, mot, dig, medi, aux, efec, cic,
                vag, vul, cer, cer1, mam, mam1, t_vag, eco, e_sol, i_med, i_fec, i_obs, 
                in_t, in_f1, in_h1, in_m1, in_f2, in_h2, in_m2, idturno_inter, in_c,
                cupon, repro, legal, cancela, cancela_motivo,
                isuser_log, date_log,
                asesor_medico_id, 
                action
                )
            SELECT
                id, estadoconsulta_ginecologia_id, tipoconsulta_ginecologia_id,
                man_motivoconsulta_id, voucher_id, interconsulta_id, dni, fec, med, fec_h, 
                fec_m, fecha_confirmacion, fecha_voucher, mot, dig, medi, aux, efec, cic, 
                vag, vul, cer, cer1, mam, mam1, t_vag, eco, e_sol, i_med, i_fec, i_obs, 
                in_t, in_f1, in_h1, in_m1, in_f2, in_h2, in_m2, idturno_inter, in_c, 
                cupon, repro, legal, cancela, cancela_motivo,
                idusercreate, createdate,
                asesor_medico_id,
                'I'
            FROM appinmater_modulo.hc_gineco
            WHERE id =?");
        
        $log_Gineco->execute(array($idp));

        // registro de atencion unica
        $data = [
            'tipo' => 'agregar_atencion',
            'area_id' => 1,
            'atencion_id' => $idp,
            'medico_id' => $_POST['med_agenda'],
            'paciente_id' => $_POST['dni'],
            'detalle' => $_POST['mot'],
        ];

        include ($_SERVER["DOCUMENT_ROOT"] . "/_operaciones/cli_atencion_unica.php");

        mkdir('paci/' . $_POST['dni'] . '/' . $idp, 0755);
        print("<div id='alerta'> Datos guardados en el historial de consultas! </div>");
    } ?>
    
    <div data-role="page" class="ui-responsive-panel" id="lista">
        <div data-role="panel" id="indice_paci">
            <img src="_images/logo.jpg"/>
            <ul data-role="listview" data-inset="true" data-theme="a">
                <li data-icon="user"><a href="perfil.php?path=lista_consulta" rel="external">Perfil</a></li>
                <li data-icon="plus"><a href="n_paci.php?path=lista_consulta" rel="external">Nuevo Paciente</a></li>
                <li data-icon="info"><a href="ayuda.php?path=lista_consulta" rel="external">Ayuda</a></li>
            </ul>
        </div>

        <div data-role="header" data-position="fixed">
            <a href="#indice_paci" data-icon="bars" id="b_indice" class="ui-icon-alt" data-theme="a">MENU</a>
            <div data-role="controlgroup" data-type="horizontal" class="ui-mini ui-btn-left">
                <?php
                if ($user['role'] == '15') {
                    print('<a href="lista.php" class="ui-btn ui-mini ui-btn-inline" rel="external">Volver</a>');
                } ?>
            </div>
            <h1>Consultas Médicas</h1>
            <a href="salir.php" class="ui-btn-right ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-right ui-icon-power" rel="external">Salir</a>
        </div>

        <div class="ui-content" data-ajax="false" role="main">
            <form action="" method="post" data-ajax="false" name="form1" id="form1">
                <?php
                $rPaci = $db->prepare("SELECT dni,ape,nom,med FROM hc_paciente limit 2 offset 0");
                $rPaci->execute();

                $rMed = $db->prepare("SELECT codigo, nombre FROM man_medico WHERE estado_consulta = 1 order by nombre;");
                $rMed->execute(); ?>
                <input type="hidden" name="dni" id="dni">

                <div class="ui-grid-a ui-responsive carga_paci" style="text-align: center;" id="contenido_consulta">
                    <div class="ui-block-a" style="width:30%;">
                        <div class="ui-body ui-body-d">
                            <ul data-role="listview" data-theme="c" data-inset="true" data-filter="true" data-filter-reveal="true" data-filter-placeholder="Buscar por nombre de paciente o pareja, documento de paciente o pareja." data-mini="true" class="fil_paci">
                                <?php
                                while ($paci = $rPaci->fetch(PDO::FETCH_ASSOC)) { ?>
                                    <li>
                                        <a href="#" class="paci_insert" dni="<?php echo $paci['dni']; ?>" med="<?php echo $paci['med']; ?>"><?php echo '<small>' . $paci['ape'] . ' ' . $paci['nom'] . '</small>'; ?></a>
                                        <span class="ui-li-count"><?php echo $paci['dni']; ?></span>
                                    </li>
                                <?php } ?>
                            </ul>
                            <small>Desea registrar un nuevo paciente?, <a href="n_paci.php?path=lista_consulta" rel="external">click aquí</a></small>
                            <legend><small>Tipo de consulta:</small></legend>
                            <fieldset data-role="controlgroup" data-type="horizontal" id="tipoconsulta_id">
                                <input type="radio" name="tipoconsulta_id" id="tipoconsulta_presencial" data-mini="true" value="1" required>
                                <label for="tipoconsulta_presencial">Presencial</label>
                                <input type="radio" name="tipoconsulta_id" id="tipoconsulta_virtual" data-mini="true" value="2">
                                <label for="tipoconsulta_virtual">Virtual</label>
                            </fieldset>
                            <legend><small>Médico y sede:</small></legend>
                            <div data-role="controlgroup" data-type="horizontal" data-mini="true">
                                <select name="cupon" id="cupon" required>
                                    <option value="0" selected>Seleccione Sede</option>
                                    <?php
                                    $stmt = $db->prepare("SELECT codigo_facturacion codigo, nombre from sedes where estado_consulta = 1 order by nombre");
                                    $stmt->execute();

                                    while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        print('<option value="' . $data['codigo'] . '"> ' . ucwords(mb_strtolower($data['nombre'])) . '</option>');
                                    } ?>
                                </select>
                                <select name="med_agenda" id="med_agenda" data-mini="true" required>
                                    <option value="" selected>Seleccione Médico</option>
                                    <?php
                                    while ($med = $rMed->fetch(PDO::FETCH_ASSOC)) { ?>
                                        <option value="<?php echo $med['codigo']; ?>"><?php echo ucwords(mb_strtolower($med['nombre'])); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <legend><small>Fecha y hora de la consulta:</small></legend>
                            <div data-role="controlgroup" data-type="horizontal" data-mini="true">
                                <input name="fec" type="date" id="fec" value="<?php echo date("Y-m-d"); ?>" data-wrapper-class="controlgroup-textinput ui-btn">
                                <select name="fec_h" id="fec_h" required>
                                    <option value="">Hra</option>
                                    <option value="07">07 hrs</option>
                                    <option value="08">08 hrs</option>
                                    <option value="09">09 hrs</option>
                                    <option value="10">10 hrs</option>
                                    <option value="11">11 hrs</option>
                                    <option value="12">12 hrs</option>
                                    <option value="13">13 hrs</option>
                                    <option value="14">14 hrs</option>
                                    <option value="15">15 hrs</option>
                                    <option value="16">16 hrs</option>
                                    <option value="17">17 hrs</option>
                                    <option value="18">18 hrs</option>
                                    <option value="19">19 hrs</option>
                                    <option value="20">20 hrs</option>
                                </select>
                                <select name="fec_m" id="fec_m" required>
                                    <option value="">Min</option>
                                    <option value="00">00 min</option>
                                    <option value="15">15 min</option>
                                    <option value="30">30 min</option>
                                    <option value="45">45 min</option>
                                </select>
                            </div>
                            <legend><small>Motivo de la consulta:</small></legend>
                            <select name="man_motivoconsulta_id" id="man_motivoconsulta_id" data-mini="true" required>
                                <option value="" selected>Seleccione el motivo de consulta</option>
                                <?php
                                $stmt = $db->prepare("SELECT id, nombre from man_gine_motivoconsulta where estado = 1 order by nombre;");
                                $stmt->execute();

                                while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
                                    <option value="<?php echo $data['id']; ?>"><?php echo ucwords(mb_strtolower($data['nombre'])); ?></option>
                                <?php } ?>
                            </select>
                            <textarea name="mot" id="mot" data-mini="true"></textarea>
                            <input type="submit" value="AGENDAR CONSULTA" name="btn_consulta" data-icon="check" data-mini="true" data-theme="b" data-inline="true"/>
                        </div>
                    </div>
                    <div class="ui-block-b" style="width:70%;">
                        <div class="ui-body ui-body-d td_agenda">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>