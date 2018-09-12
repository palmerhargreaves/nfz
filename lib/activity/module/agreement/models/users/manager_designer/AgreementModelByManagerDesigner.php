<?php

/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 16.03.2017
 * Time: 10:05
 */
class AgreementModelByManagerDesigner extends AgreementModelByAbstract
{
    /**
     * Make agreement model
     * @return mixed
     */
    public function agreement()
    {
        return AgreementModelStatusFactory::getInstance()
            ->getStatusClass(
                $this->makeSendParams(AgreementModelStatusObjectInterface::DEALER)
            )
            ->getStatusCls()
            ->getObject()
            ->acceptStatus();
    }

    /**
     * Update agreement model by manager / designer
     */
    public function agreementUpdate()
    {
        return AgreementModelStatusFactory::getInstance()
            ->getStatusClass(
                $this->makeSendParams(AgreementModelStatusObjectInterface::DEALER)
            )
            ->getStatusCls()
            ->getObject()
            ->updateStatus();
    }

    /**
     * Accept model by specialist
     */
    public function agreementSpecialistAccept()
    {
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
    public function agreementSpecialistDecline()
    {
        return AgreementModelStatusFactory::getInstance()
            ->getStatusClass(
                $this->makeSendParams(AgreementModelStatusObjectInterface::SPECIALIST)
            )
            ->getStatusCls()
            ->getObject()
            ->declineStatus();
    }

    public function agreementManagerAccept()
    {
        return AgreementModelStatusFactory::getInstance()
            ->getStatusClass(
                $this->makeSendParams(AgreementModelStatusObjectInterface::MANAGER_DESIGNER)
            )
            ->getStatusCls()
            ->getObject()
            ->acceptStatus();
    }

    public function agreementManagerDecline()
    {
        return AgreementModelStatusFactory::getInstance()
            ->getStatusClass(
                $this->makeSendParams(AgreementModelStatusObjectInterface::MANAGER_DESIGNER)
            )
            ->getStatusCls()
            ->getObject()
            ->declineStatus();
    }

    private function makeSendParams($cls_prefix, $cls = 'AgreementModelStatus')
    {
        return array
        (
            'obj' => $this->model,
            'cls' => $cls,
            'class_prefix' => $cls_prefix,
            'model_by' => $this,
            'request' => isset($this->request) ? $this->request : null,
            'form' => isset($this->form) ? $this->form : null,
            'user' => $this->user
        );
    }
}
