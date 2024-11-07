<?php
namespace App\Models\Usuario;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = SCHEMA_APPINMATER_MODULO .'.'.'roles'; 

    protected $fillable = ['nombre', 'descripcion']; 

    // Relación con los usuarios
    public function users()
    {
        return $this->belongsToMany(User::class, UsuarioRole::class, 'role_id', 'usuario_id');
    }

    // Relación con los permisos
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, RolePermission::class, 'role_id', 'permission_id');
    }
}
