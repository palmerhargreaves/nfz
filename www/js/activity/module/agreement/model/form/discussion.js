AgreementModelDiscussionController = function (config) {
    // configurable {
    this.tabs_selector = '';
    this.tab_selector = '';
    this.models_list = ''; // required selector of models list table
    this.panel_selector = '#discussion-pane';
    this.state_url = '';
    this.new_messages_url = '';
    this.post_url = '';
    this.previous_url = '';
    this.search_url = '';
    this.online_check_url = '';
    this.session_name = '';
    this.session_id = '';
    this.delete_file_url = '';

    this.load_chat_last_messages_and_files = '';
    this.scroller = '';
    this.scroller_discussion = '';
    this.discussion_file_uploader = null;
    this.model_row = '';

    // }
    $.extend(this, config);

    this.model_id = 0;
    this.discussion_id = 0;
    this.discussion = null;
    this.start_message = false;
}

AgreementModelDiscussionController.prototype = {
    start: function () {
        this.initEvents();

        return this;
    },

    stopDiscussion: function () {
        if (this.discussion)
            this.discussion.stopDiscussion();
    },

    initEvents: function () {
        this.getTab().on('activated', $.proxy(this.onActivateTab, this));
        $('.model-row', this.getModelsList()).click($.proxy(this.onClickModel, this));
    },

    disable: function () {
        this.getTab().addClass('disabled');
        this.hideNewMessageIndicator();
    },

    enable: function () {
        this.getTab().removeClass('disabled');
    },

    setDiscussion: function (discussion_id, unread) {
        this.discussion_id = discussion_id;
        this.enable();
        this.showNewMessageIndicatorIf(unread);
    },

    setModelId: function (model_id) {
        this.model_id = model_id;
    },

    setStartMessage: function (start_message) {
        this.start_message = start_message;
    },

    showNewMessageIndicatorIf: function (count) {
        if (count > 0)
            this.showNewMessageIndicator(count);
        else
            this.hideNewMessageIndicator();
    },

    showNewMessageIndicator: function (count) {
        this.getNewMessageIndicator().show().html(count + "");
    },

    hideNewMessageIndicator: function () {
        this.getNewMessageIndicator().hide();
    },

    getDiscussion: function () {
        if (!this.discussion) {
            var $upload_panel = $('.message-upload', this.getPanel());

            this.discussion = new Discussion({
                panel: this.getPanel().getIdSelector(),
                state_url: this.state_url,
                new_messages_url: this.new_messages_url,
                post_url: this.post_url,
                previous_url: this.previous_url,
                search_url: this.search_url,
                online_check_url: this.online_check_url,
                scroller_uploaded_files: this.scroller,
                discussion_file_uploader: this.discussion_file_uploader
                /*uploader: $upload_panel.length
                    ? new Uploader({
                    selector: $upload_panel.getIdSelector(),
                    session_name: this.session_name,
                    session_id: this.session_id,
                    upload_url: '/upload.php',
                    delete_url: this.delete_file_url
                }).start()
                    : null*/
            }).start();
        }

        return this.discussion;
    },

    getPanel: function () {
        return $(this.panel_selector);
    },

    getNewMessageIndicator: function () {
        return $('.message', this.getTab());
    },

    activateTab: function () {
        this.getTabs().kriktab('activate', this.getTab());
    },

    getTab: function () {
        return $(this.tab_selector);
    },

    getTabs: function () {
        return $(this.tabs_selector);
    },

    getModelsList: function () {
        return $(this.models_list);
    },

    onClickModel: function (e) {
        var $model_row = $(e.target).closest('.model-row');

        this.isBlocked = $model_row.data('is-blocked') == 1 ? true : false;
        this.discussion_id = $model_row.data('discussion');
        this.model_id = $model_row.data('model');

        if (this.discussion_id) {
            this.enable();
            this.showNewMessageIndicatorIf($model_row.data('new-messages'));
        } else {
            this.disable();
            this.hideNewMessageIndicator();
        }
    },

    onActivateTab: function () {
        this.getDiscussion().startDiscussion(this.discussion_id, this.start_message, this.model_id);
        this.start_message = false;
        this.hideNewMessageIndicator();

        $('.message-send-wrapper').show();
        if (this.isBlocked)
            $('.message-send-wrapper').hide();

    }
}