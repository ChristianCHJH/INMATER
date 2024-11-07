<!DOCTYPE HTML>
<html>
    <head>
    <?php
   include 'seguridad_login.php';
   include 'permisos_validacion.php'
    ?>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="_images/favicon.png" type="image/x-icon">
        <link rel="stylesheet" href="_themes/tema_inmater.min.css"/>
        <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css"/>
        <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css"/>
        <script src="js/jquery-1.11.1.min.js"></script>
        <script src="js/jquery.mobile-1.4.5.min.js"></script>
        <style>
            .color2 {
                color: #72a2aa;
            }

            .scroll_h {
                overflow-x: scroll;
                overflow-y: hidden;
                white-space: nowrap;
            }

            #alerta {
                background-color: #FF9;
                margin: 0 auto;
                text-align: center;
                padding: 4px;
            }

            .peke .ui-input-text {
                width: 140px !important;

            }

            .peke2 .ui-input-text {
                width: 30px !important;
            }

            .peke2 span {
                float: left;
            }

            .ui-slider-track {
                margin-left: 15px;
            }

            .enlinea div {
                display: inline-block;
                vertical-align: top;
            }
        </style>
    </head>
    <body>
        <?php  migrarCicloEstimulacionDetalle() ?>
        <script>
            $(document).ready(function () {
                // p_don(combo donacion en fresco: solo para donantes), p_od(combo OD fresco), p_des(combo descongelacion)
                $("#p_iiu").change(function () {
                    if ($(this).prop('checked')) {
                        $('#p_fiv,#p_icsi,#p_cri,#p_don,#p_od,#p_des').val("");
                        $('#p_fiv,#p_icsi,#p_cri,#p_don').checkboxradio("disable");
                        $('#p_od,#p_des').selectmenu("disable");
                    } else {
                        $('#p_fiv,#p_icsi,#p_cri,#p_don').checkboxradio("enable");
                        $('#p_od,#p_des').selectmenu("enable");
                    }
                });

                $("#tipo_procedimiento").change(function () {
                    switch ($(this).val()) {
                        case "fiv":
                            $('#t_mue option[value="3"]').attr('disabled', true);
                            $('#t_mue').val("").selectmenu("refresh");
                            break;

                        case "icsi":
                            $('#t_mue option[value="3"]').attr('disabled', true);
                            $('#t_mue').val("").selectmenu("refresh");
                            break;

                        case "iiu":
                            $('#p_fiv, #p_icsi, #p_cri, #p_don, #p_od, #p_des').val("");
                            $('#p_fiv, #p_icsi, #p_cri, #p_don').checkboxradio("disable");
                            $("#p_od, #p_des").val("");
                            $('#p_od, #p_des').selectmenu("disable");
                            $('#p_od, #p_des').selectmenu("refresh", true);
                            break;
                    
                        default:
                            $('#p_fiv, #p_icsi, #p_cri, #p_don').checkboxradio("enable");
                            $('#p_od, #p_des').selectmenu("enable");
                            $('#t_mue option[value="3"]').attr('disabled', false);
                            $('#t_mue').val("").selectmenu("refresh");
                            break;
                    }
                });

                $('#donante1-button').hide();
                $('#donante2-button').hide();

                $("#p_des").change(function () {
                    $('#donante1-button').hide();
                    $('#donante2-button').hide();
                    $('#p_fiv, #p_icsi, #p_cri, #p_iiu, #p_don').checkboxradio("enable");
                    $('#tipo_procedimiento option[value="fiv"]').attr('disabled', false);
                    $('#tipo_procedimiento option[value="icsi"]').attr('disabled', false);
                    $('#tipo_procedimiento option[value="crio_ovos"]').attr('disabled', false);
                    $('#tipo_procedimiento option[value="iiu"]').attr('disabled', false);
                    $("#tipo_procedimiento").val("").selectmenu("enable").selectmenu("refresh");
                    $('#p_od').selectmenu("enable");
                    $('#p_od').val("").selectmenu("refresh");
                    $('#t_mue option[value="3"]').attr('disabled', false);
                    $('#t_mue').val("").selectmenu("refresh");
                    $(".lista_des2").remove();

                    if ($(this).val() == 1 || $(this).val() == 3) { // embriones
                        $('#p_fiv, #p_icsi, #p_cri, #p_iiu, #p_don, #p_od').val("");
                        $('#p_fiv, #p_icsi, #p_cri, #p_iiu, #p_don').checkboxradio("disable");
                        $("#tipo_procedimiento").val("").selectmenu("disable").selectmenu("refresh");
                        $('#p_od').selectmenu("disable").selectmenu("refresh");
                    }

                    if ($(this).val() == 2 || $(this).val() == 4) { // descongelacion ovulos
                        $('#p_cri, #p_iiu, #p_don, #p_od').val("");
                        $('#p_cri, #p_iiu, #p_don').checkboxradio("disable");
                        $('#tipo_procedimiento option[value="fiv"]').attr('disabled', true);
                        $('#tipo_procedimiento option[value="crio_ovos"]').attr('disabled', true);
                        $('#tipo_procedimiento option[value="iiu"]').attr('disabled', true);
                        $("#tipo_procedimiento").val("icsi").selectmenu("refresh");
                        $('#t_mue option[value="3"]').attr('disabled', true);
                        $('#p_od').selectmenu("disable");
                    }

										if ($(this).val() == 1) {
											$('#donante1-button').show();
											$('#donante1').val('');
											$('#donante1').prop('selectedIndex', 0);
											$('#donante1').selectmenu("refresh");
										}
										if ($(this).val() == 2) {
											$('#donante2-button').show();
											$('#donante2').val('');
											$('#donante2').prop('selectedIndex', 0);
											$('#donante2').selectmenu("refresh");
										}

                    if ($(this).val() == 3 || $(this).val() == 4) { // ovulos/ embriones propios
                        var h = $(this).val();
                        var dni = $("#dni").val();
                        $('.lista_des').html('<h3>CARGANDO DATOS...</h3>');

                        $.post("le_tanque.php", {h: h, dni: dni, paci: dni, btn_guarda: 1}, function (data) {
                            $('.lista_des').html('');
                            $(".lista_des").append('<div class="lista_des2">' + data + '</div>');
                            $('.ui-page').trigger('create');
                        });
                    }
                });

                $("#donante1, #donante2").change(function () {
                    if ($(this).val() != '') {
                        $(".lista_des2").remove();
                        var h = $("#p_des").val();
                        var dni = $(this).val();
                        var paci = $("#dni").val();
                        $('.lista_des').html('<h3>CARGANDO DATOS...</h3>');

                        $.post("le_tanque.php", {h: h, dni: dni, paci: paci, btn_guarda: 1}, function (data) {
                            $('.lista_des').html('');
                            $(".lista_des").append('<div class="lista_des2">' + data + '</div>');
                            $('.ui-page').trigger('create');
                        });
                    }
                });
            });

            var descon = 0;
            var conta = 0;

            $(document).on('change', '.deschk', function (ev) {
                $("#des_dia").val($(this).attr("id")); // Esto define el dia de descongelacion segun el ultimo check q se presiono
            });

            function anular(id) {
                if (confirm("Esta apunto de eliminar esta Reproducción asistida, esta seguro?")) {
                    document.form2.borrar.value = id;
                    document.form2.submit();
                } else { return false };
            }

            function Beta(beta, pro) {
                localStorage.setItem('back_url', window.location.href);
                document.form2.val_beta.value = beta.value;
                document.form2.pro_beta.value = pro;
                document.form2.submit();
            }
        </script>
        <?php
        if (isset($_POST['borrar']) && !empty($_POST['borrar'])) {

            $log_Reprod = $db->prepare(
                "INSERT INTO appinmater_log.hc_reprod (
                            reprod_id, dni, p_dni, t_mue, p_dni_het, fec, med, eda, poseidon,
                            p_dtri, p_cic, p_fiv, p_icsi, des_dia, des_don, p_od, p_don, don_todo, p_cri, 
                            p_iiu, p_extras, p_notas, n_fol, fur, f_aco, fsh, lh, est, prol, ins, t3, t4,
                            tsh, amh, inh, m_agh, m_vdrl, m_clam, m_his, m_hsg, f_fem, f_mas, con_fec, con_od,
                            con_oi, con_end,
                            con1_med, 
                            con2_med, 
                            con3_med, 
                            con4_med, 
                            con5_med, 
                            con_iny, con_obs, obs, f_iny, h_iny, f_asp, idturno, f_tra, h_tra,
                            complicacionesparto_id, complicacionesparto_motivo, idturno_tra, cancela,
                            pago_extras, pago_notas, pago_obs, repro, 
                            idusercreate, createdate, action
                    )
                SELECT 
                    id, dni, p_dni, t_mue, p_dni_het, fec, med, eda, poseidon,
                    p_dtri, p_cic, p_fiv, p_icsi, des_dia, des_don, p_od, p_don, don_todo, p_cri, 
                    p_iiu, p_extras, p_notas, n_fol, fur, f_aco, fsh, lh, est, prol, ins, t3, t4,
                    tsh, amh, inh, m_agh, m_vdrl, m_clam, m_his, m_hsg, f_fem, f_mas, con_fec, con_od, 
                    con_oi, con_end,
                    con1_med, 
                    con2_med, 
                    con3_med, 
                    con4_med, 
                    con5_med,
                    con_iny, con_obs, obs, f_iny, h_iny, f_asp, idturno, f_tra, h_tra,
                    complicacionesparto_id, complicacionesparto_motivo, idturno_tra, cancela,
                    pago_extras, pago_notas, pago_obs, repro, 
                    ?, ?, 'D'
                FROM appinmater_modulo.hc_reprod
                WHERE id=?");
            $hora_actual = date("Y-m-d H:i:s");
            $log_Reprod->execute(array($login, $hora_actual, $_POST['borrar']));

            $stmt = $db->prepare("UPDATE appinmater_modulo.lab_aspira_dias set adju = '', rep_c = null WHERE rep_c = ?;");
            $stmt->execute(array($_POST['borrar']));
            
            $stmt = $db->prepare("update hc_reprod SET estado = false where id=?");
            $stmt->execute(array($_POST['borrar']));

            // buscar el googlecalendar_codigo
            $stmt = $db->prepare("SELECT codigo from googlecalendar where tipoprocedimiento_id = 1 and estado = 1 and procedimiento_id = ?");
            $stmt->execute([$_POST['borrar']]);

            if ($stmt->rowCount() == 1) {
              require($_SERVER["DOCUMENT_ROOT"]."/config/environment.php");

              $data_calendar = $stmt->fetch(PDO::FETCH_ASSOC);

              $stmt = $db->prepare("UPDATE googlecalendar SET estado = 0, iduserupdate = ? WHERE estado = 1 AND tipoprocedimiento_id = ? AND procedimiento_id = ?;");
              $stmt->execute([$login, 1, $_POST['borrar']]);

              googlecalendar_eliminar(array(
                'id' => $_ENV["googlecalendar_id"],
                'accountname' => $_ENV["googlecalendar_accountname"],
                'keyfilelocation' => $_ENV["googlecalendar_keyfilelocation"],
                'applicationname' => $_ENV["googlecalendar_applicationname"],
                'googlecalendar_codigo' => $data_calendar["codigo"],
              ));
            }
        }

        if (isset($_POST['val_beta']) && isset($_POST['pro_beta']) && $_POST['val_beta'] != "" && $_POST['pro_beta'] != "") {            
            if ($_POST['val_beta'] == "1") {
                header("Location: med-betas-item.php?&pro=" . $_POST['pro_beta']);
            } else {
                $stmt = $db->prepare("UPDATE lab_aspira_t SET beta = ?, fecha_ultima_regla = NULL, iduserupdate = ? where pro = ? and estado is true;");
                $stmt->execute([$_POST['val_beta'], $login, $_POST['pro_beta']]);
                echo "<div id='alerta'> BETA Guardado! </div>";
            }
           
        }

        $fec = date("Y-m-d");
        if (isset($_POST['dni']) && isset($_POST['boton_datos']) && $_POST['boton_datos'] == "GUARDAR DATOS") {
					if ($_POST['p_dni'] == "1") { $_POST['p_dni'] = ""; } // soltera con p_dni vacio
					$atencion_id=0;
					$id_hc_reprod = insertRepro([
						"dni" => $_POST['dni'],
						"p_dni" => $_POST['p_dni'],
						"fec" => $fec,
						"med" => $_POST['m_tratante'],
						"eda" => $_POST['fnac'],
						"poseidon" => $_POST['poseidon'],
						"p_dtri" => $_POST['p_dtri'],
						"p_cic" => $_POST['p_cic'],
						"p_fiv" => isset($_POST['tipo_procedimiento']) && $_POST['tipo_procedimiento'] == "fiv" ? "1" : "0",
						"p_icsi" => isset($_POST['tipo_procedimiento']) && $_POST['tipo_procedimiento'] == "icsi" ? "1" : "0",
						"des_dia" => $_POST['des_dia'],
						"des_don" => $_POST['des_don'],
						"p_od" => $_POST['p_od'],
						"p_don" => $_POST['p_don'],
						"p_cri" => isset($_POST['tipo_procedimiento']) && $_POST['tipo_procedimiento'] == "crio_ovos" ? "1" : "0",
						"p_iiu" => isset($_POST['tipo_procedimiento']) && $_POST['tipo_procedimiento'] == "iiu" ? "1" : "0",
						"t_mue" => $_POST['t_mue'],
						"obs" => $_POST['obs'],
                        "idusercreate" => $login,
					], $atencion_id);

                    if(isset($_POST['cont']))if ($_POST['cont'] >= 1) {
						for ($p = 1; $p <= $_POST['cont']; $p++) {
                            $valor = $id_hc_reprod;
                            $id_estado = ', id_estado = 2 ';
                            if (!isset($_POST['adju'.$p])) {
                                $valor = null;
                                $id_estado = '';
                            }
							$tan = explode("|", $_POST['c'.$p]);
							$stmt2 = $db->prepare("UPDATE lab_aspira_dias SET adju=?, rep_c=? $id_estado WHERE pro=? AND ovo=? and estado is true");
							$stmt2->execute(array($_POST['adju'.$p]??'', $valor, $tan[0], $tan[1])); // Adjudica el dni de la paciente al ovo/embrion
						}
					}
					// registro de atencion unica
					$data = [
						'tipo' => 'agregar_atencion',
						'area_id' => 3,
						'atencion_id' => $atencion_id,
						'medico_id' => $login,
						'paciente_id' => $_POST['dni'],
						'detalle' => '',
					];

					include ($_SERVER["DOCUMENT_ROOT"] . "/_operaciones/cli_atencion_unica.php");
        }

        if (isset($_POST['dni']) && isset($_POST['graba_nota']) && $_POST['graba_nota'] == 'GRABAR') {
            $stmt = $db->prepare("UPDATE hc_paciente SET nota=?, iduserupdate=?,updatex=? WHERE dni=?");
            $hora_actual = date("Y-m-d H:i:s");
            $stmt->execute(array($_POST['nota'],$login, $hora_actual, $_POST['dni']));
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

        if ($_GET['id'] <> "") {
            $id = $_GET['id'];

            // obtener parejas
            $consulta = $db->prepare("SELECT hc_pareja.p_dni
                from hc_paciente
                inner join hc_pare_paci on hc_pare_paci.dni = hc_paciente.dni and hc_pare_paci.estado = 1
                inner join hc_pareja on hc_pareja.p_dni = hc_pare_paci.p_dni
                inner join lab_tanque_res on lab_tanque_res.sta = hc_pareja.p_dni
                where hc_paciente.dni=?");
            $consulta->execute(array($id));
            $parejas = $consulta->fetch(PDO::FETCH_ASSOC);
            $muestra_congelada_disabled = "disabled";
            $muestra_congelada_numero = "";

            if (!empty($parejas)) {
                $muestra_congelada_disabled = "";
                $muestra_congelada_numero = $consulta->rowCount();
            }

            $rUser = $db->prepare("SELECT role, userx FROM usuario WHERE userx=?");
            $rUser->execute(array($login));
            $user = $rUser->fetch(PDO::FETCH_ASSOC);

            $stmt = $db->prepare("SELECT hc_paciente.dni, nom, ape, fnac, g_fur, don, nota, medios_comunicacion_id programa
                FROM hc_antece, hc_paciente
                WHERE hc_paciente.dni=? AND hc_antece.dni=?");
            $stmt->execute([$id, $id]);
            $paci = $stmt->fetch(PDO::FETCH_ASSOC);

            $rRepro = $db->prepare("SELECT
                id, fec,med, p_dni, p_dni_het, des_dia, des_don, p_dtri, p_cic, p_fiv, p_icsi, p_od, p_don, p_cri, p_iiu, don_todo, f_iny, cancela, f_asp
                FROM hc_reprod
                WHERE estado = true and dni=?
                ORDER BY fec desc, id desc");
            $rRepro->execute(array($id));

            $rPP = $db->prepare("SELECT p_dni FROM hc_pare_paci WHERE dni=? and estado = 1 ORDER BY p_fec DESC");
            $rPP->execute(array($id));

            if (!file_exists("paci/".$paci['dni']."/foto.jpg")) {
                $foto_url = "_images/foto.gif";
            } else {
                $foto_url = "paci/".$paci['dni']."/foto.jpg";
            }
            $key=$_ENV["apikey"];
            ?>
            <input type="hidden" name="login" id="login" value="<?php echo $login;?>">
            <input type="hidden" name="key" id="key" value="<?php echo $key;?>">
            <form action="" method="post" data-ajax="false" name="form2" id="form2">
                <div data-role="page" class="ui-responsive-panel" id="n_repro">
                    <div data-role="panel" id="indice_paci">
                        <img src="_images/logo.jpg"/>
                        <?php require ('_includes/menu_paciente.php'); ?>
                    </div>

                    <?php
                    $color_programa_inmater = '';
                    if ($paci['programa'] == 2) {
                        $color_programa_inmater = ' class="programa_inmater"';
                    } ?>

                    <div data-role="header" data-position="fixed" <?php print($color_programa_inmater); ?>>
                        <a href="#indice_paci" data-icon="bars" id="b_indice" class="ui-icon-alt" data-theme="a">MENU
                            <small>> Repro. Asistida</small>
                        </a>
                        <h2><?php echo $paci['ape']; ?>
                            <small>
                            <?php
                            echo $paci['nom'];
                            // alerta para la nota
                            $nota_color = "";

                            if ($paci['nota'] != "") {
                                $nota_color = "red";
                            }

                            $edad = date_diff(date_create($paci['fnac']), date_create('today'))->y;

                            if ($paci['fnac'] <> "1899-12-30") {
                                echo ' <a href="#popupBasic" data-rel="popup" data-transition="pop" style="color:'.$nota_color.';">('.date_diff(date_create($paci['fnac']), date_create('today'))->y.')</a>';
                            } ?></small>
                        </h2>
                        <a href="salir.php" class="ui-btn-right ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-right ui-icon-power" rel="external"> Salir</a>
                    </div>
                    <div data-role="popup" id="popupBasic" data-arrow="true">
                        <textarea name="nota" id="nota" data-mini="true"><?php echo $paci['nota']; ?></textarea>
                        <input type="Submit" value="GRABAR" name="graba_nota" data-mini="true"/>
                    </div>
                    <div class="ui-content" role="main">
                        <input type="hidden" name="dni" id="dni" value="<?php echo $paci['dni']; ?>">
                        <input type="hidden" name="fnac" value="<?php echo $edad; ?>">
                        <input type="hidden" name="borrar" id="borrar">
                        <input type="hidden" name="anular">
                        <input type="hidden" name="val_beta"> <input type="hidden" name="pro_beta">
                        <div data-role="tabs">
                            <div data-role="navbar">
                                <ul>
                                    <li><a href="#one" data-ajax="false" class="ui-btn-active ui-btn-icon-left ui-icon-bullets">Repro. Asistidas</a></li>
                                    <li><a href="#two" data-ajax="false" class="ui-btn-icon-left ui-icon-edit">Nueva Repro. Asistida</a></li>
                                </ul>
                            </div>
                            <div id="one">
                                <?php
                                if ($rRepro->rowCount() > 0) { ?>
                                    <table data-role="table" class="table-stroke ui-responsive">
                                        <thead>
                                        <tr>
                                            <th>FECHA</th>
                                            <th>MEDICO</th>
                                            <?php
                                            if ($paci['don'] == 'D') {
                                                echo '<th>RECEPTORA</th>';
                                            } else {
                                                echo '<th>PAREJA</th>';
                                            } ?>
                                            <th>PROCEDIMIENTO</th>
                                            <th>TRANSFER BETA</th>
                                            <th>CRIO</th>
                                            <th>ESTADO</th>
                                            <th>INFORME</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $crio = 0;

                                        while ($repro = $rRepro->fetch(PDO::FETCH_ASSOC)) {
                                            if ($paci['don'] == 'D') {
                                                $pareja = '';
                                            } else {
                                                $rPare2 = $db->prepare("SELECT p_nom,p_ape FROM hc_pareja WHERE p_dni=?");
                                                $rPare2->execute(array($repro['p_dni']));
                                                $pare2 = $rPare2->fetch(PDO::FETCH_ASSOC);

                                                if ($repro['p_dni'] == "") {
                                                    $pareja = "SOLTERA";
                                                } else {
                                                    $pareja = (is_array($pare2) && isset($pare2['p_ape'])) ? $pare2['p_ape'] : '';
                                                    $pareja .= ' ';
                                                    $pareja .= (is_array($pare2) && isset($pare2['p_nom'])) ? $pare2['p_nom'] : '';

                                                }
                                            }

                                            $informe = '-';
                                            if ($repro['cancela'] == 1) {
                                                $estado = 'Cancelado';
                                                $informe = '-';
                                            } else if ($repro['cancela'] == 2) {
                                                $estado = 'Trasladado';
                                                $rAspi = $db->prepare("SELECT pro FROM lab_aspira WHERE lab_aspira.rep=? and lab_aspira.estado is true");
                                                $rAspi->execute(array($repro['id']));
                                                $aspi = $rAspi->fetch(PDO::FETCH_ASSOC);
                                                $informe = '<a href="archivos_hcpacientes.php?idEmb=traslado_'.$aspi['pro'].'.pdf" target="new">Ver</a>';
                                            } else if ($repro['cancela'] == 5) {
                                                $estado = 'Retirado';
                                                $rAspi = $db->prepare("SELECT pro FROM lab_aspira WHERE lab_aspira.rep=? and lab_aspira.estado is true");
                                                $rAspi->execute(array($repro['id']));
                                                $aspi = $rAspi->fetch(PDO::FETCH_ASSOC);
                                                $protocolo = $aspi['pro']??'';
                                                $informe = '<a href="archivos_hcpacientes.php?idRet=retiro_'.$protocolo.'.pdf" target="new">Ver</a>';
                                            } else {
                                                if ($repro['p_iiu'] >= 1) {
                                                    $Rcap = $db->prepare("SELECT * FROM lab_andro_cap WHERE iiu=? and eliminado is false");
                                                    $Rcap->execute(array($repro['id']));
                                                    $cap = $Rcap->fetch(PDO::FETCH_ASSOC);

                                                    if ($Rcap->rowCount() == 0) {
                                                        $estado = 'En Proceso <a href="javascript:anular('.$repro["id"].');">(Eliminar)</a>';
                                                    } else {
                                                        $estado = 'Finalizado';
                                                        $informe = '<a href="info.php?t=cap&a='.$cap['p_dni'].'&b='.$cap['id'].'&c='.$paci['dni'].'" target="new">Ver</a>';
                                                    }
                                                } else {
                                                    $rAspi = $db->prepare("SELECT fec,pro,f_fin,dias,fec2,fec3,fec4,fec5,fec6 FROM lab_aspira WHERE lab_aspira.rep=? and lab_aspira.estado is true");
                                                    $rAspi->execute([$repro['id']]);
                                                    $aspi = $rAspi->fetch(PDO::FETCH_ASSOC);
                                                    if ($aspi !== false && is_array($aspi)) {
                                                        $rTran = $db->prepare("SELECT dia,beta FROM lab_aspira_t WHERE pro=? and estado is true");
                                                        $rTran->execute(array($aspi['pro']));
                                                    }
                                                    
                                                        if(isset($aspi['f_fin']))if ($aspi['f_fin'] == "1899-12-30") { $estado = 'En Laboratorio'; }
                                                        if(isset($aspi['f_fin']))if ($aspi['f_fin'] && $aspi['f_fin'] <> "1899-12-30") { $estado = 'Finalizado <i class="color2">'.date("d-m-Y", strtotime($aspi['f_fin'])).'</i>'; }
                                                        if ($repro['don_todo'] == 1) { $estado = 'Finalizado <i class="color2">'.date("d-m-Y", strtotime($repro['f_iny'])).'</i>'; }
                                                        
                                                   
                                                    if (!isset($aspi['f_fin']) && $repro['don_todo'] <> 1) { $estado = 'En Proceso <a href="javascript:anular('.$repro["id"].');">(Eliminar)</a>'; }

                                                    $autori_informes = validacion_permiso(1,$login);

                                                    if ($repro['don_todo'] == 1) {
                                                        $informe = 'Se donó Todo';
                                                    } else if(is_array($aspi))if ($aspi['pro'] > 0) {
                                                        
                                                        if($autori_informes){
                                                            $informe = '<a href="e_repro_info.php?a='.$aspi['pro'].'&b='.$id.'&c='.$repro['id'].'" rel="external">Editar Beta</a><br><a href="info_rm.php?a='.$aspi['pro'].'&b='.$id.'&c='.$repro['p_dni'].'" target="new">Ver Beta</a> <br><a href="info_r.php?a='.$aspi['pro'].'&b='.$id.'&c='.$repro['p_dni'].'" target="new">Ver</a>';
                                                        }else{
                                                            $informe ='<a href="info_r.php?a='.$aspi['pro'].'&b='.$id.'&c='.$repro['p_dni'].'" target="new">Ver</a>';
                                                        }

                                                        $link_video = '';
                                                        $stmt1 = $db->prepare("SELECT * from google_drive_response where drive_id <> CAST(0 as varchar) and estado = 1 and tipo_procedimiento_id = 1 and procedimiento_id = ? order by id desc LIMIT 1 OFFSET 0;");
                                                        $stmt1->execute([$repro['id']]);

                                                        if ($stmt1->rowCount() > 0) {
                                                            $data = $stmt1->fetch(PDO::FETCH_ASSOC);
                                                            $link_video = "<br><a href='https://drive.google.com/open?id=" . $data['drive_id'] . "' target='new'>Embryoscope Video</a>";
                                                        }

                                                        if (!empty($link_video)) {
                                                            $informe .= $link_video;
                                                        }

                                                        if (empty($link_video) && file_exists("emb_pic/embryoscope_" . $aspi['pro'] . ".mp4")) {
                                                            $informe .= "<br><a href='archivos_hcpacientes.php?idEmb=embryoscope_".$aspi['pro'].".mp4' target='new'>Embryoscope Video</a>";
                                                        }

                                                        if (file_exists("emb_pic/embryoscope_".$aspi['pro'].".pdf")) {
                                                            $informe .= "<br><a href='archivos_hcpacientes.php?idEmb=embryoscope_".$aspi['pro'].".pdf' target='new'>Embryoscope Pdf</a>";
                                                        }

                                                        if (file_exists("analisis/ngs_".$aspi['pro'].".pdf")) {
                                                            $informe .= "<br><a href='archivos_hcpacientes.php?idArchivo=ngs_".$aspi['pro']."' target='new'>NGS</a>";
                                                        }

                                                        if ($paci['don'] == 'D') {
                                                            $rRecep = $db->prepare("SELECT
                                                                distinct hp.dni, upper(hp.nom) nom, upper(hp.ape) ape, trim(upper(coalesce(mb.nombre, 'PENDIENTE'))) beta
                                                                from lab_aspira_dias lad
                                                                inner join lab_aspira la on la.pro = lad.pro and la.estado is true
                                                                inner join hc_paciente hp on hp.dni = la.dni
                                                                left join lab_aspira_t lat on lat.pro = la.pro
                                                                left join man_beta_rinicial mb on mb.id = lat.beta
                                                                where lad.pro_c = ? and lad.estado is true;");
                                                            $rRecep->execute(array($aspi['pro']));

                                                            if ($rRecep->rowCount() > 0) {
                                                                $pareja = '';
                                                                while ($recep = $rRecep->fetch(PDO::FETCH_ASSOC)) {
                                                                    $pareja .= "<a href='n_repro.php?id=" . $recep['dni'] . "' target='new'>" . $recep['ape'] . " " . $recep['nom'] . " (<em>" . $recep["beta"] ."</em>)</a><br>";
                                                                }
                                                            }

                                                            if ($repro['p_don'] == 1) {
                                                                $rRecep = $db->prepare("SELECT
                                                                    upper(hp.nom) nom, upper(hp.ape) ape, hp.dni, trim(upper(coalesce(mb.nombre, 'PENDIENTE'))) beta
                                                                    from hc_reprod hr
                                                                    inner join hc_paciente hp on hp.dni = hr.dni
                                                                    inner join lab_aspira la on la.rep = hr.id and la.estado is true
                                                                    left join lab_aspira_t lat on lat.pro = la.pro and lat.estado is true
                                                                    left join man_beta_rinicial mb on mb.id = lat.beta
                                                                    where hr.estado = true and hr.p_od = ? and (hr.f_asp = ? or hr.f_asp = '');");
                                                                $rRecep->execute(array($paci['dni'], $repro['f_asp']));

                                                                if ($rRecep->rowCount() > 0) {
                                                                    while ($recep = $rRecep->fetch(PDO::FETCH_ASSOC)) {
                                                                        $pareja .= "<a href='n_repro.php?id=" . $recep['dni'] . "' target='new'>" . $recep['ape'] . " " . $recep['nom'] . " (<em>" . $recep["beta"] ."</em>)</a><br>";
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    } else { $informe = '-'; }
                                                }
                                            }

                                            $rCrio = $db->prepare("SELECT
                                                lab_aspira_dias.pro
                                                FROM hc_reprod,lab_aspira,lab_aspira_dias
                                                WHERE hc_reprod.estado = true and hc_reprod.id=lab_aspira.rep AND lab_aspira_dias.pro=lab_aspira.pro AND hc_reprod.id=? and lab_aspira_dias.estado is true
                                                AND (lab_aspira_dias.d6f_cic='C' OR lab_aspira_dias.d5f_cic='C' OR lab_aspira_dias.d4f_cic='C'
                                                OR lab_aspira_dias.d3f_cic='C' OR lab_aspira_dias.d2f_cic='C' OR lab_aspira_dias.d0f_cic='C')");
                                            $rCrio->execute(array($repro['id'])); ?>
                                            <tr>
                                                <th>
                                                    <?php
                                                    if ($repro['cancela'] == 2 || $repro['cancela'] == 5) {
                                                        print(date("d-m-Y", strtotime($repro['fec'])));
                                                    } else { ?>
                                                        <a href='<?php echo "e_repro_02.php?id=".$repro['id']; ?>' rel="external"><?php echo date("d-m-Y", strtotime($repro['fec'])); ?></a><br>
                                                        <?php
                                                        // codigo
                                                        $stmt = $db->prepare("SELECT * from cli_atencion_unica cau where estado = 1 and area_id = 3 and atencion_id = ?;");
                                                        $stmt->execute([$repro['id']]);
                                                        if ($stmt->rowCount() > 0) {
                                                            $info = $stmt->fetch(PDO::FETCH_ASSOC);
                                                            print('<small><em>Código de Atención: </em> ' . $info['codigo'] . '</small><br>');
                                                        } ?>
                                                    <?php
                                                        if ($user['userx'] == 'medico1') {
                                                            print('<br><a href="e_repro_02.php?id='.$repro['id'].'" rel="external">Prueba Beta</a>');
                                                        }
                                                    } ?>
                                                </th>
                                                <td>
                                                    <?php $medicos = listarMedicos();
                                                      foreach ($medicos as $medico) {
                                                        if ($medico['codigo'] == $repro['med']) {
                                                            echo strtoupper($medico['nombre']);
                                                        }
                                                        
                                                      }
                                                    ?>
                                                </td>
                                                <td><?php echo '<small>'.$pareja.'</small>';
                                                    if ($repro['p_dni_het'] <> "") { echo " (HETEROLOGO)"; } ?>
                                                </td>
                                                <td><?php
                                                    if ($repro['p_dtri'] >= 1) { echo "Dual Trigger<br>"; }
                                                    if ($repro['p_cic'] >= 1) { echo "Ciclo Natural<br>"; }
                                                    if ($repro['p_fiv'] >= 1) { echo "FIV<br>"; }
                                                    if ($repro['p_icsi'] >= 1) { echo $_ENV["VAR_ICSI"] . "<br>"; }
                                                    if ($repro['p_od'] <> '') { echo "OD Fresco<br>"; }
                                                    if ($repro['p_cri'] >= 1) { echo "Crio Ovulos<br>"; }
                                                    if ($repro['p_iiu'] >= 1) { echo "IIU<br>"; }
                                                    if ($repro['p_don'] == 1) { echo "Donación Fresco<br>"; }
                                                    if ($repro['des_don'] == null && $repro['des_dia'] >= 1) { echo "TED<br>"; }
                                                    if ($repro['des_don'] == null && $repro['des_dia'] === 0) { echo "<small>Descongelación Ovulos Propios</small><br>"; }
                                                    if ($repro['des_don'] <> null && $repro['des_dia'] >= 1) { echo "EMBRIODONACIÓN<br>"; }
                                                    if ($repro['des_don'] <> null && $repro['des_dia'] === 0 && $repro['id']<>2192) { echo "<small>Descongelación Ovulos Donados</small><br>"; } ?>
                                                </td>
                                                <td class="enlinea"><?php
                                                    if (isset($rTran) && $rTran->rowCount() > 0 && $repro['cancela'] == 0) {
                                                        $tra = $rTran->fetch(PDO::FETCH_ASSOC);
                                                        $beta='';
                                                        if(isset($aspi['fec']) && isset($tra['dia']))$beta = $aspi['fec'.$tra['dia']] ; //la fecha del dia de transferencia

                                                        if(isset($tra['dia']))if ($tra['dia'] == 2) { $dt = 15; }
                                                        if(isset($tra['dia']))if ($tra['dia'] == 3) { $dt = 14; }
                                                        if(isset($tra['dia']))if ($tra['dia'] == 4) { $dt = 13; }
                                                        if(isset($tra['dia']))if ($tra['dia'] == 5) { $dt = 12; }
                                                        if(isset($tra['dia']))if ($tra['dia'] == 6) { $dt = 11; }

                                                        $beta = date('d-m-Y', strtotime($beta.' + '.$dt.' days')); ?>
                                                        <select name="beta<?php echo $aspi['pro']; ?>" data-mini="true" onChange="Beta(this,'<?php echo $aspi['pro']; ?>')">
                                                            <option value=0 <?php if(isset($tra['beta']))if ($tra['beta'] == 0) { echo 'selected'; } ?>>Pendiente</option>
                                                            <option value=1 <?php if(isset($tra['beta']))if ($tra['beta'] == 1) { echo 'selected'; } ?>>Positivo</option>
                                                            <option value=2 <?php if(isset($tra['beta']))if ($tra['beta'] == 2) { echo 'selected'; } ?>>Negativo</option>
                                                            <option value=3 <?php if(isset($tra['beta']))if ($tra['beta'] == 3) { echo 'selected'; } ?>>Bioquímico</option>
                                                            <option value=4 <?php if(isset($tra['beta']))if ($tra['beta'] == 4) { echo 'selected'; } ?>>Aborto</option>
                                                            <option value=5 <?php if(isset($tra['beta']))if ($tra['beta'] == 5) { echo 'selected'; } ?>>Anembrionado</option>
                                                            <option value=6 <?php if(isset($tra['beta']))if ($tra['beta'] == 6) { echo 'selected'; } ?>>Ectópico</option>
                                                        </select>
                                                        <?php echo '<i class="color2">'.$beta.'</i>';
                                                    } else { echo '-'; } ?>
                                                </td>
                                                <td><?php
                                                    if ($rCrio->rowCount() > 0) {
                                                        echo 'Si';
                                                        $crio++;
                                                    } else { echo '-'; } ?>
                                                </td>
                                                <td><?php echo $estado; ?></td>
                                                <?php $autori_andro = validacion_permiso(3,$login);?>

                                                <td>
                                                    <?php if ($autori_andro && $repro['des_dia'] < 1) {?>
                                                        <a href='<?php echo "andrologia_nuevo.php?id=".$repro['id']; ?>' rel="external">Andrologia </a><br>
                                                    <?php }?>
                                                    <?php echo $informe; ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                <?php } else {
                                    echo '<p><h3>¡ No tiene Reproducciones Asistidas !</h3></p>';
                                } ?>
                                <p>
                                <table width="100%" cellpadding="5" style="text-align:center;font-size: small;border-collapse: collapse;border: 1px solid #72a2aa;">
                                    <tr>
                                        <th colspan="8"><a href="n_adju.php?id=<?php echo $id; ?>" class="ui-btn ui-mini ui-btn-inline" data-theme="a" rel="external">RESERVA DE OVULOS Y EMBRIONES</a></th>
                                    </tr>
                                    <?php
                                    $rAdju = $db->prepare("
                                    SELECT
                                        lab_aspira.dni, lab_aspira.fec, lab_aspira_dias.*
                                    FROM lab_aspira, lab_aspira_dias
                                    WHERE lab_aspira.pro = lab_aspira_dias.pro and lab_aspira.estado is true AND adju = ? and lab_aspira_dias.estado is true
                                    ORDER BY lab_aspira_dias.ovo ASC");
                                $rAdju->execute(array($id));
                                                              

                                    if ($rAdju->rowCount() > 0) {
                                        $pro_adju = '';

                                        while ($adj = $rAdju->fetch(PDO::FETCH_ASSOC)) {
                                            if ($pro_adju <> $adj['pro']) {
                                                if ($adj['d0f_cic'] == 'C') { $ovo_emb = 'Ovulos'; } else { $ovo_emb = 'Embriones'; }
                                                if ($adj['dni'] == $adj['adju']) { $propios = ' (PROPIOS)'; } else { $propios = ' (DONADOS)'; }
                                                echo '<tr class="ui-bar-b"><th colspan="8">Protocolo: '.$adj['pro'].' ('.date("d-m-Y", strtotime($adj['fec'])).') <a href="info_r.php?a='.$adj['pro'].'&b='.$adj['dni'].'&c=" target="new">Ver Informe</a>'.$propios.'</th></tr>';
                                                echo '<tr><th>ID '.$ovo_emb.' Reservados</th><th>DIA 2</th><th>DIA 3</th><th>DIA 4</th><th>DIA 5</th><th>DIA 6</th><th>NGS</th><th>Estado</th></tr>';
                                            }

                                            echo '<tr><td>'.$adj['ovo'].'</td>';

                                            if ($adj['d2f_cic'] <> '') {
                                                echo '<td>'.$adj['d2cel'].'-'.$adj['d2fra'].'%-'.$adj['d2sim'].'</td>';
                                            } else { echo '<td>-</td>'; }

                                            if ($adj['d3f_cic'] <> '') {
                                                echo '<td>'.$adj['d3cel'].'-'.$adj['d3fra'].'%-'.$adj['d3sim'].'</td>';
                                            } else { echo '<td>-</td>'; }

                                            if ($adj['d4f_cic'] <> '') {
                                                echo '<td>'.$adj['d4cel'].'-'.$adj['d4fra'].'%-'.$adj['d4sim'].'</td>';
                                            } else { echo '<td>-</td>'; }

                                            if ($adj['d5f_cic'] <> '') {
                                                echo '<td>'.$adj['d5cel'].'</td>';
                                            } else { echo '<td>-</td>'; }

                                            if ($adj['d6f_cic'] <> '') {
                                                echo '<td>'.$adj['d6cel'].'</td>';
                                            } else { echo '<td>-</td>'; }

                                            $ngs = '-';

                                            if ($adj['ngs1'] == 1) { $ngs = 'Normal'; }
                                            if ($adj['ngs1'] == 2) { $ngs = 'Anormal'; }
                                            if ($adj['ngs1'] == 3) { $ngs = 'NR'; }
                                            if ($adj['ngs1'] == 4) { $ngs = 'Mosaico'; }

                                            echo '<td>'.$ngs.'</td>';

                                            if ($adj['des'] == '') { $esta = 'Criopreservado'; } else { $esta = 'Descongelado'; }

                                            echo '<td>'.$esta.'</td>';
                                            $pro_adju = $adj['pro'];
                                        }
                                    } ?>
                                </table>
                                </p>
                            </div>
                            <div id="two">
                                <div class="enlinea">
                                <h4 style="margin: 10px 0;"><small>Medico Tratante:</small></h4>
                                <select name="m_tratante" id="m_tratante" data-mini="true" required>
                                    <optgroup label="Lista de Medicos">
                                        <option value="">Seleccionar</option>
                                        <?php
                                          $medicos = listarMedicos();
                                          foreach ($medicos as $medico) {
                                            $selected = '';
                                            if ($medico['codigo'] == $login) {
                                              $selected = 'selected';
                                            }
                                            echo '<option value="'.$medico['codigo'].'"'.$selected.'>'.strtoupper($medico['nombre']).'</option>';
                                          }
                                        ?>
                                    </optgroup>
                                </select>
                                    <h4 style="margin: 10px 0;"><small>Procedimientos:</small></h4>
                                    <select name="tipo_procedimiento" id="tipo_procedimiento" data-mini="true">
                                        <option value="">Seleccionar tipo de procedimiento</option>
                                        <option value="fiv">FIV</option>
                                        <option value="icsi"><?php print($_ENV["VAR_ICSI"]); ?></option>
                                        <option value="crio_ovos">Crio Ovos</option>
                                        <option value="iiu">IIU</option>
                                    </select><br>
                                    <?php
                                    if ($paci['don'] == 'D') { ?>
                                        <input type="checkbox" name="p_don" id="p_don" data-mini="true" value=1>
                                        <label for="p_don">Donación Fresco</label>
                                    <?php
                                    } else {
                                        $where_od_fresco = '';
                                        if ($paci["programa"] == 2) {
                                            $where_od_fresco = ' and hp.medios_comunicacion_id = 2';
                                        }

                                        $stmt = $db->prepare("SELECT
                                            hp.dni, upper(trim(hp.ape)) ape, upper(trim(hp.nom)) nom, upper(trim(hp.med)) med, mmc.nombre programa
                                            from hc_paciente hp
                                            left join man_medios_comunicacion mmc on mmc.id = hp.medios_comunicacion_id $where_od_fresco
                                            where hp.estado=1 and hp.don='D'
                                            order by hp.medios_comunicacion_id desc, hp.ape asc;");
                                        $stmt->execute();
                                        $stmt->setFetchMode(PDO::FETCH_ASSOC);
                                        $donantes_od_fresco = $stmt->fetchAll();

                                        $stmt = $db->prepare("SELECT
                                            hp.dni, upper(trim(hp.ape)) ape, upper(trim(hp.nom)) nom, upper(trim(hp.med)) med, mmc.nombre programa
                                            from hc_paciente hp
                                            inner join man_medios_comunicacion mmc on mmc.id = hp.medios_comunicacion_id
                                            inner join hc_reprod hr on hr.dni = hp.dni
                                            inner join lab_aspira la on la.rep = hr.id and la.estado is true
                                            inner join lab_aspira_dias lad on lad.pro = la.pro and lad.estado is true and lad.des <> 1 and (lad.adju is null or lad.adju = '' or lad.adju = hr.dni)
                                                and (lad.d6f_cic = 'C' or lad.d5f_cic='C' or lad.d4f_cic='C' or lad.d3f_cic='C' or lad.d2f_cic='C')
                                            where hr.estado = true and hp.estado=1 and hp.don='D' $where_od_fresco
                                            group by hp.dni, hp.ape, hp.nom, hp.med, mmc.nombre
                                            order by hp.medios_comunicacion_id desc, hp.ape asc;");
                                        $stmt->execute();
                                        $stmt->setFetchMode(PDO::FETCH_ASSOC);
                                        $donantes_embriones = $stmt->fetchAll();

                                        $stmt = $db->prepare("SELECT
                                            hp.dni, upper(trim(hp.ape)) ape, upper(trim(hp.nom)) nom, upper(trim(hp.med)) med, mmc.nombre programa
                                            from hc_paciente hp
                                            inner join man_medios_comunicacion mmc on mmc.id = hp.medios_comunicacion_id
                                            inner join hc_reprod hr on hr.dni = hp.dni
                                            inner join lab_aspira la on la.rep = hr.id and la.estado is true
                                            inner join lab_aspira_dias lad on lad.pro = la.pro and lad.estado is true and lad.des <> 1 and (lad.adju is null or lad.adju = '' or lad.adju = hr.dni)
                                                and d0f_cic='C'
                                            where hr.estado = true and hp.estado=1 and hp.don='D' $where_od_fresco
                                            group by hp.dni, hp.ape, hp.nom, hp.med, mmc.nombre
                                            order by hp.medios_comunicacion_id desc, hp.ape asc;");
                                        $stmt->execute();
                                        $stmt->setFetchMode(PDO::FETCH_ASSOC);
                                        $donantes_ovulos = $stmt->fetchAll(); ?>

                                        <select name="p_od" id="p_od" data-mini="true">
                                            <option value="">OD Fresco</option>
                                            <optgroup label="Seleccionar Donante">
                                                <?php
                                                    $nombres_completos = '';
                                                    foreach ($donantes_od_fresco as $don) {
                                                    $nombres_completos = $don['ape'].' '.$don['nom'].' ('.$don['med'].')'; ?>
                                                    <option value="<?php echo $don['dni']; ?>"><?php print($nombres_completos); ?></option>
                                                <?php } ?>
                                            </optgroup>
                                        </select><br>
                                        <select name="p_des" id="p_des" data-mini="true">
                                            <option value="" selected>DESCONGELACIÓN: NO</option>
                                            <option value=1>DESCONGELACIÓN: EMBRIODONACIÓN</option>
                                            <option value=2>DESCONGELACIÓN: ÓVULOS DONADOS</option>
                                            <?php
                                            if ($crio > 0) { ?>
                                                <option value=3>DESCONGELACIÓN: TED</option>
                                                <option value=4>DESCONGELACIÓN: ÓVULOS PROPIOS</option>
                                            <?php } ?>
                                        </select>
                                        <select name="donante1" id="donante1" data-mini="true">
                                            <option value="">DONANTES</option>
                                            <optgroup label="Seleccionar Donante">
                                                <?php $nombres_completos = '';
                                                foreach ($donantes_embriones as $don) {
                                                    $nombres_completos = $don['ape'].' '.$don['nom'].' ('.$don['med'].')'; ?>
                                                    <option value="<?php echo $don['dni']; ?>"><?php print($nombres_completos); ?></option>
                                                <?php } ?>
                                            </optgroup>
                                        </select>
                                        <select name="donante2" id="donante2" data-mini="true">
                                            <option value="">DONANTES</option>
                                            <optgroup label="Seleccionar Donante">
                                                <?php $nombres_completos = '';
                                                foreach ($donantes_ovulos as $don) {
                                                    $nombres_completos = $don['ape'].' '.$don['nom'].' ('.$don['med'].')'; ?>
                                                    <option value="<?php echo $don['dni']; ?>"><?php print($nombres_completos); ?></option>
                                                <?php } ?>
                                            </optgroup>
                                        </select>
                                    <?php } ?>
                                    <h4 style="margin: 10px 0;"><small>Adicionales:</small></h4>
                                    <input type="checkbox" name="p_dtri" id="p_dtri" data-mini="true" value=1><label for="p_dtri">Dual Trigger</label>
                                    <input type="checkbox" name="p_cic" id="p_cic" data-mini="true" value=1><label for="p_cic">C. Natural</label><br>

                                    <select name="p_dni" id="p_dni" data-mini="true" data-inline="true">
                                        <option value="">Seleccionar pareja</option>
                                        <?php
                                        while ($pp = $rPP->fetch(PDO::FETCH_ASSOC)) {
                                            $rPare = $db->prepare("SELECT p_nom,p_ape FROM hc_pareja WHERE p_dni=?");
                                            $rPare->execute(array($pp['p_dni']));
                                            $pare = $rPare->fetch(PDO::FETCH_ASSOC);
                                            echo "<option value=".$pp['p_dni'].">".mb_strtoupper($pare['p_ape'])." ".mb_strtoupper($pare['p_nom'])."</option>";
                                        } ?>
                                        <option value="1">SOLTERA</option>
                                    </select><br>
                                    <select name="t_mue" id="t_mue" data-mini="true" data-inline="true">
                                        <option value="">Seleccionar tipo de muestra</option>
                                        <option value=1>MUESTRA: FRESCA</option>
                                        <?php print("<option value=2 ".$muestra_congelada_disabled.">MUESTRA: CONGELADA</option>"); ?>
                                        <option value=4>MUESTRA: BANCO</option>
                                        <option value=3>NO APLICA</option>
                                    </select>
                                    <?php
                                    if (!empty($muestra_congelada_numero)) {
                                        print("<span style='line-height: 50px;'><small>Número viales congelados: ".$muestra_congelada_numero."</small></span>");
                                    } ?><br>
                                    <select name="poseidon" id="poseidon" data-mini="true">
                                        <option value="" selected>Seleccionar poseidon</option>
                                        <?php
                                        $consulta = $db->prepare("select id, nombre from man_poseidon where estado = 1 order by nombre asc");
                                        $consulta->execute();

                                        while ($data = $consulta->fetch(PDO::FETCH_ASSOC)) {
                                            print("<option value=".$data['id'].">".mb_strtoupper($data['nombre'])."</option>");
                                        } ?>
                                    </select>
                                    <a href="#popupVideo" data-rel="popup" data-position-to="window" class="ui-btn ui-shadow ui-corner-all ui-icon-info ui-btn-icon-notext ui-btn-b ui-btn-inline ui-mini">Informe</a><br>
                                    <div data-role="popup" id="popupVideo" data-overlay-theme="b" data-theme="a" data-tolerance="15,15" class="ui-content">
                                        <a href="#" data-rel="back" class="ui-btn ui-btn-b ui-corner-all ui-shadow ui-btn-a ui-icon-delete ui-btn-icon-notext ui-btn-left">Close</a>
                                        <img src="_images/info_poseidon.png" alt="demo">
                                    </div>
                                </div>
                                <div class="lista_des scroll_h">&nbsp;</div>
                                <?php
                                if ($user['role'] == 1) { ?>
                                    <input type="Submit" value="GUARDAR DATOS" name="boton_datos" data-icon="check"
                                        data-iconpos="left" data-mini="true" class="show-page-loading-msg"
                                        data-textonly="false" data-textvisible="true" data-msgtext="Agregando datos.."
                                        data-theme="b" data-inline="true"/>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <form id="form-api" method="POST" style="display: none;">

            </form>
        <?php } ?>
        <script>
            $(document).on("click", ".show-page-loading-msg", function () {
                if (document.getElementById("p_des").value != "") {
                    if (!document.getElementById("des_dia")) {
                        alert("Debe marcar el dia de descongelación");
                        return false;
                    } else if (document.getElementById("des_dia").value == "") {
                        alert("Debe marcar el dia de descongelación");
                        return false;
                    }
                }

                if (document.getElementById("m_tratante").value == "") {
                    alert("Debe seleccionar el medico tratante.");
                    return false;
                }

                if (document.getElementById("p_dni").value == "") {
                    alert("Debe seleccionar el campo de pareja.");
                    return false;
                }
                if (document.getElementById("t_mue").value == "") {
                    alert("Debe seleccionar el tipo de muestra.");
                    return false;
                }

                if (document.getElementById("poseidon").value == "") {
                    alert("Debe seleccionar el campo de poseidon.");
                    return false;
                }

                var nombre_modulo="reproduccion_asistida";
                var ruta="perfil_medico/busqueda_paciente/paciente/reproduccion_asistida.php";
                var tipo_operacion="ingreso";
                var login=$('#login').val();
                var key=$('#key').val();
								var clave='';
								var valor='';
                $.ajax({
                    type: 'POST',
                    dataType: "json",
                    contentType: "application/json",
                    url: '_api_inmater/servicio.php',
                    data:JSON.stringify({ nombre_modulo:nombre_modulo,ruta:ruta,tipo_operacion:tipo_operacion,clave:clave,valor:valor,idusercreate:login,apikey:key }),
                    success: function(result) {
                        console.log(result);
                    }
                });

                var $this = $(this),
                    theme = $this.jqmData("theme") || $.mobile.loader.prototype.options.theme,
                    msgText = $this.jqmData("msgtext") || $.mobile.loader.prototype.options.text,
                    textVisible = $this.jqmData("textvisible") || $.mobile.loader.prototype.options.textVisible,
                    textonly = !!$this.jqmData("textonly");
                html = $this.jqmData("html") || "";

                $.mobile.loading("show", {
                    text: msgText,
                    textVisible: textVisible,
                    theme: theme,
                    textonly: textonly,
                    html: html
                });
            }).on("click", ".hide-page-loading-msg", function () {
                $.mobile.loading("hide");
            });

            $(function () {
                $('#alerta').delay(3000).fadeOut('slow');
            });
        </script>
    </body>
</html>
<?php ob_end_flush(); ?>