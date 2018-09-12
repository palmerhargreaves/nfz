<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 02.04.2017
 * Time: 11:52
 */

class AgreementModelDealerStatus extends ModelReportStatus implements AgreementModelStatusInterface
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
        $utils = new AgreementActivityStatusUtils($this->model->getActivity(), $this->model->getDealer());
        $utils->updateActivityAcceptance();

        $status_and_text = $this->statusAndText();

        //If checked No model changes set model status to accepted
        if ($this->model->getNoModelChanges()) {
            $this->model->setStatus('accepted');
            if ($this->model->isModelScenario()) {
                $status_and_text['text'] = "Ваши сценарий / запись согласованы.";
                $this->model->setStep1('accepted');
                $this->model->setStep2('accepted');
            } else {
                $status_and_text['text'] =  "Ваш макет согласован.";
            }
            $this->model->save();
        }

        $entry = LogEntryTable::getInstance()->addEntry(
            $this->model_by->user,
            $this->model->isConcept() ? 'agreement_concept' : 'agreement_model',
            'add',
            $this->model->getActivity()->getName() . '/' . $this->model->getName(),
            $status_and_text['text'],
            'clip',
            $this->model->getDealer(),
            $this->model->getId(),
            'agreement'
        );

        $message = $this->addMessageToDiscussion($this->model, $status_and_text['text'], true, Message::MSG_STATUS_SENDED);
        $this->model_by->attachFilesToMessage($this->model, $message);

        $this->changeStatusAndSendMessage($entry, $status_and_text);

        return $message;
    }

    public function updateStatus() {
        $utils = new AgreementActivityStatusUtils($this->model->getActivity(), $this->model->getDealer());
        $utils->updateActivityAcceptance();

        /**
         * If manager make accept model and when designer decline model, then send again to both manager / designer to agreement
         */
        if ($this->model->getManagerStatus() == 'accepted' && $this->model->getDesignerStatus() == 'declined') {
            $this->model->setManagerStatus('wait');
            $this->model->setDesignerStatus('wait');
            $this->model->save();
        }

        /**
         * Set manager and designer statuses to wait if model is scenario / record and first step of agreement completed
         */
        if ($this->model->isModelScenario() && $this->model->getStep1() == 'accepted') {
            if ($this->model->getManagerStatus() == 'accepted' && $this->model->getDesignerStatus() == 'accepted') {
                $this->model->setManagerStatus('wait');
                $this->model->setDesignerStatus('wait');
            }

            if ($this->model->getManagerStatus() == 'declined') {
                $this->model->setManagerStatus('wait');
            }

            if ($this->model->getDesignerStatus() == 'declined') {
                $this->model->setDesignerStatus('wait');
            }

            $this->model->save();
        }

        $status_and_text = $this->statusAndText();

        //If checked No model changes set model status to accepted
        if ($this->model->getNoModelChanges()) {
            $this->model->setStatus('accepted');
            if ($this->model->isModelScenario()) {
                $status_and_text['text'] = "Ваши сценарий / запись согласованы.";
                $this->model->setStep1('accepted');
                $this->model->setStep2('accepted');
            } else {
                $status_and_text['text'] =  "Ваш макет согласован.";
            }
            $this->model->save();
        }

        $entry = LogEntryTable::getInstance()->addEntry(
            $this->model_by->user,
            $this->model->isConcept() ? 'agreement_concept' : 'agreement_model',
            'edit',
            $this->model->getActivity()->getName() . '/' . $this->model->getName(),
            $status_and_text['text'],
            'clip',
            $this->model->getDealer(),
            $this->model->getId(),
            'agreement'
        );

        //$this->model->createPrivateLogEntryForSpecialists($entry);

        $message = $this->addMessageToDiscussion($this->model, $status_and_text['text'], true, Message::MSG_STATUS_SENDED);
        $this->model_by->attachFilesToMessage($this->model, $message, $this->model_by->saved_files);

        $this->changeStatusAndSendMessage($entry, $status_and_text);

        return $message;
    }

    /**
     * Decline model status
     * @return mixed
     */
    public function declineStatus()
    {
        // TODO: Implement declineStatus() method.
    }

    private function changeStatusAndSendMessage($entry, $status_and_text) {
        /**
         * Change model manager / designer statuses
         */
        $model = AgreementModelTable::getInstance()->find($this->model->getId());

        //If in model set No model changes
        if ($model->getNoModelChanges()) {
            $model->setStatus('accepted');
            $model->save();

            //Делаем доп. рассылку для заявки если отмечена галочка В макет не вносились изменения
            AgreementManagementHistoryMailSender::send(
                'AgreementModelNoModelChanges',
                $entry,
                false,
                false,
                AgreementManagementHistoryMailSender::NEW_AGREEMENT_NOTIFICATION
            );
        } else {

            if ($status_and_text['status'] == self::SEND_BOTH) {
                $model->setManagerStatus('wait');
                $model->setDesignerStatus('wait');
                $model->setStatus('wait_manager_specialist');
                $model->setWaitSpecialist(true);
                $model->save();

                $this->sendToManager($entry);
                $this->sendToDesigner($status_and_text['text']);
            } else if ($status_and_text['status'] == self::SEND_MANAGER) {
                $model->setManagerStatus('wait');
                $model->setStatus('wait');
                $model->save();

                $this->sendToManager($entry);
            } else if ($status_and_text['status'] == self::SEND_DESIGNER) {

                $model->setDesignerStatus('wait');
                $model->setStatus('wait_specialist');
                $model->setWaitSpecialist(true);
                $model->save();

                $this->sendToDesigner($status_and_text['text']);
            }

        }
    }

    private function sendToManager($entry) {
        AgreementManagementHistoryMailSender::send(
            'AgreementSendModelMail',
            $entry,
            false,
            false,
            $this->model->isConcept() ? AgreementManagementHistoryMailSender::NEW_AGREEMENT_CONCEPT_NOTIFICATION : AgreementManagementHistoryMailSender::NEW_AGREEMENT_NOTIFICATION
        );
    }

    private function statusAndText() {
        $text = array();
        $status = '';

        if ($this->model->isModelScenario()) {
            if ($this->model->getStep1() == "wait") {
                $text[] = 'Сценарий отправлен на согласование';
            }
            if ($this->model->getStep1() == "accepted" && $this->model->getStep2() == "wait") {
                $text[] =  'Запись отправлена на согласование';
            }
        } else {
            $text [] = $this->model->isConcept() ? 'Концепция отправлена на согласование' : 'Макет отправлен на согласование';
        }

        if ($this->model->getManagerStatus() != 'accepted' && $this->model->getDesignerStatus() != 'accepted') {
            $text[] = 'менеджеру  / дизайнеру';
            $status = self::SEND_BOTH;
        } else if($this->model->getManagerStatus() == 'accepted' && $this->model->getDesignerStatus() != 'accepted') {
            $text[] = 'дизайнеру';
            $status = self::SEND_DESIGNER;
        } else if($this->model->getManagerStatus() != 'accepted' && $this->model->getDesignerStatus() == 'accepted') {
            $text[] = 'менеджеру';
            $status = self::SEND_MANAGER;
        }

        return array('status' => $status, 'text' => implode(' ', $text));
    }

    protected function sendToDesigner($text) {
        $specialist_groups = UserGroupTable::getInstance()
            ->createQuery('g')
            ->distinct()
            ->select('g.*')
            ->innerJoin('g.Roles r WITH r.role=?', 'specialist')
            ->innerJoin('g.Users u WITH u.active=?', true)
            ->execute();

        foreach ($specialist_groups as $group) {
            $groups[] = $group;
        }
        $specialist_groups = array_reverse($groups);

        foreach ($specialist_groups as $group) {
            $active_users = $group->getActiveUsers();

            if ($active_users->count() > 0) {

                //Проврека на ограниченный доступ дизайнеров
                $limited_access = $this->model->getActivity()->isLimitedDesignersAccess();
                foreach ($active_users as $user) {

                    //Оганиченная рассылка писем для активности
                    if ($limited_access) {
                        //При первой отправки на согласование дизайнеру, делаем проверку на доступ дизайнера к активности
                        //Если доступ есть, фиксируем в таблице, для дальшейшего доступа дизайнера к активности, в случаи привязки другого дизайнера к активности
                        if (!ActivitySpecialistsTable::checkAllowUserForActivity($user, $this->model->getActivity())) {

                            //Если дизайнер не привязан к активности и не получал данных о заявке, переходим к сл. пользователю
                            if (AgreementModelCheckByDesignerTable::getInstance()->createQuery()->where('activity_id = ? and user_id = ? and model_id = ?', array($this->model->getActivityId(), $user->getId(), $this->model->getId()))->count() == 0) {
                                continue;
                            }
                        }

                        //Если дизайнер получал данные по заявке, проверяем на корректного дизайнера, должен получить письмо тот кто уже получал письма, а не тот кто привязан в тек. момент
                        $check_by_designer = AgreementModelCheckByDesignerTable::getInstance()->createQuery()->where('activity_id = ? and model_id = ?', array($this->model->getActivityId(), $this->model->getId()))->fetchOne();
                        if ($check_by_designer && $check_by_designer->getUserId() != $user->getId()) {
                            continue;
                        }

                        //Фиксируем данные, при первой отправки заявки дизайнеру, что именно он привязан к этой заявке, для дальнейшего получения писем в случаем смены привязки активности к др. дизайнеру
                        if (!$check_by_designer) {
                            $designer_check_item = new AgreementModelCheckByDesigner();
                            $designer_check_item->setArray(array(
                                'activity_id' => $this->model->getActivityId(),
                                'user_id' => $user->getId(),
                                'model_id' => $this->model->getId(),
                                'check_count' => 1
                            ));
                            $designer_check_item->save();
                        } else {
                            $check_count = $check_by_designer->getCheckCount();
                            $check_by_designer->setCheckCount(++$check_count);
                            $check_by_designer->setCheckData($user->getEmail());
                            $check_by_designer->save();
                        }

                        $this->sendModelToSpecialist($this->model, $user, $text);
                    } else {
                        if ($user->getIsDefaultSpecialist()) {
                            $this->sendModelToSpecialist($this->model, $user, $text);
                        }
                    }
                }
            }
        }
    }

    protected function sendModelToSpecialist(AgreementModel $model, User $specialist, $msg)
    {
        $comment = new AgreementModelComment();
        $comment->setArray(array(
            'model_id' => $this->model->getId(),
            'user_id' => $specialist->getId()
        ));

        $comment->setStatus('wait');
        $comment->save();

        $log_entry = LogEntryTable::getInstance()->addEntry(
            $this->user,
            $model->isConcept() ? 'agreement_concept_model' : 'agreement_model',
            'sent_to_specialist',
            $model->getActivity()->getName() . '/' . $model->getName(),
            'Вам отправлен макет для согласования',
            '',
            $model->getDealer(),
            $model->getId(),
            'agreement'
        );
        $log_entry->setPrivateUser($specialist);
        $log_entry->save();

        AgreementSpecialistHistoryMailSender::send('AgreementModelSentToSpecialistMail', $log_entry, $specialist, $msg);
    }
}
