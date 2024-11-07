<?php

namespace App\Models\Media;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = [
        'nombre',
        'grupo',
        'estado'
    ];

    public $timestamps = true;
 
    protected $table = SCHEMA_APPINMATER_MODULO .'.'.'man_medios_comunicacion';
}
