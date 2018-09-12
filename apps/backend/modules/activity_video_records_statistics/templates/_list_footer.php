<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 19.08.2016
 * Time: 9:58
 */
?>

<div class="modal hide fade history-models-move-modal" id="activities-copy-fields-formulas-modal"
     style="width: 950px; left: 45%; top: 30%;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4>Копирование</h4>
    </div>
    <div class="modal-body" style="max-height: 650px; ">
        <div class="modal-content-container" style="width: 100%; float:left;"></div>
    </div>
    <div class="modal-footer">
        <a href="#" id="btn-make-custom-data-copy" class="btn pull-left btn-success" style="color:#fff; display: none;" >Копировать</a>
        <a href="#" class="btn" data-dismiss="modal" aria-hidden="true">Закрыть</a>
    </div>
</div>

<script>
    $(function() {
        new ActivityCopyStatistic({
            on_copy_url: '<?php echo url_for('activity_copy_statistic'); ?>',
            on_custom_copy_init_data_url: '<?php echo url_for('activity_custom_copy_statistic_init_data'); ?>',
            on_custom_make_copy_data_url: '<?php echo url_for('activity_custom_make_copy_statistic_data'); ?>',
            btn_copy: '.bt-on-copy-activity-statistic',
            btn_custom_copy: '.bt-on-custom-copy-activity-statistic',
            btn_make_custom_copy: '#btn-make-custom-data-copy'

        }).start();
    });
</script>
