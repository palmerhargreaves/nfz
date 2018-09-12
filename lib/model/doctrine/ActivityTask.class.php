<?php

/**
 * ActivityTask
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    Servicepool2.0
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class ActivityTask extends BaseActivityTask
{
    function wasDone(Dealer $dealer, Activity $activity, $check_by_quarter = null)
    {
        $year = D::getYear(D::calcQuarterData(date('Y-m-d')));

        if($activity->getFinished()) {
            return $this->checkTaskResult($this->getId(), $dealer->getId());
        }

        $currentQuarter = D::getQuarter(D::calcQuarterData(date('d-m-Y')));
        if (!is_null($check_by_quarter)) {
            $currentQuarter = $check_by_quarter;
        }

        $query = AgreementModelTable::getInstance()
            ->createQuery('am')
            ->select('am.id modelId')
            ->leftJoin('am.Report r')
            ->where('activity_id = ? and dealer_id = ?',
                array
                (
                    $activity->getId(),
                    $dealer->getId()
                )
            )
            ->andWhere('am.status = ? and r.status = ?',
                array
                (
                    'accepted',
                    'accepted'
                )
            );

        if ($this->getIsConceptComplete()) {
            $query->andWhere('am.model_type_id = ?', Activity::CONCEPT_MODEL_TYPE_ID);
        } else {
            $query->andWhere('am.model_type_id != ?', Activity::CONCEPT_MODEL_TYPE_ID);
        }

        $items = array();
        $models = $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

        foreach ($models as $model) {
            $data = LogEntryTable::getInstance()
                ->createQuery()
                ->select('created_at, object_id')
                ->where('object_id = ?', $model['modelId'])
                ->andWhere('private_user_id = ?', 0)
                ->andWhere('icon = ?', 'clip')
                ->orderBy('id DESC')
                ->limit(1)
                ->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);

            if ($data) {
                $modelYear = D::getYear(date('Y-m-d', D::calcQuarterData($data['created_at'])));
                $quarter = D::getQuarter(D::calcQuarterData($data['created_at']));

                if($currentQuarter != $quarter) {
                    continue;
                }

                $items[$modelYear][] = $quarter;
            }
        }

        if (count($items) == 0) {
            return false;
        }

        $activityStartYear = D::getYear($activity->getStartDate());
        $activityEndYear = D::getYear($activity->getEndDate());

        foreach ($items as $keyYear => $qList) {
            if ($activityStartYear != $activityEndYear && $activityEndYear >= $year && $activity->getAllowExtendedStatistic()) {
                return $this->checkTaskResultByYearAndQ($items, $year, $currentQuarter);
            } else {
                if($activityEndYear != $activityStartYear) {
                    if(count($items) > 0 && count($items[$activityStartYear]) > 0) {
                        return $this->checkTaskResult($this->getId(), $dealer->getId());
                    }

                    return $this->checkTaskResultByYearAndQ($items, $activityEndYear, $currentQuarter);
                }
                else {
                    return $this->checkTaskResultByYearAndQ($items, $year, $currentQuarter);
                }
            }
        }

        return $this->checkTaskResult($this->getId(), $dealer->getId());
    }

    private function checkTaskResult($taskId, $dealerId)
    {
        $result = ActivityTaskResultTable::getInstance()
            ->createQuery()
            ->where('task_id=? and dealer_id=?', array($taskId, $dealerId))
            ->fetchOne();

        return $result ? $result->getDone() : false;
    }

    private function checkTaskResultByYearAndQ($items, $year, $currentQuarter)
    {
        //Проверка на наличие заявок по году и списку кварталов
        if (isset($items[$year]) && !in_array($currentQuarter, $items[$year])) {
            return true;
        }

        //Проверка на выполненные заявки в квартале
        foreach ($items as $keyYear => $qList) {
            if ($keyYear == $year) {
                return in_array($currentQuarter, $qList);
            }
        }

        return false;
    }

    function updateReportStatus(Dealer $dealer, $status)
    {
        $result = ActivityTaskResultTable::getInstance()
            ->createQuery()
            ->where('task_id=? and dealer_id=?', array($this->getId(), $dealer->getId()))
            ->fetchOne();

        /*if(!empty($result)) {
            $result->setDone($status);
            $result->save();
        }*/

    }

}