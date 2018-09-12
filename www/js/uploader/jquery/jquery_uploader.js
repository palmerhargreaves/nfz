/**
 * Created by kostet on 24.09.2016.
 */

JQueryUploader = function (config) {
    this.file_uploader_el = '';
    this.max_file_size = 0;
    this.upload_max_files_count = -1;
    this.uploader_url = '';
    this.delete_temp_file_url = '';
    this.uploaded_files_container = '';
    this.uploaded_files_caption = '';
    this.delete_uploaded_file_url = '';
    this.delete_uploaded_files_list_url = '';
    this.el_attach_files_click_bt = '';
    this.el_attach_files_model_field = '';
    this.add_caption_br_tag = false;
    this.session_name = '';
    this.session_id = '';
    this.progress_bar = '';
    this.upload_file_object_type = '';
    this.upload_file_type = '';
    this.upload_field = '';
    this.upload_files_ids_el = 'upload_files_ids';
    this.files_change_event = '';
    this.scroller = '';
    this.scroller_height = 336;
    this.draw_only_labels = false;
    this.disabled_files_extensions = [];
    this.model_form = '';

    JQueryUploader.superclass.constructor.call(this, config);

    this.MAX_UPLOADED_FILES_COUNT = 10;

    this.uploaded_files = [];
    this.uploaded_img_files = [];
    this.uploaded_files_default_list = [];

    this.caption_text = '';
    this.total_files = 0;
    this.total_files_size = 0;

    this.temp_uploaded_file_id = 0;
    this.is_dirty = false;

    this.uploaded_files_in_model = [];
    this.total_already_uploaded_files = 0;
    this.forbidden_files_extension_list = [];
}

utils.extend(JQueryUploader, utils.Observable, {
    start: function () {
        this.initEvents();

        return this;
    },

    initEvents: function () {
        this.reset();

        if (this.el_attach_files_click_bt != undefined && $(this.el_attach_files_click_bt).length > 0) {
            this.getForm().on('click', this.el_attach_files_click_bt, $.proxy(this.onAttachFilesClickBt, this));
        }

        $(this.uploaded_files_container, this.getForm()).on('click', '.remove-temp-uploaded-file', $.proxy(this.onDeleteTempUploadedFile, this));
        this.initFileUploader();
    },

    reset: function () {
        //$(document).undelegate(this.el_attach_files_click_bt, 'click');

        this.uploaded_files = [];
        this.uploaded_img_files = [];
        this.uploaded_files_default_list = [];
        this.uploaded_files_in_model = [];
        this.temp_uploaded_file_id = 0;

        this.total_files = 0;
        this.total_files_size = 0;
        this.total_already_uploaded_files = 0;

        this.removeFilesBlocks(true);

        if (this.getCaption().length > 0) {
            this.getCaption().html('Прикреплено: 0' + (this.add_caption_br_tag ? '<br/>' : '' ) + ' Общий размер: 0');
        }
        this.is_dirty = false;

        this.getUploadFilesIds().val('');
    },

    onDeleteTempUploadedFile: function (e) {
        var $el = $(e.target);

        if (confirm('Удалить файл ?')) {
            this.temp_uploaded_file_id = $el.data('file-id');

            $.post(this.delete_temp_file_url, {id: $el.data('file-id')}, $.proxy(this.onDeleteUploadedTempFile, this));
        }
    },

    onDeleteUploadedTempFile: function () {
        $('.uploaded-file-id-' + this.temp_uploaded_file_id).remove();

        this.removeUploadedFile(this.temp_uploaded_file_id);
        this.temp_uploaded_file_id = 0;

        this.total_already_uploaded_files--;
    },

    onAttachFilesClickBt: function () {
        $(this.el_attach_files_model_field, this.getForm()).trigger('click');
    },

    removeUploadedFile: function (file_id) {
        this.uploaded_files_default_list = this.uploaded_files_default_list.filter(function (item) {
            return item.id != file_id;
        });

        this.drawFiles();
    },

    initScrollBar: function () {
        var self = this;

        if (this.getScrollBar().length > 0) {
            this.getScrollBar().tinyscrollbar({size: this.scroller_height, sizethumb: 41});
            setTimeout(function () {
                self.updateScrollBar();
            }, 500);
        }
    },

    updateScrollBar: function () {
        if (this.getScrollBar().length > 0) {
            this.getScrollBar().tinyscrollbar({size: this.scroller_height, sizethumb: 41});
            this.getScrollBar().tinyscrollbar_update(0);
        }
    },

    /**
     *
     */
    initFileUploader: function () {
        var self = this;

        $res = this.getFileUploaderHandler().fileupload({
                url: self.uploader_url,
                replaceFileInput: false,
                dataType: 'json',
                dropZone: self.file_uploader_el,
                autoUpload: true,
                //acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
                maxFileSize: this.max_file_size,
                // Enable image resizing, except for Android and Opera,
                // which actually support image resizing, but fail to
                // send Blob objects via XHR requests:
                disableImageResize: true,///Android(?!.*Chrome)|Opera/.test(window.navigator.userAgent),
                previewMaxWidth: 80,
                previewMaxHeight: 0,
                previewCrop: false,
                singleFileUploads: true,
            })
            .bind('fileuploadprocessfail', function (e, data) {
                data.files.forEach(function (e) {
                    if (e.size > parseInt(data.maxFileSize)) {
                        showAlertPopup("Ошибка", "Размер файла превышает разрешенный (5 МБ).");
                    }
                });
            })
            .on('fileuploadadd', function (e, data) {
                if (self.upload_max_files_count != -1 && self.total_already_uploaded_files >= self.upload_max_files_count) {
                    showAlertPopup("Ошибка", 'Загружено максимальное количество файлов.');
                    return false;
                }
                self.total_already_uploaded_files++;

                if (!self.checkFilesExtension(data.files)) {
                    showAlertPopup("Ошибка", "Запрещенный формат файла.");
                    return false;
                }

                self.getUploadFileType().val(self.upload_file_type);
                self.getUploadFileObjectType().val(self.upload_file_object_type);
                self.getUploadField().val(self.upload_field);
            }).on('fileuploaddrop', function (e, data) {
            }).on('dragover', function (e, data) {
                var dropZone = $(self.uploaded_files_container),
                    foundDropzone,
                    timeout = window.dropZoneTimeout;

                if (!timeout) {
                    dropZone.addClass('in');
                } else {
                    clearTimeout(timeout);
                }

                var found = false, node = e.target;
                do {
                    if ($(node).hasClass('dropzone')) {
                        found = true;
                        foundDropzone = $(node);
                        break;
                    }
                    node = node.parentNode;
                } while (node != null);

                dropZone.removeClass('in hover');
                if (found) {
                    foundDropzone.addClass('hover');
                }

                window.dropZoneTimeout = setTimeout(function () {
                    window.dropZoneTimeout = null;
                    dropZone.removeClass('in hover');
                    if (found) {
                        foundDropzone.removeClass('hover');
                    }
                }, 100);
            }).on('fileuploadprocessalways', function (e, data) {

                var index = data.index,
                    file = data.files[index];

                if (file.preview) {
                    $('[data-file-name="' + file.name + '"]').append(file.preview);
                }
            }).on('fileuploadprogressall', function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 0);

                self.getProgressBar().show().css({width: progress + '%'});
            }).on('fileuploaddone', function (e, data) {
                self.getProgressBar().hide().css({width: 0 + '%'});

                if (data.result.success && self.total_files < self.MAX_UPLOADED_FILES_COUNT) {
                    self.is_dirty = true;
                    self.uploaded_files_default_list.push(data.result.file);

                    self.drawFiles();
                } else {
                    var msg = [];

                    if (self.total_files >= self.MAX_UPLOADED_FILES_COUNT) {
                        msg.push('Максимальное количество загружаемых файлов: ' + self.MAX_UPLOADED_FILES_COUNT);
                    } else {
                        $.each(data.result.errors, function (ind, error) {
                            msg.push(error.message);
                        });
                    }

                    showAlertPopup("Ошибка", msg.join('<br/>'));
                }
            }).on('fileuploadstart', function (e, data) {
            });
    },

    checkFilesExtension: function (files) {
        var allow_to_upload = true, self = this;

        files.forEach(function (file) {
            var file_extension = file.name.split('.').pop();

            if (($.inArray(file_extension, self.disabled_files_extensions) != -1) || ($.inArray('.' + file_extension, self.disabled_files_extensions) != -1)) {
                allow_to_upload = false;
            }

            if (($.inArray('.' + file_extension, self.forbidden_files_extension_list) != -1)) {
                allow_to_upload = false;
            }
        });

        return allow_to_upload;
    },

    makeCaption: function () {
        this.getCaption().html('Прикреплено: ' + this.total_files + '.' + (this.add_caption_br_tag ? '<br/>' : '' ) + ' Общий размер: ' + this.humanFileSize(this.total_files_size));

        //this.fireEvent(this.files_change_event, [this]);
        this.updateScrollBar();
    },

    drawFiles: function () {
        var self = this, files_odd_ind = 0;

        self.uploaded_img_files = [];
        self.uploaded_files = [];

        self.total_files = 0;
        self.total_files_size = 0;

        self.removeFilesBlocks();
        $.each(this.uploaded_files_default_list, function (index, file_item) {
            if (file_item.path.length > 0) {
                self.uploaded_img_files.push(file_item);
            } else {
                self.uploaded_files.push(file_item);
            }

            self.total_files++;
            self.total_files_size += file_item.size;
        });

        self.uploaded_files_default_list = [];
        if (self.uploaded_img_files.length > 0) {
            self.uploaded_img_files.forEach(function (file) {
                self.uploaded_files_default_list.push(file);
            });
        }

        if (self.uploaded_files.length > 0) {
            self.uploaded_files.forEach(function (file) {
                self.uploaded_files_default_list.push(file);
            });
        }

        var files_ids = [];
        self.uploaded_files_default_list.forEach(function (file_item) {
            self.addFileItem(file_item, self.getUploadedContainer(), files_odd_ind++);
            files_ids.push(file_item.id);
        });

        if (this.is_dirty) {
            this.getUploadFilesIds().val(files_ids.join(':'));
            this.makeCaption();
        }
    },

    /**
     *
     * @param file
     * @param cls
     * @param container
     * @param odd
     */
    addFileItem: function (file, container, odd) {
        var item = $("<span />"), ext = '';

        if (!this.draw_only_labels) {
            if (file.path.length == 0) {
                item.append('<i />');
                ext = file.name.split('.').pop();
            } else {
                item.append("<i><b data-file-name='" + file.name + "'><img src='" + file.path + "' style='width: 100px; max-width: 100px; max-height: 100px;' /></b></i>");
            }
        }

        item.addClass('d-popup-uploaded-file ' + (file.path.length == 0 ? 'odd ' + ext : ''));
        item.addClass('uploaded-file-id-' + file.id);

        item.append('<strong>' + file.name + '</strong>');
        item.append('<em>' + this.humanFileSize(file.size) + '</em>');

        item.append("<span class='remove remove-temp-uploaded-file' data-file-id='" + file.id + "'></span>");

        item.appendTo(container);
    },

    getFileUploaderHandler: function () {
        //console.log($(this.file_uploader_el, this.getForm()));
        //console.log(this.getForm());

        return $(this.file_uploader_el, this.getForm());
    },

    getUploadedContainer: function () {
        return $(this.uploaded_files_container, this.getForm());
    },

    getCaption: function () {
        return $(this.uploaded_files_caption, this.getForm());
    },

    removeFilesBlocks: function (remove_blocks) {
        this.removeBlocks(remove_blocks != undefined ? remove_blocks : false);

        if (this.getCaption().length > 0) {
            this.caption_text = this.getCaption().text();
            this.getCaption().html(this.caption_text);
        }
    },

    /**
     * Remove file item block
     * @param remove_block | if set true anyway we remove this item, else remove if we can
     */
    removeBlocks: function (remove_block) {
        $('.d-popup-uploaded-file', this.getUploadedContainer()).each(function (i, el) {
            var $el = $(el);

            if (remove_block != undefined && remove_block == true) {
                $el.remove();
            } else if ($el.attr('data-delete') == undefined) {
                $el.remove();
            }
        });
    },

    humanFileSize: function (bytes, si) {
        var thresh = si ? 1000 : 1024;

        if (Math.abs(bytes) < thresh) {
            return bytes + ' B';
        }
        var units = si
            ? ['kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB']
            : ['KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'];
        var u = -1;
        do {
            bytes /= thresh;
            ++u;
        } while (Math.abs(bytes) >= thresh && u < units.length - 1);

        return bytes.toFixed(1) + ' ' + units[u];
    },

    getProgressBar: function () {
        return $(this.progress_bar, this.getForm());
    },

    getUploadFileType: function () {
        return $('input[name=upload_file_type]', this.getForm());
    },

    getUploadFileObjectType: function () {
        return $('input[name=upload_file_object_type]', this.getForm());
    },

    getUploadField: function () {
        return $('input[name=upload_field]', this.getForm());
    },

    getUploadFilesIds: function () {
        return $('input[name=' + this.upload_files_ids_el + ']', this.getForm());
    },

    getScrollBar: function () {
        return $(this.scroller, this.getForm());
    },

    getFilesUploadList: function () {
        return this.uploaded_files_default_list.map(function (el) {
            return el.id
        });
    },

    getUploadedFilesCount: function () {
        return this.total_files;
    },

    setUploadedFiles: function (files) {
        this.uploaded_files_in_model = files;
        this.total_already_uploaded_files = files.length;
    },

    setUploadedFilesCount: function (count) {
        this.total_already_uploaded_files = count;
    },

    getAlreadyUploadedFilesCount: function () {
        return this.uploaded_files_in_model.length;
    },

    deleteUploadedFiles: function () {
        if (this.uploaded_files_in_model.length > 0 && this.delete_uploaded_files_list_url.length > 0) {
            var files_ids_to_delete = [];

            this.uploaded_files_in_model.forEach(function (item, i) {
                files_ids_to_delete.push(item.id);
            });

            $.post(this.delete_uploaded_files_list_url, {files_ids: files_ids_to_delete}, $.proxy(this.onDeleteUploadedFilesSuccess, this));
        }
    },

    onDeleteUploadedFilesSuccess: function (result) {
        if (result.success) {
            this.reset();
            this.removeBlocks(true);
        }
    },

    decrementAlreadyUploadedFile: function () {
        this.total_already_uploaded_files--;
    },

    getForm: function () {
        return $(this.model_form);
    },

    setForbiddenFilesMimeTypes: function(forbidden_files_types) {
        var self = this;

        self.forbidden_files_extension_list = [];
        console.log(forbidden_files_types);
        forbidden_files_types.forEach(function(item) {
            if (self.forbidden_files_extension_list.indexOf(item) == -1) {
                self.forbidden_files_extension_list.push(item);
            }
        });
    }
});
