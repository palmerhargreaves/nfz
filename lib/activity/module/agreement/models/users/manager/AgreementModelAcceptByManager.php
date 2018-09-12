<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 16.03.2017
 * Time: 9:18
 */
use Fxp\Composer\AssetPlugin\Repository\Util;

/**
 * Agreement model by category type of model
 * Class AgreementModelAcceptByManager
 */
class AgreementModelAcceptByManager extends AgreementAcceptDeclineAbstract {

    /**
     * Accept model by manager
     */
    public function agreementManagerAccept() {
        return $this->getClass()->agreementManagerAccept();
    }

    /**
     * Decline model by manager
     */
    public function agreementManagerDecline() {
        return $this->getClass()->agreementManagerDecline();
    }
}
