<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 16.08.2016
 * Time: 16:04
 */


class ActivityFormulaResultParam extends ActivityFormulaParams
{
    function getName()
    {
        $formula = ActivityEfficiencyFormulasTable::getInstance()->find($this->getParamValue());
        if ($formula) {
            return $formula->getName();
        }

        return '';
    }

    function calculate()
    {
        $formula = ActivityEfficiencyFormulasTable::getInstance()->find($this->getParamValue());

        return $formula->getParamsCalculateResult($this->_dealer_id);
    }
}
