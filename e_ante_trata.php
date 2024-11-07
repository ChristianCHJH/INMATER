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
	<title>Clínica Inmater | Tratamientos fuera de Inmater</title>
	<script src="js/jquery-1.11.1.min.js"></script>
	<script src="js/jquery.mobile-1.4.5.min.js"></script>
</head>
<body>
	<div data-role="page" class="ui-responsive-panel">
			<?php
			if (isset($_POST['dni']) and !empty($_POST['dni']) and isset($_POST['fec'])) {
				$stmt = $db->prepare("INSERT INTO hc_antece_trata (dni,fec,pro,med,medica,fol,ovo,emb,dia,cri,res,tras) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
				$stmt->execute([
					$_POST["dni"],
					$_POST["fec"],
					$_POST["pro"],
					$_POST["med"],
					$_POST["medica"],
					$_POST["fol"],
					$_POST["ovo"],
					$_POST["emb"],
					$_POST["dia"],
					$_POST["cri"],
					$_POST["res"],
					$_POST["tras"]
				]);
				print('
					<div class="alert alert-success" id="confirm-success">
						<b>Excelente!</b> Agregamos correctamente un Tratamiento fuera de Inmater.
					</div>');
			}
			if (isset($_GET['dni']) and !empty($_GET['dni'])) {
					$dni = $_GET['dni'];
					$id = $_GET['id'];

					$Rpop = $db->prepare("SELECT * FROM hc_antece_trata WHERE id=?");
					$Rpop->execute(array($id ? : 0));
					$pop = $Rpop->fetch(PDO::FETCH_ASSOC); ?>

					<style>
							.ui-dialog-contain {
									max-width: 1500px;
									padding: 0;
									position: relative;
									top: -15px;

							}

							.scroll_h {
									overflow-x: scroll;
									overflow-y: hidden;
									white-space: nowrap;
							}
					</style>

					<div data-role="header" data-position="fixed">
						<h3>Tratamientos de reproducción asistida anteriores</h3>
						<?php print('<a href="e_paci.php?id='.$dni.'" rel="external" class="ui-btn">Cerrar</a>'); ?>
					</div>

					<div class="ui-content" role="main">
							<h4>(NO realizados en INMATER)</h4>
							<form action="" method="post" data-ajax="false" name="form2">
									<input type="hidden" name="idx" value="<?php echo $id; ?>">
									<input type="hidden" name="dni" value="<?php echo $dni; ?>">

									<table width="100%" align="center" style="margin: 0 auto;max-width:800px;">
											<tr>
													<td width="268">Fecha</td>
													<td width="269">
															<input name="fec" type="date" id="fec" data-mini="true" value="<?php echo $pop['fec']; ?>">
													</td>
													<td width="211">Medicamentos</td>
													<td width="607">
															<textarea name="medica" id="medica" data-mini="true"><?php if(isset($pop['medica']))echo $pop['medica']; ?></textarea>
													</td>
											</tr>
											<tr>
													<td>Nº Folículos</td>
													<td>
															<input name="fol" type="number" min="0" id="fol" data-mini="true" value="<?php if(isset($pop['fol']))echo $pop['fol']; ?>">
													</td>
													<td>Nº Ovulos aspirados</td>
													<td>
															<input name="ovo" type="number" min="0" id="ovo" data-mini="true" value="<?php if(isset($pop['ovo']))echo $pop['ovo']; ?>">
													</td>
											</tr>
											<tr>
													<td>Nº Embriones transferidos</td>
													<td>
															<input name="emb" type="number" min="0" id="emb" data-mini="true" value="<?php if(isset($pop['emb']))echo $pop['emb']; ?>">
													</td>
													<td>Día de transferencia</td>
													<td>
															<select name="dia" id="dia" data-mini="true">
																	<option value="">---</option>
																	<option value=0 <?php if(isset($pop['dia']))if ($pop['dia']==0) echo 'selected'; ?>>Dia 0</option>
																	<option value=1 <?php if(isset($pop['dia']))if ($pop['dia']==1) echo 'selected'; ?>>Dia 1</option>
																	<option value=2 <?php if(isset($pop['dia']))if ($pop['dia']==2) echo 'selected'; ?>>Dia 2</option>
																	<option value=3 <?php if(isset($pop['dia']))if ($pop['dia']==3) echo 'selected'; ?>>Dia 3</option>
																	<option value=4 <?php if(isset($pop['dia']))if ($pop['dia']==4) echo 'selected'; ?>>Dia 4</option>
																	<option value=5 <?php if(isset($pop['dia']))if ($pop['dia']==5) echo 'selected'; ?>>Dia 5</option>
																	<option value=6 <?php if(isset($pop['dia']))if ($pop['dia']==6) echo 'selected'; ?>>Dia 6</option>
															</select>
													</td>
											</tr>
											<tr>
													<td>Nº Ovulos y/o embriones Criopreservados</td>
													<td>
															<input name="cri" type="number" min="0" id="cri" data-mini="true" value="<?php if(isset($pop['cri']))echo $pop['cri']; ?>">
													</td>
													<td>Resultado</td>
													<td>
															<input name="res" type="text" id="res" data-mini="true" value="<?php if(isset($pop['res']))echo $pop['res']; ?>">
													</td>
											</tr>
											<tr>
													<td>Médico</td>
													<td><input name="med" type="text" id="med" data-mini="true" value="<?php if(isset($pop['med']))echo $pop['med']; ?>"></td>
													<td>Procedimiento</td>
													<td><textarea name="pro" id="pro" data-mini="true"><?php if(isset($pop['pro']))echo $pop['pro']; ?></textarea></td>
											</tr>
											<tr>
													<td colspan="2">Solicitar TRASLADO de Ovulos / Embriones ?</td>
													<td>
														<?php
														if (isset($pop['tras']) && $pop['tras'] == 2) {
															echo 'TRASLADO FINALIZADO <input type="hidden" name="tras" value=2>';
														} else { ?>
															<select name="tras" id="tras" data-mini="true">
																	<option value=0 <?php if(isset($pop['tras']))if ($pop['tras'] == 0) echo "selected"; ?>>No</option>
																	<option value=1 <?php if(isset($pop['tras']))if ($pop['tras'] == 1) echo "selected"; ?>>Si</option>
															</select>
														<?php }
														if(isset($pop['tras']))if ($pop['tras'] == 1) echo 'EN PROCESO..'; ?>
														</td>
													<td>&nbsp;</td>
											</tr>
									</table>

									<input type="Submit" name="guardar" value="GUARDAR" data-icon="check" data-iconpos="left"
												data-mini="true" class="show-page-loading-msg" data-textonly="false" data-textvisible="true"
												data-msgtext="Guardando datos.." data-theme="b" data-inline="true"/>
							</form>

					</div>
			<?php } ?>
	</div>
	<script>
			$(document).on("click", ".show-page-loading-msg", function () {
					if (document.getElementById("fec").value == "") {
							alert("Debe especificar la Fecha");
							return false;
					}
					if (document.getElementById("pro").value == "") {
							alert("Debe especificar el Procedimiento");
							return false;
					}
					if (document.getElementById("ovo").value == "") {
							alert("Debe especificar el Nº Ovulos aspirados");
							return false;
					}
					if (document.getElementById("tras").value == 1) {
							if (document.getElementById("cri").value == "") {
									alert("Debe especificar el Nº Ovulos y/o embriones Criopreservados");
									return false;
							}
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
			}).on("click", ".hide-page-loading-msg", function () {
					$.mobile.loading("hide");
			});


	</script>
</body>
</html>
