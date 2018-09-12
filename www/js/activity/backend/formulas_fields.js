/**
 * Created by kostet on 16.08.2016.
 */

FormulasFields = function(config) {
    this.param1_selector = '';
    this.param2_selector = '';

    this.on_load_param_data_url = '';

    $.extend(this, config);

    this.work_field = '';
};

FormulasFields.prototype = {
    start: function() {
        this.initEvents();

        return this;
    },

    initEvents: function() {
        console.log(this.param1_selector);

        $(document).on('change', this.param1_selector, $.proxy(this.onLoadParam1Data, this));
        $(document).on('change', this.param2_selector, $.proxy(this.onLoadParam2Data, this));
    },

    onLoadParam1Data: function(e) {
        this.onLoadParamData('#activity_efficiency_formula_param_param1_value', $(e.target).val());
    },

    onLoadParam2Data: function(e) {
        this.onLoadParamData('#activity_efficiency_formula_param_param2_value', $(e.target).val());
    },

    onLoadParamData: function(field, param) {
        this.work_field = $(field);

        $.post(this.on_load_param_data_url, { param: param, formula_id: this.getFormulaId() }, $.proxy(this.onSuccessLoad, this));
    },

    onSuccessLoad: function(data) {
        this.work_field.html(data);
    },

    getFormulaId: function() {
        return $('#activity_efficiency_formula_param_formula_id').val();
    }
};
