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
			echo json_encode(["message" => add($_POST, $login)]);
			break;
		case 'delete':
			http_response_code(200);
			echo json_encode(["message" => delete($_POST, $login)]);
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
	$stmt=$db->prepare("SELECT
		id, trim(nombre) nombre
		from man_biopsia where estado = 1 order by id;");
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
		$html.='<td><a href="man-biopsia-edit.php?id='.$value["id"].'"><img src="_libraries/open-iconic/svg/pencil.svg" height="18" width="18" alt="icon name"></a> <a href="javascript:void(0)"><img src="_libraries/open-iconic/svg/trash.svg" data-id="'.$value["id"].'" class="form-confirm-delete" height="18" width="18" alt="icon name"></a></td>';
		$html.='</tr>';
	}
	return $html;
}

function get_item($data) {
	return ['content' => getHtmlItem(getDataItem($data))];
}
function getDataItem($data) {
	global $db;
	$stmt=$db->prepare("SELECT
		id, trim(nombre) nombre
		from man_biopsia where id=? and estado = 1;");
	$stmt->execute([
		$data["id"]
	]);
	return $stmt->fetch(PDO::FETCH_ASSOC);
}
function getHtmlItem($data) {
	return json_encode($data, TRUE);
}

function add($data, $login) {
	// obtener codigo
	global $db;
	$stmt=$db->prepare("SELECT max(codigo)+1 codigo from man_biopsia;");
	$stmt->execute();
	$data_codigo = $stmt->fetch(PDO::FETCH_ASSOC);
	// insertar
	$stmt=$db->prepare("INSERT INTO man_biopsia (codigo, nombre, idusercreate) values (?, ?, ?);");
	$stmt->execute([
		$data_codigo["codigo"],
		$data["nombre"],
		$login
	]);
	return ["success" => true];
}
function delete($data, $login) {
	global $db;
	$stmt=$db->prepare("UPDATE man_biopsia set estado=0, iduserupdate=? where id=?;");
	$stmt->execute([
		$login,
		$data["id"]
	]);
	return ["success" => true];
}
function update($data, $login) {
	global $db;
	$stmt=$db->prepare("UPDATE man_biopsia set nombre=?, iduserupdate=? where id=?;");
	$stmt->execute([
		$data["nombre"],
		$login,
		$data["id"]
	]);
	return ["success" => true];
}
