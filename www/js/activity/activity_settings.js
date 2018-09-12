/**
 * Created by kostet on 21.07.2015.
 */
ActivitySettings = function(config) {
    // configurable {
    // }
    this.modal = '';
    this.show_url = '';
    this.apply_url = '';

    $.extend(this, config);

    this.activity_id = 0;
    this.dealer_id = 0;
}

ActivitySettings.prototype = {
    start: function() {
        this.initEvents();

        return this;
    },

    initEvents: function() {
        var self = this;

        $(document).on('click', '.action-show-activity-settings', $.proxy(this.onShowActivitySettings, this));
        $(document).on('click', '#btDoApplySettingsData', $.proxy(this.onActivitySettingsApply, this))
    },

    onShowActivitySettings: function(e) {
        this.activity_id = $(e.target).data('activity-id');
        this.dealer_id = $(e.target).data('dealer-id');

        $.post(this.show_url,
            {
                activityId : this.activity_id,
                dealerId : this.dealer_id
            },
            $.proxy(this.onShowModalDataResult, this));
    },

    onShowModalDataResult: function(result) {
        this.getModalContentContainer().empty().html(result);
        this.getModal().modal('show');
    },

    onActivitySettingsApply: function(e) {
        var $bt = $(e.target);

        $.post(this.apply_url,
            {
                complete: $('#sbActivityStatus').val(),
                alwaysOpen : $('#chAlwaysOpen').is(':checked') ? 1 : 0,
                quarter: $('#sbQuarter').val(),
                activityId : this.activity_id,
                dealerId : this.dealer_id
            },
            $.proxy(this.onApplyActivitySettingsResult, this));
    },

    onApplyActivitySettingsResult: function() {
        this.getModal().modal('hide');

        window.location.reload();
    },

    getModal: function() {
        return $(this.modal);
    },

    getModalContentContainer: function() {
        return $('.modal-content-container', this.getModal());
    }
}