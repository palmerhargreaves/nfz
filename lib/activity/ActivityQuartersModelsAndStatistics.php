<?php

/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 16.04.2016
 * Time: 12:05
 */
class ActivityQuartersModelsAndStatistics
{
    private $_quarters_years = array();
    private $_cal_quarter = 0;
    private $_current_quarter = 0;

    private $_dealer = null;
    private $_activity = null;

    const CONCEPT = 1;
    const MODEL = 2;
    const ALL_TYPES = 3;

    /**
     * ActivityQuartersModelsAndStatistics constructor.
     * @param User $user
     * @param Activity $activity
     */
    public function __construct(User $user, Activity $activity)
    {
        $userDealer = $user->getDealerUsers()->getFirst();
        if ($userDealer) {
            $dealer = DealerTable::getInstance()->createQuery('d')->where('id = ?', $userDealer->getDealerId())->fetchOne();
        }

        if (!$dealer) {
            return array();
        }

        $this->_dealer = $dealer;
        $this->_activity = $activity;

        $this->_current_quarter = D::getQuarter(D::calcQuarterData(time()));
    }

    /**
     * Load data by dealer and activity
     */
    public function getData() {
        $quarters = $this->getDataModelsList(self::ALL_TYPES, true);
        $statistics_q_status = array();

        //Get list of models / concepts
        $concepts_result = $this->getModelsTotalCompleted($this->getDataModelsList(self::CONCEPT));
        $models_result = $this->getModelsTotalCompleted($this->getDataModelsList(self::MODEL));

        //Collecting data to check statistic complete
        foreach ($quarters as $qKey => $q) {
            if ($this->_activity->getActivityField()->count() > 0) {
                $statistics_q_status[$qKey] = $this->_activity->checkForSimpleStatisticComplete($this->_dealer->getId(), false, $qKey);
            } elseif ($this->_activity->getAllowExtendedStatistic()) {
                $statistics_q_status[$qKey] = $this->_activity->checkForStatisticComplete($this->_dealer->getId(), $qKey);
            }
        }

        //Fill result from list of quarters by checking if complete concepts and models
        $result = array();
        foreach ($quarters as $qKey => $q) {
            //Check for activity statistic fill and exists
            if (isset($statistics_q_status[$qKey]) && (isset($concepts_result[$qKey]) && $concepts_result[$qKey]['data']['completed']) && isset($models_result[$qKey]) && $models_result[$qKey]['data']['completed'] && $statistics_q_status[$qKey]) {
                $result[$qKey] = $concepts_result[$qKey];
            }
            //Check for activity concepts must be completed
            else if ($this->_activity->getIsConceptComplete()) {
                if ((isset($concepts_result[$qKey]) && $concepts_result[$qKey]['data']['completed']) && (isset($models_result[$qKey]) && $models_result[$qKey]['data']['completed'])) {
                    $result[$qKey] = $concepts_result[$qKey];
                }
            }
            //Check for complete concepts or models
            else if ((isset($concepts_result[$qKey]) && $concepts_result[$qKey]['data']['completed']) || (isset($models_result[$qKey]) && $models_result[$qKey]['data']['completed'])) {
                if(isset($concepts_result[$qKey]) && $concepts_result[$qKey]['data']['completed']) {
                    $result[$qKey] = $concepts_result[$qKey];
                }

                if(isset($models_result[$qKey]) && $models_result[$qKey]['data']['completed']) {
                    $result[$qKey] = $models_result[$qKey];
                }
            }

            //Fill empty data for concept if not any rules are accepted
            if (!isset($result[$qKey])) {
                if (isset($concepts_result[$qKey])) {
                    $result[$qKey] = $concepts_result[$qKey];
                    $result[$qKey]['data']['completed'] = false;
                } //Fill empty data for models
                else if (isset($models_result[$qKey]) || isset($concepts_result[$qKey])) {

                    $result[$qKey] = $models_result[$qKey];
                    $result[$qKey]['data']['completed'] = false;
                }
            }
        }

        uasort($result, function($a, $b) {
            return $a['data']['year'] > $b['data']['year'] ? 1 : -1;
        });

        $years_list = array();
        foreach ($result as $q => $q_data) {
            $years_list[$q_data['data']['year']][$q] = $result[$q];
        }

        $result = array();
        foreach ($years_list as $year => $year_data) {
            ksort($years_list[$year]);

            foreach ($years_list[$year] as $q => $q_data) {
                $result[$year][$q] = $q_data;
            }
        }

        return $result;
    }

    private function getModelsTotalCompleted($models)
    {
        $result = array();
        foreach($models as $model) {
            if ($model->getStatus() == "accepted" && $model->getReport() && $model->getReport()->getStatus() == "accepted") {
                $acceptedDate = $model->getCalcDate();

                $q = D::getQuarter($acceptedDate);
                if (!isset($result[$q]['data'])) {
                    $result[$q]['data'] = array
                    (
                        'year' => D::getYear($acceptedDate),
                        'total_completed' => 1,
                        'completed' => true,
                    );
                } else {
                    $result[$q]['data']['total_completed']++;
                    $result[$q]['data']['completed'] = true;
                }
            } else {
                $calc_model_date = D::calcQuarterData($model->getCreatedAt());

                $q = D::getQuarter($calc_model_date);
                if (!isset($result[$q]['data']['total_completed'])) {
                    $result[$q]['data'] = array
                    (
                        'year' => D::getYear($calc_model_date),
                        'total_completed' => 0,
                        'completed' => false,
                        'total_necessarily_models_complete' => 0
                    );
                }
            }
        }

        return $result;
    }

    private function getDataModelsList($model_type, $calc_q = false) {
        if (!$this->_dealer) {
            return array();
        }

        $query = AgreementModelTable::getInstance()
            ->createQuery()
            ->where('activity_id = ? and dealer_id = ?', array($this->_activity->getId(), $this->_dealer->getId()));

        if ($model_type == self::CONCEPT) {
            $query->andWhere('model_type_id = ?', Activity::CONCEPT_MODEL_TYPE_ID);
        } else if($model_type == self::MODEL) {
            $query->andWhere('model_type_id != ?', Activity::CONCEPT_MODEL_TYPE_ID);
        }

        if ($calc_q) {
            $items = $query->execute();

            $qs = array();
            foreach($items as $item) {
                if ($item->getStatus() == "accepted" && $item->getReport() && $item->getReport()->getStatus() == "accepted") {
                    $date = $item->getCalcDate();
                } else {
                    $date = D::calcQuarterData($item->getCreatedAt());
                }

                $q = D::getQuarter($date);
                $qs[$q] = array('quarter' => $q, 'year' => D::getYear($date));
            }

            return $qs;
        }

        return $query->execute();
    }

}
