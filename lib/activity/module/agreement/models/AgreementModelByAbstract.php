<?php

/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 16.03.2017
 * Time: 10:06
 */
class AgreementModelByAbstract implements AgreementModelByInterface
{
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
    public function agreement()
    {

        $utils = new AgreementActivityStatusUtils($this->model->getActivity(), $this->model->getDealer());
        $utils->updateActivityAcceptance();

        if ($this->model->isModelScenario()) {
            $text = 'Сценарий отправлен на согласование менеджеру';
        } else {
            if ($this->model->isConcept() && $this->model->getActivity()->getAllowSpecialAgreement()) {
                $text = 'Концепция отправлена на согласование рег. менеджеру';
            } else {
                $text = $this->model->isConcept() ? 'Концепция отправлена на согласование менеджеру' : 'Макет отправлен на согласование менеджеру';
            }
        }

        //If checked No model changes set model status to accepted
        if ($this->model->getNoModelChanges()) {
            $this->model->setStatus('accepted');
            if ($this->model->isModelScenario()) {
                $text = "Ваши сценарий / запись согласованы.";
                $this->model->setStep1('accepted');
                $this->model->setStep2('accepted');
            } else {
                $text = "Ваши макет согласован.";
            }
            $this->model->save();
        }

        $object_type = 'agreement_model';
        if ($this->model->isConcept()) {
            $object_type = 'agreement_concept';
            if ($this->model->getActivity()->getAllowSpecialAgreement()) {
                $object_type = 'agreement_special_concept_by_reg_manager';
            }
        }

        $entry = LogEntryTable::getInstance()->addEntry(
            $this->user,
            $object_type,
            'add',
            $this->model->getActivity()->getName() . '/' . $this->model->getName(),
            $text,
            'clip',
            $this->model->getDealer(),
            $this->model->getId(),
            'agreement'
        );

        $message = $this->addMessageToDiscussion($this->model, $text, true, Message::MSG_STATUS_SENDED);

        //$this->attachFileToMessage($model, $message, $hasEditorLink);
        $this->attachFilesToMessage($this->model, $message);

        //Спец. согласование концепции
        if ($this->model->isConcept() && $this->model->getActivity()->getAllowSpecialAgreement()) {
            $this->sendToManagerImporter($this->model, $entry, array(SpecialAgreementConceptStatuses::AGREEMENT_STATUS_REG_MANAGER_WAIT));
        } else {
            AgreementManagementHistoryMailSender::send(
                'AgreementSendModelMail',
                $entry,
                false,
                false,
                $this->model->isConcept() ? AgreementManagementHistoryMailSender::NEW_AGREEMENT_CONCEPT_NOTIFICATION : AgreementManagementHistoryMailSender::NEW_AGREEMENT_NOTIFICATION
            );


        }

        //$this->setModelChanges($this->model, $this->no_model_changes);
        return $message;
    }

    public function agreementUpdate()
    {
        $utils = new AgreementActivityStatusUtils($this->model->getActivity(), $this->model->getDealer());
        $utils->updateActivityAcceptance();

        if ($this->model->isModelScenario()) {
            if ($this->model->getStep1() == "wait") {
                $text = 'Сценарий отправлен на согласование';
            }
            if ($this->model->getStep1() == "accepted" && $this->model->getStep2() == "wait") {
                $text = 'Запись отправлена на согласование';
            }
        } else {
            if ($this->model->isConcept() && $this->model->getActivity()->getAllowSpecialAgreement()) {
                $text = 'Концепция отправлена на согласование рег. менеджеру';
            } else {
                $text = $this->model->isConcept() ? 'Концепция отправлена на согласование' : 'Макет отправлен на согласование';
            }
        }

        //If checked No model changes set model status to accepted
        if ($this->model->getNoModelChanges()) {
            $this->model->setStatus('accepted');
            if ($this->model->isModelScenario()) {
                $text = "Ваши сценарий / запись согласованы.";
                $this->model->setStep1('accepted');
                $this->model->setStep2('accepted');
            } else {
                $text = "Ваш макет согласован.";
            }
            $this->model->save();
        }

        $object_type = 'agreement_model';
        if ($this->model->isConcept()) {
            $object_type = 'agreement_concept';
            if ($this->model->getActivity()->getAllowSpecialAgreement()) {
                $object_type = 'agreement_special_concept_by_reg_manager';
            }
        }

        $entry = LogEntryTable::getInstance()->addEntry(
            $this->user,
            $object_type,
            'edit',
            $this->model->getActivity()->getName() . '/' . $this->model->getName(),
            $text,
            'clip',
            $this->model->getDealer(),
            $this->model->getId(),
            'agreement'
        );

        //Делаем доп. рассылку для заявки если отмечена галочка В макет не вносились изменения
        if ($this->model->getNoModelChanges()) {
            AgreementManagementHistoryMailSender::send(
                'AgreementModelNoModelChanges',
                $entry,
                false,
                false,
                AgreementManagementHistoryMailSender::NEW_AGREEMENT_NOTIFICATION
            );
        }

        $this->model->createPrivateLogEntryForSpecialists($entry);

        $message = $this->addMessageToDiscussion($this->model, $text, true, Message::MSG_STATUS_SENDED);
        //$this->attachFileToMessage($model, $message);
        $this->attachFilesToMessage($this->model, $message, $this->saved_files);

        //Спец. согласование концепции
        if ($this->model->isConcept() && $this->model->getActivity()->getAllowSpecialAgreement()) {
            $this->sendToManagerImporter($this->model, $entry, array(SpecialAgreementConceptStatuses::AGREEMENT_STATUS_REG_MANAGER_WAIT));
        } else {
            AgreementManagementHistoryMailSender::send(
                'AgreementSendModelMail',
                $entry,
                false,
                false,
                $this->model->isConcept() ? AgreementManagementHistoryMailSender::NEW_AGREEMENT_CONCEPT_NOTIFICATION : AgreementManagementHistoryMailSender::NEW_AGREEMENT_NOTIFICATION
            );
        }

        return $message;
    }

    /**
     * @param $model
     * @param $entry
     * @param int $mode
     */
    private function sendToManagerImporter($model, $entry, $mode = null)
    {
        //Работа со статусами при работе с концепцией
        //Если рег. менеджер согласовал концепцию, в согласовании он не участвует
        //Если импортер согласовал, а рег. менеджер отклонил, письма ипортеру приодят также

        //Удаляем все статусы которые были
        SpecialAgreementConceptStatuses::deleteStatuses($model->getId());
        if (!empty($mode)) {
            if (is_array($mode)) {
                foreach ($mode as $mode_item) {
                    SpecialAgreementConceptStatuses::setStatus($model, $mode_item);
                }
            } else {
                SpecialAgreementConceptStatuses::setStatus($model, $mode);
            }
        }

        $dealer_manager = UserTable::getInstance()->find($model->getDealer()->getRegionalManager()->getRegionalManagerId());
        if ($dealer_manager) {
            //Отправка письма Региональному менеджеру
            AgreementManagementHistoryMailSender::sendSpecial(
                'AgreementSendModelMail',
                $entry,
                array($dealer_manager->getId())
            );
        }

//Отправка письма импортеру привязанного к активности со спец. согласованием
        /*$users_ids = array_map(function($item) {
            return $item['user_id'];
        },
            ActivitySpecialAgreementUsersListTable::getInstance()
                ->createQuery()
                ->where('activity_id = ?', $model->getActivityId())
                ->execute(array(), Doctrine_Core::HYDRATE_ARRAY)
        );

        if (!empty($users_ids)) {
            AgreementManagementHistoryMailSender::sendSpecial(
                'AgreementSendModelMail',
                $entry,
                //array($model->getDealer()->getRegionalManagerId())
                $users_ids
            );
        }*/
    }

    /**
     * Make draft agreement model
     * @return mixed
     */
    public
    function agreementDraft()
    {
        $statusLabel = $this->model->isConcept() ? 'Концепция отправлена как черновик' : 'Макет отправлен как черновик';
        if ($this->model->isModelScenario()) {
            $statusLabel = 'Сценарий отправлен как черновик';
        }
        $message = $this->addMessageToDiscussion($this->model, $statusLabel);

        //$this->attachFileToMessage($model, $message, $hasEditorLink);
        $this->attachFilesToMessage($this->model, $message);
    }

    /**
     * Decline agrement model
     * @return mixed
     */
    public
    function decline()
    {
        $this->model->setStatus('not_sent');

        $this->model->setStep1('none');
        $this->model->setStep2('none');

        $this->model->setManagerStatus('wait');
        $this->model->setDesignerStatus('wait');

        $this->model->save();

        //Удаляем данные по фиксации статуса согласования рег. менеджером / специалистом
        if ($this->model->isConcept() && $this->model->getActivity()->getAllowSpecialAgreement()) {
            SpecialAgreementConceptStatuses::deleteStatuses($this->model->getId());
        }

        RealBudgetTable::getInstance()->removeByObjectOnly(ActivityModule::byIdentifier('agreement'), $this->model->getId());

        $text = $this->model->isConcept() ? 'Отменена отправка концепции на согласование' : 'Отменена отправка макета на согласование';
        if ($this->model->isModelScenario()) {
            if ($this->model->getStep1() != "accepted") {
                $text = 'Отменена отправка сценария на согласование';
            } else if ($this->model->getStep1() == "accepted" && $this->model->getStep2() != "accepted") {
                $text = 'Отменена отправка записи на согласование';
            }
        }

        $entry = LogEntryTable::getInstance()->addEntry(
            $this->user,
            $this->model->isConcept() ? 'agreement_concept' : 'agreement_model',
            'cancel',
            $this->model->getActivity()->getName() . '/' . $this->model->getName(),
            $text,
            '',
            $this->model->getDealer(),
            $this->model->getId(),
            'agreement'
        );

        $this->model->createPrivateLogEntryForSpecialists($entry);
        $this->model->cancelSpecialistSending();

        $message = $this->addMessageToDiscussion($this->model, $text);

        //Спец. согласование концепции
        if ($this->model->isConcept() && $this->model->getActivity()->getAllowSpecialAgreement()) {
            $this->sendToManagerImporter($this->model, $entry, array());
        } else {
            AgreementManagementHistoryMailSender::send(
                'AgreementCancelModelMail',
                $entry,
                false,
                false,
                $this->model->isConcept() ? AgreementManagementHistoryMailSender::NEW_AGREEMENT_CONCEPT_NOTIFICATION : AgreementManagementHistoryMailSender::NEW_AGREEMENT_NOTIFICATION
            );
        }

        return $message;
    }

    /**
     * Cancel scenario model agreement
     */
    public
    function declineScenario()
    {
        $this->model->setStatus('not_sent');

        $this->model->setStep1('none');
        $this->model->setStep2('none');

        $this->model->save();

        $text = 'Отменена отправка сценария на согласование.';
        $entry = LogEntryTable::getInstance()->addEntry(
            $this->user,
            'agreement_model_scenario',
            'cancel',
            $this->model->getActivity()->getName() . '/' . $this->model->getName(),
            $text,
            '',
            $this->model->getDealer(),
            $this->model->getId(),
            'agreement'
        );

        $this->model->createPrivateLogEntryForSpecialists($entry);
        $this->model->cancelSpecialistSending();

        $message = $this->addMessageToDiscussion($this->model, $text);

        //Спец. согласование концепции
        if ($this->model->isConcept() && $this->model->getActivity()->getAllowSpecialAgreement()) {
            $this->sendToManagerImporter($this->model, $entry);
        } else {
            AgreementManagementHistoryMailSender::send(
                'AgreementCancelModelScenarioMail',
                $entry,
                false,
                false,
                $this->model->isConcept() ? AgreementManagementHistoryMailSender::NEW_AGREEMENT_CONCEPT_NOTIFICATION : AgreementManagementHistoryMailSender::NEW_AGREEMENT_NOTIFICATION
            );
        }

        return $message;
    }

    /**
     * Cancel record model agreement
     */
    public
    function declineRecord()
    {
        $this->model->setStatus('not_sent');
        $this->model->setStep2('none');

        $this->model->save();

        $text = 'Отменена отправка записи на согласование.';
        $entry = LogEntryTable::getInstance()->addEntry(
            $this->user,
            'agreement_model_record',
            'cancel',
            $this->model->getActivity()->getName() . '/' . $this->model->getName(),
            $text,
            '',
            $this->model->getDealer(),
            $this->model->getId(),
            'agreement'
        );

        $this->model->createPrivateLogEntryForSpecialists($entry);
        $this->model->cancelSpecialistSending();

        $message = $this->addMessageToDiscussion($this->model, $text);

        //Спец. согласование концепции
        if ($this->model->isConcept() && $this->model->getActivity()->getAllowSpecialAgreement()) {
            $this->sendToManagerImporter($this->model, $entry);
        } else {
            AgreementManagementHistoryMailSender::send(
                'AgreementCancelModelRecordMail',
                $entry,
                false,
                false,
                $this->model->isConcept() ? AgreementManagementHistoryMailSender::NEW_AGREEMENT_CONCEPT_NOTIFICATION : AgreementManagementHistoryMailSender::NEW_AGREEMENT_NOTIFICATION
            );
        }

        return $message;
    }

    public
    function agreementManagerAccept()
    {
        /**
         * Make copy of uploaded temp files and remove
         */
        $msg_files = TempFileTable::copyFilesByRequest($this->request, AgreementModel::AGREEMENT_COMMENTS_FILE_PATH);

        if ($this->model->isModelScenario()) {
            if ($this->model->getStep1() != "accepted") {

                $this->model->changeStep1Statuses();
                $this->model->save();

                $message = $this->acceptManagerModel(array());

                return array('form' => $this->form, 'response' => 'window.accept_decline_form.onResponse', 'message' => $message);
            } else if ($this->model->getStep2() != "accepted") {
                $this->model->setStep2("accepted");

                $this->model->acceptModelWithMD();
            }
        } else {
            $this->model->setStatus('accepted');
        }
        $this->model->save();

        return $this->acceptManagerModel($msg_files);
    }

    protected
    function acceptManagerModel($msg_files = array())
    {
        $utils = new AgreementModelStatusUtils();

        return $utils->acceptModel(
            $this->model,
            $this->user,
            $this->form->getValue('agreement_comments'),
            $msg_files,
            false,
            $this->getManagerAcceptStatus()
        );
    }

    protected
    function getManagerAcceptStatus()
    {
        return '';
    }

    public
    function agreementManagerDecline()
    {
        /**
         * Make copy of uploaded temp files and remove
         */
        $msg_files = TempFileTable::copyFilesByRequest($this->request, AgreementModel::AGREEMENT_COMMENTS_FILE_PATH);

        $this->model->workWithScenatioAndRecordsData($this->request);

        $this->model->setManagerStatus('wait');
        $this->model->setDesignerStatus('wait');

        $this->model->setStatus('declined');
        $this->model->setDeclineReasonId($this->form->getValue('decline_reason_id'));

        //При отклонении менеджером, если стоит галочка В макет не вносились изменения, убираем ее
        if ($this->model->getNoModelChanges()) {
            $this->model->setNoModelChanges(false);
            $this->model->setNoModelChangesView(false);
        }

        $this->model->save();

        $utils = new AgreementModelStatusUtils();

        return $utils->declineModel(
            $this->model,
            $this->user,
            null, //AgreementDeclineReasonTable::getInstance()->find($model->getDeclineReasonId()),
            $this->form->getValue('agreement_comments'),
            $msg_files,
            $this->getManagerDeclineStatus()
        );
    }

    protected
    function getManagerDeclineStatus()
    {
        return '';
    }

    /**
     * Accept model by specialist
     */
    public
    function agreementSpecialistAccept()
    {
        $this->model->setDesignerStatus('accepted');

        /*If model is scenario / record and manager has accepted model make step work */
        if ($this->model->isModelScenario() && $this->model->getManagerStatus() == 'accepted') {
            if ($this->model->getStep1() == "wait" && $this->model->getStep2() == "none") {
                $this->model->setStep1("accepted");
                $this->model->setStatus('declined');
            } else if ($this->model->getStep1() == "accepted" && $this->model->getStep2() == "wait") {
                $this->model->setStep2("accepted");
                $this->model->setStatus('accepted');
            }
        } /*If model is simple only and manager has accepted model make model status accept*/
        else {
            if ($this->model->getManagerStatus() == 'accepted' && !$this->model->isModelScenario()) {
                $this->model->setStatus('accepted');
            } else {
                if (!$this->from_report) {
                    $this->model->setStatus('declined');
                }
            }
        }
        $this->model->save();

        $this->acceptCopySpecialistFilesAndMakeDiscussion();
    }

    protected
    function acceptCopySpecialistFilesAndMakeDiscussion()
    {
        /**
         * Make copy of uploaded temp files and remove
         */
        $msg_files = TempFileTable::copyFilesByRequest($this->request, AgreementModel::AGREEMENT_COMMENTS_FILE_PATH);

        $comment = $this->model->getSpecialistComment($this->user);
        if (!$comment) {
            throw new Exception('No comments found');
        }

        $discussion = $this->model->getDiscussion();

        /*If have discussion check model status if accepted or declined show messages*/
        if ($discussion && ($this->model->getStatus() == 'accepted' || $this->model->getStatus() == 'declined')) {
            $discussion->activeDisabledMessages();
        }

        /*If model status accepted show designer message*/
        $this->acceptSpecialistComment($comment, $msg_files);
    }

    protected
    function acceptSpecialistComment($comment, $msg_files)
    {
        $utils = new AgreementModelStatusUtils();
        $utils->setMsgType(Message::MSG_TYPE_SPECIALIST);
        $utils->setMsgStatus(Message::MSG_STATUS_ACCEPTED);
        $utils->setCanSendMail(false);
        $utils->acceptComment(
            $comment,
            $this->user,
            $this->form->getValue('agreement_comments'),
            $msg_files,
            $this->getSpecialistAcceptStatus($comment)
        );

        $this->sendMails($this->model);
    }

    protected
    function getSpecialistAcceptStatus($comment)
    {
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

    /**
     * Decline model by specialist
     */
    public
    function agreementSpecialistDecline()
    {
        $this->model->setDesignerStatus('declined');
        if ($this->model->getManagerStatus() == 'declined') {
            $this->model->setStatus('declined');
        }

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
    }

    /**
     * Delete model discussion messages
     */
    protected
    function deleteMessages()
    {
        $discussion = $this->model->getDiscussion();
        if ($discussion) {
            $discussion->deleteInactiveMessages();
        }
    }

    protected
    function declineCopySpecialistFilesAndMakeDiscussion()
    {
        /**
         * Make copy of uploaded temp files and remove
         */
        $msg_files = TempFileTable::copyFilesByRequest($this->request, AgreementModel::AGREEMENT_COMMENTS_FILE_PATH);

        $comment = $this->model->getSpecialistComment($this->user);
        if (!$comment) {
            throw new Exception('No comments found');
        }

        $discussion = $this->model->getDiscussion();
        if ($discussion) {
            $discussion->activeDisabledMessages();
        }

        $this->declineSpecialistComment($comment, $msg_files);
    }

    protected
    function declineSpecialistComment($comment, $msg_files)
    {
        $utils = new AgreementModelStatusUtils();
        $utils->setMsgType(Message::MSG_TYPE_SPECIALIST);
        $utils->setMsgStatus(Message::MSG_STATUS_DECLINED_BY_SPECIALIST);
        $utils->setCanSendMail(false);
        $utils->declineComment(
            $comment,
            $this->user,
            $this->form->getValue('agreement_comments'),
            $msg_files,
            $this->getSpecialistDeclineStatus($comment)
        );

        $this->sendMails($this->model);
    }

    protected
    function getSpecialistDeclineStatus($comment)
    {
        $model = $this->model;
        if (!is_null($comment)) {
            $model = $comment->getModel();

            return $model->getManagerStatus() != 'accepted' && $comment->getModel()->getManagerStatus() != 'declined' ? 'wait' : 'declined';
        }

        return $model->getManagerStatus() == 'accepted' ? 'declined' : $model->getManagerStatus();
    }

    /**
     * Add message to discussion
     *
     * @param AgreementModel $model
     * @param string $text
     * @return Message|false
     */
    public
    function addMessageToDiscussion(AgreementModel $model, $text, $msg_show = true, $msg_status = 'none')
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
        $message->setMsgShow($msg_show);
        $message->setMsgStatus($msg_status);
        $message->save();

        // mark as unread
        $discussion->getUnreadMessages($user);

        return $message;
    }

    /**
     * Save model uploaded files to discussion by model type (simple model or scenario / record)
     * @param AgreementModel $model
     * @param Message $message
     * @param array $saved_files
     */
    public
    function attachFilesToMessage(AgreementModel $model, Message $message, $saved_files = array())
    {
        if (!empty($saved_files)) {
            foreach ($saved_files as $file_item) {
                $path = $file_item['gen_file_name'];

                if (isset($file_item['upload_path']) && !empty($file_item['upload_path'])) {
                    $path = sprintf('%s/%s', $file_item['upload_path'], $file_item['gen_file_name']);
                }

                $this->saveMessageFile($message, $path, $file_item['gen_file_name'], $file_item['upload_path']);
            }
        } else {
            $query = AgreementModelReportFilesTable::getInstance()->createQuery()->select('file')
                ->where('object_id = ?', $model->getId())
                ->orderBy('id ASC');

            if ($model->isModelScenario() && $model->getNoModelChanges()) {
                $query->andWhere('object_type = ? and (file_type = ? or file_type = ? or file_type = ?)',
                    array
                    (
                        AgreementModel::UPLOADED_FILE_MODEL,
                        AgreementModel::UPLOADED_FILE_MODEL_TYPE,
                        AgreementModel::UPLOADED_FILE_SCENARIO_TYPE,
                        AgreementModel::UPLOADED_FILE_RECORD_TYPE,
                    )
                );
            } else if ($model->isModelScenario() && $model->getStep1() != 'accepted') {
                $query->andWhere('object_type = ? and (file_type = ? or file_type = ?)', array(AgreementModel::UPLOADED_FILE_MODEL, AgreementModel::UPLOADED_FILE_MODEL_TYPE, AgreementModel::UPLOADED_FILE_SCENARIO_TYPE));
            }

            $files_list = $query->execute();
            foreach ($files_list as $file_item) {
                $this->saveMessageFile($message, $file_item->getFileName(), $file_item->getFile(), $file_item->getPath());
            }
        }
    }

    protected
    function saveMessageFile($message, $file_to_save, $file_name, $file_path = '')
    {
        if ($file_to_save && file_exists(sfConfig::get('sf_upload_dir') . '/' . AgreementModel::MODEL_FILE_PATH . '/' . $file_to_save)) {
            $file = new MessageFile();

            $file->setMessageId($message->getId());
            $file->setFile($message->getId() . '-' . $file_name);
            $file->setPath($file_path);

            $msg_path = sfConfig::get('sf_upload_dir') . '/' . MessageFile::FILE_PATH;
            if (!empty($file_path)) {
                $msg_path = sfConfig::get('sf_upload_dir') . '/' . MessageFile::FILE_PATH . $file_path;
                if (!file_exists($msg_path)) {
                    mkdir($msg_path, 0777, true);
                }
            }

            copy(
                sfConfig::get('sf_upload_dir') . '/' . AgreementModel::MODEL_FILE_PATH . '/' . $file_to_save,
                $msg_path . '/' . $file->getFile()
            );

            $file->save();
        }
    }

    protected
    function setModelChanges($model, $noModelChanges)
    {
        if ($noModelChanges && $model->getStep1() != "accepted" && $model->isModelScenario()) {
            $model->setStep1('accepted');
            $model->setStep2('accepted');

            $model->setStatus('accepted');

            $model->save();
        } else if ($noModelChanges && $model->getStatus() != "accepted") {
            $model->setStatus('accepted');
            $model->save();
        }
    }

    /**
     * Отправка писем по статусу согласования менеджера / специалиста
     * @param AgreementModel $model
     */
    protected
    function sendMails(AgreementModel $model)
    {
        MailMessageTable::sendMails($model);
    }
}
