/**
 * Created by kostet on 21.07.2015.
 */
ModelLogs = function(config) {
    // configurable {
    // }
    this.modal = '';
    this.show_url = '';

    $.extend(this, config);

    this.model_id = 0;
}

ModelLogs.prototype = {
    start: function() {
        this.initEvents();

        return this;
    },

    initEvents: function() {
        var self = this;

        $(document).on('click', '.action-show-model-logs', $.proxy(this.onShowModelLogs, this));
    },

    onShowModelLogs: function(e) {
        this.model_id = $(e.target).data('object-id');
        $.post(this.show_url,
            {
                modelId : this.model_id
            },
            $.proxy(this.onShowModalDataResult, this));
    },

    onShowModalDataResult: function(result) {
        this.getModalContentContainer().empty().html(result);
        this.getModal().modal('show');
    },

    getModal: function() {
        return $(this.modal);
    },

    getModalContentContainer: function() {
        return $('.modal-content-container', this.getModal());
    }
}