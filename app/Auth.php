<?php

namespace App;

use Core\Identify;
use Core\DB;
use Core\Model;

class Auth extends Identify
{
    protected $connection = 'interno';
    protected $table = 'public.usuario';

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
        $error_msn[2] = "Lo sentimos el sistema ha detectado transmisiones con errores constantes";
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

        $dd = db()->first("
            SELECT U.id, U.usuario
            FROM public.usuario U
			WHERE U.usuario = :user", [
            'user' => $username,
        ]);
        if (empty($dd)) {
            $error = "Los datos son incorrectos(1)";
            return false;
        }

        if (!(md5($password) === $dd->clave || $password === $dd->clave)) {
            $error = "Los datos son incorrectos(2-)";
            return false;
        }
        $dd->modules = [];
        return $dd;
    }
    public function isForcing()
    {
        return false;
    }
}
