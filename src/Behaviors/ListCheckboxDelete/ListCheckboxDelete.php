<?php namespace Owl\Behaviors;

use Backend\Classes\ControllerBehavior;
use Flash;
use Lang;

class ListCheckboxDelete extends ControllerBehavior {

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
     * Delete selected rows
     *
     * @return  array
     */
    public function index_onDelete()
    {
        $model = $this->config->modelClass;

        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {
            foreach ($checkedIds as $id) {
                if ($record = $model::find($id)) {
                    $record->delete();
                }
            }

            Flash::success(Lang::get('backend::lang.list.delete_selected_success'));
        }

        return method_exists($this->controller, 'overrideListRefresh')
            ? $this->controller->overrideListRefresh()
            : $this->controller->listRefresh();
    }
}
