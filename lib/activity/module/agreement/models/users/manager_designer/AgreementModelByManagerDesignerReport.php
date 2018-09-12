<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 16.03.2017
 * Time: 10:05
 */

class AgreementModelByManagerDesignerReport extends AgreementModelReportByAbstract {

    /*public function agreement() {
        AgreementModelStatusFactory::getInstance()
            ->getStatusClass(
                $this->makeSendParams(AgreementModelStatusObjectInterface::DEALER)
            )
            ->getStatusCls()
            ->getObject()
            ->acceptStatus();
    }

    public function decline() {
        AgreementModelStatusFactory::getInstance()
            ->getStatusClass(
                $this->makeSendParams(AgreementModelStatusObjectInterface::DEALER)
            )
            ->getStatusCls()
            ->getObject()
            ->declineStatus();
    }*/

    /*public function agreementManagerAccept() {
        AgreementModelStatusFactory::getInstance()
            ->getStatusClass(
                $this->makeSendParams(AgreementModelStatusObjectInterface::MANAGER_DESIGNER)
            )
            ->getStatusCls()
            ->getObject()
            ->acceptStatus();
    }

    public function agreementManagerDecline() {
        AgreementModelStatusFactory::getInstance()
            ->getStatusClass(
                $this->makeSendParams(AgreementModelStatusObjectInterface::MANAGER_DESIGNER)
            )
            ->getStatusCls()
            ->getObject()
            ->declineStatus();
    }*/

    /**
     * Accept model by specialist
     */
    public function agreementSpecialistAccept(){
        return AgreementModelStatusFactory::getInstance()
            ->getStatusClass(
                $this->makeSendParams(AgreementModelStatusObjectInterface::SPECIALIST)
            )
            ->getStatusCls()
            ->getObject()
            ->acceptStatus();
    }

    /**
     * Decline model by specialist
     */
    public function agreementSpecialistDecline() {
        return AgreementModelStatusFactory::getInstance()
            ->getStatusClass(
                $this->makeSendParams(AgreementModelStatusObjectInterface::SPECIALIST)
            )
            ->getStatusCls()
            ->getObject()
            ->declineStatus();
    }

    protected function getSpecialistDeclineStatus($comment) {
        $report = $this->report;
        if ($report->getStatus() == 'wait_specialist') {
            return 'declined';
        }

        if (!is_null($comment)) {
            $report = $comment->getReport();

            return $report->getManagerStatus() != 'accepted' && $comment->getReport()->getManagerStatus() != 'declined' ? 'wait' : 'declined';
        }

        return $report->getManagerStatus() != 'accepted' ? 'wait' : 'declined';
    }

    private function makeSendParams($cls_prefix, $cls = 'AgreementModelReportStatus')
    {
        return array
        (
            'obj' => $this->model,
            'report' => $this->model->getReport(),
            'cls' => $cls,
            'class_prefix' => $cls_prefix,
            'request' => $this->request,
            'form' => isset($this->form) ? $this->form : null,
            'user' => $this->user,
            'agreement_comments' => isset($this->agreement_comments) ? $this->agreement_comments : null
        );
    }
}
