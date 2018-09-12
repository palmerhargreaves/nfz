AgreementModelForm = function (config) {
    // configurable {
    this.add_url = '';
    this.cancel_url = '';
    this.cancel_scenario_url = '';
    this.cancel_record_url = '';
    this.delete_url = '';
    this.load_concept_cert_fields_url = '';
    this.load_model_block_url = '';
    this.load_record_block_url = '';
    this.dates_field_url = '';
    this.load_dates_and_certificates = '';
    this.delete_date_field = '';

    this.concept_type_id = 0;
    this.max_ext_files = 10;
    // }
    this.model_id = 0;

    this.model_file_uploader = undefined;
    this.concept_file_uploader = undefined;
    this.model_record_file_uploader = undefined;

    this.init_delete_files_event = false;

    AgreementModelForm.superclass.constructor.call(this, config);

    this.model_scenario_record = null;

    this.max_upload_files_count = $('body').data('max-files-upload-count');
    this.uploaded_files_count = 0;
    this.uploaded_record_files_count = 0;
    this.left_to_upload = 0;
    this.field_name = '';
    this.is_concept = undefined;

    this.MODEL_FILE_FIELD = 'model_file';
    this.MODEL_RECORD_FILE_FIELD = 'model_record_file';

    this.model_type_default_id = 0;
    this.values = undefined;

}

utils.extend(AgreementModelForm, AgreementModelBaseForm, {
    start: function () {
        AgreementModelForm.superclass.start.call(this);

        this.syncModelType();

        return this;
    },

    initEvents: function () {
        AgreementModelForm.superclass.initEvents.call(this);

        this.getTab().on('activated', $.proxy(this.onActivateTab, this));

        this.getModelTypeField().change($.proxy(this.onSelectModelType, this));
        this.getDraftButton().click($.proxy(this.onClickDraft, this));

        this.getCancelButton().click($.proxy(this.onClickCancel, this));
        this.getCancelButtonScenario().click($.proxy(this.onClickCancelScenario, this));
        this.getCancelButtonRecord().click($.proxy(this.onClickCancelRecord, this));

        this.getDeleteButton().click($.proxy(this.onDelete, this));

        ///this.getConceptAddFileLink().click($.proxy(this.onAddConceptModelFile, this));
        this.getNoModelChangesFieldValues().click($.proxy(this.onClickNoModelChange, this));

        this.getForm().on('click', this.getModelFileRemoveControl(), $.proxy(this.onRemoveModelFiles, this));

        this.getDatesPanelFirst().on('click', '.dates-add-field', $.proxy(this.onAddDatesField, this));

        /*Work with uploaded model files*/
        //Модуль инициализируется два раза, мы разрешаем инициализзацию только одном из модулей

        if (this.init_delete_files_event) {
            $(document).on('click', '.remove-uploaded-model-file, .remove-uploaded-model-file-category', $.proxy(this.onDeleteUploadedModelFile, this));
            $(document).on('click', '.remove-uploaded-model-record-file, .remove-uploaded-model-record-file-category', $.proxy(this.onDeleteUploadedModelRecordFile, this));
        }
    },

    reset: function () {
        AgreementModelForm.superclass.reset.call(this);

        this.setValue('model_type_id', this.getFirstModelTypeValue());
        this.splitPeriods();
        this.splitSizes();
        this.getDraftField().val('false');

        this.getCertificateDatePanel().show();
        this.getDatesPanel().show();
        this.getDatesPanels().remove();

        this.getNoModelChangesFieldValues().removeAttr('checked');
        this.getNoModelChangesFieldValues().removeAttr('disabled');

        this.model_scenario_record = undefined;
        this.values = undefined;

        this.getModelAddFilesButton().show();
        this.getModelAddRecordFilesButton().show();

        this.getFileLabel().html("<strong>Макет</strong><span class='upload-info'>(до 10 файлов, каждый весом не более 5 МБ)</span>");

        if (this.getModelUploader() != undefined) {
            this.getModelUploader().reset();
            this.getConceptUploader().reset();
            this.getModelRecordUploader().reset();
        }
    },

    onActivateTab: function () {
        if (this.model_file_uploader != undefined) {
            this.model_file_uploader.initScrollBar();
            this.concept_file_uploader.initScrollBar();
            this.model_record_file_uploader.initScrollBar();
        }
    },

    deleteModel: function () {
        $.post(this.delete_url, {
            id: this.getIdField().val()
        }, $.proxy(this.onDeleteSuccess, this));
    },

    showLoader: function () {
        this.getDraftButton().hide();

        AgreementModelForm.superclass.showLoader.call(this);
    },

    hideLoader: function () {
        this.getDraftButton().show();

        AgreementModelForm.superclass.hideLoader.call(this);
    },

    enableTypeSelect: function () {
        this.getModelTypeSelect().removeClass('inactive');
    },

    disableTypeSelect: function () {
        this.getModelTypeSelect().addClass('inactive');
    },

    enableModelTypeSelect: function () {
        this.getModelTypeSelect().removeClass('inactive input');
        this.getModelTypeSelect().next().hide();
    },

    disableModelTypeSelect: function () {
        this.getModelTypeSelect().addClass('inactive input');
        this.getModelTypeSelect().next().show();
    },

    syncModelType: function (values) {
        var model_type_id = this.getModelTypeField().val();

        if (this.isConceptMode()) {
            this._switchToConceptMode(values);
        }
        else {
            this._switchToModelMode(model_type_id);
        }

        $('.type-fields', this.getForm()).hide().find(':input').data('skip-validate', 'true');
        this.getModelTypeFieldBlocks(model_type_id).show().find(':input').data('skip-validate', 'false');

        //Установить для полей с типом Size значения по умолчанию если макет создается с помощью редактора макетов
        this.getModelTypeFieldBlocks(model_type_id)
            .find(':input.size-field')
            .each(function (ind, el) {
                if ($(el).data('value')) {
                    $(el).val($(el).data('value'));
                }
            });

        var editorLinkBlock = this.getModelEditorLinkBlock();
        editorLinkBlock.hide();

        if (values == undefined) {
            var tf_block_ind = 0;
            $.each(this.getModelTypeFieldBlocks(model_type_id), function (ind, el) {
                if ($(el).data('is-hide') && tf_block_ind++ > 2) {
                    $(el).hide();
                }
            });

            if (editorLinkBlock.data('link') != '')
                editorLinkBlock.show();
        }
        else {
            var elInd = 2;

            this.loadDatesAndCertificates(values);
            this.model_id = values.id;
            if (values.status == "accepted" || values.status == "wait" || values.status == "wait_specialist") {
                $('.add-child-field').show();
                if (values.css_status != 'pencil')
                    $('.add-child-field').hide();

                this.hideModelFieldBlocks(model_type_id);
                $.each(values, $.proxy(function (name, value) {
                    if (name == this.getModelTypeLabel(model_type_id) + "[place" + elInd + "]") {
                        if (value.length == 0) {
                            $("input[name*=place" + elInd + "]").closest('.type-fields-' + model_type_id).hide();
                        }

                        elInd++;
                    }

                }, this));
            }
            else if (values.status == "declined" || values.status == "not_sent" || values.status == "pencil") {
                $('.add-child-field').show();

                this.hideModelFieldBlocks(model_type_id);
                $.each(values, $.proxy(function (name, value) {
                    if (name == this.getModelTypeLabel(model_type_id) + "[place" + elInd + "]") {
                        if (value.length == 0) {
                            $("input[name*=place" + elInd + "]").closest('tr.type-fields-' + model_type_id).hide();
                        }
                        elInd++;
                    }

                }, this));

                this.disableModelTypeSelect();
                if (values.css_status == 'pencil') {
                    this.enableModelTypeSelect();
                }
            }

            if (values.editor_link.length != 0) {
                editorLinkBlock.find('a').attr('href', values.editor_link);
                editorLinkBlock.show();
            }

            this.changeModelPeriod(values, model_type_id);
        }

    },

    splitPeriods: function () {
        this.getPeriodGroups().each(function () {
            var $value_field = $('[type=hidden]', this);
            var value_field_name = $value_field.attr('name');
            var $start_period_field = $('[name="_' + value_field_name + '[start]"]', this);
            var $end_period_field = $('[name="_' + value_field_name + '[end]"]', this);
            var period = $value_field.val().split('-');
            $start_period_field.val(period[0] || "");
            $end_period_field.val(period[1] || "");
        });
    },

    implodePeriods: function () {
        this.getPeriodGroups().each(function () {
            var $value_field = $('[type=hidden]', this);
            var value_field_name = $value_field.attr('name');
            var $start_period_field = $('[name="_' + value_field_name + '[start]"]', this);
            var $end_period_field = $('[name="_' + value_field_name + '[end]"]', this);

            $value_field.val($start_period_field.val() + '-' + $end_period_field.val());
        });

    },

    splitSizes: function () {
        this.getSizeGroups().each(function () {
            var $value_field = $('[type=hidden]', this);
            var value_field_name = $value_field.attr('name');
            var $start_period_field = $('[name="_' + value_field_name + '[start]"]', this);
            var $end_period_field = $('[name="_' + value_field_name + '[end]"]', this);
            var period = $value_field.val().split('x');
            $start_period_field.val(period[0] || "");
            $end_period_field.val(period[1] || "");
        });
    },

    implodeSizes: function () {
        this.getSizeGroups().each(function () {
            var $value_field = $('[type=hidden]', this);
            var value_field_name = $value_field.attr('name');
            var $start_period_field = $('[name="_' + value_field_name + '[start]"]', this);
            var $end_period_field = $('[name="_' + value_field_name + '[end]"]', this);

            $value_field.val($start_period_field.val() + 'x' + $end_period_field.val());
        });

    },

    hideModelFieldBlocks: function (model_type_id) {
        $.each(this.getModelTypeFieldBlocks(model_type_id), function (ind, el) {
            var v = $(el).find('div.value').text();

            if (v.length == 0 && $(el).data('is-hide') == 1) {
                $(el).hide();
            }
        });
    },

    setValue: function (name, value) {
        AgreementModelForm.superclass.setValue.call(this, name, value);

        if (name == 'model_type_id')
            this.syncModelType();
    },

    changeModelPeriod: function (values, model_type_id) {
        var show = true;

        $(".change-period").hide();

        if (values == undefined) {
            return;
        }

        if (values.status == "declined" ||
            (
                values.haveReport != 0 &&
                (
                    values.reportStatus != "not_sent"
                    && values.reportStatus != "declined"
                    && values.reportStatus != "wait"
                    && values.reportStatus != ""
                )
            )
        ) {
            show = false;
        }

        if (values.status == 'accepted' || values.reportStatus == 'accepted') {
            show = true;
        }

        if (show) {
            $(".change-period").show();
        }

        $(".change-period-model-type-" + model_type_id).data("model-id", values.id)
            .show()
            .live("click", $.proxy(function (el) {
                var bt = $(el.srcElement);
                if (bt.data("action") == "change") {
                    bt.data("action", "apply").text("Сохранить");

                    bt.closest("td").find("div.value").hide();
                    bt.closest("td").find("div.input").show();
                }
                else {
                    bt.data("action", "change")
                        .text("Изменить");

                    var t = "";

                    this.implodePeriods();
                    this.getPeriodGroups().each(function () {
                        var $value_field = $('[type=hidden]', this);

                        if ($value_field.val() != "-")
                            t = $value_field.val();
                    });

                    $.post("/activity/module/agreement/model/changeModelPeriod",
                        {
                            modelId: bt.data("model-id"),
                            fieldId: bt.data("field-id"),
                            period: t
                        },
                        function () {
                            bt.closest("td").find("div.value").show().text(t);
                            bt.closest("td").find("div.input").hide();

                            location.reload();
                        });


                }
            }, this));
    },

    showFormModal: function () {
        AgreementModelForm.superclass.showFormModal.call(this);

        this.activateTab();
    },

    resetToAdd: function () {
        this.getForm().removeClass('edit view accepted add').addClass('add');
        this.getForm().attr('action', this.add_url);
        this.reset();
        this.getTab().addClass('pencil');
        this.getNumberFieldBlock().hide();

        $('input.dates-field').val('');

        if (this.getDatesPanel().length > 0) {
            this.onLoadConceptCertFields();
        }
    },

    onLoadConceptCertFields: function () {
        var self = this;

        $.post(this.load_concept_cert_fields_url, {}, function (result) {
            self.getConceptDatesPeriodAction().empty().html(result);

            $('input.dates-field').datepicker({dateFormat: "dd.mm.yy"});
        });
    },

    sendCancel: function () {
        $.post(this.cancel_url, {
            id: this.getIdField().val()
        }, $.proxy(this.onCancelResponse, this));
    },

    sendCancelScenario: function() {
        $.post(this.cancel_scenario_url, {
            id: this.getIdField().val()
        }, $.proxy(this.onCancelResponse, this));
    },

    sendCancelRecord: function() {
        $.post(this.cancel_record_url, {
            id: this.getIdField().val()
        }, $.proxy(this.onCancelResponse, this));
    },

    applyValues: function (values) {
        var model_type_id = values.model_type_id;

        AgreementModelForm.superclass.applyValues.call(this, values);

        this.syncModelType(values);
        this.splitPeriods();
        this.splitSizes();

        if (values.step1 && !values.step2 && (values.model_type_id = 2 || values.model_type_id == 4)) {
            this.getForm().addClass((values.status == 'wait' || values.status == 'wait_specialist') ? 'view' : 'edit');
        }
        else {
            this.getForm().addClass(values.status == 'not_sent' || values.status == 'declined' ? 'edit' : 'view');
        }

        if (values.status == 'wait' || values.status == 'accepted' || values.status == 'wait_specialist') {
            $('.model-file-block').hide();
        }

        if (values.status == 'accepted') {
            this.getForm().addClass('accepted');
        }

        this.enable();

        this.getNumberFieldBlock().show();
        this.getNumberFieldValue().html(values.id);

        if (values.no_model_changes) {
            this.getNoModelChangesFieldValues().attr('checked', 'checked');

            if (values.status == 'wait' || values.status == 'wait_specialist' || values.status == 'accepted') {
                this.getNoModelChangesFieldValues().attr('disabled', 'disabled');
            }
        }

        if (values.model_accepted_in_online_redactor) {
            this.getModelAcceptedInOnlineRedactorFieldValues().attr('checked', 'checked');
        }

        this.getDummyMsg().hide();

        window.localStorage.setItem('isOutOfDate', 0);
        if (values.model_blocked) {
            this.getDummyMsg().show();

            this.getForm().removeClass('edit').addClass('view');
            this.getCancelButton().hide();
        }

        $('.what-info').live('click', function () {
            $(this).popmessage('show', 'info', 'В случае, если данный макет был ранее утвержден, укажите в данном поле номер заявки, в которой был согласован макет');

            setTimeout(function () {
                $('.what-info').popmessage('hide');
            }, 5000);
        });

        if (this.getForm().hasClass('view')) {
            this.getModelAddFilesButton().hide();
            this.getModelAddRecordFilesButton().hide();
        } else {
            if (values.step1_value != 'accepted') {
                this.getModelAddFilesButton().show();
                this.getModelAddRecordFilesButton().hide();
            } else if (values.step1_value == 'accepted') {
                this.getModelAddFilesButton().hide();
                this.getModelAddRecordFilesButton().show();

                if (values.model_type_data.is_scenario_record) {
                    this.changeModelTitle(model_type_id);
                    this.onLoadModelRecordFiles(values);
                }
                else {
                    this.getModelRecordBlock().hide();
                }
            }
        }

        this.model_id = values.id;

        if (values.is_model_scenario) {
            this.getModelUploader().setUploadedFiles(values.model_uploaded_scenario_files);

            if (this.getModelRecordUploader() != undefined) {
                this.getModelRecordUploader().setUploadedFiles(values.model_uploaded_record_files)
            }
        } else {
            this.getModelUploader().setUploadedFiles(values.model_uploaded_files);
            if (this.getConceptUploader() != undefined) {
                this.getConceptUploader().setUploadedFiles(values.model_uploaded_files);
            }
        }

        if (this.getForm().hasClass('view')) {
            this.getModelAddFilesButton().hide();
        }

        this._hideDataIfModelIsBlocked(values);

        this._showHideActivityField(values);

        this.onLoadModelFiles(values);

        this.model_scenario_record = values.model_type_data;
        if (this.isScenarioRecordModel()) {
            this.changeModelTitle(model_type_id);
            this.onLoadModelRecordFiles(values);
        }
        else {
            this.getModelRecordBlock().hide();
        }
    },

    getModelAddRecordFilesButton: function () {
        return $('#js-file-trigger-model-record', this.getForm());
    },

    changeModelTitle: function (model_type_id) {
        if (model_type_id == 2) {
            this.getFileLabel().html("<strong>Сценарий радиоролика</strong><span class='upload-info'>(до 10 файлов, каждый весом не более 5 МБ)</span>");
            this.getFileModelRecordLabel().html("<strong>Запись радиоролика</strong><span class='upload-info'>(до 10 файлов, каждый весом не более 5 МБ)</span>");
        }
        else if (model_type_id == 4) {
            this.getFileLabel().html("<strong>Сценарий видеоролика</strong><span class='upload-info'>(до 10 файлов, каждый весом не более 5 МБ)</span>");
            this.getFileModelRecordLabel().html("<strong>Запись видеоролика</strong><span class='upload-info'>(до 10 файлов, каждый весом не более 5 МБ)</span>");
        }
        else {
            this.getFileLabel().html("<strong>Макет</strong><span class='upload-info'>(до 10 файлов, каждый весом не более 5 МБ)</span>");
        }
    },

    onLoadModelFiles: function (values) {
        $.post(this.load_model_block_url, {
            id: values.id,
        }, $.proxy(this.onLoadModelFilesBlock, this));
    },

    onLoadModelFilesBlock: function (data) {
        this.applyModelFilesBlockData(data);
    },

    applyModelFilesBlockData: function (data) {
        this.getModelFileBlockN().html(data);

        var temp_caption = $('#model_files_caption_temp', this.getModelFileBlockN());
        if (temp_caption != undefined) {
            this.getModelFilesCaption().html(temp_caption.text());

            temp_caption.remove();
            if (this.isConceptMode() && this.concept_file_uploader != undefined) {
                this.concept_file_uploader.drawFiles();
                this.concept_file_uploader.initScrollBar();
            }

            if (this.model_file_uploader != undefined) {
                this.model_file_uploader.drawFiles();
                this.model_file_uploader.initScrollBar();
            }
        }
        $('div.message', this.getForm()).hide();
    },

    getModelFilesCaption: function () {
        return $(this.isConceptMode() ? '#concept_files_caption' : '#model_files_caption', this.getForm());
    },

    _switchToDraftMode: function () {
        this.getDraftField().val('true');
        $(':input', this.getForm()).not('input[name=name], input[name=model_type_id]').data('skip-validate', 'true');
    },

    _switchToNormalMode: function () {
        this.getDraftField().val('false');
        $(':input', this.getForm()).data('skip-validate', 'false');
        this.syncModelType();
    },

    _switchToConceptMode: function (values) {
        this.getModelModeFields().hide();
        this.getTab().html('<span>Концепция</span>');
        this.getFileLabel().html('Концепция');
        this.setValue('name', 'Концепция');

        this.getModelFormBlock().hide();
        this.getConceptFormBlock().show();
        this.getModelFileField().prop('disabled', true);
        this.getConceptFileField().prop('disabled', false);

        this.getConceptDatesPeriodAction().find(':input').data('required', 1);

        $(':input', this.getForm()).not('input[name=model_file]').data('skip-validate', 'true');

        if (values && (values.status == 'wait' || values.status == 'accepted'))
            this.getConceptAddFileLink().hide();
    },

    _switchToModelMode: function () {
        this.getModelModeFields().show();
        this.getTab().html('<span>Материал</span>');

        this.getModelFormBlock().show();
        this.getConceptFormBlock().hide();
        this.getModelFileField().prop('disabled', false);
        this.getConceptFileField().prop('disabled', true);

        this.getConceptDatesPeriodAction().find(':input').data('required', 0);

        $(':input', this.getForm()).data('skip-validate', 'false');
    },

    _showHideActivityField: function (values) {
        this.getCancelButtonRecord().hide();
        this.getCancelButtonScenario().hide();

        if ($.trim(values.status) == "accepted") {
            $('tr.activity').find('div.krik-select').addClass('input');
            $('tr.activity').find('div.value-activity').show();
        } else {
            $('tr.activity').find('div.krik-select').removeClass('input');
            $('tr.activity').find('div.value-activity').hide();

            if (this.isScenarioRecordModel() && (values.status != 'not_sent' && values.status != 'declined')) {
                if (values.step1_value != "none") {
                    this.getCancelButtonScenario().show();
                }

                if (values.step2_value != "none") {
                    this.getCancelButtonRecord().show();
                }
            }
        }
    },

    getConceptDatesPeriodAction: function () {
        return $(".model-concept-form", this.getForm());
    },

    isConceptMode: function (model_type_id) {
        if (model_type_id != undefined && model_type_id == 10) {
            return true;
        }

        return this.getModelTypeField().val() == this.concept_type_id;
    },

    getIdField: function () {
        return $('input[name=id]', this.getForm());
    },

    getFirstModelTypeValue: function () {
        /**
         * Small Fix when reset form set to default model type
         */
        if (this.model_type_default_id != 0) {
            return this.model_type_default_id;
        }

        return $('.model-type .select-item', this.getForm()).data('value');
    },

    getModelTypeSelect: function () {
        return this.getModelTypeField().parents('.select');
    },

    getModelTypeField: function () {
        return $(':input[name=model_type_id]', this.getForm());
    },

    getModelTypeFieldBlocks: function (id) {
        return $('.type-fields-' + id);
    },

    getDraftField: function () {
        return $('input[name=draft]', this.getForm());
    },

    getDraftButton: function () {
        return $('.draft-btn', this.getForm());
    },

    getDummyMsg: function () {
        return $('.dummy', this.getForm());
    },

    getSubmitButton: function () {
        return $('.submit-btn', this.getForm());
    },

    getDeleteButton: function () {
        return $('.delete-btn', this.getForm());
    },

    getCancelButton: function () {
        return $('.cancel-btn', this.getForm());
    },

    getCancelButtonScenario: function () {
        return $('.cancel-btn-scenario', this.getForm());
    },

    getCancelButtonRecord: function () {
        return $('.cancel-btn-record', this.getForm());
    },


    getPeriodGroups: function () {
        return $('.period-group', this.getForm());
    },

    getSizeGroups: function () {
        return $('.size-group', this.getForm());
    },

    getModelModeFields: function () {
        return $('.model-mode-field', this.getForm());
    },

    getFileLabel: function () {
        return $('.model-title', this.getForm());
    },

    getFileModelRecordLabel: function () {
        return $('.file-label-record', this.getForm());
    },

    getModelFormBlock: function () {
        return $('.model-form', this.getForm());
    },

    getConceptFormBlock: function () {
        return $('.concept-form', this.getForm());
    },

    getModelFileBlock: function () {
        return $('.model-file-block', this.getForm());
    },

    getModelFileBlockN: function () {
        return $(this.isConceptMode() ? '#concept_files' : '#model_files', this.getForm());
    },

    getConceptFileBlock: function () {
        return $('.concept-file', this.getForm());
    },

    getModelRecordBlock: function () {
        return $('.model-record-block', this.getForm());
    },

    getModelFileField: function () {
        return $('input[id=model_file]', this.getModelFormBlock());
    },

    getConceptFileField: function () {
        return $('input[id=model_file]', this.getConceptFormBlock());
    },

    getRecordFileField: function () {
        return $('input[id=model_record_file]', this.getModelFormBlock());
    },

    getNumberFieldBlock: function () {
        return $('.number-field', this.getForm());
    },

    getNumberFieldValue: function () {
        return $('.value', this.getNumberFieldBlock());
    },

    getNoModelChangesFieldValues: function () {
        return $('input[name=no_model_changes]', this.getForm());
    },

    getModelAcceptedInOnlineRedactorFieldValues: function () {
        return $('input[name=model_accepted_in_online_redactor]', this.getForm());
    },

    getModelTypeLabel: function (modelId) {
        var modelTypes = new Array();

        modelTypes[2] = {name: "radio"};
        modelTypes[3] = {name: "internet"};
        modelTypes[4] = {name: "tv"};
        modelTypes[6] = {name: "press"};

        if (modelTypes[modelId] != undefined) {
            return modelTypes[modelId].name;
        }

        return '';
    },

    getModelEditorLinkBlock: function () {
        return $('.model-editor-link', this.getForm());
    },

    onSelectModelType: function () {
        this.syncModelType();
    },

    onClickDraft: function () {
        this._switchToDraftMode();

        if (this.onSubmit()) {
            this.send();
        }

        this._switchToNormalMode();
    },

    onClickCancel: function () {
        if (confirm('Вы уверены?'))
            this.sendCancel();

        return false;
    },

    onClickCancelScenario: function() {
        if (confirm('Вы уверены?'))
            this.sendCancelScenario();

        return false;
    },

    onClickCancelRecord: function() {
        if (confirm('Вы уверены?'))
            this.sendCancelRecord();

        return false;
    },

    onCancelResponse: function (data) {
        if (data.success)
            this.loadRowToEdit(this.getIdField().val());
        else
            alert('Ошибка отмены');
    },

    onDelete: function () {
        if (confirm('Вы уверены?'))
            this.deleteModel();

        return false;
    },

    onDeleteSuccess: function () {
        location.href = location.pathname + '?' + Math.random();
    },

    onSubmit: function () {
        this.implodePeriods();
        this.implodeSizes();

        return AgreementModelForm.superclass.onSubmit.call(this);
    },

    onClickNoModelChange: function (el) {
        var $element = $(el.target), model_type_id = this.getModelTypeField().val();

        if (model_type_id == 2 || model_type_id == 4) {
            if ($element.is(':checked')) {
                this.loadModelRecordBlockContent();
            }
            else {
                this.getModelRecordBlock().not(':first').remove();
                this.getModelRecordBlock().html('');
            }
        }
    },

    //Model files block
    onLoadModelBlock: function () {
    },

    onLoadModelBlockChilds: function (data) {
        this.onLoadModelBlockChildsWithRemove(data, null, this.getModelFileBlock());
    },

    onLoadConceptBlockData: function (data) {
        this.onLoadModelBlockChildsWithRemove(data, true, this.getConceptFileBlock());
    },

    onLoadModelBlockChildsWithRemove: function (data, removeNextElements, parentEl) {
        if (removeNextElements != undefined) {
            parentEl.nextAll().remove();
            parentEl.after(data);
        } else {
            parentEl.after(data);
        }

        $('div.message', this.getForm()).hide();
    },

    loadModelBlockContent: function (childs, callbackChilds, callback) {
        /*$.post(this.load_model_block_url, {
            id: this.model_id,
            childs: childs
        }, $.proxy(childs != undefined ? callbackChilds != undefined ? callbackChilds : this.onLoadModelBlockChilds : callback != undefined ? callback : this.onLoadModelRecordBlock, this));*/
    },

    //Record files blocks
    onLoadModelRecordBlock: function (data) {
        this.getModelRecordBlock().html(data).show();

        this.getModelRecordBlock().find('.file-label-record')
            .html(this.getModelTypeField().val() == 2 ? 'Запись радиоролика' : 'Запись видеоролика');

        this.getModelRecordBlock().find('.message').hide();
        this.getMainModelRecordRemoveLink().show();

        if (this.values != undefined && (this.values.step2 || this.values.step2_value != 'none')) {
            this.getMainModelRecordRemoveLink().hide();
        }

        if (!this.getNoModelChangesFieldValues().is(':checked')) {
            if (this.values != undefined && !this.values.no_model_changes) {
                if (this.values.step1) {
                    this.getModelFileBlock().find('div.control').hide();
                    this.getModelFileBlock().find('img.remove-main-model-file').hide();
                }

                if (!this.values.step1) {
                    this.getModelRecordBlock().hide();
                }
            }
        }
    },

    onLoadModelRecordBlockChilds: function (data) {
        this.onLoadModelBlockChildsWithRemove(data, null, this.getModelRecordBlock());
    },

    loadModelRecordBlockContent: function (childs) {
        $.post(this.load_record_block_url, {
            id: this.model_id,
            childs: childs
        }, $.proxy(childs != undefined ? this.onLoadModelRecordBlockChilds : this.onLoadModelRecordBlock, this));
    },

    //Certificate dates
    getLinkToAddDatesField: function () {
        return $('.dates-add-field', this.getForm());
    },

    getDatesPanelFirst: function () {
        return $('tr.model-dates-field:first', this.getForm());
    },

    getDatesPanels: function () {
        return $('tr.model-dates-field:not(:first)', this.getForm());
    },

    getDatesPanel: function () {
        return $('tr.model-dates-field:last', this.getForm());
    },

    getCertificateDatePanel: function () {
        return $('tr.model-certificate-field', this.getForm());
    },

    onAddDatesField: function (e) {
        var self = this;

        $.post(this.dates_field_url, {}, function (result) {
            self.getDatesPanel().after(result);
            self.getDatesErrorMessage().hide();

            $('input.dates-field').datepicker({dateFormat: "dd.mm.yy"});

            var i = 1, text = $('tr.model-dates-field:first').find('td.label').text();
            $('tr.model-dates-field:not(:first)').each(function (ind, el) {
                $(el).find('td.label').empty().html(text + '№' + (i++));
            });
        });

    },

    getDatesErrorMessage: function () {
        return $('.dates-error-message', this.getForm());
    },

    loadDatesAndCertificates: function (values) {
        var self = this;

        if (values != undefined && values.concept_id != 0 ) {
            $('.model-certificate-date', this.getForm()).each(function(ind, el) {
                var $item = $(el);

                if ($item.data('value') != values.concept_id && $item.data('must-delete') == 1) {
                    $item.remove();
                }
            });
        }

        $.post(this.load_dates_and_certificates, {id: values.id}, function (result) {
            if ($.trim(result).length != 0) {
                self.getCertificateDatePanel().remove();
                self.getDatesPanel().replaceWith(result);

                self.getDatesErrorMessage().hide();
                $('input.dates-field').datepicker({dateFormat: "dd.mm.yy"});

                self.getDatesPanelFirst().on('click', '.dates-add-field', $.proxy(self.onAddDatesField, self));
                self.getDatesPanels().on('click', '.remove-date-field', $.proxy(self.onDeleteDateField, self));
            }
            else {
                self.getCertificateDatePanel().hide();
                self.getDatesPanel().hide();
            }
        });
    },

    getRemoveDateFieldLink: function () {
        return $('.remove-date-field', this.getForm());
    },

    onDeleteDateField: function (e) {
        var el = $(e.target), id = el.data('id');

        if (confirm('Удалить дату ?')) {
            $.post(this.delete_date_field, {id: id}, function (result) {
                el.closest('tr').remove();
            });
        }
    },


    resetFileUploadInfo: function () {
        this.getContainerForModelUploadedFiles().empty().fadeOut();
        this.getContainerForModelRecordFiles().empty().fadeOut()
        this.getConceptFileBlock().nextAll().remove();

        this.uploaded_files_count = 0;
        this.uploaded_record_files_count = 0;

        $('.model-file-block').show();
        $('.model-record-block').show();
    },

    getContainerForModelUploadedFiles: function () {
        return $('.model-form-files-upload-info', this.getForm());
    },

    getContainerForModelRecordFiles: function () {
        return $('.model-form-record-files-upload-info', this.getForm());
    },

    updateUploadFileInfo: function (field, files_count) {
        if (field == this.MODEL_FILE_FIELD) {
            this.formatUploadFilesInfo(this.getContainerForModelUploadedFiles(), this.uploaded_files_count, this.max_upload_files_count, files_count);
        } else if (field == this.MODEL_RECORD_FILE_FIELD) {
            this.formatUploadFilesInfo(this.getContainerForModelRecordFiles(), this.uploaded_record_files_count, this.max_upload_files_count, files_count);
        }
    },

    updateUploadFileInfoWhenDelete: function (data) {
        if (this.field_name == this.MODEL_FILE_FIELD) {
            this.uploaded_files_count -= 1;

            this.onLoadModelBlockChildsWithRemove(data, true, this.is_concept != undefined ? this.getConceptFileBlock() : this.getModelFileBlock());
            this.formatUploadFilesInfo(this.getContainerForModelUploadedFiles(), this.uploaded_files_count, this.max_upload_files_count);
        } else if (this.field_name == this.MODEL_RECORD_FILE_FIELD) {
            this.uploaded_record_files_count -= 1;

            this.onLoadModelBlockChildsWithRemove(data, true, this.getModelRecordBlock());
            this.formatUploadFilesInfo(this.getContainerForModelRecordFiles(), this.uploaded_files_count, this.max_upload_files_count);
        }
    },

    getModelFileRemoveControl: function () {
        return '.remove-report-ext-file, .remove-concept-ext-file';
    },

    onRemoveModelFiles: function (e) {
        var $el = $(e.target);

        if (confirm('Удалить файл ?')) {
            this.field_name = $el.data('field-name');
            this.is_concept = $el.data('concept');

            $.post('/activity/module/agreement/delete/model/file', {
                    fileId: $el.data('file-id')
                },
                $.proxy(this.updateUploadFileInfoWhenDelete, this));
        }
    },

    isScenarioRecordModel: function() {
        return (this.getModelTypeField().val() == 2 || this.getModelTypeField().val() == 4) ? true : false;
    },

    _hideDataIfModelIsBlocked: function(values) {
        if (values != undefined && values.model_blocked) {
            this.getForm().removeClass('edit').addClass('accepted view');
        }
    },

    getModelUploader: function () {
        return this.model_file_uploader;
    },

    getModelRecordUploader: function () {
        return this.model_record_file_uploader;
    },

    getConceptUploader: function () {
        return this.concept_file_uploader;
    },

    getModelAddFilesButton: function () {
        return $(this.isConceptMode() ? '#js-file-trigger-concept' : '#js-file-trigger-model', this.getForm());
    },

    /*Work with uploaded model files*/
    onDeleteUploadedModelFile: function (e) {
        if (confirm('Удалить файл ?')) {
            $.post(this.model_file_uploader.delete_uploaded_file_url, {id: $(e.target).data('file-id')},
                $.proxy(this.onDeleteModelFileSuccess, this)
            );
        }
    },

    onDeleteUploadedModelRecordFile: function (e) {
        if (confirm('Удалить файл ?')) {
            $.post(this.model_file_uploader.delete_uploaded_file_url, {id: $(e.target).data('file-id')},
                $.proxy(this.onDeleteModelRecordFileSuccess, this)
            );
        }
    },

    /**
     * Delete temporary uploaded files in model / scenario files
     * @param data
     */
    onDeleteModelFileSuccess: function (data) {
        this.applyModelFilesBlockData(data);

        if (this.getModelUploader()) {
            this.getModelUploader().decrementAlreadyUploadedFile();
        }
    },

    /**
     * Delete temporary uploaded files in record files
     * @param data
     */
    onDeleteModelRecordFileSuccess: function (data) {
        this.applyModelRecordFilesBlockData(data);

        if (this.getModelRecordUploader()) {
            this.getModelRecordUploader().decrementAlreadyUploadedFile();
        }
    },

    //Model files block
    onLoadModelRecordFiles: function (values) {
        if (values != undefined && (values.step1_value == 'accepted' || values.no_model_changes)) {
            $.post(this.load_record_block_url, {
                id: this.model_id,
            }, $.proxy(this.onLoadModelRecordFilesBlockSuccess, this));
        }
    },

    onLoadModelRecordFilesBlockSuccess: function (data) {
        console.log(data);
        this.applyModelRecordFilesBlockData(data);
    },

    applyModelRecordFilesBlockData: function (data) {
        this.getModelRecordFileBlock().html(data);
        this.getModelRecordBlock().show();

        var temp_caption = $('#model_record_files_caption_temp', this.getModelRecordFileBlock());
        if (temp_caption != undefined) {
            this.getModelRecordFilesCaption().html(temp_caption.text());

            if (this.model_record_file_uploader != undefined) {
                this.model_record_file_uploader.decrementAlreadyUploadedFile();
                this.model_record_file_uploader.drawFiles();
                this.model_record_file_uploader.initScrollBar();
            }

            temp_caption.remove();
        }
        $('div.message', this.getForm()).hide();
    },

    getModelRecordFileBlock: function () {
        return $('#model_record_files', this.getForm());
    },

    getModelRecordFilesCaption: function () {
        return $('#model_record_files_caption', this.getForm());
    },
});
