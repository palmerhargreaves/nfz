Uploader = function (config) {
    // configurable {
    this.selector = null; // required selector upload block
    this.upload_url = null; // required url to upload files
    this.delete_url = null; // url to delete an uploaded file
    this.session_name = '';
    this.session_id = '';
    // }
    $.extend(this, config);

    this.uploader = null;
    this.files = {}
}

Uploader.prototype = {
    start: function () {
        //this.initUploader();

        return this;
    },

    initUploader: function () {
        var post_params = {}
        if (this.session_name)
            post_params[this.session_name] = this.session_id;

        this.uploader = new SWFUpload({
            upload_url: this.upload_url,
            flash_url: "/flash/swfupload.swf",
            post_params: post_params,
            file_size_limit: "100 MB",
            button_placeholder_id: this.getUploadButton().children().generateId().attr('id'),
            button_image_url: '/images/clip-icon3.png',
            button_width: 34,
            button_height: 34,
            button_disabled: false,
            file_post_name: 'file',
            file_upload_limit: 0,
//      file_queue_limit : 2,
            file_types: "*.jpg;*.jpeg;*.gif;*.png;*.pdf;*.ai;*.psd;*.cdr;*.zip;*.rar;*.doc;*.docx;*.xsl;*.xlsx;*.xls;*.ppt;*.pptx;*.swf;*.mp3;*.avi;*.mkv;*.mov;*.wma;*.mp4;*.flv;*.wmv",
            button_cursor: SWFUpload.CURSOR.HAND,
            button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
            file_dialog_complete_handler: $.proxy(this.onSelectedFiles, this),
            upload_progress_handler: $.proxy(this.onUploadProgress, this),
            upload_error_handler: $.proxy(this.onUploadError, this),
            upload_success_handler: $.proxy(this.onUploadSuccess, this),
            upload_complete_handler: $.proxy(this.onUploadComplete, this),
            file_queue_error_handler: $.proxy(this.onFileQueueError, this),
            file_queued_handler: $.proxy(this.onFileQueued, this)
        });
    },

    getSuccessIds: function () {
        var ids = [];
        $.each(this.files, function (id, file) {
            if (file.success)
                ids.push(file.temp_id);
        });

        return ids;
    },

    deleteFilesByTempId: function (ids, skip_server_delete) {
        for (var i = 0; i < ids.length; i++) {
            var temp_id = ids[i];
            var _this = this;
            $.each(this.files, function (id, file) {
                if (file.temp_id == temp_id) {
                    _this._deleteFile(id, skip_server_delete);
                    return false;
                }
            });
        }
    },

    cancelUpload: function (id) {
        this.uploader.cancelUpload(id);
        this._deleteFile(id, false);
    },

    _deleteFile: function (id, skip_server_delete) {
        this.files[id]._delete(skip_server_delete);
        delete this.files[id];
    },

    getUploadButton: function () {
        return $('.message-upload-button', this.getEl());
    },

    getFilesBlock: function () {
        return $('.files', this.getEl());
    },

    getEl: function () {
        return $(this.selector);
    },

    onSelectedFiles: function (num_files_selected, num_files_queued) {
        if (num_files_queued > 0)
            this.uploader.startUpload();
    },

    onUploadProgress: function (file, bytes_loaded) {
        this.files[file.id].onProgress(bytes_loaded);
    },

    onUploadError: function (file, error_code, message) {
//    console.error('upload error');
//    console.error(file);
//    console.error(message + " (" + error_code + ")");
        this.files[file.id].onUploadError();
    },

    onUploadSuccess: function (file, server_data) {
        var file = this.files[file.id];
        var result = server_data ? server_data.split(',') : '';
        if (result[0] == 'success')
            file.onUploadSuccess(result[1], result[2]);
        else
            file.onUploadError();
    },

    onUploadComplete: function (file) {
        if (this.uploader.getStats().files_queued > 0) {
            this.uploader.startUpload();
        }
    },

    onFileQueueError: function (file, error_code, message) {
        if (this.files[file.id])
            return;

        switch (error_code) {
            case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
                this.files[file.id] = new UploadFile(this, this.getFilesBlock(), file, 'слишком маленький');
                break;
            case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
                this.files[file.id] = new UploadFile(this, this.getFilesBlock(), file, 'слишком большой');
                break;
            case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
                this.files[file.id] = new UploadFile(this, this.getFilesBlock(), file, 'неверный тип');
                break;
        }
    },

    onFileQueued: function (file) {
        this.files[file.id] = new UploadFile(this, this.getFilesBlock(), file);
    }
}


/**
 * Upload file
 */
UploadFile = function (uploader, $parent, file, with_error) {
    this.file = file;
    this.uploader = uploader;
    this.selector = null;
    this.uploaded = false;
    this.temp_id = 0;
    this.success = false;

    this._create($parent);

    if (with_error) {
        this.uploaded = true;
        this.getSizeEl().html(with_error);
    }
}

UploadFile.prototype = {
    _create: function ($parent) {
        var markup = '<div class="message-upload-item"><div class="name"><span class="file-name">' + this.file.name + '</span> (<span class="file-size">0 / ' + this.getSmartSize(this.file.size, true) + '</span>)</div><div class="delete"></div></div>';
        var $el = $(markup).appendTo($parent);
        $('.delete', $el).click($.proxy(this.onCancel, this));

        this.selector = $el.getIdSelector();
    },

    _delete: function (skip_server_delete) {
        this.getEl().remove();

        if (!skip_server_delete && this.temp_id && this.uploader.delete_url) {
            $.post(this.uploader.delete_url, {id: this.temp_id});
        }
    },

    getSmartSize: function (size, with_units) {
        var units = ['Б', 'КБ', 'МБ', 'ГБ'];

        for (var i = 0; i < units.length; i++) {
            if (size < 1024)
                return Math.round(size) + (with_units ? ' ' + units[i] : '');

            size /= 1024;
        }

        return Math.round(size * 1024) + (with_units ? ' ' + units[units.length - 1] : '');
    },

    getNameEl: function () {
        return $('.file-name', this.getEl());
    },

    getSizeEl: function () {
        return $('.file-size', this.getEl());
    },

    getEl: function () {
        return $(this.selector);
    },

    onCancel: function () {
        if (this.uploaded)
            this.uploader.cancelUpload(this.file.id);
        else
            this._delete();
    },

    onUploadSuccess: function (temp_id, file_name) {
        this.temp_id = temp_id;
        this.uploaded = true;
        this.success = true;
        this.getNameEl().html(file_name);
        this.getSizeEl().html(this.getSmartSize(this.file.size, true));
    },

    onUploadError: function () {
        this.uploaded = true;
        this.success = false;
        this.getSizeEl().html('ошибка');
    },

    onProgress: function (loaded) {
        this.getSizeEl().html(this.getSmartSize(loaded, true) + ' / ' + this.getSmartSize(this.file.size, true));
    }
}