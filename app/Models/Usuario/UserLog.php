<?php
namespace App\Models\Usuario;

use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{ 
    protected $fillable = ['nombre_modulo', 'ruta','tipo_operacion','clave','valor','idusercreate'];
    public $timestamps = false;
    protected $table = SCHEMA_APPINMATER_LOG .'.'.'log_inmater';

}
