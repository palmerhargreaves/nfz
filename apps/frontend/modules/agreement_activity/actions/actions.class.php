<?php

include(sfConfig::get('sf_root_dir') . '/lib/PHPExcel.php');
include(sfConfig::get('sf_root_dir') . '/lib/PHPExcel/Cell.php');
include(sfConfig::get('sf_root_dir') . '/lib/PHPExcel/Writer/Excel5.php');

/**
 * agreement_activity actions.
 *
 * @package    Servicepool2.0
 * @subpackage agreement_activity
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class agreement_activityActions extends ActionsWithJsonForm
{
    const FILTER_EXPORT_NAMESPACE = 'activity_export_filter';

    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    function executeIndex(sfWebRequest $request)
    {
        $this->outputPrev($request);
        $this->outputActivities(false);

        $this->is_finished = false;
        $this->setTemplate('activities');
    }

    function executeFinishedActivities(sfWebRequest $request)
    {
        $this->outputPrev($request);
        $this->outputActivities(true);

        $this->is_finished = true;
        $this->setTemplate('activities');
    }

    function executeActivity(sfWebRequest $request)
    {
        $this->outputPrev($request);

        $activity = ActivityTable::getInstance()->find($request->getParameter('id'));
        $this->dealer_filter = $this->getDealerFilter();
        $this->start_date_filter = $this->getStartDateFilter();
        $this->end_date_filter = $this->getEndDateFilter();
        $this->view_data_filter = $this->getViewDataFilter();

        $this->forward404Unless($activity);

        $builder = new AgreementActivityModelsStatisticBuilder($activity, $this->dealer_filter, null, $this->start_date_filter, $this->end_date_filter);
        $builder->build();

        $this->builder = $builder;
        $this->activityId = $request->getParameter('id');

        $this->outputDeclineReasons();
        $this->outputDeclineReportReasons();
        $this->outputSpecialistGroups();
    }

    /**
     * Export activity data to Excel
     * @param sfWebRequest $request
     */
    function executeExportActivity(sfWebRequest $request)
    {
        $pExcel = new PHPExcel();
        $pExcel->setActiveSheetIndex(0);
        $aSheet = $pExcel->getActiveSheet();
        $aSheet->setTitle('Дилеры');

        $headers = array('#', '№ дилера', 'Название дилера', 'Тип рекламы', 'Согласовал макет', 'Согласовал отчет', 'Сумма');

        //настройки для шрифтов
        $baseFont = array(
            'font' => array(
                'name' => 'Arial Cyr',
                'size' => '10',
                'bold' => false
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )
        );

        $boldFont = array(
            'font' => array(
                'name' => 'Arial Cyr',
                'size' => '10',
                'bold' => true
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );

        $rightFont = array(
            'font' => array(
                'name' => 'Arial Cyr',
                'size' => '10',
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );

        $smallRightFont = array(
            'font' => array(
                'name' => 'Arial Cyr',
                'size' => '8',
                'bold' => true
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );

        $left = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );

        $aSheet->getStyle('A1:G1')->applyFromArray($boldFont);
        $aSheet->getStyle('B:G')->applyFromArray($left);

        $column = 0;
        $tCount = 1;
        foreach ($headers as $head) {
            $aSheet->setCellValueByColumnAndRow($column++, 1, $head);
            $tCount++;
        }

        $aSheet->getColumnDimension('A')->setWidth(3);
        $aSheet->getColumnDimension('B')->setWidth(10);
        $aSheet->getColumnDimension('C')->setWidth(40);
        $aSheet->getColumnDimension('D')->setWidth(15);
        $aSheet->getColumnDimension('E')->setWidth(15);
        $aSheet->getColumnDimension('F')->setWidth(15);
        $aSheet->getColumnDimension('G')->setWidth(20);

        $aSheet->getRowDimension('1')->setRowHeight(35);

        $aSheet->getStyle('A1:G1')->getAlignment()->setWrapText(true);

        $activity = ActivityTable::getInstance()->find($request->getParameter('activity_id'));
        $dealer = $request->getParameter('dealer');
        $modelWorkStatus = $request->getParameter('model_work_status');

        $this->forward404Unless($activity);

        $this->dealer_filter = $this->getDealerFilter();
        $this->start_date_filter = $this->getStartDateFilter();
        $this->end_date_filter = $this->getEndDateFilter();

        $builder = new AgreementActivityModelsStatisticBuilder($activity, $this->dealer_filter, null, $this->start_date_filter, $this->end_date_filter);
        $builder->build();

        $row = 2;

        $statsResult = $builder->getStat();
        $extendedStats = $statsResult['extended'];

        foreach ($extendedStats as $q => $data) {
            foreach ($data as $year => $dealers) {
                foreach ($dealers as $id => $dealer) {
                    if ($dealer['all'] > 0) {
                        $column = 1;

                        if ($dealer['done'] && $dealer['all'] > 0) {
                            $icon = 'ok-icon-active.png';
                            $fillColor = 'D6FDD6';
                        } else {
                            if ($dealer['accepted_models'] > 0) {
                                $icon = 'ok-icon.png';
                                $fillColor = 'e6f0f2';
                            } else {
                                $icon = 'error-icon.png';
                                $fillColor = 'FBCBC6';
                            }
                        }

                        $this->drawExcelImage($icon, 'A' . $row, $pExcel, 3, 5);

                        $aSheet->setCellValueByColumnAndRow($column++, $row, $dealer['dealer']->getShortNumber());
                        $aSheet->setCellValueByColumnAndRow($column++, $row, $dealer['dealer']->getName());

                        $aSheet->getStyle('A' . $row . ':G' . $row)
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB($fillColor);

                        $aSheet->getRowDimension($row)->setRowHeight(20);

                        $totalSumm = 0;
                        $originalRow = $row;

                        foreach ($dealer['models'] as $n => $model) {
                            $row++;
                            $tempColumn = $column - 1;

                            $aSheet->setCellValueByColumnAndRow($tempColumn++, $row, $model->getId());
                            $aSheet->getStyle('C' . $row . ':C' . $row)->applyFromArray($smallRightFont);

                            $aSheet->setCellValueByColumnAndRow($tempColumn, $row, $model->getModelType()->getName());
                            $aSheet->getStyle('D' . $row . ':D' . $row)->applyFromArray($baseFont);

                            if ($model->getCssStatus() == 'ok') {
                                $this->drawExcelImage('accepted.png', 'E' . $row, $pExcel, 50);
                            } else {
                                $this->drawExcelImage('not_accepted.png', 'E' . $row, $pExcel, 50);
                            }

                            if ($model->getReportCssStatus() == 'ok') {
                                $this->drawExcelImage('accepted.png', 'F' . $row, $pExcel, 50);
                            } else {
                                $this->drawExcelImage('not_accepted.png', 'F' . $row, $pExcel, 50);
                            }

                            $aSheet->setCellValueByColumnAndRow($tempColumn + 3, $row, $this->formatPrice($model->getCost()));
                            $aSheet->getStyle('G' . $row . ':G' . $row)->applyFromArray($rightFont);

                            $totalSumm += $model->getCost();
                        }

                        $aSheet->setCellValueByColumnAndRow($column + 3, $originalRow, $this->formatPrice($totalSumm));
                        $aSheet->getStyle('G' . $originalRow . ':G' . $originalRow)->applyFromArray($boldFont);

                        $row++;
                    }
                }
            }
        }

        $objWriter = new PHPExcel_Writer_Excel5($pExcel);
        $objWriter->save(sfConfig::get('sf_root_dir') . '/www/uploads/dealers.xls');

        return $this->sendJson(array('url' => sfConfig::get('app_site_url').'/uploads/dealers.xls', 'success' => true));
    }

    private function drawExcelImage($icon, $coordinates, $pExcel, $offsetX = 3, $offsetY = 3)
    {
        $imageModelStatus = new PHPExcel_Worksheet_Drawing();

        $imageModelStatus->setPath(sfConfig::get('app_images_path') . '/' . $icon);
        $imageModelStatus->setName('work_status');
        $imageModelStatus->setDescription('work_status');
        $imageModelStatus->setHeight(16);
        $imageModelStatus->setWidth(16);

        $imageModelStatus->setOffsetX($offsetX);
        $imageModelStatus->setOffsetY($offsetY);

        $imageModelStatus->setWorksheet($pExcel->getActiveSheet());
        $imageModelStatus->setCoordinates($coordinates);
    }

    private function formatPrice($price)
    {
        return number_format($price, 2, '.', ' ') . ' руб.';
    }

    function executeNoWorkDealers(sfWebRequest $request)
    {
        $activity = ActivityTable::getInstance()->find($request->getParameter('id'));
        $this->forward404Unless($activity);

        //$builder = new AgreementActivityStatisticBuilder($activity, null, true);
        $builder = new AgreementActivitiesStatisticBuilder($activity, null, true);
        $stat = $builder->buildNoWork();

        $this->activity = $activity;
        $this->status = sfConfig::get('app_no_work');
        $this->outputDealers($stat[$activity->getId()]['no_work_dealers']);
    }

    function executeInWorkDealers(sfWebRequest $request)
    {
        $activity = ActivityTable::getInstance()->find($request->getParameter('id'));
        $this->forward404Unless($activity);

        //$builder = new AgreementActivityStatisticBuilder($activity, null, true);
        $builder = new AgreementActivitiesStatisticBuilder($activity, null, true);

        $stat = $builder->buildInWork();

        $this->activity = $activity;
        $this->status = sfConfig::get('app_in_work');
        $this->outputDealers($stat[$activity->getId()]['in_work_dealers']);
    }

    function executeDoneDealers(sfWebRequest $request)
    {
        $activity = ActivityTable::getInstance()->find($request->getParameter('id'));
        $this->forward404Unless($activity);

        //$builder = new AgreementActivityStatisticBuilder($activity, null, true);
        $builder = new AgreementActivitiesStatisticBuilder($activity, null, true);
        $stat = $builder->buildDone();

        $this->activity = $activity;
        $this->status = sfConfig::get('app_done');
        $this->outputDealers($stat[$activity->getId()]['done_dealers']);
    }

    function executeActivitiesStatus(sfWebRequest $request)
    {
        $this->dealer = $request->getParameter('dealer', '');
        $quarter = $request->getParameter('quarter', D::getQuarter(time()));
        $this->quarter = $quarter;

        $this->outputPrev($request);

        $this->activities = array();
        $query = DealerActivitiesStatsDataTable::getInstance()
            ->createQuery('ds')
            ->select('ds.*, a.name as activityName, a.id as activityId, manager_stat.manager_id')
            ->leftJoin('ds.Activity a')
            ->innerJoin('ds.DealerActivitiesStats manager_stat')
            ->where('ds.year = ?', $this->year)
            ->groupBy('ds.activity_id')
            ->orderBy('activityId DESC');

        $temp_result = $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
        if ($this->getUser()->isRegionalManager()) {
            foreach ($temp_result as $temp_item) {
                foreach ($temp_item['DealerActivitiesStats'] as $key => $temp_item_data) {
                    if ($temp_item_data['manager_id'] = $this->getUser()->getAuthUser()->getNaturalPersonId()) {
                        $this->activities[] = $temp_item;
                    }
                }
            }
        }
        else {
            $this->activities = $temp_result;
        }

        $activeActivities = array();
        foreach ($this->activities as $activity_row) {
            if (!array_key_exists($activity_row['activityId'], $activeActivities)) {
                $activeActivities[$activity_row['activityId']] = $activity_row['activityId'];
            }
        }

        $query = DealerActivitiesStatsManagersTable::getInstance()
            ->createQuery('dam')
            ->where('year = ?',
                array
                (
                    $this->year,
                )
            )
            ->orderBy('manager_id ASC');

        if ($this->getUser()->isRegionalManager()) {
            $query->andWhere('manager_id = ?', $this->getUser()->getAuthUser()->getNaturalPersonId());
        }
        $this->managers = $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

        $this->workStats = array();
        $this->dealers = array();

        foreach ($this->managers as $manager) {
            $query = DealerActivitiesStatsTable::getInstance()
                ->createQuery('s')
                ->select('s.id, d.name, s.percent_of_budget, s.dealer_id, s.models_completed, s.activities_completed, s.manager_id, s.q1, s.q2, s.q3, s.q4, s.q_activity1, s.q_activity2, s.q_activity3, s.q_activity4')
                ->innerJoin('s.DealerStat d')
                ->where('s.manager_id = ?', $manager['id'])
                ->andWhere('d.dealer_type = ? or d.dealer_type = ?', array(Dealer::TYPE_NFZ, Dealer::TYPE_NFZ_PKW))
                //->andWhere('d.regional_manager_id = ?', $manager['manager_id'])
                ->orderBy('s.manager_id ASC');

            if ($this->getUser()->isRegionalManager()) {
                $query->innerJoin('s.ManagerStat ms')
                        ->andWhere('ms.manager_id = ?', $manager['manager_id']);
            }

            $dealerStats = $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
            foreach ($dealerStats as $stat) {
                $this->dealers[$manager['manager_id']][] = $stat;
            }

            foreach ($this->dealers[$manager['manager_id']] as $dealer) {
                if ($this->getUser()->isRegionalManager()) {
                    $dealerActivities = DealerActivitiesStatsDataTable::getDataBy($dealer['id'], $this->year);
                } else {
                    $dealerActivities = DealerActivitiesStatsDataTable::getInstance()
                        ->createQuery('as')
                        ->select('activity_id, status, total_completed, in_work, complete')
                        ->leftJoin('as.Activity a')
                        ->where('dealer_stat_id = ?', $dealer['id'])
                        ->orderBy('a.id DESC')
                        ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
                }

                foreach ($dealerActivities as $item) {
                    if (!in_array($item['activity_id'], $activeActivities)) {
                        continue;
                    }

                    if (!isset($this->workStats[$item['activity_id']])) {
                        $this->workStats[$item['activity_id']] = array('in_work' => 0, 'completed' => 0);
                    }

                    if ($this->getUser()->isRegionalManager()) {
                        $activityStatusInfo = DealerActivitiesStatsDataTable::getActivityStatus($item);

                        if ($activityStatusInfo['in_work'] > 0) {
                            $this->workStats[$item['activity_id']]['in_work']++;
                        }

                        if ($activityStatusInfo['complete'] > 0) {
                            $this->workStats[$item['activity_id']]['completed']++;
                        }
                    } else {
                        if ($item['complete'] > 0) {
                            $this->workStats[$item['activity_id']]['completed'] = $item['complete'];
                        } else if ($item['in_work'] > 0) {
                            $this->workStats[$item['activity_id']]['in_work'] = $item['in_work'];
                        }
                    }
                }
            }
        }

        $this->totalItems = DealerActivitiesStatsDataTable::getInstance()
            ->createQuery()
            ->select('value, field_name')
            ->where('year = ?', $this->year)
            ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

        $this->total = array();
        foreach ($this->totalItems as $item) {
            $this->total[$item['field_name']] = $item['value'];
        }

        //$this->builder = new AgreementActivityStatusStatisticBuilder($this->year, $quarter);

    }

    function executeFake()
    {

    }

    protected function outputActivities($finished)
    {
        //$builder = new AgreementActivityStatisticBuilder(null, $finished);
        $builder = new AgreementActivitiesStatisticBuilder(null, $finished, true, $this->getUser()->getAuthUser());
        $builder->setYearFilter($this->year);

        $this->activities = $builder->build();
    }

    function outputDeclineReasons()
    {
        $this->decline_reasons = AgreementDeclineReasonTable::getInstance()->createQuery()->execute();
    }

    function outputDeclineReportReasons()
    {
        $this->decline_report_reasons = AgreementDeclineReportReasonTable::getInstance()->createQuery()->execute();
    }

    function outputSpecialistGroups()
    {
        $this->specialist_groups = UserGroupTable::getInstance()
            ->createQuery('g')
            ->distinct()
            ->select('g.*')
            ->innerJoin('g.Roles r WITH r.role=?', 'specialist')
            ->innerJoin('g.Users u WITH u.active=?', true)
            ->execute();
    }

    function outputDealers($dealers)
    {
        $this->dealers = $dealers;
        $this->setTemplate('dealers');
    }

    function outputPrev(sfWebRequest $request)
    {
        $this->year = D::getBudgetYear($request);

        $this->budgetYears = D::getBudgetYears($request, false);
    }

    private function getDealerFilter()
    {
        $default = $this->getUser()->getAttribute('dealer', null, self::FILTER_EXPORT_NAMESPACE);
        $dealer = $this->getRequestParameter('dealer', $default);
        $this->getUser()->setAttribute('dealer', $dealer, self::FILTER_EXPORT_NAMESPACE);

        if ($dealer != -1 && !is_null($dealer)) {
            $dealer = DealerTable::getInstance()->findOneById($dealer);
        }

        return $dealer;
    }

    private function getStartDateFilter()
    {
        $default = $this->getUser()->getAttribute('start_date', '', self::FILTER_EXPORT_NAMESPACE);
        $start_date = $this->getRequestParameter('start_date', $default);
        $this->getUser()->setAttribute('start_date', $start_date, self::FILTER_EXPORT_NAMESPACE);

        return preg_match('#^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$#', $start_date)
            ? D::fromRus($start_date)
            : false;
    }

    private function getEndDateFilter()
    {
        $default = $this->getUser()->getAttribute('end_date', '', self::FILTER_EXPORT_NAMESPACE);
        $end_date = $this->getRequestParameter('end_date', $default);
        $this->getUser()->setAttribute('end_date', $end_date, self::FILTER_EXPORT_NAMESPACE);

        return preg_match('#^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$#', $end_date)
            ? D::fromRus($end_date)
            : false;
    }

    private function getViewDataFilter()
    {
        $default = $this->getUser()->getAttribute('view_data', 'quarters', self::FILTER_EXPORT_NAMESPACE);
        $view_data = $this->getRequestParameter('view_data', $default);
        $this->getUser()->setAttribute('view_data', $view_data, self::FILTER_EXPORT_NAMESPACE);

        return $view_data;
    }
}
