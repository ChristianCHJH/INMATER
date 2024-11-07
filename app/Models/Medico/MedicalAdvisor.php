<?php
namespace App\Models\Medico;
use Illuminate\Database\Eloquent\Model;

class MedicalAdvisor extends Model
{
    protected $fillable = ['id', 'apellidos', 'nombres', 'eliminado'];
    public $timestamps = false;
    protected $table = SCHEMA_APPINMATER_MODULO .'.'.'asesor_medico';
}

?>