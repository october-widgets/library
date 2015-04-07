# October Widget Library
A collection of form widgets for [October CMS](http://octobercms.com).

### Installing widgets
For installation instructions, see the individual widget repositories.

| Widget        | Used for...   |
| :------------ | :-------------|
| [Address](https://github.com/october-widgets/address)     | Google maps auto-complete for address inputs. |
| [Comment](https://github.com/october-widgets/comment)     | Adding information to forms, without creating extra widgets. |
| [Has Many](https://github.com/october-widgets/hasmany)    | Managing related models through a has-many/belongs-to relationship. |
| [Knob](https://github.com/october-widgets/knob)           | A touch friendly widget to handle numeric fields. |
| [Money](https://github.com/october-widgets/money)         | Accepting currency input. |
| [Password](https://github.com/october-widgets/password)   | Masking input values on blur. |
| [Tagbox](https://github.com/october-widgets/tagbox)       | Accepting an array of strings. |

### Installing the complete library
To install the the complete library, add the following to your plugin's ```composer.json``` file.
```json
"require": {
    "owl/library": "~1.0"
}
```
Next, register the widgets in your ```Plugin.php``` file. Examples demonstrating how to do this can be found in the individual repositories.

### Contributing
Have a widget that others might find useful? [Get in touch](http://octobercms.com/forum/post/october-widget-library)!
