<?php
session_start();
require($_SERVER["DOCUMENT_ROOT"] . "/_database/database.php");
require($_SERVER["DOCUMENT_ROOT"] . "/config/environment.php");
$login = "";

if (!!$_SESSION) {
    $login = $_SESSION['login'];
} else {
    http_response_code(400);
    echo json_encode(array("message" => "no se ha iniciado sesión"));
    exit();
}

/* var_dump($_POST); var_dump($_FILES); exit(); */

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
        case 'guardar_tanque':
            guardar_tanque($_POST, $_FILES['informe']);
            header('Location: '.$_ENV["tanque_reserva_location"]);
            break;
        
        default: exit(); break;
    }
}

function info_todo_tanque()
{
    global $db;
    $detalle = "";

    $stmt = $db->prepare("SELECT tan codigo, n_tan nombre from lab_tanque where sta = 1;");
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
            $detalle .= '<input type="checkbox" class="form-control seleccionar_posicion" name="'.$data["t"].'-'.$data["c"].'-'.$data["v"].'-'.$data["p"].'" data-tanque="'.$data["t"].'" data-canister="'.$data["c"].'" data-varilla="'.$data["v"].'" data-vial="'.$data["p"].'" data-dni="'.$data["dni"].'" data-apellidos-nombres="'.ucwords(mb_strtolower($data["apellidos"])) .  " " . ucwords(mb_strtolower($data["nombres"])).'">';
        }

        $detalle .= '</td>';
    }

    return $detalle;
}

function info_caracteristica_tanque($data, $seleccionados)
{
    global $db;
    $detalle = '';
    $where = "where 1=1";

    if (!!$data[0]["value"]) {
        $where .=  (" and t.n_tan = '" . $data[0]["value"] . "'");
    }

    if (!!$data[1]["value"]) {
        $where .=  (" and tr.c = " . $data[1]["value"]);
    }

    if (!!$data[2]["value"]) {
        $where .=  (" and tr.v = " . $data[2]["value"]);
    }

    if (!!$data[3]["value"]) {
        $where .=  (" and tr.p = " . $data[3]["value"]);
    }

    if (!!$data[4]["value"]) {
        $where .=  (" and tr.sta ilike ('%" . $data[4]["value"] . "%')");
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
            $detalle .= '<input type="checkbox" class="form-control seleccionar_posicion" name="'.$data["t"].'-'.$data["c"].'-'.$data["v"].'-'.$data["p"].'" data-tanque="'.$data["t"].'" data-canister="'.$data["c"].'" data-varilla="'.$data["v"].'" data-vial="'.$data["p"].'" data-dni="'.$data["dni"].'" data-apellidos-nombres="'.ucwords(mb_strtolower($data["apellidos"])) .  " " . ucwords(mb_strtolower($data["nombres"])).'" '.$checked.'>';
        }

        $detalle .= '</td>';
    }

    return $detalle;
}

function guardar_tanque($data, $informe)
{
    global $db;
    global $login;
    $path=$_ENV["tanque_reserva_path"];
    $informe_name = "";

    if (isset($informe) && !empty($informe)) {
        if (!empty($informe['name'])) {
            $informe_name = $informe['name'];
            $informe_name = preg_replace("/[^a-zA-Z0-9.]/", "", $informe_name);
            $informe_name = time() . "-". $informe_name;

            if (!file_exists($path)) {
                mkdir($path, 0755);
            }

            $path = $path . '/' . $informe_name;
            if (is_uploaded_file($informe['tmp_name'])) {
                move_uploaded_file($informe['tmp_name'], $path);
            }
        }
    }

    foreach ($data as $key => $value) {
       if (strpos($key, "tanque") !== FALSE) {
            $posiciones = explode('-', $value);
            $tanque = $posiciones[0];
            $canister = $posiciones[1];
            $varilla = $posiciones[2];
            $vial = $posiciones[3];
            $data_tanque = info_detalle(array('tanque' => info_id_tanque($tanque), 'canister' => $canister, 'varilla' => $varilla, 'vial' => $vial));

            // ingreso las descargas
            $stmt = $db->prepare("INSERT INTO tanque_descarga
            (tanque, canister, varilla, vial, sta, tip, tip_id, med, don, documento, observacion, idusercreate) VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute(array(info_id_tanque($tanque), $canister, $varilla, $vial, $data_tanque["sta"], $data_tanque["tip"], $data_tanque["tip_id"], $data_tanque["med"], $data_tanque["don"], $informe_name, $data['observacion'], $login));
            
            // libero las posiciones del tanque
            $stmt = $db->prepare("UPDATE lab_tanque_res
            set sta=?, tip=?, tip_id=?, med=?, don=? where t=? and c=? and v=? and p=?");
            $stmt->execute(array('', 0, '', 0, '', info_id_tanque($tanque), $canister, $varilla, $vial));
       }
    }
}

function info_detalle($data)
{
    global $db;
    $stmt = $db->prepare("SELECT * from lab_tanque_res where t=? and c=? and v=? and p=?");
    $stmt->execute(array($data["tanque"], $data["canister"], $data["varilla"], $data["vial"]));

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function info_id_tanque($tanque)
{
    global $db;
    $stmt = $db->prepare("SELECT tan from lab_tanque where sta = 1 and n_tan = ?");
    $stmt->execute(array($tanque));

    return $stmt->fetch(PDO::FETCH_ASSOC)["tan"];
}
?>