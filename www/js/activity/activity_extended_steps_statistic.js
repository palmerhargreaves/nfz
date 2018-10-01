/**
 * Created by kostet
 */
ActivityExtendedStepsStatistic = function (config) {
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

ActivityExtendedStepsStatistic.prototype = {
    start: function () {
        this.initEvents();

        return this;
    },

    initEvents: function () {
        this.getForm().on('click', '.bt_on_save_statistic_data_importer', $.proxy(this.onShowSaveDialog, this));
        this.getForm().on('click', '.bt_on_save_statistic_data', $.proxy(this.onApplyButtonClick, this));
        this.getForm().on('click', '#btApplyConceptToStepsStatistic', $.proxy(this.onApplyConceptToStatistic, this));
        this.getForm().on('click', '#apply-stat-button', $.proxy(this.onApplyButtonClick, this));

        this.getForm().on('click', '.bt_on_cancel_statistic_data', $.proxy(this.onCancelStatisticData, this));

        $(document).on('click', '#bt-send-statistic-data', $.proxy(this.onSendDataToImporterClick, this));
        $(document).on('click', '#bt-close-modal, .modal-close', $.proxy(this.onCloseModal, this));

        this.getForm().on('input', 'input[type=text]', $.proxy(this.onInputText, this));
        this.getFormWithDateFields().datepicker();
    },

    onCancelStatisticData: function (event) {
        var element = $(event.target);

        event.preventDefault();
        if (confirm('Отменить отправку данных ?')) {
            $.post(this.on_cancel_statistic,
                {
                    concept_id: this.getConceptId().val(),
                    step_status_id: element.data('step-status-id'),
                    step_id: element.data('step-id')
                },
                $.proxy(this.onCancelStatisticDataSuccess, this));
        }
    },

    onCancelStatisticDataSuccess: function (result) {
        if (result.success) {
            $("input[data-step-id='" + result.step_id + "']").removeAttr("disabled");

            this.getContainerAllowToEdit(result.step_id).fadeIn();
            this.getContainerAllowToCancel(result.step_id).fadeOut();
        } else {
            alert('Ошибка. Обратитесь в тех. поддержку.');
        }
    },

    onSendDataToImporterClick: function (e) {
        e.preventDefault();

        this.getSendTo().val('importer');
        this.sendData($(e.target));
    },

    onCloseModal: function (event) {
        var element = $(event.target);

        this.getContainerAllowToEdit(element.data('step-id')).fadeIn();
        this.getSaveModal('hide');
    },

    onShowSaveDialog: function (e) {
        e.preventDefault();

        this.getContainerAllowToEdit($(e.target).data('step-id')).fadeOut();
        this.getSaveModal('show');

        this.getStepId().val($(e.target).data('step-id'));
    },

    getForm: function () {
        return $('#frmStatistics');
    },

    getFormWithDateFields: function () {
        return $('.with-date', this.getForm());
    },

    getInfoPanelMsg: function (step_id) {
        return $('.info-save-complete-' + step_id);
    },

    getFormFieldsData: function () {
        return $('#txt_frm_fields_data', this.getForm());
    },

    calcValues: function (field, symbol, el) {
        var $f = $('.calc-field-' + field);

        if ($f.data('calc-fields') != undefined) {
            var fields = $f.data('calc-fields').split(":"),
                v1 = !isNaN(parseFloat($('.field-' + fields[0]).val())) ? parseFloat($('.field-' + fields[0]).val()) : 0,
                v2 = !isNaN(parseFloat($('.field-' + fields[1]).val())) ? parseFloat($('.field-' + fields[1]).val()) : 0;

            this.calcData($f, symbol, v1, v2);
        }

        var parentField = $f.data('calc-parent-field') != 0 ? $f.data('calc-parent-field') : el.data('calc-parent-field');
        if (parentField != 0) {
            var $p = $('.calc-field-' + parentField),
                symbol = $p.data('calc-type');

            if ($p.data('calc-fields') != undefined) {
                pFields = $p.data('calc-fields').split(':');

                var v1 = this.getFieldVal(pFields[0]),
                    v2 = this.getFieldVal(pFields[1]);

                this.calcData($p, symbol, v1, v2);
            }
        }
    },

    onInputText: function (e) {
        var $field = $(e.target), reg = new RegExp($field.data('regexp'));

        if ($field.data('type') != 'date') {
            if (!reg.test($field.val()) && ($field.data('type') == 'dig' || $field.data('type') == 'money')) {
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
            $f.text((v1 *100 /  v2).toFixed(2));
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

                self.getFormWithDateFields().datepicker();
                //$.post(self.on_check_allow_to_edit_cancel, { concept_id: concept } , $.proxy(self.onCheckAllowCancelSuccess, self));
            });
    },

    onCheckAllowCancelSuccess: function (result) {

        if (result.allow_to_edit) {
            this.getContainerAllowToEdit().fadeIn();
        } else if (result.allow_to_cancel) {
            this.getContainerAllowToCancel().fadeIn();
        }
    },

    onApplyButtonClick: function (e) {
        e.preventDefault();

        this.getStepId().val($(e.target).data('step-id'));

        this.sendData($(e.target));
    },

    sendData: function (from) {
        var hasError = false, data = [], input_element = "input[data-step-id='" + this.getStepId().val() + "']";

        this.save_button = from;

        $(input_element).css('border', '1px solid gray').removeClass("field-position-error");
        $(input_element).popmessage2('hide');

        $.each($(input_element, this.getForm()), function (ind, el) {
            var regExp = new RegExp($(el).data('regexp'));

            $(el).parent().css('border-color', '');
            if ($(el).attr("required") && ($(el).val().length == 0/* || parseInt($(el).val()) == 0*/)) {
                $(el).css('border', '1px solid red').addClass("field-position-error");

                $(el).popmessage2('show', 'error', 'В полях, обязательных для заполнения, должны быть данные, отличные от 0.');
                hasError = true;
            }
            else if ($(el).data('type') == "date" && !regExp.test($(el).val())) {
                $(el).parent().css('border-color', 'red').addClass("field-position-error");

                hasError = true;
            }

            if ($(el).data('type') != "date" && $(el).data('type') != "money") {
                data.push({
                    id: $(el).data('field-id'),
                    value: $(el).val()
                });
            } else if ($(el).data('type') == "money") {
                var moneyCurrency = $('input[name*=Currency][data-field-id=' + $(el).data('field-id') + ']'),
                    moneyCoins = $('input[name*=Coins][data-field-id=' + $(el).data('field-id') + ']');

                if (moneyCurrency != undefined && moneyCoins != undefined) {
                    data.push({
                        id: $(el).data('field-id'),
                        value: $('input[name*=Currency][data-field-id=' + $(el).data('field-id') + ']').val() + ":" + $('input[name*=Coins][data-field-id=' + $(el).data('field-id') + ']').val()
                    });
                }
            }
        });

        /** Check if required files to upload */
        $.each($(input_element, this.getForm()), function (ind, el) {

            $(el).closest('.file').css('border-color', '');
            if ($(el).attr("required") && ($(el).val().length == 0/* || parseInt($(el).val()) == 0*/)) {
                $(el).closest('.file').css('border', '1px solid red !important').addClass("field-position-error");

                $(el).popmessage2('show', 'error', 'В полях, обязательных для заполнения, должны быть данные.');
                hasError = true;
            }
        });

        var startDate = this.getFieldDateTime($('input[name*=Start][data-step-id=' + this.getStepId().val() + ']')),
            endDate = this.getFieldDateTime($('input[name*=End][data-step-id=' + this.getStepId().val() + ']'));

        if (startDate != undefined && endDate != undefined) {
            if (endDate < startDate) {
                $('input[name*=Start], input[name*=End]').addClass("field-position-error");
                $('input[name*=Start], input[name*=End]').parent().css('border-color', 'red');

                hasError = true;
            }

            data.push({
                id: $('input[name*=Start][data-step-id=' + this.getStepId().val() + ']').data('field-id'),
                value: $('input[name*=Start][data-step-id=' + this.getStepId().val() + ']').val() + '-' + $('input[name*=End][data-step-id=' + this.getStepId().val() + ']').val()
            });
        }

        if (hasError) {
            //this.getInfoPanelMsg().fadeIn();
            this.scrollTop("field-position-error");

            setTimeout(function () {
                $(".field-position-error").css('border-color', '');
                $(".field-position-error").removeClass("field-position-error");

                $(".message-modal").fadeOut();
            }, 3000);
            return;
        }

        this.getSaveModal('hide');

        this.getContainerAllowToEdit(this.getStepId().val()).fadeOut();
        this.save_button.fadeOut();

        this.getInfoPanelMsg(this.getStepId().val()).fadeOut();
        this.getFormFieldsData().val(JSON.stringify(data));

        this.getForm().attr('action', this.on_activity_extended_change_stats);
        this.getForm().submit();
    },

    onSaveDataCompleted: function (data) {
        var $self = this;

        //Обнуляем данные по импортеру
        this.getSendTo().val('');
        this.getStepId().val(0);

        $self.save_button.fadeIn();
        //Проверка на успешное сохранение данных
        if (data.success) {
            if (data.allow_to_edit) {
                this.getInfoPanelMsg(data.step_id).html('Параметры статистики успешно сохранены !').fadeIn();

                this.getContainerAllowToEdit(data.step_id).fadeIn();
            } else if (data.allow_to_cancel) {
                $('input[data-step-id="' + data.step_id + '"]').attr("disabled", "disabled");
                this.getContainerAllowToCancel(data.step_id).fadeIn();
            }
        } else {
            this.getInfoPanelMsg(data.step_id).html(data.msg).fadeIn();

            $self.getContainerAllowToEdit(data.step_id).fadeIn();
        }

        setTimeout(function () {
            $self.getInfoPanelMsg(data.step_id).fadeOut();

        }, 3000);
    },

    getAccomodation: function () {
        return $('#accommodation');
    },

    getImgLoader: function () {
        return $('#imgLoader');
    },

    getApplyButton: function () {
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

    getContainerAllowToEdit: function (step_id) {
        return $('#container-allow-to-edit' + (step_id != undefined && step_id != 0 ? '-' + step_id : ''), this.getForm());
    },

    getContainerAllowToCancel: function (step_id) {
        return $('#container-allow-to-cancel' + (step_id != undefined && step_id != 0 ? '-' + step_id : ''), this.getForm());
    },

    getSaveModal: function (view) {
        $('#confirm-send-data-to-specialist-modal').krikmodal(view);
    },

    getConceptId: function () {
        return $('input[name=concept_id]', this.getForm());
    },

    getSendTo: function () {
        return $('input[name=send_to]', this.getForm());
    },

    getStepId: function () {
        return $('input[name=step_id]', this.getForm());
    }
}
