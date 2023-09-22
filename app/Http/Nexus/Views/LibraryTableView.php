<?php

namespace App\Http\Nexus\Views;

use Core\Nexus\Tablefy;
use Core\Nexus\Header;
use Core\Nexus\Action;
use Core\Request;
use App\Http\Nexus\Actions\ActivateUserAction;
use App\Models\Libro;

class LibraryTableView extends Tablefy
{
    protected $model = Libro::class;
    protected $paginate = 15;

    public function headers(): array
    {
        return [
            Header::name('Id')->width(100),
            Header::name('Username')->width(200),
            Header::name('Password')->width(300),
            'Created2'
        ];
    }

    public function row($model)
    {
        return [
            $model->id,
            $model->usuario,
            $model->usuario,
            $model->created_on
        ];
    }
    protected function repository()
    {
        return $this->query("SELECT * FROM public.usuario LIMIT 100");
    }
    protected function actionsByRow()
    {
        return [
          new ActivateUserAction,
          Action::title('Eliminar')->icon('error')->link('/dashboard'),
          Action::title('Mensajear')->icon('message')->click(function($n) {
            return true;
          })
        ];
    }

    /** For bulk actions */
    protected function bulkActions()
    {
        return [
            new ActivateUserAction,
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
