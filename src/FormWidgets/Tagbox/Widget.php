<?php namespace Owl\FormWidgets\Tagbox;

use Backend\Classes\FormField;
use Backend\Classes\FormWidgetBase;
use Exception;

class Widget extends FormWidgetBase
{

    /**
     * Render the form widget
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('widget');
    }

    /**
     * Prepare widget variables
     */
    public function prepareVars()
    {
        // Pre-populated tags
        $fieldName = $this->fieldName;
        $this->vars['tags'] = is_array($this->model->$fieldName)
            ? implode(',', $this->model->$fieldName)
            : false;

        // Sorting
        $this->vars['sortable'] = isset($this->config->sortable) && filter_var($this->config->sortable, FILTER_VALIDATE_BOOLEAN)
            ? 'true'
            : 'false';

        // Placeholder
        $this->vars['placeholder'] = isset($this->config->placeholder)
            ? $this->config->placeholder
            : "Enter tags...";
    }

    /**
     * Load widget assets
     */
    public function loadAssets()
    {
        $this->addJs('js/jquery-ui.custom.min.js');
        $this->addJs('js/tagbox.js');
        $this->addCss('css/tagbox.css');
    }

    /**
     * Return save value
     * @return  array
     */
    public function getSaveValue($value)
    {
        return post($this->alias);
    }

}