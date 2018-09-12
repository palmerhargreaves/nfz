AgreementModelReportForm = function (config) {
    // configurable {
    this.add_url = '';
    this.cancel_url = '';

    this.load_financial_docs_block_url = '';
    this.load_financial_concept_docs_block_url = '';
    this.delete_report_file = '';

    this.load_additional_financial_docs_files_url = '';

    this.max_upload_files_count = $('body').data('max-files-upload-count');
    this.uploaded_additional_files_count = 0;
    this.uploaded_financial_files_count = 0;
    this.left_to_upload = 0;
    this.field_name = '';

    this.values = null;

    this.REPORT_ADDITION_FILE_FIELD = 'additional_file';
    this.REPORT_FINANCIAL_FILE_FIELD = 'financial_docs_file';

    this.report_file_additional_uploader = undefined;
    this.report_file_financial_uploader = undefined;
    this.concept_file_uploader = undefined;

    this.init_delete_files_event = false;
    // }

    AgreementModelReportForm.superclass.constructor.call(this, config);

    this.files_container = '';
    this.values = null;

    this.uploaded_additional_files_count = 0;
    this.uploaded_financial_files_count = 0;

    this.is_concept = false;
}

utils.extend(AgreementModelReportForm, AgreementModelBaseForm, {
    initEvents: function () {
        AgreementModelReportForm.superclass.initEvents.call(this);

        this.getTab().on('activated', $.proxy(this.onActivateTab, this));

        this.initCheckCostTimer();
        this.getForm().on('click', this.getModelFileRemoveControl(), $.proxy(this.onRemoveModelFiles, this));

        $(document).on('click', '.remove-uploaded-report-file', $.proxy(this.onDeleteUploadedFile, this));

        this.getSubmitButton().click($.proxy(this.onSubmitReport, this));
        this.getCancelButton().click($.proxy(this.onClickCancel, this));

    },

    initCheckCostTimer: function () {
        setInterval($.proxy(this.onCheckCost, this), 500);
    },

    sendCancel: function () {
        $.post(this.cancel_url, {
            id: this.getIdField().val()
        }, $.proxy(this.onCancelResponse, this));
    },

    reset: function() {
        AgreementModelReportForm.superclass.reset.call(this);

        this.getAdditionalFilesToUploadWithPlaces().html('');

        this.getReportBlockedInfo().hide();
        this.getCancelButton().hide();
        this.getSubmitButton().hide();

        this.getAdditionalFilesToUploadWithPlaces().removeAttr('data-places-to-upload');
        this.getAdditionalFilesToUploadWithPlaces().removeAttr('data-required');

        this.resetFileUploadInfo();

        this.getReportBlockedInfo().hide();

        this.getAdditionalReportField().removeAttr('disabled');
        this.getFinancialReportField().removeAttr('disabled');
        this.getConceptReportField().removeAttr('disabled');

        if (this.getReportAdditionalFileUploader() != undefined) {
            this.getReportAdditionalFileUploader().reset();
            this.getReportFinancialFileUploader().reset();
            this.getReportConceptFileUploader().reset();
        }
    },

    onSubmitReport: function(event) {
        var valid = true;

        event.preventDefault();
        if (this.getReportAdditionalFileUploader() != undefined && this.values != null) {
            if (this.values.places_count != 0 && this.values.places_count > (this.uploaded_additional_files_count + this.getReportAdditionalFileUploader().getUploadedFilesCount())) {
                showAlertPopup('Ошибка при отправке отчета:', 'Необходимо загрузить ' + this.values.places_count + ' файл(а,лов). Загружено ' + (this.uploaded_additional_files_count + this.getReportAdditionalFileUploader().getUploadedFilesCount()));
                valid = false;
            }
        }

        if (valid) {
            this.getForm().submit();
        }
    },

    syncCostAndFinancialFile: function () {
        if (this.getCost())
            this.getFinacialFileBlock().show();
        else
            this.getFinacialFileBlock().hide();
    },

    applyValues: function (values) {
        AgreementModelReportForm.superclass.applyValues.call(this, values);

        this.getForm().addClass(values.status == 'not_sent' || values.status == 'declined' ? 'edit' : 'view');
        values.model_status == "accepted" ? this.enable() : this.disable();

        if (values.status == 'accepted') {
            this.getForm().addClass('accepted');
        }

        this.uploaded_additional_files_count = values.report_additional_uploaded_files_count;
        this.uploaded_financial_files_count = values.report_financial_uploaded_files_count;

        if (values.isOutOfDate) {
            this.getCancelButton().hide();
            this.getSubmitButton().hide();

            this.getReportBlockedInfo().show();
        } else if (values.status == 'not_sent' || values.status == 'declined')
            this.getSubmitButton().show();

        values.is_concept ? this._switchToConceptMode() : this._switchToModelMode();
        values.cost == 0 ? $("input[name=cost]", this.getForm()).val('') : '';

        this.is_concept = values.is_concept;

        if (values.status == 'accepted') {
            this.getForm().addClass('accepted');
            this.getCancelButton().hide();
        }

        if (this.getForm().hasClass('view')) {
            this.getPopupFileTriggerButton().hide();
            this.getCostField().attr('disabled', true);

            if (values.is_concept) {
                this.getConceptReportField().attr('disabled', true);
            } else {
                this.getAdditionalReportField().attr('disabled', true);
                this.getFinancialReportField().attr('disabled', true);
            }
        }

        this.loadAdditionalFinancialDocs(values);
    },

    _switchToConceptMode: function () {
        this.getCostBlock().hide();

        this.getConceptFormBlock().show();
        this.getModelFormBlock().hide();
    },

    _switchToModelMode: function () {
        this.getCostBlock().show();

        this.getConceptFormBlock().hide();
        this.getModelFormBlock().show();
    },


    onAddConceptReportFilesFromValues: function (values) {
        if (values == undefined)
            return;

        //this.onShowUploadedFilesInfo(values);
        $.post(this.load_financial_concept_docs_block_url, {
            id: values.id,
            childs: true
        }, $.proxy(this.onLoadReportFinancialConceptBlockChilds, this));
    },

    onLoadReportFinancialConceptBlockChilds: function (data) {
        this.onLoadReportFinancialConceptBlockChildsWithRemove(data);
    },

    onLoadReportFinancialConceptBlockChildsWithRemove: function (data, nextElementRemove) {
        if (nextElementRemove != undefined) {
            this.getReportConceptFinancialBlock().nextAll().remove();
        }

        this.getReportConceptFinancialBlock().after(data);
        $('div.message', this.getForm()).hide();
    },

    onAddReportFinancialDocsFromValues: function (values) {
        if (values == undefined) {
            return
        }

        //this.onShowUploadedFilesInfo(values);
        $.post(this.load_financial_docs_block_url, {
            id: values.id,
            report_file_type: 'fin',
            childs: true
        }, $.proxy(this.onLoadReportFinancialBlockChilds, this));
    },

    //Record files blocks
    onLoadReportFinancialBlockChilds: function (data) {
        this.onLoadReportFinancialBlockChildsWithRemove(data);
    },

    onLoadReportFinancialBlockChildsWithRemove: function (data, nextElementRemove) {
        if (nextElementRemove != undefined) {
            this.getReportFinancialBlock().nextAll().remove();
        }

        this.getReportFinancialBlock().after(data);
        $('div.message', this.getForm()).hide();
    },

    onAddReportAdditionalFromValues: function (values) {
        if (values == undefined) {
            return
        }

        $.post(this.load_financial_docs_block_url, {
            id: values.id,
            report_file_type: 'add',
            childs: true
        }, $.proxy(this.onLoadReportAdditionalBlockChilds, this));
    },

    //Record files blocks
    onLoadReportAdditionalBlockChilds: function (data) {
        this.onLoadReportAdditionalBlockChildsWithRemove(data);
    },

    onLoadReportAdditionalBlockChildsWithRemove: function (data, nextElementRemove) {
        if (nextElementRemove != undefined) {
            this.getReportAdditionalBlock().nextAll().remove();
        }

        this.getReportAdditionalBlock().after(data);
        $('div.message', this.getForm()).hide();
    },

    getReportFinancialBlock: function () {
        return $('.report-financial-docs', this.getForm())
    },

    getReportAdditionalBlock: function () {
        return $('.report-additional-docs', this.getForm())
    },

    getReportConceptFinancialBlock: function () {
        return $('.concept-report-file', this.getForm())
    },

    getModelFinancialDocsFilesCount: function () {
        return $('input[name*=financial_docs_file]', this.getForm());
    },

    getModelAdditionalFilesCount: function () {
        return $('input[data-additional-model-file=1]', this.getForm());
    },

    getModelConceptAddFinancialDocsFileLink: function () {
        return $('a.model-concept-report-add-financial-file', this.getForm());
    },

    getModelFormBlock: function () {
        return $('.model-form', this.getForm());
    },

    getConceptFormBlock: function () {
        return $('.concept-form', this.getForm());
    },

    getModelFinancialDocsFileField: function () {
        return $('input[name=financial_docs_file]', this.getModelFormBlock());
    },

    getConceptFinancialDocsFileField: function () {
        return $('input[name=financial_docs_file]', this.getConceptFormBlock());
    },

    getIdField: function () {
        return $('input[name=id]', this.getForm());
    },

    getCost: function () {
        return parseFloat(this.getCostField().val());
    },

    getCostField: function () {
        return $('input[name=cost]', this.getForm());
    },

    getCostBlock: function () {
        return $('.cost', this.getForm());
    },

    getAdditionalFileLabel: function () {
        return $('.file-label', this.getAdditionalFileBlock());
    },

    extendAdditionalFile: function (values) {
        var aFile = this.getAdditionalFileBlock(), from = 2;

        for (i = 0; i < values.fields; i++) {
            var temp = $('.additional-file' + from, this.getForm());

            $('.file-label', temp).html(values.additional_file_description + " - " + values.additional_file_header + " №" + from);
            $('input[name=additional_file]', temp).attr("name", "additional_file" + from);

            temp.show();
            from++;
        }

    },

    getAdditionalFileBlock: function () {
        return $('.additional-file', this.getForm());
    },

    getFinacialFileBlock: function () {
        return $('.financial-file', this.getForm());
    },

    getCancelButton: function () {
        return $('.cancel-btn', this.getForm());
    },

    getSubmitButton: function () {
        return $('.submit-btn', this.getForm());
    },

    onSelectModelType: function () {
        this.syncModelType();
    },

    onClickCancel: function () {
        if (confirm('Вы уверены?'))
            this.sendCancel();

        return false;
    },

    onCancelResponse: function (data) {
        if (data.success)
            this.loadRowToEdit(this.getIdField().val());
        else
            alert('Ошибка отмены');
    },

    onCheckCost: function () {
        this.syncCostAndFinancialFile();
    },

    onShowUploadedFilesInfo: function (values) {
        this.values = values;

        if (values.uploaded_files_count.report_additional != undefined && values.uploaded_files_count.report_additional != 0 && !values.is_concept) {
            //
            this.uploaded_additional_files_count = values.uploaded_files_count.report_additional;
            //
            this.formatUploadFilesInfo(this.getContainerForAdditionalUploadedFiles(), values.uploaded_files_count.report_additional, values.max_upload_files_count);
        }

        console.log(values.uploaded_files_count.report_additional_ext);
        if (values.uploaded_files_count.report_additional_ext != undefined && values.uploaded_files_count.report_additional_ext != 0 && !values.is_concept) {
            //
            this.uploaded_additional_files_count = values.uploaded_files_count.report_additional_ext;
            //
            this.formatUploadFilesInfo(this.getContainerForAdditionalUploadedFiles(), values.uploaded_files_count.report_additional_ext, values.max_upload_files_count);
        }

        if (values.uploaded_files_count.report_financial != undefined && values.uploaded_files_count.report_financial != 0 && !values.is_concept) {
            //
            this.uploaded_financial_files_count = values.uploaded_files_count.report_financial;
            //
            this.formatUploadFilesInfo(this.getContainerForFinancialRecordFiles(), values.uploaded_files_count.report_financial, values.max_upload_files_count);
        }

        if (values.is_concept) {
            //
            this.uploaded_financial_files_count = values.uploaded_files_count.report_financial;
            //
            this.formatUploadFilesInfo(this.getContainerForFinancialRecordFiles(), values.uploaded_files_count.report_financial, values.max_upload_files_count);
        }
    },

    formatUploadFilesInfo: function (obj, uploaded, max_files_count, files_to_upload) {
        var left_to_upload = max_files_count - uploaded - (files_to_upload != undefined ? files_to_upload : 0);

        if (left_to_upload <= 0) {
            left_to_upload = 0;

            obj.removeClass('alert-info').addClass('alert-danger');
        } else {
            obj.removeClass('alert-danger').addClass('alert-info');
        }

        $('body').data('max-files-upload-count', max_files_count - uploaded);
        var msg = '<h4>Загрузка файлов</h4><p>Максимальное кол. файлов: ' + max_files_count + '</p>' +
            '<p>Файлов загружено: ' + uploaded + '</p>';

        if (files_to_upload != undefined) {
            msg += '<p>Файлов к загрузке: ' + files_to_upload + '</p>';
        }
        msg += '<p class="container-upload-files-left">Разрешено к загрузке: ' + left_to_upload + '</p>';

        obj.html(msg)
            .fadeIn();
    },

    getContainerForAdditionalUploadedFiles: function () {
        return $('.model-form-additional-files-upload-info', this.getForm());
    },

    getContainerForFinancialRecordFiles: function () {
        return (this.values != undefined && this.values.is_concept) ? $('.model-form-financial-concept-files-upload-info', this.getForm()) : $('.model-form-financial-files-upload-info', this.getForm());
    },

    updateUploadFileInfo: function (field, files_count) {
        if (field == this.REPORT_ADDITION_FILE_FIELD) {
            this.formatUploadFilesInfo(this.getContainerForAdditionalUploadedFiles(), this.uploaded_additional_files_count, this.max_upload_files_count, files_count);
        } else if (field == this.REPORT_FINANCIAL_FILE_FIELD) {
            this.formatUploadFilesInfo(this.getContainerForFinancialRecordFiles(), this.uploaded_financial_files_count, this.max_upload_files_count, files_count);
        }
    },

    updateUploadFileInfoWhenDelete: function (data) {
        if (this.field_name == this.REPORT_ADDITION_FILE_FIELD) {
            this.uploaded_additional_files_count -= 1;

            this.onLoadReportAdditionalBlockChildsWithRemove(data, true);
            this.formatUploadFilesInfo(this.getContainerForAdditionalUploadedFiles(), this.uploaded_additional_files_count, this.max_upload_files_count);

            if (this.getAdditionalFilesToUploadWithPlaces() != undefined && this.getAdditionalFilesToUploadWithPlaces().data('places-to-upload-orig') != 0 && this.uploaded_additional_files_count < this.getAdditionalFilesToUploadWithPlaces().data('places-to-upload-orig')) {
                this.getAdditionalFilesToUploadWithPlaces().attr('data-places-to-upload', this.getAdditionalFilesToUploadWithPlaces().data('places-to-upload-orig') - this.uploaded_additional_files_count);
                this.getAdditionalFilesToUploadWithPlaces().attr('data-required', true);
                this.getAdditionalFilesToUploadWithPlaces().html( this.getAdditionalFilesToUploadWithPlaces().data('text'));
                this.getAdditionalFilesToUploadWithPlaces().show();
            }

        } else if (this.field_name == this.REPORT_FINANCIAL_FILE_FIELD) {
            this.uploaded_financial_files_count -= 1;

            if (this.values.is_concept) {
                this.onLoadReportFinancialConceptBlockChildsWithRemove(data, true);
            } else {
                this.onLoadReportFinancialBlockChildsWithRemove(data, true);
            }
            this.formatUploadFilesInfo(this.getContainerForFinancialRecordFiles(), this.uploaded_financial_files_count, this.max_upload_files_count);
        }
    },

    getModelFileRemoveControl: function () {
        return '.remove-report-ext-file, .remove-concept-ext-file';
    },

    getReportAdditionalFilesToUploadContainer: function() {
        return $('.report-form-selected-additional-files-to-upload', this.getForm());
    },

    getReportFinancialFilesToUploadContainer: function() {
        return $('.report-form-selected-financial-files-to-upload', this.getForm());
    },

    onRemoveModelFiles: function (e) {
        var $el = $(e.target);

        if (confirm('Удалить файл ?')) {
            this.field_name = $el.data('field-name');

            $.post(this.delete_report_file, {
                    fileId: $el.data('file-id'),
                    concept: this.values.is_concept
                },
                $.proxy(this.updateUploadFileInfoWhenDelete, this));
        }
    },

    resetFileUploadInfo: function() {
        this.getContainerForAdditionalUploadedFiles().empty().hide();
        this.getContainerForFinancialRecordFiles().empty().hide();

        this.getReportAdditionalFilesToUploadContainer().empty();
        this.getReportFinancialFilesToUploadContainer().empty();

        this.getReportConceptFinancialBlock().nextAll().remove();

        this.uploaded_additional_files_count = 0;
        this.uploaded_financial_files_count = 0;
    },

    getAdditionalFilesToUploadWithPlaces: function() {
        return $('.additional-files-to-upload-with-places', this.getForm());
    },

    getReportBlockedInfo: function () {
        return $('.report-blocked-info', this.getForm());
    },

    getAdditionalReportField: function() {
        return $('#additional_file', this.getForm());
    },

    getFinancialReportField: function() {
        return $('#financial_docs_file', this.getForm());
    },

    getConceptReportField: function() {
        return $('#concept_report_file', this.getForm());
    },

    getReportAdditionalFileUploader: function() {
        return this.report_file_additional_uploader;
    },

    getReportFinancialFileUploader: function() {
        return this.report_file_financial_uploader;
    },

    getReportConceptFileUploader: function() {
        return this.concept_file_uploader;
    },

    onActivateTab: function () {
        if (this.getReportAdditionalFileUploader() != undefined) {
            this.getReportAdditionalFileUploader().initScrollBar();
            this.getReportFinancialFileUploader().initScrollBar();
            this.getReportConceptFileUploader().initScrollBar();
        }
    },

    onFilesChange: function() {
        //this.updateScrollBar();
    },

    getPopupFileTriggerButton: function() {
        return $('.js-d-popup-file-trigger', this.getForm());
    },

    loadAdditionalFinancialDocs: function(values) {
        if (this.getReportAdditionalFileUploader() != undefined) {
            this.loadFilesBlock(values.report_id, 'report_additional', this.onLoadAdditionalFilesBlocksSuccess);
            this.loadFilesBlock(values.report_id, 'report_financial', this.onLoadFinancialFilesBlocksSuccess);

            this.getReportAdditionalFileUploader().setUploadedFilesCount(this.uploaded_additional_files_count);
            this.getReportFinancialFileUploader().setUploadedFilesCount(this.uploaded_financial_files_count);
            this.getReportConceptFileUploader().setUploadedFilesCount(this.uploaded_additional_files_count);
        }
    },

    loadFilesBlock: function(report_id, type, callback) {
        $.post(this.load_additional_financial_docs_files_url,
            {
                id: report_id,
                by_type: type,
            },
            $.proxy(callback, this));
    },

    onLoadAdditionalFilesBlocksSuccess: function(result) {
        this.getAdditionalFilesContainer().html(result);
        this.getReportAdditionalCaptionContainer().html(this.getTempCaption('report_additional'));

        if (this.getReportAdditionalFileUploader() != undefined) {
            this.getReportAdditionalFileUploader().drawFiles();
        }
    },

    onLoadFinancialFilesBlocksSuccess: function(result) {
        this.getFinancialDocsFilesContainer().html(result);
        this.getReportFinancialCaptionContainer().html(this.getTempCaption('report_financial'));

        if (this.getReportFinancialFileUploader() != undefined) {
            this.getReportFinancialFileUploader().drawFiles();
        }
    },

    getAdditionalFilesContainer: function() {
        return $('#report_additional_files', this.getForm());
    },

    getFinancialDocsFilesContainer: function() {
        if (this.is_concept) {
            return $('#concept_report_files', this.getForm());
        }

        return $('#report_financial_files', this.getForm());
    },

    getReportFinancialCaptionContainer: function() {
        if (this.is_concept) {
            return $('#concept_report_files_caption', this.getForm());
        }

        return $('#report_financial_files_caption', this.getForm());
    },

    getReportAdditionalCaptionContainer: function() {
        return $('#report_additional_files_caption', this.getForm());
    },

    getTempCaption: function(by_type) {
        return $('#report_files_caption_' + by_type +'_temp', this.getForm());
    },

    onDeleteUploadedFile: function(e) {
        var $from = $(e.target);

        this.by_type = $from.data('by-type');
        this.report_id = $from.data('report-id');
        this.is_concept = $from.data('is-concept') == 1 ? true : false;

        if (confirm('Удалить файл ?')) {
            $.post(this.delete_uploaded_add_fin_doc_files_url,
                {
                    id: $from.data('file-id'),
                    by_type: this.by_type
                },
                $.proxy(this.onDeleteFileSuccess, this));
        }
    },

    onDeleteFileSuccess: function(result) {
        console.log(result);
        //if (result.success)
        {
            this.loadFilesBlock(this.report_id, this.by_type,
                this.by_type == 'report_additional'
                    ? this.onLoadAdditionalFilesBlocksSuccess
                    : this.onLoadFinancialFilesBlocksSuccess
            );

            if (this.by_type == 'report_additional') {
                this.getReportAdditionalFileUploader().decrementAlreadyUploadedFile();
            } else {
                this.getReportFinancialFileUploader().decrementAlreadyUploadedFile();
            }

            this.uploaded_additional_files_count--;
        }
    },
});
