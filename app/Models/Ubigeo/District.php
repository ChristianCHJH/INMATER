<?php

namespace App\Models\Ubigeo;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $fillable = ['nomdistrito', 'idprovincia'];
    public $timestamps = false;
    protected $table = SCHEMA_APPINMATER_MODULO . '.' . 'distritos';

    public function province()
    {
        return $this->belongsTo(Province::class, 'idprovincia');
    }
}

?>

