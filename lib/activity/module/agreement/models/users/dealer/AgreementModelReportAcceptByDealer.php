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
class AgreementModelReportAcceptByDealer extends AgreementAcceptDeclineAbstract {
    protected $_postfix = 'report';

    /**
     * Make agreement model by model category type
     */
    public function agreement() {
        return $this->getReportClass()->agreement();
    }

    /**
     * Cancel model agreement
     */
    public function cancel() {
        return $this->getReportClass()->decline();
    }

}
