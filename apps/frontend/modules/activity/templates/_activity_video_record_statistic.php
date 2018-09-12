<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 21.06.2016
 * Time: 20:24
 */

?>
<?php include_partial('activity/activity_head', array( 'activity' => $activity, 'quartersModels' => $quartersModels, 'current_q' => $current_q, 'current_year' => $current_year, 'show_quarters_tabs' => true )); ?>
<div class="content-wrapper">
    <?php include_partial('activity/activity_tabs', array( 'activity' => $activity, 'active' => 'statistic' )) ?>

    <div class="pane-shadow"></div>

    <form id='frmStatistics' enctype="multipart/form-data" method="post" target="activity_video_record_target_iframe">
        <div id="agreement-models" class="pane clear" style="margin-top: 20px;">
            <?php if ($pre_check_statistic == ActivityStatisticPreCheckAbstract::CHECK_STATUS_IN_PROGRESS): ?>
                <div class="alert alert-callout alert-info" role="alert" style="display: block;">
                    <strong>Статистика отправлена на согласование.</strong>
                </div>
            <?php elseif ($pre_check_statistic == ActivityStatisticPreCheckAbstract::CHECK_STATUS_CHECKED): ?>
                <div class="alert alert-callout alert-success" role="alert" style="display: block;">
                    <strong>Статистика согласована.</strong>
                </div>
            <?php elseif ($pre_check_statistic == ActivityStatisticPreCheckAbstract::CHECK_STATUS_CANCEL): ?>
                <div class="alert alert-callout alert-warning" role="alert" style="display: block;">
                    <strong>Статистика отклонена.</strong>
                </div>
            <?php endif; ?>

            <div id="approvement" class="active">
                <div id="materials" style="float: left; width: 99%;">
                    <div id="accommodation" class="active">
                        <div class="container-for-activity-video-record-statistics-fields">
                            <?php include_partial('activity_headers_fields_group_list',
                                array(
                                    'activity' => $activity,
                                    'allow_to_edit' => $allow_to_edit,
                                    'current_q' => $current_q,
                                    'allow_to_cancel' => $allow_to_cancel
                                )
                            ); ?>
                        </div>
                    </div>
                </div>
            </div>

            <table class="models">
                <tbody></tbody>
            </table>

            <div class="info-save-complete" style="display: none; width: 99%; margin: 10px; padding: 10px; color: red; text-align: center; font-weight: bold;"></div>

            <?php if (isset($pre_check_statistic) && !is_null($pre_check_statistic) && $pre_check_statistic == ActivityStatisticPreCheckAbstract::CHECK_STATUS_IN_PROGRESS
                && ($sf_user->getAuthUser()->isImporter() || $sf_user->getAuthUser()->isManager())): ?>

                <div id="bts-container" style="display: block; width: 99%; height: 55px;">
                    <button id="bt_on_decline_statistic" class="button apply-stat-button"
                            style="width: 25%; margin: 10px; margin-right: -5px; float:right;"
                            data-id='<?php echo $sf_user->getAuthUser()->getRawValue()->getDealer()->getId(); ?>'>
                        Отклонить
                    </button>

                    <button id="bt_on_accept_statistic" class="button apply-stat-button"
                            style="width: 25%; margin: 10px; margin-right: -5px; float:right;"
                            data-id='<?php echo $sf_user->getAuthUser()->getRawValue()->getDealer()->getId(); ?>'>
                        Согласовать
                    </button>
                </div>
            <?php else: ?>
                <?php if ($allow_to_edit): ?>
                    <div id="bts-container" style="display: block; width: 99%; height: 55px;">
                        <?php if (!$disable_importer): ?>
                            <button id="bt_on_save_statistic_data_once" class="button apply-stat-button"
                                    style="width: 25%; margin: 10px; margin-right: -5px; float:right;"
                                    data-id='<?php echo $sf_user->getAuthUser()->getRawValue()->getDealer()->getId(); ?>'>
                                Отправить импортеру
                            </button>
                        <?php endif; ?>

                        <button id="bt_on_save_statistic_data_many" class="button apply-stat-button"
                                style="width: 25%; margin: 10px; margin-right: -5px; float:right;"
                                data-id='<?php echo $sf_user->getAuthUser()->getRawValue()->getDealer()->getId(); ?>'>
                            Сохранить
                        </button>
                    </div>
                <?php endif; ?>

                <?php if ($allow_to_cancel): ?>
                    <button id="bt_on_cancel_statistic_data" class="button gray cancel-stat-button"
                            style="width: 25%; margin: 10px; margin-right: -5px; float:right;"
                            data-id='<?php echo $sf_user->getAuthUser()->getRawValue()->getDealer()->getId(); ?>'>Отменить
                    </button>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <input type="hidden" name="quarter"
               value="<?php echo !empty($statisticQuarter) ? $statisticQuarter : $current_q; ?>"/>
        <input type="hidden" name="year" value="<?php echo $current_year; ?>" />
        <input type="hidden" name="activity" value="<?php echo $activity->getId(); ?>"/>
        <input type="hidden" name="txt_frm_fields_data" id="txt_frm_fields_data"/>
    </form>
</div>

<iframe src="/blank.html" width="1" height="1" frameborder="0" hspace="0" marginheight="0" marginwidth="0"
        name="activity_video_record_target_iframe" scrolling="no"></iframe>

<div id="confirm-send-data-to-specialist-modal" class="modal" style="width:400px;">
    <div class="white modal-header">Отправка данных</div>
    <div class="modal-close"></div>
    После отправки данных Вы не сможете их отредактировать. Вы уверены, что хотите отправить заполненную информацию ?
    <div style="display: block; width: 75%; margin: auto; margin-top: 40px;">
        <button id="bt-send-video-record-statistic-data" class="button accept-button"
                style="width: 45%; float: left; clear: both;">Отправить
        </button>
        <button id="bt-cancel-send-video-record-statistic" class="button gray decline-button"
                style="width: 45%; float: right;"
                data-id='<?php echo $sf_user->getAuthUser()->getId(); ?>'>Отменить
        </button>
    </div>
</div>

<?php if ($activity->getActivityVideoStatistics() && $activity->getActivityVideoStatistics()->getFirst()->getAllowStatisticPreCheck()): ?>
<div id="activity-accept-cancel-info-modal" class="modal" style="width:400px;">
    <div class="white modal-header">Согласование статистики</div>
    Статистика отправлена на согласование. Дождитесь ответа импортера.

    <div style="display: block; width: 75%; margin: auto; margin-top: 40px;">
        <button id="bt-activity-close-pre-check" class="button gray" style="width: 45%; float: right;" data-id='<?php echo $sf_user->getAuthUser()->getId(); ?>'>Закрыть
        </button>
    </div>
</div>
<?php endif; ?>


<div id="confirm-send-data-to-specialist-modal" class="modal" style="width:400px;">
    <div class="white modal-header">Отправка данных</div>
    <div class="modal-close"></div>
    После отправки данных Вы не сможете их отредактировать. Вы уверены, что хотите отправить заполненную информацию ?
    <div style="display: block; width: 75%; margin: auto; margin-top: 40px;">
        <button id="bt-send-video-record-statistic-data" class="button accept-button"
                style="width: 45%; float: left; clear: both;">Отправить
        </button>
        <button id="bt-cancel-send-video-record-statistic" class="button gray decline-button"
                style="width: 45%; float: right;"
                data-id='<?php echo $sf_user->getAuthUser()->getId(); ?>'>Отменить
        </button>
    </div>
</div>

<script>
    $(function () {
        window.activity_video_record_statistic = new ActivityVideoRecordStatistic({
            on_add_new_fields: '<?php echo url_for('@on_add_new_video_record_statistic_fields');?>',
            on_save_data: '<?php echo url_for('@on_save_video_record_statistic'); ?>',
            on_save_importer_data: '<?php echo url_for('@on_save_importer_video_record_statistic');?>',
            on_delete_field: '<?php echo url_for('@on_delete_video_record_field'); ?>',
            on_cancel_url: '<?php echo url_for('@on_cancel_statistic_data'); ?>',
            on_accept_statistic_data_by_user_url: '<?php echo url_for('@on_accept_statistic_data_by_user'); ?>',
            on_cancel_statistic_data_by_user: '<?php echo url_for('@on_cancel_statistic_data_by_user'); ?>',
            activity_id: <?php echo $activity->getId(); ?>,
            quarter: <?php echo $sf_user->getCurrentQuarter(); ?>,
            year: <?php echo $current_year; ?>,
        }).start();

        $('#frmStatistics .with-date').datepicker();
    });
</script>
