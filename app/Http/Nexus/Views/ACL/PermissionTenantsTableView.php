<?php
namespace App\Http\Nexus\Views\ACL;

use Core\Nexus\Tablefy;
use Core\Nexus\Header;
use Core\Nexus\Action;
use Core\Request;
use App\Models\ACL\Tenant;
use App\Models\ACL\Rol;
use App\Models\ACL\Plan;


class PermissionTenantsTableView extends Tablefy
{
    protected $model = Tenant::class;
    protected $paginate = 100;

    public function headers(): array
    {
      return [
          Header::name('Id')->width(100),
          Header::name('Rotulo')->width(100),
          Header::name('Habilitado')->width(100),
          Header::name('Verificado')->width(100),
          Header::name('Sesión')->width(100),
          Header::name('Oportunidades')->width(100),
          Header::name('Etiquetas')->width(100),
          Header::name('Créditos')->width(100),
          Header::name('Usuarios')->width(100),
          Header::name('Plan')->width(100),
          Header::name('Rol'),
        ];
    }

    public function row($model)
    {
        return [
          $model->id,
          $model->rotulo,
          (!empty($model->habilitado) ? 'SI' : 'no'),
          clock($model->activado)->ago(),
          clock($model->last_sesion)->ago(),
          $model->oportunidades,
          $model->etiquetas,
          $model->creditos,
          $model->usuarios,
          'plan_id' => $model->plan,
          'rol_id' => $model->rol
        ];
    }
    protected function events() {
      return $this
      ->on('edit', 'plan_id', function($row) {
        return [
          'type' => 'select',
          'attrs' => [],
          'options' => Plan::all()->pluck('rotulo','id'),
        ];
      })
      ->on('save', 'plan_id', function($row, $res) {
db()->transaction();
db()->first("UPDATE public.acl_tenant SET habilitado = TRUE WHERE id = :tid", [
  'tid' => $row->id,
]);
db()->first("UPDATE public.licencia
  SET vigencia_hasta = (NOW()::date - INTERVAL '1' DAY)::date
  WHERE tenant_id = :tid AND vigencia_hasta >= NOW()::date AND plan_id <> :pid", [
  'tid' => $row->id,
  'pid' => $res->value,
]);
db()->first("
INSERT INTO public.licencia (tenant_id, plan_id, vigencia_desde, vigencia_hasta, monto)
SELECT :tid, :pid, NOW()::date, (NOW() + INTERVAL '1' YEAR)::date, 1
WHERE NOT EXISTS(SELECT 1 FROM public.licencia L WHERE L.tenant_id = :tid AND L.plan_id = :pid AND L.vigencia_hasta >= NOW()::date)
AND :pid IS NOT NULL
", [
  'tid' => $row->id,
  'pid' => $res->value,
]);
db()->commit();

        return true;
      })
      ->on('edit', 'rol_id', function($row) {
        return [
          'type' => 'select',
          'attrs' => [],
          'options' => Rol::all()->pluck('rotulo','id'),
        ];
      })
      ->on('save', 'rol_id', function($row, $res) {
        $editar = [];
        $editar[$res->field] = $res->value;
        unset($row->_map);
        $row->update($editar);
        return true;
      });
    }
    protected function repository()
    {
      return $this->query("
        SELECT *
        FROM (
        SELECT
          T.*, R.rotulo rol, (SELECT COUNT(1) FROM public.usuario u WHERE u.tenant_id = T.id) usuarios,
          (
            SELECT ARRAY_TO_STRING(ARRAY_AGG(P.rotulo), ', ')
            FROM public.licencia L
            JOIN public.plan P ON P.id = L.plan_id
            WHERE L.tenant_id = T.id AND L.vigencia_hasta >= NOW()::date
          ) plan,
          (
            SELECT L.plan_id
            FROM public.licencia L
            WHERE L.tenant_id = T.id AND L.vigencia_hasta >= NOW()::date
            LIMIT 1
          ) plan_id,
          (SELECT COUNT(1) FROM osce.empresa_etiqueta EE WHERE EE.tenant_id = T.id) etiquetas,
          (SELECT COUNT(1) FROM osce.oportunidad O WHERE O.tenant_id = T.id) oportunidades
        FROM public.acl_tenant T
        LEFT JOIN public.acl_rol R ON R.id = T.rol_id) T
        ORDER BY (T.habilitado IS TRUE) DESC, (T.last_sesion IS NOT NULL) DESC, T.last_sesion DESC, T.id ASC");
    }
    protected function actionsByRow($row)
    {
        return [
          //Action::title('Ver')->icon('show')->ajax(true)->route('oportunidad.show', $row->id),
        ];
    }

    /** For bulk actions */
    protected function bulkActions()
    {
        return [
        ];
    }
    public function controller(Request $request)
    {
        $response = $this
          ->repository()
          ->appends(request()->input())
          ->get();
        return response()->json($response);
    }
}

