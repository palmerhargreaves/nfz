<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 02.04.2017
 * Time: 11:52
 */

interface AgreementModelStatusInterface {
    /**
     * Get model discussion status text
     * @return mixed
     */
    public function getStatusText();

    /**
     * Agreement model status
     * @return mixed
     */
    public function acceptStatus();

    /**
     * Agreement model update
     * @return mixed
     */
    public function updateStatus();

    /**
     * Decline model status
     * @return mixed
     */
    public function declineStatus();
}
