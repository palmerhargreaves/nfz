<?php

/**
 * Helps to build statistic by Activities agreement
 *
 */
class AgreementActivitiesStatisticBuilder
{
    protected $activities = null;
    /**
     * Activity to filter statistic
     *
     * @var Activity
     */
    protected $activity;
    protected $finished;
    protected $load_dealers;
    protected $dealers = null;
    protected $year_filter = false;
    protected $quarter_filter = false;
    protected $_user = null;

    function __construct(Activity $activity = null, $finished = null, $load_dealers = true, User $user = null)
    {
        $this->activity = $activity;
        if ($finished === null)
            $this->finished = $activity ? $activity->getFinished() : null;
        else
            $this->finished = $finished;

        $this->load_dealers = $load_dealers;
        $this->year_filter = date('Y');

        $this->_user = $user;
    }

    function setQuarterFilter($year, $quarter)
    {
        $this->year_filter = $year;
        $this->quarter_filter = $quarter;

        $this->reset();
    }

    function setYearFilter($year)
    {
        $this->year_filter = date('Y');
        if (!empty($year))
            $this->year_filter = $year;

        $this->quarter_filter = false;

        $this->reset();
    }

    function reset()
    {
        $this->activities = null;
    }

    /**
     * Build statistic
     *
     * @return array a statistic
     */
    function build()
    {
        $this->loadActivities();

        if ($this->activities) {
            $this->loadAll();
            $this->loadDone();
            $this->loadInWork();
            $this->loadNoWork();
        }

        return $this->activities;
    }

    function buildDone()
    {
        $this->loadActivities();
        if ($this->load_dealers)
            $this->loadDealers();

        if ($this->activities)
            $this->loadDone();

        return $this->activities;
    }

    function buildInWork()
    {
        $this->loadActivities();

        if ($this->activities) {
            $this->loadAll();
            $this->loadInWork();
        }

        return $this->activities;
    }

    function buildNoWork()
    {
        $this->loadActivities();

        if ($this->activities) {
            $this->loadAll();

            $this->loadDone();
            $this->loadInWork();
            $this->loadNoWork();
        }

        return $this->activities;
    }

    function getStat()
    {
        return $this->activities;
    }

    protected function loadActivities()
    {
        if ($this->dealers !== null)
            return;

        $this->activities = array();
        $query = ActivityTable::getInstance()
            ->createQuery('a')
            ->orderBy('a.importance DESC, sort DESC, a.id DESC');

        if ($this->finished !== null)
            $query->where('finished=?', $this->finished);

        if ($this->activity)
            $query->andWhere('id=?', $this->activity->getId());

        if ($this->year_filter || $this->quarter_filter) {
            if ($this->quarter_filter) {
                //var_dump(($this->quarter_filter - 1) * 3 + 1);exit;

                $start_date = mktime(0, 0, 0, ($this->quarter_filter - 1) * 3 + 1, 1, $this->year_filter);
                $end_date = strtotime('+3 months -1 day', $start_date);
            } else {
                $start_date = mktime(0, 0, 0, 1, 1, $this->year_filter);
                $end_date = strtotime('+1 year -1 day', $start_date);
            }

            $start_date = D::toDb($start_date);
            $end_date = D::toDb($end_date);

            $query->andWhere(
                '(a.custom_date<>? and a.custom_date is not null) or a.start_date between ? and ? or a.end_date between ? and ?'
                . ' or ? between a.start_date and a.end_date or ? between a.start_date and a.end_date',
                array('', $start_date, $end_date, $start_date, $end_date, $start_date, $end_date)
            );
        }

        foreach ($query->execute() as $activity) {
            $this->activities[$activity->getId()] = array(
                'activity' => $activity,
                'done' => 0,
                'done_dealers_count' => 0,
                'done_dealers' => array(),
                'in_work' => 0,
                'in_work_dealers' => array(),
                'no_work' => 0,
                'no_work_dealers' => array(),
                'all' => array()
            );
        }
    }

    protected function loadDone()
    {
        $query = Doctrine_Query::create()
            ->select('a.*, m.dealer_id, m.name, r.status')
            ->from('Activity a')
            ->innerJoin('a.AgreementModels m')
            ->leftJoin('m.Report r')
            ->whereIn('a.id', $this->getLoadedActivityIds())
            ->andWhere('m.status = ? and r.status = ?', array('accepted', 'accepted'))
            ->andWhere('m.model_type_id != ?', Activity::CONCEPT_MODEL_TYPE_ID)
            ->groupBy('a.id, m.dealer_id');

        $show = true;
        $user_dealers_list = $this->getUserDealersList();
        if (!empty($user_dealers_list)) {
            $query->andWhereIn('m.dealer_id', $user_dealers_list);
        }

        if ($this->year_filter) {
            $query->andWhere('year(r.accept_date)=?', $this->year_filter);
        }

        if ($this->_user->isRegionalManager() && empty($user_dealers_list)) {
            $show = false;
        }

        if ($show) {
            $result = $query->execute();
            foreach ($result as $activity) {
                foreach ($activity->getAgreementModels() as $model) {
                    if ($model->getStatus() == "accepted" && $model->getReport() && $model->getReport()->getStatus() == "accepted") {
                        $this->activities[$activity->getId()]['done']++;

                        if ($this->load_dealers && isset($this->dealers[$model->getDealer()->getId()])) {
                            $this->activities[$activity->getId()]['done_dealers'][$model->getDealer()->getId()] = $this->dealers[$model->getDealer()->getId()];
                        }
                    }
                }
            }
        }

    }

    protected function loadInWork()
    {
        if ($this->year_filter || $this->quarter_filter) {
            if ($this->year_filter && !$this->quarter_filter) {
                if ($this->year_filter != date('Y'))
                    return;
            } else {
                if ($this->year_filter != date('Y') || $this->quarter_filter != D::getQuarter(time()))
                    return;
            }
        }

        $query = Doctrine_Query::create()
            ->select('a.id, m.dealer_id, count(m.id) as models')
            ->from('Activity a')
            ->innerJoin('a.AgreementModels m')
            ->whereIn('a.id', $this->getLoadedActivityIds())
            ->groupBy('a.id, m.dealer_id')
            ->having('models > 0');

        $show = true;

        $user_dealers_list = $this->getUserDealersList();
        if (!empty($user_dealers_list)) {
            $query->andWhereIn('m.dealer_id', $user_dealers_list);
        }

        if ($this->year_filter) {
            $query->andWhere('year(m.created_at) = ?', $this->year_filter);
        }

        if ($this->_user->isRegionalManager() && empty($user_dealers_list)) {
            $show = false;
        }

        if ($show) {
            $result = $query->execute();
            foreach ($result as $row) {
                foreach ($row->getAgreementModels() as $model) {
                    if ($model->getStatus() != "accepted" || ($model->getReport() && $model->getReport()->getStatus() != "accepted")) {
                        $this->activities[$row->getId()]['in_work']++;

                        if ($this->load_dealers && isset($this->dealers[$model->getDealerId()])) {
                            $this->activities[$row->getId()]['in_work_dealers'][$model->getDealerId()] = $this->dealers[$model->getDealerId()];
                        }
                    }
                }
            }
        }
    }

    protected function loadNoWork()
    {
        foreach ($this->activities as $id => $item) {
            foreach ($this->dealers as $did => $dealer) {
                if (empty($this->activities[$id]['done_dealers'][$dealer->getId()]) && empty($this->activities[$id]['in_work_dealers'][$dealer->getId()])) {

                    $this->activities[$id]['no_work']++;

                    if ($this->load_dealers && isset($this->dealers[$dealer->getId()])) {
                        $this->activities[$id]['no_work_dealers'][$dealer->getId()] = $this->dealers[$dealer->getId()];
                    }
                }
            }
        }
    }

    protected function loadAll()
    {
        $query = Doctrine_Query::create()
            ->select('a.id, m.dealer_id, count(m.id) as models')
            ->from('Activity a')
            ->innerJoin('a.AgreementModels m')
            ->whereIn('a.id', $this->getLoadedActivityIds())
            ->groupBy('a.id, m.dealer_id')
            ->having('models > 0');

        $show = true;

        $user_dealers_list = $this->getUserDealersList();
        if (!empty($user_dealers_list)) {
            $query->andWhereIn('m.dealer_id', $user_dealers_list);
        }

        if ($this->_user->isRegionalManager() && empty($user_dealers_list)) {
            $show = false;
        }

        if ($show) {
            $result = $query->execute();
            foreach ($result as $row) {
                foreach ($row->getAgreementModels() as $model)
                    $this->activities[$row->getId()]['all'][$model->getDealerId()] = $model->models;
            }
        }

        if ($this->load_dealers)
            $this->loadDealers();
    }

    protected function loadDealers()
    {
        if ($this->dealers !== null)
            return;

        $user_dealers_list = array();
        if (!is_null($this->_user) && $this->_user->isRegionalManager()) {
            $user_dealers_list = $this->getUserDealersList();
        }

        foreach (DealerTable::getVwDealersQuery()->execute() as $dealer) {
            if ($this->_user->isRegionalManager() && empty($user_dealers_list)) {
                continue;
            }

            if (!empty($user_dealers_list)) {
                if (in_array($dealer->getId(), $user_dealers_list)) {
                    $this->dealers[$dealer->getId()] = $dealer;
                }
            } else {
                $this->dealers[$dealer->getId()] = $dealer;
            }
        }
    }

    protected function getLoadedActivityIds()
    {
        return array_keys($this->activities);
    }

    private function getUserDealersList() {
        $user_dealers_list = array();

        if (!is_null($this->_user)) {
            $userDealers = $this->_user->hasDealersListFromNaturalPerson();

            foreach ($userDealers as $k => $i) {
                $user_dealers_list[] = $k;
            }
        }

        return $user_dealers_list;
    }
}
