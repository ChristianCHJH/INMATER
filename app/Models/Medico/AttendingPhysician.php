<?php
namespace App\Models\Medico;
use Illuminate\Database\Eloquent\Model;
/*** Médico tratante ***/
class AttendingPhysician extends Model
{
    protected $fillable = ['codigo', 'nombre', 'nombre_corto', 'eliminado', 'estado', 'estado_enfermeria', 'estado_consulta', 'idusercreate', 'iduserupdate', 'eva_id', 'updatex'];
    public $timestamps = false;
    protected $table = SCHEMA_APPINMATER_MODULO .'.'.'man_medico';
}

?>