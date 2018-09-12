<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 02.04.2017
 * Time: 11:52
 */

class AgreementConceptSpecialImporterStatus extends ModelReportStatus implements AgreementModelStatusInterface
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

        //Статус выполнения концепции по импортеру
        SpecialAgreementConceptStatuses::setStatus($this->model, SpecialAgreementConceptStatuses::AGREEMENT_STATUS_IMPORTER_ACCEPTED);

        $model_status = SpecialAgreementConceptStatuses::getStatus($this->model->getId());

        $connection = Doctrine_Manager::getInstance()->getConnection('doctrine');
        $connection->beginTransaction();
        try {
            $this->model->setStatus($model_status);
            $this->model->save();

            $connection->commit();
        }
        catch(Exception $e) {
            $connection->rollback();
        }

        $message = $this->acceptImporterModel($msg_files, $model_status);

        $this->showDiscussionMsg();
        $this->sendMails($this->model);

        return $message;
    }

    protected function acceptImporterModel($msg_files = array(), $model_status = 'none') {
        $utils = new AgreementSpecialConceptStatusUtilsByImporter();

        $utils->setMsgStatus(Message::MSG_STATUS_ACCEPTED);
        $utils->setMsgType(Message::MSG_TYPE_SPECIALIST);

        return $utils->acceptModel(
            $this->model,
            $this->user,
            $this->form->getValue('agreement_comments'),
            $msg_files,
            false,
            $model_status
        );
    }

    public function updateStatus() {

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

        //Статус выполнения концепции по импортеру
        SpecialAgreementConceptStatuses::setStatus($this->model, SpecialAgreementConceptStatuses::AGREEMENT_STATUS_IMPORTER_DECLINED);

        $model_status = SpecialAgreementConceptStatuses::getStatus($this->model->getId());

        $this->model->setStatus($model_status);
        $this->model->setDeclineReasonId($this->form->getValue('decline_reason_id'));
        $this->model->save();

        $utils = new AgreementSpecialConceptStatusUtilsByImporter();
        $utils->setMsgStatus(Message::MSG_STATUS_DECLINED);
        $utils->setMsgType(Message::MSG_TYPE_SPECIALIST);

        $message = $utils->declineModel(
            $this->model,
            $this->user,
            null, //AgreementDeclineReasonTable::getInstance()->find($model->getDeclineReasonId()),
            $this->form->getValue('agreement_comments'),
            $msg_files,
            $model_status
        );

        $this->showDiscussionMsg();
        $this->sendMails($this->model);

        return $message;
    }

    protected function sendMails(AgreementModel $model)
    {
        //Активируем отправку писем
        //Если менеджер / специалист согласовали или отклонили заявку отправляем все письма в ожидании

        if ((SpecialAgreementConceptStatuses::status($model->getId(), SpecialAgreementConceptStatuses::AGREEMENT_STATUS_REG_MANAGER_ACCEPTED)
                && SpecialAgreementConceptStatuses::status($model->getId(), SpecialAgreementConceptStatuses::AGREEMENT_STATUS_IMPORTER_ACCEPTED)) ||
            (SpecialAgreementConceptStatuses::status($model->getId(), SpecialAgreementConceptStatuses::AGREEMENT_STATUS_REG_MANAGER_DECLINED)
                && SpecialAgreementConceptStatuses::status($model->getId(), SpecialAgreementConceptStatuses::AGREEMENT_STATUS_IMPORTER_DECLINED)))
        {
            //Активируем отправку писем
            $mails_list = MailMessageTable::getInstance()->createQuery()->where('model_id = ? and can_send = ?', array($model->getId(), false))->execute();
            foreach ($mails_list as $mail) {
                $mail->setCanSend(true);
                $mail->save();
            }
        }
        //Если менеджер согласовал и специалист нет, удаляем все письма от менеджера, письма от специалиста отправляем
        else if (SpecialAgreementConceptStatuses::status($model->getId(), SpecialAgreementConceptStatuses::AGREEMENT_STATUS_REG_MANAGER_ACCEPTED) && !SpecialAgreementConceptStatuses::status($model->getId(), SpecialAgreementConceptStatuses::AGREEMENT_STATUS_IMPORTER_ACCEPTED)) {
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
        else if (!SpecialAgreementConceptStatuses::status($model->getId(), SpecialAgreementConceptStatuses::AGREEMENT_STATUS_REG_MANAGER_ACCEPTED) && SpecialAgreementConceptStatuses::status($model->getId(), SpecialAgreementConceptStatuses::AGREEMENT_STATUS_IMPORTER_ACCEPTED)) {
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

    /**
     * Show message model when accepted / declined by current state of manager / designer
     * @return bool
     */
    protected function showAllMessages() {
        $model_status = SpecialAgreementConceptStatuses::getStatus($this->model->getId());
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
        return SpecialAgreementConceptStatuses::isDirtyConcept($this->model->getId());
    }

}
