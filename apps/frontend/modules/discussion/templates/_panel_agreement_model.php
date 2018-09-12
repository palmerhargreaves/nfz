<div class="mod-popup-l">
    <div class="scroller" style="margin-bottom: 10px;">
        <div class="scrollbar">
            <div class="track">
                <div class="thumb">
                    <div class="end"></div>
                </div>
            </div>
        </div>
        <div class="viewport scroller-wrapper" style="">
            <div class="overview scroller-inner">
                <?php /*
			<div class="show-old-messages">
					<div class="links">Показать сообщения: <span class="seven">7 дней</span>, <span class="thirty">30 дней</span>, <span class="all">все</span></div>
					<div class="line"></div>
			</div>
*/ ?>
                <div class="messages"></div>
            </div>
        </div>
    </div>
</div>

<div class="mod-popup-r">
    <div class="mod-popup-comment" data-not-hide='1'>
        <form id="discussion_model_comments" action="<?php echo url_for('@discussion_post') ?>" target="agreement-model-comments-frame"  method="post" class="post " enctype="multipart/form-data">
            <textarea name="message" rows="8"></textarea>

            <div class="model-form-selected-files-to-upload"></div>

            <div class="mod-popup-buttons" style="min-height: 30px;">
                <input type="hidden" name="id" id="model_id" />
                <input type="submit" class="message-button" value="Отправить" title="Ctrl+Enter">
            </div>
        <?php if (!isset($disable_upload) || !$disable_upload): ?>
            <div class="message-upload-wrapper message-upload">
                <div class="message-upload-button">

                    <input type="file" data-name="comment_files" id="comments_files" name="comments_files[]"
                            multiple data-container-cls="model-form-selected-files-to-upload"
                            style="height: 40px; width: 40px; opacity: 0; cursor: pointer;">
                </div>
                <div class="files"></div>
                <div class="clear"></div>
            </div>
        <?php else: ?>
            <div class="message-upload-wrapper"></div>
        <?php endif; ?>
        </form>


    </div>
</div>

<div style="clear:both;"></div>


