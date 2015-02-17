/**
 * October Widget Library - Hasmany
 */
+function ($) { 'use strict'
    var HasMany = function (el, options) {
        this.$el = $(el)
        this.options = options

        this.alias          = this.$el.data('alias')
        this.config         = this.$el.data('config')
        this.$listContainer = this.$el.find('[data-control="list-container"]')
        this.$list          = this.$el.find('[data-control="list"]')
        this.$addItem       = this.$el.find('[data-control="add-model"]')
        this.$template      = this.$el.find('[data-control="template"]')
        this.$deleteBox     = this.$el.find('[data-control="delete-box"]')

        this.init()
    }

    /**
     * Initialize widget
     */
    HasMany.prototype.init = function() {
        var self = this

        // Attach sortable
        if (this.config.sortable) {
            this.$list.sortable({
                animation: 350,
                handle: '.sort-handle',
                onStart: function () {
                    self.$list.addClass('sorting')
                },
                onEnd: function () {
                    self.$list.removeClass('sorting')
                },
            })
        }

        // Attach item and delete click handlers
        this.$list.unbind().on('click', '[data-control="delete"]', function() {
            self.deleteItem($(this).closest('li'))
            return false
        }).on('click', 'li[data-control="item"]', function() {
            self.popup($(this))
            return false
        })

        // Handle add-model clicks
        this.$addItem.unbind().on('click', function() {
            var $item = self.addItem()
            self.popup($item, true)
            return false
        })
    }

    /**
     * Gets a model ID from a list item
     * @param   <li>
     * @return  integer
     */
    HasMany.prototype.getModelId = function ($item) {
        var model = {}
        if (model = $item.find('[data-control="model"]').val()) {
            model = JSON.parse(model)
            if (typeof model.id != 'undefined')
                return model.id
        }
        return 0
    }

    /**
     * Append a new list item, and pass to popup()
     */
    HasMany.prototype.addItem = function() {
        this.$list.append('<li data-control="item" style="display:none"></li>')
        return this.$list.find('li').last()
    }

    /*
     * Delete a list item
     * @param   <li>    $item
     */
    HasMany.prototype.deleteItem = function($item) {
        var self = this,
            modelId = this.getModelId($item),

            // Saved records are deferred, unsaved records are deleted immediately
            confirmationMsg = modelId !== 0
                ? 'Delete this record upon saving?'
                : 'Delete this unsaved record?',
            successMsg = modelId !== 0
                ? 'The record will be deleted upon saving.'
                : 'The record has been deleted.'

        $item.addClass('delete-highlight')

        sweetAlert({
            title: confirmationMsg,
            showCancelButton: true,
            confirmButtonText: 'Confirm',
        }, function(isConfirm){
            if (isConfirm) {
                $.oc.flashMsg({
                    text: successMsg,
                    'class': 'success',
                    'interval': 3
                })
                $item.slideUp('fast', function() {
                    $(this).remove()
                    if (modelId) {
                        var deleteJson = JSON.parse(self.$deleteBox.val())
                        deleteJson.push(modelId)
                        self.$deleteBox.val(JSON.stringify(deleteJson))
                    }
                })
            } else {
                $item.removeClass('delete-highlight')
            }
        })
    }

    /**
     * Open a popup form
     */
    HasMany.prototype.popup = function($item, newModel) {
        var self = this,
            model = {},
            modelId = this.getModelId($item)

        if (model = $item.find('[data-control="model"]').val())
            model = JSON.parse(model)

        this.itemSaved = false

        // Popup opened
        $item.one('show.oc.popup', function(e) {
            self.openPopup(e, modelId, $item)
            return false
        })

        // Popup closed
        $item.one('hide.oc.popup', function() {
            self.hidePopup($item, newModel)
            return false
        })

        // Popup settings
        var popSettings = {
            placement: 'center', modal: true,
            closeOnPageClick: true,
            highlightModelTarget: true,
            useAnimation: true,
            width: 600
        }

        // If this is a new model, create a popup from the template. Otherwise,
        // make an ajax request to render the popup with the supplied model data
        if (newModel) {
            $item.popup({
                content: this.$template.html().replace(new RegExp('script&gt;', 'g'), 'script>'),
                placement: popSettings.placement,
                modal: popSettings.modal,
                closeOnPageClick: popSettings.closeOnPageClick,
                highlightModelTarget: popSettings.highlightModelTarget,
                useAnimation: popSettings.useAnimation,
                width: popSettings.width
            })
        } else {
            $item.popup({
                extraData: {':model': model},
                handler: this.alias + '::onRenderForm',
                placement: popSettings.placement,
                modal: popSettings.modal,
                closeOnPageClick: popSettings.closeOnPageClick,
                highlightModelTarget: popSettings.highlightModelTarget,
                useAnimation: popSettings.useAnimation,
                width: popSettings.width
            })
        }

        return false
    }

    /**
     * Handles popup opened events
     * @param   event   e
     * @param   integer modelId
     * @param   <li>    $item
     */
    HasMany.prototype.openPopup = function(e, modelId, $item) {
        var self = this

        // Capture the container and form
        self.$popupContainer = $(e.relatedTarget)
        self.$popupForm = self.$popupContainer.find('form')

        $(document).trigger('render')

        // Attach a save handler to the "apply" button
        $('button[data-control="apply-btn"]', self.$popupContainer).on('click', function() {
            self.applyChanges(modelId, $item)
            return false
        })

        // Fire opened event
        $(document).trigger('owl.hasmany.opened', {
            alias: self.alias,
            item: $item,
            form: self.$popupForm
        })
    }

    /**
     * Apply changes to an item form
     * @param   integer modelId
     * @param   <li>    $item
     */
    HasMany.prototype.applyChanges = function(modelId, $item) {
        var self = this
        var $loadingIndicator = self.$popupContainer.find('[data-control="loading-indicator"]'),
            $modalFooter = self.$popupContainer.find('.modal-footer')

        $modalFooter.addClass('in-progress')
        $loadingIndicator.loadIndicator({
            text: 'Applying...'
        })

        self.$popupForm.request(self.alias + '::onProcessForm', {
            data: {
                id: modelId
            },
            success: function(data) {
                self.itemSaved = true
                $item.html(data.item)
                self.$popupContainer.trigger('close.oc.popup')
                self.$el.trigger('change')
            },
            complete: function() {
                $modalFooter.removeClass('in-progress')
                $loadingIndicator.loadIndicator('hide')
            }
        })
    }

    /**
     * Handles popup closed events
     */
    HasMany.prototype.hidePopup = function($item, newModel) {
        var self = this

        // Remove new un-saved items
        if (newModel && !self.itemSaved)
            $item.remove()

        $item.css('display', '')

        // Prevent dom pollution
        $('span[role="status"]').remove()

        // Fire closed event
        $(document).trigger('owl.hasmany.closed', {
            alias: self.alias,
            item: $item,
            form: self.$popupForm
        })
    }

    /*
     * Bind and construct non-conflicting hasmany
     */
    var old = $.fn.HasMany

    $.fn.HasMany = function (config) {
        return new HasMany($(this), config)
    }

    $.fn.HasMany.Constructor = HasMany

    $.fn.HasMany.noConflict = function () {
        $.fn.HasMany = old
        return this
    }

}(window.jQuery)
