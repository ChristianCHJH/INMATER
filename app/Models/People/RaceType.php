<?php

namespace App\Models\People;

use Illuminate\Database\Eloquent\Model;

class RaceType extends Model
{
    protected $fillable = ['codigo', 'raza', 'eliminado'];
    public $timestamps = false;
    protected $table = SCHEMA_APPINMATER_MODULO .'.'.'tipo_raza';
}

?>
