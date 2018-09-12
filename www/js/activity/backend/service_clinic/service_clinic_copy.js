/**
 * Created by kostet on 11.01.2017.
 */
ServiceClinicCopy = function(config) {
    this.make_copy_url = '';
    this.on_get_activity_data_url = '';

    $.extend(this, config);

    this.btn_copy = undefined;
}

ServiceClinicCopy.prototype = {
    start: function() {
        this.initEvents();

        return this;
    },

    initEvents: function() {
        this.getFromActivity().change($.proxy(this.onGetActivityCopyData, this));

        $(document).on('click', '#btMakeCopy', $.proxy(this.onCopyStart, this));
    },

    onGetActivityCopyData: function(event) {
        $.post(this.on_get_activity_data_url, {
            activity: this.getFromActivity().val()
        }, $.proxy(this.onGetActivityDataSuccess, this));
    },

    onGetActivityDataSuccess: function(result) {
        this.getServiceClinitContainerForCopyData().html(result);
    },

    onCopyStart: function(event) {
        event.preventDefault();

        var $btn = $(event.target), from_activity = this.getFromActivity().val(), to_activity = this.getToActivity().val();

        if (from_activity == -1 || to_activity == -1) {
            this.showMsg('Для продолжения выберите активности.', 'error');
            return;
        }

        if (from_activity == to_activity) {
            this.showMsg('Выберите разные активности для копирования.', 'error');
            return;
        }

        var headers_ids = this.makeHeadersListIds(), fields_ids = this.makeFieldsListIds();

        if (headers_ids.length == 0 || fields_ids.length == 0) {
            this.showMsg('Выберите заголовки / поля для продолжения копирования.', 'error');
            return;
        }

        this.btn_copy = $btn;
        this.btn_copy.fadeOut();
        $.post(this.make_copy_url,
            {
                from_activity: this.getFromActivity().val(),
                to_activity: this.getToActivity().val(),
                headers_list_ids: headers_ids.join(':'),
                fields_list_ids: fields_ids.join(':')
            }, $.proxy(this.onMakeCopyResult, this));
    },

    onMakeCopyResult: function(result) {
        if (result.success) {
            this.showMsg(result.message);
        } else {
            this.showMsg(result.message, 'error');
            this.btn_copy.fadeIn();
        }
    },

    makeHeadersListIds: function() {
        return this.makeIds('.ch-statistic-header');
    },

    makeFieldsListIds: function() {
        return this.makeIds('.ch-statistic-field');
    },

    makeIds: function(field) {
        var ids = [];

        $(field).each(function(ind, el) {
            var $ch_el = $(el);

            if ($ch_el.is(':checked') && $ch_el != undefined) {
                ids.push($ch_el.data('id'));
            }
        });

        return ids;
    },

    getFromActivity: function() {
        return $('#sbFromActivity');
    },

    getToActivity: function() {
        return $('#sbToActivity');
    },

    getMakeCopyBtn: function() {
        return $('#btMakeCopy');
    },

    showMsg: function(msg, status) {
        this.getMsgContainer().removeClass('alert-info alert-error');

        if (status == 'error') {
            this.getMsgContainer().addClass('alert-error');
        }

        this.getMsgContainer().html(msg).fadeIn();
    },

    hideMsg: function() {
        this.getMsgContainer().fadeOut();
    },

    getMsgContainer: function() {
        return $('#msg-info-container');
    },

    getServiceClinitContainerForCopyData: function() {
        return $('#container-service-clinic-copy-data');
    }
}
