<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 18.03.2017
 * Time: 13:53
 */

interface AgreementModelByInterface{
    /**
     * Make agreement model
     * @return mixed
     */
    public function agreement();

    public function agreementUpdate();

    /**
     * Make draft agreement model
     * @return mixed
     */
    public function agreementDraft();

    /**
     * Decline agrement model
     * @return mixed
     */
    public function decline();

    /**
     * Cancel scenario model agreement
     */
    public function declineScenario();

    /**
     * Cancel record model agreement
     */
    public function declineRecord();

    public function agreementManagerAccept();

    public function agreementManagerDecline();

    /**
     * Accept model by specialist
     */
    public function agreementSpecialistAccept();

    /**
     * Decline model by specialist
     */
    public function agreementSpecialistDecline();


}
