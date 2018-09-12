<?php

/**
 * ActivityEfficiencyFormulasParams form.
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ActivityEfficiencyFormulaParamsForm extends BaseActivityEfficiencyFormulaParamsForm
{
    private $_activity_id = -1;

    public function configure()
    {
        $this->widgetSchema['formula_id'] = new sfWidgetFormInputHidden();
        $this->widgetSchema['calc_position'] = new sfWidgetFormInputHidden();

        if (!$this->getObject()->isNew()) {
            $first_param = new ActivityFormulasUtils($this->getObject()->getFormulaId(), $this->getObject()->getParam1Type());
            $second_param = new ActivityFormulasUtils($this->getObject()->getFormulaId(), $this->getObject()->getParam2Type());

            $this->initChoicesFields('param1_value', $first_param->build());
            $this->initChoicesFields('param2_value', $second_param->build());
        }

        foreach ($this->validatorSchema->getFields() as $validator) {
            $validator->setMessage('required', 'Обязательно для заполнения');
        }
    }

    protected function doBind(array $values)
    {
        if (!isset($values['param1_type'])) {
            $fields = $this->getFieldsList($values['formula_id']);

            $this->initChoicesFields('param1_value', $fields);
            $this->initChoicesFields('param2_value', $fields);
        }

        parent::doBind($values);
    }

    public function setActivityId($activityId) {
        $this->_activity_id = $activityId;
    }

    public function getActivityId() {
        return $this->_activity_id;
    }

    private function initChoicesFields($field, $values) {
        $this->widgetSchema[$field] = new sfWidgetFormChoice(array('choices' => $values));
        $this->validatorsSchema[$field] = new sfValidatorChoice(array('choices' => array_keys($values)));
    }

    private function getFieldsList($formula_id, $activity_id = null) {
        return ActivityFieldsTable::getFieldListByFormulaId($formula_id);
    }
}
