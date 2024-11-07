<?php
session_start();
require($_SERVER["DOCUMENT_ROOT"] . "/_database/database.php");
require($_SERVER["DOCUMENT_ROOT"] . "/config/environment.php");

$login = "";
if (!!$_SESSION) {
	$login = $_SESSION['login'];
} else {
	http_response_code(400);
	echo json_encode(["message" => "no se ha iniciado sesión"]);
	exit();
}

if (isset($_POST["tipo"]) && !empty($_POST["tipo"])) {
	switch ($_POST["tipo"]) {
		case 'get-data':
			http_response_code(200);
			echo json_encode(["message" => getData($_POST)]);
			break;
		case 'descargar-reporte':
			http_response_code(200);
			echo json_encode(["message" => descargarReporte($_POST)]);
			break;
		default:
			http_response_code(400);
			echo json_encode(["message" => "la operacion no existe"]);
			break;
	}
} else {
	http_response_code(400);
	echo json_encode(["message" => "no se enviaron los parametros correctamente"]);
	exit();
}

function getData($data) {
	return ['content' => getHtml(getQuery($data))];
	// return $data["dias_semana"];
}
function getQuery($data) {
	$between=" and 1=2";
	if ($data["fecha_inicio"] != "" && $data["fecha_fin"] != "") {
		$ini=$data["fecha_inicio"];
		$fin=$data["fecha_fin"];
		// $between = " and hr.fec between '$ini' and '$fin'";
		$between = " and (
			(hr.des_dia >= 1 and hr.f_tra is not null and CAST(hr.f_tra as date) between '$ini' and '$fin') or
			(hr.des_dia >= 1 and hr.f_tra is null and CAST(hr.f_iny as date) between '$ini' and '$fin') or
			(hr.p_fiv = 1 and CAST(hr.f_asp as date) between '$ini' and '$fin') or
			(hr.p_icsi = 1 and CAST(hr.f_asp as date) between '$ini' and '$fin') or
			(hr.p_cri = 1 and CAST(hr.f_asp as date) between '$ini' and '$fin')
		)";
	}
	if ($data["protocolo"] != "") {
		$between = " and la.pro = '".$data["protocolo"]."'";
	}
	if (isset($data["dias_semana"]) && $data["dias_semana"] != "") {
		$where_in="-1";
		foreach ($data["dias_semana"] as $key => $value) {
			$where_in .= ", $value";
		}
		// $between .= " and weekday(hr.fec) in ($where_in)";
		$between .= " and (
			(hr.des_dia >= 1 and hr.f_tra is not null and weekday(CAST(hr.f_tra as date)) in ($where_in)) or
			(hr.des_dia >= 1 and hr.f_tra is null and weekday(CAST(hr.f_iny as date)) in ($where_in)) or
			(hr.p_fiv = 1 and weekday(CAST(hr.f_asp as date)) in ($where_in)) or
			(hr.p_icsi = 1 and weekday(CAST(hr.f_asp as date)) in ($where_in)) or
			(hr.p_cri = 1 and weekday(CAST(hr.f_asp as date)) in ($where_in))
		)";
	}
	global $db;
	$stmt=$db->prepare("SELECT
		hr.id
		, case
			when hr.des_dia >= 1 and hr.f_tra <> '1899-12-30' then hr.f_tra
			when hr.des_dia >= 1 and hr.f_tra = '1899-12-30' then hr.f_iny
			when hr.p_fiv = 1 or hr.p_icsi = 1 then CAST(hr.f_asp as date)
			else CAST(hr.f_asp as date)
			end fecha
		, EXTRACT(dow FROM hr.fec) AS week
		, la.pro protocolo
		, la.dias
		, hr.p_dtri, hr.p_cic, hr.p_od, hr.p_cri, hr.p_iiu, hr.p_don, hr.des_don, hr.des_dia, hr.pago_extras
		, COALESCE(hr.p_fiv, 0) fiv, COALESCE(hr.p_icsi, 0) icsi
		, COALESCE(hr.p_cri, 0) crio_ovulos
		, la.emb_a, la.emb0c, la.emb1, la.emb1c, la.emb2c, la.emb3c, la.emb4c, la.emb5c, la.emb6c
		, COUNT(CASE WHEN lad.d0est = 'MII' AND lad.d0f_cic = 'c' THEN true END) crio_ovulos_total
		, LOWER(la.o_ovo) origen_ovocitos
		, UPPER(hp.ape) apellidos, UPPER(hp.nom) nombres
		, hr.p_fiv, hr.p_icsi, la.n_ovo aspirados
		, COUNT(CASE WHEN hr.p_fiv = 1 and lad.d1est = 'MII' AND lad.d1c_pol = '2' AND lad.d1pron = '2' THEN true END) cantidad_fiv
		, COUNT(CASE WHEN hr.p_icsi = 1 and lad.d1est = 'MII' AND lad.d1c_pol = '2' AND lad.d1pron = '2' THEN true END) cantidad_icsi
		, COUNT(CASE WHEN lad.d5d_bio<>0 or lad.d6d_bio<>0 THEN true END) cantidad_biopsiados
		, COUNT(CASE WHEN lad.d1est = 'ATR' THEN true END) atresicos
		, COUNT(CASE WHEN lad.d1est = 'CT' THEN true END) citolizados
		, COUNT(CASE WHEN lad.d1est = 'VG' OR lad.d1est = 'MI' THEN true END) inmaduros
		, COUNT(CASE WHEN lad.d1est = 'MII' AND lad.d1f_cic = 'o' AND lad.d1c_pol = '2' AND lad.d1pron = '2' THEN true END) fecundados
		, COUNT(CASE WHEN lad.d1est = 'MII' AND lad.d1f_cic = 'N' AND ((lad.d1c_pol = '0' OR lad.d1c_pol = '1' OR lad.d1c_pol = '2') AND (lad.d1pron = '0' OR lad.d1pron = '1' OR lad.d1pron = '2')) THEN true END) no_fecundados
		, COUNT(CASE WHEN lad.d1est = 'MII' AND lad.d1f_cic = 'N' AND (lad.d1c_pol = '3' OR lad.d1c_pol = '4' OR lad.d1c_pol = 'MULT' OR lad.d1pron = '3' OR lad.d1pron = '4' OR lad.d1pron = 'mul') THEN true END) triploides
		-- crio embriones
		, COUNT(CASE WHEN hr.p_extras ilike ('%ngs%') and (lad.d5f_cic = 'c' OR lad.d6f_cic = 'c') THEN true END) crio_embriones_total
		-- transferencias
		, COUNT(CASE WHEN lad.d5f_cic = 't' OR lad.d6f_cic = 't' THEN true END) ted_total
		, COUNT(CASE WHEN hr.pago_extras ILIKE '%NGS%' AND la.dias >= 5 AND la.tip <> 'T' AND (((lad.d5d_bio<>0) AND lad.d5f_cic='C') OR (lad.d6d_bio<>0 AND lad.d6f_cic='C')) THEN true END) ngs_total
		, COUNT(CASE WHEN ((lad.d5d_bio<>0) AND lad.d5f_cic='C' AND lad.ngs1 = 1) OR (lad.d6d_bio<>0 AND lad.d6f_cic='C' AND lad.ngs1 = 1) THEN true END) ngs_normal
		, COUNT(CASE WHEN ((lad.d5d_bio<>0) AND lad.d5f_cic='C' AND lad.ngs1 = 2) OR (lad.d6d_bio<>0 AND lad.d6f_cic='C' AND lad.ngs1 = 2) THEN true END) ngs_anormal
		, COUNT(CASE WHEN ((lad.d5d_bio<>0) AND lad.d5f_cic='C' AND lad.ngs1 = 3) OR (lad.d6d_bio<>0 AND lad.d6f_cic='C' AND lad.ngs1 = 3) THEN true END) ngs_nr
		, COUNT(CASE WHEN ((lad.d5d_bio<>0) AND lad.d5f_cic='C' AND lad.ngs1 = 4) OR (lad.d6d_bio<>0 AND lad.d6f_cic='C' AND lad.ngs1 = 4) THEN true END) ngs_mosaico
		from hc_reprod hr
		inner join hc_paciente hp on hp.dni = hr.dni
		inner join lab_aspira la on la.rep = hr.id and la.estado is true
		inner join lab_aspira_dias lad on lad.pro = la.pro and lad.estado is true
		where hr.estado = true and 1=1
		and hr.cancela <> 1$between
		group by la.rep, la.pro,hr.id,hp.ape,hp.nom
		order by hr.id desc;");
	$stmt->execute();
	return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getHtml($data) {
	$html='';
	$html_modal='';
	$html_modal_procedimiento='';
	global $db;
	$resumen_totales = [];
	$resumen_totales_procedimiento = [];

	// embriologos
	$stmt1 = $db->prepare("SELECT id, nom embriologo from lab_user where eliminado_repo_biologos = 0 order by orden;");
	$stmt1->execute();
	while ($item = $stmt1->fetch(PDO::FETCH_ASSOC)) {
		$resumen_totales[$item["id"]]["total_crioembriones"] = 0;
		$resumen_totales[$item["id"]]["total_transferencia"] = 0;
		$resumen_totales[$item["id"]]["total_criovos"] = 0;
		$resumen_totales[$item["id"]]["total_aspirados"] = 0;
		$resumen_totales[$item["id"]]["total_desvitrificados"] = 0;
		$resumen_totales[$item["id"]]["total_fiv"] = 0;
		$resumen_totales[$item["id"]]["total_icsi"] = 0;
		$resumen_totales[$item["id"]]["total_biopsiados"] = 0;
		$resumen_totales_procedimiento[$item["id"]]["total_crioembriones"] = 0;
		$resumen_totales_procedimiento[$item["id"]]["total_transferencia"] = 0;
		$resumen_totales_procedimiento[$item["id"]]["total_criovos"] = 0;
		$resumen_totales_procedimiento[$item["id"]]["total_aspirados"] = 0;
		$resumen_totales_procedimiento[$item["id"]]["total_desvitrificados"] = 0;
		$resumen_totales_procedimiento[$item["id"]]["total_fiv"] = 0;
		$resumen_totales_procedimiento[$item["id"]]["total_icsi"] = 0;
		$resumen_totales_procedimiento[$item["id"]]["total_biopsiados"] = 0;
	}

	// query
	foreach ($data as $item) {
		$procedimiento = "";
		if ($item['p_dtri'] >= 1) $procedimiento .= "DUAL TRIGGER<br>";
		if ($item['p_cic'] >= 1) $procedimiento .= "CICLO NATURAL<br>";
		if ($item['p_fiv'] >= 1) $procedimiento .= "FIV<br>";
		if ($item['p_icsi'] >= 1) $procedimiento .= $_ENV["VAR_ICSI"] . "<br>";
		if ($item['p_od'] <> '') $procedimiento .= "OD FRESCO<br>";
		if ($item['p_cri'] >= 1) $procedimiento .= "CRIO ÓVULOS<br>";
		if ($item['p_iiu'] >= 1) $procedimiento .= "IIU<br>";
		if ($item['p_don'] == 1) $procedimiento .= "DONACIÓN FRESCO<br>";
		if ($item['des_don'] == null and $item['des_dia'] >= 1) $procedimiento .= "TED<br>";
		if ($item['des_don'] == null and $item['des_dia'] === 0) $procedimiento .= "<small>Descongelación Ovulos Propios</small><br>";
		if ($item['des_don'] <> null and $item['des_dia'] >= 1) $procedimiento .= "EMBRIODONACIÓN<br>";
		if ($item['des_don'] <> null and $item['des_dia'] === 0) $procedimiento .= "<small>Descongelación Ovulos Donados</small><br>";
		if (strpos($item['pago_extras'], "EMBRYOSCOPE") !== false) {$procedimiento .= "EMBRYOSCOPE<br>";}
		// buscar embriologo ted
		if ($item["ted_total"] != 0) {
			$stmt = $db->prepare("SELECT
				lu.id, lu.nom embriologo_ted
				FROM lab_aspira_t lat
				INNER JOIN lab_user lu ON lu.id = lat.emb
				WHERE lat.pro = ? and lat.estado is true;");
			$stmt->execute([$item["protocolo"]]);
			$data_ted = $stmt->fetch(PDO::FETCH_ASSOC);
			$resumen_totales[$data_ted["id"]]["total_transferencia"] += $item["ted_total"];
			$resumen_totales_procedimiento[$data_ted["id"]]["total_transferencia"] += 1;
		}
		// buscar embriologo crio ovulos
		if ($item["crio_ovulos"] != 0 && $item["emb0c"] <> 0) {
			$stmt = $db->prepare("SELECT lu.nom embriologo FROM lab_user lu WHERE lu.id = ?;");
			$stmt->execute([$item["emb0c"]]);
			$data_crio_ovulos = $stmt->fetch(PDO::FETCH_ASSOC);
			$resumen_totales[$item["emb0c"]]["total_criovos"] += $item["crio_ovulos_total"];
			$resumen_totales_procedimiento[$item["emb0c"]]["total_criovos"]++;
		}
		// buscar embriologo crio embriones
		if ($item["crio_embriones_total"] != 0) {
			$embriologo_id = $item["emb".($item["dias"] - 1)."c"];
			if ($embriologo_id == 0) {
				$embriologo_id = $item["emb".($item["dias"] - 2)."c"];
			}
			$stmt = $db->prepare("SELECT lu.nom embriologo FROM lab_user lu WHERE lu.id = ?;");
			$stmt->execute([$embriologo_id]);
			$data_crio_embriones = $stmt->fetch(PDO::FETCH_ASSOC);
			$resumen_totales[$embriologo_id]["total_crioembriones"] += $item["crio_embriones_total"];
			$resumen_totales_procedimiento[$embriologo_id]["total_crioembriones"]++;
		}
		// buscar embriologo aspirados
		if ($item["emb_a"] <> 0) {
			$stmt = $db->prepare("SELECT lu.nom embriologo FROM lab_user lu WHERE lu.id = ?;");
			$stmt->execute([$item["emb_a"]]);
			if ($item["origen_ovocitos"] == "fresco") {
				$resumen_totales[$item["emb_a"]]["total_aspirados"] += $item["aspirados"];
				$resumen_totales_procedimiento[$item["emb_a"]]["total_aspirados"]++;
			} else {
				$resumen_totales[$item["emb_a"]]["total_desvitrificados"] += $item["aspirados"];
				$resumen_totales_procedimiento[$item["emb_a"]]["total_desvitrificados"]++;
			}
		}
		// buscar embriologo fiv
		if ($item["fiv"] == 1) {
			$embriologo_id = $item["emb".($item["dias"] - 1)."c"];
			if ($embriologo_id != 0) {
				$resumen_totales[$embriologo_id]["total_fiv"] += $item["cantidad_fiv"];
				$resumen_totales_procedimiento[$embriologo_id]["total_fiv"]++;
			}
		}
		// buscar embriologo icsi
		if ($item["icsi"] == 1) {
			$embriologo_id = $item["emb".($item["dias"] - 1)."c"];
			if ($embriologo_id != 0) {
				$resumen_totales[$embriologo_id]["total_icsi"] += $item["cantidad_icsi"];
				$resumen_totales_procedimiento[$embriologo_id]["total_icsi"]++;
			}
		}
		// buscar embriologo biopsiados
		if ($item["cantidad_biopsiados"] <> 0) {
			$embriologo_id = $item["emb".($item["dias"] - 1)."c"];
			if ($embriologo_id != 0) {
				$resumen_totales[$embriologo_id]["total_biopsiados"] += $item["cantidad_biopsiados"];
				$resumen_totales_procedimiento[$embriologo_id]["total_biopsiados"]++;
			}
		}
		//
		$date = new DateTime($item["fecha"]);
		$week = $date->format("W");
		$html.='
			<tr>
				<td class="text-center">'.$item["id"].'</td>
				<td class="text-center">'.$item["fecha"].'</td>
				<td class="text-center">'.$item["protocolo"].'</td>
				<td>'.$procedimiento.'</td>
				<td class="text-center">'.($item["fiv"] == 1 ? "1" : "-").'</td>
				<td class="text-center">'.($item["icsi"] == 1 ? "1" : "-").'</td>
				<td class="text-center">'.($item["origen_ovocitos"] == "fresco" ? "1" : "-").'</td>
				<td class="text-center">'.($item["origen_ovocitos"] == "vitrificado" ? "1" : "-").'</td>
				<td>'.mb_strtoupper($item["apellidos"]).' '.mb_strtoupper($item["nombres"]).'</td>
				<td class="text-center">'.$item["aspirados"].'</td>
				<td class="text-center">'.$item["atresicos"].'</td>
				<td class="text-center">'.$item["citolizados"].'</td>
				<td class="text-center">'.$item["inmaduros"].'</td>
				<td class="text-center">'.$item["fecundados"].'</td>
				<td class="text-center">'.$item["no_fecundados"].'</td>
				<td class="text-center">'.$item["triploides"].'</td>
				<td class="text-center">'.($item["crio_embriones_total"] == 0 ? "-" : mb_strtoupper($data_crio_embriones["embriologo"])).'</td>
				<td class="text-center">'.($item["crio_embriones_total"] == 0 ? "-" : "1").'</td>
				<td class="text-center">'.($item["crio_embriones_total"] == 0 ? "-" : $item["crio_embriones_total"]).'</td>
				<td class="text-center">'.($item["ted_total"] == 0 ? "-" : mb_strtoupper($data_ted["embriologo_ted"])).'</td>
				<td class="text-center">'.($item["ted_total"] == 0 ? "-" : "1").'</td>
				<td class="text-center">'.($item["ted_total"] == 0 ? "-" : $item["ted_total"]).'</td>
				<td class="text-center">'.($item["crio_ovulos_total"] == 0 ? "-" : mb_strtoupper($data_crio_ovulos["embriologo"]) ).'</td>
				<td class="text-center">'.($item["crio_ovulos_total"] == 0 ? "-" : $item["crio_ovulos"]).'</td>
				<td class="text-center">'.($item["crio_ovulos_total"] == 0 ? "-" : $item["crio_ovulos_total"]).'</td>
				<td class="text-center">'.($item["ngs_total"] == '0' ? "-" : "1").'</td>
				<td class="text-center">'.($item["ngs_total"] == '0' ? "-" : $item["ngs_total"]).'</td>
				<td class="text-center">'.($item["ngs_total"] == '0' ? "-" : $item["ngs_normal"]).'</td>
				<td class="text-center">'.($item["ngs_total"] == '0' ? "-" : $item["ngs_anormal"]).'</td>
				<td class="text-center">'.($item["ngs_total"] == '0' ? "-" : $item["ngs_nr"]).'</td>
				<td class="text-center">'.($item["ngs_total"] == '0' ? "-" : $item["ngs_mosaico"]).'</td>
			</tr>';
	}

	// modal
	$stmt = $db->prepare("SELECT id, nom embriologo from lab_user where eliminado_repo_biologos = 0 order by orden;");
	$stmt->execute();
	$total_aspirados = 0;
	$total_desvitrificados = 0;
	$total_fiv = 0;
	$total_icsi = 0;
	$total_biopsiados = 0;
	$total_crioembriones = 0;
	$total_transferencia = 0;
	$total_criovos = 0;
	$total_aspirados_procedimiento = 0;
	$total_desvitrificados_procedimiento = 0;
	$total_fiv_procedimiento = 0;
	$total_icsi_procedimiento = 0;
	$total_biopsiados_procedimiento = 0;
	$total_crioembriones_procedimiento = 0;
	$total_transferencia_procedimiento = 0;
	$total_criovos_procedimiento = 0;

	while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
		// totales por detalle
		$total_aspirados += $resumen_totales[$data["id"]]["total_aspirados"];
		$total_desvitrificados += $resumen_totales[$data["id"]]["total_desvitrificados"];
		$total_fiv += $resumen_totales[$data["id"]]["total_fiv"];
		$total_icsi += $resumen_totales[$data["id"]]["total_icsi"];
		$total_biopsiados += $resumen_totales[$data["id"]]["total_biopsiados"];
		$total_crioembriones += $resumen_totales[$data["id"]]["total_crioembriones"];
		$total_transferencia += $resumen_totales[$data["id"]]["total_transferencia"];
		$total_criovos += $resumen_totales[$data["id"]]["total_criovos"];
		// totales por procedimiento
		$total_aspirados_procedimiento += $resumen_totales_procedimiento[$data["id"]]["total_aspirados"];
		$total_desvitrificados_procedimiento += $resumen_totales_procedimiento[$data["id"]]["total_desvitrificados"];
		$total_fiv_procedimiento += $resumen_totales_procedimiento[$data["id"]]["total_fiv"];
		$total_icsi_procedimiento += $resumen_totales_procedimiento[$data["id"]]["total_icsi"];
		$total_biopsiados_procedimiento += $resumen_totales_procedimiento[$data["id"]]["total_biopsiados"];
		$total_crioembriones_procedimiento += $resumen_totales_procedimiento[$data["id"]]["total_crioembriones"];
		$total_transferencia_procedimiento += $resumen_totales_procedimiento[$data["id"]]["total_transferencia"];
		$total_criovos_procedimiento += $resumen_totales_procedimiento[$data["id"]]["total_criovos"];
	}

	$stmt = $db->prepare("SELECT id, nom embriologo from lab_user where eliminado_repo_biologos = 0 order by orden;");
	$stmt->execute();
	while ($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$html_modal.="
			<tr>
				<td>".mb_strtoupper($item["embriologo"])."</td>
				<td class='text-center'>".$resumen_totales[$item["id"]]["total_aspirados"]."(".($total_aspirados == 0 ? "-" : number_format(($resumen_totales[$item["id"]]["total_aspirados"]) * 100 / $total_aspirados, 2))." %)</td>
				<td class='text-center'>".$resumen_totales[$item["id"]]["total_desvitrificados"]."(".($total_desvitrificados == 0 ? "-" : number_format(($resumen_totales[$item["id"]]["total_desvitrificados"]) * 100 / $total_desvitrificados, 2))." %)</td>
				<td class='text-center'>".$resumen_totales[$item["id"]]["total_fiv"]."(".($total_fiv == 0 ? "-" : number_format(($resumen_totales[$item["id"]]["total_fiv"]) * 100 / $total_fiv, 2))." %)</td>
				<td class='text-center'>".$resumen_totales[$item["id"]]["total_icsi"]."(".($total_icsi == 0 ? "-" : number_format(($resumen_totales[$item["id"]]["total_icsi"]) * 100 / $total_icsi, 2))." %)</td>
				<td class='text-center'>".$resumen_totales[$item["id"]]["total_biopsiados"]."(".($total_biopsiados == 0 ? "-" : number_format(($resumen_totales[$item["id"]]["total_biopsiados"]) * 100 / $total_biopsiados, 2))." %)</td>
				<td class='text-center'>".$resumen_totales[$item["id"]]["total_crioembriones"]."(".($total_crioembriones == 0 ? "-" : number_format(($resumen_totales[$item["id"]]["total_crioembriones"]) * 100 / $total_crioembriones, 2))." %)</td>
				<td class='text-center'>".$resumen_totales[$item["id"]]["total_transferencia"]."(".($total_transferencia == 0 ? "-" : number_format(($resumen_totales[$item["id"]]["total_transferencia"]) * 100 / $total_transferencia, 2))." %)</td>
				<td class='text-center'>".$resumen_totales[$item["id"]]["total_criovos"]."(".($total_criovos == 0 ? "-" : number_format(($resumen_totales[$item["id"]]["total_criovos"]) * 100 / $total_criovos, 2))." %)</td>
				<td class='text-center'><b>".
				(
					$resumen_totales[$item["id"]]["total_aspirados"] +
					$resumen_totales[$item["id"]]["total_desvitrificados"] +
					$resumen_totales[$item["id"]]["total_fiv"] +
					$resumen_totales[$item["id"]]["total_icsi"] +
					$resumen_totales[$item["id"]]["total_biopsiados"] +
					$resumen_totales[$item["id"]]["total_crioembriones"] +
					$resumen_totales[$item["id"]]["total_transferencia"] +
					$resumen_totales[$item["id"]]["total_criovos"]
				)."</td>
			</tr>";

		$html_modal_procedimiento.="
			<tr>
				<td>".mb_strtoupper($item["embriologo"])."</td>
				<td class='text-center'>".$resumen_totales_procedimiento[$item["id"]]["total_aspirados"]." (".($total_aspirados_procedimiento == 0 ? "-" : number_format(($resumen_totales_procedimiento[$item["id"]]["total_aspirados"]) * 100 / $total_aspirados_procedimiento, 2))." %)</td>
				<td class='text-center'>".$resumen_totales_procedimiento[$item["id"]]["total_desvitrificados"]." (".($total_desvitrificados_procedimiento == 0 ? "-" : number_format(($resumen_totales_procedimiento[$item["id"]]["total_desvitrificados"]) * 100 / $total_desvitrificados_procedimiento, 2))." %)</td>
				<td class='text-center'>".$resumen_totales_procedimiento[$item["id"]]["total_fiv"]." (".($total_fiv_procedimiento == 0 ? "-" : number_format(($resumen_totales_procedimiento[$item["id"]]["total_fiv"]) * 100 / $total_fiv_procedimiento, 2))." %)</td>
				<td class='text-center'>".$resumen_totales_procedimiento[$item["id"]]["total_icsi"]." (".($total_icsi_procedimiento == 0 ? "-" : number_format(($resumen_totales_procedimiento[$item["id"]]["total_icsi"]) * 100 / $total_icsi_procedimiento, 2))." %)</td>
				<td class='text-center'>".$resumen_totales_procedimiento[$item["id"]]["total_biopsiados"]." (".($total_biopsiados_procedimiento == 0 ? "-" : number_format(($resumen_totales_procedimiento[$item["id"]]["total_biopsiados"]) * 100 / $total_biopsiados_procedimiento, 2))." %)</td>
				<td class='text-center'>".$resumen_totales_procedimiento[$item["id"]]["total_crioembriones"]." (".($total_crioembriones_procedimiento == 0 ? "-" : number_format(($resumen_totales_procedimiento[$item["id"]]["total_crioembriones"]) * 100 / $total_crioembriones_procedimiento, 2))." %)</td>
				<td class='text-center'>".$resumen_totales_procedimiento[$item["id"]]["total_transferencia"]." (".($total_transferencia_procedimiento == 0 ? "-" : number_format(($resumen_totales_procedimiento[$item["id"]]["total_transferencia"]) * 100 / $total_transferencia_procedimiento, 2))." %)</td>
				<td class='text-center'>".$resumen_totales_procedimiento[$item["id"]]["total_criovos"]." (".($total_criovos_procedimiento == 0 ? "-" : number_format(($resumen_totales_procedimiento[$item["id"]]["total_criovos"]) * 100 / $total_criovos_procedimiento, 2))." %)</td>
				<td class='text-center'><b>".
				(
					$resumen_totales_procedimiento[$item["id"]]["total_aspirados"] +
					$resumen_totales_procedimiento[$item["id"]]["total_desvitrificados"] +
					$resumen_totales_procedimiento[$item["id"]]["total_fiv"] +
					$resumen_totales_procedimiento[$item["id"]]["total_icsi"] +
					$resumen_totales_procedimiento[$item["id"]]["total_biopsiados"] +
					$resumen_totales_procedimiento[$item["id"]]["total_crioembriones"] +
					$resumen_totales_procedimiento[$item["id"]]["total_transferencia"] +
					$resumen_totales_procedimiento[$item["id"]]["total_criovos"]
				)."</td>
			</tr>";
	}
	// return
	return [
		"table_main" => $html,
		"table_modal" => $html_modal,
		"table_modal_procedimiento" => $html_modal_procedimiento
	];
}

function descargarReporte($data) {
	$data=getQuery($data);
	require($_SERVER["DOCUMENT_ROOT"] . "/_libraries/php_excel_18/PHPExcel.php");
	$objPHPExcel=new PHPExcel();
	$objPHPExcel->getProperties()

	->setCreator("Clínica Inmater")
	->setLastModifiedBy("Clínica Inmater")
	->setTitle("Office 2007 XLSX")
	->setSubject("Office 2007 XLSX")
	->setDescription("Office 2007 XLSX.")
	->setKeywords("office 2007 openxml php")
	->setCategory("Clinica Inmater");

	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'Protocolo');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', 'FIV');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', 'F. Transferencia FIV');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', 'TED');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E1', 'F. Transferencia TED');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F1', 'Embriodonación');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G1', 'F. Transferencia Embriodonación');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H1', 'En Fresco');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I1', 'F. Transferencia en Fresco');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J1', 'Crio');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K1', 'N° documento');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L1', 'Paciente');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('M1', 'Sede');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N1', 'Distrito');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('O1', 'Dirección');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('P1', 'N° Personal');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q1', 'N° Casa');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('R1', 'N° Oficina');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('S1', 'Email');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('T1', 'F. Ingreso');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('U1', 'Médico Beta');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('V1', 'F. Beta');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('W1', 'Resultado Beta');
	$index=2;
	foreach ($data as $key => $value) {
		$index_column=0;
		foreach ($value as $key1 => $value1) {
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column).$index, $value1);
		}
		$index++;
	}
	$objPHPExcel->getActiveSheet()->setTitle('base');
	$objPHPExcel->setActiveSheetIndex(0);
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="repo-base-general.xlsx"');
	header('Cache-Control: max-age=0');
	header('Cache-Control: max-age=1');
	header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header ('Cache-Control: cache, must-revalidate');
	header ('Pragma: public');
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	ob_start();
	$objWriter->save('php://output');
	$xlsData = ob_get_contents();
	ob_end_clean();
	$response =  [
		'op' => 'ok',
		'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
	];
	die(json_encode($response));
}
function getNameFromNumber(&$num) {
	$numeric = $num % 26;
	$letter = chr(65 + $numeric);
	$num2 = intval($num / 26);
	$num++;
	if ($num2 > 0) {
		$demo = $num2 - 1;
		return getNameFromNumber($demo) . $letter;
	} else {
		return $letter;
	}
}

