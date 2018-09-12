<?php

/**
 * Utils class
 *
 */
class Utils
{
    static function trim_text($input, $length, $ellipses = true, $strip_html = true)
    {
        //strip tags, if desired
        if ($strip_html) {
            $input = strip_tags($input);
        }

        //no need to trim, already shorter than trim length
        if (strlen($input) <= $length) {
            return $input;
        }

        //find last space within length
        $last_space = strrpos(substr($input, 0, $length), ' ');
        $trimmed_text = substr($input, 0, $last_space ? $last_space : $length);

        //add ellipses (...)
        if ($ellipses) {
            $trimmed_text .= '...';
        }

        return $trimmed_text;
    }

    static function oi_encode_token($input = null, $key = null)
    {
        if ($input && $key) {
            $encoded = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $input, MCRYPT_MODE_CBC, md5(md5($key))));

            return $encoded;
        }

        return null;
    }

    static function normalize($name)
    {
        $str = '';
        $name = mb_strtolower(Utils::toUtf8($name), 'UTF-8');

        for ($n = 0, $len = mb_strlen($name, 'UTF-8'); $n < $len; $n++) {
            $new_sym = $sym = mb_substr($name, $n, 1, 'UTF-8');
            if (!Utils::isSymEnabled($sym)) {
                $new_sym = Utils::symToTranslit($sym);
                if (!$new_sym)
                    $new_sym = '_';
            }

            $str .= $new_sym;
        }

        return $str;
    }

    static function isSymEnabled($sym)
    {
        $enabled = 'abcdefghijklmnopqrstuvwxyz0123456789';
        return mb_strpos($enabled, $sym, 0, 'UTF-8') !== false;
    }

    static function symToTranslit($sym)
    {
        static $translit = array(
            'а' => 'a',
            'б' => 'b',
            'в' => 'v',
            'г' => 'g',
            'д' => 'd',
            'е' => 'e',
            'ё' => 'yo',
            'ж' => 'zh',
            'з' => 'z',
            'и' => 'i',
            'й' => 'j',
            'к' => 'k',
            'л' => 'l',
            'м' => 'm',
            'н' => 'n',
            'о' => 'o',
            'п' => 'p',
            'р' => 'r',
            'с' => 's',
            'т' => 't',
            'у' => 'u',
            'ф' => 'f',
            'х' => 'h',
            'ц' => 'c',
            'ч' => 'ch',
            'ш' => 'sh',
            'щ' => 'sch',
            'ы' => 'yi',
            'э' => 'ye',
            'ю' => 'yu',
            'я' => 'ya'
        );

        return isset($translit[$sym]) ? $translit[$sym] : false;
    }

    static function toUtf8($name)
    {
        return mb_convert_encoding($name, 'UTF-8', 'UTF-8,CP1251,ASCII');
    }

    static function getRemoteFileSize($link)
    {
        $ch = curl_init($link);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); //not necessary unless the file redirects (like the PHP example we're using here)

        $data = curl_exec($ch);
        curl_close($ch);
        if ($data === false) {
            return 0;
        }

        $contentLength = 'unknown';
        $status = 'unknown';
        if (preg_match('/^HTTP\/1\.[01] (\d\d\d)/', $data, $matches)) {
            $status = (int)$matches[1];
        }

        if (preg_match('/Content-Length: (\d+)/', $data, $matches)) {
            $contentLength = (int)$matches[1];
        }

        return F::getSmartSize($contentLength);
    }

    static function eqModelDateFromLogEntryWithYear($modelId, $year, $quarter = 0)
    {
        $calcDate = self::getModelDateFromLogEntryWithYear($modelId);
        if (!is_null($calcDate)) {
            $modelYear = D::getYear($calcDate);

            if ($quarter != 0) {
                $modelQuarter = D::getQuarter($calcDate);

                return $modelYear == $year && $modelQuarter == $quarter;
            }

            return $modelYear == $year;
        }

        return true;
    }

    static function getModelDateFromLogEntryWithYear($modelId, $returnAsObject = false, $dealer_id = 0)
    {
        $query = LogEntryTable::getInstance()
            ->createQuery()
            ->select('created_at, object_id')
            ->andWhere('private_user_id = ? and icon = ? and (object_type = ? or object_type = ? or object_type = ?)', array(0, 'clip', 'agreement_report', 'agreement_model', 'agreement_concept_report'))
            //->andWhere('private_user_id = ? and icon = ?', array(0, 'clip'))
            ->orderBy('id DESC');

        if ($dealer_id != 0) {
            $query->andWhere('dealer_id = ?', $dealer_id);
        }

        if (is_array($modelId)) {
            $query->andWhereIn('object_id', $modelId);

            return $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
        } else {
            $query->andWhere('object_id = ?', $modelId)
                ->limit(1);
        }

        if ($returnAsObject) {
            return $query->fetchOne();
        } else {
            $data = $query->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);
            if (!empty($data)) {
                return date('Y-m-d H:i:s', D::calcQuarterData($data['created_at']));
            }
        }

        return null;
    }

    /**
     * @param $modelId
     * @param $returnAsObject
     * @param null $actions
     * @return false|null|string
     */
    static function getModelDateFromLogEntryByActions($modelId, $returnAsObject, $actions) {
        $query = self::getModelDateFromLogQuery();

        //Доп. проверка на действия над заявкой
        $query->andWhere('object_type = ? or object_type = ? or object_type = ? or object_type = ?', array('agreement_report', 'agreement_model', 'agreement_concept_report', 'agreement_concept'));
        $query->andWhereIn('action', $actions);

        $result = null;
        if (is_array($modelId)) {
            $query->andWhereIn('object_id', $modelId);

            $result = $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
        } else {
            $query->andWhere('object_id = ?', $modelId)
                ->limit(1);

            if ($returnAsObject) {
                $result = $query->fetchOne();
            } else {
                $data = $query->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);
                if (!empty($data)) {
                    $result = date('Y-m-d H:i:s', D::calcQuarterData($data[ 'created_at' ]));
                }
            }
        }

        if (!$result) {
            return self::getModelDateFromLogEntry($modelId, $returnAsObject);
        }

        return $result;
    }

    static function getModelDateFromLogEntry($modelId, $returnAsObject) {
        $query = self::getModelDateFromLogQuery();

        $query->andWhere('private_user_id = ? and icon = ? and (object_type = ? or object_type = ? or object_type = ? or object_type = ?)', array(0, 'clip', 'agreement_report', 'agreement_model', 'agreement_concept_report', 'agreement_concept'));

        if (is_array($modelId)) {
            $query->andWhereIn('object_id', $modelId);

            return $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
        } else {
            $query->andWhere('object_id = ?', $modelId)
                ->limit(1);
        }

        if ($returnAsObject) {
            return $query->fetchOne();
        } else {
            $data = $query->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);
            if (!empty($data)) {
                return date('Y-m-d H:i:s', D::calcQuarterData($data['created_at']));
            }
        }

        return null;
    }

    static function getModelDateFromLogQuery() {
        return $query = LogEntryTable::getInstance()
            ->createQuery()
            ->select('created_at, object_id, icon')
            ->orderBy('id DESC');;
    }

    static function getUploadedFilesByField($files, $file_field)
    {
        if (is_array($file_field)) {
            $fields = $file_field;
        } else {
            $fields = array($file_field);
        }

        $max_upload_files = sfConfig::get('app_max_files_upload_count');
        $ind = $file_ind = 0;

        $uploaded_files_result = array();
        foreach ($files as $key => $file) {
            if (isset($files[$key]['tmp_name']) && $files[$key]['tmp_name']) {
                $uploaded_files_result[$key] = $files[$key];
            }
        }

        foreach ($fields as $field) {
            if (isset($files[$field]) && count($files[$field]) > 1) {
                foreach ($files[$field] as $key => $values) {
                    if ($ind > $max_upload_files) {
                        break;
                    }

                    $uploaded_files_result[$field . ($ind != 0 ? '_' . $ind : '')] = $values;
                    $ind++;
                }
            } else if (isset($files[$field]) && isset($files[$field][0]['tmp_name']) && $files[$field][0]['tmp_name']) {
                $uploaded_files_result[$field] = $files[$field][0];
            }
        }

        return $uploaded_files_result;
    }

    static function makeUrl($text)
    {
        $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";

        if (preg_match($reg_exUrl, $text, $matches)) {
            return preg_replace($reg_exUrl, "<a href='" . $matches[0] . "' target='_blank'>" . $matches[0] . "</a>", $text);
        }

        return $text;
    }

    static function numberFormat($number, $currency = 'руб.')
    {
        return sprintf('%s %s', number_format($number, 2, '.', ' '), $currency);
    }

    static function array_merge_custom()
    {
        $array = array();

        foreach (func_get_args() as $key => $args) {
            foreach ($args as $arg_key => $value) {
                $array[$arg_key] = $value;
            }
        }

        return $array;
    }

    static function getElapsedTime($st)
    {
        $mins = floor($st / 60);
        $hours = floor($mins / 60);
        $days = floor($hours / 24);

        if ($days > 0) {
            return $days;
        }

        return $days;
    }

    static function plural($n, $forms)
    {
        return $n % 10 == 1 && $n % 100 != 11 ? $forms[0] : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20) ? $forms[1] : $forms[2]);
    }

    public static function allowedIps()
    {
        $ip = getenv('REMOTE_ADDR');
        //$ips = array('46.175.160.37', '46.175.166.67', '46.175.166.61', '46.175.165.37', '109.73.13.105', '93.170.246.38', '109.73.13.105');
        $ips = array('46.175.160.37', '46.175.166.67', '46.175.166.61', '46.175.165.37');

        return in_array($ip, $ips);
    }

    static function drawExcelImage($icon, $coordinates, $pExcel, $offsetX = 3, $offsetY = 3, $label = '')
    {
        $imageModelStatus = new PHPExcel_Worksheet_Drawing();

        $imageModelStatus->setPath(sfConfig::get('app_images_path') . '/' . $icon);
        $imageModelStatus->setName($label);
        $imageModelStatus->setDescription($label);
        $imageModelStatus->setHeight(16);
        $imageModelStatus->setWidth(16);

        $imageModelStatus->setOffsetX($offsetX);
        $imageModelStatus->setOffsetY($offsetY);

        $imageModelStatus->setWorksheet($pExcel->getActiveSheet());
        $imageModelStatus->setCoordinates($coordinates);
    }

    static function isImage($img)
    {
        $ext = pathinfo($img, PATHINFO_EXTENSION);

        return in_array($ext, array('gif', 'png', 'jpg', 'jpeg'));
    }

    static function checkModelsCompleted(Activity $activity, Dealer $dealer, $year, $quarter)
    {
        $complete = false;

        //Выбираем список заявок по активности и по дилеру
        //Делаем проверку по заявкам, год создания заявки или год согласования заявки
        $activityModelsComplete = AgreementModelTable::getInstance()
            ->createQuery('am')
            ->select('id')
            ->leftJoin('am.Report r')
            ->where('am.activity_id = ? and am.dealer_id = ?', array($activity->getId(), $dealer->getId()))
            //->andWhere('year(r.updated_at) = ? and quarter(r.updated_at) = ?', array($year, $quarter))
            ->andWhere('(year(r.updated_at) = ? or year(am.created_at) = ?)', array($year, $year))
            ->andWhere('am.status = ? and r.status = ?', array('accepted', 'accepted'))
            ->andWhere('model_type_id != ?', Activity::CONCEPT_MODEL_TYPE_ID)
            ->orderBy('am.id ASC')
            ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

        foreach ($activityModelsComplete as $model) {
            $date = Utils::getModelDateFromLogEntryWithYear($model['id']);
            if (!is_null($date)) {
                if ($year == D::getYear($date) && $quarter == D::getQuarter($date)) {
                    $complete = true;
                }
            }
        }

        return $complete;
    }

    static function getYearsList($from, $plus_years = 10)
    {
        $gen_year = range($from, date('Y') + $plus_years);

        return array_merge(array_combine($gen_year, $gen_year));
    }

    public static function getUploadedFilesList(sfWebRequest $request, $field, $uploaded_files_count = null)
    {
        $files = $request->getFiles();
        if (!is_array($files)) {
            return $files;
        }

        if (empty($files)) {
            return $files;
        }

        $uploaded_files = self::getUploadedFiles($files, $field, $uploaded_files_count);
        if (!empty($uploaded_files)) {
            return $uploaded_files;
        }

        $server_file = $request->getPostParameter('server_model_file');
        if (!$server_file || preg_match('#[\\\/]#', $server_file)) {
            return $files;
        }

        return $files;
    }

    static function getUploadedFiles($files, $file_field, $uploaded_files_count = null)
    {
        if (is_array($file_field)) {
            $fields = $file_field;
        } else {
            $fields = array($file_field);
        }

        $uploaded_files_result = array();
        foreach ($files as $key => $file) {
            if (isset($files[$key]['tmp_name']) && $files[$key]['tmp_name']) {
                $uploaded_files_result[$key] = $files[$key];
            }
        }

        foreach ($fields as $field) {
            if (isset($files[$field])) {
                $uploaded_files_result[$field] = $files[$field];
            }
        }

        return $uploaded_files_result;
    }
}
