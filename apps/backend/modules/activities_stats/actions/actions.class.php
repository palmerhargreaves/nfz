<?php

include(sfConfig::get('sf_root_dir').'/lib/PHPExcel.php');
include(sfConfig::get('sf_root_dir').'/lib/PHPExcel/Cell.php');
include(sfConfig::get('sf_root_dir').'/lib/PHPExcel/Writer/Excel5.php');

/**
 * activities_stats actions.
 *
 * @package    Servicepool2.0
 * @subpackage deleted_models
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class activities_statsActions extends sfActions
{
	
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
    function executeIndex(sfWebRequest $request)
    {
        $this->activities = ActivityTable::getInstance()
            ->createQuery('a')
            ->innerJoin('a.ActivityField af')
                ->orderBy('a.id DESC')
            ->execute();
    }
  
    function executeShow(sfWebRequest $request)
    {
        $this->setTemplate('index');
    }

    public function executeExportData(sfWebRequest $request)
    {
        $activity = ActivityTable::getInstance()->find($request->getParameter('activityId'));
        $this->builder = new ActivityStatisticFieldsBuilder(
            array
            (
                'year' => date('Y'),
                'quarter' => -1
            ),
            $activity,
            $this->getUser());

        $stats = $this->builder->getStat();


        $pExcel = new PHPExcel();
        $pExcel->setActiveSheetIndex(0);
        $aSheet = $pExcel->getActiveSheet();
        $aSheet->setTitle($activity->getName());

        $headers = array();
        $headers[] = "Статистика по активностям";
        foreach($stats['fields'] as $field) {
            $headers[] = $field->getName();
        }

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

        $aSheet->getStyle('A1:G1')->applyFromArray($boldFont);
        $aSheet->getStyle('B:G')->applyFromArray($center);

        $aSheet->setCellValueByColumnAndRow(0, 1, $activity->getName());

        $column = 0;
        $tCount = 1;
        foreach($headers as $head) {
            $aSheet->setCellValueByColumnAndRow($column++, 4, $head);
            $tCount++;
        }

        $sheet = $pExcel->getActiveSheet();
        $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells( true );
        /** @var PHPExcel_Cell $cell */
        foreach( $cellIterator as $cell ) {
            $sheet->getColumnDimension( $cell->getColumn() )->setAutoSize( true );
        }

        $row = 6;
        foreach($stats['dealers'] as $qKey => $quarters)
        {
            foreach($quarters as $dKey => $dealers)
            {
                $column = 0;
                $dealer = $dealers['dealer'];

                if(!$dealer) {
                    continue;
                }

                $aSheet->setCellValueByColumnAndRow($column++, $row, sprintf('%s %s', $dealer->getShortNumber(), $dealer->getName()));
                foreach ($dealers['values'] as $item)
                {
                    $itemField = ActivityFieldsTable::getInstance()->createQuery()->select('type, content')->where('id = ?', $item['field_id'])->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);

                    $val = $item['val'];
                    if($itemField['content'] == "price") {
                        $val = number_format($val, 0, '.', ' ') . ' руб.';
                    }

                    $aSheet->setCellValueByColumnAndRow($column++, $row, $val);
                }
                $row++;
            }
        }

        $objWriter = new PHPExcel_Writer_Excel5($pExcel);
        $objWriter->save(sfConfig::get('sf_root_dir').'/www/uploads/activity_stats.xls');

        return sfView::NONE;
    }
}
