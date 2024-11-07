<!DOCTYPE HTML>
<html>
<head>
<?php
   include 'seguridad_login.php'
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="_themes/tema_inmater.min.css"/>
    <link rel="stylesheet" href="_themes/jquery.mobile.icons.min.css"/>
    <link rel="stylesheet" href="css/jquery.mobile.structure-1.4.5.min.css"/>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>

</head>

<body>

<div data-role="page" class="ui-responsive-panel" id="le_andro_esp" data-dialog="true">

    <?php if ($_POST['p_dni'] <> "" and $_POST['fec']) {

        updateAndro_esp($_POST['idx'], $_POST['dni'], $_POST['p_dni'], $_POST['fec'], $_POST['vol_f'], $_POST['con_f'], $_POST['via'], $_POST['agl'], $_POST['sha'], $_POST['c_red'], $_POST['ph'], $_POST['lic'], $_POST['deb'], $_POST['con'], $_POST['pl_f'], $_POST['pnl_f'], $_POST['ins_f'], $_POST['inm_f'], $_POST['m_n'], $_POST['m_a'], $_POST['m_mic'], $_POST['m_mac'], $_POST['m_cab'], $_POST['m_col'], $_POST['m_inm'], $_POST['m_bic'], $_POST['m_bic2'], $_POST['nota'], $_POST['emb']);

    }

    if ($_GET['ip'] <> "") {

        $dni = $_GET['dni'];
        $p_dni = $_GET['ip'];
        $id = $_GET['id'];

        $rPare = $db->prepare("SELECT p_nom,p_ape FROM hc_pareja WHERE p_dni=?");
        $rPare->execute(array($p_dni));
        $pare = $rPare->fetch(PDO::FETCH_ASSOC);

        $rEmb = $db->prepare("SELECT id,nom FROM lab_user WHERE sta=0");
        $rEmb->execute();

        $Rpop = $db->prepare("SELECT * FROM lab_andro_esp WHERE p_dni=? AND fec=?");
        $Rpop->execute(array($p_dni, $id));
        $pop = $Rpop->fetch(PDO::FETCH_ASSOC); ?>
        <style>
            .ui-dialog-contain {
                max-width: 800px;
                margin: 2% auto 15px;
                padding: 0;
                position: relative;
                top: -15px;
            }

            .peke2 .ui-input-text, #pm_n, #pm_a {
                width: 80px !important;
                display: inline-block !important;
            }

            .scroll_h {
                overflow-x: scroll;
                overflow-y: hidden;
                white-space: nowrap;
            }
            .enlinea div {
                display: inline-block;
                vertical-align: middle;
            }
        </style>
        <script>
            $(document).ready(function () {
                $('#form2').submit(function () {

                    var m_n = Number($('#m_n').val());
                    var m_a = Number($('#m_a').val());

                    if ((m_n + m_a) > 0 && (m_n + m_a) < 100) {
                        alert("El valor morfologico debe ser igual o mayor a 100 unidades");
                        return false;
                    }
                    // if (confirm("Presione ACEPTAR para guardar, los cambios seran IRREVERSIBLES por Ud.")) {
                    //	return true;
                    //} else return false;
                });

                var vol_f = $('#vol_f').val();
                var con_f = $('#con_f').val();
                $('#spz_f').html((vol_f * con_f).toFixed(2));

                var m_n = Number($('#m_n').val());
                var m_a = Number($('#m_a').val());
                $('#pm_a').html(((m_a * 100) / (m_a + m_n)).toFixed(2) + '%');
                $('#pm_n').html((100 - ((m_a * 100) / (m_a + m_n))).toFixed(2) + '%');

                $(".total_spz").change(function () {
                    var vol_f = $('#vol_f').val();
                    var con_f = $('#con_f').val();

                    var pl_f = Number($('#pl_f').val());
                    var pnl_f = Number($('#pnl_f').val());
                    var ins_f = Number($('#ins_f').val());

                    $('#spz_f').html((vol_f * con_f).toFixed(2));

                    $("#inm_f").val(100 - (pl_f + pnl_f + ins_f));
                });

                $(".total_mor").change(function () {
                    var m_mic = Number($('#m_mic').val());
                    var m_mac = Number($('#m_mac').val());
                    var m_cab = Number($('#m_cab').val());
                    var m_col = Number($('#m_col').val());
                    var m_inm = Number($('#m_inm').val());
                    var m_bic = Number($('#m_bic').val());
                    var m_bic2 = Number($('#m_bic2').val());

                    $("#m_a").val(m_mic + m_mac + m_cab + m_col + m_inm + m_bic + m_bic2);

                    var m_n = Number($('#m_n').val());
                    var m_a = Number($('#m_a').val());
                    $('#pm_a').html(((m_a * 100) / (m_a + m_n)).toFixed(2) + '%');
                    $('#pm_n').html((100 - ((m_a * 100) / (m_a + m_n))).toFixed(2) + '%');

                });

                $("#azoos").change(function () {
                    if ($(this).prop('checked') ) {
                        $("#con_f,#pl_f,#pnl_f,#ins_f,#inm_f,#m_n,#m_a,#m_mic,#m_mac,#m_cab,#m_col,#m_inm,#m_bic,#m_bic2").val(0);
                        $("#nota").prop('required',true);
                    } else {
                        $("#con_f,#pl_f,#pnl_f,#ins_f,#inm_f,#m_n,#m_a,#m_mic,#m_mac,#m_cab,#m_col,#m_inm,#m_bic,#m_bic2").val('');
                        $("#nota").val('required',false);
                    }
                });

            });
        </script>
    <?php if ($pop['con_f']==0 and $pop['pl_f']==0 and $pop['pnl_f']==0 and $pop['ins_f']==0 and $pop['inm_f']==100 and $pop['m_n']==0 and $pop['m_a']==0 and $pop['m_mic']==0 and $pop['m_mac']==0 and $pop['m_cab']==0 and $pop['m_col']==0 and $pop['m_inm']==0 and $pop['m_bic']==0 and $pop['m_bic2']==0) { ?>
        <script>
            $(document).ready(function () {
                $("#azoos").attr("checked", "checked");
                $("#azoos").checkboxradio("refresh");
            });
        </script>
    <?php } ?>
        <div data-role="header" data-position="fixed">
            <h3>Espermatograma
                <small> (<?php echo $pare['p_ape'] . " " . $pare['p_nom']; ?>)</small>
            </h3>
        </div><!-- /header -->

        <div class="ui-content" role="main">

            <form action="le_andro_esp.php" method="post" data-ajax="false" id="form2">
                <input type="hidden" name="idx" value="<?php echo $id; ?>">
                <input type="hidden" name="dni" value="<?php echo $dni; ?>">
                <input type="hidden" name="p_dni" value="<?php echo $p_dni; ?>">

                <table width="100%" align="center" style="margin: 0 auto; font-size: small;">
                    <tr>
                        <td width="18%">Fecha</td>
                        <td width="18%"><input name="fec" type="date" required id="fec" tabindex="1"
                                               value="<?php if ($pop['fec']) echo $pop['fec']; else echo date("Y-m-d"); ?>"
                                               data-mini="true"></td>
                        <td width="14%">&nbsp;</td>
                        <td width="19%">Embriologo</td>
                        <td width="31%"><select name="emb" required id="emb" tabindex="12" data-mini="true">
                                <option value="">---</option>
                                <?php while ($embrio = $rEmb->fetch(PDO::FETCH_ASSOC)) { ?>
                                    <option value=<?php echo $embrio['id'];
                                    if ($pop['emb'] == $embrio['id']) echo " selected"; ?>><?php echo $embrio['nom']; ?></option>
                                <?php } ?></select></td>
                    </tr>
                    <tr>
                        <td>Volumen</td>
                        <td align="left" class="peke2"><input name="vol_f" type="number" step="any" required
                                                              class="total_spz" id="vol_f" min="0" tabindex="2"
                                                              value="<?php echo $pop['vol_f']; ?>" data-mini="true">
                            (ml)
                        </td>
                        <td>&nbsp;</td>
                        <td colspan="2" align="center" class="ui-bar-a">Motilidad</td>
                    </tr>
                    <tr>
                        <td>Concentración</td>
                        <td align="left" class="peke2"><input name="con_f" type="number" step="any" required
                                                              class="total_spz" id="con_f" min="0" tabindex="3"
                                                              value="<?php echo $pop['con_f']; ?>" data-mini="true">
                            x10<sup>6</td>
                        <td>&nbsp;</td>
                        <td>Progresivo Lineal</td>
                        <td align="center" class="peke2"><input name="pl_f" type="number" step="any" required
                                                                class="total_spz" id="pl_f" tabindex="13"
                                                                value="<?php echo $pop['pl_f']; ?>" data-mini="true">
                            %
                        </td>
                    </tr>
                    <tr>
                        <td>Total de spz</td>
                        <td align="left" class="peke2">
                            <div id="spz_f" style="color:#900"></div>
                        </td>
                        <td>&nbsp;</td>
                        <td>Progresivo No Lineal</td>
                        <td align="center" class="peke2"><input name="pnl_f" type="number" step="any" required
                                                                class="total_spz" id="pnl_f" tabindex="14"
                                                                value="<?php echo $pop['pnl_f']; ?>" data-mini="true">
                            %
                        </td>
                    </tr>
                    <tr>
                        <td class="peke2">Viabilidad</td>
                        <td align="left" class="peke2"><input name="via" type="number" step="any" required
                                                              class="total_spz" id="via" tabindex="4"
                                                              value="<?php echo $pop['via']; ?>" data-mini="true">
                            %
                        </td>
                        <td>&nbsp;</td>
                        <td>Insitu</td>
                        <td align="center" class="peke2"><input name="ins_f" type="number" step="any" required
                                                                class="total_spz" id="ins_f" tabindex="15"
                                                                value="<?php echo $pop['ins_f']; ?>" data-mini="true">
                            %
                        </td>
                    </tr>
                    <tr>
                        <td class="peke2">Aglutinación</td>
                        <td align="left" class="peke2"><input name="agl" type="number" step="any" required id="agl"
                                                              tabindex="5" value="<?php echo $pop['agl']; ?>"
                                                              data-mini="true">
                            %
                        </td>
                        <td>&nbsp;</td>
                        <td>No Motil</td>
                        <td align="center" class="peke2"><input name="inm_f" type="number" step="any" required
                                                                id="inm_f" tabindex="16"
                                                                value="<?php echo $pop['inm_f']; ?>" readonly
                                                                data-mini="true">
                            %
                        </td>
                    </tr>
                    <tr>
                        <td class="peke2">Shaking</td>
                        <td align="left" class="peke2"><input name="sha" type="number" step="any" required id="sha"
                                                              tabindex="6" value="<?php echo $pop['sha']; ?>"
                                                              data-mini="true">
                            %
                        </td>
                        <td>&nbsp;</td>
                        <td colspan="2" align="center" class="ui-bar-a">Morfologia</td>
                    </tr>
                    <tr>
                        <td class="peke2">Cel. Redondas</td>
                        <td align="left" class="peke2"><input name="c_red" type="number" step="any" required id="c_red"
                                                              min="0" tabindex="7" value="<?php echo $pop['c_red']; ?>"
                                                              data-mini="true">
                            x10<sup>6</td>
                        <td>&nbsp;</td>
                        <td class="peke2">NORMAL</td>
                        <td align="center" class="peke2"><input name="m_n" type="number" step="any" class="total_mor"
                                                                id="m_n" tabindex="17" value="<?php echo $pop['m_n']; ?>"
                                                                data-mini="true">
                            <div id="pm_n"></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="peke2">Ph</td>
                        <td align="left" class="peke2"><input name="ph" type="number" step="any" id="ph" tabindex="8"
                                                              value="<?php echo $pop['ph']; ?>" data-mini="true"></td>
                        <td>&nbsp;</td>
                        <td class="peke2">ANORMAL</td>
                        <td align="center" class="peke2"><input name="m_a" type="number" id="m_a" step="any"
                                                                tabindex="18" value="<?php echo $pop['m_a']; ?>" readonly
                                                                data-mini="true">
                            <div id="pm_a"></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="peke2">Licuefacción</td>
                        <td align="left" class="peke2"><input name="lic" type="number" min="0" max="60" id="lic"
                                                              tabindex="8" value="<?php echo $pop['lic']; ?>"
                                                              data-mini="true">
                            minutos
                        </td>
                        <td>&nbsp;</td>
                        <td class="peke2">- Microcefalo</td>
                        <td align="center" class="peke2"><input name="m_mic" type="number" step="any" class="total_mor"
                                                                id="m_mic" tabindex="19"
                                                                value="<?php echo $pop['m_mic']; ?>" data-mini="true"></td>
                    </tr>
                    <tr>
                        <td class="peke2">Debris</td>
                        <td align="left" class="peke2"><select name="deb" id="deb" tabindex="9" data-inline="true"
                                                               data-mini="true">
                                <option value=0 <?php if ($pop['deb'] == 0) echo "selected"; ?>>0</option>
                                <option value=1 <?php if ($pop['deb'] == 1) echo "selected"; ?>>1</option>
                                <option value=2 <?php if ($pop['deb'] == 2) echo "selected"; ?>>2</option>
                                <option value=3 <?php if ($pop['deb'] == 3) echo "selected"; ?>>3</option>
                            </select></td>
                        <td>&nbsp;</td>
                        <td class="peke2">- Macrocefalo</td>
                        <td align="center" class="peke2"><input name="m_mac" type="number" step="any" class="total_mor"
                                                                id="m_mac" tabindex="20"
                                                                value="<?php echo $pop['m_mac']; ?>" data-mini="true"></td>
                    </tr>
                    <tr>
                        <td class="peke2">Consistencia</td>
                        <td align="left" class="peke2"><select name="con" id="con" tabindex="10" data-inline="true"
                                                               data-mini="true">
                                <option value=0 <?php if ($pop['con'] == 0) echo "selected"; ?>>Normal</option>
                                <option value=1 <?php if ($pop['con'] == 1) echo "selected"; ?>>Aumentada</option>
                            </select></td>
                        <td>&nbsp;</td>
                        <td class="peke2">- Amorfo cabeza</td>
                        <td align="center" class="peke2"><input name="m_cab" type="number" step="any" class="total_mor"
                                                                id="m_cab" tabindex="21"
                                                                value="<?php echo $pop['m_cab']; ?>" data-mini="true"></td>
                    </tr>
                    <tr>
                        <td>Observaciones</td>
                        <td><textarea name="nota" id="nota" tabindex="11"><?php echo $pop['nota']; ?></textarea></td>
                        <td>&nbsp;</td>
                        <td class="peke2">- Amorfo cola</td>
                        <td align="center" class="peke2"><input name="m_col" type="number" step="any" class="total_mor"
                                                                id="m_col" tabindex="22"
                                                                value="<?php echo $pop['m_col']; ?>" data-mini="true"></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td class="peke2">- Inmaduro</td>
                        <td align="center" class="peke2"><input name="m_inm" type="number" step="any" class="total_mor"
                                                                id="m_inm" tabindex="23"
                                                                value="<?php echo $pop['m_inm']; ?>" data-mini="true"></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td class="peke2">&nbsp;</td>
                        <td class="peke2">- Bicaudo</td>
                        <td align="center" class="peke2"><input name="m_bic" type="number" step="any" class="total_mor"
                                                                id="m_bic" tabindex="24"
                                                                value="<?php echo $pop['m_bic']; ?>" data-mini="true"></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td class="peke2">&nbsp;</td>
                        <td class="peke2">- Bicefalo</td>
                        <td align="center" class="peke2"><input name="m_bic2" type="number" step="any" class="total_mor"
                                                                id="m_bic2" tabindex="25"
                                                                value="<?php echo $pop['m_bic2']; ?>" data-mini="true">
                        </td>
                    </tr>
                </table>
                <?php //if ($login=="lab" and $pop['emb']=="") {
                if ($_SESSION['role'] == 2) { ?>
                <div class="enlinea">
                    <input type="Submit" name="guardar" value="GUARDAR" data-icon="check" data-iconpos="left"
                           data-mini="true" data-theme="b" data-inline="true"/>
                    <label for="azoos">AZOOSPERMICO</label>
                    <input type="checkbox" name="azoos" id="azoos" data-mini="true">
                </div>
                <?php } ?>

            </form>

        </div><!-- /content -->


    <?php } ?>
</div><!-- /page -->

</body>
</html>
