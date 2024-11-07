<?php

namespace App\Models\Ubigeo;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    // Define the table name with schema
    protected $table = SCHEMA_APPINMATER_MODULO . '.' . 'departamentos';
    
    // Fillable fields for mass assignment
    protected $fillable = ['nomdepartamento','country_name'];
    
    // Disable timestamps if not needed
    public $timestamps = false;

    /**
     * Get the country that the department belongs to.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the provinces for the department.
     */
    public function provinces()
    {
        return $this->hasMany(Province::class, 'department_id');
    }

    /**
     * Get the districts for the department through provinces.
     */
    public function districts()
    {
        return $this->hasManyThrough(District::class, Province::class, 'department_id', 'province_id');
    }
}

?>
