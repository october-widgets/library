<?php namespace Owl\FormWidgets\HasMany;

use Exception;
use Backend\Classes\FormField;
use Backend\Classes\FormWidgetBase;
use Twig_Environment;
use Twig_Loader_Array;

class Widget extends FormWidgetBase {

    /**
     * {@inheritDoc}
     */
    public $defaultAlias = 'owl-hasmany';

    /**
     * @var Model   Related model
     */
    public $relatedModel;

    /**
     * Load the related model
     */
    public function init()
    {
        // Make sure the relationship exists
        if (!isset($this->model->hasMany[$this->fieldName]))
            throw new Exception('Unknown model relationship "'.$this->fieldName.'".');

        // Make sure the default style or a custom partial was defined
        if (!isset($this->config->default) && !isset($this->config->partial))
            throw new Exception('Default properties or a custom partial must be defined.');

        $this->relatedModel = new $this->model->hasMany[$this->fieldName][0];
    }

    /**
     * Render the widget
     * @return  $this->makePartial()
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('widget');
    }

    /**
     * {@inheritDoc}
     */
    public function loadAssets()
    {
        $this->addCss('css/hasmany.css');
        $this->addJs('js/sortable.min.js');
        $this->addJs('js/jquery-binding.js');
        $this->addJs('js/hasmany.js', 'core');
    }

    /**
     * Loads relationship
     */
    private function loadRelationship()
    {
        // Explode model path for determining plugin path
        $fieldName = $this->fieldName;
        $parts = explode('\\', strtolower($this->model->hasMany[$fieldName][0]));

        // Load relationship model, and configure the widget
        $relationship = $this->makeConfig("@/plugins/$parts[0]/$parts[1]/models/$parts[3]/fields.yaml");
        $relationship->model = $this->relatedModel;
        $relationship->alias = $this->alias.$fieldName;

        // Load form header
        if (isset($this->config->formLabel))
            $this->vars['formLabel'] = htmlspecialchars($this->config->formLabel);
        else {
            $modelName = explode('\\', get_class($this->relatedModel));
            $this->vars['formLabel'] = end($modelName);
        }

        return $relationship;
    }

    /**
     * {@inheritDoc}
     */
    public function prepareVars()
    {
        $this->vars['alias'] = $this->alias;

        $relationship = $this->loadRelationship();
        $this->vars['formWidget'] = $this->makeWidget('Backend\Widgets\Form', $relationship);

        $partials = [];
        $fieldName = $this->fieldName;
        foreach ($this->model->$fieldName as $item)
            $partials[] = $this->loadItemPartial($item);
        $this->vars['items'] = $partials;

        // Add button icon and label
        $this->vars['addIcon'] = isset($this->config->addIcon)
            ? $this->config->addIcon
            : 'icon-plus';
        $this->vars['addLabel'] = isset($this->config->addLabel)
            ? $this->config->addLabel
            : 'Add '.$this->vars['formLabel'];

        $this->vars['default'] = isset($this->config->default);

        // Load config
        $config['sortable'] = isset($this->config->sortColumn);
        $this->vars['config'] = htmlspecialchars(json_encode($config));
    }

    /**
     * Renders the related form widget
     * @return  array
     */
    public function onRenderForm()
    {
        // Load and popupate the related model
        $relationship = $this->loadRelationship();
        if ($data = post(':model')) {
            foreach ($data as $key => $value)
                $relationship->model->$key = $value;
        }

        $formWidget = $this->makeWidget('Backend\Widgets\Form', $relationship);

        return $this->makePartial('popup', ['formWidget' => $formWidget]);
    }

    /**
     * Validate the related model and return it's json data
     * @return  array | bool (false)
     */
    public function onProcessForm()
    {
        // Find or new the model
        $model = ($id = post('id')) && $id
            ? $this->relatedModel->findOrNew($id)
            : new $this->relatedModel;

        // Popuplate and validate the model with our form data
        foreach (post() as $key => $value) {
            if ($key == 'id' || $key == 'created_at' || $key == 'updated_at')
                continue;
            $model->$key = $value;
        }
        $model->validate();

        // Render the partial and return it as our list item
        return [
            'item' => $this->loadItemPartial($model)
        ];
    }

    /**
     * Loads an item partial
     * @param   Model   $item
     * @return  string
     */
    private function loadItemPartial($item)
    {
        // Convert our item data to a html-safe json object
        $data = [];
        foreach ($item->toArray() as $key => $value) {
            // If the item is json, convert it to an array and filter empty values
            $jsonArray = json_decode($value, true);
            if (json_last_error() == JSON_ERROR_NONE && is_array($jsonArray))
                $value = array_filter($jsonArray);

            $data[$key] = $value;
        }
        $this->vars['modelData'] = htmlspecialchars(json_encode($data));

        $partialPath = isset($this->config->default)
            ? 'item'
            : $this->config->partial;

        $loader = new Twig_Loader_Array([
            'item' => $this->makePartial($partialPath, ['item' => $item])
        ]);

        $twig = new Twig_Environment($loader);

        return $this->makePartial('input', ['item' => $item])
            .$twig->render('item', $item->toArray());
    }

    /**
     * Attach deferred bindings and execute deferred deletes
     * @param   null        $value
     * @return  FormField
     */
    public function getSaveValue($value)
    {
        $formData = post($this->alias);

        // Create new models with deferred bindings, or update existing models
        $fieldName = $this->fieldName;
        if (isset($formData['models']) && count($formData['models'])) {
            foreach ($formData['models'] as $i => $data) {
                $data = json_decode($data, true);
                $model = isset($data['id'])
                    ? $this->relatedModel->findOrNew($data['id'])
                    : new $this->relatedModel;

                foreach ($data as $key => $value) {
                    if ($key == 'id' || $key == 'created_at' || $key == 'updated_at')
                        continue;

                    $model->$key = $value;
                }

                if (isset($this->config->sortColumn)) {
                    $sortColumn = $this->config->sortColumn;
                    $model->$sortColumn = $i;
                }

                $model->save();

                if (!isset($data['id']))
                    $this->model->$fieldName()->add($model, $this->sessionKey);
            }
        }

        // Deferred deletes
        $deleteIds = json_decode($formData['delete'], true);
        if (count($deleteIds)) {
            $this->model->bindEvent('model.afterSave', function() use ($deleteIds) {
                $this->relatedModel->whereIn('id', $deleteIds)->delete();
            });
        }

        return FormField::NO_SAVE_DATA;
    }

}
