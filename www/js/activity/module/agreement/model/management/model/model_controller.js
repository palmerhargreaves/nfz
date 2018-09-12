AgreementModelManagementController = function (config) {
    // configurable {
    this.selector = '';
    this.load_url = '';
    this.decline_url = '';
    this.accept_url = '';
    this.send_to_specialists_url = '';
    this.accept_decline_url = '';
    this.decline_type = '';
    this.callbackLoadReport = null;
    // }

    this.max_file_size = 0;
    this.uploader_url = '';
    this.delete_temp_file_url = '';

    this.panel_type = '';

    this.model_file_uploader = null;
    this.model_report_file_uploader = null;

    this.addEvents({
        load: true
    });

    AgreementModelManagementController.superclass.constructor.call(this, config);

    this.decline_form = null;
    this.accept_form = null;
    this.specialists_form = null;
    this.accept_decline_form = null;
}

utils.extend(AgreementModelManagementController, utils.Observable, {
    start: function () {
        this.initEvents();

        return this;
    },

    initEvents: function () {
        this.getValuesBlock().on('click', '.decline', $.proxy(this.onDecline, this));
        this.getValuesBlock().on('click', '.accept', $.proxy(this.onAccept, this));
        this.getValuesBlock().on('click', '.specialists', $.proxy(this.onSpecialists, this));
        this.getCancelDeclineButton().click($.proxy(this.onCancelDecline, this));
        this.getAcceptDeclineButton().click($.proxy(this.onAcceptDecline, this));
        this.getCancelAcceptButton().click($.proxy(this.onCancelAccept, this));
        this.getAcceptAcceptButton().click($.proxy(this.onAcceptAccept, this));

        this.getPanel().on('click', '.krik-select', $.proxy(this.onClickUsersList, this));

        this.getPanel().on('click', 'input[name=designer_approve]', $.proxy(this.onDesignerApproveClick, this));
    },

    load: function (id, callback) {
        this.showValues();
        this.callbackLoadReport = callback;

        this.accept_decline_form = null;
        /*this.getDeclineForm().reset();
        this.getDeclineModelField().val(id);
        this.getAcceptModelField().val(id);
        this.getSpecialistsForm().resetWithId(id);*/

        $('.mod-popup-comment #model_id').val(id);

        $.post(this.load_url, { id: id }, $.proxy(this.onLoad, this));
    },

    showValues: function () {
        this.getValuesBlock().show();

        /*this.getDeclineBlock().hide();
        this.getAcceptBlock().hide();
        this.getSpecialistsBlock().hide();*/
    },

    showDeclinePanel: function (step) {
        this.getValuesBlock().hide();
        this.getDeclineBlock().show();

        this.getAcceptBlock().hide();
        this.getSpecialistsBlock().hide();

        if (step != undefined) {
            this.getDeclineModelStepField().val(step);
        }
    },

    showAcceptPanel: function () {
        this.getValuesBlock().hide();
        this.getDeclineBlock().hide();
        this.getAcceptBlock().show();
        this.getSpecialistsBlock().hide();
    },

    showSpecialistsPanel: function () {
        this.getValuesBlock().hide();
        this.getDeclineBlock().hide();
        this.getAcceptBlock().hide();
        this.getSpecialistsBlock().show();
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

    getAcceptAcceptButton: function () {
        return $('.accept-accept-btn', this.getAcceptBlock());
    },

    getCancelAcceptButton: function () {
        return $('.cancel-btn', this.getAcceptBlock());
    },

    getAcceptModelField: function () {
        return $(':input[name=id]', this.getAcceptFormEl());
    },

    getAcceptFormEl: function () {
        return $('form', this.getAcceptBlock());
    },

    getAcceptDeclineFormEl: function() {
        return $('form', this.getPanel());
    },

    getAcceptDeclineButton: function () {
        return $('.accept-decline-btn', this.getDeclineBlock());
    },

    getCancelDeclineButton: function () {
        return $('.cancel-btn', this.getDeclineBlock());
    },

    getDeclineModelField: function () {
        return $(':input[name=id]', this.getDeclineFormEl());
    },

    getDeclineModelStepField: function () {
        return $(':input[name=step]', this.getValuesBlock());
    },

    getDeclineFormEl: function () {
        return $('form', this.getDeclineBlock());
    },

    getValuesBlock: function () {
        return $('.values', this.getPanel());
    },

    getValuesElements: function () {
        return $('td.value', this.getPanel());
    },

    getLoadedValuesElements: function () {
        return $('td div.value', this.getPanel());
    },

    getDeclineBlock: function () {
        return $('.decline-panel', this.getPanel());
    },

    getAcceptBlock: function () {
        return $('.accept-panel', this.getPanel());
    },

    getSpecialistsBlock: function () {
        return $('.specialists-panel', this.getPanel());
    },

    getPanel: function () {
        return $(this.selector);
    },

    onLoad: function (data) {
        this.getValuesBlock().html(data);
        this.fireEvent('load', [this]);

        $.each(this.getValuesElements(), function (ind, el) {
            if ($.trim($(el).text()).length == 0) {
                $(el).closest('tr').hide();
            }
        });

        this.getLoadedValuesElements().show();

        if (this.getAcceptDeclineForm() != null) {
            window.accept_decline_form = this.getAcceptDeclineForm()
            window.accept_report_form = this.getAcceptDeclineForm();

            window.decline_model_form = this.getAcceptDeclineForm();
            window.decline_report_form = this.getAcceptDeclineForm();
        }

        $('.krik-select', this.getPanel()).krikselect();

        if (this.callbackLoadReport != undefined) {
            this.callbackLoadReport();
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
                upload_file_type: 'model_comments',
                upload_field: 'agreement_comments_file',
                scroller: '.scroller-model',
                scroller_height: 200,
                model_form: '#agreement-model-form'
            }).start();

            this.model_file_uploader.initScrollBar();
            this.initScrollBar(this.model_file_uploader, 'scroller-model-files');
        } else if (this.panel_type == 'report') {
            this.model_report_file_uploader = new JQueryUploader({
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
                upload_file_type: 'report_comments',
                upload_field: 'agreement_comments_file',
                scroller: '.scroller-report',
                scroller_height: 200,
                model_form: '#agreement-model-report-form'
            }).start();

            this.model_report_file_uploader.initScrollBar();
            this.initScrollBar(this.model_report_file_uploader, 'scroller-report-files');
        }
    },

    onDecline: function (e) {
        var step = $(e.target).data('step');

        if (step == undefined) {
            step = $(e.target).parent().data('step')
        }

        if (!this.isSpecialistChecked()) {
            return false;
        }

        if (this.confimAcceptDeclineModel($(e.target), 'отклонить')) {
            this.getActionType().val(this.decline_type);
            this.getDeclineModelStepField().val(step);

            this.getAcceptDeclineForm().send();
        }

        return false;
    },

    onAccept: function (e) {
        if (!this.isSpecialistChecked()) {
            return false;
        }

        if (this.confimAcceptDeclineModel($(e.target), 'согласовать')) {
            this.getActionType().val('accept');
            this.getAcceptDeclineForm().send();
        }

        return false;
    },

    confimAcceptDeclineModel: function($el, text) {
        var model_type = $el.data('model-type'), allow = false;

        if (model_type == undefined) {
            model_type = $el.parent().data('model-type');
        }

        if (model_type == 'model_record' || model_type == 'model_scenario') {
            if (model_type == 'model_scenario') {
                if (confirm('Вы действительно хотите ' + text + ' запись ?')) {
                    allow = true;
                }
            } else if(model_type == 'model_record') {
                if (confirm('Вы действительно хотите ' + text + ' сценарий ?')) {
                    allow = true;
                }
            }
        } else if (model_type == 'model_simple') {
            if (confirm('Вы действительно хотите ' + text + ' макет ?')) {
                allow = true;
            }
        } else if (model_type == 'report_accept') {
            if (confirm('Вы действительно хотите ' + text + ' отчет ?')) {
                allow = true;
            }
        } else if (model_type == 'report_decline') {
            if (confirm('Вы действительно хотите ' + text + ' отчет ?')) {
                allow = true;
            }
        }

        return allow;
    },

    isSpecialistChecked: function() {
        if ($('.check :input:checked', this.getSpecialistsPanelContainer()).length == 0 && this.getDesignerApprove().is(':checked')) {
            alert("Выберите хотябы одного специалиста");

            return false;
        }

        return true;
    },

    onSpecialists: function () {
        this.showSpecialistsPanel();

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
        var allow_accept = false;
        if (values != undefined) {
            if (values.step1 == 'accepted') {
                if (confirm('Вы действительно хотите согласовать запись ?')) {
                    allow_accept = true;
                }
            } else if(values.step1 != 'accepted' && values.step1 != 'none') {
                if (confirm('Вы действительно хотите согласовать сценарий ?')) {
                    allow_accept = true;
                }
            }
        } else if (confirm('Вы действительно хотите согласовать макет ?')) {
            allow_accept = true;
        }

        if (allow_accept) {
            this.getAcceptForm().send();
        }

        return false;
    },

    onCancelSendToSpecialists: function () {
        this.showValues();
    },

    getGroupCheckbox: function (id) {
        return $('[name="specialist[group][' + id + ']"]', this.getSpecialistsPanelContainer());
    },

    onClickUsersList: function (e) {
        var group_id = $(e.target).closest('.group-row').data('group');

        this.getGroupCheckbox(group_id).attr('checked', 'checked');
    },

    onSuccessDecline: function () {
        location.href = location.pathname + '?' + Math.random();
    },

    onSuccessAccept: function () {
        location.href = location.pathname + '?' + Math.random();
    },

    onSuccessAcceptDecline: function() {
        location.href = location.pathname + '?' + Math.random();
    },

    onSuccessSendToSpecialists: function () {
        location.href = location.pathname + '?' + Math.random();
    },

    onDesignerApproveClick: function(e) {
        this.getSpecialistsPanelContainer().slideToggle();
    },

    getSpecialistsPanelContainer: function() {
        return $('.specialists-panel-container', this.getPanel());
    },

    getDesignerApprove: function() {
        return $('input[name=designer_approve]', this.getPanel());
    },

    getActionType: function() {
        return $('input[name=action_type]', this.getPanel());
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
