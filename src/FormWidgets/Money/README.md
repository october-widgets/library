# Money
Money form widget for OctoberCMS.

![Packagist](https://img.shields.io/packagist/dt/owl/money.svg)

### Installation
To install the Money widget with your plugin, add the following to your plugin's ```composer.json``` file.

```json
"require": {
    "owl/money": "~1.0@dev"
}
```
Next, register the widget in your plugin's ```Plugin.php``` file.
```php
public function registerFormWidgets()
{
    return [
        'Owl\FormWidgets\Money\Widget' => [
            'label' => 'Money',
            'code' => 'owl-money'
        ],
    ];
}
```

### Usage
To use the Money widget, simply declare a field type as ```owl-money```
```yaml
price:
    label: Price
    type: owl-money
```
There are several parameters that can be used to customize the money widget. Defining a ```thousands``` or ```decimal``` will change the thousands and decimal characters. Defining a ```prefix``` or ```suffix``` string will allow you to add currency symbols before or after the input. Lastly, setting ```allowNegative``` to ```true``` will allow negative values to be submitted.

The below example will accept negative inputs with a USD prefix (```$ -1,234.56```)
```yaml
price:
    label: Price
    type: owl-money
    prefix: "$ "
    allowNegative: true
```

You may need to define the widget configuration dynamically. For example, your plugin might have a variable currency symbol. This can be achieved like so...
```php
Config::set('owl.formwidgets::money', [
    'prefix'        => '$ ',
    'placeholder'   => '$ 0.00',
]);
```
