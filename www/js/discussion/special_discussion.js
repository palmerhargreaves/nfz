SpecialDiscussion = function (config) {
    this.bt_send_message = '';

    SpecialDiscussion.superclass.constructor.call(this, config);

    this.userId = 0;
    this.discussion_id = 0;
    this.message_id = 0;
    this.markAsRead = false;
    this.mark_as_read = 0;
    this.form = '';
}

utils.extend(SpecialDiscussion, Discussion, {
    initEvents: function () {
        //this.getSpecialDiscussionButton().click($.proxy(this.onShowSpecialDialogSuccess, this));
        $(document).on('click', '.special-discussion-button', $.proxy(this.onShowSpecialDialogSuccess, this));
        //this.getSpecialMessagesPanel().on('click', '.special-discussion-button-submit', $.proxy(this.onSubmitSpecialMessage, this));
        this.getSpecialMessagesPanel().on('click', '.special-discussion-button-submit-read', $.proxy(this.onSubmitSpecialMessageAsRead, this));
        this.getSpecialMessagesPanel().on('click', '.special-discussion-button-close', $.proxy(this.onCloseButton, this));

        $(document).on('click', this.bt_send_message, $.proxy(this.sendMessage, this));
    },

    sendMessage: function (event) {
        event.preventDefault();

        var $self = this;

        this.posting = true;
        this.saveFilesToSend();
        $.post($self.post_url, $.extend(this.getFilesParam(), {
                id: $self.discussion_id,
                message: $self.getSpecialMessageText().val(),
                userId: $self.user_id,
                markAsRead: $self.markAsRead,
            }))
            .success($.proxy(this.onPostSuccess, this))
            .error($.proxy(this.onPostError, this))
            .complete($.proxy(this.onPostComplete, this));
    },

    onResponse: function(result) {
        if (result.success) {
            this.getSpecialMessageText().val('');
            this.deleteSentFiles();

            this.onLoadMessagesList();
        }
    },

    deleteSentFiles: function() {
        $('.model-form-selected-files-to-upload').html('');

        if (this.discussion_file_uploader != null) {
            this.discussion_file_uploader.reset();
        }
    },

    getPostForm: function () {
        return $('form.post-special', this.getPanel());
    },

    setUserId: function (uId) {
        this.userId = uId;
    },

    setDiscussionId: function (dId) {
        this.discussion_id = dId;
    },

    setMarkAsRead: function (mark) {
        this.maskAsRead = mark;
    },

    onPostSuccess: function () {
        this.getSpecialMessageText().val('');
        this.deleteSentFiles();

        this.onLoadMessagesList();
    },

    onPostComplete: function () {
        this.posting = false;
    },

    showDialog: function ($el) {
        var dId = $el.data('discussion-id'),
            message_id = $el.data('message-id'),
            uId = $el.data('user-id');

        this.form = $el.closest('form');
        this.discussion_id = dId;
        this.message_id = message_id;
        this.user_id = uId;
        this.mark_as_read = $el.data('mark-as-read');

        //Устанавливаем номер дискусси для отправляемой формы
        var discussion_hidden_field = this.getSubmitForm().find('input[id="id"]');
        if (discussion_hidden_field.length > 0) {
            discussion_hidden_field.val(this.discussion_id);
        }

        this.getSpecialMessageText().removeClass('discussion-special-panel-error');

        this.getSpecialModal().krikmodal('show');
        this.onLoadMessagesList();
    },

    onLoadMessagesList: function () {
        $.post(this.form.data('url-list'),
            { id: this.discussion_id, message_id: this.message_id, mark_as_read: this.mark_as_read },
            $.proxy(this.onSuccessSpecialDiscussionLoad, this)
        );
    },

    onShowSpecialDialogSuccess: function (el) {
        this.showDialog($(el.target));
    },

    onSuccessSpecialDiscussionLoad: function (result) {
        this.getSpecialMessages().empty().html(result);
        this.makeScroller();

        this.moveMessageItemToReaded();
    },

    moveMessageItemToReaded: function() {
        if (this.message_id != 0 && this.mark_as_read) {
            $('tr[data-item-id=' + this.message_id + ']').remove();
        }
    },

    onSubmitSpecialMessage: function (el) {
        this.markAsRead = 0;
        this.onSubmitForm();
    },

    onSubmitSpecialMessageAsRead: function () {
        this.markAsRead = 1;

        this.onSubmitForm();
    },

    onCloseButton: function () {
        this.getSpecialModal().hide();
    },

    onSubmitForm: function () {

        var text = $.trim(this.getSpecialMessageText().val());
        if (text.length == 0) {
            this.getSpecialMessageText().addClass("discussion-special-panel-error");
            return;
        }

        this.sendMessage();

        /*this.getSpecialMessageText().val('');
         $.post(this.post_url,
         {
         id : this.discussion_id,
         userId : this.user_id,
         text : text,
         markAsRead : this.markAsRead
         },
         $.proxy(this.onSpecialMessageAddSuccess, this));              */
    },

    getSpecialMessages: function () {
        return $('.special-messages');
    },

    getSpecialMessagesPanel: function () {
        return $('.panel-special-message');
    },

    makeScroller: function () {
        this.getScroller().tinyscrollbar({size: 336, sizethumb: 41});
        this.getScroller().tinyscrollbar_update('bottom');
    },

    getSpecialModal: function () {
        return $("#special-modal");
    },

    getSpecialMessageText: function () {
        return $('textarea[name=message]', this.getPanel());
    },

    getScroller: function() {
        return $('.scroller', '#special-modal');
    },

    getSubmitForm: function() {
        return $('#discussion_model_comments');
    },

    getCommentPostBt: function() {
        return $('#bt-special-message');
    }
});
