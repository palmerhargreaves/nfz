$(document).ready(function () {
    var opened;

    $('#user-menu').click(function () {
        //$(this).toggleClass('open');
        $(this).find(".items").slideToggle();
    });

    $('#user-menu li').click(function () {
        $(this).find('a').each(function () {
            location.href = this.href;
        });
    });

    $('#user-messages').click(function (e) {
        if ($('.items', e.currentTarget).length > 0)
            $(this).toggleClass('open');
        else
            location.href = $('.messages-view-all', e.currentTarget).data('url');
    });

    $('.tabs').each(function () {
        var width = 0;
        $(this).children('.tab').each(function () {
            width += $(this).outerWidth();
        });
        $(this).siblings('.pane-shadow').width(width + 6);
    });

//	$('.tabs li').click(function(){
//		$('.tabs li').removeClass('active');
//		$(this).addClass('active');
//		$('.pane>div').removeClass('active');
//		$('.pane>div').eq($(this).index()).addClass('active');
//	});

    $('.tabs li').click(function (e) {
        if ($(e.target).closest('a').length > 0)
            return;

        var $a = $(this).closest('li').find('a');
        if ($a.length > 0)
            $a.clickAnchor();
    });

    $('.group.open .group-content').show();

    $('.group-header').click(function () {
        $(this).parents('.group').toggleClass('open');
        $(this).parents('.group').find('.group-content').slideToggle();

        if ($(this).parents('.group').hasClass('open'))
            $('html,body').animate({scrollTop: $(this).offset().top}, 500);
    });

    var matches = location.hash.match(/#material\/([0-9]+)(\/([0-9]+))?/);
    if (matches) {
        $('#materials .group').removeClass('open');
        $('#material-group-' + matches[1]).addClass('open');
    }

    $('#pass-change-link').click(function () {
        $('#pass-change').krikmodal('show');
    });

    $('#switch-to-dealer-link').click(function () {
        $('#switch-to-dealer').krikmodal('show');
    });

//	$('.modal-close').click(function(){
//		$(this).parents('.modal').hide();
//	});

//	$('#change-button').click(function(){
//		$('#pass-change').hide();
//		$('#pass-changed').show();
//	});

//	$('.banner').click(function(){
//		$('#zoom').show();
//		$('#zoom2').show();
//		opened = $(this);
//	});
//	
//	$('#zoom .modal-close').click(function(){
//		opened.addClass("closed");
//	});

    $('#begin-chat').click(function () {
        $('#chat-modal').krikmodal('show');
        if ($('#chat-modal').data('manager-discussion') != 'yes') {
            window.common_discussion.startDiscussion($('#chat-modal').data('dealer-discussion'));
        }
    });

    $('.unblock-model').click(function (e) {
        e.preventDefault();

        var bt = $(this);
        $.post(bt.closest('div[id=agreement-models]').data('url'),
            {
                modelId: bt.data('model-id')
            },
            function (result) {
                bt.fadeOut();
            });
    });

    $('#chat-modal select[name=dealer]').change(function () {
        var value = $(this).val();
        if (!value)
            window.common_discussion.stopDiscussion();
        else
            window.common_discussion.startDiscussionWithDealer(value);
    });

    var matches = location.hash.match(/#ask\/([0-9]+)\/([0-9]+)/);
    if (matches) {
        setTimeout(function () {
            $('#chat-modal').krikmodal('show');
            if ($('#chat-modal').data('manager-discussion') != 'yes') {
                window.common_discussion.startDiscussion($('#chat-modal').data('dealer-discussion'), matches[2]);
            } else {
                $('#chat-modal select[name=dealer]').val(matches[1]);
                window.common_discussion.startDiscussionWithDealer(matches[1], matches[2]);
            }
        }, 500);
    }

    $('#chat-modal').on('close-modal', function () {
        window.common_discussion.stopDiscussion();
    })

    $('.scroller').tinyscrollbar({size: 336, sizethumb: 41});

    $('#chat-modal').hide();

    $('.modal-file-wrapper .js-dealer-statistics-upload-file, .modal-file-wrapper .js-dealer-extended-statistics-upload-file').live('change', function () {
        var titles = [], files = this.files, total_files = 0, max_upload_files = $(this).data('max-upload-files'), max_files_cls = '';

        $.each(files, function (i, file) {
            var item_to_add = $('<div/>');

            item_to_add.append('<span class="model-add-file-name ' + max_files_cls + '">' + getUploadedFileTitle(file.name) + '</span>');
            item_to_add.append('( <span class="model-add-file-size ' + max_files_cls + '">' + humanFileSize(file.size) + '</span>)');

            titles.push('<div class="model-add-file-container">' + item_to_add.html() + '</div>');

            total_files++;
        });

        $(this).parents('.file').find('.file-name').html(titles.join('<br/>'));

    }).live('reset', function () {
        $(this).parents('.file').find('.file-name').html('');

        if ($(this).data('ext-model-file') == 1)
            $(this).closest('tr').remove();
    });

    $('.modal-file-wrapper input, .model-main-file, #comments_files').live('change', function () {

        //$(this).parents('.file').find('.file-name').html(file_title);
        var $parent_input = $("input[data-name=" + $(this).data('name') + "]"),
            max_upload_file_size = $('body').data('max-upload-file-size'),
            max_uploaded_files_count = $('body').data('max-files-upload-count') - 1,
            err_cls = '', err_count_cls ='', files = this.files,
            parentCls = $parent_input.data('container-cls');

        if ($(this).attr('data-file-index') != undefined) {
            file_title = getUploadedFileTitle(this.value);

            $(this).parents('.file').find('.file-name').html(file_title);
        } else {
            $('.' + parentCls).empty();

            console.log($(this).data('name'));
            if (window.agreement_model_report_form != undefined && (parentCls == 'report-form-selected-additional-files-to-upload' || parentCls == 'report-form-selected-financial-files-to-upload')) {
                if (window.agreement_model_report_form.getAdditionalFilesToUploadWithPlaces() != undefined && window.agreement_model_report_form.getAdditionalFilesToUploadWithPlaces().data('places-to-upload-orig') != 0 && parentCls == 'report-form-selected-additional-files-to-upload') {
                    if (files.length >= window.agreement_model_report_form.getAdditionalFilesToUploadWithPlaces().data('places-to-upload') ) {
                        window.agreement_model_report_form.getAdditionalFilesToUploadWithPlaces().removeAttr('data-required');
                        window.agreement_model_report_form.getAdditionalFilesToUploadWithPlaces().hide();
                    } else {
                        window.agreement_model_report_form.getAdditionalFilesToUploadWithPlaces().attr('data-required', true);
                        window.agreement_model_report_form.getAdditionalFilesToUploadWithPlaces().show();
                    }
                }
                window.agreement_model_report_form.updateUploadFileInfo($(this).data('name'), files.length);
            } else if (window.agreement_model_form != undefined) {
                window.agreement_model_form.updateUploadFileInfo($(this).data('name'), files.length);
            }
            if (parentCls != undefined) {
                $.each(files, function (i, file) {
                    file_title = getUploadedFileTitle(file.name);

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

                    $('.' + parentCls).append(append_text);
                    i++;
                });
            }
        }

    }).live('reset', function () {
        $(this).parents('.file').find('.file-name').html('');

        if ($(this).data('ext-model-file') == 1)
            $(this).closest('tr').remove();
    });

    function getUploadedFileTitle(file) {
        var name = file;
        var win_pattern = /.*\\(.*)/;
        var file_title = name.replace(win_pattern, "$1");
        var unix_pattern = /.*\/(.*)/;

        return file_title.replace(unix_pattern, "$1");
    }

    $('.modal-file-model-report input').live('change', function () {
        var idx = $(this).data('idx');

        if ($(this).prop('data-is-loaded') == undefined) {
            $(this).prop('data-is-loaded', true);

            idx++;
            $.post($(this).data('file-container-url'),
                {
                    fileIdx: idx
                },
                function (result) {
                    $('.panel-decline-files-container').append(result);
                }
            )
        }
    });

    $('input.date').datepicker({
        dateFormat: "dd.mm.y",
        beforeShowDay: function (date) {
            var modelType = $('input[name="model_type_id"]'),
                changeBt = $("div.change-period-model-type-" + modelType.val());

            if (modelType.length != 0) {
                if (modelType.data('is-sys-admin') && changeBt != undefined && changeBt.data('action') == 'apply')
                    return [true];

                var today = new Date().getTime() + (2 * 86400000),
                    tmp = new Date(today);

                if (date.getTime() > tmp.getTime())
                    return [true];

                if (parseInt(date.getMonth()) == parseInt(tmp.getMonth())) {
                    if (parseInt(date.getDate()) > parseInt(tmp.getDate()))
                        return [true];
                    else
                        return [false];
                }
            }

            return [false];
        }
    });

    $('input.dates-field').datepicker({dateFormat: "dd.mm.yy"});

    $(':input[placeholder]').defaultValue();
    $(':input.date.empty').removeClass('date');

    var rainbow = new Rainbow();
    rainbow.setSpectrum('00e900', 'ffcc00', 'f91800');

    $('.quarter-pane .timeline-wrapper .line .caret').each(function () {
        var percent = $(this).data("percent");
        var color = rainbow.colourAt(percent);
        $(this).css("background-color", "#" + color);
    });

    var blue = $(".budget .progressbar .blue");

    blue.each(function () {
        var blueElement = $(this);
        if (blueElement.data("percent") > 0)
            setTimeout(function () {
                blueElement.show().animate({width: blueElement.data("percent") + "%"}, 1000);
            }, 700)
    })

    showInfoMsg('what-info', 'В случае, если данный макет был ранее утвержден, укажите в данном поле номер заявки, в которой был согласован макет.');
    showInfoMsg('what-info-conception', 'Здесь вам необходимо добавить конечную дату действия сертификата на выгодное обслуживание, который вы будете выдавать участникам мероприятия после проверки.');

    $('div.add-child-field').live('click', function () {
        var isHide = false;

        $.each($(".type-fields-" + $(this).data('model-id')), function (ind, el) {
            if (!$(el).is(':visible') && !isHide) {
                $(el).fadeIn();

                isHide = true;
            }

        });
    });

    /*$('.remove-report-ext-file, .remove-concept-ext-file').live('click', function () {

    });*/

    if (RegExp('hash', 'gi').test(window.location.href)) {
        setTimeout(function () {
            $('#add-model-button').trigger('click');
        }, 2000);
    }

    $(document).on('mouseover', '.info-download-file-size', function () {

        $(this).popmessage('show', 'info', 'Размер загружаемого файла не должен превышать 5 Мб.');

        setTimeout(function () {
            $(this).popmessage('hide');
        }, 5000);
    });

    $(document).on('mouseout', '.info-download-file-size', function () {
        $(this).popmessage('hide');
    });

	$(document).on('click','.js-show-popup-article',function(){
		var href = $(this).attr('href');
		  $(href).krikmodal('show');
		return false;
	});

    $("a.tabHeader").live('click', function() {
        $.each($("a.tabHeader"), function(ind, el) {
            if($(el).parent().hasClass('active')) {
                $(el).parent().removeClass('active');
                $('#' + $(el).prop('name')).hide();
            }
        });

        $('.tab-pane').removeClass('active');
        if(!$(this).parent().hasClass('active')) {
            $(this).parent().addClass('active');
            $('#' + $(this).prop('name')).fadeIn().addClass('active');
        }
    });
});

function startDiscussionWithDealer(id) {
    $('#chat-modal').krikmodal('show');
    $('#chat-modal select[name=dealer]').val(id);
    window.common_discussion.startDiscussionWithDealer(id);
}

function showInfoMsg(from, msg) {
    $('.' + from).live('click', function () {
        $(this).popmessage('show', 'info', msg);

        setTimeout(function () {
            $('.' + from).popmessage('hide');
        }, 5000);
    });
}

function showAlertPopup(title, msg) {
    var $errorPopup = $('#j-alert-global');

    $errorPopup.find('.j-title').html(title);
    $errorPopup.find('.j-message').html(msg);
    $errorPopup.fadeIn();

    scrollTop('#j-alert-global');
    setTimeout(function() {
        $('#j-alert-global').fadeOut();
    }, 5000);
}

function scrollTop (ancor){
    if ($(ancor).length > 0) {
        $("body, html").animate({
                scrollTop: ($(ancor).offset().top - 10) + "px"
            },
            {duration: 500});
    }
}

function humanFileSize(bytes, si) {
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
}

function addShakeAnim (cls, form) {
    $(cls, form).addClass('shake-container');
    setTimeout(function() {
        $(cls, form).removeClass('shake-container');
    }, 500);
}
