<?php namespace Owl\Widgets\TreeSort;

use Backend\Classes\WidgetBase;
use DB;

class Widget extends WidgetBase
{
    protected $defaultAlias = 'treesort';

    /**
     * @var string              The popup window's header
     */
    public $header = 'Sort records';

    /**
     * @var string              Message to display when there are no records
     */
    public $empty = 'There are no records to sort.';

    /**
     * @var boolean | string    The index to sort tree nodes by
     */
    public $index = false;

    /**
     * Returns information about this widget.
     *
     * @return  array
     */
    public function widgetDetails()
    {
        return [
            'name'          => 'Tree Sort',
            'description'   => 'Controller list sorting.'
        ];
    }

    /**
     * Inject CSS and JS assets
     */
    public function loadAssets()
    {
        $this->addCss('css/treesort.css');
        $this->addJs('js/treesort.js');
    }

    /**
     * Load the re-order popup
     */
    public function onLoadPopup()
    {
        $model = $this->controller->widget->list->model;

        $records = $model::make()->getAllRoot();
        if ($this->index) {
            $records->sortBy($this->index);
        }

        return $this->makePartial('popup', [
            'records'   => $records,
            'header'    => $this->header,
            'empty'     => $this->empty,
        ]);
    }
}
