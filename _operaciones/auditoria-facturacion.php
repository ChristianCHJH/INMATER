<?php
session_start();
error_reporting( error_reporting() & ~E_NOTICE );

require($_SERVER["DOCUMENT_ROOT"] . "/config/environment.php");
require($_SERVER["DOCUMENT_ROOT"] . "/_database/database.php");
require($_SERVER["DOCUMENT_ROOT"] . "/_database/database_log.php");

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
        case 'descargar_reporte':
            http_response_code(200);
            echo json_encode(["message" => descargar_reporte($_POST)]);
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

function descargar_reporte($data) {
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
    global $dblog;

    if (true) {
        // print("<pre>"); print_r($data); print("</pre>");
        if (isset($data["repo_ini"]) && !empty($data["repo_ini"]) && isset($data["repo_fin"]) && !empty($data["repo_fin"])) {
            $ini = $data["repo_ini"];
            $fin = $data["repo_fin"];
            $where .= " and r.fec between '$ini' and '$fin'";
        } else {
            $ini = $fin = "";
        }

        if (isset($data["repo_seriecpe"]) && !empty($data["repo_seriecpe"]) && isset($data["repo_correlativocpe"]) && !empty($data["repo_correlativocpe"])) {
            $stmt = $db->prepare("SELECT tip_recibo recibo_tip, id_recibo recibo_id, serie_cpe, correlativo_cpe
            from facturacion_recibo_mifact_response frmr
            where correlativo_cpe ilike ? and serie_cpe ilike ? and estado_documento in ('101', '102', '103')
            limit 1 offset 0");
            $stmt->execute(["%" . $data["repo_correlativocpe"] . "%", "%" . $data["repo_seriecpe"] . "%"]);

            if ($stmt->rowCount() > 0) {
                $info = $stmt->fetch(PDO::FETCH_ASSOC);
                $serie_cpe = $info["repo_seriecpe"];
                $correlativo_cpe = $info["repo_correlativocpe"];
                $where .= " and r.recibo_tip = " . $info["recibo_tip"] . " and r.recibo_id = " . $info["recibo_id"];
            } else {
                $where .= " and r.id = 0";
            }
        }

        if (isset($data["repo_recibotip"]) && !empty($data["repo_recibotip"]) && isset($data["repo_reciboid"]) && !empty($data["repo_reciboid"])) {
            $recibo_tip = $data["repo_recibotip"];
            $recibo_id = $data["repo_reciboid"];
            $where .= " and r.recibo_tip = $recibo_tip and r.recibo_id = $recibo_id";
        }

        if ($where == "") {
            $where = " and r.id = 0";
        }
    } else {
        $where .= " and r.id = 0";
    }

    // $where = "";

    $stmt = $dblog->prepare("SELECT
        upper(mc.nombre) tipo_comprobante, mts.nombre tipo_servicio, mtf.abreviatura tipo_documento_facturacion
        , r.*
        from recibos r
        inner join appinmater_modulo.man_comprobantes mc on mc.id = r.recibo_tip
        inner join appinmater_modulo.man_tipo_servicio mts on mts.id = r.t_ser
        inner join appinmater_modulo.man_tipo_documento_facturacion mtf on mtf.id = r.id_tipo_documento_facturacion
        where 1=1$where
        order by r.id desc");
    $stmt->execute();
    $rows = $stmt->fetchAll();
    $index = 2;
    $index_column = 0;

    for ($i=0; $i < count($columnas); $i++) {
        // informacion de auditoria
        if ($columnas[$i] == "createdate") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Fecha de auditoría'); }
        if ($columnas[$i] == "action") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Estado de auditoría'); }
        if ($columnas[$i] == "idusercreate") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Usuario auditado'); }
        // informacion de comprobante
        if ($columnas[$i] == "fecha_emision") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Fecha de emisión'); }
        if ($columnas[$i] == "tipo_comprobante") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Tipo comprobante'); }
        if ($columnas[$i] == "numero_comprobante") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Número comprobante'); }
        if ($columnas[$i] == "serie_cpe") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Serie electrónico'); }
        if ($columnas[$i] == "correlativo_cpe") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Correlativo electrónico'); }
        // datos de paciente
        if ($columnas[$i] == "numero_documento") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Número documento'); }
        if ($columnas[$i] == "apellidos_nombres") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Apellidos y nombres'); }
        if ($columnas[$i] == "medico") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Médico'); }
        // servicios contratados
        if ($columnas[$i] == "t_ser") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Tipo de servicio'); }
        if ($columnas[$i] == "pak") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Paquetes'); }
        if ($columnas[$i] == "ser") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Servicios'); }
        // datos de facturacion
        if ($columnas[$i] == "correo_electronico") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Correo electrónico'); }
        if ($columnas[$i] == "id_tipo_documento_facturacion") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Tipo de documento'); }
        if ($columnas[$i] == "ruc") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Número de documento'); }
        if ($columnas[$i] == "raz") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Nombres'); }
        if ($columnas[$i] == "direccionfiscal") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Dirección'); }
        // total a pagar
        if ($columnas[$i] == "mon") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Moneda'); }
        if ($columnas[$i] == "tot") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Total'); }
        if ($columnas[$i] == "descuento") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Descuento'); }
        if ($columnas[$i] == "total_cancelar") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Total a cancelar'); }
        // formas de pago
        if ($columnas[$i] == "t1") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Medio de pago 1'); }
        if ($columnas[$i] == "m1") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Moneda 1'); }
        if ($columnas[$i] == "p1") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Monto 1'); }
        if ($columnas[$i] == "banco1") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Banco 1'); }
        if ($columnas[$i] == "tipotarjeta1") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Tipo de tarjeta 1'); }
        if ($columnas[$i] == "numerocuotas1") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'N° de cuotas 1'); }
        if ($columnas[$i] == "t2") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Medio de pago 2'); }
        if ($columnas[$i] == "m2") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Moneda 2'); }
        if ($columnas[$i] == "p2") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Monto 2'); }
        if ($columnas[$i] == "banco2") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Banco 2'); }
        if ($columnas[$i] == "tipotarjeta2") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Tipo de tarjeta 2'); }
        if ($columnas[$i] == "numerocuotas2") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'N° de cuotas 2'); }
        if ($columnas[$i] == "t3") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Medio de pago 3'); }
        if ($columnas[$i] == "m3") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Moneda 3'); }
        if ($columnas[$i] == "p3") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Monto 3'); }
        if ($columnas[$i] == "banco3") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Banco 3'); }
        if ($columnas[$i] == "tipotarjeta3") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Tipo de tarjeta 3'); }
        if ($columnas[$i] == "numerocuotas3") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'N° de cuotas 3'); }
        // otros servicios
        if ($columnas[$i] == "anglo") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Estado Anglolab'); }
        // estado de comprobante
        if ($columnas[$i] == "comentarios") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Comentarios'); }
        if ($columnas[$i] == "comprobante_referencia") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Comprobante de referencia'); }
        if ($columnas[$i] == "anu") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . '1', 'Anulado'); }
    }

    foreach ($rows as $item) {
        $index_column = 0;
        $servicio = "";

        for ($i=0; $i < count($columnas); $i++) {
            // informacion de auditoria
            if ($columnas[$i] == "createdate") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, date('Y-m-d H:i:s', strtotime($item["createdate"]))); }
            if ($columnas[$i] == "action") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item['action']); }
            if ($columnas[$i] == "idusercreate") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item['idusercreate']); }
            // informacion de comprobante
            if ($columnas[$i] == "fecha_emision") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, date('Y-m-d', strtotime($item["fec"]))); }
            if ($columnas[$i] == "tipo_comprobante") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item['tipo_comprobante']); }
            if ($columnas[$i] == "numero_comprobante") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item['recibo_id']); }
            if ($columnas[$i] == "serie_cpe") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, ""); }
            if ($columnas[$i] == "correlativo_cpe") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, ""); }
            // datos de paciente
            if ($columnas[$i] == "numero_documento") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item['dni']); }
            if ($columnas[$i] == "apellidos_nombres") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, mb_strtoupper($item["nom"])); }
            if ($columnas[$i] == "medico") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, mb_strtoupper($item["med"])); }
            // servicios contratados
            if ($columnas[$i] == "t_ser") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, mb_strtoupper($item["tipo_servicio"])); }
            if ($columnas[$i] == "pak") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item['pak']); }
            if ($columnas[$i] == "ser") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $servicio); }
            // datos de facturacion
            if ($columnas[$i] == "correo_electronico") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, mb_strtolower($item["correo_electronico"])); }
            if ($columnas[$i] == "id_tipo_documento_facturacion") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, mb_strtoupper($item["tipo_documento_facturacion"])); }
            if ($columnas[$i] == "ruc") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item['ruc']); }
            if ($columnas[$i] == "raz") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, mb_strtoupper($item['raz'])); }
            if ($columnas[$i] == "direccionfiscal") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item['direccionfiscal']); }
            // total a pagar
            if ($columnas[$i] == "mon") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item['mon']); }
            if ($columnas[$i] == "tot") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item['tot']); }
            if ($columnas[$i] == "descuento") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item['descuento']); }
            if ($columnas[$i] == "total_cancelar") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item['total_cancelar']); }
            // formas de pago
            if ($columnas[$i] == "t1") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item['t1']); }
            if ($columnas[$i] == "m1") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item['m1']); }
            if ($columnas[$i] == "p1") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item['p1']); }
            if ($columnas[$i] == "banco1") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item['banco1']); }
            if ($columnas[$i] == "tipotarjeta1") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item['tipotarjeta1']); }
            if ($columnas[$i] == "numerocuotas1") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item['numerocuotas1']); }
            if ($columnas[$i] == "t2") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item['t2']); }
            if ($columnas[$i] == "m2") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item['m2']); }
            if ($columnas[$i] == "p2") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item['p2']); }
            if ($columnas[$i] == "banco2") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item['banco2']); }
            if ($columnas[$i] == "tipotarjeta2") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item['tipotarjeta2']); }
            if ($columnas[$i] == "numerocuotas2") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item['numerocuotas2']); }
            if ($columnas[$i] == "t3") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item['t3']); }
            if ($columnas[$i] == "m3") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item['m3']); }
            if ($columnas[$i] == "p3") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item['p3']); }
            if ($columnas[$i] == "banco3") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item['banco3']); }
            if ($columnas[$i] == "tipotarjeta3") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item['tipotarjeta3']); }
            if ($columnas[$i] == "numerocuotas3") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item['numerocuotas3']); }
            // otros servicios
            if ($columnas[$i] == "anglo") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index,substr($item["anglo"], 0, 50)); }
            // estado de comprobante
            if ($columnas[$i] == "comentarios") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item['comentarios']); }
            if ($columnas[$i] == "comprobante_referencia") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item['comprobante_referencia']); }
            if ($columnas[$i] == "anu") { $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, ($item["anu"] == 0 ? "No": "Si")); }
        }

        $index++;
    }

    // Rename worksheet
    $objPHPExcel->getActiveSheet()->setTitle('base');

    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);

    // Redirect output to a client’s web browser (Excel2007)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="repo-auditoria-facturacion.xlsx"');
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