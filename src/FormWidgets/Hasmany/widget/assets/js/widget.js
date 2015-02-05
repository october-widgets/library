/*
 * HasMany widget
 * https://github.com/october-widgets/hasmany
 */
+function ($) { "use strict";

    /**
     * Constructor
     */
    var HasManyEditor = function (el, sortable) {
        this.$el = $(el)
        this.$list      = this.$el.find('[data-hasmany-list]')
        this.$template  = this.$el.find('script[data-hasmany-template]')
        this.$editor    = this.$el.find('script[data-hasmany-editor]')
        this.validation = this.$el.data('validation')
        this.sortable   = sortable
        this.init()
    }

    /**
     * Listen for editor events
     */
    HasManyEditor.prototype.init = function () {
        var self = this

        this.enableSorting()

        // Delete an item
        this.$el.on('click', 'a[data-control="remove-item"]', function(e) {
            self.removeItem($(this))
            return false
        })

        this.$el.on('click', '*[data-control="sort-handle"]', function(e) {
            e.preventDefault()
            return false
        })

        // Open an existing item
        this.$el.on('click', 'li[data-hasmany-item]', function(e) {
            self.openEditor($(this))
            return false
        })

        // Add a new item
        this.$el.on('click', 'a[data-control="add-item"]', function(e) {
            self.addItem(e)
            return false
        })
    }

    /**
     * Enables related model sorting
     */
    HasManyEditor.prototype.enableSorting = function () {
        if (this.sortable) {
            this.$list.sortable('destroy')
            this.$list.sortable({
                forcePlaceholderSize: true
            });
        }
    }

    /**
     * Add a new item to the list
     */
    HasManyEditor.prototype.addItem = function (target) {

        // Add a new item to the list
        this.$list.append(this.$template.html())

        // Open the editor and set the context to new
        this.openEditor(this.$list.find('li').last(), true)

        this.enableSorting()
    }

    /**
     * Removes an item from the list
     */
    HasManyEditor.prototype.removeItem = function (target) {
        sweetAlert({
            title: "Do you really want to delete this item?",
            showCancelButton: true,
            confirmButtonText: "Confirm"
        }, function(){
            $(target).closest('li[data-hasmany-item]').remove()
            $.oc.flashMsg({
                text: 'The item will be deleted upon saving.',
                'class': 'success',
                'interval': 3
            })
            return false
        })        
    }

    /**
     * Loads item properties into the popup form
     */
    HasManyEditor.prototype.loadProperties = function ($item) {
        var self = this,
            properties = $item.data('properties')

        if (!properties)
            return false

        $.each(properties, function(propertyName) {
            var value = this,
                $field = self.$popupForm.find('*[name="' + propertyName + '"]')
            if ($field) {
                if ($field.is(':checkbox')) {
                    $field.prop('checked', value)
                } else
                    $field.val(value)
            }
        })
    }

    /**
     * Open the editor
     */
    HasManyEditor.prototype.openEditor = function ($item, newItem) {
        var self = this
        this.itemSaved = false

        // Render the popup
        $item.one('show.oc.popup', function(e){
            // Populate and render the popup
            self.$popupContainer = $(e.relatedTarget)
            self.$popupForm = self.$popupContainer.find('form')

            self.loadProperties($item)
            $(document).trigger('render')

            // Catch all other enter-key events
            self.$popupContainer.keypress(function(e) {
                var $focused = $(':focus');
                if(e.which == 13 && !$focused.is('textarea') && $focused.data('control') != 'tagbox-input') {
                    e.preventDefault()
                    self.validateAndSave($item)
                    return false
                }
            })

            // Listen for any other submission events
            self.$popupForm.on('submit', function(e) {
                e.preventDefault()
                self.applyChanges($item)
                return false
            })

            // Attach a save handler to the "apply" button
            $('button[data-control="apply-btn"]', self.$popupContainer).on('click', function() {
                self.validateAndSave($item)
                return false
            })

            return false
        })

        // Close the editor
        $item.one('hide.oc.popup', function() {
            // Remove new un-saved items
            if (newItem && !self.itemSaved)
                $item.remove()

            // Prevent dom pollution
            $('span[role="status"]').remove()
            return false
        })

        // The editor has script tags cleaned to prevent nesting. Before opening
        // the pop up, we have to convert them back.
        var editorHtml = this.$editor.html().split('script&gt;').join('script>')

        // Open the editor
        $item.popup({
            content: editorHtml,
            placement: 'center',
            modal: true,
            closeOnPageClick: true,
            highlightModalTarget: true,
            useAnimation: true,
            width: 600
        })

        return false
    }

    /**
     * Validate the related model, and calls applyChanges if it passes
     */
    HasManyEditor.prototype.validateAndSave = function ($item) {
        var self = this,
            buttonContainer = this.$popupContainer.find('.modal-footer');
        
        buttonContainer.loadIndicator({text: 'Applying changes...'})

        // Validate the related model
        if (this.validation !== undefined) {
            this.$popupForm.request(this.validation, {
                success: function(data) {
                    self.applyChanges($item)
                },
                complete: function() {
                    buttonContainer.loadIndicator('hide')
                }
            })
        }
        
        // No validation required
        else this.applyChanges($item)
    }

    /**
     * Apply changes
     */
    HasManyEditor.prototype.applyChanges = function ($item) {
        var self = this,
            data = {},
            propertyNames = this.$el.data('properties'),
            original = $item.data('properties')

        // Loop through form items and build data array
        $.each(original, function(key, value) {
            if (key != 'created_at' && key != 'updated_at')
                data[key] = value
        })
        $.each(propertyNames, function() {
            var propertyName = this,
                $input = self.$popupForm.find('*[name="' + propertyName + '"]').not('[type=hidden]')
            if ($input.is(':checkbox')) {
                data[propertyName] = $input.is(':checked') ? 1 : 0
            } else {
                var value = $input.val()
                if (value !== '' && value !== undefined)
                    data[propertyName] = value
            }
        })

        // Reset and parse the new partial
        var template = twig({
            autoescape: true,
            data: $(this.$template.html()).html()
        })
        $item.html(template.render(data));
        $item.data('properties', data)
        $item.find('input[data-properties]').val(JSON.stringify(data))

        // Mark the item as saved and close the popup
        this.itemSaved = true
        this.$popupContainer.trigger('close.oc.popup')
        this.$el.trigger('change')
        return false
    }

    var old = $.fn.hasManyEditor

    /**
     * Bind the container to our editor
     */
    $.fn.hasManyEditor = function (alias, sortable) {
        return new HasManyEditor( $('div[data-alias="' + alias + '"]'), sortable )
    }

    $.fn.hasManyEditor.Constructor = HasManyEditor

    // MENUITEMSEDITOR NO CONFLICT
    // =================

    $.fn.hasManyEditor.noConflict = function () {
        $.fn.hasManyEditor = old
        return this
    }

}(window.jQuery);