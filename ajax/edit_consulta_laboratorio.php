<?php
include '../seguridad_login.php';
global $db;
 
$id = isset($_GET['id']) ? $_GET['id'] : '';

$response = [];

try {
    if (trim($id) != '') {
        $stmt = $db->prepare(
            "SELECT fecha, paciente_id, idusercreate, mensaje, estado, id_gineco_tip_atencion,estado FROM hc_gineco_mensajes WHERE id = ?"
        );

        $stmt->execute([$id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($result) { 
            $response = [
                'status' => 'success',
                'data' => $result
            ];
        } else { 
            $response = [
                'status' => 'error',
                'message' => 'No records found for the provided ID'
            ];
        }
    } else { 
        $response = [
            'status' => 'info',
            'message' => 'Agregando consulta'
        ];
    }
} catch (Exception $e) { 
    $response = [
        'status' => 'error',
        'message' => 'An error occurred: ' . $e->getMessage()
    ];
}

header('Content-Type: application/json');
echo json_encode($response);
