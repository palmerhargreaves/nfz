<?php

/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 02.07.2017
 * Time: 14:40
 */

class ActivityStatusesFactory
{
    protected static $_instance = null;

    protected $activity_status = null;

    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new ActivityStatusesFactory();
        }

        return self::$_instance;
    }

    /**
     * Get activity status by activity type (with simple statistic, with extended statistic or have mandatory quarters)
     * @param $user
     * @param Activity $activity
     * @param null $by_year
     * @param null $by_quarter
     * @param bool $consider_activity_quarter
     * @param bool $limit_activity
     * @return int
     */
    public function getStatus($user, Activity $activity, $by_year = null, $by_quarter = null, $consider_activity_quarter = false, $limit_activity = true) {
        $cls = 'WithBaseStatistic';
        if ($activity->getAllowExtendedStatistic() && ActivityExtendedStatisticFieldsTable::getInstance()->createQuery()->where('activity_id = ?', $activity->getId())->count() > 0) {
            $cls = 'WithExtendedStatistic';
        }

        $cls = 'ActivityStatus'.$cls;
        $this->activity_status = new $cls($user, $activity, $by_year, $by_quarter, $consider_activity_quarter, $limit_activity);

        $status = $this->activity_status->getStatus();

        //Делаем дополнительную проверку на обязательную активность в квартале
        if ($activity->hasMandatoryStatus() && $consider_activity_quarter) {
            $cls = 'ActivityStatusWithMandatoryQuarters';

            $this->activity_mandatory_status = new $cls($user, $activity, $by_year, $by_quarter, $consider_activity_quarter, $limit_activity);
            if ($this->activity_mandatory_status->getStatus() == ActivityModuleDescriptor::STATUS_NONE) {
                return $status;
            }

            return $status == ActivityModuleDescriptor::STATUS_ACCEPTED && $this->activity_mandatory_status->getStatus() == ActivityModuleDescriptor::STATUS_ACCEPTED ? ActivityModuleDescriptor::STATUS_ACCEPTED : ActivityModuleDescriptor::STATUS_WAIT_DEALER;
        }

        return $status;
    }

    /**
     * Get user
     */
    public function getUser() {
        return $this->activity_status->getUser();
    }
}
