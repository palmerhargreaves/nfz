<?php

/**
 * ActivityEfficiencyFormulas form base class.
 *
 * @method ActivityEfficiencyFormulas getObject() Returns the current form's model object
 *
 * @package    Servicepool2.0
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseActivityEfficiencyFormulasForm extends BaseFormDoctrine
{
    public function setup()
    {
        $this->setWidgets(array(
            'id' => new sfWidgetFormInputHidden(),
            'activity_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Activity'), 'add_empty' => false)),
            'work_formula_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ActivityEfficiencyWorkFormulas'), 'add_empty' => false)),
            'name' => new sfWidgetFormInputText(),
            'description' => new sfWidgetFormTextarea(),
            'status' => new sfWidgetFormInputCheckbox(),
        ));

        $this->setValidators(array(
            'id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
            'activity_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Activity'), 'required' => true)),
            'work_formula_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('ActivityEfficiencyWorkFormulas'), 'required' => true)),
            'name' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
            'description' => new sfValidatorString(array('required' => false, 'required' => false)),
            'status' => new sfValidatorBoolean(array('required' => false)),
        ));

        $this->widgetSchema->setNameFormat('activity_efficiency_formulas[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'ActivityEfficiencyFormulas';
    }

}
