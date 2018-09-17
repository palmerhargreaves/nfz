<div class="scroller" style="margin-bottom: 10px; width: 630px;">
    <div class="scrollbar">
        <div class="track">
            <div class="thumb">
                <div class="end"></div>
            </div>
        </div>
    </div>
    <div class="viewport scroller-wrapper">
        <div class="overview scroller-inner">
            <?php /*
                        <div class="show-old-messages">
                                <div class="links">Показать сообщения: <span class="seven">7 дней</span>, <span class="thirty">30 дней</span>, <span class="all">все</span></div>
                                <div class="line"></div>
                        </div>
 * 
 */ ?>

            <div class="special-messages"></div>
        </div>
    </div>
</div>

<div class="message-send-wrapper panel-special-message" style="min-height: 176px;">
    <form id="discussion_upload_form" name="discussion_upload_form" action="<?php echo url_for('@discussion_post') ?>"
           enctype="multipart/form-data" method="post" class="post">
        <input type="hidden" name="<?php echo session_name(); ?>" value="<?php echo session_id(); ?>">
        <input type="hidden" name="upload_file_object_type" value=""/>
        <input type="hidden" name="upload_file_type" value=""/>
        <input type="hidden" name="upload_field" value=""/>
        <input type="hidden" name="upload_files_discussion_agreement_ids" value=""/>

        <div class="textarea-wrapper" style="margin-bottom: 10px; height: 140px; width: 470px;">
            <textarea name="message" style="height: 130px; width: 460px !important;"></textarea>
        </div>

        <div class="model-form-selected-files-to-upload"></div>

        <div class="message-upload-wrapper message-upload">
            <div class="file">
                <div class="modal-file-wrapper input">
                    <div id="discussion-files-progress-bar"
                         class="progress-bar-content progress-bar-full-width"></div>
                </div>
            </div>

            <div class="file" style="min-height: 1px;">
                <div class="modal-file-wrapper input">
                    <div class="d-popup-files-wrap scrollbar-inner">
                        <div class="d-popup-files-row">
                            <div id="discussion_files"
                                 class="d-popup-uploaded-files d-cb" style="padding: 0px; min-height: 1px;"></div>
                        </div>
                    </div>

                    <div id="container_discussion_files" class="control dropzone"
                         style="min-height: 0px; height: 0px !important; border: none !important; padding: 1px;">
                        <input type="file" id="discussion_comment_file" name="discussion_comment_file"
                               style="height: 0px;" multiple>
                    </div>
                </div>
            </div>
            <div class="files"></div>
            <div class="clear"></div>
        </div>

        <div class="mod-popup-buttons" style="min-height: 30px; float: left; margin-left: 20px; width: 95%;">
            <input type="hidden" name="response_form_name" value="special_discussion_form"/>
            <input type="hidden" name="model_id" id="model_id"/>
            <input type="hidden" name="id" id="id"/>

            <input id="bt-special-message" type="submit" class="message-button " style="margin-top: 10px; margin-bottom: 5px; float: right;" value="Отправить" title="Ctrl+Enter">

            <div class="message-button-wrapper btn-add-file" id="btn-add-discussion-dealer-files" style="margin-top: 5px;">
                <div style="margin-top: 1px; margin-bottom: 5px; width: 145px !important; " class="gray button">Добавить файл</div>
            </div>
        </div>
    </form>
</div>

