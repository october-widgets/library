# Address
Address widget for [OctoberCMS](http://octobercms.com).

![Packagist](https://img.shields.io/packagist/dt/owl/address.svg)

### Installation
To install the Address widget, add the following to your plugin's ```composer.json``` file.
```json
"require": {
    "owl/address": "~1.0@dev"
}
```
Next, register the widget in your plugin's ```Plugin.php``` file.
```php
public function registerFormWidgets()
{
    return [
        'Owl\FormWidgets\Address\Widget' => [
            'label' => 'Address',
            'alias' => 'owl-address'
        ],
    ];
}
```

### Usage
Usage documentation coming soon...
