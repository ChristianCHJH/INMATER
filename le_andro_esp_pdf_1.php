<?php
	$head_foot = '
	<!--mpdf
	<htmlpageheader name="myHTMLHeader"><img src="_images/info_head.jpg" width="100%" ></htmlpageheader>
	<htmlpagefooter name="myHTMLFooter"><img src="_images/info_foot.jpg" width="100%" ></htmlpagefooter>
	mpdf-->';
	$estilo = '
	<style>
		.diagnostico {
			font-size: 16px;
		}
		.valores {
			font-size: 10px;
		}
		.bordecolor {
			border: 1px solid #aeaaaa;
			background-color: #f2f2f2;
		}
		.borde {
		   border: 1px solid #aeaaaa;
		}
		.cabecera_normal {
			padding: 5px 15px;
		}
		@page {
			margin-header: 0mm;
			margin-footer: 0mm;
			margin-left: 0cm;
			margin-right: 0cm;
			margin-top: 3cm;
			header: html_myHTMLHeader;
			footer: html_myHTMLFooter;
		}
		.xxx {margin-left: 2.3cm;margin-right: 1.7cm;}
		.tabla table {border-collapse: collapse;}
		.tabla table, .tabla th, .tabla td {border: 1px solid #72a2aa;}
	</style>';
	require ('_includes/le_andro_esp_01_model.php'); 
	// validacion de azoospermia
	if ($pop['resul_azo']==1) {
		$morfo_anormal="0.00";
	} else {
		$morfo_anormal=100-$pop['morfo_normal'];
	}
	$info_lobtencion=$info_mobtencion=$info_dobtencion=$info_medicacion=$macro_apariencia=$macro_viscosidad=$macro_liquefaccion=$macro_aglutinacion=$diagnostico="";
    while ($lobte = $rlobte->fetch(PDO::FETCH_ASSOC)) {
    	if ($pop['info_lobtencion'] == $lobte['id'])
    		$info_lobtencion=$lobte['nombre'];
    }
    while ($mobte = $rmobte->fetch(PDO::FETCH_ASSOC)) {
    	if ($pop['info_mobtencion'] == $mobte['id'])
    		$info_mobtencion=$mobte['nombre'];
    }
    foreach ($rows as $sino) {
    	if ($pop['info_dobtencion'] == $sino['id'])
    		$info_dobtencion = $sino['nombre'];
    }
    foreach ($rows as $sino) {
    	if ($pop['info_medicacion'] == $sino['id'])
    		$info_medicacion = $sino['nombre'];
    }
	foreach ($apaespe as $row) {
    	if ($pop['macro_apariencia'] == $row['id'])
    		$macro_apariencia = $row['nombre'];
	}
	foreach ($visespe as $row) {
    	if ($pop['macro_viscosidad'] == $row['id'])
    		$macro_viscosidad = $row['nombre'];
	}
	foreach ($liqespe as $row) {
    	if ($pop['macro_liquefaccion'] == $row['id'])
    		$macro_liquefaccion = $row['nombre'];
	}
	foreach ($rows1 as $sino) {
    	if ($pop['macro_aglutinacion'] == $sino['id'])
    		$macro_aglutinacion = $sino['nombre'];
	}
	//firma embriologo
	$consulta = $db->prepare("SELECT id, nom, cbp, nombre, apellido FROM lab_user WHERE id=?");
	$consulta->execute(array($pop['emb']));
	$embrio = $consulta->fetch(PDO::FETCH_ASSOC);
	//diagnostico
	if ($pop['resul_cripto'] == 1) {
		$diagnostico="CRIPTOzoospermia";
	} else if ($pop['resul_azo'] == 1) {
		$diagnostico="Azoospermia";
	} else {
		// Hipospermia
		if (@$pop['macro_volumen'] < 1.5) {
			$diagnostico.="HIPO";
		}
		// Oligozoospermia
		if ( $pop['concen_exml'] < 15 || (@$pop['macro_volumen']*$pop['concen_exml']) < 39 ) {
			$diagnostico.="OLIGO";
		}
		// Astenozoospermia
		if ( ($pop['movi_mprogresivo']+$pop['movi_mnoprogresivo']) < 40 || $pop['movi_mprogresivo'] < 32 ) {
			$diagnostico.="ASTENO";
		}
		// Teratozoospermia
		if ($pop['morfo_normal'] < 4) {
			$diagnostico.="TERATO";
		}
		// Necrozoospermia
		if ($pop['movi_tvitalidad'] < 58) {
			$diagnostico.="NECRO";
		}
		if (!empty($diagnostico)) {
			$diagnostico.="zoospermia";
			if (strpos($diagnostico, "HIPO")!==false) {
				$diagnostico.=", se sugiere evaluación ecográfica (Ecografia testicular)";
			}
			if (strpos($diagnostico, "Azoospermia")!==false || strpos($diagnostico, "OLIGO")!==false || strpos($diagnostico, "CRIPTO")!==false) {
				$diagnostico.=", se sugiere evaluación ecográfica (Ecografia testicular) y Cariotipo en sangre periférica.";
			}
		} else {
			$diagnostico="Normozoospermia";
		}
	}

    require($_SERVER["DOCUMENT_ROOT"] . "/config/environment.php");
    require_once __DIR__ . '/vendor/autoload.php';
    $mpdf = new \Mpdf\Mpdf($_ENV["pdf_regular"]);

	$consumos='';
	if ($pop['info_medicacion'] == 1 && date("Y-m-d") >= '2020-10-11') {
		$consumos.=(!!$pop["antibioticos"] ? ', Antibioticos o Antifúngicos' : '');
		$consumos.=(!!$pop["antidepresivos"] ? ', Antidepresivos o Anticonvulsivos' : '');
		$consumos.=(!!$pop["antiinflamatorios"] ? ', Antiinflamatorios' : '');
		$consumos.=(!!$pop["protectores"] ? ', Protectores Gástricos o Hepáticos' : '');
		$consumos.=(!!$pop["otros_texto"] ? '<br>Otros: ' . $pop["otros_texto"] : '');
		$consumos='<tr><td class="borde" colspan="4" style="font-size: 10px;">Consumos recientes: '.$consumos.'</td></tr>';
	}

	$html = '
	<link rel="icon" href="_images/favicon.png" type="image/x-icon">
	<table>
		<tbody>
			<tr>
				<td colspan="4" align="center"><h2>Laboratorio de Reproducción Asistida: Espermatograma</h2></td>
			</tr>
			<tr><td colspan="4" align="left"><b>Información General</b></td></tr>
			<tr>
				<td class="bordecolor">Fecha de Resultado</td>
				<td class="borde" colspan="3">'.$pop["fec"].'</td>
			</tr>
			<tr>
				<td class="bordecolor">Médico</td>
				<td class="borde" colspan="3">'.mb_strtoupper($medico).'</td>
			</tr>
			<tr>
				<td class="bordecolor">Paciente</td>
				<td class="borde" colspan="3">'.mb_strtoupper($pareja).'</td>
			</tr>
			<tr>
				<td class="bordecolor">Pareja</td>
				<td class="borde" colspan="3">'.mb_strtoupper($paciente).'</td>
			</tr>
			<tr>
				<td class="bordecolor">Fecha de Obtención</td>
				<td class="borde">'.date($pop["info_fmuestra"]).'</td>
				<td class="bordecolor">Lugar de Obtención</td>
				<td class="borde">'.$info_lobtencion.'</td>
			</tr>
			<tr>
				<td class="bordecolor">Hora de Entrega</td>
				<td class="borde">'.$pop["info_hentrega"].'</td>
				<td class="bordecolor">Método de Obtención</td>
				<td class="borde">'.$info_mobtencion.'</td>
			</tr>
			<tr>
				<td class="bordecolor">Dificultad para Obtención</td>
				<td class="borde">'.$info_dobtencion.'</td>
				<td class="bordecolor">Abstinencia sexual</td>
				<td class="borde">'.$pop['abstinencia'].'</td>
			</tr>
			<tr>
				<td class="bordecolor">Medicación</td>
				<td class="borde" colspan="3">'.$info_medicacion.'</td>
			</tr>
			'.$consumos.'
			<tr><td colspan="4" align="left"><b>Análisis Macroscópico</b></td></tr>
			<tr>
				<td></td>
				<td align="center">Resultado</td>
				<td align="center" colspan="2">Valor referencial</td>
			</tr>
			<tr>
				<td class="bordecolor">Apariencia</td>
				<td class="borde" align="center">'.$macro_apariencia.'</td>
				<td class="borde" colspan="2"></td>
			</tr>
			<tr>
				<td class="bordecolor">Viscosidad</td>
				<td class="borde" align="center">'.$macro_viscosidad.'</td>
				<td class="borde" colspan="2"></td>
			</tr>
			<tr>
				<td class="bordecolor">Licuefacción</td>
				<td class="borde" align="center">'.$macro_liquefaccion.'</td>
				<td class="borde" colspan="2"></td>
			</tr>
			<tr>
				<td class="bordecolor">Aglutinación</td>
				<td class="borde" align="center">'.$macro_aglutinacion.'</td>
				<td class="borde" colspan="2"></td>
			</tr>
			<tr>
				<td class="bordecolor">PH</td>
				<td class="borde" align="center">'.$pop['macro_ph'].'</td>
				<td class="borde" colspan="2" align="center">≥ 7.2</td>
			</tr>
			<tr>
				<td class="bordecolor">Volumen</td>
				<td class="borde" align="center">'.@$pop['macro_volumen'].' ml</td>
				<td class="borde" colspan="2" align="center">≥ 1.5 ml</td>
			</tr>

			<tr><td colspan="4" align="left"><b>Concentración</b></td></tr>
			<tr>
				<td></td>
				<td align="center">Resultado</td>
				<td align="center" colspan="2">Valor referencial</td>
			</tr>
			<tr>
				<td class="bordecolor">Espermatozoides por ml</td>
				<td class="borde" align="center">'.$pop['concen_exml'].' M/ml</td>
				<td class="borde" colspan="2" align="center">≥ 15 M/ ml</td>
			</tr>
			<tr>
				<td class="bordecolor">Células redondas</td>
				<td class="borde" align="center">'.$pop['concen_credon'].' M/ml</td>
				<td class="borde" colspan="2" align="center">< 5 M/ ml</td>
			</tr>
			<tr>
				<td class="bordecolor">Espermatozoides por eyaculado</td>
				<td class="borde" align="center">'.($pop['concen_exml']*@$pop['macro_volumen']).' Millones</td>
				<td class="borde" colspan="2" align="center">≥ 39 Millones</td>
			</tr>
			<tr><td colspan="4"><hr></td></tr>
			<tr>
				<td class="bordecolor">Móvil Progresivo (P)</td>
				<td class="borde" align="center">'.number_format($pop['concen_exml']*@$pop['macro_volumen']*$pop['movi_mprogresivo']/100, 2).' M/ml</td>
				<td class="borde" colspan="2" align="center"></td>
			</tr>
			<tr>
				<td class="bordecolor">Móvil No progresivo (NP)</td>
				<td class="borde" align="center">'.number_format($pop['concen_exml']*@$pop['macro_volumen']*$pop['movi_mnoprogresivo']/100, 2).' M/ml</td>
				<td class="borde" colspan="2" align="center"></td>
			</tr>
			<tr>
				<td class="bordecolor">No móviles</td>
				<td class="borde" align="center">'.number_format($pop['concen_exml']*@$pop['macro_volumen']*(100-$pop['movi_mprogresivo']-$pop['movi_mnoprogresivo'])/100, 2).' M/ml</td>
				<td class="borde" colspan="2" align="center"></td>
			</tr>
			<tr>
				<td colspan="4" align="center">
					<img src="_upload/andro/'.$pop['img_concen'].'" height="250" width="550">
				</td>
			</tr>
		</tbody>
	</table>';
	$mpdf->WriteHTML($estilo . '<body><div class="xxx">' . $head_foot . $html . '</div></body>');
	$mpdf->AddPage();

	if ($pop['movi_mprogresivo_lineal_cantidad'] + $pop['movi_mprogresivo_no_lineal_cantidad'] == 0) {
		$html = '
		<table>
			<tbody>
				<tr><td colspan="4" align="left"><br><b>Movilidad y Vitalidad</b></td></tr>
				<tr>
					<td></td>
					<td align="center">Resultado</td>
					<td align="center" colspan="2">Valor referencial</td>
				</tr>
				<tr>
					<td class="bordecolor">Total móviles (P + NP)</td>
					<td class="borde" align="center">'.number_format(($pop['movi_mprogresivo']+$pop['movi_mnoprogresivo']), 2).' %</td>
					<td class="borde" colspan="2" align="center">≥ 40 %</td>
				</tr>
				<tr>
					<td class="bordecolor">Móvil Progresivo (P)</td>
					<td class="borde" align="center">'.$pop['movi_mprogresivo'].' %</td>
					<td class="borde" colspan="2" align="center">≥ 32 %</td>
				</tr>
				<tr>
					<td class="bordecolor">Móvil No progresivo (NP)</td>
					<td class="borde" align="center">'.$pop['movi_mnoprogresivo'].' %</td>
					<td class="borde" colspan="2" align="center"></td>
				</tr>
				<tr>
					<td class="bordecolor">No móviles</td>
					<td class="borde" align="center">'.number_format((100-$pop['movi_mprogresivo']-$pop['movi_mnoprogresivo']), 2).' %</td>
					<td class="borde" colspan="2" align="center"></td>
				</tr>
				<tr>
					<td class="bordecolor">Test de Vitalidad</td>
					<td class="borde" align="center">'.$pop['movi_tvitalidad'].' %</td>
					<td class="borde" colspan="2" align="center">≥ 58 %</td>
				</tr>
				<tr>
					<td colspan="4" align="center">
						<img src="_upload/andro/'.$pop['img_movi'].'">
					</td>
				</tr>
			</tbody>
		</table>';
	} else {
		$html = '
		<table>
			<tbody>
				<tr><td colspan="4" align="left"><b>Movilidad y Vitalidad</b></td></tr>
				<tr><td colspan="4" align="center"></td></tr>
				<tr><td colspan="4" align="center"></td></tr>
				<tr><td colspan="4" align="center"></td></tr>
				<tr><td colspan="4" align="center"></td></tr>
				<tr><td colspan="4" align="center"></td></tr>
				<tr><td colspan="4" align="center"></td></tr>
				<tr>
					<td></td>
					<td align="center">Resultado</td>
					<td align="center" colspan="2">Valor referencial</td>
				</tr>
				<tr>
					<td class="bordecolor">Total móviles (P + NP)</td>
					<td class="borde" align="center">'.($pop['movi_mprogresivo_lineal_cantidad'] + $pop['movi_mprogresivo_no_lineal_cantidad'] + $pop['movi_mnoprogresivo_cantidad']).'&nbsp;&nbsp;&nbsp;('.number_format(($pop['movi_mprogresivo']+$pop['movi_mnoprogresivo']), 2).' %)</td>
					<td class="borde" colspan="2" align="center">≥ 40 %</td>
				</tr>
				<tr>
					<td class="bordecolor">Móvil Progresivo (P)</td>
					<td class="borde" align="center">'.($pop['movi_mprogresivo_lineal_cantidad'] + $pop['movi_mprogresivo_no_lineal_cantidad']).'&nbsp;&nbsp;&nbsp;('.$pop['movi_mprogresivo'].' %)</td>
					<td class="borde" colspan="2" align="center">≥ 32 %</td>
				</tr>

				<tr>
					<td class="bordecolor">M.P. Lineal (VAP >= 25&#181;m/s)</td>
					<td class="borde" align="center">'.$pop['movi_mprogresivo_lineal_cantidad'].'&nbsp;&nbsp;&nbsp;('.(number_format($pop['movi_mprogresivo_lineal_cantidad']*100/($pop['movi_mprogresivo_lineal_cantidad']+$pop['movi_mprogresivo_no_lineal_cantidad']+$pop['movi_mnoprogresivo_cantidad']+$pop['movi_nmoviles_cantidad']), 2)).' %)</td>
					<td class="borde" colspan="2" align="center"></td>
				</tr>
				<tr>
					<td class="bordecolor">M.P. No Lineal (5&#181;m/s <= VAP < 25&#181;m/s)</td>
					<td class="borde" align="center">'.$pop['movi_mprogresivo_no_lineal_cantidad'].'&nbsp;&nbsp;&nbsp;('.(number_format($pop['movi_mprogresivo_no_lineal_cantidad'] * 100 / ($pop['movi_mprogresivo_lineal_cantidad'] + $pop['movi_mprogresivo_no_lineal_cantidad'] + $pop['movi_mnoprogresivo_cantidad'] + $pop['movi_nmoviles_cantidad']), 2)).' %)</td>
					<td class="borde" colspan="2" align="center"></td>
				</tr>

				<tr>
					<td class="bordecolor">Móvil No progresivo (NP)</td>
					<td class="borde" align="center">'.$pop['movi_mnoprogresivo_cantidad'].'&nbsp;&nbsp;&nbsp;('.$pop['movi_mnoprogresivo'].' %)</td>
					<td class="borde" colspan="2" align="center"></td>
				</tr>
				<tr>
					<td class="bordecolor">No móviles</td>
					<td class="borde" align="center">'.$pop['movi_nmoviles_cantidad'].'&nbsp;&nbsp;&nbsp;('.number_format((100-$pop['movi_mprogresivo']-$pop['movi_mnoprogresivo']), 2).' %)</td>
					<td class="borde" colspan="2" align="center"></td>
				</tr>
				<tr>
					<td class="bordecolor">Test de Vitalidad</td>
					<td class="borde" align="center">'.$pop['movi_tvitalidad'].' %</td>
					<td class="borde" colspan="2" align="center">≥ 58 %</td>
				</tr>
				<tr>
					<td colspan="4" align="center">
						<img src="_upload/andro/'.$pop['img_movi'].'">
					</td>
				</tr>
			</tbody>
		</table>';
	}

	$html.='
	<table>
		<tbody>
			<tr><td colspan="3" align="left"><br><b>Cinética Espermática</b></td></tr>
			<tr>
				<td align="center">
					<table>
						<tbody>
							<tr><td align="center" class="bordecolor cabecera_normal" colspan="2">(&#181;m/s)</td></tr>
							<tr><td class="bordecolor cabecera_normal">VAP</td><td class="borde cabecera_normal">'.$pop['cine_vap'].' &#181;m/s</td></tr>
							<tr><td class="bordecolor cabecera_normal">VSL</td><td class="borde cabecera_normal">'.$pop['cine_vsl'].' &#181;m/s</td></tr>
							<tr><td class="bordecolor cabecera_normal">VCL</td><td class="borde cabecera_normal">'.$pop['cine_vcl'].' &#181;m/s</td></tr>
							<tr>
								<td align="left" colspan="2"><small>
									*VAP: Velocidad de Trayectoria<br>
									*VSL: Velocidad Linea Recta<br>
									*VCL: Velocidad Curvilinea</small>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
				<td>
					<table>
						<tbody>
							<tr><td align="center" class="bordecolor cabecera_normal" colspan="2">(%)</td></tr>
							<tr><td class="bordecolor cabecera_normal">LIN</td><td class="borde cabecera_normal">'.$pop['cine_lin'].' %</td></tr>
							<tr><td class="bordecolor cabecera_normal">STR</td><td class="borde cabecera_normal">'.$pop['cine_str'].' %</td></tr>
							<tr><td class="bordecolor cabecera_normal">WOB</td><td class="borde cabecera_normal">'.$pop['cine_wob'].' %</td></tr>
							<tr>
								<td align="left" colspan="2"><small>
									*LIN: Indice de Linealidad<br>
									*STR: Indice de Rectitud<br>
									*WOB: Indice de Oscilacion</small>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
				<td>
					<table>
						<tbody>
							<tr><td align="center" class="bordecolor cabecera_normal" colspan="2">(m, Hz)</td></tr>
							<tr><td class="bordecolor cabecera_normal">ALH</td><td class="borde cabecera_normal">'.$pop['cine_alh'].' &#181;m</td></tr>
							<tr><td class="bordecolor cabecera_normal">BCF</td><td class="borde cabecera_normal">'.$pop['cine_bcf'].' Hz</td></tr>
							<tr><td class="borde cabecera_normal" colspan="2">-</td></tr>
							<tr>
								<td align="left" colspan="2"><small>
									*ALH: Amplitud Lateral de Cabeza<br>
									*BCF: Frecuencia de Batido de Cola<br>-</small>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>';

	$mpdf->WriteHTML($estilo . '<body><div class="xxx">' . $head_foot . $html . '</div></body>');
	$mpdf->AddPage();

	$html_1 = '
	<table>
		<tbody>
			<tr><td colspan="4" align="left"><br><b>Morfología</b></td></tr>
			<tr>
				<td class="bordecolor"></td>
				<td class="bordecolor" align="center">Resultado</td>
				<td class="bordecolor" align="center" colspan="2">Valor Referencial</td>
			</tr>
			<tr>
				<td class="bordecolor">Normales</td>
				<td class="borde" align="center">'.$pop['morfo_normal'].' %</td>
				<td class="borde" colspan="2" align="center">4 %</td>
			</tr>
			<tr>
				<td class="bordecolor">Anormales</td>
				<td class="borde" align="center">'.$morfo_anormal.' %</td>
				<td class="borde" align="center" colspan="2"></td>
			</tr>
			<tr>
				<td colspan="4" align="center">
					<img src="_upload/andro/'.$pop['img_mtotal'].'">
				</td>
			</tr>
			<tr>
				<td colspan="4" align="center">
					<table>
						<thead>
							<tr>
								<td class="bordecolor cabecera_normal">PARÁMETROS EVALUADOS</td>
								<td class="bordecolor cabecera_normal">PROMEDIO</td>
								<td class="bordecolor cabecera_normal">PORCENTAJE DE ANORMALES</td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="borde">Largo de Cabeza</td>
								<td class="borde">'.$pop['normal_largocabeza_promedio'].' &#181;m</td>
								<td class="borde">'.$pop['normal_largocabeza_porcentaje'].' %</td>
							</tr>
							<tr>
								<td class="borde">Ancho de Cabeza</td>
								<td class="borde">'.$pop['normal_ancho_promedio'].' &#181;m</td>
								<td class="borde">'.$pop['normal_ancho_porcentaje'].' %</td>
							</tr>
							<tr>
								<td class="borde">Perímetro de Cabeza</td>
								<td class="borde">'.$pop['normal_perimetro_promedio'].' &#181;m</td>
								<td class="borde">'.$pop['normal_perimetro_porcentaje'].' %</td>
							</tr>
							<tr>
								<td class="borde">Área de Cabeza</td>
								<td class="borde">'.$pop['normal_area_promedio'].' &#181;m<sup>2</sup></td>
								<td class="borde">'.$pop['normal_area_porcentaje'].' %</td>
							</tr>
							<tr>
								<td class="borde">Largo de la Cola</td>
								<td class="borde">'.$pop['normal_largocola_promedio'].' &#181;m</td>
								<td class="borde">'.$pop['normal_largocola_porcentaje'].' %</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>';
	$mpdf->WriteHTML($estilo . '<body><div class="xxx">' . $head_foot . $html_1 . '</div></body>');
	$mpdf->AddPage();
	$html_2='
	<table>
		<tbody>
            <tr><td align="center" colspan="4"><b>Espermatozoides con Morfología Normal</b></td></tr>
            <tr>
                <td><img src="_images/morfo_normal_01.jpg" height="120px" width="25%"></td>
                <td><img src="_images/morfo_normal_02.jpg" height="120px" width="25%"></td>
                <td><img src="_images/morfo_normal_03.jpg" height="120px" width="25%"></td>
                <td><img src="_images/morfo_normal_04.jpg" height="120px" width="25%"></td>
            </tr>
            <tr><td align="center" colspan="4"><br><b>Espermatozoides con defectos de Cabeza</b></td></tr>
            <tr>
                <td align="center"><img src="_images/anormal_cabeza_01.jpg" height="120px" width="25%"><br>Cabeza Vacuolada</td>
                <td align="center"><img src="_images/anormal_cabeza_02.jpg" height="120px" width="25%"><br>Cabeza Alargada</td>
                <td align="center"><img src="_images/anormal_cabeza_03.jpg" height="120px" width="25%"><br>Cabeza Redonda</td>
                <td align="center"><img src="_images/anormal_cabeza_04.jpg" height="120px" width="25%"><br>Bicefalo</td>
            </tr>
            <tr><td align="center" colspan="4"><br><b>Espermatozoides con defectos de Cuello</b></td></tr>
            <tr>
                <td align="center"><img src="_images/anormal_cuello_01.jpg" height="120px" width="25%"><br>Gota Citoplasmática</td>
                <td align="center"><img src="_images/anormal_cuello_02.jpg" height="120px" width="25%"><br>Cuello Engrosado</td>
                <td align="center"><img src="_images/anormal_cuello_03.jpg" height="120px" width="25%"><br>Cuello Angulado</td>
                <td align="center"><img src="_images/anormal_cuello_04.jpg" height="120px" width="25%"><br>Cuello Delgado</td>
            </tr>
            <tr><td align="center" colspan="4"><br><b>Espermatozoides con defectos de Cola</b></td></tr>
            <tr>
                <td align="center"><img src="_images/anormal_cola_01.jpg" height="120px" width="25%"><br>Cola Enrollada</td>
                <td align="center"><img src="_images/anormal_cola_02.jpg" height="120px" width="25%"><br>Bicaudo</td>
                <td align="center"><img src="_images/anormal_cola_03.jpg" height="120px" width="25%"><br>Cola Angulada</td>
                <td align="center"><img src="_images/anormal_cola_04.jpg" height="120px" width="25%"><br>Cola Corta</td>
            </tr>
            <!-- <tr><td><br></td></tr> -->
            <tr>
            	<td class="bordecolor"><b>Diagnóstico</b></td>
            </tr>
            <tr>
            	<td class="borde" colspan="4"><span class="diagnostico"><b>'.$diagnostico.'</b></span></td>
			</tr>';
	if (!empty($pop['nota'])) {
		$html_2.='<tr><td colspan="4"><br><b>Observaciones: </b>'.$pop['nota'].'</td></tr>';
	}
	$cbp= '<br><i>CBP: ' . $embrio['cbp'] . '</i>';
	if($embrio['cbp']=='0'){ $cbp= '';}
	$html_2.='
			<tr>
				<td align="right" colspan="4">
					<img src="emb_pic/emb_' . $embrio['id'] . '.jpg" width="200px" height="100px"><br><br>
					<i>Blgo. ' . $embrio['nombre'].' '. $embrio['apellido'] . '</i>'.$cbp.'
				</td>
            </tr>
            <tr><td colspan="4"><span class="valores">Valores referenciales basados en el 5to manual de la OMS-2010</span></td></tr>
		</tbody>
	</table>';
	$mpdf->WriteHTML($estilo.'<body><div class="xxx">' . $head_foot . $html_2 . '</div></body>');
	$mpdf->Output();
	/* print($html); */
	exit();
?>