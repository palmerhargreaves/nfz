<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 02.04.2017
 * Time: 11:51
 */

class AgreementModelReportManagerStatus  implements AgreementModelStatusInterface
{
    private $_obj = null;

    public  function __construct($obj)
    {
        $this->_obj = $obj;
    }

    /**
     * Get model discussion status text
     * @return mixed
     */
    public function getStatusText()
    {
        // TODO: Implement getStatusText() method.
    }

    /**
     * Decline model status
     * @return mixed
     */
    public function declineStatus()
    {
        // TODO: Implement declineStatus() method.
    }

    /**
     * Agreement model status
     * @return mixed
     */
    public function acceptStatus()
    {
        // TODO: Implement acceptStatus() method.
    }

    /**
     * Agreement model update
     * @return mixed
     */
    public function updateStatus()
    {
        // TODO: Implement updateStatus() method.
    }
}
