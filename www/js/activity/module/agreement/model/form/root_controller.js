AgreementModelRootControler = function (config) {
    // confugurable {
    this.report_form = null;
    this.model_form = null;
    this.discussion_controller = null;

    this.modal = ''; // required selector of form modal
    this.list_selector = ''; // required models list selector
    this.sort_url = ''; // url to sort models

    this.add_model_button = null; // required selector of a button to add model
    this.add_many_concepts_url = ''; //add many concepts

    this.btn_add_new_concept = '';

    this.model_row = '';
    this.concept_row = '';

    $.extend(this, config);

    this.mode = false;
}

AgreementModelRootControler.prototype = {
    start: function () {
        this.initEvents();
        this.checkPath();

        return this;
    },

    initEvents: function () {
        this.getAddModelButton().click($.proxy(this.onAddModel, this));
        this.model_form.on('load', this.onLoadModel, this);
        this.model_form.on('select', this.onSelectRow, this);
        this.report_form.on('load', this.onLoadReport, this);
        this.getModal().on('close-modal', $.proxy(this.onCloseModal, this));
//    this.getList().on('click', '.has-sort', $.proxy(this.onSort, this));

        //add many concepts
        this.getAddManyConceptsButton().click($.proxy(this.onAddManyConcepts, this));
        this.getActivityConceptContainer().on('click', '.many-concept', $.proxy(this.onManyConceptRowClick, this));
    },

    checkPath: function () {
        var matches = location.href.match(/#model\/([0-9]+)\/discussion\/([0-9]+)\/(.+)/);
        if (matches) {
            this.report_form.loadRowToEdit(matches[1]);
            this.model_form.loadRowToEdit(matches[1]);
            this.discussion_controller.setDiscussion(matches[2]);
            this.mode = matches[3];
        }
    },

    showModal: function () {
        this.getModal().krikmodal('show');
    },

    addModel: function () {
        this.showModal();

        this.model_form.resetToAdd();
        this.model_form.activateTab();
        this.report_form.disable();
        this.report_form.reset();
        this.discussion_controller.disable();

        this.model_form.setValue('blank_id', 0);
        this.model_form.enableTypeSelect();

        this.model_form.getDummyMsg().hide();
        window.localStorage.setItem('isOutOfDate', 0);

    },

    addConcept: function () {
        this.showModal();

        this.model_form.resetToAdd();
        this.model_form.activateTab();
        this.report_form.disable();
        this.report_form.reset();
        this.discussion_controller.disable();

        this.model_form.setValue('model_type_id', this.model_form.concept_type_id);
        this.model_form.enableTypeSelect();
    },

    addModelFromBlank: function ($row) {
        this.addModel();
        this.model_form.setValue('blank_id', $row.data('blank'));
        this.model_form.setValue('name', $row.data('name'));
        this.model_form.setValue('model_type_id', $row.data('type'));
        this.model_form.disableTypeSelect();
    },

    getList: function () {
        return $(this.list_selector);
    },

    getAddModelButton: function () {
        return $(this.add_model_button);
    },

    getModal: function () {
        return $(this.modal);
    },

    onAddModel: function () {
        this.addModel();

        return false;
    },

    getAddManyConceptsButton: function () {
        return $('#add-model-concept-button');
    },

    getManyConceptsContainer: function () {
        return $('#activity-concept > tbody');
    },

    onAddManyConcepts: function () {
        $.post(this.add_many_concepts_url, {}, $.proxy(this.onAddManyConceptsSuccess, this));
    },

    onAddManyConceptsSuccess: function (data) {
        this.getManyConceptsContainer().append(data);
    },

    onManyConceptRowClick: function () {
        this.onAddConcept();
    },

    getActivityConceptContainer: function () {
        return $('#activity-concept');
    },

    onLoadModel: function () {
        this.showModal();

        var message_re = /message\/([0-9]+)/;
        if (this.mode && this.mode.match(message_re)) {
            var matches = this.mode.match(message_re);
            this.discussion_controller.setStartMessage(matches[1]);
            this.discussion_controller.activateTab();
        } else if (this.mode == 'model' || !this.mode) {
            this.model_form.activateTab();
        }

        if (this.model_form.getValue('blank_id') == '0')
            this.model_form.enableTypeSelect();
        else
            this.model_form.disableTypeSelect();
    },

    onLoadReport: function () {
        if (this.mode == 'report') {
            if (this.report_form.isEnabled())
                this.report_form.activateTab();
            else
                this.model_form.activateTab();
        }
    },

    onCloseModal: function () {
        this.discussion_controller.stopDiscussion();
    },

    onSelectRow: function (form, target) {
        this.mode = false;

        var $row = $(target).closest('.model-row');
        if ($row.data('blank'))
            this.onSelectBlank($row);

        if ($(target).closest('.concept-row').data('new-concept'))
            this.onAddConcept();
    },

    onAddConcept: function () {
        this.addConcept();
    },

    onSelectBlank: function ($row) {
        this.addModelFromBlank($row);
    },

    onSort: function (e) {
        location.href = this.sort_url + '?sort=' + $(e.target).closest('.has-sort').data('sort');
    }
}
