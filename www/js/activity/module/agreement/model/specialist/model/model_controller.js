AgreementModelSpecialistController = function (config) {
    // configurable {
    this.selector = '';
    this.load_url = '';
    this.decline_url = '';
    this.accept_url = '';
    this.accept_decline_url = '';
    this.decline_type = '';
    this.callbackLoadReport = null;

    this.max_file_size = 0;
    this.uploader_url = '';
    this.delete_temp_file_url = '';

    this.report_file_uploader = null;

    this.panel_type = '';

    this.addEvents({
        load: true
    });

    AgreementModelSpecialistController.superclass.constructor.call(this, config);

    this.decline_form = null;
    this.accept_form = null;
    this.accept_decline_form = null;

    this.id = 0;
}

utils.extend(AgreementModelSpecialistController, utils.Observable, {
    start: function () {
        this.initEvents();

        return this;
    },

    initEvents: function () {
        this.getValuesBlock().on('click', '.decline', $.proxy(this.onDecline, this));
        this.getValuesBlock().on('click', '.accept', $.proxy(this.onAccept, this));

        this.getValuesBlock().on('click', '[data-view="list"], [data-view="grid"]', $.proxy(this.onChangeViewType, this));
        /*this.getCancelDeclineButton().click($.proxy(this.onCancelDecline, this));
         this.getCancelAcceptButton().click($.proxy(this.onCancelAccept, this));

         this.getAcceptDeclineButton().click($.proxy(this.onAcceptDecline, this));
         this.getAcceptAcceptButton().click($.proxy(this.onAcceptAccept, this));*/
    },

    load: function (id) {
        this.showValues();
        //this.getDeclineForm().reset();

        this.id = id;
        $.post(this.load_url, {id: id}, $.proxy(this.onLoad, this));
    },

    showValues: function () {
        this.getValuesBlock().show();
        this.getDeclineBlock().hide();
        this.getAcceptBlock().hide();
    },

    showDeclinePanel: function () {
        this.getValuesBlock().hide();
        this.getDeclineBlock().show();
        this.getAcceptBlock().hide();
    },

    showAcceptPanel: function () {
        this.getValuesBlock().hide();
        this.getDeclineBlock().hide();
        this.getAcceptBlock().show();
    },

    showSpecialistsPanel: function () {
        this.getValuesBlock().hide();
        this.getDeclineBlock().hide();
        this.getAcceptBlock().hide();
    },

    isConcept: function () {
        return $('.model-data', this.getValuesBlock()).data('is-concept');
    },

    getStatus: function () {
        return $('.model-data', this.getValuesBlock()).data('model-status');
    },

    getCssStatus: function () {
        return $('.model-data', this.getValuesBlock()).data('css-status');
    },

    getDeclineForm: function () {
        if (!this.decline_form) {
            this.getDeclineFormEl().attr('action', this.decline_url);
            this.decline_form = new Form({
                form: this.getDeclineFormEl().getIdSelector(),
                onSuccess: $.proxy(this.onSuccessDecline, this)
            });
        }
        return this.decline_form;
    },

    getAcceptForm: function () {
        if (!this.accept_form) {
            this.getAcceptFormEl().attr('action', this.accept_url);
            /*this.accept_form = new AjaxForm({
             form: this.getAcceptFormEl().getIdSelector(),
             onSuccess: $.proxy(this.onSuccessAccept, this)
             });*/
            this.accept_form = new Form({
                form: this.getAcceptFormEl().getIdSelector(),
                onSuccess: $.proxy(this.onSuccessAccept, this)
            });
        }
        return this.accept_form;
    },

    getAcceptAcceptButton: function () {
        return $('.accept-accept-btn', this.getAcceptBlock());
    },

    getCancelAcceptButton: function () {
        return $('.cancel-btn', this.getAcceptBlock());
    },

    getAcceptModelField: function () {
        return $('input[name=id]', this.getAcceptDeclineFormEl());
    },

    getAcceptFormEl: function () {
        return $('form', this.getAcceptBlock());
    },

    getAcceptDeclineButton: function () {
        return $('.accept-decline-btn', this.getDeclineBlock());
    },

    getCancelDeclineButton: function () {
        return $('.cancel-btn', this.getDeclineBlock());
    },

    getDeclineModelField: function () {
        return $(':input[name=id]', this.getAcceptDeclineFormEl());
    },

    getDeclineFormEl: function () {
        return $('form', this.getDeclineBlock());
    },

    getValuesBlock: function () {
        return $('.values', this.getPanel());
    },

    getDeclineBlock: function () {
        return $('.decline-panel', this.getPanel());
    },

    getAcceptBlock: function () {
        return $('.accept-panel', this.getPanel());
    },

    getPanel: function () {
        return $(this.selector);
    },

    getAcceptDeclineForm: function() {
        if (!this.accept_decline_form && this.getAcceptDeclineFormEl().length != 0) {
            this.getAcceptDeclineFormEl().attr('action', this.accept_decline_url);

            this.accept_decline_form = new Form({
                form: this.getAcceptDeclineFormEl().getIdSelector(),
                onSuccess: $.proxy(this.onSuccessAcceptDecline, this)
            });
        }

        return this.accept_decline_form;
    },

    getAcceptDeclineFormEl: function() {
        return $('form', this.getPanel());
    },

    onSuccessAcceptDecline: function() {
        location.href = location.pathname + '?' + Math.random();
    },

    onLoad: function (data) {
        this.getValuesBlock().html(data);

        if (this.getAcceptDeclineForm() != null) {
            window.accept_decline_form = this.getAcceptDeclineForm()
            window.accept_report_form = this.getAcceptDeclineForm();

            window.decline_model_form = this.getAcceptDeclineForm();
            window.decline_report_form = this.getAcceptDeclineForm();
        }

        if (this.panel_type == 'model') {
            this.model_file_uploader = new JQueryUploader({
                file_uploader_el: '#agreement_comments_file',
                max_file_size: this.max_file_size,
                uploader_url: this.uploader_url,
                delete_temp_file_url: this.delete_temp_file_url,
                uploaded_files_container: '#model_files',
                uploaded_files_caption: '#model_files_caption',
                el_attach_files_click_bt: '#js-file-trigger-model',
                el_attach_files_model_field: '#agreement_comments_file',
                add_caption_br_tag: true,
                progress_bar: '#model-files-progress-bar',
                upload_file_object_type: 'model',
                upload_file_type: 'model_specialist_comments',
                upload_field: 'agreement_comments_file',
                scroller: '.scroller-model',
                scroller_height: 200,
                model_form: '#agreement-model-specialist-form'
            }).start();
            this.model_file_uploader.initScrollBar();
            //this.initScrollBar(this.model_file_uploader, 'scroller-model-files');
        } else if (this.panel_type == 'report') {
            this.report_file_uploader = new JQueryUploader({
                file_uploader_el: '#agreement_report_comments_file',
                max_file_size: this.max_file_size,
                uploader_url: this.uploader_url,
                delete_temp_file_url: this.delete_temp_file_url,
                uploaded_files_container: '#model_report_files',
                uploaded_files_caption: '#model_report_files_caption',
                el_attach_files_click_bt: '#js-file-trigger-model-report',
                el_attach_files_model_field: '#agreement_report_comments_file',
                add_caption_br_tag: true,
                progress_bar: '#report-files-progress-bar',
                upload_file_object_type: 'report',
                upload_file_type: 'report_specialist_comments',
                upload_field: 'agreement_comments_file',
                scroller: '.scroller-report',
                scroller_height: 200,
                model_form: '#agreement-model-report-specialist-form'
            }).start();
            this.report_file_uploader.initScrollBar();
            this.initScrollBar(this.report_file_uploader, 'scroller-specialist-report-files');
        }

        this.fireEvent('load', [this]);

        this.getDeclineModelField().val(this.id);
        this.getAcceptModelField().val(this.id);
    },

    onDecline: function () {
        this.getActionType().val(this.decline_type);
        this.getAcceptDeclineForm().send();
//        this.showDeclinePanel();

        return false;
    },

    onAccept: function () {
        this.getActionType().val('accept');
        this.getAcceptDeclineForm().send();
        //      this.showAcceptPanel();

        return false;
    },

    onCancelDecline: function () {
        this.showValues();

        return false;
    },

    onAcceptDecline: function () {
        this.getDeclineForm().send();

        return false;
    },

    onCancelAccept: function () {
        this.showValues();

        return false;
    },

    onAcceptAccept: function () {
        this.getAcceptForm().send();

        return false;
    },

    onSuccessDecline: function () {
        location.href = location.pathname + '?' + Math.random();
    },

    onSuccessAccept: function () {
        location.href = location.pathname + '?' + Math.random();
    },

    getActionType: function() {
        return $('input[name=action_type]', this.getPanel());
    },

    onChangeViewType: function(e) {
        var $from  = $(e.target),
            view = $from.data('view'),
            $parent = $from.parent(),
            parent_target = $parent.data('toggle');

        $('[data-toggled="' + parent_target + '"]').removeClass('is-list-mode is-grid-mode').addClass('is-' + view + '-mode');
    },

    initScrollBar: function(obj, cls) {
        var idx = 1, scrollers = [];

        if (obj != undefined) {
            while(1) {
                var scroller = $('.' + cls + '-' + idx);
                if (scroller.length == 0) {
                    break;
                }

                scrollers.push(scroller);
                idx++;
            }

            scrollers.forEach(function(scroller) {
                scroller.tinyscrollbar({size: 210, sizethumb: 41});
                setTimeout(function () {
                    scroller.tinyscrollbar_update(0);
                }, 500);
            });
        }
    }
});
