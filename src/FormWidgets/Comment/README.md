# Comment
A simple placeholder widget for [OctoberCMS](http://octobercms.com).

### Installation
To install the Comment widget, add the following to your plugin's ```composer.json``` file.
```json
"require": {
    "owl/comment": "~1.0"
}
```
Next, register the widget in your plugin's ```Plugin.php``` file.
```php
public function registerFormWidgets()
{
    return [
        'Owl\FormWidgets\Comment\Widget' => [
            'label' => 'Comment',
            'alias' => 'owl-comment'
        ],
    ];
}
```

### Usage
The comment widget serves no form function, it exists solely to place extra text in your form.
```yaml
comment:
    type: owl-comment
    label: Hello world
    comment: >
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla porttitor
        imperdiet elit, sed pellentesque eros. Morbi blandit elit a turpis 
        pellentesque tincidunt.
```