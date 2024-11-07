<?php
	error_reporting( error_reporting() & ~E_NOTICE );
	include('nusoap/lib/nusoap.php');
	$client = new nusoap_client('http://www.anglolab.com:287/Service.svc?wsdl','wsdl');
	$client->soap_defencoding = 'UTF-8';

	if ($client->getError()) {
		echo '<h2>Constructor error:</h2><pre>' . $err . '</pre>'; exit();
	}

	$param = array('dato' => $_GET["dato"]);
	$result = $client->call('Consulta_Resultado_Laboratorio_Inmater', $param);

	if (!$client->fault && $result['Consulta_Resultado_Laboratorio_InmaterResult']['diffgram'])
	{
		$tablas = $result['Consulta_Resultado_Laboratorio_InmaterResult']['diffgram']['NewDataSet']['Table'];
		/* var_dump($tablas); */

		$estudio_id = 0;

        require($_SERVER["DOCUMENT_ROOT"] . "/config/environment.php");
        require_once __DIR__ . '/vendor/autoload.php';
        $mpdf = new \Mpdf\Mpdf($_ENV["pdf_regular_fontsize8"]);
		
		$estilo = '
		<style>
			#estudio {
				font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
			}

			#customers {
				font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
				border-collapse: collapse;
				width: 100%;
			}

			#customers td, #customers th {
				border: 1px solid #ddd;
				padding: 8px;
			}

			#customers tr:nth-child(even){background-color: #f2f2f2;}

			#customers th {
				padding-top: 12px;
				padding-bottom: 12px;
				text-align: left;
				background-color: #EDECFF;
				color: black;
			}
			
			#cabecera {
				font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
				border-collapse: collapse;
				width: 100%;
			}

			#cabecera td, #cabecera th {
				border: 1px solid #ddd;
				padding: 8px;
			}

			#cabecera th {
				padding-top: 12px;
				padding-bottom: 12px;
				text-align: left;
				background-color: #EDECFF;
				color: black;
			}

			@page {
				margin-header: 0mm;
				margin-footer: 0mm;
				margin-left: 0cm;
				margin-right: 0cm;
				header: html_myHTMLHeader;
				footer: html_myHTMLFooter;
			}

			.xxx {margin-left: 2.3cm;margin-right: 1.7cm;}
		</style>';

		$head = '<!--mpdf
		<htmlpageheader name="myHTMLHeader"><img src="_images/anglolab_logo.png" width="100%"></htmlpageheader>
		mpdf-->';

		$items = [];
		
		$encontro = false;
		
		foreach ($tablas as $key => $value) {
			$encontro = false;

			$data = array(
				'descripcion' => mb_strtoupper(utf8_encode($value['variabledescripcion'])),
				'resultado' => mb_strtoupper(utf8_encode($value['resultado'])),
				'rango' => mb_strtoupper(utf8_encode($value['Rango'])). "" . mb_strtoupper(utf8_encode($value['Obs_Rango'])) . (isset($value['unidad']) ? mb_strtoupper(utf8_encode($value['unidad'])) : ""),
				'metodo' => mb_strtoupper(utf8_encode($value['Metodo'])),
			);

			foreach ($items as $key1 => $value1) {
				if ( strcmp($value1['id'], mb_strtoupper(utf8_encode($value['SectorDescripcion']))) === 0 ) {
					/* $items[$key1]["contenido"] .= $html; */
					array_push($items[$key1]["data"], $data);
					$encontro = true;
				}
			}

			if (!$encontro) {
				$item = array(
					'id' => mb_strtoupper(utf8_encode($value['SectorDescripcion'])),
					'orden' => mb_strtoupper(utf8_encode($value['orden'])),
					'numero_cliente' => mb_strtoupper(utf8_encode($value['ordencliente'])),
					'paciente' => mb_strtoupper(utf8_encode($value['Paciente'])),
					'edad' => mb_strtoupper(utf8_encode($value['Edad'])),
					'medico' => mb_strtoupper(utf8_encode($value['Medico'])),
					'compania' => mb_strtoupper(utf8_encode($value['Convenio'])),
					'fecha_toma_muestra' => date("d/m/Y", strtotime(explode("T", $value['Fe_Resultado'])[0])) . " " . substr(explode("T", $value['Fe_Resultado'])[1], 0, 5),
					'fecha_resultado' => date("d/m/Y", strtotime(explode("T", $value['fechavalidacion'])[0])) . " " . substr(explode("T", $value['fechavalidacion'])[1], 0, 5),
					/* 'ubicacion_historia_clinica' => mb_strtoupper(utf8_encode($value[''])), */
					'historia_clinica' => mb_strtoupper(utf8_encode($value['Historia'])),
					/* 'pagina_numero' => , */
					/* 'ubicacion' => , */
					'fecha_impresion' => date("d/m/Y", strtotime(explode("T", $value['FecImpresion'])[0])) . " " . substr(explode("T", $value['FecImpresion'])[1], 0, 5),
					'validador' => mb_strtoupper(utf8_encode($value['Validador'])),
					'colegiatura' => mb_strtoupper(utf8_encode($value['Colegio'])),
					'firma' => $value['Firma'],
					'data' => array($data)
				);

				array_push($items, $item);
			}
		}

		//exit;
		/* print("<pre>".print_r($items, true)."</pre>"); */
		/* var_dump($items); */

		foreach ($items as $key => $value) {
			$html = "";

			// datos de cabecera
			$html .= "<table id='cabecera'><tbody>";

			$html .= "<tr>
			<td><b>Orden</b></td><td>" . $items[$key]["orden"] . "</td>
			<td><b>Fecha Toma Muestra</b></td><td>" . $items[$key]["fecha_toma_muestra"] . "</td>
			</tr>
			<tr>
			<td><b>Nro. Cliente</b></td><td>" . $items[$key]["numero_cliente"] . "</td>
			<td><b>Fecha Resultado</b></td><td>" . $items[$key]["fecha_resultado"] . "</td>
			</tr>
			<tr>
			<td><b>Paciente</b></td><td>" . $items[$key]["paciente"] . "</td>
			<td><b>Ubic. H.C.</b></td><td></td>
			</tr>
			<tr>
			<td><b>Edad</b></td><td>" . $items[$key]["edad"] . "</td>
			<td><b>Historia Clínica (H.C.)</b></td><td>" . $items[$key]["historia_clinica"] . "</td>
			</tr>
			<tr>
			<td><b>Médico</b></td><td>" . $items[$key]["medico"] . "</td>
			<td><b>Página Número</b></td><td></td>
			</tr>
			<tr>
			<td><b>Compañía</b></td><td>" . $items[$key]["compania"] . "</td>
			<td><b>Ubicación</b></td><td></td>
			</tr>";
			
			$html .= "</tbody></table><br>";

			$html .=  "<div id='estudio'><b>" . $items[$key]["id"] . "</b></div><br>";

			$html .= "<table id='customers'>";
			$html .= "<thead>";
			$html .= "<tr>
			<th>Exámenes realizados</th>
			<th>Resultados</th>
			<th>Rangos Referenciales/Unidades</th>
			<th>Método</th>
			</tr>";
			$html .= "</thead>";
			$html .= "<tbody>";

			foreach ($items[$key]["data"] as $key1 => $value1) {
				$html .= "<tr>";
				$html .= "<td>" . $items[$key]["data"][$key1]["descripcion"] . "</td>";
				$html .= "<td>" . $items[$key]["data"][$key1]["resultado"] . "</td>" ;
				$html .= "<td>" . $items[$key]["data"][$key1]["rango"] . "</td>";
				$html .= "<td>" . $items[$key]["data"][$key1]["metodo"] . "</td>";
				$html .= "</tr>";
			}

			$html .= "</tbody>";
			$html .= "</table>";

			// header("Content-type: image/gif");
			// $data = $items[$key]["firma"];
			// echo base64_decode($items[$key]["firma"]);

			$foot = '<!--mpdf
			<htmlpagefooter name="myHTMLFooter" footer-style="float: right;">
				<table style="width: 100%;">
					<tr>
						<!-- <td><b>Fecha de Impresión</b> ' . $items[$key]["fecha_impresion"] . '<br></td> -->
						<td style="float: right;"> ' . "<img src=\"data:image/jpg;base64, " . $items[$key]["firma"] . "\"/><br>" . $items[$key]["validador"] . "<br>" . $items[$key]["colegiatura"] .  '<br></td>
					</tr>
				</table>
			</htmlpagefooter>
			mpdf-->';

			$mpdf->WriteHTML($estilo . '<body><div class="xxx">'  . $foot . $head . $html . '</div></body>');
			$mpdf->AddPage();
			
			/* print($estilo . '<body><div class="xxx">'  . $foot . $head . $html . '</div></body>'); */
		}

		$mpdf->Output();
	} else {
		print("Aún no se han cargado resultados para este examen.");
	}
?>