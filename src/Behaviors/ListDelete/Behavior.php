<?php namespace Owl\Behaviors\ListDelete;

use Backend\Classes\ControllerBehavior;
use Flash;
use Lang;

class Behavior extends ControllerBehavior {

    /**
     * @var Controller
     */
    protected $controller;

    /**
     * Behavior constructor
     *
     * @param   Controller  $controller
     */
    public function __construct($controller)
    {
        parent::__construct($controller);

        $this->controller = $controller;

        $this->setConfig($controller->listConfig, ['modelClass']);
    }

    /**
     * Delete the selected rows
     *
     * @return  array
     */
    public function index_onDelete()
    {
        $model = $this->config->modelClass;

        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {
            foreach ($checkedIds as $id) {
                if ($record = $model::find($id)) {
                    if (method_exists($this->controller, 'overrideListDelete')) {
                        $this->controller->overrideListDelete($record);
                    } else {
                        $record->delete();
                    }
                }
            }

            if (method_exists($this->controller, 'afterListDelete')) {
                $this->controller->afterListDelete();
            } else {
                Flash::success(Lang::get('backend::lang.list.delete_selected_success'));
            }
        }

        return method_exists($this->controller, 'overrideListRefresh')
            ? $this->controller->overrideListRefresh()
            : $this->controller->listRefresh();
    }
}
