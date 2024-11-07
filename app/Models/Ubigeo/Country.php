<?php

namespace App\Models\Ubigeo;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [ 'countrycode', 'countryname'];
    public $timestamps = false;
    protected $table = SCHEMA_APPINMATER_MODULO .'.'.'countries';
}

?>
