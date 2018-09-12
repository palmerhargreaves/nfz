RegistrationForm = function (config) {
    RegistrationForm.superclass.constructor.call(this, config);

    this.onLoadCompanyPostUrl = config.onLoadCompanyPostUrl;
}

utils.extend(RegistrationForm, AjaxForm, {
    initEvents: function () {
        RegistrationForm.superclass.initEvents.call(this);

        this.getCompanyTypeField().change($.proxy(this.onChangeCompanyType, this));
        this.getCompanyDepartment().change($.proxy(this.onChangeCompanyDepartment, this));
    },

    reset: function () {
        RegistrationForm.superclass.reset.call(this);

        this.syncCompanyType();
    },

    syncCompanyType: function () {
        switch (this.getCompanyTypeField().val()) {
            case 'dealer':
                this.getCompanyBlock().hide();
                this.getDealerBlock().show();
                this.getCompanyNameField().data('required', false);
                break;
            case 'importer':
            case 'regional_manager':
                this.getCompanyBlock().hide();
                this.getDealerBlock().hide();
                this.getCompanyNameField().data('required', false);
                break;
            case 'other':
                this.getCompanyBlock().show();
                this.getDealerBlock().hide();
                this.getCompanyNameField().data('required', true);
                break;
        }
    },

    syncCompanyPost: function () {
        $.post(this.onLoadCompanyPostUrl, {companyDep: this.getCompanyDepartment().val()}, $.proxy(this.onLoadCompanyPost, this));
    },

    onLoadCompanyPost: function (data) {
        this.getCompanyPost().empty().html(data);

        if (this.getCompanyDepartment().val() != '4') {
            this.getCompanyPostBlock().show();
            $('.company-post-krik-select').krikselect();
        }
        else
            this.getCompanyPostBlock().hide();
    },

    getCompanyTypeField: function () {
        return $(':input[name=company_type]', this.getForm());
    },

    getCompanyDepartment: function () {
        return $(':input[name=company_department]', this.getForm());
    },

    getCompanyNameField: function () {
        return $(':input[name=company_name]', this.getForm());
    },

    getDealerBlock: function () {
        return $('.dealer', this.getForm());
    },

    getCompanyBlock: function () {
        return $('.company', this.getForm());
    },

    onChangeCompanyType: function () {
        this.syncCompanyType();
    },

    getCompanyDepartmentBlock: function () {
        return $('.company-department', this.getForm());
    },

    getCompanyPost: function () {
        return $('.company-post', this.getForm());
    },

    getCompanyPostBlock: function () {
        return $('.company-post-block', this.getForm());
    },

    onChangeCompanyDepartment: function () {
        this.syncCompanyPost();
    }
});