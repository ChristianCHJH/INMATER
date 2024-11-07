<?php

ini_set("display_errors","1");
error_reporting(E_ALL);
// error_reporting( error_reporting() & ~E_NOTICE );

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
	$stmt=$db->prepare("SELECT
		id, fecha, tipo_cambio_compra, tipo_cambio_venta
		from tipo_cambio where estado = 1 order by id desc;");
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
		$html.='<a href="man-tipo-cambio-edit.php?id='.$value["id"].'">';
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
	$stmt=$db->prepare("SELECT
		id, fecha, tipo_cambio_compra, tipo_cambio_venta
		from tipo_cambio where id=? and estado = 1;");
	$stmt->execute([$data["id"]]);
	return $stmt->fetch(PDO::FETCH_ASSOC);
}
function getHtmlItem($data) {
	return json_encode($data, TRUE);
}

function add($data, $login) {
	// obtener codigo
	global $db;
	$stmt=$db->prepare("SELECT id from tipo_cambio where estado=1 and fecha=?;");
	$stmt->execute([$data["fecha"]]);
	if ($stmt->rowCount() > 0) {
		return ["false" => true, "message" => "La fecha ya tiene un tipo de cambio."];
	} else {
		// insertar
		$stmt=$db->prepare("INSERT INTO tipo_cambio (fecha, tipo_cambio_compra, tipo_cambio_venta, idusercreate) values (?, ?, ?, ?);");
		$stmt->execute([
			$data["fecha"],
			$data["tipo_cambio_compra"],
			$data["tipo_cambio_venta"],
			$login
		]);
		return ["success" => true, "message" => "Excelente, agregamos correctamente un Tipo de Cambio."];
	}
	
}
function delete($data, $login) {
	global $db;
	$stmt=$db->prepare("UPDATE tipo_cambio set estado=0 where id=?;");
	$stmt->execute([
		$data["id"]
	]);
	return ["success" => true, "message" => "Excelente, eliminamos correctamenet el Tipo de Cambio."];
}
function update($data, $login) {
	global $db;
	$stmt=$db->prepare("UPDATE tipo_cambio set fecha=?, tipo_cambio_compra=?, tipo_cambio_venta=? where id=?;");
	$stmt->execute([
		$data["fecha"],
		$data["tipo_cambio_compra"],
		$data["tipo_cambio_venta"],
		$data["id"]
	]);
	return ["success" => true];
}
