<?php
namespace App\Models\Usuario;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    // Relaciones y otros métodos

    protected $fillable = ['userx', 'mail','pass','nom','role','cmp'];
    public $timestamps = false;
    protected $table = SCHEMA_APPINMATER_MODULO .'.'.'usuario';

    public function roles()
    {
        return $this->belongsToMany(Role::class, UsuarioRole::class, 'usuario_id', 'role_id');
    }

    public function hasRole($roleName)
    {
        return $this->roles()->where('nombre', $roleName)->exists();
    }
}
