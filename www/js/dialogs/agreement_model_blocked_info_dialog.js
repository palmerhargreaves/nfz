/**
 * Created by kostet on 19.04.2016.
 */

/**
 * Created by kostet on 21.07.2015.
 */
AgreementModelBlockedInfo = function(config) {
    // configurable {
    // }
    this.modal = '';
    this.show_url = '';

    $.extend(this, config);

    this.model_id = 0;
}

AgreementModelBlockedInfo.prototype = {
    start: function() {
        this.initEvents();

        return this;
    },

    initEvents: function() {
        var self = this;

        $(document).on('click', '.action-show-model-blocked-info', $.proxy(this.onShowModelBlockedInfo, this));
    },

    onShowModelBlockedInfo: function(e) {
        this.model_id = $(e.target).data('model-id');

        $.post(this.show_url,
            {
                model_id : this.model_id,
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