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
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>

    <style>
        .controlgroup-textinput {
            padding-top: .10em;
            padding-bottom: .10em;
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

        .ui-tabs-panel {
            background-color: #FFF;
            padding: 5px;
        }

        /* Hide the number input */
        .ui-slider input[type=number] {
            display: none;
        }

        .peke .ui-input-text {
            width: 70% !important;

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
    </style>
</head>

<body>

    <script>
        $(document).ready(function () {
            // No close unsaved windows --------------------
            var unsaved = false;
            $(":input").change(function () {

                unsaved = true;

            });

            $(window).on('beforeunload', function () {
                if (unsaved) {
                    return 'UD. HA REALIZADO CAMBIOS';
                }
            });

            // Form Submit
            $(document).on("submit", "form", function (event) {
                // disable unload warning
                $(window).off('beforeunload');
            });

            $('.numeros').keyup(function () {

                var $th = $(this);
                $th.val($th.val().replace(/[^0-9]/g, function (str) {
                    //$('#cod small').replaceWith('<small>Error: Porfavor ingrese solo letras y números</small>');

                    return '';
                }));

                //$('#cod small').replaceWith('<small>Aqui ingrese siglas o un nombre corto de letras y números</small>');
            });

            $("#fur").on("change", function () {
                var date = new Date($(this).val());
                date.setDate(date.getDate() + 280); // + 280 dias
                $("#fpp").val(date.toInputFormat());

            });

            //From: http://stackoverflow.com/questions/3066586/get-string-in-yyyymmdd-format-from-js-date-object
            Date.prototype.toInputFormat = function () {
                var yyyy = this.getFullYear().toString();
                var mm = (this.getMonth() + 1).toString(); // getMonth() is zero-based
                var dd = this.getDate().toString();
                return yyyy + "-" + (mm[1] ? mm : "0" + mm[0]) + "-" + (dd[1] ? dd : "0" + dd[0]); // padding
            };

            $("#g_abo").keyup(function () {
                a = parseInt($("#g_abo").val()) + parseInt($('#g_pt').val()) + parseInt($('#g_pp').val()) + parseInt($('#g_nv').val());
                $("#g_ges").val(a);
            });
            $("#g_pt").keyup(function () {
                a = parseInt($("#g_abo").val()) + parseInt($('#g_pt').val()) + parseInt($('#g_pp').val()) + parseInt($('#g_nv').val());
                $("#g_ges").val(a);
            });
            $("#g_p").keyup(function () {
                a = parseInt($("#g_abo").val()) + parseInt($('#g_pt').val()) + parseInt($('#g_pp').val()) + parseInt($('#g_nv').val());
                $("#g_ges").val(a);
            });
            $("#g_nv").keyup(function () {
                a = parseInt($("#g_abo").val()) + parseInt($('#g_pt').val()) + parseInt($('#g_pp').val()) + parseInt($('#g_nv').val());
                $("#g_ges").val(a);
            });
        });

    </script>

    <?php
    if (isset($_POST['dni']) and $_POST['boton_datos'] == "GUARDAR DATOS") {
        insertObst($_POST['dni'], $_POST['fec'], $login, $_POST['g_3par'] ?? 0, $_POST['g_rn_men'] ?? 0, $_POST['g_gem'] ?? 0, $_POST['g_ges'], $_POST['g_abo'], $_POST['g_pt'], $_POST['g_pp'], $_POST['g_vag'], $_POST['g_ces'], $_POST['g_nv'], $_POST['g_nm'], $_POST['g_viv'], $_POST['g_m1'], $_POST['g_m2'], $_POST['g_fup'], $_POST['g_rn_may'], $_POST['pes'], $_POST['tal'], $_POST['fur'], $_POST['fpp'], $_POST['dud'] ?? 0, $_POST['fuma'], $_POST['vdrl'], $_POST['vdrl_f'], $_POST['hb'], $_POST['hb_f']);
    }

    if (isset($_POST['dni']) and $_POST['graba_nota'] == 'GRABAR') {
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
        $rUser = $db->prepare("SELECT role FROM usuario WHERE userx=?");
        $rUser->execute(array($login));
        $user = $rUser->fetch(PDO::FETCH_ASSOC);

        $rPaci = $db->prepare("SELECT hc_paciente.dni,nom,ape,fnac,g_ges,g_abo,g_pt,g_pp,g_vag,g_ces,g_nv,g_nm,g_viv,nota,medios_comunicacion_id FROM hc_antece,hc_paciente WHERE hc_paciente.dni=? AND hc_antece.dni=?");
        $rPaci->execute(array($id, $id));
        $paci = $rPaci->fetch(PDO::FETCH_ASSOC);

        $rObst = $db->prepare("SELECT id,g_ges,parto_nom,fec FROM hc_obste WHERE dni=? ORDER BY g_ges DESC");
        $rObst->execute(array($id));

        if (!file_exists("paci/" . $paci['dni'] . "/foto.jpg"))
            $foto_url = "_images/foto.gif";
        else $foto_url = "paci/" . $paci['dni'] . "/foto.jpg"; ?>

        <form action="n_obst.php?id=<?php echo $paci['dni']; ?>" method="post" data-ajax="false">
            <div data-role="page" class="ui-responsive-panel" id="n_obst">

                <div data-role="panel" id="indice_paci">
                    <img src="_images/logo.jpg"/>
                    <?php require ('_includes/menu_paciente.php'); ?>
                </div>

                <?php
                $color_programa_inmater = '';
                if ($paci['medios_comunicacion_id'] == 2) {
                    $color_programa_inmater = ' class="programa_inmater"';
                } ?>

                <div data-role="header" data-position="fixed" <?php print($color_programa_inmater); ?>>
                    <a href="#indice_paci" data-icon="bars" id="b_indice" class="ui-icon-alt" data-theme="a">MENU
                        <small>> Obstetricia</small>
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
                                echo ' <a href="#popupBasic" data-rel="popup" data-transition="pop" style="color:'.$nota_color.';">(' . date_diff(date_create($paci['fnac']), date_create('today'))->y . ')</a>'; ?></small>
                    </h2>
                    <a href="salir.php"
                    class="ui-btn-right ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-right ui-icon-power"
                    rel="external"> Salir</a>
                </div>

                <div data-role="popup" id="popupBasic" data-arrow="true">
                    <textarea name="nota" id="nota" data-mini="true"><?php echo $paci['nota']; ?></textarea>
                    <input type="Submit" value="GRABAR" name="graba_nota" data-mini="true"/>
                </div>

                <div class="ui-content" role="main">


                    <input type="hidden" name="dni" value="<?php echo $paci['dni']; ?>">
                    <div data-role="tabs">
                        <div data-role="navbar">
                            <ul>
                                <li><a href="#one" data-ajax="false" class="ui-btn-active ui-btn-icon-left ui-icon-bullets">Embarazos</a>
                                </li>
                                <li><a href="#two" data-ajax="false" class="ui-btn-icon-left ui-icon-edit">Nueva Embarazo</a>
                                </li>
                            </ul>
                        </div>
                        <div id="one" class="ui-body-d ui-content">

                            <ul data-role="listview" data-theme="a" data-inset="true">
                                <?php while ($obst = $rObst->fetch(PDO::FETCH_ASSOC)) { ?>
                                    <li>
                                        <a href='<?php echo "e_obst.php?id=" . $obst['id']; ?>' rel="external">
                                            <?php echo "Gestación # " . $obst['g_ges'];

                                            //echo date("d-m-Y", strtotime($obst['fec'])); ?>
                                        </a>
                                        <?php if ($obst['parto_nom'] <> "") echo '<span class="ui-li-count">' . $obst['parto_nom'] . '</span>'; ?>
                                    </li>

                                <?php }
                                if ($rObst->rowCount() < 1) echo '<p><h3>¡ No tiene embarazos !</h3></p>'; ?>
                            </ul>
                        </div>
                        <div id="two">
                            <div class="scroll_h">
                                <table width="100%" align="center" style="margin: 0 auto;max-width:900px;">
                                    <tr>
                                        <td class="peke2">Fecha</td>
                                        <td width="28%"><input name="fec" type="date" id="fec" value="<?php echo date("Y-m-d"); ?>"
                                                            data-mini="true"/></td>
                                        <td align="center" class="peke2">&nbsp;</td>
                                        <td align="center" class="peke2">&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td width="12%" class="peke2"># Gestación <input type="text" name="g_ges" id="g_ges"
                                                                                        data-mini="true" class="numeros"></td>
                                        <td class="peke2"><span>P.T
                    <input type="text" name="g_pt" id="g_pt" data-mini="true" class="numeros">
                    </span><span>P.P
                    <input type="text" name="g_pp" id="g_pp" data-mini="true" class="numeros">
                    </span><span>A
                        <input type="text" name="g_abo" id="g_abo" data-mini="true" class="numeros">
                        </span><span>N.V
                        <input type="text" name="g_nv" id="g_nv" data-mini="true" class="numeros">
                        </span></td>
                                        <td width="20%" align="center" class="peke2"><span>P.V
                    <input type="text" name="g_vag" id="g_vag" data-mini="true" class="numeros">
                    </span><span>P.C
                    <input type="text" name="g_ces" id="g_ces" data-mini="true" class="numeros">
                    </span></td>
                                        <td align="center" class="peke2"><span>Hijos Vivos
                    <input type="text" name="g_viv" id="g_viv" data-mini="true" class="numeros">
                    </span><span>N. Muertos
                    <input type="text" name="g_nm" id="g_nm" data-mini="true" class="numeros">
                    </span></td>
                                        <td width="12%"><label for="g_fup">Último parto</label>
                                            <input type="month" name="g_fup" id="g_fup" data-mini="true"></td>
                                    </tr>
                                    <tr>
                                        <td class="peke"><label for="pes">Peso anterior</label>
                                            <input name="pes" type="number" step="any" id="pes" data-mini="true"></td>
                                        <td><input type="checkbox" value=1 name="dud" id="dud" data-mini="true">
                                            <label for="dud">Dudas</label></td>
                                        <td width="20%" align="center" class="peke2">Muertos 1º sem
                                            <input name="g_m1" type="text" id="g_m1" data-mini="true" class="numeros"></td>
                                        <td width="28%" align="center" class="peke2">Despues 1º sem
                                            <input name="g_m2" type="text" id="g_m2" data-mini="true" class="numeros"></td>
                                        <td rowspan="2">
                                            <fieldset data-role="controlgroup" data-mini="true">
                                                <input type="checkbox" value=1 name="g_3par" id="g_3par" data-mini="true">
                                                <label for="g_3par">Ninguno o mas de 3 partos</label>
                                                <input type="checkbox" value=1 name="g_rn_men" id="g_rn_men" data-mini="true">
                                                <label for="g_rn_men">RN menor a 2500g</label>
                                                <input type="checkbox" value=1 name="g_gem" id="g_gem" data-mini="true">
                                                <label for="g_gem">Gemelares</label>
                                            </fieldset>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="peke"><label for="tal">Talla (cms)</label>
                                            <input name="tal" type="number" step="any" id="tal" data-mini="true"></td>
                                        <td><label for="fuma">Cigarros al día</label>
                                            <input name="fuma" type="range" id="fuma" max="40" min="0" data-show-value="true"
                                                data-popup-enabled="true" data-highlight="true"></td>
                                        <td colspan="2">
                                            <div data-role="controlgroup" data-type="horizontal" data-mini="true">
                                                <select name="vdrl" id="vdrl">
                                                    <option value="">VDRL:</option>
                                                    <option value=1>VDRL: Positivo</option>
                                                    <option value=2>VDRL: Negativo</option>
                                                </select>
                                                <input name="vdrl_f" type="date" id="vdrl_f"
                                                    data-wrapper-class="controlgroup-textinput ui-btn"/>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><label for="fur">FUR</label>
                                            <input name="fur" type="date" id="fur" data-mini="true"></td>
                                        <td><label for="fpp">FPP</label>
                                            <input name="fpp" type="date" id="fpp" readonly data-mini="true"></td>
                                        <td colspan="2">
                                            <div data-role="controlgroup" data-type="horizontal" data-mini="true">
                                                <select name="hb" id="hb">
                                                    <option value="">Hb:</option>
                                                    <option value=7>Hb: 7</option>
                                                    <option value=8>Hb: 8</option>
                                                    <option value=9>Hb: 9</option>
                                                    <option value=10>Hb: 10</option>
                                                    <option value=11>Hb: 11</option>
                                                    <option value=12>Hb: 12</option>
                                                    <option value=13>Hb: 13</option>
                                                    <option value=14>Hb: 14</option>
                                                </select>
                                                <input name="hb_f" type="date" id="hb_f"
                                                    data-wrapper-class="controlgroup-textinput ui-btn"/>
                                            </div>
                                        </td>
                                        <td class="peke">RN con mayor peso(gr)
                                            <input name="g_rn_may" id="g_rn_may" size="6" maxlength="6" data-mini="true"
                                                class="numeros"></td>
                                    </tr>

                                </table>

                            </div>
                            <?php if ($user['role'] == 1) { ?>
                                <input type="Submit" value="GUARDAR DATOS" name="boton_datos" data-icon="check"
                                    data-iconpos="left" data-mini="true" class="show-page-loading-msg" data-textonly="false"
                                    data-textvisible="true" data-msgtext="Agregando datos.." data-theme="b"
                                    data-inline="true"/>
                            <?php } ?>
                        </div>
                    </div>
                    <!--end TABS ------------------------------------------------------------------------------------- -->


                </div>

            </div>
        </form>

    <?php } ?>
    <script>
        $(document).on("click", ".show-page-loading-msg", function () {

            if (document.getElementById("fec").value == "") {
                alert("Debe llenar el campo 'Fecha'");
                return false;
            }

            if (document.getElementById("g_ges").value == "") {
                alert("Debe llenar el campo 'Gestaciones'");
                return false;
            }
            if (document.getElementById("fur").value == "") {
                alert("Debe llenar el campo 'FUR'");
                return false;
            }


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
        })
            .on("click", ".hide-page-loading-msg", function () {
                $.mobile.loading("hide");
            });

        $(function () {
            $('#alerta').delay(3000).fadeOut('slow');

        });//]]>

    </script>
</body>
</html>
