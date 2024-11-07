<?php
namespace App\Models\Usuario;

use Illuminate\Database\Eloquent\Model;

class UsuarioRole extends Model
{ 
    protected $table = SCHEMA_APPINMATER_MODULO .'.'.'usuario_roles'; 
    public $timestamps = false;
    protected $primaryKey = ['usuario_id', 'role_id'];
    public $incrementing = false;
     
    protected $fillable = [
        'usuario_id',
        'role_id',
    ];

    // Definir las relaciones
    public function user()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
