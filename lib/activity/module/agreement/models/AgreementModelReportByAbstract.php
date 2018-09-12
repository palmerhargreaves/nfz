<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 16.03.2017
 * Time: 10:06
 */

class AgreementModelReportByAbstract implements AgreementModelByInterface {
    protected $model = null;

    public function __construct(AgreementModel $model, $params = array())
    {
        $this->model = $model;

        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * Make agreement model
     * @return mixed
     */
    public function agreement() {
        $status_text = $this->getReportAcceptText();
        $entry = LogEntryTable::getInstance()->addEntry(
            $this->user,
            $this->model->isConcept() ? 'agreement_concept_report' : 'agreement_report',
            'edit',
            $this->model->getActivity()->getName() . '/' . $this->model->getName(),
            $status_text,
            'clip',
            $this->model->getDealer(),
            $this->model->getId(),
            'agreement'
        );

        $this->model->createPrivateLogEntryForSpecialists($entry);

        $message = $this->addMessageToDiscussion($this->model, $status_text, true, Message::MSG_STATUS_SENDED);

        $this->attachFinancialFilesDocsToMessage($this->report, $message);
        $this->attachAdditionalFilesToMessage($this->report, $message);

        $this->report->setStatus('wait');
        $this->report->save();

        return $message;
    }

    public function agreementUpdate() {

    }

    /**
     * Make draft agreement model
     * @return mixed
     */
    public function agreementDraft() {

    }

    /**
     * Decline agrement model
     * @return mixed
     */
    public function decline() {
        $this->model->setWaitSpecialist(false);
        $this->model->save();

        $this->report->setManagerStatus('wait');
        $this->report->setDesignerStatus('wait');
        $this->report->save();

        $entry = LogEntryTable::getInstance()->addEntry(
            $this->user,
            $this->model->isConcept() ? 'agreement_concept_report' : 'agreement_report',
            'cancel',
            $this->model->getActivity()->getName() . '/' . $this->model->getName(),
            'Отменена отправка отчёта на согласование',
            '',
            $this->model->getDealer(),
            $this->model->getId(),
            'agreement'
        );

        $this->model->createPrivateLogEntryForSpecialists($entry);
        $this->report->cancelSpecialistSending();

        $message = $this->addMessageToDiscussion($this->model, 'отменена отправка отчёта на согласование');

        AgreementManagementHistoryMailSender::send(
            'AgreementCancelReportMail',
            $entry,
            false,
            false,
            $this->model->isConcept() ? AgreementManagementHistoryMailSender::NEW_AGREEMENT_CONCEPT_REPORT_NOTIFICATION : AgreementManagementHistoryMailSender::NEW_AGREEMENT_REPORT_NOTIFICATION
        );

        return $message;
    }

    /**
     * Cancel scenario model agreement
     */
    public function declineScenario() {
       //TODO: not implements in report
    }

    /**
     * Cancel record model agreement
     */
    public function declineRecord() {
        //TODO: not implements in report
    }

    public function agreementManagerAccept() {
        $this->report = $this->model->getReport();
        if (!$this->report)
            $this->report = new AgreementModelReport();

        //$this->setManagerAcceptStatus();

        $this->report->setModelId($this->model->getId());
        $this->report->setStatus($this->getManagerAcceptStatus());
        $this->report->save();

        $this->model->setReportId($this->report->getId());
        $this->model->save();

        /**
         * Make copy of uploaded temp files and remove
         */
        $msg_files = TempFileTable::copyFilesByRequest($this->request, AgreementModelReport::AGREEMENT_COMMENTS_FILE_PATH);

        $utils = new AgreementReportStatusUtils();
        return $utils->acceptReport($this->report, $this->user, $this->form->getValue('agreement_comments'), $msg_files, $this->getManagerAcceptStatus());
    }

    protected function setManagerAcceptStatus() { }

    protected function setManagerDeclineStatus() {
        if ($this->report) {
            $this->report->setStatus('declined');
            $this->report->setManagerStatus('wait');
            $this->report->setDesignerStatus('wait');
            $this->report->save();
        }
    }

    protected function getManagerAcceptStatus() {
        return 'accepted';
    }

    protected function getManagerDeclineStatus() {
        return 'declined';
    }

    protected function acceptManagerModel($msg_files = array()) {

    }

    public function agreementManagerDecline() {
        $report = $this->model->getReport();
        if (!$report)
            $report = new AgreementModelReport();

        $report_status = $this->getManagerDeclineStatus();
        if ($report->getStatus() == 'accepted') {
            $report_status = 'declined';
        }

        $this->setManagerDeclineStatus();

        $report->setModelId($this->model->getId());
        $report->setStatus($report_status);
        $report->setDeclineReasonId($this->form->getValue('decline_reason_id'));
        $report->setAgreementComments($this->form->getValue('agreement_comments'));

        $report->save();

        $this->model->setReportId($report->getId());
        $this->model->save();

        /**
         * Make copy of uploaded temp files and remove
         */
        $msg_files = TempFileTable::copyFilesByRequest($this->request, AgreementModelReport::AGREEMENT_COMMENTS_FILE_PATH);

        $utils = new AgreementReportStatusUtils();
        return $utils->declineReport(
            $report,
            $this->user,
            null, //AgreementDeclineReasonTable::getInstance()->find($report->getDeclineReasonId()),
            $this->form->getValue('agreement_comments'),
            $msg_files,
            $report_status
        );
    }

    /**
     * Accept model by specialist
     */
    public function agreementSpecialistAccept(){
        /**
         * Make copy of uploaded temp files and remove
         */
        $msg_files = TempFileTable::copyFilesByRequest($this->request, AgreementModelReport::AGREEMENT_COMMENTS_FILE_PATH);

        $this->report->setDesignerStatus('accepted');
        if ($this->report->getManagerStatus() == 'accepted') {
            $this->report->setStatus('accepted');
        } else {
            $this->report->setStatus('declined');
        }
        $this->report->save();

        $comment = $this->report->getSpecialistComment($this->user);
        if (!$comment) {
            throw new Exception('Report comment not found');
        }

        $utils = new AgreementReportStatusUtils();
        $utils->acceptComment($comment, $this->user, $this->form->getValue('agreement_comments'), $msg_files, 'дизайнера', 'accepted');

        $discussion = $this->model->getDiscussion();
        if ($discussion) {
            $discussion->activeDisabledMessages();
        }

        $this->sendMails($this->model);


    }

    /**
     * Decline model by specialist
     */
    public function agreementSpecialistDecline() {
        /**
         * Make copy of uploaded temp files and remove
         */
        $msg_files = TempFileTable::copyFilesByRequest($this->request, AgreementModelReport::AGREEMENT_COMMENTS_FILE_PATH);

        $this->report->setAgreementComments('');
        if ($this->agreement_comments) {
            $this->report->setAgreementComments($this->agreement_comments);
            $this->report->save();
        }

        if ($this->report->getManagerStatus() != 'wait') {
            $this->report->setDesignerStatus('declined');
            $this->report->save();
        }

        $comment = $this->report->getSpecialistComment($this->user);
        if (!$comment) {
            throw new Exception('Report comment not found');
        }

        $utils = new AgreementReportStatusUtils();
        $utils->declineComment(
            $comment,
            $this->user,
            $this->form->getValue('agreement_comments'),
            $msg_files,
            'дизайнера'
        );

        $discussion = $this->model->getDiscussion();
        if ($discussion) {
            $discussion->activeDisabledMessages();
        }

        $this->sendMails($this->model);
    }

    /**
     * Add message to discussion
     *
     * @param AgreementModel $model
     * @param string $text
     * @return Message|false
     */
    protected function addMessageToDiscussion(AgreementModel $model, $text, $msg_show = true, $msg_status = 'none')
    {
        $discussion = $model->getDiscussion();
        if (!$discussion) {
            return;
        }

        $message = new Message();
        $user = $this->user;
        $message->setDiscussionId($discussion->getId());
        $message->setUser($user);
        $message->setUserName($user->selectName());
        $message->setText($text);
        $message->setSystem(true);
        $message->setMsgStatus($msg_status);
        $message->save();

        // mark as unread
        $discussion->getUnreadMessages($user);

        return $message;
    }

    protected function getReportAcceptText() {
        return 'Отчёт отправлен на согласование';
    }

    /**
     *
     * @param AgreementModel $model
     */
    protected function sendMails(AgreementModel $model) {
        $mails_list = MailMessageTable::getInstance()->createQuery()->where('model_id = ? and can_send = ?', array($model->getId(), false))->execute();
        foreach ($mails_list as $mail) {
            $mail->setCanSend(true);
            $mail->save();
        }
    }

    //
    //Report
    protected function attachFinancialFilesDocsToMessage($report, $message)
    {
        $uploaded_files_list = $report->getUploadedFilesList(AgreementModelReport::UPLOADED_FILE_FINANCIAL);

        foreach ($uploaded_files_list as $file) {
            $this->saveAttachFile($message, $file->getFile(), AgreementModelReport::FINANCIAL_DOCS_FILE_PATH, 'fin');
        }
    }

    protected function attachAdditionalFilesToMessage($report, $message)
    {
        $uploaded_files_list = $report->getUploadedFilesList(AgreementModelReport::UPLOADED_FILE_ADDITIONAL);
        foreach ($uploaded_files_list as $file) {
            $this->saveAttachFile($message, $file->getFile(), AgreementModelReport::ADDITIONAL_FILE_PATH, 'add');
        }
    }

    protected function saveAttachFile($message, $file_name, $path, $label)
    {
        $file = new MessageFile();
        $file->setMessageId($message->getId());
        $file->setFile($label . '-' . $message->getId() . '-' . $file_name);

        copy(
            sfConfig::get('sf_upload_dir') . '/' . $path . '/' . $file_name,
            sfConfig::get('sf_upload_dir') . '/' . MessageFile::FILE_PATH . '/' . $file->getFile()
        );

        $file->save();
    }
}
