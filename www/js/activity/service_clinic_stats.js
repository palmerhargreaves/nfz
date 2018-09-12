/**
 * Created by kostet on 27.08.2015.
 */
ServiceClinicStats = function(config) {
    // configurable {
    // }
    this.modal = '';
    this.show_url = '';

    $.extend(this, config);

    this.activity_id = 0;
    this.quarter = 0;
}

ServiceClinicStats.prototype = {
    start: function() {
        this.initEvents();

        return this;
    },

    initEvents: function() {
        $(document).on('click', '#show-service-clinic-stats', $.proxy(this.onShowServiceClinicStats, this));
        $(document).on('click', '.bt-on-export-service-clinic-stats', $.proxy(this.onExportServiceClinicStats, this));
    },

    onShowServiceClinicStats: function(e) {
        $.post(this.show_url,
            { },
            $.proxy(this.onShowModalDataResult, this));
    },

    onShowModalDataResult: function(result) {
        this.getModalContentContainer().empty().html(result);
        this.getModal().krikmodal('show');
    },

    getModal: function() {
        return $(this.modal);
    },

    getModalContentContainer: function() {
        return $('.modal-service-clinic-stats-content-container', this.getModal());
    },

    onExportServiceClinicStats: function(e) {
        var $bt = $(e.target);

        this.activity_id = $bt.data('activity-id');
        this.quarter = $bt.data('quarter');

        $bt.hide();
        this.getImgLoader().show();
        $.post(
            $bt.data('url'),
            {
                activity: this.activity_id,
                quarter: this.quarter
            },
            $.proxy(this.onExportComplete, this));
    },

    getImgLoader: function() {
        return $('.img-export-service-clinic-loader-' + this.activity_id + '-' + this.quarter, this.getModal());
    },

    getBtExport: function() {
        return $('.bt-on-export-service-clinic-stats-' + this.activity_id + '-' + this.quarter, this.getModal());
    },

    onExportComplete: function(url) {
        this.getImgLoader().hide();
        this.getBtExport().show();

        window.location.href = url;
    }
}