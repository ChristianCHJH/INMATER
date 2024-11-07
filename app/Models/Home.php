<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\ConnectionInterface;

class Home extends Model
{
    protected $table = 'usuario'; 
 
    public function validate($username, $password, $status = 1)
    {
        $this->setTable(SCHEMA_APPINMATER_MODULO .'.'. $this->table);
        $res['status'] = false;

        try {
            $query = $this->where('userx', $username)
                          ->where('pass', $password)
                          ->where('estado', $status);

            $sql = $query->toSql();
            $bindings = $query->getBindings();
            $sqlWithParams = vsprintf(str_replace('?', "'%s'", $sql), $bindings); 
            $user = $query->first();
            $res['status'] = true;
            $res['sql'] = "SQL Query with Parameters: $sqlWithParams\n"; 
        } catch (\Exception $e) {
            $res['message'] = 'Error al ejecutar la consulta: ' . $e->getMessage(); 
        }
        
        return $res;
    }

    function addUserLog($user, $idusercreate, $createdate)
    {
        try {
            $usuarioLog = new UsuarioLog(); 
            $usuarioLog->userx = $user;
            $usuarioLog->idusercreate = $idusercreate;
            $usuarioLog->createdate = $createdate;
            $usuarioLog->timestamps = false;
            $usuarioLog->save();

            return true; 
        } catch (\Exception $e) {
            echo 'Error al insertar usuario log: ' . $e->getMessage();
            return false;
        }
    } 

    public function addLogInmater($user, $createdate)
    {
        try {
            $logInmater = new LogInmater();  
            $logInmater->nombre_modulo = 'login';
            $logInmater->ruta = 'login.php';
            $logInmater->tipo_operacion = 'ingreso';
            $logInmater->idusercreate = $user;
            $logInmater->createdate = $createdate;
            $logInmater->timestamps = false;
            $logInmater->save();

            return true; 
        } catch (\Exception $e) {
            echo 'Error al insertar log inmater: ' . $e->getMessage();
            return false;
        }
    }

    function getUser($user_id){
        $this->setTable(SCHEMA_APPINMATER_MODULO .'.'. $this->table); 
        $res['status'] = false;
        
        try {
            
            $query = $this->where('userx', $user_id);
            $sql = $query->toSql();
            $bindings = $query->getBindings();
            $sqlWithParams = vsprintf(str_replace('?', "'%s'", $sql), $bindings); 
            $user = $query->first();
            $res['status'] = true;
            $res['data'] = $user;
            $res['sql'] = "SQL Query with Parameters: $sqlWithParams\n"; 
        } catch (\Exception $e) {
            $res['message'] = 'Error al ejecutar la consulta: ' . $e->getMessage(); 
        }
        
        return $res;
    }
}
