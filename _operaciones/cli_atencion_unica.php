<?php
session_start();
require($_SERVER["DOCUMENT_ROOT"] . "/_database/database.php");
require($_SERVER["DOCUMENT_ROOT"] . "/_database/database_farmacia.php");
require($_SERVER["DOCUMENT_ROOT"] . "/config/environment.php");

if (!!$_SESSION) {
    $login = $_SESSION['login'];
} else {
    http_response_code(400);
    echo json_encode(["message" => "no se inició sesión"]);
    exit();
}

if (isset($data["tipo"]) && !empty($data["tipo"])) {
    switch ($data["tipo"]) {
        case 'agregar_atencion':
            http_response_code(200);
            echo json_encode(["message" => agregarAtencion($data, $login)]);
            break;
        
        default:
            http_response_code(400);
            echo json_encode(["message" => "la operacion no existe"]);
            break;
    }
}

if (isset($_POST["tipo"]) && !empty($_POST["tipo"])) {
    switch ($_POST["tipo"]) {
        case 'buscar_atencion':
            http_response_code(200);
            echo json_encode(["message" => buscar_atencion($_POST, $login)]);
            break;
        
        default:
            http_response_code(400);
            echo json_encode(["message" => "la operacion no existe"]);
            break;
    }
}

function buscar_atencion($data) {
    global $db;
    $stmt = $db->prepare("SELECT
        cau.*, mc.nombre medios_comunicacion, mc.color medios_comunicacion_color, hp.tip tipo_documento, upper(concat(hp.ape, ' ', hp.nom)) paciente, hp.mai correo_electronico, cau.area_id, upper(ma.nombre) area
        from cli_atencion_unica cau
        inner join hc_paciente hp on hp.dni = cau.paciente_id
        inner join man_medios_comunicacion mc on mc.id = hp.medios_comunicacion_id
        inner join man_area ma on ma.estado = 1 and ma.id = cau.area_id
        where cau.estado = 1 and (cau.codigo = ? or cast(cau.codigo as unsigned) = ? or cau.paciente_id = ?);");
    $stmt->execute([$data['codigo'], $data['codigo'], $data['codigo']]);

    $content = '-';
    $tipo_documento = '';
    $paciente_id = '';
    $paciente = '';
    $correo_electronico = '';
    $medico = '';
    $sede = '';
    $medios_comunicacion = '';
    $medios_comunicacion_color = '';

    if ($stmt->rowCount() > 0) {
        while ($info = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // consulto segun el tipo de area
            $detalle = '';
            $paciente_id = $info["paciente_id"];
            $paciente = $info["paciente"];
            $correo_electronico = $info["correo_electronico"];
            $medios_comunicacion = '<b>El paciente pertenece a la campaña: ' . $info["medios_comunicacion"] . '</b>';
            $medios_comunicacion_color = $info["medios_comunicacion_color"];

            // tipo documento
            if ($info['tipo_documento'] == 'CEX') {
                $info['tipo_documento'] = 'CE';
            }

            $stmt_tip = $db->prepare("SELECT id from man_tipo_documento_facturacion where codigo = ?;");
            $stmt_tip->execute([$info['tipo_documento']]);
            if ($stmt_tip->rowCount() > 0) {
                $tipo_documento = $stmt_tip->fetch(PDO::FETCH_ASSOC)["id"];
            }

            switch ($info['area_id']) {
                case 1: // ginecologia
                    $stmt_detalle = $db->prepare("SELECT hg.mot detalle, upper(u.nom) medico, s.codigo_facturacion sede
                        from hc_gineco hg
                        inner join usuario u on u.`userx` = hg.med
                        inner join sedes s on s.id = u.sede_id
                        where hg.id = ?;");
                    $stmt_detalle->execute([$info['atencion_id']]);
                    if ($stmt_detalle->rowCount() > 0) {
                        $data = $stmt_detalle->fetch(PDO::FETCH_ASSOC);
                        $medico = $data["medico"];
                        $sede = $data["sede"];
                    }
                    break;
                case 2: // urologia
                    /* $stmt_detalle = $db->prepare("SELECT mot detalle
                        from hc_urolo
                        where id = ?;");
                    $stmt_detalle->execute([$info['atencion_id']]);
                    $detalle = $stmt_detalle->fetch(PDO::FETCH_ASSOC)['detalle']; */
                    break;
                case 3: // reproduccion asistida
                    $stmt_detalle = $db->prepare("SELECT upper(u.nom) medico, s.codigo_facturacion sede
                        from hc_reprod hr
                        inner join usuario u on u.`userx` = hr.med
                        inner join sedes s on s.id = u.sede_id
                        where hr.estado = true and hr.id = ?;");
                    $stmt_detalle->execute([$info['atencion_id']]);
                    if ($stmt_detalle->rowCount() > 0) {
                        $data = $stmt_detalle->fetch(PDO::FETCH_ASSOC);
                        $medico = $data["medico"];
                        $sede = $data["sede"];
                    }
                    break;

                default: break;
            }

            $content .= '<tr>';
            $content .= '<td style="text-align: center;"><input type="radio" name="seleccion_atenciones" value="' . $info["id"] . '" data-mini="true"></td>';
            $content .= '<td>' . $info['codigo'] . '</td>';
            $content .= '<td>' . $info['area'] . '</td>';
            // $content .= '<td>' . $detalle . '</td>';
            $content .= '<td>' . mb_strtoupper($info['medico_id']) . '</td>';
            $content .= '<td>' . $info['paciente'] . '</td>';
            $content .= '<td><small>' . date("d-m-Y", strtotime($info['fecha_atencion'])) . '</small></td>';
            $content .= '</tr>';
        }
    } else {
        $stmt = $db->prepare(
            "SELECT
            hp.tip tipo_documento, hp.dni documento, upper(concat(hp.ape, ' ', hp.nom)) paciente, hp.med medico_id, hp.mai correo_electronico
            from hc_paciente hp
            where hp.dni ilike ?
            union
            select
            hp.p_tip tipo_documento, p_dni documento, upper(concat(hp.p_ape, ' ', hp.p_nom)) paciente, p_med medico_id, hp.p_mai correo_electronico
            from hc_pareja hp
            where hp.p_dni ilike ?;"
        );
        $stmt->execute([$data['codigo'], $data['codigo']]);

        if ($stmt->rowCount() > 0) {
            $info = $stmt->fetch(PDO::FETCH_ASSOC);

            $medico = $info['medico_id'];
            $pos = strrpos($medico, ",");
    
            if ($pos !== false) {
                $medico = substr($medico, $pos+1, strlen($medico));
            }
            $stmt_detalle = $db->prepare("SELECT upper(u.nom) medico, s.codigo_facturacion sede
                from sedes s
                inner join usuario u on s.id = u.sede_id
                where u.`userx` = ?;");
            $stmt_detalle->execute([$medico]);

            if ($stmt_detalle->rowCount() > 0) {
                $data_detalle = $stmt_detalle->fetch(PDO::FETCH_ASSOC);
                $medico = $data_detalle["medico"];
                $sede = $data_detalle["sede"];
                $medios_comunicacion_color = '#000';
                $medios_comunicacion = '<b>Paciente encontrado!</b>';
                $paciente_id = $info["documento"];
                $paciente = $info["paciente"];
                $correo_electronico = $info["correo_electronico"];

                $content .= '<tr>';
                $content .= '<td style="text-align: center;">-</td>';
                $content .= '<td>-</td>';
                $content .= '<td>-</td>';
                $content .= '<td>' . mb_strtoupper($info['medico_id']) . '</td>';
                $content .= '<td>' . $info['paciente'] . '</td>';
                $content .= '<td><small>-</small></td>';
                $content .= '</tr>';
            }
        } else {
            $medios_comunicacion_color = '#FF0000';
            $medios_comunicacion = '<b>El paciente con el N° documento: ' . $data['codigo'] . ' no existe!</b>';
        }
    }

    return [
        'content' => $content,
        'medios_comunicacion_color' => $medios_comunicacion_color,
        'medios_comunicacion' => $medios_comunicacion,
        'tipo_documento' => $tipo_documento,
        'documento' => $paciente_id,
        'paciente' => $paciente,
        'medico' => $medico,
        'sede' => $sede,
        'correo_electronico' => $correo_electronico,
    ];
}

function agregarAtencion($data, $login) {
    global $db;
    // obtener codigo
    $stmt = $db->prepare("SELECT COALESCE(MAX(CAST(codigo AS INTEGER)), 0) + 1 AS codigo FROM cli_atencion_unica WHERE estado = 1;");
    $stmt->execute();
    $info = $stmt->fetch(PDO::FETCH_ASSOC);
    $codigo = $info['codigo'];

    // registrar
    $stmt = $db->prepare(
        "INSERT INTO cli_atencion_unica (area_id, atencion_id, medico_id, paciente_id, codigo, detalle, fecha_atencion, idusercreate) VALUES
        (?, ?, ?, ?, ?, ?, ?, ?)"
    );

    $stmt->execute([
        $data['area_id'],
        $data['atencion_id'],
        $data['medico_id'],
        $data['paciente_id'],
        str_pad($codigo, 10, '0', STR_PAD_LEFT),
        $data['detalle'],
        date("Y-m-d H:i:s"),
        $login,
    ]);
}

function getTipoProcedimiento($id) {
    global $db;

    $stmt = $db->prepare("SELECT
        case when hr.p_fiv = 1 then 'FIV' when hr.p_icsi = 1 then 'ICSI' else '-' end fiv_icsi
        , case when hr.des_dia >= 1 and hr.des_don is null then 'ted' else '' end ted
        , case when hr.des_dia >= 1 and hr.des_don is not null then 'embrioadopcion' else '' end embrioadopcion
        , case when hr.des_dia = 0 and hr.des_don is null then 'descongelacion_propios' else '' end descongelacion_propios
        , case when hr.des_dia = 0 and hr.des_don is not null then 'descongelacion_donados' else '' end descongelacion_donados
    from hc_reprod hr
    where hr.estado = true and hr.id = ?;");
    $stmt->execute([$id]);
    $info = $stmt->fetch(PDO::FETCH_ASSOC);
    $codigo = $info['codigo'];
}