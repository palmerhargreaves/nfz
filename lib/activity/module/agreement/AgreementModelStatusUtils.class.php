<?php

/**
 * Description of AgreementStatusUtils
 *
 * @author Сергей
 */
class AgreementModelStatusUtils
{
    private $_accept_decline_message = false;

    function acceptModel(AgreementModel $model, User $user, $comments = '', $msg_files = array(), $isSpecialist = false)
    {
        $message = null;

        /*Agreement status*/
        $statusLabelDiscussion = $model->isConcept() ? 'Концепция согласована' : 'Макет согласован';
        if ($model->isModelScenario()) {
            if (($model->getStatus() == "not_sent" || $model->getStatus() == "wait_specialist") && $model->getStep1() == "accepted" && $model->getStep2() == "wait") {
                $statusLabelDiscussion = 'Сценарий согласован.';
            } else if ($model->getStep1() == "accepted" && $model->getStep2() == "accepted") {
                $statusLabelDiscussion = 'Запись согласована.';
                $model->setStatus('accepted');
            }
        } else {
            if ($model->getManagerStatus() == 'wait' && $model->getDesignerStatus() == 'wait') {
                $statusLabelDiscussion = ($model->isConcept() ? 'Концепция согласована. ' : 'Макет согласован. ');
            } else {
                $statusLabelDiscussion = ($model->isConcept() ? 'Концепция согласована. ' : 'Макет согласован. ');
            }
        }

        //$model->acceptModelWithMD();
        $model->setAgreementComments($comments);
        $model->save();

        if (!$this->_accept_decline_message) {
            LogEntryTable::getInstance()->addEntry(
                $user,
                $model->isConcept() ? 'agreement_concept' : 'agreement_model',
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
                $message = $this->addMessageToDiscussion($model, $user, 'Комментарий менеджера. '.$comments);
            }

            if (!$message && count($commentFiles) > 0) {
                $message = $this->addMessageToDiscussion($model, $user, 'Комментарий менеджера. ');
            }

            if ($message && count($commentFiles) > 0) {
                $this->attachModelCommentsFileToMessage($model, $message, $commentFiles, true);
            }

            //AgreementDealerHistoryMailSender::send('AgreementModelAcceptedMail', $entry, $model->getDealer());
            //AgreementManagementHistoryMailSender::send('AgreementModelAcceptedMail', $entry, false, false, AgreementManagementHistoryMailSender::FINAL_AGREEMENT_NOTIFICATION);
            AgreementCompleteModelMailSender::send($model);
        }

        return $message;
    }

    function acceptModelOnlyMail(AgreementModel $model, User $user, $comments = '', $msg_files = array(), $message = null)
    {
        $discussionLabel = $model->isConcept() ? 'Концепция согласована.' : 'Макет согласован.';
        if ($model->isModelScenario()) {
            if ($model->getStep1() != "accepted" || ($model->getStep1() == 'accepted' && $model->getStep2() == 'none')) {
                $discussionLabel = 'Сценарий согласован.';
            } else if ($model->getStep2() == "wait" || $model->getStep2() == 'accepted') {
                $discussionLabel = 'Запись согласована.';
            }
        }

        $this->addMessageToDiscussion(
            $model,
            $user,
            $discussionLabel,
            false
        );

        /*Agreement status*/
        /*Agreement models files */
        $commentFiles = array();
        if (!empty($msg_files)) {
            foreach ($msg_files as $file) {
                $commentFiles[] = $file;
                $model->addAcceptFile($file);
            }
        }

        if (!empty($comments)) {
            $message = $this->addMessageToDiscussion(
                $model,
                $user,
                'Комментарий менеджера. ' . (!empty($comments) ? $comments : ''),
                false
            );
        }

        if (!$message && count($commentFiles) > 0) {
            $message = $this->addMessageToDiscussion(
                $model,
                $user,
                'Комментарий менеджера. ',
                false
            );
        }

        if ($message && !empty($msg_files)) {
            $this->attachModelCommentsFileToMessage($model, $message, $commentFiles, true);
        }

        AgreementCompleteModelMailSender::send($model, false);
    }

    function declineModel(AgreementModel $model, User $user, AgreementDeclineReason $reason = null, $comments = '', $msg_files = array(), $model_status = '')
    {
        $message = null;

        $model->setStatus(empty($model_status) ? 'declined' : $model_status);
        $model->setDeclineReasonId($reason ? $reason->getId() : 0);

        if ($model->getManagerStatus() == 'wait') {
            $model->setAgreementComments($comments);
        }
        $model->save();

        $report = $model->getReport();
        if ($report) {
            $report->setStatus('not_sent');
            $report->save();
        }

        $utils = new AgreementActivityStatusUtils($model->getActivity(), $model->getDealer());
        $utils->updateActivityAcceptance();

        RealBudgetTable::getInstance()->removeByObjectOnly(ActivityModule::byIdentifier('agreement'), $model->getId());

        /*Agreement status*/
        if (!$this->_accept_decline_message) {
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
                ($model->isConcept() ?
                    'Концепция отклонена. Внесите комментарии.':
                    'Макет отклонен. Внесите комментарии.');
            if ($model->getModelType()->getId() == 4 || $model->getModelType()->getId() == 2) {
                if ($model->getStatus() == "declined" && $model->getStep1() == "none" && $model->getStep2() == "none") {
                    $statusLabel = $reason ? $reason->getName() . '.' : "Сценарий отклонён";
                    $statusDiscussionLabel = 'Сценарий отклонен. Внесите комментарии.';
                } else if ($model->getStatus() == "declined" && $model->getStep2() == "none") {
                    $statusLabel = $reason ? $reason->getName() . '.' : "Запись отклонёна";
                    $statusDiscussionLabel = 'Запись отклонена. Внесите комментарии.';
                }
            }

            $entry = LogEntryTable::getInstance()->addEntry(
                $user,
                $model->isConcept() ? 'agreement_concept' : 'agreement_model',
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
            if ($model->getModelType()->getId() == 4 || $model->getModelType()->getId() == 2) {
                $discussionLabel = $statusDiscussionLabel;
            }

            $message = $this->addMessageToDiscussion(
                $model,
                $user,
                $discussionLabel
            );

            if (!empty($comments)) {
                $message_text = 'Комментарии менеджера. '.$comments;
                $message = $this->addMessageToDiscussion(
                    $model,
                    $user,
                    $message_text
                );
            }

            if ($message && (!empty($msg_files) || $model->getAgreementCommentsFile())) {
                $this->attachModelCommentsFileToMessage($model, $message, $commentFiles);
            }

            AgreementDealerHistoryMailSender::send('AgreementModelDeclinedMail', $entry, $model->getDealer(), $message);
            AgreementManagementHistoryMailSender::send(
                'AgreementModelDeclinedMail',
                $entry,
                false,
                false,
                $model->isConcept() ? AgreementManagementHistoryMailSender::AGREEMENT_CONCEPT_NOTIFICATION : AgreementManagementHistoryMailSender::AGREEMENT_NOTIFICATION
            );

            $this->_accept_decline_message = false;
        }

        return $message;
    }

    function declineModelOnlyMail(AgreementModel $model, User $user, $comments = '', $msg_files = array(), $can_send_mail = true)
    {
        /*Agreement status*/
        //$statusLabel = $model->isConcept() ? 'Концепция согласована.' . ($user->isDesigner() ? 'дизайнером' : 'менеджером') . '.' : 'Макет согласован ' . ($user->isDesigner() ? 'дизайнером' : 'менеджером') . '.';
        $discussionLabel = $model->isConcept() ? 'Концепция не согласована. Внесите комментарии.' : 'Макет не согласован. Внесите комментарии.';
        if ($model->isModelScenario()) {
            if ($model->getStep1() != "accepted" || ($model->getStep1() == 'accepted' && $model->getStep2() == 'none')) {
                $discussionLabel = 'Сценарий не согласован. Внесите комментарии.';
            } else if ($model->getStep2() == "wait" || $model->getStep2() == 'accepted') {
                $discussionLabel = 'Запись не согласована. Внесите комментарии.';
            }
        }

        $this->addMessageToDiscussion(
            $model,
            $user,
            $discussionLabel,
            false
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
            $model->isConcept() ? 'agreement_concept' : 'agreement_model',
            'declined',
            $model->getActivity()->getName() . '/' . $model->getName(),
            //$reason ? $reason->getName() . '.' : ($model->isConcept() ? 'Концепция отклонена.' : 'Макет отклонён.'),
            $comments,
            !empty($msg_files) ? 'clip' : '',
            $model->getDealer(),
            $model->getId(),
            'agreement',
            true
        );

        //Если есть комментарий менеджера добавляем его
        if (!empty($comments)) {
            $message = $this->addMessageToDiscussion(
                $model,
                $user,
                'Комментарий менеджера. ' . (!empty($comments) ? $comments : ''),
                false
            );
        }

        //Если есть загруженный файл от менеджера и нет комментария, добавляем сообщение для файлов
        if (!$message && $commentFiles) {
            $message = $this->addMessageToDiscussion(
                $model,
                $user,
                'Комментарий менеджера.',
                false
            );
        }

        if ($message && !empty($commentFiles)) {
            $this->attachModelCommentsFileToMessage($model, $message, $msg_files, false);
        }

        AgreementDealerHistoryMailSender::send('AgreementModelDeclinedMail', $entry, $model->getDealer(), $message, $can_send_mail);
    }

    function acceptComment(AgreementModelComment $comment, User $user, $comments = '', $msg_files = array())
    {
        $model = AgreementModelTable::getInstance()->find($comment->getModelId());

        /*Agreement status*/
        //$statusLabel = $model->isConcept() ? 'Концепция согласована.' . ($user->isDesigner() ? 'дизайнером' : 'менеджером') . '.' : 'Макет согласован ' . ($user->isDesigner() ? 'дизайнером' : 'менеджером') . '.';
        $discussionLabel = $model->isConcept()
            ? ($model->getManagerStatus() == 'accepted' ? 'Концепция согласована.' : '')
            : ($model->getManagerStatus() == 'accepted' ? 'Макет согласован.' : '');

        if ($model->isModelScenario()) {
            if ($model->getStep1() != "accepted" || ($model->getStep1() == 'accepted' && $model->getStep2() == 'none')) {
                $discussionLabel = $model->getManagerStatus() == 'accepted' ? "Сценарий согласован." : '';
            } else if ($model->getStep2() == "wait" || $model->getStep2() == 'accepted') {
                $discussionLabel = $model->getManagerStatus() == 'accepted' ? "Запись согласована." : '';
            }
        }

        if ($model->getManagerStatus() != 'wait') {
            if ($model->getManagerStatus() == 'accepted') {
                $comment->setStatus('accepted');
            } else if ($model->getManagerStatus() == 'declined' || $model->getDesignerStatus() == 'declined') {
                if ($model->getStep1() == 'accepted' && $model->getStep2() == 'accepted') {
                    $model->getStep2('none');
                } else  {
                    $model->getStep1('none');
                    $model->getStep2('none');
                }
                $comment->setStatus('declined');
            }
        } else {
            $comment->setStatus('accepted');
        }
        $comment->save();

        $entry = LogEntryTable::getInstance()->addEntry(
            $user,
            $model->isConcept() ? 'agreement_concept' : 'agreement_model',
            'accepted_by_specialist',
            $model->getActivity()->getName() . '/' . $model->getName(),
            //$model->isConcept() ? 'Концепция утверждена специалистом' : 'Макет утверждён специалистом',
            $discussionLabel,
            'ok',
            $model->getDealer(),
            $model->getId(),
            'agreement'
        );

        $model->createPrivateLogEntryForSpecialists($entry);

        $commentFiles = array();
        if (!empty($msg_files)) {
            foreach ($msg_files as $file) {
                $commentFiles[] = $file;

                $model->addAcceptFile($file);
            }
        }

        if (!empty($discussionLabel)) {
            $this->addMessageToDiscussion($model, $user, $discussionLabel);
        }

        $message = null;
        $status_label = 'Комментарий дизайнера. ';
        if (!empty($comments)) {
            $message = $this->addMessageToDiscussion($model, $user, $status_label . ($comments ? $comments : ''));
        }

        if (!$message && count($commentFiles) > 0) {
            $message = $this->addMessageToDiscussion($model, $user, $status_label);
        }

        if ($message && count($commentFiles)) {
            $this->attachModelCommentsFileToMessage($model, $message, $commentFiles, true);
        }

        !$this->_accept_decline_message = true;
        $this->syncModelAndCommentsStatus($model, $user, true);

        /*if ($model->getManagerStatus() == 'declined') {
            MessageTable::cloneMessage($model, $user);
        }*/

        if ($entry && $model->getManagerStatus() == "accepted") {
            AgreementManagementHistoryMailSender::send(
                'AgreementModelCommentAcceptedMail',
                $entry,
                array(
                    'specialist' => $user,
                    'comment' => $comments
                ),
                'manager',
                $model->isConcept() ? AgreementManagementHistoryMailSender::AGREEMENT_CONCEPT_NOTIFICATION : AgreementManagementHistoryMailSender::AGREEMENT_NOTIFICATION
            );

            AgreementCompleteModelMailSender::send($model);
        }
    }

    function declineComment(AgreementModelComment $comment, User $user, $comments = '', $msg_files = array())
    {
        $comment->setStatus('declined');
        $comment->save();

        $model = AgreementModelTable::getInstance()->find($comment->getModelId());
        if ($model->getManagerStatus() == "accepted") {
            MailMessageTable::getInstance()->createQuery()->delete()->where('model_id = ?', $model->getId())->execute();
        }

        /*Agreement status*/
        $statusLabel = $statusLabelDiscussion = $model->isConcept() ? 'Концепция не согласована. Внесите комментарии.': 'Макет не согласован. Внесите комментарии.';
        if ($model->isModelScenario()) {
            $model->setStatus('declined');
            if ($model->getStep1() != "accepted") {
                $model->setStep1('none');
                $model->setStep2('none');
            } else if ($model->getStep1() == "accepted") {
                $model->setStep2('none');
            }
            $model->save();

            if ($model->getStatus() == "declined" && $model->getStep1() == "none" && $model->getStep2() == "none") {
                $statusLabel = $statusLabelDiscussion = "Сценарий не согласован. Внесите комментарии.";
            } else if ($model->getStatus() == "declined" && $model->getStep2() == "none") {
                $statusLabel = $statusLabelDiscussion = "Запись не согласована. Внесите комментарии.";
            }
        }

        $entry = LogEntryTable::getInstance()->addEntry(
            $user,
            $model->isConcept() ? 'agreement_concept' : 'agreement_model',
            'declined_by_specialist',
            $model->getActivity()->getName() . '/' . $model->getName(),
            //$model->isConcept() ? 'Концепция отклонена специалистом.' : 'Макет отклонён специалистом.',
            $statusLabel,
            !empty($msg_files) ? 'clip' : '',
            $model->getDealer(),
            $model->getId(),
            'agreement'
        );

        $model->createPrivateLogEntryForSpecialists($entry);

        $commentFiles = array();
        if (!empty($msg_files)) {
            $commentFile = array_shift($msg_files);
            $commentFiles[] = $commentFile;

            $model->setAgreementCommentsFile($commentFile);
            $model->addDeclineFile($commentFile);

            foreach ($msg_files as $file) {
                $model->addDeclineFile($file);
                $commentFiles[] = $file;
            }
        }

        $this->_accept_decline_message = true;

        $this->syncModelAndCommentsStatus($model, $user, false, $statusLabel);

        if ($model->getManagerStatus() != 'declined') {
            $message = $this->addMessageToDiscussion($model, $user, $statusLabelDiscussion);
        }

        $model->setAgreementComments($comments);
        $model->setAgreementCommentManager('');
        $model->save();

        //MessageTable::cloneMessage($model, $user);

        if (!empty($comments)) {
            $message = $this->addMessageToDiscussion($model, $user, 'Комментарий дизайнера. '.$comments);
        }

        if (empty($comments) && $commentFiles) {
            $message = $this->addMessageToDiscussion($model, $user, 'Комментарий дизайнера. ');
        }

        if ($message && $commentFiles) {
            $attached_file = $this->attachCommentsFileToMessage($message, $commentFiles)->getFile();
        }

        AgreementDealerHistoryMailSender::send('AgreementModelDeclinedMail', $entry, $model->getDealer(), $message);
        AgreementManagementHistoryMailSender::send(
            'AgreementModelCommentDeclinedMail',
            $entry,
            array(
                'specialist' => $user,
                'comment' => $comments,
                'comment_file' => $attached_file
            ),
            'manager',
            $model->isConcept() ? AgreementManagementHistoryMailSender::AGREEMENT_CONCEPT_NOTIFICATION : AgreementManagementHistoryMailSender::AGREEMENT_NOTIFICATION
        );
    }

    function syncModelAndCommentsStatus(AgreementModel $model, User $user, $isSpecialist = false, $comments = '', sfValidatedFile $comments_file = null, AgreementDeclineForm $declineForm = null)
    {
        $waits = AgreementModelCommentTable::getInstance()
            ->createQuery()
            ->where('model_id=? and status=?', array($model->getId(), 'wait'))
            ->count();

        if ($waits == 0) {
            $declined = AgreementModelCommentTable::getInstance()
                    ->createQuery()
                    ->where('model_id=? and status=?', array($model->getId(), 'declined'))
                    ->count() > 0;

            if ($declined) {
                /*Agreement status*/
                $this->declineModel($model, $user, null);
            } else
                $this->acceptModel($model, $user, '', null, $isSpecialist);
        }
    }

    /**
     * Add message to discussion
     *
     * @param AgreementModel $model
     * @param string $text
     * @return Message|false
     */
    function addMessageToDiscussion(AgreementModel $model, User $user, $text, $show_msg = true)
    {
        $discussion = $model->getDiscussion();

        if (!$discussion)
            return;

        $message = new Message();
        $message->setDiscussionId($discussion->getId());
        $message->setUser($user);
        $message->setUserName($user->selectName());
        $message->setText($text);
        $message->setSystem(true);
        $message->setMsgShow($show_msg);
        $message->save();

        // mark as unread
        $discussion->getUnreadMessages($user);

        return $message;
    }

    function attachModelCommentsFileToMessage(AgreementModel $model, Message $message, array $commentFiles, $accept = false, $message_with_comments = null)
    {
        if (!is_null($message_with_comments)) {
            $message = $message_with_comments;
        }

        if (!$accept) {
            $file = new MessageFile();
            $file->setMessageId($message->getId());
            $file->setFile($message->getId() . '-' . $model->getAgreementCommentsFile());

            copy(
                sfConfig::get('sf_upload_dir') . '/' . AgreementModel::AGREEMENT_COMMENTS_FILE_PATH . '/' . $model->getAgreementCommentsFile(),
                sfConfig::get('sf_upload_dir') . '/' . MessageFile::FILE_PATH . '/' . $file->getFile()
            );

            $file->save();
        }

        foreach ($commentFiles as $commentFile) {
            $file = new MessageFile();
            $file->setMessageId($message->getId());
            $file->setFile($message->getId() . '-' . $commentFile);

            copy(
                sfConfig::get('sf_upload_dir') . '/' . AgreementModel::AGREEMENT_COMMENTS_FILE_PATH . '/' . $commentFile,
                sfConfig::get('sf_upload_dir') . '/' . MessageFile::FILE_PATH . '/' . $file->getFile()
            );

            $file->save();
        }
    }

    /**
     * Attaches a file to message
     *
     * @param Message $message
     * @param null $commentFiles
     * @return MessageFile attached file
     */
    function attachCommentsFileToMessage(Message $message, $commentFiles = null)
    {
        foreach ($commentFiles as $commentFile) {
            $fileC = new MessageFile();
            $fileC->setMessageId($message->getId());
            $fileC->setFile($message->getId() . '-' . $commentFile);

            copy(
                sfConfig::get('sf_upload_dir') . '/' . AgreementModel::AGREEMENT_COMMENTS_FILE_PATH . '/' . $commentFile,
                sfConfig::get('sf_upload_dir') . '/' . MessageFile::FILE_PATH . '/' . $fileC->getFile()
            );

            $fileC->save();
        }

        return $fileC;
    }

}
