<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 02.04.2017
 * Time: 11:52
 */

class AgreementConceptSpecialReportImporterStatus extends ModelReportStatus implements AgreementModelStatusInterface
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
        $this->report = $this->model->getReport();
        if (!$this->report) {
            $this->report = new AgreementModelReport();
        }

        //Статус выполнения концепции по импортеру
        SpecialAgreementConceptStatuses::setStatus($this->model, SpecialAgreementConceptStatuses::AGREEMENT_REPORT_STATUS_IMPORTER_ACCEPTED);

        $report_status = SpecialAgreementConceptStatuses::getReportStatus($this->model->getId());

        $this->report->setModelId($this->model->getId());
        $this->report->setStatus($report_status);
        $this->report->save();

        $this->model->setReportId($this->report->getId());
        $this->model->save();

        /**
         * Make copy of uploaded temp files and remove
         */
        $msg_files = TempFileTable::copyFilesByRequest($this->request, AgreementModelReport::AGREEMENT_COMMENTS_FILE_PATH);

        $utils = new AgreementSpecialConceptReportImporterStatusUtils();
        $utils->setMsgStatus(Message::MSG_STATUS_ACCEPTED);
        $utils->setMsgType(Message::MSG_TYPE_SPECIALIST);

        $utils->acceptReport($this->report, $this->user, $this->form->getValue('agreement_comments'), $msg_files, $report_status);

        $this->sendReportMails($this->report);
        $this->showReportDiscussionMsg();
    }

    protected function sendReportMails ( AgreementModelReport $report )
    {
        //Активируем отправку писем
        //Если менеджер / специалист согласовали или отклонили заявку отправляем все письма в ожидании
        $model = $report->getModel();

        if ((SpecialAgreementConceptStatuses::status($model->getId(), SpecialAgreementConceptStatuses::AGREEMENT_REPORT_STATUS_REG_MANAGER_ACCEPTED)
                && SpecialAgreementConceptStatuses::status($model->getId(), SpecialAgreementConceptStatuses::AGREEMENT_REPORT_STATUS_IMPORTER_ACCEPTED)) ||
            (SpecialAgreementConceptStatuses::status($model->getId(), SpecialAgreementConceptStatuses::AGREEMENT_REPORT_STATUS_REG_MANAGER_DECLINED)
                && SpecialAgreementConceptStatuses::status($model->getId(), SpecialAgreementConceptStatuses::AGREEMENT_REPORT_STATUS_IMPORTER_DECLINED)))
        {
            //Активируем отправку писем
            $mails_list = MailMessageTable::getInstance()->createQuery()->where('model_id = ? and can_send = ?', array($model->getId(), false))->execute();
            foreach ($mails_list as $mail) {
                $mail->setCanSend(true);
                $mail->save();
            }
        }
        //Если менеджер согласовал и специалист нет, удаляем все письма от менеджера, письма от специалиста отправляем
        else if (SpecialAgreementConceptStatuses::status($model->getId(), SpecialAgreementConceptStatuses::AGREEMENT_REPORT_STATUS_REG_MANAGER_ACCEPTED) && !SpecialAgreementConceptStatuses::status($model->getId(), SpecialAgreementConceptStatuses::AGREEMENT_REPORT_STATUS_IMPORTER_ACCEPTED)) {
            //Удалем письма
            MailMessageTable::getInstance()->createQuery()->where('model_id = ? and msg_type = ?', array($model->getId(), Message::MSG_TYPE_MANAGER))->delete()->execute();

            //Активируем отправку писем
            $mails_list = MailMessageTable::getInstance()->createQuery()->where('model_id = ? and can_send = ?', array($model->getId(), false))->execute();
            foreach ($mails_list as $mail) {
                $mail->setCanSend(true);
                $mail->save();
            }
        }
        //Если менеджер не согласовал а специалист да, отправляем все письма от менеджера, письма от специалиста удаляем
        else if (!SpecialAgreementConceptStatuses::status($model->getId(), SpecialAgreementConceptStatuses::AGREEMENT_REPORT_STATUS_REG_MANAGER_ACCEPTED) && SpecialAgreementConceptStatuses::status($model->getId(), SpecialAgreementConceptStatuses::AGREEMENT_REPORT_STATUS_IMPORTER_ACCEPTED)) {
            //Удалем письма
            MailMessageTable::getInstance()->createQuery()->where('model_id = ? and msg_type = ?', array($model->getId(), Message::MSG_TYPE_SPECIALIST))->delete()->execute();

            //Активируем отправку писем
            $mails_list = MailMessageTable::getInstance()->createQuery()->where('model_id = ? and can_send = ?', array($model->getId(), false))->execute();
            foreach ($mails_list as $mail) {
                $mail->setCanSend(true);
                $mail->save();
            }
        }
    }

    public function updateStatus() {

    }

    /**
     * Decline model status
     * @return mixed
     */
    public function declineStatus()
    {
        $report = $this->model->getReport();
        if (!$report)
            $report = new AgreementModelReport();

        //Статус выполнения концепции по импортеру
        SpecialAgreementConceptStatuses::setStatus($this->model, SpecialAgreementConceptStatuses::AGREEMENT_REPORT_STATUS_IMPORTER_DECLINED);

        $report_status = SpecialAgreementConceptStatuses::getReportStatus($this->model->getId());

        $report->setModelId($this->model->getId());
        $report->setStatus($report_status);
        $report->setDeclineReasonId($this->form->getValue('decline_reason_id'));
        $report->setAgreementComments($this->form->getValue('agreement_comments'));

        $report->save();

        $this->model->setReportId($report->getId());
        $this->model->save();

        /**
         * Make copy of uploaded temp files and remove
         */
        $msg_files = TempFileTable::copyFilesByRequest($this->request, AgreementModelReport::AGREEMENT_COMMENTS_FILE_PATH);

        $utils = new AgreementSpecialConceptReportImporterStatusUtils();
        $utils->setMsgStatus(Message::MSG_STATUS_DECLINED);
        $utils->setMsgType(Message::MSG_TYPE_SPECIALIST);
        $utils->declineReport(
            $report,
            $this->user,
            null, //AgreementDeclineReasonTable::getInstance()->find($report->getDeclineReasonId()),
            $this->form->getValue('agreement_comments'),
            $msg_files,
            $report_status
        );

        $this->sendReportMails($report);
        $this->showReportDiscussionMsg();
    }

    /**
     * Show message model when accepted / declined by current state of manager / designer
     * @return bool
     */
    protected function showAllMessages() {
        $model_status = SpecialAgreementConceptStatuses::getReportStatus($this->model->getId());
        if ($model_status == 'accepted') {
            return true;
        }

        return !($model_status != 'accepted');
    }

    /**
     * Can show message when accepted / declined model
     * @return bool
     */
    protected function canShowMessage() {
        return SpecialAgreementConceptStatuses::isDirtyReportConcept($this->model->getId());
    }
}
