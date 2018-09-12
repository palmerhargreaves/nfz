<?php
/**
 * Created by PhpStorm.
 * User: kostet
 * Date: 16.08.2016
 * Time: 16:03
 */

class ActivityFieldIdParam extends ActivityFormulaParams
{

    function getName()
    {
        $field = ActivityFieldsTable::getInstance()->find($this->getParamValue());
        if ($field) {
            return $field->getName();
        }

        return '';
    }

    /**
     * @return int
     */
    function calculate()
    {
        if (!$this->getParamValue()) {
            return 0;
        }

        $query = ActivityFieldsValuesTable::getInstance()->createQuery()->where('field_id = ? and dealer_id = ?', array($this->getParamValue(), $this->_dealer_id))->orderBy('field_id');
        if ($this->getAllowSumValues()) {
            return $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
        }

        $calc_result = $query->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);

        return $calc_result['val'];
    }
}