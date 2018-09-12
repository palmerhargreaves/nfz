<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 02.04.2017
 * Time: 11:51
 */

class AgreementModelSpecialistStatus extends ModelReportStatus implements AgreementModelStatusInterface
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
        $this->model->setDesignerStatus('accepted');
        $this->model->save();

        if ($this->model->isModelScenario()) {
            $this->model->setStep2($this->model->getStep1() == 'accepted' && $this->model->getManagerStatus() == 'accepted' ? 'accepted' : 'wait');
            if ($this->model->getStep1() != 'accepted') {
                $this->model->setStep1($this->model->getManagerStatus() == 'accepted' ? 'accepted' : 'wait');
            }

            if ($this->model->getManagerStatus() != 'accepted' && $this->model->getManagerStatus() != 'declined') {
                $model_status = 'wait';
            } else if ($this->model->getStep2() == 'accepted' && $this->model->getStep1() == 'accepted') {
                $model_status = 'accepted';
            } else {
                $model_status = 'declined';
            }
        } else {
            $model_status = AgreementModelStatusRules::ruleForSpecialist($this->model->getManagerStatus());
        }

        /**
         * Change model status by manager / designer statuses
         */
        $this->model->setStatus($model_status);
        $this->model->save();

        $this->acceptCopySpecialistFilesAndMakeDiscussion();
        $this->showDiscussionMsg();

        $this->sendMails($this->model);
    }

    public function updateStatus()
    {
        //TODO: do not implement
    }

    /**
     * Decline model status
     * @return mixed
     */
    public function declineStatus()
    {
        $this->model->setDesignerStatus('declined');
        $this->model->setStatus($this->getSpecialistDeclineStatus(null));

        if ($this->model->isModelScenario()) {
            if ($this->model->getStep1() == "accepted") {
                $this->model->setStep2("none");
            } else {
                $this->model->setStep1("none");
            }
        }
        $this->model->save();

        //Delete messages if manager has accepted model, to not show messages in chat list
        if ($this->model->getManagerStatus() == 'accepted') {
            $this->deleteMessages();
        }

        $this->declineCopySpecialistFilesAndMakeDiscussion();
        $this->showDiscussionMsg();

        $this->sendMails($this->model);
    }

    protected function getSpecialistAcceptStatus($comment) {
        $model = $comment->getModel();

        if ($this->model->isModelScenario()) {
            if ($this->model->getStep1() == 'accepted' && $this->model->getStep2() == 'accepted') {
                return 'accepted';
            }

            return $model->getManagerStatus() != 'accepted' && $comment->getModel()->getManagerStatus() != 'declined' ? 'wait' : 'declined';
        }

        if ($model->getManagerStatus() == 'declined') {
            return 'declined';
        }

        return $model->getManagerStatus() != 'accepted' && $comment->getModel()->getManagerStatus() != 'declined' ? 'wait' : 'accepted';
    }

    protected function getSpecialistDeclineStatus($comment) {
        $model = $this->model;
        if (!is_null($comment)) {
            $model = $comment->getModel();

            return $model->getManagerStatus() != 'accepted' && $comment->getModel()->getManagerStatus() != 'declined' ? 'wait' : 'declined';
        }

        return $model->getManagerStatus() == 'accepted' ? 'declined' : $model->getManagerStatus();
    }

    protected function declineCopySpecialistFilesAndMakeDiscussion() {
        /**
         * Make copy of uploaded temp files and remove
         */
        $msg_files = TempFileTable::copyFilesByRequest($this->request, AgreementModel::AGREEMENT_COMMENTS_FILE_PATH);

        $comment = $this->model->getSpecialistComment($this->model_by->user);
        if (!$comment) {
            throw new Exception('No comments found');
        }


        $this->declineSpecialistComment($comment, $msg_files);
    }

    protected function declineSpecialistComment($comment, $msg_files)
    {
        $utils = new AgreementModelSpecialistStatusUtils();
        $utils->setMsgStatus(Message::MSG_STATUS_DECLINED);
        $utils->setMsgType(Message::MSG_TYPE_SPECIALIST);

        $utils->declineComment(
            $comment,
            $this->model_by->user,
            $this->form->getValue('agreement_comments'),
            $msg_files,
            $this->getSpecialistDeclineStatus($comment)
        );
    }

    protected function acceptCopySpecialistFilesAndMakeDiscussion() {
        /**
         * Make copy of uploaded temp files and remove
         */
        $msg_files = TempFileTable::copyFilesByRequest($this->request, AgreementModel::AGREEMENT_COMMENTS_FILE_PATH);

        $comment = $this->model->getSpecialistComment($this->user);
        if (!$comment) {
            throw new Exception('No comments found');
        }

        $this->acceptSpecialistComment($comment, $msg_files);
    }

    protected function acceptSpecialistComment($comment, $msg_files) {
        $utils = new AgreementModelSpecialistStatusUtils();
        $utils->setMsgStatus(Message::MSG_STATUS_ACCEPTED);
        $utils->setMsgType(Message::MSG_TYPE_SPECIALIST);
        $utils->acceptComment(
            $comment,
            $this->user,
            $this->form->getValue('agreement_comments'),
            $msg_files,
            $this->getSpecialistAcceptStatus($comment)
        );
    }


}
