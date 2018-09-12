<?php

/**
 * comment_stat actions.
 *
 * @package    Servicepool2.0
 * @subpackage comment_stat
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class reports_infoActions extends sfActions
{
    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    private $filesDir = '/activities/module/agreement/report/additional/';
    private $filesDirConcept = '/activities/module/agreement/report/financial/';

    const CONCEPT = 'concept';
    const MODEL_TYPE_CONCEPT = 10;

    function executeIndex(sfWebRequest $request)
    {
        $start_date = $request->getParameter('start_date');
        $end_date = $request->getParameter('end_date');
        $dealer = $request->getParameter('dealer_filter');
        $activity = $request->getParameter('activity_filter');
        $onlyConcepts = $request->getParameter('ch_only_concepts');

        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->dealer_filter = $dealer;
        $this->activity_filter = $activity;
        $this->onlyConcepts = $onlyConcepts;

        $this->status = false;

        if (preg_match('#^[0-9]{2}\.[0-9]{2}\.[0-9]{2}$#', $start_date))
            $start_date = D::fromRus($start_date);
        else
            $start_date = false;

        if (preg_match('#^[0-9]{2}\.[0-9]{2}\.[0-9]{2}$#', $end_date)) {
            $end_date = D::fromRus($end_date);
        } else
            $end_date = false;

        if ($request->isMethod('post')) {
            $query = AgreementModelTable::getInstance()
                ->createQuery('m')
                ->select('*')
                ->innerJoin('m.Report r')
                ->innerJoin('m.ModelType mt')
                ->where('r.status = ?', array('accepted'));

            if ($dealer && $dealer != -1) {
                $query->andWhere('m.dealer_id = ?', $dealer);
            }

            if ($activity && $activity != -1) {
                $query->andWhere('m.activity_id = ?', $activity);
            }

            if (!empty($start_date)) {
                $query->andWhere('r.accept_date >= ?', D::toDb($start_date));
            }

            if (!empty($end_date)) {
                $query->andWhere('r.accept_date <= ?', D::toDb($end_date));
            }

            if (!is_null($onlyConcepts)) {
                $query->andWhere('m.model_type_id = ?', self::MODEL_TYPE_CONCEPT);
            }

            $result = $query->execute();

            $zip = new ZipArchive();
            $zipFile = sfConfig::get('sf_upload_dir') . DIRECTORY_SEPARATOR . 'reports.zip';

            @unlink($zipFile);
            $res = $zip->open($zipFile, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);
            if ($res) {
                foreach ($result as $item) {
                    $dir = $item->ModelType->getIdentifier();

                    $activity = $this->normalize($item->getActivity()->getName());
                    $dealer = $this->normalize($item->getDealer()->getName());

                    if (!is_null($onlyConcepts)) {
                        if ($item->Report->getFinancialDocsFile()) {
                            $zip->addFile(sfConfig::get('sf_upload_dir') . $this->filesDirConcept . $item->Report->getFinancialDocsFile(), $dealer . '/' . $activity . '/' . $dir . '/' . $item->Report->getFinancialDocsFile());

                            /**Financial files ext*/
                            for ($file_ind = 1; $file_ind <= sfConfig::get('app_max_files_upload_count'); $file_ind++) {
                                $fin_file = $item->Report->{"getFinancialDocsFile" . $file_ind}();

                                if (!empty($fin_file)) {
                                    $zip->addFile(sfConfig::get('sf_upload_dir') . $this->filesDirConcept . $fin_file, $dealer . '/' . $activity . '/' . $dir . '/' . $fin_file);
                                }
                            }
                        }
                        else {
                            $report_files = AgreementModelReportFilesTable::getInstance()->createQuery()->where('object_id = ? and file_type = ?', array($item->getId(), 'report_financial'))->execute();
                            foreach ($report_files as $report_file) {
                                $zip->addFile(sfConfig::get('sf_upload_dir') . $this->filesDirConcept . $report_file->getFile(), $dealer . '/' . $activity . '/' . $dir . '/' . $report_file->getFile());
                            }
                        }

                    } else {
                        if (!$item->Report->getAdditionalFile()) {
                            $report_files = AgreementModelReportFilesTable::getInstance()->createQuery()->where('object_id = ? and file_type = ?', array($item->getId(), 'report_additional'))->execute();
                            foreach ($report_files as $report_file) {
                                $zip->addFile(sfConfig::get('sf_upload_dir') . $this->filesDir . $report_file->getFile(), $activity . '/' . $dealer . '/' . $dir . '/' . $report_file->getFile());
                            }
                        }
                        else {
                            $zip->addFile(sfConfig::get('sf_upload_dir') . $this->filesDir . $item->Report->getAdditionalFile(), $activity . '/' . $dealer . '/' . $dir . '/' . $item->Report->getAdditionalFile());

                            /**Additional files ext*/
                            for ($file_ind = 1; $file_ind <= sfConfig::get('app_max_files_upload_count'); $file_ind++) {
                                $add_file_ext = $item->Report->{"getAdditionalFileExt" . $file_ind}();
                                if (!empty($add_file_ext)) {
                                    $zip->addFile(sfConfig::get('sf_upload_dir') . $this->filesDir . $add_file_ext, $activity . '/' . $dealer . '/' . $dir . '/' . $add_file_ext);
                                }

                                $method = "getAdditionalFile" . $file_ind;
                                if (method_exists($item->Report, $method)) {
                                    $add_file = $item->Report->$method();
                                    if (!empty($add_file)) {
                                        $zip->addFile(sfConfig::get('sf_upload_dir') . $this->filesDir . $add_file, $activity . '/' . $dealer . '/' . $dir . '/' . $add_file);
                                    }
                                }
                            }
                        }
                    }
                }
                $res = $zip->close();
            }

            if ($res && count($result)) {
                $this->redirect('/uploads/reports.zip');

                $this->status = true;
            } else
                $this->status = false;

        }

        $this->dealers = DealerTable::getVwDealersQuery()->execute();
        $this->activities = ActivityTable::getInstance()
            ->createQuery()
            ->select()
            ->orderBy('id ASC')
            ->execute();
    }

    function executeShow(sfWebRequest $request)
    {

        $this->setTemplate('index');
    }


    function normalize($name)
    {
        $str = '';
        $name = mb_strtolower($this->toUtf8($name), 'UTF-8');

        for ($n = 0, $len = mb_strlen($name, 'UTF-8'); $n < $len; $n++) {
            $new_sym = $sym = mb_substr($name, $n, 1, 'UTF-8');
            if (!$this->isSymEnabled($sym)) {
                $new_sym = $this->symToTranslit($sym);
                if (!$new_sym)
                    $new_sym = '_';
            }

            $str .= $new_sym;
        }

        return $str;
    }

    function isSymEnabled($sym)
    {
        $enabled = 'abcdefghijklmnopqrstuvwxyz0123456789';
        return mb_strpos($enabled, $sym, 0, 'UTF-8') !== false;
    }

    function symToTranslit($sym)
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

    function toUtf8($name)
    {
        return mb_convert_encoding($name, 'UTF-8', 'UTF-8,CP1251,ASCII');
    }

}
