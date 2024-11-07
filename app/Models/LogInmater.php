<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; 

class LogInmater extends Model
{
    protected $table = 'log_inmater';  
 
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes); 
        $this->setTable(SCHEMA_APPINMATER_LOG . '.' . $this->table);
    }
}
