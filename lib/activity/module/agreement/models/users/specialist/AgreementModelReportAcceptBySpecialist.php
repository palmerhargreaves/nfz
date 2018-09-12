<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 16.03.2017
 * Time: 9:18
 */

/**
 * Agreement model by category type of model
 * Class AgreementModelAcceptBySpecialist
 */
class AgreementModelReportAcceptBySpecialist extends AgreementAcceptDeclineAbstract {
    protected $_postfix = 'report';

    /**
     * Accept model by specialist
     */
    public function agreementSpecialistAccept() {
        $this->getReportClass()->agreementSpecialistAccept();
    }

    /**
     * Decline model by specialist
     */
    public function agreementSpecialistDecline() {
        $this->getReportClass()->agreementSpecialistDecline();
    }
}
