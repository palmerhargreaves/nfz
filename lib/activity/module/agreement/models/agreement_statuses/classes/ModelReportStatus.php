<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 02.04.2017
 * Time: 14:06
 */

class ModelReportStatus {
    const SEND_BOTH = 'both';
    const SEND_MANAGER = 'manager';
    const SEND_DESIGNER = 'designer';

    protected $params = null;

    public function __construct($params)
    {
        $this->params = $params;

        foreach ($params as $key => $value) {
            $this->{$key} = $value;
        }

        if (isset($this->obj)) {
            $this->model = $this->obj;
        }
    }

    protected function showDiscussionMsg() {
        $discussion = $this->model->getDiscussion();
        if ($discussion) {
            $discussion->activeDisabledMessages($this->canShowMessage(), $this->showAllMessages());
        }
    }

    /**
     * Delete model discussion messages
     */
    protected function deleteMessages() {
        $discussion = $this->model->getDiscussion();
        if ($discussion) {
            $discussion->deleteInactiveMessages();
        }
    }

    /**
     * Show message model when accepted / declined by current state of manager / designer
     * @return bool
     */
    protected function showAllMessages() {
        if ($this->model->getManagerStatus() == 'accepted' && $this->model->getDesignerStatus() == 'accepted') {
            return true;
        }

        return !($this->model->getManagerStatus() != 'wait' && $this->model->getDesignerStatus() != 'wait');
    }

    /**
     * Can show message when accepted / declined model
     * @return bool
     */
    protected function canShowMessage() {
        return ($this->model->getManagerStatus() != 'wait' && $this->model->getDesignerStatus() != 'wait') ? true : false;
    }

    protected function showReportDiscussionMsg() {
        $discussion = $this->model->getDiscussion();
        if ($discussion) {
            $discussion->activeDisabledMessages($this->canShowMessage(), $this->showAllMessages());
        }
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
        $message->setMsgShow($msg_show);
        $message->save();

        // mark as unread
        $discussion->getUnreadMessages($user);

        return $message;
    }

    /**
     * Отправка писем по статусу согласования менеджера / специалиста
     * @param AgreementModel $model
     */
    protected function sendMails(AgreementModel $model) {
        MailMessageTable::sendMails($model);
    }

    /**
     *
     * @param AgreementModelReport $report
     */
    protected function sendReportMails(AgreementModelReport $report) {
        if ($report->getManagerStatus() != 'wait' && $report->getDesignerStatus() != 'wait') {
            $mails_list = MailMessageTable::getInstance()->createQuery()->where('model_id = ? and can_send = ?', array($report->getModel()->getId(), false))->execute();
            foreach ($mails_list as $mail) {
                $mail->setCanSend(true);
                $mail->save();
            }
        }
    }
}
