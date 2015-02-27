<?php namespace Owl\FormWidgets\Money;

use Backend\Classes\FormField;
use Backend\Classes\FormWidgetBase;
use Config;

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

        $this->vars['config']['placeholder'] = isset($this->config->placeholder)
            ? $this->config->placeholder
            : '0.00';

        $this->vars['config']['thousands'] = isset($this->config->thousands)
            ? $this->config->thousands
            : ',';

        $this->vars['config']['decimal'] = isset($this->config->decimal)
            ? $this->config->decimal
            : '.';

        $this->vars['config']['suffix'] = isset($this->config->suffix)
            ? $this->config->suffix
            : '';

        $this->vars['config']['prefix'] = isset($this->config->prefix)
            ? $this->config->prefix
            : '';

        $this->vars['config']['allowNegative'] = isset($this->config->allowNegative) && $this->config->allowNegative
            ? 'true'
            : 'false';

        // Allow plugins to override the configuration
        if ($config = Config::get('owl.formwidgets::money')) {
            if (is_array($config) && count($config) >= 1) {
                foreach ($config as $key => $value)
                    $this->vars['config'][$key] = $value;
            }
        }
    }

    /**
     * Return save value
     *
     * @return  float
     */
    public function getSaveValue($value)
    {
        if (!$input = input($this->fieldName)
            return 0;

        $input = preg_replace("/[^0-9]/", '', $input);
        $input = substr($input, 0, -2) . '.' . substr($input, -2);
        $input = floatval($input);

        return $input;
    }
}
