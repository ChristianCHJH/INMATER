<?php

namespace App\Models\MedicalRecords;
use App\Models\MedicalRecords\Paciente;
use Illuminate\Database\Eloquent\Model;

class PartnerPatient extends Model
{ 
    public $timestamps = false;
    protected $table = SCHEMA_APPINMATER_MODULO .'.'.'hc_pare_paci';
    
    protected $fillable = [
        'dni',      // DNI del paciente
        'p_dni'     // DNI de la pareja
    ];

    // Puedes definir relaciones si las hay, por ejemplo, si el modelo se relaciona con otros modelos
    public function partner()
    {
        return $this->belongsTo(Partner::class, 'p_dni', 'p_dni');
    }

    public function patient()
    {
        return $this->belongsTo(Paciente::class, 'dni', 'dni');
    }
}
