<div class="activity">
    <?php
    /*foreach($models as $n => $model) {
        $model->updateActivityTaskResult($activity, $sf_user->getAuthUser()->getDealer()->getRawValue());
    }*/

    ?>

    <?php include_partial('modal_model', array('activity' => $activity, 'model_types' => $model_types, 'model_types_fields' => $model_types_fields, 'model_place_fields' => $model_place_fields, 'concept_type' => $concept_type, 'dealer_files' => $dealer_files, 'activities' => $activities)) ?>
    <?php
    if ($activity->getHide()) {
        if ($sf_user->getRawValue()->getAuthUser()->isSuperAdmin() ||
            $sf_user->getRawValue()->getAuthUser()->checkUserDealerAcceptServiceActivity($activity->getId())
        ) {
            include_partial('activity/activity_head', array('activity' => $activity, 'quartersModels' => $quartersModels, 'current_q' => $current_q, 'current_year' => $current_year));

            include_partial('activity_models',
                array(
                    'activity' => $activity,
                    'has_concept' => $has_concept,
                    'concept' => $concept,
                    'models' => $models,
                    'blanks' => $blanks
                )
            );
        } else {
            include_partial('activity/activity_head', array('activity' => $activity, 'showTask' => false, 'quartersModels' => $quartersModels, 'current_q' => $current_q, 'current_year' => $current_year ));

            echo "<div class='activity-unavailable-text'>" . sfConfig::get('app_activity_unavailable') . "</div>";
        }
    } else {
        include_partial('activity/activity_head', array('activity' => $activity, 'quartersModels' => $quartersModels, 'current_q' => $current_q, 'current_year' => $current_year));

        /*if($activity->isManyYearsActivity()) {
            include_partial('activity_models_with_accordion',
                array(
                    'activity' => $activity,
                    'has_concept' => $has_concept,
                    'concept' => $concept,
                    'models' => $models,
                    'blanks' => $blanks,
                    'modelId' => $modelId
                )
            );

        } else {*/
            include_partial('activity_models',
                array(
                    'activity' => $activity,
                    'has_concept' => $has_concept,
                    'concept' => $concept,
                    'models' => $models,
                    'blanks' => $blanks,
                    'modelId' => $modelId
                )
            );
        //}
    }
    ?>
</div>
