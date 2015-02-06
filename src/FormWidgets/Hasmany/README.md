# Hasmany
Has-many/belongs-to popup widget for [OctoberCMS](http://octobercms.com).

![Packagist](https://img.shields.io/packagist/dm/owl/hasmany.svg)

### Installation
To install the Hasmany widget, add the following to your plugin's ```composer.json``` file.
```json
"require": {
    "owl/hasmany": "~1.0"
}
```
Next, register the widget in your ```Plugin.php``` file.
```php
public function registerFormWidgets()
{
    return [
        'Owl\FormWidgets\Hasmany\Widget' => [
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

Next, you'll need to define the default parameters, or a custom partial. The default parameters will create a list that is very similar to the Sitemap plugin's UI. You may use basic twig markup inside these fields, variable names will reference model attributes. The ```icon``` option should be a valid [Font-Autumn](http://daftspunk.github.io/Font-Autumn/) icon class, or omitted.
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
To enable drag-and-drop reordering, define a ```sort_column``` that corresponds to the related model's "order by" attribute. You may also customize the appearance of the add button by specifying a ```add_label``` or ```add_icon```.