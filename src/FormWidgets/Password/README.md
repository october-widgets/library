# Password
Hides input value while element is blurred.

![Packagist](https://img.shields.io/packagist/dt/owl/password.svg)

### Installation
To install the Password widget with your plugin, add the following to your plugin's ```composer.json``` file.

```json
"require": {
    "owl/password": "~1.0@dev"
}
```
Next, register the widget in your plugin's ```Plugin.php``` file.
```php
public function registerFormWidgets()
{
    return [
        'Owl\FormWidgets\Password\Widget' => [
            'label' => 'Password',
            'code'  => 'owl-password'
        ],
    ];
}
```

### Usage
To use the Password widget, simply declare a field type as ```owl-password```
```yaml
password:
    label: Password
    type: owl-password
```
