<?php

/**
 * Description of ActivityExtendedStatisticsBuilder
 *
 *
 */
class ActivityExtendedStatisticsBuilder
{
    private $_stats = array();
    private $_statsDealers = array();
    private $_activitiesStats = array();

    private $_filter = null;

    function __construct($filter = null)
    {
        //$this->build();
        $this->_filter = $filter;
    }

    private function getFieldsData($dealer, $fieldType = null, $limit = -1, $asArray = false)
    {
        $query = ActivityExtendedStatisticFieldsDataTable::getInstance()
            ->createQuery('f')
            ->leftJoin('f.Field pf')
            //->where('f.value != ?', array(''))
            ->andWhere('f.dealer_id = ?', $dealer->getId())
            ->orderBy('pf.order ASC');

        if (!is_null($fieldType)) {
            $query->andWhere('pf.value_type = ?', $fieldType);
        }

        if (!empty($this->_filter) && isset($this->_filter['activity'])) {
            $query->andWhere('pf.activity_id = ?', $this->_filter['activity']);
        }

        /*if (!empty($this->_filter) && isset($this->_filter['quarter'])) {
            $query->andWhere('quarter(f.updated_at) = ?', $this->_filter['quarter']);
        }*/

        if ($asArray && $limit != -1) {
            return $query->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);
        }

        return $query->execute();
    }

    public function build()
    {
        $result = array();

        $conceptInd = 1;
        $dealers = DealerTable::getInstance()->createQuery()->where('status = ?', 1)->execute();

        foreach ($dealers as $dealer) {
            $field = $this->getFieldsData($dealer, 'date', 1, true);
            if (!$field) {
                continue;
            }

            $conceptQuery = AgreementModelTable::getInstance()
                ->createQuery('am')
                ->innerJoin('am.AgreementModelSettings ams')
                ->where('model_type_id = ?',10)
                //->andWhere('ams.certificate_date_to >= ?', date('Y-m-d'))
                ->andWhere('am.dealer_id = ?', $dealer->getId())
                ->orderBy('ams.id ASC');

            if (!empty($this->_filter) && isset($this->_filter['activity'])) {
                $conceptQuery->andWhere('am.activity_id = ?', $this->_filter['activity']);
            }

            $concepts = $conceptQuery->execute();
            foreach($concepts as $concept) {
                $quarter = D::getQuarter($concept->getCalcDate());

                $createdAt = explode(' ', $field['created_at']);
                $date = $concept->getAgreementModelSettings()->getCertificateDateTo();

                if($this->_filter && isset($this->_filter['quarter'])) {
                    if($quarter != $this->_filter['quarter']) {
                        continue;
                    }
                }

                if($this->queryFilledData($concept->getId(), $dealer->getId(), true) == 0) {
                    continue;
                }

                $items = $this->queryFilledData($concept->getId(), $dealer->getId());
                foreach ($items as $item) {

                    $itemValue = $item->getField()->getValueType() == ActivityExtendedStatisticFields::FIELD_TYPE_CALC ?
                        $item->getField()->calculateValue($item->getDealerId(), $createdAt[0]) :
                        $item->getValue();

                    $tempVal = $item->getValue();
                    if (empty($tempVal) && $item->getField()->getValueType() != ActivityExtendedStatisticFields::FIELD_TYPE_CALC) {
                        $itemValue = 0;
                    }

                    $conceptId = $concept ? 'concept-' . $concept->getId() : $conceptInd++;
                    $result[$conceptId][$item->getFieldId()]['data'] =
                        array(
                            'value' => $itemValue,
                            'name' => $item->getField()->getHeader(),
                            'dealerId' => $dealer->getId(),
                            'dealerName' => $item->getDealer()->getName(),
                            'dealerNumber' => $item->getDealer()->getNumber(),
                            'concept' => isset($date) ? $date : ''
                        );
                }
            }
        }

        $this->_stats = $result;
    }

    private function queryFilledData($conceptId, $dealerId, $count = false) {
        $query = ActivityExtendedStatisticFieldsDataTable::getInstance()
            ->createQuery('f')
            ->leftJoin('f.Field pf')
            ->andWhere('f.dealer_id = ? and f.concept_id = ?', array($dealerId, $conceptId))
            ->orderBy('pf.order ASC');

        if (!empty($this->_filter) && isset($this->_filter['activity'])) {
            $query->andWhere('pf.activity_id = ?', $this->_filter['activity']);
        }

        if($count) {
            $query->andWhere('pf.value_type != ?', 'date')
                ->andWhere('f.value != ?', '');

            return $query->count();
        }

        return $query->execute();
    }

    public function buildDealerStats()
    {
        $result = array();

        $items = ActivityExtendedStatisticFieldsDataTable::getInstance()
            ->createQuery('f')
            ->leftJoin('f.Field pf')
            ->orderBy('pf.order ASC')->execute();
        foreach ($items as $item) {
            $val = $item->getValue();

            if (!array_key_exists($item->getDealerId(), $result)) {
                $result[$item->getDealerId()] = array
                (
                    'totalFillValues' => 0,
                    'dealerName' => $item->getDealer()->getName(),
                    'dealerNumber' => $item->getDealer()->getNumber(),
                    'percentOfComplete' => 0
                );
            }

            $result[$item->getDealerId()]['totalFillValues'] = !empty($val) && $val != 0 ?
                $result[$item->getDealerId()]['totalFillValues'] += 1 :
                $result[$item->getDealerId()]['totalFillValues'];
        }


        $fieldsCount = ActivityExtendedStatisticFieldsTable::getInstance()->createQuery()->count();
        foreach ($result as $dealerId => $data) {
            $result[$dealerId]['percentOfComplete'] = round($data['totalFillValues'] * 100 / $fieldsCount, 0);
        }

        $this->_statsDealers = $result;
    }

    public function buildActivitiesStats()
    {
        $result = array();

        $activities = ActivityTable::getInstance()->createQuery()->where('allow_extended_statistic = ? and allow_certificate = ?',
            array
            (
                true,
                true
            )
        )
            ->orderBy('id ASC')
            ->execute();

        foreach ($activities as $activity) {
            $quarters = ActivityQuartersTable::getInstance()->createQuery()->where('activity_id = ?', $activity->getId())->execute();

            $result[$activity->getId()]['activity'] = array('name' => $activity->getName(), 'id' => $activity->getId());
            foreach ($quarters as $quarter) {
                $fieldData = ActivityExtendedStatisticFieldsDataTable::getInstance()
                    ->createQuery('fd')
                    ->leftJoin('fd.Field f')
                    ->where('f.activity_id = ? and f.value_type = ? and quarter(fd.updated_at) = ?',
                        array
                        (
                            $activity->getId(),
                            ActivityExtendedStatisticFields::FIELD_TYPE_DATE,
                            $quarter->getQuarter()->getQuarter()
                        )
                    )
                    ->orderBy('updated_at ASC')
                    ->execute();

                $tHaveConcept = 0;
                $tDontHaveConcept = 0;
                foreach ($fieldData as $data) {
                    if ($data->getConceptId() != 0) {
                        $tHaveConcept++;
                    } else {
                        $tDontHaveConcept++;
                    }
                }

                $result[$activity->getId()]['data'][$quarter->getQuarter()->getQuarter()] =
                    array(
                        'data' =>
                            array(
                                'haveConcept' => $tHaveConcept,
                                'dontHaveConcept' => $tDontHaveConcept
                            )
                    );
            }

        }

        $this->_activitiesStats = $result;
    }

    public static function makeExportFile(sfWebRequest $request)
    {
        $pExcel = new PHPExcel();
        $pExcel->setActiveSheetIndex(0);
        $aSheet = $pExcel->getActiveSheet();
        $aSheet->setTitle('Расширенная статистика');


        $headers = array();
        $headers[] = 'Дилер (название и номер)';
        $headers[] = 'Срок действия сертификата';

        $fields = ActivityExtendedStatisticFieldsTable::getInstance()->createQuery()->orderBy('order ASC')->execute();
        foreach ($fields as $field)
            $headers[] = $field->getHeader();

        $boldFont = array(
            'font' => array(
                'name' => 'Arial Cyr',
                'size' => '10',
                'bold' => true
            )
        );
        $center = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP
            )
        );

        $left = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP
            )
        );

        $column = 0;
        foreach ($headers as $head) {
            $aSheet->setCellValueByColumnAndRow($column++, 1, $head);
        }

        $aSheet->getStyle('2:' . count($headers))->applyFromArray($center);

        $aSheet->getStyle('A1:A' . count($headers))->applyFromArray($left);
        $aSheet->getStyle('A1:B' . count($headers))->applyFromArray($boldFont);

        $cellIterator = $aSheet->getRowIterator()->current()->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(true);

        foreach ($cellIterator as $cell) {
            $aSheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
        }

        $row = 2;
        $stats = new ActivityExtendedStatisticsBuilder(array('quarter' => $request->getParameter('quarter'), 'activity' => $request->getParameter('activity')));
        $stats->build();

        foreach ($stats->getStats() as $conceptId => $stat) {
            $column = 0;

            foreach ($fields as $field) {
                if (array_key_exists($field->getId(), $stat)) {
                    $dealerId = $stat[$field->getId()]['data']['dealerId'];

                    $dealer = DealerTable::getInstance()->find($dealerId);
                    $val = $stat[$field->getId()]['data']['value'];
                    if ($column == 0) {
                        $dealerD = sprintf('[%s] %s', $dealer->getNumber(), $dealer->getName());

                        $aSheet->setCellValueByColumnAndRow($column++, $row, $dealerD);

                        if (is_numeric($conceptId)) {
                            $conceptData = 'Нет';
                        } else {
                            $conceptData = $stat[$field->getId()]['data']['concept'];
                        }
                        $aSheet->setCellValueByColumnAndRow($column++, $row, $conceptData);
                    }

                    $aSheet->setCellValueByColumnAndRow($column++, $row, $val);
                }
            }

            $row++;
        }
        $aSheet->freezePane('C2');
        //exit;

        $objWriter = new PHPExcel_Writer_Excel5($pExcel);
        $objWriter->save(sfConfig::get('sf_root_dir') . '/www/uploads/extended_stats.xls');

        return 'http://dm.vw-servicepool.ru/uploads/extended_stats.xls';
    }

    public function getStats()
    {
        return $this->_stats;
    }

    public function getDealerStats()
    {
        return $this->_statsDealers;
    }

    public function getActivitiesStats()
    {
        return $this->_activitiesStats;
    }
}
