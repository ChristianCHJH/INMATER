<?php

class ManExamenController {
    private $response;
    private $db;
    private $login;

    public function __construct() {
        // Iniciar la sesión si no está activa
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
            $this->login = $_SESSION['login'];
        }
        
        $this->response = array();
        $this->response['status'] = false;    
        require "_database/database.php";
        
        // Inicializar variables de conexión y sesión
        $this->db = $db;
    }

    /**
     * Método para cargar los datos de un examen
     * 
     * Este método obtiene los datos de un examen médico específico basado en el ID proporcionado
     * mediante una solicitud POST. La información se recupera de la base de datos y se devuelve
     * en formato JSON.
     * 
     * @return string JSON con los datos del examen y el estado de la operación
     */
    public function loadData() {
        try {
            // Obtener el ID del examen desde la solicitud POST
            $id = isset($_POST['id']) ? $_POST['id'] : 0;

            // Preparar y ejecutar la consulta para obtener los datos del examen
            $stmt = $this->db->prepare("SELECT me.id, me.tipo_examen_id, me.resultado_id, COALESCE(a.nombre_base, '-') archivo, me.fecha, me.observacion
                                         FROM man_examenes me LEFT JOIN man_archivo a ON a.id = me.archivo_id
                                         WHERE me.id = ?");
            $stmt->execute([$id]);
            $pop = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Almacenar los datos en la respuesta
            $this->response['data'] = $pop;
            $this->response['status'] = true;
        } catch (Exception $e) {
            $this->response['success'] = false;
            $this->response['message'] = 'Error: ' . $e->getMessage();
        }
        
        // Devolver la respuesta en formato JSON
        return json_encode($this->response);
    }

    /**
     * Método para procesar y guardar los datos del examen
     * 
     * Este método maneja la solicitud POST para guardar los datos de un examen médico,
     * incluyendo la subida de archivos si es necesario. Inserta o actualiza los datos
     * en la base de datos y devuelve el resultado de la operación en formato JSON.
     * 
     * @return string JSON con el estado de la operación y el mensaje correspondiente
     */
    public function processData() { 
        try {
            // Verificar si la solicitud es de tipo POST
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Método de solicitud no válido");
            }

            // Inicializar y limpiar los datos recibidos

            $id = 0;//isset($_POST['idx']) ? trim($_POST['idx']) : 0;
            $archivo_id = null;
            $dni = isset($_POST['dni']) ? $_POST['dni'] : '';
            $tipo_examen_id = isset($_POST['tipo_examen_id']) ? $_POST['tipo_examen_id'] : '';
            $resultado_id = isset($_POST['resultado_id']) ? $_POST['resultado_id'] : '';
            $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : '';
            $observacion = isset($_POST['observacion']) ? $_POST['observacion'] : '';
            $idx = isset($_POST['idx']) ? TRIM($_POST['idx']) : 0;

            // Ruta para guardar los archivos
            $path = $_SERVER["DOCUMENT_ROOT"] . "/storage/examenes/";

            // Manejo de la subida del archivo
            if (isset($_FILES['informe']) && !empty($_FILES['informe']['name'])) {
                $informe = $_FILES['informe'];
                $informe_name = $informe['name'];
                $nombre_original = $informe_name;
                $informe_name = preg_replace("/[^a-zA-Z0-9.]/", "", $informe_name);
                $nombre_base = time() . "-" . $informe_name;
                $ruta = $path . $nombre_base;

                // Verificar y mover el archivo subido
                if (is_uploaded_file($informe['tmp_name'])) {
                    if (move_uploaded_file($informe['tmp_name'], $ruta)) {
                        // Insertar los detalles del archivo en la base de datos
                        $stmt = $this->db->prepare("INSERT INTO man_archivo (nombre_base, nombre_original, idusercreate) VALUES (?, ?, ?)");
                        $stmt->execute([$nombre_base, $nombre_original, $this->login]);
                        $archivo_id = $this->db->lastInsertId();
                    } else {
                        throw new Exception("Error al mover el archivo al servidor");
                    }
                } else {
                    throw new Exception("Error al subir el archivo");
                }
            }

            // Insertar o actualizar datos del examen médico
            if ($idx == 0 || $idx == "") {
                // Insertar nuevo examen
                $stmt = $this->db->prepare("INSERT INTO man_examenes (paciente_id, tipo_examen_id, resultado_id, archivo_id, fecha, observacion, idusercreate) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$dni, $tipo_examen_id, $resultado_id, $archivo_id, $fecha, $observacion, $this->login]);
                $id = $this->db->lastInsertId();
            } else {
                // Actualizar examen existente
                if (isset($archivo_id) && $archivo_id != 0) {
                    $stmt = $this->db->prepare("UPDATE man_examenes SET paciente_id = ?, tipo_examen_id = ?, resultado_id = ?, archivo_id = ?, fecha = ?, observacion = ?, iduserupdate = ? WHERE id = ?");
                    $stmt->execute([$dni, $tipo_examen_id, $resultado_id, $archivo_id, $fecha, $observacion, $this->login, $idx]);
                } else {
                    $stmt = $this->db->prepare("UPDATE man_examenes SET paciente_id = ?, tipo_examen_id = ?, resultado_id = ?, fecha = ?, observacion = ?, iduserupdate = ? WHERE id = ?");
                    $stmt->execute([$dni, $tipo_examen_id, $resultado_id, $fecha, $observacion, $this->login, $idx]);
                }
            }

            // Establecer respuesta de éxito
            $this->response['success'] = true;
            $this->response['message'] = 'Datos guardados exitosamente';
            $this->response['status'] = true;
        } catch (Exception $e) {
            // Establecer respuesta de error
            $this->response['success'] = false;
            $this->response['message'] = 'Error: ' . $e->getMessage();
        }

        // Devolver la respuesta en formato JSON
        return json_encode($this->response);
    }

    /**
     * Método para eliminar un examen clínico
     *
     * Este método maneja la solicitud para eliminar un examen clínico basado en el ID proporcionado.
     *
     * @return string JSON con el estado de la operación y el mensaje correspondiente
     */
    public function deleteClinicalExam() {
        try {
            // Obtener el ID del examen clínico a eliminar
            $id = $_POST["id"];

            // Preparar la consulta SQL para actualizar el estado del examen
            $query = "UPDATE man_examenes SET estado = 0 WHERE id = :id";
            $statement = $this->db->prepare($query);

            // Vincular el parámetro :id con el valor de $id
            $statement->bindParam(':id', $id, PDO::PARAM_INT);

            // Ejecutar la consulta preparada
            $statement->execute();

            // Verificar si se actualizó correctamente algún registro
            $count = $statement->rowCount();
            if ($count <= 0) {
                throw new Exception("No se encontró ningún registro para eliminar.");
            }

            // Establecer respuesta de éxito
            $this->response['message'] = "Registro eliminado exitosamente.";
            $this->response['status'] = true;
        } catch (PDOException $e) {
            // Manejar cualquier excepción que pueda ocurrir durante la ejecución de la consulta
            $this->response['message'] = "Error al eliminar el registro: " . $e->getMessage();
        }

        // Devolver la respuesta en formato JSON
        return json_encode($this->response);
    }
}

?>
