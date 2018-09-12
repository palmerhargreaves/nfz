<?php

/**
 * Description of AgreementStatusUtils
 *
 * @author Сергей
 */
class AgreementSpecialConceptStatusUtilsByImporter extends AgreementModelStatusUtils
{
    /**
     * Add message to discussion
     *
     * @param AgreementModel $model
     * @param string $text
     * @return Message|false
     */
    function addMessageToDiscussion(AgreementModel $model, User $user, $text, $show_msg = true, $msg_status = 'none')
    {
        $discussion = $model->getDiscussion();

        if (!$discussion) {
            return;
        }

        if (empty($text)) {
            return;
        }

        $message = new Message();
        $message->setDiscussionId($discussion->getId());
        $message->setUser($user);
        $message->setUserName($user->selectName());
        $message->setText($text);
        $message->setSystem(true);
        $message->setMsgShow(false);
        $message->setMsgStatus($this->_msg_status);
        $message->setMsgType($this->_msg_type);
        $message->save();

        // mark as unread
        $discussion->getUnreadMessages($user);

        return $message;
    }

    protected function canSendMail() {
        return false;
    }

    function getDiscussionDeclineMessage(AgreementModel $model) {
        $statusLabel = $statusDiscussionLabel = 'Концепция отклонена. Внесите комментарии.';

        return array($statusLabel, $statusDiscussionLabel);
    }

    function acceptModel(AgreementModel $model, User $user, $comments = '', $msg_files = array(), $isSpecialist = false)
    {
        $message = null;

        /*Agreement status*/
        $statusLabelDiscussion = 'Концепция согласована специалистом';

        //$model->acceptModelWithMD();
        $model->setAgreementComments($comments);
        $model->save();

        if (!$this->_accept_decline_message) {
            LogEntryTable::getInstance()->addEntry(
                $user,
                'agreement_concept',
                'accepted',
                $model->getActivity()->getName() . '/' . $model->getName(),
                $statusLabelDiscussion,
                'ok',
                $model->getDealer(),
                $model->getId(),
                'agreement',
                true
            );

            $commentFiles = array();
            if (!empty($msg_files)) {
                foreach ($msg_files as $file) {
                    $commentFiles[] = $file;
                    $model->addAcceptFile($file);
                }
            }

//    $model->createPrivateLogEntryForSpecialists($entry);
            $message = $this->addMessageToDiscussion($model, $user, $statusLabelDiscussion);
            if (!empty($comments)) {
                $message = $this->addMessageToDiscussion($model, $user, 'Комментарий специалиста. '.$comments);
            }

            if (!$message && count($commentFiles) > 0) {
                $message = $this->addMessageToDiscussion($model, $user, 'Комментарий специалиста. ');
            }

            if ($message && count($commentFiles) > 0) {
                $this->attachModelCommentsFileToMessage($model, $message, $commentFiles, true);
            }

            //AgreementDealerHistoryMailSender::send('AgreementModelAcceptedMail', $entry, $model->getDealer());
            //AgreementManagementHistoryMailSender::send('AgreementModelAcceptedMail', $entry, false, false, AgreementManagementHistoryMailSender::FINAL_AGREEMENT_NOTIFICATION);
            AgreementCompleteModelMailSender::send($model, $this->canSendMail(), $this->getMsgType());
        }

        return $message;
    }

    function declineModel(AgreementModel $model, User $user, AgreementDeclineReason $reason = null, $comments = '', $msg_files = array(), $model_status = '')
    {
        $message = null;

        $model->setStatus(empty($model_status) ? 'declined' : $model_status);
        $model->setDeclineReasonId($reason ? $reason->getId() : 0);
        $model->save();

        $report = $model->getReport();
        if ($report) {
            $report->setStatus('not_sent');
            $report->save();
        }

        $utils = new AgreementActivityStatusUtils($model->getActivity(), $model->getDealer());
        $utils->updateActivityAcceptance();

        RealBudgetTable::getInstance()->removeByObjectOnly(ActivityModule::byIdentifier('agreement'), $model->getId());

        if (!empty($comments)) {
            $model->setAgreementComments($comments);
        }

        $model->setAgreementCommentsFile('');
        if (!empty($msg_files)) {
            $model->setAgreementCommentsFile(array_shift($msg_files));
        }
        $model->save();

        $commentFiles = array();
        if (!empty($msg_files)) {
            foreach ($msg_files as $file)
            {
                $commentFiles[] = $file;
                $model->addDeclineFile($file);
            }
        }

        $statusLabel = $statusDiscussionLabel = $reason ? $reason->getName() . '.' :
                'Концепция отклонена. Внесите комментарии.';

        $entry = LogEntryTable::getInstance()->addEntry(
            $user,
            'agreement_concept',
            'declined',
            $model->getActivity()->getName() . '/' . $model->getName(),
            //$reason ? $reason->getName() . '.' : ($model->isConcept() ? 'Концепция отклонена.' : 'Макет отклонён.'),
            $statusLabel,
            $msg_files ? 'clip' : '',
            $model->getDealer(),
            $model->getId(),
            'agreement',
            true
        );

//    $model->createPrivateLogEntryForSpecialists($entry);
        $discussionLabel = ($reason ? $reason->getName() . '. ' : '') . $statusDiscussionLabel;
        $message = $this->addMessageToDiscussion(
            $model,
            $user,
            $discussionLabel,
            true,
            Message::MSG_STATUS_DECLINED
        );

        if (!empty($comments)) {
            $message_text = 'Комментарии специалиста. '.$comments;
            $message = $this->addMessageToDiscussion(
                $model,
                $user,
                $message_text,
                true,
                Message::MSG_STATUS_DECLINED
            );
        }

        if ($message && (!empty($msg_files) || $model->getAgreementCommentsFile())) {
            $this->attachModelCommentsFileToMessage($model, $message, $commentFiles);
        }

        AgreementDealerHistoryMailSender::send('AgreementModelDeclinedMailSpecialConceptImporter',
            $entry,
            $model->getDealer(),
            $message,
            $this->canSendMail(),
            $this->getMsgType());

        AgreementManagementHistoryMailSender::send(
            'AgreementModelDeclinedMailSpecialConceptImporter',
            $entry,
            false,
            false,
            $model->isConcept() ? AgreementManagementHistoryMailSender::AGREEMENT_CONCEPT_NOTIFICATION : AgreementManagementHistoryMailSender::AGREEMENT_NOTIFICATION,
            null,
            $this->canSendMail(),
            $this->getMsgType()
        );

        $this->_accept_decline_message = false;

        return $message;
    }
}
