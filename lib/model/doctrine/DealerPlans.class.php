<?php

/**
 * DealerPlans
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    Servicepool2.0
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class DealerPlans extends BaseDealerPlans
{
    public static function readImporterFile($file)
    {
        if (!empty($file)) {
            $uploadfile = '../apps/frontend/modules/mailing/load_files/' . time() . '-' . basename($file['data_file']['name']);

            if (move_uploaded_file($file['data_file']['tmp_name'], $uploadfile)) {
                $objPHPExcel = PHPExcel_IOFactory::load($uploadfile);
                $objPHPExcel->setActiveSheetIndex(0);
                $aSheet = $objPHPExcel->getActiveSheet();
                $array = array();
                foreach ($aSheet->getRowIterator() as $key => $row) {
                    $cellIterator = $row->getCellIterator(); // Ячейки строки
                    $string_item = array();
                    foreach ($cellIterator as $cell) {
                        //заносим значения ячеек одной строки в отдельный массив
                        array_push($string_item, $cell->getValue());
                    }
                    //заносим массив со значениями ячеек отдельной строки в "общий массв строк"
                    if ($key !== 0 && $key !== 1) {
                        array_push($array, $string_item);
                    }
                }
                unlink($uploadfile);
                return $array;
            }
        } else return array();
    }

    public function addPlan($item)
    {
        $dealerNumber = '93500' . mb_substr($item[0], -3, 3);
        $plan = DealerPlansTable::getInstance()->findOneByDealerId($dealerNumber);
        if ($plan) {
            $added_date = $plan->getAddedDate();
            $date = new DateTime($added_date);

            if ($date->format('Y') == date('Y') && $date->format('m') == date('m') && $date->format('d') <= 22) {
                $this->addPlanItem($item, $plan);
                return true;
            }
        }
        $this->addPlanItem($item);
        return true;
    }

    public function addPlanItem($item, $model = null)
    {
        if (!$model)
            $model = new DealerPlans();

        $dateTime = new DateTime();
        $dateTime->modify('-1 month');

        $model->setDealerId('93500' . mb_substr($item[0], -3, 3));
        $model->setName(trim($item[1]));
        $model->setPlan1(round($item[2]));
        $model->setPlan2(round($item[3]));
        $model->setAddedDate($dateTime->format('Y-m-d'));
        $model->save();
    }

    public static function exportStatsToXLS($data, $year = null)
    {
        $pExcel = new PHPExcel();
        $pExcel->setActiveSheetIndex(0);
        $aSheet = $pExcel->getActiveSheet();

        $aSheet->setTitle('Cтатистика по клиентам');

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
            ),
            'font' => array(
                'name' => 'Arial Cyr',
                'size' => '9',
                'bold' => false
            )
        );

        $left = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP
            )
        );

        $right = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP
            )
        );

        $column = 0;
        $row = 2;

        $aSheet->setCellValue("A1", 'Номер');
        $aSheet->getStyle('A1')->applyFromArray($right);

        $aSheet->setCellValue("B1", 'Название дилера');
        $aSheet->setCellValue("C1", 'Янв.');
        $aSheet->setCellValue("D1", 'Фев.');
        $aSheet->setCellValue("E1", 'Мар.');
        $aSheet->setCellValue("F1", '1Кв.');
        $aSheet->getStyle('F1')->applyFromArray($boldFont);

        $aSheet->setCellValue("G1", 'Апр.');
        $aSheet->setCellValue("H1", 'Май.');
        $aSheet->setCellValue("I1", 'Июн.');
        $aSheet->setCellValue("J1", '2Кв.');
        $aSheet->getStyle('J1')->applyFromArray($boldFont);

        $aSheet->setCellValue("K1", 'Июл.');
        $aSheet->setCellValue("L1", 'Авг.');
        $aSheet->setCellValue("M1", 'Сен.');
        $aSheet->setCellValue("N1", '3Кв.');
        $aSheet->getStyle('N1')->applyFromArray($boldFont);

        $aSheet->setCellValue("O1", 'Окт.');
        $aSheet->setCellValue("P1", 'Ноя.');
        $aSheet->setCellValue("Q1", 'Дек.');
        $aSheet->setCellValue("R1", '4Кв.');
        $aSheet->getStyle('R1')->applyFromArray($boldFont);


        $aSheet->getStyle('2:' . (count($data) + 1))->applyFromArray($center);
        $aSheet->getStyle('A1:B' . (count($data) + 1))->applyFromArray($boldFont);

        $cellIterator = $aSheet->getRowIterator()->current()->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(true);

        foreach ($cellIterator as $cell) {
            $aSheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
        }

        foreach($data as $d_key => $dealer) {
            array_unshift($dealer, mb_substr($d_key, -3, 3));
            $dealer = array_values($dealer);
            foreach($dealer as $key => $value) {
                $aSheet->setCellValueByColumnAndRow($key, $row, ($key > 1 ? $value.'%' : $value));
            }
            $row++;
        }

        // Выводим HTTP-заголовки
        header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
        header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
        header ( "Cache-Control: no-cache, must-revalidate" );
        header ( "Pragma: no-cache" );
        header ( "Content-type: application/vnd.ms-excel" );
        header ( "Content-Disposition: attachment; filename=mail_stat.xls" );

        // Выводим содержимое файла
        $objWriter = new PHPExcel_Writer_Excel5($pExcel);
        $objWriter->save('php://output');

    }
}
