<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 20.02.2016
 * Time: 15:50
 */

if ($sf_user->getAuthUser()->isSuperAdmin()):
    $roman = array(1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV');
    $periods = $activity->getActivityStatisticPeriodsInfo();

    if (!empty($periods)):
        foreach ($periods as $year => $quarters): ?>
            <div
                style="float: left; width: 100%; line-height: 15px; font-size: 11px; margin-left: 3px;">
                <div style="float: left; margin-right: 5px;"><?php echo $year; ?>г. -</div>
                <?php foreach ($quarters as $q): ?>
                    <div style="float: left; margin-right: 5px;"><?php echo $roman[$q]; ?></div>
                <?php endforeach; ?>
            </div>
            <?php
        endforeach;
    endif;
endif;
?>