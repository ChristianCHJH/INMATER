<?php
namespace App\Models\Usuario;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    protected $table = SCHEMA_APPINMATER_MODULO .'.'.'role_permissions'; 

    // Si no utilizas los timestamps (created_at y updated_at) en esta tabla
    public $timestamps = false;
 
    // Si usas una clave primaria diferente a la que Laravel espera (id)
    protected $primaryKey = ['role_id', 'permission_id'];
    public $incrementing = false;

    // Si necesitas definir los campos fillable
    protected $fillable = [
        'role_id',
        'permission_id',
    ];

    // Definir las relaciones
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }
}
