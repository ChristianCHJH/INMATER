<?php
namespace App\Models\Usuario;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = SCHEMA_APPINMATER_MODULO .'.'.'permisos';  

    protected $fillable = ['nombre', 'descripcion']; // Campos que se pueden llenar masivamente

    // Relación con los roles
    public function roles()
    {
        return $this->belongsToMany(Role::class, RolePermission::class, 'permission_id', 'role_id');
    }
}
