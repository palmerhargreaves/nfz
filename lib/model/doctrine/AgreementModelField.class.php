<?php

/**
 * AgreementModelField
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    Servicepool2.0
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class AgreementModelField extends BaseAgreementModelField
{
    const IDENTIFIER_PLACE = 'place';
    const IDENTIFIER_PERIOD_FIELD = 'period';

    public function isPlaceField() {
        return $this->getChildField() == 1 || $this->getHide() == 1;
    }

    public function isPeriodField() {
        return $this->getType() == AgreementModelField::IDENTIFIER_PERIOD_FIELD;
    }
}