<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <div class="well sidebar-nav">
                <ul class="nav nav-list">
                    <li class="nav-header">Заблокированные заявки</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12">
            <div class="well sidebar-nav">
                <?php $blocked_models_count = $models['total_blocked_models']; ?>
                <div
                    class="alert alert-<?php echo $blocked_models_count > 0 ? "success" : "error"; ?> container-success"
                    style="">
                    <?php if ($blocked_models_count > 0): ?>
                        Всего заблокировано заявок: <?php echo $blocked_models_count; ?>
                    <?php else: ?>
                        Список блокированных заявок пуст
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div id="blocked-models-list" class="row-fluid">
        <?php include_partial('blocked_models_list', array('models' => $models)); ?>
    </div>
</div>

<div class="modal hide fade model-history-modal" id="blocked-model-info" style="width: 500px; left: 45%;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4>История блокировок</h4>
    </div>
    <div class="modal-body">
        <div class="modal-content-container" style="width: 100%; float:left;"></div>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn" data-dismiss="modal" aria-hidden="true">Закрыть</a>
    </div>
</div>

<script type="text/javascript">
    window.agreementModelsBlocked = new AgreementModelBlockedInfo({
        modal: '#blocked-model-info',
        show_url: '<?php echo url_for('agreement_model_show_blocked_info'); ?>',
    }).start();
</script>