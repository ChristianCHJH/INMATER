<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; 

class UsuarioLog extends Model
{
    protected $table = 'usuario_log';  
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes); 
        $this->setTable(SCHEMA_APPINMATER_MODULO . '.' . $this->table);
    }
}
