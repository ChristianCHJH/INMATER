<?php
// Incluir el archivo de conexión o configuración de la base de datos
require("../../_database/db_tools.php");

// Obtener el ID del registro a eliminar desde la solicitud POST
$id = $_POST["id"];

$response['status'] = false;

try {
    // Preparar la consulta SQL para eliminar el registro (Añadir columna eliminado  del tipo boolean a la tabla lab_andro_cap)
    $query = "UPDATE lab_andro_cap SET eliminado = true WHERE id = :id and eliminado is false";
    $statement = $db->prepare($query);

    // Vincular el parámetro :id con el valor de $id
    $statement->bindParam(':id', $id, PDO::PARAM_INT);

    // Ejecutar la consulta preparada
    $statement->execute();

    // Verificar si se eliminó correctamente algún registro
    $count = $statement->rowCount();
    
    if ($count > 0) {
        $response['message'] = "Registro eliminado exitosamente.";
        $response['status'] = true;
    } else {
        $response['message'] = "No se encontró ningún registro para eliminar.";
    }
} catch (PDOException $e) {
    // Manejar cualquier excepción que pueda ocurrir durante la ejecución de la consulta
    $response['message'] = "Error al eliminar el registro: " . $e->getMessage();
}

echo json_encode($response); exit;
?>
