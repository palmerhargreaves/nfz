<?php
if ($activity->isActivityStatisticHasSteps()) {
    include_partial('extended_statistic_with_steps_data', array( 'activity' => $activity, 'concept' => $concept, 'bindedConcept' => $bindedConcept, 'current_q' => $current_q, 'current_year' => $current_year ));
} else {
    include_partial('extended_statistic_data', array( 'activity' => $activity, 'concept' => $concept, 'current_q' => $current_q, 'current_year' => $current_year ));
}
?>
