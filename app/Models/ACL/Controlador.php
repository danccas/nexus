<?php

namespace App\Models\ACL;

use Core\Model;

class Controlador extends Model
{
  protected $connection = 'interno';
  protected $table = 'public.acl_controlador';
    const UPDATED_AT = null;
    const CREATED_AT = null;
    protected $fillable = [
        'rotulo', 'link', 'permisos','controlador_padre_id','visible','orden',
    ];
    protected $hidden = [];
    protected $casts = [
    ];

    public function padre() {
        return $this->belongsTo('App\Models\ACLControlador','controlador_padre_id')->first();
    }

    public static function busqueda($q) {
      $q = strtolower($q);
      return db()->get("SELECT * FROM public.acl_controlador WHERE LOWER(rotulo) LIKE :q", ['q' => '%' . $q . '%']);
    }

    public function rotulo() {
        return $this->rotulo;
    }

}
