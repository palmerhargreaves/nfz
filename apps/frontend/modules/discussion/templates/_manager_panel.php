<div class="ask-q" style="display: inline-block;">
    <?php include_partial('discussion/form_search'); ?>

    <fieldset class="mod-popup-l">
        <div class="scroller">
            <div class="scrollbar">
                <div class="track">
                    <div class="thumb">
                        <div class="end"></div>
                    </div>
                </div>
            </div>
            <div class="viewport scroller-wrapper">
                <div class="overview scroller-inner">
                    <div class="messages"></div>
                </div>
            </div>
        </div>
    </fieldset>


    <fieldset class="mod-popup-r">
        <div class="mod-popup-comment">
            <form action="<?php echo url_for('@discussion_post') ?>" method="post" class="post"
                  enctype="multipart/form-data" id="discussion_upload_form" name="discussion_upload_form">
                <input type="hidden" name="<?php echo session_name(); ?>" value="<?php echo session_id(); ?>">
                <input type="hidden" name="upload_file_object_type" value=""/>
                <input type="hidden" name="upload_file_type" value=""/>
                <input type="hidden" name="upload_field" value=""/>
                <input type="hidden" name="upload_files_discussion_agreement_ids" value=""/>

                <textarea name="message" rows="8" placeholder="Текст сообщения"></textarea>

                <div class="message-send-buttons">
                    <div class="message-button-wrapper">
                        <input type="submit" class="message-button" value="Отправить" title="Ctrl+Enter"/>
                    </div>

                    <div class="message-button-wrapper btn-add-file" id="btn-add-discussion-agreement-dealer-files"
                         style="float: right; margin-right: 26px;">
                        <div style="margin: auto; width: 145px; float: right;" class="gray button">Добавить файл</div>
                    </div>
                </div>

                <div class="message-upload-wrapper message-upload">
                    <div class="file">
                        <div class="modal-file-wrapper input">
                            <div id="discussion-agreement-files-progress-bar"
                                 class="progress-bar-content progress-bar-full-width"></div>
                        </div>
                    </div>

                    <div class="file" style="min-height: 50px;">
                        <div class="modal-file-wrapper input">
                            <div class="d-popup-files-wrap scrollbar-inner">
                                <div class="d-popup-files-row">
                                    <div id="discussion_agreement_files"
                                         class="d-popup-uploaded-files d-cb" style="padding: 0px;"></div>
                                </div>
                            </div>

                            <div id="container_discussion_files" class="control dropzone"
                                 style="min-height: 0px; height: 0px !important; border: none !important;">
                                <input type="file" id="discussion_agreement_comment_file"
                                       name="discussion_agreement_comment_file"
                                       style="height: 0px;" multiple>
                            </div>
                        </div>
                    </div>
                    <div class="files"></div>
                    <div class="clear"></div>
                </div>
            </form>
        </div>
    </fieldset>
</div>
