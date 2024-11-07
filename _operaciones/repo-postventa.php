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
		case 'obtener_reporte':
			http_response_code(200);
			echo json_encode(["message" => obtener_reporte($_POST)]);
			break;
		case 'descargar_reporte':
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

function obtener_reporte($data) {
	return ['content' => getHtml(calcularData($data))];
}

function calcularData($data) {
	global $db;
	$fechaIni = empty($data["ini"]) ? '1899-12-30' : $data["ini"];
	$fechaFin = empty($data["fin"]) ? date('Y-m-d') : $data["fin"];
	
	$stmt = $db->prepare("SELECT * FROM marketingreportecrio(?, ?);");
	$stmt->execute([$fechaIni, $fechaFin]);
	return $stmt->fetchAll(PDO::FETCH_ASSOC);

}
function getHtml($data) {
	$html='';
	foreach ($data as $key => $value) {
		$html.='<tr align="center">';
		foreach ($value as $key1 => $value1) {
			$html.='<td>'.$value1.'</td>';
		}
		$html.='</tr>';
	}
	return $html;
}

function descargarReporte($data) {
	$data=calcularData($data);
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

	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'F. Consulta');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', 'Protocolo');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', 'FIV');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', 'F. Transferencia FIV');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E1', 'TED');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F1', 'F. Transferencia TED');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G1', 'Embriodonación');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H1', 'F. Transferencia Embriodonación');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I1', 'En Fresco');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J1', 'F. Transferencia en Fresco');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K1', 'Crio');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L1', 'N° documento');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('M1', 'Paciente');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N1', 'F. Nacimiento');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('O1', 'Sede');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('P1', 'Distrito');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q1', 'Dirección');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('R1', 'N° Personal');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('S1', 'N° Casa');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('T1', 'N° Oficina');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('U1', 'Email');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('V1', 'F. Ingreso');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('W1', 'Médico Beta');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('X1', 'F. Beta');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Y1', 'Resultado Beta');
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
