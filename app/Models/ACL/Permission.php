<?php

namespace App\Models\ACL;

use Core\Model;
use Core\DB;

class Permission extends Model
{
    protected $table = null;
    const UPDATED_AT = null;
    const CREATED_AT = null;
    protected $fillable = [];
    protected $hidden = [];
    protected $casts = [];

    public static function usuarios()
    {
        return db()->get("
            SELECT
                U.id,
                U.usuario
            FROM public.usuario U
WHERE U.habilitado IS TRUE
            ORDER BY U.usuario");
    }


    public static function roles()
    {
        return db()->get("
        SELECT
            G.id,
            G.rotulo
        FROM public.acl_rol G
        ORDER BY G.id ASC");
    }
    public static function grupos()
    {
        return db()->get("
        SELECT
            G.id,
            G.nombre,
            G.descripcion
        FROM public.acl_grupo G
        ORDER BY G.nombre");
    }

    public static function modulos()
    {
        return db()->get("
        SELECT
            c.id,
            CASE WHEN c.controlador_padre_id IS NOT NULL THEN 1 ELSE 0 END AS es_hijo,
            c.rotulo,
            c.link,
            c.visible,
            c.orden,
            cp.orden padre_orden,
            array_to_string(c.permisos, ',') as permisos
        FROM public.acl_controlador c
        LEFT JOIN public.acl_controlador cp ON cp.id = c.controlador_padre_id
        ORDER BY COALESCE(cp.orden, c.orden) ASC, c.visible DESC, CONCAT(COALESCE(cp.rotulo,''), c.rotulo) ASC, c.controlador_padre_id DESC, c.rotulo ASC");
    }
}

