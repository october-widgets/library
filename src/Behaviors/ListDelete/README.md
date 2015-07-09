# List Delete Behavior
List delete behavior for [OctoberCMS](http://octobercms.com).

![Packagist](https://img.shields.io/packagist/dt/owl/list-delete.svg)

### Installation
To install the List Delete behavior, add the following to your plugin's ```composer.json``` file.
```json
"require": {
    "owl/list-delete": "~1.0@dev"
}
```

Next, implement the behavior in your controllers...
```php
public $implement = [
    'Backend.Behaviors.FormController',
    'Backend.Behaviors.ListController',
    'Owl.Behaviors.ListDelete.Behavior',    // <-- add this line
];
```

Then enable checkboxes in your ```config_list.yaml``` file...
```yaml
showCheckboxes: true    # <-- uncomment this line
```
And lastly, add a button to your `_list_toolbar.htm` file...
```html
<button
    class="btn btn-default oc-icon-trash-o"
    disabled="disabled"
    onclick="$(this).data('request-data', {
        checked: $('.control-list').listWidget('getChecked')
    })"
    data-request="onDelete"
    data-request-confirm="<?= e(trans('backend::lang.list.delete_selected_confirm')) ?>"
    data-trigger-action="enable"
    data-trigger=".control-list input[type=checkbox]"
    data-trigger-condition="checked"
    data-request-success="$(this).prop('disabled', false)"
    data-stripe-load-indicator>
    <?= e(trans('backend::lang.list.delete_selected')) ?>
</button>
```

### Overriding default actions
If you need to perform additional delete logic, simply add the following method to your controller.
```php
public function overrideListDelete($record)
{
    $record->delete();
    // do whatever else you need to do
}
```

To override what should happen after your records are deleted, add the following method to your controller.
```php
public function afterListDelete()
{
    Flash::success('Things were deleted!');
}
```

By default, the list will be refreshed after a delete has occured. If you'd like to override this behavior, add the following method to your controller.
```php
public function overrideListRefresh()
{
    // do stuff here
}
```
