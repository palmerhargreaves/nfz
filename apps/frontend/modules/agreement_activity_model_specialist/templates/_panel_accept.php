<h1>Утверждения материала</h1>
<form action="/" method="post" target="accept-frame" enctype="multipart/form-data">
  <input type="hidden" name="id">
    <textarea class="full-width" placeholder="Введите комментарий" name="agreement_comments"></textarea>

    <div class="panel-decline-files-container template-popup-form" style="margin-top: 15px;">
        <?php include_partial('agreement_activity_model_management/panel_file', array('id' => 1)); ?>
    </div>

<?php if($sf_user->isSpecialist()): ?>
    <div class="buttons" style="width: 300px;">
            <div style="width: 105px;" class="accept green button float-left modal-form-button send-btn accept-accept-btn submit-btn">Утвердить</div>
            <div style="width: 105px;" class="decline gray button float-right modal-form-button cancel-btn">Отмена</div>
            <div class="clear"></div>
    </div>
<?php endif; ?>

</form>