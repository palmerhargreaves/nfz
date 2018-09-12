<div class="scroller" style="margin-bottom: 10px; width: 630px;">
        <div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>
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
    <form id="discussion_model_comments" action="<?php echo url_for('@discussion_post') ?>" target="agreement-model-comments-frame" enctype="multipart/form-data" method="post" class="post">
        <div class="textarea-wrapper" style="margin-bottom: 10px; height: 140px; width: 470px;">
                <textarea name="message" style="height: 130px; width: 460px !important;"></textarea>
        </div>

        <div class="model-form-selected-files-to-upload"></div>

        <div class="message-upload-wrapper message-upload">
            <div class="message-upload-button">

                <input type="file" data-name="comment_files" id="comments_files" name="comments_files[]"
                       multiple data-container-cls="model-form-selected-files-to-upload"
                       style="height: 40px; width: 40px; opacity: 0; cursor: pointer;">
            </div>
            <div class="files"></div>
            <div class="clear"></div>
        </div>

        <div class="mod-popup-buttons" style="min-height: 30px; float: left; margin-left: 20px;">
            <input type="hidden" name="response_form_name" value="special_discussion_form" />
            <input type="hidden" name="model_id" id="model_id" />
            <input type="hidden" name="id" id="id" />
            <input type="submit" class="message-button " style="margin-top: 10px; margin-bottom: 5px;" value="Отправить" title="Ctrl+Enter">
        </div>
    </form>
</div>

