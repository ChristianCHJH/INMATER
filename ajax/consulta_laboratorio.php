<?php 
include '../seguridad_login.php';
global $db;

function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
 
$id = sanitize($_POST['id']);
$id2 = sanitize($_POST['id2']);
$fecha = sanitize($_POST['fecha']);
$paciente_id = sanitize($_POST['paciente']);
$idusercreate = sanitize($_POST['id_user']);
$mensaje = sanitize($_POST['mensaje']);
$estado = 1;//sanitize($_POST['estado']);
$tipo = sanitize($_POST['tipo']);

$res = ['status' => false, 'message' => ''];

$requiredFields = [
    'fecha' => $fecha,
    'mensaje' => $mensaje,
    'estado' => $estado,
    'tipo' => $tipo,
];

foreach ($requiredFields as $fieldName => $value) {
    if (empty($value)) {
        $res['message'] = "El campo $fieldName es obligatorio.";
        echo json_encode($res);
        exit;
    }
}

try {
     
    if (!empty($id)) {
        $paciente_id = empty($paciente_id) ? $id2 : $paciente_id;  
        $stmt = $db->prepare(
            "UPDATE hc_gineco_mensajes 
            SET fecha = ?, paciente_id = ?, mensaje = ?, estado = ?, id_gineco_tip_atencion = ? 
            WHERE id = ?"
        );
        $stmt->execute([$fecha, $paciente_id, $mensaje, $estado, $tipo, $id]);

        $res['status'] = true;
        $res['message'] = 'Actualización correcta';
    } else { 
        $paciente_id = empty($paciente_id) ? $id2 : $paciente_id;
        $stmt = $db->prepare(
            "INSERT INTO hc_gineco_mensajes (fecha, paciente_id, idusercreate, mensaje, estado, id_gineco_tip_atencion) 
            VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$fecha, $paciente_id, $idusercreate, $mensaje, $estado, $tipo]);

        $res['status'] = true;
        $res['message'] = 'Registro exitoso';
    }
} catch (\Exception $e) { 
    $res['message'] = 'Error en la base de datos: ' . $e->getMessage();
}

echo json_encode($res);
exit;
