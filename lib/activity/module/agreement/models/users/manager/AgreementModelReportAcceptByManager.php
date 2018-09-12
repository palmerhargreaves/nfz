<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 16.03.2017
 * Time: 9:18
 */

/**
 * Agreement model by category type of model
 * Class AgreementModelAcceptByManager
 */
class AgreementModelReportAcceptByManager extends AgreementAcceptDeclineAbstract {
    protected $_postfix = 'report';

    /**
     * Accept model by manager
     */
    public function agreementManagerAccept() {
        return $this->getReportClass()->agreementManagerAccept();
    }

    /**
     * Decline model by manager
     */
    public function agreementManagerDecline() {
        return $this->getReportClass()->agreementManagerDecline();
    }
}
