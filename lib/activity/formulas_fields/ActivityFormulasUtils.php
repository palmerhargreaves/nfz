<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 16.08.2016
 * Time: 11:09
 */
class ActivityFormulasUtils
{
    private $_param = '';
    private $_formula_id = 0;

    private $_params = array('field_id' => 'getFieldsList', 'custom_function' => 'getCustomFunctionsList', 'formula_result' => 'getFormulasResult');

    function __construct($formula_id, $param)
    {
        $this->_formula_id = $formula_id;
        $this->_param = $param;
    }

    public function build() {
        if (isset($this->_params[$this->_param]) && method_exists($this, $this->_params[$this->_param])) {
            return $this->{$this->_params[$this->_param]}();
        }

        return array();
    }

    private function getFieldsList() {
        return ActivityFieldsTable::getFieldListByFormulaId($this->_formula_id);
    }

    private function getCustomFunctionsList() {
        return array('getFormulaModelsCount' => 'Количество заявок', 'getModelsAcceptedSumm' => 'Сумма согласованных заявок');
    }

    private function getFormulasResult() {
        $formulas = array();

        $result = ActivityEfficiencyFormulasTable::getInstance()->createQuery()->where('id != ? and activity_id = ?', array($this->_formula_id, $this->getActivity()))->execute();
        foreach ($result as $item) {
            $formulas[$item->getId()] = $item->getName();
        }

        return $formulas;
    }

    private function getActivity() {
        $formula = ActivityEfficiencyFormulasTable::getInstance()->find($this->_formula_id);
        if ($formula) {
            return $formula->getActivityId();
        }

        return null;
    }
}
