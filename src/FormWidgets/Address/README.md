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
            'code' => 'owl-address'
        ],
    ];
}
```

### Usage
You can add this widget to your forms simply by decalring the field type as `owl-address`.

```
address:
    label: Address
    type: owl-address
```
This allows the form field to query the google maps public API and predict the location using autocomplete.

You can customize it further by using field maps, so that it automatically fills other fields with values based on the selected place i.e. such as latitude, longitude, etc. To do this first declare the form field with the field maps.
```
address:
    label: Address
    type: owl-address
    fieldMap:
        latitude: latitude
        longitude: longitude
        city: city
        zip: zip
        country: country_code
        state: state_code
```

Now define fields which should get filled up with the result values. The field name should be same as the value specified in the fieldMap fields.
```
city:
    label: City
zip:
    label: Zip
country_code:
    label: Country
state_code:
    label: State
latitude:
    label: Latitude
longitude:
    label: Longitude
```
The widget automatically detects the presence of the fields and applies the correct values whenever a place is selected using the autocomplete.

Available mappings:

* street
* city
* zip
* state
* country
* country-long
* latitude
* longitude