<?php

namespace App\Models;

use Core\Model;

class Libro extends Model
{
    protected $connection = 'interno';
    protected $table = 'public.libro';
    protected $fillable = ['id','rotulo','categoria'];

}
