<?php

namespace App;

use Core\Identify;
use Core\DB;
use Core\Model;

class Auth extends Identify
{
    protected $connection = 'sutran';
    protected $table = 'robusto.srt_usuario';

    public function can($route_name)
    {
        $mm = collect($this->modules);
        if ($mm->count() == 0) {
            return true;
        }
        if (!is_array($route_name)) {
            $route_name = array($route_name);
        }
        foreach ($mm as $m) {
            foreach ($route_name as $r) {
                if ($m->controlador == $r) {
                    return true;
                }
            }
        }
        return true;
    }
    public function handle($code_company, $username, $password)
    {
        $error_msn[0] = "Cuenta bloqueada";
        $error_msn[1] = "Su cuenta se encuentra suspendida, comuniquese al 01-6336883 anexo 100  o al correo ticket@creainter.com.pe";
        $error_msn[2] = "Lo sentimos el sistema ha detectado transmisiones con errores constantes, por tal motivo se ha bloqueado el acceso a su cuenta del SRT.  <br><br>
                        Debe contactarse con CGM<br>
                        <b>sgse@sutran.gob.pe</b>";
        $error_msn[3] = "Actividad de IP sospechosa: Se ha detectado varias sesiones de su cuenta en diferentes direcciones IP<br><b>Cuenta bloqueada</b>";
        $error_msn[4] = "Urgente el sistema ha detectado envíos masivos de botón de pánico, por tal motivo se ha suspendido el acceso a su cuenta del SRT.  <br><br>
                        Debe contactarse con CGM para volver activarlo.";
        $error = "Datos Invalidos";

        if (empty($username) || empty($password) || !is_string($username) || !is_string($password)) {
            return false;
        }

        if (strlen($username) > 30 || strlen($password) > 30) {
            return false;
        }

        if (!empty($_SERVER['TENANT_ID']) && false) {
            $tenant_id = $_SERVER['TENANT_ID'];
        } else {
            $dde = db()->first("
	                    SELECT T.id
						FROM robusto.tenant T
						WHERE T.ruc = :ruc
						LIMIT 1", [
                'ruc' => $code_company
            ]);
            if (empty($dde)) {
                $error = "Los datos son incorrectos(0)";
                return false;
            }
            $tenant_id = $dde->id;
        }

        $dd = db()->first("
            SELECT
                U.id, U.usuario, U.clave, U.tenant_id, U.estado,  U.empresa_id, T.empresa_id empresa_tenant, U.created_on, U.observacion,
                T.rol_id, T.endpoint_id, U.cargo_id,
                robusto.fn_rotulo_de_tenant(T.id) tenant_rotulo
            FROM robusto.srt_usuario U
            JOIN robusto.tenant T ON T.id = U.tenant_id AND T.id = :tenant
			WHERE U.usuario = :user", [
            'user' => $username,
            'tenant' => $tenant_id,
        ]);
        if (empty($dd)) {
            $error = "Los datos son incorrectos(1)";
            return false;
        }
        /*if (empty($dd->estado)) {
            //$error = $error_msn[$dd->];
            $error = $error_msn[1];
            return false;
				}*/

        if (!(md5($password) === $dd->clave || $password === $dd->clave)) {
            $error = "Los datos son incorrectos(2-)";
            return false;
        }
        if (!empty($dd->cargo_id)) {
            $dd->modules = db()->select("
                SELECT R.id,R.clase_icono,R.nombre, R.controlador,R.prioridad, R.es_grupo, GR.nombre grupo
                FROM acl.cargo_ruta CR
                JOIN acl.ruta R on R.id = CR.ruta_id
								LEFT JOIN acl.ruta GR on GR.id = R.grupo_id
								WHERE CR.cargo_id = :id 
								ORDER BY R.prioridad DESC
								", ['id' => $dd->cargo_id]);
        } else {

            $dd->modules = db()->select(" 
                SELECT R.id,R.clase_icono, R.nombre, R.controlador,R.prioridad, R.es_grupo, GR.nombre grupo 
                FROM robusto.srt_usuario U 
                JOIN robusto.tenant T on T.id = U.tenant_id
                JOIN acl.ruta R on T.rol_id = ANY (R.rol_ids)
								LEFT JOIN acl.ruta GR on GR.id = R.grupo_id
								WHERE U.id = :id
								ORDER BY R.prioridad DESC
								", ['id' => $dd->id]);
        }

        /*if (!empty($dd->BLOQUEADO) && false) {
            if (strtotime($dd->BLOQUEADO) > time()) {
                $error = $error_msn[0];
                return false;
            } else {
                //usuario_log($dd, "Desbloqueo de usuario", 4);
            }
        }*/
        return $dd;
    }
    public function isForcing()
    {
        return false;
    }
}
