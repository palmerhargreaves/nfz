/**
 * Created by kostet on 22.09.2016.
 */
BindDealers = function(config) {
    this.on_delete_binded_dealer_url = '';
    this.on_add_binded_dealer_url = '';
    this.on_load_user_binded_data = '';
    this.on_reload_user_binded_dealers_row = '';

    this.modal = '';

    $.extend(this, config);

    this.user_id = 0;
    this.dealer_id = 0;
}

BindDealers.prototype = {
    start: function() {
        this.initEvents();
    },

    initEvents: function() {
        $(document).on('click', this.getUserBindDealers(), $.proxy(this.onLoadDealersList, this));

        $(document).on('click', this.getUnbindDealerBt(), $.proxy(this.onUnbindUserDealer, this));
        $(document).on('click', this.getBindUserDealerBt(), $.proxy(this.onBindUserDealer, this));
    },

    onBindUserDealer: function (e) {
        var dealer_id = this.getUserDealersListEl().val();

        e.preventDefault();

        this.dealer_id = dealer_id;
        if (confirm('Добавить привязку ?')) {
            $.post(this.on_add_binded_dealer_url, { dealer_id : dealer_id, user_id: this.user_id }, $.proxy(this.onBindUserDealerSuccess, this));
        }
    },

    onBindUserDealerSuccess: function(data) {
        this.getModalContentContainer().html(data);

        this.onReloadUserBindedDealersRow();
    },

    onUnbindUserDealer: function(e) {
        var $from = $(e.target);

        e.preventDefault();
        if (confirm('Удалить привязанного дилера ?')) {

            this.user_id = $from.data('user-id');
            this.dealer_id = $from.data('dealer-id');

            $.post(this.on_delete_binded_dealer_url, {
                user_id: $from.data('user-id'),
                dealer_id: $from.data('dealer-id')
            }, $.proxy(this.onUnbindUserDealerSuccess, this))
        }
    },

    onUnbindUserDealerSuccess: function(data) {
        this.getModalContentContainer().html(data);

        this.onReloadUserBindedDealersRow();
    },

    onLoadDealersList: function(e) {
        this.user_id = $(e.target).data('user-id');

        $.post(this.on_load_user_binded_data, { user_id : $(e.target).data('user-id')}, $.proxy(this.onLoadDataSuccess, this));
    },

    onLoadDataSuccess: function(data) {
        this.getModalContentContainer().html(data);
        this.getModal().modal('show');
    },

    getUserBindDealers: function() {
        return '.action-edit-user-binded-dealers';
    },

    getModal: function() {
        return $(this.modal);
    },

    getModalContentContainer: function() {
        return $('.modal-body', this.getModal());
    },

    getUnbindDealerBt: function() {
        return '.bt-unbind-dealer-from-user';
    },

    getBindUserDealerBt: function() {
        return '#bt-bind-selected-dealer-to-user';
    },

    getUserDealersListEl: function() {
        return $('#sb_user_dealer_to_bind', this.getModal());
    },

    onReloadUserBindedDealersRow: function() {
        $.post(this.on_reload_user_binded_dealers_row, { user_id : this.user_id }, $.proxy(this.onReloadUserBindDealersRowSuccess, this));
    },

    onReloadUserBindDealersRowSuccess: function(data) {
        $('.container-user-binded-dealers-' + this.user_id).html(data);
    }
}
