# GeoAddress
GeoAddress widget for [OctoberCMS](http://octobercms.com).

![Packagist](https://img.shields.io/packagist/dt/owl/geoaddress.svg)

### Installation
To install the GeoAddress widget, add the following to your plugin's ```composer.json``` file.
```json
"require": {
    "owl/geoaddress": "~1.0@dev"
}
```
Next, register the widget in your plugin's ```Plugin.php``` file.
```php
public function registerFormWidgets()
{
    return [
        'Owl\FormWidgets\GeoAddress\Widget' => [
            'label' => 'GeoAddress',
            'alias' => 'owl-geoaddress'
        ],
    ];
}
```

### Usage
Usage documentation coming soon...