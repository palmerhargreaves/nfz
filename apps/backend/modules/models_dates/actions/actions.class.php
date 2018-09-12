<?php

/**
 *  models_date actions.
 *
 * @package    Servicepool2.0
 * @subpackage comment_stat
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class models_datesActions extends sfActions
{
    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */

    const MOVE_TYPE_ACTIVITIES = 'activity';
    const MOVE_TYPE_DEALERS = 'dealer';

    function executeIndex(sfWebRequest $request)
    {
    }

    function executeFindModel(sfWebRequest $request)
    {
        $modelsId = explode(',', $request->getParameter('model_id'));
        $this->moveType = $request->getParameter('sbMoveType');

        if (count($modelsId) == 0) {
            $this->models = null;
        } else {
            $this->models = AgreementModelTable::getInstance()
                ->createQuery('m')
                ->select('*')
                ->leftJoin('m.Report r')
                ->leftJoin('m.ModelType mt')
                //->where('r.status = ?', array('accepted'))
                ->whereIn('m.id', $modelsId)
                ->execute();

            $notInActivities = array();
            foreach ($this->models as $model) {
                $notInActivities[] = $model->getActivity()->getId();
            }

            if ($this->models && $this->moveType == self::MOVE_TYPE_ACTIVITIES) {
                $this->activities = ActivityTable::getInstance()
                    ->createQuery()
                    ->select()
                    ->whereNotIn('id', $notInActivities)
                    ->orderBy('id DESC')
                    ->execute();
            } else if ($this->moveType == self::MOVE_TYPE_DEALERS) {
                $this->dealers = DealerTable::getVwDealersQuery()->execute();
            }

            if (count($this->models) == 0) {
                $this->success = false;
                $this->makeChanges = true;

                $this->setTemplate('index');
            }
        }
    }

    function executeShow(sfWebRequest $request)
    {
        $this->setTemplate('index');
    }

    /**
     * Перенос заявок выбранному дилеру
     * @param sfWebRequest $request
     */
    function executeModelMoveToDealer(sfWebRequest $request)
    {
        $modelsIds = $request->getParameter('modelsIds');
        $dealer = $request->getParameter('moveTo');

        if (count($modelsIds) > 0) {
            $query = AgreementModelTable::getInstance()
                ->createQuery('m')
                ->select('*')
                ->leftJoin('m.Report r')
                ->leftJoin('m.ModelType mt')
                ->andWhereIn('m.id', $modelsIds);
            $models = $query->execute();

            foreach ($models as $model) {
                if ($dealer != -1) {
                    $model->setDealerId($dealer);
                    $model->save();
                }
            }

            echo json_encode(
                array
                (
                    'success' => true,
                    'msg' => 'Перенос заявок успешно завершен. Всего заявок: ' . count($models) .
                        '<br/><a href="/backend.php/models_dates">Вернуться к поиску</a>'
                )
            );
        } else {
            echo json_encode(array('success' => false, 'msg' => 'Ошибка при переносе заявок.'));
        }

        return sfView::NONE;
    }

    /**
     * Перенос заявок в другую активность
     * @param sfWebRequest $request
     */
    function executeModelDate(sfWebRequest $request)
    {
        $date = $request->getParameter('modelToDate');
        $modelsIds = $request->getParameter('modelsIds');

        $this->date = $date;
        $this->modelsIds = $modelsIds;
        $this->activity = $request->getParameter('moveTo');

        if (preg_match('#^[0-9]{2}\-[0-9]{2}\-[0-9]{4}$#', $date)) {
            $date = date('Y-m-d H:i:s', strtotime($date . date('H:i:s')));
        } else {
            $date = false;
        }

        if (count($modelsIds) > 0) {
            $query = AgreementModelTable::getInstance()
                ->createQuery('m')
                ->select('*')
                ->leftJoin('m.Report r')
                ->leftJoin('m.ModelType mt')
                //->where('r.status = ?', array('accepted'))
                ->andWhereIn('m.id', $modelsIds);
            $models = $query->execute();
            foreach ($models as $model) {
                if ($this->activity != -1) {
                    $model->setActivityId($this->activity);
                }

                if(!is_null($date)) {
                    $model->setUpdatedAt($date);
                    $model->save();

                    $report = $model->getReport();
                    if ($report && $report->getId() != null) {
                        $report->setAcceptDate($date);
                        $report->save();
                    }

                    $entry = LogEntryTable::getInstance()
                        ->createQuery()
                        ->where('object_id = ?', array($model->getId()))
                        ->andWhere('object_type = ? and icon = ? and action = ? and private_user_id = ?', array('agreement_report', 'clip', 'edit', 0))
                        ->orderBy('id DESC')
                        ->limit(1)
                        ->fetchOne();
                    if ($entry) {
                        $entry->setCreatedAt($date);
                        $entry->save();
                    }
                }

                $this->success = true;
            }

            echo json_encode(
                array
                (
                    'success' => true,
                    'msg' => 'Перенос заявок успешно завершен. Всего заявок: ' . count($models) .
                        '<br/><a href="/backend.php/models_dates">Вернуться к поиску</a>'
                )
            );
        } else {
            echo json_encode(array('success' => false, 'msg' => 'Ошибка при переносе заявок.'));
        }

        return sfView::NONE;
    }
}
