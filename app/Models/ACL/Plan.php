<?php

namespace App\Models\ACL;

use Core\Model;
use Core\DB;

class Plan extends Model
{
  protected $connection = 'interno';
  protected $table = 'public.plan';
    const UPDATED_AT = null;
    const CREATED_AT = null;
    protected $fillable = [
        'rotulo'
    ];
    protected $hidden = [];
    protected $casts = [];

}

