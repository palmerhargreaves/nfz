<div class="activity">
<?php
    if($activity->getHide())
    {
        if($sf_user->getRawValue()->getAuthUser()->isSuperAdmin() ||
            $sf_user->getRawValue()->getAuthUser()->checkUserDealerAcceptServiceActivity($activity->getId())) {
            include_partial('activity/activity_head', array('activity' => $activity, 'year' => $year));
            include_partial('activity_data', array('activity' => $activity));
        }
        else {
            include_partial('activity/activity_head', array('activity' => $activity, 'quartersModels' => $quartersModels, 'showTask' => false, 'year' => $year));

            echo "<div class='activity-unavailable-text'>".sfConfig::get('app_activity_unavailable')."</div>";
        }
    }
    else {
        include_partial('activity/activity_head', array('activity' => $activity, 'quartersModels' => $quartersModels, 'year' => $year));
        include_partial('activity_data', array('activity' => $activity));
    }

?>
</div>