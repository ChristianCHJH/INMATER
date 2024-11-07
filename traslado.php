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
</head>
<body>
    <div data-role="page" class="ui-responsive-panel" id="traslado">
        <?php
        $stmt = $db->prepare("SELECT role FROM usuario WHERE userx=?");
        $stmt->execute(array($login));
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (isset($_POST['conf']) and !empty($_POST['conf'])) {
            $stmt = $db->prepare("DELETE FROM hc_antece_trata WHERE id=?");
            $stmt->execute(array($_POST['conf']));
        }

        if (isset($_POST['dni']) and !empty($_POST['dni']) and isset($_POST['cri']) and !empty($_POST['cri']) and isset($_POST['fec']) and !empty($_POST['fec'])) {
            updateAnte_trata('', $_POST['dni'], $_POST['fec'], $_POST['pro'], $_POST['med'], $_POST['medica'], $_POST['fol'], $_POST['ovo'], $_POST['emb'], $_POST['dia'], $_POST['cri'], $_POST['res'], 'x');
						print('
							<div class="alert alert-success" id="confirm-success">
								<b>Excelente!</b> Agregamos correctamente un Traslado.
							</div>');						
        }

        $Rpop = $db->prepare("SELECT
					nom, ape, hc_paciente.med, hc_antece_trata.id, hc_antece_trata.dni, hc_antece_trata.cri, hc_antece_trata.fec
					FROM hc_antece_trata, hc_paciente
					WHERE hc_antece_trata.dni=hc_paciente.dni AND hc_antece_trata.tras=1 AND hc_antece_trata.eliminado=0
					ORDER BY hc_antece_trata.fec DESC");
        $Rpop->execute(); ?>

        <style>
            .ui-dialog-contain {
                max-width: 1500px;
                padding: 0;
                position: relative;
                top: -35px;
            }

            .peke2 .ui-input-text {
                width: 120px !important;
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
        </style>

        <script>
            function anular(x) {
                document.form2.conf.value = x;
                document.form2.dni.value = ""; //para que no inserte registros
                document.form2.submit();
            }

            $(document).ready(function () {
							$(".ui-input-search input").attr("id", "paci_nom");
            });
        </script>

        <div data-role="header" data-position="fixed">
            <a href="lista_facturacion.php" rel="external" class="ui-btn ui-btn-inline ui-mini ui-corner-all ui-btn-icon-left ui-icon-delete">Cerrar</a>
            <h3>Traslados</h3>
        </div>

        <div class="ui-content" role="main">
            <form action="" method="post" data-ajax="false" name="form2">
                <input type="hidden" name="conf">
                <input type="hidden" name="dni" id="dni">

                <div data-role="collapsibleset" data-theme="a" data-content-theme="a" data-mini="true">
                    <?php
                    if ($data["role"] == 3 or $data["role"] == 10) { ?>
                        <div data-role="collapsible"><h3>AGREGAR TRASLADO</h3>
                            <div class="scroll_h">
															<div id="lista_pacientes">
																<ul
																	data-role="listview"
																	data-theme="c"
																	data-inset="true"
																	data-filter="true"
																	data-filter-reveal="true"
																	data-filter-placeholder="Buscar paciente por Nombre o DNI..."
																	data-mini="true" class="fil_paci">
																</ul>
															</div>

                                <table width="100%" align="center">
                                    <tr>
                                        <td width="268">Fecha de Traslado*</td>
                                        <td width="269">
                                            <input name="fec" type="date" id="fec" data-mini="true" required>
                                        </td>
                                        <td width="211">Medicamentos</td>
                                        <td width="607">
                                            <textarea name="medica" id="medica" data-mini="true"></textarea>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Nº Folículos</td>
                                        <td>
                                            <input name="fol" type="number" min="0" id="fol" data-mini="true">
                                        </td>
                                        <td>Nº Ovulos aspirados</td>
                                        <td>
                                            <input name="ovo" type="number" min="0" id="ovo" data-mini="true">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Nº Embriones transferidos</td>
                                        <td>
                                            <input name="emb" type="number" min="0" id="emb" data-mini="true">
                                        </td>
                                        <td>Día de transferencia</td>
                                        <td>
                                            <select name="dia" id="dia" data-mini="true">
                                                <option value="">---</option>
                                                <option value=0>Dia 0</option>
                                                <option value=1>Dia 1</option>
                                                <option value=2>Dia 2</option>
                                                <option value=3>Dia 3</option>
                                                <option value=4>Dia 4</option>
                                                <option value=5>Dia 5</option>
                                                <option value=6>Dia 6</option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Nº Ovulos y/o embriones Criopreservados*</td>
                                        <td>
                                            <input name="cri" type="number" min="0" id="cri" data-mini="true" required>
                                        </td>
                                        <td>Resultado</td>
                                        <td>
                                            <input name="res" type="text" id="res" data-mini="true">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Médico</td>
                                        <td><input name="med" type="text" id="med" data-mini="true">
                                        </td>
                                        <td>Procedimiento</td>
                                        <td><textarea name="pro" id="pro" data-mini="true"></textarea></td>
                                    </tr>
                                </table>
                            </div>
                            <input type="Submit" name="guardar" value="AGREGAR" data-icon="check" data-iconpos="left" data-mini="true" data-theme="b" data-inline="true"/>
                        </div>
                    <?php } ?>

                    <div data-role="collapsible" data-collapsed="false"><h3>TRASLADOS EN PROCESO</h3>
                        <?php if ($Rpop->rowCount() > 0) { ?>
                        <div class="scroll_h">
                            <table width="100%" align="center">
                                <thead>
                                <tr>
                                    <th align="left">Apellidos y Nombres</th>
                                    <th>DNI</th>
                                    <th>Médico</th>
                                    <th># Crios</th>
                                    <th>Fecha traslado</th>
                                    <?php
                                    if ($data["role"] == 3 or $data["role"] == 10) { ?>
                                        <th></th>
                                    <?php } ?>
                                </tr>
                                </thead>

                                <?php
                                while ($pop = $Rpop->fetch(PDO::FETCH_ASSOC)) { ?>
                                    <tr style="font-size: small;">
                                        <td align="left"><?php echo $pop['ape'] . ' ' . $pop['nom']; ?></td>
                                        <td align="center"><?php echo $pop['dni']; ?></td>
                                        <td align="center"><?php echo $pop['med']; ?></td>
                                        <td align="center"><?php echo $pop['cri']; ?></td>
                                        <td align="center"><?php echo date("d-m-Y", strtotime($pop['fec'])); ?></td>
                                        <?php
                                        if ($data["role"] == 3 or $data["role"] == 10) { ?>
                                            <td align="center"><a href="javascript:anular(<?php echo $pop['id']; ?>);">CANCELAR</a></td>
                                        <?php } ?>
                                    </tr>
                                <?php } ?>
                            </table>
                        </div>
                        <?php } else echo '<p><h4>No hay Traslados en proceso</h4></p>'; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(function () {
            $("#alerta").prependTo(".ui-content");
            $('#alerta').delay(3000).fadeOut('slow');
        });

			$(document).on('input paste', '#lista_pacientes .ui-input-search', function(e) {
				var paciente = $('#lista_pacientes .ui-input-search :input')[0].value;

				if (paciente.length > 3) {
					$.post("le_tanque.php", {carga_paci_det: paciente}, function (data) {
						$("#lista_pacientes ul").html("");
						$("#lista_pacientes ul").append(data);
						$('.ui-page').trigger('create');
					});
				}
			});

			$(document).on('click', '.paci_insert', function(e) {
				$('.paci_insert').click(function (e) {
					$('#paci_nom').val($(this).text());
					$('#dni').val($(this).attr("dni"));
					$('#paci_nom').textinput('refresh');
					$('.fil_paci li').addClass('ui-screen-hidden');
					$('#paci_nom').focus();
				});
			});
    </script>
</body>
</html>