<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 02.04.2017
 * Time: 11:51
 */

class AgreementModelReportManagerDesignerStatus extends ModelReportStatus implements AgreementModelStatusInterface
{
    /**
     * Get model discussion status text
     * @return mixed
     */
    public function getStatusText()
    {
        // TODO: Implement getStatusText() method.
    }

    /**
     * Agreement model status
     * @return mixed
     */
    public function acceptStatus()
    {
        $this->report = $this->model->getReport();
        if (!$this->report)
            $this->report = new AgreementModelReport();

        $this->report->setManagerStatus('accepted');

        $this->report->setModelId($this->model->getId());
        $this->report->setStatus($this->getManagerAcceptStatus());
        $this->report->save();

        $this->model->setReportId($this->report->getId());
        $this->model->save();

        /**
         * Make copy of uploaded temp files and remove
         */
        $msg_files = TempFileTable::copyFilesByRequest($this->request, AgreementModelReport::AGREEMENT_COMMENTS_FILE_PATH);

        $utils = new AgreementModelReportManagerDesignerStatusUtils();
        $utils->setMsgStatus(Message::MSG_STATUS_ACCEPTED);
        $utils->acceptReport($this->report, $this->user, $this->form->getValue('agreement_comments'), $msg_files, $this->getManagerAcceptStatus());

        $this->sendReportMails($this->report);
        $this->showReportDiscussionMsg();
    }

    /**
     * Decline model status
     * @return mixed
     */
    public function declineStatus()
    {
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

        $utils = new AgreementModelReportManagerDesignerStatusUtils();
        $utils->setMsgStatus(Message::MSG_STATUS_DECLINED);
        $utils->declineReport(
            $report,
            $this->user,
            null, //AgreementDeclineReasonTable::getInstance()->find($report->getDeclineReasonId()),
            $this->form->getValue('agreement_comments'),
            $msg_files,
            $report_status
        );

        $this->sendReportMails($this->report);
        $this->showReportDiscussionMsg();
    }

    public function updateStatus()
    {
        // TODO: Implement updateStatus() method.
    }

    protected function getManagerAcceptStatus() {
        return $this->report->getDesignerStatus() == 'accepted' ? 'accepted' : ($this->report->getDesignerStatus() == 'declined' ? 'declined' : 'wait_specialist');
    }

    protected function getManagerDeclineStatus() {
        return 'declined';//$this->report->getDesignerStatus() == 'accepted' ? 'declined' : ($this->report->getDesignerStatus() == 'declined' ? 'declined' : 'wait_specialist');
    }

    protected function setManagerAcceptStatus() {

    }

    protected function setManagerDeclineStatus() {
        if ($this->report->getStatus() == 'accepted') {
            $this->report->setStatus('declined');
            $this->report->setManagerStatus('wait');
            $this->report->setDesignerStatus('wait');
            $this->report->save();
        } else {
            $this->report->setManagerStatus('declined');
            $this->report->save();
        }
    }

    /**
     * Show message model when accepted / declined by current state of manager / designer
     * @return bool
     */
    protected function showAllMessages() {
        if ($this->report->getManagerStatus() == 'accepted' && $this->report->getDesignerStatus() == 'accepted') {
            return true;
        }

        return !($this->report->getManagerStatus() != 'wait' && $this->report->getDesignerStatus() != 'wait');
    }

    /**
     * Can show message when accepted / declined model
     * @return bool
     */
    protected function canShowMessage() {
        return $this->report->getManagerStatus() != 'wait' && $this->report->getDesignerStatus() != 'wait' ? true : false;
    }
}
