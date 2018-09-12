<?php

/**
 * Description of AgreementActivityModelsStatisticBuilder
 *
 * @author Сергей
 */
class AgreementActivityModelsStatisticBuilder
{
    protected $dealers = array();
    /**
     * Activity
     *
     * @var Activity
     */
    protected $activity = null;
    protected $dealer_id = null;
    protected $model_work_status = null;
    protected $start_date = null;
    protected $end_date = null;

    protected $stats = array();

    function __construct(Activity $activity, $dealer_id = NULL, $model_work_status = null, $start_date = null, $end_date = null)
    {
        $this->activity = $activity;
        $this->dealer_id = $dealer_id;
        $this->model_work_status = $model_work_status;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    /**
     * Builds statistic
     *
     * @return array
     */
    function build()
    {
        $this->loadDealers();
        foreach ($this->getModels() as $model) {
            $this->addToStat($model);
        }

        $this->updateDone();

        return $this->dealers;
    }

    function getStat()
    {
        return array('dealers' => $this->dealers, 'extended' => $this->stats);
    }

    protected function loadDealers()
    {
        $this->dealers = array();
        foreach (DealerTable::getVwDealersQuery()->execute() as $dealer) {
            ///if ($this->dealer_id == NULL || $this->dealer_id === $dealer->getId())
            $this->dealers[$dealer->getId()] = array(
                'dealer' => $dealer,
                'models' => array(),
                'done' => false,
                'all' => 0,
                'accepted' => 0,
                'accepted_models' => 0,
                'sum' => 0
            );
        }
    }

    protected function addToStat(AgreementModel $model)
    {
        $dealer_id = $model->getDealerId();
        if (!isset($this->dealers[$dealer_id])) {
            return;
        }

        $calcDate = $model->getCalcDate();

        $q = D::getQuarter($calcDate);
        $year = D::getYear($calcDate);

        if (empty($this->stats[$q][$year][$dealer_id])) {
            $this->stats[$q][$year][$dealer_id] = array(
                'dealer' => $this->dealers[$dealer_id]['dealer'],
                'models' => array(),
                'done' => false,
                'all' => 0,
                'accepted' => 0,
                'accepted_models' => 0,
                'sum' => 0
            );
        }

        $this->stats[$q][$year][$dealer_id]['all']++;
        $this->stats[$q][$year][$dealer_id]['models'][] = $model;
        if ($model->getStatus() == 'accepted' && $model->getReport() && $model->getReport()->getStatus() == 'accepted') {
            $this->stats[$q][$year][$dealer_id]['accepted']++;
            $this->stats[$q][$year][$dealer_id]['sum'] += $model->getCost();
        } elseif ($model->getStatus() == 'accepted') {
            $this->stats[$q][$year][$dealer_id]['accepted_models']++;
        }

        //$this->stats[D::getQuarter($calcDate)][D::getYear($calcDate)][$dealer_id] = $this->dealers[$dealer_id];
    }

    public function getExtendedStats() {
        return $this->stats;
    }

    protected function updateDone()
    {
        $accepted = AcceptedDealerActivityTable::getInstance()
            ->createQuery()
            ->where('activity_id=?', $this->activity->getId());

        if ($this->dealer_id != NULL) {
            $accepted = $accepted->andWhere('dealer_id=?', $this->dealer_id);
        }

        $accepted = $accepted->execute();

        foreach ($accepted as $a) {
            /*if (isset($this->dealers[$a->getDealerId()]))
                $this->dealers[$a->getDealerId()]['done'] = true;*/
            foreach($this->stats as $q => $data) {
                foreach($data as $year => $dealers) {
                    if (isset($dealers[$a->getDealerId()])) {
                        $this->stats[$q][$year][$a->getDealerId()]['done'] = true;
                    }
                }
            }
        }

        foreach ($this->stats as $q => $data) {
            foreach($data as $year => $dealers) {
                foreach ($dealers as $id => $item) {
                    $dealer = $item['dealer'];

                    $totalModels = count($item['models']);
                    $it = 0;
                    foreach ($item['models'] as $model) {
                        if ($model->getStatus() == 'accepted' && $model->getReport() && $model->getReport()->getStatus() == 'accepted')
                            $it++;
                    }

                    if ($totalModels != 0 && $it == $totalModels)
                        $this->stats[$q][$year][$dealer->getId()]['done'] = true;
                }
            }
        }
    }

    /**
     * Returns activity
     *
     * @return Activity
     */
    function getActivity()
    {
        return $this->activity;
    }

    protected function getModels()
    {
        $query = AgreementModelTable::getInstance()
            ->createQuery('m')
            ->innerJoin('m.Activity a WITH a.id=?', $this->activity->getId())
            ->innerJoin('m.ModelType mt')
            ->leftJoin('m.Discussion d')
            ->leftJoin('m.Report r');

        if(!empty($this->start_date)) {
            $query->andWhere('m.created_at >= ?', D::toDb($this->start_date));
        }

        if(!empty($this->end_date)) {
            $query->andWhere('m.created_at <= ?', D::toDb($this->end_date));
        }

        if($this->dealer_id) {
            $query->andWhere('m.dealer_id = ?', $this->dealer_id->getId());
        }

        return $query->execute();
    }
}
