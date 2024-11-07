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
        case 'cargar_grafica':
            http_response_code(200);
            echo json_encode(["message" => cargar_grafica($_POST["anio"])]);
            break;
        case 'descargar_base':
            http_response_code(200);
            echo json_encode(["message" => descargar_base($_POST["anio_consulta"])]);
            break;
        
        default:
            http_response_code(400);
            echo json_encode(["message" => "la operacion no existe"]);
            break;
    }
} else {
    // descargar_base();
    http_response_code(400);
    echo json_encode(["message" => "no se enviaron los parametros correctamente"]);
    exit();
}

function descargar_base($anio) {
    // require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';
    require($_SERVER["DOCUMENT_ROOT"] . "/_libraries/php_excel_18/PHPExcel.php");

    $objPHPExcel = new PHPExcel();

    $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
    ->setLastModifiedBy("Maarten Balliauw")
    ->setTitle("Office 2007 XLSX Test Document")
    ->setSubject("Office 2007 XLSX Test Document")
    ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
    ->setKeywords("office 2007 openxml php")
    ->setCategory("Test result file");

    global $db;
    $anio = ($anio == '-' ? "'2015', '2016', '2017', '2018', '2019', '2020'" : "'$anio'");

    $stmt = $db->prepare("SELECT
        hr.id
        , la.pro protocolo
        , la1.pro protocolo_anterior
        , hp.dni, hp.ape apellidos, hp.nom nombres
        , hp1.dni dni_anterior, hp1.ape apellidos_anterior, hp1.nom nombres_anterior
        , hr.fec fecha_procedimiento
        , hp.fnac fecha_nacimiento
        , year(now()) - year(hp.fnac) - (date_format(now(), '%m%d') < date_format(hp.fnac, '%m%d')) edad_actual
        , year(now()) - year(hp1.fnac) - (date_format(now(), '%m%d') < date_format(hp1.fnac, '%m%d')) edad_actual_anterior
        -- , hr.eda edad_informe
        , case
            when hr.des_dia = 0 and hr.des_don is not null then year(hr.fec) - year(hp1.fnac) - (date_format(hr.fec, '%m%d') < date_format(hp1.fnac, '%m%d'))
            when hr.p_od is not null and hr.p_od <> '' then year(hr.fec) - year(hp2.fnac) - (date_format(hr.fec, '%m%d') < date_format(hp2.fnac, '%m%d'))
            else year(hr.fec) - year(hp.fnac) - (date_format(hr.fec, '%m%d') < date_format(hp.fnac, '%m%d')) end edad_calculada
        , case when hr.p_fiv = 1 then 'si' else 'no' end fiv
        , case when hr.p_icsi = 1 then 'si' else 'no' end icsi
        , hr.p_od od_fresco
        , case when hr.des_dia >= 1 and hr.des_don is null then 'si' else 'no' end ted
        , case when hr.des_dia >= 1 and hr.des_don is not null then 'si' else 'no' end embrioadopcion
        , case when hr.des_dia = 0 and hr.des_don is null then 'si' else 'no' end descongelacion_propios
        , case when hr.des_dia = 0 and hr.des_don is not null then 'si' else 'no' end descongelacion_donados
        , hr.pago_extras extras
        , count(case when lad.ngs1 = 1 then true end) normales
        , count(case when lad.ngs1 = 2 then true end) anormales
        , count(case when lad.ngs1 = 3 then true end) nr
        , count(case when lad.ngs1 = 4 then true end) mosaico
        , count(*) total
        from hc_reprod hr
        inner join hc_paciente hp on hp.dni = hr.dni and hp.fnac <> '1899-12-30'
        inner join lab_aspira la on la.rep = hr.id and la.estado is true
        inner join lab_aspira_dias lad on lad.pro = la.pro and lad.estado is true
        left join lab_aspira la1 on la1.pro = lad.pro_c and la1.estado is true
        left join hc_paciente hp1 on hp1.dni = la1.dni -- paciente procedimiento anterior
        left join hc_paciente hp2 on hp2.dni = hr.p_od -- paciente od fresco
        where hr.estado = true and lad.ngs1 <> 0 and la.f_fin <> '1899-12-30'
        and hr.pago_extras ILIKE '%NGS%'
        and year(hr.fec) in ($anio)
        group by hr.id, lad.pro
        having count(case when lad.d2f_cic = 't' or  lad.d3f_cic = 't' or  lad.d4f_cic = 't' or  lad.d5f_cic = 't' or lad.d6f_cic = 't' then true end) = 0
        order by hr.id desc;");
    $stmt->execute();
    $index = 2;

    // Add some data
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue("A1", 'Id')
        ->setCellValue("B1", 'Protocolo')
        ->setCellValue("C1", 'Protocolo Anterior')
        ->setCellValue("D1", 'DNI')
        ->setCellValue("E1", 'F. Procedimiento')
        ->setCellValue("F1", 'F. Nacimiento')
        ->setCellValue("G1", 'Edad Actual Paciente')
        // ->setCellValue("G1", 'Edad Paciente en Informe')
        ->setCellValue("H1", 'Edad Calculada')
        // ->setCellValue("I1", 'TED')
        // ->setCellValue("J1", 'EMBRIODONACION')
        ->setCellValue("I1", 'Extras')
        ->setCellValue("J1", 'Normales')
        ->setCellValue("K1", 'Anormales')
        ->setCellValue("L1", 'NR')
        ->setCellValue("M1", 'Mosaico')
        ->setCellValue("N1", 'Total');

    while ($info = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A$index", $info['id']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("B$index", $info['protocolo']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("C$index", $info['protocolo_anterior']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("D$index", $info['dni']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("E$index", $info['fecha_procedimiento']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("F$index", $info['fecha_nacimiento']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("G$index", $info['edad_actual']);
        // $objPHPExcel->setActiveSheetIndex(0)->setCellValue("G$index", $info['edad_informe']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("H$index", $info['edad_calculada']);
        // $objPHPExcel->setActiveSheetIndex(0)->setCellValue("I$index", $info['ted']);
        // $objPHPExcel->setActiveSheetIndex(0)->setCellValue("J$index", $info['embrioadopcion']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("I$index", $info['extras']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("J$index", $info['normales']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("K$index", $info['anormales']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("L$index", $info['nr']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("M$index", $info['mosaico']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("N$index", $info['total']);
        $index++;
    }

    // Rename worksheet
    $objPHPExcel->getActiveSheet()->setTitle('base');

    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);


    // Redirect output to a client’s web browser (Excel2007)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="grafica-normales.xlsx"');
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

    // If you're serving to IE over SSL, then the following may be needed
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header ('Pragma: public'); // HTTP/1.0

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
    exit;
}

function cargar_grafica($anio) {
    global $db;
    $anio = ($anio == '-' ? "'2015', '2016', '2017', '2018', '2019', '2020'" : "'$anio'");

$stmt = $db->prepare("SELECT
    edad_calculada, sum(normales) normales, sum(total) total, sum(normales)/sum(total)*100 tasa
    from (
        select
        case
            WHEN hr.des_dia = 0 AND hr.des_don IS NOT NULL THEN 
                extract(year from hr.fec) - extract(year from hp1.fnac) - ((to_char(hr.fec, 'MMDD') < to_char(hp1.fnac, 'MMDD'))::int)
            WHEN hr.p_od IS NOT NULL AND hr.p_od <> '' THEN 
                extract(year from hr.fec) - extract(year from hp2.fnac) - ((to_char(hr.fec, 'MMDD') < to_char(hp2.fnac, 'MMDD'))::int)
            ELSE 
                extract(year from hr.fec) - extract(year from hp.fnac) - ((to_char(hr.fec, 'MMDD') < to_char(hp.fnac, 'MMDD'))::int) 
        end AS edad_calculada
        , count(case when lad.ngs1 = 1 then true end) normales
        , count(*) total
        from hc_reprod hr
        inner join hc_paciente hp on hp.dni = hr.dni and hp.fnac <> '1899-12-30'
        inner join lab_aspira la on la.rep = hr.id and la.estado is true
        inner join lab_aspira_dias lad on lad.pro = la.pro and lad.estado is true
        left join lab_aspira la1 on la1.pro = lad.pro_c and la1.estado is true
        left join hc_paciente hp1 on hp1.dni = la1.dni
        left join hc_paciente hp2 on hp2.dni = hr.p_od
        where hr.estado = true and lad.ngs1 <> 0 and la.f_fin <> '1899-12-30'
        and hr.pago_extras ILIKE '%NGS%'
        and extract(year from hr.fec) in ($anio)
        group by hr.id, lad.pro,hp1.fnac,hp2.fnac,hp.fnac
        having count(case when lad.d2f_cic = 't' or  lad.d3f_cic = 't' or  lad.d4f_cic = 't' or  lad.d5f_cic = 't' or lad.d6f_cic = 't' then true end) = 0
    ) as x
    group by x.edad_calculada;");
$stmt->execute();


    $totales=[];
    $price = [];

    while ($info = $stmt->fetch(PDO::FETCH_ASSOC)) {
        array_push($totales, [
            'edad_calculada' => $info['edad_calculada'],
            'tasa' => $info['tasa'],
        ]);
    }

    foreach ($totales as $key => $row) {
        $price[$key] = $row['edad_calculada'];
    }

    array_multisort($price, SORT_ASC, $totales);

    $labels = [];
    $data = [];

    $edad_calculada_last = 0;
    foreach ($totales as $key => $value) {
        if ($edad_calculada_last != $value['edad_calculada']) {
            array_push($labels, $value['edad_calculada']);
            array_push($data, $value['tasa']);
            $edad_calculada_last = $value['edad_calculada'];
        }
    }

    return [
        'labels' => $labels,
        'data' => $data,
    ];
} ?>