<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 16.08.2016
 * Time: 16:03
 */


class ActivityCustomFunctionParam extends ActivityFormulaParams
{

    function getName()
    {
        return $this->getParamValue();
    }

    function calculate()
    {
        $custom_funcs = new AgreementModelsCustomFunction($this->_activity_id, $this->_dealer_id);

        if ($this->getParamValue()) {
            return $custom_funcs->{$this->getParamValue()}();
        }

        return 0;
    }
}