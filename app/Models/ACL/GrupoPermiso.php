<?php

namespace App\Models\ACL;

use Core\Model;

class GrupoPermiso extends Model
{
  protected $connection = 'interno';
  protected $table = 'public.acl_grupo_permiso';
    const UPDATED_AT = null;
    const CREATED_AT = null;
    protected $fillable = [
        'grupo_id', 'controlador_id', 'permisos','eliminado'
    ];
    protected $hidden = [];
    protected $casts = [
    ];
}
