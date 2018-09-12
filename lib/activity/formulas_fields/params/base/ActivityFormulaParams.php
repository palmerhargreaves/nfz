<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 16.08.2016
 * Time: 16:04
 */

abstract class ActivityFormulaParams
{
    protected $_param = null;
    protected $_dealer_id = null;
    protected $_param_index = 0;
    protected $_activity_id = 0;

    public function __construct($param, $param_index, $dealer_id = null)
    {
        $this->_param = $param;
        $this->_dealer_id = $dealer_id;
        $this->_param_index = $param_index;

        $formula = ActivityEfficiencyFormulasTable::getInstance()->find($param->getFormulaId());
        if ($formula) {
            $this->_activity_id = $formula->getActivityId();

        }
    }

    protected function getParamValue() {
        return $this->_param->{'getParam'.$this->_param_index.'Value'}();
    }

    protected function getAllowSumValues() {
        return $this->_param->{'getParam'.$this->_param_index.'AllowToSum'}();
    }

    abstract function getName();
    abstract function calculate();
}
