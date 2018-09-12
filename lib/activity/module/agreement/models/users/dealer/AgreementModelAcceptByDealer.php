<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 16.03.2017
 * Time: 9:18
 */

/**
 * Agreement model by category type of model
 * Class AgreementModelAcceptByDealer
 */
class AgreementModelAcceptByDealer extends AgreementAcceptDeclineAbstract {
    /**
     * Make agreement model by model category type
     */
    public function agreement() {
        return $this->getClass()->agreement();
    }

    /**
     * Make draft agreement by model category
     */
    public function agreementDraft() {
        return $this->getClass()->agreementDraft();
    }

    /**
     * Cancel model agreement
     */
    public function cancel() {
        return $this->getClass()->decline();
    }

    /**
     * Cancel model scenario agreement
     */
    public function cancelScenario() {
        return $this->getClass()->declineScenario();
    }

    /**
     * Cancel model record agreement
     */
    public function cancelRecord() {
        return $this->getClass()->declineRecord();
    }

    /**
     * Update agreement model
     */
    public function agreementUpdate() {
        return $this->getClass()->agreementUpdate();
    }

}
