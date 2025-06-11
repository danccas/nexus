<?php

namespace App\Models\ACL;

use Core\Model;
use Core\DB;

class Grupo extends Model
{
  protected $connection = 'interno';
  protected $table = 'public.acl_grupo';
    const UPDATED_AT = null;
    const CREATED_AT = null;
    protected $fillable = [
        'nombre', 'descripcion'
    ];
    protected $hidden = [];
    protected $casts = [];

    public function permisos() {
        return db()->get("
            SELECT
                GP.controlador_id,
                C.rotulo as controlador,
                array_to_string(GP.permisos, ',') as permisos
            FROM public.acl_grupo_permiso GP
            JOIN public.acl_controlador C ON C.id = GP.controlador_id
            WHERE GP.grupo_id = " . $this->id . " AND GP.eliminado = 0");
    }
}

