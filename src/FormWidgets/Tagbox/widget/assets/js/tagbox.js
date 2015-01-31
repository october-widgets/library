/**
 * Tagbox 1.0
 * Beautifully simple tagging
 * 
 * Scott Bedard
 * http://scottbedard.net
 */
(function($){
$.widget('ui.tagbox', {

    /**
     * Happy customizing!
     */
    options : {

        // Custom name for input elements
        name: '',

        // Adds a tag when the element loses focus
        addOnBlur: true,

        // Any keypress matching 'blocked' will be prevented or removed
        blocked: /[^a-zA-Z0-9\-\s]+/g,  // This blocks special characters, excluding spaces and hyphens

        // Breaker keys will fire the _addTag() method
        breakers: [
            9,      // Tab
            13,     // Enter
            //186,  // Semi-colon
            188,    // Comma
            //190,  // Period
        ],

        // Clears the input box on duplicate entries
        clearNonUnique: false,

        // When pre-populating the form, tags are seperated by this value
        delimeter: ',',

        // On breaker 'filter' matches will be substituted for 'replace'
        //filter: false,
        //replace: false,
        filter: /(\s|\-)+/g,                // These two lines would connect words with a -
        replace: '-',
        
        // On breaker 'remove' matches will be removed
        remove: /(^-|-$)/g,             // This would remove trailing or leading hypens

        // How long to show .unique-flash class for duplicate tags
        flashDuration: 300,

        // Minimum & Maximum char length for tags
        minLength: 1,
        maxLength: 25,

        // Maximum number of tags, false will allow unlimitted tags
        maxTags: false,

        // Default placeholder, to use a custom set this to false or define the placeholder in the input
        placeholder: 'Enter tags...',

        // The text or image for the remove button
        removeButton: '<i class="icon-times"></i>',

        // Allows the list to be sorted
        sortable: false,

        // Options for jQuery UI Sortable
        // See: http://api.jqueryui.com/sortable/
        sortOptions: {
            // This keeps our tags inside their container
            containment: 'parent',

            // This prevents the input box from being sortable
            items: '.tagbox-tag',

            tolerance: 'pointer',
        },

        // Force tags to be unique
        unique: true,
        caseSensetive: false,

    },

    /**
     * _create()
     * Builds the widget and sets neccessary variables
     */
    _create : function() {

        // Set 'self' so we can reference our element from inside anonymous functions
        var self = this;

        // Define input
        this.element.addClass('tagbox-input');
        this.input = this.element;
        this._name = this.input.attr('id');

        // Set the default placeholder
        if (! this.input.attr('placeholder') && this.options.placeholder !== false) {
            this.input.attr('placeholder', this.options.placeholder);
        }

        // Build and define ul
        this.input.before($('<ul id="tagbox-' + this._name + '">'));
        this.ul = $('#tagbox-'+this._name);
        this.ul.addClass('tagbox-ul');

        // Move the input into the ul
        this.input.appendTo(this.ul);
        this.input.wrap('<li class="tagbox-li tagbox-li-input">');

        // Loop through initial tags and pass them to _addTag()
        if (this.input.data('tags')) {
            var initial = this.input.data('tags').split(this.options.delimeter);
            for (var i = 0; i < initial.length; i++) { 
                this._addTag(initial[i]);
            }
        }

        // Catch backspace / breakers and pass to _processBreaker()
        this.input.keydown(function(e){
            // Catch backspace
            if (e.keyCode == 8) self._processBackspace();

            // Catch breakers
            else if ($.inArray(e.keyCode, self.options.breakers) !== -1) self._processBreaker(e);
        });

        // Catch input blur
        this.input.blur(function() {
            // If addOnBlur is enabled, pass to _addTag()
            if (self.options.addOnBlur) {
                self._addTag(self.input.val());
            }
        });

        // Catch all other keypress events and pass to _processKeyPress()
        this.input.keypress(function (e) {
            self._processKeyPress(e);
        });

        // Catch .remove-button clicks, and remove the tag
        this.ul.on('click', '.tagbox-remove', function() {
            var tag = $(this).closest('li');
            self._processRemove(tag);
        });

        // Catch ul clicks and set focus to the input
        this.ul.click(function() {
            self.input.focus();
        });

    },

    _processRemove: function(tag) {
        tag.remove();

        // If we're below our maxTags length, make sure the input is visible
        if (this.options.maxTags === false || (this.ul.children('li').size() - 1) < (this.options.maxTags)) {
            this.input.show();
        }
    },

    /**
     * _processBackspace()
     * Handles backspace keydown
     */
    _processBackspace: function() {

        // If input isn't empty, abort method and let backspace act normally
        var value = this.input.val();
        if (value !== '') return;

        // Get the last tag (second to last <li> in the list)
        var target = this.ul.find('li:last-of-type').prev();

        // Check if target has the .tagbox-delete class
        if ( ! $(target).hasClass('tagbox-delete') ) {
            // Target doesn't have .tagbox-delete class, add it
            $(target).addClass('tagbox-delete');
        } else {
            // Target does have .tagbox-delete class, remove target
            $(target).remove();
        }

    },

    /**
     * _processBreaker( e )
     * Catches breakers and creates a new tag
     */
    _processBreaker: function(e) {

        // Prevent the default action
        e.preventDefault();

        // Send to _addTag
        this._addTag(this.input.val());

    },

    /**
     * _processKeyPress()
     * Handles all non-breakers and non-backspace keypress events
     */
    _processKeyPress: function(e) {

        // Capture the key that was pressed
        var keyCode = e.which || e.keyCode || e.charCode;
        var keyChar = String.fromCharCode(keyCode);

        // This fixes a backspace bug with Firefox
        if (keyCode == 8) return;

        // Make sure the keyChar doesn't match the blocked regex
        if (this.options.blocked && keyChar.match(this.options.blocked)) {
            // keyChar is blocked, prevent default
            e.preventDefault();
        }

        // Check if the tag is at the maxLength
        if (this.options.maxLength !== false && this.input.val().length >= this.options.maxLength) {
            // Tag is at it's limit, prevent default
            e.preventDefault();
        }

        // Everything checked out, last thing we need to do is make sure the .tagbox-delete class isn't still
        // being applied to the previous tag.
        this.ul.find('.tagbox-delete').removeClass('tagbox-delete');

    },

    /**
     * _addTag()
     * Processes input and inserts a new tag
     */
    _addTag: function(tag) {

        // Check if we're at our maxTags limit
        if (this.options.maxTags !== false && this.ul.children('li').size() > (this.options.maxTags)) {
            // There are too many tags, abort method
            return;
        }

        // Clean the tag, then pass it through our filter and remover
        tag = tag.replace(this.options.blocked, '')
                 .replace(this.options.filter, this.options.replace)
                 .replace(this.options.remove, '');

        // Make sure tag conforms to our minLength and maxLength
        if ((this.options.minLength !== false && tag.length < this.options.minLength) || 
            (this.options.maxLength !== false && tag.length > this.options.maxLength)) {
            // Tag is too small or too big, abort method
            return;
        }

        // Check if our tag needs to be unique
        if (this.options.unique) {
            var caseSensetive = this.options.caseSensetive;
            var flashDuration = this.options.flashDuration;
            var clearNonUnique = this.options.clearNonUnique;

            // Loop through existing tags and see if any match
            var unique = true;
            this.ul.find('li span.value').each(function(){
                // Our current tag, and the tag it's being compaired against
                var tagVal = tag;
                var tagCheck = $(this).html();

                // Transform both tags to lower case for case insensetive matching
                if (!caseSensetive) {
                    tagVal = tagVal.toLowerCase();
                    tagCheck = tagCheck.toLowerCase();
                }

                // Compair the tags
                if (tagVal == tagCheck) {
                    // We have a match, flash the existing tag
                    var existingTag = $(this).closest('li');
                    existingTag.addClass('tagbox-flash');
                    setTimeout(function() {
                        existingTag.removeClass('tagbox-flash');
                    },flashDuration);

                    // Set unique to false, and exit our loop
                    unique = false;
                    return;
                }
            });
            if ( ! unique ) {
                // Clear the input box if needed
                if (clearNonUnique === true) this.input.val('');

                // Tag needs to be unique, abort the method
                return;
            }
        }

        // Everything checked out, build the tag
        var li;
        if (this.options.sortable) {
            li = '<li class="tagbox-li tagbox-tag ui-sortable-handle">';
        } else {
            li = '<li class="tagbox-li tagbox-tag">';
        }

        // Set the input name
        if (this.options.name == '') {
            var inputName = this._name;
        } else {
            var inputName = this.options.name;
        }
        
        li += '<input type="hidden" name="' + inputName + '[]" value="' + tag + '">'
            + '<span class="value">' + tag + '</span>'
            + '<span class="remove-span"><a class="tagbox-remove">' + this.options.removeButton + '</a></span>'
            + '</li>';

        // Insert our new tag behind the input
        var input = this.input.closest('li');
        $(li).insertBefore(input);

        // Make list sortable if this is the first tag. We are initiating sortable here to
        // prevent the jumpy sorting that can happen when you initialize sortable on an empty list.
        if (this.options.sortable && ! this.ul.hasClass('ui-sortable')) {
            this.ul.sortable(this.options.sortOptions);
        }

        // If we're at our maxTags amount, hide the input and change the cursor attribute
        if (this.options.maxTags !== false && this.ul.children('li').size() > (this.options.maxTags)) {
            this.ul.css('cursor', 'context-menu');
            this.input.hide();
        }

        // Clear the input
        this.input.val('');

    }

});

})(jQuery);