AjaxForm = function (config) {
    AjaxForm.superclass.constructor.call(this, config);
}

utils.extend(AjaxForm, Form, {
    send: function () {
        this.showLoader();

        $.post(this.getForm().attr('action'), this.getForm().serialize(), $.proxy(this.onResponse, this));
    },

    onSubmit: function () {
        if (AjaxForm.superclass.onSubmit.call(this))
            this.send();

        return false;
    }
});