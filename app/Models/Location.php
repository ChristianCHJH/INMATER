<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['id', 'nombre', 'estado'];
    public $timestamps = false;
    protected $table = SCHEMA_APPINMATER_MODULO .'.'.'sedes';
}

?>