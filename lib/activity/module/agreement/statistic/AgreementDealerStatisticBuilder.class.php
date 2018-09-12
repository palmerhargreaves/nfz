<?php

/**
 * Description of AgreementDealerStatisticBuilder
 *
 * @author Сергей
 */
class AgreementDealerStatisticBuilder
{
    protected $year;
    protected $stat = array();
    /**
     * A dealer
     *
     * @var Dealer
     */
    protected $dealer;
    private $activities = array();

    function __construct($year, Dealer $dealer)
    {
        $this->year = $year;
        $this->dealer = $dealer;
    }

    function build()
    {
        $this->stat = array();

        $this->loadActivities();
        $models = AgreementModelTable::getInstance()
            ->createQuery('m')
            ->select('m.id as mId, m.created_at as mCreatedAt, r.accept_date as rAcceptDate, r.status as rStatus, m.activity_id as mActivityId, m.cost as mCost, log.created_at as logCreatedAt')
            ->leftJoin('m.Report r')
            ->innerJoin('m.LogEntry log')
            ->where('m.dealer_id = ? and year(m.updated_at) = ?', arraY($this->dealer->getId(), $this->year))
            ->andWhere('log.object_type = ? and log.icon = ? and log.action = ?', array('agreement_report', 'clip', 'edit'))
            //->orderBy('m.id DESC')
                ->orderBy('log.id DESC')
                ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

        foreach ($models as $model) {
            $this->addModelToStat($model);
        }

        $models = AgreementModelTable::getInstance()
            ->createQuery('m')
            ->select('m.id as mId, m.created_at as mCreatedAt, r.accept_date as rAcceptDate, r.status as rStatus, m.activity_id as mActivityId, m.cost as mCost, log.created_at as logCreatedAt')
            ->leftJoin('m.Report r')
            ->innerJoin('m.LogEntry log')
            ->where
            (
                'm.dealer_id = ? and year(m.updated_at) = ? and quarter(m.updated_at) = ?',
                arraY(
                    $this->dealer->getId(),
                    $this->year + 1,
                    1
                )
            )
            ->andWhere('log.object_type = ? and log.icon = ? and log.action = ?', array('agreement_report', 'clip', 'edit'))
            //->orderBy('m.id DESC')
                ->orderBy('log.id DESC')
            ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

        foreach ($models as $model) {
            $this->addModelToStat($model);
        }

        krsort($this->stat);

        return $this->stat;
    }

    private function loadActivities() {
        $items = ActivityTable::getInstance()->createQuery()->orderBy('id ASC')->execute();

        foreach($items as $item) {
            $this->activities[$item->getId()] = $item;
        }
    }

    function getStat()
    {
        return $this->stat;
    }

    function getYear()
    {
        return $this->year;
    }

    /**
     * Returns a dealer
     *
     * @return Dealer
     */
    function getDealer()
    {
        return $this->dealer;
    }

    protected function addModelToStat($model)
    {
        if (isset($model['logCreatedAt']) && !empty($model['logCreatedAt'])) {
            $date = $model['logCreatedAt'];
            //vaR_dump($date.'--'.$model['mActivityId']);

        }
        else {
            if(isset($model['rAcceptDate']) && !empty($model['rAcceptDate'])) {
                $date = $model['rAcceptDate'];
            }
            else {
                $date = $model['mCreatedAt'];
            }
        }

        $nDate = D::calcQuarterData($date);
        $year = D::getYear($nDate);
        if ($year != $this->year) {
            return;
        }

        $quarter = D::getQuarter($nDate);
        if(!isset($this->activities[$model['mActivityId']])) {
            return;
        }

        $activity = $this->activities[$model['mActivityId']];
        if (!$activity->isActivityStatisticComplete($this->dealer, $nDate)) {
            return;
        }

        if (!isset($this->stat[$quarter])) {
            $this->stat[$quarter] = array(
                'activities' => array()
            );
        }
        if (!isset($this->stat[$quarter]['activities'][$model['mActivityId']])) {
            $this->stat[$quarter]['activities'][$model['mActivityId']] = array(
                'activity' => $activity,
                'sum' => 0,
                'models' => array()
            );
        }

        if ($model['rStatus'] == 'accepted') {
            $this->stat[$quarter]['activities'][$model['mActivityId']]['sum'] += $model['mCost'];
        }

        $this->stat[$quarter]['activities'][$model['mActivityId']]['models'][] = AgreementModelTable::getInstance()->find($model['mId']);
    }

    protected function addModelToStatExt(AgreementModel $model)
    {
        $report = $model->getReport();
        if ($model->getReportCssStatus() != 'ok') {
            $date = $model->getCreatedAt();
        } else {
            $date = $report->getAcceptDate();
            $year = date('Y', $date);

            $entry = LogEntryTable::getInstance()
                ->createQuery()
                ->where('object_id = ?', array($model->getId()))
                ->andWhere('object_type = ? and icon = ? and action = ?', array('agreement_report', 'clip', 'edit'))
                ->orderBy('id DESC')
                ->limit(1)
                ->fetchOne();

            if ($entry) {
                $date = $entry->getCreatedAt();
            }
        }

        $nDate = D::calcQuarterData($date);

        $year = D::getYear($nDate);
        if ($year != $this->year) {
            return;
        }

        $quarter = D::getQuarter($nDate);

        if (!$model->getActivity()->isActivityStatisticComplete($this->dealer, $nDate))
            return;

        //$quarter = D::getQuarter($model->created_at);

        if (!isset($this->stat[$quarter]['activities'][$model->getActivityId()])) {
            $this->stat[$quarter]['activities'][$model->getActivityId()] = array(
                'activity' => $model->getActivity(),
                'sum' => 0,
                'models' => array()
            );
        }

        if ($model->getReportCssStatus() == 'ok')
            $this->stat[$quarter]['activities'][$model->getActivityId()]['sum'] += $model->getCost();


        $this->stat[$quarter]['activities'][$model->getActivityId()]['models'][] = $model;
    }
}
