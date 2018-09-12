<?php

/**
 * Description of RealBudgetCalculator
 *
 * @author Сергей
 */
class RealBudgetCalculator
{
    /**
     * Dealer
     *
     * @var Dealer
     */
    protected $dealer;
    /**
     * Year
     *
     * @var int
     */
    protected $year;
    protected $real_budget = array();
    protected $plan_budget = array();

    const LAST_QUARTER = 4;
    const FIRST_QUARTER = 1;
    const MIN_DAYS = 20;

    function __construct(Dealer $dealer, $year)
    {
        $this->dealer = $dealer;
        $this->year = $year;
    }

    function calculate()
    {
        $this->real_budget = array(1 => 0, 2 => 0, 3 => 0, 4 => 0);
        $this->plan_budget = $this->getPlanBudget();

        /*$query = RealBudgetTable::getInstance()
                 ->createQuery()
                 ->where('dealer_id=? and year=?', array($this->dealer->getId(), $this->year))
                 ->orderBy('id');*/
        $activities = array();
        $query = AgreementModelTable::getInstance()
            ->createQuery('am')
            ->select('am.id as mId, am.cost, r.accept_date as rAcceptDate, am.activity_id as mActivityId')
            ->leftJoin('am.Report r')
            ->where('am.dealer_id = ? and year(am.updated_at) = ? and am.status = ? and r.status = ?',
                array
                (
                    $this->dealer->getId(),
                    $this->year,
                    'accepted',
                    'accepted'
                )
            );

        $items = $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

        foreach ($items as $real_row) {
            $date = $real_row['rAcceptDate'];

            $entry = LogEntryTable::getInstance()
                ->createQuery()
                ->select('created_at')
                ->where('object_id = ?', array($real_row['mId']))
                ->andWhere('object_type = ? and icon = ? and action = ?', array('agreement_report', 'clip', 'edit'))
                ->orderBy('id DESC')
                    ->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);

            if ($entry) {
                $date = $entry['created_at'];
            }

            $nDate = D::calcQuarterData($date);

            $year = D::getYear($nDate);
            $q = D::getQuarter($nDate);
            if ($this->year != $year) {
                continue;
            }

            if(!array_key_exists($real_row['mActivityId'], $activities)) {
                $activities[$real_row['mActivityId']] = ActivityTable::getInstance()->find($real_row['mActivityId']);
            }

            $activity = $activities[$real_row['mActivityId']];
            if (!$activity->isActivityStatisticComplete($this->dealer, $nDate)) {
                continue;
            }

            $realSum = $real_row['cost'];
            $this->addToRealBudget($q, $realSum);
        }

        $query = AgreementModelTable::getInstance()
            ->createQuery('am')
            ->select('am.id as mId, am.activity_id as mActivityId, r.accept_date as rAcceptDate, am.cost')
            ->leftJoin('am.Report r')
            ->where('am.dealer_id = ? and year(am.updated_at) = ? and quarter(am.updated_at) = ? and am.status = ? and r.status = ?',
                array
                (
                    $this->dealer->getId(),
                    ($this->year + 1),
                    self::FIRST_QUARTER,
                    'accepted',
                    'accepted'
                )
            );


        foreach ($query->execute(array(), Doctrine_Core::HYDRATE_ARRAY) as $real_row) {
            $date = $real_row['rAcceptDate'];

            $entry = LogEntryTable::getInstance()
                ->createQuery()
                ->select('created_at')
                ->where('object_id = ?', array($real_row['mId']))
                ->andWhere('object_type = ? and icon = ? and action = ?', array('agreement_report', 'clip', 'edit'))
                ->orderBy('id DESC')
                ->limit(1)
                    ->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);

            if ($entry) {
                $date = $entry['created_at'];
            }

            $nDate = D::calcQuarterData($date);
            $q = D::getQuarter($nDate);

            if(!array_key_exists($real_row['mActivityId'], $activities)) {
                $activities[$real_row['mActivityId']] = ActivityTable::getInstance()->find($real_row['mActivityId']);
            }

            $activity = $activities[$real_row['mActivityId']];
            if (!$activity->isActivityStatisticComplete($this->dealer, $nDate))
                continue;

            if (date('n', strtotime($real_row['rAcceptDate'])) == self::FIRST_QUARTER &&
                (int)date('j', strtotime($real_row['rAcceptDate'])) <= self::MIN_DAYS &&
                $q == self::LAST_QUARTER
            ) {

                $realSum = $real_row['cost'];
                $this->addToRealBudget(self::LAST_QUARTER, $realSum, $real_row);
            }
        }

        return $this->real_budget;
    }

    protected function addToRealBudget($quarter, $sum, $row = null)
    {
        $new_sum = $this->real_budget[$quarter] + $sum;

        if ($quarter < 4 && $new_sum > $this->plan_budget[$quarter] && $this->plan_budget[$quarter] != 0) {
            $this->addToRealBudget($quarter + 1, $new_sum - $this->plan_budget[$quarter]);
            $new_sum = $this->plan_budget[$quarter];
        }

        $this->real_budget[$quarter] = $new_sum;
    }

    protected function getPlanBudget()
    {
        $budget = array(1 => 0, 2 => 0, 3 => 0, 4 => 0);

        $query = BudgetTable::getInstance()
            ->createQuery()
            ->select('quarter, plan')
            ->where('dealer_id=? and year=?', array($this->dealer->getId(), $this->year));

        foreach ($query->execute(array(), Doctrine_Core::HYDRATE_ARRAY) as $budget_row)
            $budget[$budget_row['quarter']] = $budget_row['plan'];

        return $budget;
    }
}
