<?php namespace Owl\FormWidgets\Tagbox;

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
     * Prepare widget variables
     */
    public function prepareVars()
    {
        // Break key codes
        if (isset($this->config->break_codes)) {
            $config['break_codes'] = is_array($this->config->break_codes)
                ? $this->config->break_codes
                : [$this->config->break_codes];
        } else {
            $config['break_codes'] = [13, 9];
        }

        // Slugify
        $config['slugify'] = isset($this->config->slugify) && 
            filter_var($this->config->slugify, FILTER_VALIDATE_BOOLEAN);

        // Accepted characters
        $config['filter'] = isset($this->config->filter)
            ? $this->config->filter
            : false;

        // Validation rules
        $config['validation'] = isset($this->config->validation)
            ? $this->config->validation
            : false;

        // Validation message
        $config['validation_message'] = isset($this->config->validation_message)
            ? $this->config->validation_message : 'The tag format is invalid.';

        // Javascript configuration
        $config['alias'] = $this->alias;  // Popup script bug
        $this->vars['config'] = json_encode($config);

        // Pre-populated tags
        $fieldName = $this->fieldName;
        $this->vars['tags'] = is_array($this->model->$fieldName)
            ? implode(',', $this->model->$fieldName)
            : false;

        // Placeholder
        $this->vars['placeholder'] = isset($this->config->placeholder)
            ? htmlspecialchars($this->config->placeholder)
            : "Enter tags...";
    }

    /**
     * Load widget assets
     */
    public function loadAssets()
    {
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