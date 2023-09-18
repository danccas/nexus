<?php

namespace App\Models;

use Core\Model;

class Libro extends Model
{
    protected $connection = 'financiero';
    protected $table = 'public.usuario';
    protected $fillable = ['id','rotulo','categoria'];

}
