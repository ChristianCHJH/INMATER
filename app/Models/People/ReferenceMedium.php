<?php

namespace App\Models\People;

use Illuminate\Database\Eloquent\Model;

class ReferenceMedium extends Model
{
    // La tabla asociada al modelo.
    protected $table = SCHEMA_APPINMATER_MODULO .'.'.'medios_referencia'; 

    // Los atributos que se pueden asignar masivamente.
    protected $fillable = [
        'id', 'nombre'
    ];

    // Los atributos que deberían ser ocultos para los arrays.
    protected $hidden = [];

    // Los atributos que deberían ser cast a otro tipo de datos.
    protected $casts = [
        'id' => 'integer',
    ];

    // Si no tienes una columna de timestamps, desactívala.
    public $timestamps = false;

    // Accesor para obtener el nombre en mayúsculas.
    public function getNombreAttribute($value)
    {
        return strtoupper($value);
    }
}
