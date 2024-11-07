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
		case 'obtener_data':
			http_response_code(200);
			echo json_encode(["message" => get_data($_POST)]);
			break;
		case 'get_item':
			http_response_code(200);
			echo json_encode(["message" => get_item($_POST)]);
			break;
		case 'add':
			http_response_code(200);
			echo json_encode(add($_POST, $login));
			break;
		case 'delete':
			http_response_code(200);
			echo json_encode(delete($_POST, $login));
			break;
		case 'update':
			http_response_code(200);
			echo json_encode(["message" => update($_POST, $login)]);
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

function get_data($data) {
	return ['content' => getHtml(getData($data))];
}
function getData($data) {
	global $db;
	$stmt=$db->prepare("SELECT id, nombre from man_cateter where estado = 1 order by id desc;");
	$stmt->execute();
	return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getHtml($data) {
	$html='';
	foreach ($data as $key => $value) {
		$html.='<tr align="center">';
		foreach ($value as $key1 => $value1) {
			$html.='<td>'.$value1.'</td>';
		}
		$html.='<td>';
		$html.='<a href="man-cateter-edit.php?id='.$value["id"].'">';
		$html.='<img src="_libraries/open-iconic/svg/pencil.svg" height="18" width="18" alt="icon name">';
		$html.='</a>';
		$html.='<a href="javascript:void(0)">';
		$html.='<img src="_libraries/open-iconic/svg/trash.svg" data-id="'.$value["id"].'" class="form-confirm-delete" height="18" width="18" alt="icon name">';
		$html.='</a>';
		$html.='</td>';
		$html.='</tr>';
	}
	return $html;
}

function get_item($data) {
	return ['content' => getHtmlItem(getDataItem($data))];
}
function getDataItem($data) {
	global $db;
	$stmt=$db->prepare("SELECT id, nombre from man_cateter where id=? and estado = 1;");
	$stmt->execute([$data["id"]]);
	return $stmt->fetch(PDO::FETCH_ASSOC);
}
function getHtmlItem($data) { return json_encode($data, TRUE); }
function add($data, $login) {
	global $db;
	$stmt=$db->prepare("INSERT INTO man_cateter (nombre, idusercreate) values (?, ?);");
	$stmt->execute([$data["nombre"], $login]);
	return ["success" => true, "message" => "Excelente, agregamos correctamente este contenido."];
}
function delete($data, $login) {
	global $db;
	$stmt=$db->prepare("UPDATE man_cateter set estado=0 where id=?;");
	$stmt->execute([$data["id"]]);
	return ["success" => true, "message" => "Excelente, eliminamos correctamenet este contenido."];
}
function update($data, $login) {
	global $db;
	$stmt=$db->prepare("UPDATE man_cateter set nombre=? where id=?;");
	$stmt->execute([$data["nombre"], $data["id"]]);
	return ["success" => true];
}
