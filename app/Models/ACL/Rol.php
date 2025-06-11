<?php

namespace App\Models\ACL;

use Core\Model;
use Core\DB;

class Rol extends Model
{
  protected $connection = 'interno';
  protected $table = 'public.acl_rol';
    const UPDATED_AT = null;
    const CREATED_AT = null;
    protected $fillable = [
        'rotulo','grupo_ids'
    ];
    protected $hidden = [];
    protected $casts = [];

    public function permisos() {
        return db()->get("
            SELECT
              G.nombre grupo,
              G.id grupo_id,
              (G.id = ANY(R.grupo_ids)) seleccion
            FROM public.acl_rol R
            JOIN public.acl_grupo G ON TRUE
            WHERE R.id = :id AND R.eliminado IS NULL", ['id' => $this->id]);
    }
}

