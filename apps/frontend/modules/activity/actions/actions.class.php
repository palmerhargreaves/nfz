<?php

/**
 * activity actions.
 *
 * @package    Servicepool2.0
 * @subpackage activity
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class activityActions extends BaseActivityActions
{
    const FILTER_NAMESPACE = 'stats';

    function executeIndex(sfWebRequest $request)
    {
        $this->outputActivity($request);

        $this->getUser()->setAttribute('current_q', 0, self::FILTER_Q_NAMESPACE);
        $current_q = $request->getParameter('current_q');

        if (!is_null($current_q)) {
            sfContext::getInstance()->getConfiguration()->loadHelpers(array('Url'));

            //$this->outputModelsQuarters($request);
            $this->outputFilterByYear();
            $this->outputFilterByQuarter();

            $this->redirect(url_for('@agreement_module_models?activity=' . $request->getParameter('activity')));
        }

        $this->activity->markAsViewed($this->getUser()->getAuthUser());
    }

    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    function executeFinished(sfWebRequest $request)
    {
        $this->year = D::getBudgetYear($request);

        $user = $this->getUser();
        $show_hidden = $user->isAdmin() || $user->isImporter() || $user->isManager();

        $query = ActivityTable::getInstance()
            ->createQuery('a')
            ->select('a.id, a.start_date, a.end_date, a.custom_date, a.name, a.brief, a.importance, v.id is_viewed')
            ->leftJoin('a.UserViews v WITH v.user_id=?', $this->getUser()->getAuthUser()->getId())
            ->where('finished=?', true)
            ->orderBy('a.position ASC');

        if (!$show_hidden)
            $query->andWhere('a.hide=?', false);

        $this->activities = $query->execute();
    }

    function executeStatistic(sfWebRequest $request)
    {
        $this->outputModelsQuarters($request);
        $this->outputFilterByYear();
        $this->outputFilterByQuarter();

        $this->activity = $this->getActivity($request);

        //$this->checkAllowToEdit();
        $this->preCheckStatisticStatus($request);
    }


    function executeStatisticOne(sfWebRequest $request)
    {
        $this->activity = $this->getActivity($request);

        $this->outputModelsQuarters($request);
        $this->outputFilterByYear();
        $this->outputFilterByQuarter();

        $user = $this->getUser()->getAuthUser();

        $userDealer = $user->getDealerUsers()->getFirst();
        if ($userDealer) {
            $dealer = DealerTable::getInstance()->createQuery('d')->where('id = ?', $userDealer->getDealerId())->fetchOne();
        }

        if (!$dealer) {
            return sfView::ERROR;
        }

        $models = AgreementModelTable::getInstance()
            ->createQuery('am')
            ->select('am.created_at as amUpdatedAt, amr.created_at as amrUpdatedAt, am.status as amStatus, amr.status as amrStatus')
            ->leftJoin('am.Report amr')
            ->where('activity_id = ? and dealer_id = ?', array($this->activity->getId(), $dealer->getId()))
            ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

        $activityStatisticPeriods = $this->activity->getActivityStatisticPeriodsInfo();

        $quarter = 0;
        foreach ($models as $model) {
            $year = intval(D::getYear($model['amUpdatedAt']));
            $q = intval(D::getQuarter($model['amUpdatedAt']));

            if (count($activityStatisticPeriods) && isset($activityStatisticPeriods[$year]) && !in_array($q, $activityStatisticPeriods[$year])) {
                continue;
            }

            $quarter = D::getQuarter(D::calcQuarterData($model['amUpdatedAt']));
        }

        $this->current_q = $quarter != 0 ? $quarter : $request->getParameter('current_q', $this->current_q != 0 ? $this->current_q : D::getQuarter(time()) );

        $this->preCheckStatisticStatus($request);

        $this->setTemplate('statistic');
    }

    function executeExtendedStatistic(sfWebRequest $request)
    {
        $this->activity = $this->getActivity($request);//ActivityTable::getInstance()->find($request->getParameter('activity'));
        $this->outputModelsQuarters($request);
    }

    public function outputModelsQuarters(sfWebRequest $request)
    {
        sfContext::getInstance()->getConfiguration()->loadHelpers(array('Url'));

        $quartersModels = new ActivityQuartersModelsAndStatistics($this->getUser()->getAuthUser(), $this->getActivity($request));
        $qData = $quartersModels->getData();

        $qList = array();
        $yearsList = array();

        foreach ($qData as $y_key => $q_data) {
            $qList = array_merge($qList, array_map(function($key) {
                return $key;
            }, array_keys($q_data))) ;

            if (!in_array($y_key, $yearsList)) {
                $yearsList[] = $y_key;
            }
        }

        $current_date = D::calcQuarterData(time());
        $currentQ = D::getQuarter($current_date);
        $currentY = D::getYear($current_date);

        $q = $this->getUser()->getAttribute('current_q', D::getQuarter(time()), self::FILTER_Q_NAMESPACE);
        $year = $this->getUser()->getAttribute('current_year', D::getYear(time()), self::FILTER_YEAR_NAMESPACE);

        if ($q == 0 || (!in_array($q, $qList) && !empty($qList))) {
            $q = isset($qList[0]) ? max($qList) : D::getQuarter(D::calcQuarterData(time()));
        }

        if (!empty($year) && !in_array($year, $yearsList)) {
            $year = isset($yearsList[0]) ? $yearsList[0] : D::getYear(D::calcQuarterData(time()));
        }

        if (!empty($q) && $q != 0) {
            $this->year = $year;
            $this->current_q = $q;
            $this->default_module = 'agreement';

            $this->getUser()->setAttribute('current_q', $this->current_q, self::FILTER_Q_NAMESPACE);
            $this->getUser()->setAttribute('current_year', $this->year, self::FILTER_YEAR_NAMESPACE);
        } else {
            if (in_array($currentQ, $qList)) {
                $this->current_q = $currentQ;
            } else {
                $this->current_q = count($qList) > 0 ? $qList[0] : null;
            }

            if (in_array($currentY, $yearsList)) {
                $this->current_year = $currentY;
            } else {
                $this->current_q = count($yearsList) > 0 ? $yearsList[0] : null;
            }

            if ($this->getUser()->getAttribute('current_q') != $this->current_q) {
                $this->getUser()->setAttribute('current_q', $this->current_q, self::FILTER_Q_NAMESPACE);
                $this->getUser()->setAttribute('current_year', $this->current_year, self::FILTER_YEAR_NAMESPACE);

                $this->redirect(url_for('@activity_quarter_data?activity='.$this->getActivity($request)->getId().'&current_q='.$this->current_q.'&current_year='.$this->current_year));
            }
        }

        $this->quartersModels = $quartersModels;
        $this->open_model = $request->getParameter('model');
    }

    function executeChangeStats(sfWebRequest $request)
    {
        $fields = $request->getParameter('data');

        foreach ($fields as $field) {
            $row = ActivityFieldsValuesTable::getInstance()->find($field['id']);

            if ($row) {
                $row->setVal($field['value']);
                $row->setUpdatedAt(date('Y-m-d H:i:s'));
                $row->save();
            }
        }

        return $this->sendJson(array('success' => true));
    }

    function executeChangeExtendedStats(sfRequest $request)
    {
        $fields = $request->getParameter('data');

        foreach ($fields as $field) {
            $row = ActivityExtendedStatisticFieldsDataTable::getInstance()->createQuery()->where('id = ? and concept_id = ?', array($field['id'], $request->getParameter('concept')))->fetchOne();

            if ($row) {
                $row->setValue($field['value']);
                $row->setUpdatedAt(date('Y-m-d H:i:s'));
                $row->save();
            }
        }

        return $this->sendJson(array('success' => true));
    }

    function executeStatisticInfo(sfWebRequest $request)
    {
        $this->outputActivityFilter($request);
        $this->outputActivityQuarterFilter($request);

        $this->builder = new ActivityStatisticFieldsBuilder(
            array
            (
                'year' => date('Y'),
                'quarter' => $this->activityQuarter
            ),
            $this->activity,
            $this->getUser());
    }

    function executeBindToConcept(sfWebRequest $request)
    {
        $this->concept = $request->getParameter('concept');
        $this->activity = ActivityTable::getInstance()->find($request->getParameter('activity'));
    }

    function getActivityFilter()
    {
        $default = $this->getUser()->getAttribute('activity', 0, self::FILTER_NAMESPACE);
        $activity = $this->getRequestParameter('activity', $default);

        $this->getUser()->setAttribute('activity', $activity, self::FILTER_NAMESPACE);

        return $activity;
    }

    function getActivityQuarterFilter()
    {
        $default = $this->getUser()->getAttribute('activityQuarter', 0, self::FILTER_NAMESPACE);
        $q = $this->getRequestParameter('activityQuarter', $default);

        $this->getUser()->setAttribute('activityQuarter', $q, self::FILTER_NAMESPACE);

        return $q;
    }

    function outputActivityFilter(sfWebRequest $request)
    {
        $this->year = D::getBudgetYear($request);

        $this->activity = $this->getActivityFilter();
        if ($this->activity != 0)
            $this->activity = ActivityTable::getInstance()->find($this->activity);
        else
            $this->activity = null;

    }

    function outputActivityQuarterFilter()
    {
        $this->activityQuarter = $this->getActivityQuarterFilter();
    }

    function executeGetHolidaysDays(sfWebRequest $request)
    {
        $currDate = date("Y-m", $request->getParameter('currentDate'));
        $days = array();

        $dates = CalendarTable::getInstance()->createQuery()->where('start_date LIKE ?', $currDate . '%')->execute();
        foreach ($dates as $date) {
            $firstDay = date("d", strtotime($date->getStartDate()));
            $endDay = date("d", strtotime($date->getEndDate()));

            $currDay = intval($firstDay);
            $days[] = date("Ymd", strtotime($date->getStartDate()));
            $dayIndex = 1;

            while ($currDay < $endDay) {
                $currDay++;
                $days[] = date("Ymd", strtotime('+' . $dayIndex . ' days ' . $date->getStartDate()));

                $dayIndex++;
            }
        }

        return $this->sendJson(array('days' => $days));
    }

    /**
     * Show activities list for Service Clinic
     * @param sfWebRequest $request
     */
    public function executeServiceClinicStatsShow(sfWebRequest $request)
    {
        $builder = new ActivityExtendedStatisticsBuilder();
        $builder->buildActivitiesStats();

        $this->stats = $builder->getActivitiesStats();
    }

    /**
     * Export data with selected params
     * @param sfWebRequest $request
     * @return NONE
     */
    public function executeServiceClinicStatsExport(sfWebRequest $request)
    {
        $url = ActivityExtendedStatisticsBuilder::makeExportFile($request);
        echo $url;

        return sfView::NONE;
    }

    public function executeDownloadFile(sfWebRequest $request)
    {
        $id = $request->getParameter('id');

        $file = ActivityFileTable::getInstance()->find($id);
        if ($file) {
            $filePath = sfConfig::get('app_activities_upload_path') . '/file/' . $file->getFile();

            $file_download_result = F::downloadFile($filePath, $file->getFile());
            if (empty($file_download_result)) {
                $this->getResponse()->setContentType('application/json');
                $this->getResponse()->setContent(json_encode(array('success' => false, 'message' => 'Файл не найден')));
            } else {
                $file_download_result != 'success' ? $this->redirect($file_download_result) : '';
            }
        } else {

            $this->getResponse()->setContentType('application/json');
            $this->getResponse()->setContent(json_encode(array('success' => false, 'message' => 'Файл не найден')));
        }

        return sfView::NONE;
    }

    private function checkAllowToEdit()
    {
        $this->allow_to_edit_fields = true;
        $this->allow_to_cancel = false;
        $this->disable_importer = false;

        if ($this->current_q != 0) {
            /*Если статистика не заполнена и нет данных в БД, разрешаем заполнение статистики и ее сохранение*/
            if ( ActivityFieldsValuesTable::getInstance()->createQuery('afv')
                    ->innerJoin('afv.ActivityFields af')
                    ->where('afv.dealer_id = ? and afv.q = ? and afv.year = ?', array($this->getUser()->getAuthUser()->getDealer()->getId(), $this->current_q, $this->current_year))
                    ->andWhere('af.activity_id = ?', $this->activity->getId())
                    ->count() == 0
            ) {
                $this->allow_to_cancel = false;
                $this->allow_to_edit_fields = true;
            } else {

                $q = 'q' . $this->current_q;
                $stat_item = ActivityDealerStaticticStatusTable::getInstance()->createQuery()
                    ->select('ignore_q1_statistic, ignore_q2_statistic, ignore_q3_statistic, ignore_q4_statistic')
                    ->where('dealer_id = ? and activity_id = ? and stat_type = ? and ' . $q . ' != ? and year = ?',
                        array
                        (
                            $this->getUser()->getAuthUser()->getDealer()->getId(),
                            $this->activity->getId(),
                            Activity::ACTIVITY_STATISTIC_TYPE_SIMPLE,
                            0,
                            $this->year
                        )
                    )
                    ->fetchOne();

                if ($stat_item && !$stat_item->getIgnoreStatisticStatus($this->current_q)) {
                    $this->allow_to_edit_fields = false;
                }

                if ($this->getUser()->getAuthUser()->isSuperAdmin() && !$this->allow_to_edit_fields) {
                    $this->allow_to_cancel = true;
                }

            }
        }
    }

    private function getActivityAndQuarterAndYear(sfWebRequest $request) {
        $this->outputFilterByYear();
        $this->outputFilterByQuarter();

        $this->activity = $this->getActivity($request);
    }

    /**
     * Проверка статистики активности на возможность согалосвоания / отклонения для администраторов
     * Возможность редактироваия данных для дилеров если статистика активности не на проверке у администрации
     * @param sfWebRequest $request
     */
    private function preCheckStatisticStatus(sfWebRequest $request) {
        $this->getActivityAndQuarterAndYear($request);

        $this->checkAllowToEdit();
        if ($this->activity->isVideoRecordStatisticsActive()) {
            $statistic = $this->activity->getActivityVideoStatistics()->getFirst();

            if ($statistic && $statistic->getNotUsingImporter()) {
                $this->allow_to_edit = true;
                $this->allow_to_edit_fields = true;
                $this->allow_to_cancel = false;
                $this->disable_importer = true;
            }
        }
    }

    /*******
    **
     *
     *
     *
    */
    public function executeCancelStatisticData(sfWebRequest $request)
    {
        $q = 'q' . $request->getParameter('quarter');
        $activity = $request->getParameter('activity');
        $year = $request->getParameter('year');

        if (is_null($year)) {
            $year = D::getYear(date('Y-m-d'));
        }

        $stat_item = ActivityDealerStaticticStatusTable::getInstance()
            ->createQuery()
            ->where('dealer_id = ? and activity_id = ? and stat_type = ? and ' . $q . ' != ? and year = ?',
                array
                (
                    $this->getUser()->getAuthUser()->getDealer()->getId(),
                    $activity,
                    Activity::ACTIVITY_STATISTIC_TYPE_SIMPLE,
                    0,
                    $year
                )
            )
            ->fetchOne();

        if ($stat_item) {
            $stat_item->setComplete(false);
            $stat_item->setIgnoreStatisticStatus(true, $request->getParameter('quarter'));
            $stat_item->save();
        }

        return $this->sendJson(array('success' => true));
    }

    function executeCheckAllowToEditCancelStatData(sfWebRequest $request)
    {
        return $this->sendJson($this->checkAllowToEditExtendedStat($request));
    }

    private function checkAllowToEditExtendedStat(sfWebRequest $request)
    {
        $item_exit = ActivityDealerStaticticStatusTable::getInstance()->createQuery()->where('dealer_id = ? and concept_id = ?',
            array
            (
                $this->getUser()->getAuthUser()->getDealer()->getId(),
                $request->getParameter('concept_id'),
            )
        )->fetchOne();

        $allow_to_edit = true;

        if ($item_exit) {
            if (is_null($this->current_q)) {
                $this->current_q = $this->getUser()->getAttribute('current_q', 0, self::FILTER_Q_NAMESPACE);
            }

            $allow_to_edit = !$item_exit->getIgnoreStatisticStatus($this->current_q) ? false : true;
            $allow_to_cancel = $this->getUser()->getAuthUser()->isSuperAdmin() && !$allow_to_edit;
        } else {
            $allow_to_cancel = false;
        }

        return array('allow_to_edit' => $allow_to_edit, 'allow_to_cancel' => $allow_to_cancel);
    }

    function executeChangeExtendedStatsToImporter(sfWebRequest $request)
    {
        $this->activity = $this->getActivity($request);
        $result = ActivityExtendedStatisticFields::saveData($request, $this->getUser(), $_FILES, $this->activity);

        return $this->sendJson($result, 'activity_extended_statistic.onSaveDataCompleted');
    }

    function executeCancelExtendedStatisticData(sfWebRequest $request)
    {
        $stat_item = ActivityDealerStaticticStatusTable::getInstance()->createQuery()->where('dealer_id = ? and concept_id = ?',
            array
            (
                $this->getUser()->getAuthUser()->getDealer()->getId(),
                $request->getParameter('concept_id'),
            )
        )->fetchOne();

        if ($stat_item) {
            if (is_null($this->current_q)) {
                $this->current_q = $this->getUser()->getAttribute('current_q', 0, self::FILTER_Q_NAMESPACE);
            }

            $stat_item->setIgnoreStatisticStatus(true, $this->current_q);
            $stat_item->save();

            //Если передаем при отмене индекс шага, отменяем принятые данные
            $step_status_id = intval($request->getParameter('step_status_id'));
            if (!empty($step_status_id) && $step_status_id > 0) {
                $step_status = ActivityExtendedStatisticStepStatusTable::getInstance()->createQuery()->where('id = ?', $step_status_id)->fetchOne();
                if ($step_status) {
                    $step_status->setStatus(false);
                    $step_status->save();
                }
            }

            return $this->sendJson(array('success' => true, 'step_id' => intval($request->getParameter('step_id', 0))));
        }

        return $this->sendJson(array('success' => false));
    }

    public function executeOnSaveSimpleActivityStatistic(sfWebRequest $request)
    {
        return $this->sendJson($this->saveVideoRecordStatisticData($request, false), 'activity_simple_statistic.onSaveDataCompleted');
    }

    public function executeOnSaveVideoRecordStatisticData(sfWebRequest $request)
    {
        return $this->sendJson($this->saveVideoRecordStatisticData($request, false), 'activity_video_record_statistic.onSaveDataCompleted');
    }

    public function executeOnSaveImporterVideoRecordStatisticData(sfWebRequest $request)
    {
        return $this->sendJson($this->saveVideoRecordStatisticData($request, true), 'activity_video_record_statistic.onSaveImporterDataCompleted');
    }

    private function saveVideoRecordStatisticData(sfWebRequest $request, $to_importer = false)
    {
        $this->activity = $this->getActivity($request);

        $result = ActivityStatisticCheckFactory::getInstance($this->activity)->save($request, $this->getUser(), $_FILES, $to_importer);
        //$result = ActivityFields::saveData($request, $this->getUser(), $_FILES, $to_importer, $this->activity);

        $result['hide_data'] = $to_importer;

        return $result;
    }

    /*Activity efficiency*/
    public function executeEfficiency(sfWebRequest $request)
    {
        $this->activity = $this->getActivity($request);

        $efficiency = new ActivityCalculateEfficiencyUtils($this->activity, $this->getUser()->getAuthUser());
        $this->efficiency_result = $efficiency->getResult();

        $this->outputModelsQuarters($request);
        $this->outputFilterByYear();
        $this->outputFilterByQuarter();
    }


    /***
     * @param sfWebRequest $request
     */
    public function executeEfficiencyInfo(sfWebRequest $request)
    {
        $this->outputActivityFilter($request);
        $this->outputActivityQuarterFilter($request);

        $this->builder = new ActivitiesEfficiencyDealersStatistic(
            array
            (
                'year' => date('Y'),
                'quarter' => $this->activityQuarter
            ),
            $this->activity,
            $this->getUser()->getAuthUser());
    }

    public function executeCheckActivityEfficiency(sfWebRequest $request)
    {
        $activity = $this->getActivity($request);

        $efficiency = new ActivityCalculateEfficiencyUtils($activity, $this->getUser()->getAuthUser());
        $efficiency_result = $efficiency->getResult();

        $is_effective = true;
        foreach ($efficiency_result as $key => $data) {
            if ($data['formula']->isEfficiencyFormula()) {
                if (is_null($data['value']) || $data['value'] <= 0 || empty($data['value'])) {
                    $is_effective = false;
                    break;
                }
            }
        }

        return $this->sendJson(array('is_effective' => $is_effective));
    }

    public function executeExportActivityEfficiencyData(sfWebRequest $request)
    {
        $this->activity = $this->getActivity($request);

        $pExcel = new PHPExcel();
        $pExcel->setActiveSheetIndex(0);
        $aSheet = $pExcel->getActiveSheet();
        $aSheet->setTitle(Utils::trim_text('Экспорт данных', 30));

        $builder = new ActivitiesEfficiencyDealersStatistic(
            array
            (
                'year' => date('Y'),
                'quarter' => $this->activityQuarter
            ),
            $this->activity,
            $this->getUser()->getAuthUser());

        $builder->build();
        $results = $builder->getResults();

        $headers = array();
        $headers[] = "Дилер";

        foreach ($results['formulas'] as $formula) {
            $headers[] = $formula->getName();
        }

        $headers[] = "Эффективность";

        $boldLeftFont = array(
            'font' => array(
                'name' => 'Arial Cyr',
                'size' => '10',
                'bold' => true
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
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
        $center = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP
            )
        );

        $aSheet->getStyle('A1:G1')->applyFromArray($boldLeftFont);
        $aSheet->getStyle('A4:M4')->applyFromArray($boldFont);
        $aSheet->getStyle('B:M')->applyFromArray($center);

        $column = 0;
        $tCount = 1;
        foreach ($headers as $head) {
            $aSheet->setCellValueByColumnAndRow($column++, 1, $head);
            $tCount++;
        }

        $aSheet->getRowDimension('1')->setRowHeight(35);
        $aSheet->getStyle('A1:M1')->getAlignment()->setWrapText(true);

        $cellIterator = $aSheet->getRowIterator()->current()->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);
        /** @var PHPExcel_Cell $cell */
        foreach ($cellIterator as $cell) {
            $aSheet->getColumnDimension($cell->getColumn())->setWidth(35);
        }

        $fillColor = 'ececec';
        $row = 2;

        foreach ($results['results'] as $key => $result_data) {
            $column = 0;
            $aSheet->getStyle('A' . $row . ':M' . $row)->applyFromArray($boldLeftFont);
            $aSheet->getRowDimension($row)->setRowHeight(25);

            $dealer = DealerTable::getInstance()->find($key);

            if ($row % 2 == 0) {
                $aSheet->getStyle('A' . $row . ':M' . $row)
                    ->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB($fillColor);
            }

            $aSheet->setCellValueByColumnAndRow($column++, $row, sprintf('[%s] %s', $dealer->getShortNumber(), $dealer->getName()));
            $efficiency = false;
            $efficiency_ind = 0;
            foreach ($results['formulas'] as $formula) {
                if ($efficiency_ind == 0) {
                    $efficiency = $result_data[$formula->getId()] > 0 ? true : false;
                    $efficiency_ind++;
                }

                $aSheet->setCellValueByColumnAndRow($column++, $row, Utils::numberFormat($result_data[$formula->getId()]));
            }

            if ($efficiency) {
                Utils::drawExcelImage('efficiency/hand_up.png', 'F' . $row, $pExcel, 50);
            } else {
                Utils::drawExcelImage('efficiency/hand_down.png', 'F' . $row, $pExcel, 50);
            }

            $row++;
        }

        $save_file_name = 'activity_efficiency_data.xls';
        $objWriter = new PHPExcel_Writer_Excel5($pExcel);
        $objWriter->save(sfConfig::get('sf_root_dir') . '/www/uploads/' . $save_file_name);

        return $this->sendJson(array('success' => true, 'file_url' => sfConfig::get('site_url') . '/uploads/' . $save_file_name));
    }

    /**
     *
     * @param sfWebRequest $request
     * @return string
     */
    public function executeDownloadFileField(sfWebRequest $request)
    {
        $id = $request->getParameter('id');
        $type = $request->getParameter('type');

        if (!empty($type) && $type == 'extended') {
            $field_value = ActivityExtendedStatisticFieldsDataTable::getInstance()->find($id);
            $func_value_name = 'getValue';
        } else {
            $field_value = ActivityFieldsValuesTable::getInstance()->find($id);
            $func_value_name = 'getVal';
        }

        if ($field_value) {
            $filePath = sfConfig::get('app_activities_upload_path') . '/module/statistics/' . $field_value->$func_value_name();
            $file_name = $field_value->$func_value_name();

            if (!F::downloadFile($filePath, $file_name)) {
                $this->getResponse()->setContentType('application/json');
                $this->getResponse()->setContent(json_encode(array('success' => false, 'message' => 'Файл не найден.')));
            }
        }

        $this->getResponse()->setContentType('application/json');
        $this->getResponse()->setContent(json_encode(array('success' => false, 'message' => 'Файл не найден.')));

        return sfView::NONE;
    }
}
