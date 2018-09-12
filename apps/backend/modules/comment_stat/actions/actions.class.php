<?php

/**
 * comment_stat actions.
 *
 * @package    Servicepool2.0
 * @subpackage comment_stat
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class comment_statActions extends sfActions
{
    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    const STATUS_COMMENTED = 'commented';
    const STATUS_COMMENTED_BY_SPECIALIST = 'commented_by_specialist';
    const STATUS_SENDED = 'sended';
    const STATUS_COMPLETED = 'completed';

    const AGREEMENT_MODEL = 'agreement_model';
    const ACTION_TYPE_DELETE = 'delete';

    const MODEL_STATUS_DELETED = 'deleted';
    const MODEL_STATUS_EXISTS = 'exists';

    function executeIndex(sfWebRequest $request)
    {
        $this->getItems();
    }

    function executeShow(sfWebRequest $request)
    {
        $start_date = $request->getParameter('start_date');
        $end_date = $request->getParameter('end_date');
        $makeChanges = $request->getParameter('make_changes');

        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->make_changes = $makeChanges;

        if (preg_match('#^[0-9]{2}\.[0-9]{2}\.[0-9]{2}$#', $start_date)) {
            $start_date = D::fromRus($start_date);
        } else {
            $start_date = false;
        }

        if (preg_match('#^[0-9]{2}\.[0-9]{2}\.[0-9]{2}$#', $end_date)) {
            $end_date = D::fromRus($end_date);
            $end_date = D::fromRus($this->Calc_due_date_orig($end_date, 'day', 1, 'd.m.y'));
        } else {
            $end_date = false;
        }

        $query = LogEntryTable::getInstance()
            ->createQuery('l')
            ->select('id, object_id, action')
            ->whereIn('action', array('add', 'edit', 'accepted', 'declined', 'sent_to_specialist', 'accepted_by_specialist', 'declined_by_specialist'))
            ->andWhere('private_user_id=?', 0)
            ->andWhere('object_type=?', 'agreement_model');
            //->groupBy('action');

        if ($start_date) {
            $query->andWhere('created_at>=?', D::toDb($start_date, true));
        }

        if ($end_date) {
            $query->andWhere('created_at<=?', D::toDb($end_date, true));
        }

        $this->result_commented = 0;
        $this->result_commented_by_specialist = 0;

        $actions_list = array('add', 'edit', 'accepted', 'declined', 'sent_to_specialist');
        $periodItem = $this->addModelPeriod($start_date, $end_date);
        if (isset($makeChanges)) {
            $result = $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

            $models = array();
            foreach ($result as $r) {
                $models[] = $r['object_id'];

                if (in_array($r['action'], $actions_list)) {
                    $this->addModel($periodItem, $r['object_id'], AgreementModelsPeriods::STATUS_COMMENTED);
                } else {
                    $this->addModel($periodItem, $r['object_id'], AgreementModelsPeriods::STATUS_COMMENTED_BY_SPECIALIST);
                }
            }

            $this->result = AgreementModelTable::getInstance()->createQuery()->whereIn('id', $models)->andWhere('no_model_changes = ?', 1)->count();
        } else {
            $result = $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
            foreach ($result as $item) {
                if (in_array($item['action'], $actions_list)) {
                    $this->addModel($periodItem, $item['object_id'], AgreementModelsPeriods::STATUS_COMMENTED);
                    $this->result_commented++;
                } else {
                    $this->addModel($periodItem, $item['object_id'], AgreementModelsPeriods::STATUS_COMMENTED_BY_SPECIALIST);
                    $this->result_commented_by_specialist++;
                }
            }
        }

        $query = AgreementModelTable::getInstance()
            ->createQuery('m')
            ->innerJoin('m.LogEntry log')
            ->leftJoin('m.Discussion d')
            ->leftJoin('m.Report r');

        $query->andWhereIn('log.object_type', array('agreement_model'))
            ->andWhereIn('log.action', array('add'));

        //Начальная дата
        if ($start_date) {
            $query->andWhere('log.created_at >= ?', D::toDb($start_date));
        }

        //Конечная дата
        if ($end_date) {
            $query->andWhere('log.created_at <= ?', D::toDb($end_date));
        }

        if (isset($makeChanges)) {
            $query->andWhere('m.no_model_changes = ?', 1);
        }

        $result = array('total' => 0, 'withReport' => 0);

        $this->models = $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
        foreach ($this->models as $model) {
            $this->addModel($periodItem, $model['id'], AgreementModelsPeriods::STATUS_SENDED);
        }

        $result['total'] = count($this->models);

        $query = AgreementModelTable::getInstance()
            ->createQuery('m')
            ->innerJoin('m.LogEntry log')
            ->select('m.*, r.status')
            ->innerJoin('m.Report r');

        $query->andWhereIn('log.object_type', array('agreement_report'))
            ->andWhereIn('log.action', array('accepted'));

        if ($start_date) {
            $query->andWhere('log.created_at >= ?', D::toDb($start_date));
        }

        if ($end_date) {
            $query->andWhere('log.created_at <= ?', D::toDb($end_date));
        }

        if (isset($makeChanges)) {
            $query->andWhere('m.no_model_changes = ?', 1);
        }

        $this->reports = $query->execute();
        foreach ($this->reports as $report) {
            $this->addModel($periodItem, $report->getId(), AgreementModelsPeriods::STATUS_COMPLETED);
        }

        $result['withReport'] = $this->reports->count();
        $this->result2 = $result;

        $this->getItems();

        $this->setTemplate('index');
    }

    function Calc_due_date_orig($date, $interval, $add, $return_date_format = 'd-m-Y')
    {
        $date = $date;
        if ($date !== -1) {
            $date = getdate($date);
            switch (strtolower($interval)) {
                case  'month'  :
                    $date['mon'] += $add;
                    break;
                case  'day'    :
                    $date['mday'] += $add;
                    break;
                default        :
                    $date['year'] += $add;
            }
            return (date($return_date_format, mktime(0, 0, 0, $date['mon'], $date['mday'], $date['year'])));
        }
        return (false);
    }

    private function getItems()
    {
        $this->items = AgreementModelsPeriodsTable::getInstance()
            ->createQuery()
            ->where('year(created_at) = ?', date('Y'))
            ->orderBy('id DESC')
            ->execute();
    }

    private function addModel($period, $modelId, $modelStatus)
    {
        if (!$period)
            return false;

        $item = new AgreementModelsPeriodsStats();
        $item->setArray(
            array(
                'model_id' => $modelId,
                'model_status' => $modelStatus,
                'model_period_id' => $period->getId()
            )
        );
        $item->save();

        return true;
    }

    private function addModelPeriod($startDate, $endDate)
    {
        $item = new AgreementModelsPeriods();
        $item->setArray(
            array(
                'period_from_date' => D::toDb($startDate),
                'period_to_date' => D::toDb($endDate)
            )
        );
        $item->save();

        return $item;
    }

    public function executeDeletedModelsCommentsStats(sfWebRequest $request)
    {
        AgreementModelsPeriodsTable::getInstance()->find($request->getParameter('id'))->delete();

        return sfView::NONE;
    }

    public function executeCompareModelsCommentStats(sfWebRequest $request)
    {
        $items = $request->getParameter('items');

        $this->comparedItems = $this->compareItems($items[0], $items[1]);
    }

    private function compareItems($item1, $item2)
    {
        $result = array();

        $this->item1 = AgreementModelsPeriodsTable::getInstance()->find($item1);
        $this->item2 = AgreementModelsPeriodsTable::getInstance()->find($item2);

        $this->statuses = array(
            AgreementModelsPeriods::STATUS_COMMENTED => "Прокомментировано макетов:",
            AgreementModelsPeriods::STATUS_COMMENTED_BY_SPECIALIST => "Прокомментировано макетов специалистом:",
            AgreementModelsPeriods::STATUS_SENDED => "Количество отправленных заявок:",
            AgreementModelsPeriods::STATUS_COMPLETED => "Количество согласованных отчетов:");

        if ($this->item1 && $this->item2) {
            foreach ($this->statuses as $key => $status) {
                $result[$key] = $this->compareItemsByStatus($key, $this->item1, $this->item2);
            }
        }

        return $result;
    }

    private function compareItemsByStatus($status, $item1, $item2)
    {
        $itemsForItem1 = $item1->getItemsListByStatus($status);
        $itemsForItem2 = $item2->getItemsListByStatus($status);

        $itemsResult = array_diff($itemsForItem1, $itemsForItem2);
        $result['left']['items'] = $this->compareAndCheckItems($itemsResult);

        $itemsResult = array_diff($itemsForItem2, $itemsForItem1);
        $result['right']['items'] = $this->compareAndCheckItems($itemsResult);

        $result['left']['stats'] = array(
            AgreementModelsPeriods::STATUS_COMMENTED => $item1->getCommentModelsCount(),
            AgreementModelsPeriods::STATUS_COMMENTED_BY_SPECIALIST => $item1->getCommentBySpecialistModelsCount(),
            AgreementModelsPeriods::STATUS_SENDED => $item1->getSendedModelsCount(),
            AgreementModelsPeriods::STATUS_COMPLETED => $item1->getCompletedModelsCount()
        );

        $result['right']['stats'] = array(
            AgreementModelsPeriods::STATUS_COMMENTED => $item2->getCommentModelsCount(),
            AgreementModelsPeriods::STATUS_COMMENTED_BY_SPECIALIST => $item2->getCommentBySpecialistModelsCount(),
            AgreementModelsPeriods::STATUS_SENDED => $item2->getSendedModelsCount(),
            AgreementModelsPeriods::STATUS_COMPLETED => $item2->getCompletedModelsCount()
        );

        return $result;
    }

    private function compareAndCheckItems($items)
    {
        $result = array();
        foreach ($items as $item) {
            if (AgreementModelTable::getInstance()->createQuery()->where('id = ?', $item)->count() > 0) {
                $result[$item][] = array('status' => self::MODEL_STATUS_EXISTS, 'model_id' => $item, 'msg' => '');
            } else {
                $logEntry = LogEntryTable::getInstance()
                    ->createQuery()
                    ->where('object_id = ?', $item)
                    ->andWhere('object_type = ? and action = ?',
                        array(
                            self::AGREEMENT_MODEL,
                            self::ACTION_TYPE_DELETE
                        )
                    )
                    ->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);
                if ($logEntry) {
                    $msg = sprintf("Макет %s (%s)б был удален %s",
                        $logEntry['title'],
                        $item,
                        $logEntry['created_at']);

                    $result[$item][] = array('status' => self::MODEL_STATUS_DELETED, 'model_id' => $item, 'msg' => $msg);
                }
            }
        }

        return $result;
    }
}
