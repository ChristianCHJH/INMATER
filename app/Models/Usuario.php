<?php
namespace Models; 
use Illuminate\Database\Eloquent\Model;
 
class Usuario extends Model { 
    protected $table = 'usuario';
    
    public function validate($username, $password, $status = 1)
    {
        $user = self::where('userx', $username)
                    ->where('pass', $password)
                    ->where('estado', $status)
                    ->first();
 
        if ($user) {
            return true; 
        } else {
            return false; 
        }
    }
}
