# Hasmany
Has-many/belongs-to popup widget for [OctoberCMS](http://octobercms.com).

![Packagist](https://img.shields.io/packagist/dt/owl/hasmany.svg)

### Installation
To install the Hasmany widget, add the following to your plugin's ```composer.json``` file.
```json
"require": {
    "owl/hasmany": "~1.0@dev"
}
```
Next, register the widget in your ```Plugin.php``` file.
```php
public function registerFormWidgets()
{
    return [
        'Owl\FormWidgets\HasMany\Widget' => [
            'label' => 'Hasmany',
            'alias' => 'owl-hasmany'
        ],
    ];
}
```

### Usage
First things first, you'll need to have a pair of models related via a has-many / belongs-to relationship. From there, in your parent model's fields.yaml file use the relationship name as the field name, and ```owl-hasmany``` as the type.
```yaml
relationship:
    type: owl-hasmany
```

Next, you'll need to define the default parameters, or a custom partial. The default parameters will create a list that is very similar to the Sitemap plugin's UI. You may use basic twig markup inside these fields, variable names will reference model attributes. The ```icon``` option should be a valid [Font-Autumn](http://daftspunk.github.io/Font-Autumn/) icon class, or ```false```.
```yaml
relationship:
    type: owl-hasmany
    default:
        icon: icon-file-o
        label: "{{ name }}"
        comment: "{{ description }}"
```
To customize the widget appearance, you may also define a custom partial instead of the default.
```yaml
relationship:
    type: owl-hasmany
    partial: @/plugins/author/plugin/models/relationship/_partial.htm
```
There are a few additional parameters available to customize the widget's appearance. Defining a ```sortColumn``` enables drag-and-drop re-ordering. This value should reference the model's "order by" column name. Defining a ```formHeader``` will change the default header of popup windows. Defining an ```addLabel``` or ```addIcon``` will customize the appearance of the add button. ```addLabel```.

Javascript events will be triggered when a popup window is opened or closed. Listening for these events can be useful when you wish to show / hide fields based on form values.
```javascript
$(document).bind("owl.hasmany.opened", function(e, data) {
    // popup was opened
});
$(document).bind("owl.hasmany.closed", function(e, data) {
    // popup was closed
});
```
The ```data``` array will contain 3 keys. ```data.alias``` refers to the widget alias, ```data.item``` refers to the popup's cooresponding li element, and ```data.form``` references the popup form element.
