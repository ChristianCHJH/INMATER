<?php
namespace App\Models\MedicalRecords;
use Illuminate\Database\Eloquent\Model;

class ClientType extends Model
{
    // Relaciones y otros métodos 
    protected $fillable = ['codigo', 'nombre','eliminado'];
    public $timestamps = false;
    protected $table = SCHEMA_APPINMATER_MODULO .'.'.'tipo_cliente';
}
