/**
 * Created by kostet on 02.10.2015.
 */
ModelDiscussionCountLoad = function(config) {
    // configurable {
    // }
    this.load_url = '';
    this.designer_filter = false;

    $.extend(this, config);

}

ModelDiscussionCountLoad.prototype = {
    start: function() {
        this.initEvents();

        return this;
    },

    initEvents: function() {
        this.onLoadDiscussionCount();
    },

    onLoadDiscussionCount: function() {
        $.post(this.load_url, {
                models : this.getModelsList(),
                designer_filter: this.designer_filter
            },
            $.proxy(this.onLoadSuccess, this));
    },

    onLoadSuccess: function(data) {
        if(data.success) {
            $.each(data.data, function(ind, val) {
                if (val.count != 0) {
                    $('#agreement-models .message-model-' + ind).html(val.count).show();
                } else {
                    $('#agreement-models .message-model-' + ind).hide();
                }

                if (data.designer_filter) {
                    $('#agreement-models .darker-model-' + ind).css('background-color', 'rgb(233, 66, 66)');
                }
            });
        }
    },

    getModelsList: function() {
        var models = [];

        $('#agreement-models tr.model').each(function(ind, el) {
            models.push($(el).data('model'));
        });

        return models.join(':');
    },

}