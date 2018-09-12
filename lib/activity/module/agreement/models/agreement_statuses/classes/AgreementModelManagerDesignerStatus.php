<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 02.04.2017
 * Time: 11:51
 */

class AgreementModelManagerDesignerStatus extends ModelReportStatus implements AgreementModelStatusInterface
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
        /**
         * Make copy of uploaded temp files and remove
         */
        $msg_files = TempFileTable::copyFilesByRequest($this->request, AgreementModel::AGREEMENT_COMMENTS_FILE_PATH);

        $connection = Doctrine_Manager::getInstance()->getConnection('doctrine');
        $connection->beginTransaction();
        try {
            $this->model->setManagerStatus('accepted');
            if ($this->model->isModelScenario()) {
                if ($this->model->getStep1() != "accepted") {
                    $this->model->setStep1($this->model->getDesignerStatus() == 'accepted' && $this->model->getStep1() != 'accepted' ? 'accepted' : 'wait');

                    $this->model->setStep2('none');
                    $this->model->setStatus($this->getManagerAcceptStatus());
                    $this->model->save();

                    $this->acceptManagerModel($msg_files);

                    $this->showDiscussionMsg();
                    $this->sendMails($this->model);

                    return array('form' => $this->form, 'response' => 'window.accept_decline_form.onResponse');
                } else if ($this->model->getStep2() != "accepted" && $this->model->getStep1() == 'accepted') {
                    $this->model->setStep2(($this->model->getStep1() == 'accepted' && $this->model->getDesignerStatus() == 'accepted') ? 'accepted' : 'wait');
                    $this->model->setStatus($this->getManagerAcceptStatus());
                }
            } else {
                $this->model->setStatus($this->getManagerAcceptStatus());
            }
            $this->model->save();

            $connection->commit();
        }
        catch(Exception $e) {
            $connection->rollback();
        }

        $message = $this->acceptManagerModel($msg_files);

        $this->showDiscussionMsg();
        $this->sendMails($this->model);

        return $message;
    }

    public function updateStatus()
    {
        // TODO: Implement updateStatus() method.
    }

    /**
     * Decline model status
     * @return mixed
     */
    public function declineStatus()
    {
        /**
         * Make copy of uploaded temp files and remove
         */
        $msg_files = TempFileTable::copyFilesByRequest($this->request, AgreementModel::AGREEMENT_COMMENTS_FILE_PATH);

        if ($this->model->getStatus() == 'accepted') {
            $this->model->setDesignerStatus('declined');
        }
        $this->model->setManagerStatus('declined');

        if ($this->model->getStep1() != 'accepted') {
            $this->model->setStep1('none');
        }

        if ($this->model->getStep2() != 'accepted') {
            $this->model->setStep2('none');
        }

        //При отклонении менеджером, если стоит галочка В макет не вносились изменения, убираем ее
        if ($this->model->getNoModelChanges()) {
            $this->model->setNoModelChanges(false);
            $this->model->setNoModelChangesView(false);
        }

        $this->model->setStatus($this->getManagerDeclineStatus());
        $this->model->setDeclineReasonId($this->form->getValue('decline_reason_id'));
        $this->model->save();

        $utils = new AgreementModelManagerDesignerStatusUtils();
        $utils->setMsgStatus(Message::MSG_STATUS_DECLINED);
        $utils->setMsgType(Message::MSG_TYPE_MANAGER);

        $message = $utils->declineModel(
            $this->model,
            $this->user,
            null, //AgreementDeclineReasonTable::getInstance()->find($model->getDeclineReasonId()),
            $this->form->getValue('agreement_comments'),
            $msg_files,
            $this->getManagerDeclineStatus()
        );

        $this->showDiscussionMsg();
        $this->sendMails($this->model);

        return $message;
    }

    protected function getManagerAcceptStatus()
    {
        if ($this->model->isModelScenario()) {
            return $this->model->getStep1() == 'accepted' && $this->model->getStep2() == 'accepted' ? 'accepted' :
                (($this->model->getDesignerStatus() != 'accepted' && $this->model->getDesignerStatus() != 'declined') ? 'wait_specialist' : 'declined');
        }

        return $this->model->getDesignerStatus() == 'accepted' ? 'accepted' : ($this->model->getDesignerStatus() == 'declined' ? 'declined' : 'wait_specialist');
    }

    protected function getManagerDeclineStatus()
    {
        return $this->model->getDesignerStatus() == 'accepted' ? 'declined' : ($this->model->getDesignerStatus() == 'declined' ? 'declined' : 'wait_specialist');
    }

    protected function acceptManagerModel($msg_files = array()) {
        $utils = new AgreementModelManagerDesignerStatusUtils();
        $utils->setMsgStatus(Message::MSG_STATUS_ACCEPTED);
        $utils->setMsgType(Message::MSG_TYPE_MANAGER);

        return $utils->acceptModel(
            $this->model,
            $this->user,
            $this->form->getValue('agreement_comments'),
            $msg_files,
            false,
            $this->getManagerAcceptStatus()
        );
    }
}
