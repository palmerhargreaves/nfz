<?php

/**
 * ActivityEfficiencyFormulasParams form base class.
 *
 * @method ActivityEfficiencyFormulasParams getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseActivityEfficiencyFormulaParamsForm extends BaseFormDoctrine
{
    public function setup()
    {
        $this->setWidgets(array(
            'id' => new sfWidgetFormInputHidden(),
            'formula_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ActivityEfficiencyFormulas'), 'add_empty' => false)),
            'param1_type' => new sfWidgetFormChoice(array('choices' => array('field_id' => 'Поле', 'formula_result' => 'Результат формулы', 'custom_function' => 'Кастомная функция'))),
            'param1_value' => new sfWidgetFormInputText(),
            'param1_allow_to_sum' => new sfWidgetFormInputCheckbox(),
            'param2_type' => new sfWidgetFormChoice(array('choices' => array('field_id' => 'Поле', 'formula_result' => 'Результат формулы', 'custom_function' => 'Кастомная функция'))),
            'param2_value' => new sfWidgetFormInputText(),
            'param2_allow_to_sum' => new sfWidgetFormInputCheckbox(),
            'description' => new sfWidgetFormTextarea(),
            'calc_position'    => new sfWidgetFormInputText(),
            'params_action' => new sfWidgetFormChoice(array('choices' => array('mult' => '*', 'plus' => '+', 'minus' => '-', 'div' => '/'))),

        ));

        $this->setValidators(array(
            'id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
            'formula_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('ActivityEfficiencyFormulas'))),
            'param1_type' => new sfValidatorChoice(array('choices' => array(0 => 'field_id', 1 => 'formula_result', 2 => 'custom_function'), 'required' => false)),
            'param1_value' => new sfValidatorString(array('required' => false)),
            'param1_allow_to_sum' => new sfValidatorBoolean(array('required' => false)),
            'param2_type' => new sfValidatorChoice(array('choices' => array(0 => 'field_id', 1 => 'formula_result', 2 => 'custom_function'), 'required' => false)),
            'param2_value' => new sfValidatorString(array('required' => false)),
            'param2_allow_to_sum' => new sfValidatorBoolean(array('required' => false)),
            'description' => new sfValidatorString(array('required' => false)),
            'calc_position' => new sfValidatorInteger(array('required' => false)),
            'params_action' => new sfValidatorChoice(array('choices' => array(0 => 'mult', 1 => 'plus', 2 => 'minus', 3 => 'div'), 'required' => false)),

        ));

        $this->widgetSchema->setNameFormat('activity_efficiency_formula_param[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'ActivityEfficiencyFormulaParams';
    }

}
