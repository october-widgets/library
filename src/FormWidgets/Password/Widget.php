<?php namespace Owl\FormWidgets\Password;

use Backend\Classes\FormWidgetBase;

/**
 * Password Form Widget
 */
class Widget extends FormWidgetBase
{

    /**
     * {@inheritDoc}
     */
    protected $defaultAlias = 'owl_password';

    /**
     * {@inheritDoc}
     */
    public function init()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('widget');
    }

    /**
     * Prepares the form widget view data
     */
    public function prepareVars()
    {
        $this->vars['name'] = $this->formField->getName();
        $this->vars['value'] = $this->getLoadValue();
        $this->vars['model'] = $this->model;
    }

    /**
     * {@inheritDoc}
     */
    public function loadAssets()
    {
        $this->addJs('js/password.min.js');
        $this->addCss('css/owl-password.css');
    }

    /**
     * {@inheritDoc}
     */
    public function getSaveValue($value)
    {
        return $value;
    }

}
