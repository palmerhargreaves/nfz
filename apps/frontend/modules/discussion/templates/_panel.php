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
                <?php /*
                        <div class="show-old-messages">
                                <div class="links">Показать сообщения: <span class="seven">7 дней</span>, <span class="thirty">30 дней</span>, <span class="all">все</span></div>
                                <div class="line"></div>
                        </div>
 * 
 */ ?>

                <div class="messages"></div>
            </div>
        </div>
    </div>
</fieldset>

<fieldset class="mod-popup-r">
    <div class="mod-popup-comment">
        <form id="discussion_model_comments" action="<?php echo url_for('@discussion_post') ?>" target="agreement-model-comments-frame" method="post" enctype="multipart/form-data"
              class="post ">
            <textarea name="message" rows="8" placeholder="Текст сообщения"></textarea>

            <div class="mod-popup-buttons" style="min-height: 30px;">
                <input type="hidden" name="response_form_name" />
                <input type="hidden" name="id" id="model_id"/>
                <input type="submit" class="message-button" value="Отправить" title="Ctrl+Enter">
            </div>

            <div class="model-form-selected-files-to-upload" style="margin-top: 10px;"></div>

            <?php if (!isset($disable_upload) || !$disable_upload): ?>
                <div class="message-upload-wrapper message-upload">
                    <div class="message-upload-button">
                        <div>
                            <input type="file" data-name="comments_files" id="comments_files" name="comments_files[]"
                                   multiple data-container-cls="model-form-selected-files-to-upload"
                                   style="height: 40px; width: 40px; opacity: 0; cursor: pointer;">
                        </div>
                    </div>
                    <div class="files"></div>
                    <div class="clear"></div>
                </div>
            <?php else: ?>
                <div class="message-upload-wrapper">
                </div>
            <?php endif; ?>
        </form>

    </div>
</fieldset>

<div style="clear:both;"></div>
