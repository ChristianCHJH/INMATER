<!DOCTYPE HTML>
<html>

<head>
    <title>Inmater Clínica de Fertilidad | Editar Datos de Paciente</title>
    <?php
   include 'seguridad_login.php'
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="_images/favicon.png" type="image/x-icon">

    <link rel="stylesheet" href="_themes/tema_inmater.min.css" />
    <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css" />
    <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
    <link rel="stylesheet" href="css/e_paci.css" />

    <script src="js/jquery-1.11.1.min.js?v=1.0"></script>
    <script src="js/jquery.mobile-1.4.5.min.js?v=1.0"></script>
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
    .color_red {
        color: red;
    }

    #medios_comunicacion_id {
        pointer-events: none;
    }
    </style>
</head>

<body>
    <?php
	if (isset($_POST['dni']) and $_POST['boton_datos'] == 'GUARDAR DATOS') {
		try {
			updatePaci(
				$_POST['dni'],
				$_POST['validarDniValue'],
				$_POST['tip'],
                $_POST['m_tratante'],
                $_POST['asesora'],
				$_POST['nom'],
				$_POST['ape'],
				$_POST['sedes'],
				$_POST['fnac'],
				$_POST['tcel'],
				$_POST['tcas'],
				$_POST['tofi'],
				$_POST['mai'],
				$_POST['dir'],
				$_POST['nac'],
				$_POST['depa'],
				$_POST['prov'],
				$_POST['dist'],
				$_POST['prof'],
				$_POST['san'],
				$_POST['don'],
				$_POST['raz'],
				$_POST['peso'],
				$_POST['talla'],
				$_FILES['foto'],
				$_POST['rem'],
				$_POST['sta'],
				[
					"nivel_instruccion" => $_POST["nivel_instruccion"],
					"icq" => $_POST["icq"],
					"color_cabello" => $_POST["color_cabello"],
					"color_ojos" => $_POST["color_ojos"],
					"donante_foto1" => $_FILES["donante_foto1"],
					"donante_foto2" => $_FILES["donante_foto2"],
					"donante_evaluacion_psicologica" => $_FILES["donante_evaluacion_psicologica"],
					"donante_cariotipo" => $_FILES["donante_cariotipo"]
				],
				$login,
				$_POST['medios_comunicacion_id']
			);
			updatePaciAnte(
				$_POST['dni'],
				$_POST['f_dia'],
				$_POST['f_hip'],
				$_POST['f_gem'],
				$_POST['f_hta'],
				$_POST['f_tbc'],
				$_POST['f_can'],
				$_POST['f_otr'],
				$_POST['m_dia'],
				$_POST['m_hip'],
				$_POST['m_inf1'],
				$_POST['m_ale'],
				$_POST['m_ale1'],
				$_POST['m_tbc'],
				$_POST['m_ets'],
				$_POST['m_can'],
				$_POST['m_otr'],
				$_POST['h_str'],
				$_POST['h_dep'],
				$_POST['h_dro'],
				$_POST['h_tab'],
				$_POST['h_alc'],
				$_POST['h_otr'],
				$_POST['g_men'],
				$_POST['g_per'],
				$_POST['g_dur'],
				$_POST['g_vol'],
				$_POST['g_fur'],
				$_POST['g_ant'],
				$_POST['g_pap'],
				$_POST['g_pap1'],
				$_POST['g_pap2'],
				$_POST['g_dis'],
				$_POST['g_ges'],
				$_POST['g_abo'],
				$_POST['g_abo1'],
				$_POST['g_abo_ges'],
				$_POST['g_abo_com'],
				$_POST['g_pt'],
				$_POST['g_pp'],
				$_POST['g_vag'],
				$_POST['g_ces'],
				$_POST['g_nv'],
				$_POST['g_nm'],
				$_POST['g_neo'],
				$_POST['g_viv'],
				$_POST['g_fup'],
				$_POST['g_rn_men'],
				$_POST['g_rn_mul'],
				$_POST['g_rn_may'],
				$_POST['g_agh'],
				$_POST['g_his'],
				$_POST['g_obs'],
				$_POST['fe_exa']
			);
		} catch (Exception $e) {
			var_dump($e);
		}
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

    if (isset($_GET['id']) and !empty($_GET['id'])) {
			$id = $_GET['id'];
			$rUser = $db->prepare("SELECT role, userx FROM usuario WHERE userx=?");
			$rUser->execute(array($login));
			$user = $rUser->fetch(PDO::FETCH_ASSOC);

			$rPaci = $db->prepare("SELECT * FROM hc_antece,hc_paciente WHERE hc_paciente.dni=? AND hc_antece.dni=?");
			$rPaci->execute(array($id, $id));
			$paci = $rPaci->fetch(PDO::FETCH_ASSOC);

			$a_pap = $db->prepare("SELECT * FROM hc_antece_pap WHERE dni=? ORDER BY fec DESC");
			$a_pap->execute(array($id));

			$a_quiru = $db->prepare("SELECT * FROM hc_antece_quiru WHERE dni=? ORDER BY fec DESC");
			$a_quiru->execute(array($id));

			$Sero = $db->prepare("SELECT * FROM hc_antece_p_sero WHERE p_dni=? ORDER BY fec DESC");
			$Sero->execute(array($id));

			$a_cirug = $db->prepare("SELECT * FROM hc_antece_cirug WHERE dni=? ORDER BY fec DESC");
			$a_cirug->execute(array($id));

			$a_trata = $db->prepare("SELECT * FROM hc_antece_trata WHERE dni=? and eliminado=0 ORDER BY fec DESC");
			$a_trata->execute(array($id));

			/* $rPago = $db->prepare("SELECT
				id, tip
				FROM recibos
				WHERE dni=? AND anglo ILIKE '%Correcto%'");
			$rPago->execute(array($id));

			if ($rPago->rowCount() > 0) {
				include 'nusoap/lib/nusoap.php';
				include $_SERVER["DOCUMENT_ROOT"] . "/config/environment.php";

				$client = new nusoap_client($_ENV["anglolab_ws"], 'wsdl');
				$client->soap_defencoding = 'UTF-8';

				while ($pago = $rPago->fetch(PDO::FETCH_ASSOC)) {
                    $param = array('dato' => $pago['id'] . '-' . $pago['tip']);
                    $result = $client->call('Consulta_Resultado_Laboratorio_Inmater', $param);
				}
			} */

        $rLegal = $db->prepare("SELECT * FROM hc_legal WHERE a_dni=? ORDER BY a_fec ASC");
        $rLegal->execute(array($id));

        if (!file_exists("paci/" . $paci['dni'] . "/foto.jpg")) {
            $foto_url = "_images/foto.gif";
        } else {
            $foto_url = "paci/" . $paci['dni'] . "/foto.jpg";
        }
$key=$_ENV["apikey"];
        ?>
    <input type="hidden" name="login" id="login" value="<?php echo $login;?>">
    <input type="hidden" name="key" id="key" value="<?php echo $key;?>">
    <input type="hidden" name="paciente" id="paciente" value="<?php echo $id; ?>">
    <form action="" method="post" enctype="multipart/form-data" data-ajax="false" name="form2" name="formapi" id="formapi">
        <input type="hidden" name="dni" value="<?php echo $paci['dni']; ?>">
        <input type="hidden" name="login" value="<?php print($login); ?>">
        <div data-role="page" class="ui-responsive-panel" id="e_paci">
            <div data-role="panel" id="indice_paci">
                <img src="_images/logo.jpg" />
                <?php require ('_includes/menu_paciente.php'); ?>
            </div>
            <?php
                if ((isset($_GET['pop']) && $_GET['pop'] <> 1) || !isset($_GET['pop'])) {
                    $color_programa_inmater = '';
                    if ($paci['medios_comunicacion_id'] == 2) {
                        $color_programa_inmater = ' class="programa_inmater"';
                    } ?>
            <div data-role="header" data-position="fixed" <?php print($color_programa_inmater); ?>>
                <a href="#indice_paci" data-icon="bars" id="b_indice" class="ui-icon-alt" data-theme="a">MENU
                    <small> Datos y Antecedentes</small>
                </a>
                <h2><?php echo $paci['ape']; ?>
                    <small>
                        <?php
                                    echo $paci['nom'];
                                    //alerta para la nota
                                    $nota_color = "";
                                    if ($paci['nota'] != "") {
                                        $nota_color = "red";
                                    }
                                    if ($paci['fnac'] <> "1899-12-30")
                                        echo ' <a href="#popupBasic" data-rel="popup" data-transition="pop" style="color:'.$nota_color.';">(' . date_diff(date_create($paci['fnac']), date_create('today'))->y . ')</a>';
                                ?>
                    </small>
                </h2>
                <a href="salir.php" id="salir" class="ui-btn-right ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-right ui-icon-power" rel="external">Salir</a>
            </div><!-- /header -->
            <div data-role="popup" id="popupBasic" data-arrow="true">
                <textarea name="nota" id="nota" data-mini="true"><?php echo $paci['nota']; ?></textarea>
                <input type="Submit" value="GRABAR" name="graba_nota" data-mini="true" />
            </div>
            <?php } ?>
            <div class="ui-content" role="main">
                <?php // $older_than_yesterday = strtotime('yesterday') > strtotime($paci['update']) ?>
                <?php // $must_update = !$paci['update'] || @$older_than_yesterday ?>
                <?php $must_update = !$paci['idsedes'] && $user['role'] == 1 ?>

                <?php if( $must_update ): ?>
                <p style="color: red;">Debe registrar la sede y guardar los datos.</p>
                <?php endif ?>
                <div data-role="collapsibleset" data-theme="a" data-content-theme="a" data-mini="true">
                    <?php
                            if ($login == 'medico1') {
                                print('<label for="correo_acceso">Generar acceso virtual:</label>');
                                $consulta = $db->prepare("select email from hc_paciente_accesos where estado = 1 and dni = ?");
                                $consulta->execute(array($id));
                                $accesos = $consulta->fetch(PDO::FETCH_ASSOC);

                                if (!!$accesos['email']) {
                                    print('<label style="font-size: 12px; color: green;">Ya hemos enviado sus datos de acceso al correo: ' . $accesos['email'] . '</label>');
                                }

                                print('
                                <fieldset class="ui-grid-c">
                                    <div class="ui-block-a">
                                        <input name="correo_acceso" type="text" id="correo_acceso" data-mini="true" data-theme="b" data-inline="true" placeholder="Escribir correo electrónico"/>
                                    </div>
                                    <div class="ui-block-b">
                                        <input type="button" value="Enviar" name="enviar_acceso" id="enviar_acceso" data-mini="true" data-theme="b" data-inline="true" data-icon="mail"/>
                                        <span id="send"></span>
                                    </div>
                                </fieldset>');
                            }
                            $data_collapsed_false = '';
                            if ((isset($_GET['pop']) and $_GET['pop'] <> 1) || $must_update) {
                                $data_collapsed_false =  ' data-collapsed="false"';
                            }

                            print('<div data-role="collapsible"' . $data_collapsed_false . '>'); ?>
                    <h3>Datos Generales</h3>
                    <div class="scroll_h">
                        <table width="100%" align="center" style="margin: 0 auto;max-width:800px;">
                            <tr>
                                <td class="color_red" colspan="3">*Campos obligatorios</td>

                                <?php 
                                    if ($paci['valid_reniec_api'] == true) { ?>

                                <td style="text-align: right; width: 50%;color:green" id="msgValidacion" colspan="2">**VALIDADO CON <strong>RENIEC</strong></td>

                                <?php } elseif($paci['valid_reniec_api'] == false){?>

                                <td style="text-align: right; width: 50%;color:#c63737" id="msgValidacion" colspan="2">**<strong>NO</strong> VALIDADO CON <strong>RENIEC</strong></td>
                                <?php   }
                                ?>


                            </tr>
                            <tr>
                                <td><small>Tipo de Cliente<span class="color_red">*</span></small></td>
                                <td colspan="2"><small>Programa<span class="color_red">*</span></small></td>
                            </tr>
                            <tr>
                                </td>
                                <td>

                                    <?php
                                    $restric = '';
                                    if ($paci['don'] == "EXT"){ 
                                        $restric = ' style="pointer-events: none;"';
                                    } 
                                 ?>
                                    <select name="don" id="don" data-mini="true" <?='onchange="validacion_med(this);"'?> <?php echo $restric ?>>
                                        <?php
                                                    //Dato extraido
                                                    $codigo= $paci['don'];
                                                        $stmt = $db->prepare("SELECT codigo, nombre from tipo_cliente where codigo='$codigo'");
                                                        $stmt->execute();
                                                        if(!isset($stmt)){
                                                            print("<option value='' selected >Seleccionar</option>");
                                                        }
                                                        print("<option value='' >Seleccionar</option>");
                                                        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                            print("<option value=" . $data['codigo'] . " selected>" . $data['nombre']."</option>");
                                                        } 
                                                    
                                                    //Datos disponibles
                                                    $stmt2 = $db->prepare("SELECT codigo, nombre from tipo_cliente where eliminado=0 and (codigo='EXT' or codigo != '$codigo')");
                                                        $stmt2->execute();
                                                        while ($data2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                                                            print("<option value=" . $data2['codigo'] . ">" . $data2['nombre']."</option>");
                                                        } 
                                                    ?>
                                    </select>
                                </td>

                                <script>
                                function validacion_med(valor) {
                                    var val = valor.value;

                                    if (val == 'EXT') {
                                        $('#medios_comunicacion_id').val('5').change();
                                    } else if (val == 'P' || val == 'D') {
                                        $('#medios_comunicacion_id').val('<?php echo $paci['medios_comunicacion_id'];?>').change();
                                    }


                                };
                                </script>

                                <td colspan="2">
                                    <select name="medios_comunicacion_id" id="medios_comunicacion_id" data-mini="true">
                                        <?php
                                                $dato= $paci['medios_comunicacion_id'];
                                                $stmt = $db->prepare("SELECT id, nombre from man_medios_comunicacion where estado = 1 ");
                                                $stmt->execute();
                                                print("<option id= 'mc0' value='0' > NA </option>");
                                                while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                    if ($data['id'] == $paci['medios_comunicacion_id']) {
                                                        print("<option id='mc" . $data['id'] . "' value=" . $data['id'] . " selected>" . $data['nombre']."</option>");
                                                    } else if ($data['id'] == 5) {
                                                        print("<option id='mc" . $data['id'] . "' value=" . $data['id'] . ">" . $data['nombre']."</option>");
                                                    }
                                                } ?>
                                    </select>
                                </td>
                                <td style="position:relative; top:20px;text-align: center;">Medico Tratante<span class="color_red">*</span></td>
                                <td style="position:relative; top:20px;text-align: center;">Asesora</td>

                            </tr>
                            <tr>
                                <td>Procedencia<span class="color_red">*</span></td>
                                <td colspan="2">
                                    <select name="sedes" id="sedes" data-mini="true">
                                        <option value="">Seleccionar</option>
                                        <?php
                                                    $rSedes = $db->prepare("SELECT * FROM sedes where estado = 1 order by nombre;");
                                                    $rSedes->execute();
                                                    $selected = "";
                                                    while ($sede = $rSedes->fetch(PDO::FETCH_ASSOC)) {
                                                        if ($sede['id'] == $paci['idsedes']) {
                                                            $selected="selected";
                                                        } else {
                                                            $selected = "";
                                                        }
                                                        print("<option value=".$sede['id']." $selected>".$sede['nombre']."</option>");
                                                    }
                                                ?>
                                    </select>
                                </td>
                                <td colspan="1">
                                    <select name="m_tratante" id="m_tratante" data-mini="true">
                                        <option value="">Seleccionar</option>
                                        <?php
                                        $mTratante = $db->prepare("SELECT codigo id, upper(nombre)nombre FROM man_medico where estado=1;");
                                        $mTratante->execute();
                                        $selected = "";
                                        while ($med = $mTratante->fetch(PDO::FETCH_ASSOC)) {
                                            if ($med['id'] != $paci['med']) {
                                                $selected="";
                                            } else {
                                                $selected = "selected";
                                            }
                                            print("<option value=".$med['id']." $selected>".$med['nombre']."</option>");
                                        } ?>
                                    </select>
                                </td>
                                <td colspan="1">
                                    <select name="asesora" id="asesora" data-mini="true">
                                        <option value="">Seleccionar</option>
                                        <?php
                                                    $aMedico = $db->prepare("SELECT id,upper(apellidos || ' ' || nombres)nombre FROM asesor_medico where eliminado=0 ");
                                                    $aMedico->execute();
                                                    $selected = "";
                                                    while ($asesor = $aMedico->fetch(PDO::FETCH_ASSOC)) {
                                                        if ($asesor['id'] != $paci['asesor_medico_id']) {
                                                            $selected="";
                                                        } else {
                                                            $selected = "selected";
                                                        }
                                                        print("<option value=".$asesor['id']." $selected>".$asesor['nombre']."</option>");
                                                    }
                                                ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>

                                <?php 
                                $readonly_val_dni = "";
                                $value_val_dni = "0"; 
                                $btn_val_dni = "";
                                $select_val_dni = 'style ="pointer-events: none;"';
                                if ($paci['valid_reniec_api'] == true) {
                                    $readonly_val_dni = "readonly";
                                    $value_val_dni = "2"; 
                                    $btn_val_dni = "display: none;";

                                } elseif($paci['valid_reniec_api'] == false){
                                    $readonly_val_dni = "";
                                    $value_val_dni = "1"; 
                                    $btn_val_dni = '';
                            }
                            ?>
                                <td>
                                    <input type="hidden" name="validarDniValue" id="validarDniValue" value="<?php echo $value_val_dni;?>">

                                    <select name="tip" id="tip" data-mini="true" <?php echo $select_val_dni;?>>
                                        <option value="DNI" <?php if ($paci['tip'] == "DNI") echo "selected"; ?>>DNI
                                        </option>
                                        <option value="PAS" <?php if ($paci['tip'] == "PAS") echo "selected"; ?>>PAS
                                        </option>
                                        <option value="CEX" <?php if ($paci['tip'] == "CEX") echo "selected"; ?>>CEX
                                        </option>
                                    </select>
                                </td>
                                <td style="display: flex; align-items: center;">
                                    <input id="dni" name="dni" data-mini="true" value="<?php echo $paci['dni']; ?>" readonly>
                                </td>
                                <td>F. Nac<span class="color_red">*</span></td>
                                <td>
                                    <input name="fnac" type="date" id="fnac" data-mini="true" value="<?php echo $paci['fnac']; ?>" <?php echo $readonly_val_dni;?> />
                                </td>

                                <td colspan="2" rowspan="8">
                                    <fieldset data-role="controlgroup">
                                        <select name="nac" id="nac" data-mini="true" title="Nacionalidad">
                                            <option value="">Nacionalidad *</option>
                                            <?php $rPais = $db->prepare("SELECT * FROM countries ORDER BY countryname ASC");
                                                    $rPais->execute();
                                                    while ($pais = $rPais->fetch(PDO::FETCH_ASSOC)) { ?>
                                            <option value="<?php echo $pais['countrycode']; ?>" <?php if ($paci['nac'] == $pais['countrycode']) echo " selected"; ?>>
                                                <?php echo $pais['countryname']; ?></option>
                                            <?php } ?>
                                        </select>
                                        <select name="raz" id="raz" data-mini="true" title="Raza">
                                            <option value="">Raza:</option>
                                            <option value="Blanca" <?php if ($paci['raz'] == "Blanca") echo "selected"; ?>>
                                                Blanca
                                            </option>
                                            <option value="Morena" <?php if ($paci['raz'] == "Morena") echo "selected"; ?>>
                                                Morena
                                            </option>
                                            <option value="Mestiza" <?php if ($paci['raz'] == "Mestiza") echo "selected"; ?>>
                                                Mestiza
                                            </option>
                                            <option value="Asiatica" <?php if ($paci['raz'] == "Asiatica") echo "selected"; ?>>
                                                Asiatica
                                            </option>
                                        </select>
                                        <select name="san" id="san" data-mini="true" title="Grupo Sanguineo">
                                            <option value="">Grupo Sanguineo:</option>
                                            <option value="O+" <?php if ($paci['san'] == "O+") echo "selected"; ?>>GS:
                                                O+
                                            </option>
                                            <option value="O-" <?php if ($paci['san'] == "O-") echo "selected"; ?>>GS:
                                                O-
                                            </option>
                                            <option value="A+" <?php if ($paci['san'] == "A+") echo "selected"; ?>>GS:
                                                A+
                                            </option>
                                            <option value="A-" <?php if ($paci['san'] == "A-") echo "selected"; ?>>GS:
                                                A-
                                            </option>
                                            <option value="B+" <?php if ($paci['san'] == "B+") echo "selected"; ?>>GS:
                                                B+
                                            </option>
                                            <option value="B-" <?php if ($paci['san'] == "B-") echo "selected"; ?>>GS:
                                                B-
                                            </option>
                                            <option value="AB+" <?php if ($paci['san'] == "AB+") echo "selected"; ?>>GS:
                                                AB+
                                            </option>
                                            <option value="AB-" <?php if ($paci['san'] == "AB-") echo "selected"; ?>>GS:
                                                AB-
                                            </option>
                                        </select>

                                        <input name="talla" type="number" step="any" id="talla" data-mini="true" placeholder="Talla(Cm)" value="<?php echo $paci['talla']; ?>" />
                                        <input name="peso" type="number" step="any" id="peso" data-mini="true" placeholder="Peso(Kg)" value="<?php echo $paci['peso']; ?>" />

                                        <a href="#popupPerfil" data-rel="popup" data-position-to="window" data-transition="fade"><img src="<?php echo $foto_url; ?>" width="100px" height="100px" id="preview" /></a>
                                        <div data-role="popup" id="popupPerfil" data-overlay-theme="b" data-theme="b" data-corners="false">
                                                <a href="#" data-rel="back" class="ui-btn ui-corner-all ui-shadow ui-btn-a ui-icon-delete ui-btn-icon-notext ui-btn-right">Close</a><img src="<?php echo $foto_url; ?>" style="max-height:512px;">
                                        </div>
                                        <input name="foto" type="file" onchange="previewImage(this)" accept="image/jpeg" id="foto" />
                                    </fieldset>
                                    <script type="text/javascript">
                                    function previewImage(input) {
                                        var preview = document.getElementById('preview');
                                        if (input.files && input.files[0]) {
                                            var reader = new FileReader();
                                            reader.onload = function(e) {
                                                preview.setAttribute('src', e.target.result);
                                            }
                                            reader.readAsDataURL(input.files[0]);
                                        } else {
                                            preview.setAttribute('src', 'placeholder.png');
                                        }
                                    }
                                    </script>
                                </td>
                            </tr>
                            <tr>
                                <td width="9%">Nombres<span class="color_red">*</span></td>
                                <td width="19%">
                                    <input name="nom" type="text" id="nom" data-mini="true" value="<?php echo $paci['nom']; ?>" <?php echo $readonly_val_dni;?> readonly/>
                                </td>
                                <td width="13%">Apellidos<span class="color_red">*</span></td>
                                <td width="29%">
                                    <input name="ape" type="text" id="ape" data-mini="true" value="<?php echo $paci['ape'];?>" <?php echo $readonly_val_dni;?> readonly/>
                                </td>

                            </tr>
                            <tr>
                                <td>Celular<span class="color_red">*</span></td>
                                <td><input name="tcel" type="text" id="tcel" data-mini="true" class="numeros" value="<?php echo $paci['tcel']; ?>" /></td>
                                <td>E-Mail<span class="color_red">*</span></td>
                                <td><input name="mai" type="text" id="mai" data-mini="true" value="<?php echo $paci['mai']; ?>"></td>
                            </tr>
                            <tr>
                                <td>T. Casa</td>
                                <td><input name="tcas" type="text" id="tcas" data-mini="true" class="numeros" value="<?php echo $paci['tcas']; ?>" /></td>
                                <td>Profesión</td>
                                <td><input name="prof" type="text" id="prof" data-mini="true" value="<?php echo $paci['prof']; ?>" /></td>
                            </tr>
                            <tr>
                                <td>T. Oficina</td>
                                <td><input name="tofi" type="text" id="tofi" data-mini="true" value="<?php echo $paci['tofi']; ?>" /></td>
                                <td>Referido por<span class="color_red">*</span></td>
                                <td colspan="1">
                                    <select name="rem" id="rem" data-mini="true">
                                        <option value="">Seleccionar</option>
                                        <?php
                                                    $mReferencia = $db->prepare("SELECT id, upper(nombre)nombre FROM medios_referencia where eliminado=0 ");
                                                    $mReferencia->execute();
                                                    $selected = "";
                                                    while ($referencia = $mReferencia->fetch(PDO::FETCH_ASSOC)) {
                                                        if ($referencia['id'] != $paci['medio_referencia_id']) {
                                                            $selected="";
                                                        } else {
                                                            $selected = "selected";
                                                        }
                                                        print("<option value=".$referencia['id']." $selected>".$referencia['nombre']."</option>");
                                                    }
                                                ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Dep/Prov/Dis</td>
                                <td><select name="depa" id="depa" data-mini="true" title="Departamento">
                                        <option value="">Departamento:</option>
                                        <?php $rDepa = $db->prepare("SELECT * FROM departamentos ORDER BY nomdepartamento ASC");
                                                $rDepa->execute();
                                                while ($depa = $rDepa->fetch(PDO::FETCH_ASSOC)) { ?>
                                        <option value="<?php echo $depa['iddepartamento']; ?>" <?php if ($paci['depa'] == $depa['iddepartamento']) echo " selected"; ?>>
                                            <?php echo $depa['nomdepartamento']; ?></option>
                                        <?php } ?>
                                    </select></td>
                                <td>
                                    <select name="prov" id="prov" data-mini="true">
                                        <?php
                                                    $rProvincia = $db->prepare("SELECT * FROM provincias where idprovincia=? ");
                                                    $rProvincia->execute(array($paci['prov']));
                                                    $selected = "";
                                                    while ($prov = $rProvincia->fetch(PDO::FETCH_ASSOC)) {
                                                        if ($prov['idprovincia'] != $paci['prov']) {
                                                            $selected="";
                                                        } else {
                                                            $selected = "selected";
                                                        }
                                                        print("<option value=".$prov['idprovincia']." $selected>".$prov['nomprovincia']."</option>");
                                                    }
                                                ?>
                                    </select>
                                </td>
                                <td><select name="dist" id="dist" data-mini="true" title="Distrito">
                                        <?php $rDist = $db->prepare("SELECT * FROM distritos WHERE iddistrito=?");
                                                $rDist->execute(array($paci['dist']));
                                                $dist = $rDist->fetch(PDO::FETCH_ASSOC);
                                                if ($rDist->rowCount() !== 0) echo "<option value=" . $dist['iddistrito'] . " selected>" . $dist['nomdistrito'] . "</option>"; ?>
                                    </select></td>
                            </tr>
                            <tr>
                                <td>Dirección<span class="color_red">*</span></td>
                                <td colspan="3"><input name="dir" type="text" id="dir" data-mini="true" value="<?php echo $paci['dir']; ?>" /></td>
                            </tr>
                            <tr>
                                <td>Observaciones</td>
                                <td colspan="3"><textarea name="sta" id="sta" data-mini="true"><?php echo $paci['sta']; ?></textarea></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <?php include "_includes/e-paci-donante.php"; ?>

                <div data-role="collapsible">
                    <h3>Familiares</h3>
                    <div class="scroll_h">
                        <table width="100%" align="center" style="margin: 0 auto;max-width:800px;">
                            <tr>
                                <td width="7%"><input type="checkbox" name="f_dia" id="f_dia" data-mini="true" value="Si" <?php if ($paci['f_dia'] == "Si") echo "checked"; ?>><label for="f_dia">Diabetes</label></td>
                                <td width="10%"><input type="checkbox" name="f_hip" id="f_hip" data-mini="true" value="Si" <?php if ($paci['f_hip'] == "Si") echo "checked"; ?>><label for="f_hip">Hipertensión</label></td>
                                <td width="17%"><input type="checkbox" name="f_gem" id="f_gem" data-mini="true" value="Si" <?php if ($paci['f_gem'] == "Si") echo "checked"; ?>><label for="f_gem">Gemelares</label></td>
                                <td width="5%"><input type="checkbox" name="f_hta" id="f_hta" data-mini="true" value="Si" <?php if ($paci['f_hta'] == "Si") echo "checked"; ?>><label for="f_hta">HTA</label></td>
                                <td width="61%"><select name="f_tbc" id="f_tbc" data-mini="true">
                                        <option value="" selected="selected">TBC:</option>
                                        <option value="NO" <?php if ($paci['f_tbc'] == "NO") echo "selected"; ?>>TBC: NO
                                        </option>
                                        <optgroup label="TBC: SI">
                                            <option value="Pulmonar" <?php if ($paci['f_tbc'] == "Pulmonar") echo "selected"; ?>>
                                                TBC: Pulmonar
                                            </option>
                                            <option value="Extrapulmonar" <?php if ($paci['f_tbc'] == "Extrapulmonar") echo "selected"; ?>>
                                                TBC: Extrapulmonar
                                            </option>
                                        </optgroup>

                                    </select></td>
                            </tr>
                            <tr>
                                <td colspan="4">Cancer
                                    <textarea name="f_can" id="f_can" data-mini="true"><?php echo $paci['f_can']; ?></textarea>
                                </td>
                                <td>Otros
                                    <textarea name="f_otr" id="f_otr" data-mini="true"><?php echo $paci['f_otr']; ?></textarea>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div data-role="collapsible">
                    <h3>Médicos</h3>
                    <table width="100%" align="center" style="margin: 0 auto;max-width:800px;">
                        <tr>
                            <td width="24%"><input type="checkbox" name="m_dia" id="m_dia" data-mini="true" value="Si" <?php if ($paci['m_dia'] == "Si") echo "checked"; ?>>
                                <label for="m_dia">Diabetes</label>
                            </td>
                            <td width="27%"><input type="checkbox" name="m_hip" id="m_hip" data-mini="true" value="Si" <?php if ($paci['m_hip'] == "Si") echo "checked"; ?>>
                                <label for="m_hip">Hipertensión</label>
                            </td>
                            <td width="49%"><select name="m_tbc" id="m_tbc" data-mini="true">
                                    <option value="" selected="selected">TBC:</option>
                                    <option value="NO" <?php if ($paci['m_tbc'] == "NO") echo "selected"; ?>>TBC: NO
                                    </option>
                                    <optgroup label="TBC: SI">
                                        <option value="Pulmonar" <?php if ($paci['m_tbc'] == "Pulmonar") echo "selected"; ?>>
                                            TBC: Pulmonar
                                        </option>
                                        <option value="Extrapulmonar" <?php if ($paci['m_tbc'] == "Extrapulmonar") echo "selected"; ?>>
                                            TBC: Extrapulmonar
                                        </option>
                                    </optgroup>
                                </select></td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" name="m_inf" id="m_inf" data-mini="true" class="chekes" <?php if ($paci['m_inf'] <> "") echo "checked"; ?>>
                                <label for="m_inf">Infecciones</label>
                            </td>
                            <td colspan="2"><input name="m_inf1" type="text" id="m_inf1" data-mini="true" placeholder="Especifique.." readonly value="<?php echo $paci['m_inf']; ?>"></td>
                        </tr>
                        <tr>
                            <td><select name="m_ale" id="m_ale" data-mini="true" class="chekes">
                                    <option value="" selected="selected">Alergias:</option>
                                    <option value="NO" <?php if ($paci['m_ale'] == "NO") echo "selected"; ?>>Alergia: NO
                                    </option>
                                    <option value="Medicamentada" <?php if ($paci['m_ale'] == "Medicamentada") echo "selected"; ?>>
                                        Alergia: Medicamentada
                                    </option>
                                    <option value="Otra" <?php if ($paci['m_ale'] == "Otra") echo "selected"; ?>>
                                        Alergia:
                                        Otra
                                    </option>
                                </select></td>
                            <td colspan="2"><input name="m_ale1" type="text" id="m_ale1" data-mini="true" placeholder="Especifique.." readonly value="<?php echo $paci['m_ale1']; ?>"></td>
                        </tr>
                        <tr>
                            <td colspan="2">Cancer
                                <textarea name="m_can" id="m_can" data-mini="true"><?php echo $paci['m_can']; ?></textarea>
                            </td>
                            <td>Otros
                                <textarea name="m_otr" id="m_otr" data-mini="true"><?php echo $paci['m_otr']; ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td><select name="select" class="med_insert" title="m_ets" data-mini="true">
                                    <option value="" selected="selected">ETS:</option>
                                    <option value="Borrar">--- Borrar Datos ---</option>
                                    <option value="Clamidiasis">ETS: Clamidiasis</option>
                                    <option value="Gonorrea">ETS: Gonorrea</option>
                                    <option value="Chancroide">ETS: Chancroide</option>
                                    <option value="Sífilis">ETS: Sífilis</option>
                                    <option value="Mycoplasma genitalium">ETS: Mycoplasma genitalium</option>
                                    <option value="VIH">ETS: VIH</option>
                                    <option value="Herpes virus">ETS: Herpes virus</option>
                                    <option value="Tricomoniasis">ETS: Tricomoniasis</option>
                                    <option value="Ladillas">ETS: Ladillas</option>
                                    <option value="VPH">ETS: VPH</option>
                                    <option value="Vaginosis bacteriana">ETS: Vaginosis bacteriana</option>
                                    <option value="Hepatitis C">ETS: Hepatitis C</option>
                                    <option value="Enfermedad pélvica inflamatoria">ETS: Enfermedad pélvica
                                        inflamatoria
                                    </option>
                                    <option value="Verrugas genitales por papiloma humano">ETS: Verrugas genitales por
                                        papiloma humano
                                    </option>
                                </select></td>
                            <td colspan="2"><textarea name="m_ets" readonly id="m_ets" data-mini="true"><?php echo $paci['m_ets']; ?></textarea></td>
                        </tr>
                    </table>
                </div>

                <div data-role="collapsible">
                    <h3>Hábitos</h3>
                    <div class="scroll_h">
                        <table width="100%" align="center" style="margin: 0 auto;max-width:800px;">
                            <tr>
                                <td><select name="h_str" id="h_str" data-mini="true">
                                        <option value="">Stress</option>
                                        <option value="NO" <?php if ($paci['h_str'] == "NO") echo "selected"; ?>>Stress:
                                            NO
                                        </option>
                                        <optgroup label="Stress: SI">
                                            <option value="Bajo" <?php if ($paci['h_str'] == "Bajo") echo "selected"; ?>>
                                                Stress: Bajo
                                            </option>
                                            <option value="Medio" <?php if ($paci['h_str'] == "Medio") echo "selected"; ?>>
                                                Stress: Medio
                                            </option>
                                            <option value="Alto" <?php if ($paci['h_str'] == "Alto") echo "selected"; ?>>
                                                Stress: Alto
                                            </option>
                                        </optgroup>
                                    </select></td>
                                <td width="9%"><select name="h_dep" id="h_dep" data-mini="true">
                                        <option value="">Deportes</option>
                                        <option value="NO" <?php if ($paci['h_dep'] == "NO") echo "selected"; ?>>
                                            Deportes:
                                            NO
                                        </option>
                                        <optgroup label="Deportes: SI">
                                            <option value="Bajo" <?php if ($paci['h_dep'] == "Bajo") echo "selected"; ?>>
                                                Deportes: Bajo
                                            </option>
                                            <option value="Medio" <?php if ($paci['h_dep'] == "Medio") echo "selected"; ?>>
                                                Deportes: Medio
                                            </option>
                                            <option value="Alto" <?php if ($paci['h_dep'] == "Alto") echo "selected"; ?>>
                                                Deportes: Alto
                                            </option>
                                        </optgroup>
                                    </select></td>
                                <td width="8%"><select name="h_dro" id="h_dro" data-mini="true">
                                        <option value="">Drogas</option>
                                        <option value="NO" <?php if ($paci['h_dro'] == "NO") echo "selected"; ?>>Drogas:
                                            NO
                                        </option>
                                        <optgroup label="Drogas: SI">
                                            <option value="Bajo" <?php if ($paci['h_dro'] == "Bajo") echo "selected"; ?>>
                                                Drogas: Bajo
                                            </option>
                                            <option value="Medio" <?php if ($paci['h_dro'] == "Medio") echo "selected"; ?>>
                                                Drogas: Medio
                                            </option>
                                            <option value="Alto" <?php if ($paci['h_dro'] == "Alto") echo "selected"; ?>>
                                                Drogas: Alto
                                            </option>
                                        </optgroup>
                                    </select></td>
                                <td width="9%"><select name="h_tab" id="h_tab" data-mini="true">
                                        <option value="">Tabaco</option>
                                        <option value="NO" <?php if ($paci['h_tab'] == "NO") echo "selected"; ?>>Tabaco:
                                            NO
                                        </option>
                                        <optgroup label="Tabaco: SI">
                                            <option value="Bajo" <?php if ($paci['h_tab'] == "Bajo") echo "selected"; ?>>
                                                Tabaco: Bajo
                                            </option>
                                            <option value="Medio" <?php if ($paci['h_tab'] == "Medio") echo "selected"; ?>>
                                                Tabaco: Medio
                                            </option>
                                            <option value="Alto" <?php if ($paci['h_tab'] == "Alto") echo "selected"; ?>>
                                                Tabaco: Alto
                                            </option>
                                        </optgroup>
                                    </select></td>
                                <td><select name="h_alc" id="h_alc" data-mini="true">
                                        <option value="">Alcohol</option>
                                        <option value="NO" <?php if ($paci['h_alc'] == "NO") echo "selected"; ?>>
                                            Alcohol:
                                            NO
                                        </option>
                                        <optgroup label="Alcohol: SI">
                                            <option value="Bajo" <?php if ($paci['h_alc'] == "Bajo") echo "selected"; ?>>
                                                Alcohol: Bajo
                                            </option>
                                            <option value="Medio" <?php if ($paci['h_alc'] == "Medio") echo "selected"; ?>>
                                                Alcohol: Medio
                                            </option>
                                            <option value="Alto" <?php if ($paci['h_alc'] == "Alto") echo "selected"; ?>>
                                                Alcohol: Alto
                                            </option>
                                        </optgroup>
                                    </select></td>
                            </tr>
                            <tr>
                                <td width="9%">Otro</td>
                                <td colspan="4">
                                    <input name="h_otr" type="text" id="h_otr" data-mini="true" value="<?php echo $paci['h_otr']; ?>">
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div data-role="collapsible" id="Quiru">
                    <h3>Quirúrgicos</h3>

                    <a href="e_ante_quiru.php?dni=<?php echo $paci['dni'] . "&id="; ?>" rel="external" class="ui-btn ui-btn-inline ui-mini" style="float:left">Agregar</a>
                    <div class="scroll_h">

                        <table width="85%" style="margin:0 auto;font-size:small;" class="ui-responsive table-stroke">
                            <thead>
                                <tr>

                                    <th width="11%" align="left">Fecha</th>
                                    <th width="31%" align="left">Procedimiento</th>
                                    <th width="13%" align="left">Médico</th>
                                    <th width="33%" align="left">Diagnóstico</th>
                                    <th width="11%" align="left">Lugar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($quiru = $a_quiru->fetch(PDO::FETCH_ASSOC)) { ?>
                                <tr>
                                    <td><a href="e_ante_quiru.php?dni=<?php echo $paci['dni'] . "&id=" . $quiru['id']; ?>" rel="external"><?php echo date("d-m-Y", strtotime($quiru['fec'])); ?></a><?php if (file_exists("analisis/quiru_" . $quiru['id'] . ".pdf")) echo "<br><a href='archivos_hcpacientes.php?idArchivo=quiru_" . $quiru['id'] . "' target='new'>Descargar</a>"; ?>
                                    </td>
                                    <td><?php echo $quiru['pro']; ?></td>
                                    <td><?php echo $quiru['med']; ?></td>
                                    <td><?php echo $quiru['dia']; ?></td>
                                    <td><?php echo $quiru['lug']; ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <?php if ($a_quiru->rowCount() < 1) echo '<p><h3>¡ No hay datos aún !</h3></p>'; ?>

                    </div>
                </div>

                <div data-role="collapsible">
                    <h3>Gineco - Obstétricos</h3>

                    <div class="scroll_h">
                        <table width="100%" style="margin: 0 auto;max-width:800px;">
                            <tr>
                                <td><select name="g_men" id="g_men" data-mini="true">
                                        <option value="" selected="selected">Menarquia:</option>

                                        <?php for ($i = 9; $i <= 16; $i++) { ?>
                                        <option value=<?php echo $i;
                                                    if ($paci['g_men'] == $i) echo " selected"; ?>>
                                            Menarquia: <?php echo $i; ?></option>
                                        <?php } ?>

                                    </select></td>
                                <td><select name="g_per" id="g_per" data-mini="true">
                                        <option value="" selected="selected">Periocidad:</option>
                                        <option value="Regular" <?php if ($paci['g_per'] == "Regular") echo " selected"; ?>>
                                            Periocidad: Regular
                                        </option>
                                        <option value="Iregular" <?php if ($paci['g_per'] == "Iregular") echo " selected"; ?>>
                                            Periocidad: Iregular
                                        </option>
                                    </select></td>
                                <td width="16%"><select name="g_dur" id="g_dur" data-mini="true">
                                        <option value="" selected="selected">Duración:</option>
                                        <option value="3 a 5" <?php if ($paci['g_dur'] == "3 a 5") echo "selected"; ?>>
                                            Duración: 3 a 5
                                        </option>
                                        <option value="5 a 10" <?php if ($paci['g_dur'] == "5 a 10") echo "selected"; ?>>
                                            Duración: 5 a 10
                                        </option>
                                        <option value="Más de 10" <?php if ($paci['g_dur'] == "Aumentado") echo "selected"; ?>>
                                            Duración: Más de 10
                                        </option>
                                    </select></td>
                                <td width="14%"><select name="g_vol" id="g_vol" data-mini="true">
                                        <option value="" selected="selected">Volumen:</option>
                                        <option value="Normal" <?php if ($paci['g_vol'] == "Normal") echo "selected"; ?>>
                                            Volumen: Normal
                                        </option>
                                        <option value="Disminuido" <?php if ($paci['g_vol'] == "Disminuido") echo "selected"; ?>>
                                            Volumen: Disminuido
                                        </option>
                                        <option value="Aumentado" <?php if ($paci['g_vol'] == "Aumentado") echo "selected"; ?>>
                                            Volumen: Aumentado
                                        </option>
                                    </select></td>
                                <td width="3%"><label for="g_fur">FUR</label></td>
                                <td width="11%"><input type="date" data-clear-btn="false" name="g_fur" id="g_fur" value="<?php echo $paci['g_fur']; ?>"></td>
                                <td><select name="g_ant" id="g_ant" data-mini="true">
                                        <option value="" selected="selected">Anticoncepción:</option>
                                        <option value="NO" <?php if ($paci['g_ant'] == "NO") echo "selected"; ?>>
                                            Anticoncepción: NO
                                        </option>
                                        <optgroup label="Anticoncepción: SI">
                                            <option value="Implante anticonceptivo" <?php if ($paci['g_ant'] == "Implante anticonceptivo") echo "selected"; ?>>
                                                Implante anticonceptivo
                                            </option>
                                            <option value="Parche anticonceptivo" <?php if ($paci['g_ant'] == "Parche anticonceptivo") echo "selected"; ?>>
                                                Parche anticonceptivo
                                            </option>
                                            <option value="Píldora anticonceptiva" <?php if ($paci['g_ant'] == "Píldora anticonceptiva") echo "selected"; ?>>
                                                Píldora anticonceptiva
                                            </option>
                                            <option value="Inyección anticonceptiva" <?php if ($paci['g_ant'] == "Inyección anticonceptiva") echo "selected"; ?>>
                                                Inyección anticonceptiva
                                            </option>
                                            <option value="Condon" <?php if ($paci['g_ant'] == "Condon") echo "selected"; ?>>
                                                Condon
                                            </option>
                                            <option value="Diafragma" <?php if ($paci['g_ant'] == "Diafragma") echo "selected"; ?>>
                                                Diafragma
                                            </option>
                                            <option value="Condon femenino" <?php if ($paci['g_ant'] == "Condon femenino") echo "selected"; ?>>
                                                Condon femenino
                                            </option>
                                            <option value="Dispositivo intrauterino (DiU)" <?php if ($paci['g_ant'] == "Dispositivo intrauterino (DiU)") echo "selected"; ?>>
                                                Dispositivo intrauterino (DiU)
                                            </option>
                                            <option value="Espermicidas" <?php if ($paci['g_ant'] == "Espermicidas") echo "selected"; ?>>
                                                Espermicidas
                                            </option>
                                            <option value="Vasectomía" <?php if ($paci['g_ant'] == "Vasectomía") echo "selected"; ?>>
                                                Vasectomía
                                            </option>
                                            <option value="Coitus interruptus" <?php if ($paci['g_ant'] == "Coitus interruptus") echo "selected"; ?>>
                                                Coitus interruptus
                                            </option>
                                            <option value="Esponja anticonceptiva" <?php if ($paci['g_ant'] == "Esponja anticonceptiva") echo "selected"; ?>>
                                                Esponja anticonceptiva
                                            </option>
                                            <option value="Esterilización femenina (tubárica)" <?php if ($paci['g_ant'] == "Esterilización femenina (tubárica)") echo "selected"; ?>>
                                                Esterilización femenina (tubárica)
                                            </option>
                                            <option value="Relaciones sexuales sin penetración" <?php if ($paci['g_ant'] == "Relaciones sexuales sin penetración") echo "selected"; ?>>
                                                Relaciones sexuales sin penetración
                                            </option>
                                        </optgroup>
                                    </select></td>
                            </tr>
                            <tr>
                                <td>
                                    <fieldset data-role="controlgroup" data-type="horizontal">
                                        <select name="g_pap" id="g_pap" data-mini="true" class="chekes">
                                            <option value="" selected="selected">PAP:</option>
                                            <option value="Normal" <?php if ($paci['g_pap'] == "Normal") echo "selected"; ?>>
                                                PAP: Normal
                                            </option>
                                            <option value="Anormal" <?php if ($paci['g_pap'] == "Anormal") echo "selected"; ?>>
                                                PAP: Anormal
                                            </option>
                                        </select>
                                    </fieldset>
                                </td>
                                <td colspan="3"><input name="g_pap1" type="text" id="g_pap1" data-mini="true" placeholder="Especifique.." readonly value="<?php echo $paci['g_pap1']; ?>">
                                </td>
                                <td colspan="2"><input type="month" data-clear-btn="false" name="g_pap2" id="g_pap2" value="<?php echo $paci['g_pap2']; ?>"></td>
                                <td><select name="g_dis" id="g_dis" data-mini="true">
                                        <option value="" selected="selected">Dismenorrea:</option>
                                        <option value="Ausente" <?php if ($paci['g_dis'] == "Ausente") echo "selected"; ?>>
                                            Dismenorrea: Ausente
                                        </option>
                                        <option value="Leve" <?php if ($paci['g_dis'] == "Leve") echo "selected"; ?>>
                                            Dismenorrea: Leve
                                        </option>
                                        <option value="Moderada" <?php if ($paci['g_dis'] == "Moderada") echo "selected"; ?>>
                                            Dismenorrea: Moderada
                                        </option>
                                        <option value="Severa" <?php if ($paci['g_dis'] == "Severa") echo "selected"; ?>>
                                            Dismenorrea: Severa
                                        </option>
                                    </select></td>
                            </tr>
                            <tr>
                                <td width="15%" class="peke2">
                                    Gestaciones
                                    <input type="text" name="g_ges" id="g_ges" value="<?php echo $paci['g_ges']; ?>" data-mini="true">
                                </td>
                                <td colspan="3" class="peke2">
                                    <span>P.T
                                        <input type="text" name="g_pt" id="g_pt" value="<?php echo $paci['g_pt']; ?>" data-mini="true" class="numeros">
                                    </span><span>P.P
                                        <input type="text" name="g_pp" id="g_pp" value="<?php echo $paci['g_pp']; ?>" data-mini="true" class="numeros">
                                    </span><span>A
                                        <input type="text" name="g_abo" id="g_abo" value="<?php echo $paci['g_abo']; ?>" data-mini="true" class="numeros">
                                    </span><span>N.V
                                        <input type="text" name="g_nv" id="g_nv" value="<?php echo $paci['g_nv']; ?>" data-mini="true" class="numeros">
                                    </span>
                                </td>
                                <td colspan="2" class="peke">
                                    <select name="g_neo" id="g_neo" data-mini="true">
                                        <option value="">Neonatal:</option>
                                        <option value="Precoz" <?php if ($paci['g_neo'] == "Precoz") echo "selected"; ?>>
                                            Neonatal: Precoz
                                        </option>
                                        <option value="Tardía" <?php if ($paci['g_neo'] == "Tardía") echo "selected"; ?>>
                                            Neonatal: Tardía
                                        </option>
                                    </select>
                                </td>
                                <td width="26%"><label for="g_fur">Último parto</label>
                                    <input type="month" data-clear-btn="false" name="g_fup" id="g_fup" value="<?php echo $paci['g_fup']; ?>">
                                </td>
                            </tr>
                            <tr>
                                <td rowspan="3" class="peke2">
                                    <div data-role="controlgroup" data-mini="true">
                                        <select name="g_abo1" id="g_abo1" data-mini="true">
                                            <option value="">Aborto:</option>
                                            <option value="Espontaneo" <?php if ($paci['g_abo1'] == "Espontaneo") echo "selected"; ?>>
                                                Aborto: Espontaneo
                                            </option>
                                            <option value="Provocado" <?php if ($paci['g_abo1'] == "Provocado") echo "selected"; ?>>
                                                Aborto: Provocado
                                            </option>
                                        </select>

                                        <input type="checkbox" name="g_rn_men" id="g_rn_men" data-mini="true" value=1 <?php if ($paci['g_rn_men'] == 1) echo "checked"; ?>>
                                        <label for="g_rn_men">RN menor 2500gr</label>

                                        <select name="g_rn_mul" id="g_rn_mul" data-mini="true">
                                            <option value="" selected="selected">Múltiples:</option>
                                            <?php for ($i = 1; $i <= 4; $i++) { ?>
                                            <option value=<?php echo $i;
                                                        if ($paci['g_rn_mul'] == $i) echo " selected"; ?>>
                                                Múltiples: <?php echo $i; ?></option>
                                            <?php } ?>
                                        </select>

                                        <div>Edad Gestacional</div>
                                        <input type="text" name="g_abo_ges" id="g_abo_ges" value="<?php echo $paci['g_abo_ges']; ?>" class="numeros">

                                    </div>
                                </td>
                                <td width="16%" class="peke2"><label for="g_vag">P.V</label>
                                    <input type="text" name="g_vag" id="g_vag" value="<?php echo $paci['g_vag']; ?>" data-mini="true" class="numeros">
                                </td>
                                <td class="peke2"><label for="g_ces">P.C</label>
                                    <input type="text" name="g_ces" id="g_ces" value="<?php echo $paci['g_ces']; ?>" data-mini="true" class="numeros">
                                </td>
                                <td class="peke2"><label for="g_nm">N. Muertos</label>
                                    <input type="text" name="g_nm" id="g_nm" value="<?php echo $paci['g_nm']; ?>" data-mini="true" class="numeros">
                                </td>
                                <td colspan="2" class="peke2"><label for="g_viv">Hijos Vivos</label>
                                    <input type="text" name="g_viv" id="g_viv" value="<?php echo $paci['g_viv']; ?>" data-mini="true" class="numeros">
                                </td>
                                <td class="peke">RN con mayor peso(gr)
                                    <input name="g_rn_may" id="g_rn_may" value="<?php echo $paci['g_rn_may']; ?>" size="6" maxlength="6" data-clear-btn="false" class="numeros">
                                </td>
                            </tr>
                            <tr>
                                <td>Complicaciones</td>
                                <td colspan="4"><textarea name="g_abo_com" id="g_abo_com"><?php echo $paci['g_abo_com']; ?></textarea></td>
                                <td><select name="g_agh" id="g_agh" data-mini="true">
                                        <option value="" selected="selected">AgHbs:</option>
                                        <option value="Positivo" <?php if ($paci['g_agh'] == "Positivo") echo "selected"; ?>>
                                            AgHbs: Positivo
                                        </option>
                                        <option value="Negativo" <?php if ($paci['g_agh'] == "Negativo") echo "selected"; ?>>
                                            AgHbs: Negativo
                                        </option>
                                    </select></td>
                            </tr>
                            <tr>
                                <td>Observaciones</td>
                                <td colspan="4"><textarea name="g_obs" id="g_obs"><?php echo $paci['g_obs']; ?></textarea>
                                </td>
                                <td>Histero.<input type="text" name="g_his" id="g_his" value="<?php echo $paci['g_his']; ?>" data-mini="true"></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <?php
                    if( $user['role'] == 1 ) { ?>
                <div data-role="collapsible" id="Perfi">
                    <h3>Resultados de Análisis Clínicos</h3>
                    <?php
                            if( false ) { ?>
                    <hr>
                    <p>
                        <a href="e_ante_p_sero.php?dni=mujer<?php echo "&ip=" . $paci['dni'] . "&id="; ?>" rel="external" class="ui-btn ui-btn-inline ui-mini" style="float:left">Agregar<br>Serologías</a>
                    <div class="scroll_h">
                        <table width="85%" style="margin:0 auto;font-size:small;text-align:center;" class="ui-responsive table-stroke">
                            <thead>
                                <tr>
                                    <th width="5%">Fecha</th>
                                    <th>Hepatitis B<br>HBs Ag</th>
                                    <th>Hepatitis C <br>HCV Ac</th>
                                    <th>HIV</th>
                                    <th width="14%">RPR</th>
                                    <th>Rubeola<br>IgG</th>
                                    <th width="14%">Toxoplasma<br>IgG</th>
                                    <th width="14%">Clamidia<br>IgG</th>
                                    <th width="14%">clamidia<br>IgM</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($sero = $Sero->fetch(PDO::FETCH_ASSOC)) { ?>
                                <tr>
                                    <td valign="top">
                                        <?php if ($sero['lab'] <> "") echo date("d-m-Y", strtotime($sero['fec'])) . ' (' . $sero['lab'] . ')'; else { ?>
                                        <a href="e_ante_p_sero.php?dni=mujer<?php echo "&ip=" . $paci['dni'] . "&id=" . $sero['fec']; ?>" rel="external"><?php echo date("d-m-Y", strtotime($sero['fec'])); ?></a><?php } ?><?php if (file_exists("analisis/sero_" . $paci['dni'] . "_" . $sero['fec'] . ".pdf")) echo "<br><a href='analisis/hsero_" . $paci['dni'] . "_" . $sero['fec'] . ".pdf' target='new'>Descargar</a>"; ?>
                                    </td>
                                    <td valign="top" <?php if ($sero['hbs'] == 1) echo 'class="color"'; ?>><?php if ($sero['hbs'] == 1) echo "Positivo";
                                                    if ($sero['hbs'] == 2) echo "Negativo";
                                                    if ($sero['hbs'] == 3) echo "En proceso";
                                                    if ($sero['hbs'] == 4) echo "Indeterminado";
                                                    if ($sero['hbs'] == 0) echo "No Realizado"; ?></td>
                                    <td valign="top" <?php if ($sero['hcv'] == 1) echo 'class="color"'; ?>><?php if ($sero['hcv'] == 1) echo "Positivo";
                                                    if ($sero['hcv'] == 2) echo "Negativo";
                                                    if ($sero['hcv'] == 3) echo "En proceso";
                                                    if ($sero['hcv'] == 4) echo "Indeterminado";
                                                    if ($sero['hcv'] == 0) echo "No Realizado"; ?></td>
                                    <td valign="top" <?php if ($sero['hiv'] == 1) echo 'class="color"'; ?>><?php if ($sero['hiv'] == 1) echo "Positivo";
                                                    if ($sero['hiv'] == 2) echo "Negativo";
                                                    if ($sero['hiv'] == 3) echo "En proceso";
                                                    if ($sero['hiv'] == 4) echo "Indeterminado";
                                                    if ($sero['hiv'] == 0) echo "No Realizado"; ?></td>
                                    <td valign="top" <?php if ($sero['rpr'] == 1) echo 'class="color"'; ?>><?php if ($sero['rpr'] == 1) echo "Positivo";
                                                    if ($sero['rpr'] == 2) echo "Negativo";
                                                    if ($sero['rpr'] == 3) echo "En proceso";
                                                    if ($sero['rpr'] == 4) echo "Indeterminado";
                                                    if ($sero['rpr'] == 0) echo "No Realizado"; ?></td>
                                    <td valign="top" <?php if ($sero['rub'] == 1) echo 'class="color"'; ?>><?php if ($sero['rub'] == 1) echo "Positivo";
                                                    if ($sero['rub'] == 2) echo "Negativo";
                                                    if ($sero['rub'] == 3) echo "En proceso";
                                                    if ($sero['rub'] == 4) echo "Indeterminado";
                                                    if ($sero['rub'] == 0) echo "No Realizado"; ?></td>
                                    <td valign="top" <?php if ($sero['tox'] == 1) echo 'class="color"'; ?>><?php if ($sero['tox'] == 1) echo "Positivo";
                                                    if ($sero['tox'] == 2) echo "Negativo";
                                                    if ($sero['tox'] == 3) echo "En proceso";
                                                    if ($sero['tox'] == 4) echo "Indeterminado";
                                                    if ($sero['tox'] == 0) echo "No Realizado"; ?></td>
                                    <td valign="top" <?php if ($sero['cla_g'] == 1) echo 'class="color"'; ?>><?php if ($sero['cla_g'] == 1) echo "Positivo";
                                                    if ($sero['cla_g'] == 2) echo "Negativo";
                                                    if ($sero['cla_g'] == 3) echo "En proceso";
                                                    if ($sero['cla_g'] == 4) echo "Indeterminado";
                                                    if ($sero['cla_g'] == 0) echo "No Realizado"; ?></td>
                                    <td valign="top" <?php if ($sero['cla_m'] == 1) echo 'class="color"'; ?>><?php if ($sero['cla_m'] == 1) echo "Positivo";
                                                    if ($sero['cla_m'] == 2) echo "Negativo";
                                                    if ($sero['cla_m'] == 3) echo "En proceso";
                                                    if ($sero['cla_m'] == 4) echo "Indeterminado";
                                                    if ($sero['cla_m'] == 0) echo "No Realizado"; ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <?php if ($Sero->rowCount() < 1) echo '<p><h3>¡ No hay datos aún !</h3></p>'; ?>
                    </div>
                    </p>
                    <?php } ?>

                    <label for="fe_exa">Otros Exámenes:</label>
                    <textarea name="fe_exa" id="fe_exa" data-mini="true"><?php echo $paci['fe_exa']; ?></textarea>
                </div>
                <?php
                        }
                    ?>
                <div data-role="collapsible" id="Cirug">
                    <h3>Cirugías ginecológicas y/o Pélvicas</h3>

                    <a href="e_ante_cirug.php?dni=<?php echo $paci['dni'] . "&id="; ?>" rel="external" class="ui-btn ui-btn-inline ui-mini" style="float:left">Agregar</a>
                    <div class="scroll_h">

                        <table width="85%" style="margin:0 auto;font-size:small;" class="ui-responsive table-stroke">
                            <thead>
                                <tr>

                                    <th width="11%" align="left">Fecha</th>
                                    <th width="31%" align="left">Procedimiento</th>
                                    <th width="13%" align="left">Médico</th>
                                    <th width="33%" align="left">Diagnóstico</th>
                                    <th width="11%" align="left">Lugar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($cirug = $a_cirug->fetch(PDO::FETCH_ASSOC)) { ?>
                                <tr>
                                    <td><a href="e_ante_cirug.php?dni=<?php echo $paci['dni'] . "&id=" . $cirug['id']; ?>" rel="external"><?php echo date("d-m-Y", strtotime($cirug['fec'])); ?></a><?php if (file_exists("analisis/cirug_" . $cirug['id'] . ".pdf")) echo "<br><a href='archivos_hcpacientes.php?idArchivo=cirug_" . $cirug['id'] . "' target='new'>Descargar</a>"; ?>
                                    </td>
                                    <td><?php echo $cirug['pro']; ?></td>
                                    <td><?php echo $cirug['med']; ?></td>
                                    <td><?php echo $cirug['dia']; ?></td>
                                    <td><?php echo $cirug['lug']; ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <?php if ($a_cirug->rowCount() < 1) echo '<p><h3>¡ No hay datos aún !</h3></p>'; ?>

                    </div>
                </div>

                <div data-role="collapsible" id="Trata">
                    <h3>Tratamientos de reproducción asistida anteriores (NO realizados en INMATER)</h3>
                    <a href="e_ante_trata.php?dni=<?php echo $paci['dni'] . "&id="; ?>" rel="external" class="ui-btn ui-btn-inline ui-mini">Agregar</a>
                    <div class="scroll_h">
                        <table width="85%" style="margin:0 auto;font-size:small;" class="ui-responsive table-stroke">
                            <thead>
                                <tr>

                                    <th width="5%" align="left">Fecha</th>
                                    <th width="18%" align="left">Procedimiento</th>
                                    <th width="10%" align="left">Médico</th>
                                    <th width="16%" align="left">Medicamentos</th>
                                    <th width="3%" align="left">Nº Folículos</th>
                                    <th width="7%" align="left">Nº Ovocitos<br>aspirados</th>
                                    <th width="8%" align="left">Nº Embriones<br>transferidos</th>
                                    <th width="8%" align="left">Día de<br>transferencia</th>
                                    <th width="11%" align="left">Criopreservados</th>
                                    <th width="14%" align="left">Resultado</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($trata = $a_trata->fetch(PDO::FETCH_ASSOC)) { ?>
                                <tr>
                                    <td><a href="e_ante_trata.php?dni=<?php echo $paci['dni'] . "&id=" . $trata['id']; ?>" rel="external"><?php echo date("d-m-Y", strtotime($trata['fec'])); ?></a>
                                    </td>
                                    <td><?php echo $trata['pro'];
                                                if ($trata['tras'] == 1) echo ' <b>(TRASLADO EN PROCESO)</b>';
                                                if ($trata['tras'] == 2) echo ' <b>(TRASLADO FINALIZADO)</b>'; ?></td>
                                    <td><?php echo $trata['med']; ?></td>
                                    <td><?php echo $trata['medica']; ?></td>
                                    <td><?php echo $trata['fol']; ?></td>
                                    <td><?php echo $trata['ovo']; ?></td>
                                    <td><?php echo $trata['emb']; ?></td>
                                    <td><?php echo $trata['dia']; ?></td>
                                    <td><?php echo $trata['cri']; ?></td>
                                    <td><?php echo $trata['res']; ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <?php if ($a_trata->rowCount() < 1) echo '<p><h3>¡ No hay datos aún !</h3></p>'; ?>

                    </div>
                </div>

                <?php
                        if( false ) {
                    ?>
                <div data-role="collapsible" id="Legal">
                    <h3>Legal <span id="ultimo"></span></h3>
                    <?php if ($rLegal->rowCount() > 0) { ?>
                    <table style="font-size:small;" data-role="table" class="ui-responsive table-stroke">
                        <thead>
                            <tr>
                                <th>TIPO</th>
                                <th>OBSERVACION</th>
                                <th>INFORME</th>
                                <th>FECHA</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($legal = $rLegal->fetch(PDO::FETCH_ASSOC)) { ?>
                            <tr>
                                <th><?php echo $legal['a_exa']; ?></th>
                                <td><?php echo $legal['a_obs']; ?></td>
                                <td><?php $a_sta = '';
                                                if ($legal['a_sta'] == 1) $a_sta = ' (APTO)';
                                                if ($legal['a_sta'] == 2) $a_sta = ' (OBSERVADO)';
                                                if ($legal['a_sta'] == 3) $a_sta = ' (NO APTO)'; ?>
                                    <a href='<?php echo "archivos_hcpacientes.php?idLegal=" . $legal['id'] . "_" . $legal['a_dni'] . ".pdf"; ?>' target="new">Ver/Descargar</a>
                                    <?php echo $a_sta; ?>
                                </td>
                                <td><?php echo date("d-m-Y", strtotime($legal['a_fec'])); ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <script>
                    $(function() {
                        $("#ultimo").html("<?php echo $a_sta; ?>");
                    });
                    </script>
                    <?php } else echo "<h5>No hay Documentos</h5>"; ?>
                </div>
                <?php
                        }
                    ?>
            </div>
            <?php
                    if ($user['role'] == 1 || $user['role'] == 15) { ?>
            <input type="Submit" value="GUARDAR DATOS" name="boton_datos" data-icon="check" data-iconpos="left" data-mini="true" class="show-page-loading-msg" data-textonly="false" data-textvisible="true" data-msgtext="Agregando datos.." data-theme="b" data-inline="true" />
            <?php } ?>
        </div>
        </div>
        <?php } ?>
    </form>
    <?php 
        $refresh = 0;
        if($refresh == 0){?>
    <!--  -->
    <script>
    $(document).ready(function() {

        var nombre_modulo = "historias_clinicas";
        var ruta = "perfil_medico/busqueda_paciente/paciente.php";
        var tipo_operacion = "consulta";
        var login = $('#login').val();
        var key = $('#key').val();
        var clave = 'paciente';
        var valor = $('#paciente').val();
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
                console.log(result);
            }
        });

        $('.numeros').keyup(function() {

            var $th = $(this);
            $th.val($th.val().replace(/[^0-9]/g, function(str) {
                //$('#cod small').replaceWith('<small>Error: Porfavor ingrese solo letras y números</small>');

                return '';
            }));

            //$('#cod small').replaceWith('<small>Aqui ingrese siglas o un nombre corto de letras y números</small>');
        });

        $('.chekes').change(function() {

            var temp = '#' + $(this).attr("id") + '1';

            if ($(this).prop('checked') || $(this).val() == "Medicamentada" || $(this).val() ==
                "Otra" || $(this).val() == "Anormal") {

                $(temp).prop('readonly', false);
                //$(temp).placeholder=$(this).val();

            } else {
                $(temp).prop('readonly', true);
                $(temp).val('');
            }

        });

        $(".med_insert").change(function() {
            var med = $(this).attr("title");
            var str = $('#' + med).val();
            var items = $(this).val();

            var n = str.indexOf(items);

            if (n == -1) { // no agrega duplicados
                $('#' + med).val(items + ", " + str);
                if (items == "Borrar") $('#' + med).val("");
                $('#' + med).textinput('refresh');
            }

            $(this).prop('selectedIndex', 0);
            $(this).selectmenu("refresh", true);
        });

        $("#depa").change(function() {

            $("#depa option:selected").each(function() {
                var depa = $(this).val();
                //$(".varillas").remove();
                $.post("le_tanque.php", {
                    depa: depa
                }, function(data) {
                    $("#prov").html(data);
                    $("#prov").selectmenu("refresh");
                });
            });
        });

        $("#prov").change(function() {

            $("#prov option:selected").each(function() {
                var prov = $(this).val();
                //$(".varillas").remove();
                $.post("le_tanque.php", {
                    prov: prov
                }, function(data) {
                    $("#dist").html(data);
                    $("#dist").selectmenu("refresh");
                });
            });
        });

        <?php if (isset($_GET['pop']) && !empty($_GET['pop'])): ?>
        $(document).ready(function() {

            var x = "<?php echo $_GET['pop']; ?>";
            $("#" + x).collapsible({
                collapsed: false
            });

        });
        <?php endif ?>

        <?php if($must_update): ?>
        $('a').on('click', function(e) {
            const id = $(this).attr('id');
            if (id == 'b_indice' || id == 'lista-pacientes' || id == 'salir') return;

            if (confirm("Desea continuar sin guardar la sede?")) {
                return;
            }
            e.preventDefault();
            e.stopPropagation();
            // alert('Debe registrar la sede y guardar los datos');
        });
        <?php endif ?>
    });
    $(document).on("click", ".show-page-loading-msg", function() {
        if (document.getElementById("don").value == "") {
            alert("Debe llenar el campo 'Tipo de Cliente'");
            return false;
        }
        if (document.getElementById("medios_comunicacion_id").value == "") {
            alert("Debe llenar el campo 'Programa'");
            return false;
        }
        if (document.getElementById("nom").value == "") {
            alert("Debe llenar el campo 'Nombre'");
            return false;
        }
        if (document.getElementById("ape").value == "") {
            alert("Debe llenar el campo 'Apellidos'");
            return false;
        }
        if (document.getElementById("mai").value == "") {
            alert("Debe llenar el campo 'E-Mail'");
            return false;
        }
        if (document.getElementById("m_tratante").value == "") {
            alert("Debe llenar el campo 'Medico Tratante'");
            return false;
        }
        if (document.getElementById("dir").value == "") {
            alert("Debe llenar el campo 'Direccion'");
            return false;
        }

        if (document.getElementById("rem").value == "") {
            alert("Debe llenar el campo 'Referido Por'");
            return false;
        }
        if (document.getElementById("tcel").value == "") {
            alert("Debe llenar el campo 'Celular'");
            return false;
        }
        if (document.getElementById("nac").value == "") {
            alert("Debe llenar el campo 'Nacionalidad'");
            return false;
        }
        if (document.getElementById("fnac").value == "") {
            alert("Debe llenar el campo: Fecha de Nacimiento (Datos Generales)");
            return false;
        }
        if ($('#m_inf').prop('checked')) {
            if (document.getElementById("m_inf1").value == "") {
                alert("Debe especificar la Infección");
                return false;
            }
        }
        if (document.getElementById("m_ale").value == "Medicamentada" || document.getElementById("m_ale")
            .value == "Otra") {
            if (document.getElementById("m_ale1").value == "") {
                alert("Debe especificar la alergia");
                return false;
            }
        }

        var nombre_modulo = "historias_clinicas";
        var ruta = "perfil_medico/busqueda_paciente/paciente.php";
        var tipo_operacion = "actualizacion";
        var login = $('#login').val();
        var key = $('#key').val();
        var clave = 'paciente';
        var valor = $('#paciente').val();
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
            // processData: false,  // tell jQuery not to process the data
            // contentType: false,   // tell jQuery not to set contentType
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
    }).on("click", ".hide-page-loading-msg", function() {
        $.mobile.loading("hide");
    });

    $(function() {
        $("#alerta").prependTo(".ui-content");
        $('#alerta').delay(3000).fadeOut('slow');
    });

    $("#validarDni").click(function() {
        dni = $('#dni').val()
        tipo = $('#tip').val()
        validarDniValue = $('#validarDniValue').val()
        tipDoc = 0
        switch (tipo) {
            case 'DNI':
                tipDoc = 1;
                break;

            case 'PAS':
                tipDoc = 4;
                break;

            case 'CEX':
                tipDoc = 2;
                break;
        }
        valDni = 'dni'
        nom = 'nom'
        ape = 'ape'
        fnac = 'fnac'
        selectTipDoc = 'tip'
        campDni = 'validarDni'
        msgVal = 'msgValidacion'
        valueDni = 'validarDniValue'
        sistema = window.location.pathname
        sistema = sistema.slice(1)
        usuario = '<?php echo $login;?>';

        if (validarDniValue == '1') {
            validarDocumento(valDni, nom, ape, fnac, dni, tipDoc, selectTipDoc, campDni, msgVal, valueDni, sistema, usuario)
        } else if (validarDniValue == '2') {
            habilitarCampos(valDni, nom, ape, fnac, selectTipDoc, campDni, msgVal, valueDni)
        }

        $('#dni').prop('readonly', true)

    })
    </script>

    <script src="js/e_paci.js?v=200117.6" crossorigin="anonymous"></script>
    <?php }; ?>
    <?php include($_SERVER["DOCUMENT_ROOT"] . "/_componentes/n_paci/validacion_reniec.php"); ?>

</body>

</html>