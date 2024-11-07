<?php
session_start();
require($_SERVER["DOCUMENT_ROOT"] . "/_database/database.php");
/* require($_SERVER["DOCUMENT_ROOT"] . "/_database/database_farmacia.php"); */
$login = "";

if (!!$_SESSION) {
    $login = $_SESSION['login'];
} else {
    http_response_code(400);
    echo json_encode(array("message" => "no se ha iniciado sesión"));
    exit();
}

if (isset($_POST["tipo"]) && !empty($_POST["tipo"])) {
    switch ($_POST["tipo"]) {
        case 'info_todo_tanque':
            http_response_code(201);
            echo json_encode(array("message" => info_todo_tanque()));
            break;
        case 'info_detalle_tanque':
            http_response_code(201);
            echo json_encode(array("message" => info_detalle_tanque()));
            break;
        case 'info_caracteristica_tanque':
            http_response_code(201);
            echo json_encode(array("message" => info_caracteristica_tanque($_POST["data"], json_decode(stripslashes($_POST['seleccionados'])))));
            break;
        
        default: exit(); break;
    }
}

function info_todo_tanque()
{
    global $db;
    $detalle = "";

    $stmt = $db->prepare("SELECT tan codigo, n_tan nombre from lab_tanque where sta = 1");
    $stmt->execute();

    $detalle .= '<option value="">Seleccionar</option>';

    while ($info = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $detalle .= '<option value="' . $info['codigo'] . '">Tanque ' . $info['nombre'] .'</option>';
    }

    return $detalle;
}

function info_detalle_tanque()
{
    global $db;
    $detalle = "";

    $stmt = $db->prepare("SELECT t.n_tan t, tr.c, tr.v, tr.p, p.p_dni dni, p.p_ape apellidos, p.p_nom nombres
        from lab_tanque_res tr
        inner join lab_tanque t on t.tan = tr.t and t.sta = 1
        left join hc_pareja p on p.p_dni = tr.sta
        where tr.t = 1
        order by tr.t, tr.c, tr.v, tr.p asc");
    $stmt->execute();

    while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $detalle .= '
        <tr class="nuevo_posicion">
            <td class="text-center">' . $data["t"] . '</td>
            <td class="text-center">' . $data["c"] . '</td>
            <td class="text-center">' . $data["v"] . '</td>
            <td class="text-center">' . $data["p"] . '</td>
            <td class="text-center">' . $data["dni"] . '</td>
            <td class="text-center">' . ucwords(mb_strtolower($data["apellidos"])) .  " " . ucwords(mb_strtolower($data["nombres"])) . '</td>
            <td>';

        if (!empty($data["dni"])) {
            $detalle .= '<input type="checkbox" class="form-control seleccionar_posicion" name="tanque-'.$data["t"].'-'.$data["c"].'-'.$data["v"].'-'.$data["p"].'" data-tanque="'.$data["t"].'" data-canister="'.$data["c"].'" data-varilla="'.$data["v"].'" data-vial="'.$data["p"].'" data-dni="'.$data["dni"].'" data-apellidos-nombres="'.ucwords(mb_strtolower($data["apellidos"])) .  " " . ucwords(mb_strtolower($data["nombres"])).'">';
        }

        $detalle .= '</td>';
    }

    return $detalle;
}

function info_caracteristica_tanque($data, $seleccionados)
{
    /* $demo = "";
    foreach ($seleccionados as $key => $value) {
        $demo += $value->tanque;
    }
    return $demo; */

    global $db;
    $detalle = '';
    $where = "where 1=1 ";

    if (!!$data[0]["value"]) {
        $where .=  ("and t.n_tan ilike ('%" . $data[0]["value"] . "%')");
    }

    if (!!$data[1]["value"]) {
        $where .=  ("and tr.c ilike ('%" . $data[1]["value"] . "%')");
    }

    if (!!$data[2]["value"]) {
        $where .=  ("and tr.v ilike ('%" . $data[2]["value"] . "%')");
    }

    if (!!$data[3]["value"]) {
        $where .=  ("and tr.p ilike ('%" . $data[3]["value"] . "%')");
    }

    if (!!$data[4]["value"]) {
        $where .=  ("and tr.sta ilike ('%" . $data[4]["value"] . "%')");
    }

    $stmt = $db->prepare("SELECT t.n_tan t, tr.c, tr.v, tr.p, p.p_dni dni, p.p_ape apellidos, p.p_nom nombres
        from lab_tanque_res tr
        inner join lab_tanque t on t.tan = tr.t and t.sta = 1
        left join hc_pareja p on p.p_dni = tr.sta
        " . $where . "
        order by tr.t, tr.c, tr.v, tr.p asc");
    $stmt->execute();

    while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $checked = "";

        $detalle .= '
        <tr class="nuevo_posicion">
            <td class="text-center">' . $data["t"] . '</td>
            <td class="text-center">' . $data["c"] . '</td>
            <td class="text-center">' . $data["v"] . '</td>
            <td class="text-center">' . $data["p"] . '</td>
            <td class="text-center">' . $data["dni"] . '</td>
            <td class="text-center">' . ucwords(mb_strtolower($data["apellidos"])) .  " " . ucwords(mb_strtolower($data["nombres"])) . '</td>
            <td>';

        foreach ($seleccionados as $key => $value) {
            if ($value->tanque == $data["t"] && $value->canister == $data["c"] && $value->varilla == $data["v"] && $value->vial == $data["p"]) {
                $checked = "checked";
            }
        }

        if (!empty($data["dni"])) {
            $detalle .= '<input type="checkbox" class="form-control seleccionar_posicion" name="tanque-'.$data["t"].'-'.$data["c"].'-'.$data["v"].'-'.$data["p"].'" data-tanque="'.$data["t"].'" data-canister="'.$data["c"].'" data-varilla="'.$data["v"].'" data-vial="'.$data["p"].'" data-dni="'.$data["dni"].'" data-apellidos-nombres="'.ucwords(mb_strtolower($data["apellidos"])) .  " " . ucwords(mb_strtolower($data["nombres"])).'" '.$checked.'>';
        }

        $detalle .= '</td>';
    }

    return $detalle;
}
?>