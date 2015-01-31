<?php namespace Owl\FormWidgets\Comment;

use Backend\Classes\FormField;
use Backend\Classes\FormWidgetBase;

class Widget extends FormWidgetBase
{

    /**
     * Return no save data
     */
    public function getSaveValue($value)
    {
        return FormField::NO_SAVE_DATA;
    }

}