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
    .controlgroup-textinput {
        padding-top: .10em;
        padding-bottom: .10em;
    }

    .peke2 .ui-input-text,
    #pm_n,
    #pm_a {
        width: 80px !important;
        display: inline-block !important;
    }
    </style>
</head>

<body>
    <div data-role="page" class="ui-responsive-panel" id="le_andro_cap" data-dialog="true">
        <?php
		if (!!$_POST && !!$_POST['fec']) {
			print('<ul data-role="listview" data-theme="a" data-inset="true"><li data-role="list-divider" style="background-color: #FFFF91;">Se actualizaron los datos correctamente!</li></ul>');
			updateAndro_cap($_POST['idx'],$_POST['dni'],$_POST['p_dni'],$_POST['fec'],$_POST['vol_f'],$_POST['con_f'],$_POST['esp'],$_POST['con_c'],$_POST['pl_f'],$_POST['pl_c'],$_POST['pnl_f'],$_POST['pnl_c'],$_POST['ins_f'],$_POST['ins_c'],$_POST['inm_f'],$_POST['inm_c'],$_POST['cap'],$_POST['sel'],$_POST['mue'],$_POST['des_tip'],$_POST['des_fec'],(isset($_POST['cont']) ? $_POST['cont'] : 0),$_POST['des'],$_POST['pro'],$_POST['p_dni_het'],$_POST['emb'],$_POST['rep'],$login);
		}

		$dni = $_GET['dni'];
		$p_dni = $_GET['ip'];
		$pro = isset($_GET['pro']) ? $_GET['pro']: '';
		$rep = isset($_GET['rep']) ? $_GET['rep']: 0;
		$het_dni = isset($_GET['het']) ? $_GET['het'] : '';
		$id = $_GET['id'];

		$rPare = $db->prepare("SELECT p_nom, p_ape FROM hc_pareja WHERE p_dni=?");
		$rPare->execute(array($p_dni));
		$pare = $rPare->fetch(PDO::FETCH_ASSOC);

		$rEmb = $db->prepare("SELECT id, nom FROM lab_user WHERE sta=0");
		$rEmb->execute();

		$Rpop = $db->prepare("SELECT * FROM lab_andro_cap WHERE id=? and eliminado is false");
        if($id == ''){
		    $Rpop->execute(array(0));
        }else{
		    $Rpop->execute(array($id));
        }
		$pop = $Rpop->fetch(PDO::FETCH_ASSOC);

        $pop['pro'] = $pop['pro'] ?? '';
		if ($pop['pro']>0) {$pro = $pop['pro'];}

		$rTan = $db->prepare("SELECT * FROM lab_tanque WHERE sta = 1 and tip = 1;");
		$rTan->execute();
	?>
        <style>
        .ui-dialog-contain {
            max-width: 800px;
            margin: 2% auto 15px;
            padding: 0;
            position: relative;
            top: -15px;

        }

        .tablex td {
            border-left: 1px solid #72a2aa;
        }

        .tablex td:first-child {
            border-left: none;
        }

        .chk_otro {
            background-color: gray;
        }

        .chk_bio {
            background-color: #E99885;
        }

        .chk_crio {
            background-color: #9AC2F1;
        }

        .chk_free {
            background-color: white;
        }

        .varillas_cel input[type="checkbox"] {
            display: none;
        }

        .varillas_cel td {
            width: 80px;
        }

        .varillas_cel tr:last-child {
            display: none;
        }

        .ui-slider input {
            display: none;
        }

        .ui-slider-track {
            margin: 0 15px 0 15px;
        }
        </style>
        <script>
        var descon = 0;
        var conta = 0;

        $(document).on('change', '.deschk', function(ev) {
            if (descon == $(this).attr("id") || descon == 0) {
                conta++;
                descon = $(this).attr("id");

                if (conta == 1) {
                    var arr = descon.split('|');
                    $("#des_tip").val(arr[0]);
                    $("#des_fec").val(arr[1]);

                    if ($("#mue").val() == 3) var p_dni = $("#p_dni").val();
                    if ($("#mue").val() == 4) var p_dni = $("#p_dni_het").val();

                    $.post("le_tanque.php", {
                        e: arr[0],
                        f: arr[1],
                        p_dni: p_dni
                    }, function(data) {
                        var data = data.split('|');
                        $("#vol_f").val(data[0]);
                        $("#con_f").val(data[1]);
                        $("#esp").val(data[2]);
                        $("#esp").selectmenu("refresh", true);
                        $("#pl_f").val(data[3]);
                        $("#pnl_f").val(data[4]);
                        $("#ins_f").val(data[5]);
                        $("#inm_f").val(data[6]);
                        //$(".varillas_cel").append('<div class="varillas">'+data+'</div>'); 
                    });
                }
            } else {
                $('.deschk').attr('checked', false);
                descon = $(this).attr("id");
                conta = 0;
                $("#vol_f,#con_f,#pl_f,#pnl_f,#ins_f,#inm_f").val('');
            }
        });

        $(document).ready(function() {
            $('#formxxxxx').submit(function() {
                if (confirm("Presione ACEPTAR para guardar, los cambios seran IRREVERSIBLES por Ud.")) {
                    if (confirm("Esta segurooo seguroooooo???? pero MUY segurooo??"))
                        return true;
                    else
                        return false;
                } else return false;
            });

            $(".crio").hide();

            var vol_f = $('#vol_f').val();
            var con_f = $('#con_f').val();
            var con_c = $('#con_c').val();
            $('#spz_f').html((vol_f * con_f).toFixed(2));
            $('#spz_c').html((0.3 * con_c).toFixed(2));

            $(".total_spz,.deschk").change(function() {
                var vol_f = $('#vol_f').val();
                var con_f = $('#con_f').val();
                var con_c = $('#con_c').val();

                var pl_f = Number($('#pl_f').val());
                var pl_c = Number($('#pl_c').val());
                var pnl_f = Number($('#pnl_f').val());
                var pnl_c = Number($('#pnl_c').val());
                var ins_f = Number($('#ins_f').val());
                var ins_c = Number($('#ins_c').val());

                $('#spz_f').html((vol_f * con_f).toFixed(2));
                $('#spz_c').html((0.3 * con_c).toFixed(2));
                $("#inm_f").val(100 - (pl_f + pnl_f + ins_f));
                $("#inm_c").val(100 - (pl_c + pnl_c + ins_c));
            });

            $("#mue").change(function() {
                var items = $(this).val();

                if (items == 3 || items == 4) {
                    $(".crio").show();
                    $(".varillas").remove();
                    $(".descon").remove();
                    if (items == 3) var d = $("#p_dni").val();
                    if (items == 4) {
                        var d = $("#p_dni_het").val();
                        var het = 1;
                    }

                    $.post("le_tanque.php", {
                        d: d,
                        het: het
                    }, function(data) {
                        $(".descon_cel").append('<div class="descon">' + data + '</div>');
                    });
                } else {
                    $(".crio").hide();
                }
            });

            $("#c_tan").change(function() {
                $("#c_tan option:selected").each(function() {
                    var t = $(this).val();
                    $(".varillas").remove();
                    $.post("le_tanque.php", {
                        t: t
                    }, function(data) {
                        $("#c_can").html(data);
                        $("#c_can").selectmenu("refresh");
                    });
                });
            });

            $("#c_can").change(function() {
                $("#c_can option:selected").each(function() {
                    if ($("#mue").val() == 3) var p_dni = $("#p_dni").val();
                    if ($("#mue").val() == 4) var p_dni = $("#p_dni_het").val();

                    var c = $(this).attr("id");
                    var tip_id = $("#idx").val();
                    var tip = 0; // 0=descongelados

                    $(".varillas").remove();
                    $('.varillas_cel').html('<h3>CARGANDO DATOS...</h3>');
                    $.post("le_tanque.php", {
                        c: c,
                        p_dni: p_dni,
                        tip: tip,
                        tip_id: tip_id
                    }, function(data) {
                        $('.varillas_cel').html('');
                        $(".varillas_cel").append('<div class="varillas">' + data +
                            '</div>');
                    });
                });
            });
        });
        </script>
        <?php $pop['mue'] = $pop['mue'] ?? 0; ?>
        <?php if ($pop['mue']==3 or $pop['mue']==4) { ?>
        <script>
        $(document).ready(function() {
            $(".crio").show();
            $(".deschk").hide();
        });
        </script>
        <?php } ?>
        <div data-role="header" data-position="fixed">
            <?php
		if (isset($_GET["path"]) && !empty($_GET["path"])) {
			print('<a href="' . $_GET["path"] . '.php' . $_GET["path_url"] . '" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>');
		} ?>
        <?php    $pare['p_ape'] = $pare['p_ape'] ?? '';?>
        <?php    $pare['p_nom'] = $pare['p_nom'] ?? '';?>
            <h3>Capacitación espermática<small> (<?php echo $pare['p_ape']." ".$pare['p_nom']; ?>)</small></h3>
        </div>
        <div class="ui-content" role="main">
            <form action="" method="post" data-ajax="false" id="form2">
                <input type="hidden" name="idx" id="idx" value="<?php echo $id;?>">
                <!--idx="idx" se usa en el javascript-->
                <input type="hidden" name="dni" value="<?php echo $dni;?>">
                <input type="hidden" name="p_dni" id="p_dni" value="<?php echo $p_dni;?>">
                <!--id="p_dni" se usa en el javascript-->
                <input type="hidden" name="p_dni_het" id="p_dni_het" value="<?php echo $het_dni;?>">
                <!--id="p_dni_het" se usa en el javascript-->
                <input type="hidden" name="pro" value="<?php echo $pro;?>">
                <input type="hidden" name="rep" value="<?php echo $rep;?>">

                <table width="100%" align="center" style="margin: 0 auto; font-size: small;">
                    <tr>
                        <td width="22%">Fecha</td>
                        <td width="34%"><input name="fec" type="date" required id="fec"
                                value="<?php if (!empty($pop['fec'])) echo $pop['fec']; else echo date("Y-m-d"); ?>"
                                data-mini="true"></td>
                        <td width="11%">Embriologo</td>
                        <td width="33%">
                            <select name="emb" required id="emb" data-mini="true">
                                <option value="">Seleccionar</option>
                                <?php  while ($embrio = $rEmb->fetch(PDO::FETCH_ASSOC)) { ?>
                                <option
                                <?php    $pop['emb'] = $pop['emb'] ?? '';?>
                                <?php    $embrio['id'] = $embrio['id'] ?? '';?>
                                    value=<?php echo $embrio['id']; if ($pop['emb']==$embrio['id']) echo " selected"; ?>>
                                    <?php echo $embrio['nom']; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td align="center">Tipo de Selección Espermatica
                            <select name="sel" required id="sel" data-mini="true">
                                <?php  $pop['sel'] = $pop['sel'] ?? 0; ?>
                                <option value=1 <?php if ($pop['sel']==1 or $pop['sel']=="") echo "selected"; ?>>
                                    Aleatoria</option>
                                <option value=2 <?php if ($pop['sel']==2) echo "selected"; ?>>Masculina</option>
                                <option value=3 <?php if ($pop['sel']==3) echo "selected"; ?>>Femenina</option>
                            </select>
                        </td>
                        <td colspan="2" align="center" class="ui-bar-a">Tipo de Muestra
                            <select name="mue" required id="mue" tabindex="12" data-mini="true">
                                <option value="">Seleccionar</option>
                                <option value=1 <?php if ($pop['mue']==1) echo "selected"; ?>>Fresca PAREJA (Homólogo)
                                </option>
                                <option value=2 <?php if ($pop['mue']==2) echo "selected"; ?>>Fresca DONANTE
                                    (Heterólogo)</option>
                                <option value=3 <?php if ($pop['mue']==3) echo "selected"; ?>>Criopreservada PAREJA
                                    (Homólogo)</option>
                                <option value=4 <?php if ($pop['mue']==4) echo "selected"; ?>>Criopreservada DONANTE
                                    (Heterólogo)</option>
                            </select>
                        </td>
                        <td align="center" class="ui-bar-a">Tipo de Capacitación
                            <select name="cap" required id="cap" data-mini="true">
                                <?php    $pop['cap'] = $pop['cap'] ?? 0;?>
                                <option value=1 <?php if ($pop['cap']==1 or $pop['cap']=="") echo "selected"; ?>>
                                    Gradiente densidad</option>
                                <option value=2 <?php if ($pop['cap']==2) echo "selected"; ?>>Lavado</option>
                                <option value=3 <?php if ($pop['cap']==3) echo "selected"; ?>>Swim up</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Volumen</td>
                        <td colspan="2" align="center" class="peke2">
                            <?php   $pop['vol_f'] = $pop['vol_f'] ?? 0; ?>
                            <input name="vol_f" type="number" required class="total_spz" id="vol_f" min="0" step="any"
                                tabindex="1" value="<?php echo $pop['vol_f'];?>" data-mini="true"> (ml)
                        </td>
                        <td align="center" class="peke2">0.3 ml</td>
                    </tr>
                    <tr>
                        <td>Concentración</td>
                        <td colspan="2" align="center" class="peke2">
                            <div data-role="controlgroup" data-type="horizontal" data-mini="true">
                                <?php    $pop['con_f'] = $pop['con_f'] ?? 0.00; ?>
                                <input name="con_f" type="number" required class="total_spz" id="con_f" min="0"
                                    step="any" tabindex="2" value="<?php echo $pop['con_f'];?>"
                                    data-wrapper-class="controlgroup-textinput ui-btn">
                                <select name="esp" id="esp" tabindex="3">
                                <?php    $pop['esp'] = $pop['esp'] ?? 0;?>
                                    <option value=0 <?php if ($pop['esp']==0) echo "selected"; ?>>x10 (6)</option>
                                    <option value=1 <?php if ($pop['esp']==1) echo "selected"; ?>>Spz/Camp</option>
                                </select>
                            </div>
                        </td>
                        <td align="center" class="peke2">
                                <?php    $pop['con_c'] = $pop['con_c'] ?? 0.00;?>
                            <input name="con_c" type="number" required class="total_spz" id="con_c" min="0" step="any"
                                tabindex="7" value="<?php echo $pop['con_c'];?>" data-mini="true">
                            x10<sup>6
                        </td>
                    </tr>
                    <tr>
                        <td>Total de spz</td>
                        <td colspan="2" align="center" class="peke2">
                            <div id="spz_f" style="color:#900"></div>
                        </td>
                        <td align="center" class="peke2">
                            <div id="spz_c" style="color:#900"></div>
                        </td>
                    </tr>
                    <tr>
                        <td>Progresivo Lineal</td>
                                <?php $pop['pl_f'] = $pop['pl_f'] ?? 0;?>
                        <td colspan="2" align="center" class="peke2"><input name="pl_f" type="number" required
                                class="total_spz" id="pl_f" tabindex="3" value="<?php echo $pop['pl_f'];?>"
                                data-mini="true"> %</td>
                                <?php $pop['pl_c'] = $pop['pl_c'] ?? 0;?>
                        <td align="center" class="peke2"><input name="pl_c" type="number" required class="total_spz"
                                id="pl_c" tabindex="8" value="<?php echo $pop['pl_c'];?>" data-mini="true"> %</td>
                    </tr>
                    <tr>
                        <td>Progresivo No Lineal</td>
                                <?php $pop['pnl_f'] = $pop['pnl_f'] ?? 0;?>
                        <td colspan="2" align="center" class="peke2"><input name="pnl_f" type="number" required
                                class="total_spz" id="pnl_f" tabindex="4" value="<?php echo $pop['pnl_f'];?>"
                                data-mini="true"> %</td>
                                <?php $pop['pnl_c'] = $pop['pnl_c'] ?? 0;?>
                        <td align="center" class="peke2"><input name="pnl_c" type="number" required class="total_spz"
                                id="pnl_c" tabindex="9" value="<?php echo $pop['pnl_c'];?>" data-mini="true"> %</td>
                    </tr>
                    <tr>
                        <td>Insitu</td>
                                <?php $pop['ins_f'] = $pop['ins_f'] ?? 0;?>
                        <td colspan="2" align="center" class="peke2"><input name="ins_f" type="number" required
                                class="total_spz" id="ins_f" tabindex="5" value="<?php echo $pop['ins_f'];?>"
                                data-mini="true"> %</td>
                                <?php $pop['ins_c'] = $pop['ins_c'] ?? 0;?>
                        <td align="center" class="peke2"><input name="ins_c" type="number" required class="total_spz"
                                id="ins_c" tabindex="10" value="<?php echo $pop['ins_c'];?>" data-mini="true"> %</td>
                    </tr>
                    <tr>
                        <td>No Motil</td>
                                <?php $pop['inm_f'] = $pop['inm_f'] ?? 0;?>
                        <td colspan="2" align="center" class="peke2"><input name="inm_f" type="number" required min="0"
                                id="inm_f" tabindex="6" value="<?php echo $pop['inm_f'];?>" readonly data-mini="true"> %
                        </td>
                                <?php $pop['inm_c'] = $pop['inm_c'] ?? 0;?>
                        <td align="center" class="peke2"><input name="inm_c" type="number" required min="0" id="inm_c"
                                tabindex="11" value="<?php echo $pop['inm_c'];?>" readonly data-mini="true"> %</td>
                    </tr>
                </table>
                <table width="100%" align="center" style="margin: 0 auto; font-size: small;" class="crio">
                    <tr>
                        <td colspan="4" align="center" class="ui-bar-a">Criopreservado</td>
                    </tr>
                    <tr>
                        <td class="peke2">Tanque</td>
                        <td class="peke2">
                            <select name="c_tan" id="c_tan" data-mini="true">
                                <option value="" selected>Seleccionar</option>
                                <?php  while ($tan = $rTan->fetch(PDO::FETCH_ASSOC)) { ?>
                                <option value="<?php echo $tan['tan']; ?>"><?php echo $tan['n_tan']; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td width="32%" rowspan="5" align="center" valign="top" class="varillas_cel"></td>
                        <td width="42%" rowspan="5" align="center" valign="top">
                            <?php
						if ($pop['mue']>=3) {
							if ($pop['des']<>"") {
								if ($pop['mue']==4) {
									$rHete = $db->prepare("SELECT p_nom,p_ape FROM hc_pareja WHERE p_dni=?");
									$rHete->execute(array($pop['des_dni']));	
									$het = $rHete->fetch(PDO::FETCH_ASSOC);
									echo 'DONANTE:<b>  '.$het['p_ape'].' '.$het['p_nom'].'</b>';
								}
							
								echo '<table bordercolor="#72a2aa" style="text-align:center">
									<tr class="ui-bar-b"> <td colspan="6">Viales Descongelados:</td></tr>
									<tr class="ui-bar-c"><td>T</td><td>C</td><td>Varilla</td><td>Vial</td><td>Tipo</td><td>Fecha</td></tr>';

								$des = explode('|' , $pop['des']);

								foreach($des as $vial) {
									if ($vial) {
										$ds = explode('-' , $vial);
										if ($pop['des_tip']==1) { $tipo = 'Biop.';$bg='class="chk_bio"'; } else { $tipo = 'Crio.';$bg='class="chk_crio"'; }
										echo '<tr> <td> '.$ds[0].'</td><td> '.$ds[1].'</td><td '.$bg.'> '.$ds[2].'</td><td '.$bg.'> '.$ds[3].'</td><td> '.$tipo.'</td><td> '.date("d-m-Y", strtotime($pop['des_fec'])).'</td></tr>';
									}
								}

								echo '</table>';
							}
						} ?>
                            <div class="descon_cel"></div>

                            <?php    $pop['des'] = $pop['des'] ?? '';?>
                            <?php    $pop['des_tip'] = $pop['des_tip'] ?? 0;?>
                            <?php    $pop['des_fec'] = $pop['des_fec'] ?? '1899-12-30';?>

                            <input type="hidden" name="des" value="<?php echo $pop['des'];?>">
                            <input type="hidden" name="des_tip" id="des_tip" value="<?php echo $pop['des_tip'];?>">
                            <input type="hidden" name="des_fec" id="des_fec" value="<?php echo $pop['des_fec'];?>">
                        </td>
                    </tr>
                    <tr>
                        <td><span class="peke2">Canister</span></td>
                        <td class="peke2"><select name="c_can" id="c_can" data-mini="true"></select>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
                <?php
			if ($_SESSION['role'] == 2) {?>
                <input type="Submit" name="guardar" value="GUARDAR" data-icon="check" data-iconpos="left"
                    data-mini="true" data-theme="b" data-inline="true" />
                <?php } ?>
            </form>
        </div>
    </div>
</body>

</html>