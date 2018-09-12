<h1>Отклонение материала</h1>
<form action="/" method="post" target="decline-frame" enctype="multipart/form-data">
    <input type="hidden" name="id">

    <textarea placeholder="Введите комментарий" class="full-width" name="agreement_comments"></textarea>

    <div class="panel-decline-files-container template-popup-form">
        <?php include_partial('agreement_activity_model_management/panel_file', array('id' => 1)); ?>
    </div>


    <?php if ($sf_user->isSpecialist()): ?>
        <div class="buttons" style="width: 300px;">
            <div style="width: 105px;"
                 class="accept orange button float-left modal-form-button send-btn accept-decline-btn submit-btn">
                Отклонить
            </div>
            <div style="width: 105px;" class="decline gray button float-right modal-form-button cancel-btn">Отмена</div>
            <div class="clear"></div>
        </div>

    <?php endif; ?>

</form>