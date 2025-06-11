<?php
namespace App\Models\ACL;

use Core\Model;
use Core\Formity;

class Tenant extends Model
{
    protected $connection = 'interno';


    protected $table = 'public.acl_tenant';


    protected $fillable = ['empresa_id', 'rotulo', 'celular','rol_id'];

    protected $casts = [
      'rol_id' => 'integer',
    ];

  public static function form() {
    $form = Formity::instance('tenant');
      $form->addField('empresa_id', 'input:integer');
      $form->addField('rotulo', 'input:string');
      $form->addField('celular', 'input:string');
    return $form;
  }
}

