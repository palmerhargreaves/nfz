<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 02.04.2017
 * Time: 11:52
 */

class AgreementModelReportDealerStatus extends ModelReportStatus implements AgreementModelStatusInterface
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
     * Agreement model update
     * @return mixed
     */
    public function updateStatus()
    {
        // TODO: Implement updateStatus() method.
    }

    /**
     * Agreement model status
     * @return mixed
     */
    public function acceptStatus()
    {
        $status_text = $this->getReportAcceptText();
        $entry = LogEntryTable::getInstance()->addEntry(
            $this->user,
            $this->model->isConcept() ? 'agreement_concept_report' : 'agreement_report',
            'edit',
            $this->model->getActivity()->getName() . '/' . $this->model->getName(),
            $status_text['text'],
            'clip',
            $this->model->getDealer(),
            $this->model->getId(),
            'agreement'
        );

        $this->model->createPrivateLogEntryForSpecialists($entry);

        $message = $this->addMessageToDiscussion($this->model, $status_text['text'], true, Message::MSG_STATUS_SENDED);

        $this->attachFinancialFilesDocsToMessage($this->report, $message);
        $this->attachAdditionalFilesToMessage($this->report, $message);

        $this->changeStatusAndSendMessage($entry, $this->getReportAcceptText());
    }


    /**
     * Decline model status
     * @return mixed
     */
    public function declineStatus()
    {
        $this->model->setWaitSpecialist(false);
        $this->model->save();

        $this->report->setManagerStatus('wait');
        $this->report->setDesignerStatus('wait');
        $this->report->save();

        $entry = LogEntryTable::getInstance()->addEntry(
            $this->user,
            $this->model->isConcept() ? 'agreement_concept_report' : 'agreement_report',
            'cancel',
            $this->model->getActivity()->getName() . '/' . $this->model->getName(),
            'Отменена отправка отчёта на согласование',
            '',
            $this->model->getDealer(),
            $this->model->getId(),
            'agreement'
        );

        $this->model->createPrivateLogEntryForSpecialists($entry);
        $this->report->cancelSpecialistSending();

        $this->addMessageToDiscussion($this->model, 'отменена отправка отчёта на согласование');

        AgreementManagementHistoryMailSender::send(
            'AgreementCancelReportMail',
            $entry,
            false,
            false,
            $this->model->isConcept() ? AgreementManagementHistoryMailSender::NEW_AGREEMENT_CONCEPT_REPORT_NOTIFICATION : AgreementManagementHistoryMailSender::NEW_AGREEMENT_REPORT_NOTIFICATION
        );
    }

    protected function getReportAcceptText() {
        $text[] = 'Отчёт отправлен на согласование';

        $status = self::SEND_BOTH;
        if ($this->model->isValidModelCategory()) {
            if ($this->report->getManagerStatus() != 'accepted' && $this->report->getDesignerStatus() != 'accepted') {
                $text[] = 'менеджеру  / дизайнеру';
            } else if ($this->report->getManagerStatus() == 'accepted' && $this->report->getDesignerStatus() != 'accepted') {
                $text[] = 'дизайнеру';
                $status = self::SEND_DESIGNER;
            } else if ($this->report->getManagerStatus() != 'accepted' && $this->report->getDesignerStatus() == 'accepted') {
                $text[] = 'менеджеру';
                $status = self::SEND_MANAGER;
            }

            return array('text' => implode(' ', $text), 'status' => $status);
        }

        return 'Отчёт отправлен на согласование';
    }

    protected function changeStatusAndSendMessage($entry, $status_and_text) {
        /**
         * Change model manager / designer statuses
         */
        $model = AgreementModelTable::getInstance()->find($this->model->getId());

        if ($status_and_text['status'] == self::SEND_BOTH) {
            $this->report->setManagerStatus('wait');
            $this->report->setDesignerStatus('wait');
            $this->report->setStatus('wait_manager_specialist');
            $this->report->save();

            $this->sendToManager($entry);
            $this->sendToDesigner($status_and_text['text']);
        } else if ($status_and_text['status'] == self::SEND_MANAGER) {
            $this->report->setManagerStatus('wait');
            $this->report->setStatus('wait');
            $this->report->save();

            $this->sendToManager($entry);
        } else if ($status_and_text['status'] == self::SEND_DESIGNER) {
            $this->report->setDesignerStatus('wait');
            $this->report->setStatus('wait_specialist');
            $this->report->save();

            $this->sendToDesigner($status_and_text['text']);
        }

        $model->save();
    }

    private function sendToManager($entry) {
        AgreementManagementHistoryMailSender::send(
            'AgreementSendReportMail',
            $entry,
            false,
            false,
            $this->model->isConcept() ? AgreementManagementHistoryMailSender::NEW_AGREEMENT_CONCEPT_REPORT_NOTIFICATION : AgreementManagementHistoryMailSender::NEW_AGREEMENT_REPORT_NOTIFICATION
        );
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
                foreach ($active_users as $user) {
                    //Оганиченная рассылка писем для активности
                    if ($this->model->getActivity()->isLimitedDesignersAccess()) {
                        if (!ActivitySpecialistsTable::checkAllowUserForActivity($user, $this->model->getActivity())) {
                            continue;
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
        $comment = new AgreementModelReportComment();
        $comment->setArray(array(
            'report_id' => $this->report->getId(),
            'user_id' => $specialist->getId()
        ));

        $comment->setStatus('wait');
        $comment->save();

        $log_entry = LogEntryTable::getInstance()->addEntry(
            $this->user,
            $model->isConcept() ? 'agreement_concept_report' : 'agreement_report',
            'sent_to_specialist',
            $model->getActivity()->getName() . '/' . $model->getName(),
            'Вам отправлен отчёт для согласования',
            '',
            $model->getDealer(),
            $model->getId(),
            'agreement'
        );
        $log_entry->setPrivateUser($specialist);
        $log_entry->save();

        AgreementSpecialistHistoryMailSender::send('AgreementReportSentToSpecialistMail', $log_entry, $specialist, $msg);
    }

    //
    //Report
    protected function attachFinancialFilesDocsToMessage($report, $message)
    {
        $uploaded_files_list = $report->getUploadedFilesList(AgreementModelReport::UPLOADED_FILE_FINANCIAL);

        foreach ($uploaded_files_list as $file) {
            $this->saveAttachFile($message, $file->getFile(), AgreementModelReport::FINANCIAL_DOCS_FILE_PATH, 'fin');
        }
    }

    protected function attachAdditionalFilesToMessage($report, $message)
    {
        $uploaded_files_list = $report->getUploadedFilesList(AgreementModelReport::UPLOADED_FILE_ADDITIONAL);
        foreach ($uploaded_files_list as $file) {
            $this->saveAttachFile($message, $file->getFile(), AgreementModelReport::ADDITIONAL_FILE_PATH, 'add');
        }
    }

    protected function saveAttachFile($message, $file_name, $path, $label)
    {
        $file = new MessageFile();
        $file->setMessageId($message->getId());
        $file->setFile($label . '-' . $message->getId() . '-' . $file_name);

        copy(
            sfConfig::get('sf_upload_dir') . '/' . $path . '/' . $file_name,
            sfConfig::get('sf_upload_dir') . '/' . MessageFile::FILE_PATH . '/' . $file->getFile()
        );

        $file->save();
    }


}
