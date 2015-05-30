+function ($) { "use strict";

    //
    // TreeSort
    //
    var TreeSort = function ($el) {
        var self = this;

        this.$el = $el;

        this.$el.unbind().on('click', function() {
            self.popup();
        });
    }

    //
    // Popup form
    //
    TreeSort.prototype.popup = function() {

        this.$el.on('show.oc.popup', function(e) {
            var $popup = $(e.relatedTarget),
                $list = $popup.find('ol[data-control="records"]').first();

            // Inline the list height to prevent the modal from
            // shrinking while categories are being re-ordered
            $list.css('height', $list.height());

            // Submit the request on apply button clicks
            $popup.find('button[data-control="apply-btn"]').on('click', function() {
                var $loadingIndicator = $popup.find('div.loading-indicator'),
                    treeData = [],
                    i = 0;

                // Cycle through the list items and create the array of
                // data to send to the ajax handler.
                $list.find('li').each(function() {
                    treeData[i] = {
                        id: parseInt($(this).data('record-id')) || null,
                        parent_id: parseInt($(this).parent().data('parent-id')) || null
                    };
                    i++;
                });

                $loadingIndicator.show();
                $.request('onUpdateTree', {
                    data: {
                        treeData: treeData,
                    },
                    success: function(data) {
                        this.success(data).done(function() {
                            $popup.trigger('close.oc.popup');
                            $(document).trigger('render');
                        });
                    },
                    complete: function(data) {
                        $loadingIndicator.hide();
                    }
                });
            });
        });

        // Trigger the popup
        this.$el.popup({
            handler: 'treesort::onLoadPopup'
        });
    }

    //
    // Attach TreeSort to the element
    //
    $.fn.TreeSort = function () {
        return new TreeSort(this);
    }

    $(document).on('render', function() {
        $('[data-control="treesort"]').TreeSort();
    });

}(window.jQuery);
