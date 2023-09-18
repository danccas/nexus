<?php

namespace App\Http\Nexus\Actions;

use Core\Nexus\Action;
use Core\View;

class ActivateUserAction extends Action
{
    /**
     * Any title you want to be displayed
     * @var String
     * */
    protected $title = "Activate user";

    /**
     * This should be a valid Feather icon string
     * @var String
     */
    protected $icon = "bell";


    /**
     * Execute the action when the user clicked on the button
     *
     * @param $model Model object of the list where the user has clicked
     * @param $view Current view where the action was executed from
     */
    public function handle($model)
    {
        $model->active = true;
        $model->save();
    }
}
