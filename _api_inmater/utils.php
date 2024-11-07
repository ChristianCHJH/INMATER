<?php
date_default_timezone_set('America/Lima');


function getParams($input)
{
    global $db;
    $filterParams = [];
    foreach($input as $param => $value)
    {
        $filterParams[] = "$param=:$param";
    }
    return implode(", ", $filterParams);
}

//Asociar todos los parametros a un sql
function bindAllValues($statement, $params)
{
//    print_r($params);
//    exit();
    $_array = is_object($params) ? get_object_vars($params) : $params;

    foreach($_array as $param => $value)
    {
        if($param!='apikey'){
            $statement->bindValue(':'.$param, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
    }

    return $statement;
}
?>