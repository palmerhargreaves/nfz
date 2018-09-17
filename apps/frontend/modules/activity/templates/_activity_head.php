<?php
$roman = array(1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV');

$customDateLen = 0;
$customDate = $activity->getCustomDate();
if (!$customDate) {
    $startDate = D::toLongRus($activity->getStartDate());
    $endDate = D::toLongRus($activity->getEndDate());

    $customDateLen = strlen($startDate . $endDate) + 4;
} else
    $customDateLen = strlen($customDate);
?>
<a href="<?php echo url_for('home/index') ?>" class="lnk-button lnk-button-back">&#9668; &nbsp;&nbsp;Назад</a>

<div class="activity-header-wrapper">
    <div class="activity-header">
        <div class="num"><?php echo $activity->getId() ?></div>
        <div class="title"
             style="<?php echo $customDateLen > 40 ? "width: 700px;" : ""; ?>"><?php echo $activity->getName() ?></div>
        <div class="date" style="<?php echo $customDateLen > 40 ? "width: 102px;" : ""; ?>">
            <?php if (!$activity->getCustomDate()) { ?>
                с <?php echo $startDate; ?>
                <br>
                по <?php echo $endDate; ?>
            <?php } else {
                echo $activity->getCustomDate();
            }
            ?>

        </div>

        <div class="activity-status-ico">
            <div class="img-wrapper">
                <?php

                $status_icon = null;
                switch ($activity->getStatus($sf_user->getRawValue()->getAuthUser())) {
//                    case ActivityModuleDescriptor::STATUS_IMPORTANCE;
//                      $status_icon = 'warn-icon.png';
//                      break;
                    case ActivityModuleDescriptor::STATUS_ACCEPTED:
                        $status_icon = 'ok-icon-active.png';
                        break;
                    case ActivityModuleDescriptor::STATUS_WAIT_AGREEMENT:
                        $status_icon = 'wait-icon.png';
                        break;
                    case ActivityModuleDescriptor::STATUS_WAIT_DEALER:
                        $status_icon = 'pencil-icon.png';
                        break;
                }
                ?>
                <?php if ($status_icon): ?>
                    <img src="/images/<?php echo $status_icon ?>"
                         style="<?php echo $activity->getSelectActivity() == 1 ? "position: relative; top: -10px;" : ""; ?>"
                         alt="">
                <?php endif; ?>

                <?php if ($activity->getSelectActivity() == 1): ?>
                    <img src="/images/warn-icon.png" alt="<?php ?>" title="<?php ?>"
                         style="position: relative; top: <?php echo $status_icon ? "-12px;" : ""; ?>">
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php
    $sel_q = false;
    if (!is_null($quartersModels) && $activity->isManyQuartersActivity()):
        $is_task_activity_complete = false;
        $dealer = $sf_user->getAuthUser()->getDealer();

        $qData = $quartersModels->getData();
        $quarters_list = $qData->getRawValue();

        if (!empty($quarters_list)):
            ?>
            <div class="tabs-quart">
                <ul>
                    <?php
                    $selected_q = $current_q;

                    $q_list = array();
                    $years_list = arraY();
                    foreach ($quarters_list as $y_key => $q_data) {
                        $years_list[] = $y_key;
                        $q_list = array_merge($q_list, array_map(function ($key) {
                            return $key;
                        }, array_keys($q_data)));
                    }

                    if (!in_array($current_q, $q_list)) {
                        $selected_q = key(array_slice($q_data, -1, 1, true));
                    }

                    /** check year if not exists in exists years, set last of years */
                    if (!empty($years_list) && !in_array($current_year, $years_list)) {
                        $current_year = array_pop($years_list);
                    }

                    //Учет статуса заполнения статистики
                    $activities_task_statistics = array(1 => false, 2 => false, 3 => false, 4 => false);

                    foreach ($qData as $y_key => $yItem):
                        foreach ($yItem as $q => $qItem):
                            $qDataItem = $qItem['data'];

                            $is_activity_complete = true;
                            if ($activity->getActivityField()->count() > 0 && $dealer) {
                                $is_activity_complete = $activity->isActivityStatisticComplete($dealer, null, false, $y_key, $q, array('check_by_quarter' => true));
                                //$is_activity_complete = $activity->checkForSimpleStatisticComplete($dealer->getId(), $q, $y_key);
                            } else if ($activity->getAllowExtendedStatistic() && $dealer) {
                                //$is_activity_complete = $activity->checkForStatisticComplete($dealer->getId(), $q, $y_key);
                                $is_activity_complete = $activity->isActivityStatisticComplete($dealer, null, false, $y_key, $q, array('check_by_quarter' => true));
                            }

                            $activities_task_statistics[$q] = $is_activity_complete;

                            /*if ($q == $selected_q) {
                                $is_task_activity_complete = $is_activity_complete;
                            }*/
                            ?>

                            <li id="statistic-tab-<?php echo $q; ?>"
                                class="<?php echo $selected_q == $q && $y_key == $current_year ? "active" : ""; ?>""
                            data-activity-q="<?php echo $q; ?>"
                            data-activity-year="<?php echo $y_key; ?>">
                            <a href="<?php echo url_for('@agreement_module_models_q_with_year?activity=' . $activity->getId() . '&quarter=' . $q . '&current_year=' . $y_key); ?>"><?php echo $y_key; ?>
                                г. <?php echo $roman[$q]; ?>-Квартал</a>
                            </li>

                        <?php endforeach;
                    endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!--<li class="complete"><a href="javascript:">г. III-Квартал</a></li>-->
    <?php //if (!isset($showTask) && $sel_q): ?>
    <div class="stages-wrapper" id="activity-stages">
        <?php
        $activity->callWithModule(function (ActivityModuleDescriptor $descriptor) use ($activity, $sf_user) {
            //$count = $activity->getTasks()->count();
            $additional = $descriptor->getActivityAdditional();

            echo $additional;
        }, $sf_user->getAuthUser()->getRawValue());

        ?>
        <?php
        $tasks = ActivityTaskTable::getInstance()->createQuery()->where('activity_id = ?', $activity->getId())->orderBy('position ASC')->execute();
        //foreach($activity->getTasks() as $n => $task):
        foreach ($tasks as $n => $task):
            $wasDone = false;

            try {
                $dealer = $sf_user->getAuthUser()->getDealer();
                if ($dealer) {
                    if ($task->wasDone($dealer->getRawValue(), $activity->getRawValue(), $current_q))
                        $wasDone = true;
                }
            } catch (Exception $ex) {
                if ($activity->getStatus($sf_user->getRawValue()->getAuthUser()) == ActivityModuleDescriptor::STATUS_ACCEPTED)
                    $wasDone = true;
            }
            ?>
            <div class="stage<?php if ($sf_user->isDealerUser() && $wasDone) echo ' active' ?>"><?php echo $task->getName() ?></div>
            <?php
        endforeach;

        $dealer = $sf_user->getAuthUser()->getDealer();

        //if ($activity->getActivityField()->count() > 0 && $dealer):
        if ($activity->isActivityStatisticActivatedInPeriod($current_year, $current_q)): ?>
            <div class="stage<?php echo isset($activities_task_statistics[$selected_q]) && $activities_task_statistics[$selected_q] ? ' active' : ''; ?>"
                 style="<?php echo $sf_user->getAuthUser()->isSuperAdmin() ? "height: 85px;" : ""; ?>">

                <div style="width: 100%; float: left;">
                    Статистика
                </div>

                <?php include_partial('activity/activity_statistic_quarters', array('activity' => $activity)); ?>
            </div>
        <?php elseif ($activity->getAllowExtendedStatistic() && $dealer):
            $is_activity_complete = $activity->isActivityStatisticComplete($dealer, null, false, $current_year, $current_q, array('check_by_quarter' => true));
            ?>
            <div class="stage<?php echo $is_activity_complete ? ' active' : ''; ?>" style="<?php //echo $sf_user->getAuthUser()->isSuperAdmin() ? "height: 85px;" : ""; ?>">
                <div style="width: 100%; float: left;">
                    Статистика
                </div>

                <?php include_partial('activity/activity_statistic_quarters', array('activity' => $activity)); ?>
            </div>
        <?php endif; ?>
    </div>

    <?php //endif; ?>

</div>
<script type="text/javascript">
    $(function () {
        $('#activity-stages .stage:last').addClass('last');
    });
</script>

