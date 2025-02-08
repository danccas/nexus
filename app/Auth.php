<?php

namespace App;

use Core\Identify;
use Core\DB;
use Core\Model;

use App\Models\ACL\User;

class Auth extends User
{
    public function can($route_name)
    {
      if(empty($this->modules_allow)) {
        return false;
      }
      $mm = collect($this->modules_allow);
      if ($mm->count() == 0) {
            return true;
        }
        if (!is_array($route_name)) {
            $route_name = array($route_name);
        }
        foreach ($mm as $m) {
          foreach ($route_name as $r) {
            if ($m == $r) {
                    return true;
                }
            }
        }
        return false;
    }
    public function handle($username, $password)
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
            SELECT
                U.id, U.usuario, U.clave, U.tenant_id, U.nombres,
                T.rotulo tenant_rotulo,
                R.rotulo tenant_tipo,
                R.id rol_id
            FROM public.usuario U
            JOIN public.acl_tenant T ON T.id = U.tenant_id
            JOIN public.acl_rol R ON R.id = T.rol_id
			WHERE U.usuario = :user", [
            'user' => $username,
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

        User::find($dd->id)->update(['last_sesion' => db()->raw('now()')]);

        $modules = db()->get("
SELECT
	G.id grupo_id,
	C.id controlador_id,
	CP.id padre_id,
	CP.rotulo padre,
	C.rotulo controlador,
	C.link route,
	C.visible,
	array_to_string(GP.permisos, ',') permisos
FROM public.acl_rol R
JOIN public.acl_grupo G ON G.id = ANY(R.grupo_ids)
JOIN public.acl_grupo_permiso GP ON GP.grupo_id = G.id AND GP.eliminado = 0
JOIN public.acl_controlador C ON C.id = GP.controlador_id
LEFT JOIN public.acl_controlador CP ON CP.id = C.controlador_padre_id
WHERE R.id = :rid
ORDER BY C.orden ASC, CONCAT(CP.rotulo, C.rotulo) ASC", ['rid' => $dd->rol_id]);

//$modules_allow = collect($modules)->map(function($n) {
//  return $n;->route;
//});
$mms = [];
foreach ($modules as $m) {
  foreach(explode(',', $m->permisos) as $e) {
    if(strpos('.', $m->route) === false) {
      $mms[] = explode('.', $m->route)[0] . '.' . $e;
    }
    $mms[] = $m->route;
  }
}
$mms = array_unique($mms);
$dd->modules_allow = $mms;

$modules = collect($modules)->filter(function($n) {
  return !empty($n->visible);
});

$navs = [];
foreach($modules as $m) {
  if(empty($m->padre_id)) {
    $m->navs = [];
    $navs[$m->controlador_id] = $m;
  }
}
foreach($modules as $m) {
  if(!empty($m->padre_id)) {
    if(isset($navs[$m->padre_id])) {
      ($navs[$m->padre_id])->navs[$m->controlador_id] = $m;
    }
  }
}

$dd->modules_nav = $navs;

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
    public function navs() {
      if(empty($this->modules_nav)) {
        return [];
      }
      return $this->modules_nav;
    }
    public function byId($id) {
      $user = db()->first("SELECT osce.fn_usuario_rotulo(:uid) rotulo", [
        'uid' => $id
      ]);
      return $user->rotulo;
    }
    public function allow($tipo, $externo = null) {
      return (collect(db()->get("SELECT osce.fn_usuario_permiso(:id, :tipo, :externo) estado", [
        'id'      => Auth::user()->id,
        'tipo'    => $tipo,
        'externo' => $externo
      ]))->first())->estado;
    }
}
