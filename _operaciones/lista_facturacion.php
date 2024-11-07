<?php
session_start();
$idinforme="";
$login = "";
date_default_timezone_set('America/Lima');

if (!!$_SESSION) {
	$login = $_SESSION['login'];
} else {
	http_response_code(400);
	echo json_encode(["message" => "no se ha iniciado sesión"]);
	exit();
}

if (isset($_POST["origen"]) && !empty($_POST["origen"])) {
	$origen = $_POST["origen"];
} else {
	exit();
}

require("../_database/database.php");
$estado_documento = [
		'101' => 'En proceso',
		'102' => 'Aceptado',
		'103' => 'Aceptado con observaciones',
		'104' => 'Rechazado por SUNAT',
		'105' => 'Anulado',
		'108' => 'Solicitado de baja'
];

switch ($origen) {
	case 'ingresar_tipo_cambio':
		if (!validateTipoCambio()) {
			$stmt = $db->prepare("INSERT INTO tipo_cambio (fecha, tipo_cambio_compra, tipo_cambio_venta, idusercreate) VALUES (?, ?, ?, ?)");
			$stmt->execute([date("Y-m-d"), $_POST["tipo_cambio_compra"], $_POST["tipo_cambio_venta"], $login]);
			echo json_encode([
				'status' => true
			]);
		}
		break;
	case 'verificar_tipo_cambio':
		if (validateTipoCambio()) {
			echo json_encode(['status' => false]);
		} else {
			echo json_encode(['status' => true]);
		}
		break;
	case 'reproduccion_asistida':
			$tip = $_POST["tip_recibo"];
			$num = $_POST["num_recibo"];

			$stmt = $db->prepare("SELECT * FROM recibos WHERE id=? AND tip=?");
			$stmt->execute([$num, $tip]);
			$data_recibo = $stmt->fetch(PDO::FETCH_ASSOC);

			$stmt = $db->prepare("SELECT
					id, fec, p_dni, p_dni_het, des_dia, des_don, p_cic, p_fiv, p_icsi, p_od, p_don, p_cri, p_iiu, don_todo, f_iny, cancela, p_extras, pago_extras
					from hc_reprod
					where estado = true and dni=?
					order by fec desc");
			$stmt->execute([$data_recibo['dni']]);
			$reproducciones_realizadas = "";

			while ($repro = $stmt->fetch(PDO::FETCH_ASSOC)) {
					$crio = '-';
					$bio = '-';

					if ($repro['cancela'] == 1) {
							$estado = 'Cancelado';
							$informe = '-';
							$fecha = $repro['fec'];
					} else if ($repro['cancela'] == 2) {
							$estado = 'Trasladado';
							$rAspi = $db->prepare("SELECT pro,fec FROM lab_aspira WHERE lab_aspira.rep=? and lab_aspira.estado is true");
							$rAspi->execute(array($repro['id']));
							$aspi = $rAspi->fetch(PDO::FETCH_ASSOC);
							$informe = '<a href="archivos_hcpacientes.php?idEmb=traslado_' . $aspi['pro'] . '.pdf" target="new">Ver traslados</a>';
							$fecha = $aspi['fec'];
					} else {
							if ($repro['p_iiu'] >= 1) {
									$Rcap = $db->prepare("SELECT * FROM lab_andro_cap WHERE iiu=? and eliminado is false");
									$Rcap->execute(array($repro['id']));
									$cap = $Rcap->fetch(PDO::FETCH_ASSOC);
									if ($cap['emb'] == 0) $estado = 'En Proceso'; else {
											$estado = 'Finalizado';
											$informe = '<a href="info.php?t=cap&a=' . $cap['p_dni'] . '&b=' . $cap['id'] . '&c=' . $paci['dni'] . '" target="new">Ver IIU</a>';
									}
									$fecha = $repro['fec'];

							} else {
									$rAspi = $db->prepare("SELECT pro,f_fin,dias,fec2,fec3,fec4,fec5,fec6,tip,n_ovo,n_ins,fec FROM lab_aspira WHERE lab_aspira.rep=? and lab_aspira.estado is true");
									$rAspi->execute(array($repro['id']));
									$aspi = $rAspi->fetch(PDO::FETCH_ASSOC);

									$fecha = $aspi['fec'];
									if ($aspi['f_fin'] == "1899-12-30") $estado = 'En Laboratorio';
									if ($aspi['f_fin'] && $aspi['f_fin'] <> "1899-12-30") $estado = 'Finalizado <i class="color2">' . date("d-m-Y", strtotime($aspi['f_fin'])) . '</i>';
									if ($repro['don_todo'] == 1) $estado = 'Finalizado <i class="color2">' . date("d-m-Y", strtotime($repro['f_iny'])) . '</i>';
									if (!$aspi['f_fin'] && $repro['don_todo'] <> 1) { $estado = 'En Proceso'; $fecha = $repro['fec']; }

									$rFalso = $db->prepare("SELECT * FROM hc_reprod_info WHERE pro=?");
									$rFalso->execute(array($aspi['pro']));

									if ($rFalso->rowCount() == 1) {
											$falso = $rFalso->fetch(PDO::FETCH_ASSOC);
											$tran = $falso['c_t'];
											$crio = $falso['c_c'];
											$bio = substr_count(strtoupper($falso['bio']), strtoupper("si"));
											if ($repro['des_dia'] >= 1) // si es TED o embrioadpocion
													$informe = 'Embriones Desvitrificados: ' . $falso['n_ovo'];
											else if ($repro['des_dia'] === 0) // si es Descongelacion Ovos
													$informe = 'Óvulos Desvitrificados: ' . $falso['n_ovo'];
											else
													$informe = 'Óvulos aspirados: ' . $falso['n_ovo'];

											if ((is_null($repro['des_dia']) or $repro['des_dia'] === 0) and $aspi['tip'] <> 'T') {
													$informe .= '<br>Óvulos Inseminados: ' . $falso['ins'];
											}

									} else { // reporte original
											$rCrio = $db->prepare("SELECT pro FROM lab_aspira_dias WHERE pro=? and estado is true AND (d6f_cic='C' OR d5f_cic='C' OR d4f_cic='C' OR d3f_cic='C' OR d2f_cic='C' OR d1f_cic='C' OR d0f_cic='C')");
											$rCrio->execute(array($aspi['pro']));
											$crio = $rCrio->rowCount();

											$rBio = $db->prepare("SELECT pro FROM lab_aspira_dias WHERE pro=? and estado is true AND (d6d_bio<>0 OR d5d_bio<>0 OR d3c_bio>0)");
											$rBio->execute(array($aspi['pro']));
											$bio = $rBio->rowCount();

											$rTran = $db->prepare("SELECT pro FROM lab_aspira_dias WHERE pro=? and estado is true AND (d6f_cic='T' OR d5f_cic='T' OR d4f_cic='T' OR d3f_cic='T' OR d2f_cic='T')");
											$rTran->execute(array($aspi['pro']));
											if ($rTran->rowCount() > 0) {
													//xxxxxxx
											}
											$tran = $rTran->rowCount();

											if ($repro['don_todo'] == 1) {
													$informe = 'Se donó Todo';
													$fecha = $repro['fec'];
											}else if ($aspi['pro'] > 0) {
													if ($repro['des_dia'] >= 1) // si es TED o embrioadpocion
															$informe = 'Embriones Desvitrificados: ' . $aspi['n_ovo'];
													else if ($repro['des_dia'] === 0) // si es Descongelacion Ovos
															$informe = 'Óvulos Desvitrificados: ' . $aspi['n_ovo'];
													else if ($aspi['tip'] == 'T') // traslado
															$informe = '-';
													else
															$informe = 'Óvulos aspirados: ' . $aspi['n_ovo'];

													if ((is_null($repro['des_dia']) or $repro['des_dia'] === 0) and $aspi['tip'] <> 'T') {
															if ($aspi['n_ins'] == 0) $n_ins = $aspi['n_ovo']; else $n_ins = $aspi['n_ins'];
															$informe .= '<br>Óvulos Inseminados: ' . $n_ins;
													}
											} else $informe = '-';
									}
							}
					}
					
					$reproducciones_realizadas .= "<tr>";
					$reproducciones_realizadas .= "<td>".date("d-m-Y", strtotime($fecha))."</td><td>";

					if ($repro['p_cic'] >= 1) $reproducciones_realizadas .= "Ciclo Natural<br>";
					if ($repro['p_fiv'] >= 1) $reproducciones_realizadas .= "FIV<br>";
					if ($repro['p_icsi'] >= 1) $reproducciones_realizadas .= "ICSI / PIEZO - ICSI<br>";
					if ($repro['p_od'] <> '') $reproducciones_realizadas .= "OD Fresco<br>";
					if ($repro['p_cri'] >= 1) $reproducciones_realizadas .= "Crio Ovulos<br>";
					if ($repro['p_iiu'] >= 1) $reproducciones_realizadas .= "IIU<br>";
					if ($repro['p_don'] == 1) $reproducciones_realizadas .= "Donación Fresco<br>";
					if ($repro['des_don'] == null and $repro['des_dia'] >= 1) $reproducciones_realizadas .= "TED<br>";
					if ($repro['des_don'] == null and $repro['des_dia'] === 0) $reproducciones_realizadas .= "<small>Descongelación Ovulos Propios</small><br>";
					if ($repro['des_don'] <> null and $repro['des_dia'] >= 1) $reproducciones_realizadas .= "EMBRIODONACIÓN<br>";
					if ($repro['des_don'] <> null and $repro['des_dia'] === 0) $reproducciones_realizadas .= "<small>Descongelación Ovulos Donados</small><br>";
					$reproducciones_realizadas .= "</td>";

					$reproducciones_realizadas .= "<td>";
					if ($repro['pago_extras'] == '') {
							$reproducciones_realizadas .= "-";
					} else {
							$reproducciones_realizadas .= $repro['pago_extras'];
					}
					$reproducciones_realizadas .= "</td>";

					$reproducciones_realizadas .= "<td class='text-center'>".$tran."</td>";
					$reproducciones_realizadas .= "<td class='text-center'>".$crio."</td>";
					$reproducciones_realizadas .= "<td class='text-center'>".$bio."</td>";
					$reproducciones_realizadas .= "<td class='text-center'>".$informe."</td>";
					$reproducciones_realizadas .= "<td class='text-center'>".$estado."</td></tr>";
			}

			echo json_encode([
					"numerorecibo" => $num,
					"fecha" => date("d-m-Y", strtotime($data_recibo['fec'])),
					"paciente" => $data_recibo['nom'],
					"medico" => $data_recibo['med'],
					"servicios_pagados" => cargarDetalleComprobante($data_recibo['ser'], $data_recibo['tip']),
					"reproducciones_realizadas" => $reproducciones_realizadas
			]);

			break;
	case 'btn_si_modal_documento_credito':
			parse_str($_POST['data_cabecera'], $data_cabecera);
			$data_detalle = $_POST["data_detalle"];
			$data_referencia = $_POST["data_referencia"];

			// datos de facturacion
			$stmt = $db->prepare("INSERT INTO factu_datosfacturacion (
					documentotipo_id, numero, nombre, direccion, correo, idusercreate) VALUES (?, ?, ?, ?, ?, ?)");
			$stmt->execute(array(
					$data_cabecera['documentotipo_id'],
					$data_cabecera['numero'],
					$data_cabecera['nombre'],
					$data_cabecera['direccion'],
					$data_cabecera['correo'],
					$login
			));

			$factu_datosfacturacion_id = $db->lastInsertId();

			// nota credito cabecera
			$stmt = $db->prepare("INSERT INTO factu_notacredito (
					recibo_tip, recibo_id, comprobantetipo_id, datosfacturacion_id, moneda_id, motivo_id, serie, correlativo, total, observacion, idusercreate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
			$stmt->execute(array(
					$data_referencia["tip_recibo"],
					$data_referencia["num_recibo"],
					$data_cabecera["comprobantetipo_id"],
					$factu_datosfacturacion_id,
					$data_cabecera["moneda"],
					$data_cabecera["motivotipo_id"],
					$data_cabecera["serie"],
					$data_cabecera["correlativo"],
					$data_cabecera["total"],
					$data_cabecera["observacion"],
					$login
			));

			$factu_notacredito_id = $db->lastInsertId();

			// nota credito detalle
			foreach ($data_detalle as $key => $value) {
					$stmt = $db->prepare("INSERT INTO factu_notacredito_detalle (
							factu_notacredito_id, servicio_id, cantidad, nombre, precio, idusercreate) VALUES (?, ?, ?, ?, ?, ?)");
					$stmt->execute(array(
							$factu_notacredito_id,
							$value['servicio_id'],
							$value['cantidad'],
							$value['nombre'],
							$value['precio'],
							$login
					));
			}

			// facturacion electronica: nc/ nd
			require($_SERVER["DOCUMENT_ROOT"]."/_database/db_facturacion_electronica.php");

			// tipo documento facturacion
			$consulta = $db->prepare("select codigo_facturacion_electronica documentotipo_codigo from man_tipo_documento_facturacion where estado = 1 and id = ?");
			$consulta->execute(array( $data_cabecera['documentotipo_id'] ));
			$info1 = $consulta->fetch(PDO::FETCH_ASSOC);

			$consulta = $db->prepare("select codigo motivotipo_codigo from man_tipo_comprobante where estado = 1 and id = ?");
			$consulta->execute(array( $data_cabecera['motivotipo_id'] ));
			$info2 = $consulta->fetch(PDO::FETCH_ASSOC);

			$consulta = $db->prepare("SELECT m.id, m.tipo_cpe, m.serie_cpe, m.correlativo_cpe, m.createdate fecha
					from facturacion_recibo_mifact_response m
					where m.estado = 1 and m.id = (
							select a.id
							from facturacion_recibo_mifact_response a
							where a.estado = 1 and a.tip_recibo = ? and a.id_recibo = ? and a.estado_documento in ('101', '102', '103')
							order by id desc
							limit 1 offset 0)");
			$consulta->execute(array( $data_referencia["tip_recibo"], $data_referencia["num_recibo"] ));
			$info3 = $consulta->fetch(PDO::FETCH_ASSOC);

			$stmt = $db->prepare("SELECT me.* from man_empresas me where me.id = (select r.id_empresa from recibos r where r.id =? and r.tip =?)");
			$stmt->execute(array( $data_referencia["num_recibo"], $data_referencia["tip_recibo"] ));
			$empresa = $stmt->fetch(PDO::FETCH_ASSOC);

			$data = array(
					"documentotipo_codigo" => $info1["documentotipo_codigo"],
					"numero" => $data_cabecera["numero"],
					"nombre" => $data_cabecera["nombre"],
					"direccion" => $data_cabecera["direccion"],
					"correo" => $data_cabecera["correo"],
					"comprobantetipo_codigo" => $data_cabecera['comprobantetipo_id'] == 3 ? "07" : "08",
					"serie" => $data_cabecera["serie"],
					"correlativo" => $data_cabecera["correlativo"],
					"moneda_codigo" => $data_cabecera["moneda"] == 1 ? "PEN" : "USD",
					"total" => $data_cabecera["total"],
					"observacion" => $data_cabecera["observacion"],
					"comprobantetipo_nombre" => $data_cabecera['comprobantetipo_id'] == 3 ? "COD_TIP_NC" : "COD_TIP_ND",
					"motivotipo_codigo" => $info2["motivotipo_codigo"],
					"data_detalle" => $_POST["data_detalle"],
					"tipo_cpe_referencia" => $info3["tipo_cpe"],
					"tip" => $data_referencia["tip_recibo"],
					"serie_cpe_referencia" => $info3["serie_cpe"],
					"correlativo_cpe_referencia" => $info3["correlativo_cpe"],
					"fecha_referencia" => $info3["fecha"],
					"empresa" => $empresa,
			);
			
			$request = json_encode(cargar_facturacion_electronica_credito($data), true);
			$response = enviar_facturacion_electronica($request, "/SendInvoice", $empresa);

			// grabar log
			$stmt = $db->prepare('INSERT INTO factu_mifact_response (recibo_tip, recibo_id, request, response, error, idusercreate) VALUES (?, ?, ?, ?, ?, ?)');
			$stmt->execute([$data_cabecera['comprobantetipo_id'], $factu_notacredito_id, $request, $response["cadena_para_codigo_qr"], $response["errors"], $login]);

			// grabar respuesta
			$stmt = $db->prepare("INSERT INTO facturacion_recibo_mifact_response (
					id_recibo, tip_recibo, cadena_para_codigo_qr, cdr_sunat, codigo_hash, correlativo_cpe, errors, estado_documento, pdf_bytes, serie_cpe, sunat_description, sunat_note, sunat_responsecode, ticket_sunat, tipo_cpe, url, xml_enviado, idusercreate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
			$stmt->execute(array(
					$factu_notacredito_id,
					$data_cabecera['comprobantetipo_id'],
					$response["cadena_para_codigo_qr"],
					$response["cdr_sunat"],
					$response["codigo_hash"],
					$response["correlativo_cpe"],
					$response["errors"],
					$response["estado_documento"],
					$response["pdf_bytes"],
					$response["serie_cpe"],
					$response["sunat_description"],
					$response["sunat_note"],
					$response["sunat_responsecode"],
					$response["ticket_sunat"],
					$response["tipo_cpe"],
					$response["url"],
					$response["xml_enviado"],
					$login
			));


			// actualizar correlativo
			if ( $data_referencia["tip_recibo"] == 1 ) {
					if ( $data_cabecera["comprobantetipo_id"] == 3 ) {
							$configuracion_codigo = "nota_credito_boleta";
					} else {
							$configuracion_codigo = "nota_debito_boleta";
					}
			} else {
					if ( $data_cabecera["comprobantetipo_id"] == 3 ) {
							$configuracion_codigo = "nota_credito_factura";
					} else {
							$configuracion_codigo = "nota_debito_factura";
					}
			}

			$stmt = $db->prepare("UPDATE man_correlativos set valor=valor+1 where estado=1 and codigo = ? and sede_id = ?");
			$stmt->execute(array($configuracion_codigo,$data_cabecera["sede_id"]));

			echo json_encode(array(
					'status' => true,
					'demo1' => "realizado"
			));

			break;
	case 'btn_si_modal_factura_electronica':
			$tip = $_POST["tip_recibo"];
			$num = $_POST["num_recibo"];

			try {
					require("../_database/database.php");
					require_once("../_database/db_facturacion_electronica.php");

					$data = array('id' => $num, 'tip' => $tip);
					$response = consultar_facturacion_electronica($data);

					// grabar respuesta
					$stmt = $db->prepare("INSERT INTO facturacion_recibo_mifact_response (
							id_recibo, tip_recibo
							, cadena_para_codigo_qr, cdr_sunat, codigo_hash, correlativo_cpe, errors, estado_documento, pdf_bytes, serie_cpe, sunat_description, sunat_note, sunat_responsecode, ticket_sunat, tipo_cpe, url, xml_enviado
							, idusercreate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
					$stmt->execute(array($num, $tip, $response["cadena_para_codigo_qr"], $response["cdr_sunat"], $response["codigo_hash"], $response["correlativo_cpe"], $response["errors"], $response["estado_documento"], $response["pdf_bytes"], $response["serie_cpe"], $response["sunat_description"], $response["sunat_note"], $response["sunat_responsecode"], $response["ticket_sunat"], $response["tipo_cpe"], $response["url"], $response["xml_enviado"], $login));

					$data = '<tr><td class="text-center">-</td><td>' . date("Y-m-d") . '</td><td>' . $response["serie_cpe"] . '</td><td>' . $response["correlativo_cpe"] . '</td><td>' . $estado_documento[$response["estado_documento"]] . '</td><td>' . $response["sunat_description"] . '</td></tr>';
					http_response_code(201);
					echo $data;
			} catch( Exception  $e ) {
					http_response_code(400);
					echo json_encode(array("data" => $e->getMessage()));
			}

			break;
	case 'modal_factura_electronica':
			$tip = $_POST["tip_recibo"];
			$num = $_POST["num_recibo"];

			
			$consulta = $db->prepare("SELECT * from obtener_log_facturacion(?,?)");
			$consulta->execute(array($tip, $num));

			print('
					<thead class="thead-dark">
							<tr>
									<th class="text-center">Item</th>
									<th class="text-center">TABLA BD</th>
									<th class="text-center">Fecha</th>
									<th>Serie</th>
									<th class="text-center">Correlativo</th>
									<th class="text-center">Estado</th>
									<th>TRAMA</th>
									<th>Observaciones</th>
							</tr>
					</thead>
					<tbody>');

					$item=1;

					while ($info = $consulta->fetch(PDO::FETCH_ASSOC)) {
							print("<tr>
									<td class='text-center'>".$item++."</td>
									<td>".$info['tabla']."</td>
									<td>" . date('Y-m-d H:i:s', strtotime( $info['fecha'] . " -5 hour" )) . "</td>
									<td>".$info['serie']."</td>
									<td>".$info['correlativo']."</td>
									<td>".$info['estado']."</td>
									<td>".$info['trama']."</td>
									<td>".(empty($info['observacion']) ? '-' : $info['observacion'])."</td></tr>");
					}
			print("</tbody>");
			break;
	
	case 'modal_documento_credito':
			$tip = $_POST["tip_recibo"];
			$num = $_POST["num_recibo"];

			$consulta = $db->prepare("SELECT *
					from recibos
					where tip = ? and id = ?");
			$consulta->execute( array( $tip, $num ) );
			$info = $consulta->fetch(PDO::FETCH_ASSOC);
			$cadena_detalle = "<tbody>";

			// agregar detalle
			$i = 1;
			$cadena = $info["ser"];
			$total = substr_count($info['ser'], "</tr>");
			$items = [];
			$precio = "";

			while ($i <= $total) {
					$pos = strpos($cadena, "</tr>");
					$tam = strlen($cadena);
					$servicios = substr($cadena, 4, $pos-4);
					$cadena = substr($cadena, $pos+5, $tam-3);
					$nombre="";
					$precio="";
					$codigoproducto="";
					$idserviciopos = strpos($servicios, "</td>");
					$tamservicio = strlen($servicios);
					$codigoproducto = substr($servicios, 4, $idserviciopos-4);
					$cadena1 = substr($servicios, $idserviciopos+5, $tamservicio-3);
					$demopos = strpos($cadena1, "</td>");
					$tamdemo = strlen($cadena1);
					$nombre = substr($cadena1, 4, $demopos-4);
					$precio = substr(substr($cadena1, $demopos+5, $tamdemo-3), 4, strlen($precio)-5);
					$i++;
					// $cadena_detalle.=$nombre;
					// $cadena_detalle.=$codigoproducto;
					// $cadena_detalle.=$precio;
					$cadena_detalle.='<tr>
					<td class="text-center">' . $codigoproducto . '</td>
					<td class="text-center">1</td>
					<td>' . $nombre . '</td>
					<td class="text-center">
							<input type="number" class="text-center form-control item_cantidad" value="1" onchange="' . ($info["tip"] == 1 ? 'calcular_total_bo()' : 'calcular_total_ft()' ) . '">
					</td>
					<td class="text-center" style="vertical-align: middle;">
							<input type="number" class="text-center form-control item_precio" value="' . $precio . '" onchange="' . ($info["tip"] == 1 ? 'calcular_total_bo()' : 'calcular_total_ft()' ) . '">
					</td>
					<td class="text-center" style="vertical-align: middle;">
							<input type="number" class="text-center form-control item_valorventa" value="' . $precio . '" readonly>
					</td></tr>';
			}

			$moneda_id = 0;
			$moneda_select_1 = "";
			$moneda_select_2 = "";

			if ($info['t_ser'] == 1 || $info['t_ser'] == 2 || $info['t_ser'] == 3) {
					if ($info['mon'] == 1) { $moneda_id = 2; } else { $moneda_id = 1; }
			} else {
					if ($info['mon'] == 1) { $moneda_id = 1; } else { $moneda_id = 2; }
			}

			if ($moneda_id == 1) {
					$moneda_select_1 = "selected";
			} else {
					$moneda_select_2 = "selected";
			}
			
			$cadena_detalle .= "</tbody><tfoot>";

			$cadena_detalle .= '<tr><th></th><th></th><th></th><th></th><th class="text-right" style="vertical-align: middle;">Moneda</th><th>
			<select name="moneda" id="moneda" class="form-control">
					<option value="1" ' . $moneda_select_1 . '>Soles</option>
					<option value="2" ' . $moneda_select_2 . '>Dólares</option>
			</select></tr>';

			if ($info["tip"] == 2) {
					$cadena_detalle .= "
							<tr>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th class='text-right'>Subtotal</th>
									<th class='text-center'>
											<input type='number' class='text-center form-control item_valorventa' id='subtotal' name='subtotal' value='" . (string)number_format($info["tot"]/ 1.18, 2, '.', '') . "' readonly>
									</th>
							</tr>
							<tr>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th class='text-right'>IGV</th>
									<th class='text-center'>
											<input type='number' class='text-center form-control item_valorventa' id='igv' name='igv' value='" . (string)number_format(0.18 * $info["tot"] / 1.18, 2, '.', '') . "' readonly>
									</th>
							</tr>";
			}

			$cadena_detalle .= "<tr>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th class='text-right'>Total</th>
					<th class='text-center'>
							<input type='number' class='text-center form-control item_valorventa' id='total' name='total' value='" . (string)number_format($info["tot"], 2, '.', '') . "' readonly>
					</th>
			</tr>";

			$cadena_detalle .= "</tfoot>";

			echo json_encode( array(
					"status" => true,
					"lista_detalle" => $cadena_detalle,
					"documentotipo_id" => $info["id_tipo_documento_facturacion"],
					"numero"  => $info["ruc"],
					"sede_id"  => $info["sede_pago_id"],
					"nombre" => $info["raz"],
					"direccion" => $info["direccionfiscal"],
					"correo" => $info["correo_electronico"]
			));

			break;
	
	case 'consulta_configuracion':
			$recibo_tip = $_POST["recibo_tip"];
			$recibo_id = $_POST["recibo_id"];
			$comprobantetipo_id = $_POST["comprobantetipo_id"];
			$configuracion_codigo = "";

			$stmt = $db->prepare("SELECT sede_pago_id from recibos where tip = ? and id = ?");
			$stmt->execute([$recibo_tip, $recibo_id]);
			$data = $stmt->fetch(PDO::FETCH_ASSOC);

			if ( $recibo_tip == 1 ) {
					if ( $comprobantetipo_id == 3 ) {
							$configuracion_codigo = "nota_credito_boleta";
					} else {
							$configuracion_codigo = "nota_debito_boleta";
					}
			} else {
					if ( $comprobantetipo_id == 3 ) {
							$configuracion_codigo = "nota_credito_factura";
					} else {
							$configuracion_codigo = "nota_debito_factura";
					}
			}
			
			$consulta = $db->prepare("SELECT *
					from man_configuracion
					where estado = 1 and sede_id = ? and codigo = ?");
			$consulta->execute(array($data["sede_pago_id"], $configuracion_codigo));
			$serie_data = $consulta->fetch(PDO::FETCH_ASSOC);

			$consulta = $db->prepare("SELECT *
					from man_correlativos
					where estado = 1 and sede_id = ? and codigo = ?");
			$consulta->execute(array($data["sede_pago_id"], $configuracion_codigo));
			$correlativo_data = $consulta->fetch(PDO::FETCH_ASSOC);

			$consulta = $db->prepare("SELECT *
					from man_tipo_comprobante
					where estado = 1 and comprobante_id = ?");
			$consulta->execute( array( $comprobantetipo_id ) );
			$lista_motivo = "<option value=''>Seleccionar</option>";

			while ($info = $consulta->fetch(PDO::FETCH_ASSOC)) {
					$lista_motivo .= '<option value="' . $info["id"] . '">' . $info["descripcion"] . '</option>';
			}

			echo json_encode( array( 
					"status" => true,
					"serie" => $serie_data["valor"],
					"correlativo" => str_pad($correlativo_data["valor"] + 1, 8, "0", STR_PAD_LEFT),
					"lista_motivo" => $lista_motivo
			) );
			break;

	default: break;
}

function cargarDetalleComprobante($cadena, $comprobante_tipo_id) {
    $i = 1;
    $detalle_comprobante = "";
    $total = substr_count($cadena, "</tr>");
    $valorventacod = "";

    while ($i <= $total) {
        $descripcion="";
        $idservicio="";
        $valorventacod=""; // precio venta del detalle de servicio
        $pos = strpos($cadena, "</tr>");
        $servicios = substr($cadena, 4, $pos-4);
        $cadena = substr($cadena, $pos+5, strlen($cadena)-3);
        $idserviciopos = strpos($servicios, "</td>");
        $tamservicio = strlen($servicios);
        $idservicio = substr($servicios, 4, $idserviciopos-4); // codigo del servicio
        $cadena1 = substr($servicios, $idserviciopos+5, $tamservicio-3);
        $demopos = strpos($cadena1, "</td>");
        $tamdemo = strlen($cadena1);
        $descripcion = substr($cadena1, 4, $demopos-4); // descripcion del detalle de servicio
        $valorventa = substr($cadena1, $demopos+5, $tamdemo-3);
        $valorventacod = substr($valorventa, 4, strlen($valorventacod)-5);

        if ($comprobante_tipo_id == 2) {
            $valorventacod = number_format((float)$valorventacod * 1.18, 3, '.', '');
        }
        $detalle_comprobante .= "<tr><td>$idservicio</td><td>$descripcion</td><td>".number_format((float)$valorventacod, 2, '.', '')."</td></tr>";
        $i++;
    }

    return $detalle_comprobante;
}

function validateTipoCambio() {
	global $db;
	$stmt = $db->prepare("SELECT id from tipo_cambio where estado = 1 and fecha=?;");
	$stmt->execute([date("Y-m-d")]);
	$stmt->fetch(PDO::FETCH_ASSOC);
	if ($stmt->rowcount() == 0) {
		return false;
	} else {
		return true;
	}
}
?>