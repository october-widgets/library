# Tagbox
Tag form widget for OctoberCMS.

### Installation
To install the Tagbox widget with your plugin, add the following to your plugin's ```composer.json``` file.
```json
"require": {
    "owl/tagbox": "~1.0"
}
```
Next, register the widget in your plugin's ```Plugin.php``` file.
```php
public function registerFormWidgets()
{
    return [
        'Owl\FormWidgets\Tagbox\Widget' => [
            'label' => 'Tagbox',
            'alias' => 'owl-tagbox'
        ],
    ];
}
```

### Usage
To use the Tagbox widget, simply declare a field type as ```owl-tagbox```
```yaml
tags:
    label: Tags
    type: owl-tagbox
```
If tags *are not* being stored through a related model, the model attribute must be [jsonable](http://octobercms.com/docs/database/model#attribute-modifiers). If tags *are* being stored through a related model, the ```getTagsAttribute``` and ```setTagsAttribute``` methods must be declared to process the relationship. These methods should return / accept an array of strings.

Lastly, there are two optional parameters that you may use to customize your widget. They are ```sortable``` and ```placeholder```. Sortable allows tags to be drag-and-drop sorted when set to true, and placeholder defines a custom placeholder for the input field.