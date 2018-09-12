AgreementModelSpecialistsForm = function (config) {
    // configurable {
    this.form = null; // required form selector
    this.url = ''; // required url to send for specialists
    // }

    this.addEvents({
        cancel: true
    });

    AgreementModelSpecialistsForm.superclass.constructor.call(this, config);
}

utils.extend(AgreementModelSpecialistsForm, AjaxForm, {
    start: function () {
        AgreementModelSpecialistsForm.superclass.start.call(this);

        this.initForm();

        return this;
    },

    initEvents: function () {
        AgreementModelSpecialistsForm.superclass.initEvents.call(this);

        this.getForm().on('click', '.msg-button', $.proxy(this.onClickMsgButton, this));
        this.getForm().on('click', '.krik-select', $.proxy(this.onClickUsersList, this));
        this.getCancelButton().click($.proxy(this.onClickCancelButton, this));
    },

    initForm: function () {
        this.getForm().attr('action', this.url);
    },

    setId: function (id) {
        this.getIdField().val(id);
    },

    reset: function () {
        AgreementModelSpecialistsForm.superclass.reset.call(this);

        $('.msg-button', this.getForm()).removeClass('active');
        $('.specialist-message', this.getForm()).hide();
        $('.check :input', this.getForm()).removeAttr('checked');
    },

    resetWithId: function (id) {
        this.reset();
        this.setId(id);
    },

    validate: function () {
        if ($('.check :input:checked', this.getForm()).length > 0)
            return true;

        alert("Выберите хотябы одного специалиста");

        return false;
    },

    getMessageEl: function (id) {
        return $('[name="specialist[msg][' + id + ']"]', this.getForm());
    },

    getGroupCheckbox: function (id) {
        return $('[name="specialist[group][' + id + ']"]', this.getForm());
    },

    getIdField: function () {
        return $(':input[name=id]', this.getForm());
    },

    getCancelButton: function () {
        return $('.cancel-btn', this.getForm());
    },

    onClickMsgButton: function (e) {
        var $btn = $(e.target).closest('.msg-button');
        var group_id = $btn.closest('.group-row').data('group');
        if ($btn.hasClass('active')) {
            $btn.removeClass('active');
            this.getMessageEl(group_id).closest('.specialist-message').hide();
        } else {
            $btn.addClass('active');
            this.getMessageEl(group_id).closest('.specialist-message').show();
            this.getGroupCheckbox(group_id).attr('checked', 'checked');
        }
    },

    onClickUsersList: function (e) {
        var group_id = $(e.target).closest('.group-row').data('group');
        this.getGroupCheckbox(group_id).attr('checked', 'checked');
    },

    onClickCancelButton: function () {
        this.fireEvent('cancel', [this]);
    }
});