<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 16.03.2017
 * Time: 10:05
 */

class AgreementSpecialConceptAcceptDeclineByImporter extends AgreementAcceptDeclineAbstract {
    /**
     * Accept model by manager
     */
    public function accept() {
        return $this->getClass()->agreement();
    }

    /**
     * Decline model by manager
     */
    public function decline() {
        return $this->getClass()->agreementManagerDecline();
    }
}


