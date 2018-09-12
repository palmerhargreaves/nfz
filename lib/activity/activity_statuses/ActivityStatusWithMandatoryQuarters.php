<?php

/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 02.07.2017
 * Time: 14:53
 */
class ActivityStatusWithMandatoryQuarters extends ActivityStatusBase
{
    public function getStatus()
    {
        //Только для кварталов которые больше 1-го
        /*if ($this->quarter < 2) {
            return ActivityModuleDescriptor::STATUS_ACCEPTED;
        }*/

        //Mandatory activity by quarters
        if ($this->activity->getMandatoryActivity() && $this->activity->hasMandatoryStatus()) {
            $mandatory_activity_q = $this->activity->getMandatoryQuartersList(true, $this->year);

            //If current quarter not in mandatory quarters list, do not check activity as complete
            if (!in_array($this->quarter, $mandatory_activity_q)) {
                return ActivityModuleDescriptor::STATUS_NONE;
            }
        }

        return ActivityModuleDescriptor::STATUS_ACCEPTED;
    }
}
