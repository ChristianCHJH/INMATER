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
        case 'obtener_data':
            http_response_code(200);
            echo json_encode(["message" => obtener_data($_POST)]);
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

function obtener_data($data) {
    switch ($data['condicion']) {
        case 'aspiracion_donante':
            $data_aspiracion = selectAspiracion($data, $data['medico'], $data['medio_comunicacion']);
            return ['content' => validarHtmlDetalle(array_filter($data_aspiracion, function($item) {return $item['aspiracion_donante'] == 1;}))];
            break;
        case 'aspiracion_receptora':
            $data_aspiracion = selectAspiracion($data, $data['medico'], $data['medio_comunicacion']);
            return ['content' => validarHtmlDetalle(array_filter($data_aspiracion, function($item) {return $item['aspiracion_receptora'] == 1;}))];
            break;
        case 'aspiracion_crio_paciente':
            $data_aspiracion = selectAspiracion($data, $data['medico'], $data['medio_comunicacion']);
            return ['content' => validarHtmlDetalle(array_filter($data_aspiracion, function($item) {return $item['aspiracion_crio_paciente'] == 1;}))];
            break;
        case 'aspiracion_crio_donante':
            $data_aspiracion = selectAspiracion($data, $data['medico'], $data['medio_comunicacion']);
            return ['content' => validarHtmlDetalle(array_filter($data_aspiracion, function($item) {return $item['aspiracion_crio_donante'] == 1;}))];
            break;
        default: break;
    }
}

function validarHtmlDetalle($data) {
    $html='';
    foreach ($data as $key => $value) {
        $html.='<tr align="center">';
        for ($i=0; $i < 3; $i++) { 
            $html.='<td>'.$value[$i].'</td>';
        }
        $html.='</tr>';
    }
    return $html;
}


function obtener_reporte($data) {
    return ['content' => validarHtml(calcularData($data))];
}

function calcularData($data) {
    global $db;
    $response='';
    $response_array=[];
    $stmt2=$db->prepare("SELECT id, upper(nombre) nombre, upper(abreviatura) abreviatura from man_medios_comunicacion mmc;");
    $stmt2->execute();
    $rows2=$stmt2->fetchAll();
    $medio_comunicacion="(0";
    foreach ($rows2 as $key => $value) {
        if ($data["medio_comunicacion"] == "" || $value["id"] == $data["medio_comunicacion"]) {
            $medio_comunicacion.=(", ".$value["id"]);
        }
    }
    $medio_comunicacion.=")";

    // totales
    array_push($response_array, totalAspiraciones($data, "", $medio_comunicacion, ""));
    // detalle en filas
    if (empty($data["medico"])) {
        $stmt1 = $db->prepare("SELECT userx, nom from usuario where role=1 order by nom;");
        $stmt1->execute();
        $rows1 = $stmt1->fetchAll();
        foreach ($rows1 as $key1 => $value) {
            foreach ($rows2 as $key2 => $value2) {
                if ($data["medio_comunicacion"] == "" || $value2["id"] == $data["medio_comunicacion"]) {
                    array_push($response_array, totalAspiraciones($data, $value["userx"], "(".$value2["id"].")", $value2["abreviatura"]));
                }
            }
        }
    } else {
        foreach ($data["medico"] as $key1 => $value) {
            foreach ($rows2 as $key2 => $value2) {
                if ($data["medio_comunicacion"] == "" || $value2["id"] == $data["medio_comunicacion"]) {
                    array_push($response_array, totalAspiraciones($data, $value, "(".$value2["id"].")", $value2["abreviatura"]));
                }
            }
        }
    }
    
    return $response_array;
}

function validarHtml($data) {
    $html='';
    foreach ($data as $key => $value) {
        $html.='<tr align="center">';
        foreach ($value as $key1 => $value1) {
            foreach ($value1 as $key2 => $value2) {
                if ($key2 == 0) {
                    $html.='<td><a href="javascript:void(0)" class="obtener_data" data-toggle="modal" data-target="#exampleModal" data-medico="'.$value1[1].'" data-medio-comunicacion="'.$value1[2].'" data-condicion="'.$value1[3].'">'.$value2.'</a></td>';
                   }
            }
        }
        $html.='</tr>';
    }
    return $html;
}

function totalAspiraciones($data, $medico="", $medio_comunicacion="", $abreviatura="") {
    global $db;
    $data_aspiracion = selectAspiracion($data, $medico, $medio_comunicacion);
    $inseminacion_fiv = count(array_filter($data_aspiracion, function($item) {return $item['inseminacion_fiv'] == 1;}));
    $inseminacion_icsi = count(array_filter($data_aspiracion, function($item) {return $item['inseminacion_icsi'] == 1;}));
    $data_transferencia = selectTransferencia($data, $medico, $medio_comunicacion);
    $data_crio_ovulos = selectCrioOvulos($data, $medico, $medio_comunicacion);
    $data_crio_embriones = selectCrioEmbriones($data, $medico, $medio_comunicacion);
    return [
        [$medico=="" ? "TODOS" : mb_strtoupper($medico)."-".$abreviatura, '', '', ''],
        [$inseminacion_fiv + $inseminacion_icsi, $medico, $medio_comunicacion, 'total_inseminacion'],
        [count(array_filter($data_aspiracion, function($item) {return $item['aspiracion_donante'] == 1;})), $medico, $medio_comunicacion, 'aspiracion_donante'],
        [count(array_filter($data_aspiracion, function($item) {return $item['aspiracion_receptora'] == 1;})), $medico, $medio_comunicacion, 'aspiracion_receptora'],
        [count(array_filter($data_aspiracion, function($item) {return $item['aspiracion_crio_paciente'] == 1;})), $medico, $medio_comunicacion, 'aspiracion_crio_paciente'],
        [count(array_filter($data_aspiracion, function($item) {return $item['aspiracion_crio_donante'] == 1;})), $medico, $medio_comunicacion, 'aspiracion_crio_donante'],
        [$inseminacion_fiv, $medico, $medio_comunicacion, 'inseminacion_fiv'],
        [$inseminacion_icsi, $medico, $medio_comunicacion, 'inseminacion_icsi'],
        [count(array_filter($data_aspiracion, function($item) {return $item['desarrollo_ngs'] == 1;})), $medico, $medio_comunicacion, 'desarrollo_ngs'],
        [count(array_filter($data_aspiracion, function($item) {return $item['desarrollo_embryoscope'] == 1;})), $medico, $medio_comunicacion, 'desarrollo_embryoscope'],
        [count($data_crio_ovulos), $medico, $medio_comunicacion, 'data_crio_ovulos'],
        [count($data_crio_embriones), $medico, $medio_comunicacion, 'data_crio_embriones'],
        [count(array_filter($data_transferencia, function($item) {return $item['transferencia1'] == 1;})), $medico, $medio_comunicacion, 'transferencia1'],
        [count(array_filter($data_transferencia, function($item) {return $item['transferencia2'] == 1;})), $medico, $medio_comunicacion, 'transferencia2'],
    ];
}

function selectAspiracion($data, $medico="", $medio_comunicacion="") {
    global $db;
    $stmt = $db->prepare("SELECT
        la.fec, hr.dni, concat(hp.ape, ' ', hp.nom) paciente,
        case when (la.tip = 'D' or hr.don_todo = 1) and (hr.p_cri<>1 or hr.p_cri is null) then true else false end aspiracion_donante,
        case when (la.tip = 'R') then true else false end aspiracion_receptora,
        case when (la.tip = 'P' and hr.p_cri = 1) then true else false end aspiracion_crio_paciente,
        case when (la.tip = 'D' and hr.p_cri = 1) then true else false end aspiracion_crio_donante,
        case when (la.tip = 'P' or la.tip = 'R') and (hr.p_fiv=1) then true else false end inseminacion_fiv,
        case when (la.tip = 'P' or la.tip = 'R') and (hr.p_icsi=1) then true else false end inseminacion_icsi,
        case when (la.tip = 'P' or la.tip = 'R') and hr.pago_extras ilike('%ngs%') then true else false end desarrollo_ngs,
        case when (la.tip = 'P' or la.tip = 'R') and hr.pago_extras ilike('%EMBRYOSCOPE%') then true else false end desarrollo_embryoscope
        from hc_reprod hr
        inner join hc_paciente hp on hp.dni = hr.dni and hp.medios_comunicacion_id in $medio_comunicacion
        left join lab_aspira la ON hr.id = la.rep and la.estado is true
        where hr.estado = true and 1=1
        and hr.cancela=0
        and hr.med ilike ? and CAST(la.fec as date) between ? and ?;");
    $stmt->execute(["%".$medico."%", $data["ini"], $data["fin"]]);
    return $stmt->fetchAll();
}

function selectTransferencia($data, $medico="", $medio_comunicacion="") {
    global $db;
    $stmt = $db->prepare("SELECT
        la.fec, hr.dni, concat(hp.ape, ' ', hp.nom) paciente,
        case when not(hr.des_don is not null and hr.des_dia >= 1) then true else false end transferencia1,
        case when hr.des_don is not null and hr.des_dia >= 1 then true else false end transferencia2
        from hc_reprod hr
        inner join hc_paciente hp on hp.dni = hr.dni and hp.medios_comunicacion_id in $medio_comunicacion
        inner join lab_aspira la on la.rep=hr.id and la.estado is true
        inner join lab_aspira_t lat on lat.pro=la.pro and lat.estado is true
        where hr.estado = true and 1=1
        and hr.cancela=0
        and hr.med ilike ? and CAST(la.fec as date) between ? and ?;");
    $stmt->execute(["%".$medico."%", $data["ini"], $data["fin"]]);
    return $stmt->fetchAll();
}

function selectCrioOvulos($data, $medico="", $medio_comunicacion="") {
    global $db;
    $stmt = $db->prepare("SELECT
        la.fec, hr.dni, concat(hp.ape, ' ', hp.nom) paciente, lad.pro
        from lab_aspira_dias lad
        inner join lab_aspira la on la.pro = lad.pro and la.estado is true
        inner join hc_reprod hr on hr.id = la.rep
        inner join hc_paciente hp on hp.dni = hr.dni and hp.medios_comunicacion_id in $medio_comunicacion
        where hr.estado = true and 1=1 and lad.estado is true
        and lad.d0f_cic='C'
        and hr.med ilike ? and CAST(la.fec as date) between ? and ?
        group by lad.pro,la.fec,hr.dni,hp.ape,hp.nom;");
    $stmt->execute(["%".$medico."%", $data["ini"], $data["fin"]]);
    return $stmt->fetchAll();
}

function selectCrioEmbriones($data, $medico="", $medio_comunicacion="") {
    global $db;
    $stmt = $db->prepare("SELECT
        la.fec, hr.dni, concat(hp.ape, ' ', hp.nom) paciente, lad.pro
        from lab_aspira_dias lad
        inner join lab_aspira la on la.pro = lad.pro and la.estado is true
        inner join hc_reprod hr on hr.id = la.rep
        inner join hc_paciente hp on hp.dni = hr.dni and hp.medios_comunicacion_id in $medio_comunicacion
        where hr.estado = true and 1=1 and lad.estado is true
        and (lad.d6f_cic='C' or lad.d5f_cic='C' or lad.d4f_cic='C' or lad.d3f_cic='C' or lad.d2f_cic='C')
        and unaccent(hr.med) ilike ? and CAST(la.fec as date) between ? and ?
        group by lad.pro,la.fec,hr.dni,hp.ape,hp.nom;");
    $stmt->execute(["%".$medico."%", $data["ini"], $data["fin"]]);
    return $stmt->fetchAll();
}

function descargarReporte($data) {
    $data = calcularData($data);
    require($_SERVER["DOCUMENT_ROOT"] . "/_libraries/php_excel_18/PHPExcel.php");
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()

    ->setCreator("Clínica Inmater")
    ->setLastModifiedBy("Clínica Inmater")
    ->setTitle("Office 2007 XLSX")
    ->setSubject("Office 2007 XLSX")
    ->setDescription("Office 2007 XLSX, generated using PHP classes.")
    ->setKeywords("office 2007 openxml php")
    ->setCategory("Test result file");

    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'Médico');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', 'Aspiración Paciente');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', 'Aspiración Donante');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', 'Aspiración Receptora');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E1', 'Aspiración Crio Paciente');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F1', 'Aspiración Crio Donante');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G1', 'Inseminacion FIV');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H1', 'Inseminacion ICSI');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I1', 'NGS');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J1', 'Embryoscope');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K1', 'Crio Ovos');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L1', 'Crio Embriones');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M1', 'Transferencia Propios');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N1', 'Transferencia Embriodonación');
    $index=2;

    foreach ($data as $key => $value) {
        $index_column=0;
        foreach ($value as $key1 => $value1) {
            foreach ($value1 as $key2 => $value2) {
                if ($key2 == 0) {
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column).$index, $value2);
                }
            }
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