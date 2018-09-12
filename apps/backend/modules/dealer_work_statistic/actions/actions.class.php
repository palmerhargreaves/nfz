<?php

/**
 * dealer_work_statistic actions.
 *
 * @package    Servicepool2.0
 * @subpackage dealer_work_statistic
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class dealer_work_statisticActions extends sfActions
{
    const FILTER_NAMESPACE = 'filter';

    function executeIndex(sfWebRequest $request)
    {
        $this->outputFilters();
        $this->outputData();
    }

    function executeShow(sfWebRequest $request)
    {
        $this->setTemplate('index');
    }

    function executeDealerWorkStatisticClear(sfWebRequest $request)
    {
        $this->getUser()->setAttribute('dealer_id', 0, self::FILTER_NAMESPACE);
        $this->getUser()->setAttribute('start_date', '', self::FILTER_NAMESPACE);
        $this->getUser()->setAttribute('end_date', '', self::FILTER_NAMESPACE);
        $this->getUser()->setAttribute('by_year', -1, self::FILTER_NAMESPACE);

        $this->outputData();

        $this->setTemplate('index');
    }

    function outputData()
    {
        $this->dealers = DealerTable::getInstance()->getDealersList()->execute();

        $query = DealerWorkStatisticTable::getInstance()
            ->createQuery()
            ->orderBy('id ASC');

        $dealerId = $this->getDealerFilter();
        if ($dealerId != 0) {
            $query->andWhere('dealer_id = ?', $dealerId);
        }

        $byYear = $this->getByYearFilter();

        if(!empty($byYear) && $byYear != -1) {
            $query->andWhere("year(created_at) = ?", $byYear);
        }
        else {
            $startDateFilter = $this->getStartDateFilter();
            if (!empty($startDateFilter))
                $query->andWhere('created_at >= ?', D::toDb($startDateFilter));

            $endDateFilter = $this->getEndDateFilter;
            if (!empty($endDateFilter)) {
                $query->andWhere('created_at <= ?', D::toDb($endDateFilter));
            }

            if (empty($startDateFilter) && empty($endDateFilter)) {
                $query->andWhere("created_at LIKE ?", array(date('Y-m-d') . '%'));
            }
        }



        $this->stats = $query->execute();
    }

    function outputFilters()
    {
        $this->filterDealer = $this->getDealerFilter();
        $this->startDateFilter = $this->getStartDateFilter();
        $this->endDateFilter = $this->getEndDateFilter();
        $this->byYear = $this->getByYearFilter();
    }

    function getDealerFilter()
    {
        $default = $this->getUser()->getAttribute('dealer_id', '0', self::FILTER_NAMESPACE);
        $dealerId = $this->getRequestParameter('sb_dealer', $default);

        $this->getUser()->setAttribute('dealer_id', $dealerId, self::FILTER_NAMESPACE);

        return $dealerId;
    }

    function getStartDateFilter()
    {
        $default = $this->getUser()->getAttribute('start_date', '', self::FILTER_NAMESPACE);
        $startDate = $this->getRequestParameter('start_date', $default);

        $this->getUser()->setAttribute('start_date', $startDate, self::FILTER_NAMESPACE);

        return $startDate;
    }

    function getEndDateFilter()
    {
        $default = $this->getUser()->getAttribute('end_date', '', self::FILTER_NAMESPACE);
        $endDate = $this->getRequestParameter('end_date', $default);

        $this->getUser()->setAttribute('end_date', $endDate, self::FILTER_NAMESPACE);

        return $endDate;
    }

    function getByYearFilter()
    {
        $default = $this->getUser()->getAttribute('by_year', '', self::FILTER_NAMESPACE);
        $byYear = $this->getRequestParameter('sbByYear', $default);

        $this->getUser()->setAttribute('by_year', $byYear, self::FILTER_NAMESPACE);

        return $byYear;
    }


}
