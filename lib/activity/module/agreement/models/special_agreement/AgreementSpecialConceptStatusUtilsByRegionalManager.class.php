<?php

/**
 * Description of AgreementStatusUtils
 *
 * @author Сергей
 */
class AgreementSpecialConceptStatusUtilsByRegionalManager extends AgreementModelStatusUtils
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
        $message->setMsgStatus($msg_status);
        $message->setMsgType($this->_msg_type);
        $message->save();

        // mark as unread
        $discussion->getUnreadMessages($user);

        return $message;
    }

    protected function canSendMail() {
        return false;
    }

    function acceptModelOnlyMail(AgreementModel $model, User $user, $comments = '', $msg_files = array(), $message = null)
    {
        $discussionLabel = 'Концепция согласована региональным менеджером.';
        $this->addMessageToDiscussion(
            $model,
            $user,
            $discussionLabel,
            false
        );

        /*Agreement models files */
        $commentFiles = array();
        if (!empty($msg_files)) {
            foreach ($msg_files as $file) {
                $commentFiles[] = $file;
                $model->addAcceptFile($file);
            }
        }

        $message = null;
        if (!empty($comments)) {
            $message = $this->addMessageToDiscussion(
                $model,
                $user,
                'Комментарий регионального менеджера. ' . (!empty($comments) ? $comments : ''),
                false
            );
        }

        if (!$message && count($commentFiles) > 0) {
            $message = $this->addMessageToDiscussion(
                $model,
                $user,
                'Комментарий регионального менеджера. ',
                false
            );
        }

        if ($message && !empty($msg_files)) {
            $this->attachModelCommentsFileToMessage($model, $message, $commentFiles, true);
        }

        AgreementCompleteModelMailSender::send($model, $this->canSendMail(), $this->getMsgType());
    }

    function declineModelOnlyMail(AgreementModel $model, User $user, $comments = '', $msg_files = array(), $can_send_mail = true)
    {
        $this->_msg_type = Message::MSG_TYPE_REGIONAL_MANAGER;

        /*Agreement status*/
        //$statusLabel = $model->isConcept() ? 'Концепция согласована.' . ($user->isDesigner() ? 'дизайнером' : 'менеджером') . '.' : 'Макет согласован ' . ($user->isDesigner() ? 'дизайнером' : 'менеджером') . '.';
        $discussionLabel = 'Концепция не согласована. Внесите комментарии.';
        $this->addMessageToDiscussion(
            $model,
            $user,
            $discussionLabel,
            false,
            Message::MSG_STATUS_DECLINED_TO_SPECIALST
        );

        $commentFiles = array();
        if (!empty($msg_files)) {
            $commentFiles = array_shift($msg_files);

            foreach ($msg_files as $file) {
                $model->addDeclineFile($file);
            }
        }

        $entry = LogEntryTable::getInstance()->addEntry(
            $user,
            'agreement_concept',
            'declined',
            $model->getActivity()->getName() . '/' . $model->getName(),
            //$reason ? $reason->getName() . '.' : ($model->isConcept() ? 'Концепция отклонена.' : 'Макет отклонён.'),
            !empty($comments) ?: $discussionLabel,
            !empty($msg_files) ? 'clip' : '',
            $model->getDealer(),
            $model->getId(),
            'agreement',
            true
        );

        $message = null;
        if (!empty($comments)) {
            $message = $this->addMessageToDiscussion(
                $model,
                $user,
                'Комментарий регионального менеджера. ' . (!empty($comments) ? $comments : ''),
                false,
                Message::MSG_STATUS_DECLINED_TO_SPECIALST
            );
        }

        if (!$message && !empty($commentFiles)) {
            $message = $this->addMessageToDiscussion(
                $model,
                $user,
                'Комментарий регионального менеджера.',
                false,
                Message::MSG_STATUS_DECLINED_TO_SPECIALST
            );
        }

        if ($message && !empty($commentFiles)) {
            $this->attachModelCommentsFileToMessage($model, $message, $msg_files, false);
        }

        AgreementDealerHistoryMailSender::send('AgreementModelDeclinedMailSpecialConceptRegManager', $entry, $model->getDealer(), $message, $can_send_mail, $this->getMsgType());
    }

    function acceptModel(AgreementModel $model, User $user, $comments = '', $msg_files = array(), $isSpecialist = false)
    {
        $message = null;

        /*Agreement status*/
        $statusLabelDiscussion = 'Концепция согласована региональным менеджером';

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
                $message = $this->addMessageToDiscussion($model, $user, 'Комментарий регионального менеджера. '.$comments);
            }

            if (!$message && count($commentFiles) > 0) {
                $message = $this->addMessageToDiscussion($model, $user, 'Комментарий регионального менеджера. ');
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
            $message_text = 'Комментарии регионального менеджера. '.$comments;
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

        AgreementDealerHistoryMailSender::send('AgreementModelDeclinedMailSpecialConceptRegManager',
            $entry,
            $model->getDealer(),
            $message,
            $this->canSendMail(),
            $this->getMsgType());

        AgreementManagementHistoryMailSender::send(
            'AgreementModelDeclinedMailSpecialConceptRegManager',
            $entry,
            false,
            false,
            $model->isConcept() ? AgreementManagementHistoryMailSender::AGREEMENT_CONCEPT_NOTIFICATION : AgreementManagementHistoryMailSender::AGREEMENT_NOTIFICATION,
            null,
            $this->canSendMail(),
            $this->getMsgType()
        );

        return $message;
    }
}
