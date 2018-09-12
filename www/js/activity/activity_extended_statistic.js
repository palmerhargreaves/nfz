/**
 * Created by kostet
 */
ActivityExtendedStatistic = function(config) {
    // configurable {
    // }
    this.on_save_data = '';
    this.on_apply_concept_to_statistic = '';
    this.on_check_allow_to_edit_cancel = '';
    this.on_cancel_statistic = '';
    //activity_extended_bind_to_concept

    this.on_activity_extended_change_stats = '';
    //activity_extended_change_stats

    this.quarter = 0;
    this.activity_id = 0;

    $.extend(this, config);

    this.save_button = null;
}

ActivityExtendedStatistic.prototype = {
    start: function() {
        this.initEvents();

        return this;
    },

    initEvents: function() {
        this.getForm().on('click', '#bt_on_save_statistic_data_importer', $.proxy(this.onShowSaveDialog, this));
        this.getForm().on('click', '#bt_on_save_statistic_data', $.proxy(this.onApplyButtonClick, this));
        this.getForm().on('click', '#btApplyConceptToStatistic', $.proxy(this.onApplyConceptToStatistic, this));
        this.getForm().on('click', '#apply-stat-button', $.proxy(this.onApplyButtonClick, this));

        this.getForm().on('click', '#bt_on_cancel_statistic_data', $.proxy(this.onCancelStatisticData, this));

        $(document).on('click', '#bt-send-statistic-data', $.proxy(this.onSendDataToImporterClick, this));
        $(document).on('click', '#bt-close-modal, .modal-close', $.proxy(this.onCloseModal, this));

        this.getForm().on('input', 'input[type=text]', $.proxy(this.onInputText, this));
        this.getFormWithDateFields().datepicker();
    },

    onCancelStatisticData: function() {
        if (confirm('Отменить отправку данных ?')) {
            $.post(this.on_cancel_statistic, { concept_id : this.getConceptId().val() }, $.proxy(this.onCancelStatisticDataSuccess, this));
        }
    },

    onCancelStatisticDataSuccess: function(result) {
        if (result.success) {
            this.getContainerAllowToEdit().fadeIn();
            this.getContainerAllowToCancel().fadeOut();
        } else {
            alert('Ошибка. Обратитесь в тех. поддержку.');
        }
    },

    onSendDataToImporterClick: function(e) {
        e.preventDefault();

        this.getSendTo().val('importer');
        this.sendData($(e.target));
    },

    onCloseModal: function() {
        this.getContainerAllowToEdit().fadeIn();
        this.getSaveModal('hide');
    },

    onShowSaveDialog: function(e) {
        e.preventDefault();

        this.getContainerAllowToEdit().fadeOut();
        this.getSaveModal('show');
    },

    getForm: function() {
        return $('#frmStatistics');
    },

    getFormWithDateFields: function() {
        return $('.with-date', this.getForm());
    },

    getInfoPanelMsg: function() {
        return $('.info-save-complete');
    },

    getFormFieldsData: function() {
        return $('#txt_frm_fields_data', this.getForm());
    },

    calcValues: function (field, symbol, el) {
        var $f = $('.calc-field-' + field), fields = $f.data('calc-fields').split(":"),
            v1 = !isNaN(parseFloat($('.field-' + fields[0]).val())) ? parseFloat($('.field-' + fields[0]).val()) : 0,
            v2 = !isNaN(parseFloat($('.field-' + fields[1]).val())) ? parseFloat($('.field-' + fields[1]).val()) : 0;

        this.calcData($f, symbol, v1, v2);

        var parentField = $f.data('calc-parent-field') != 0 ? $f.data('calc-parent-field') : el.data('calc-parent-field');
        if (parentField != 0) {
            var $p = $('.calc-field-' + parentField),
                symbol = $p.data('calc-type'),
                pFields = $p.data('calc-fields').split(':');

            var v1 = this.getFieldVal(pFields[0]),
                v2 = this.getFieldVal(pFields[1]);

            this.calcData($p, symbol, v1, v2);
        }
    },

    onInputText: function (e) {
        var $field = $(e.target), reg = new RegExp($field.data('regexp'));

        if ($field.data('type') != 'date') {
            if (!reg.test($field.val()) && $field.data('type') == 'dig') {
                $field.val($field.val().replace(/[^\d.]/, ''));
            }
        }

        if ($field.attr('data-calc-field')) {
            this.calcValues($field.data('calc-parent-field'), $field.data('calc-type'), $field);
        }
    },

    calcData: function ($f, symbol, v1, v2) {
        if (symbol == 'plus') {
            $f.text(v1 + v2);
        }
        else if (symbol == 'minus') {
            $f.text(v1 - v2);
        }
        else if (symbol == 'divide') {
            if (v2 != 0)
                $f.text((v1 / v2).toFixed(2));
        }
        else if (symbol == 'percent') {
            $f.text((v1 * v2 / 100).toFixed(2));
        }
    },

    getFieldVal: function (id) {
        var $f = $('.calc-field-' + id);

        if ($f.length != 0)
            return !isNaN(parseFloat($f.text())) ? parseFloat($f.text()) : 0;

        $f = $('.field-' + id);
        if ($f.length != 0)
            return !isNaN(parseFloat($f.val())) ? parseFloat($f.val()) : 0;

        return 0;
    },

    onApplyConceptToStatistic: function (e) {
        var concept = $('#sbActivityCertificates').val(),
            $bt = $(e.target),
            activity = $bt.data('activity-id'),
            self = this;

        e.preventDefault();
        if (concept == -1) {
            alert("Для продолжения выберите доступную концепцию.");
            return;
        }

        this.getImgLoader().show();
        $bt.hide();

        this.getApplyButton().attr('data-concept-id', concept);
        this.getConceptId().val(concept);

        $.post(this.on_apply_concept_to_statistic,
            {
                concept: concept,
                activity: activity
            },
            function (result) {
                self.getAccomodation().html(result);
                $('.group-content').show();

                self.getImgLoader().hide();
                $bt.show();

                self.getApplyButton().show();

                $.post(self.on_check_allow_to_edit_cancel, { concept_id: concept } , $.proxy(self.onCheckAllowCancelSuccess, self));
            });
    },

    onCheckAllowCancelSuccess: function(result) {

        if (result.allow_to_edit) {
            this.getContainerAllowToEdit().fadeIn();
        } else if (result.allow_to_cancel) {
            this.getContainerAllowToCancel().fadeIn();
        }
    },

    onApplyButtonClick: function (e) {
        e.preventDefault();

        this.sendData($(e.target));
    },

    sendData: function(from) {
        var hasError = false, data = [];

        this.save_button = from;

        $('input[type=text]').css('border', '1px solid gray').removeClass("field-position-error");
        $('input[type=text]').popmessage2('hide');

        $.each($("input[type=text]", this.getForm()), function (ind, el) {
            var regExp = new RegExp($(el).data('regexp'));

            $(el).parent().css('border-color', '');
            if ($(el).attr("required") && ($(el).val().length == 0/* || parseInt($(el).val()) == 0*/)) {
                $(el).css('border', '1px solid red').addClass("field-position-error");

                $(el).popmessage2('show', 'error', 'В полях, обязательных для заполнения, должны быть данные, отличные от 0.');
                hasError = true;
            }
            else if ($(el).data('type') == "date" && !regExp.test($(el).val())) {
                $(el).parent().css('border-color', 'red');
                hasError = true;
            }

            if ($(el).data('type') != "date")
                data.push({
                    id: $(el).data('field-id'),
                    value: $(el).val()
                });
        });

        /** Check if required files to upload */
        $.each($('input[type=file]', this.getForm()), function(ind, el) {
            console.log($(el).closest('.file'));
            $(el).closest('.file').css('border-color', '');
            if ($(el).attr("required") && ($(el).val().length == 0 || parseInt($(el).val()) == 0)) {
                $(el).closest('.file').css('border', '1px solid red !important').addClass("field-position-error");

                $(el).popmessage2('show', 'error', 'В полях, обязательных для заполнения, должны быть данные.');
                hasError = true;
            }
        });

        var startDate = this.getFieldDateTime($('input[name*=Start]')),
            endDate = this.getFieldDateTime($('input[name*=End]'));

        if (startDate == undefined || endDate == undefined) {
            this.scrollTop('dates');
            return;
        }

        if (endDate < startDate) {
            $('input[name*=Start]').parent().css('border-color', 'red');
            $('input[name*=End]').parent().css('border-color', 'red');

            hasError = true;
        }

        if (hasError) {
            //this.getInfoPanelMsg().fadeIn();
            this.scrollTop("field-position-error");
            return;
        }

        data.push({
            id: $('input[name*=Start]').data('field-id'),
            value: $('input[name*=Start]').val() + '-' + $('input[name*=End]').val()
        });

        this.getSaveModal('hide');

        this.getContainerAllowToEdit().fadeOut();
        this.save_button.fadeOut();

        this.getInfoPanelMsg().fadeOut();
        this.getFormFieldsData().val(JSON.stringify(data));

        this.getForm().attr('action', this.on_activity_extended_change_stats);
        this.getForm().submit();
    },

    onSaveDataCompleted: function(data) {
        var $self = this;

        if (data.success) {
            if (data.allow_to_edit) {
                this.getInfoPanelMsg().html('Параметры статистики успешно сохранены !').fadeIn();
                this.getContainerAllowToEdit().fadeIn();
            } else if (data.allow_to_cancel) {
                this.getContainerAllowToCancel().fadeIn();
            }
        } else {
            this.getInfoPanelMsg().html(data.msg).fadeIn();
        }

        setTimeout(function() {
            $self.getInfoPanelMsg().fadeOut();
            
            $self.save_button.fadeIn();
            $self.getContainerAllowToEdit().fadeIn();
        }, 3000);
    },

    getAccomodation: function() {
        return $('#accommodation');
    },

    getImgLoader: function() {
        return $('#imgLoader');
    },

    getApplyButton: function() {
        return $('.apply-stat-button');
    },

    parseDate: function (date) {
        if (date != undefined) {
            var tmp = date.split('.').reverse();

            return new Date(tmp[0], tmp[1] - 1, tmp[2]);
        }

        return null;
    },

    getFieldDateTime: function (el) {
        if ($(el) != undefined && $(el).length != 0) {
            return this.parseDate($(el).val()).getTime();
        }

        return null;
    },

    scrollTop: function (ancor) {
        $("body, html").animate
        (
            {
                scrollTop: ($("." + ancor).eq(0).offset().top - 10) + "px"
            },
            {
                duration: 500
            }
        );
    },

    getContainerAllowToEdit: function() {
        return $('#container-allow-to-edit', this.getForm());
    },

    getContainerAllowToCancel: function() {
        return $('#container-allow-to-cancel', this.getForm());
    },

    getSaveModal: function(view) {
        $('#confirm-send-data-to-specialist-modal').krikmodal(view);
    },

    getConceptId: function() {
        return $('input[name=concept_id]', this.getForm());
    },

    getSendTo: function() {
        return $('input[name=send_to]', this.getForm());
    }
}
