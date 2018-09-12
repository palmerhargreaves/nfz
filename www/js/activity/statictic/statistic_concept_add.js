/**
 * Created by kostet on 21.07.2015.
 */
StatisticConceptAdd = function(config) {
    // configurable {
    // }
    this.modal = '';

    this.accept_url = '';
    this.delete_url = '';
    this.activity_id = 0;
    this.show_dialog = '';
    this.dealer_id = 0;
    this.concept_id = 0;

    $.extend(this, config);
}

StatisticConceptAdd.prototype = {
    start: function() {
        this.initEvents();

        return this;
    },

    initEvents: function() {
        var self = this;

        /*this.getDealersSelect().change($.proxy(this.onSelectDealer, this));
         this.getTasksListBlock().on('click', 'a', $.proxy(this.onClickActionLink, this));*/
        this.getModal().on('click', '.action-activity-add-concept', $.proxy(this.onAddNewConcept, this));

        $(document).on('click', this.show_dialog, $.proxy(this.onBeginAddNewConcept, this));
        $(document).on('click', '.on-delete-dealer-concept-certificate', $.proxy(this.onDeleteDealerConcept, this));
    },

    onBeginAddNewConcept: function(e) {
        this.dealer_id = $(e.target).data('dealer-id');

        this.getModal().modal('show');
    },

    onAddNewConcept: function() {
        $.post(this.accept_url,
                {
                    concept: this.getConceptField().val(),
                    dealer_id : this.dealer_id,
                    activity_id : this.activity_id
                },
            $.proxy(this.onConceptAddSuccess, this));
    },

    onConceptAddSuccess: function(result) {
        $('#dealer-concept-cetrificate-' + this.dealer_id + ' > td').eq(2).empty().html(result);

        this.getModal().modal('hide');
    },

    onDeleteDealerConcept: function(e) {
        if(confirm('Удалить сертификат ?')) {
            this.concept_id = $(e.target).data('id');

            console.log(this.concept_id);
            $.post(this.delete_url,
                {
                    concept : $(e.target).data('id')
                },
                $.proxy(this.onDeleteDealerConceptSuccess, this));
        }
    },

    onDeleteDealerConceptSuccess: function() {
        $('.dealer-certificate-item-' + this.concept_id).remove();
    },

    getConceptField: function() {
        return $('#sbConcept', this.getModal());
    },

    getModal: function() {
        return $(this.modal);
    },
}