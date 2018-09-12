<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 16.03.2017
 * Time: 10:05
 */

class AgreementModelByManagerReport extends AgreementModelReportByAbstract {

    public function agreement()
    {
        $this->report->setManagerStatus('accepted');
        $this->report->save();

        $entry = LogEntryTable::getInstance()->addEntry(
            $this->user,
            $this->model->isConcept() ? 'agreement_concept_report' : 'agreement_report',
            'edit',
            $this->model->getActivity()->getName() . '/' . $this->model->getName(),
            $this->getReportAcceptText(),
            'clip',
            $this->model->getDealer(),
            $this->model->getId(),
            'agreement'
        );

        $this->model->createPrivateLogEntryForSpecialists($entry);

        $message = $this->addMessageToDiscussion($this->model, $this->getReportAcceptText(), Message::MSG_STATUS_SENDED);

        $this->attachFinancialFilesDocsToMessage($this->report, $message);
        $this->attachAdditionalFilesToMessage($this->report, $message);

        AgreementManagementHistoryMailSender::send(
            'AgreementSendReportMail',
            $entry,
            false,
            false,
            $this->model->isConcept() ? AgreementManagementHistoryMailSender::NEW_AGREEMENT_CONCEPT_REPORT_NOTIFICATION : AgreementManagementHistoryMailSender::NEW_AGREEMENT_REPORT_NOTIFICATION
        );

        return $entry;
    }

}
