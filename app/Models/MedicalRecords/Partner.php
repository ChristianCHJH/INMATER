<?php

namespace App\Models\MedicalRecords;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    // Definir los campos que se pueden llenar masivamente
    protected $fillable = [
        'p_dni',
        'p_tip',
        'p_nom',
        'p_ape',
        'p_fnac',
        'p_tcel',
        'p_tcas',
        'p_tofi',
        'p_mai',
        'p_dir',
        'p_prof',
        'p_san',
        'p_raz',
        'p_med',
        'idusercreate',
        'tipo_clienteid',
        'programaid',
        'sedeid',
    ];
    public $timestamps = false;
    protected $table = SCHEMA_APPINMATER_MODULO .'.'.'hc_pareja';
}
