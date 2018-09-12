AgreementModelBaseForm = function (config) {
    // configurable {
    this.models_list = ''; // required selector of models list table
    this.load_url = ''; // required url to load a model row
    this.update_url = '';
    this.tab_selector = '';
    this.tabs_selector = '';
    // }

    this.addEvents({
        load: true,
        select: true
    });

    AgreementModelBaseForm.superclass.constructor.call(this, config);
}

utils.extend(AgreementModelBaseForm, Form, {
    initEvents: function () {
        AgreementModelBaseForm.superclass.initEvents.call(this);

        $('.model-row', this.getModelsList()).click($.proxy(this.onClickModel, this));
    },

    reset: function () {
        AgreementModelBaseForm.superclass.reset.call(this);

        $('.modal-file-wrapper div.control').show();

        $('.value', this.getForm()).empty();
        this.getTab().removeClass('clock ok pencil none');
    },

    loadRowToEdit: function (id) {
        this.getForm().removeClass('edit view accepted add');
        this.getForm().attr('action', this.update_url);
        this.reset();
        this.disable();

        $.post(this.load_url, {
            id: id
        }, $.proxy(this.onLoadRow, this));
    },

    applyValues: function (values) {
        $.each(values, $.proxy(function (name, value) {
            this.setValue(name, value);
        }, this));
        this.getTab().removeClass('clock ok pencil none').addClass(values.css_status);
    },

    setValue: function (name, value) {
        var $input = $(':input[data-name="' + name + '"]', this.getForm());

        if ($input.length == 0) {
            $input = $(':input[name="' + name + '"]', this.getForm());

            if ($input.length == 0) {
                return;
            }
        }

        if (!$input.is(':file')) {
            $input.val(value);
            $input.trigger('update');
        }

        var $value = $input.parents('.controls').find('.value');
        if ($input.parents('.modal-select-wrapper').length > 0) {
            $value.empty();
            $input.parents('.modal-select-wrapper').find('.select-item').each(function () {
                if ($(this).data('value') == value) {
                    $value.html($(this).html());
                    return false;
                }
            });
        } else if ($input.is(':file')) {
            if (value) {
                $value.html('<a href="' + value.path + '" target="_blank">' + value.name + ' (' + value.size + ')</a>')
            }
            else {
                $value.html('');
            }
        } else {
            $value.html(value);
        }

    },

    getValue: function (name) {
        return $(':input[name="' + name + '"]', this.getForm()).val();
    },

    disable: function () {
        this.getTab().addClass('disabled');
    },

    enable: function () {
        this.getTab().removeClass('disabled');
    },

    isDisabled: function () {
        return this.getTab().hasClass('disabled');
    },

    isEnabled: function () {
        return !this.getTab().hasClass('disabled');
    },

    activateTab: function () {
        this.getTabs().kriktab('activate', this.getTab());
    },

    getTab: function () {
        return $(this.tab_selector);
    },

    getTabs: function () {
        return $(this.tabs_selector);
    },

    getModelsList: function () {
        return $(this.models_list);
    },

    onClickModel: function (e) {
        this.fireEvent('select', [this, e.target]);
        var id = $(e.target).closest('.model-row').data('model');
        if (id) {
            this.loadRowToEdit(id);
        }
    },

    onLoadRow: function (data) {
        if (data.success) {
            this.applyValues(data.values);
            this.fireEvent('load', [this, data]);
        } else if (data.error == 'not_found') {
            alert('Макет отсутствует');
        } else if (data.error == 'wrong_status') {
            alert('Макет не может быть изменён');
        } else {
            alert('Неизвестная ошибка');
        }
    },

    onSuccess: function () {
        location.href = location.pathname + '?' + Math.random();
    }

});