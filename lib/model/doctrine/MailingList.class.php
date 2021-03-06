<?php
require_once "/var/www/vwgroup/data/www/nfz.vw-servicepool.ru/apps/frontend/modules/mailing/lib/ApiConnector.class.php";

/**
 * MailingList
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    Servicepool2.0
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class MailingList extends BaseMailingList
{
    /**
     * @param $file
     * @return array|bool
     */
    public static function readDealerFile($file)
    {
        if (!empty($file) && $file['data_file']['type'] == 'application/vnd.ms-excel' || $file['data_file']['type'] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
            $file_date = new DateTime();
            $uploadfile = '../apps/frontend/modules/mailing/load_files/' . $file_date->format('d-m-Y H:i:s') . ' | ' . basename($file['data_file']['name']);

            if (move_uploaded_file($file['data_file']['tmp_name'], $uploadfile)) {

                return static::getXlsData($uploadfile);
//                return static::readFileLibXL($uploadfile); //Get data new version

            }
        } else return array();
    }

    /**
     * New version getting data
     * @param null $file_path
     * @return array
     */
    public static function readFileLibXL($file_path = null)
    {
        $data = array();

        if (empty($file_path))
            return $data;

        $excelBook = new ExcelBook();
        $excelBook->loadFile($file_path);


        $sheet = $excelBook->getSheet(0);
        $excelBook->Setlocale("en_US.UTF-8");


        for ($r = $sheet->firstRow(); $r <= $sheet->lastRow(); $r++) {
            $tmp = array();
            for ($c = $sheet->firstCol(); $c <= $sheet->lastCol(); $c++) {
                $value = $sheet->read($r, $c);

                if ($r !== 0 && $r !== 1) {
                    $tmp[] = trim($value);
                }

            }

            if (!empty($tmp)) {
//                $tmp = array_diff($tmp, array(''));
//                if (count($tmp) == 11 || count($tmp) == 13)
                    array_push($data, $tmp);
            }
        }
//
//        $count = 0;
//        foreach ($data as $d) {
//            if(isset($d[8]) && !empty($d[8]))
//                $count++;
//        }
        return $data;
    }

    /**
     * Old version getting data
     * @param null $file_path
     * @return array
     */
    public static function getXlsData($file_path = null)
    {
        $data = array();

        if (empty($file_path))
            return $data;

        $objPHPExcel = PHPExcel_IOFactory::load($file_path);
        $objPHPExcel->setActiveSheetIndex(0);
        $aSheet = $objPHPExcel->getActiveSheet();

        foreach ($aSheet->getRowIterator() as $key => $row) {
            $cellIterator = $row->getCellIterator(); // Ячейки строки
            $cellIterator->setIterateOnlyExistingCells(false); // Учитывать даже пустые строки

            $string_item = array();
            foreach ($cellIterator as $cell) {
                //заносим значения ячеек одной строки в отдельный массив
                $value = $cell->getValue();
//                if (!empty($value)) {
                array_push($string_item, $value);
//                }

            }

            //заносим массив со значениями ячеек отдельной строки в "общий массв строк"
            $string_item = array_diff($string_item, array(''));
            if ($key !== 1 && $key !== 2 && !empty($string_item)) {
                array_push($data, $string_item);
            }
        }
//echo '<pre>'. print_r($data, 1) .'</pre>'; die();
        return $data;
    }

    /**
     * Метод добавляет и удаляет данные из базы так же изменяет статистику для вывода в результатах загрузки файла.
     * @param $item
     * @param $result - link to $total_result
     * @param $dealer
     */
    public function validateItem($item, &$result, $dealer = null)
    {
        if (empty($item[0])) {
            self::logger('../log/mailing-errors.log', date('Y-m-d H:i:s') . " dealer number owner - " . $dealer->getNumber() . " - Unknown dealer number\n", FILE_APPEND);
            $result['total_incorrect']++;
            return false;
        }

        if (empty($item[1])) {
            self::logger('../log/mailing-errors.log', date('Y-m-d H:i:s') . " dealer number owner - " . $dealer->getNumber() . " - Unknown firstname\n", FILE_APPEND);
            $result['total_incorrect']++;
            return false;
        }

        if (empty($item[2])) {
            self::logger('../log/mailing-errors.log', date('Y-m-d H:i:s') . " dealer number owner - " . $dealer->getNumber() . " - Unknown lastname\n", FILE_APPEND);
            $result['total_incorrect']++;
            return false;
        }

        if (empty($item[3])) {
            self::logger('../log/mailing-errors.log', date('Y-m-d H:i:s') . " dealer number owner - " . $dealer->getNumber() . " - Unknown middlename\n", FILE_APPEND);
            $result['total_incorrect']++;
            return false;
        }

        if (empty($item[4])) {
            self::logger('../log/mailing-errors.log', date('Y-m-d H:i:s') . " dealer number owner - " . $dealer->getNumber() . " - Unknown gender\n", FILE_APPEND);
            $result['total_incorrect']++;
            return false;
        }

        $item[5] = trim($item[5]);
        if (empty($item[5]) && preg_match('/[а-я]+/i', $item[5])) {
            self::logger('../log/mailing-errors.log', date('Y-m-d H:i:s') . " dealer number - " . $dealer->getNumber() . " - Firm name '" . $item[5] . "' He was not added to the database\n", FILE_APPEND);
            $result['total_incorrect']++;
            return false;
        }
//
        $item[6] = trim($item[6]);
        if (empty($item[6]) && preg_match('/[а-я]+/i', $item[6])) {
            self::logger('../log/mailing-errors.log', date('Y-m-d H:i:s') . " dealer number - " . $dealer->getNumber() . " - OPF '" . $item[6] . "' He was not added to the database\n", FILE_APPEND);
            $result['total_incorrect']++;
            return false;
        }

        if (empty($item[8]) || !filter_var(trim($item[8]), FILTER_VALIDATE_EMAIL)) {
            self::logger('../log/mailing-errors.log', date('Y-m-d H:i:s') . " dealer number owner - " . $dealer->getNumber() . " - Email address '" . $item[8] . "' He was not added to the database\n", FILE_APPEND);
            $result['total_incorrect']++;
            return false;
        }

        $date = self::getDateValidation($item[9]);
        if (empty($date)) { // Проверка на корректность даты "Последнего обращения в сервисный центр"
            $result['date_error'] = 1;
            self::logger('../log/mailing-errors.log', date('Y-m-d H:i:s') . " | Dealer number owner - " . $dealer->getNumber() . ", date of the application - (" . $item[9] . ") - empty or INCORRECT! He was not added to the database\n", FILE_APPEND);
            $result['total_incorrect']++;
            return false;
        }

        $item[11] = trim($item[11]);
        if (empty($item[11]) && !self::vinValidate($item[11])) { // Валидация WIN номера автомобиля
            $result['vin_error'] = 1;
            self::logger('../log/mailing-errors.log', date('Y-m-d H:i:s') . " | Dealer number owner - " . $dealer->getNumber() . ", VIN number - (" . $item[11] . ") - empty or INCORRECT! He was not added to the database\n", FILE_APPEND);
            $result['total_incorrect']++;
            return false;
        }

        $item[12] = trim($item[12]);
        if (empty($item[12])) {
            $result['model_error'] = 1;
            self::logger('../log/mailing-errors.log', date('Y-m-d H:i:s') . " | Dealer number owner - " . $dealer->getNumber() . ", MODEL number - (" . $item[12] . ") - empty or INCORRECT! He was not added to the database\n", FILE_APPEND);
            $result['total_incorrect']++;
            return false;
        }

        return true;

        /** ///// */
//        if ($this->addClient($item, $dealer, $result)) {
//            $result['total_added']++;
//        } else $result['total_incorrect']++;
    }


    /**
     * @param $date
     * @return DateTime|null|string
     */
    public static function getDateValidation($date)
    {
        return 1;
        $date = trim($date);
        if (is_numeric($date)) {
            $date = new DateTime($date);
            return $date->format('d.m.Y');
        } elseif (!is_numeric(strtotime($date))) {
            return $date;
        }
        return null;
    }

    public static function checkDuplicate($item, $dealer, $quarter)
    {
        $email = null;
        if (filter_var(trim($item[8]), FILTER_VALIDATE_EMAIL)) {
            $email = trim($item[8]);
        } elseif (filter_var(trim($item[6]), FILTER_VALIDATE_EMAIL)) {
            $email = trim($item[6]);
        } else {
            return false;
        }

        return MailingListTable::getInstance()
            ->createQuery()
            ->where('email = ?', $email)
            ->andWhere('dealer_id = ?', $dealer->getNumber())
            ->andWhere('YEAR(added_date) = ?', date('Y'))
            ->andWhere('QUARTER(added_date) = ?', $quarter)
            ->count() > 0 ? true : false;
    }

    public function addClient($item, $dealer = null, &$result)
    {
        $model = new MailingList();
        $model->setDealerId($dealer->getNumber());

        $model->setFirstName($item[1]);
        $model->setLastName($item[2]);
        $model->setMiddleName($item[3]);
        $model->setFirmName($item[5]);
        $model->setOpf($item[6]);
        $model->setGender($item[4]);
        $model->setPhone($item[7]);
        $model->setEmail(trim($item[8]));
        $model->setLastVisitDate(trim($item[9]));

        $date = new DateTime();
        $date->modify('-1 month');
        $model->setLastUploadData($date->format('Y-m-d'));
        $model->setAddedDate($date->format('Y-m-d'));

        $model->setVin($item[11]);
        $model->setModel($item[12]);
        $model->save();

        $id = $model->getId();
        if (!empty($id)) {
            $result['total_added']++;
            self::addDataToAPI($dealer, $model, $id);
        }
        return true;
    }

    /**
     * Добавление данные в API
     * @param $dealer
     * @param $model
     */
    public static function addDataToAPI($dealer, $model, $id)
    {
        $ApiConnector = new ApiConnector($dealer, $model);
        $responce = json_decode($ApiConnector->sendClientData(), true);
        if (empty($responce['status_code'])) {
            self::logger('../log/api-mailing.log', date('Y-m-d H:i:s') . " | record_id - $id | success answer id - " . $responce['id'] . " success.\n");
        } elseif ($responce['status_code'] == 22) {
            $error_string = '';
            foreach ($responce['error_list'] as $err_id => $error) {
                $error_string .= "[" . $err_id . "] " . implode(' | ', $error) . " ";
            }
            self::logger('../log/api-mailing.log', date('Y-m-d H:i:s') . " | record_id - $id | error - $error_string \n");
        }
    }

    /**
     * @param $vin
     * @return bool
     */
    public static function vinValidate($vin)
    {
        if (strlen($vin) != 17)
            return false;

        preg_match('/[a-zA-Z0-9]{0,17}/', $vin, $matches);
        if (strlen($matches[0]) != 17)
            return false;

        return true;
    }

    /**
     * Удаление записей дилера за текущий месяц
     * @param $dealer_id - номер дилера
     */
    public static function deleteMailings($dealer_id)
    {
        $date = new DateTime();
        $date->modify('-1 month');
        $date->format('Y-m-d');
        $mailings = MailingListTable::getInstance()->createQuery()->delete()->where('dealer_id = ' . $dealer_id . ' AND YEAR(added_date) IN (' . $date->format('Y') . ') AND MONTH(added_date) IN (' . $date->format('m') . ')')->execute();

    }

    /**
     * @param $filename - полный путь до файла
     * @param $data - сообщение для лога
     */
    public static function logger($filename, $data)
    {
//        var_dump(file_exists($filename)); die();
        if (file_exists($filename) && filesize($filename) > 5048576)
            unlink($filename);

        file_put_contents($filename, $data, FILE_APPEND);
    }

    /**
     * Выгрузка в файл статистики по емейлам
     * @param $data
     */
    public static function exportStatToXls($data)
    {
        $pExcel = new PHPExcel();
        $pExcel->setActiveSheetIndex(0);
        $aSheet = $pExcel->getActiveSheet();

        $aSheet->setTitle('Cтатистика по емейлам');

        $boldFont = array(
            'font' => array(
                'name' => 'Arial Cyr',
                'size' => '12',
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
        $aSheet->getStyle('A1')->applyFromArray($boldFont);
        $aSheet->setCellValue("B1", 'Клиент');
        $aSheet->getStyle('B1')->applyFromArray($boldFont);
        $aSheet->setCellValue("C1", 'Телефон');
        $aSheet->getStyle('C1')->applyFromArray($boldFont);
        $aSheet->setCellValue("D1", 'Email');
        $aSheet->getStyle('D1')->applyFromArray($boldFont);
        $aSheet->setCellValue("E1", 'Дата посещения');
        $aSheet->getStyle('E1')->applyFromArray($boldFont);
        $aSheet->setCellValue("F1", 'Дата выгрузки');
        $aSheet->getStyle('F1')->applyFromArray($boldFont);
        $aSheet->setCellValue("G1", 'Дата загрузки');
        $aSheet->getStyle('G1')->applyFromArray($boldFont);
        $aSheet->setCellValue("H1", 'VIN номер');
        $aSheet->getStyle('H1')->applyFromArray($boldFont);
        $aSheet->setCellValue("I1", 'Модель автомобиля');
        $aSheet->getStyle('I1')->applyFromArray($boldFont);
        $aSheet->setCellValue("J1", 'Пол');
        $aSheet->getStyle('J1')->applyFromArray($boldFont);
        $aSheet->setCellValue("K1", 'Название дилера');
        $aSheet->getStyle('K1')->applyFromArray($boldFont);


//        $aSheet->getStyle('2:' . (count($data) + 1))->applyFromArray($center);
//        $aSheet->getStyle('A1:B' . (count($data) + 1))->applyFromArray($boldFont);

        $cellIterator = $aSheet->getRowIterator()->current()->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(true);
        foreach ($cellIterator as $cell) {
            $aSheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
        }

        sfProjectConfiguration::getActive()->loadHelpers('Date');
        foreach ($data as $d_key => $client) {
            $name = trim(str_replace('===', '', $client->getFirstName() . ' ' . $client->getLastName()));
            $phone = trim(str_replace('===', '', $client->getPhone()));
            $email = $client->getEmail() == "===" ? "" : strtolower(trim($client->getEmail()));
//            $last_v_date = trim(str_replace('===', '', $client->getLastVisitDate()));
            $last_v_date = date('d.m.Y');
            $last_up_date = trim(str_replace('===', '', $client->getLastUploadData()));
            $gender = $client->getGender() == "===" ? "" : strtolower(trim($client->getGender()));

            $Dealer = DealerTable::getInstance()->findOneByNumber($client->getDealerId());
//            echo '<pre>'. print_r($Dealer->name, 1) .'</pre>'; die();

            $aSheet->setCellValueByColumnAndRow(0, $row, $d_key + 1);
            $aSheet->setCellValueByColumnAndRow(1, $row, $name);
            $aSheet->setCellValueByColumnAndRow(2, $row, $phone);
            $aSheet->setCellValueByColumnAndRow(3, $row, $email);
            $aSheet->setCellValueByColumnAndRow(4, $row, format_date($last_v_date, 'd MMMM yyyy', 'ru'));
            $aSheet->setCellValueByColumnAndRow(5, $row, format_date($last_up_date, 'd MMMM yyyy', 'ru'));
            $aSheet->setCellValueByColumnAndRow(6, $row, format_date($client->getAddedDate(), 'd MMMM yyyy', 'ru'));
            $aSheet->setCellValueByColumnAndRow(7, $row, $client->getVin());
            $aSheet->setCellValueByColumnAndRow(8, $row, $client->getModel());
            $aSheet->setCellValueByColumnAndRow(9, $row, $gender);
            $aSheet->setCellValueByColumnAndRow(10, $row, $Dealer['name']);
            $row++;
        }

        // Выводим HTTP-заголовки
        header("Expires: Mon, 1 Apr 1974 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=dealer_mail_stat.xls");


        // Выводим содержимое файла
        $objWriter = new PHPExcel_Writer_Excel5($pExcel);
        $objWriter->save('php://output');
        die();
    }

    /**
     * @param int $month
     * @param int $year
     */
    public static function exportStatToXlsAll($month = 1)
    {
        $year = date('Y');
        ini_set('memory_limit', '4095M');
        set_time_limit(0);

        $pExcel = new PHPExcel();
        $Dealers = MailingListTable::getInstance()->createQuery()->select()
            ->groupBy('dealer_id')->execute();

        $index = 0;
        foreach ($Dealers as $dealer) {
            if (self::getCountDataFromDealer($dealer->getDealerId(), $year, $month)) {
                $generalDealer = DealerTable::getInstance()->createQuery()->select('number, name')->where('number = ?', $dealer->getDealerId())->execute()->toArray();
                $title = substr($dealer->getDealerId(), -3, 3) . '-' . mb_substr($generalDealer[0]['name'], 0, 25, 'utf-8');
                $pExcel->createSheet($index);
                $pExcel->setActiveSheetIndex($index);
                $aSheet = $pExcel->getActiveSheet();
                $aSheet->setTitle($title);

                $aSheet = self::setSheetHeader($aSheet);
                self::setSheetData($aSheet, $dealer->getDealerId(), $year, $month);
                ++$index;
            }
        }

        // Выводим HTTP-заголовки
        header("Expires: Mon, 1 Apr 1974 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=dealer_mail_stat.xls");

        // Выводим содержимое файла
        $objWriter = new PHPExcel_Writer_Excel5($pExcel);
        $objWriter->save('php://output');
    }

    public static function ExportStatLibXL($month = 1)
    {
        $year = date('Y');
        ini_set('memory_limit', '4095M');
        set_time_limit(0);

        $Mailings_dealers = MailingListTable::getInstance()
            ->createQuery()
            ->groupBy('dealer_id')->execute();

        $excelBook = new ExcelBook();
        $excelBook->setLocale('UTF-8');

        // create a small sample data set
        $dataset = array(
            array('Номер', 'Клиент', 'Пол', 'Телефон', 'Email', 'Дата посещения', 'Дата выгрузки', 'VIN номер', 'Модель автомобиля'),
        );

        foreach ($Mailings_dealers as $item) {
            $Dealer = Doctrine_Core::getTable('Dealer')->findOneByNumber($item->getDealerId());
            sfProjectConfiguration::getActive()->loadHelpers('Date');
            $data = MailingListTable::getInstance()->createQuery()->select()
                ->where('dealer_id = ?', $Dealer->getNumber())
                ->andWhere('YEAR(added_date) = ?', $year)
                ->andWhere('MONTH(added_date) = ?', $month)
                ->execute();

            if (count($data) > 0) {
                $Sheet = $excelBook->addSheet(substr($item->getDealerId(), -3, 3) . ' ' . mb_substr($Dealer->getName(), 0, 25, 'utf-8'));
                $dataset = array('Номер', 'Клиент', 'Пол', 'Телефон', 'Email', 'Название фирмы', 'ОПФ', 'Дата посещения', 'Дата выгрузки', 'VIN номер', 'Модель автомобиля');
                $Sheet->writeRow(1, $dataset);

                $row = 2;
                foreach ($data as $k => $di) {
                    $first_name = $di->getFirstName();
                    $last_name = $di->getLastName();
                    $middle_name = $di->getMiddleName();
                    $name = $first_name . " " . $last_name . " " . $middle_name;
                    $compsny_name = $di->getFirmName();
                    $opf = $di->getOpf();
                    $tem_arr = array(
                        0 => $k + 1,
                        1 => $name,
                        2 => ($di->getGender() ? $di->getGender() : '---'),
                        3 => ($di->getPhone() ? $di->getPhone() : '---'),
                        5 => ($di->getEmail() ? $di->getEmail() : ' '),
                        6 => $compsny_name,
                        7 => $opf,
                        8 => ($di->getLastVisitDate() ? $di->getLastVisitDate() : ' '),
                        9 => ($di->getLastUploadData() ? $di->getLastUploadData() : ' '),
                        10 => $di->getVin(),
                        11 => $di->getModel()
                    );
                    $Sheet->writeRow($row, $tem_arr);
                    $row++;
                }

                $Sheet->setAutoFitArea(1, 9);
                // write sum formula under data set
                $col = 2;
                $Sheet->write($row, $col, '=SUM(B1:B3)');
            }
        }

//        exit();
//// add second sheet to work book
//        $xlSheet2 = $Sheet->addSheet('Sheet2');
//
//// add a date with specific date format to second sheet
//        $row = 1; $col = 0;
//        $date = new \DateTime('2014-08-02');
//        $dateFormat = new \ExcelFormat($excelBook);
//        $dateFormat->numberFormat(\ExcelFormat::NUMFORMAT_DATE);
//        $xlSheet2->write($row, $col, $date->getTimestamp(), $dateFormat, \ExcelFormat::AS_DATE);

// save workbook
        // Выводим HTTP-заголовки
        header("Expires: Mon, 1 Apr 1974 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=mail_stat_" . date('Y-m-d') . ".xls");
        $excelBook->save('php://output');

    }

    /**
     * @param $aSheet
     * @return mixed
     */
    private static function setSheetHeader($aSheet)
    {
        $boldFont = array(
            'font' => array(
                'name' => 'Arial Cyr',
                'size' => '12',
                'bold' => true
            )
        );

        $aSheet->setCellValue("A1", 'Номер');
        $aSheet->getStyle('A1')->applyFromArray($boldFont);
        $aSheet->setCellValue("B1", 'Клиент');
        $aSheet->getStyle('B1')->applyFromArray($boldFont);
        $aSheet->setCellValue("C1", 'Пол');
        $aSheet->getStyle('C1')->applyFromArray($boldFont);
        $aSheet->setCellValue("D1", 'Телефон');
        $aSheet->getStyle('D1')->applyFromArray($boldFont);
        $aSheet->setCellValue("E1", 'Email');
        $aSheet->getStyle('E1')->applyFromArray($boldFont);
        $aSheet->setCellValue("F1", 'Дата посещения');
        $aSheet->getStyle('F1')->applyFromArray($boldFont);
        $aSheet->setCellValue("G1", 'Дата выгрузки');
        $aSheet->getStyle('G1')->applyFromArray($boldFont);
        $aSheet->setCellValue("H1", 'Дата выгрузки');
        $aSheet->getStyle('H1')->applyFromArray($boldFont);
        $aSheet->setCellValue("I1", 'VIN номер');
        $aSheet->getStyle('I1')->applyFromArray($boldFont);
        $aSheet->setCellValue("J1", 'Модель автомобиля');
        $aSheet->getStyle('J1')->applyFromArray($boldFont);

        $cellIterator = $aSheet->getRowIterator()->current()->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(true);
        foreach ($cellIterator as $cell) {
            $aSheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
        }

        return $aSheet;
    }

    /**
     * @param $dealer_id
     * @param $year
     * @param $month
     * @return bool
     */
    private static function getCountDataFromDealer($dealer_id, $year, $month)
    {
        $data = MailingListTable::getInstance()
            ->createQuery()->select()
            ->where('dealer_id = ?', $dealer_id)
            ->andWhere('YEAR(added_date) = ?', $year)
            ->andWhere('MONTH(added_date) = ?', $month);
        return $data->count() > 0 ? true : false;
    }

    /**
     * @param $aSheet
     * @param $data
     */
    private static function setSheetData($aSheet, $dealer_id, $year, $month)
    {
        $row = 2;
        sfProjectConfiguration::getActive()->loadHelpers('Date');
        $data = MailingListTable::getInstance()->createQuery()->select()
            ->where('dealer_id = ?', $dealer_id)
            ->andWhere('YEAR(added_date) = ?', $year)
            ->andWhere('MONTH(added_date) = ?', $month)
            ->execute();

        foreach ($data as $d_key => $client) {
            $name = trim(str_replace('===', '', $client->getFirstName() . ' ' . $client->getLastName()));
            $phone = trim(str_replace('===', '', $client->getPhone()));
            $email = trim(str_replace('===', '', $client->getEmail()));
            $gender = trim(str_replace('===', '', $client->getGender()));
            $last_v_date = trim(str_replace('===', '', $client->getLastVisitDate()));
            $last_up_date = trim(str_replace('===', '', $client->getLastUploadData()));

            $aSheet->setCellValueByColumnAndRow(0, $row, $d_key + 1);
            $aSheet->setCellValueByColumnAndRow(1, $row, $name);
            $aSheet->setCellValueByColumnAndRow(2, $row, $gender);
            $aSheet->setCellValueByColumnAndRow(3, $row, $phone);
            $aSheet->setCellValueByColumnAndRow(4, $row, $email);
            $aSheet->setCellValueByColumnAndRow(5, $row, $last_v_date);
            $aSheet->setCellValueByColumnAndRow(6, $row, $last_up_date);
            $aSheet->setCellValueByColumnAndRow(7, $row, format_date($client->getAddedDate(), 'd MMMM yyyy', 'ru'));
            $aSheet->setCellValueByColumnAndRow(8, $row, $client->getVin());
            $aSheet->setCellValueByColumnAndRow(9, $row, $client->getModel());
            $row++;
        }
        return $aSheet;
    }
}
