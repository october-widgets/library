<?php namespace Owl\FormWidgets\Hasmany;

use Backend\Classes\FormField;
use Backend\Classes\FormWidgetBase;
use Exception;
use Schema;
use Twig_Environment;
use Twig_Loader_Array;

class Widget extends FormWidgetBase {

    /**
     * {@inheritDoc}
     */
    public $defaultAlias = 'owl-hasmany';

    /**
     * The related target model
     */
    public $relatedModel;

    /**
     * Load the related model
     */
    public function init()
    {
        // Make sure the relationship exists
        if (!isset($this->model->hasMany[$this->fieldName]))
            throw new Exception('Unknown hasmany relationship "'.$this->fieldName.'".');

        $this->relatedModel = new $this->model->hasMany[$this->fieldName][0];
    }

    /**
     * Return the widget
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('widget');
    }

    /**
     * Prepares widget variables
     */
    private function prepareVars()
    {
        // Make sure a partial was defined
        $fieldName = $this->fieldName;
        if (!isset($this->config->default['label']) && empty($this->config->partial))
            throw new Exception('A default label or custom partial must be defined for "'.$fieldName.'".');

        // Explode model path for determining plugin path
        $modelPath = $this->model->hasMany[$fieldName][0];
        $parts = explode('\\', strtolower($modelPath));

        // New up the relationship model, and configure the widget
        $relatedWidget = $this->makeConfig("@/plugins/$parts[0]/$parts[1]/models/$parts[3]/fields.yaml");
        $relatedWidget->model = $this->relatedModel;
        $relatedWidget->alias = $this->alias.$fieldName;

        $partial = isset($this->config->default)
            ? 'default'
            : $this->config->partial;

        $this->vars['partial']          = $partial;
        $this->vars['relatedWidget']    = $this->makeWidget('Backend\Widgets\Form', $relatedWidget);
        $this->vars['relatedModel']     = $this->relatedModel;
        $this->vars['relatedName']      = $parts[3];
        $this->vars['properties']       = Schema::getColumnListing($this->relatedModel->table);
        $this->vars['validation']       = $this->getEventHandler('onValidateModel');
        $this->vars['add_icon']         = isset($this->config->add_icon) ? $this->config->add_icon : 'icon-plus';
        $this->vars['add_label']        = isset($this->config->add_label) ? $this->config->add_label : "Add $parts[3]";
        
        $this->prepareItems($this->model->$fieldName, $partial);
    }

    /**
     * Prepare related items
     * @param  Collection   $relationships
     */
    private function prepareItems($relationships, $partial)
    {
        $items = [];
        $loader = new Twig_Loader_Array([
            'item' => $this->makePartial($partial)
        ]);
        $twig = new Twig_Environment($loader);

        foreach ($relationships as $item) {

            // Workaround to get toJson() returning an object instead of a
            // string on jsonable attributes.
            $data = [];
            foreach ($item->attributes as $key => $value) {
                if ($key == 'created_at' || $key == 'updated_at')
                    continue;
                $data[$key] = $item[$key];
            }
            $data = json_encode($data);

            $items[] = [
                'data' => htmlspecialchars($data),
                'html' => $twig->render('item', $item->toArray())
            ];
        }

        $this->vars['items'] = $items;
    }

    /**
     * {@inheritDoc}
     */
    public function loadAssets()
    {
        $this->addJs('js/twig.min.js');
        $this->addJs('js/widget.js');
        $this->addCss('css/widget.css');

        if (isset($this->config->default))
            $this->addCss('css/items.css');

        if (isset($this->config->sort_column))
            $this->addJs('js/html5sortable.js');
    }

    /**
     * {@inheritDoc}
     */
    public function getSaveValue($value)
    {
        // Return no save data if there are no attachments
        if (!$formData = post($this->alias))
            return FormField::NO_SAVE_DATA;

        // Otherwise, popular and attach related models
        $ids = [];
        $fieldName = $this->fieldName;
        foreach ($formData as $i => $json) {
            $data = json_decode($json);
            $model = isset($data->id)
                ? $this->relatedModel->find($data->id)
                : new $this->relatedModel;

            if (!$this->model)
                continue;

            foreach ($data as $key => $value) {
                if($key == 'id' || $key == 'updated_at' || $key == 'created_at')
                    continue;
                $model->$key = $value;
            }

            // Sort order
            if (isset($this->config->sort_column)) {
                $sort_column = $this->config->sort_column;
                $model->$sort_column = $i;
            }

            $model->save();
            $ids[] = $model->id;

            // Deferred bindings
            if (!isset($data->id))
                $this->model->$fieldName()->add($model, $this->sessionKey);
        }

        // Run deferred deletes
        $belongsTo = false;
        $master = get_class($this->model);
        foreach ($this->relatedModel->belongsTo as $key => $relationship) {
            if ($relationship[0] == $master)
                $belongsTo = $key;
        }
        if ($belongsTo) {
            $this->model->bindEvent('model.afterSave', function() use ($ids, $belongsTo) {
                $delete = $this->relatedModel
                    ->whereNotIn('id', $ids)
                    ->whereHas($belongsTo, function($q) {
                        $q->where('id', $this->model->id);
                    })
                    ->delete();
            });
        }

        return FormField::NO_SAVE_DATA;
    }

    /**
     * Ajax handler to validate the related model
     */
    public function onValidateModel()
    {
        if (isset($this->relatedModel->rules) && $this->relatedModel->rules) {
            foreach (post() as $key => $value)
                $this->relatedModel->$key = $value;
            
            $this->relatedModel->validate();
        }
    }

    /**
     * Deletes an item
     */
    public function onDeleteModel()
    {
        return $this->relatedModel->find(post('owl_id'))->delete();
    }
}