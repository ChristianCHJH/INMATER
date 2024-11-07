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
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
    <style>
    .scroll_h {
        overflow-x: scroll;
        overflow-y: hidden;
        white-space: nowrap;
    }

    .peke2 .ui-input-text {
        width: 50px !important;
    }

    .controlgroup-textinput {
        padding-top: .10em;
        padding-bottom: .10em;
    }

    .bg_C {
        background-color: rgba(124, 203, 231, 0.30);
    }

    .bg_N {
        background-color: rgba(240, 131, 132, 0.30);
    }

    .bg_T {
        background-color: rgba(169, 235, 143, 1.30);
    }

    .bg_D {
        background-color: rgba(236, 215, 107, 1.30);
    }

    .enlinea div {
        display: inline-block;
        vertical-align: middle;
    }

    .tran .ui-btn {
        background-color: rgba(169, 235, 143, 1.30);
    }

    .libro .ui-input-text {
        width: 70px !important;
        background-color: rgba(124, 203, 231, 0.30);
    }
    </style>
    <script>
    $(document).ready(function() {
        $('#form1').submit(function() {
            var crio = false;

            for (var i = 1; i <= $('#total_embriones').val(); i++) {
                if ($('#' + i).val() == "C") {
                    crio = true;
                }
            }

            if ($('#book').val() == 0 && crio) {
                alert("Debe ingresar los datos de Cuaderno");
                return false;
            }

            if ($('#hoja').val() == 0 && crio) {
                alert("Debe ingresar los datos de Hoja.");
                return false;
            }

            if ($('#emb3c').val() == "" && crio) {
                alert("Debe ingresar el Embriologo de Crio.");
                return false;
            }

            if ($("#Tra").val() == 1) {
                if ($("#T_t_cat").val() == "") {
                    alert("Debe ingresar los datos de TRANSFERENCIA");
                    return false;
                } else $("#cargador").popup("open", {
                    positionTo: "window"
                });
                return true;
            } else {
                $("#cargador").popup("open", {
                    positionTo: "window"
                });
                return true;
            }
        });

        // No close unsaved windows
        var unsaved = false;
        $(":input").change(function() {

            unsaved = true;

        });

        $(window).on('beforeunload', function() {
            if (unsaved) {
                return 'UD. HA REALIZADO CAMBIOS';
            }
        });

        // Form Submit
        $(document).on("submit", "form", function(event) {
            // disable unload warning
            $(window).off('beforeunload');
        });

        $(".med_insert, .med_insert1").change(function() {
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
        $('.tran').hide();

        var t = 0;
        $(".f_cic").change(function() {
            var med = $(this).attr("title");
            var items = $(this).val();
            var id = $(this).attr("id");
            $('#fila' + id).removeClass();
            if (items == "T") $('#fila' + id).addClass('bg_T');
            if (items == "D") $('#fila' + id).addClass('bg_D');
            if (items == "N") $('#fila' + id).addClass('bg_N');

            if (items == "C") {
                $('#crio' + med).show();
                $('#fila' + id).addClass('bg_C');
            } else {
                $('#crio' + med).hide();
                $('#T' + med + ',#C' + med + ',#G' + med + ',#P' + med).val('');
            }

            if (items == "T" && t >= 0) t++;
            if (items != "T" && t > 0) t--;
            if (t == 0) {
                $('.tran').hide();
                $('#Tra').val(0);
            } else {
                $('.tran').show();
                $('#Tra').val(1);
            }
        });

        $(".fotox").change(function() {
            var idfoto = $(this).attr("name");
            if ($(this).val() == '') {
                $("#l" + idfoto).removeClass("bg_N");
            } else {
                $("#l" + idfoto).addClass("bg_N");
            }
        });
    });
    </script>
</head>

<body>

    <?php
$mExtras = $db->prepare("select * from man_extras where estado = 1");
$mExtras->execute();
$mNotas = $db->prepare("select * from man_notas where estado = 1");
$mNotas->execute();

if (isset($_POST['n_ovo']) and $_POST['guardar'] == "GUARDAR DATOS") {
    $cancela = 0;
    $c = $_POST['c'];
    $c2 = 0;
    $fin = 0;

    if ($c > 0) {
        if ($_POST['don'] == 'D') $don = 1; else $don = 0;

        for ($i = 1; $i <= $c; $i++) {

            if ($_POST['anu' . $i] == 0 or $_POST['anu' . $i] >= 4) {
                $c2++;
                if ($_POST['f_cic' . $i] == "O") {
                    if ($_POST['anu' . $i] == 4) $anu = 0; else $anu = $_POST['anu' . $i];
                } else {
                    $anu = 4;
                    $cancela++;
                }

                lab_updateAspi_d3($_POST['pro'], $i, $anu, $_POST['cel' . $i], $_POST['fra' . $i], $_POST['sim' . $i], $_POST['c_bio' . $i], $_POST['f_cic' . $i], $_POST['obs' . $i], $_POST['T' . $i], $_POST['C' . $i], $_POST['G' . $i], $_POST['P' . $i], $_POST['col' . $i], $don, $_FILES['i' . $i]);
            }
        }
    }

    if ($_POST['dias'] <= 4)
        lab_updateAspi_sta($_POST['pro'], 'Dia 4', 4, $_POST['hra3'], $_POST['emb3'], $_POST['hra3c'], $_POST['emb3c']);

    if ($_POST['Tra'] == 1)
        lab_updateAspi_sta_T($_POST['Tra_id'], $_POST['pro'], 3, $_POST['T_t_cat'], $_POST['T_s_gui'], $_POST['T_s_cat'], $_POST['T_endo'], $_POST['T_inte'], $_POST['T_eco'], $_POST['T_med'], $_POST['T_emb'], $_POST['T_obs'], $login);
    if ($cancela == $c2) {
        if ($_POST['dias'] > 4)
            lab_updateAspi_sta($_POST['pro'], 'Dia 4', 4, $_POST['hra3'], $_POST['emb3'], $_POST['hra3c'], $_POST['emb3c']);
        lab_updateAspi_fin($_POST['pro']);
        $fin=1;
    }

    lab_updateRepro2([
        "rep" => $_POST['rep'],
        "p_extras" => $_POST['p_extras'],
        "p_notas" => $_POST['p_notas'],
        "obs" => $_POST['obs'],
        "obs_med" => $_POST['obs_med'],
        "book" => $_POST['book'],
        "hoja" => $_POST['hoja'],
        "fin" => $fin,
        "iduserupdate" => $login
    ]);
}

if ($_GET['id'] <> "") {
    $id = $_GET['id'];
    $rPaci = $db->prepare("SELECT lab_aspira.*,hc_reprod.id,hc_reprod.eda,hc_reprod.p_cic,hc_reprod.p_fiv,hc_reprod.p_icsi,hc_reprod.p_od,hc_reprod.p_don,hc_reprod.p_cri,hc_reprod.p_extras,hc_reprod.p_notas,hc_reprod.pago_extras,hc_reprod.pago_notas,hc_reprod.p_dni,hc_reprod.med FROM lab_aspira LEFT JOIN hc_reprod ON hc_reprod.id=lab_aspira.rep WHERE hc_reprod.estado = true and lab_aspira.estado is true and lab_aspira.pro=?");
    $rPaci->execute(array($id));
    $paci = $rPaci->fetch(PDO::FETCH_ASSOC);

    $rMujer = $db->prepare("SELECT nom,ape,don FROM hc_paciente WHERE dni=?");
    $rMujer->execute(array($paci['dni']));
    $mujer = $rMujer->fetch(PDO::FETCH_ASSOC);

    $rHombre = $db->prepare("SELECT p_nom,p_ape FROM hc_pareja WHERE p_dni=?");
    $rHombre->execute(array($paci['p_dni']));
    $hombre = $rHombre->fetch(PDO::FETCH_ASSOC);
    if ($paci['p_dni'] == "") $pareja = "SOLTERA"; else $pareja = $hombre['p_ape'] . " " . $hombre['p_nom'];

    $campos = "ovo,anu,d0est,d3cel,d3fra,d3sim,d3c_bio,d3f_cic,obs,t,c,g,p,col,des,don";
    $rAspi = $db->prepare("SELECT " . $campos . " FROM lab_aspira_dias WHERE pro=? and estado is true order by ovo asc");
    $rAspi->execute(array($id));

		if ($paci['f_fin'] <> "1899-12-30") {
			$rEmb = $db->prepare("SELECT id,nom FROM lab_user;");
			$rEmb->execute();
			$rEmb->setFetchMode(PDO::FETCH_ASSOC);
			$rows = $rEmb->fetchAll();
		} else {
			$rEmb = $db->prepare("SELECT id,nom FROM lab_user WHERE sta=0");
			$rEmb->execute();
			$rEmb->setFetchMode(PDO::FETCH_ASSOC);
			$rows = $rEmb->fetchAll();
		}

    $rTran = $db->prepare("SELECT * FROM lab_aspira_t WHERE pro=? and estado is true");
    $rTran->execute(array($id));
    $tra = $rTran->fetch(PDO::FETCH_ASSOC);

    $rMed = $db->prepare("SELECT userx FROM usuario WHERE role=1");
    $rMed->execute();
    ?>
    <?php if ($rTran->rowCount() > 0) { ?>
    <script>
    $(document).ready(function() {
        $('.tran').show();
        $('#Tra').val(1);
    });
    </script>
    <?php } ?>

    <?php if ($paci['tip'] == 'T') { // Traslado ?>
    <script>
    $(document).ready(function() {
        $('.no_traslado').hide();
        $(".f_cic option[value='T']").remove();
        $(".f_cic option[value='O']").remove();
    });
    </script>
    <?php } ?>
    <form action="" method="post" enctype="multipart/form-data" data-ajax="false" id="form1">
        <div data-role="page" class="ui-responsive-panel" id="le_aspi3">
            <?php require ('_includes/menu_laboratorio.php'); ?>
            <div data-role="header" data-position="fixed">
                <a href="#indice_paci" data-icon="bars" id="b_indice" class="ui-icon-alt" data-theme="a">MENU
                    <small>> Dia 3</small>
                </a>
                <h2><?php if ($paci['tip'] == 'T') echo "(" . $paci['pro'] . ")"; else echo "(" . $paci['tip'] . "-" . $paci['pro'] . "-" . $paci['vec'] . ")";
                    echo " Dia 3"; if ($paci['fec3'] <> '1899-12-30') echo ': <small>'.date("d-m-Y", strtotime($paci['fec3'])).'</small>'; ?>
                </h2>
                <a href="salir.php"
                    class="ui-btn-right ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-right ui-icon-power"
                    rel="external"> Salir</a>
                <div style="background-color:#d7e5e5; width:100%; font-size:13px; text-align:center;">
                    <?php echo $mujer['ape'] . " " . $mujer['nom'] . " (" . $paci['eda'] . ") / " . $pareja . " (Medico: " . $paci['med'] . ")" ?>
                </div>
            </div>
            <div class="ui-content" role="main">

                <input type="hidden" name="n_ovo" value="<?php echo $paci['n_ovo']; ?>">
                <input type="hidden" name="pro" id="pro" value="<?php echo $paci['pro']; ?>">
                <!--id="pro" se usa en el javascript-->
                <input type="hidden" name="rep" value="<?php echo $paci['rep']; ?>">
                <input type="hidden" name="dni" value="<?php echo $paci['dni']; ?>">
                <input type="hidden" name="don" value="<?php echo $mujer['don']; ?>">
                <input type="hidden" name="dias" value="<?php echo $paci['dias']; ?>">
                <input type="hidden" name="Tra" id="Tra" value="0">
                <!--Tra="Tra" se usa en el javascript-->
                <div class="scroll_h" style="background-color:rgba(189,213,211,1.00)">
                    <table width="100%" align="center" style="margin: 0 auto;font-size:small;">
                        <tr>
                            <td width="11%">PROCEDIMIENTOS:</td>
                            <td width="15%">
                                <div class="enlinea">
                                    <?php
                                    if ($paci['p_cic'] == 1) {echo "(Ciclo Natural) ";}
                                    if ($paci['p_fiv'] == 1) {echo "(FIV) ";}
                                    if ($paci['p_icsi'] == 1) {echo "(" . $_ENV["VAR_ICSI"] . ") ";}
                                    if ($paci['p_od'] <> '') {echo "(OD Fresco) ";}
                                    if ($paci['p_don'] == 1) {echo "(Donación Fresco) ";}
                                    if ($paci['p_cri'] == 1) {echo "(Crio Ovos)";}
                                    ?>
                                </div>
                            </td>
                            <td>CONCLUSIONES:</td>
                            <td><textarea name="obs" id="obs2" data-mini="true"><?php echo $paci['obs']; ?></textarea>
                            </td>
                            <td width="7%">Embriologo
                                <select name="emb3" required id="emb3" data-mini="true">
                                    <option value="">Seleccionar</option>
                                    <?php foreach ($rows as $embrio) { ?>
                                    <option value=<?php echo $embrio['id'];
                                        if ($paci['emb3'] == $embrio['id']) {echo " selected";} ?>>
                                        <?php echo $embrio['nom']; ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td width="12%">Hora
                                <input name="hra3" type="time" data-mini="true" required id="hra3"
                                    value="<?php if ($paci['hra3'] <> "") {echo $paci['hra3'];} else {echo date("H:i");} ?>">
                            </td>
                        </tr>
                        <tr>
                            <td width="15%" class="no_traslado">
                                <select name="select" class="med_insert" title="p_extras" data-mini="true">
                                    <option value="" selected>EXTRAS:</option>
                                    <option value="Borrar">- Borrar Datos -</option>
                                    <?php
                                        while ($me = $mExtras->fetch(PDO::FETCH_ASSOC)) {
                                            print("<option value='".mb_strtoupper($me["nombre"])."'>".mb_strtoupper($me["nombre"])."</option>");
                                        }
                                    ?>
                                </select>
                                <?php if ($paci['p_extras'] <> "") echo "<small>Extras del Médico: " . $paci['p_extras'] . "</small>"; ?>
                                <textarea name="p_extras" readonly id="p_extras"
                                    data-mini="true"><?php echo $paci['pago_extras']; ?></textarea>
                            </td>
                            <td width="15%" class="no_traslado">
                                <select name="select" class="med_insert1" title="p_notas" data-mini="true">
                                    <option value="" selected>NOTAS:</option>
                                    <option value="Borrar">- Borrar Datos -</option>
                                    <?php
                                        while ($mo = $mNotas->fetch(PDO::FETCH_ASSOC)) {
                                            print("<option value='".mb_strtoupper($mo["nombre"])."'>".mb_strtoupper($mo["nombre"])."</option>");
                                        }
                                    ?>
                                </select>
                                <?php if ($paci['p_notas'] <> "") echo "<small>Notas del Médico: " . $paci['p_notas'] . "</small>"; ?>
                                <textarea name="p_notas" readonly id="p_notas"
                                    data-mini="true"><?php echo $paci['pago_notas']; ?></textarea>
                            </td>
                            <td width="10%">
                                <p>OBSERVACIONES:</p>
                            </td>
                            <td width="45%">
                                <textarea name="obs_med" id="obs_med"
                                    data-mini="true"><?php echo $paci['obs_med']; ?></textarea>
                            </td>
                            <td width="5%">
                                Embriologo Crio
                                <select name="emb3c" id="emb3c" data-mini="true">
                                    <option value="">Seleccionar</option>
                                    <?php foreach ($rows as $embrio) { ?>
                                    <option value=<?php echo $embrio['id'];
                                        if ($paci['emb3c'] == $embrio['id']) echo " selected"; ?>>
                                        <?php echo $embrio['nom']; ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td width="10%" class="peke">
                                Hora Crio
                                <input name="hra3c" type="time" data-mini="true" id="hra3c"
                                    value="<?php if ($paci['hra3c'] <> "") echo $paci['hra3c']; else echo date("H:i"); ?>">
                            </td>
                        </tr>
                    </table>
                </div>
                <?php if ($rAspi->rowCount() > 0) { ?>
                <div data-role="collapsible" data-mini="true" class="tran">
                    <h1>TRANSFERENCIA</h1>
                    <input type="hidden" name="Tra_id" value="<?php echo $tra['pro']; ?>">
                    <table width="100%" align="center" style="margin: 0 auto; font-size: small;">
                        <tr>
                            <td width="19%">
                                Tipo de cateter
                                <select name="T_t_cat" id="T_t_cat" data-mini="true">
                                    <option value="" selected>Seleccionar</option>
                                    <?php
																				$stmt_cateter = $db->prepare("SELECT id, nombre from man_cateter where estado=1;");
																				$stmt_cateter->execute();
																				while ($item = $stmt_cateter->fetch(PDO::FETCH_ASSOC)) {
																					print('<option value="'.$item["id"].'" '. ($item['id'] == $tra['t_cat'] ? "selected" : "") .'>' . mb_strtoupper($item['nombre']) . '</option>');
																				} ?>
                                </select>
                            </td>
                            <td width="7%">Sangre en guia
                                <select name="T_s_gui" id="T_s_gui" data-mini="true">
                                    <option value=0 <?php if ($tra['s_gui'] == 0) {echo "selected";} ?>>0</option>
                                    <option value=1 <?php if ($tra['s_gui'] == 1) {echo "selected";} ?>>1</option>
                                    <option value=2 <?php if ($tra['s_gui'] == 2) {echo "selected";} ?>>2</option>
                                    <option value=3 <?php if ($tra['s_gui'] == 3) {echo "selected";} ?>>3</option>
                                </select>
                            </td>
                            <td width="10%">Sangre en cateter
                                <select name="T_s_cat" id="T_s_cat" data-mini="true">
                                    <option value=0 <?php if ($tra['s_cat'] == 0) {echo "selected";} ?>>0</option>
                                    <option value=1 <?php if ($tra['s_cat'] == 1) {echo "selected";} ?>>1</option>
                                    <option value=2 <?php if ($tra['s_cat'] == 2) {echo "selected";} ?>>2</option>
                                    <option value=3 <?php if ($tra['s_cat'] == 3) {echo "selected";} ?>>3</option>
                                </select>
                            </td>
                            <td width="5%">Endometrio
                                <select name="T_endo" id="T_endo" data-mini="true">
                                    <option value=0 <?php if ($tra['endo'] == 0) {echo "selected";} ?>>Seleccionar
                                    </option>
                                    <option value=5 <?php if ($tra['endo'] == 5) {echo "selected";} ?>>Menor/igual a 5mm
                                    </option>
                                    <option value=6 <?php if ($tra['endo'] == 6) {echo "selected";} ?>>6mm</option>
                                    <option value=7 <?php if ($tra['endo'] == 7) {echo "selected";} ?>>7mm</option>
                                    <option value=8 <?php if ($tra['endo'] == 8) {echo "selected";} ?>>8mm</option>
                                    <option value=9 <?php if ($tra['endo'] == 9) {echo "selected";} ?>>9mm</option>
                                    <option value=10 <?php if ($tra['endo'] == 10) {echo "selected";} ?>>10mm</option>
                                    <option value=11 <?php if ($tra['endo'] == 11) {echo "selected";} ?>>11mm</option>
                                    <option value=12 <?php if ($tra['endo'] == 12) {echo "selected";} ?>>12mm</option>
                                    <option value=13 <?php if ($tra['endo'] == 13) {echo "selected";} ?>>13mm</option>
                                    <option value=14 <?php if ($tra['endo'] == 14) {echo "selected";} ?>>Mayo/igual a
                                        14mm</option>
                                </select>
                            </td>
                            <td width="5%">Intentos
                                <select name="T_inte" id="T_inte" data-mini="true">
                                    <option value=0 <?php if ($tra['inte'] == 0) {echo "selected";} ?>>Seleccionar
                                    </option>
                                    <option value=1 <?php if ($tra['inte'] == 1) {echo "selected";} ?>>1</option>
                                    <option value=2 <?php if ($tra['inte'] == 2) {echo "selected";} ?>>2</option>
                                    <option value=3 <?php if ($tra['inte'] == 3) {echo "selected";} ?>>3</option>
                                </select>
                            </td>
                            <td width="5%">Ecografía
                                <select name="T_eco" id="T_eco" data-mini="true">
                                    <option value="">Seleccionar</option>
                                    <?php
                                        $stmt = $db->prepare("SELECT codigo, nombre
                                            from transfer_ecografia
                                            where estado = 1
                                            order by nombre");
                                        $stmt->execute();

                                        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            $selected = "";

                                            if ($tra['eco'] == $data['codigo']) $selected = "selected";

                                            print('<option value="' . $data['codigo'] . '" '.$selected.'>'.ucwords(mb_strtolower($data['nombre'])).'</option>');
                                        } ?>
                                </select>
                            </td>
                            <td width="4%">Medico
                                <select name="T_med" id="T_med" data-mini="true">
                                    <?php if ($tra['med'] == "") {$tra['med'] = $paci['med'];}
                                        while ($med = $rMed->fetch(PDO::FETCH_ASSOC)) { ?>
                                    <option value="<?php echo $med['userx']; ?>"
                                        <?php if ($tra['med'] == $med['userx']) {echo "selected";} ?>>
                                        <?php echo $med['userx']; ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td width="6%">Embriologo
                                <select name="T_emb" id="T_emb" data-mini="true">
                                    <option value="">Seleccionar</option>
                                    <?php foreach ($rows as $embrio) { ?>
                                    <option value=<?php echo $embrio['id'];
                                            if ($tra['emb'] == $embrio['id']) echo " selected"; ?>>
                                        <?php echo $embrio['nom']; ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td width="39%" align="center" valign="top">Observaciones
                                <textarea name="T_obs" id="T_obs"><?php echo $tra['obs']; ?></textarea>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="enlinea libro">
                    Cuaderno: <input name="book" min="0" max="999" type="number" data-mini="true" id="book"
                        value="<?php echo $paci['book']; ?>">
                    Hoja: <input name="hoja" min="0" max="99999" type="number" data-mini="true" id="hoja"
                        value="<?php echo $paci['hoja']; ?>">
                </div>
                <div class="scroll_h">
                    <table data-role="table" style="margin: 0 auto;font-size:small;" class="ui-responsive table-stroke">
                        <thead>
                            <tr>
                                <th>ID<br>Embrión</th>
                                <th>Células</th>
                                <th>Frag.</th>
                                <th>Sime.</th>
                                <th>Cél. biop</th>
                                <th>Fin Ciclo</th>
                                <th></th>
                            </tr>
                        </thead>
                        <?php $c = 0;
                            while ($aspi = $rAspi->fetch(PDO::FETCH_ASSOC)) {
                                $c++; ?>
                        <tr <?php if ($aspi['anu'] > 0) {
                                    if ($aspi['d3f_cic'] == 'C') {echo 'class="bg_C"';}
                                    if ($aspi['d3f_cic'] == 'N') {echo 'class="bg_N"';}
                                    if ($aspi['d3f_cic'] == 'T') {echo 'class="bg_T"';}
                                } ?> id="fila<?php echo $c; ?>">

                            <?php if ($aspi['anu'] <> 0 and $aspi['anu'] < 4) { ?>
                            <script>
                            $(document).ready(function() {
                                $("#fila<?php echo $c; ?>").hide();
                            });
                            </script>
                            <?php } ?>

                            <td><input type="hidden" name="anu<?php echo $c; ?>"
                                    value="<?php echo $aspi['anu']; ?>"><?php echo $aspi['ovo']; ?>
                            <td>
                                <select name="cel<?php echo $c; ?>" id="cel" data-mini="true">
                                    <?php
                                                $consulta = $db->prepare("SELECT
                                                    c.codigo, c.nombre, COALESCE(p3.id, 0) pred
                                                    FROM lab_celulas c
                                                    LEFT JOIN lab_celulas p3 ON p3.id = c.id AND p3.dia3predeterminado = 1 AND p3.estado = 1
                                                    WHERE c.estado=1 AND c.dia3=1
                                                    ORDER BY pred DESC, c.id ASC");
                                                $consulta->execute();
                                                $data = $consulta->fetchAll();
                                                foreach ($data as $item) { ?>
                                    <option value="<?php print($item['codigo']); ?>"
                                        <?php if ($aspi['d3cel'] == $item['codigo']) print(" selected"); ?>>
                                        <?php print($item['nombre']); ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td><select name="fra<?php echo $c; ?>" id="fra" data-mini="true">
                                    <?php for ($i = 0; $i <= 100; $i = $i + 5) {
                                                echo '<option value=' . $i;
                                                if ($aspi['d3fra'] == $i) echo " selected";
                                                echo '>' . $i . '%</option>';
                                            } ?>
                                </select></td>
                            <td><select name="sim<?php echo $c; ?>" id="sim" data-mini="true">
                                    <option value=1
                                        <?php if ($aspi['d3sim'] == 1 or $aspi['d3sim'] == "") {echo "selected";} ?>>
                                        1
                                    </option>
                                    <option value=2 <?php if ($aspi['d3sim'] == 2) {echo "selected";} ?>>2</option>
                                    <option value=3 <?php if ($aspi['d3sim'] == 3) {echo "selected";} ?>>3</option>
                                    <option value=4 <?php if ($aspi['d3sim'] == 4) {echo "selected";} ?>>4</option>
                                </select></td>
                            <td class="peke2"><select name="c_bio<?php echo $c; ?>" id="c_bio" data-mini="true">
                                    <option value=0
                                        <?php if ($aspi['d3c_bio'] === 0 || $aspi['d3c_bio'] == "") {echo "selected";} ?>>
                                        0
                                    </option>
                                    <option value=1
                                        <?php if ($aspi['d3c_bio'] == 1 || strpos($paci['pago_extras'], "3") !== false) {echo "selected";} ?>>
                                        1
                                    </option>
                                    <option value=2 <?php if ($aspi['d3c_bio'] == 2) {echo "selected";} ?>>2</option>
                                </select></td>
                            <td class="enlinea">
                                <script>
                                $(document).ready(function() {
                                    <?php if ($aspi['d3f_cic'] == "C") { ?>
                                    $('#crio<?php echo $c; ?>').show();
                                    <?php } else { ?>
                                    $('#crio<?php echo $c; ?>').hide();
                                    <?php } ?>
                                });
                                </script>
                                <select name="f_cic<?php echo $c; ?>" class="f_cic" title="<?php echo $aspi['ovo']; ?>"
                                    id="<?php echo $c; ?>" data-mini="true">
                                    <option value="T" <?php if ($aspi['d3f_cic'] == "T") {echo "selected";} ?>>T
                                    </option>
                                    <option value="N" <?php if ($aspi['d3f_cic'] == "N") {echo "selected";} ?>>NV
                                    </option>
                                    <option value="C" <?php if ($aspi['d3f_cic'] == "C") {echo "selected";} ?>>Crio
                                    </option>
                                    <option value="O"
                                        <?php if ($aspi['d3f_cic'] == "O" or $aspi['anu'] == 0 or $aspi['d3f_cic'] == "") echo "selected"; ?>>
                                        Obs
                                    </option>
                                </select>
                                <div data-role="controlgroup" data-type="horizontal" data-mini="true"
                                    id="crio<?php echo $c; ?>" class="peke2">
                                    <input name="T<?php echo $c; ?>" class="tanque" id="T<?php echo $c; ?>"
                                        type="number" min="0" value="<?php echo $aspi['t']; ?>" placeholder="T"
                                        data-wrapper-class="controlgroup-textinput ui-btn">
                                    <input name="C<?php echo $c; ?>" class="canister" id="C<?php echo $c; ?>"
                                        type="number" min="0" value="<?php echo $aspi['c']; ?>" placeholder="C"
                                        data-wrapper-class="controlgroup-textinput ui-btn">
                                    <input name="G<?php echo $c; ?>" class="varilla" id="G<?php echo $c; ?>"
                                        type="number" min="0" value="<?php echo $aspi['g']; ?>" placeholder="G"
                                        data-wrapper-class="controlgroup-textinput ui-btn">
                                    <input name="P<?php echo $c; ?>" id="P<?php echo $c; ?>" type="number" min="0"
                                        value="<?php echo $aspi['p']; ?>" placeholder="P"
                                        data-wrapper-class="controlgroup-textinput ui-btn">
                                    <select name="col<?php echo $c; ?>" id="col<?php echo $c; ?>">
                                        <option value="">Seleccionar</option>
                                        <option value=1 <?php if ($aspi['col'] == 1) {echo "selected";} ?>>Azul
                                        </option>
                                        <option value=2 <?php if ($aspi['col'] == 2) {echo "selected";} ?>>Amarillo
                                        </option>
                                        <option value=3 <?php if ($aspi['col'] == 3) {echo "selected";} ?>>Blanco
                                        </option>
                                        <option value=4 <?php if ($aspi['col'] == 4) {echo "selected";} ?>>Rosado
                                        </option>
                                        <option value=5 <?php if ($aspi['col'] == 5) {echo "selected";} ?>>Verde
                                        </option>
                                    </select>
                                </div>
                            </td>
                            <td><a href="#f<?php echo $c; ?>" data-rel="popup" data-transition="pop"
                                    id="li<?php echo $c; ?>">Detalles
                                    <?php if ($aspi['obs'] <> "") echo " (Obs)"; ?></a>
                                <?php if (file_exists("emb_pic/p" . $paci['pro'] . "d3_" . $aspi['ovo'] . ".jpg"))
                                            echo "<br><a href='emb_pic/p" . $paci['pro'] . "d3_" . $aspi['ovo'] . ".jpg' target='new'> (Ver foto)</a>"; ?>
                                <div data-role="popup" id="f<?php echo $c; ?>" class="ui-content"> Subir/Cambiar
                                    Foto (Embrion <?php echo $aspi['ovo']; ?>)
                                    <input name="i<?php echo $c; ?>" type="file" accept="image/jpeg" data-mini="true"
                                        class="fotox" /> Observaciones<textarea name="obs<?php echo $c; ?>" id="obs"
                                        data-mini="true"><?php echo $aspi['obs']; ?></textarea>
                                </div>
                            </td>
                        </tr>

                        <?php } ?>
                    </table>
                </div>
                <?php } ?>

                <input type="hidden" name="c" id="total_embriones" value="<?php echo $c; ?>">
                <input name="guardar" type="Submit" id="guardar" value="GUARDAR DATOS" data-icon="check"
                    data-iconpos="left" data-inline="true" data-theme="b" data-mini="true" />
                <a href="info_r.php?a=<?php echo $paci['pro'] . "&b=" . $paci['dni'] . "&c=" . $paci['p_dni']; ?>"
                    target="new"
                    class="ui-btn ui-shadow ui-corner-all ui-icon-info ui-btn-icon-notext ui-btn-b ui-btn-inline ui-mini">Informe</a>
                <div data-role="popup" id="cargador" data-overlay-theme="b" data-dismissible="false">
                    <p>GUARDANDO DATOS..</p>
                </div>
            </div>
        </div>
    </form>
    <?php } ?>
    <script src="js/le_asp.js"></script>
</body>

</html>