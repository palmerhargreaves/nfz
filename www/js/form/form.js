Form = function (config) {
    // configurable {
    this.form = ''; // required form selector
    this.success_modal = ''; // modal with a success message
    this.modal_selector = null; // modal selector to auto reset form by open window
    this.default_message_field = false;
    this.default_messages = {}
    this.button_selector = ':submit, .submit-btn';
    this.enable_loader_image = true;
    this.loader_image = '/images/form-loader.gif';
    this.MAX_FILE_SIZE = 10194304;
    // }

    this.addEvents({
        success: true,
        error: true
    });

    Form.superclass.constructor.call(this, config);

    this.loader_selector = null;
}

utils.extend(Form, utils.Observable, {
    start: function () {
        this.initEvents();
        this.initModal();
        this.showWarnings();

        return this;
    },

    initEvents: function () {
        this.getForm().submit($.proxy(this.onSubmit, this));

        $(':input[name=accept_in_model]').live('input', function (e) {
            var regEx = new RegExp(/^[0-9.]+$/);

            if ($(this).val().length == 0)
                $(this).val(0);

            if (!regEx.test($(this).val())) {
                $(this).popmessage('show', 'error', 'Только числа.');
                $(this).val($(this).val().replace(/[^\d]/, ''));

            }
        });
    },

    initModal: function () {
        if (!this.modal_selector)
            return;

        $(this.modal_selector).on('show-modal', $.proxy(this.onOpenModal, this));
    },

    showLoader: function () {
        if (this.enable_loader_image) {
            /*this.getLoader().show();
             this.getButton().hide();*/

            this.getButton().parent().addClass('loader-bg');
        }
    },

    hideLoader: function () {
        if (this.enable_loader_image) {
            /*this.getLoader().hide();
             this.getButton().show();*/

            this.getButton().parent().removeClass('loader-bg');
        }
    },

    send: function () {
        this.showLoader();

        this.getForm().submit();
    },

    showWarnings: function () {
        $(':input', this.getForm()).popmessage('show', 'warning');
    },

    reset: function () {
        this.getForm().get(0).reset();
        $(':input', this.getForm()).popmessage('hide');
        $(':input', this.getForm()).trigger('reset').trigger('update');

        $('.model-form-selected-files-to-upload').empty();
        $('.input_field_appended').remove();

        $.each(this.default_messages, $.proxy(function (name, text) {
            $(':input[name="' + name + '"]', this.getForm()).popmessage('show', 'warning', text);
        }, this));
    },

    validate: function () {
        var valid = true;

        var $acceptInModel = $(':input[name=accept_in_model]');
        if ($.trim($acceptInModel.val()).length != 0) {
            if (parseInt($acceptInModel.val()) == NaN) {
                $acceptInModel.popmessage('show', 'error', 'Только числа.');
                valid = false;
            }
        }

        $(':input', this.getForm()).filter(function () {
            return $(this).data('skip-validate') != 'true' && !$(this).hasClass('empty');
        }).each(function () {
            var $field = $(this);
            var value = $.trim($field.val());

            if ($field.data('required')) {
                if (value == '' || $field.is(':checkbox') && !$field.is(':checked')) {
                    $field.popmessage('show', 'error', 'Поле должно быть заполнено');
                    valid = false;

                    return;
                }
            } else if (value == '') {
                return;
            }

            if (!$field.data('format-expression'))
                return;

            var re = new RegExp($field.data('format-expression'), $field.data('format-expression-flags'));
            if (!re.test(value)) {
                var msg = "Введено неверное значение.";

                if ($field.data('right-format'))
                    msg += '<br/>Пример: ' + $field.data('right-format');

                $field.popmessage('show', 'error', msg);

                valid = false;
            }

        });

        $places_upload_files = $('.additional-files-to-upload-with-places', this.getForm());

        if ($places_upload_files.length != 0 && $places_upload_files.attr('data-required') != undefined) {
            valid = false;
        }

        var selModelType = $(".select-value-model-type", this.getForm());
        if (selModelType.length != 0 && selModelType.text().length == 0) {
            selModelType.popmessage('show', 'error', 'Выберите тип модели');
            valid = false;
        }

        var selModelConcept = $(".select-value-model-concept", this.getForm());
        if (selModelConcept.length != 0 && selModelConcept.text().length == 0) {
            selModelConcept.popmessage('show', 'error', 'Выберите мероприятие');
            valid = false;
        }

        $(':input[name*="[size][start]"], :input[name*="[size][end]"]', this.getForm()).filter(function () {
            return parseFloat(this.value).toFixed(1) == 0;
        }).each(function () {
            var $field = $(this);

            $field.popmessage('show', 'error', 'Введено неверное значение.');
            valid = false;
        });


        return valid;
    },

    getButton: function () {
        return $(this.button_selector, this.getForm());
    },

    getLoader: function () {
        if (!this.loader_selector) {
            var $loader = $('<img src="' + this.loader_image + '" class="form-loader" alt="загрузка..." alt="загрузка..."/>').insertAfter(this.getButton());
            this.loader_selector = $loader.getIdSelector();
        }
        return $(this.loader_selector);
    },

    getForm: function () {
        return $(this.form);
    },

    checkFieldFileFormat: function (field, formats) {
        var result = true;

        var win_pattern = /.*\\(.*)/;
        var unix_pattern = /.*\/(.*)/;

        var self = this;

        $.each($("input[name*=" + field + "]", this.getForm()), function (ind, el) {
            var name = $(el).val();

            if (name.length != 0) {
                var file_title = name.replace(win_pattern, "$1");
                file_title = file_title.replace(unix_pattern, "$1");

                if (!self.checkModelTypeFileFormat(el, file_title, formats)) {
                    result = false;
                }
            }
        });

        return result;
    },

    checkModelTypeFileFormat: function (file, file_title, formats) {
        if (!this.checkFileFormat(file_title, formats)) {
            $(file).popmessage('show', 'error', 'Неверный формат файла.');

            return false;
        }

        return true;
    },

    checkFileFormat: function (file, formats) {
        //var formats = ['txt', 'doc', 'docx', 'rtf', 'pdf', 'ods'], ext = file.split('.')[1];
        var ext = file.split('.').pop();
        if ($.inArray(ext.toLowerCase(), formats) != -1)
            return true;

        return false;
    },

    onSubmit: function () {
        $(':input', this.getForm()).popmessage('hide');

        if (!this.isConcept()) {
            var bt = this.getButton();

            if (bt.hasClass('accept-from-draft')) {

                var id = $($('input[type="hidden"][name="id"]')[0]).val(),
                    fDate = '', lDate = '', today = new Date().getTime() + (2 * 86400000);
                ;

                fDate = this.getElDate('start');
                lDate = this.getElDate('end');

                if (this.getEl('start') && this.getEl('end')) {

                    if (fDate < today && this.getEl('start')) {
                        this.getEl('start').popmessage('show', 'error', 'Необходимо исправить период размещения');
                        return false;
                    }

                    if (lDate < today && this.getEl('end')) {
                        this.getEl('end').popmessage('show', 'error', 'Необходимо исправить период размещения');
                        return false;
                    }

                    if (fDate >= lDate) {
                        this.getEl('start').popmessage('show', 'error', 'Необходимо исправить период размещения');
                        this.getEl('end').popmessage('show', 'error', 'Необходимо исправить период размещения');

                        return false;
                    }
                }
            }

            var result = this.validate();
            if (result) {
                this.showLoader();

                return true;
            }
        }
        else {
            var valid = true;
            $('input.dates-field', this.getForm()).each(function () {
                var $field = $(this);
                var value = $.trim($field.val());

                if ($field.data('required')) {
                    if (value == '' || $field.is(':checkbox') && !$field.is(':checked')) {
                        $field.popmessage('show', 'error', 'Поле должно быть заполнено');
                        valid = false;

                        return;
                    }
                } else if (value == '') {
                    return;
                }

                if (!$field.data('format-expression'))
                    return;

                var re = new RegExp($field.data('format-expression'), $field.data('format-expression-flags'));
                if (!re.test(value)) {
                    var msg = "Введено неверное значение.";

                    if ($field.data('right-format'))
                        msg += '<br/>Пример: ' + $field.data('right-format');

                    $field.popmessage('show', 'error', msg);

                    valid = false;
                }
            });

            if (valid) {
                this.showLoader();

                return true;
            }
        }

        return false;
    },

    onResponse: function (data, temp) {
        this.showWarnings();

        if (data.success) {
            this.fireEvent('success', [this, data]);

            if (data.redirect) {
                window.location.href = data.redirect;
                return;
            }

            this.onSuccess();
        } else {
            this.hideLoader();
            this.fireEvent('error', [this, data]);
            this.onError(data.errors);
        }
    },

    onError: function (errors) {
        var fileMsgError = '';

        if (!errors) {
            return;
        }

        if (!$.isArray(errors)) {
            $('#j-alert-login').html('У Вас нет прав для доступа к ресурсу!').fadeIn();
            setTimeout(function() {
                $('#j-alert-login').hide();
            }, 2500);
        } else {

            for (var i = 0; i < errors.length; i++) {
                var name = errors[i].name;
                var message = errors[i].message;
                var $field = $(':input[data-name=' + name + ']', this.getForm());

                if ($field.length == 0) {

                    console.log(errors[i]);
                    if (name != 0) {
                        var base_files_fields = ['model_file', 'model_record_file', 'additional_file', 'financial_docs_file'], self = this;

                        base_files_fields.forEach(function (val) {
                            if (name.indexOf(val) != -1) {
                                $field = $(':input[data-name=' + val + ']', self.getForm());
                            }
                        });
                    }

                    if ($field.length == 0) {
                        $field = $(':input[name=' + name + ']', this.getForm());
                    }
                }

                //
                if ($field.length == 0 && this.default_message_field) {
                    $field = $(':input[name=' + this.default_message_field + ']', this.getForm());
                }

                $field.popmessage('show', 'error', message);
                if (name == 'is_valid_data') {
                    addShakeAnim('.scroller', this.getForm());
                }

                if (name == 'is_valid_add_data') {
                    addShakeAnim('.scroller-add-docs', this.getForm());
                }

                if (name == 'is_valid_fin_data') {
                    addShakeAnim('.scroller-add-fin', this.getForm());
                }

                fileMsgError += message + '<br/>';
            }

            if (fileMsgError.length > 0) {
                showAlertPopup('При заполнении формы возникли следующие ошибки:', fileMsgError);
            }
        }
    },

    onSuccess: function () {
        $(this.success_modal).krikmodal('show');
    },

    onOpenModal: function () {
        this.reset();


    },

    isConcept: function () {
        return $('div.concept-form', this.getForm()).is(":visible");
    },

    parseDate: function (date) {
        if (date != undefined) {
            var tmp = date.split('.').reverse();

            return new Date('20' + tmp[0], tmp[1] - 1, tmp[2]);
        }

        return null;
    },

    getEl: function (el) {
        var tEl = null;

        $('input[name*="[period][' + el + ']"]').each(function (ind, el) {
            if ($(el).val() == '') {
                $(el).popmessage('show', 'error', 'Необходимо выбрать дату размещения');
            }
            else
                tEl = $(el);
        });

        return tEl;
    },

    getElDate: function (el) {
        var tmp = '';

        tmp = this.getEl(el);
        if (tmp != undefined)
            tmp = tmp.val();

        if (tmp != '') {
            tmp = this.parseDate(tmp);
            if (tmp != undefined)
                tmp = tmp.getTime();
        }

        return tmp;
    },


});
