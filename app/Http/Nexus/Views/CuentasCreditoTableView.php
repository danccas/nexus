<?php

namespace App\Http\Nexus\Views;

use Core\Nexus\Tablefy;
use Core\Nexus\Header;
use Core\Nexus\Action;
use App\Http\Nexus\Actions\ActivateUserAction;
use App\Models\Financiero;

class CuentasCreditoTableView extends Tablefy
{
    protected $model = Financiero::class;
    protected $paginate = 15;

    public function headers(): array
    {
        return [
            Header::name('Cuenta')->width(150),
            Header::name('Cred.')->width(60),
            Header::name('Cons.')->width(60),
            Header::name('Proy.')->width(60),
        ];
    }

    public function row($n)
    {
      return [
        '<div><a href="?cid=' . $n->id . '">#' . $n->id . ':' . $n->banco . ':' . $n->moneda . ':' . $n->cuenta . '</a></div><small>' . $n->numero . ' - Ajustado: ' . ($n->ajustes) . '</small>',
        '<span title="' . implode(',', [$n->moneda, 'CONTABLE ACTUAL']) . '">' . $n->contable . '</span>',
        '<span title="' . implode(',', [$n->moneda, 'DISPONIBLE ACTUAL']) . '">' . $n->disponible . '</span>',
        '<span title="' . implode(',', [$n->moneda, ': PROYECTADO DISPONIBLE ACTUAL']) . '">' . ($n->proyectado_disponible + $n->disponible) . '</span>',
      ];
    }
    protected function repository()
    {
        return $this->query("SELECT * FROM financiero.obtener_cuentas_creditos(NOW()::timestamp)");
    }
    protected function actionsByRow()
    {
      return [
        Action::title('Editar')->icon('refresh')->link('/demo')
      ];
    }
    /** For bulk actions */
    protected function bulkActions()
    {
        return [
        ];
    }
}
