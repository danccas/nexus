<?php

namespace App\Tablefy\Views;

use Core\Tablefy\Tablefy;
use App\Tablefy\Actions\ActivateUserAction;
use App\Models\Libro;

class LibroTablefy extends Tablefy
{
    protected $model = Libro::class;
    protected $paginate = 10;

    public function headers(): array
    {
        return [
            'Name',
            'Email',
            'Created',
            'Updated'
        ];
    }

    public function row($model)
    {
        return [
            $model->name,
            $model->email,
            $model->created_at,
            $model->updated_at
        ];
    }
    protected function repository()
    {
        return [];
        return $this->query("SELECT * FROM public.usuario LIMIT 100");
    }
    protected function actionsByRow()
    {
        return [
            new ActivateUserAction,
        ];
    }

    /** For bulk actions */
    protected function bulkActions()
    {
        return [
            new ActivateUserAction,
        ];
    }
}
