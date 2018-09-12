/**
 * Created by kostet
 */
ActivitySimpleStatistic = function(config) {
    // configurable {
    // }
    this.on_save_data = '';
    this.on_cancel_url = '';

    this.quarter = 0;
    this.activity_id = 0;

    $.extend(this, config);

    this.bg_colors = [];
}

ActivitySimpleStatistic.prototype = {
    start: function() {
        this.initEvents();

        $('#frmStatistics .with-date').datepicker();

        return this;
    },

    initEvents: function() {
        this.getForm().on('click', '#bt_on_save_statistic_data', $.proxy(this.onSaveStatisticData, this));
        this.getForm().on('click', '#bt_on_cancel_statistic_data', $.proxy(this.onCancelStatisticData, this));

        this.getForm().on('input', 'input[type=text]', $.proxy(this.onInputText, this));
        this.resetData();
    },

    resetData: function () {
        this.bg_colors = [];

        $('input[type=text]', this.getForm()).each(function(ind, el) {
            window.localStorage.setItem($(el).attr('class'), '');
        });
    },

    onCancelStatisticData: function(e) {
        if (confirm('Отменить данные ?')) {
            $.post(this.on_cancel_url,
                {
                    activity: this.activity_id,
                    quarter: this.quarter,
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

    onInputText: function(e) {
        var $el = $(e.target);
        var reg = new RegExp($el.data('regexp'));

        if ($el.data('type') != 'date') {
            if (!reg.test($el.val()) && $el.data('type') == 'number') {
                $el.val($el.val().replace(/[^\d.]/, ''));
            }
        }
    },

    validate: function(e) {
        var data = [], hasError = false, $bt = $(e.target);

        e.preventDefault();

        $('input[type=text]').css('border', '1px solid gray').removeClass("field-position-error");
        $('input[type=text]').popmessage2('hide');

        $.each($("input[type=text]", this.getForm()), function (ind, el) {
            var regExp = new RegExp($(el).data('regexp'));

            $(el).parent().css('border-color', '');
            if ($(el).attr("required") && ($(el).val().length == 0 || parseInt($(el).val()) == 0)) {
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

    onSaveDataCompleted: function(data) {
        this.saveResultMsg(data);
    },

    saveResultMsg: function(data) {
        var $self = this;

        if (data.success) {
            this.getInfoPanelMsg().html('Параметры статистики успешно сохранены !').fadeIn();
        } else {
            this.getInfoPanelMsg().html(data.msg).fadeIn();
        }

        if (data.hide_data) {
            this.save_button.fadeOut();

            $(':input').prop('disabled', true);
            $('#bt_add_new_group_fields').fadeOut();
            $('.on-delete-video-record-field').fadeOut();

        } else {
            this.save_button.fadeIn();
        }

        setTimeout(function() {
            $self .getInfoPanelMsg().fadeOut();
        }, 3000);
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
}