<!DOCTYPE HTML>
<html>

<head>
    <?php
   include 'seguridad_login.php'
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="_themes/tema_inmater.min.css" />
    <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css" />
    <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css" />
    <link rel="stylesheet" href="css/n_gine.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/capacitacion.js"></script>
    <script>
    $(document).ready(function() {
        var unsaved = false;
        $(":input").change(function() {

            unsaved = true;

        });
        $(window).on('beforeunload', function() {
            if (unsaved) {
                return 'UD. HA REALIZADO CAMBIOS';
            }
        });
        $(document).on("submit", "form", function(event) {
            $(window).off('beforeunload');
        });
        $('.numeros').keyup(function() {
            var $th = $(this);
            $th.val($th.val().replace(/[^0-9]/g, function(str) {
                return '';
            }));
        });

        $('.chekes').change(function() {
            var temp = '#' + $(this).attr("id") + '1';
            if ($(this).prop('checked') || $(this).val() == "Anormal") {
                $(temp).prop('readonly', false);
            } else {
                $(temp).prop('readonly', true);
                $(temp).val('');
            }
        });

        $('.ui-icon-calendar').click(function(e) {
            $('#mot').val('xxx');
        });
        $('.ui-icon-edit').click(function(e) {
            $('#mot').val('');
        });
    });

    function anular(id, tipo) {
        if (confirm("Esta seguro de eliminar este registro?")) {
            document.form2.consulta_id.value = id;
            document.form2.tipo.value = tipo;
            document.form2.submit();
            return true;
        }
        return false;
    }
    </script>
</head>

<body>
    
    <?php
    if (isset($_POST['consulta_id']) and !empty($_POST['consulta_id']) && isset($_POST['tipo']) and !empty($_POST['tipo'])) {
        switch ($_POST['tipo']) {
            case 'consulta':
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
                        ?, ?, 
                        asesor_medico_id,
                        'D'
                    FROM appinmater_modulo.hc_gineco
                    WHERE id =?");
                $hora_actual = date("Y-m-d H:i:s");
                $log_Gineco->execute(array($login, $hora_actual, $_POST['consulta_id']));

                $stmt = $db->prepare("DELETE FROM hc_gineco WHERE id=?;");
                $stmt->execute(array($_POST['consulta_id']));
                break;
            case 'gineco_mensajes':
                $stmt = $db->prepare("UPDATE hc_gineco_mensajes SET estado = 0 WHERE id=?;");
                $stmt->execute(array($_POST['consulta_id']));
                break;
            default: break;
        }
    }

    if (isset($_POST['boton_datos']) and !empty($_POST['boton_datos']) and $_POST['boton_datos'] == "AGENDAR CONSULTA" and isset($_POST['dni']) and isset($_POST['fec']) and isset($_POST['fec_h'])) {
        insertGine($_POST['dni'], $_POST['fec'], $_POST['asesora'], $_POST['fec_h'], $_POST['fec_m'], $_POST['mot'], (isset($_POST['cupon']) ? intval($_POST['cupon']) : 11),$_POST['m_tratante'],$atencion_id, $_POST['man_motivoconsulta_id'], $login,$_POST['tipoconsulta_id']);
        $data = [
            'tipo' => 'agregar_atencion',
            'area_id' => 1,
            'atencion_id' => $atencion_id,
            'medico_id' => $login,
            'paciente_id' => $_POST['dni'],
            'detalle' => $_POST['mot'],
        ];

        include ($_SERVER["DOCUMENT_ROOT"] . "/_operaciones/cli_atencion_unica.php");
    }

    if (isset($_POST['boton_datos']) and !empty($_POST['boton_datos']) and $_POST['boton_datos'] == "REGISTRAR MENSAJE" and isset($_POST['fecha']) and isset($_POST['paciente_id']) and isset($_POST['idusercreate']) and isset($_POST['mensaje'])) {
        $estado=1;
        insertGineMensaje($_POST['fecha'], $_POST['paciente_id'],  $_POST['idusercreate'], $_POST['mensaje'],$estado, $_POST['id_gineco_tip_atencion']);
    }

    if (isset($_POST['dni']) and isset($_POST['graba_nota']) and $_POST['graba_nota'] == 'GRABAR') {
        $stmt = $db->prepare("UPDATE hc_paciente SET nota=?, iduserupdate=?,updatex=? WHERE dni=?;");
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

    if (isset($_GET['id']) and !empty($_GET['id'])) {
        $id = $_GET['id'];
        $rUser = $db->prepare("SELECT role FROM usuario WHERE userx=?");
        $rUser->execute(array($login));
        $user = $rUser->fetch(PDO::FETCH_ASSOC);

        $rPaci = $db->prepare("SELECT dni, nom, ape, fnac, nota, medios_comunicacion_id, med, asesor_medico_id, idsedes FROM hc_paciente WHERE dni=?");
        $rPaci->execute(array($id));
        $paci = $rPaci->fetch(PDO::FETCH_ASSOC);

        $rGine = $db->prepare(
            "SELECT *
                        from  (
                            SELECT 'consulta' AS tipo, hc.id, hc.fec, hc.mot, hc.cupon, hc.repro, hc.legal, hc.med, am.apellidos || ' ' || am.nombres AS med_t, hc.idusercreate, '' as tipo_atencion
                            FROM hc_gineco hc
                            LEFT JOIN asesor_medico am ON hc.asesor_medico_id = am.id
                            WHERE hc.dni = ?
                            UNION
                            SELECT 'ecografia' as tipo, id, a_mue as fec, a_exa as mot, 0 as cupon, '' as repro, 0 as legal, a_med as med, '' as med_t, '' as idusercreate, '' as tipo_atencion
                            FROM hc_analisis
                            WHERE a_dni = ? AND lab='eco' and estado = 1
                            UNION
                            SELECT 'ecografia-externa' as tipo, id, fconsulta as fec, 'Ecografía externa' as mot, 0 as cupon, '' as repro, 0 as legal, idusercreate as med, '' as med_t, '' as idusercreate, '' as tipo_atencion
                            FROM hc_eco_consultorio
                            WHERE documento = ? and estado = 1
                            UNION
                            SELECT 'ecografia-beta' as tipo, id, fec, CASE WHEN mot = '' THEN 'Primera ecografía beta positivo' ELSE mot END as mot
                            , 0 as cupon, '' as repro, 0 as legal, med, '' as med_t, '' as idusercreate, '' as tipo_atencion
                            FROM hc_eco_beta_positivo
                            WHERE dni = ?
                            UNION
                            SELECT 'gineco_mensajes' as tipo, hgm.id, hgm.fecha as fec, CASE WHEN hgm.estado= '1' THEN 'leido' ELSE 'pendiente' END AS mot
                            , 0 as cupon, hgm.mensaje as repro, 0 as legal, hgm.idusercreate as med, '' as med_t, '' as idusercreate, gta.nombre as tipo_atencion
                            FROM hc_gineco_mensajes hgm
                            INNER JOIN appinmater_modulo.gineco_tipo_atencion gta on gta.id = hgm.id_gineco_tip_atencion
                            WHERE hgm.paciente_id = ? and hgm.estado = 1
                        ) a
                        ORDER BY a.fec DESC;");

        $rGine->execute(array($id, $id, $id, $id, $paci['dni']));

        if (!file_exists("paci/" . $paci['dni'] . "/foto.jpg")) {
            $foto_url = "_images/foto.gif";
        } else {
            $foto_url = "paci/" . $paci['dni'] . "/foto.jpg";
        }
        $key=$_ENV["apikey"];
        ?>
        <div class="modal fade" id="consultaLabModal" tabindex="-1" aria-labelledby="consultaLabModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="consultaLabModalLabel"><span></span>Consulta de laboratorio</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class="row mb-3">
                                <label for="id_tip_atencion_lab" class="col-md-2 col-form-label">Tipo Consulta:</label>
                                <div class="col-md-10">
                                    <select name="id_tip_atencion_lab" id="id_tip_atencion_lab" class="form-control" required>
                                        <option value="">Seleccionar</option>
                                        <?php
                                            $aMedico = $db->prepare("SELECT id, nombre FROM gineco_tipo_atencion where estado = true order by id asc");
                                            $aMedico->execute();
                                            $selected = "";
                                            while ($asesor = $aMedico->fetch(PDO::FETCH_ASSOC)) {
                                                if ($paci['asesor_medico_id'] == $asesor['id'] || $asesor['id'] == 16) {
                                                    $selected = "selected";
                                                } else {
                                                    $selected = "";
                                                }
                                                print("<option value=".$asesor['id']." $selected>".$asesor['nombre']."</option>");
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="fecha_lab" class="col-md-2 col-form-label">Fecha:</label>
                                <div class="col-md-4">
                                    <input type="datetime-local" name="fecha_lab" id="fecha_lab" class="form-control" value="<?php echo date("Y-m-d"); ?>" required>
                                    <input type="hidden" name="paciente_id_lab" id="paciente_id_lab" value='<?php echo $GET['id']; ?>'>
                                    <input type="hidden" name="idusercreate_lab" id="idusercreate_lab" value='<?php echo $login; ?>'>
                                </div>
                            </div>

                             

                            <div class="row mb-3">
                                <label for="mensaje_lab" class="col-md-2 col-form-label">Mensaje:</label>
                                <div class="col-md-10">
                                    <textarea name="mensaje_lab" id="mensaje_lab" class="form-control" rows="4" maxlength="5000" required></textarea>
                                    <small class="form-text text-danger">*Máximo número de caracteres para el mensaje: 5000</small>
                                </div>
                            </div>
                        </div> 
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <?php if($user['role'] =='2'){?>
                        <button type="button" onclick="reg_consulta_labo(this,'')" class="btn btn-primary submit">Guardar</button>
                        <?php }?>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="login" id="login" value="<?php echo $login;?>">
        <input type="hidden" name="key" id="key" value="<?php echo $key;?>">
        <form action="" method="post" data-ajax="false" name="form2" id="form2">
            <div data-role="page" class="ui-responsive-panel" id="n_gine">
                <div data-role="panel" id="indice_paci">
                    <img src="_images/logo.jpg" />
                    <?php require ('_includes/menu_paciente.php'); ?>
                </div>

                <?php
                    $color_programa_inmater = '';
                    if ($paci['medios_comunicacion_id'] == 2) {
                        $color_programa_inmater = ' class="programa_inmater"';
                    } ?>

                <div data-role="header" data-position="fixed" <?php print($color_programa_inmater); ?>>
                    <a href="#indice_paci" data-icon="bars" id="b_indice" class="ui-icon-alt" data-theme="a">MENU
                        <small>> Ginecología</small>
                    </a>
                    <h2><?php echo $paci['ape']; ?>
                        <small>
                            <?php
                                echo $paci['nom'];
                                $nota_color = "";
                                if ($paci['nota'] != "") {
                                    $nota_color = "red";
                                }
                                if ($paci['fnac'] <> "1899-12-30")
                                    echo ' <a href="#popupBasic" data-rel="popup" data-transition="pop" style="color:'.$nota_color.';">(' . date_diff(date_create($paci['fnac']), date_create('today'))->y . ')</a>'; ?>
                        </small>
                    </h2>
                    <a href="salir.php" class="ui-btn-right ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-right ui-icon-power" rel="external"> Salir</a>
                </div>

                <div data-role="popup" id="popupBasic" data-arrow="true">
                    <textarea name="nota" id="nota" data-mini="true"><?php echo $paci['nota']; ?></textarea>
                    <input type="Submit" value="GRABAR" name="graba_nota" data-mini="true" />
                </div>

                <div class="ui-content" role="main">
                    <input type="hidden" name="dni" value="<?php echo $paci['dni']; ?>">
                    <input type="hidden" name="consulta_id">
                    <input type="hidden" name="tipo">
                    <div data-role="tabs">
                        <div data-role="navbar">
                            <ul>
                                <?php if($user['role'] === 2){?>
                                    <li><a onclick="consulta_laboratorio(this)" edit="" class="ui-btn-icon-left ui-icon-edit" data-bs-toggle="modal" data-bs-target="#consultaLabModal">Agendar nueva Consulta</a></li>
                                <?php }else{?>
                                    <li><a href="#one" data-ajax="false" class="ui-btn-active ui-btn-icon-left ui-icon-bullets">Historial de consultas</a>
                                    </li>
                                    <li><a href="#two" data-ajax="false" class="ui-btn-icon-left ui-icon-edit">Agendar nueva Consulta</a></li>
                                <?php }?>    
                            </ul>
                        </div>

                        <div id="one">
                            <ol data-role="listview" data-theme="a" data-split-icon="delete" data-inset="true">
                                <?php while ($gine = $rGine->fetch(PDO::FETCH_ASSOC)) { ?>
                                <li>
                                    <?php
                                            switch ($gine['tipo']) {
                                                case 'consulta': ?>
                                    <a href='<?php  echo "e_gine.php?id=" . $gine['id'] ?>' rel="external">
                                        <b>(Consulta)</b><br>
                                        <?php break ?>

                                        <?php
                                                case 'ecografia-externa':
                                                    $consulta_ecos = $db->prepare("SELECT
                                                        id, nombre
                                                        from hc_eco_consultorio_img
                                                        where id_eco_consultorio=?");
                                                    $consulta_ecos->execute(array($gine["id"]));
                                                    $eco = $consulta_ecos->fetch(PDO::FETCH_ASSOC); ?>
                                        <a href='<?php  echo "eco_consultorio/" . $id . '/' . $eco['nombre'] ?>' target="_blank">
                                            <b>(Eco-externa)</b>
                                            <?php break ?>

                                            <?php case 'ecografia': ?>
                                            <a href='<?php echo "archivos_hcpacientes.php?idArchivo=" . $gine['id'] .'_'. $id ?>' target="_blank" rel="external">
                                                <b>(Eco-Inmater)</b>
                                                <?php break ?>

                                                <?php case 'ecografia-beta': ?>
                                                <a href='<?php  echo "eco_beta_positivo.php?id=" . $gine['id'] ?>&n_gine' rel="external">
                                                    <b>(Eco-Inmater)</b>
                                                    <?php break ?>
                                                    <?php case 'gineco_mensajes': ?> 
                                                        
                                                            <a onclick="consulta_laboratorio(this)" edit="<?php echo $gine['id']; ?>" data-bs-toggle="modal" data-bs-target="#consultaLabModal">
                                                         

                                                        <?php  echo $gine['tipo_atencion']."<br>" ?>

                                                        <?php break ?>
                                                        <?php } ?>

                                                        <?php
                                            $stmt = $db->prepare("SELECT * from cli_atencion_unica cau where estado = 1 and area_id = 1 and atencion_id = ?;");
                                            $stmt->execute([$gine['id']]);
                                            if ($stmt->rowCount() > 0) {
                                                $info = $stmt->fetch(PDO::FETCH_ASSOC);
                                                print('<small><em>Código de Atención: </em> ' . $info['codigo'] . '</small><br>');
                                            } ?>
                                                        <?php if($gine['tipo']=='gineco_mensajes'){?>
                                                        <small>Mensaje: <?php echo $gine['repro']; ?></small><br>
                                                        <?php }else{?>
                                                        <small><?php echo $gine['mot']; ?></small><br>
                                                        <?php }?>
                                                        <?php
                                                $estado = '';

                                                if ($gine['repro'] <> '' and $gine['repro'] <> 'NINGUNA') $estado .= 'REPRO. ASISTIDA  ';
                                                //cupones
                                                $stmt = $db->prepare("SELECT codigo_facturacion codigo, nombre from sedes where estado = 1 order by nombre");
                                                $stmt->execute();

                                                while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                    if ($data['codigo'] == $gine['cupon']) {
                                                        $estado .= "(" . mb_strtoupper($data['nombre']) . ") ";
                                                    }
                                                }

                                                if ($gine['fec'] > date("Y-m-d")) $estado .= 'PENDIENTE PARA '; ?>
                                                        <?php if($gine['tipo']=='gineco_mensajes'){?>
                                                        <br><span class="ui-li-count"><?php echo date("d-m-Y", strtotime($gine['fec'])); ?></span><br>
                                                        <?php }else{?>
                                                        <br><span class="ui-li-count"><?php echo $estado . date("d-m-Y", strtotime($gine['fec'])); ?></span><br>
                                                        <?php }?>
                                                        <?php print("<span class='ui-li-count'>Medico Tratante: " . $gine['med'] . "</span>"); ?><br>
                                                        <?php print("<span class='ui-li-count'>Asesora: " . $gine['med_t'] . "</span>"); ?><br>
                                                    </a>

                                                    <?php if ($gine['legal'] == 0) { 
                                                                if (trim($gine["tipo"]) == 'gineco_mensajes') {
                                                                    if ($user['role'] =='2') {
                                                                        echo '<a href="javascript:anular(\''.$gine["id"].'\', \''.$gine["tipo"].'\');">Eliminar</a>';
                                                                    }
                                                                   
                                                                }
                                                                else{
                                                                    echo '<a href="javascript:anular(\''.$gine["id"].'\', \''.$gine["tipo"].'\');">Eliminar</a>';
                                                                }
                                                          } ?>
                                </li>
                                <?php }
                                    if ($rGine->rowCount() < 1) echo '<p><h3>¡ No hay consultas pasadas !</h3></p>'; ?>
                            </ol>
                        </div>
                        <?php if($user['role']!='2'){?>
                        <div id="two">
                            <p>Aquí podra agendar la próxima consulta, la cual aparecerá en Historial de Consulta como
                                "pendiente".</p>
                            <table width="100%" align="center" style="margin: 0 auto;max-width:800px;">
                                <tr>
                                    <td>Medico Tratante:</td>
                                    <td>
                                        <div data-role="controlgroup" data-type="horizontal" data-mini="true">
                                            <select name="m_tratante" id="m_tratante" data-mini="true" required>
                                                <optgroup label="Lista de Medicos">
                                                    <option value="">Seleccionar</option>
                                                    <?php
                                                    $medicos = listarMedicos();
                                                    $selected = "";
                                                    foreach ($medicos as $medico) {
                                                        
                                                        if ($paci['med'] == $medico['codigo']) {
                                                            $selected = "selected";
                                                        } else {
                                                            $selected = "";
                                                        }

                                                        echo '<option value="'.$medico['codigo'].'" '.$selected .'>'.strtoupper($medico['nombre']).'</option>';
                                                    }
                                                    ?>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Asesora:</td>
                                    <td>
                                        <div data-role="controlgroup" data-type="horizontal" data-mini="true">
                                            <select name="asesora" id="asesora" data-mini="true" required>
                                                <option value="">Seleccionar</option>
                                                <?php
                                                            $aMedico = $db->prepare("SELECT id,upper(apellidos || ' ' || nombres)nombre FROM asesor_medico where eliminado=0 order by id asc");
                                                            $aMedico->execute();
                                                            $selected = "";
                                                            while ($asesor = $aMedico->fetch(PDO::FETCH_ASSOC)) {
                                                                
                                                                if ($paci['asesor_medico_id'] == $asesor['id'] || $asesor['id']==16) {
                                                                    $selected = "selected";
                                                                } else {
                                                                    $selected = "";
                                                                }
                                                                print("<option value=".$asesor['id']." $selected>".$asesor['nombre']."</option>");
                                                            }
                                                        ?>
                                            </select>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td width="20%">Tipo de consulta:</td>
                                    <td width="50%">
                                        <div data-role="controlgroup" data-type="horizontal" data-mini="true">
                                            <input type="radio" name="tipoconsulta_id" id="tipoconsulta_presencial" data-mini="true" value="1">
                                            <label for="tipoconsulta_presencial">Presencial</label>
                                            <input type="radio" name="tipoconsulta_id" id="tipoconsulta_virtual" data-mini="true" value="2">
                                            <label for="tipoconsulta_virtual">Virtual</label>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Sede:</td>
                                    <td>
                                        <div data-role="controlgroup" data-type="horizontal" data-mini="true">
                                            <select name="cupon" id="cupon" data-mini="true">
                                                <option value="0" selected>Seleccione Sede</option>
                                                <?php
                                                    $stmt = $db->prepare("SELECT codigo_facturacion codigo, nombre from sedes where estado_consulta = 1 order by nombre");
                                                    $stmt->execute();

                                                    while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {

                                                        if ($paci['idsedes'] == $data['codigo'] ) {
                                                            $selected = "selected";
                                                        } else {
                                                            $selected = "";
                                                        }

                                                        print('<option value="' . $data['codigo'] . '" '. $selected.'>'. ucwords(mb_strtolower($data['nombre'])) . '</option>');
                                                    } ?>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Fecha:</td>
                                    <td>
                                        <div data-role="controlgroup" data-type="horizontal" data-mini="true">
                                            <input name="fec" type="date" id="fec" value="<?php echo date("Y-m-d"); ?>" data-wrapper-class="controlgroup-textinput ui-btn">

                                            <select name="fec_h" id="fec_h">
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

                                            <select name="fec_m" id="fec_m">
                                                <option value="">Min</option>
                                                <option value="00">00 min</option>
                                                <option value="15">15 min</option>
                                                <option value="30">30 min</option>
                                                <option value="45">45 min</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td width="30%">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>Motivo de Consulta</td>
                                    <td colspan="2">
                                        <div data-role="controlgroup" data-type="horizontal" data-mini="true">
                                            <select name="man_motivoconsulta_id" id="man_motivoconsulta_id" data-mini="true">
                                                <option value="" selected>Seleccione el motivo de consulta</option>
                                                <?php
                                                    $stmt = $db->prepare("SELECT id, nombre from man_gine_motivoconsulta where estado = 1 order by nombre;");
                                                    $stmt->execute();

                                                    while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
                                                <option value="<?php echo $data['id']; ?>">
                                                    <?php echo ucwords(mb_strtolower($data['nombre'])); ?></option>
                                                <?php } ?>
                                            </select><br><br>
                                            <textarea name="mot" id="mot" data-mini="true"></textarea>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <?php
                                if ($user['role'] == 1 || $user['role'] == 12 || $user['role'] == 15) { ?>
                                    <input type="Submit" value="AGENDAR CONSULTA" name="boton_datos" data-icon="check" data-iconpos="left" data-mini="true" class="btn btn-success btn-sm" data-textonly="false" data-textvisible="true" data-msgtext="Agregando datos.." data-theme="b" data-inline="true" />
                            <?php } ?>
                        </div>

                        <?php }else{
                            if ($user['role'] != 2){
                            ?>
                                <div id="two">
                                    <h4>Aquí podra agendar la próxima consulta, la cual aparecerá en Historial de Consulta como
                                        "pendiente".</h4>
                                    <table width="100%" align="center" style="margin: 0 auto;max-width:1000px;">

                                        <tr>
                                            <td>Tipo Consulta:</td>
                                            <td>
                                                <div data-role="controlgroup" data-type="horizontal" data-mini="true">
                                                    <select name="id_gineco_tip_atencion" id="id_gineco_tip_atencion" data-mini="true" required>
                                                        <option value="">Seleccionar</option>
                                                        <?php
                                                                    $aMedico = $db->prepare("SELECT id, nombre FROM gineco_tipo_atencion where estado = true order by id asc");
                                                                    $aMedico->execute();
                                                                    $selected = "";
                                                                    while ($asesor = $aMedico->fetch(PDO::FETCH_ASSOC)) {
                                                                        
                                                                        if ($paci['asesor_medico_id'] == $asesor['id'] || $asesor['id']==16) {
                                                                            $selected = "selected";
                                                                        } else {
                                                                            $selected = "";
                                                                        }
                                                                        print("<option value=".$asesor['id']." $selected>".$asesor['nombre']."</option>");
                                                                    }
                                                                ?>
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="10%">Fecha:</td>
                                            <td width="50%">
                                                <div data-role="controlgroup" data-type="horizontal" data-mini="true">
                                                    <input type="datetime-local" name="fecha" id="fecha" value="<?php echo date("Y-m-d"); ?>" data-wrapper-class="controlgroup-textinput ui-btn" required>
                                                    <input type="hidden" name="paciente_id" id="paciente_id" data-mini="true" value='<?php echo $paci["dni"]; ?>'>
                                                    <input type="hidden" name="idusercreate" id="idusercreate" data-mini="true" value='<?php echo $login; ?>'>

                                                </div>
                                            </td>

                                        </tr>
                                        <td style="color: red; font-size: 12px" colspan="3">*Maximo numero de caracteres para el mensaje: 5000</td>
                                        <tr>
                                            <td>Mensaje:</td>
                                            <td colspan="2">
                                                <div data-role="" data-type="horizontal" data-mini="true">
                                                    <textarea rows="4" name="mensaje" id="mensaje" data-mini="true" maxlength="5000" required></textarea>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                    <?php
                                        if ($user['role'] == 1 || $user['role'] == 12 || $user['role'] == 15 ) { ?>
                                    <input type="Submit" value="AGENDAR CONSULTA" name="boton_datos" data-icon="check" data-iconpos="left" data-mini="true" class="show-page-loading-msg" data-textonly="false" data-textvisible="true" data-msgtext="Agregando datos.." data-theme="b" data-inline="true" />
                                    <?php } ?>
                                    <?php
                                        if ($user['role'] == 2) { ?>
                                    <input type="Submit" value="REGISTRAR MENSAJE" name="boton_datos" data-icon="check" data-iconpos="left" data-mini="true" class="show-page-loading-msg" data-textonly="false" data-textvisible="true" data-msgtext="Agregando datos.." data-theme="b" data-inline="true" />
                                    <?php } ?>

                                </div>
                        <?php }}?>
                    </div>
                </div>
            </div>
        </form>
        <form id="form-api" method="POST" style="display: none;"></form>
    <?php } ?>

    <script>
    $(document).ready(function() {
        $("#cupon").selectmenu('disable');

        $("input[name='tipoconsulta_id']").bind("change", function(event, ui) {
            // 1: presencial, 2: virtual
            if (this.value == 1) {
                $("#cupon").selectmenu('enable');
            } else {
                $("#cupon").prop('selectedIndex', 0);
                $("#cupon").selectmenu("refresh", true);
                $("#cupon").selectmenu('disable');
            }
        });
    });
    $(document).on("click", ".show-page-loading-msg", function() {
        if (document.getElementById("m_tratante").value == "") {
            alert("Debe llenar el campo 'Medico'");
            return false;
        }

        if (document.getElementById("asesora").value == "") {
            alert("Debe llenar el campo 'Asesora'");
            return false;
        }

        if (document.getElementById("fec").value == "") {
            alert("Debe llenar el campo 'Fecha'");
            return false;
        }

        if (document.getElementById("fec_h").value == "") {
            alert("Debe llenar el campo 'Hora'");
            return false;
        }

        if (document.getElementById("fec_m").value == "") {
            alert("Debe llenar el campo 'Minuto'");
            return false;
        }

        if (document.getElementById("man_motivoconsulta_id").value == "") {
            alert("Debe llenar el campo de motivo de consulta");
            return false;
        }

        if (!($('input[name="tipoconsulta_id"]:checked').val())) {
            alert("Debe marcar el tipo de consulta.");
            return false;
        }

        var nombre_modulo = "ginecologia";
        var ruta = "perfil_medico/busqueda_paciente/ginecologia.php";
        var tipo_operacion = "ingreso";
        var login = $('#login').val();
        var key = $('#key').val();
        var clave = '';
        var valor = '';
        
        $.ajax({
            type: 'POST',
            dataType: "json",
            contentType: "application/json",
            url: '_api_inmater/servicio.php',
            data: JSON.stringify({
                nombre_modulo: nombre_modulo,
                ruta: ruta,
                tipo_operacion: tipo_operacion,
                clave: clave,
                valor: valor,
                idusercreate: login,
                apikey: key
            }),
            success: function(result) {
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
    }).on("click", ".hide-page-loading-msg", function() {
        $.mobile.loading("hide");
    });

    $(function() {
        $('#alerta').delay(3000).fadeOut('slow');
    });
    function consulta_laboratorio(THIS){
        edit = $(THIS).attr("edit");
        
        if (edit.trim() != '') {
            mostrarLoader("Edición de consulta","Espere por favor");
            $('#consultaLabModal .modal-header span').text('Editar - ');
            $.ajax({
                type: 'GET',
                dataType: "json",
                contentType: "application/json",
                url: 'ajax/edit_consulta_laboratorio.php?id='+edit,
                success: function(result) {
                    ocultarLoader();
                    if (result.status == 'success') {
                        if (result.data) { 
                            $("#consultaLabModal .submit").attr("onclick","reg_consulta_labo(this, "+edit+");")
                            $('#idusercreate_lab').val(result.data[0].idusercreate);
                            $('#paciente_id_lab').val(result.data[0].paciente_id);
                            $('#id_tip_atencion_lab').val(result.data[0].id_gineco_tip_atencion);
                            $('#fecha_lab').val(result.data[0].fecha);
                            $('#mensaje_lab').val(result.data[0].mensaje); 
                            $('#estado_lab').val(result.data[0].estado);
                        } 
                    }
                    else{ 
                    } 
                },error:function(err){
                    ocultarLoader();
                }
            });
        }
        else{
            $('#idusercreate_lab').val('');
            $('#paciente_id_lab').val('');
            $('#id_tip_atencion_lab').val('');
            $('#fecha_lab').val('');
            $('#mensaje_lab').val(''); 
            $('#estado_lab').val('');
            $('#consultaLabModal .modal-header span').text('Registro - ');
            ocultarLoader();
        }
    }
    function reg_consulta_labo(THIS, edit = ''){
        id_form = null;
        modal = $('#consultaLabModalLabel');
        //edit = $(THIS).attr("edit");
        id_user = $('#idusercreate_lab').val();
        paciente = $('#paciente_id_lab').val();
        tipo = $('#id_tip_atencion_lab').val();
        fecha = $('#fecha_lab').val();
        mensaje = $('#mensaje_lab').val();
        estado = $('#estado_lab').val();

        if (edit == '') {
            const url = new URL(window.location.href);
            id_form = url.searchParams.get('id');
            mostrarLoader("Espere por favor...","Registrando consulta");
        }else{
            mostrarLoader("Espere por favor...","Guardando consulta");
        }
        
        var formData = new FormData();
         
        formData.append('id', edit); 
        formData.append('tipo', tipo); 
        formData.append('fecha', fecha); 
        formData.append('mensaje', mensaje); 
        formData.append('id_user', id_user);  
        formData.append('paciente', paciente); 
        formData.append('estado', estado); 
        formData.append('id2', id_form);   

        $.ajax({
            type: "POST",
            url: 'ajax/consulta_laboratorio.php',
            data: formData,
            dataType: "json",
            processData: false,
            contentType: false,
            success: function(response) {
                ocultarLoader();
                if (response.status) {
                    mostrarToast("success",response.message);
                }
                else{
                    mostrarToast("error",response.message);
                }
                if (edit == '') {
                    $('#idusercreate_lab').val('');
                    $('#paciente_id_lab').val('');
                    $('#id_tip_atencion_lab').val('');
                    $('#fecha_lab').val('');
                    $('#mensaje_lab').val(''); 
                    $('#estado_lab').val('');
                } 
                setTimeout(function() {
                    window.location.reload();
                }, 3000);
            },
            error: function(errResponse) {
                ocultarLoader();
                mostrarToast("error",errResponse);
            }
        });

         
    }
    </script>
</body>
</html>
