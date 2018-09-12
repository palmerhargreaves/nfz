<?php

/**
 * deleted_models actions.
 *
 * @package    Servicepool2.0
 * @subpackage deleted_models
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class deleted_modelsActions extends sfActions
{
	
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
    const FILTER_NAMESPACE = 'models';

    const AGREEMENT_MODEL = 'agreement_model';
    const ACTION_TYPE_DELETE = 'delete';


    function executeIndex(sfWebRequest $request)
    {
        $this->outputModelIndex();
        $this->outputStartDateFilter();
        $this->outputEndDateFilter();
        $this->outputDealerFilter();
        $this->outputShowAllFilter();

        $this->outputResult();
    }
  
    /*function executeShow(sfWebRequest $request)
    {
        $this->setTemplate('index');
    }*/

    private function outputResult()
    {
        $this->dealers = DealerTable::getVwDealersQuery()->execute();

        $query = LogEntryTable::getInstance()
            ->createQuery('l')
            ->where('l.object_type = ? and l.action = ?',
                array(
                    self::AGREEMENT_MODEL,
                    self::ACTION_TYPE_DELETE
                )
            )
            ->orderBy('l.object_id DESC')
            ->groupBy('l.object_id');

        if($this->getShowAllFilter() != 0) {

        }
        else if($this->getModelIndexFilter() != 0) {
            $query->andWhere('object_id = ?', $this->getModelIndexFilter());
        }
        else {

            if ($this->getStartDateFilter()) {
                $query->andWhere('l.created_at >= ?', date("Y-m-d", D::toUnix($this->getStartDateFilter())));
            }
            else {
                $firstDay = mktime(0, 0, 0, date('m'), 1, date('Y'));
                $query->andWhere('l.created_at >= ?', date('Y-m-d', $firstDay));
            }

            if ($this->getEndDateFilter()) {
                $query->andWhere('l.created_at <= ?', date("Y-m-d", D::toUnix($this->getEndDateFilter())));
            }
            else {
                $lastDay = mktime(0, 0, 0, date('m'), date('t'), date('Y'));
                $query->andWhere('l.created_at <= ?', date('Y-m-d', $lastDay));
            }

            if ($this->getDealerFilter() != -1) {
                $query->andWhere('l.dealer_id = ?', $this->getDealerFilter());
            }
        }

        $this->items = $query->execute();
        $this->totalDeletedItems = LogEntryTable::getInstance()
            ->createQuery()
            ->where('object_type = ? and action = ?',
                array(
                    self::AGREEMENT_MODEL,
                    self::ACTION_TYPE_DELETE
                )
            )
            ->orderBy('object_id')
            ->execute()
                ->count();
    }

    private function getStartDateFilter()
    {
        $default = $this->getUser()->getAttribute('start_date', '', self::FILTER_NAMESPACE);
        $startDate = $this->getRequestParameter('txtStartDateFilter', $default);
        $this->getUser()->setAttribute('start_date', $startDate, self::FILTER_NAMESPACE);

        return $startDate;
    }

    private function getEndDateFilter() {
        $default = $this->getUser()->getAttribute('end_date', '', self::FILTER_NAMESPACE);
        $endDate = $this->getRequestParameter('txtEndDateFilter', $default);
        $this->getUser()->setAttribute('end_date', $endDate, self::FILTER_NAMESPACE);

        return $endDate;
    }

    private function getDealerFilter()
    {
        $default = $this->getUser()->getAttribute('dealer', -1, self::FILTER_NAMESPACE);
        $dealer = $this->getRequestParameter('sbDealer', $default);
        $this->getUser()->setAttribute('dealer', $dealer, self::FILTER_NAMESPACE);

        return $dealer;
    }

    private function getShowAllFilter()
    {
        $default = $this->getUser()->getAttribute('show_all', 0, self::FILTER_NAMESPACE);
        $showAll = $this->getRequestParameter('chShowAll');
        $showAll = isset($showAll) ? 1 : 0;
        $this->getUser()->setAttribute('show_all', $showAll, self::FILTER_NAMESPACE);

        return $showAll;
    }

    private function getModelIndexFilter() {
        $default = $this->getUser()->getAttribute('model_id', 0, self::FILTER_NAMESPACE);
        $modelIndex = $this->getRequestParameter('txtModelIndex', $default);
        $this->getUser()->setAttribute('model_id', $modelIndex, self::FILTER_NAMESPACE);

        return $modelIndex;
    }

    private function outputStartDateFilter() {
        $this->startDateFilter = $this->getStartDateFilter();
    }

    private function outputEndDateFilter() {
        $this->endDateFilter = $this->getEndDateFilter();
    }

    private function outputDealerFilter() {
        $this->dealerFilter = $this->getDealerFilter();
    }

    private function outputShowAllFilter() {
        $this->showAllFilter = $this->getShowAllFilter();
    }

    private function outputModelIndex() {
        $this->modelId = $this->getModelIndexFilter();
    }

    public function executeDeletedModelHistory(sfWebRequest $request)
    {
        $modelId = $request->getParameter('modelId');

        $this->items = LogEntryTable::getInstance()
            ->createQuery('l')
            ->where('l.object_type = ? and l.action != ?',
                array(
                    self::AGREEMENT_MODEL,
                    self::ACTION_TYPE_DELETE
                )
            )
            ->andWhere('l.object_id = ?', $modelId)
            ->orderBy('id DESC')
                ->execute();
    }
}
