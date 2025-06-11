<?php

namespace App\Models\ACL;

use Core\Model;
use Core\DB;

class UsuarioGrupo extends Model
{
  protected $connection = 'interno';
  protected $table = 'public.acl_usuario_grupo';
    const UPDATED_AT = null;
    const CREATED_AT = null;
    protected $fillable = [
        'usuario_id', 'grupo_id', 'eliminado'
    ];
    protected $hidden = [];
    protected $casts = [];

}
