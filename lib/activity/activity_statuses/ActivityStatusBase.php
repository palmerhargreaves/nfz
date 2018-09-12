<?php

/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 02.07.2017
 * Time: 14:43
 */

class ActivityStatusBase
{
    protected $user = null;
    protected $activity = null;
    protected $year = null;
    protected $quarter = null;
    protected $limit_activity = true;

    protected $dealer = null;

    protected $activity_models_created_count = 0;
    protected $activity_models_completed_count = 0;

    protected $fields_values = 0;

    protected $consider_activity_quarter = false;

    /**
     * ActivityStatusBase constructor.
     * @param $user
     * @param Activity $activity
     * @param null $by_year
     * @param null $by_quarter
     * @param bool $consider_activity_quarter
     * @param bool $limit_activity
     */
    public function __construct($user, Activity $activity, $by_year = null, $by_quarter = null, $consider_activity_quarter = false, $limit_activity = true)
    {
        $this->user = $user;
        $this->activity = $activity;
        $this->year = $by_year;
        $this->quarter = $by_quarter;
        $this->consider_activity_quarter = $consider_activity_quarter;
        $this->limit_activity = $limit_activity;

        $this->getCorrectDealer();
    }

    /**
     * Get correct user dealer
     */
    protected function getCorrectDealer() {
        $this->dealer = null;
        if ($this->user instanceof User) {
            $this->dealer = DealerUserTable::getInstance()->createQuery()->select('dealer_id')->where('user_id = ?', $this->user->getId())->fetchOne();

            if ($this->dealer) {
                $this->dealer = DealerTable::getInstance()->find($this->dealer->getDealerId());
            }
        } else if (is_numeric($this->user)) {
            $this->dealer = DealerTable::getInstance()->find($this->user);
        }
    }

    public function getUser() {
        return $this->dealer->getUser();
    }

    /**
     * Get activity status
     */
    public function getStatus() {
        //Если активность не привязана не к одному из модулей
        if ($this->activity->getModules()->count() == 0) {
            return ActivityModuleDescriptor::STATUS_NONE;
        }

        if (!$this->dealer) {
            $last_status = ActivityModuleDescriptor::STATUS_NONE;
            foreach ($this->activity->getAllModuleDescriptors($this->user) as $descriptor) {
                $status = $descriptor->getStatus();

                if ($status == ActivityModuleDescriptor::STATUS_WAIT_DEALER)
                    return $status;

                if ($status > $last_status)
                    $last_status = $status;
            }

            return $last_status;
        }

        $this->quarter = is_null($this->quarter) ? D::getQuarter(D::calcQuarterData(time())) : $this->quarter;

        $this->year = is_null($this->year) ? D::getYear(time()) : $this->year;

        //Get activity models count by year and quarter
        $this->activity_models_created_count = $this->activity->getActivityCreatedModelsByParams($this->dealer->getId(), $this->year, $this->quarter);

        //Get activity models count by year and quarters only completed
        $this->activity_models_completed_count = $this->activity->getActivityCreatedModelsByParams($this->dealer->getId(), $this->year, $this->quarter, true);

        /*Get models list and check if activity is own*/
        $model_list = AgreementModelTable::getInstance()->createQuery()->where('activity_id = ? and dealer_id = ?', array($this->activity->getId(), $this->dealer->getId()))->execute();
        if ($this->activity->getIsOwn() && count($model_list) > 0) {
            return ActivityModuleDescriptor::STATUS_WAIT_DEALER;
        } else if ($this->activity->getIsOwn()) {
            return ActivityModuleDescriptor::STATUS_NONE;
        }

        /*Check if activity must have completed all concepts*/
        if (!$this->activity->isConceptComplete($this->dealer) && $model_list->count() > 0) {
            return ActivityModuleDescriptor::STATUS_WAIT_DEALER;
        }

        /*Check activity tis completed by filled statistic*/
        /*Check us activity limit run, if set than check only accepted statistic by any quarter*/

        return $this->limitRunActivity();
    }

    /**
     * Правила поведения выполнения активности при ограниченно выполнении активности в год
     */
    protected function limitRunActivity() {
        /*
         * Get activity statistic fields counts
         * */
        $this->fields_values = ActivityFieldsValuesTable::getInstance()
            ->createQuery('fv')
            ->select()
            ->leftJoin('fv.ActivityFields af')
            ->where('dealer_id = ?', $this->dealer->getId())
            ->andWhere('af.activity_id = ?', $this->activity->getId())
            ->count();

        $activity_complete_result = AcceptedDealerActivityTable::getInstance()->createQuery()->where('activity_id = ? and dealer_id = ?', array($this->activity->getId(), $this->dealer->getId()))->execute();
        if ($this->activity->getIsLimitRun() && $this->limit_activity) {
            if (!$this->activity->getAllowExtendedStatistic()
                && $activity_complete_result->count() > 0
                && $this->activity->checkStatisticComplete($this->dealer->getId(), Activity::ACTIVITY_STATISTIC_TYPE_SIMPLE, null)) {
                return ActivityModuleDescriptor::STATUS_ACCEPTED;
            }
        } else if (!$this->activity->getAllowExtendedStatistic() && $activity_complete_result->count() > 0
            && $this->fields_values > 0
            && $this->activity_models_completed_count > 0
            && $this->activity->isActivityStatisticComplete($this->dealer, null, false, $this->year, $this->quarter, $this->consider_activity_quarter ? array('check_by_quarter' => true) : null)) {
            return ActivityModuleDescriptor::STATUS_ACCEPTED;
        }

        return ActivityModuleDescriptor::STATUS_NONE;
    }
}
