<h1>Утверждения материала</h1>
<form action="/" method="post" target="accept-frame" enctype="multipart/form-data">
    <input type="hidden" name="id">
    <input type="hidden" name="step">
  <textarea class="full-width" placeholder="Введите комментарий" name="agreement_comments"></textarea>

    <div class="panel-decline-files-container" style="margin-top: 10px;">
        <?php include_partial('agreement_activity_model_management/panel_file', array('id' => 1)); ?>
        <!--<div class="file">
            <div class="modal-file-wrapper input">
                <div class="control">
                    <div class="button modal-zoom-button modal-form-button"></div>
                    <input type="file" name="agreement_comments_file" size="1">
                </div>
                <div class="file-name"></div>
                <div class="cl"></div>
                <div class="modal-input-error-icon error-icon"></div>
                <div class="error message"></div>
                <!--<div class="modal-form-requirements">Допустимый формат: pdf</div>-->
        <!--  </div>
          <div class="value modal-form-uploaded-file"></div>
      </div>-->
    </div>

<?php if($sf_user->isManager()): ?>
  <div class="buttons" style="width: 300px;">
            <div style="width: 105px;" class="accept green button float-left modal-form-button send-btn accept-accept-btn submit-btn">Утвердить</div>
            <div style="width: 105px;" class="decline gray button float-right modal-form-button cancel-btn">Отмена</div>
            <div class="clear"></div>
  </div>
<?php endif; ?>
</form>