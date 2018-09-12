<div class="activity">
    <?php

    if ($activity->isVideoRecordStatisticsActive()) {
        include_partial('activity_video_record_statistic',
            array
            (
                'activity' => $activity,
                'current_q' => $current_q,
                'allow_to_edit' => $allow_to_edit_fields,
                'allow_to_cancel' => $allow_to_cancel,
                'disable_importer' => $disable_importer,
                'quartersModels' => $quartersModels,
                'current_year' => $current_year,
                'pre_check_statistic' => isset($pre_check_statistic) ? $pre_check_statistic : null
            )
        );
    } else {
        include_partial('activity/activity_head', array('activity' => $activity, 'quartersModels' => $quartersModels, 'current_q' => $current_q, 'current_year' => $current_year, 'show_quarters_tabs' => true));
        ?>
        <div class="content-wrapper">
            <?php include_partial('activity/activity_tabs', array('activity' => $activity, 'active' => 'statistic')) ?>

            <div id="agreement-models" class="pane clear pull-left">
                <div id="approvement" class="active">
                    <form id='frmStatistics' enctype="multipart/form-data" method="post" target="activity_statistic_target_iframe">
                        <div class="agreement-info" style="margin:10px; font-weight: bold;">
                            <?php
                            $descr = $activity->getStatsDescription();
                            if (!empty($descr)) echo $descr;
                            ?>
                        </div>
                        <br/>

                        <div class="stats-description" style="color: red; display: none; margin: 20px; margin-top: 25px;">
                            В периоде начальная дата должна быть меньше (равна) даты окончания<br/>
                            Все поля должны быть заполнены (для числовых значений разрешено использование "." )
                        </div>

                        <table class="models">
                            <tbody>
                            <?php
                            $n = 0;
                            $fields = ActivityFieldsTable::getInstance()->createQuery()->select('*')->where('activity_id = ?', $activity->getId())->orderBy('id ASC')->execute();
                            //foreach($activity->getActivityField() as $item):
                            foreach ($fields as $item):
                                $field = $item->getFieldByDealer($sf_user->getRawValue()->getAuthUser(), $statisticQuarter);

                                $description = $item->getDescription();
                                if (!$field) continue;
                                ?>
                                <tr class="statistic-item">
                                    <td style="width:605px; font-weight: bold; padding-left: 22px; <?php echo !empty($description) ? 'border-bottom: 0px;' : ''; ?>">
                                    <span style="float: left; display: block; width: 100%;">
                                        <?php echo $item->getName(); ?>
                                    </span>
                                    </td>
                                    <td class="darker"
                                        style="<?php echo !empty($description) ? 'border-bottom: 0px;' : ''; ?>">
                                        <?php
                                        if ($item->getType() == "date") {
                                            $period = explode("-", $field->getVal());
                                            ?>
                                            <div class="modal-input-wrapper input"
                                                 style='width: 124px; margin: 7px; float: left;'>
                                                <input type='text' name="periodStart" class='with-date'
                                                       style='height: 24px; padding: 5px; width: 110px;' placeholder='От'
                                                       value="<?php echo $period[0]; ?>"
                                                       data-type="<?php echo $item->getType(); ?>"
                                                       data-regexp="^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$"
                                                       data-field-id="<?php echo $item->getId(); ?>" required="true">
                                                <div class="modal-input-error-icon error-icon"></div>
                                                <div class="error message" style='display: none; z-index: 1;'></div>
                                            </div>
                                            <div class="modal-input-wrapper input"
                                                 style='width: 124px; margin: 7px; float: right;'>
                                                <input type='text' name="periodEnd" class='with-date'
                                                       style='height: 24px; padding: 5px; width: 110px;' placeholder='До'
                                                       value="<?php echo $period[1]; ?>"
                                                       data-type="<?php echo $item->getType(); ?>"
                                                       data-regexp="^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$"
                                                       data-field-id="<?php echo $item->getId(); ?>" required="true">
                                                <div class="modal-input-error-icon error-icon"></div>
                                                <div class="error message" style='display: none; z-index: 1;'></div>
                                            </div>
                                        <?php } else { ?>
                                            <div class="modal-input-wrapper input"
                                                 style='width: 124px; margin: 7px; float: right;'>
                                                <input type='text' class='' placeholder='0'
                                                       style='height: 24px; padding: 5px; width: 110px;'
                                                       data-type="<?php echo $item->getType(); ?>"
                                                    <?php if ($item->getType() == "number") { ?>
                                                        data-regexp="/^[0-9.]+$/"
                                                    <?php } else { ?>
                                                        data-regexp="/^[0-9a-zA-Zа-яА-Я\_\(\)\+\-\= ]+$/"
                                                    <?php } ?>
                                                       data-field-id="<?php echo $item->getId(); ?>"
                                                       value="<?php echo $field->getVal(); ?>"
                                                    <?php echo $item->getReq() == 1 ? "required" : ""; ?>>
                                                <div class="modal-input-error-icon error-icon"></div>
                                                <div class="error message" style='display: none; z-index: 1;'></div>
                                            </div>
                                        <?php } ?>
                                    </td>
                                </tr>

                                <?php
                                if (!empty($description)): ?>
                                    <tr class="statistic-item">
                                        <td colspan="2"><span
                                                    style="font-size: 10px; font-weight: normal; float: left; margin: 3px; padding-left: 10px; padding-right: 10px; text-align: justify;"><i><?php echo $description; ?></i></span>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            </tbody>
                        </table>

                        <div class="info-save-complete"
                             style="display: none; width: 99%; margin: 10px; padding: 10px; color: red; text-align: center; font-weight: bold;">
                            Параметры статистики успешно сохранены !
                        </div>

                        <div style="display: block; width: 99%; height: 55px;">
                            <button id="bt_on_save_statistic_data" class="button apply-stat-button"
                                    style="width: 25%; margin: 10px; margin-right: -5px; float:right; "
                                    data-id='<?php echo $sf_user->getAuthUser()->getId(); ?>'>Сохранить
                            </button>
                        </div>

                        <input type="hidden" name="year" value="<?php echo $current_year; ?>" />
                        <input type="hidden" name="quarter" value="<?php echo $statisticQuarter; ?>" />
                        <input type="hidden" name="activity" value="<?php echo $activity->getId(); ?>" />
                        <input type="hidden" name="txt_frm_fields_data" id="txt_frm_fields_data" />
                    </form>
                </div>
            </div>
        </div>

    <?php } ?>
</div>

<iframe src="/blank.html" width="1" height="1" frameborder="0" hspace="0" marginheight="0" marginwidth="0"
        name="activity_statistic_target_iframe" scrolling="no"></iframe>

<script>
    $(function () {
        window.activity_simple_statistic = new ActivitySimpleStatistic({
            on_save_data: '<?php echo url_for('@on_save_simple_activity_statistic'); ?>',
            on_cancel_url: '<?php //echo url_for(''); ?>'
        }).start();
    });
</script>

