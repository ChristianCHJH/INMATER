<?php
	if (is_null($repro['des_dia']) || $repro['des_dia']==0) {
		echo '<div data-role="collapsible" data-collapsed="false"><h3>Seguimiento Ovulatorio</h3>';
	}else{
		echo '<div data-role="collapsible" data-collapsed="false"><h3>Preparacion Endometrial</h3>';
	}
?>

<style>
	#result{
		color: #72A2AA;
		font-weight: 700;
		text-transform: uppercase;
		text-align: center;
		background-color:transparent;
	}
	label,h3{
		text-align: center;
	}
	
</style>

	<?php 
		if(empty($iniciclo[0]['dia'])){ ?>

		<div style="width: 100%; padding-bottom: 10px;">
			<div style="width: 250px;">
				<div><span class="title-secondary-bold">Fecha de ultima regla (FUR): </span></div>
				<div class="in-input"><input type="date" data-mini="true" id="con_fec0" name="con_fec0" onchange="anular(this.value,'<?php echo $id; ?>');"></div>
			</div>
		</div>

	<?php } else { ?>
	
	<table width="100%" align="center" style="margin: 0 auto;" class="peke">
		<tr>
			<td width="43%"  id="result"></td>
			<?php if (is_null($repro['des_dia'])) { ?>
				<td width="11%">Número Folículos</td>
				<td width="12%" class="peke2">
					<input name="n_fol" type="number" id="n_fol" min="0" value="<?php echo $repro['n_fol']; ?>" data-mini="true" <?='onchange="updateMedicamento(this,\''.$id.'\',\''.$login.'\',\'n_fol\');"'?>>
				</td>
			<?php } ?>
			<?php 
			if (!is_null($repro['des_dia'])) { ?>
				<td width="3%">FECHA AMPOLLA LORELINA</td>
				<td width="12%">
					<input type="date" name="fecha_lorelina" id="fecha_lorelina" value="<?php echo $repro['fecha_lorelina']; ?>" data-mini="true">
				</td>
			<?php } ?>
			
			<td width="3%">FUR</td>
			<td width="12%">
				<input type="date" name="fur" id="fur" value="<?php echo $repro['inc_fech']; ?>" data-mini="true" readonly>
			</td>
			<td width="7%">1era ACO</td>
			<td width="12%">
				<input type="date" name="f_aco" id="f_aco" value="<?php echo $repro['f_aco']; ?>" data-mini="true" <?='onchange="updateMedicamento(this,\''.$id.'\',\''.$login.'\',\'f_aco\');"'?>>
			</td>
		</tr>
	</table>

	<?php
	$con_iny = explode("|", $repro['con_iny']);
	$con_obs = explode("|", $repro['con_obs']);
	$Tcon1_med = $Tcon2_med = $Tcon3_med = $Tcon4_med = $Tcon5_med = $Tcon6_med = $Tcon7_med = $Tcon_iny = 0;

	if (sizeof($con_iny) != 1 || $con_iny != "") { $Tcon_iny = array_sum( $con_iny ); } ?>

	<div id="wrap">
		<table id="data" cellspacing="0" cellpadding="0" align="center" style="margin: 0 auto;font-size: small;" class="columnahc">
			<tr>
				<td class="column-sticky">DIA CICLO</td>
				<td></td>
				<?php
				for ($i = 1; $i <= $total_semanas; $i++) {
					print('<td><div style="display:flex; justify-content:space-around;padding:10px;"><strong>'.$i.'</strong></div></td>');
				} ?>
			</tr>
			<tr>
				<td class="column-sticky">DIA MES </td>
				<td></td>
				<?php

				$fecha = !empty($iniciclo[0]['dia']) ? DateTime::createFromFormat('Y-m-d', $iniciclo[0]['dia']) : '';
				for ($i = 0; $i < $total_semanas; $i++) {
					$fecha_format = $fecha->format('d-m-Y');
					print('<td><input type="text" readonly data-mini="true" name="con_fec'.$i.'" id="con_fec'.$i.'" value="'.$fecha_format.'"></td>');
					$fecha->add(new DateInterval('P1D')); 
				}

				?>
			</tr>
			<?php
			if (is_null($repro['des_dia']) || $repro['des_dia']==0) { ?>
				<tr>
					<td class="column-sticky">O.D.</td>
					<td></td>
					<?php
					$ciclo_data = [];
					$ciclo_medico = [];
					if(isset($ciclo))foreach ($ciclo as $dato) {
						$ciclo_data[$dato['dia_ciclo']] = $dato['ovulo_derecho']??"";
						$ciclo_medico[$dato['dia_ciclo']] = $dato['medico'];
					}
					for ($i = 1; $i <= $total_semanas; $i++) {
						$readonly = '';
						if (array_key_exists($i, $ciclo_data) && !is_null($ciclo_data[$i])) {
							$ovulo_derecho = $ciclo_data[$i];
							if ($ciclo_medico[$i]) {
								if($ciclo_medico[$i] == $login){
									$readonly = '';
								}else{
									$readonly = 'readonly';
								}
							}
						} else {
							$ovulo_derecho = "";
						}
						
						print('<td><input type="text" name="con_od'.$i.'" id="con_od'.$i.'" value="'.$ovulo_derecho.'" data-mini="true" onchange="updateCiclo(this,\''.$i.'\',\''.$id.'\',\''.$login.'\',\'ovulo_derecho\');" '.$readonly.' ></td>');
					} ?>
				</tr>
				<tr>
					<td class="column-sticky">O.I.</td>
					<td></td>
					<?php
					$ciclo_data = [];
					$ciclo_medico = [];
					if(isset($ciclo))foreach ($ciclo as $dato) {
						$ciclo_data[$dato['dia_ciclo']] = $dato['ovulo_izquierdo']??"";
						$ciclo_medico[$dato['dia_ciclo']] = $dato['medico'];
					}
					for ($i = 1; $i <= $total_semanas; $i++) {
						$readonly = '';
						if (array_key_exists($i, $ciclo_data) && !is_null($ciclo_data[$i])) {
							$ovulo_izquierdo = $ciclo_data[$i];
							if ($ciclo_medico[$i]) {
								if($ciclo_medico[$i] == $login){
									$readonly = '';
								}else{
									$readonly = 'readonly';
								}
							}
						} else {
							$ovulo_izquierdo = "";
						}
						print('<td><input type="text" name="con_oi'.$i.'" id="con_oi'.$i.'" value="'.$ovulo_izquierdo.'" data-mini="true" onchange="updateCiclo(this,\''.$i.'\',\''.$id.'\',\''.$login.'\',\'ovulo_izquierdo\');" '.$readonly.' ></td>');
					} ?>
				</tr>
			<?php } ?>
			<tr>
				<td class="column-sticky">ENDOMETRIO</td>
				<td></td>
				<?php
				$ciclo_data = [];
				$ciclo_medico = [];
				if(isset($ciclo))foreach ($ciclo as $dato) {
					$ciclo_data[$dato['dia_ciclo']] = $dato['endometrio']??"";
					$ciclo_medico[$dato['dia_ciclo']] = $dato['medico'];
				}
				for ($i = 1; $i <= $total_semanas; $i++) {
						$readonly = '';
						if (array_key_exists($i, $ciclo_data) && !is_null($ciclo_data[$i])) {
						$endometrio = $ciclo_data[$i];
						if ($ciclo_medico[$i]) {
							if($ciclo_medico[$i] == $login){
								$readonly = '';
							}else{
								$readonly = 'readonly';
							}
						}
					} else {
						$endometrio = "";
					}
					print('<td><input type="text" class="numeros" name="con_end'.$i.'" id="con_end'.$i.'" value="'.$endometrio.'" data-mini="true"onchange="updateCiclo(this,\''.$i.'\',\''.$id.'\',\''.$login.'\',\'endometrio\');" '.$readonly.' ></td>');
				} ?>
			</tr>
			<tr>
				<td class="column-sticky">
				<select name="medicamento_1" class="chosen-select" id="medicamento_1" data-mini="true" <?='onchange="updateMedicamento(this,\''.$id.'\',\''.$login.'\',\'medicamento1_id\');"'?>>
						<option value=0>MEDICAMENTO 1</option>
						<?php
						$medicamento1_ver="display: none;";
						foreach ($data_medicamentos as $info) { ?>
							<option value=<?php echo $info['value'];
									if ($repro['medicamento1_id'] == $info['value']) echo " selected"; ?>><?php echo $info['text']; ?></option><?php
								} ?>
					</select>
					<div class="ui-select">
					<input style="<?php print($medicamento1_ver); ?>" type="text" name="con1_med0" id="con1_med0" value="<?php echo $ciclo[1]['concentracion1']; ?>" data-mini="true" placeholder="Medicamento 1.." <?='onchange="updateCiclo(this,\''.$i.'\',\''.$id.'\',\''.$login.'\',\'concentracion1\');"'?>>
					</div>
				</td>
				<td><?php echo $Tcon1_med; ?></td>
				<?php
				$ciclo_data = [];
				$ciclo_medico = [];
				if(isset($ciclo))foreach ($ciclo as $dato) {
					$ciclo_data[$dato['dia_ciclo']] = $dato['concentracion1']??"";
					$ciclo_medico[$dato['dia_ciclo']] = $dato['medico'];
				}
				for ($i = 1; $i <= $total_semanas; $i++) {
						$readonly = '';
						if (array_key_exists($i, $ciclo_data) && !is_null($ciclo_data[$i])) {
						$concentracion1 = $ciclo_data[$i];
						if ($repro['medicamento1_id'] == 0) {
							$readonly = 'readonly';
						}else {
							if ($ciclo_medico[$i]) {
								if($ciclo_medico[$i] == $login){
									$readonly = '';
								}else{
									$readonly = 'readonly';
								}
							}
						}

 					} else {
						$concentracion1 = "";
						if ($repro['medicamento1_id'] == 0) {
							$readonly = 'readonly';
						}
					}
					print('<td><input type="text" class="numeros" name="con1_med'.$i.'" id="con1_med'.$i.'" value="'.$concentracion1.'" data-mini="true" onchange="updateCiclo(this,\''.$i.'\',\''.$id.'\',\''.$login.'\',\'concentracion1\');" '.$readonly.' ></td>');
				} ?>
			</tr>
			<tr>
				<td class="column-sticky">
				<select name="medicamento_2" class="chosen-select" id="medicamento_2" data-mini="true" <?='onchange="updateMedicamento(this,\''.$id.'\',\''.$login.'\',\'medicamento2_id\');"'?>>
						<option value=0>MEDICAMENTO 2</option>
						<?php
						$medicamento2_ver="display: none;";
						foreach ($data_medicamentos as $info) { ?>
							<option value=<?php echo $info['value'];
							if ($repro['medicamento2_id'] == $info['value']) echo " selected"; ?>><?php echo $info['text']; ?></option><?php
						} ?>
					</select>
					<div class="ui-select"><input style="<?php print($medicamento2_ver); ?>" type="text" name="con2_med0" id="con2_med0" value="<?php echo $ciclo[0]['concentracion2']; ?>" data-mini="true" placeholder="Medicamento 2.." <?='onchange="updateCiclo(this,\''.$i.'\',\''.$id.'\',\''.$login.'\',\'concentracion2\');"'?>></div>
				</td>
				<td><?php echo $Tcon2_med; ?></td>
				<?php
				$ciclo_data = [];
				$ciclo_medico = [];
				if(isset($ciclo))foreach ($ciclo as $dato) {
					$ciclo_data[$dato['dia_ciclo']] = $dato['concentracion2']??"";
					$ciclo_medico[$dato['dia_ciclo']] = $dato['medico'];
				}
				for ($i = 1; $i <= $total_semanas; $i++) {
						$readonly = '';
						if (array_key_exists($i, $ciclo_data) && !is_null($ciclo_data[$i])) {
						$concentracion2 = $ciclo_data[$i];
						if ($repro['medicamento2_id'] == 0) {
							$readonly = 'readonly';
						}else {
							if ($ciclo_medico[$i]) {
								if($ciclo_medico[$i] == $login){
									$readonly = '';
								}else{
									$readonly = 'readonly';
								}
							}
						}
 					} else {
						$concentracion2 = "";
						if ($repro['medicamento2_id'] == 0) {
							$readonly = 'readonly';
						}
					}
					print('<td><input type="text" class="numeros" name="con2_med'.$i.'" id="con2_med'.$i.'" value="'.$concentracion2.'" data-mini="true" onchange="updateCiclo(this,\''.$i.'\',\''.$id.'\',\''.$login.'\',\'concentracion2\');" '.$readonly.' ></td>');
				} ?>
			</tr>
			<tr>
				<td class="column-sticky">
				<select name="medicamento_3" class="chosen-select" id="medicamento_3" data-mini="true" <?='onchange="updateMedicamento(this,\''.$id.'\',\''.$login.'\',\'medicamento3_id\');"'?>>
						<option value=0>MEDICAMENTO 3</option>
						<?php
						$medicamento3_ver="display: none;";
						foreach ($data_medicamentos as $info) { ?>
							<option value=<?php echo $info['value'];
							if ($repro['medicamento3_id'] == $info['value']) echo " selected"; ?>><?php echo $info['text']; ?></option><?php
						} ?>
					</select>
					<div class="ui-select"><input style="<?php print($medicamento3_ver); ?>" type="text" name="con3_med0" id="con3_med0" value="<?php echo $ciclo[0]['concentracion3']; ?>" data-mini="true" placeholder="Medicamento 3.." <?='onchange="updateCiclo(this,\''.$i.'\',\''.$id.'\',\''.$login.'\',\'concentracion3\');"'?>></div>
				</td>
				<td><?php echo $Tcon3_med; ?></td>
				<?php
				$ciclo_data = [];
				$ciclo_medico = [];
				if(isset($ciclo))foreach ($ciclo as $dato) {
					$ciclo_data[$dato['dia_ciclo']] = $dato['concentracion3']??"";
					$ciclo_medico[$dato['dia_ciclo']] = $dato['medico'];
				}
				for ($i = 1; $i <= $total_semanas; $i++) {
						$readonly = '';
						if (array_key_exists($i, $ciclo_data) && !is_null($ciclo_data[$i])) {
						$concentracion3 = $ciclo_data[$i];
						if ($repro['medicamento3_id'] == 0) {
							$readonly = 'readonly';
						}else {
							if ($ciclo_medico[$i]) {
								if($ciclo_medico[$i] == $login){
									$readonly = '';
								}else{
									$readonly = 'readonly';
								}
							}
						}
					} else {
						$concentracion3 = "";
						if ($repro['medicamento3_id'] == 0) {
							$readonly = 'readonly';
						}
					}
					print('<td><input type="text" class="numeros" name="con3_med'.$i.'" id="con3_med'.$i.'" value="'.$concentracion3.'" data-mini="true" onchange="updateCiclo(this,\''.$i.'\',\''.$id.'\',\''.$login.'\',\'concentracion3\');" '.$readonly.' ></td>');
				} ?>
			</tr>
			<tr>
				<td class="column-sticky">
				<select name="medicamento_4" class="chosen-select" id="medicamento_4" data-mini="true" <?='onchange="updateMedicamento(this,\''.$id.'\',\''.$login.'\',\'medicamento4_id\');"'?>>
						<option value=0>MEDICAMENTO 4</option>
						<?php
						$medicamento4_ver="display: none;";
						foreach ($data_medicamentos as $info) { ?>
							<option value=<?php echo $info['value'];
							if ($repro['medicamento4_id'] == $info['value']) echo " selected"; ?>><?php echo $info['text']; ?></option>
						<?php } ?>
					</select>
					<div class="ui-select"><input style="<?php print($medicamento4_ver); ?>" type="text" name="con4_med0" id="con4_med0" value="<?php echo $ciclo[0]['concentracion4']; ?>" data-mini="true" placeholder="Medicamento 4.." <?='onchange="updateCiclo(this,\''.$i.'\',\''.$id.'\',\''.$login.'\',\'concentracion4\');"'?>></div>
				</td>
				<td><?php echo $Tcon4_med; ?></td>
				<?php
				$ciclo_data = [];
				$ciclo_medico = [];
				if(isset($ciclo))foreach ($ciclo as $dato) {
					$ciclo_data[$dato['dia_ciclo']] = $dato['concentracion4']??"";
					$ciclo_medico[$dato['dia_ciclo']] = $dato['medico'];
				}
				for ($i = 1; $i <= $total_semanas; $i++) {
						$readonly = '';
						if (array_key_exists($i, $ciclo_data) && !is_null($ciclo_data[$i])) {
						$concentracion4 = $ciclo_data[$i];
						if ($repro['medicamento4_id'] == 0) {
							$readonly = 'readonly';
						}else {
							if ($ciclo_medico[$i]) {
								if($ciclo_medico[$i] == $login){
									$readonly = '';
								}else{
									$readonly = 'readonly';
								}
							}
						}
					} else {
						$concentracion4 = "";
						if ($repro['medicamento4_id'] == 0) {
							$readonly = 'readonly';
						}
					}
					print('<td><input type="text" class="numeros" name="con4_med'.$i.'" id="con4_med'.$i.'" value="'.$concentracion4.'" data-mini="true" onchange="updateCiclo(this,\''.$i.'\',\''.$id.'\',\''.$login.'\',\'concentracion4\');" '.$readonly.' ></td>');
				} ?>
			</tr>
			<tr>
			<td class="column-sticky">
			<select name="medicamento_5" class="chosen-select" id="medicamento_5" data-mini="true" <?='onchange="updateMedicamento(this,\''.$id.'\',\''.$login.'\',\'medicamento5_id\');"'?>>
					<option value=0>MEDICAMENTO 5</option>
					<?php
					$medicamento5_ver="display: none;";
					foreach ($data_medicamentos as $info) { ?>
						<option value=<?php echo $info['value'];
						if ($repro['medicamento5_id'] == $info['value']) echo " selected"; ?>><?php echo $info['text']; ?></option>
					<?php } ?>
				</select>
				<div class="ui-select"><input style="<?php print($medicamento5_ver); ?>" type="text" name="con5_med0" id="con5_med0" value="<?php echo $ciclo[0]['concentracion5']; ?>" data-mini="true" placeholder="Medicamento 5.."<?='onchange="updateCiclo(this,\''.$i.'\',\''.$id.'\',\''.$login.'\',\'concentracion5\');"'?>></div>
			</td>
			<td><?php echo $Tcon5_med; ?></td>
				<?php
				$ciclo_data = [];
				$ciclo_medico = [];
				if(isset($ciclo))foreach ($ciclo as $dato) {
					$ciclo_data[$dato['dia_ciclo']] = $dato['concentracion5']??"";
					$ciclo_medico[$dato['dia_ciclo']] = $dato['medico'];
				}
				for ($i = 1; $i <= $total_semanas; $i++) {
						$readonly = '';
						if (array_key_exists($i, $ciclo_data) && !is_null($ciclo_data[$i])) {
						$concentracion5 = $ciclo_data[$i];
						if ($repro['medicamento5_id'] == 0) {
							$readonly = 'readonly';
						}else {
							if ($ciclo_medico[$i]) {
								if($ciclo_medico[$i] == $login){
									$readonly = '';
								}else{
									$readonly = 'readonly';
								}
							}
						}
					} else {
						$concentracion5 = "";
						if ($repro['medicamento5_id'] == 0) {
							$readonly = 'readonly';
						}
					}
					print('<td><input type="text" class="numeros" name="con5_med'.$i.'" id="con5_med'.$i.'" value="'.$concentracion5.'" data-mini="true" onchange="updateCiclo(this,\''.$i.'\',\''.$id.'\',\''.$login.'\',\'concentracion5\');" '.$readonly.' ></td>');
				} ?>
			</tr>
			
			<tr>
			<td class="column-sticky">
			<select name="medicamento_6" class="chosen-select" id="medicamento_6" data-mini="true" <?='onchange="updateMedicamento(this,\''.$id.'\',\''.$login.'\',\'medicamento6_id\');"'?>>
					<option value=0>MEDICAMENTO 6</option>
					<?php
					$medicamento6_ver="display: none;";
					foreach ($data_medicamentos as $info) { ?>
						<option value=<?php echo $info['value']; ?>><?php echo $info['text']; ?></option>
					<?php } ?>
				</select>
				<div class="ui-select"><input style="<?php print($medicamento6_ver); ?>" type="text" name="con6_med0" id="con6_med0" value="<?php echo $ciclo[0]['concentracion6']; ?>" data-mini="true" placeholder="Medicamento 6.."<?='onchange="updateCiclo(this,\''.$i.'\',\''.$id.'\',\''.$login.'\',\'concentracion6\');"'?>></div>
			</td>
			<td><?php echo $Tcon6_med; ?></td>
				<?php
				$ciclo_data = [];
				$ciclo_medico = [];
				if(isset($ciclo))foreach ($ciclo as $dato) {
					$ciclo_data[$dato['dia_ciclo']] = $dato['concentracion6']??"";
					$ciclo_medico[$dato['dia_ciclo']] = $dato['medico'];
				}
				for ($i = 1; $i <= $total_semanas; $i++) {
						$readonly = '';
						if (array_key_exists($i, $ciclo_data) && !is_null($ciclo_data[$i])) {
						$concentracion6 = $ciclo_data[$i];
						if ($repro['medicamento6_id'] == 0) {
							$readonly = 'readonly';
						}else {
							if ($ciclo_medico[$i]) {
								if($ciclo_medico[$i] == $login){
									$readonly = '';
								}else{
									$readonly = 'readonly';
								}
							}
						}
					} else {
						$concentracion6 = "";
						if ($repro['medicamento6_id'] == 0) {
							$readonly = 'readonly';
						}
					}
					print('<td><input type="text" class="numeros" name="con6_med'.$i.'" id="con6_med'.$i.'" value="'.$concentracion6.'" data-mini="true" onchange="updateCiclo(this,\''.$i.'\',\''.$id.'\',\''.$login.'\',\'concentracion6\');" '.$readonly.' ></td>');
				} ?>
			</tr>
			<td class="column-sticky">
			<select name="medicamento_7" class="chosen-select" id="medicamento_7" data-mini="true" <?=' onchange="updateMedicamento(this,\''.$id.'\',\''.$login.'\',\'medicamento7_id\');"'?>>
					<option value=0>MEDICAMENTO 7</option>
					<?php
					$medicamento7_ver="display: none;";
					foreach ($data_medicamentos as $info) { ?>
						<option value=<?php echo $info['value']; ?>><?php echo $info['text']; ?></option>
					<?php } ?>
				</select>
				<div class="ui-select"><input style="<?php print($medicamento7_ver); ?>" type="text" name="con7_med0" id="con7_med0" value="<?php echo $ciclo[0]['concentracion7']; ?>" data-mini="true" placeholder="Medicamento 7.."<?= 'onchange="updateCiclo(this,\''.$i.'\',\''.$id.'\',\''.$login.'\',\'concentracion7\');"'?>></div>
			</td>
			<td><?php echo $Tcon7_med; ?></td>
				<?php
				$ciclo_data = [];
				$ciclo_medico = [];
				if(isset($ciclo))foreach ($ciclo as $dato) {
					$ciclo_data[$dato['dia_ciclo']] = $dato['concentracion7']??"";
					$ciclo_medico[$dato['dia_ciclo']] = $dato['medico'];
				}
				for ($i = 1; $i <= $total_semanas; $i++) {
						$readonly = '';
						if (array_key_exists($i, $ciclo_data) && !is_null($ciclo_data[$i])) {
						$concentracion7 = $ciclo_data[$i];
						if ($repro['medicamento7_id'] == 0) {
							$readonly = 'readonly';
						}else {
							if ($ciclo_medico[$i]) {
								if($ciclo_medico[$i] == $login){
									$readonly = '';
								}else{
									$readonly = 'readonly';
								}
							}
						}
					} else {
						$concentracion7 = "";
						if ($repro['medicamento7_id'] == 0) {
							$readonly = 'readonly';
						}
					}
					print('<td><input type="text" class="numeros" name="con7_med'.$i.'" id="con7_med'.$i.'" value="'.$concentracion7.'" data-mini="true" onchange="updateCiclo(this,\''.$i.'\',\''.$id.'\',\''.$login.'\',\'concentracion7\');" '.$readonly.' ></td>');
				} ?>
			</tr>
			<td class="column-sticky"><h3>Observaciones:</h3></td><td></td>
				<?php
				$ciclo_data = [];
				$ciclo_medico = [];
				if(isset($ciclo))foreach ($ciclo as $dato) {
					$ciclo_data[$dato['dia_ciclo']] = $dato['observaciones']??"";
					$ciclo_medico[$dato['dia_ciclo']] = $dato['medico'];
				}
				for ($i = 1; $i <= $total_semanas; $i++) {
						$readonly = '';
						if (array_key_exists($i, $ciclo_data) && !is_null($ciclo_data[$i])) {
						$observaciones = $ciclo_data[$i];
						if ($ciclo_medico[$i]) {
							if($ciclo_medico[$i] == $login){
								$readonly = '';
							}else{
								$readonly = 'readonly';
							}
						}
					} else {
						$observaciones = "";
					}
					print('<td><textarea name="con_obs'.$i.'" id="con_obs'.$i.'" data-mini="true" style="width: 125px; margin: .446em .446em;" onchange="updateCiclo(this,\''.$i.'\',\''.$id.'\',\''.$login.'\',\'observaciones\');" '.$readonly.' >'.$observaciones.'</textarea></td>');
				}
			?>
		</tr>
		<script>
			medico_dia_ciclo = <?php echo json_encode($ciclo_medico);?>;
			login = "<?php echo $login;?>";
		</script>
		<!-- <tr>
			<td class="column-sticky"><h3>Recordatorio:</h3></td><td></td>
				<?php
				/* $ciclo_data = [];
				$ciclo_medico = [];
				if(isset($ciclo))foreach ($ciclo as $dato) {
					$ciclo_data[$dato['dia_ciclo']] = $dato['indicaciones']??"";
					$ciclo_medico[$dato['dia_ciclo']] = $dato['medico'];
				}
				for ($i = 1; $i <= $total_semanas; $i++) {
						$readonly = '';
						if (array_key_exists($i, $ciclo_data) && !is_null($ciclo_data[$i])) {
						$indicaciones = $ciclo_data[$i];
					} else {
						$indicaciones = "";
					}
					print('<td style="text-align: center;"><textarea name="con_obs'.$i.'" id="con_obs'.$i.'" style="width: 110px; margin: .446em .446em;"  data-mini="true" onchange="updateCiclo(this,\''.$i.'\',\''.$id.'\',\''.$login.'\',\'indicaciones\');" '.$readonly.' >'.$indicaciones.'</textarea></td>');
				} */
			?>
		</tr> -->
		<tr>
			<td class="column-sticky"><h3>Proximo control:</h3></td><td></td>
				<?php
				$ciclo_data = [];
				$ciclo_medico = [];
				$colorDate = '';
				if(isset($ciclo))foreach ($ciclo as $dato) {
					$ciclo_data[$dato['dia_ciclo']] = $dato['proximo_control']??"";
					$ciclo_medico[$dato['dia_ciclo']] = $dato['medico'];
				}
				for ($i = 1; $i <= $total_semanas; $i++) {
						$readonly = '';
						if (array_key_exists($i, $ciclo_data) && !is_null($ciclo_data[$i])) {
						$proximo_control = $ciclo_data[$i];
						if($ciclo_data[$i]){
							$colorDate = 'black';
						}else{
							$colorDate = 'white';
						}
					} else {
						$proximo_control = "";
						$colorDate = 'white';
					}
					print('<td><input type="date" style="width: 125px; color:'.$colorDate.';" class="numeros" name="fec_control_med'.$i.'" id="fec_control_med'.$i.'" value="'.$proximo_control.'" data-mini="true" onchange="updateCiclo(this,\''.$i.'\',\''.$id.'\',\''.$login.'\',\'proximo_control\');" '.$readonly.' ></td>');
				}
			?>
		</tr>

		<script>
			function colorDateCiclo(id) {
				var input = document.getElementById(id);
				if (input) {
					input.style.color = 'white';
				}
			}
		</script>
		
		<tr style="color: black;">
		<td class="column-sticky"><h3>Medico:</h3></td><td></td>
		<?php
			$ciclo_data = [];
			if(isset($ciclo))foreach ($ciclo as $dato) {
				$ciclo_data[$dato['dia_ciclo']] = $dato['medico'];
			}
			for ($i = 1; $i <= $total_semanas; $i++) {
				if (isset($ciclo_data[$i])) {
					$medico = $ciclo_data[$i];
				} else {
					$medico = "";
				}
				print('<td><label>'.$medico.'</label></td>');
			}
			?>
		
			</tr>
			<tr><td class="column-sticky"></td><td></td>
				<?php
					for ($i = 1; $i <= $total_semanas; $i++) {
						print('<td ><div style="display:flex; justify-content:space-around;padding:10px;"><i style="font-size:1.5em;cursor: pointer;" class="fas fa-trash-alt" onclick="eliminar(\''.$i.'\',\''.$id.'\',\''.$login.'\');"></i></div></td>');
					}
				?>
			</tr>
			<tr>
				<td>
					<select name="con_iny0" id="con_iny0" data-mini="true">
						<option value="" selected>Inyección</option>
						<option value="OVIDREL" <?php if ($con_iny[0] == "OVIDREL") { echo "selected"; } ?>>OVIDRELL 250ug/0.5ml</option>
						<option value="GONAPEPTIL" <?php if ($con_iny[0] == "GONAPEPTIL") { echo "selected"; } ?>>GONAPEPTYL 0,1MG/ML</option>
						<option value="OVIDREELL_GONAPEPTYL" <?php if ($con_iny[0] == "OVIDREELL_GONAPEPTYL") { echo "selected"; } ?>>OVIDREELL + GONAPEPTYL (DUAL TRIGGER)</option>
<!-- 						<option value="CHORAGON" <?php if ($con_iny[0] == "CHORAGON") { echo "selected"; } ?>>CHORAGON</option>
						<option value="HUCOG" <?php if ($con_iny[0] == "HUCOG") { echo "selected"; } ?>>HUCOG</option>
 -->					</select>
				</td>
				<td></td>
				<td>
					<input type="number" step="any" name="cant_iny" id="cant_iny" value="<?php if(isset($repro['cant_iny'])) echo $repro['cant_iny'];?>" data-mini="true">
				</td>
			</tr>
		</table>
	</div>
	<?php }?>
</div>
<script>

$(document).ready(function() {
	if ($('#cancela').is(':checked')) {
		$("#motivo_cancelacion_cont").css("display", "block");
   	}
	
	$('#medicamento_1').on('change', function() { desabilitarInput(1) });
	$('#medicamento_2').on('change', function() { desabilitarInput(2) });
	$('#medicamento_3').on('change', function() { desabilitarInput(3) });
	$('#medicamento_4').on('change', function() { desabilitarInput(4) });
	$('#medicamento_5').on('change', function() { desabilitarInput(5) });
	$('#medicamento_6').on('change', function() { desabilitarInput(6) });
	$('#medicamento_7').on('change', function() { desabilitarInput(7) });
	$('#cancela').on('change', function() {
	    if ($(this).is(':checked')) {
			$("#motivo_cancelacion_cont").css("display", "block");
   		}else{
			$("#motivo_cancelacion").val("");
			$("#motivo_cancelacion_cont").css("display", "none");
		}
});
});

function desabilitarInput(x){
	if (this.value == 0) {
		return false
	} else {
		for (let i = 1; i <= 60; i++) {
			$("#con"+x+"_med"+i).removeAttr("readonly");
			if (medico_dia_ciclo[i]) {
				if(medico_dia_ciclo[i] == login){
					$("#con"+x+"_med"+i).removeAttr("readonly");
				}else{
					$("#con"+x+"_med"+i).attr("readonly", true);
				}
			}
		}
	}
}

function anular(fech,id) {
  if (confirm("¿Está seguro de registrar la fecha ("+fech+") para el inicio del ciclo de estimulacion?")) {

    updateCiclo(document.getElementById('con_fec0'), 0, id, '<?php echo $login; ?>', 'fecha_estimulacion');
  } else {
    return false;
  }
}

	function validarFechaMayor2000(date) {
	  var x = new Date(date);
	  var fech = new Date('2000/01/01');
		
	  if (x < fech) {
	    return false; 
	  } else {
	    return true; 
	  }
	}

	function updateCiclo(valor, dia, idCiclo, login, campo)
    { 
		var fecha = document.getElementById('con_fec'+dia).value;
		if(campo=='fecha_estimulacion' && !validarFechaMayor2000(fecha))
		return true;
		
		if(dia==0){
			dia=1;
		}
    	var valor = valor.value; 
      var parametross = 
      {
        "valor" : valor,
		"fecha" : fecha,
		"dia" : dia,
		"idCiclo" : idCiclo,
		"login" : login,
		"campo" : campo,
        "accion" : "cicloEstimulacion"
      };
      $.ajax({
        data: parametross,
        url: '../_database/db_tools.php',
        type: 'POST',
        
    	beforesend: function(){
			$('#result').html('Esperando');
		},
        success: function(mensaje)
        {
			$('#result').html(mensaje);
			if(campo=='fecha_estimulacion'){
				window.setTimeout(recarga, 1000);
			}else{
				validador=0
				for (let i = 1; i <= 60; i++) {
					if (medico_dia_ciclo[i]) {
						if(medico_dia_ciclo[i] == login){
							validador=1
							break;
						}
					}
				}

				if(validador!=1){
					$('#bloqGuardar').css('display', 'block');
					$('#bloqMensaje').css('display', 'none');
					//window.setTimeout(recarga, 1000);
				}

			}
        }
      });

	  if ('proximo_control' == campo) {
		$('#fec_control_med'+dia).attr("style",  "color: black !important;")
	  }
    }
	function recarga(){
		window.location.reload();
	}
	function updateMedicamento(valor, idCiclo, login, campo){
		valor = valor.value;
		var parametross = 
      {
        "valor" : valor,
		"idCiclo" : idCiclo,
		"login" : login,
		"campo" : campo,
        "accion" : "hcreprod"
      };
      $.ajax({
        data: parametross,
        url: '../_database/db_tools.php',
        type: 'POST',
        
    	beforesend: function(){
			$('#result').html('Esperando');
		},
        success: function(mensaje)
        {
			$('#result').html(mensaje);	
			if(campo=='med'){
				window.setTimeout(recarga, 500);
			}
        }
      });
	}
	function eliminar(dia, idCiclo, login) {
  	if (confirm("¿Estás seguro de que deseas eliminar?")) {
    	var parametros = {
    	  "dia": dia,
    	  "idCiclo": idCiclo,
    	  "login": login,
    	  "accion": "elimina"
    	};
	
    	$.ajax({
    	  data: parametros,
    	  url: '../_database/db_tools.php',
    	  type: 'POST',
    	  beforeSend: function() {
    	    $('#result').html('Esperando');
    	  },
    	  success: function(mensaje) {
    	    $('#result').html(mensaje);
    	    if (mensaje == 'Dia eliminado exitosamente') {
    	      window.setTimeout(recarga, 1000);
    	    }
    	  }
    	});
  	} else {
		
  	}
}
 </script>