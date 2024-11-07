<?php
// _operaciones
session_start();
// error_reporting(error_reporting() & ~E_NOTICE);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require($_SERVER["DOCUMENT_ROOT"] . "/_database/database.php");
require($_SERVER["DOCUMENT_ROOT"] . "/_database/database_log.php");
require($_SERVER["DOCUMENT_ROOT"] . "/_database/database_farmacia.php");
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
			echo json_encode(getData($_POST));
			break;
		case 'descargar-reporte':
			http_response_code(200);
			echo json_encode(["message" => descargarReporte($_POST)]);
			break;
		/* case 'cargar-data':
			http_response_code(200);
			echo json_encode(["message" => cargarData($_POST)]);
			break; */
		default:
			http_response_code(400);
			echo json_encode(["response" => "la operacion no existe"]);
			break;
	}
} else {
	http_response_code(400);
	echo json_encode(["message" => "no se enviaron los parametros correctamente"]);
	exit();
}

function getData($data) {
	$info = getQuery($data);
	if (isset($info["message"])) {
		return $info;
	} else {
		return getHtml($info);
	}
}

function getQuery($data) {
    global $db;
	global $db_repo;
	$between = "";

	if (isset($data["protocolo"]) && !empty($data["protocolo"])) {
		$protocolo = $data["protocolo"];
		$between .= " and la.pro = '$protocolo'";
	} else {
		if (isset($data["fecha_inicio"]) && !empty($data["fecha_inicio"]) && isset($data["fecha_fin"]) && !empty($data["fecha_fin"])) {
			$ini = $data['fecha_inicio'];
			$fin = $data['fecha_fin'];
			$between .= " and '$ini' < la.fec and la.fec < '$fin'";
		} else {
			$ini = date('Y-01-01');
			$fin = date('Y-01-01');
			$between = " and 1=2";
		}
	}

    $stmt = $db->prepare("SELECT
        -- informacion general
        concat(
            CASE WHEN hr.p_fiv = 1 THEN 'FIV' ELSE '' END,
            CASE WHEN hr.p_icsi = 1 THEN 'ICSI' ELSE '' END,
            CASE WHEN hr.des_dia >= 1 and hr.des_don is null THEN 'TED' ELSE '' END,
            CASE WHEN hr.des_dia >= 1 and hr.des_don is not null THEN 'EMBRIODONACION' ELSE '' END,
            CASE WHEN hr.des_dia = 0 and hr.des_don is null THEN 'DESCONGELACION DE OVULOS PROPIOS' ELSE '' END,
            CASE WHEN hr.des_dia = 0 and hr.des_don is not null THEN 'DESCONGELACION DE OVULOS DONADOS' ELSE '' END,
            CASE WHEN hr.p_od is not null THEN 'OD FRESCO' ELSE '' END,
            CASE WHEN hr.p_cri = 1 THEN 'CRIO DE OVOS' ELSE '' END
        ) as procedimiento_tipo
        , la.tip tipo
        , la.pro protocolo
        , hr.med medico
        , lad.ovo
        , 0 edad_ovulo
        -- datos del paciente
        , hp.dni paciente_dni
        , concat(upper(rtrim(ltrim(hp.ape))), ' ', upper(rtrim(ltrim(hp.nom)))) paciente_nombres
        , hp.fnac paciente_fecha_nacimiento
        , extract(year from current_date) - extract(year from hp.fnac) - CASE WHEN (extract(month from current_date) * 100 + extract(day from current_date) < extract(month from hp.fnac) * 100 + extract(day from hp.fnac)) THEN 1 ELSE 0 END AS paciente_edad
        -- procedimiento anterior
        , lad1.pro protocolo_anterior
        -- datos de la pareja
        , hr.p_dni pareja_dni
        , concat(upper(rtrim(ltrim(hpar.p_ape))), ' ', upper(rtrim(ltrim(hpar.p_nom)))) pareja_nombres
        -- espermatograma
        , esp.fec esp_fecha
        , esp.macro_volumen esp_volumen
        , esp.concen_exml esp_concentracion
        , (esp.movi_mprogresivo + esp.movi_mnoprogresivo) esp_total
        , esp.morfo_normal esp_morfologia
        -- capacitacion espermatica
        , upper(cts.nombre) cap_tipo_seleccion
        , upper(ctm.nombre) cap_tipo_muestra
        , upper(ctc.nombre) cap_tipo_capacitacion
        , case when lac.id is not null then lac.vol_f else 0 end cap_volumen_muestra
        , case when lac.id is not null then lac.con_f else 0 end cap_muestra_concentracion
        , case when lac.id is not null then coalesce(lac.pl_f, 0) else 0 end +
            case when lac.id is not null then coalesce(lac.pnl_f, 0) else 0 end cap_muestra_plinealnlineal
        , case when lac.id is not null then lac.con_c else 0 end cap_capacitado_concentracion
        , case when lac.id is not null then coalesce(lac.pl_c, 0) else 0 end +
            case when lac.id is not null then coalesce(lac.pnl_c, 0) else 0 end cap_capacitado_plinealnlineal
        -- dia 0
        , la_dia0.nom dia0_embriologo
        , la.hra_a dia0_hora_aspiracion
        , coalesce(la.hra0, '') dia0_hora_inseminacion
        , la.o_ovo dia0_ovocitos
        , hr.pago_extras dia0_extras
        , la.inc dia0_incubadora
        , lad.d0est dia0_estadio
        , upper(dia0_fin_ciclo.nombre) dia0_fin_ciclo
        -- dia 1
        , la_dia1.nom dia1_embriologo
        , coalesce(la.hra1, '') dia1_hora_inseminacion
        , concat(case when lad.d1est = 'MII' and lad.d1f_cic = 'O' and lad.d1c_pol = '2' and lad.d1pron = '2' then 'fecundado' else '' end, case when lad.d1est = 'MII' and lad.d1f_cic = 'N' and lad.d1c_pol in ('0', '1', '2') and lad.d1pron in ('0', '1', '2') then 'no fecundado' else '' end) dia1_regla
        , la.inc1 dia1_incubadora
        , lad.d1est dia1_estadio
		, case when lad.d1est = 'MII' then 1 else 0 end dia1_mii
		, case when lad.d1est = 'MI' then 1 else 0 end dia1_mi
		, case when lad.d1est = 'VG' then 1 else 0 end dia1_vg
		, case when lad.d1est = 'ATR' then 1 else 0 end dia1_atr
		, case when lad.d1est = 'CIT' then 1 else 0 end dia1_cit
        , coalesce(lad.d1pron, '-') dia1_pronucleo
		, case when coalesce(lad.d1pron, '-') = '0' then 1 else 0 end dia1_fec_no
		, case when coalesce(lad.d1pron, '-') = '1' then 1 else 0 end dia1_fec_anormal
		, case when coalesce(lad.d1pron, '-') = '2' then 1 else 0 end dia1_fec_normal
		, case when coalesce(lad.d1pron, '-') = '3' then 1 else 0 end dia1_fec_triploide
        , upper(dia1_fin_ciclo.nombre) dia1_fin_ciclo
        -- dia 4
        , la_dia4.nom dia4_embriologo
        , coalesce(la.hra4, '') dia4_hora_inseminacion
        , coalesce(lad.d4cel, '-') dia4_celulas
        , lad.d4fra dia4_fragmentacion
        , coalesce(lad.d4f_cic, '') dia4_fin_ciclo
        -- dia 5
        , la_dia5.nom dia5_embriologo
        , coalesce(la.hra5, '') dia5_hora_inseminacion
        , coalesce(lad.d5cel, '-') dia5_celulas
        , lad.d5mci dia5_mci
        , lad.d5tro dia5_trofo
        , '' dia5_fragmentacion
        , dia5_contraccion.nombre dia5_contraccion
        , upper(mb5.nombre) dia5_biopsia
        , CASE WHEN lad.d5kid_tipo = 1 THEN lad.d5kid_decimal ELSE NULL END AS dia5_kidscore
        , CASE WHEN lad.d5kid_tipo = 2 THEN lad.d5kid_decimal ELSE NULL END AS dia5_idascore
        , coalesce(lad.d5kid_decimal, 0) dia5_scorevalue
        , upper(dia5_fin_ciclo.nombre) dia5_fin_ciclo
        -- dia 6
        , la_dia6.nom dia6_embriologo
        , coalesce(la.hra6, '') dia6_hora_inseminacion
        , coalesce(lad.d6cel, '-') dia6_celulas
        , lad.d6mci dia6_mci
        , lad.d6tro dia6_trofo
        , '' dia6_fragmentacion
        , dia6_contraccion.nombre dia6_contraccion
        , upper(mb6.nombre) dia6_biopsia
        , CASE WHEN lad.d6kid_tipo = 1 THEN lad.d6kid_decimal ELSE NULL END AS dia6_kidscore
        , CASE WHEN lad.d6kid_tipo = 2 THEN lad.d6kid_decimal ELSE NULL END AS dia6_idascore
        , coalesce(lad.d6kid_decimal, 0) dia6_scorevalue
        , upper(dia6_fin_ciclo.nombre) dia6_fin_ciclo
        , la_dia5c.nom crio_embriologo_dia5
        , la_dia6c.nom crio_embriologo_dia6
        , case when lad.t is not null then lad.t else '0' end crio_tanque
        , case when lad.c is not null then lad.c else '0' end crio_canister
        , case when lad.g is not null then lad.g else '0' end crio_varilla
        , case when lad.p is not null then lad.p else '0' end crio_pajuela
        , case when lad.col is not null then lad.col else '0' end crio_color
        , '' crio_ngs
        , lad.ngs1 crio_resultado
        , lad.ngs3 crio_sexo
        , coalesce(lad.valores_mitoscore, 0) crio_mitoscore
        , coalesce(lad.prioridad_transferencia, NULL) AS crio_prioridad_transferencia
        , lad.ngs2 crio_resultado_detalles
        , coalesce(lad_count.cantidad, 0) total_embriones_biopsiados
        , lat.endo endometrio
        , lat.t_cat tipo_cateter
        , case when lad.d5f_cic = 'T' or lad.d6f_cic = 'T' then 'Transferido' else '' end trans_ted
        , trim(upper(coalesce(mb.nombre, 'PENDIENTE'))) trans_beta
        from hc_reprod hr
        inner join hc_paciente hp on hp.dni = hr.dni and hp.fnac <> '1899-12-30'
        left join hc_paciente hp1_od on hp1_od.dni = hr.p_od
        left join hc_paciente hp1_desdon on hp1_desdon.dni = hr.des_don
        left join hc_reprod hr_dontodo on hr_dontodo.f_asp = hr.f_asp and hr.p_od = hr_dontodo.dni 
        left join hc_pareja hpar on hr.p_dni = hpar.p_dni
        left join lab_andro_esp esp on esp.p_dni = hpar.p_dni and esp.fec = (
            select a.fec from lab_andro_esp as a
            where a.p_dni = hpar.p_dni
            order by a.fec desc
            limit 1
        )
        inner join lab_aspira la on la.rep = hr.id and la.estado is true
        left join (select pro, count(*) cantidad from lab_aspira_dias where ngs2 <> '' and estado is true group by pro) lad_count on lad_count.pro = la.pro
        left join lab_user la_dia0 on la_dia0.id = la.emb0
        left join lab_user la_dia1 on la_dia1.id = la.emb1
        left join lab_user la_dia4 on la_dia4.id = la.emb4
        left join lab_user la_dia5 on la_dia5.id = la.emb5
        left join lab_user la_dia6 on la_dia6.id = la.emb6
        left join lab_user la_dia5c on la_dia5c.id = la.emb5c
        left join lab_user la_dia6c on la_dia6c.id = la.emb6c
        left join lab_aspira_t lat on lat.pro = la.pro and lat.estado is true
        left join man_beta_rinicial mb on mb.id = lat.beta
        inner join lab_aspira_dias lad on lad.pro = la.pro and lad.estado is true
        -- procedimiento anterior
        left join lab_aspira_dias lad1 on lad1.pro = lad.pro_c and lad1.ovo = lad.ovo_c and lad1.estado is true
        -- fin de ciclo
        left join laboratorio_fin_ciclo dia0_fin_ciclo on dia0_fin_ciclo.codigo = lad.d0f_cic
        left join laboratorio_fin_ciclo dia1_fin_ciclo on dia1_fin_ciclo.codigo = lad.d1f_cic
        left join laboratorio_fin_ciclo dia5_fin_ciclo on dia5_fin_ciclo.codigo = lad.d5f_cic
        left join laboratorio_fin_ciclo dia6_fin_ciclo on dia6_fin_ciclo.codigo = lad.d6f_cic
        left join man_biopsia mb5 on mb5.codigo = lad.d5d_bio
        left join man_biopsia mb6 on mb6.codigo = lad.d6d_bio
        left join lab_contraccion dia5_contraccion on dia5_contraccion.codigo = lad.d5col
        left join lab_contraccion dia6_contraccion on dia6_contraccion.codigo = lad.d6col
        -- espermatograma
        left join lab_andro_cap lac on (lac.pro = la.pro or lac.rep = la.rep) and lac.eliminado is false
        left join capacitacion_tipo_seleccion_espermatica cts on cts.id = lac.sel
        left join capacitacion_tipo_muestra ctm on ctm.id = lac.mue
        left join capacitacion_tipo_capacitacion ctc on ctc.id = lac.cap
        where hr.estado = true and la.tip <> 'T' and la.f_fin <> '1899-12-30'
        $between
        -- and la.fec between '2021-01-01' and '2021-12-31'
        -- and la.pro = '1338-22'
        order by la.fec, lad.ovo;");
    $stmt->execute();

	if ($stmt->rowCount() == 0) {
		return ["message" => "<b>Mensaje</b> No se encontraron datos en la consulta!"];
	} else {
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}

function getHtml($data) {
	require($_SERVER["DOCUMENT_ROOT"] . "/data/base-general.php");
	$i=1;
	$table = '';
	foreach ($data as $item) {
		$table .= '<tr>';
		foreach ($base_general_columnas as $value) {
            if(isset($item[$value["columna"]]))$table.='<td class="text-center">'.$item[$value["columna"]].'</td>';
		}
		$table .= '</tr>';
	}
	return ["table" => $table];
}

function descargarReporte($data) {
	try {
		$info = getQuery($data);
		if (isset($info["message"])) {
			return $info;
		}
		require($_SERVER["DOCUMENT_ROOT"] . "/data/base-general.php");
		require($_SERVER["DOCUMENT_ROOT"] . "/_libraries/php_excel_18/PHPExcel.php");
	
		global $db;
		global $db_repo;
	
		$index = 2;
		$index_column = 0;
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()
		->setCreator("Maarten Balliauw")
		->setLastModifiedBy("Maarten Balliauw")
		->setTitle("Office 2007 XLSX Test Document")
		->setSubject("Office 2007 XLSX Test Document")
		->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
		->setKeywords("office 2007 openxml php")
		->setCategory("Test result file");
	
		foreach ($base_general_columnas as $key=>$info1) {
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($key) . '1', $info1["texto"]);
		}
	
		foreach ($info as $item) {
			$index_column = 0;
            foreach ($base_general_columnas as $info2) {
                    if (false) {
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, ((($item["dia5_fin_ciclo"] == 'C' or $item["dia6_fin_ciclo"] == 'C') and file_exists($_SERVER["DOCUMENT_ROOT"] . "/analisis/ngs_" . $item['protocolo'] . ".pdf")) ? 'Si' : '-'));
                    } else {
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item[$info2["columna"]]);
                    }
            }
			$index++;
		}
	
		$objPHPExcel->getActiveSheet()->setTitle('reporte-base-general');
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
	} catch (Exception $e) {
		var_dump($e);
	}
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
?>