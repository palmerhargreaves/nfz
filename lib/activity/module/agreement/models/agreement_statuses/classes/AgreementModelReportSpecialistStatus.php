<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 02.04.2017
 * Time: 11:51
 */

class AgreementModelReportSpecialistStatus extends ModelReportStatus  implements AgreementModelStatusInterface
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
     * Decline model status
     * @return mixed
     */
    public function declineStatus()
    {
        $report_status = $this->getSpecialistDeclineStatus(null);

        $this->report->setDesignerStatus('declined');
        $this->report->setStatus($report_status);
        $this->report->save();

        /**
         * Make copy of uploaded temp files and remove
         */
        $msg_files = TempFileTable::copyFilesByRequest($this->request, AgreementModelReport::AGREEMENT_COMMENTS_FILE_PATH);

        $this->report->setAgreementComments('');
        if ($this->agreement_comments) {
            $this->report->setAgreementComments($this->agreement_comments);
            $this->report->save();
        }

        $comment = $this->report->getSpecialistComment($this->user);
        if (!$comment) {
            throw new Exception('Report comment not found');
        }

        $utils = new AgreementModelReportManagerDesignerStatusUtils();
        $utils->setMsgStatus(Message::MSG_STATUS_DECLINED);
        $utils->declineComment(
            $comment,
            $this->user,
            $this->form->getValue('agreement_comments'),
            $msg_files,
            'дизайнера',
            $report_status
        );

        $this->sendReportMails($this->report);
        $this->showReportDiscussionMsg();
    }

    /**
     * Agreement model status
     * @return mixed
     */
    public function acceptStatus()
    {
        $this->report->setDesignerStatus('accepted');

        /**
         * Make copy of uploaded temp files and remove
         */
        $msg_files = TempFileTable::copyFilesByRequest($this->request, AgreementModelReport::AGREEMENT_COMMENTS_FILE_PATH);

        /**
         * Change report status by manager / designer statuses
         */
        $report_status = AgreementModelStatusRules::ruleForSpecialist($this->report->getManagerStatus());
        $this->report->setStatus($report_status);
        $this->report->save();

        $comment = $this->report->getSpecialistComment($this->user);
        if (!$comment) {
            throw new Exception('Report comments not found');
        }

        $utils = new AgreementModelReportManagerDesignerStatusUtils();
        $utils->setMsgStatus(Message::MSG_STATUS_ACCEPTED);
        $utils->acceptComment($comment, $this->user, $this->form->getValue('agreement_comments'), $msg_files, 'дизайнера', $report_status);

        $this->sendReportMails($this->report);
        $this->showReportDiscussionMsg();
    }

    /**
     * Agreement model update
     * @return mixed
     */
    public function updateStatus()
    {
        // TODO: Implement updateStatus() method.
    }

    protected function getSpecialistDeclineStatus($comment) {
        $report = $this->report;
        if (!is_null($comment)) {
            $report = $comment->getReport();

            return $report->getManagerStatus() != 'accepted' && $comment->getReport()->getManagerStatus() != 'declined' ? 'wait' : 'declined';
        }

        return $report->getManagerStatus() == 'accepted' ? 'declined' : $report->getManagerStatus();
    }

    /**
     * Show message model when accepted / declined by current state of manager / designer
     * @return bool
     */
    protected function showAllMessages() {
        if ($this->report->getManagerStatus() == 'accepted' && $this->report->getDesignerStatus() == 'accepted') {
            return true;
        }

        return ($this->report->getManagerStatus() != 'wait' && $this->report->getDesignerStatus() != 'wait');
    }

    /**
     * Can show message when accepted / declined model
     * @return bool
     */
    protected function canShowMessage() {
        return $this->report->getManagerStatus() != 'wait' && $this->report->getDesignerStatus() != 'wait' ? true : false;
    }
}
