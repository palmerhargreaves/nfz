/**
 * Created by kostet on 19.08.2016.
 */

ActivityCopyStatistic = function(config) {
    this.on_copy_url = '';
    this.on_custom_copy_init_data_url = '';
    this.on_custom_make_copy_data_url = '';

    this.btn_copy = '';
    this.btn_custom_copy = '';
    this.btn_make_custom_copy = '';

    $.extend(this, config);

    this.activity_id = 0;
    this.activities_list = '';
}


ActivityCopyStatistic.prototype = {
    start: function () {
        this.initEvents();

        return this;
    },

    initEvents: function() {
        this.getCopyButton().click($.proxy(this.onCopyActivityStatistic, this));
        this.getCustomCopyButton().click($.proxy(this.onCustomCopyActivityStatistic, this));
        this.getBtnMakeCustomCopy().click($.proxy(this.onMakeCustomCopyData, this));

        this.getCustomCopyDialog().on('click', '.copy-field', $.proxy(this.onCheckBoxCopyFieldClick, this));
        this.getCustomCopyDialog().on('click', '.copy-only-formula', $.proxy(this.onCheckBoxCopyFieldFormulaClick, this));
    },

    onMakeCustomCopyData: function () {
        if (confirm('Копировать ?')) {
            var fields_to_copy = [], formulas_to_copy = [];

            this.getCheckBoxCopyField().each(function(ind, element) {
                var $el = $(element);
                if ($el.is(':checked')) {
                    fields_to_copy.push($el.data('field-id'));
                }
            });

            this.getCheckBoxCopyFieldFormula().each(function(ind, element) {
                var $el = $(element);
                if ($el.is(':checked')) {
                    formulas_to_copy.push($el.data('formula-id'));
                }
            });

            if (formulas_to_copy.length == 0) {
                this.getCheckBoxCopyOnlyFormula().each(function(ind, element) {
                    var $el = $(element);
                    if ($el.is(':checked')) {
                        formulas_to_copy.push($el.data('only-formula-id'));
                    }
                });
            }

            $.post(this.on_custom_make_copy_data_url,
                {
                    fields_to_copy: fields_to_copy,
                    formulas_to_copy: formulas_to_copy,
                    activity_id: this.activity_id,
                    activities_list: this.activities_list
                },
                $.proxy(this.onMakeCustomCopyCompleted, this)
            );
        }
    },

    onMakeCustomCopyCompleted: function(result) {
        window.location.reload();
    },

    onCheckBoxCopyFieldFormulaClick: function(e) {
        if (this.getCheckBoxCopyOnlyFormula().is(':checked')) {
            this.getBtnMakeCustomCopy().fadeIn();
        } else {
            this.getBtnMakeCustomCopy().fadeOut();
        }
    },

    onCheckBoxCopyFieldClick: function(e) {
        var $from = $(e.target);

        if ($from.is(':checked')) {
            $('.field-formulas-' + $from.data('field-id')).slideDown();
        } else {
            $('.field-formulas-' + $from.data('field-id')).slideUp();
        }

        if (this.getCheckBoxCopyField().is(':checked')) {
            this.getBtnMakeCustomCopy().fadeIn();
        } else {
            this.getBtnMakeCustomCopy().fadeOut();
        }
    },

    onCopyActivityStatistic: function(e) {
        var data = this.checkIsActivitiesSelected(e);

        if (data == null) {
            return;
        }

        if (confirm('Создать копию статистики ?')) {
            $.post(this.on_copy_url, {
                activity_id: data.activity_id,
                values: data.selected_activites.val()
            }, $.proxy(this.onCopyComplete, this));
        }
    },

    onCustomCopyActivityStatistic: function(e) {
        var data = this.checkIsActivitiesSelected(e);

        if (data == null) {
            return;
        }

        this.activity_id = data.activity_id;
        this.activities_list = data.selected_activites.val();

        $.post(this.on_custom_copy_init_data_url, {
            activity_id: data.activity_id,
            values: data.selected_activites.val()
        }, $.proxy(this.onCustomCopyInitDataComplete, this));
    },

    onCustomCopyInitDataComplete: function(data) {
        this.getCustomCopyDialogContentContainer().html(data);
        this.getCustomCopyDialog().modal('show');
    },

    checkIsActivitiesSelected: function (e) {
        var $from = $(e.target), activity_id = $from.data('from-activity'),
            selected_activities = $('.sb-copy-to-activities-' + activity_id);

        e.preventDefault();
        if (selected_activities.val() == null) {
            alert('Для продолжения выберите активности.')
            return null;
        }

        return { activity_id: activity_id, selected_activites: selected_activities };
    },

    onCopyComplete: function(result) {
        window.location.reload();
    },

    getCopyButton: function() {
        return $(this.btn_copy);
    },

    getCustomCopyButton: function() {
        return $(this.btn_custom_copy);
    },

    getCustomCopyDialog: function() {
        return $('#activities-copy-fields-formulas-modal');
    },

    getCustomCopyDialogContentContainer: function() {
        return $('.modal-content-container', this.getCustomCopyDialog());
    },

    getCheckBoxCopyField: function() {
        return $('.copy-field', this.getCustomCopyDialog);
    },

    getCheckBoxCopyFieldFormula: function() {
        return $('.copy-field-formula', this.getCustomCopyDialog);
    },

    getCheckBoxCopyOnlyFormula: function() {
        return $('.copy-only-formula', this.getCustomCopyDialog());
    },

    getBtnMakeCustomCopy: function() {
        return $(this.btn_make_custom_copy, this.getCustomCopyDialog());
    }
}