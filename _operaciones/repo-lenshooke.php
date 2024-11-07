<?php
session_start();
require($_SERVER["DOCUMENT_ROOT"] . "/_database/database.php");
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
        case 'descargar_base':
            http_response_code(200);
            echo json_encode(["message" => descargar_base($_POST)]);
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

function descargar_base($data) {
    $ini = $data["ini"];
    $fin = $data["fin"];
    $pro_descargar = $data["pro_descargar"];
    $columnas = explode(',', $data["columnas"]);
    require($_SERVER["DOCUMENT_ROOT"] . "/_libraries/php_excel_18/PHPExcel.php");

    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()
    ->setCreator("Maarten Balliauw")
    ->setLastModifiedBy("Maarten Balliauw")
    ->setTitle("Office 2007 XLSX Test Document")
    ->setSubject("Office 2007 XLSX Test Document")
    ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
    ->setKeywords("office 2007 openxml php")
    ->setCategory("Test result file");

    global $db;
    global $farma;

    if (!empty($pro_descargar)) {
        $between = " and la.pro = '$pro_descargar'";
    } else {
        $between = " and lae.info_fmuestra between '$ini' and '$fin'";
    }

    // si - no
    $stmt = $db->prepare("SELECT id, nombre FROM si_no;");
    $stmt->execute();
    $si_no = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    array_unshift($si_no, "-");
    // apariencias
    $stmt = $db->prepare("SELECT id, nombre FROM apariencia_esperma;");
    $stmt->execute();
    $apariencias = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    array_unshift($apariencias, "-");
    // viscocidades
    $stmt = $db->prepare("SELECT id, nombre FROM viscosidad_esperma;");
    $stmt->execute();
    $viscocidades = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    array_unshift($viscocidades, "-");
    // liquefacciones
    $stmt = $db->prepare("SELECT id, nombre FROM licuefaccion_esperma;");
    $stmt->execute();
    $lique_array = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    array_unshift($lique_array, "-");
    // aglutinaciones
    $stmt = $db->prepare("SELECT id, nombre from man_aglutinacion;");
    $stmt->execute();
    $aglu_array = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    array_unshift($aglu_array, "-");

    $stmt = $db->prepare("SELECT
        hp.p_dni dni, concat(upper(hp.p_ape) , ' ', upper(hp.p_nom)) paciente
        , hp.p_fnac fecha_nacimiento
        , year(now()) - year(hp.p_fnac) - (date_format(now(), '%m%d') < date_format(hp.p_fnac, '%m%d')) edad_actual
        , case when hp.p_fnac = '1899-12-30' then '-' else  year(lae.info_fmuestra) - year(hp.p_fnac) - (date_format(lae.info_fmuestra, '%m%d') < date_format(hp.p_fnac, '%m%d')) end edad_del_momento
        , lae.abstinencia
        , lae.info_fmuestra, lae.info_medicacion
        , lae.macro_apariencia, lae.macro_viscosidad, lae.macro_liquefaccion, lae.macro_aglutinacion, lae.macro_ph, coalesce(lae.macro_volumen) macro_volumen
        , coalesce(lae.concen_exml) concen_exml, lae.concen_credon, lae.concen_exeyac
        , lae.movi_mprogresivo, lae.movi_mnoprogresivo
        , lae.movi_mprogresivo_lineal_cantidad, lae.movi_mprogresivo_no_lineal_cantidad, lae.movi_mnoprogresivo_cantidad, lae.movi_tvitalidad
        , lae.cine_vap cinetica_vap, lae.cine_vsl cinetica_vsl, lae.cine_vcl cinetica_vcl, lae.cine_lin cinetica_lin, lae.cine_str cinetica_str, lae.cine_wob cinetica_wob, lae.cine_alh cinetica_alh, lae.cine_bcf cinetica_bcf
        , lae.morfo_normal
        , lae.normal_largocabeza_porcentaje, lae.normal_ancho_porcentaje, lae.normal_perimetro_porcentaje, lae.normal_area_porcentaje, lae.normal_largocola_porcentaje
        , lae.resul_cripto, lae.resul_azo
        from lab_andro_esp lae
        inner join hc_pareja hp on hp.p_dni = lae.p_dni
        where lae.cine_vap is not null$between
        order by lae.info_fmuestra desc;
    ");
    $stmt->execute();
    $index = 2;
    $index_column = 0;

    for ($i=0; $i < count($columnas); $i++) {
        if ($columnas[$i] == "dni") {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'ID Paciente');
        }
        if ($columnas[$i] == "edad") {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Edad (años)');
        }
        if ($columnas[$i] == "paciente") {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Paciente');
        }
        if ($columnas[$i] == "abstinencia") {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Dias Abstinencia');
        }
        if ($columnas[$i] == "info_fmuestra") {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Fecha de Obtención');
        }
        if ($columnas[$i] == "info_medicacion") {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Medicación');
        }
        if ($columnas[$i] == "macro_apariencia") {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Apariencia');
        }
        if ($columnas[$i] == "macro_viscosidad") {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Viscocidad');
        }
        if ($columnas[$i] == "macro_liquefaccion") {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Licuefacción');
        }
        if ($columnas[$i] == "macro_aglutinacion") {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Aglutinación');
        }
        if ($columnas[$i] == "macro_ph") {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'pH');
        }
        if ($columnas[$i] == "macro_volumen") {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Volumen (mL)');
        }
        if ($columnas[$i] == "concen_exml") {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Espermatozoides por ml (millones)');
        }
        if ($columnas[$i] == "concen_credon") {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Células redondas');
        }
        if ($columnas[$i] == "concen_exeyac") {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Espermatozoides por eyaculado (millones)');
        }
        if ($columnas[$i] == "movi_total_moviles") {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Total móviles (P + NP) %');
        }
        if ($columnas[$i] == "movi_mprogresivo") {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Móvil Progresivo (P) %');
        }
        if ($columnas[$i] == "movi_mprogresivo_lineal_cantidad") {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'M.P. Lineal (VAP >= 25µm/s) %');
        }
        if ($columnas[$i] == "movi_mprogresivo_no_lineal_cantidad") {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'M.P. No Lineal (5µm/s <= VAP < 25µm/s) %');
        }
        if ($columnas[$i] == "movi_mnoprogresivo_cantidad") {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Móvil No progresivo (NP) %');
        }
        if ($columnas[$i] == "movi_no_moviles") {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'No móviles %');
        }
        if ($columnas[$i] == "movi_tvitalidad") {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Test de Vitalidad %');
        }
        if ($columnas[$i] == "cinetica_vap") {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'VAP');
        }
        if ($columnas[$i] == "cinetica_vsl") {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'VSL');
        }
        if ($columnas[$i] == "cinetica_vcl") {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'VCL');
        }
        if ($columnas[$i] == "cinetica_lin") {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'LIN');
        }
        if ($columnas[$i] == "cinetica_str") {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'STR');
        }
        if ($columnas[$i] == "cinetica_wob") {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'WOB');
        }
        if ($columnas[$i] == "cinetica_alh") {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'ALH');
        }
        if ($columnas[$i] == "cinetica_bcf") {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'BCF');
        }
        if ($columnas[$i] == "morfo_normal") {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Morfología Normales %');
        }
        if ($columnas[$i] == "morfo_anormal") {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Morfología Anormales %');
        }
        if ($columnas[$i] == "normal_largocabeza_porcentaje") {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Largo de Cabeza %');
        }
        if ($columnas[$i] == "normal_ancho_porcentaje") {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Ancho de Cabeza %');
        }
        if ($columnas[$i] == "normal_perimetro_porcentaje") {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Perímetro de Cabeza %');
        }
        if ($columnas[$i] == "normal_area_porcentaje") {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Área de Cabeza %');
        }
        if ($columnas[$i] == "normal_largocola_porcentaje") {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Largo de la Cola %');
        }
        if ($columnas[$i] == "diagnostico") {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Diagnóstico');
        }
    }

    while ($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $diagnostico = '';
        //diagnostico
        if ($item['resul_cripto'] == 1) {
            $diagnostico="CRIPTOzoospermia";
        } else if ($item['resul_azo'] == 1) {
            $diagnostico="Azoospermia";
        } else {
            // Hipospermia
            if ($item['macro_volumen'] < 1.5) {
                $diagnostico.="HIPO";
            }
            // Oligozoospermia
            if ($item['concen_exml'] < 15 || ((!!$item['macro_volumen'] ? $item['macro_volumen'] : 0) * (!!$item['concen_exml'] ? $item['concen_exml'] : 0)) < 39) {
                $diagnostico.="OLIGO";
            }
            // Astenozoospermia
            if (($item['movi_mprogresivo']+$item['movi_mnoprogresivo']) < 40 || $item['movi_mprogresivo'] < 32) {
                $diagnostico.="ASTENO";
            }
            // Teratozoospermia
            if ($item['morfo_normal'] < 4) {
                $diagnostico.="TERATO";
            }
            // Necrozoospermia
            if ($item['movi_tvitalidad'] < 58) {
                $diagnostico.="NECRO";
            }
            if (!empty($diagnostico)) {
                $diagnostico.="zoospermia";
                if (strpos($diagnostico, "OLIGO")!==false || strpos($diagnostico, "HIPO")!==false) {
                    $diagnostico.=", se sugiere evaluación ecográfica (Ecografia testicular)";
                }
            } else {
                $diagnostico="Normozoospermia";
            }
        }

        $index_column = 0;

        for ($i=0; $i < count($columnas); $i++) {
            /* if ($columnas[$i] == "finicio_pro") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item['finicio_pro']);
            } */
            if ($columnas[$i] == "dni") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item["dni"]);
            }
            if ($columnas[$i] == "edad") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item["edad_del_momento"]);
            }
            if ($columnas[$i] == "paciente") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item["paciente"]);
            }
            if ($columnas[$i] == "abstinencia") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item["abstinencia"]);
            }
            if ($columnas[$i] == "info_fmuestra") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item["info_fmuestra"]);
            }
            if ($columnas[$i] == "info_medicacion") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, (isset($item["info_medicacion"]) ? $si_no[$item["info_medicacion"]] : '-'));
            }
            if ($columnas[$i] == "macro_apariencia") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, (isset($item["macro_apariencia"]) ? $apariencias[$item["macro_apariencia"]] : '-'));
            }
            if ($columnas[$i] == "macro_viscosidad") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, (isset($item["macro_viscosidad"]) ? $viscocidades[$item["macro_viscosidad"]] : '-'));
            }
            if ($columnas[$i] == "macro_liquefaccion") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, (isset($item["macro_liquefaccion"]) ? $lique_array[$item["macro_liquefaccion"]] : '-'));
            }
            if ($columnas[$i] == "macro_aglutinacion") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, (isset($item["macro_aglutinacion"]) ? $aglu_array[$item["macro_aglutinacion"]] : '-'));
            }
            if ($columnas[$i] == "macro_ph") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item["macro_ph"]);
            }
            if ($columnas[$i] == "macro_volumen") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item["macro_volumen"]);
            }
            if ($columnas[$i] == "concen_exml") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item["concen_exml"]);
            }
            if ($columnas[$i] == "concen_credon") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item["concen_credon"]);
            }
            if ($columnas[$i] == "concen_exeyac") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index,  ((is_numeric($item['concen_exml']) and is_numeric($item['macro_volumen'])) ? number_format($item['concen_exml'] * $item['macro_volumen'], 2, '.', '') : '0.00'));
            }
            if ($columnas[$i] == "movi_total_moviles") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index,  ($item["movi_mprogresivo"] + $item["movi_mnoprogresivo"]));
            }
            if ($columnas[$i] == "movi_mprogresivo") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index,  ($item["movi_mprogresivo"]));
            }
            if ($columnas[$i] == "movi_mprogresivo_lineal_cantidad") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item["movi_mprogresivo_lineal_cantidad"]);
            }
            if ($columnas[$i] == "movi_mprogresivo_no_lineal_cantidad") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item["movi_mprogresivo_no_lineal_cantidad"]);
            }
            if ($columnas[$i] == "movi_mnoprogresivo_cantidad") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item["movi_mnoprogresivo_cantidad"]);
            }
            if ($columnas[$i] == "movi_no_moviles") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, (100 - $item["movi_mprogresivo"] - $item["movi_mnoprogresivo"]));
            }
            if ($columnas[$i] == "movi_tvitalidad") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item["movi_tvitalidad"]);
            }
            if ($columnas[$i] == "cinetica_vap") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item["cinetica_vap"]);
            }
            if ($columnas[$i] == "cinetica_vsl") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item["cinetica_vsl"]);
            }
            if ($columnas[$i] == "cinetica_vcl") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item["cinetica_vcl"]);
            }
            if ($columnas[$i] == "cinetica_lin") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item["cinetica_lin"]);
            }
            if ($columnas[$i] == "cinetica_str") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item["cinetica_str"]);
            }
            if ($columnas[$i] == "cinetica_wob") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item["cinetica_wob"]);
            }
            if ($columnas[$i] == "cinetica_alh") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item["cinetica_alh"]);
            }
            if ($columnas[$i] == "cinetica_bcf") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item["cinetica_bcf"]);
            }
            if ($columnas[$i] == "morfo_normal") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item["morfo_normal"]);
            }
            if ($columnas[$i] == "morfo_anormal") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, (100 - $item["morfo_normal"]));
            }
            if ($columnas[$i] == "normal_largocabeza_porcentaje") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item["normal_largocabeza_porcentaje"]);
            }
            if ($columnas[$i] == "normal_ancho_porcentaje") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item["normal_ancho_porcentaje"]);
            }
            if ($columnas[$i] == "normal_perimetro_porcentaje") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item["normal_perimetro_porcentaje"]);
            }
            if ($columnas[$i] == "normal_area_porcentaje") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item["normal_area_porcentaje"]);
            }
            if ($columnas[$i] == "normal_largocola_porcentaje") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item["normal_largocola_porcentaje"]);
            }
            if ($columnas[$i] == "diagnostico") {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $diagnostico);
            }
        }

        $index++;
    }

    // Rename worksheet
    $objPHPExcel->getActiveSheet()->setTitle('info-lenshooke');

    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);

    // Redirect output to a client’s web browser (Excel2007)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="repo-lenshooke.xlsx"');
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
} ?>
