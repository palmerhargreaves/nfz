/**
 * Created by kostet on 19.05.2017.
 */

AgreementCategoryMimeTypes = function (config) {
    this.js_add_mime_type_action = '';
    this.on_get_mime_types_list = '';
    this.on_mime_type_check = '';
    this.dialog = '';
    this.dialog_content = '';
    this.js_mime_type_check = '';

    $.extend(this, config);
}

AgreementCategoryMimeTypes.prototype = {
    start: function () {
        this.initEvents();

        return this;
    },

    initEvents: function () {
        $(document).on('click', this.js_add_mime_type_action, $.proxy(this.onShowMimeTypeAddDialog, this));
        $(document).on('change', this.js_mime_type_check, $.proxy(this.onCheckMimeType, this));
    },

    onCheckMimeType: function (event) {
        var $element = $(event.target);

        $.post(this.on_mime_type_check, {
                category_id: $element.data('category-id'),
                mime_type_id: $element.data('mime-id'),
                check_action_type: $element.is(':checked') ? 1 : 0
            },
            $.proxy(this.onCheckMimeTypeSuccess, this));
    },

    onCheckMimeTypeSuccess: function () {

    },

    onShowMimeTypeAddDialog: function (event) {
        var $element = $(event.target);

        $.post(this.on_get_mime_types_list, {id: $element.data('id')}, $.proxy(this.onShowMimeTypesDialogSuccess, this));
    },

    onShowMimeTypesDialogSuccess: function (result) {
        this.getDialogContent().empty().html(result);
        this.getDialog().modal('show');
    },

    getDialogContent: function () {
        return $(this.dialog_content, this.getDialog());
    },

    getDialog: function () {
        return $(this.dialog);
    }
}
