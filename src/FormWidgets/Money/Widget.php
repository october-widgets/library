<?php namespace Owl\FormWidgets\Money;

use Backend\Classes\FormField;
use Backend\Classes\FormWidgetBase;

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
     * Load widget assets
     */
    public function loadAssets()
    {
        $this->addJs('js/jquery.maskMoney.js');
    }

    /**
     * Prepare widget variables
     */
    public function prepareVars()
    {
        $this->vars['loadValue'] = $this->getLoadValue();

        $this->vars['placeholder'] = isset($this->config->placeholder)
            ? $this->config->placeholder
            : '0.00';

        $this->vars['thousands'] = isset($this->config->thousands)
            ? $this->config->thousands
            : ',';

        $this->vars['decimal'] = isset($this->config->decimal)
            ? $this->config->decimal
            : '.';

        $this->vars['suffix'] = isset($this->config->suffix)
            ? $this->config->suffix
            : false;

        $this->vars['prefix'] = isset($this->config->prefix)
            ? $this->config->prefix
            : false;

        $this->vars['allowNegative'] = isset($this->config->allowNegative) && $this->config->allowNegative
            ? 'true'
            : 'false';
    }

    /**
     * Return save value
     *
     * @return  float
     */
    public function getSaveValue($value)
    {
        if (!$input = post($this->alias))
            return 0;

        $input = preg_replace("/[^0-9]/", '', $input);
        $input = substr($input, 0, -2) . '.' . substr($input, -2);
        $input = floatval($input);

        return $input;
    }
}
