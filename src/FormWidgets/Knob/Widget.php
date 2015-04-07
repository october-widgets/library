<?php namespace Owl\FormWidgets\Knob;

use Backend\Classes\FormWidgetBase;

/**
 * Widget Form Widget
 */
class Widget extends FormWidgetBase
{

    /**
     * {@inheritDoc}
     */
    protected $defaultAlias = 'owl_knob';

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
        $this->vars['default'] = $default = $this->getConfig('default', 0);
        $this->vars['label'] = $this->getConfig('knobLabel');
        $this->vars['comment'] = $this->getConfig('knobComment');
        $this->vars['knobSettings'] = [
            'angleArc'          => $this->getConfig('angleArc', 360),
            'angleOffset'       => $this->getConfig('angleOffset', 0),
            'bgColor'           => '#'.$this->getConfig('bgColor', 'EEEEEE'),
            'cursor'            => $this->getConfig('cursor', 'false'),
            'displayInput'      => $this->getConfig('displayInput', 'true'),
            'displayPrevious'   => $this->getConfig('displayPrevious', 'false'),
            'fgColor'           => '#'.$this->getConfig('fgColor', '87CEEB'),
            'font'              => $this->getConfig('font', 'Open Sans'),
            'fontWeight'        => $this->getConfig('fontWeight', 'normal'),
            'height'            => $this->getConfig('width', 100),
            'inputColor'        => '#'.$this->getConfig('inputColor', '87CEEB'),
            'lineCap'           => $this->getConfig('linecap', 'default'),
            'max'               => $this->getConfig('max', 100),
            'min'               => $this->getConfig('min', 0),
            'readOnly'          => $this->getConfig('disabled', 'false'),
            'rotation'          => $this->getConfig('rotation', 'clockwise'),
            'step'              => $this->getConfig('step', 1),
            'stopper'           => $this->getConfig('stopper', 'true'),
            'thickness'         => $this->getConfig('thickness', 0.3),
            'width'             => $this->getConfig('width', 100),
        ];

        $this->vars['value'] = $this->getLoadValue() ?: $default;
    }

    /**
     * {@inheritDoc}
     */
    public function loadAssets()
    {
        $this->addCss('css/widget.css');
        $this->addJs('js/widget.js');
    }

    /**
     * {@inheritDoc}
     */
    public function getSaveValue($value)
    {
        return $value;
    }

}
