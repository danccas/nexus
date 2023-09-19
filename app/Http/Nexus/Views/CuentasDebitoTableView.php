<?php

namespace App\Http\Nexus\Views;

use Core\Nexus\Tablefy;
use Core\Nexus\Header;
use Core\Nexus\Action;
use App\Http\Nexus\Actions\ActivateUserAction;
use App\Models\Financiero;

class CuentasDebitoTableView extends Tablefy
{
    protected $model = Financiero::class;
    protected $paginate = 15;

    public function headers(): array
    {
        return [
            Header::name('Cuenta')->width(300),
            Header::name('Contable')->width(100),
            Header::name('Disponible'),
        ];
    }

    public function row($n)
    {
      return [
        '<div><a href="?cid=' . $n->id . '">#' . $n->id . ':' . $n->banco . ':' . $n->moneda . ':' . $n->cuenta . '</a></div><small>' . $n->numero . ' - Ajustado: ' . ($n->ajustes) . '</small>',
        implode(',', [$n->contable, $n->moneda, 'CONTABLE ACTUAL']),
        implode(',', [$n->disponible, $n->moneda, 'DISPONIBLE ACTUAL']),
        implode(',', [$n->proyectado_disponible + $n->disponible, $n->moneda, 'PROYECTADO DISPONIBLE ACTUAL']),
      ];
    }
    protected function repository()
    {
        return $this->query("SELECT * FROM financiero.obtener_cuentas_debitos(NOW()::timestamp)");
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
