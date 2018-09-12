<?php
/**
 * Created by PhpStorm.
 * User: kostig51
 * Date: 20.05.2018
 * Time: 16:47
 */

class AgreementSpecialConceptByRegionalManager extends AgreementModelByAbstract {
    /**
     * Make agreement model
     * @return mixed
     * @throws Exception
     */
    public function agreement()
    {
        return AgreementModelStatusFactory::getInstance()
            ->getStatusClass(
                $this->makeSendParams(AgreementModelStatusObjectInterface::REGIONAL_MANAGER)
            )
            ->getStatusCls()
            ->getObject()
            ->acceptStatus();
    }

    public function agreementManagerDecline()
    {
        return AgreementModelStatusFactory::getInstance()
            ->getStatusClass(
                $this->makeSendParams(AgreementModelStatusObjectInterface::REGIONAL_MANAGER)
            )
            ->getStatusCls()
            ->getObject()
            ->declineStatus();
    }

    private function makeSendParams($cls_prefix, $cls = 'AgreementSpecialConceptByImporterStatus' )
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
