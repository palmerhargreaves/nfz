<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 20.03.2018
 * Time: 13:01
 */

class ActivityBudgetPointsStatusesFactory extends ActivityStatusesFactory {
    protected static $_self_instance = null;

    public static function getInstance() {
        if (is_null(self::$_self_instance)) {
            self::$_self_instance = new ActivityBudgetPointsStatusesFactory();
        }

        return self::$_self_instance;
    }

    /**
     * Получить статус выполнения активности по бюджету
     * @param $user
     * @param Activity $activity
     * @param null $by_year
     * @param null $by_quarter
     * @param bool $consider_activity_quarter
     * @param bool $limit_activity
     * @return int|void
     */
    public function getStatus($user, Activity $activity, $by_year = null, $by_quarter = null, $consider_activity_quarter = false, $limit_activity = true)
    {
        if ($activity->getAllowExtendedStatistic() && ActivityExtendedStatisticFieldsTable::getInstance()->createQuery()->where('activity_id = ?', $activity->getId())->count() > 0) {
            $this->activity_status = new ActivityBudgetPointsExtendedStatisticStatuses($user, $activity, $by_year, $by_quarter, $consider_activity_quarter, $limit_activity);
        } else {
            $this->activity_status = new ActivityBudgetPointsStatuses($user, $activity, $by_year, $by_quarter, $consider_activity_quarter, $limit_activity);
        }

        return $this->activity_status->getStatus();
    }
}
