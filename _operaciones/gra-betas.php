<?php
session_start();
require($_SERVER["DOCUMENT_ROOT"] . "/_database/database.php");
require($_SERVER["DOCUMENT_ROOT"] . "/config/environment.php");
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
        case 'cargar_grafica':
            http_response_code(200);
            echo json_encode(["message" => cargar_grafica($_POST)]);
            break;
        case 'descargar_excel':
            http_response_code(200);
            echo json_encode(["message" => descargar_excel($_POST)]);
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

function descargar_excel($data) {
    global $dblog;
    $login = $_SESSION['login'];
    $nombre_modulo="graficas_betas";
    $ruta="perfil_medico/cargar_grafica.php";
    $tipo_operacion="descarga_excel";
    $createdate=date("Y-m-d H:i:s");
    $sql = "INSERT INTO log_inmater
          (nombre_modulo, ruta, tipo_operacion, idusercreate,createdate)
          VALUES
          (?, ?, ?, ?,?)";
    $statement = $dblog->prepare($sql);
    $statement->execute(array($nombre_modulo,$ruta,$tipo_operacion,$login, $createdate));
    $where = "";
    $having = "";
    $medico = $data["medico"];
    $embriologo_transferencia = $data["embriologo_transferencia"];
    $ngs = $data["ngs"];
    $edesde_ovulo = $data["edesde_ovulo"];
    $ehasta_ovulo = $data["ehasta_ovulo"];
    $edesde_utero = $data["edesde_utero"];
    $ehasta_utero = $data["ehasta_utero"];
    $tipo_paciente = $data["tipo_paciente"];

    if ($medico != 'false' and $medico != '') {
        $where .= " and lat.med ilike '%$medico%'";
    }

    if ($embriologo_transferencia != 'false' and $embriologo_transferencia != '') {
        $where .= " and lat.emb = '$embriologo_transferencia'";
    }

    if ($ngs != 'false' and $ngs != '') {
        if ($ngs == "s") {
            $having .= " having sum(case when lad.ngs3 in (1, 2) then true else false end) != 0";
        } else {
            $having .= " having sum(case when lad.ngs3 in (1, 2) then true else false end) = 0";
        }
    }
    if ($edesde_utero != 'false' and $edesde_utero != '' and $ehasta_utero != 'false' and $ehasta_utero != '') {
        $where .= " and (
            (coalesce(rep.des_dia, 0) >= 1 and year(rep.f_asp) - year(pac.fnac) - (date_format(rep.f_asp, '%m%d') < date_format(pac.fnac, '%m%d')) between $edesde_utero and $ehasta_utero) or
            (coalesce(rep.des_dia, 0) < 1 and year(rep.f_tra) - year(pac.fnac) - (date_format(rep.f_tra, '%m%d') < date_format(pac.fnac, '%m%d')) between $edesde_utero and $ehasta_utero)
        )";
    }
    if ($edesde_ovulo != 'false' and $edesde_ovulo != '' and $ehasta_ovulo != 'false' and $ehasta_ovulo != '') {
        $where .= " and (d2.pro is not null and year(r2.f_asp) - year(p2.fnac) - (date_format(r2.f_asp, '%m%d') < date_format(p2.fnac, '%m%d')) between $edesde_ovulo and $ehasta_ovulo) or 
            (d1.pro is not null and coalesce(r1.p_od, '') <> '' and year(r1.f_asp) - year(p_od.fnac) - (date_format(r1.f_asp, '%m%d') < date_format(p_od.fnac, '%m%d')) between $edesde_ovulo and $ehasta_ovulo) or
            (d1.pro is not null and coalesce(r1.des_don, '') <> '' and year(r1.f_asp) - year(p_desdon.fnac) - (date_format(r1.f_asp, '%m%d') < date_format(p_desdon.fnac, '%m%d')) between $edesde_ovulo and $ehasta_ovulo) or
            (d1.pro is not null and not(coalesce(r1.p_od, '') <> '') and not(coalesce(r1.des_don, '') <> '') and year(r1.f_asp) - year(p1.fnac) - (date_format(r1.f_asp, '%m%d') < date_format(p1.fnac, '%m%d')) between $edesde_ovulo and $ehasta_ovulo)
            (lad.pro is not null and coalesce(rep.p_od, '') <> '' and year(rep.f_asp) - year(pac_od.fnac) - (date_format(rep.f_asp, '%m%d') < date_format(pac_od.fnac, '%m%d')) between $edesde_ovulo and $ehasta_ovulo) or
            (lad.pro is not null and coalesce(rep.des_don, '') <> '' and year(rep.f_asp) - year(pac_desdon.fnac) - (date_format(rep.f_asp, '%m%d') < date_format(pac_desdon.fnac, '%m%d')) between $edesde_ovulo and $ehasta_ovulo) or
            (lad.pro is not null and coalesce(rep.p_od, '') = '' and coalesce(rep.des_don, '') = '' and year(rep.f_asp) - year(pac.fnac) - (date_format(rep.f_asp, '%m%d') < date_format(pac.fnac, '%m%d')) between $edesde_ovulo and $ehasta_ovulo)";
    }
    if ($tipo_paciente != 'false' and $tipo_paciente != '') {
        $where.= " and la.tip = '$tipo_paciente'";
    }

    global $db;

    $stmt = $db->prepare("SELECT
        lat.beta
        , lat.pro protocolo
        , case
            when d2.pro is not null and coalesce(r2.p_od, '') <> '' then a2.tip
            when d2.pro is not null and coalesce(r2.des_don, '') <> '' then a2.tip
            when d2.pro is not null then (case when a2.tip = 'D' and pac.dni <> p2.dni then 'R' when a2.tip = 'D' and pac.dni = p2.dni then 'P' else a2.tip end)
            when d1.pro is not null and coalesce(r1.p_od, '') <> '' then a1.tip
            when d1.pro is not null and coalesce(r1.des_don, '') <> '' then a1.tip
            when d1.pro is not null then (case when a1.tip = 'D' and a1.dni <> pac.dni then 'R' when a1.tip = 'D' and a1.dni = pac.dni then 'P' else a1.tip end)
            when lad.pro is not null and coalesce(rep.p_od, '') <> '' then la.tip
            when lad.pro is not null and coalesce(rep.des_don, '') <> '' then la.tip
            when lad.pro is not null and la.tip = 'D' then 'P'
            when lad.pro is not null then la.tip
            else la.tip end paciente_tipo
        , concat(trim(upper(pac.ape)), ' ', trim(upper(pac.nom))) paciente
        , trim(upper(lat.med)) medico
        , case
            when d2.pro is not null and coalesce(r2.p_od, '') <> '' then year(r2.f_asp) - year(p2_od.fnac) - (date_format(r2.f_asp, '%m%d') < date_format(p2_od.fnac, '%m%d'))
            when d2.pro is not null and coalesce(r2.des_don, '') <> '' then year(r2.f_asp) - year(p2_desdon.fnac) - (date_format(r2.f_asp, '%m%d') < date_format(p2_desdon.fnac, '%m%d'))
            when d2.pro is not null then year(r2.f_asp) - year(p2.fnac) - (date_format(r2.f_asp, '%m%d') < date_format(p2.fnac, '%m%d'))
            when d1.pro is not null and coalesce(r1.p_od, '') <> '' then year(r1.f_asp) - year(p_od.fnac) - (date_format(r1.f_asp, '%m%d') < date_format(p_od.fnac, '%m%d'))
            when d1.pro is not null and coalesce(r1.des_don, '') <> '' then year(r1.f_asp) - year(p_desdon.fnac) - (date_format(r1.f_asp, '%m%d') < date_format(p_desdon.fnac, '%m%d'))
            when d1.pro is not null then year(r1.f_asp) - year(p1.fnac) - (date_format(r1.f_asp, '%m%d') < date_format(p1.fnac, '%m%d'))
            when lad.pro is not null and coalesce(rep.p_od, '') <> '' then year(rep.f_asp) - year(pac_od.fnac) - (date_format(rep.f_asp, '%m%d') < date_format(pac_od.fnac, '%m%d'))
            when lad.pro is not null and coalesce(rep.des_don, '') <> '' then year(rep.f_asp) - year(pac_desdon.fnac) - (date_format(rep.f_asp, '%m%d') < date_format(pac_desdon.fnac, '%m%d'))
            when lad.pro is not null then year(rep.f_asp) - year(pac.fnac) - (date_format(rep.f_asp, '%m%d') < date_format(pac.fnac, '%m%d'))
            else year(rep.f_asp) - year(pac.fnac) - (date_format(rep.f_asp, '%m%d') < date_format(pac.fnac, '%m%d')) end edad_ovulo
        , case
            when coalesce(rep.des_dia, 0) >= 1 then year(rep.f_asp) - year(pac.fnac) - (date_format(rep.f_asp, '%m%d') < date_format(pac.fnac, '%m%d'))
            when coalesce(rep.des_dia, 0) < 1 then year(rep.f_tra) - year(pac.fnac) - (date_format(rep.f_tra, '%m%d') < date_format(pac.fnac, '%m%d'))
            else '-' end edad_utero
        , trim(upper(lu.nom)) embriologo_transferencia
        , case
            when coalesce(rep.des_dia, 0) >= 1 then rep.f_asp
            when lat.dia = 6 then la.fec6
            when lat.dia = 5 then la.fec5
            when lat.dia = 4 then la.fec4
            when lat.dia = 3 then la.fec3
            else null end fecha
        , case
            when coalesce(rep.des_dia, 0) >= 1 then year(rep.f_asp)
            when lat.dia = 6 then year(la.fec6)
            when lat.dia = 5 then year(la.fec5)
            when lat.dia = 4 then year(la.fec4)
            when lat.dia = 3 then year(la.fec3)
            else null end anio
        , case
            when d2.pro is not null and sum(d2.ngs3) > 0  then 'Si'
            when d1.pro is not null and sum(d1.ngs3) > 0  then 'Si'
            when lad.pro is not null and sum(lad.ngs3) > 0 then 'Si'
            else 'No' end ngs
        , trim(upper(coalesce(mb.nombre, 'PENDIENTE'))) beta
        from lab_aspira_t lat
        inner join lab_aspira la on la.pro = lat.pro and la.estado is true
        inner join lab_aspira_dias lad on lad.pro = la.pro and lad.estado is true
        inner join hc_reprod rep on rep.id = la.rep
        inner join hc_paciente pac on pac.dni = rep.dni
        left join hc_paciente pac_od on pac_od.dni = rep.p_od
        left join hc_paciente pac_desdon on pac_desdon.dni = rep.des_don
        -- procedimiento anterior
        left join lab_aspira_dias d1 on d1.pro = lad.pro_c and d1.estado is true
        left join lab_aspira a1 on a1.pro = d1.pro and a1.estado is true
        left join hc_reprod r1 on r1.id = a1.rep
        left join hc_paciente p1 on p1.dni = r1.dni
        left join hc_paciente p_od on p_od.dni = r1.p_od
        left join hc_paciente p_desdon on p_desdon.dni = r1.des_don
        -- procedimiento origen
        left join lab_aspira_dias d2 on d2.pro = d1.pro_c and d2.estado is true
        left join lab_aspira a2 on a2.pro = d2.pro and a2.estado is true
        left join hc_reprod r2 on r2.id = a2.rep
        left join hc_paciente p2 on p2.dni = r2.dni
        left join hc_paciente p2_od on p2_od.dni = r2.p_od
        left join hc_paciente p2_desdon on p2_desdon.dni = r2.des_don
        left join lab_user lu on lu.id = lat.emb
        left join man_beta_rinicial mb on mb.id = lat.beta
        where 1=1 and lat.estado is true$where
        group by lat.pro, lat.beta, lad.pro_c
        $having
        order by case when coalesce(rep.des_dia, 0) >= 1 then year(rep.f_asp)
            when lat.dia = 6 then year(la.fec6)
            when lat.dia = 5 then year(la.fec5)
            when lat.dia = 4 then year(la.fec4)
            when lat.dia = 3 then year(la.fec3)
            else null end desc, lat.beta asc;");
    $stmt->execute();

    // descargar excel
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

    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'Protocolo');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', 'Tipo Paciente');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', 'Paciente');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', 'Edad Ovulo');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E1', 'Edad Utero');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F1', 'Médico');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G1', 'Embriologo Transferencia');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H1', 'NGS');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I1', 'Fecha');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J1', 'Año');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K1', 'Beta');
    $index = 2;

    while ($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $index_column = 0;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item["protocolo"]);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item["paciente_tipo"]);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item["paciente"]);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item["edad_ovulo"]);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item["edad_utero"]);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item["medico"]);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item["embriologo_transferencia"]);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item["ngs"]);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item["fecha"]);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item["anio"]);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue(getNameFromNumber($index_column) . $index, $item["beta"]);
        $index++;
    }

    // Rename worksheet
    $objPHPExcel->getActiveSheet()->setTitle('grafica-betas');

    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);

    // Redirect output to a client’s web browser (Excel2007)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="repo-grafica-betas.xlsx"');
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

    // If you're serving to IE over SSL, then the following may be needed
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header ('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header ('Pragma: public'); // HTTP/1.0

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    ob_start();
    $objWriter->save('php://output');
    // exit;
    $xlsData = ob_get_contents();
    ob_end_clean();
    
    $response =  [
        'op' => 'ok',
        'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
    ];
    
    die(json_encode($response));
}

function cargar_grafica($data) {
    global $dblog;
    $login = $_SESSION['login'];
    $nombre_modulo="grafica_betas";
    $ruta="perfil_medico/cargar_grafica.php";
    $tipo_operacion="consulta";
    $createdate=date("Y-m-d H:i:s");
    $sql = "INSERT INTO log_inmater
          (nombre_modulo, ruta, tipo_operacion, idusercreate,createdate)
          VALUES
          (?, ?, ?, ?,?)";
    $statement = $dblog->prepare($sql);
    $statement->execute(array($nombre_modulo,$ruta,$tipo_operacion,$login, $createdate));
    $where = "";
    $having = "";
    $medico = $data["medico"];
    $embriologo_transferencia = $data["embriologo_transferencia"];
    $ngs = $data["ngs"];
    $edesde_ovulo = $data["edesde_ovulo"];
    $ehasta_ovulo = $data["ehasta_ovulo"];
    $edesde_utero = $data["edesde_utero"];
    $ehasta_utero = $data["ehasta_utero"];
    $tipo_paciente = $data["tipo_paciente"];
    if ($medico != 'false' and $medico != '') {
        $where .= " and lat.med ilike '%$medico%'";
    }
    if ($embriologo_transferencia != 'false' and $embriologo_transferencia != '') {
        $where .= " and lat.emb = '$embriologo_transferencia'";
    }
    if ($ngs != 'false' and $ngs != '') {
        if ($ngs == "s") {
            $having .= " having sum(case when lad.ngs3 in (1, 2) then 1 else 0 end) != 0";
        } else {
            $having .= " having sum(case when lad.ngs3 in (1, 2) then 1 else 0 end) = 0";
        }        
    }
    if ($edesde_utero != 'false' and $edesde_utero != '' and $ehasta_utero != 'false' and $ehasta_utero != '') {
        $where .= " and (
            (coalesce(rep.des_dia, 0) >= 1 and date_part('year', age(rep.f_asp, pac.fnac)) between $edesde_utero and $ehasta_utero) or
            (coalesce(rep.des_dia, 0) < 1 and date_part('year', age(rep.f_tra, pac.fnac)) between $edesde_utero and $ehasta_utero)
        )";
    }
    if ($edesde_ovulo != 'false' and $edesde_ovulo != '' and $ehasta_ovulo != 'false' and $ehasta_ovulo != '') {
        $where .= " and (d2.pro is not null and date_part('year', age(r2.f_asp, p2.fnac)) between $edesde_ovulo and $ehasta_ovulo) or 
            (d1.pro is not null and coalesce(r1.p_od, '') <> '' and date_part('year', age(r1.f_asp, p_od.fnac)) between $edesde_ovulo and $ehasta_ovulo) or
            (d1.pro is not null and coalesce(r1.des_don, '') <> '' and date_part('year', age(r1.f_asp, p_desdon.fnac)) between $edesde_ovulo and $ehasta_ovulo) or
            (d1.pro is not null and not(coalesce(r1.p_od, '') <> '') and not(coalesce(r1.des_don, '') <> '') and date_part('year', age(r1.f_asp, p1.fnac)) between $edesde_ovulo and $ehasta_ovulo)
            (lad.pro is not null and coalesce(rep.p_od, '') <> '' and date_part('year', age(rep.f_asp, pac_od.fnac)) between $edesde_ovulo and $ehasta_ovulo) or
            (lad.pro is not null and coalesce(rep.des_don, '') <> '' and date_part('year', age(rep.f_asp, pac_desdon.fnac)) between $edesde_ovulo and $ehasta_ovulo) or
            (lad.pro is not null and coalesce(rep.p_od, '') = '' and coalesce(rep.des_don, '') = '' and date_part('year', age(rep.f_asp, pac.fnac)) between $edesde_ovulo and $ehasta_ovulo)";
    }
    if ($tipo_paciente != 'false' and $tipo_paciente != '') {
        $where.= " and la.tip = '$tipo_paciente'";
    }

    global $db;
    $stmt = $db->prepare("SELECT
        x.beta, x.anio, count(*) total
        from (
        select
        lat.beta
        , case when coalesce(rep.des_dia, 0) >= 1 then date_part('year', rep.f_asp::date)
            when lat.dia = 6 then date_part('year', la.fec6::date)
            when lat.dia = 5 then date_part('year', la.fec5::date)
            when lat.dia = 4 then date_part('year', la.fec4::date)
            when lat.dia = 3 then date_part('year', la.fec3::date)
            else null end anio
        from lab_aspira_t lat
        inner join lab_aspira la on la.pro = lat.pro and la.estado is true
        inner join lab_aspira_dias lad on lad.pro = la.pro and lad.estado is true
        inner join hc_reprod rep on rep.id = la.rep
        inner join hc_paciente pac on pac.dni = rep.dni
        left join hc_paciente pac_od on pac_od.dni = rep.p_od
        left join hc_paciente pac_desdon on pac_desdon.dni = rep.des_don
        -- procedimiento anterior
        left join lab_aspira_dias d1 on d1.pro = lad.pro_c and d1.estado is true
        left join lab_aspira a1 on a1.pro = d1.pro and a1.estado is true
        left join hc_reprod r1 on r1.id = a1.rep
        left join hc_paciente p1 on p1.dni = r1.dni
        left join hc_paciente p_od on p_od.dni = r1.p_od
        left join hc_paciente p_desdon on p_desdon.dni = r1.des_don
        -- procedimiento origen
        left join lab_aspira_dias d2 on d2.pro = d1.pro_c and d2.estado is true
        left join lab_aspira a2 on a2.pro = d2.pro and a2.estado is true
        left join hc_reprod r2 on r2.id = a2.rep
        left join hc_paciente p2 on p2.dni = r2.dni
        left join hc_paciente p2_od on p2_od.dni = r2.p_od
        left join hc_paciente p2_desdon on p2_desdon.dni = r2.des_don
        left join lab_user lu on lu.id = lat.emb
        left join man_beta_rinicial mb on mb.id = lat.beta
        where 1=1 and lat.estado is true$where
        group by lat.pro, lat.beta, lad.pro_c, rep.des_dia, rep.f_asp, la.fec6, la.fec5, la.fec4, la.fec3
        $having
        order by lat.beta, case when coalesce(rep.des_dia, 0) >= 1 then date_part('year', rep.f_asp::date)
            when lat.dia = 6 then date_part('year', la.fec6::date)
            when lat.dia = 5 then date_part('year', la.fec5::date)
            when lat.dia = 4 then date_part('year', la.fec4::date)
            when lat.dia = 3 then date_part('year', la.fec3::date)
            else null end
        ) as x
        group by x.beta, x.anio
        order by x.beta, x.anio desc;");
    $stmt->execute();

    $sort = [2021, 2020, 2019, 2018, 2017, 2016, 2015];
    $anios = [];
    $totales = [];
    $data1 = [];
    $beta = 0;
    $total_anio = [];
    while ($info = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (!empty($info["anio"])) {
            if ($beta != $info["beta"]) {
                foreach ($sort as $key => $value) {
                    if (!in_array($value, $anios)) {
                        array_splice($anios, $key, 0, [$value]);
                        array_splice($totales, $key, 0, [0]);
                    }
                }

                if (count($total_anio) == 0) {
                    foreach ($totales as $id => $value1) {
                        array_push($total_anio, $value1);
                    }
                } else {
                    foreach ($totales as $id => $value1) {
                        if(isset($total_anio[$id]))$total_anio[$id] += $value1;
                    }
                }

                array_push($data1, [$anios, $totales]);
                $anios = [];
                $totales = [];
                array_push($anios, $info["anio"]);
                array_push($totales, $info["total"]);
                $beta = $info["beta"];
            } else {
                array_push($anios, $info["anio"]);
                array_push($totales, $info["total"]);
            }
        }
    }

    foreach ($sort as $key => $value) {
        if (!in_array($value, $anios)) {
            array_splice($anios, $key, 0, [$value]);
            array_splice($totales, $key, 0, [0]);
        }
    }

    if (count($total_anio) == 0) {
        foreach ($totales as $id => $value1) {
            array_push($total_anio, $value1);
        }
    } else {
        foreach ($totales as $id => $value1) {
            $total_anio[$id] += $value1;
        }
    }

    array_push($data1, [$anios, $totales]);



    return ['total' => $data1, 'total_anio' => $total_anio];
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