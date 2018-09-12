<?php

/**
 * Helps to build statistic by activity agreement
 *
 * @author Сергей
 */
class AgreementActivityStatisticBuilder
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

    function __construct(Activity $activity = null, $finished = null, $load_dealers = false)
    {
        $this->activity = $activity;
        if ($finished === null)
            $this->finished = $activity ? $activity->getFinished() : null;
        else
            $this->finished = $finished;

        $this->load_dealers = $load_dealers;
        $this->year_filter = date('Y');
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

            /*$query->andWhere(
                '(a.custom_date<>? and a.custom_date is not null) or a.start_date between ? and ? or a.end_date between ? and ?'
                . ' or ? between a.start_date and a.end_date or ? between a.start_date and a.end_date',
                array('', $start_date, $end_date, $start_date, $end_date, $start_date, $end_date)
            );*/

            $query->andWhere(
                'a.start_date between ? and ? or a.end_date between ? and ?'
                . ' or ? between a.start_date and a.end_date or ? between a.start_date and a.end_date',
                array( $start_date, $end_date, $start_date, $end_date, $start_date, $end_date)
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
        /*$query = AcceptedDealerActivityTable::getInstance()
                ->createQuery();

        if($this->year_filter)
          $query->andWhere('year(accept_date)=?', $this->year_filter);
        if($this->quarter_filter)
          $query->andWhere('quarter(accept_date)=?', $this->quarter_filter);*/

        $query = Doctrine_Query::create()
            ->select('am.dealer_id as amDealerId, am.id as mId, r.accept_date as rAcceptDate, am.activity_id a mActivityId')
            ->from('AgreementModel am')
            ->innerJoin('am.Report r')
            ->innerJoin('am.Activity a')
            //->where('am.status = ? and r.status = ? and a.is_own = ?', array('accepted', 'accepted', false))
            ->where('am.status = ? and r.status = ?', array('accepted', 'accepted'))
            ->andWhere('am.model_type_id != ?', Activity::CONCEPT_MODEL_TYPE_ID)
            //->andWhere('am.activity_id != ?', array(13))
            ->orderBy('am.activity_id DESC');

        /*$query = AgreementModelTable::getInstance()
                ->createQuery('m')
                  ->leftJoin('m.Report r')
                  ->andWhere('m.status = ? and r.status = ?', array('accepted', 'accepted'));*/

        if ($this->year_filter) {
            $query->andWhere('year(am.created_at)=?', $this->year_filter);
        }
        /*if($this->quarter_filter)
          $query->andWhere('quarter(r.accept_date)=?', $this->quarter_filter);*/

        $result = $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
        foreach ($result as $row) {
            //$activity = $row->getActivity();

            $nDate = date('Y-m-d', D::calcQuarterData($row['rAcceptDate']));
            $calcDate = utils::getModelDateFromLogEntryWithYear($row['mId'], $this->year_filter);
            if(!is_null($calcDate)) {
                $nDate = $calcDate;
            }

            $q = D::getQuarter($nDate);
            if(!isset($this->activities[$row['mActivityId']]) || !$this->activities[$row['mActivityId']]['activity']) {
                continue;
            }

            $activity = $this->activities[$row['mActivityId']]['activity'];
            if (!$activity->isActivityStatisticComplete(
                    $this->load_dealers && isset($this->dealers[$row['amDealerId']]) ? $this->dealers[$row['amDealerId']] : $row['amDealerId'],
                    $nDate
                )
            ) {
                continue;
            }



            if (!$this->quarter_filter && array_key_exists($activity->getId(), $this->activities)) {
                $this->activities[$activity->getId()]['done']++;

                if ($this->load_dealers && isset($this->dealers[$row['amDealerId']])) {
                    $this->activities[$activity->getId()]['done_dealers'][$row['amDealerId']] = $this->dealers[$row['amDealerId']];
                }
            } else if (($q == $this->quarter_filter) && array_key_exists($activity->getId(), $this->activities)) {
                $this->activities[$activity->getId()]['done']++;

                if ($this->load_dealers && isset($this->dealers[$row['mDealerId']])) {
                    $this->activities[$activity->getId()]['done_dealers'][$row['amDealerId']] = $this->dealers[$row['amDealerId']];
                }
            }
        }

        $this->loadDoneDealersCount();
    }

    protected function loadDoneDealersCount()
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

        $result = Doctrine_Query::create()
            ->select('a.id, m.dealer_id, count(m.id) as models')
            ->from('Activity a')
            ->innerJoin('a.AgreementModels m')
            ->leftJoin('m.Report r')
            ->whereIn('a.id', $this->getLoadedActivityIds())
            ->andWhere('m.status = ? and r.status = ?', array('accepted', 'accepted'))
            ->groupBy('a.id, m.dealer_id')
            ->execute();

        foreach ($result as $row) {
            foreach ($row->getAgreementModels() as $model) {
                if (!isset($this->activities[$row->getId()]['all'][$model->getDealerId()])) {
                    continue;
                }

                if (utils::eqModelDateFromLogEntryWithYear($model->getId(), $this->year_filter)) {
                    $this->activities[$row->getId()]['done_dealers_count']++;
                }
            }
        }
    }

    protected function loadInWork()
    {
        /*if ($this->year_filter || $this->quarter_filter) {
            if ($this->year_filter && !$this->quarter_filter) {
                if ($this->year_filter != date('Y'))
                    return;
            } else {
                if ($this->year_filter != date('Y') || $this->quarter_filter != D::getQuarter(time()))
                    return;
            }
        }*/

        $result = Doctrine_Query::create()
            ->select('a.id, m.dealer_id, count(m.id) as models')
            ->from('Activity a')
            ->innerJoin('a.AgreementModels m')
            //->leftJoin('m.Report r')
            ->whereIn('a.id', $this->getLoadedActivityIds())
            //->andWhere('m.status <> ? or r.status <> ?', array('accepted', 'accepted'))*/
            ->groupBy('a.id, m.dealer_id')
            ->execute();

        foreach ($result as $row) {
            foreach ($row->getAgreementModels() as $model) {
                if ($model->getStatus() != 'accepted' || $model->getReport()->getStatus() != 'accepted') {
                    if (!isset($this->activities[$row->getId()]['all'][$model->getDealerId()])) {
                        continue;
                    }

                    $this->activities[$row->getId()]['in_work']++;
                    if ($this->load_dealers && isset($this->dealers[$model->getDealerId()])) {
                        $this->activities[$row->getId()]['in_work_dealers'][$model->getDealerId()] = $this->dealers[$model->getDealerId()];
                    }
                }
            }
        }
    }

    protected function loadNoWork()
    {
        $count_dealers = DealerTable::getVwDealersQuery()->count();

        foreach ($this->activities as $id => $activity) {
            $this->activities[$id]['no_work'] = $count_dealers;
            if ($this->load_dealers)
                $this->activities[$id]['no_work_dealers'] = $this->dealers;
        }

        $result = Doctrine_Query::create()
            ->select('a.id, count(distinct du.dealer_id) as dealers')
            ->from('Activity a')
            ->innerJoin('a.UserViews uv')
            ->innerJoin('uv.User u')
            ->innerJoin('u.DealerUsers du')
            ->whereIn('a.id', $this->getLoadedActivityIds())
            ->groupBy('a.id')
            ->execute();

        foreach ($result as $row)
            $this->activities[$row->getId()]['no_work'] -= $row->dealers;

        if ($this->load_dealers) {
            $result = Doctrine_Query::create()
                ->select('a.id, uv.id, u.id, du.*')
                ->distinct()
                ->from('Activity a')
                ->innerJoin('a.UserViews uv')
                ->innerJoin('uv.User u')
                ->innerJoin('u.DealerUsers du')
                ->whereIn('a.id', $this->getLoadedActivityIds())
                ->execute();

            foreach ($result as $activity) {
                foreach ($activity->getUserViews() as $view) {
                    foreach ($view->getUser()->getDealerUsers() as $dealer_user) {
                        if (isset($this->activities[$activity->getId()]) && isset($this->activities[$activity->getId()]['no_work_dealers'][$dealer_user->getDealerId()])) {
                            unset($this->activities[$activity->getId()]['no_work_dealers'][$dealer_user->getDealerId()]);
                        }
                    }
                }
            }
        }
    }

    protected function loadAll()
    {
        $result = Doctrine_Query::create()
            ->select('a.id, m.dealer_id, count(m.id) as models')
            ->from('Activity a')
            ->innerJoin('a.AgreementModels m')
            ->whereIn('a.id', $this->getLoadedActivityIds())
            ->groupBy('a.id, m.dealer_id')
            ->having('models > 0')
            ->execute();

        foreach ($result as $row) {
            foreach ($row->getAgreementModels() as $model)
                $this->activities[$row->getId()]['all'][$model->getDealerId()] = $model->models;
        }

        if ($this->load_dealers)
            $this->loadDealers();
    }

    protected function loadDealers()
    {
        if ($this->dealers !== null)
            return;

        $this->dealers = array();

        foreach (DealerTable::getVwDealersQuery()->execute() as $dealer) {
            $this->dealers[$dealer->getId()] = $dealer;
        }
    }

    protected function getLoadedActivityIds()
    {
        return array_keys($this->activities);
    }
}
