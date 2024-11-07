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

    .bg_N {
        background-color: rgba(240, 131, 132, 0.30);
    }

    .enlinea div {
        display: inline-block;
        vertical-align: middle;
    }
    </style>
    <script>
    $(document).ready(function() {
        $('#form1').submit(function() {
            var nv = false;
            for (i = 1; i <= $('#num_ovos').val(); i++) {
                if ($('#est' + i).val() == "MII" && $('#f_cic' + i).val() == "N" && $('#c_pol' + i)
                    .val() == "2" && $('#pron' + i).val() == "2") {
                    nv = true;
                }
            }

            /* if (nv) {
                alert("No se puede registrar un MII con resultado NV.");
                return false;                    
            } */

            if (document.getElementById("inc1").value == "") {
                alert("Debe ingresar la incubadora.");
                return false;
            }

            var no_fec = 0;
            let pn1 = 0;
            var pn2 = 0;
            var pn3 = 0;
            var inma = 0;
            var atre = 0;
            var ct = 0;
            for (c = 1; c <= $('#num_ovos').val(); c++) {
                if ($('#fila' + c).is(":visible")) {
                    //Haploide : MII y NV y ademas cp y pn igual a 1
                    if ($('#est' + c).val() == 'MII' && (($('#pron' + c).val() == '1'))) pn1++;
                    //Fecundados: MII y OBS
                    if ($('#est' + c).val() == 'MII' && $('#f_cic' + c).val() == 'O' && $('#c_pol' + c)
                        .val() == '2' && $('#pron' + c).val() == '2') pn2++;
                    //NO Fecundados: MII y NV
                    if ($('#est' + c).val() == 'MII' && $('#f_cic' + c).val() == 'N' && (($('#c_pol' +
                                c).val() == '0' || $('#c_pol' + c).val() == '1' || $('#c_pol' + c)
                            .val() == '2') && ($('#pron' + c).val() == '0' || $('#pron' + c)
                            .val() == '1' || $('#pron' + c).val() == '2'))) no_fec++;
                    //Triploides : MII y NV y ademas cp y pn mayor q 2 
                    if ($('#est' + c).val() == 'MII' && (($('#pron' + c).val() == '3' ))) pn3++;
                    //Inmaduros: MI o VG
                    if ($('#est' + c).val() == 'VG' || $('#est' + c).val() == 'MI') inma++;
                    //Atresicos: ATR
                    if ($('#est' + c).val() == 'ATR') atre++;
                    //Citolizados: CT
                    if ($('#est' + c).val() == 'CT') ct++;
                }
            }
            if (confirm("INSEMINACIÓN: " +"\n-Fecundados: " + pn2 + "\n-NO Fecundados: " + no_fec + "\n-Un pronúcleo: " + pn1 +
                    "\n-Triploides : " + pn3 + "\n-Inmaduros: " + inma +
                    "\n-Atresicos: " + atre + "\n-Citolizados: " + ct)) {
                $("#cargador").popup("open", {
                    positionTo: "window"
                });
                return true;
            } else
                return false;

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
        $(".val_defect").change(function() {
            var med = $(this).attr("title");
            var items = $(this).val();
            if (items != "MII") {
                $('#f_cic' + med).val('N');
                $('#f_cic' + med).selectmenu("refresh", true);
            }

            if (items == "VG" || items == "ATR") {
                $('#c_pol' + med).val('0');
                $('#c_pol' + med + '-button').hide();
                $('#pron' + med).val('0');
                $('#pron' + med + '-button').hide();

            } else {
                $('#c_pol' + med + '-button').show();
                $('#pron' + med + '-button').show();
            }
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

        $(".f_cic").change(function() {
            var med = $(this).attr("title");
            var items = $(this).val();
            var id = $(this).attr(
                "fila"); // solo para el dia 1 , para los demas dias es: var id = $(this).attr("id");
            $('#fila' + id).removeClass();
            if (items == "N") $('#fila' + id).addClass('bg_N');
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
            if ($_POST['anu' . $i] == 0 or $_POST['anu' . $i] >= 2) {
                $c2++;

                if ($_POST['f_cic' . $i] == "O") {
                    if ($_POST['anu' . $i] == 2) $anu = 0; else $anu = $_POST['anu' . $i];
                } else {
                    $anu = 2;
                    $cancela++;
                }

                lab_updateAspi_d1($_POST['pro'], $i, $anu, $_POST['est' . $i], $_POST['c_pol' . $i], $_POST['pron' . $i], $_POST['t_pro' . $i], $_POST['d_nuc' . $i], $_POST['hal' . $i], $_POST['f_cic' . $i], $_POST['obs' . $i], $don, $_FILES['i' . $i]);
            }
        }
    }

    if ($_POST['dias'] <= 2)
        lab_updateAspi_sta($_POST['pro'], 'Dia 2', 2, $_POST['hra1'], $_POST['emb1'], $_POST['hra1c'], $_POST['emb1c']);

    if ($cancela == $c2) {
        if ($_POST['dias'] > 2)
            lab_updateAspi_sta($_POST['pro'], 'Dia 2', 2, $_POST['hra1'], $_POST['emb1'], $_POST['hra1c'], $_POST['emb1c']);
        lab_updateAspi_fin($_POST['pro']);
        $fin=1;
    }

    lab_incubadora1($_POST['rep'], $_POST['inc1']);
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

    $campos = "ovo,anu,d0est,d1est,d1c_pol,d1pron,d1t_pro,d1d_nuc,d1hal,d1f_cic,obs,t,c,g,p,col,des,don";
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
		} ?>

    <?php if (is_null($paci['id'])) { // no hay reproducion = traslado ?>
    <script>
    $(document).ready(function() {

        $('.no_traslado').hide();

    });
    </script>
    <?php } ?>
    <form action="" method="post" enctype="multipart/form-data" data-ajax="false" id="form1">
        <div data-role="page" class="ui-responsive-panel" id="le_aspi1">
            <?php require ('_includes/menu_laboratorio.php'); ?>
            <div data-role="header" data-position="fixed">
                <a href="#indice_paci" data-icon="bars" id="b_indice" class="ui-icon-alt" data-theme="a">MENU
                    <small>> Dia 1</small>
                </a>
                <h2><?php echo "(" . $paci['tip'] . "-" . $paci['pro'] . "-" . $paci['vec'] . ")";
                    echo " Dia 1"; if ($paci['fec1'] <> '1899-12-30') echo ': <small>'.date("d-m-Y", strtotime($paci['fec1'])).'</small>'; ?>
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
                <input type="hidden" name="rep" value="<?php echo $paci['rep']; ?>">
                <input type="hidden" name="dni" value="<?php echo $paci['dni']; ?>">
                <input type="hidden" name="don" value="<?php echo $mujer['don']; ?>">
                <input type="hidden" name="dias" value="<?php echo $paci['dias']; ?>">
                <input type="hidden" name="book" value="<?php echo $paci['book']; ?>">
                <input type="hidden" name="hoja" value="<?php echo $paci['hoja']; ?>">

                <div class="scroll_h" style="background-color:rgba(189,213,211,1.00)">
                    <table width="100%" align="center" style="margin: 0 auto;font-size:small;">
                        <tr>
                            <td width="11%">PROCEDIMIENTOS:</td>
                            <td width="15%">
                                <div class="enlinea">
                                    <?php if ($paci['p_cic'] == 1) echo "(Ciclo Natural) ";
                                    if ($paci['p_fiv'] == 1) echo "(FIV) ";
                                    if ($paci['p_icsi'] == 1) echo "(" . $_ENV["VAR_ICSI"] . ") ";
                                    if ($paci['p_od'] <> '') echo "(OD Fresco) ";
                                    if ($paci['p_don'] == 1) echo "(Donación Fresco) ";
                                    if ($paci['p_cri'] == 1) echo "(Crio Ovos)"; ?>
                                </div>
                            </td>
                            <td>CONCLUSIONES:</td>
                            <td><textarea name="obs" id="obs2" data-mini="true"><?php echo $paci['obs']; ?></textarea>
                            </td>
                            <td width="7%">Embriologo
                                <select name="emb1" required id="emb1" data-mini="true">
                                    <option value="">Seleccionar</option>
                                    <?php foreach ($rows as $embrio) { ?>
                                    <option value=<?php echo $embrio['id'];
                                        if ($paci['emb1'] == $embrio['id']) echo " selected"; ?>>
                                        <?php echo $embrio['nom']; ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td width="12%">Hora
                                <input name="hra1" type="time" data-mini="true" required id="hra1"
                                    value="<?php if ($paci['hra1'] <> "") echo $paci['hra1']; else echo date("H:i"); ?>">
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
                                <select name="emb1c" id="emb1c" data-mini="true">
                                    <option value="">Seleccionar</option>
                                    <?php foreach ($rows as $embrio) { ?>
                                    <option value=<?php echo $embrio['id'];
                                        if ($paci['emb1c'] == $embrio['id']) echo " selected"; ?>>
                                        <?php echo $embrio['nom']; ?></option>
                                    <?php } ?>
                                </select>
                                INCUBADORA:
                                <select name="inc1" id="inc1" required data-mini="true">
                                    <option value="">Seleccionar</option>
                                    <?php
                                        $consulta = $db->prepare("SELECT codigo, nombre FROM lab_incubadora where dia1=1 and estado=1");
                                        $consulta->execute();
                                        $data = $consulta->fetchAll();
                                        foreach ($data as $item) { ?>
                                    <option value=<?php print($item['codigo']);
                                            if ($paci['inc1'] == $item['codigo']) print(" selected"); ?>>
                                        <?php print($item['nombre']); ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td width="10%" class="peke">
                                Hora Crio
                                <input name="hra1c" type="time" data-mini="true" id="hra1c"
                                    value="<?php if ($paci['hra1c'] <> "") echo $paci['hra1c']; else echo date("H:i"); ?>">
                            </td>
                        </tr>
                    </table>
                </div>
                <?php if ($rAspi->rowCount() > 0) { ?>
                <div class="scroll_h">
                    <table data-role="table" style="margin: 0 auto;font-size:small;" class="ui-responsive table-stroke">
                        <thead>
                            <tr>
                                <th>Ovo</th>
                                <th>Proceso</th>
                                <th>Estadio</th>
                                <th>CP</th>
                                <th>PN</th>
                                <th>Tam. pronuc.</th>
                                <th>Dist. nucleolos</th>
                                <th>Halo</th>
                                <th>Fin Ciclo</th>
                                <th></th>
                            </tr>
                        </thead>
                        <?php $c = 0;
                            while ($aspi = $rAspi->fetch(PDO::FETCH_ASSOC)) {
                                $c++; ?>
                        <tr <?php if ($aspi['anu'] > 0) {
                                    if ($aspi['d1f_cic'] == 'N') echo 'class="bg_N"';
                                } ?> id="fila<?php echo $c; ?>">

                            <?php if ($aspi['anu'] <> 0 and $aspi['anu'] < 2) { ?>
                            <script>
                            $(document).ready(function() {
                                $("#fila<?php echo $c; ?>").hide();
                            });
                            </script>
                            <?php } ?>

                            <td><input type="hidden" name="anu<?php echo $c; ?>"
                                    value="<?php echo $aspi['anu']; ?>"><?php echo $aspi['ovo']; ?>
                            <td><?php echo $aspi['d0est']; ?></td>
                            <td>
                                <select name="est<?php echo $c; ?>" id="est<?php echo $c; ?>" class="val_defect"
                                    title="<?php echo $c; ?>" data-mini="true">
                                    <?php if ($paci['p_fiv'] == 1) { ?>
                                    <option value="FIV" <?php if ($aspi['d1est'] == "FIV") echo "selected"; ?>>FIV
                                    </option>
                                    <?php } ?>
                                    <option value="MII" <?php if ($aspi['d1est'] == "MII") echo "selected"; ?>>M II
                                    </option>
                                    <option value="MI" <?php if ($aspi['d1est'] == "MI") echo "selected"; ?>>M I
                                    </option>
                                    <option value="VG" <?php if ($aspi['d1est'] == "VG") echo "selected"; ?>>VG</option>
                                    <option value="ATR" <?php if ($aspi['d1est'] == "ATR") echo "selected"; ?>>ATR
                                    </option>
                                    <option value="DV" <?php if ($aspi['d1est'] == "DV") echo "selected"; ?>>DV</option>
                                    <option value="CT" <?php if ($aspi['d1est'] == "CT") echo "selected"; ?>>CT</option>
                                </select>
                            </td>
                            <td>
                                <select name="c_pol<?php echo $c; ?>" id="c_pol<?php echo $c; ?>" data-mini="true">
                                    <option value="0" <?php if ($aspi['d1c_pol'] == "0") echo "selected"; ?>>0</option>
                                    <option value="1" <?php if ($aspi['d1c_pol'] == "1") echo "selected"; ?>>1</option>
                                    <option value="2"
                                        <?php if ($aspi['d1c_pol'] == "2" or $aspi['d1c_pol'] == "") echo "selected"; ?>>
                                        2</option>
                                    <option value="3" <?php if ($aspi['d1c_pol'] == "3") echo "selected"; ?>>3</option>
                                    </option>
                                </select>
                            </td>
                            <td>
                                <select name="pron<?php echo $c; ?>" id="pron<?php echo $c; ?>" data-mini="true">
                                    <option value="0" <?php if ($aspi['d1pron'] === "0") echo "selected"; ?>>0</option>
                                    <option value="1" <?php if ($aspi['d1pron'] == "1") echo "selected"; ?>>1</option>
                                    <option value="2"
                                        <?php if ($aspi['d1pron'] == "2" or $aspi['d1pron'] == "") echo "selected"; ?>>2
                                    </option>
                                    <option value="3" <?php if ($aspi['d1pron'] == "3") echo "selected"; ?>>3</option>
                                    </option>
                                </select>
                            </td>
                            <td>
                                <select name="t_pro<?php echo $c; ?>" required id="t_pro" data-mini="true">
                                    <option value=1 <?php if ($aspi['d1t_pro'] == 1) echo "selected"; ?>>1</option>
                                    <option value=2
                                        <?php if ($aspi['d1t_pro'] == 2 or $aspi['d1t_pro'] == "") echo "selected"; ?>>2
                                    </option>
                                    <option value=3 <?php if ($aspi['d1t_pro'] == 3) echo "selected"; ?>>3</option>
                                    <option value=4 <?php if ($aspi['d1t_pro'] == 4) echo "selected"; ?>>4</option>
                                </select>
                            </td>
                            <td>
                                <select name="d_nuc<?php echo $c; ?>" required id="d_nuc" data-mini="true">
                                    <option value=1
                                        <?php if ($aspi['d1d_nuc'] == 1 or $aspi['d1d_nuc'] == "") echo "selected"; ?>>1
                                    </option>
                                    <option value=2 <?php if ($aspi['d1d_nuc'] == 2) echo "selected"; ?>>2</option>
                                    <option value=3 <?php if ($aspi['d1d_nuc'] == 3) echo "selected"; ?>>3</option>
                                    <option value=4 <?php if ($aspi['d1d_nuc'] == 4) echo "selected"; ?>>4</option>
                                </select>
                            </td>
                            <td><input type="checkbox" name="hal<?php echo $c; ?>" id="hal" data-mini="true" value=1
                                    <?php if ($aspi['d1hal'] == 1) echo "checked"; ?> data-role="none">
                            </td>
                            <td class="enlinea">
                                <select name="f_cic<?php echo $c; ?>" class="f_cic" title="<?php echo $aspi['ovo']; ?>"
                                    id="f_cic<?php echo $c; ?>" fila="<?php echo $c; ?>" data-mini="true">
                                    <option value="N" <?php if ($aspi['d1f_cic'] == "N") echo "selected"; ?>>NV
                                    </option>
                                    <option value="O"
                                        <?php if ($aspi['d1f_cic'] == "O" or $aspi['anu'] == 0) echo "selected"; ?>>
                                        Obs
                                    </option>
                                </select>
                            </td>
                            <td><a href="#f<?php echo $c; ?>" data-rel="popup" data-transition="pop"
                                    id="li<?php echo $c; ?>">Detalles
                                    <?php if ($aspi['obs'] <> "") echo " (Obs)"; ?></a>
                                <?php if (file_exists("emb_pic/p" . $paci['pro'] . "d1_" . $aspi['ovo'] . ".jpg"))
                                            echo "<br><a href='emb_pic/p" . $paci['pro'] . "d1_" . $aspi['ovo'] . ".jpg' target='new'> (Ver foto)</a>"; ?>
                                <div data-role="popup" id="f<?php echo $c; ?>" class="ui-content"> Subir/Cambiar
                                    Foto (Embrion <?php echo $aspi['ovo']; ?>)
                                    <input name="i<?php echo $c; ?>" type="file" accept="image/jpeg" data-mini="true"
                                        class="fotox" /> Observaciones<textarea name="obs<?php echo $c; ?>" id="obs"
                                        data-mini="true"><?php echo $aspi['obs']; ?></textarea>
                                </div>
                            </td>
                        </tr>

                        <?php if ($aspi['d1est'] == "VG" or $aspi['d1est'] == "ATR") { ?>
                        <script>
                        $(document).ready(function() {
                            $('#c_pol<?php echo $c; ?>-button').hide();
                            $('#pron<?php echo $c; ?>-button').hide();
                            //}
                        });
                        </script>
                        <?php }
                            } ?>
                    </table>
                </div>
                <?php } ?>
                <input type="hidden" name="c" id="num_ovos" value="<?php echo $c; ?>">
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
</body>

</html>