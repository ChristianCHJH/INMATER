<?php
require($_SERVER["DOCUMENT_ROOT"] . "/_database/database_log.php");
include "utils.php";
require($_SERVER["DOCUMENT_ROOT"] . "/config/environment.php");
if ($_SERVER['REQUEST_METHOD'] == 'GET')
{

}

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    global $dblog;
    $input = json_decode(file_get_contents("php://input"));

    $apikey=$input->apikey;
    $apikeySistema=$_ENV["apikey"];
    if ($apikey==$apikeySistema){

        $sql = "INSERT INTO log_inmater
          (nombre_modulo, ruta, tipo_operacion, clave, valor, idusercreate)
          VALUES
          (:nombre_modulo, :ruta, :tipo_operacion, :clave, :valor, :idusercreate)";
        $statement = $dblog->prepare($sql);
        try{
            bindAllValues($statement, $input);
            $statement->execute();
            $postId = $dblog->lastInsertId();
            if($postId)
            {
                $resultado["message"]="Se registro correctamente";
                $resultado["success"]=true;
                echo json_encode($resultado);
                header("HTTP/1.1 200 OK");
                exit();
            }else{
                $resultado["message"]="Error al registrar";
                $resultado["success"]=false;
                echo json_encode($resultado);
                header("HTTP/1.1 400 Bad Request");
            }
        }catch(Exception $e){
            $resultado["message"]="Error al registrar";
            $resultado["success"]=false;
            echo json_encode($resultado);
            header("HTTP/1.1 400 Bad Request");
        }



    }else{
        $resultado["message"]="Api key no es correcta";
        $resultado["success"]=false;
        echo json_encode($resultado);
        header("HTTP/1.1 400 Bad Request");
    }


}
//Borrar
if ($_SERVER['REQUEST_METHOD'] == 'DELETE')
{

}
//Actualizar
if ($_SERVER['REQUEST_METHOD'] == 'PUT')
{

}
//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");
?>