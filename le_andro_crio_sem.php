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
    <title>Inmater Clínica de Fertilidad | Criopreservacion de semen</title>
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery.mobile-1.4.5.min.js"></script>
</head>

<body>
    <div data-role="page" class="ui-responsive-panel" id="le_andro_crio_sem" data-dialog="true">
        <?php
		if (!!$_POST && isset($_POST['p_dni']) && !empty($_POST['p_dni']) and isset($_POST['fec']) && !empty($_POST['fec'])) {
			$data = [
				'cuaderno' => $_POST['cuaderno'],
				'pagina' => $_POST['pagina'],
			];
			updateAndro_crio_sem($_POST['idx'],$_POST['dni'],$_POST['p_dni'],$_POST['fec'],$_POST['vol_f'],$_POST['vol_c'] ? : 0,$_POST['con_f'],$_POST['con_c'] ? : 0,$_POST['pl_f'],$_POST['pl_c'] ? : 0,$_POST['pnl_f'],$_POST['pnl_c'] ? : 0,$_POST['ins_f'],$_POST['ins_c'] ? : 0,$_POST['inm_f'],$_POST['inm_c'] ? : 0,$_POST['obs'],$_POST['cap'] ? : 0,$_POST['tra'],$_FILES['doc'],$_POST['vol'],$_POST['via'],$_POST['met'],$_POST['emb'],$_POST['c_tan'],$_POST['c_can'],$_POST['v_p'],$data);
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

			$Rpop = $db->prepare("SELECT * FROM lab_andro_crio_sem WHERE p_dni=? and fec=?");
			$Rpop->execute(array($p_dni,$id));
			$pop = $Rpop->fetch(PDO::FETCH_ASSOC);

			$rTan = $db->prepare("SELECT * FROM lab_tanque WHERE sta = 1 and tip=1;");
			$rTan->execute(); ?>

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

        .varillas_cel input {
            width: 50px !important;
        }

        .varillas_cel tr:last-child {
            display: none;
        }

        .pequeno2 .ui-input-text {
            width: 80px !important;
            display: inline-block !important;
        }

        .scroll_h {
            overflow-x: scroll;
            overflow-y: hidden;
            white-space: nowrap;
        }

        .ui-slider input {
            display: none;
        }

        .ui-slider-track {
            margin: 0 15px 0 15px;
        }
        </style>

        <script>
        $(document).on('change', '.varillas input:checkbox', function(ev) {
            n_via = $("#n_via").val();
            //$("#via").val(0);

            if ($(this).prop('checked')) {
                n_via++;
                $("#n_via").val(n_via);
                $(this).parent("td").css("background", "#9AC2F1");
                //if (n_via == ($("#via").val() - 1)) { alert("Solo le queda un Vial mas"); }
                if (n_via >= $("#via").val()) {
                    $(".varillas").css("pointer-events", "none");
                    $("#n_via").val(0);
                }
            } else {
                n_via--;
                $("#n_via").val(n_via);
                $(this).parent("td").css("background", "white");
            }
        });

        $(document).ready(function() {
            $('#formxxxxx').submit(function() {
                if (confirm("Presione ACEPTAR para guardar, los cambios seran IRREVERSIBLES por Ud.")) {
                    if (confirm("Esta segurooo seguroooooo???? pero MUY segurooo??")) {
                        $("#cargador").popup("open", {
                            positionTo: "window"
                        });
                        return true;
                    } else
                        return false;
                } else return false;
            });

            $("#vol_c,#con_c,#pnl_c,#pl_c,#inm_c,#ins_c").hide();

            var vol_f = $('#vol_f').val();
            var vol_c = $('#vol_c').val();
            var con_f = $('#con_f').val();
            var con_c = $('#con_c').val();

            $('#spz_f').html((vol_f * con_f).toFixed(2));
            $('#spz_c').html((vol_c * con_c).toFixed(2));

            $(".total_spz").change(function() {
                var vol_f = $('#vol_f').val();
                var vol_c = $('#vol_c').val();
                var con_f = $('#con_f').val();
                var con_c = $('#con_c').val();

                var pl_f = Number($('#pl_f').val());
                var pl_c = Number($('#pl_c').val());
                var pnl_f = Number($('#pnl_f').val());
                var pnl_c = Number($('#pnl_c').val());
                var ins_f = Number($('#ins_f').val());
                var ins_c = Number($('#ins_c').val());

                $('#spz_f').html((vol_f * con_f).toFixed(2));
                $('#spz_c').html((vol_c * con_c).toFixed(2));

                $("#inm_f").val(100 - (pl_f + pnl_f + ins_f));
                $("#inm_c").val(100 - (pl_c + pnl_c + ins_c));
            });

            $("#cap").change(function() {

                if ($(this).val() != "") {
                    $("#vol_c,#con_c,#pnl_c,#pl_c,#inm_c,#ins_c").show();
                    $("#vol_c,#con_c,#pnl_c,#pl_c,#inm_c,#ins_c").prop('required', true);
                } else {
                    $("#vol_c,#con_c,#pnl_c,#pl_c,#inm_c,#ins_c").hide();
                    $("#vol_c,#con_c,#pnl_c,#pl_c,#inm_c,#ins_c").prop('required', false);
                }
            });

            $("#c_tan").change(function() {

                if ($("#via").val() > 0) {

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
                } else {
                    alert("Debe seleccionar # Viales");
                }
            });

            $("#doc").hide();
            $("#tra").change(function() {

                if ($(this).val() == 1) {
                    $("#doc").show();
                    $("#doc").prop('required', true);
                } else {
                    $("#doc").hide();
                    $("#doc").prop('required', false);
                }
            });

            $("#c_can").change(function() {

                $("#c_can option:selected").each(function() {
                    var c = $(this).attr("id");
                    var p_dni = $("#p_dni").val();
                    var tip_id = $("#idx").val();
                    var tip = 2; // 1=bio_tes 2=crio_sem 3=embrio 4=ovo
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

        <?php
			if ($pop['cap']<>"" && $pop['cap']<>0) { ?>
        <script>
        $(document).ready(function() {
            $("#vol_c").show();
            $("#con_c").show();
            $("#pnl_c").show();
            $("#pl_c").show();
            $("#inm_c").show();
            $("#ins_c").show();
        });
        </script>
        <?php } ?>

        <div data-role="header" data-position="fixed">
            <h3>Criopreservación semen<small> (<?php echo $pare['p_ape']." ".$pare['p_nom']; ?>)</small></h3>
        </div>

        <div class="ui-content" role="main">
            <form action="" method="post" enctype="multipart/form-data" data-ajax="false" id="form2">
                <input type="hidden" name="idx" id="idx" value="<?php echo $id;?>">
                <!--idx="idx" se usa en el javascript-->
                <input type="hidden" name="dni" value="<?php echo $dni;?>">
                <input type="hidden" name="p_dni" id="p_dni" value="<?php echo $p_dni;?>">
                <!--id="p_dni" se usa en el javascript-->
                <input type="hidden" name="n_via" id="n_via" value=0>
                <!--id="n_via" se usa en el javascript-->

                <table width="100%" align="center" style="margin: 0 auto; font-size: small;">
                    <tr>
                        <td width="25%">Fecha</td>
                        <td width="29%"><input name="fec" type="date" required id="fec"
                                value="<?php if ($pop['fec']) echo $pop['fec']; else echo date("Y-m-d"); ?>"
                                data-mini="true" <?php //if ($pop['fec']<>"0000-00-00") echo "readonly"; ?>></td>
                        <td width="5%">Embriologo</td>
                        <td width="41%"><select name="emb" required id="emb" data-mini="true">
                                <option value="">---</option>
                                <?php  while ($embrio = $rEmb->fetch(PDO::FETCH_ASSOC)) { ?>
                                <option
                                    value=<?php echo $embrio['id']; if ($pop['emb']==$embrio['id']) echo " selected"; ?>>
                                    <?php echo $embrio['nom']; ?></option>
                                <?php } ?>
                            </select></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td colspan="2" align="center" class="ui-bar-a">FRESCO</td>
                        <td align="center" class="ui-bar-a"><select name="cap" id="cap" tabindex="7" data-mini="true">
                                <option value="">CAPACITADO: ---</option>
                                <option value=1 <?php if ($pop['cap']==1) echo "selected"; ?>>CAPACITADO: Gradiente
                                    densidad</option>
                                <option value=2 <?php if ($pop['cap']==2) echo "selected"; ?>>CAPACITADO: Lavado
                                </option>
                                <option value=3 <?php if ($pop['cap']==3) echo "selected"; ?>>CAPACITADO: Swim up
                                </option>
                            </select></td>
                    </tr>
                    <tr>
                        <td>Volumen </td>
                        <td colspan="2" align="center" class="pequeno2"><input name="vol_f" type="number" step="any"
                                required class="total_spz" id="vol_f" min="0" tabindex="1"
                                value="<?php echo $pop['vol_f'];?>" data-mini="true">
                            (ml)</td>
                        <td align="center" class="pequeno2"><input name="vol_c" type="number" step="any"
                                class="total_spz" id="vol_c" min="0" tabindex="8" value="<?php echo $pop['vol_c'];?>"
                                data-mini="true">
                            (ml)</td>
                    </tr>
                    <tr>
                        <td>Concentración</td>
                        <td colspan="2" align="center" class="pequeno2"><input name="con_f" type="number" step="any"
                                required class="total_spz" id="con_f" min="0" tabindex="2"
                                value="<?php echo $pop['con_f'];?>" data-mini="true">
                            x10<sup>6</td>
                        <td align="center" class="pequeno2"><input name="con_c" type="number" step="any"
                                class="total_spz" id="con_c" min="0" tabindex="9" value="<?php echo $pop['con_c'];?>"
                                data-mini="true">
                            x10<sup>6</td>
                    </tr>
                    <tr>
                        <td>Total de spz</td>
                        <td colspan="2" align="center" class="pequeno2">
                            <div id="spz_f" style="color:#900"></div>
                        </td>
                        <td align="center" class="pequeno2">
                            <div id="spz_c" style="color:#900"></div>
                        </td>
                    </tr>
                    <tr>
                        <td>Progresivo Lineal</td>
                        <td colspan="2" align="center" class="pequeno2"><input name="pl_f" type="number" step="any"
                                required class="total_spz" id="pl_f" tabindex="3" value="<?php echo $pop['pl_f'];?>"
                                data-mini="true">
                            %</td>
                        <td align="center" class="pequeno2"><input name="pl_c" type="number" step="any"
                                class="total_spz" id="pl_c" tabindex="10" value="<?php echo $pop['pl_c'];?>"
                                data-mini="true">
                            %</td>
                    </tr>
                    <tr>
                        <td>Progresivo No Lineal</td>
                        <td colspan="2" align="center" class="pequeno2"><input name="pnl_f" type="number" step="any"
                                required class="total_spz" id="pnl_f" tabindex="4" value="<?php echo $pop['pnl_f'];?>"
                                data-mini="true">
                            %</td>
                        <td align="center" class="pequeno2"><input name="pnl_c" type="number" step="any"
                                class="total_spz" id="pnl_c" tabindex="11" value="<?php echo $pop['pnl_c'];?>"
                                data-mini="true">
                            %</td>
                    </tr>
                    <tr>
                        <td>Insitu</td>
                        <td colspan="2" align="center" class="pequeno2"><input name="ins_f" type="number" step="any"
                                required class="total_spz" id="ins_f" tabindex="5" value="<?php echo $pop['ins_f'];?>"
                                data-mini="true">
                            %</td>
                        <td align="center" class="pequeno2"><input name="ins_c" type="number" step="any"
                                class="total_spz" id="ins_c" tabindex="12" value="<?php echo $pop['ins_c'];?>"
                                data-mini="true">
                            %</td>
                    </tr>
                    <tr>
                        <td>No Motil</td>
                        <td colspan="2" align="center" class="pequeno2"><input name="inm_f" type="number" step="any"
                                required id="inm_f" tabindex="6" value="<?php echo $pop['inm_f'];?>" readonly
                                data-mini="true">
                            %</td>
                        <td align="center" class="pequeno2"><input name="inm_c" type="number" step="any" id="inm_c"
                                value="<?php echo $pop['inm_c'];?>" readonly data-mini="true">
                            %</td>
                    </tr>
                    <tr>
                        <td>Observaciones</td>
                        <td colspan="3" align="center"><textarea name="obs" id="obs"
                                data-mini="true"><?php echo $pop['obs'] ? : '';?></textarea></td>
                    </tr>
                </table>

                <table width="100%" align="center" style="margin: 0 auto; font-size: small;">
                    <tr>
                        <td colspan="4" align="center" class="ui-bar-a">Criopreservado</td>
                    </tr>

                    <tr>
                        <td width="7%" class="crio">Cuaderno</td>
                        <td width="19%" class="crio">
                            <input type="number" name="cuaderno" id="cuaderno" value="<?php echo $pop['cuaderno'];?>"
                                style="text-align: center;" placeholder="##">
                        </td>
                    </tr>

                    <tr>
                        <td width="7%" class="crio">Página</td>
                        <td width="19%" class="crio">
                            <input type="number" name="pagina" id="pagina" value="<?php echo $pop['pagina'];?>"
                                style="text-align: center;" placeholder="##">
                        </td>
                    </tr>

                    <tr>
                        <td width="7%" class="crio">Traslado</td>
                        <td width="19%" class="crio">
                            <select name="tra" id="tra" data-mini="true">
                                <option value=0 <?php if ($pop['tra']==0) echo "selected"; ?>>No</option>
                                <option value=1 <?php if ($pop['tra']==1) echo "selected"; ?>>Si</option>
                            </select>
                            <?php
							if (file_exists("pare/".$p_dni."/crio_traslado_".$id.".pdf"))
								echo "<a href='archivos_hcpacientes.php?idPare=".$p_dni."/crio_traslado_".$id.".pdf' target='new'>Ver Documento</a>"; ?>
                        </td>
                        <td width="32%" rowspan="7" align="center" valign="top" class="varillas_cel"></td>
                        <td width="42%" rowspan="7" align="center" valign="top">
                            <?php
								if ($id<>"") {
									$rRes = $db->prepare("SELECT
										t.n_tan t,r.c,r.v,r.p
										from lab_tanque_res r
										inner join lab_tanque t on t.tan = r.t
										WHERE r.sta=? and r.tip=? and r.tip_id=?");
									$rRes->execute(array($p_dni,2,$id)); // 1=bio_tes 2=crio_sem

									$Rcap = $db->prepare("SELECT des FROM lab_andro_cap WHERE des_dni=? and des_tip=? and des_fec=? and eliminado is false");
									$Rcap->execute(array($p_dni,2,$id)); // 1=bio_tes 2=crio_sem

									if ($Rcap->rowCount()>0) {
										echo '<table bordercolor="#72a2aa" style="text-align:center"><tr class="ui-bar-b"> <td colspan="4">Viales Descongelados:</td></tr>
										<tr class="ui-bar-c"><td>T</td><td>C</td><td>Varilla</td><td>Vial</td></tr>';
								
										$bg='class="chk_crio"';
								
										while($cap = $Rcap->fetch(PDO::FETCH_ASSOC)) {
											$des = explode('|' , $cap['des']);
											
											foreach($des as $vial) {
												if ($vial) {
													$ds = explode('-' , $vial);
													echo '<tr> <td> '.$ds[0].'</td><td> '.$ds[1].'</td><td '.$bg.'> '.$ds[2].'</td><td '.$bg.'> '.$ds[3].'</td></tr>';
												}
											}
										}

										echo '</table>';
									} ?>

                            <p>
                            <table bordercolor="#72a2aa" style="text-align:center">
                                <tr class="ui-bar-b">
                                    <td colspan="5">Viales Criopreservados:</td>
                                </tr>
                                <tr class="ui-bar-c">
                                    <td>T</td>
                                    <td>C</td>
                                    <td>Varilla</td>
                                    <td>Vial</td>
                                </tr>

                                <?php
											while($res = $rRes->fetch(PDO::FETCH_ASSOC)) {  ?>
                                <tr>
                                    <td><?php echo $res["t"];?></td>
                                    <td><?php echo $res["c"];?></td>
                                    <td class="chk_crio"><?php echo $res["v"];?></td>
                                    <td class="chk_crio"><?php echo $res["p"];?></td>
                                </tr>
                                <?php } ?>
                            </table>
                            </p>
                            <?php } ?>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" class="crio"><input name="doc" id="doc" type="file" accept="application/pdf"
                                data-mini="true" /></td>
                    </tr>

                    <tr>
                        <td width="7%">Volumen </td>
                        <td width="19%" class="pequeno2"><input name="vol" type="number" step="any" required id="vol"
                                value="<?php echo $pop['vol'];?>" data-mini="true">
                            (ml)</td>
                    </tr>

                    <tr>
                        <td># Viales</td>
                        <td class="pequeno2"><input name="via" type="range" id="via" min="0" max="10"
                                data-show-value="true" value="<?php echo $pop['via'];?>" data-popup-enabled="true"
                                data-highlight="true"></td>
                    </tr>

                    <tr>
                        <td class="pequeno2">Metodo</td>
                        <td class="pequeno2"><select name="met" required id="met" data-mini="true">
                                <option value=1 <?php if ($pop['met']==1 or $pop['met']=="") echo "selected"; ?>>Crio
                                    Vial</option>
                                <option value=2 <?php if ($pop['met']==2) echo "selected"; ?>>Pajuela</option>
                                <option value=3 <?php if ($pop['met']==3) echo "selected"; ?>>Perla</option>
                            </select></td>
                    </tr>

                    <tr>
                        <td class="pequeno2">Tanque</td>
                        <td class="pequeno2"><select name="c_tan" required id="c_tan" data-mini="true">
                                <option value="" selected>---</option>
                                <?php  while ($tan = $rTan->fetch(PDO::FETCH_ASSOC)) { ?>
                                <option value="<?php echo $tan['tan']; ?>"><?php echo $tan['n_tan']; ?></option>
                                <?php } ?>
                            </select></td>
                    </tr>

                    <tr>
                        <td><span class="pequeno2">Canister</span></td>
                        <td class="pequeno2"><select name="c_can" required id="c_can" data-mini="true"> </select></td>
                    </tr>
                </table>

                <?php //if ($login=="lab" and $pop['emb']=="") { 
					if ($_SESSION['role'] == 2) { ?>
                <input type="Submit" name="guardar" value="GUARDAR" data-icon="check" data-iconpos="left"
                    data-mini="true" data-theme="b" data-inline="true" />
                <?php } ?>
            </form>

            <div data-role="popup" id="cargador" data-overlay-theme="b" data-dismissible="false">
                <p>GUARDANDO DATOS..</p>
            </div>
        </div>

        <?php } ?>
    </div>
</body>

</html>