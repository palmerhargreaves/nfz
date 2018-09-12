<?php

include(sfConfig::get('sf_root_dir').'/lib/PHPExcel.php');
include(sfConfig::get('sf_root_dir').'/lib/PHPExcel/Cell.php');
include(sfConfig::get('sf_root_dir').'/lib/PHPExcel/IOFactory.php');

/**
 * dealers_budges_files actions.
 *
 * @package    Servicepool2.0
 * @subpackage dealers_budges_files
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class dealers_budgets_filesActions extends sfActions
{
    function executeIndex(sfWebRequest $request)
    {
        $this->files = DealersBudgetsFilesTable::getInstance()->createQuery()->orderBy('id DESC')->execute();
    }

    function executeUploadFile(sfWebRequest $request) {
        $uploadTo = sfConfig::get('sf_upload_dir').DIRECTORY_SEPARATOR.'dealers_budgets'.DIRECTORY_SEPARATOR;

        try {
            foreach ($request->getFiles() as $file) {
                $fileName = uniqid("dealers_"). '_' . $file['name'];
                if (move_uploaded_file($file['tmp_name'], $uploadTo . $fileName)) {
                    $this->parseUploadedFile($uploadTo.$fileName);
                }
            }
        }
        catch(Exception $ex) {
            $this->getUser()->setFlash('error', $ex->getMessage());
        }

        exit;
    }

    private function parseUploadedFile($fileName)
    {
        $currentYear = date('Y');

        $objPHPExcel = PHPExcel_IOFactory::load($fileName);
        $objPHPExcel->setActiveSheetIndex(0);
        $aSheet = $objPHPExcel->getActiveSheet();

        $items = array();
        foreach ($aSheet->getRowIterator() as $key => $row) {
            if($key == 1) {
                continue;
            }

            $cellIterator = $row->getCellIterator();

            $rowData = array();
            foreach ($cellIterator as $cell) {
                $rowData[] = $cell->getValue();
            }

            $items[$key] = $rowData;
        }

        $inserted = 0;
        $updated = 0;
        foreach($items as $key => $item) {
            $isInserted = false;
            $isUpdated = false;

            $dealerId = $item[0];

            $qData = array();
            for ($i = 1; $i <= 4; $i++) {
                $qData[$i] = $item[$i];
            }

            $dealer = DealerTable::getInstance()
                ->createQuery()
                ->select('id')
                ->where('number LIKE ?', '%'.$dealerId.'%')
                    ->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);

            if(count($dealer) && isset($dealer['id'])) {
                foreach($qData as $qKey => $value) {
                    $budget = BudgetTable::getInstance()
                        ->createQuery()
                        ->where('dealer_id = ? and year = ? and quarter = ?',
                            array
                            (
                                $dealer['id'],
                                $currentYear,
                                $qKey
                            )
                        )
                        ->fetchOne();

                    if(!$budget) {
                        $budget = new Budget();

                        $budget->setDealerId($dealer['id']);
                        $budget->setYear($currentYear);
                        $budget->setQuarter($qKey);
                        $budget->setPlan(!empty($value) ? $value : 0);

                        $isInserted = true;
                    } else {
                        $budget->setPlan(!empty($value) ? $value : 0);
                        $isUpdated = true;
                    }

                    $budget->save();
                }
            }

            if($isInserted) {
                $inserted++;
            }

            if($isUpdated) {
                $updated++;
            }
        }

        $uploadStat = new DealersBudgetsFiles();

        $uploadStat->setTotalDealers($inserted + $updated);
        $uploadStat->setYear($currentYear);
        $uploadStat->setFileName(basename($fileName));
        $uploadStat->setStatus(true);
        $uploadStat->save();


        $this->getUser()->setFlash('success', sprintf("Файл успешно загружен.|Всего добавлено: %s|Всего обновлено: %s", $inserted, $updated));

        $this->redirect('/backend.php/dealers_budgets_files');
    }

}
