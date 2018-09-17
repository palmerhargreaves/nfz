Discussion = function (config) {
    // configurable {
    this.panel = null; // required selector of a discussion panel
    this.state_url = null; // required url to request discussion state
    this.new_messages_url = null; // require url to request new messages
    this.post_url = null; // required url to post messages
    this.previous_url = null; // required url to request previous messages
    this.search_url = null;
    this.online_check_url = null;
    this.fast_request_delay = 2000;
    this.calm_request_delay = 30000;
    this.fast_requests_amount = 50;
    this.scroller_handle_delay = 250;
    this.offset_to_load_previous = 100;
    this.online_check_delay = 60000;
    this.uploader = null;
    this.response_form = '';
    this.scroller_uploaded_files = null;
    this.discussion_file_uploader = null;
    // }
    $.extend(this, config);

    this.discussion_id = 0;
    this.model_id = 0;
    this.started = false;
    this.request_timer = null;
    this.current_request_delay = 0;
    this.requests_counter = 0;
    this.requesting = false;
    this.posting = false;
    this.scroller_handle_timer = null;
    this.last_scroll_position = 0;
    this.loading_previous = false;
    this.has_previous = true;
    this.sending_files = [];
    this.search_mode = false;
    this.online = {}
    this.online_check_timer = null;
    this.start_message = false;
    this.comments_form = null;
}

Discussion.prototype = {
    start: function () {
        this.initEvents();

        return this;
    },

    initEvents: function () {
        //this.getPostForm().submit($.proxy(this.onPost, this));
        this.getCommentPostBt().click($.proxy(this.onPost, this));
        $(document).on('change', '.message-upload-wrapper input', $.proxy(this.onSelectFiles, this));

        this.getMessageField().keydown($.proxy(this.onKeyDownMessageField, this));
        this.getSearchForm().submit($.proxy(this.onSearch, this));
    },

    onResponse: function() {
        this.posting = false;

        this.getCommentFormEl().find('.mod-popup-buttons').removeClass('loader-bg');
        this.onPostSuccess();
    },

    startDiscussion: function (id, start_message, model_id) {
        this.discussion_id = id;
        this.model_id = model_id;

        this.started = true;
        this.start_message = start_message;
        this.requestState(start_message);
        this.startCheckOnlineTimer();

        $('.mod-popup-comment #model_id').val(id);

        if (this.getScroller().length > 0)
            this.startScrollerHandlerTimer();
    },

    onSelectFiles: function (e) {
        //$(this).parents('.file').find('.file-name').html(file_title);
        var $parent_input = $("input[data-name=" + $(e.target).data('name') + "]"),
            max_upload_file_size = $('body').data('max-upload-file-size'),
            max_uploaded_files_count = $('body').data('max-files-upload-count') - 1,
            err_cls = '', err_count_cls = '', files = e.target.files,
            parentCls = $parent_input.data('container-cls'),
            container = $('.' + parentCls, this.getPanel()), self = this;

        container.empty();
        $.each(files, function (i, file) {
            file_title = self.getUploadedFileTitle(file.name);

            err_cls = '';
            err_count_cls = '';

            if (file.size > max_upload_file_size) {
                err_cls = 'error-max-file-size';
            }

            if (i > max_uploaded_files_count) {
                err_count_cls = 'error-max-uploaded-files-count';
            }

            var append_text = '<div class="' + err_cls + ' ' + $parent_input.data('name') + '_cls_' + (i + 1) + ' "><span>' + (i + 1) + '.</span><span class="' + err_count_cls + '">' + file_title
                + '</span><span>' + humanFileSize(file.size) + '</span>';

            container.append(append_text);
            i++;
        });
    },

    getUploadedFileTitle: function (file) {
        var name = file;
        var win_pattern = /.*\\(.*)/;
        var file_title = name.replace(win_pattern, "$1");
        var unix_pattern = /.*\/(.*)/;

        return file_title.replace(unix_pattern, "$1");
    },

    stopDiscussion: function () {
        this.started = false;

        this.stopCheckOnlineTimer();
        this.stopRequestTimer();
        if (this.getScroller().length > 0)
            this.stopScrollerHandlerTimer();
    },

    search: function () {
        if (this.getSearchField().val()) {
            this.stopRequestTimer();
            this.has_previous = false;
            this.search_mode = true;
            $.post(this.search_url, {
                id: this.discussion_id,
                text: this.getSearchField().val(),
                model_id: this.model_id
            }).success($.proxy(this.onReceiveSearchResult, this))
                .error($.proxy(this.onSearchError, this));
        } else {
            this.requestState();
        }
    },

    requestState: function (start_message) {
        this.stopRequestTimer();
        this.search_mode = false;
        this.has_previous = true;
        this.getSearchForm().get(0).reset();
        this.current_request_delay = this.calm_request_delay;
        this.requesting = true;

        var params = {
            id: this.discussion_id,
            model_id: this.model_id
        }
        if (start_message)
            params.start = start_message

        $.post(this.state_url, params)
            .success($.proxy(this.onReceiveState, this))
            .error($.proxy(this.onErrorReceiveState, this))
            .complete($.proxy(this.onCompleteRequest, this));
    },

    checkOnline: function () {
        $.post(this.online_check_url, {id: this.discussion_id, model_id: this.model_id}, $.proxy(function (data) {
            this.online = data || {};
            this._applyOnline(this.getMessagesContainer());
        }, this));
    },

    _applyOnline: function (content) {
        var $content = $(content);
        var online = this.online;
        $('.name', $content).each(function () {
            var $el = $(this);
            var user_id = $el.data('user');
            if (user_id && online[user_id])
                $el.addClass('online');
            else
                $el.removeClass('online');
        });

        return $content;
    },

    _doNextRequest: function (new_delay, no_wait) {
        if (!this.started || this.search_mode)
            return;

        if (new_delay) {
            this.current_request_delay = new_delay;
            this.requests_counter = this.fast_requests_amount;
        } else {
            this.requests_counter--;
            if (this.requests_counter <= 0)
                this.current_request_delay = this.calm_request_delay;
        }

        if (!this.requesting) {
            if (no_wait)
                this._requestNewMessages();
            else
                this.startRequestTimer();
        }
    },

    _requestNewMessages: function () {
        this.requesting = true;

        $.post(this.new_messages_url, {id: this.discussion_id, model_id: this.model_id})
            .success($.proxy(this.onReceiveNewMessages, this))
            .complete($.proxy(this.onCompleteRequest, this));
    },

    requestPrevious: function () {
        if (this.loading_previous)
            return;

        var before = this.getFirstMessageId();
        if (!before)
            return;

        this.loading_previous = true;
        $.post(this.previous_url, {id: this.discussion_id, before: before, model_id: this.model_id})
            .success($.proxy(this.onReceivePrevious, this))
            .complete($.proxy(this.onCompleteReceivePrevious, this));
    },

    startRequestTimer: function () {
        this.stopRequestTimer();

        this.request_timer = setTimeout($.proxy(this.onNextRequest, this), this.current_request_delay);
    },

    stopRequestTimer: function () {
        if (this.request_timer) {
            clearTimeout(this.request_timer);
            this.request_timer = null;
        }
    },

    startScrollerHandlerTimer: function () {
        this.stopScrollerHandlerTimer();

        this.scroller_handle_timer = setInterval($.proxy(this.onHandleScroller, this), this.scroller_handle_delay);
    },

    stopScrollerHandlerTimer: function () {
        if (this.scroller_handle_timer) {
            clearInterval(this.scroller_handle_timer);
            this.scroller_handle_timer = null;
        }
    },

    startCheckOnlineTimer: function () {
        this.stopCheckOnlineTimer();

        this.online_check_timer = setInterval($.proxy(this.onCheckOnline, this), this.online_check_delay);
    },

    stopCheckOnlineTimer: function () {
        if (this.online_check_timer) {
            clearInterval(this.online_check_timer);
            this.online_check_timer = null;
        }
    },

    sendMessage: function () {
        var total_uploaded_files = 0;
        for (var i in this.getFilesParam()) {
            if (this.getFilesParam().hasOwnProperty(i)) {
                total_uploaded_files++;
            }
        }

        if (this.posting || ($.trim(this.getMessageField().val()) == '' && total_uploaded_files == 0)) {
            showAlertPopup('Ошибка.', 'Введите сообщение или загрузите файл.');
            return;
        }

        this.posting = true;
        this.saveFilesToSend();
        $.post(this.post_url, $.extend(this.getFilesParam(), {
            id: this.discussion_id,
            message: this.getMessageField().val()
        }))
            .success($.proxy(this.onPostSuccess, this))
            .error($.proxy(this.onPostError, this))
            .complete($.proxy(this.onPostComplete, this));
    },

    scrollBottom: function () {
        this.getScroller().tinyscrollbar_update('bottom');
    },

    scrollTop: function () {
        this.getScroller().tinyscrollbar_update(0);
    },

    insertError: function (msg) {
        this.getMessagesContainer().append('<div class="error">' + msg + '</div>');
        this.scrollBottom();
    },

    saveFilesToSend: function () {
        this.sending_files = this.uploader ? this.uploader.getSuccessIds() : [];
    },

    deleteSentFiles: function () {
        if (this.uploader || this.discussion_file_uploader) {
            if (this.uploader) {
                this.uploader.deleteFilesByTempId(this.sending_files, true);
            }

            if (this.discussion_file_uploader != null) {
                this.discussion_file_uploader.reset();
                this.discussion_file_uploader.drawFiles();
            }

            this.sending_files = [];
        }
    },

    getFilesParam: function () {
        var params = {}

        if (this.sending_files.length == 0 && this.discussion_file_uploader) {
            this.sending_files = this.discussion_file_uploader.getFilesUploadList();
        }

        for (var i = 0; i < this.sending_files.length; i++) {
            params['files[' + i + ']'] = this.sending_files[i];
        }

        return params;
    },

    getMessageField: function () {
        return $(':input[name=message]', this.getPostForm());
    },

    getMessageTextAreaField: function() {
        return $('textarea[name=message]', this.getPostForm());
    },

    getPostForm: function () {
        return $('form.post', this.getPanel());
    },

    getFirstMessageId: function () {
        return this.getFirstMessageEl().data('message');
    },

    getFirstMessageEl: function () {
        return $('.message:first', this.getMessagesContainer());
    },

    getMessagesContainer: function () {
        return $('.messages', this.getPanel());
    },

    getScrollerOverview: function () {
        return $('.overview', this.getScroller());
    },

    getScroller: function () {
        return $('.scroller', this.getPanel());
    },

    getSearchField: function () {
        return $('input[name=search]', this.getSearchForm());
    },

    getSearchForm: function () {
        return $('form.search', this.getPanel());
    },

    getPanel: function () {
        return $(this.panel);
    },

    onReceiveState: function (data) {
        this.getMessagesContainer().html(this._applyOnline(data));

        if (this.start_message) {
            this.scrollTop();
            this.requestPrevious();
            this.start_message = false;
        } else {
            this.scrollBottom();
        }

        this.checkOnline();
    },

    onErrorReceiveState: function () {
        this.insertError('Ошибка получения сообщений');
    },

    onCompleteRequest: function () {
        this.requesting = false;
        this._doNextRequest();

        this.getCommentPostBt().fadeIn();
    },

    onPost: function (event) {
        event.stopPropagation();

        if (this.started)
            this.sendMessage(event);

        return false;
    },

    onPostSuccess: function () {
        this.getMessageField().val('');
        this.getMessageTextAreaField().val('');

        this.deleteSentFiles();
        if (this.search_mode) {
            this.requestState();
        }
        else {
            this._doNextRequest(this.fast_request_delay, true);
        }
    },

    onPostError: function () {
        this.insertError('Ошибка отправки сообщения');
    },

    onPostComplete: function () {
        this.posting = false;
    },

    onNextRequest: function () {
        this._requestNewMessages();
    },

    onReceiveNewMessages: function (data) {
        if ($.trim(data) != '') {
            this.getMessagesContainer().append(this._applyOnline(data));
            this.scrollBottom();
            this._doNextRequest(this.fast_request_delay);
        }
    },

    onKeyDownMessageField: function (e) {
        if (e.ctrlKey && e.keyCode == 13)
            this.sendMessage();
    },

    onHandleScroller: function () {
        if (!this.has_previous || this.requesting)
            return;

        var pos = this.getScrollerOverview().position().top;
        if (this.last_scroll_position == pos)
            return;

        this.last_scroll_position = pos;
        if (Math.abs(pos) <= this.offset_to_load_previous)
            this.requestPrevious();
    },

    onReceivePrevious: function (data) {
        if ($.trim(data) == '') {
            this.has_previous = false;
            return;
        }

        var $first_message = this.getFirstMessageEl();
        var prev_top = $first_message.position().top;
        this.getFirstMessageEl().before(this._applyOnline(data));
        var new_top = $first_message.position().top;
        this.getScroller().tinyscrollbar_update('relative', new_top - prev_top);
    },

    onCompleteReceivePrevious: function () {
        this.loading_previous = false;
    },

    onSearch: function () {
        this.search();

        return false;
    },

    onReceiveSearchResult: function (data) {
        this.getMessagesContainer().html(this._applyOnline(data));
        this.scrollBottom();
    },

    onSearchError: function () {
        this.insertError('Ошибка поиска');
    },

    onCheckOnline: function () {
        this.checkOnline();
    },

    getCommentPostBt: function () {
        return $('.message-button', this.getPanel());
    },

    getFileListContainer: function () {
        return $('.model-form-selected-files-to-upload', this.getPanel());
    },

    getCommentsForm: function() {
        if(!this.comments_form) {
            this.getCommentFormEl().attr('action', this.post_url);

            this.comments_form = new Form({
                form: this.getCommentFormEl().getIdSelector(),
                onSuccess: $.proxy(this.onSuccessComment, this)
            });
        }

        return this.comments_form;
    },

    getCommentFormEl: function() {
        return $('form#discussion_model_comments', this.getPanel());
    },

    onSuccessComment: function() {

    }
}
