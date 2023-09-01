<?php

namespace App\Models;

use Core\Model;

class Agente extends Model
{
    protected $connection = 'sutran';
    protected $table = 'robusto.agente';
    protected $fillable = ['id','rotulo','token'];

    static public function connect($tenant_id, $uuid) {
        return db()->first("SELECT * FROM robusto.fn_agente_connect(:tenant, :uuid);", [
            'tenant' => $tenant_id,
            'uuid'   => $uuid,
        ]);
    }
}
