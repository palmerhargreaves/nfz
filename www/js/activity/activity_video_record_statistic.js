/**
 * Created by kostet
 */
ActivityVideoRecordStatistic = function(config) {
    // configurable {
    // }
    this.on_add_new_fields = '';
    this.on_save_data = '';
    this.on_save_importer_data = '';
    this.on_delete_field = '';
    this.on_cancel_url = '';

    //Роут для согласования / отмены выполнения статистики
    this.on_accept_statistic_data_by_user_url = '';
    this.on_cancel_statistic_data_by_user = '';

    this.quarter = 0;
    this.year = 0;
    this.activity_id = 0;

    $.extend(this, config);

    this.bg_colors = [];
}

ActivityVideoRecordStatistic.prototype = {
    start: function() {
        this.initEvents();

        return this;
    },

    initEvents: function() {
        this.getForm().on('click', '#bt_add_new_group_fields', $.proxy(this.onAddNewGroupField, this));
        this.getForm().on('click', '#bt_on_save_statistic_data_many', $.proxy(this.onSaveStatisticData, this));
        this.getForm().on('click', '#bt_on_save_statistic_data_once', $.proxy(this.onSaveImporterStatisticData, this));
        this.getForm().on('click', '#bt_on_cancel_statistic_data', $.proxy(this.onCancelStatisticData, this));

        //Обработка событий при согласовании / отмене статистики от дилера
        this.getForm().on('click', '#bt_on_accept_statistic', $.proxy(this.onAcceptByUserStatisticData, this));
        this.getForm().on('click', '#bt_on_decline_statistic', $.proxy(this.onCancelByUserStatisticData, this));

        $(document).on('click', '#bt-activity-close-pre-check', $.proxy(this.onStatisticDataToPreCheck, this));

        $(document).on('click', '#bt-send-video-record-statistic-data', $.proxy(this.onSendStatisticDataToImporter, this));
        $(document).on('click', '#bt-cancel-send-video-record-statistic', $.proxy(this.onCancelSendStatisticDataToImporter, this));

        $(document).on('click', '.modal-close', $.proxy(this.onCancelSendStatisticDataToImporter, this));

        this.getForm().on('input', 'input[type=text]', $.proxy(this.onInputText, this));
        this.getForm().on('click', '.on-delete-video-record-field', $.proxy(this.onDeleteField, this));
        this.getForm().on('mouseover', '.on-delete-video-record-field', $.proxy(this.onDeleteImgMouseOver, this));
        this.getForm().on('mouseout', '.on-delete-video-record-field', $.proxy(this.onDeleteImgMouseOut, this));

        this.resetData();
    },

    resetData: function () {
        this.bg_colors = [];

        $('input[type=text]', this.getForm()).each(function(ind, el) {
            window.localStorage.setItem($(el).attr('class'), '');
        });
    },

    onAcceptByUserStatisticData: function(e) {
        e.preventDefault();

        if (confirm('Согаласовать данные по статистике ?')) {
            $.post(this.on_accept_statistic_data_by_user_url,
                {
                    activity: this.activity_id,
                    quarter: this.quarter,
                    year: this.year
                },
                $.proxy(this.onAcceptStatisticDataByUserSuccess, this)
            );
        }
    },

    onAcceptStatisticDataByUserSuccess: function(data) {
        if (data.success) {
            window.location.reload();
        }
    },

    onCancelByUserStatisticData: function(e) {
        e.preventDefault();

        if (confirm('Отклонить данные по статистике ?')) {
            $.post(this.on_cancel_statistic_data_by_user,
                {
                    activity: this.activity_id,
                    quarter: this.quarter,
                    year: this.year
                },
                $.proxy(this.onCancelStatisticDataByUserSuccess, this)
            );
        }
    },

    onStatisticDataToPreCheck: function() {
        this.getActivityAcceptCancelMessageDialog('hide');

        window.location.reload();
    },

    onCancelStatisticDataByUserSuccess: function(data) {
        if (data.success) {
            window.location.reload();
        }
    },

    onCancelStatisticData: function(e) {
        e.stopPropagation();

        if (confirm('Отменить данные ?')) {
            $.post(this.on_cancel_url,
                {
                    activity: this.activity_id,
                    quarter: this.quarter,
                    year: this.year
                },
                $.proxy(this.onCancelStatisticDataSuccess, this)
            );
        }
    },

    onCancelStatisticDataSuccess: function(data) {
        if (data.success) {
            window.location.reload();
        }
    },

    onDeleteImgMouseOver: function(e) {
        var $el = $(e.target), $self = this;

        this.bg_colors = [];
        $('tr.hash-' + $el.data('hash')).each(function(ind, el) {
            $self.bg_colors.push({
                color: $(el).find('td').eq(0).css('background-color'),
                hash: $el.data('hash'),
                ind: ind
            });

           $(el).find('td').eq(0).css('background-color', '#f3c0c3');
        });
    },

    onDeleteImgMouseOut: function(e) {
        this.bg_colors.forEach(function(obj) {
            $('tr.hash-' + obj.hash).eq(obj.ind).find('td').eq(0).css('background-color', obj.color);
        });
    },

    onDeleteField: function(e) {
        var $el = $(e.target);

        if (confirm('Удалить пол(е,я) ?')) {
            $.post(this.on_delete_field, { activity: this.activity_id, field_id: $el.data('field-id')}, $.proxy(this.onDeleteFieldComplete, this));
        }
    },

    onDeleteFieldComplete: function(data) {
        this.applyData(data);
    },

    onInputText: function(e) {
        var $el = $(e.target);
        var reg = new RegExp($el.data('regexp'));

        if ($el.data('type') != 'date') {
            if (!reg.test($el.val()) && $el.data('type') == 'number') {
                $el.val($el.val().replace(/[^\d.]/, ''));
            }
        }

        if (window.localStorage) {
            window.localStorage.setItem($el.attr('class'), $el.val());
        }
    },

    onAddNewGroupField: function(e) {
        var $bt = $(e.target);

        if (confirm('Добавить поля ?')) {
            $.post(this.on_add_new_fields,
                {
                    activity: this.activity_id,
                    group_id: $bt.data('group-id'),
                    header_id: $bt.data('header-id')
                },
                $.proxy(this.onAddGroupFieldsSuccess, this)
            );
        }
    },

    onAddGroupFieldsSuccess: function(data) {
        this.applyData(data);
    },

    applyData: function(data) {
        this.getFieldsContainer().html(data);

        $('#materials .group-content').show();

        $('input[type=text]', this.getForm()).each(function(ind, el) {
            if ($(el).val().length == 0) {
                $(el).val(window.localStorage.getItem($(el).attr('class')));
            }
        });
    },

    validate: function(e) {
        var data = [], hasError = false, $bt = $(e.target);

        e.preventDefault();

        $('input[type=text]').css('border', '1px solid gray').removeClass("field-position-error");
        $('input[type=text]').popmessage2('hide');

        $.each($("input[type=text]", this.getForm()), function (ind, el) {
            var regExp = new RegExp($(el).data('regexp'));

            $(el).parent().css('border-color', '');
            if ($(el).attr("required") && ($(el).val().length == 0 /*|| parseInt($(el).val()) == 0*/)) {
                $(el).css('border', '1px solid red').addClass("field-position-error");

                $(el).popmessage2('show', 'error', 'В полях, обязательных для заполнения, должны быть данные, отличные от 0.');
                hasError = true;
            }
            else if ($(el).data('type') == "date" && !regExp.test($(el).val())) {
                $(el).parent().css('border-color', 'red');
                hasError = true;
            }

            if ($(el).data('type') != "date") {
                data.push({
                    id: $(el).data('field-id'),
                    value: $(el).val()
                });
            } else {
                data.push({
                    id: $(el).data('field-id'),
                    value: $('input[name=periodStart]').val() + '-' + $('input[name=periodEnd]').val()
                });
            }
        });

        var self = this;

        $.each($('input[type=file]', this.getForm()), function(ind, el) {
            if ($(el).attr("required") && ($(el).val().length == 0)) {
                $(el).css('border', '1px solid red').addClass("field-position-error");

                addShakeAnim('.file', self.getForm());
                showAlertPopup('При заполнении формы возникли следующие ошибки:', 'Загрузите файл');

                hasError = true;
            }
        });

        if (!hasError) {
            this.getFormFieldsData().val(JSON.stringify(data));

            return true;
        }

        return false;
    },

    onSaveStatisticData: function(e) {
        var result = this.validate(e), $bt_container = $(e.target).parent();

        if (result) {
            $bt_container.fadeOut();
            this.save_button = $bt_container;

            this.getForm().attr('action', this.on_save_data);
            this.getForm().submit();
        }
    },

    onSaveImporterStatisticData: function(e) {
        var result = this.validate(e), $bt_container = $(e.target).parent();

        this.save_button = $bt_container;
        if (result) {
            $bt_container.fadeOut();

            this.getConfirmDialog('show');
        }
    },

    onSendStatisticDataToImporter: function() {
        this.getForm().attr('action', this.on_save_importer_data);

        this.getForm().submit();
        this.getConfirmDialog('hide');

        if ($('#activity-accept-cancel-info-modal').length > 0) {
            this.getActivityAcceptCancelMessageDialog('show');
        }
    },

    onCancelSendStatisticDataToImporter: function() {
        if (this.save_button != undefined) {
            this.save_button.fadeIn();
        }

        this.getConfirmDialog('hide');
    },

    onSaveDataCompleted: function(data) {

        this.saveResultMsg(data);
    },

    onSaveImporterDataCompleted: function(data) {
        this.saveResultMsg(data);
    },

    saveResultMsg: function(data) {
        var $self = this;

        if (data.success) {
            if (data.hide_data) {
                this.getInfoPanelMsg().css('color', '#23b33a').html('Ваша статистика успешно отправлена Импортеру!').fadeIn();
            } else {
                this.getInfoPanelMsg().html('Параметры статистики успешно сохранены !').fadeIn();
            }
        } else {
            this.getInfoPanelMsg().html(data.msg).fadeIn();
        }

        if (data.hide_data) {
            this.save_button.fadeOut();

            $(':input[type=text]').prop('disabled', true);
            $('#bt_add_new_group_fields').fadeOut();
            $('.on-delete-video-record-field').fadeOut();

        } else {
            this.save_button.fadeIn();
        }

        setTimeout(function() {
            $self .getInfoPanelMsg().fadeOut();
        }, 3000);
    },

    getFieldsContainer: function() {
        return $('.container-for-activity-video-record-statistics-fields', this.getForm());
    },

    getForm: function() {
        return $('#frmStatistics');
    },

    getInfoPanelMsg: function() {
        return $('.info-save-complete', this.getForm());
    },

    getFormFieldsData: function() {
        return $('#txt_frm_fields_data', this.getForm());
    },

    getConfirmDialog: function(status) {
        return $('#confirm-send-data-to-specialist-modal').krikmodal(status);
    },

    getActivityAcceptCancelMessageDialog: function(status) {
        return $('#activity-accept-cancel-info-modal').krikmodal(status);
    },

    getButtonsContainer: function() {
        return $('#bts-container');
    }
}
