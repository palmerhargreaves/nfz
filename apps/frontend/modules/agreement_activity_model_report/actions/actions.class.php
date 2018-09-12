<?php

/**
 * agreement_activity_model_report actions.
 *
 * @package    Servicepool2.0
 * @subpackage agreement_activity_model_report
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class agreement_activity_model_reportActions extends BaseActivityActions
{
    protected $check_for_module = 'agreement';

    const MAX_FILES = 10;

    private $uploaded_files_result = array();

    function executeEdit(sfWebRequest $request)
    {
        sfContext::getInstance()->getConfiguration()->loadHelpers(array('Url', 'Asset', 'Tag'));

        $report = $this->getReport($request);

        if ($report) {
            $model = $report->getModel();

            $places_to_upload = $place_to_upload_orig = $model->getTotalFilledPlacesToUploadReportFiles();
            $files_uploaded_count_info = $model->getReportUploadedFilesCount();

            if (isset($files_uploaded_count_info[AgreementModel::UPLOADED_FILE_ADDITIONAL_FILE_TYPE])) {
                $report_additional_uploaded_files = $files_uploaded_count_info[AgreementModel::UPLOADED_FILE_ADDITIONAL_FILE_TYPE];

                $places_to_upload -= $files_uploaded_count_info[AgreementModel::UPLOADED_FILE_ADDITIONAL_FILE_TYPE];
                if ($places_to_upload < 0) {
                    $places_to_upload = 0;
                }
            }

            $report_additional_uploaded_files = AgreementModelReportFilesTable::getUploadedFilesListBy($model->getId(), AgreementModel::UPLOADED_FILE_REPORT, AgreementModel::UPLOADED_FILE_ADDITIONAL_FILE_TYPE, false);
            $report_financial_uploaded_files = AgreementModelReportFilesTable::getUploadedFilesListBy($model->getId(), AgreementModel::UPLOADED_FILE_REPORT, AgreementModel::UPLOADED_FILE_FINANCIAL_FILE_TYPE, false);

            $result = array(
                'success' => true,
                'values' => array(
                    'id' => $report->getModelId(),
                    'report_id' => $report->getId(),
                    'is_concept' => $report->getModel()->isConcept(),
                    'status' => $report->getStatus(),
                    'css_status' => $report->getModel()->getReportCssStatus(),
                    'model_status' => $report->getModel()->getStatus(),
                    'additional_file_description' => $report->getModel()->getModelType()->getReportFieldDescription(),
                    'cost' => $model->getStatus() == 'accepted' && $report->getId() ? $model->getCost() : '',
                    'isOutOfDate' => $model->getIsBlocked() && !$model->getAllowUseBlocked(),
                    'places_count' => $model->getModelTypePlacesCount(),
                    'report_additional_uploaded_files_count' => count($report_additional_uploaded_files),
                    'report_financial_uploaded_files_count' => count($report_financial_uploaded_files),
                    'place_to_upload_files_orig' => $place_to_upload_orig,
                    'places_to_upload_files' => $places_to_upload,
                    'places_to_upload_files_text' => ($place_to_upload_orig > 0
                        ? 'Не забудьте подгрузить ' . $place_to_upload_orig . ' ' . Utils::plural($place_to_upload_orig, array('фотоотчет', 'фотоотчета', 'фотоотчетов')) . ' так как у вас заполнено ' . $place_to_upload_orig . ' ' . Utils::plural($place_to_upload_orig, array('место', 'места', 'мест')) . ' размещения макета'
                        : '')
                )
            );
        } else {
            $result = array(
                'success' => false,
                'error' => 'not_found'
            );
        }

        return $this->sendJson($result);
    }

    function getModelFieldValue(AgreementModel $model, AgreementModelField $field)
    {
        return AgreementModelValueTable::getInstance()->createQuery()->select()->where('model_id = ? and field_id = ?', array($model->getId(), $field->getId()))->fetchOne();
    }

    function executeUpdate(sfWebRequest $request)
    {
        $report = $this->getReport($request);
        if (!$report) {
            return $this->sendJson(array('success' => false, 'error' => 'not_found'), 'agreement_model_report_form.onResponse');
        }

        if ($report->getModel()->getStatus() != 'accepted' || $report->getStatus() != 'not_sent' && $report->getStatus() != 'declined') {
            return $this->sendJson(array('success' => false, 'error' => 'wrong_status'), 'agreement_model_report_form.onResponse');
        }

        $form = new AgreementModelReportFormN($report);

        $cost = $request->getParameter('cost');
        $required_financial = (is_numeric($cost) && floatval($cost)) || $report->getModel()->isConcept();

        /**
         * Work with additional files block
         */
        $upload_files_add_ids = $request->getPostParameter('upload_files_additional_ids');
        if (empty($upload_files_add_ids) && !$report->getModel()->isConcept()) {
            $uploaded_files_list = $report->getUploadedFilesList(AgreementModelReport::UPLOADED_FILE_ADDITIONAL);
            if (count($uploaded_files_list) == 0) {
                $form->getValidator('is_valid_add_data')->setOption('required', true);
            }
        }

        /**
         * Work with financial files block
         */
        $upload_files_fin_ids = $request->getPostParameter('upload_files_financial_ids');

        if (empty($upload_files_fin_ids) && $required_financial) {
            $uploaded_files_list = $report->getUploadedFilesList(AgreementModelReport::UPLOADED_FILE_FINANCIAL);
            if (count($uploaded_files_list) == 0) {
                $form->getValidator('is_valid_fin_data')->setOption('required', true);
            }
        }

        $form->bind(
            array(
                'model_id' => $report->getModelId(),
                'status' => 'wait',
                'cost' => $cost
            ),
            array()
        );

        $message = null;

        if ($form->isValid()) {
            $form->save();

            $model = $form->getObject()->getModel();
            $model->setReport($form->getObject());
            $model->setCost($cost);

            $model->setIsBlocked(false);
            $model->setAllowUseBlocked(false);
            $model->setUseBlockedTo('');
            $model->save();

            $saved_add_files = $this->saveReportFilesN($model, $request, 'upload_files_additional_ids', AgreementModelReport::ADDITIONAL_FILE_PATH, AgreementModelReport::UPLOADED_FILE_ADDITIONAL);
            $saved_fin_files = $this->saveReportFilesN($model, $request, 'upload_files_financial_ids', AgreementModelReport::FINANCIAL_DOCS_FILE_PATH, AgreementModelReport::UPLOADED_FILE_FINANCIAL);

            /**
             * Save additional / financial files to report
             */
            if (!empty($saved_add_files) && !$report->getAdditionalFile()) {
                $report->setAdditionalFile(empty($saved_add_files[0]['upload_path']) ? $saved_add_files[ 0 ][ 'gen_file_name'] : sprintf('%s/%s', $saved_add_files[ 0 ][ 'upload_path'], $saved_add_files[ 0 ][ 'gen_file_name']));
            }

            if (!empty($saved_fin_files) && !$report->getFinancialDocsFile()) {
                $report->setFinancialDocsFile(empty($saved_fin_files[0]['upload_path']) ? $saved_fin_files[ 0 ][ 'gen_file_name'] : sprintf('%s/%s', $saved_fin_files[ 0 ][ 'upload_path'], $saved_fin_files[ 0 ][ 'gen_file_name']));
            }
            $report->save();

            $entry = LogEntryTable::getInstance()->addEntry(
                $this->getUser()->getAuthUser(),
                $model->isConcept() ? 'agreement_concept_report' : 'agreement_report',
                'edit',
                $model->getActivity()->getName() . '/' . $model->getName(),
                'Отчёт отправлен на согласование',
                'clip',
                $model->getDealer(),
                $model->getId(),
                'agreement'
            );


            $model->createPrivateLogEntryForSpecialists($entry);

            $message = $this->addMessageToDiscussion($model, 'Отчёт отправлен на согласование');

            $this->attachFinancialFilesDocsToMessageN($report, $message);
            $this->attachAdditionalFilesToMessageN($report, $message);

            AgreementManagementHistoryMailSender::send(
                'AgreementSendReportMail',
                $entry,
                false,
                false,
                $model->isConcept() ? AgreementManagementHistoryMailSender::NEW_AGREEMENT_CONCEPT_REPORT_NOTIFICATION : AgreementManagementHistoryMailSender::NEW_AGREEMENT_REPORT_NOTIFICATION
            );
        }

        $message_data = null;
        /*if ($message) {
            $message_data = Utils::formatMessageData($message);
        }*/

        return $this->sendFormBindResult($form, 'agreement_model_report_form.onResponse');
    }

    /**
     * @param $model
     * @param $request
     * @param $param_name
     * @param $path
     * @param string $field_type
     * @return array
     * @internal param $form
     */
    private
    function saveReportFilesN($model, $request, $param_name, $path, $field_type = 'report_additional_ext')
    {
        $fileModel = AgreementModelReport::UPLOADED_FILE_REPORT;

        $files_list = TempFileTable::copyFilesByRequest($request, $path, $param_name, $this->getUser()->getAuthUser());
        foreach ($files_list as $file) {
            $record = new AgreementModelReportFiles();
            $record->setArray(
                array(
                    'file' => $file['gen_file_name'],
                    'object_id' => $model->getId(),
                    'object_type' => $fileModel,
                    'file_type' => $field_type,
                    'user_id' => $this->getUser()->getAuthUser()->getId(),
                    'field' => '',
                    'field_name' => '',
                    'path' => $file['upload_path']
                )
            );

            $record->save();
        }

        /**
         * Get already uploaded files
         */
        if ($field_type == AgreementModelReport::UPLOADED_FILE_ADDITIONAL) {
            $uploaded_files = AgreementModelReportFilesTable::getUploadedFilesListBy($model->getId(), AgreementModel::UPLOADED_FILE_REPORT, AgreementModel::UPLOADED_FILE_ADDITIONAL_FILE_TYPE, false);
        } else {
            $uploaded_files = AgreementModelReportFilesTable::getUploadedFilesListBy($model->getId(), AgreementModel::UPLOADED_FILE_REPORT, AgreementModel::UPLOADED_FILE_FINANCIAL_FILE_TYPE, false);
        }

        foreach ($uploaded_files as $file) {
            $files_list[] = array('gen_file_name' => $file->getFile(), 'upload_path' => $file->getPath());
        }

        return $files_list;
    }

    function executeCancel(sfWebRequest $request)
    {
        $report = $this->getReport($request);
        if ($report && $report->getStatus() != 'accepted') {
            $report->setStatus('not_sent');
            $report->save();

            $model = $report->getModel();

            RealBudgetTable::getInstance()->removeByObjectOnly(ActivityModule::byIdentifier('agreement'), $model->getId());

            $entry = LogEntryTable::getInstance()->addEntry(
                $this->getUser()->getAuthUser(),
                $model->isConcept() ? 'agreement_concept_report' : 'agreement_report',
                'cancel',
                $model->getActivity()->getName() . '/' . $model->getName(),
                'Отменена отправка отчёта на согласование',
                '',
                $model->getDealer(),
                $model->getId(),
                'agreement'
            );

            $model->createPrivateLogEntryForSpecialists($entry);

            $report->cancelSpecialistSending();

            $this->addMessageToDiscussion($model, 'отменена отправка отчёта на согласование');

            AgreementManagementHistoryMailSender::send(
                'AgreementCancelReportMail',
                $entry,
                false,
                false,
                $model->isConcept() ? AgreementManagementHistoryMailSender::NEW_AGREEMENT_CONCEPT_REPORT_NOTIFICATION : AgreementManagementHistoryMailSender::NEW_AGREEMENT_REPORT_NOTIFICATION
            );
        }

        return $this->sendJson(array('success' => true));
    }

    /**
     * Returns a report
     *
     * @param sfWebRequest $request
     * @return AgreementModelReport
     */
    function getReport(sfWebRequest $request)
    {
        $activity = $this->getActivity($request);
        $dealer = $this->getUser()->getAuthUser()->getDealer();
        $model = AgreementModelTable::getInstance()
            ->createQuery()
            ->where('activity_id=? and dealer_id=? and id=?', array($activity->getId(), $dealer->getId(), $request->getParameter('id')))
            ->fetchOne();

        if (!$model)
            return false;

        $report = $model->getReport();
        if ($report->isNew()) {
            $report->setModel($model);
            $report->status = 'not_sent';
        }

        return $report;
    }

    protected function attachFinancialDocsToMessage(AgreementModelReport $report, Message $message)
    {
        $file = new MessageFile();
        $file->setMessageId($message->getId());
        $file->setFile('fin-' . $message->getId() . '-' . $report->getFinancialDocsFile());

        copy(
            sfConfig::get('sf_upload_dir') . '/' . AgreementModelReport::FINANCIAL_DOCS_FILE_PATH . '/' . $report->getFinancialDocsFile(),
            sfConfig::get('sf_upload_dir') . '/' . MessageFile::FILE_PATH . '/' . $file->getFile()
        );

        $file->save();
    }

    protected function attachAdditionalFileToMessage(AgreementModelReport $report, Message $message)
    {
        $file = new MessageFile();
        $file->setMessageId($message->getId());
        $file->setFile('add-' . $message->getId() . '-' . $report->getAdditionalFile());

        copy(
            sfConfig::get('sf_upload_dir') . '/' . AgreementModelReport::ADDITIONAL_FILE_PATH . '/' . $report->getAdditionalFile(),
            sfConfig::get('sf_upload_dir') . '/' . MessageFile::FILE_PATH . '/' . $file->getFile()
        );

        $file->save();

    }

    public function attachFinancialFilesDocsToMessage($report, $message)
    {
        $files = $report->getModel()->getFinancialDocsFiles();
        foreach ($files as $file) {
            $this->saveAttachFile($message, $file, AgreementModelReport::FINANCIAL_DOCS_FILE_PATH, 'fin');
        }
    }

    public function attachAdditionalFilesToMessage($report, $message)
    {
        $additionalFiles = $report->getModel()->getAdditionalFiles();
        foreach ($additionalFiles as $file) {
            $this->saveAttachFile($message, $file, AgreementModelReport::ADDITIONAL_FILE_PATH, 'add');
        }

        $additionalExtFiles = $report->getModel()->getAdditionalExtFiles();
        foreach ($additionalExtFiles as $file) {
            $this->saveAttachFile($message, $file, AgreementModelReport::ADDITIONAL_FILE_PATH, 'add');
        }
    }

    private function saveAttachFile($message, $orig_file, $path, $label)
    {
        $file = new MessageFile();
        $file->setMessageId($message->getId());
        $file->setFile($label . '-' . $message->getId() . '-' . $orig_file->getFile());

        copy(
            sfConfig::get('sf_upload_dir') . '/' . $path . '/' . $orig_file->getFile(),
            sfConfig::get('sf_upload_dir') . '/' . MessageFile::FILE_PATH . '/' . $file->getFile()
        );

        $file->save();
    }

    /**
     * Add message to discussion
     *
     * @param AgreementModel $model
     * @param string $text
     * @return Message|false
     */
    protected function addMessageToDiscussion(AgreementModel $model, $text)
    {
        $discussion = $model->getDiscussion();

        if (!$discussion)
            return;

        $message = new Message();
        $user = $this->getUser()->getAuthUser();
        $message->setDiscussionId($discussion->getId());
        $message->setUser($user);
        $message->setUserName($user->selectName());
        $message->setText($text);
        $message->setSystem(true);
        $message->save();

        // mark as unread
        $discussion->getUnreadMessages($user);

        return $message;
    }

    public function executeDownloadAdditionalFile(sfWebRequest $request)
    {
        return $this->downloadFile($request->getParameter('file'), AgreementModelReport::ADDITIONAL_FILE_PATH);
    }

    public function executeDownloadFinancialFile(sfWebRequest $request)
    {
        return $this->downloadFile($request->getParameter('file'), AgreementModelReport::FINANCIAL_DOCS_FILE_PATH);
    }

    private function downloadFile($file, $path)
    {
        $filePath = sfConfig::get('app_uploads_path') . '/' . $path . '/' . $file;
        if (file_exists($filePath)) {
            $file = end(explode('/', $filePath));

            $file_download_result = F::downloadFile($filePath, $file);
            if (empty($file_download_result)) {
                $this->getResponse()->setContentType('application/json');
                $this->getResponse()->setContent(json_encode(array('success' => false, 'message' => 'Файл не найден')));
            } else {
                $file_download_result != 'success' ? $this->redirect($file_download_result) : '';
            }
        }

        return sfView::NONE;
    }

    public function executeLoadBlock(sfWebRequest $request)
    {
        $this->model = $this->getModel($request);
        $this->report_file_type = $request->getParameter('report_file_type');
    }

    /**
     * Returns an agreement model
     *
     * @param sfWebRequest $request
     * @return AgreementModel|false
     */
    protected function getModel(sfWebRequest $request)
    {
        if ($request->getParameter('id')) {
            $model = AgreementModelTable::getInstance()
                ->createQuery()
                ->where('id = ?', array($request->getParameter('id')))
                ->fetchOne();

            return $model;
        }

        return null;
    }

    function executeDeleteReportFile(sfWebRequest $request)
    {
        $fileId = $request->getParameter('fileId');
        $file = AgreementModelReportFilesTable::getInstance()->find($fileId);
        if ($file) {
            $model = AgreementModelTable::getInstance()
                ->createQuery()
                ->where('id = ?', array($file->getObjectId()))
                ->fetchOne();

            $type = $file->getFileType() == AgreementModel::UPLOADED_FILE_FINANCIAL_FILE_TYPE ? 'fin' : 'add';
            $filePath = sfConfig::get('app_uploads_path') . '/' . ($type == "add" ? AgreementModelReport::ADDITIONAL_FILE_PATH : AgreementModelReport::FINANCIAL_DOCS_FILE_PATH) . '/' . $file->getFile();

            $file->delete();
            if ($model) {
                $model->reindexFiles();

                if (file_exists($filePath)) {
                    unlink($filePath);
                }

                $this->model = $model;
                $this->report_file_type = $type;

                if ($model->isConcept()) {
                    $this->setTemplate('loadConceptBlock');
                } else {
                    $this->setTemplate('loadBlock');
                }
            }
        } else {
            $this->setTemplate('error');
        }
    }

    public function executeLoadConceptBlock(sfWebRequest $request)
    {
        $this->model = $this->getModel($request);
    }

    public
    function executeLoadAdditionalFinancialDocsFiles(sfWebRequest $request)
    {
        $this->by_type = $request->getParameter('by_type');

        $this->report = AgreementModelReportTable::getInstance()->find($request->getParameter('id'));

    }

    public
    function attachFinancialFilesDocsToMessageN($report, $message)
    {
        $uploaded_files_list = $report->getUploadedFilesList(AgreementModelReport::UPLOADED_FILE_FINANCIAL);

        foreach ($uploaded_files_list as $file) {
            $this->saveAttachFileN($message, $file->getFile(), AgreementModelReport::FINANCIAL_DOCS_FILE_PATH . $file->getPath(), 'fin', $file->getPath());
        }
    }

    public
    function attachAdditionalFilesToMessageN($report, $message)
    {
        $uploaded_files_list = $report->getUploadedFilesList(AgreementModelReport::UPLOADED_FILE_ADDITIONAL);
        foreach ($uploaded_files_list as $file) {
            $this->saveAttachFileN($message, $file->getFile(), AgreementModelReport::ADDITIONAL_FILE_PATH . $file->getPath(), 'add', $file->getPath());
        }
    }

    private
    function saveAttachFileN($message, $file_name, $path, $label, $file_path = '')
    {
        $file = new MessageFile();
        $file->setMessageId($message->getId());
        $file->setFile($label . '-' . $message->getId() . '-' . $file_name);
        $file->setPath($file_path);

        $msg_path = sfConfig::get('sf_upload_dir') . '/' . MessageFile::FILE_PATH;
        if (!empty($file_path)) {
            $msg_path = sfConfig::get('sf_upload_dir') . '/' . MessageFile::FILE_PATH . $file_path;
            if (!file_exists($msg_path)) {
                mkdir($msg_path, 0777, true);
            }
        }

        copy(
            sfConfig::get('sf_upload_dir') . '/' . $path . '/' . $file_name,
            $msg_path . '/' . $file->getFile()
        );

        $file->save();
    }

    public
    function executeDeleteUploadedAddFinDocsFile ( sfWebRequest $request )
    {
        $file = AgreementModelReportFilesTable::getInstance()->find($request->getParameter('id'));
        if ($file) {
            $file->delete();

            return $this->sendJson(array( 'success' => true ));
        }

        return $this->sendJson(array( 'success' => false ));
    }

    public
    function executeDownloadUploadedFile ( sfWebRequest $request )
    {
        $file_id = $request->getParameter('file');

        $file_item = AgreementModelReportFilesTable::getInstance()->find($file_id);
        if ($file_item) {
            if ($file_item->getFileType() == AgreementModelReport::UPLOADED_FILE_ADDITIONAL) {
                $path = AgreementModelReport::ADDITIONAL_FILE_PATH;
            } else if ($file_item->getFileType() == AgreementModelReport::UPLOADED_FILE_FINANCIAL) {
                $path = AgreementModelReport::FINANCIAL_DOCS_FILE_PATH;
            }

            $filePath = sfConfig::get('app_uploads_path') . '/' . $path . '/' . $file_item->getFileName();
            if (file_exists($filePath)) {
                $file = basename($filePath);

                $file_download_result = F::downloadFile($filePath, $file);
                if (empty($file_download_result)) {
                    $this->getResponse()->setContentType('application/json');
                    $this->getResponse()->setContent(json_encode(array('success' => false, 'message' => 'Файл не найден')));
                } else {
                    $file_download_result != 'success' ? $this->redirect($file_download_result) : '';
                }
            }
        }

        return sfView::NONE;
    }

    /**
     * Download files by model and model file type
     * @param sfWebRequest $request
     * @throws sfStopException
     */
    public
    function executeDownloadAllFiles ( sfWebRequest $request )
    {
        $report = AgreementModelReportTable::getInstance()->find($request->getParameter('id'));
        $by_type = $request->getParameter('model_file_type');

        $this->redirect(ModelReportFiles::packUploadedFilesToZip($report, $by_type));
    }
}
