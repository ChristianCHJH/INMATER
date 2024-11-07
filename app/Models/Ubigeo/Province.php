<?php

namespace App\Models\Ubigeo;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $fillable = ['nomprovincia', 'iddepartamento'];
    public $timestamps = false;
    protected $table = SCHEMA_APPINMATER_MODULO . '.' . 'provincias';

    public function department()
    {
        return $this->belongsTo(Department::class, 'iddepartamento');
    }

    public function districts()
    {
        return $this->hasMany(District::class, 'province_id');
    }
}

?>
