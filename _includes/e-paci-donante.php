<?php
if ($paci["don"] == "D") {
?>
<div data-role="collapsible">
	<h3>Datos de Donante</h3>
	<div class="scroll_h">
		<table width="100%" align="center" style="margin: 0 auto; max-width:800px;">
			<tr>
				<td>Nivel de Instrucción</td>
				<td>
					<select name="nivel_instruccion" id="nivel_instruccion" data-mini="true">
						<option value="" selected>SELECCIONAR</option>
						<?php
						$stmt = $db->prepare("SELECT id, nombre from man_nivel_instruccion where estado=1;");
						$stmt->execute();
						while ($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
							print('<option value="'.$item["id"].'" '. ($item['id'] == $paci["nivel_instruccion_id"] ? "selected" : "") .'>' . mb_strtoupper($item['nombre']) . '</option>');
						} ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>ICQ</td>
				<td>
					<input type="number" name="icq" id="icq" value="<?php echo $paci['icq']; ?>" data-mini="true">
				</td>
			</tr>
			<tr>
				<td>Color Cabello</td>
				<td>
					<select name="color_cabello" id="color_cabello" data-mini="true">
						<option value="" selected>SELECCIONAR</option>
						<?php
						$stmt = $db->prepare("SELECT id, nombre from man_color_cabello where estado=1;");
						$stmt->execute();
						while ($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
							print('<option value="'.$item["id"].'" '. ($item['id'] == $paci["color_cabello_id"] ? "selected" : "") .'>' . mb_strtoupper($item['nombre']) . '</option>');
						} ?>
					</select>
				</td>
				<td>Color Ojos</td>
				<td>
					<select name="color_ojos" id="color_ojos" data-mini="true">
						<option value="" selected>SELECCIONAR</option>
						<?php
						$stmt = $db->prepare("SELECT id, nombre from man_color_ojos where estado=1;");
						$stmt->execute();
						while ($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
							print('<option value="'.$item["id"].'" '. ($item['id'] == $paci["color_ojos_id"] ? "selected" : "") .'>' . mb_strtoupper($item['nombre']) . '</option>');
						} ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Foto de infancia 1</td>
				<td>
					<?php
					if (!file_exists("paci/" . $paci['dni'] . "/donante_foto1.jpg")) { $foto_donante1 = "_images/foto.gif"; }
					else { $foto_donante1 = "paci/" . $paci['dni'] . "/donante_foto1.jpg"; } ?>
					<a href="#popup_donante_foto1"
						data-rel="popup"
						data-position-to="window"
						data-transition="fade">
						<img src="<?php echo $foto_donante1; ?>"
							width="100px"
							height="100px" id="preview"/>
					</a>
					<div data-role="popup"
						id="popup_donante_foto1"
						data-overlay-theme="b"
						data-theme="b"
						data-corners="false">
						<a href="#" data-rel="back"
							class="ui-btn ui-corner-all ui-shadow ui-btn-a ui-icon-delete ui-btn-icon-notext ui-btn-right">Close</a>
							<img src="<?php echo $foto_donante1; ?>" style="max-height:512px;">
					</div>
					<input name="donante_foto1" type="file" accept="image/jpeg"/>
				</td>
			</tr>
			<tr>
				<td>Foto de infancia 2</td>
				<td>
					<?php
					if (!file_exists("paci/" . $paci['dni'] . "/donante_foto2.jpg")) { $foto_donante2 = "_images/foto.gif"; }
					else { $foto_donante2 = "paci/" . $paci['dni'] . "/donante_foto2.jpg"; } ?>
					<a href="#popup_donante_foto2"
						data-rel="popup"
						data-position-to="window"
						data-transition="fade">
						<img src="<?php echo $foto_donante2; ?>"
							width="100px"
							height="100px" id="preview"/>
					</a>
					<div data-role="popup"
						id="popup_donante_foto2"
						data-overlay-theme="b"
						data-theme="b"
						data-corners="false">
						<a href="#" data-rel="back"
							class="ui-btn ui-corner-all ui-shadow ui-btn-a ui-icon-delete ui-btn-icon-notext ui-btn-right">Close</a>
							<img src="<?php echo $foto_donante2; ?>" style="max-height:512px;">
					</div>
					<input name="donante_foto2" type="file" accept="image/jpeg"/>
				</td>
			</tr>
			<tr>
				<td>Evaluacion Psicologica</td>
				<td>
					<?php
					if (!file_exists("paci/" . $paci['dni'] . "/donante_evaluacion_psicologica.jpg")) { $foto_donante_evaluacion_psicologica = "_images/foto.gif"; }
					else { $foto_donante_evaluacion_psicologica = "paci/" . $paci['dni'] . "/donante_evaluacion_psicologica.jpg"; } ?>
					<a href="#popup_donante_evaluacion_psicologica"
						data-rel="popup"
						data-position-to="window"
						data-transition="fade">
						<img src="<?php echo $foto_donante_evaluacion_psicologica; ?>"
							width="100px"
							height="100px" id="preview"/>
					</a>
					<div data-role="popup"
						id="popup_donante_evaluacion_psicologica"
						data-overlay-theme="b"
						data-theme="b"
						data-corners="false">
						<a href="#" data-rel="back"
							class="ui-btn ui-corner-all ui-shadow ui-btn-a ui-icon-delete ui-btn-icon-notext ui-btn-right">Close</a>
							<img src="<?php echo $foto_donante_evaluacion_psicologica; ?>" style="max-height:512px;">
					</div>
					<input name="donante_evaluacion_psicologica" type="file" accept="image/jpeg"/>
				</td>
			</tr>
			<tr>
				<td>Cariotipo</td>
				<td>
					<?php
					if (!file_exists("paci/" . $paci['dni'] . "/donante_cariotipo.jpg")) { $foto_donante_cariotipo = "_images/foto.gif"; }
					else { $foto_donante_cariotipo = "paci/" . $paci['dni'] . "/donante_cariotipo.jpg"; } ?>
					<a href="#popup_donante_cariotipo"
						data-rel="popup"
						data-position-to="window"
						data-transition="fade">
						<img src="<?php echo $foto_donante_cariotipo; ?>"
							width="100px"
							height="100px" id="preview"/>
					</a>
					<div data-role="popup"
						id="popup_donante_cariotipo"
						data-overlay-theme="b"
						data-theme="b"
						data-corners="false">
						<a href="#" data-rel="back"
							class="ui-btn ui-corner-all ui-shadow ui-btn-a ui-icon-delete ui-btn-icon-notext ui-btn-right">Close</a>
							<img src="<?php echo $foto_donante_cariotipo; ?>" style="max-height:512px;">
					</div>
					<input name="donante_cariotipo" type="file" accept="image/jpeg"/>
				</td>
			</tr>
		</table>
	</div>
</div>
<?php } ?>