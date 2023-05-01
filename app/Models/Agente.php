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
    static public function ping($tenant_id, $uuid, $ccid) {
        return db()->first("SELECT * FROM robusto.fn_agente_ping(:tenant, :uuid, :ccid);", [
            'tenant' => $tenant_id,
            'uuid'   => $uuid,
            'ccid'   => $ccid,
        ]);
    }
    static public function backup($tenant_id, $uuid, $ccid) {
        return db()->first("SELECT * FROM robusto.fn_agente_backup(:tenant, :uuid, :ccid);", [
            'tenant' => $tenant_id,
            'uuid'   => $uuid,
            'ccid'   => $ccid,
        ]);
    }
}
