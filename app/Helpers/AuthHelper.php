<?php namespace App\Helpers;

use App\Models\Usuario\Usuario;

class AuthHelper {
    public static function hash($password) {
        return hash('sha256', $password); // Ejemplo de algoritmo de hash
    }

    public static function verify($password, $hash) {
        return ($hash == self::hash($password));
    }

    public static function authentification($username, $pass) {
         // Hashear la contraseña antes de la consulta
         $hashedPassword = self::hash($pass);

         $query = Usuario::where('userx', $username)
             ->where('pass', $hashedPassword)
             ->where('estado', 1);

            $sql = $query->toSql();
            $bindings = $query->getBindings();

            print_r($sql);
            print_r($bindings); exit;
         //return $hasResults;
    }
}
