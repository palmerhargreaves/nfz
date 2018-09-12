/**
 * Created by kostet on 15.01.2017.
 */

AgreementModelCategoryFields = function (config) {
    this.on_save_category_fields = '';

    $.extend(this, config);

    this.category_txt_field = null;
}

AgreementModelCategoryFields.prototype = {
    start: function () {
        this.initEvents();

        return this;
    },

    initEvents: function() {
        this.getCategoryField().on('keydown', $.proxy(this.onKeyDown, this));
        this.getCategoryField().on('input', $.proxy(this.onChangeValue, this));

        this.getBtnCategoryAddNewField().on('click', $.proxy(this.onAddFieldData, this));
        this.getBtnCategoryRemoveField().on('click', $.proxy(this.onRemoveFieldData, this));
    },

    onKeyDown: function(event) {
        event.preventDefault();
    },

    onChangeValue: function(e) {
        var $from = $(e.target);

        if ($from.val() < $from.data('def-value')) {
            this.getBtnCategoryAddNewField($from).hide();
            this.getBtnCategoryRemoveField($from).show();
        } else if ($from.val() > $from.data('def-value')) {
            this.getBtnCategoryAddNewField($from).show();
            this.getBtnCategoryRemoveField($from).hide();
        } else {
            this.getBtnCategoryAddNewField($from).hide();
            this.getBtnCategoryRemoveField($from).hide();
        }
    },

    onAddFieldData: function(event) {
        event.preventDefault();

        if (confirm('Добавить поле (я) ?')) {
            this.onSaveFieldData(event);
        }
    },

    onRemoveFieldData: function(event) {
        event.preventDefault();

        if (confirm('Удалить поле (я) ?')) {
            this.onSaveFieldData(event);
        }
    },

    onSaveFieldData: function(event) {
        var $from = $(event.target), field_id = $from.data('field-id');

        this.category_txt_field = this.getCategoryTxtField(field_id);
        add_save_fields = this.category_txt_field.val();

        this.category_txt_field.data('def-value', add_save_fields);
        $.post(this.on_save_category_fields,
            {
                add_save_fields: add_save_fields,
                field_id: field_id
            },
            $.proxy(this.onSaveSuccess, this));
    },

    onSaveSuccess: function(result) {
        this.getBtnCategoryAddNewField().hide();
        this.getBtnCategoryRemoveField().hide();
    },

    getCategoryField: function() {
        return $('.on-save-category-field');
    },

    getBtnCategoryAddNewField: function($parent) {
        if ($parent != undefined) {
            return $('.btn-category-field-add-' + $parent.data('field-id'));
        }

        return $('.btn-category-add-new-field');
    },

    getBtnCategoryRemoveField: function($parent) {
        if ($parent != undefined) {
            return $('.btn-category-field-delete-' + $parent.data('field-id'));
        }

        return $('.btn-category-remove-field');
    },

    getCategoryTxtField: function(field_id) {
        return $('#txt_field_category_field_' + field_id);
    }
}


